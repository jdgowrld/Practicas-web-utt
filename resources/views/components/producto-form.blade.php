@props(['producto' => null, 'categorias'])

<div class="mb-3">
    <label class="form-label small fw-bold text-muted">Nombre del Producto</label>
    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
           value="{{ old('nombre', $producto?->nombre) }}" placeholder="Ej. Monitor Gamer 27 pulgadas">
    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label small fw-bold text-muted">Descripción</label>
    <textarea name="descripcion" rows="3" class="form-control @error('descripcion') is-invalid @enderror"
              placeholder="Características principales del producto">{{ old('descripcion', $producto?->descripcion) }}</textarea>
    @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label small fw-bold text-muted">Categoría</label>
        <select name="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror">
            <option value="">Selecciona categoría...</option>
            @foreach($categorias as $cat)
                <option value="{{ $cat->id }}" {{ old('categoria_id', $producto?->categoria_id) == $cat->id ? 'selected' : '' }}>
                    {{ $cat->nombre }}
                </option>
            @endforeach
        </select>
        @error('categoria_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label small fw-bold text-muted">Estado</label>
        <select name="estado" class="form-select @error('estado') is-invalid @enderror">
            <option value="Disponible" {{ old('estado', $producto?->estado) == 'Disponible' ? 'selected' : '' }}>Disponible</option>
            <option value="Agotado" {{ old('estado', $producto?->estado) == 'Agotado' ? 'selected' : '' }}>Agotado</option>
        </select>
        @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label small fw-bold text-muted">Precio ($ MXN)</label>
        <input type="number" step="0.01" name="precio" class="form-control @error('precio') is-invalid @enderror"
               value="{{ old('precio', $producto?->precio) }}" placeholder="0.00">
        @error('precio') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-4">
        <label class="form-label small fw-bold text-muted">Existencias (Stock)</label>
        <input type="number" name="existencias" class="form-control @error('existencias') is-invalid @enderror"
               value="{{ old('existencias', $producto?->existencias ?? 0) }}">
        @error('existencias') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>