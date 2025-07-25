@extends('layouts.app')

@section('title', 'Editar Usuário')

@section('content')
<div class="content-box">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold mb-0">Editar Usuário</h2>
  </div>

  <form action="{{ route('users.update', $user) }}" method="POST">
    @csrf
    @method('PUT')
    @include('users._form', ['user' => $user])
    <div class="text-end">
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Voltar</a>
  <button type="submit" class="btn btn-success">Salvar</button>
</div>
  </form>


<!-- Modal Confirmação Desativar -->
@include('components.modal-desativaruser')



</div>
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

