# Plano de Implementação - D12: Sistema de Múltiplas Listas de Favoritos (API)

**Tarefa:** D12 - Lista de Favoritos
**Data:** 09 de Maio de 2026
**Responsável:** Antigravity (AI Assistant)

## 1. Visão Geral
Evolução do sistema de favoritos para suportar múltiplas listas personalizadas por usuário. Uma propriedade poderá pertencer a várias listas simultaneamente.

## 2. Detalhamento Técnico

### 2.1. Banco de Dados (Migrações)
- **Tabela `favorite_lists`**:
    - `id`, `user_id` (FK), `name`, `is_default` (boolean, default: false), `timestamps`.
    - Unique constraint em `(user_id, name)`.
- **Tabela `favorite_list_properties`**:
    - `id`, `favorite_list_id` (FK), `property_id` (FK), `timestamps`.
- **Script de Migração de Dados**:
    - Criar a lista padrão "Favoritos" com `is_default = true` para **todos** os usuários cadastrados no banco de dados.
    - Migrar os vínculos da tabela antiga `user_favorite_properties` para a nova estrutura.

### 2.2. Automação e Proteção
- **Model User**: Implementar um listener no evento `created` para que todo novo usuário receba automaticamente sua lista "Favoritos" (`is_default = true`).
- **Middleware/Logic**: Bloquear a exclusão (`DELETE`) de qualquer lista que possua a flag `is_default = true`.

### 2.3. Endpoints de API (Novos)
- `GET /api/favorite-lists`: Lista todas as coleções do usuário.
- `POST /api/favorite-lists`: Cria uma nova lista (Valida nome único por usuário).
- `DELETE /api/favorite-lists/{id}`: Remove a lista e desvincula as propriedades contidas nela (Bloqueado para listas default).

### 2.4. Alterações em Endpoints Existentes
- `POST /api/properties/{id}/favorite`:
    - Parâmetro: `list_ids` (array de IDs).
    - Lógica: Sincroniza a propriedade com as listas informadas.
- `GET /api/properties`:
    - Adicionar `favorite_count`: Quantidade de listas em que a propriedade está para o usuário logado.
    - Adicionar `in_lists`: IDs das listas onde a propriedade foi adicionada.

### 2.5. Regras de Negócio
- **Padrão:** Todo usuário tem a lista "Favoritos" por padrão e ela é imutável/irremovível.
- **Unicidade:** Nomes de lista não podem se repetir para o mesmo usuário.
- **Exclusão:** Ao excluir uma lista personalizada, as propriedades param de ser favoritas apenas naquela lista. Se a propriedade estiver em outras listas, ela permanece como favorita nelas.
- **Edição:** Não haverá edição de nome; o usuário deve excluir e recriar se desejar mudar o título.

## 3. Critérios de Aceite
- Todos os usuários (antigos e novos) possuem a lista "Favoritos".
- Favoritos antigos migrados corretamente.
- Tentativa de excluir a lista "Favoritos" retorna erro de permissão.
- Criação de listas com nomes duplicados bloqueada (erro 422).
- Mobile recebendo o array de IDs das listas nas propriedades.

---
**Aguardando aprovação para iniciar a implementação das migrações.**
