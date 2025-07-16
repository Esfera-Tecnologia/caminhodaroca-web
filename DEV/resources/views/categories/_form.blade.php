@php
  $status = old('status', $category->status ?? 'ativo');
@endphp

<div class="row mb-4">
  <div class="col-md-8">
    <label for="nome" class="form-label">Nome *</label>
    <input type="text" name="nome" id="nome" value="{{ old('nome', $category->nome ?? '') }}" class="form-control @error('nome') is-invalid @enderror" required>
    @error('nome')
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

