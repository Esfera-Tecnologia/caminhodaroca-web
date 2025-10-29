<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $property->name }} - Ficha da Propriedade</title>
    <style>
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
            background-color: #306D60; /* Verde escuro */
            color: white;
            display: flex;
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
            margin: 0;
            padding: 0;
        }

        /* Reset adicional para garantir que não haja margens */
        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .content {
            padding: 50px;
            padding-top: 120px;
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
            background-color: #306D60;
            color: white;
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
            <td style="width: 29%;"><img style="width: 100%; margin-bottom: auto; margin-top: auto"
                                         src="storage/{{ $property->logo_path }}" alt="Logo {{ $property->name }}"></td>
            <td style="padding-left: 15px">
                <h2>{{ $property->name }}</h2>
                <span class="badge">Status: {{ $property->status ? 'Ativo' : 'Inativo' }}</span><br>
                <span>Categoria Principal: {{ $categoria_principal->name }}</span><br>
                <span>Subcategorias: {{ implode(', ', $subcategorias_principais) }}</span><br>
                <span>Whatsapp: {{ $property->whatsapp_mask }}</span> |
                <span>Instagram: {{ $property->instagram }}</span><br>
                <span>E-mail: {{ $property->instagram }}</span><br>
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
        @foreach($categorias as $categoria)
            <span>- {{ $categoria->name }}: {{ implode(', ', $property->subcategorias->where('category_id', $categoria->id)->pluck('name')->toArray()) }}</span>
            <br>
        @endforeach
        <div class="section-title">Descrição dos Serviços</div>
        <p>{{ $property->descricao_servico }}</p>
    </div>

    <div class="section">
        <div class="section-title">Certificações e Produtos Artesanais</div>
        <span>Possui certificação: {{ $property->certificacao == '1' ? 'Sim' : 'Não' }}</span><br><br>
        <span>Vende produtos artesanais: {{ $property->vende_produtos_artesanais == '1' ? 'Sim' : 'Não' }}</span><br>
        @if($property->vende_produtos_artesanais == 1)
            <span>Produtos: {{ implode(', ', $property->products->pluck('nome')->toArray()) }}</span>
        @endif

    </div>

    <!-- Quebra de página -->
    <div class="page-break"></div>

    <!-- Funcionamento -->
    <div class="section" style="margin-top: 120px">
        <div class="section-title">Horário de Funcionamento</div>
        <div><strong>Tipo de funcionamento:</strong> {{ $property->tipo_funcionamento->label() ?? 'Não informado' }}
        </div>

        @if($property->agenda_personalizada)
            @php
                $agenda = $property->agenda_personalizada;
                $diasOrdenados = ['segunda', 'terça', 'quarta', 'quinta', 'sexta', 'sábado', 'domingo'];
            @endphp

            @if($property->observacoes_funcionamento)
                <div style="margin-top: 15px;"><strong>Observações:</strong> {{ $property->observacoes_funcionamento }}
                </div>
            @endif

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
                @foreach($diasOrdenados as $dia)
                    @if(isset($agenda[$dia]) && $agenda[$dia]['ativo'])
                        @php
                            $diaInfo = $agenda[$dia];
                            $fechaAlmoco = $diaInfo['fecha_almoco'] ?? '0';
                            // Horários padrão baseados no exemplo original
                            $horarios = [
                                'segunda' => ['abertura' => '09:00', 'fechamento' => '18:00'],
                                'terça' => ['abertura' => '09:00', 'fechamento' => '18:00'],
                                'quarta' => ['abertura' => '09:00', 'fechamento' => '18:00'],
                                'quinta' => ['abertura' => '09:00', 'fechamento' => '20:00'],
                                'sexta' => ['abertura' => '09:00', 'fechamento' => '22:00'],
                                'sábado' => ['abertura' => '08:00', 'fechamento' => '22:00'],
                                'domingo' => ['abertura' => '08:00', 'fechamento' => '18:00']
                            ];
                        @endphp
                        <tr>
                            <td style="text-transform: capitalize;">{{ $diaInfo['dia'] ?? $dia }}</td>
                            <td>
                                {{ $horarios[$dia]['abertura'] ?? '--:--' }}
                            </td>
                            <td>
                                {{ $horarios[$dia]['fechamento'] ?? '--:--' }}
                            </td>
                            <td>
                                {{ $fechaAlmoco == '1' ? 'Sim' : 'Não' }}
                            </td>
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
                <img src="storage/{{ $imagePath->path }}" alt="Imagem da propriedade">
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
