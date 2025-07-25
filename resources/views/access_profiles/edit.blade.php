@extends('layouts.app')

@section('title', 'Editar Perfil de Acesso')

@section('content')
<div class="content-box">
  <h2 class="fw-bold mb-4">Editar Perfil</h2>

  <form action="{{ route('access-profiles.update', $accessProfile) }}" method="POST">
    @csrf
    @method('PUT')
    @include('access_profiles._form', ['accessProfile' => $accessProfile, 'permissions' => $permissions ?? collect()])
    <div class="d-flex justify-content-end gap-2">
      <a href="{{ route('access-profiles.index') }}" class="btn btn-outline-secondary">Voltar</a>
      @if ($accessProfile->nome !== 'Administrador de Sistema')
        <button type="submit" class="btn btn-success">Salvar</button>
      @endif
      
    </div>
  </form>
</div>
<!-- Modal Confirmação Desativar -->
@include('components.modal-desativarperfil')

@endsection

@push('scripts')
<script>
$(document).ready(function () {
    let $statusSelect = $('#status');
    let $form = $statusSelect.closest('form');
    let $modal = $('#confirmStatusModal');
    let $confirmBtn = $('#confirmStatusSubmit');

    let originalAction;

    $statusSelect.on('change', function () {
        if ($(this).val() === 'inativo') {
            originalAction = $form.attr('action');
            $modal.modal('show');
        }
    });

    $confirmBtn.on('click', function () {
        $modal.modal('hide');
    });
});
</script>
@endpush
