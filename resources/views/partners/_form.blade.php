<div class="row g-4">
    <div class="col-12">
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">Logotipo *</label>
                <input
                  accept=".jpg,.jpeg,.png,.gif"
                  onchange="previewLogo(this)"
                  class="form-control"
                  type="file"
                  name="logo"
                  id="logo"
                  required />
                <img id="preview-logo"
                    src="{{ isset($partner->logo) ? asset('storage/' . $partner->logo) : asset('assets/teste3.png') }}"
                    class="preview-img mt-2"
                    alt="Preview Logo" />
                <div id="logo-error" class="text-danger mt-1"></div>
            </div>
            <div class="col">
                <div class="row gy-3">
                    <div class="col-md-6">
                        <label class="form-label">Nome do Parceiro *</label>
                        <input type="text" class="form-control" required name="name" id="name" value="{{ old('name', $partner->name ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status *</label>
                        <select class="form-select" required name="status" id="status">
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
                    <div class="col-md-6">
                        <label class="form-label">E-mail do Parceiro *</label>
                        <input type="email" class="form-control" required name="email" id="email"
                            value="{{ old('email', $partner->email ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Categoria *</label>
                        <select class="form-select @error('partner_category_id') is-invalid @enderror" required name="partner_category_id" id="partner_category_id">
                            <option value="">Selecione</option>
                            @foreach(\App\Models\PartnerCategory::active()->orderBy('titulo')->get() as $category)
                                <option value="{{ $category->id }}"
                                        data-experiencias="{{ $category->experiencias_oferecidas ? 1 : 0 }}"
                                    {{ (old('partner_category_id', $partner->partner_category_id ?? '') == $category->id) ? 'selected' : '' }}>
                                    {{ $category->titulo }}
                                </option>
                            @endforeach
                        </select>
                        @error('partner_category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label">Descrição *</label>
        <textarea class="form-control" rows="4" required
                  name="description" id="description">{!! old('description', $partner->description ?? '') !!}</textarea>
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
        <label class="form-label">
            Municípios *
            <a href="#" id="cities-toggler" style="font-size: 0.8rem">Selecionar todos</a>
        </label>
        <select class="form-select select2" name="cities[]" id="cities" multiple required>
            <option value="disabled" disabled>Selecione</option>
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
        <textarea id="routes" class="form-control" name="routes" rows="3" maxlength="1000">{!! old('routes', $partner->routes ?? '') !!}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">Circuitos</label>
        <textarea id="circuits" class="form-control" name="circuits" rows="3" maxlength="1000">{!! old('circuits', $partner->circuits ?? '') !!}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">Atrativos</label>
        <textarea id="attractions" class="form-control" name="attractions" rows="3" maxlength="1000">{!! old('attractions', $partner->attractions ?? '') !!}</textarea>
    </div>
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <label class="form-label mb-0">Eventos</label>
            <button type="button" class="btn btn-outline-success btn-sm" id="add-evento">
                <i class="fas fa-plus me-1"></i>Adicionar evento
            </button>
        </div>
        <p class="text-muted small mb-3">Informe os dados de cada evento, incluindo um link e uma imagem.</p>

        <div id="eventos-container">
            @foreach($partner->events as $event)
                @include('partners.partials.events', ['event' => $event])
            @endforeach
        </div>
    </div>

</div>


<template id="evento-template">
    <div class="card shadow-sm border event-item">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="fw-semibold mb-0">Evento <span class="evento-index"></span></h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-evento" aria-label="Remover evento">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Imagem do Evento</label>
                    <input
                        accept=".jpg,.jpeg,.png,.gif"
                        onchange="previewLogo(this)"
                        class="form-control"
                        type="file"
                        name="new_event_imagem[]" />
                    <img src="{{ asset('assets/teste3.png') }}"
                        class="preview-img mt-2" />
                    </div>
                <div class="col">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Nome do Evento *</label>
                            <input type="text" class="form-control" name="new_event_name[]" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Link do Evento</label>
                            <input type="url" class="form-control" name="new_event_link[]" placeholder="https://">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descrição do Evento *</label>
                            <textarea class="form-control" rows="2" name="new_event_description[]" placeholder="Compartilhe os principais detalhes"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<div id="custom-alert" class="alert d-none" role="alert"></div>

@push('scripts')
    <script>
        function previewLogo(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => $(input).next('img').attr('src', e.target.result);
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Rotas/Circuitos/Atrativos: sempre visíveis, obrigatórios conforme a categoria selecionada
        function atualizarExperienciasObrigatorias() {
            const experiencias = $('#partner_category_id').find('option:selected').data('experiencias') == 1;
            $('#routes, #circuits, #attractions').each(function () {
                if (experiencias) {
                    $(this).attr('required', 'required');
                } else {
                    $(this).removeAttr('required');
                }
            });
        }
        $(document).ready(function () {
            atualizarExperienciasObrigatorias();
            $('#partner_category_id').on('change', atualizarExperienciasObrigatorias);
        });

        document.addEventListener('DOMContentLoaded', function () {
            const validator = (window.caminhoRocaValidators || {})['form-parceiro'];
            const eventosContainer = document.getElementById('eventos-container');
            const addEventoBtn = document.getElementById('add-evento');
            const eventoTemplate = document.getElementById('evento-template');

            // Rotas/Circuitos/Atrativos: obrigatórios conforme a categoria (mesmo padrão do cadastro)
            if (validator) {
                const requerExperiencias = function () {
                    const opt = $('#partner_category_id').find('option:selected');
                    return opt.length > 0 && opt.data('experiencias') == 1;
                };
                ['routes', 'circuits', 'attractions'].forEach(function (id) {
                    validator.addField('#' + id, [
                        {
                            validator: function () {
                                if (!requerExperiencias()) return true;
                                const el = document.getElementById(id);
                                return el && el.value && el.value.trim().length > 0;
                            },
                            errorMessage: 'Este campo é obrigatório.'
                        }
                    ]);
                });
            }

            // Registra nome/descrição de um evento no just-validate (mesmo padrão do cadastro)
            function registrarEventoParceiro(item) {
                if (!validator) return;
                const $item = $(item);
                const inputNome = $item.find('input[name$="[name]"], input[name="new_event_name[]"]')[0];
                const inputDesc = $item.find('textarea[name$="[description]"], textarea[name="new_event_description[]"]')[0];
                const inputLink = $item.find('input[name$="[url]"], input[name="new_event_link[]"]')[0];
                const inputImg = $item.find('input[type="file"]')[0];
                if (!inputNome || !inputDesc) return;

                // Evento totalmente vazio → não cobra nome/descrição
                const eventoVazio = function () {
                    return !(inputNome.value || '').trim()
                        && !(inputDesc.value || '').trim()
                        && !(inputLink ? (inputLink.value || '').trim() : '')
                        && (inputImg ? inputImg.files.length : 0) === 0;
                };

                validator.addField(inputNome, [
                    {
                        validator: () => eventoVazio() || (inputNome.value || '').trim().length > 0,
                        errorMessage: 'Este campo é obrigatório.'
                    }
                ]);
                validator.addField(inputDesc, [
                    {
                        validator: () => eventoVazio() || (inputDesc.value || '').trim().length > 0,
                        errorMessage: 'Este campo é obrigatório.'
                    }
                ]);
            }

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

            function adicionarEvento() {
                const clone = eventoTemplate.content.cloneNode(true);
                eventosContainer.appendChild(clone);
                const itens = eventosContainer.querySelectorAll('.event-item');
                registrarEventoParceiro(itens[itens.length - 1]);
                updateIndices();
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
                if (validator) {
                    $(item).find('input[name$="[name]"], textarea[name$="[description]"], input[name="new_event_name[]"], textarea[name="new_event_description[]"]').each(function () {
                        validator.removeField(this);
                    });
                }
                item.remove();
                updateIndices();
            });

            // Registra os eventos já existentes (carregados do banco)
            eventosContainer.querySelectorAll('.event-item').forEach(function (item) {
                registrarEventoParceiro(item);
            });

            updateIndices();
        });
        $(function () {
            function getAllOptionValues() {
                return $('#cities').find('option:not(:disabled)').map(function () {
                    return this.value;
                }).get().filter(v => v !== '' && v != null);
            }
            function getCurrentValues() {
                return $('#cities').val() || [];
            }
            function isAllSelected() {
                const all = getAllOptionValues();
                const cur = getCurrentValues();
                return all.length > 0 && cur.length === all.length;
            }
            function updateButtonText() {
                $("#cities-toggler").text(isAllSelected() ? 'Remover todos' : 'Selecionar todos');
            }
            updateButtonText();
            $('#cities').on('change', function () {
                updateButtonText();
            });
            $("#cities-toggler").on('click', function () {
                const allValues = getAllOptionValues();
                if (isAllSelected()) {
                    $('#cities').val(null).trigger('change');
                } else {
                    $('#cities').val(allValues).trigger('change');
                }
                updateButtonText();
            });
        });
    </script>
@endpush
