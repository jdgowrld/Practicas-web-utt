@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Catálogo de Productos</h3>
            <p class="text-muted small mb-0">Práctica 1.4 - Filtros dinámicos, ordenamiento y paginación</p>
        </div>

        @if(Auth::user()->rol === 'admin')
            <a href="{{ route('productos.create') }}" class="btn btn-info text-dark fw-bold">+ Nuevo Producto</a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- PANEL DE FILTROS, ORDENAMIENTO Y PAGINACIÓN -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('productos.index') }}" method="GET" novalidate>
                <div class="row g-3">
                    <!-- Buscar por nombre -->
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Buscar por Nombre</label>
                        <input type="text" name="buscar" class="form-control form-control-sm"
                               value="{{ request('buscar') }}" placeholder="Ej. Producto Técnico...">
                    </div>

                    <!-- Filtrar por categoría -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Categoría</label>
                        <select name="categoria_id" class="form-select form-select-sm">
                            <option value="">Todas las categorías</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtrar por estado -->
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Estado</label>
                        <select name="estado" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="Disponible" {{ request('estado') == 'Disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="Agotado" {{ request('estado') == 'Agotado' ? 'selected' : '' }}>Agotado</option>
                        </select>
                    </div>

                    <!-- Rango de precios -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Rango de Precio ($)</label>
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.01" name="precio_min" class="form-control"
                                   value="{{ request('precio_min') }}" placeholder="Mín">
                            <input type="number" step="0.01" name="precio_max" class="form-control"
                                   value="{{ request('precio_max') }}" placeholder="Máx">
                        </div>
                    </div>

                    <!-- Ordenamiento -->
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Ordenar por</label>
                        <select name="orden" class="form-select form-select-sm">
                            <option value="recientes" {{ $orden == 'recientes' ? 'selected' : '' }}>Fecha: Más recientes</option>
                            <option value="antiguos" {{ $orden == 'antiguos' ? 'selected' : '' }}>Fecha: Más antiguos</option>
                            <option value="nombre_asc" {{ $orden == 'nombre_asc' ? 'selected' : '' }}>Nombre (A - Z)</option>
                            <option value="nombre_desc" {{ $orden == 'nombre_desc' ? 'selected' : '' }}>Nombre (Z - A)</option>
                            <option value="precio_desc" {{ $orden == 'precio_desc' ? 'selected' : '' }}>Precio: Mayor a Menor</option>
                            <option value="precio_asc" {{ $orden == 'precio_asc' ? 'selected' : '' }}>Precio: Menor a Mayor</option>
                            <option value="existencias_desc" {{ $orden == 'existencias_desc' ? 'selected' : '' }}>Existencias: Mayor a Menor</option>
                            <option value="existencias_asc" {{ $orden == 'existencias_asc' ? 'selected' : '' }}>Existencias: Menor a Mayor</option>
                        </select>
                    </div>

                    <!-- Registros por página -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Mostrar por página</label>
                        <select name="per_page" class="form-select form-select-sm">
                            @foreach([10, 15, 25, 50] as $cantidad)
                                <option value="{{ $cantidad }}" {{ $perPage == $cantidad ? 'selected' : '' }}>
                                    {{ $cantidad }} registros
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Botones de acción -->
                    <div class="col-md-5 d-flex align-items-end justify-content-end gap-2">
                        <a href="{{ route('productos.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar Filtros</a>
                        <button type="submit" class="btn btn-sm btn-dark fw-bold px-4">Aplicar Filtros</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- INFORMACIÓN DE PAGINACIÓN Y TOTALES -->
    <div class="d-flex justify-content-between align-items-center mb-2 px-1">
        <span class="text-muted small">
            Total encontrados: <strong>{{ $productos->total() }}</strong> registros
        </span>
        <span class="text-muted small">
            Página <strong>{{ $productos->currentPage() }}</strong> de <strong>{{ $productos->lastPage() }}</strong>
        </span>
    </div>

    <!-- TABLA DE PRODUCTOS -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th class="text-center">Existencias</th>
                        <th class="text-center">Estado</th>
                        <th>Registro</th>
                        @if(Auth::user()->rol === 'admin')
                            <th class="text-end">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $prod)
                    <tr>
                        <td>#{{ $prod->id }}</td>
                        <td class="fw-bold">{{ $prod->nombre }}</td>
                        <td class="small text-muted">{{ Str::limit($prod->descripcion, 45) }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $prod->categoria->nombre }}</span></td>
                        <td class="fw-bold text-success">${{ number_format($prod->precio, 2) }}</td>
                        <td class="text-center">{{ $prod->existencias }}</td>
                        <td class="text-center">
                            <span class="badge {{ $prod->estado === 'Disponible' ? 'bg-success' : 'bg-danger' }}">
                                {{ $prod->estado }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $prod->created_at->format('d/m/Y') }}</td>

                        @if(Auth::user()->rol === 'admin')
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('productos.edit', $prod) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                                <form action="{{ route('productos.destroy', $prod) }}" method="POST" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ Auth::user()->rol === 'admin' ? 9 : 8 }}" class="text-center text-muted py-4">
                            No se encontraron productos con los filtros seleccionados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- LINKS DE PAGINACIÓN (Bootstrap 5) -->
    <div class="d-flex justify-content-center">
        {{ $productos->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection