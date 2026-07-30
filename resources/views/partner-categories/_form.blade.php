@php
  $status = old('status', $category->status ?? 'ativo');
  $experiencias = old('experiencias_oferecidas', $category->experiencias_oferecidas ?? false);
@endphp

<div class="row mb-4">
  <div class="col-md-8">
    <label for="titulo" class="form-label">Título *</label>
    <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $category->titulo ?? '') }}" class="form-control @error('titulo') is-invalid @enderror" required>
    @error('titulo')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-4">
    <label for="status" class="form-label">Status *</label>
    <select name="status" id="status" class="form-select" required>
      <option value="ativo" {{ $status == 'ativo' ? 'selected' : '' }}>Ativo</option>
      <option value="inativo" {{ $status == 'inativo' ? 'selected' : '' }}>Inativo</option>
    </select>
  </div>
</div>

<div class="row mb-4">
  <div class="col-md-6">
    <label for="experiencias_oferecidas" class="form-label">Experiências Oferecidas (Obrigatório) *</label>
    <select name="experiencias_oferecidas" id="experiencias_oferecidas" class="form-select" required>
      <option value="1" {{ $experiencias ? 'selected' : '' }}>Sim</option>
      <option value="0" {{ !$experiencias ? 'selected' : '' }}>Não</option>
    </select>
    <small class="text-muted">Define se o parceiro deverá preencher obrigatoriamente as experiências oferecidas.</small>
  </div>
</div>
