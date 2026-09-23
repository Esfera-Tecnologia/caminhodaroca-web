<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Perfil de acesso de quem responde por uma propriedade.
     * Precisa bater exatamente com o nome usado em App\Models\User::isResponsible(),
     * App\Helpers\App::getPermissao() e em App\Http\Controllers\PropertyController.
     */
    private const RESPONSIBLE_PROFILE = 'Responsável';

    /**
     * Nome padrão da lista de favoritos criada junto com cada usuário
     * (mesmo comportamento de App\Models\User::booted()).
     */
    private const DEFAULT_FAVORITE_LIST = 'Favoritos';

    /**
     * Repara as propriedades cujo `email_responsavel` não tem usuário correspondente.
     *
     * Uma propriedade não possui login próprio: quem acessa é o usuário da tabela
     * `users` cujo e-mail é igual a `properties.email_responsavel` e que tenha o
     * perfil "Responsável". Sem esse usuário, o fluxo "Esqueci minha senha" falha
     * com "Não conseguimos encontrar nenhum usuário com o endereço de e-mail
     * informado" e o responsável também não consegue ver a propriedade no painel.
     *
     * Causas conhecidas:
     *  - propriedades cadastradas antes de 10/11/2025 (a criação automática do
     *    usuário responsável ainda não existia);
     *  - e-mail da propriedade editado antes de 19/05/2026 (quando passou a existir
     *    a sincronização do e-mail do usuário);
     *  - contas excluídas pelo app (e-mail anonimizado para deleted_<uuid>@example.com)
     *    ou removidas definitivamente pelo painel.
     *
     * A migração é idempotente e pode rodar mais de uma vez sem duplicar dados:
     * só cria usuário quando realmente não existe ninguém com aquele e-mail e só
     * vincula o perfil quando o vínculo ainda não existe.
     *
     * Observação sobre propriedades cujo responsável hoje tem conta com OUTRO
     * e-mail: será criada uma conta nova com o e-mail da propriedade, porque o
     * `email_responsavel` é a referência de acesso do sistema (é assim que
     * PropertyController::storeProperty() e updateResponsibleUserEmail() trabalham).
     * A conta antiga não é alterada nem apagada.
     */
    public function up(): void
    {
        if (! Schema::hasTable('properties') || ! Schema::hasTable('users')) {
            return;
        }

        $responsibleProfileId = $this->responsibleProfileId();
        $hasProfilePivot = Schema::hasTable('user_has_access_profile');
        $hasFavoriteLists = Schema::hasTable('favorite_lists');

        $properties = DB::table('properties')
            ->select(['id', 'name', 'nome_responsavel', 'email_responsavel'])
            ->whereNotNull('email_responsavel')
            ->orderBy('id')
            ->get();

        $createdUsers = 0;
        $linkedProfiles = 0;
        $skippedPropertyIds = [];
        $unusualEmails = [];

        foreach ($properties as $property) {
            $email = trim((string) $property->email_responsavel);

            // E-mails vazios, sem "@" ou de contas já anonimizadas não são recuperáveis.
            if (
                $email === ''
                || ! str_contains($email, '@')
                || Str::startsWith(Str::lower($email), 'deleted_')
            ) {
                $skippedPropertyIds[] = $property->id;
                continue;
            }

            // O e-mail é a chave de ligação da propriedade. Por isso vale mais criar a
            // conta com o valor exato que está no banco do que deixar a propriedade sem
            // acesso; valores fora do padrão ficam registados no log para revisão.
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $unusualEmails[$property->id] = $email;
            }

            $userId = DB::table('users')->where('email', $email)->value('id');

            if ($userId === null) {
                $userId = DB::table('users')->insertGetId([
                    'name' => $this->responsibleName($property, $email),
                    'email' => $email,
                    // Conta sem senha utilizável: o responsável define a sua senha
                    // pelo fluxo "Esqueci minha senha".
                    'password' => Hash::make(Str::random(40)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->createDefaultFavoriteList($userId, $hasFavoriteLists);

                $createdUsers++;
            }

            if (
                $hasProfilePivot
                && $responsibleProfileId !== null
                && $this->attachResponsibleProfile((int) $userId, $responsibleProfileId)
            ) {
                $linkedProfiles++;
            }
        }

        $this->reportSummary([
            'propriedades_verificadas' => $properties->count(),
            'usuarios_criados' => $createdUsers,
            'perfis_responsavel_vinculados' => $linkedProfiles,
            'propriedades_ignoradas' => count($skippedPropertyIds),
            'ids_ignorados' => $skippedPropertyIds,
            'emails_fora_do_padrao' => $unusualEmails,
        ]);
    }

    /**
     * Não rollback proposital: os usuários criados podem já ter definido senha,
     * recebido favoritos ou outros vínculos, e apagá-los destruiria dados legítimos.
     */
    public function down(): void
    {
        //
    }

    /**
     * Imprime o resumo no console e tenta registá-lo no log.
     *
     * O registo nunca pode derrubar a migração: em produção o usuário que roda o
     * deploy pode não ter permissão de escrita em storage/logs (foi exactamente o
     * que aconteceu no primeiro deploy desta migração).
     */
    private function reportSummary(array $summary): void
    {
        $payload = json_encode($summary, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // Sai no output do `php artisan migrate`, logo aparece no log do deploy.
        echo 'backfill_property_responsible_users: ' . $payload . PHP_EOL;

        try {
            Log::info('Migração backfill_property_responsible_users concluída.', $summary);
        } catch (\Throwable $e) {
            echo 'backfill_property_responsible_users: log indisponível (' . $e->getMessage() . ')' . PHP_EOL;
        }
    }

    /**
     * Usa o nome do responsável e, na falta dele, o nome da propriedade.
     */
    private function responsibleName(object $property, string $email): string
    {
        foreach ([$property->nome_responsavel, $property->name] as $candidate) {
            $candidate = trim((string) $candidate);

            if ($candidate !== '') {
                return $candidate;
            }
        }

        return $email;
    }

    /**
     * Descobre o id do perfil "Responsável" pelo nome, já que o id pode variar
     * entre ambientes (local x servidor).
     */
    private function responsibleProfileId(): ?int
    {
        if (! Schema::hasTable('access_profiles')) {
            return null;
        }

        $id = DB::table('access_profiles')
            ->where('nome', self::RESPONSIBLE_PROFILE)
            ->value('id');

        return $id === null ? null : (int) $id;
    }

    /**
     * Garante o vínculo do perfil sem duplicar (a tabela pivô não tem índice único).
     *
     * @return bool true quando o vínculo foi criado agora.
     */
    private function attachResponsibleProfile(int $userId, int $profileId): bool
    {
        $alreadyLinked = DB::table('user_has_access_profile')
            ->where('user_id', $userId)
            ->where('access_profile_id', $profileId)
            ->exists();

        if ($alreadyLinked) {
            return false;
        }

        DB::table('user_has_access_profile')->insert([
            'user_id' => $userId,
            'access_profile_id' => $profileId,
        ]);

        return true;
    }

    /**
     * Replica o que App\Models\User::booted() faz ao criar um usuário.
     */
    private function createDefaultFavoriteList(int $userId, bool $tableExists): void
    {
        if (! $tableExists) {
            return;
        }

        DB::table('favorite_lists')->insertOrIgnore([
            'user_id' => $userId,
            'name' => self::DEFAULT_FAVORITE_LIST,
            'is_default' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
