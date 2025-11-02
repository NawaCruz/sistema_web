<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetalleCompra;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\MetodoPago;
use App\Models\Compra;

class DetalleCompraController extends Controller
{
    public function index(Request $request)
    {
        // Validar que existe una compra válida
        $id = $request->query('id_compra');
        if (!$id || !Compra::find($id)) {
            abort(404, 'Compra no encontrada.');
        }

        $page = (int) $request->get('page', 1);
        $detalles = DetalleCompra::with(['producto'])->where('id_compra', $id)->orderBy('id', 'desc')->paginate(10)->appends(['id_compra' => $id]);
        $compras = Compra::with('proveedor')->where('id', $id)->get();
        $productos = Producto::all();

        if ($detalles->isEmpty() && $page > 1) {
            $last = max(1, $detalles->lastPage()); // normalmente será page-1
            $url = route(auth()->user()->role . '.detalleCompras.index') . "?id_compra={$id}&page={$last}";
            return redirect($url);
        }
        
        if (auth()->user()->role === 'admin') {
            return view('admin.detalleCompras.index', compact('detalles', 'productos', 'compras'));
        } elseif (auth()->user()->role === 'empleado') {
            return view('empleado.detalleCompras.index', compact('detalles', 'productos', 'compras'));
        } else {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_compra' => 'required|exists:compras,id',
            'id_producto' => 'required|exists:productos,id',
            'cantidad' => 'required|numeric|min:1',
            'precio_unitario' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
        ]);
        
        $subtotal = $request->cantidad * $request->precio_unitario;

        DetalleCompra::create($request->only(['id_compra', 'id_producto', 'cantidad', 'precio_unitario', 'subtotal']));
        if ($request->ajax()) {
            return response()->json(['message' => 'Detalle de compra creada correctamente']);
        }

        if (auth()->user()->role === 'empleado') {
            return redirect()->route('empleado.detalleCompras.index')->with('success', 'Detalle de compra creada');
        } elseif (auth()->user()->role === 'admin') {
            return redirect()->route('admin.detalleCompras.index')->with('success', 'Detalle de compra creada');
        }else {
            abort(403, 'No tienes permiso para crear compras.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_producto' => 'required|exists:productos,id',
            'cantidad' => 'required|numeric|min:1',
            'precio_unitario' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0'
        ]);
        DetalleCompra::findOrFail($id)->update($request->only(['id_producto', 'cantidad', 'precio_unitario', 'subtotal']));
        if ($request->ajax()) {
            return response()->json(['message' => 'Detalle de compra actualizado correctamente']);
        }
        if (auth()->user()->role === 'empleado') {
            return redirect()->route('empleado.detalleCompras.index')->with('success', 'Detalle de compra actualizada');
        } elseif (auth()->user()->role === 'admin') {
            return redirect()->route('admin.detalleCompras.index')->with('success', 'Detalle de compra actualizada');
        } else {
            abort(403, 'No tienes permiso para actualizar compras.');
        }
    }

    public function destroy(Request $request, DetalleCompra $detalleCompra)
    {
        $detalleCompra->delete();
        if ($request->ajax()) {
            return response()->json(['ok' => true]);
        }
        if (auth()->user()->role === 'empleado') {
            return redirect()->route('empleado.detalleCompras.index')->with('success', 'Detalle de compra eliminada');
        } elseif (auth()->user()->role === 'admin') {
            return redirect()->route('admin.detalleCompras.index')->with('success', 'Detalle de compra eliminada');
        } else {
            abort(403, 'No tienes permiso para eliminar compras.');
        }
    }
}

