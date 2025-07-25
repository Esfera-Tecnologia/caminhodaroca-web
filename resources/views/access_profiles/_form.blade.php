@php
  $selectedPermissions = $permissions ?? collect();
@endphp

<div class="row mb-4">
  <div class="col-md-4">
    <label for="nome" class="form-label">Nome *</label>
    <input type="text" class="form-control" name="nome" id="nome" value="{{ old('nome', $accessProfile->nome ?? '') }}" required>
    @error('nome')
      <div class="text-danger mt-1">{{ $message }}</div>
    @enderror

  </div>
  <div class="col-md-4">
    <label for="descricao" class="form-label">Descrição *</label>
    <input type="text" class="form-control" name="descricao" id="descricao" value="{{ old('descricao', $accessProfile->descricao ?? '') }}" required>
  </div>
  <div class="col-md-4">
    <label for="status" class="form-label">Status *</label>
    <select name="status" id="status" class="form-select" required>
      <option value="ativo" {{ old('status', $accessProfile->status ?? 'ativo') == 'ativo' ? 'selected' : '' }}>Ativo</option>
      <option value="inativo" {{ old('status', $accessProfile->status ?? 'ativo') == 'inativo' ? 'selected' : '' }}>Inativo</option>
    </select>
  </div>
</div>

<div class="mb-4">
  <label class="form-label">Permissões por Menu *</label>
  <div class="table-responsive">
    <table class="table table-bordered align-middle text-center">
      <thead class="table-light">
        <tr>
          <th>Menu</th>
          <th>Todos</th>
          <th>Visualizar</th>
          <th>Incluir</th>
          <th>Alterar</th>
          <th>Excluir</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($menus as $menu)
          @php
            $perm = $selectedPermissions->get($menu->id);
          @endphp
          <tr>
            <td>{{ $menu->nome }}</td>
            <td>
              <div class="form-check form-switch justify-content-center d-flex">
                @php
                  $view = old("permissions.{$menu->id}.view", $perm?->can_view);
                  $create = old("permissions.{$menu->id}.create", $perm?->can_create);
                  $edit = old("permissions.{$menu->id}.edit", $perm?->can_edit);
                  $delete = old("permissions.{$menu->id}.delete", $perm?->can_delete);
                  $isAll = $view && $create && $edit && $delete;
                @endphp

                <input class="form-check-input"
                      type="checkbox"
                      name="permissions[{{ $menu->id }}][all]"
                      {{ $isAll ? 'checked' : '' }}>

              </div>
            </td>
            <td>
              <div class="form-check form-switch justify-content-center d-flex">
                <input class="form-check-input" type="checkbox" name="permissions[{{ $menu->id }}][view]" {{ old("permissions.{$menu->id}.view", $perm?->can_view) ? 'checked' : '' }}>
              </div>
            </td>
            <td>
              <div class="form-check form-switch justify-content-center d-flex">
                <input class="form-check-input" type="checkbox" name="permissions[{{ $menu->id }}][create]" {{ old("permissions.{$menu->id}.create", $perm?->can_create) ? 'checked' : '' }}>
              </div>
            </td>
            <td>
              <div class="form-check form-switch justify-content-center d-flex">
                <input class="form-check-input" type="checkbox" name="permissions[{{ $menu->id }}][edit]" {{ old("permissions.{$menu->id}.edit", $perm?->can_edit) ? 'checked' : '' }}>
              </div>
            </td>
            <td>
              <div class="form-check form-switch justify-content-center d-flex">
                <input class="form-check-input" type="checkbox" name="permissions[{{ $menu->id }}][delete]" {{ old("permissions.{$menu->id}.delete", $perm?->can_delete) ? 'checked' : '' }}>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@push('scripts')
<script>
  $(function () {
    // Quando clicar em "Todos", marca ou desmarca os outros da linha
    $('input[name*="[all]"]').on('change', function () {
      const $row = $(this).closest('tr');
      const isChecked = $(this).is(':checked');

      $row.find('input[type="checkbox"]').each(function () {
        const name = $(this).attr('name');
        if (!name.endsWith('[all]')) {
          $(this).prop('checked', isChecked);
        }
      });
    });

    // Quando alterar qualquer permissão individual, atualiza o "Todos"
    $('input[type="checkbox"]').not('[name*="[all]"]').on('change', function () {
      const $row = $(this).closest('tr');
      const total = $row.find('input[type="checkbox"]').not('[name*="[all]"]').length;
      const checked = $row.find('input[type="checkbox"]').not('[name*="[all]"]').filter(':checked').length;

      $row.find('input[name*="[all]"]').prop('checked', total === checked);
    });
  });
</script>
@endpush





