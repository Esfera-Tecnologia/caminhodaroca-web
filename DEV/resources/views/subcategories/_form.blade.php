@php
  $status = old('status', $subcategory->status ?? 'ativo');
@endphp

<div class="row mb-4">
 
 

  <div class="col-md-4">
    <label for="categoria_id" class="form-label">Categoria *</label>
    <select name="category_id" id="category_id" class="form-select" required>
      <option value="">Selecione...</option>
      @foreach ($categories as $category)
            <option value="{{ $category->id }}" {{ old('category_id', $subcategory->category_id ?? '') == $category->id ? 'selected' : '' }}>
              {{ $category->nome }}
            </option>
          @endforeach
    </select>
    <div class="d-flex justify-content-end mt-1">
          <a href="#" class="d-block mt-2 text-secondary small" data-bs-toggle="modal" data-bs-target="#modalNovaCategoria">
          <i class="fas fa-plus me-1"></i> Adicionar nova Categoria
        </a>
      </div>

 
  </div>




  <div class="col-md-4">
    <label for="nome" class="form-label">Nome *</label>
    <input type="text" name="nome" id="nome" value="{{ old('nome', $subcategory->nome ?? '') }}" class="form-control @error('nome') is-invalid @enderror" required>
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






