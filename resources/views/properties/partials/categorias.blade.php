<div class="row g-3">
  
<div class="col-md-12">
    <label class="form-label">Selecione uma Categoria * </label>
    <div class="input-group">
      <select class="form-select" id="category_id">
        <option value="">Selecione uma Categoria</option>
        @foreach($categories as $categoria)
          <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
        @endforeach
      </select>
      <button class="btn btn-outline-success" type="button" onclick="adicionarCategoria()">Adicionar</button>
    </div>
     <div class="d-flex justify-content-end mt-1 addcategoria">
          <a href="#" class="d-block mt-2 text-secondary small" data-bs-toggle="modal" data-bs-target="#modalNovaCategoria">
          <i class="fas fa-plus me-1"></i> Adicionar Nova Categoria
        </a>
      </div>
  </div>


  <div id="categorias-container">
    @if(old('categoria_ids') || isset($property))
      @php
        $relacionamentos = old('categoria_ids', []);

        if (isset($property)) {
            $pivots = \DB::table('category_property_subcategories')
                ->where('property_id', $property->id)
                ->get();

            foreach ($pivots as $item) {
                $relacionamentos[$item->category_id][] = $item->subcategory_id; // pode ser null
            }
        }

        $categoriasSelecionadas = $categories->filter(fn($categoria) => array_key_exists($categoria->id, $relacionamentos))->values();
      @endphp

      @for ($i = 0; $i < $categoriasSelecionadas->count(); $i += 2)
        <div class="row g-3 mb-2">
          @for ($j = $i; $j < $i + 2 && $j < $categoriasSelecionadas->count(); $j++)
            @php
              $categoria = $categoriasSelecionadas[$j];
              $subSelecionadas = collect($relacionamentos[$categoria->id])->filter()->toArray();
              $semSubcategoriasSelecionadas = empty($subSelecionadas);
            @endphp

            <div class="col-md-6" id="categoria_{{ $categoria->id }}">
              <div class="categoria-block border p-3 h-100 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex justify-content-between">
                    <strong>{{ $categoria->nome }}</strong>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.col-md-6').remove()">Remover</button>
                  </div>
                  @if($categoria->subcategories->isNotEmpty())
                    @foreach($categoria->subcategories as $sub)
                      <div class="form-check mt-2">
                        <input class="form-check-input"
                              type="checkbox"
                              name="categoria_ids[{{ $categoria->id }}][]"
                              value="{{ $sub->id }}"
                              id="sub{{ $sub->id }}-{{ $categoria->id }}"
                              {{ in_array($sub->id, $subSelecionadas) ? 'checked' : '' }}>
                        <label class="form-check-label" for="sub{{ $sub->id }}-{{ $categoria->id }}">{{ $sub->nome }}</label>
                      </div>
                    @endforeach
                    @if($semSubcategoriasSelecionadas)
                      <input type="hidden" name="categoria_ids[{{ $categoria->id }}][]" value="">
                    @endif
                  @else
                    <div class="text-muted mt-2 small">
                      Essa categoria não possui subcategorias.
                    </div>
                    <input type="hidden" name="categoria_ids[{{ $categoria->id }}][]" value="">
                  @endif
                </div>

                <div class="d-flex justify-content-end mt-2">
                  <a href="#" class="text-secondary small" onclick="abrirModalSubcategoria({{ $categoria->id }}); return false;">
                    <i class="fas fa-plus me-1"></i> Adicionar Subcategoria
                  </a>
                </div>
              </div>
            </div>
          @endfor
        </div>
      @endfor
    @endif
  </div>

</div>

