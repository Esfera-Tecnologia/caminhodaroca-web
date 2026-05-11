# Plano de Implementação: Check-in por QRCode (Visitas Realizadas) - D12

Este documento descreve o plano técnico para a implementação do sistema de check-in em propriedades rurais via QRCode, com validação de distância geográfica e exibição de indicadores de visita no aplicativo.

## 1. Objetivos
*   Permitir que usuários logados registrem uma visita a uma propriedade lendo um QRCode no local.
*   Garantir que o usuário esteja fisicamente presente (raio de 1000m da propriedade).
*   Impedir múltiplos check-ins na mesma propriedade pelo mesmo usuário.
*   Exibir tags de "Visita Realizada" nas listagens e detalhes das propriedades.

## 2. Mudanças no Banco de Dados (Migration)
Criaremos a tabela `property_visits` para persistir as visitas.

| Coluna | Tipo | Descrição |
| :--- | :--- | :--- |
| `id` | BigInt (PK) | Identificador único |
| `user_id` | ForeignID | Referência ao usuário logado |
| `property_id` | ForeignID | Referência à propriedade visitada |
| `checkin_latitude` | Decimal(10,8) | Latitude capturada no momento do check-in |
| `checkin_longitude` | Decimal(11,8) | Longitude capturada no momento do check-in |
| `created_at` | Timestamp | Data e hora da visita |

*   **Índice Único**: `['user_id', 'property_id']` para garantir que a visita seja registrada apenas uma vez.

## 3. Implementação Backend

### 3.1. Novo Model `PropertyVisit`
*   Model simples para gerenciar os registros de visitas.
*   Relacionamentos: `belongsTo(User)` e `belongsTo(Property)`.

### 3.2. Lógica de Geolocalização (Cálculo de Distância)
Utilizaremos a fórmula de Haversine para calcular a distância entre dois pontos (Latitude/Longitude) diretamente no PHP:
*   **Ponto A**: Coordenadas da propriedade (armazenadas na tabela `properties`).
*   **Ponto B**: Coordenadas enviadas pelo celular do usuário via API.
*   **Limite**: 1000 metros.

### 3.3. API de Check-in
`POST /api/properties/{id}/checkin`
*   **Input**: `latitude`, `longitude`.
*   **Processamento**:
    1. Verifica se o usuário está autenticado (Middleware `auth:sanctum`).
    2. Busca a propriedade e suas coordenadas.
    3. Calcula a distância.
    4. Se distância > 1000m, retorna erro `403` com mensagem "Fora do raio permitido".
    5. Verifica se o usuário já fez check-in (evita duplicidade).
    6. Salva o registro na tabela `property_visits`.
*   **Output**: Sucesso ou Erro detalhado.

### 3.4. Atualização de Endpoints de Propriedades
*   **`GET /api/properties` (Listagem)** e **`GET /api/properties/{id}` (Detalhes)**:
    *   Inclusão do campo booleano `isVisited` no retorno JSON.
    *   Valor será `true` se houver um registro na tabela `property_visits` para o par (User, Property).

## 4. Documentação API (Swagger)
Atualização do arquivo `api.yaml`:
*   Inclusão do endpoint `/api/properties/{id}/checkin`.
*   Adição do campo `isVisited` no esquema de resposta das propriedades.

## 5. Cronograma Estimado
1.  **Migração e Model**: 30 min.
2.  **Lógica de Distância e Controller de Check-in**: 1h 30min.
3.  **Refatoração do Resource de Propriedades (Tags)**: 1h.
4.  **Testes e Documentação**: 30 min.

---

**Aguardo sua aprovação para iniciar a implementação.**
