<div class="modal fade" id="modalNovaSubcategoria" tabindex="-1" aria-labelledby="modalNovaSubcategoriaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formNovaSubcategoria" class="modal-content">
      @csrf
      <div id="erroNovaSubcategoria" class="mt-2"></div>
      <div class="modal-header">
        <h5 class="modal-title" id="modalNovaSubcategoriaLabel">Nova Subcategoria</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="subcategoriaCategoriaId" class="form-label">Categoria *</label>
          <select class="form-select" id="subcategoriaCategoriaId" name="category_id" required>
            <option value="">Selecione uma categoria</option>
            @if(isset($categories))
              @foreach($categories as $categoria)
                <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
              @endforeach
            @endif
          </select>
        </div>
        <div class="mb-3">
          <label for="novaSubcategoriaNome" class="form-label">Nome da Subcategoria *</label>
          <input type="text" class="form-control" id="novaSubcategoriaNome" name="nome" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Salvar</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  $('#formNovaSubcategoria').off('submit').on('submit', function (e) {
    e.preventDefault();

    const form = $(this);
    const btn = form.find('button[type="submit"]');
    const erroContainer = $('#erroNovaSubcategoria');
    const categoryId = $('#subcategoriaCategoriaId').val();

    btn.prop('disabled', true).text('Salvando...');
    erroContainer.empty();

    $.ajax({
      type: 'POST',
      url: '{{ route('subcategories.ajax.store') }}',
      data: form.serialize(),
      success: function (res) {
        if (res.success) {
          // Atualiza o objeto global de subcategorias
          if (!window.subcategoriasPorCategoria[categoryId]) {
            window.subcategoriasPorCategoria[categoryId] = [];
          }
          
          window.subcategoriasPorCategoria[categoryId].push({
            id: res.id,
            nome: res.nome
          });

          // Se a categoria já está adicionada ao formulário, atualiza o bloco
          const categoriaBloco = $(`#categoria_${categoryId}`);
          if (categoriaBloco.length > 0) {
            const novaSubcategoria = `
              <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox"
                      name="categoria_ids[${categoryId}][]" value="${res.id}"
                      id="sub${res.id}-${categoryId}">
                <label class="form-check-label" for="sub${res.id}-${categoryId}">
                  ${res.nome}
                </label>
              </div>
            `;
            
            // Procura pela div de conteúdo (primeiro filho do categoria-block)
            const contentDiv = categoriaBloco.find('.categoria-block > div:first-child');
            
            // Remove o texto "sem subcategorias" se existir
            contentDiv.find('.text-muted').remove();
            
            // Remove o input hidden se for a primeira subcategoria
            if (contentDiv.find('.form-check').length === 0) {
              contentDiv.find('input[type="hidden"][value=""]').remove();
            }
            
            // Adiciona a nova subcategoria na div de conteúdo
            contentDiv.append(novaSubcategoria);
            
            // Adiciona o input hidden para subcategorias se não existir
            if (contentDiv.find('.subcategoria-hidden').length === 0) {
              contentDiv.append(`
                <input type="hidden" class="subcategoria-hidden" name="categoria_ids[${categoryId}][]" value="">
              `);
            }
          }

          $('#modalNovaSubcategoria').modal('hide');
          form[0].reset();
          erroContainer.empty();
        }
      },
      error: function (xhr) {
        let msg = 'Erro inesperado.';

        if (xhr.status === 422 && xhr.responseJSON?.errors) {
          const erros = xhr.responseJSON.errors;
          msg = Object.values(erros).map(m => `<li>${m}</li>`).join('');
          erroContainer.html(`<ul class="text-danger small mb-0">${msg}</ul>`);
        } else {
          erroContainer.html(`<div class="text-danger small">${xhr.responseJSON?.message || msg}</div>`);
        }
      },
      complete: function () {
        btn.prop('disabled', false).text('Salvar');
      }
    });
  });

  // Função para abrir o modal com categoria pré-selecionada
  window.abrirModalSubcategoria = function(categoryId) {
    $('#subcategoriaCategoriaId').val(categoryId);
    $('#modalNovaSubcategoria').modal('show');
  };

  // Limpar o formulário quando o modal for fechado
  $('#modalNovaSubcategoria').on('hidden.bs.modal', function () {
    $('#formNovaSubcategoria')[0].reset();
    $('#erroNovaSubcategoria').empty();
  });
</script>
@endpush