# Plano de Implementação - D11: API de Eventos (Mobile)

**Tarefa:** D11 - Lista de Eventos - API
**Data:** 08 de Maio de 2026
**Responsável:** Antigravity (AI Assistant)

## 1. Visão Geral
Criação de endpoints de API para suprir as necessidades do aplicativo mobile, incluindo carrossel de destaques na home, listagem geral com filtros (próximos, expirados, busca) e suporte para visualização em calendário.

## 2. Detalhamento Técnico

### 2.1. Rotas (API Routes)
Novas rotas em `routes/api.php`:
- `GET /api/events`: Listagem geral com suporte a queries de filtro.
- `GET /api/events/{id}`: Detalhes específicos de um evento.
- `GET /api/events/calendar-stats`: Dados consolidados para marcação no calendário (bolinhas e contadores).

### 2.2. Parâmetros de Filtro (`/api/events`)
| Parâmetro | Tipo | Descrição |
| :--- | :--- | :--- |
| `is_highlight` | Boolean | Filtra apenas eventos marcados como destaque (Home). |
| `filter` | String | `upcoming` (próximos 7 dias) ou `expired` (já realizados). |
| `search` | String | Busca textual (LIKE) por nome, estado ou cidade. |
| `date` | Date | Eventos em uma data específica (clique no calendário). |
| `month`/`year` | Int | Filtro de período para o calendário. |

### 2.3. Lógica de Negócio (Controller)
- **Ordenação:** Eventos futuros ordenados por proximidade (ASC); Eventos expirados por data mais recente (DESC).
- **Tag de Expirado:** Inclusão de um atributo booleano `expired` no retorno JSON para controle de UI no mobile.
- **Busca por Local:** JOIN com as tabelas `states` e `cities` para permitir busca por nome legível.
- **Calendário:** Agrupamento por data (`GROUP BY start_date`) para retornar o contador de eventos por dia.

### 2.4. Formatação (Resources)
- Criação do `EventResource` para garantir que:
    - `image_url` retorne o caminho completo do storage.
    - Datas sejam formatadas no padrão ISO para o mobile.
    - Relacionamentos (Propriedades/Parceiros) sejam incluídos de forma otimizada.

### 2.5. Documentação (Swagger/OpenAPI)
- Atualizar o arquivo `api.yaml` na raiz do projeto para incluir:
    - Definição dos novos endpoints de eventos.
    - Esquemas de dados (`Event`, `EventDetail`, `CalendarStat`).
    - Exemplos de requisição e resposta para facilitar o desenvolvimento mobile.

## 3. Critérios de Aceite
- API de destaques retorna os eventos marcados corretamente.
- Filtro "Próximos" retorna apenas eventos dos próximos 7 dias.
- Filtro "Expirados" retorna eventos passados com a tag correspondente.
- Busca por nome ou local funciona conforme o esperado (LIKE).
- Endpoint de calendário retorna os dias corretos com seus respectivos contadores.
- **Documentação Swagger atualizada e refletindo o comportamento real da API.**

---
**Aguardando aprovação do usuário para iniciar a implementação.**
