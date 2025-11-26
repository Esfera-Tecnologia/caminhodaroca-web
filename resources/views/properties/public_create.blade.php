@extends('layouts.login')

@section('title', 'Cadastrar Propriedade - Caminhos da Roça')

@section('content')
    <form action="{{ route('properties.public.store') }}" id="form-propriedade" method="POST" novalidate
          enctype="multipart/form-data">
        @csrf
        <div class="d-flex vh-100">
            <div class="bg-image d-none d-md-block col"></div>

            <div class="d-flex col-md-7 col-12 justify-content-center bg-white p-4 px-5 overflow-auto">
                <div class="w-100 px-4 mx-5">

                    <div class="text-center mb-5 d-flex flex-column">
                        <div class="w-auto mb-3">
                            <img src="{{ asset('assets/LogoCaminhodaRoca.png') }}" alt="Caminhos da Roça"
                                 class="img-fluid"
                                 style="max-height: 140px;">
                        </div>

                        <h2 class="fw-bold">Cadastro de Propriedade</h2>
                        <span>Preencha os campos abaixo para cadastrar sua propriedade no Caminho da Roça.</span>

                    </div>

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif


                    @php
                        $cidadesRJ = [
                          'Angra dos Reis', 'Aperibé', 'Araruama', 'Areal', 'Armação dos Búzios', 'Arraial do Cabo', 'Barra do Piraí', 'Barra Mansa', 'Belford Roxo', 'Bom Jardim',
                          'Bom Jesus do Itabapoana', 'Cabo Frio', 'Cachoeiras de Macacu', 'Cambuci', 'Campos dos Goytacazes', 'Cantagalo', 'Carapebus', 'Cardoso Moreira', 'Carmo',
                          'Casimiro de Abreu', 'Comendador Levy Gasparian', 'Conceição de Macabu', 'Cordeiro', 'Duas Barras', 'Duque de Caxias', 'Engenheiro Paulo de Frontin',
                          'Guapimirim', 'Iguaba Grande', 'Itaboraí', 'Itaguaí', 'Italva', 'Itaocara', 'Itaperuna', 'Itatiaia', 'Japeri', 'Laje do Muriaé', 'Macaé', 'Macuco',
                          'Magé', 'Mangaratiba', 'Maricá', 'Mendes', 'Mesquita', 'Miguel Pereira', 'Miracema', 'Natividade', 'Nilópolis', 'Niterói', 'Nova Friburgo', 'Nova Iguaçu',
                          'Paracambi', 'Paraíba do Sul', 'Paraty', 'Paty do Alferes', 'Petrópolis', 'Pinheiral', 'Piraí', 'Porciúncula', 'Porto Real', 'Quatis', 'Queimados',
                          'Quissamã', 'Resende', 'Rio Bonito', 'Rio Claro', 'Rio das Flores', 'Rio das Ostras', 'Rio de Janeiro', 'Santa Maria Madalena', 'Santo Antônio de Pádua',
                          'São Fidélis', 'São Francisco de Itabapoana', 'São Gonçalo', 'São João da Barra', 'São João de Meriti', 'São José de Ubá', 'São José do Vale do Rio Preto',
                          'São Pedro da Aldeia', 'São Sebastião do Alto', 'Sapucaia', 'Saquarema', 'Seropédica', 'Silva Jardim', 'Sumidouro', 'Tanguá', 'Teresópolis',
                          'Trajano de Moraes', 'Três Rios', 'Valença', 'Varre-Sai', 'Vassouras', 'Volta Redonda'
                        ];
                    @endphp

                    <div class="row g-4">
                        <div class="col-md-3">
                            <label class="form-label">Logo da Propriedade *</label>
                            <input type="file" name="logo" id="logo" class="form-control" value=""
                                   accept=".jpg,.jpeg,.png,.gif" onchange="previewLogo(this)">
                            <img id="preview-logo" src="{{ asset('assets/teste3.png') }}" class="preview-img mt-2"
                                 alt="Preview Logo">
                            <div id="logo-error" class="text-danger mt-1"></div>

                        </div>

                        <div class="col-md-9">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Nome da Propriedade *</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                           value="{{ old('name', '') }}"
                                           required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">WhatsApp *</label>
                                    <input type="text" name="whatsapp" id="whatsapp" class="form-control telefone"
                                           value="{{ old('whatsapp', '') }}" required>

                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Status *</label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="ativo" @selected(old('status', '') === 'ativo')>Ativo</option>
                                        <option value="inativo" @selected(old('status', '') === 'inativo')>Inativo
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Instagram *</label>
                                    <input type="text" name="instagram" id="instagram" class="form-control"
                                           value="{{ old('instagram', ) }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Endereço do Empreendimento *</label>
                                    <input type="text" name="endereco_principal" id="endereco_principal"
                                           class="form-control"
                                           value="{{ old('endereco_principal', '') }}" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Link do Google Maps</label>
                                    <input type="url" name="google_maps_url" id="google_maps_url" class="form-control"
                                           required
                                           value="{{ old('google_maps_url', '') }}"
                                           placeholder="Cole o link do Google Maps aqui">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Latitude</label>
                                    <input type="text" name="latitude" id="latitude" class="form-control"
                                           value="{{ old('latitude', '') }}" required placeholder="Latitude">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Longitude</label>
                                    <input type="text" name="longitude" id="longitude" class="form-control"
                                           value="{{ old('longitude', '') }}" required placeholder="Longitude">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Cidade *</label>
                                    <select name="cidade" id="cidade" class="form-select select2" required>
                                        <option value="">Selecione uma Cidade</option>
                                        @foreach($cidadesRJ as $cidade)
                                            <option
                                                value="{{ $cidade }}" @selected(old('cidade', '') === $cidade)>{{ $cidade }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>


                    <hr class="my-4">
                    <div class="row">
                        <div class="col">
                            <label class="form-label">Nome do Responsável *</label>
                            <input type="text" name="nome_responsavel" id="nome_responsavel" class="form-control"
                                   placeholder="Quem responde pela propriedade"
                                   value="{{ old('name', '') }}"
                                   required>
                        </div>
                        <div class="col">
                            <label class="form-label">E-mail do Responsável *</label>
                            <input type="text" name="email_responsavel" id="email_responsavel" class="form-control"
                                   value="{{ old('name', '') }}"
                                   placeholder="responsavel@propriedade.com"
                                   required>
                        </div>
                    </div>


                    <hr class="my-4">

                    @include('properties.partials.categorias', ['property' => null, 'hide_add_button' => true, 'categories' => $categories])

                    <hr class="my-4">

                    @include('properties.partials.artesanais', ['property' => null])

                    <hr class="my-4">

                    @include('properties.partials.agenda', ['property' => null])

                    <div class="row g-3 mt-4">
                        <div class="col-md-6">
                            <label class="form-label">Aceita animais de estimação? *</label>
                            <select name="aceita_animais" id="aceita_animais" required class="form-select">
                                <option value="1" @selected(old('aceita_animais', false))>Sim</option>
                                <option value="0" @selected(!old('aceita_animais', false))>Não</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Possui acessibilidade? *</label>
                            <select name="possui_acessibilidade" id="possui_acessibilidade" required
                                    class="form-select">
                                <option value="1" @selected(old('possui_acessibilidade', false))>Sim</option>
                                <option value="0" @selected(!old('possui_acessibilidade', false))>Não</option>
                            </select>
                        </div>
                    </div>


                    <!-- Upload moderno -->

                    <div class="mt-4 mb-5">
                        <label class="form-label">Fotos dos seus produtos ou do estabelecimento *</label>
                        <a href="javascript:void(0)" type="button" data-bs-toggle="popover" data-bs-trigger="focus"
                           data-bs-title="Atenção"
                           data-bs-content="Fotos que contenham crianças e/ou pessoas não serão aceitas. As imagens devem transmitir a essência da propriedade rural ou do negócio, destacando suas atividades, paisagens, produtos ou estrutura."><i
                                class="fa-solid fa-circle-info"></i></a>
                        <div id="uploadBox" class="upload-box">
                            <p class="upload-message m-0">
                                Arraste e solte as imagens ou <a href="#" id="linkUpload">clique aqui para
                                    selecionar</a>
                            </p>
                            <input type="file" id="imageUploader" name="images[]" multiple
                                   accept="image/png,image/jpeg,image/jpg,image/gif" hidden>
                        </div>

                        <div id="imagePreviewContainer" class="preview-container mt-3 d-flex flex-wrap gap-3"></div>


                    </div>

                    <div class="text-end mb-5 pb-5">
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-success">Enviar Cadastro</button>
                    </div>


                    <div id="custom-alert" class="alert d-none" role="alert"></div>

                </div>

            </div>
        </div>
    </form>

    @push('scripts')
        <script>

            window.subcategoriasPorCategoria = @json(
                $categories->mapWithKeys(fn($cat) => [
                  $cat->id => $cat->subcategories->map(fn($s) => [
                    'id' => $s->id,
                    'nome' => $s->nome // <- agora está correto
                  ])
                ])
              );

            function previewLogo(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = e => $('#preview-logo').attr('src', e.target.result);
                    reader.readAsDataURL(input.files[0]);
                }
            }

            function adicionarCategoria() {
                const select = $('#category_id');
                const categoriaId = select.val();
                const categoriaNome = select.find('option:selected').text();
                const categoriaHtmlId = 'categoria_' + categoriaId;

                if (!categoriaId) {
                    alert('Por favor, selecione uma categoria válida.');
                    return;
                }

                if ($('#' + categoriaHtmlId).length > 0) return;

                // Cria o bloco da categoria
                const $col = $('<div>', {
                    class: 'col-md-6',
                    id: categoriaHtmlId
                });

                const $block = $('<div>', {
                    class: 'categoria-block border p-3 h-100 d-flex flex-column justify-content-between'
                });

                // Cria a div wrapper para o conteúdo principal
                const $contentDiv = $('<div>');

                // Adiciona o header com título e botão remover
                $contentDiv.append(`
        <div class="d-flex justify-content-between">
          <strong>${categoriaNome}</strong>
          <button type="button" class="btn btn-sm btn-outline-danger">Remover</button>
        </div>
      `);

                const subcategorias = window.subcategoriasPorCategoria?.[categoriaId] || [];

                if (subcategorias.length > 0) {
                    subcategorias.forEach(sub => {
                        $contentDiv.append(`
            <div class="form-check mt-2">
              <input class="form-check-input" type="checkbox"
                    name="categoria_ids[${categoriaId}][]" value="${sub.id}"
                    id="sub${sub.id}-${categoriaId}">
              <label class="form-check-label" for="sub${sub.id}-${categoriaId}">
                ${sub.nome}
              </label>
            </div>
          `);
                    });

                    $contentDiv.append(`
          <input type="hidden" class="subcategoria-hidden" name="categoria_ids[${categoriaId}][]" value="">
        `);
                } else {
                    $contentDiv.append(`
          <div class="text-muted mt-2 small">
            Essa categoria não possui subcategorias.
          </div>
          <input type="hidden" name="categoria_ids[${categoriaId}][]" value="">
        `);
                }

                // Adiciona a div de conteúdo ao bloco principal
                $block.append($contentDiv);

                $col.append($block);

                // Verifica se já existe uma row com menos de 2 colunas
                const $container = $('#categorias-container');
                let $lastRow = $container.children('.row.g-3').last();

                if (!$lastRow.length || $lastRow.children('.col-md-6').length >= 2) {
                    $lastRow = $('<div class="row g-3 mb-2"></div>');
                    $container.append($lastRow);
                }

                $lastRow.append($col);

                // Botão de remover com reestruturação automática
                $block.find('button').on('click', function () {
                    $col.remove();

                    // Após remoção, reestruturar todas as colunas novamente em pares de dois
                    const $allCols = $('#categorias-container .col-md-6').detach();
                    $('#categorias-container').empty();

                    for (let i = 0; i < $allCols.length; i += 2) {
                        const $row = $('<div class="row g-3 mb-2"></div>');
                        $row.append($allCols[i]);

                        if ($allCols[i + 1]) {
                            $row.append($allCols[i + 1]);
                        }

                        $('#categorias-container').append($row);
                    }
                });

                // Resetar select
                $('#category_id').val('');
                $('#erro-categorias').remove();
            }


            $(document).on('click', '.btn-remover-categoria', function () {
                const $col = $(this).closest('.col-md-6');
                $col.remove();

                // Reorganiza novamente as linhas (duplas)
                const $container = $('#categorias-container');
                const $restantes = $container.find('.col-md-6').toArray();
                $container.empty();

                for (let i = 0; i < $restantes.length; i += 2) {
                    const $linha = $('<div class="row g-3"></div>');
                    $linha.append($restantes[i]);
                    if ($restantes[i + 1]) $linha.append($restantes[i + 1]);
                    $container.append($linha);
                }
            });


            // mostra produtos artesanais
            function toggleProdutosArtesanais() {
                const val = $('#vende-produtos-artesanais').val();
                if (val === '1') {
                    $('#produtos-artesanais').slideDown();
                } else {
                    $('#produtos-artesanais').slideUp();
                    $('#produtos-artesanais input[type=checkbox]').prop('checked', false);
                }
            }

            $(document).on('change', '#vende-produtos-artesanais', toggleProdutosArtesanais);
            $(document).ready(toggleProdutosArtesanais);


            // Galeria
            const input = document.getElementById('imageUploader');
            const linkUpload = document.getElementById('linkUpload');
            const previewContainer = document.getElementById('imagePreviewContainer');
            const uploadBox = document.getElementById('uploadBox');

            const maxFiles = 6;
            let selectedFiles = []; // Arquivos novos
            let loadedImageIds = new Set(); // IDs do backend

            linkUpload.addEventListener('click', e => {
                e.preventDefault();
                input.click();
            });

            input.addEventListener('change', function () {
                handleFiles(Array.from(this.files));
            });

            uploadBox.addEventListener('dragenter', preventDefaults, false);
            uploadBox.addEventListener('dragover', preventDefaults, false);
            uploadBox.addEventListener('dragleave', preventDefaults, false);
            uploadBox.addEventListener('drop', preventDefaults, false);

            uploadBox.addEventListener('dragover', () => uploadBox.classList.add('dragover'));
            uploadBox.addEventListener('dragleave', () => uploadBox.classList.remove('dragover'));

            uploadBox.addEventListener('drop', e => {
                uploadBox.classList.remove('dragover');
                const files = Array.from(e.dataTransfer.files);
                handleFiles(files);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }


            // Limites
            const MAX_FILES = 6;
            const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2 MB
            const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];

            function handleFiles(files) {
                for (const file of files) {
                    // 1) Tipo permitido
                    if (!ALLOWED_TYPES.includes(file.type)) {
                        showImageAlert('Apenas imagens nos formatos PNG, JPG, JPEG e GIF são permitidas.');
                        continue;
                    }

                    // 2) Tamanho permitido (2MB)
                    if (file.size > MAX_FILE_SIZE) {
                        const mb = (file.size / (1024 * 1024)).toFixed(2);
                        showImageAlert(`A imagem "${file.name}" tem ${mb} MB. O limite é 2 MB.`);
                        continue;
                    }

                    // 3) Limite de quantidade
                    const totalImagens = selectedFiles.length + loadedImageIds.size;
                    if (totalImagens >= MAX_FILES) {
                        showImageAlert(`Você só pode enviar no máximo ${MAX_FILES} imagens.`);
                        break; // para de processar o restante deste lote
                    }

                    // 4) OK: adiciona e renderiza
                    selectedFiles.push(file);
                    renderThumbnail(file);
                }

                updateFileInput(); // mantém o input em sincronia
            }


            function renderThumbnail(file) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    const thumb = document.createElement('div');
                    thumb.classList.add('image-thumb');

                    const img = document.createElement('img');
                    img.src = e.target.result;

                    const removeBtn = document.createElement('button');
                    removeBtn.classList.add('remove-btn');
                    removeBtn.innerHTML = '×';

                    removeBtn.addEventListener('click', function () {
                        previewContainer.removeChild(thumb);
                        selectedFiles = selectedFiles.filter(f => f !== file);
                        updateFileInput();
                    });

                    thumb.appendChild(img);
                    thumb.appendChild(removeBtn);
                    previewContainer.appendChild(thumb);
                };

                reader.readAsDataURL(file);
            }

            function updateFileInput() {
                const dataTransfer = new DataTransfer();
                selectedFiles.forEach(file => dataTransfer.items.add(file));
                input.files = dataTransfer.files;
            }

            // Imagens do backend (Laravel)
            const imagensSalvas = @json([]);

            imagensSalvas.forEach(img => {
                const thumb = document.createElement('div');
                thumb.classList.add('image-thumb');
                thumb.setAttribute('data-id', img.id);

                const image = document.createElement('img');
                image.src = img.url;

                const removeBtn = document.createElement('button');
                removeBtn.classList.add('remove-btn');
                removeBtn.innerHTML = '×';

                removeBtn.addEventListener('click', function () {
                    previewContainer.removeChild(thumb);
                    loadedImageIds.delete(img.id);

                    fetch('/imagens/remover', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({id: img.id})
                    });
                });

                thumb.appendChild(image);
                thumb.appendChild(removeBtn);
                previewContainer.appendChild(thumb);
                loadedImageIds.add(img.id);
            });

            function showImageAlert(message) {
                // Remove alertas existentes antes de adicionar outro
                const existingAlert = document.querySelector('.custom-image-alert');
                if (existingAlert) {
                    existingAlert.remove();
                }

                // Cria a div de alerta
                const alert = document.createElement('div');
                alert.className = 'alert alert-warning custom-image-alert mt-3';
                alert.setAttribute('role', 'alert');
                alert.textContent = message;

                // Adiciona ao DOM — logo abaixo do container de preview
                const previewContainer = document.getElementById('imagePreviewContainer');
                previewContainer.insertAdjacentElement('afterend', alert);

                // Remove após 6 segundos
                setTimeout(() => {
                    alert.remove();
                }, 5000);
            }

            function gerarAgendaSemanal() {
                const dias = [
                    'segunda', 'terça', 'quarta', 'quinta', 'sexta', 'sábado', 'domingo'
                ];

                const container = $('#agendaSemanal');
                container.empty();

                const agenda = window.agendaPersonalizada || {};

                dias.forEach((dia) => {
                    const dados = agenda[dia] || {};
                    const ativo = dados.ativo === 1 || dados.ativo === true || dados.ativo === '1';
                    const fechaAlmoco = dados.fecha_almoco === 1 || dados.fecha_almoco === true || dados.fecha_almoco === '1';
                    const abertura = dados.abertura || '';
                    const fechamento = dados.fechamento || '';

                    const bloco = `
      <div class="day-block border rounded p-3 mb-3" data-dia="${dia}">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <strong>${dia.charAt(0).toUpperCase() + dia.slice(1)}</strong>
          <div>
            <input type="hidden" name="agenda_personalizada[${dia}][ativo]" value="0">
            <input type="checkbox" class="form-check-input me-1 ativar-dia"
              id="check_${dia}" name="agenda_personalizada[${dia}][ativo]" value="1"
              ${ativo ? 'checked' : ''}>
            <label for="check_${dia}" class="form-check-label">Ativo</label>
          </div>
        </div>

        <input type="hidden" name="agenda_personalizada[${dia}][dia]" value="${dia}">

        <div class="row g-2">
          <div class="col-6">
            <label class="form-label">Abertura</label>
            <input type="time" class="form-control horario-abertura"
              name="agenda_personalizada[${dia}][abertura]" value="${abertura}">
          </div>
          <div class="col-6">
            <label class="form-label">Fechamento</label>
            <input type="time" class="form-control horario-fechamento"
              name="agenda_personalizada[${dia}][fechamento]" value="${fechamento}">
          </div>
        </div>

        <div class="form-check mt-2">
          <input type="hidden" name="agenda_personalizada[${dia}][fecha_almoco]" value="0">
          <input type="checkbox" class="form-check-input fechar-almoco"
            id="almoco_${dia}" name="agenda_personalizada[${dia}][fecha_almoco]" value="1"
            ${fechaAlmoco ? 'checked' : ''}>
          <label class="form-check-label" for="almoco_${dia}">Fecha no almoço</label>
        </div>
      </div>
    `;

                    container.append(bloco);
                });

                controlarInputsPorDia(); // Desabilita campos de dias inativos
            }

            $(document).ready(() => {
                gerarAgendaSemanal();

                $('input[name="tipo_funcionamento"]').on('change', function () {
                    const tipo = $(this).val();
                    $('#horarioTexto').toggleClass('d-none', !['feriados', 'agendamento', 'personalizado'].includes(tipo));
                });
            });


            function ajustarHorarioFuncionamento(tipoSelecionado = null) {
                const tipo = tipoSelecionado || $('input[name="tipo_funcionamento"]:checked').val();

                const diasUteis = ['segunda', 'terça', 'quarta', 'quinta', 'sexta'];
                const diasFinais = ['sábado', 'domingo'];
                const todosDias = [...diasUteis, ...diasFinais];

                //  Limpa agendas ou texto conforme o tipo atual
                if (['agendamento', 'personalizado', 'feriados'].includes(tipo)) {
                    $('#agendaSemanal').addClass('d-none');
                    $('#horarioTexto').removeClass('d-none');
                    return;
                } else {
                    $('#agendaSemanal').removeClass('d-none');
                    $('#horarioTexto').addClass('d-none');

                    // Se tipo é agendamento/personalizado → NÃO limpar!
                    if (!['agendamento', 'personalizado'].includes(tipo)) {
                        $('textarea[name="observacoes_funcionamento"]').val('');
                    }

                }

                gerarAgendaSemanal();

                // 🧠 Regras específicas por tipo
                if (tipo === 'todos') {
                    // Mantém tudo conforme estado do banco (ou default inativo, já tratado em gerarAgendaSemanal)
                    controlarInputsPorDia(); // ativa/desativa campos com base no checkbox "Ativo"

                } else if (tipo === 'fins') {
                    todosDias.forEach((dia) => {
                        const bloco = $(`.day-block[data-dia="${dia}"]`);
                        const isUtil = diasUteis.includes(dia);

                        // Desativa e limpa dias úteis
                        if (isUtil) {
                            bloco.find('.ativar-dia').prop('checked', false).prop('disabled', true);
                            bloco.find('input[type="time"]').val('').prop('disabled', true);
                            bloco.find('.fechar-almoco').prop('checked', false).prop('disabled', true);
                        } else {
                            // Mantém finais de semana com base no estado atual (não força)
                            bloco.find('.ativar-dia').prop('disabled', false);
                        }
                    });
                }
            }


            // Aplica ao carregar e ao trocar opção
            $(document).ready(() => {
                ajustarHorarioFuncionamento();
                // Evento de mudança de tipo de funcionamento
                $('input[name="tipo_funcionamento"]').on('change', function () {
                    const tipo = $(this).val();

                    if (['feriados', 'agendamento', 'personalizado'].includes(tipo)) {
                        $('#horarioTexto').removeClass('d-none');
                        $('#agendaSemanal').addClass('d-none');
                    } else {
                        $('#agendaSemanal').removeClass('d-none');
                        $('#horarioTexto').addClass('d-none');
                    }

                    ajustarHorarioFuncionamento(tipo);
                });
            });

            function controlarInputsPorDia() {
                $('.ativar-dia').each(function () {
                    const isChecked = $(this).is(':checked');
                    const bloco = $(this).closest('.day-block');

                    bloco.find('input[type="time"], .fechar-almoco').prop('disabled', !isChecked);
                });
            }

            // Ativa a escuta para quando o checkbox "Ativo" de cada dia for alterado
            $(document).on('change', '.ativar-dia', controlarInputsPorDia);

            // Executa ao carregar a página
            $(document).ready(controlarInputsPorDia);

            // Remove erro de observação ao digitar
            $('textarea[name="observacoes_funcionamento"]').on('input', function () {
                $('#erro-observacoes').remove();
            });

            $(document).on('input change', '.horario-abertura, .horario-fechamento, .ativar-dia', function () {
                $(this).closest('.day-block').find('.erro-horario').remove();
            });


            $(document).ready(function () {
                $('#google_maps_url').on('blur', function () {
                    const url = $(this).val();
                    if (!url) return;

                    let lat = '';
                    let lng = '';

                    // Captura todos os !3dLAT!4dLONG do link
                    const regexAll = /!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/g;
                    let match, lastMatch;

                    while ((match = regexAll.exec(url)) !== null) {
                        lastMatch = match; // sempre pega o último par encontrado
                    }

                    if (lastMatch) {
                        lat = lastMatch[1];
                        lng = lastMatch[2];
                    } else {
                        // Se não tiver !3d / !4d, tenta pegar o @lat,long
                        const regexAt = /@(-?\d+\.\d+),(-?\d+\.\d+)/;
                        const matchAt = url.match(regexAt);

                        if (matchAt) {
                            lat = matchAt[1];
                            lng = matchAt[2];
                        }
                    }

                    // Preenche os campos (ou limpa se não encontrou)
                    $('#latitude').val(lat);
                    $('#longitude').val(lng);
                    $('#latitude').closest('div').find('.just-validate-error-label').remove();
                    $('#longitude').closest('div').find('.just-validate-error-label').remove();
                });
            });


        </script>
    @endpush
@endsection
