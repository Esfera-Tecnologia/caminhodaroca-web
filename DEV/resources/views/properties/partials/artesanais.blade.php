<div class="row g-3 mt-4">
  <div class="col-md-12">
    <label class="form-label">Descreva brevemente o tipo de serviço oferecido na propriedade *</label>
    <textarea name="descricao_servico" class="form-control" rows="4" required>{{ old('descricao_servico', $property->descricao_servico ?? '') }}</textarea>
  </div>
  <div class="col-md-6">
    <label class="form-label">Você possui certificação orgânica, agroecológica ou outra?</label>
    <select name="certificacao" class="form-select" required>
      <option value="0" @selected(old('certificacao', $property->certificacao ?? 0) == 0)>Não</option>
      <option value="1" @selected(old('certificacao', $property->certificacao ?? 0) == 1)>Sim </option>
      <option value="2" @selected(old('certificacao', $property->certificacao ?? 0) == 2)>Em Processo</option>
    </select>
  </div>

  <div class="col-md-6">
    <label class="form-label">Vende produtos artesanais? *</label>
    <select name="vende_produtos_artesanais" class="form-select" id="vende-produtos-artesanais">
      <option value="1" @selected(old('vende_produtos_artesanais', $property->vende_produtos_artesanais ?? false))>Sim</option>
      <option value="0" @selected(!old('vende_produtos_artesanais', $property->vende_produtos_artesanais ?? false))>Não</option>
    </select>
  </div>

  <div class="form-group mt-3" id="produtos-artesanais" style="{{ old('vende_produtos_artesanais', $property->vende_produtos_artesanais ?? false) ? '' : 'display:none' }}">
    <label class="form-label"><strong>Selecione os produtos abaixo:</strong></label>
    @php
      $produtos = [
        'Queijos', 'Geleias', 'Doces em barra', 'Doces cristalizados', 'Compotas',
        'Conservas', 'Molhos', 'Antepastos', 'Produtos desidratados',
        'Pães integrais', 'Pães de fermentação natural', 'Pães e bolos caseiros',
        'Cachaça e derivados', 'Licores', 'Vinhos'
      ];
      $selecionados = old('produtos_artesanais', $property->produtos_artesanais ?? []);
    @endphp

    <div class="row">
      @foreach(array_chunk($produtos, 5) as $grupo)
        <div class="col-md-4">
          @foreach($grupo as $produto)
            <div class="form-check">
              <input class="form-check-input"
                     type="checkbox"
                     name="produtos_artesanais[]"
                     value="{{ $produto }}"
                     id="prod_{{ Str::slug($produto) }}"
                     {{ in_array($produto, $selecionados) ? 'checked' : '' }}>
              <label class="form-check-label" for="prod_{{ Str::slug($produto) }}">{{ $produto }}</label>
            </div>
          @endforeach
        </div>
      @endforeach
    </div>
  </div>
</div>
