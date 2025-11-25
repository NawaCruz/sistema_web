<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // App\Http\Controllers\ClienteController.php
    public function buscarPorDni(Request $request)
    {
        $dni = $request->query('dni');
        $cliente = \App\Models\Cliente::where('dni', $dni)->first();

        if (!$cliente) {
            return response()->json(['error' => 'No se encontró cliente con ese DNI'], 404);
        }
        return response()->json([
            'id' => $cliente->id,
            'nombre' => $cliente->nombre,
            'dni' => $cliente->dni,
        ]);
    }

}
