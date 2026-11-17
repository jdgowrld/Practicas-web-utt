@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4">Editar Producto: {{ $producto->nombre }}</h4>

                    <form action="{{ route('productos.update', $producto) }}" method="POST" novalidate>
                        @csrf
                        @method('PUT')
                        <x-producto-form :producto="$producto" :categorias="$categorias" />

                        <div class="d-flex justify-content-between">
                            <a href="{{ session('productos_url_retorno', route('productos.index')) }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-warning fw-bold">Actualizar Producto</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection