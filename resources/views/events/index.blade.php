@extends('layouts.app')

@section('title', 'Eventos')

@section('content')
    <div class="content-box">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="fw-bold mb-0">Eventos</h2>
            @php
                $permissoes = getPermissao('events');
            @endphp
            @if ($permissoes?->can_create)
                <a href="{{ route('events.create') }}" class="btn btn-menu">
                    <i class="fas fa-plus me-1"></i> Adicionar Novo
                </a>
            @endif
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="table-responsive">
            <table id="tabelaEventos" class="table table-bordered table-hover datatable">
                <thead class="table-light">
                <tr>
                    <th>Título</th>
                    <th>Data</th>
                    <th>Local</th>
                    <th>Destaque</th>
                    <th>Propriedade Vinculada</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach($events as $event)
                    <tr>
                        <td>{{ $event->name }}</td>
                        <td>{{ $event->start_date?->format('d/m/Y') }}</td>
                        <td>{{ $event->city?->name }} / {{ $event->state?->code }}</td>
                        <td>
                            @if($event->is_highlight)
                                <span class="badge bg-success">Sim</span>
                            @else
                                <span class="badge bg-secondary">Não</span>
                            @endif
                        </td>
                        <td>
                            @if($event->properties->count() > 0)
                                {{ $event->properties->pluck('name')->implode(', ') }}
                            @else
                                <span class="text-muted">Nenhuma</span>
                            @endif
                        </td>
                        <td>
                            @if ($permissoes?->can_edit)
                                <a href="{{ route('events.edit', $event) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif
                            @if ($permissoes?->can_delete)
                                <button type="button"
                                        class="btn btn-sm btn-danger btn-delete"
                                        data-route="{{ route('events.destroy', $event) }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmDeleteModal">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
