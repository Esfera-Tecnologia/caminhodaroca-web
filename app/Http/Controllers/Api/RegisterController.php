<?php

namespace App\Http\Controllers\Api;

use App\Enums\PartnerStatus;
use App\Enums\PreapprovedPartnerStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterPartnerRequest;
use App\Http\Requests\RegisterPersonalDataRequest;
use App\Http\Requests\RegisterCategoriesRequest;
use App\Http\Requests\RegisterFinishRequest;
use App\Models\AccessProfile;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Notifications\WelcomeNewUserNotification;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /**
     * Etapa 1: Validar dados pessoais
     */
    public function personalData(RegisterPersonalDataRequest $request): JsonResponse
    {
        $request->validated();
        // Apenas valida os dados, não armazena nada
        return response()->json([
            'message' => 'Etapa 1 concluída com sucesso.'
        ]);
    }

    /**
     * Etapa 2: Validar categorias/subcategorias
     */
    public function categories(RegisterCategoriesRequest $request): JsonResponse
    {
        $request->validated();
        // Apenas valida os dados, não armazena nada
        return response()->json([
            'message' => 'Etapa 2 concluída com sucesso.'
        ]);
    }

    /**
     * Finalização: Criar usuário com todos os dados validados
     */
    public function finish(RegisterFinishRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Cria o usuário
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'state' => $data['state'] ?? '',
            'age_range' => $data['ageRange'] ?? null,
            'travel_with' => $data['travelWith'] ?? null, // Agora é array
            'category_id' => $data['category'],
            'avatar' => 'https://picsum.photos/200/300',
            'registration_source' => 'api',
            'status' => 1,
        ]);
        $user->profiles()->attach(
            AccessProfile::where('nome', 'Visitante')->first()->id
        );
        // Associa as subcategorias se fornecidas
        if (!empty($data['subcategories'])) {
            $user->subcategories()->sync($data['subcategories']);
        }

        // Envia e-mail de boas-vindas
        $user->notify(new WelcomeNewUserNotification($user));

        // Cria o token de autenticação
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => null,
            'email' => $user->email,
            'state' => $user->state,
            'ageRange' => $user->age_range,
            'travelWith' => $user->travel_with, // Retorna como array
            'category' => $user->category_id,
            'subcategories' => $data['subcategories'] ?? [],
            'token' => $token,
        ], 201);
    }

    /**
     * @throws \Throwable
     */
    public function partner(RegisterPartnerRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['logo'] = $request->file('logo')->store('partners', 'public');
            $profile = AccessProfile::query()->firstOrCreate(['nome' => 'Parceiro'], [
                'descricao' => 'Responsável dos parceiros'
            ])->id;
            // Busca o usuário pelo e-mail; se não existir, cria.
            $user = User::where('email', $request->input('email'))->first();
            if (!$user) {
                try {
                    $user = User::create([
                        'email' => $request->input('email'),
                        'name' => $request->input('name'),
                        'password' => bcrypt(Str::random(12)),
                        'registration_source' => 'api',
                    ]);
                } catch (QueryException $e) {
                    // Corrida entre envios simultâneos: outro request já criou o usuário
                    // com o mesmo e-mail entre a consulta e o insert. Reaproveita o existente.
                    if ($e->errorInfo[1] === 1062) {
                        $user = User::where('email', $request->input('email'))->firstOrFail();
                    } else {
                        throw $e;
                    }
                }
            }

            // Evita duplicidade de parceiro: se o usuário já possui um cadastro, não cria outro.
            if ($user->partner()->exists()) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Este e-mail já possui um parceiro cadastrado aguardando aprovação. Em caso de dúvidas, entre em contato conosco.',
                ], 422);
            }

            $user->profiles()->syncWithoutDetaching($profile);
            if($user->wasRecentlyCreated){
                $user->notify(new WelcomeNewUserNotification($user));
            }
            $data['status'] = PartnerStatus::INATIVO;
            $partner = $user->partner()->create($data);
            $data['status'] = PreapprovedPartnerStatus::PENDING;
            $data['user_id'] = $user->id;
            $preapproved_partner = $partner->preapproved_partner()->create($data);
            $partner->cities()->sync($data['cities']);
            $preapproved_partner->cities()->sync($data['cities']);
            foreach ($data['events'] ?? [] as $eventData) {
                $event = $partner->events()->create($eventData);
                $eventData['event_id'] = $event->id;
                $preapproved_event = $preapproved_partner->events()->create($eventData);
                foreach ($eventData['images'] ?? [] as $image) {
                    $imageData['image'] = $image->store('partners/events', 'public');
                    $event->images()->create($imageData);
                    $preapproved_event->images()->create($imageData);
                }
            }
            DB::commit();
            return response()->json(['message' => "O parceiro foi cadastrado com sucesso!"]);
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e->getMessage(),  $e->getTrace());
            return response()->json(['message' => "Não foi possível cadastrar o parceiro: " . $e->getMessage()], 500);
        }
    }
}
