# Relatório de Erros de Migração

**Data:** 2026-04-28
**Resumo:** De 56 migrações analisadas, apenas 1 apresentou erro.

## Erro Detectado
- **Arquivo:** `2025_11_18_123247_insert_menu_parceiros.php`
- **Mensagem:** `Call to a member function permissions() on null`
- **Localização:** Linha 24
- **Causa Provável:** A migração tenta buscar um perfil de acesso chamado "Administrador de Sistema" (`AccessProfile::query()->where('nome', 'Administrador de Sistema')->first()`), mas como o banco foi criado do zero e ainda não tem dados, esse perfil não existe, retornando `null`. 

## Status do Banco de Dados
Apesar deste erro pontual, todas as outras tabelas de estrutura foram criadas corretamente, pois forcei a execução individual de cada arquivo. O banco está pronto para uso estrutural, faltando apenas o ajuste nesta lógica de inserção de dados.
