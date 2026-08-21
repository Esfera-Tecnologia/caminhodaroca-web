<div class="eventos-container d-flex flex-column gap-3 pb-4">
    <div class="card shadow-sm border event-item">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="fw-semibold mb-0">Evento <span class="evento-index">{{ $loop->index+1 }}</span></h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-evento"
                        aria-label="Remover evento">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Imagem do Evento</label>
                    <input
                        accept=".jpg,.jpeg,.png,.gif"
                        onchange="previewLogo(this)"
                        class="form-control"
                        type="file"
                        name="events[{{ $event->id }}][images][]" />
                    <img src="{{ 
                        isset($event->images->first()?->image_url) 
                            ? $event->images->first()?->image_url
                            : asset('assets/teste3.png') 
                        }}"
                        class="preview-img mt-2" />
                </div>
                <div class="col">
                    <div class="row gy-2">
                        <div class="col">
                            <label class="form-label">Nome do Evento *</label>
                            @if(isset($event) && $event->id)
                                <input type="hidden" name="events[{{ $event->id }}][id]" id="id_{{ $event->id }}" class="form-control"
                                    value="{{ old('id', $event->id ?? '') }}" required>
                            @endif
                            <input type="text" name="events[{{ $event->id }}][name]" id="name_{{ $event->id }}" class="form-control"
                                value="{{ old('name', $event?->name ?? '') }}" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Link do Evento</label>
                            <input type="url" class="form-control" name="events[{{ $event->id }}][url]" id="url_{{ $event->id }}"
                                placeholder="https://"
                                value="{{ old('url', $event?->url ?? '') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descrição do Evento *</label>
                            <textarea class="form-control" id="description_{{ $event?->id??':id' }}"
                                    name="events[{{ $event->id }}][description]">{!! $event?->description !!}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
