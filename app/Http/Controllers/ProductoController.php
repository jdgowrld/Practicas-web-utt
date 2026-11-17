<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        // Validar cantidad por página permitida (10, 15, 25, 50)
        $perPage = in_array((int) $request->get('per_page'), [10, 15, 25, 50])
            ? (int) $request->get('per_page')
            : 10;

        $orden = $request->get('orden', 'recientes');
        $filtros = $request->only(['buscar', 'categoria_id', 'precio_min', 'precio_max', 'estado']);

        $query = Producto::with('categoria')->filtrar($filtros);

        // Ordenamiento dinámico
        switch ($orden) {
            case 'nombre_asc':
                $query->orderBy('nombre', 'asc');
                break;
            case 'nombre_desc':
                $query->orderBy('nombre', 'desc');
                break;
            case 'precio_desc':
                $query->orderBy('precio', 'desc');
                break;
            case 'precio_asc':
                $query->orderBy('precio', 'asc');
                break;
            case 'existencias_desc':
                $query->orderBy('existencias', 'desc');
                break;
            case 'existencias_asc':
                $query->orderBy('existencias', 'asc');
                break;
            case 'antiguos':
                $query->orderBy('created_at', 'asc')->orderBy('id', 'asc');
                break;
            default: // recientes
                $query->orderBy('created_at', 'desc')->orderBy('id', 'desc');
                break;
        }

        // Paginación conservando los parámetros de búsqueda y ordenamiento
        $productos = $query->paginate($perPage)->withQueryString();
        $categorias = Categoria::orderBy('nombre')->get();
        session(['productos_url_retorno' => $request->fullUrl()]);
        return view('productos.index', compact('productos', 'categorias', 'perPage', 'orden'));
    }

    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('productos.create', compact('categorias'));
    }

    public function store(StoreProductoRequest $request)
    {
        Producto::create($request->validated());

        return redirect(session('productos_url_retorno', route('productos.index')))
            ->with('success', 'Producto registrado correctamente.');
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(UpdateProductoRequest $request, Producto $producto)
    {
    $producto->update($request->validated());

    return redirect(session('productos_url_retorno', route('productos.index')))
        ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
    $producto->delete();

    return redirect(session('productos_url_retorno', route('productos.index')))
        ->with('success', 'Producto eliminado del catálogo.');
    }
}