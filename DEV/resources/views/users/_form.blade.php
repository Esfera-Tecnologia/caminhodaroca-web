@php
  $status = old('status', $user->status ?? 'ativo');
@endphp

<div class="row mb-4">
  <div class="col-md-6">
    <label for="name" class="form-label">Nome *</label>
    <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" class="form-control" required>
  </div>
  <div class="col-md-6">
    <label for="email" class="form-label">E-mail *</label>
    <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" class="form-control" required>
    @error('email')
      <div class="text-danger mt-1">{{ $message }}</div>
    @enderror
  </div>
</div>

<div class="row mb-4">
  <div class="col-md-6">
    <label for="access_profile_id" class="form-label">Perfil de Acesso *</label>
    <select name="access_profile_id" id="access_profile_id" class="form-select" required>
      <option value="">Selecione...</option>
      @foreach ($accessProfiles as $profile)
        <option value="{{ $profile->id }}" {{ old('access_profile_id', $user->access_profile_id ?? '') == $profile->id ? 'selected' : '' }}>
          {{ $profile->nome }}
        </option>
      @endforeach
    </select>
     @error('nome')
      <div class="text-danger mt-1">{{ $message }}</div>
    @enderror
  </div>
  <div class="col-md-6">
    <label for="status" class="form-label">Status *</label>
    <select name="status" id="status" class="form-select" required>
      <option value="ativo" {{ $status == 'ativo' ? 'selected' : '' }}>Ativo</option>
      <option value="inativo" {{ $status == 'inativo' ? 'selected' : '' }}>Inativo</option>
    </select>
  </div>
</div>


