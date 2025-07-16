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
        <input type="file" name="logo" class="form-control" onchange="previewLogo(this)">
        <img id="preview-logo" src="{{ isset($property->logo_path) ? asset('storage/' . $property->logo_path) : asset('assets/teste3.png') }}" class="preview-img mt-2" alt="Preview Logo">
    </div>

    <div class="col-md-9">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Nome da Propriedade *</label>
          <input type="text" name="name" class="form-control" value="{{ old('name', $property->name ?? '') }}" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">WhatsApp *</label>
          <input type="text" name="whatsapp" class="form-control telefone" value="{{ old('whatsapp', $property->whatsapp ?? '') }}" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Status *</label>
          <select name="status" class="form-select" required>
            <option value="ativo" @selected(old('status', $property->status ?? '') === 'ativo')>Ativo</option>
            <option value="inativo" @selected(old('status', $property->status ?? '') === 'inativo')>Inativo</option>
          </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Instagram *</label>
            <div class="input-group">
                <span class="input-group-text" id="at-sign">@</span>
                <input type="text"
                    name="instagram"
                    class="form-control"
                    aria-describedby="at-sign"
                    placeholder="usuario.exemplo"
                    value="{{ old('instagram', ltrim($property->instagram ?? '', '@')) }}">
            </div>
          </div>
        
        <div class="col-md-6">
          <label class="form-label">Endereço do Empreendimento *</label>
          <input type="text" name="endereco_principal" class="form-control" value="{{ old('endereco_principal', $property->endereco_principal ?? '') }}" required>
        </div>
        
        <div class="col-md-6">
          <label class="form-label">Endereço Secundário</label>
          <input type="text" name="endereco_secundario" class="form-control" value="{{ old('endereco_secundario', $property->endereco_secundario ?? '') }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Cidade *</label>
            <select name="cidade" class="form-select select2" required>
                <option value="">Selecione uma Cidade</option>
                @foreach($cidadesRJ as $cidade)
                    <option value="{{ $cidade }}" @selected(old('cidade', $property->cidade ?? '') === $cidade)>{{ $cidade }}</option>
                 @endforeach
            </select>
        </div>
      </div>
    </div>
  

  <hr class="my-4">

  @include('properties.partials.categorias', ['property' => $property ?? null, 'categories' => $categories])

  <hr class="my-4">

  @include('properties.partials.artesanais', ['property' => $property ?? null])

  <hr class="my-4">

  @include('properties.partials.agenda', ['property' => $property ?? null])

  <div class="row g-3 mt-4">
    <div class="col-md-6">
      <label class="form-label">Aceita animais de estimação? *</label>
      <select name="aceita_animais" class="form-select">
        <option value="1" @selected(old('aceita_animais', $property->aceita_animais ?? false))>Sim</option>
        <option value="0" @selected(!old('aceita_animais', $property->aceita_animais ?? false))>Não</option>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">Possui acessibilidade? *</label>
      <select name="possui_acessibilidade" class="form-select">
        <option value="1" @selected(old('possui_acessibilidade', $property->possui_acessibilidade ?? false))>Sim</option>
        <option value="0" @selected(!old('possui_acessibilidade', $property->possui_acessibilidade ?? false))>Não</option>
      </select>
    </div>
  </div>

  <div class="col-md-12 mt-4">
    <label class="form-label">Fotos dos seus produtos ou do estabelecimento *</label>
    <input type="file" name="galeria[]" multiple class="form-control">
    @if(!empty($property->galeria_paths))
      <div class="gallery-preview d-flex flex-wrap mt-2">
        @foreach($property->galeria_paths as $img)
          <img src="{{ asset('storage/' . $img) }}" alt="Galeria" class="me-2 mb-2" style="height: 120px; width: 120px; object-fit: cover;">
        @endforeach
      </div>
    @endif
  </div>
</div>

