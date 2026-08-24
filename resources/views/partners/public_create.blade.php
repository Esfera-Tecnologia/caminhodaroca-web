@extends('layouts.login')

@section('title', 'Cadastro de Instituição/Parceiro - Caminhos da Roça')

@section('content')
    <form action="{{ route('partners.public.store') }}" id="form-parceiro-publico" method="POST" novalidate
          enctype="multipart/form-data">
        @csrf
        <div class="d-flex vh-100">
            <div class="bg-image d-none d-md-block col"></div>

            <div class="d-flex col-md-7 col-12 justify-content-center bg-white p-4 px-5 overflow-auto">
                <div class="w-100 px-4 mx-5">

                    <div class="text-center mb-5 d-flex flex-column">
                        <div class="w-auto mb-3">
                            <img src="{{ asset('assets/LogoCaminhodaRoca.png') }}" alt="Caminhos da Roça"
                                 class="img-fluid" style="max-height: 140px;">
                        </div>
                        <h2 class="fw-bold">Cadastro de Instituição / Parceiro</h2>
                        <span>Preencha os campos abaixo para cadastrar sua instituição ou parceiro de apoio ao turismo no Caminho da Roça.</span>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger not-fade">{{ session('error') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger not-fade">
                            <strong>Verifique os campos abaixo:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $erro)
                                    <li>{{ $erro }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-4">
                        <div class="col-md-3">
                            <label class="form-label">Logotipo *</label>
                            <input accept=".jpg,.jpeg,.png,.gif" onchange="previewLogo(this)" class="form-control"
                                   type="file" name="logo" id="logo" required />
                            <img id="preview-logo" src="{{ asset('assets/teste3.png') }}" class="preview-img mt-2"
                                 alt="Preview Logo" />
                            @error('logo')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-9">
                            <div class="row gy-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nome da Instituição/Parceiro *</label>
                                    <input type="text" class="form-control" required name="name" id="name"
                                           value="{{ old('name') }}">
                                    @error('name')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">E-mail *</label>
                                    <input type="email" class="form-control" required name="email" id="email"
                                           value="{{ old('email') }}">
                                    @error('email')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row gy-3">
                                <div class="col-md-12">
                                    <label for="partner_category_id" class="form-label">Categoria *</label>
                                    <select name="partner_category_id" id="partner_category_id" class="form-select" required>
                                        <option value="">Selecione a categoria</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}"
                                                    data-experiencias="{{ $category->experiencias_oferecidas ? 1 : 0 }}"
                                                    @selected(old('partner_category_id') == $category->id)>
                                                {{ $category->titulo }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('partner_category_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="form-label">Descrição *</label>
                        <textarea class="form-control" rows="4" required name="description" id="description">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Instagram</label>
                            <input type="text" class="form-control" placeholder="@instituicao" name="instagram"
                                   value="{{ old('instagram') }}">
                            @error('instagram')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Site</label>
                            <input type="url" class="form-control" placeholder="https://" name="site"
                                   value="{{ old('site') }}">
                            @error('site')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Municípios de atuação *</label>
                            <select class="form-select select2" name="cities[]" id="cities" multiple required>
                                <option value="">Selecione os municípios</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}"
                                        @selected(in_array($city->id, old('cities', [])))>{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @error('cities')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Experiências oferecidas: sempre visíveis; obrigatórias conforme a categoria selecionada -->
                    <div id="experiencias-section">
                        <h6 class="fw-semibold mb-1">Experiências Oferecidas</h6>
                        <p class="text-muted small mb-3">Conte sobre rotas, circuitos e atrativos relacionados à sua instituição.</p>
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label">Rotas</label>
                                <textarea class="form-control" name="routes" id="routes" rows="3"
                                          maxlength="1000">{{ old('routes') }}</textarea>
                                @error('routes')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Circuitos</label>
                                <textarea class="form-control" name="circuits" id="circuits" rows="3"
                                          maxlength="1000">{{ old('circuits') }}</textarea>
                                @error('circuits')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Atrativos</label>
                                <textarea class="form-control" name="attractions" id="attractions" rows="3"
                                          maxlength="1000">{{ old('attractions') }}</textarea>
                                @error('attractions')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label mb-0">Eventos</label>
                            <button type="button" class="btn btn-outline-success btn-sm" id="add-evento">
                                <i class="fas fa-plus me-1"></i>Adicionar evento
                            </button>
                        </div>
                        <p class="text-muted small mb-3">Informe os dados de cada evento, incluindo um link e uma imagem.</p>
                        <div id="eventos-container"></div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="terms" id="terms" value="1" required @checked(old('terms'))>
                        <label class="form-check-label" for="terms">
                            Eu concordo com os <a href="#" data-bs-toggle="modal" data-bs-target="#modalTermos" role="button">Termos de Uso</a>
                        </label>
                        @error('terms')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end mt-4 mb-5 pb-5">
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-success" id="btn-enviar-cadastro" disabled>Enviar Cadastro</button>
                    </div>

                </div>
            </div>
        </div>
    </form>

    <!-- Modal Termos de Uso -->
    <div class="modal fade" id="modalTermos" tabindex="-1" aria-labelledby="modalTermosLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTermosLabel">Termos de Uso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe src="https://senar-rio.com.br/caminhodaroca/termo-de-uso/"
                            title="Termos de Uso"
                            class="w-100"
                            style="height: 60vh; border: 0; display: block;"
                            loading="lazy"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <template id="evento-template">
        <div class="card shadow-sm border event-item mb-3" data-index="__INDEX__">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h6 class="fw-semibold mb-0">Evento <span class="evento-index"></span></h6>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeEvent(this)"
                            aria-label="Remover evento">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Imagem do Evento</label>
                        <input accept=".jpg,.jpeg,.png,.gif" class="form-control" type="file"
                               name="events[__INDEX__][images][]" onchange="previewEventImage(this)" />
                        <img class="event-image-preview preview-img mt-2" src="{{ asset('assets/teste3.png') }}"
                             alt="Preview Imagem do Evento" />
                    </div>
                    <div class="col">
                        <div class="row gy-2">
                            <div class="col">
                                <label class="form-label">Nome do Evento *</label>
                                <input type="text" class="form-control" id="event-__INDEX__-name" name="events[__INDEX__][name]" required>
                            </div>
                            <div class="col">
                                <label class="form-label">Link do Evento</label>
                                <input type="url" class="form-control" name="events[__INDEX__][url]" placeholder="https://">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Descrição do Evento *</label>
                                <textarea class="form-control" id="event-__INDEX__-description" name="events[__INDEX__][description]" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    @push('scripts')
        <script>
            let eventoIndex = 0;

            function previewLogo(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        $('#preview-logo').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            // Preview da imagem do evento (como a logo)
            function previewEventImage(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        $(input).closest('.event-item').find('.event-image-preview').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            function addEvento() {
                const index = eventoIndex++;
                const html = $('#evento-template').html().replace(/__INDEX__/g, index);
                $('#eventos-container').append(html);

                // Registra nome/descrição do evento no just-validate (mesmo padrão dos demais campos)
                const validator = (window.caminhoRocaValidators || {})['form-parceiro-publico'];
                if (!validator) return;

                const $item = $('#eventos-container .event-item').last();
                const inputNome = $item.find('input[name$="[name]"]')[0];
                const inputDesc = $item.find('textarea[name$="[description]"]')[0];
                const inputLink = $item.find('input[name$="[url]"]')[0];
                const inputImg = $item.find('input[type="file"]')[0];

                // Evento totalmente vazio → não cobra nome/descrição (será descartado no servidor)
                const eventoVazio = function () {
                    return !(inputNome.value || '').trim()
                        && !(inputDesc.value || '').trim()
                        && !(inputLink.value || '').trim()
                        && (inputImg.files.length || 0) === 0;
                };

                validator.addField('#event-' + index + '-name', [
                    {
                        validator: () => eventoVazio() || (inputNome.value || '').trim().length > 0,
                        errorMessage: 'Este campo é obrigatório.'
                    }
                ]);
                validator.addField('#event-' + index + '-description', [
                    {
                        validator: () => eventoVazio() || (inputDesc.value || '').trim().length > 0,
                        errorMessage: 'Este campo é obrigatório.'
                    }
                ]);
            }

            function removeEvent(btn) {
                const $item = $(btn).closest('.event-item');
                const idx = $item.data('index');
                const validator = (window.caminhoRocaValidators || {})['form-parceiro-publico'];
                if (validator && idx !== undefined) {
                    validator.removeField('#event-' + idx + '-name');
                    validator.removeField('#event-' + idx + '-description');
                }
                $item.remove();
            }

            // Rotas/Circuitos/Atrativos: sempre visíveis, obrigatórios conforme a categoria
            $('#partner_category_id').on('change', function () {
                const experiencias = $(this).find('option:selected').data('experiencias') == 1;
                $('#routes, #circuits, #attractions').each(function () {
                    if (experiencias) {
                        $(this).attr('required', 'required');
                    } else {
                        $(this).removeAttr('required');
                    }
                });
            }).trigger('change');

            // Registra no just-validate a obrigatoriedade dinâmica de Rotas/Circuitos/Atrativos.
            // A validação global só captura campos já marcados como required no carregamento da página.
            document.addEventListener('DOMContentLoaded', function () {
                const validator = (window.caminhoRocaValidators || {})['form-parceiro-publico'];
                if (!validator) return;

                const requerExperiencias = function () {
                    const opt = $('#partner_category_id').find('option:selected');
                    return opt.length > 0 && opt.data('experiencias') == 1;
                };

                ['routes', 'circuits', 'attractions'].forEach(function (id) {
                    validator.addField('#' + id, [
                        {
                            validator: function () {
                                if (!requerExperiencias()) return true;
                                const valor = document.getElementById(id).value;
                                return valor && valor.trim().length > 0;
                            },
                            errorMessage: 'Este campo é obrigatório.'
                        }
                    ]);
                });
            });

            $('#add-evento').on('click', addEvento);

            // Habilita o botão de envio somente após aceitar os Termos de Uso
            $(function () {
                const terms = document.getElementById('terms');
                const btn = document.getElementById('btn-enviar-cadastro');
                if (!terms || !btn) return;
                const atualizar = () => { btn.disabled = !terms.checked; };
                terms.addEventListener('change', atualizar);
                atualizar(); // aplica o estado inicial (ex.: ao voltar de um erro com o aceite marcado)
            });

        </script>
    @endpush
@endsection
