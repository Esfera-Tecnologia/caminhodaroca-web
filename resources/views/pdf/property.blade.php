<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $property->name }} - Ficha da Propriedade</title>
    <style>
        @php $padding = 120 @endphp
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            padding-top: 100px; /* Espaço para o header fixo */
            overflow-x: hidden; /* Impede scroll horizontal */
        }

        /* Header fixo em todas as páginas - CORRIGIDO */
        .header-fixed {
            position: fixed;
            left: 0;
            right: 0;
            top: -{{$padding}}px;
            background-color: #306D60; /* Verde escuro */
            color: white;
            align-items: center;
            justify-content: space-between;
            z-index: 1000;
            height: 75px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 100vw; /* Usar viewport width em vez de 100% */
            margin: 0;
            padding: 15px 0 0 0; /* Remover padding lateral */
            box-sizing: border-box; /* Inclui padding na largura total */
        }

        .header-logo {
            position: absolute;
            top: -10px;
            right: 20px;
            max-height: 50px;
            max-width: 150px;
        }

        .header-title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            flex-grow: 1;
        }

        .header-status {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            margin-right: 20px; /* Adicionar margem interna em vez de padding no container */
        }

        /* Garantir que o header apareça em todas as páginas do PDF */
        @page {
            margin: {{$padding}}px 0;
        }

        /* Reset adicional para garantir que não haja margens */
        * {
            box-sizing: border-box;
        }

        body {
            padding: 0;
        }

        .content {
            padding: 50px;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
            background-color: #f5fbf9;
            padding: 15px
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #306D60;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .contact-info div, .address-info div {
            margin-bottom: 5px;
        }

        .non-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background: transparent;
            border: none;
        }

        .non-table tr {
            border: none;
        }

        .non-table th, .non-table td {
            border: none;
            padding: 8px;
            text-align: left;
        }

        .hours-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .hours-table th, .hours-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .hours-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .hours-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        .bg-success {
            background-color: #306D60;
        }
        .bg-danger {
            background-color: #dc3545;
        }


        .gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .gallery img {
            max-width: 200px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 4px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body style="margin: 0">
<!-- Header fixo em todas as páginas -->
<div class="header-fixed">
    <img src="assets/Logobrancahorizontal.png" alt="Logo" class="header-logo" style="max-height: 160px;">
</div>

<!-- Conteúdo principal -->
<div class="content">
    <table class="section">
        <tr>
            @php
                    $path = realpath(base_path('public/storage/'.$property->logo_path));
                    $imageData = base64_encode(file_get_contents($path));
                    $mime = mime_content_type($path);
            @endphp
            <td style="width: 29%;"><img style="width: 100%; margin-bottom: auto; margin-top: auto"
                                         src="data:{{ $mime }};base64,{{ $imageData }}" alt="Logo {{ $property->name }}"></td>
            <td style="padding-left: 15px">
                <h2>{{ $property->name }}</h2>
                <span class="badge {{$property->status == App\Enums\StatusProperty::ATIVO ? 'bg-success' : 'bg-danger'}}">
                    Status: {{ $property->status->label() }}
                </span><br>
                <span>Categoria Principal: {{ $categoria_principal->name }}</span><br>
                <span>Subcategorias: {{ implode(', ', $subcategorias_principais) }}</span><br>
                <span>Whatsapp: {{ $property->whatsapp_mask }}</span> |
                <span>Instagram: {{ $property->instagram }}</span><br>
                <span>E-mail: {{ $property->email_responsavel }}</span><br>
                <span>Endereço principal: {{ $property->endereco_principal }}</span><br>
                @if(!empty($property->endereco_secundario))
                    <span>Endereço secundário: {{ $property->endereco_secundario }}</span><br>
                @endif
                <span>Cidade/Estado: {{ $property->cidade }} - Rio de Janeiro</span><br>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">Categorias cadastradas</div>
        @foreach($categorias->pluck('name', 'id') as $key=>$categoria)
            <span>- {{ $categoria }}: {{ implode(', ', $property->subcategorias->where('category_id', $key)->pluck('name')->toArray()) }}</span>
            <br>
        @endforeach
    </div>

    <div class="section">
        <div class="section-title">Descrição dos Serviços</div>
        <p>{!! nl2br(e($property->descricao_servico)) !!}</p>
    </div>

    <div class="section">
        <div class="section-title">Certificações e Produtos Artesanais</div>
        <span>Possui certificação: {{ $property->certificacao == '1' ? 'Sim' : 'Não' }}</span><br><br>
        <span>Vende produtos artesanais: {{ $property->vende_produtos_artesanais == '1' ? 'Sim' : 'Não' }}</span><br>
        @if($property->vende_produtos_artesanais == 1)
            <span>Produtos: {{ implode(', ', $property->products->pluck('nome')->toArray()) }}</span>
        @endif

    </div>

    <!-- Funcionamento -->
    <div class="section">
        <div class="section-title">Horário de Funcionamento</div>
        <div><strong>Tipo de funcionamento:</strong> {{ $property->tipo_funcionamento?->label() ?? 'Não informado' }}
        </div>

        @php
            $tipoFuncionamento = $property->tipo_funcionamento?->value ?? 'todos';

            $diasOrdenados = ['segunda', 'terça', 'quarta', 'quinta', 'sexta', 'sábado', 'domingo'];

            // Normaliza a chave do dia para aceitar tanto "sabado" quanto "sábado"
            $normalizarDia = function ($valor) {
                return strtr(mb_strtolower((string) $valor), [
                    'á' => 'a', 'ã' => 'a', 'â' => 'a', 'à' => 'a',
                    'é' => 'e', 'ê' => 'e', 'í' => 'i',
                    'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
                    'ú' => 'u', 'ç' => 'c',
                ]);
            };

            $agendaPersonalizada = $property->agenda_personalizada;
            if (is_string($agendaPersonalizada)) {
                $agendaPersonalizada = json_decode($agendaPersonalizada, true);
            }
            if (! is_array($agendaPersonalizada)) {
                $agendaPersonalizada = [];
            }

            $agendaPorDia = [];
            foreach ($agendaPersonalizada as $chaveDia => $dadosDia) {
                $agendaPorDia[$normalizarDia($chaveDia)] = $dadosDia;
            }

            // Exibe o horário no formato H:i (aceita "08:00" ou "08:00:00")
            $formatarHora = function ($valor) {
                return empty($valor) ? '--:--' : substr((string) $valor, 0, 5);
            };

            // Com agendamento / personalizado: mostra somente o texto informado
            $mostraTexto = in_array($tipoFuncionamento, ['agendamento', 'personalizado', 'feriados'], true);

            // Finais de semana: exibe apenas sábado e domingo
            $diasVisiveis = $tipoFuncionamento === 'fins'
                ? ['sábado', 'domingo']
                : $diasOrdenados;

            $diasAtivos = 0;
            foreach ($diasVisiveis as $dia) {
                $dadosDia = $agendaPorDia[$normalizarDia($dia)] ?? null;
                if ($dadosDia && ($dadosDia['ativo'] ?? 0) == 1) {
                    $diasAtivos++;
                }
            }

            $mostraTabela = ! $mostraTexto && $diasAtivos > 0;
        @endphp

        @if($mostraTexto && $property->observacoes_funcionamento)
            <div style="margin-top: 15px;">{!! nl2br(e($property->observacoes_funcionamento)) !!}</div>
        @endif

        @if($mostraTabela)
            <table class="non-table">
                <thead>
                <tr>
                    <th>Dia</th>
                    <th>Abertura</th>
                    <th>Fechamento</th>
                    <th>Fecha almoço</th>
                </tr>
                </thead>
                <tbody>
                @foreach($diasVisiveis as $dia)
                    @php
                        $dadosDia = $agendaPorDia[$normalizarDia($dia)] ?? null;
                    @endphp
                    @if($dadosDia && ($dadosDia['ativo'] ?? 0) == 1)
                        <tr>
                            <td style="text-transform: capitalize;">{{ $dadosDia['dia'] ?? $dia }}</td>
                            <td>{{ $formatarHora($dadosDia['abertura'] ?? null) }}</td>
                            <td>{{ $formatarHora($dadosDia['fechamento'] ?? null) }}</td>
                            <td>{{ in_array($dadosDia['fecha_almoco'] ?? '0', ['1', 1, true], true) ? 'Sim' : 'Não' }}</td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        @endif
        <span>Aceita animais de estimação: {{ $property->aceita_animais ? 'Sim' : 'Não' }}</span><br>
        <span>Possui acessibilidade: {{ $property->possui_acessibilidade ? 'Sim' : 'Não' }}</span><br>
    </div>

    <!-- Galeria de Imagens -->
    <div class="section">
        <div class="gallery" style="padding-top: 15px">
            @foreach($property->images as $imagePath)
                @php
                    $path = realpath(base_path('public/storage/'.$imagePath->path));
                    $imageData = base64_encode(file_get_contents($path));
                    $mime = mime_content_type($path);
                @endphp
                <img src="data:{{ $mime }};base64,{{ $imageData }}" alt="Imagem da propriedade">
            @endforeach
        </div>
        <span style="font-size: 12px; color: #a9a9a9">Imagens ilustrativas fornecidas pela propriedade.</span>
    </div>

    <!-- Rodapé -->
    <div style="margin-top: 30px; text-align: center; font-size: 12px; color: #777;">
        <p>Documento gerado em {{ date('d/m/Y H:i:s') }}</p>
    </div>
</div>
</body>
</html>
