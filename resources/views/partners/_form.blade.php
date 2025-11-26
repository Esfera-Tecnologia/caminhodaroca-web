<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">Nome do Parceiro *</label>
        <input type="text" class="form-control" required name="name" value="{{ old('name', $partner->name ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">E-mail do Parceiro *</label>
        <input type="email" class="form-control" required name="email"
               value="{{ old('email', $partner->email ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Logotipo *</label>
        <input type="file" class="form-control" accept="image/*" required
               name="logo"
               data-original-value="{{ old('logo', $partner->logo ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Status *</label>
        <select class="form-select" required name="status">
            <option value="">Selecione</option>
            @php
                if($partner instanceof \App\Models\Partner)
                    $status_enum = \App\Enums\PartnerStatus::cases();
                else
                    $status_enum = \App\Enums\PreapprovedPartnerStatus::cases();
            @endphp
            @foreach($status_enum as $status)
                <option value="{{ $status->value }}"
                    {{ (old('status', $partner->status->value ?? '') == $status->value) ? 'selected' : '' }}>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Descrição *</label>
        <textarea class="form-control" rows="4" required
                  name="description">{!! old('description', $partner->description ?? '') !!}</textarea>
    </div>
</div>

<hr class="my-4">

<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">Instagram</label>
        <input type="text" class="form-control" placeholder="@parceiro" name="instagram"
               value="{{ old('instagram', $partner->instagram ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Site</label>
        <input type="url" class="form-control" placeholder="https://" name="site"
               value="{{ old('site', $partner->site ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Municípios *</label>
        <select class="form-select select2" name="cities[]" id="cities" multiple required>
            <option value="">Selecione</option>
            @foreach(\App\Models\City::query()->pluck('name', 'id') as $key=>$cidade)
                <option value="{{ $key }}"
                    {{ (collect(old('cities', $partner->cities->pluck('id')->toArray() ?? []))->contains($key)) ? 'selected':'' }}>
                    {{ $cidade }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<hr class="my-4">

<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">Rotas</label>
        <textarea class="form-control" name="routes" rows="3">{!! old('routes', $partner->routes ?? '') !!}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">Circuitos</label>
        <textarea class="form-control" name="circuits" rows="3">{!! old('circuits', $partner->circuits ?? '') !!}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">Atrativos</label>
        <textarea class="form-control" name="attractions" rows="3">{!! old('attractions', $partner->attractions ?? '') !!}</textarea>
    </div>
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <label class="form-label mb-0">Eventos</label>
            <button type="button" class="btn btn-outline-success btn-sm" id="add-evento">
                <i class="fas fa-plus me-1"></i>Adicionar evento
            </button>
        </div>
        <p class="text-muted small mb-3">Informe os dados de cada evento, incluindo um link e uma imagem.</p>

        @foreach($partner->events as $event)
            @include('partners.partials.events', ['event' => $event])
        @endforeach
    </div>

</div>


<template id="evento-template">
    <div class="card shadow-sm border event-item">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="fw-semibold mb-0">Evento <span class="evento-index"></span></h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-evento" onclick="removeEvent($(this))" aria-label="Remover evento">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nome do Evento *</label>
                    <input type="text" class="form-control" name="new_event_name[]" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Link do Evento</label>
                    <input type="url" class="form-control" name="new_event_link[]" placeholder="https://" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Descrição do Evento</label>
                    <textarea class="form-control" rows="2" name="new_event_description[]"
                              placeholder="Compartilhe os principais detalhes"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Imagem do Evento</label>
                    <input type="file" class="form-control" name="new_event_imagem[]" accept="image/*" required>
                </div>
            </div>
        </div>
    </div>
</template>

<div id="custom-alert" class="alert d-none" role="alert"></div>

@push('scripts')
    <script>

        document.addEventListener('DOMContentLoaded', function () {
            const eventosContainer = document.getElementById('eventos-container');
            const addEventoBtn = document.getElementById('add-evento');
            const eventoTemplate = document.getElementById('evento-template');

            if (!eventosContainer || !addEventoBtn || !eventoTemplate) {
                return;
            }

            function updateIndices() {
                const itens = eventosContainer.querySelectorAll('.event-item');
                itens.forEach((item, index) => {
                    const marcador = item.querySelector('.evento-index');
                    if (marcador) {
                        marcador.textContent = index + 1;
                    }
                });
            }

            function toggleRemoveButtons() {
                const itens = eventosContainer.querySelectorAll('.event-item');
                itens.forEach((item) => {
                    const botaoRemover = item.querySelector('.remove-evento');
                    if (botaoRemover) {
                        const esconder = itens.length === 1;
                        botaoRemover.classList.toggle('d-none', esconder);
                        botaoRemover.disabled = esconder;
                    }
                });
            }

            function adicionarEvento() {
                const clone = eventoTemplate.content.cloneNode(true);
                eventosContainer.appendChild(clone);
                updateIndices();
                toggleRemoveButtons();
            }

            addEventoBtn.addEventListener('click', function () {
                adicionarEvento();
            });

            eventosContainer.addEventListener('click', function (event) {
                const botao = event.target.closest('.remove-evento');
                if (!botao) {
                    return;
                }
                const item = botao.closest('.event-item');
                if (!item) {
                    return;
                }
                const itens = eventosContainer.querySelectorAll('.event-item');
                if (itens.length === 1) {
                    return;
                }
                item.remove();
                updateIndices();
                toggleRemoveButtons();
            });

            updateIndices();
            toggleRemoveButtons();
        });


        function removeEvent(button){
            const item = button.closest('.eventos-container');
            item.remove();
        }




    </script>
@endpush
