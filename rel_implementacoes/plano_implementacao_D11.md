# Plano de Implementação - D11: CRUD Eventos - WEB

**Tarefa:** D11 - Crud Eventos - WEB Menu
**Data:** 08 de Maio de 2026
**Responsável:** Antigravity (AI Assistant)

## 1. Visão Geral
Implementação completa do módulo de eventos, incluindo menu lateral dinâmico, listagem com filtros e formulário de cadastro/edição com suporte a upload de imagens e relacionamentos.

## 2. Etapas Técnicas

### 2.1. Banco de Dados (Database)
- **Ajuste na Tabela `events`:** Criar migração para adicionar:
    - `start_date` (timestamp) - Data Inicial
    - `end_date` (timestamp) - Data Final
    - `state_id` (FK) - Estado
    - `city_id` (FK) - Cidade
    - `organization` (string) - Organização
    - `full_description` (text) - Sobre o Evento
    - `image` (string) - Foto de Capa
    - `is_highlight` (boolean) - Destaque (default: false)
- **Tabela Intermediária `event_has_property`:** Criar tabela para o relacionamento muitos-para-muitos entre Eventos e Propriedades.
- **Menu e Permissões:** 
    - Criar migração/seeder para inserir "Eventos" na tabela `menus`.
    - Posicionar `ordem` abaixo do menu "Parceiros".
    - Vincular permissões de visualização/edição ao perfil Administrador.

### 2.2. Backend (Laravel)
- **Model `Event`:** 
    - Configurar `$fillable`.
    - Implementar relacionamentos: `property()`, `state()`, `city()`.
- **Controller `EventController`:**
    - `index()`: Listagem padrão com colunas solicitadas.
    - `create()`/`edit()`: Carregar dependências (Properties, States).
    - `store()`/`update()`: Validação rigorosa e tratamento de upload de `image`.
- **Rotas:** Adicionar `Route::resource('eventos', EventController::class)`.

### 2.3. Frontend (Blade)
- **Listagem:** Seguir padrão de tabelas do sistema (DataTables) com colunas:
    - Título | Data | Local | Destaque | Propriedade | Ações
- **Formulário:** 
    - Campos obrigatórios demarcados com `*`.
    - Select2 para seleção de Propriedades e Cidades.
    - Input de data nativo ou biblioteca do sistema.

## 3. Critérios de Aceite
- Menu "Eventos" funcional e bem posicionado.
- Listagem exibindo dados corretamente.
- Cadastro salvando todos os campos e processando a imagem.
- Validação impedindo campos nulos nos itens obrigatórios.

---
**Aguardando aprovação para iniciar a Etapa 2.1.**
