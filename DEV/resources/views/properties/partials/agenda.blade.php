<div class="schedule-section mt-4">
  <h5 class="mb-3">Dias e Horários de Funcionamento</h5>
  <div class="mb-3 d-flex flex-wrap gap-3">
    @php
      $tipos = ['todos' => 'Todos os Dias', 'fins' => 'Finais de Semana',  'agendamento' => 'Com Agendamento', 'personalizado' => 'Personalizado'];
      $tipoSelecionado = old('tipo_funcionamento', $property->tipo_funcionamento ?? 'todos');
    @endphp
    @foreach($tipos as $valor => $label)
      <div class="form-check">
        <input class="form-check-input" type="radio" name="tipo_funcionamento" value="{{ $valor }}" id="tipo_{{ $valor }}" {{ $tipoSelecionado === $valor ? 'checked' : '' }}>
        <label class="form-check-label" for="tipo_{{ $valor }}">{{ $label }}</label>
      </div>
    @endforeach
  </div>

  <div id="horarioTexto" class="{{ in_array($tipoSelecionado, ['feriados', 'agendamento', 'personalizado']) ? '' : 'd-none' }} mb-3">
    <label class="form-label">Informe sobre os dias de funcionamento:</label>
    <textarea name="observacoes_funcionamento" class="form-control" rows="3">{{ old('observacoes_funcionamento', $property->observacoes_funcionamento ?? '') }}</textarea>
  </div>

  <div id="agendaSemanal" class="day-grid">
    {{-- Blocos dos dias serão gerados via JS (igual ao HTML original) --}}
  </div>

  <input type="hidden" name="agenda_personalizada_json" id="agenda_personalizada_json">
</div>
@push('scripts')
<script>
  window.agendaPersonalizada = @json(old('agenda_personalizada', $property->agenda_personalizada ?? []));
</script>
@endpush