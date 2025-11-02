<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use App\Models\Categoria; // Importamos el modelo Categoria
use App\Models\Proveedor; // Importamos el modelo Proveedor

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with(['categoria', 'proveedor'])->paginate(10);
        $categorias = Categoria::all(); // obtenemos las categorías
        $proveedores = Proveedor::all(); // obtenemos los proveedores

        if (auth()->user()->role === 'admin') {
            return view('admin.productos.index', compact('productos', 'categorias', 'proveedores'));
        } elseif (auth()->user()->role === 'empleado') {
            return view('empleado.productos.index', compact('productos', 'categorias', 'proveedores'));
        } else {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'         => 'required|string|max:255',
            'descripcion'    => 'required|string|max:1000',
            'precio_compra'  => 'nullable|numeric|min:0',
            'precio_venta'   => 'required|numeric|min:0',
            'stock'          => 'required|integer|min:0',
            'descuento'      => 'nullable|numeric|min:0|max:100',
            'categoria_id'   => 'required|exists:categorias,id',
            'proveedor_id'   => 'nullable|exists:proveedores,id',
        ]);

        Producto::create($request->all());
        if ($request->ajax()) {
            return response()->json(['message' => 'Producto creado correctamente']);
        }

        if (auth()->user()->role === 'empleado') {
            return redirect()->route('empleado.productos.index')->with('success', 'Producto creado');
        } elseif (auth()->user()->role === 'admin') {
            return redirect()->route('admin.productos.index')->with('success', 'Producto creado');
        }else {
            abort(403, 'No tienes permiso para crear productos.');
        }
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre'         => 'required|string|max:255',
            'descripcion'    => 'required|string|max:1000',
            'precio_compra'  => 'nullable|numeric|min:0',
            'precio_venta'   => 'required|numeric|min:0',
            'stock'          => 'required|integer|min:0',
            'descuento'      => 'nullable|numeric|min:0|max:100',
            'categoria_id'   => 'required|exists:categorias,id',
            'proveedor_id'   => 'nullable|exists:proveedores,id',
        ]);

        $producto->update($request->all());
        if ($request->ajax()) {
            return response()->json(['message' => 'Producto actualizado correctamente']);
        }
        
        if (auth()->user()->role === 'empleado') {
            return redirect()->route('empleado.productos.index')->with('success', 'Producto actualizado');
        } elseif (auth()->user()->role === 'admin') {
            return redirect()->route('admin.productos.index')->with('success', 'Producto actualizado');
        } else {
            abort(403, 'No tienes permiso para actualizar productos.');
        }
    }


    public function destroy(Producto $producto)
    {
        $producto->delete();
        if (auth()->user()->role === 'empleado') {
            return redirect()->route('empleado.productos.index')->with('success', 'Producto eliminado');
        } elseif (auth()->user()->role === 'admin') {
            return redirect()->route('admin.productos.index')->with('success', 'Producto eliminado');
        } else {
            abort(403, 'No tienes permiso para eliminar productos.');
        }
    }
}
