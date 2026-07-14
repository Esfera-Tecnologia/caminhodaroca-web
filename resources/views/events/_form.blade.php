<div class="row g-4">
    <!-- Lado Esquerdo: Foto de Capa -->
    <div class="col-md-3">
        <label class="form-label">Foto de Capa *</label>
        <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept=".jpg,.jpeg,.png" onchange="previewImage(this)">
        <input type="hidden" name="image_base64" id="image_base64" value="{{ old('image_base64') }}">
        @error('image')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        <img id="preview-image" 
             src="{{ old('image_base64') ?: (isset($event->image) ? asset('storage/' . $event->image) : asset('assets/teste3.png')) }}" 
             class="preview-img mt-2" alt="Preview Capa">
        
        <div class="form-check form-switch mt-4">
            <input class="form-check-input" type="checkbox" name="is_highlight" id="is_highlight" {{ old('is_highlight', $event->is_highlight ?? false) ? 'checked' : '' }}>
            <label class="form-check-label fw-bold" for="is_highlight">Destacar este evento</label>
        </div>
    </div>

    <!-- Lado Direito: Dados do Evento -->
    <div class="col-md-9">
        <div class="row g-3">
            <!-- Título -->
            <div class="col-md-12">
                <label class="form-label">Título do Evento *</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $event->name ?? '') }}" required placeholder="Ex: Feira da Roça 2024">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Descrição Breve -->
            <div class="col-md-12">
                <label class="form-label">Descrição Breve *</label>
                <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" value="{{ old('description', $event->description ?? '') }}" required placeholder="Um resumo rápido do que é o evento">
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Organização Texto -->
            <div class="col-md-6">
                <label class="form-label">Nome da Organização (Exibição)</label>
                <input type="text" name="organization" class="form-control @error('organization') is-invalid @enderror" value="{{ old('organization', $event->organization ?? '') }}" placeholder="Ex: SENAR / Sindicato Rural">
                @error('organization')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Datas -->
            <div class="col-md-6">
                <label class="form-label">Data Inicial *</label>
                <input type="datetime-local" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', isset($event->start_date) ? $event->start_date->format('Y-m-d\TH:i') : '') }}" required>
                @error('start_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Data Final *</label>
                <input type="datetime-local" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', isset($event->end_date) ? $event->end_date->format('Y-m-d\TH:i') : '') }}" required>
                @error('end_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Localização: Estado -->
            <div class="col-md-6">
                <label class="form-label">Estado *</label>
                <select name="state_id" id="state_id" class="form-select select2 @error('state_id') is-invalid @enderror" required>
                    <option value="">Selecione o Estado</option>
                    @foreach($states as $state)
                        <option value="{{ $state->id }}" {{ old('state_id', $event->state_id ?? '') == $state->id ? 'selected' : '' }}>
                            {{ $state->name }}
                        </option>
                    @endforeach
                </select>
                @error('state_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Localização: Cidade -->
            <div class="col-md-6">
                <label class="form-label">Cidade *</label>
                <select name="city_id" id="city_id" class="form-select select2 @error('city_id') is-invalid @enderror" required>
                    <option value="">Selecione primeiro o Estado</option>
                    @if(isset($cities))
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('city_id', $event->city_id ?? '') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('city_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Propriedades Vinculadas (Múltiplo) -->
            <hr class="my-4">
            <div class="col-md-12">
                <label class="form-label">Propriedades Vinculadas</label>
                <select name="property_ids[]" class="form-select select2 @error('property_ids') is-invalid @enderror" multiple data-placeholder="Selecione as propriedades que participarão do evento">
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}" {{ in_array($property->id, old('property_ids', $selectedProperties ?? [])) ? 'selected' : '' }}>
                            {{ $property->name }}
                        </option>
                    @endforeach
                </select>
                @error('property_ids')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Link -->
            <div class="col-md-12">
                <label class="form-label">Link do Evento (Site/Inscrição)</label>
                <input type="url" name="url" class="form-control @error('url') is-invalid @enderror" value="{{ old('url', $event->url ?? '') }}" placeholder="https://exemplo.com/evento">
                @error('url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Sobre o Evento -->
            <hr class="my-4">
            <div class="col-md-12">
                <label class="form-label">Sobre o Evento (Descrição Completa) *</label>
                <textarea name="full_description" class="form-control @error('full_description') is-invalid @enderror" rows="5" required placeholder="Conte em detalhes o que terá no evento...">{{ old('full_description', $event->full_description ?? '') }}</textarea>
                @error('full_description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .select2-container--bootstrap-5 .select2-selection--multiple {
        min-height: 38px !important;
        border: 1px solid #ced4da !important;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
        padding: 4px 8px !important;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
        background-color: #e9ecef !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 4px !important;
        padding: 2px 8px !important;
        margin: 2px !important;
        font-size: 0.9rem !important;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Inicializa Select2 para campos simples
        $('.select2').not('[multiple]').select2({
            theme: 'bootstrap-5',
            width: '100%',
            language: {
                noResults: function() { return "Nenhum resultado encontrado"; }
            }
        });

        // Inicializa Select2 para o campo múltiplo de Propriedades
        $('select[multiple].select2').each(function() {
            $(this).select2({
                theme: 'bootstrap-5',
                width: '100%',
                closeOnSelect: false,
                allowClear: true,
                placeholder: $(this).attr('data-placeholder') || 'Selecione...',
                language: {
                    noResults: function() { return "Nenhum resultado encontrado"; }
                }
            });
        });

        // Lógica de Cidades Dinâmicas
        $('#state_id').on('change', function() {
            let stateId = $(this).val();
            let citySelect = $('#city_id');
            
            citySelect.html('<option value="">Carregando...</option>');
            
            if (stateId) {
                $.ajax({
                    url: '/ajax/cities/' + stateId,
                    type: 'GET',
                    success: function(cities) {
                        citySelect.html('<option value="">Selecione uma Cidade</option>');
                        cities.forEach(function(city) {
                            citySelect.append('<option value="' + city.id + '">' + city.name + '</option>');
                        });
                    },
                    error: function() {
                        citySelect.html('<option value="">Erro ao carregar cidades</option>');
                    }
                });
            } else {
                citySelect.html('<option value="">Selecione primeiro o Estado</option>');
            }
        });
    });

    // Preview de Imagem
    function previewImage(input) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-image').attr('src', e.target.result);
                $('#image_base64').val(e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
