<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::paginate(10);

        if (auth()->user()->role === 'admin') {
            return view('admin.proveedores.index', compact('proveedores'));
        } elseif (auth()->user()->role === 'empleado') {
            return view('empleado.proveedores.index', compact('proveedores'));
        } else {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'ruc' => 'required|string|max:11|unique:proveedores,ruc',
            'telefono' => 'required|string|max:15',
            'correo' => 'required|email|max:255',
            'direccion' => 'required|string|max:255',
            'contacto' => 'required|string|max:255',
            'estado' => 'required|string|max:50',
        ]);

        Proveedor::create($validatedData);
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        

        if (auth()->user()->role === 'empleado') {
            return redirect()->route('empleado.proveedores.index')->with('success', 'Proveedor creado');
        } elseif (auth()->user()->role === 'admin') {
            return redirect()->route('admin.proveedores.index')->with('success', 'Proveedor creado');
        } else {
            abort(403, 'No tienes permiso para crear proveedores.');
        }
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'ruc' => 'required|string|max:11',
            'telefono' => 'required|string|max:15',
            'correo' => 'required|email|max:255',
            'direccion' => 'required|string|max:255',
            'contacto' => 'required|string|max:255',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        $proveedor->update($request->all());
        // $proveedor->update($validatedData);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        if (auth()->user()->role === 'empleado') {
            return redirect()->route('empleado.proveedores.index')->with('success', 'Proveedor actualizado');
        } elseif (auth()->user()->role === 'admin') {
            return redirect()->route('admin.proveedores.index')->with('success', 'Proveedor actualizado');
        } else {
            abort(403, 'No tienes permiso para actualizar proveedores.');
        }
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->productos()->update(['proveedor_id' => null]);
        $proveedor->delete();

        if (auth()->user()->role === 'empleado') {
            return redirect()->route('empleado.proveedores.index')->with('success', 'Proveedor eliminado');
        } elseif (auth()->user()->role === 'admin') {
            return redirect()->route('admin.proveedores.index')->with('success', 'Proveedor eliminado');
        } else {
            abort(403, 'No tienes permiso para eliminar proveedores.');
        }
    }
}
