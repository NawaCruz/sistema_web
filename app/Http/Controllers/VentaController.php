<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Caja;
use App\Models\MetodoPago;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use App\Models\DetalleVenta;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\validator;
use Illuminate\Validation\ValidationException;
use Throwable;


class VentaController extends Controller
{

    public function index()
    {
        $ventas = Venta::with(['cliente', 'user', 'metodoPago', 'detalles.producto'])->orderBy('id', 'desc')->paginate(10);
        $productos = Producto::all();
        $clientes = Cliente::all();
        $usuarios = User::all();
        $metodosPago = MetodoPago::all();

        if (auth()->user()->role === 'admin') {
            return view('admin.ventas.index', compact('ventas', 'clientes', 'usuarios', 'metodosPago', 'productos'));
        } elseif (auth()->user()->role === 'empleado') {
            return view('empleado.ventas.index', compact('ventas', 'clientes', 'usuarios', 'metodosPago', 'productos'));
        } else {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    public function store(Request $request)
    {
        // Fuerza respuesta JSON para fetch/XHR
        $request->headers->set('Accept', 'application/json');

        // Validación (Laravel devolverá 422 JSON si falla)
        $data = $request->validate([
            'cliente_id'      => 'required|exists:clientes,id',
            'user_id'         => 'required|exists:users,id',
            'metodo_pago_id'  => 'required|exists:metodos_pago,id',
            'total'           => 'required|numeric|min:0', // validamos, pero no confiamos para guardar

            'items'                       => 'required|array|min:1',
            'items.*.id_producto'         => 'required|exists:productos,id|distinct',
            'items.*.cantidad'            => 'required|integer|min:1',
            'items.*.subtotal'            => 'required|numeric|min:0',
            'items.*.descuento'          => 'required|numeric|min:0',
        ], [
            'items.required' => 'Agregue al menos un ítem.',
        ]);

        try {
            DB::beginTransaction();

            // Creamos cabecera con total 0; se recalculará
            $venta = Venta::create([
                'cliente_id'     => (int)$data['cliente_id'],
                'user_id'        => (int)$data['user_id'],
                'metodo_pago_id' => (int)$data['metodo_pago_id'],
                'total'          => 0,
            ]);

            foreach ($data['items'] as $idx => $item) {
                $producto = Producto::lockForUpdate()->find($item['id_producto']);
                if (!$producto) {
                    throw ValidationException::withMessages([
                        "items.$idx.id_producto" => ["Producto no disponible."],
                    ]);
                }

                $descuento   = (float)($item['descuento'] ?? 0);
                $cantidad    = (int)($item['cantidad'] ?? 0);
                $precioUnit  = (float)($producto->precio_venta ?? 0);
                // Puedes ignorar el subtotal del front y calcularlo aquí:
                $subtotal    = round(max(0, $precioUnit * $cantidad - $descuento), 2);

                DetalleVenta::create([
                    'id_venta'        => $venta->id,
                    'id_producto'     => $producto->id,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => round($precioUnit, 2),
                    'descuento'       => round($descuento, 2),
                    'subtotal'        => $subtotal,
                ]);

                // Stock (permitiendo negativo)
                $producto->stock = (int)$producto->stock - $cantidad;
                $producto->save();
            }

            // Recalcular total desde BD (verdad única)
            $venta->total = (float) DetalleVenta::where('id_venta', $venta->id)->sum('subtotal');
            $venta->save();

            // 3) ACTUALIZAR CAJA del usuario dentro de la misma transacción
            $montoCaja = $venta->total;
            $caja = Caja::where('id_user', auth()->id())
                ->where('estado', 'abierta')
                ->lockForUpdate()              // evita condiciones de carrera
                ->latest('id_caja')
                ->first();

            if (!$caja) {
                throw ValidationException::withMessages([
                    'caja' => ['No tienes una caja abierta para registrar esta venta.'],
                ]);
            }
            $caja->monto_total = round($caja->monto_total + $montoCaja, 2);
            $caja->save();

            DB::commit();

            return response()->json([
                'ok'       => true,
                'message'  => 'Venta creada correctamente',
                'venta_id' => $venta->id,
                'total'    => number_format($venta->total, 2),
                'creados'  => count($data['items']),
            ], 201);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('VENTAS.store ERROR', ['msg' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            // Si es error de validación manual, Laravel lo formatea; para otros, 500:
            return response()->json([
                'ok'      => false,
                'message' => 'No se pudo crear la venta: '.$e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        Log::info('VentaController@update ejecutado', ['venta_id' => $id, 'payload' => $request->all()]);
        $request->headers->set('Accept', 'application/json');

        $validator = Validator::make($request->all(), [
            'cliente_id'     => 'required|exists:clientes,id',
            'metodo_pago_id' => 'required|exists:metodos_pago,id',
            'total'          => 'required|numeric|min:0', // se valida, pero NO se confía para guardar
            'nuevos'         => 'array',
            'editados'       => 'array',
            'eliminados'     => 'array',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'ok'      => false,
                'message' => 'Revisa los campos del formulario.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Normaliza arrays
        $nuevos     = $request->input('nuevos', []);
        $editados   = $request->input('editados', []);
        $eliminados = $request->input('eliminados', []);

        DB::beginTransaction();
        try {
            // Bloqueo pesimista para coherencia en stock
            $venta = Venta::lockForUpdate()->findOrFail($id);

            // Actualiza cabecera (NO toques 'total' aún)
            $venta->cliente_id     = (int)$request->cliente_id;
            $venta->metodo_pago_id = (int)$request->metodo_pago_id;
            $venta->save();

            // 1) ELIMINADOS: devolver stock y borrar detalle
            $nEliminados = 0;
            if (!empty($eliminados)) {
                foreach ($eliminados as $detalleId) {
                    $detalle = DetalleVenta::lockForUpdate()->find((int)$detalleId);
                    if ($detalle) {
                        $producto = Producto::lockForUpdate()->find($detalle->id_producto);
                        if ($producto) {
                            $producto->increment('stock', (int)$detalle->cantidad);
                        }
                        $detalle->delete();
                        $nEliminados++;
                    }
                }
            }

            // 2) EDITADOS: ajustar stock por diferencia y recalcular subtotal en servidor
            $nActualizados = 0;
            if (!empty($editados)) {
                foreach ($editados as $edit) {
                    $detalle = DetalleVenta::lockForUpdate()->find((int)($edit['detalle_id'] ?? 0));
                    if (!$detalle) continue;

                    $producto = Producto::lockForUpdate()->find((int)($edit['producto_id'] ?? 0));
                    if (!$producto) {
                        throw new \RuntimeException("Producto no encontrado para el detalle {$edit['detalle_id']}.");
                    }

                    $cantidad_nueva     = (int)($edit['cantidad_nueva'] ?? 0);
                    $cantidad_original  = (int)($edit['cantidad_original'] ?? 0);
                    $precio             = (float)($edit['precio'] ?? 0);
                    $descuento          = (float)($edit['descuento'] ?? 0);

                    $diff = $cantidad_nueva - $cantidad_original; // >0 resta más stock, <0 devuelve
                    if ($diff > 0 && $producto->stock < $diff) {
                        throw new \RuntimeException("Stock insuficiente del producto {$producto->id} (+{$diff}).");
                    }
                    $producto->stock -= $diff;
                    $producto->save();

                    // Subtotal = precio * cantidad - descuento (no negativo), calculado en backend
                    $subtotal = round(max(0, ($precio * $cantidad_nueva) - $descuento), 2);

                    $detalle->cantidad        = $cantidad_nueva;
                    $detalle->precio_unitario = $precio;
                    $detalle->descuento       = $descuento;
                    $detalle->subtotal        = $subtotal;
                    $detalle->save();

                    $nActualizados++;
                }
            }

            // 3) NUEVOS: crear detalle, descontar stock y calcular subtotal en backend
            $nCreados = 0;
            if (!empty($nuevos)) {
                foreach ($nuevos as $nuevo) {
                    $producto = Producto::lockForUpdate()->find((int)($nuevo['producto_id'] ?? 0));
                    if (!$producto) {
                        throw new \RuntimeException("Producto no encontrado para crear detalle.");
                    }

                    $cantidad  = (int)($nuevo['cantidad'] ?? 0);
                    $precio    = (float)($nuevo['precio'] ?? 0);
                    $descuento = (float)($nuevo['descuento'] ?? 0);

                    if ($cantidad > 0 && $producto->stock < $cantidad) {
                        throw new \RuntimeException("Stock insuficiente del producto {$producto->id} (+{$cantidad}).");
                    }

                    $subtotal = round(max(0, ($precio * $cantidad) - $descuento), 2);

                    $detalle = DetalleVenta::create([
                        'id_venta'        => $venta->id,
                        'id_producto'     => (int)$nuevo['producto_id'],
                        'cantidad'        => $cantidad,
                        'precio_unitario' => $precio,
                        'descuento'       => $descuento,
                        'subtotal'        => $subtotal,
                    ]);

                    if ($cantidad > 0) {
                        $producto->decrement('stock', $cantidad);
                    }

                    $nCreados++;
                }
            }

            // 4) Recalcular TOTAL desde BD (verdad única)
            $venta->total = (float) DetalleVenta::where('id_venta', $venta->id)->sum('subtotal');
            $venta->save();

            DB::commit();

            return response()->json([
                'ok'           => true,
                'message'      => 'Venta actualizada correctamente',
                'venta_id'     => $venta->id,
                'total'        => number_format($venta->total, 2),
                'creados'      => $nCreados,
                'actualizados' => $nActualizados,
                'eliminados'   => $nEliminados,
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('VENTAS.update ERROR', [
                'venta_id' => $id,
                'msg'      => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);
            // si quieres distinguir stock insuficiente, podrías devolver 409
            return response()->json([
                'ok'      => false,
                'message' => 'Error al actualizar: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function destroy(Request $request, $id)
    {
        // Fuerza JSON en las respuestas
        $request->headers->set('Accept', 'application/json');

        DB::beginTransaction();
        try {
            // Trae la venta con sus detalles
            $venta = Venta::with('detalles')->findOrFail($id);

            // Reponer stock y borrar detalles
            foreach ($venta->detalles as $detalle) {
                // tus columnas: id_producto, cantidad
                $producto = Producto::lockForUpdate()->find($detalle->id_producto);
                if ($producto) {
                    $producto->stock += (int)$detalle->cantidad;
                    $producto->save();
                }
                $detalle->delete();
            }

            // Borra la venta
            $venta->delete();

            DB::commit();

            return response()->json([
                'ok'       => true,
                'message'  => 'Venta eliminada correctamente.',
                'venta_id' => (int)$id,
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('VENTAS.destroy ERROR', ['venta_id' => $id, 'msg' => $e->getMessage()]);

            return response()->json([
                'ok'      => false,
                'message' => 'No se pudo eliminar la venta: '.$e->getMessage(),
            ], 500);
        }
    }
}
