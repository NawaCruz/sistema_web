<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DetalleVentasExport;
use Throwable;

class DetalleVentaController extends Controller
{
    public function index(Request $request)
    {
        $clientes  = Cliente::orderBy('nombre')->get(['id', 'nombre', 'apellido', 'dni']);
        $usuarios  = User::orderBy('name')->get(['id', 'name']);
        $productos = Producto::orderBy('nombre')->get(['id', 'nombre']);

        if ($request->filled('id_venta')) {
            $idVenta  = (int) $request->id_venta;

            $venta = Venta::with([
                'cliente:id,nombre,apellido,dni',
                'user:id,name',
                'metodoPago:id,nombre',
            ])->findOrFail($idVenta);

            $detalles = DetalleVenta::with(['producto:id,nombre,descripcion'])
                ->where('id_venta', $idVenta)
                ->orderByDesc('id')
                ->paginate(10); // <- NO appends, para no “contaminar” filtros

            $totalFiltrado = (clone $detalles)->total(); // o $detalles->total() en Laravel 10+

            return view(auth()->user()->role . '.detalleVentas.index', [
                'clientes'      => $clientes,
                'usuarios'      => $usuarios,
                'productos'     => $productos,
                'venta'         => $venta,        // útil para cabecera
                'detalles'      => $detalles,
                'totalFiltrado' => $totalFiltrado,
                'modo'          => 'solo_venta',  // flag para la vista
            ]);
        }

        // ==== Query base con eager loading ====
        $detalles = DetalleVenta::query()
            ->with([
                'venta.cliente:id,nombre,apellido,dni',
                'venta.user:id,name',
                'venta.metodoPago:id,nombre',
                'producto:id,nombre,descripcion'
            ]);

        // === Filtros ====
        // Cliente (nombre, apellido o DNI)
        if ($request->filled('cliente')) {
            $txt = trim($request->input('cliente'));

            $detalles->whereHas('venta.cliente', function ($sub) use ($txt) {
                $sub->where(function ($detalles) use ($txt) {
                    $detalles->where('nombre', 'like', "%{$txt}%")
                    ->orWhere('apellido', 'like', "%{$txt}%")
                    ->orWhere('dni', 'like', "%{$txt}%");
                });
            });
        }

        // Usuario (por id)
        if ($request->filled('usuario')) {
            $detalles->whereHas('venta', function ($sub) use ($request) {
                $sub->where('user_id', $request->input('usuario'));
            });
        }

        // Producto (por nombre o descripción, texto libre del datalist)
        if ($request->filled('producto')) {
            $txt = trim($request->input('producto'));
            $detalles->whereHas('producto', function ($sub) use ($txt) {
                $sub->where(function ($w) use ($txt) {
                    $w->where('nombre', 'like', "%{$txt}%")
                    ->orWhere('descripcion', 'like', "%{$txt}%");
                });
            });
        }

        // Rango de fechas (sobre la fecha de la venta)
        // Formato esperado: DD-MM-AAAA
        $from = $request->input('from');
        $to   = $request->input('to');

        // Normalizamos fechas si llegaron
        $fromDate = null;
        $toDate   = null;

        if (!empty($from)) {
            try {
                $fromDate = Carbon::createFromFormat('d-m-Y', $from)->startOfDay();
            } catch (Throwable $e) {
                $fromDate = null; // si falla el parseo, ignoramos el filtro
            }
        }

        if (!empty($to)) {
            try {
                $toDate = Carbon::createFromFormat('d-m-Y', $to)->endOfDay();
            } catch (\Throwable $e) {
                $toDate = null;
            }
        }

        if ($fromDate && $toDate) {
            $detalles->whereHas('venta', function ($sub) use ($fromDate, $toDate) {
                $sub->whereBetween('created_at', [$fromDate, $toDate]);
            });
        } elseif ($fromDate) {
            $detalles->whereHas('venta', function ($sub) use ($fromDate) {
                $sub->where('created_at', '>=', $fromDate);
            });
        } elseif ($toDate) {
            $detalles->whereHas('venta', function ($sub) use ($toDate) {
                $sub->where('created_at', '<=', $toDate);
            });
        }

        // ==== Conteo total filtrado (antes de paginar) ====
        $totalFiltrado = (clone $detalles)->count();

        // ==== Orden y paginación ====
        // Ajusta el orden si lo prefieres por id descendente o por fecha de venta
        $detalles = $detalles->orderByDesc('id')
            ->paginate(10)
            ->appends($request->query()); // conserva filtros en la paginación

        // Render
        return view(auth()->user()->role . '.detalleVentas.index', [
            'clientes'       => $clientes,
            'usuarios'       => $usuarios,
            'productos'      => $productos,
            'detalles'       => $detalles,
            'totalFiltrado'  => $totalFiltrado,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $nombre = 'detalle_ventas_'.now()->format('Ymd_His').'.xlsx';
        return Excel::download(new DetalleVentasExport($request->all()), $nombre);
    }
}
