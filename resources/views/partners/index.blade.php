@extends('layouts.app')

@section('title', 'Propriedades')

@section('content')
    <div class="content-box">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="fw-bold mb-0">Parceiros</h2>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="table-responsive">
            <table id="tabelaPropriedades" class="table table-bordered table-hover datatable">
                <thead class="table-light">
                <tr>
                    <th>Logo</th>
                    <th>Nome</th>
                    <th>Cidade</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach($partners as $partner)
                    @php($preapproved = $partner->preapproved_partner()->first())
                    @php($is_pending = $preapproved?->status == \App\Enums\PreapprovedPartnerStatus::PENDING)
                    <tr>
                        <td>
                            @if($partner->logo_url)
                                <img src="{{ $partner->logo_url }}" style="height: 40px;">
                            @endif
                        </td>
                        <td>{{ $partner->name }}</td>
                        <td>{{ $partner->city }}</td>
                        <td>
                            @if($is_pending)
                                <span
                                    class="badge bg-{{ $preapproved->status->badge() }}">{{ $preapproved->status->label() }}</span>
                            @else
                                <span
                                    class="badge bg-{{ $partner->status->badge() }}">{{ $partner->status->label() }}</span>
                            @endif
                        </td>
                        <td>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

