<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compra;
use App\Models\Proveedor;
use App\Models\User;
use App\Models\MetodoPago;

class CompraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $compras = Compra::with(['proveedor', 'user', 'metodoPago'])->orderBy('id', 'desc')->paginate(10);
        $proveedores = Proveedor::all();
        $usuarios = User::all();
        $metodosPago = MetodoPago::all();

        if (auth()->user()->role === 'admin') {
            return view('admin.compras.index', compact('compras', 'proveedores', 'usuarios', 'metodosPago'));
        } elseif (auth()->user()->role === 'empleado') {
            return view('empleado.compras.index', compact('compras', 'proveedores', 'usuarios', 'metodosPago'));
        } else {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'metodo_pago_id'   => 'required|exists:metodos_pago,id',
            'total' => 'required|numeric|min:0',
            'user_id' => 'required|exists:users,id',
        ]);

        Compra::create($request->only(['proveedor_id', 'metodo_pago_id', 'total', 'user_id']));
        if ($request->ajax()) {
            return response()->json(['message' => 'Compra creada correctamente']);
        }

        if (auth()->user()->role === 'empleado') {
            return redirect()->route('empleado.compras.index')->with('success', 'Compra creada');
        } elseif (auth()->user()->role === 'admin') {
            return redirect()->route('admin.compras.index')->with('success', 'Compra creada');
        }else {
            abort(403, 'No tienes permiso para crear compras.');
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'metodo_pago_id' => 'required|exists:metodos_pago,id',
            'user_id'      => 'required|exists:users,id',
        ]);
        Compra::findOrFail($id)->update($request->only(['proveedor_id', 'metodo_pago_id', 'user_id']));
        if ($request->ajax()) {
            return response()->json(['message' => 'Compra actualizada correctamente']);
        }

        if (auth()->user()->role === 'empleado') {
            return redirect()->route('empleado.compras.index')->with('success', 'Compra actualizada');
        } elseif (auth()->user()->role === 'admin') {
            return redirect()->route('admin.compras.index')->with('success', 'Compra actualizada');
        } else {
            abort(403, 'No tienes permiso para actualizar compras.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Compra $compra)
    {
        $compra->delete();
        if (auth()->user()->role === 'empleado') {
            return redirect()->route('empleado.compras.index')->with('success', 'Compra eliminada');
        } elseif (auth()->user()->role === 'admin') {
            return redirect()->route('admin.compras.index')->with('success', 'Compra eliminada');
        } else {
            abort(403, 'No tienes permiso para eliminar compras.');
        }
    }
}
