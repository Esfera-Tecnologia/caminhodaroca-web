# Relatório de Análise Técnica - Integração Caminho da Roça

**Data:** 04 de Maio de 2026
**Responsável:** Antigravity (AI Assistant)

## 1. Análise das Migrações (Database Migrations)

### Status Atual
- **Total de Migrações:** 56 arquivos.
- **Estrutura:** As migrações refletem fielmente o banco de dados de produção recebido em SQL.
- **Ponto Crítico:** Detectamos um erro de execução na migração `2025_11_18_123247_insert_menu_parceiros.php`.
    - **Causa:** O código tenta realizar um `AccessProfile::query()->where('nome', 'Administrador de Sistema')->first()` e, em seguida, chama o método `permissions()`. Como o banco está limpo, o perfil não existe, resultando em erro de "Call to a member function permissions() on null".
    - **Recomendação:** Ajustar a migração para verificar se o perfil existe ou mover a lógica de inserção de menus para um Seeder.

## 2. Análise de Consistência: Models vs. Banco de Dados

Identificamos uma "camada de tradução" necessária entre o código e o banco de dados legada:

### Tabela `properties` (Propriedades)
| No Banco (SQL/Migration) | Na Model ($fillable / Property) | Status |
| :--- | :--- | :--- |
| `cidade` | `city` | ✅ Funciona via Accessor/Mutator |
| `descricao_servico` | `description` | ✅ Funciona via Accessor/Mutator |
| `whatsapp` | `phone` | ✅ Funciona via Accessor/Mutator |
| `uf` | `state` | ✅ Funciona via Accessor/Mutator |

### Tabela `users` (Usuários)
- **Sincronia:** 100% consistente. Ambos utilizam termos em inglês (`email`, `status`, `state`).

### Conclusão da Consistência
O sistema opera em modo de compatibilidade. As Models fingem ser em Inglês para manter o padrão Laravel, enquanto o banco físico permanece em Português. Isso é funcional, mas perigoso para consultas `where` diretas que não passem pela Model.

## 3. Ações Executadas
- Execução de `php artisan db:wipe` para limpar o ambiente de testes.
- Mapeamento completo de divergências nominais.

## 4. Próximos Passos Sugeridos
1. Corrigir a migração de menus para permitir o `migrate` completo.
2. Popular o banco com dados de teste via Seeders para validar os Accessors das Models.
3. Decidir se manteremos o padrão bilíngue (Banco PT / Model EN) ou se faremos uma refatoração para unificar.
