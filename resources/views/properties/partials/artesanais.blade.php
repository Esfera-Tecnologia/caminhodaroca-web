<div class="row g-3 mt-4">
  <div class="col-md-12">
    <label class="form-label">Descreva brevemente o tipo de serviço oferecido na propriedade *</label>
    <textarea name="descricao_servico" id="descricao_servico" class="form-control" rows="4" required>{{ old('descricao_servico', $property->descricao_servico ?? '') }}</textarea>
  </div>
  <div class="col-md-6">
    <label class="form-label">Você possui certificação orgânica, agroecológica ou outra?</label>
    <select name="certificacao" id="certificacao" class="form-select">
      <option value="0" @selected(old('certificacao', $property->certificacao ?? 0) == 0)>Não</option>
      <option value="1" @selected(old('certificacao', $property->certificacao ?? 0) == 1)>Sim </option>
      <option value="2" @selected(old('certificacao', $property->certificacao ?? 0) == 2)>Em Processo</option>
    </select>
  </div>

  <div class="col-md-6">
    <label class="form-label">Vende produtos artesanais? *</label>
    <select name="vende_produtos_artesanais" id="vende-produtos-artesanais"  class="form-select" id="vende-produtos-artesanais">
      <option value="1" @selected(old('vende_produtos_artesanais', $property->vende_produtos_artesanais ?? false))>Sim</option>
      <option value="0" @selected(!old('vende_produtos_artesanais', $property->vende_produtos_artesanais ?? false))>Não</option>
    </select>
  </div>

  <div class="form-group mt-3"  id="produtos-artesanais" style="{{ old('vende_produtos_artesanais', $property->vende_produtos_artesanais ?? false) ? '' : 'display:none' }}">
    <label class="form-label"><strong>Selecione os produtos abaixo:</strong></label>

   <div class="row">
    @if(isset($products) != '')
    @foreach(array_chunk($products->all(), 5) as $grupo)
      <div class="col-md-4">
        @foreach($grupo as $product)
          <div class="form-check">
            <input type="checkbox"
                  name="product_ids[]"
                  value="{{ $product->id }}"
                  class="form-check-input"
                  id="product_{{ $product->id }}"
                  {{ in_array($product->id, old('product_ids', isset($property) ? $property->products->pluck('id')->toArray() : [])) ? 'checked' : '' }}

            <label class="form-check-label" for="product_{{ $product->id }}">
              {{ $product->nome }}
            </label>
          </div>
        @endforeach
      </div>
    @endforeach
    @endif
  </div>


  </div>
</div>

