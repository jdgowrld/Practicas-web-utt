@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="fw-bold mb-0">Boleta Académica Individual</h5>
                </div>
                <div class="card-body p-4">
                    @if($miCalificacion)
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Nombre del Alumno:</span>
                                <span class="fw-bold">{{ $miCalificacion->alumno->name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Número de Faltas:</span>
                                <span class="fw-bold">{{ $miCalificacion->faltas }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Calificación Numérica:</span>
                                <span class="fw-bold fs-5">{{ number_format($miCalificacion->calificacion, 2) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="text-muted">Equivalencia Alfabética:</span>
                                <span class="badge bg-primary fs-5">{{ $miCalificacion->equivalencia }}</span>
                            </li>
                        </ul>
                    @else
                        <div class="alert alert-info mb-0 text-center">
                            Aún no tienes calificaciones ni faltas capturadas por tu profesor.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection