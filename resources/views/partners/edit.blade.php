@extends('layouts.app')

@section('title', 'Editar Parceiro')

@section('content')
    <div class="content-box mx-auto" style="max-width: 1400px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="fw-bold mb-0">Editar Parceiro</h2>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Erros encontrados:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $erro)
                        <li>{{ $erro }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
                action="{{ $partner instanceof \App\Models\Partner?route('partners.update', $partner):route('partners.preapproved.update', $partner) }}"
                id="form-parceiro" novalidate method="POST"
                enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @if($partner instanceof \App\Models\PreapprovedPartner && auth()->user()->can_approve_property)
                <div class="alert alert-warning not-fade d-flex justify-content-between align-itens-center"
                     role="alert">
                    <span class="my-auto"><strong>Atualização pendente:</strong> Este parceiro possui alterações aguardando aprovação administrativa.</span>
                    <button class="btn btn-success aprove_partner">Aprovar Atualizações</button>
                </div>
            @endif
            @include('partners._form', ['partner' => $partner])
            <div class="text-end mt-4">
                <a href="{{ route('partners.index') }}" class="btn btn-outline-secondary">Voltar</a>
                <button type="submit" class="btn btn-success">Salvar</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $('.aprove_partner').click(function (e) {
            e.preventDefault();
            $('#form-parceiro').append('<input type="hidden" name="approve_updates" value="1">');
            $('#form-parceiro').submit();

        });
    </script>
@endpush


