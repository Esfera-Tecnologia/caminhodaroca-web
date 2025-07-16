
<div class="modal fade" id="modalNovaCategoria" tabindex="-1" aria-labelledby="modalNovaCategoriaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formNovaCategoria" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="modalNovaCategoriaLabel">Nova Categoria</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="novaCategoriaNome" class="form-label">Nome *</label>
          <input type="text" class="form-control" id="novaCategoriaNome" name="nome" required>
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
  $('#formNovaCategoria').on('submit', function (e) {
    e.preventDefault();

    const form = $(this);
    const btn = form.find('button[type="submit"]');
    btn.prop('disabled', true).text('Salvando...');

    $.ajax({
      type: 'POST',
      url: '{{ route('categories.ajax.store') }}',
      data: form.serialize(),
      success: function (res) {
        if (res.success) {
          const option = $('<option>', {
            value: res.id,
            text: res.nome,
            selected: true
          });
          $('#category_id').append(option);

          $('#modalNovaCategoria').modal('hide');
          form[0].reset();
        }
      },
      error: function (xhr) {
        alert('Erro ao salvar a categoria: ' + xhr.responseJSON.message);
      },
      complete: function () {
        btn.prop('disabled', false).text('Salvar');
      }
    });
  });
</script>
@endpush