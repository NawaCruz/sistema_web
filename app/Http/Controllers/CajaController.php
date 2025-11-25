<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;


class CajaController extends Controller
{

    public function index(Request $request)
    {
        // 1) Leer filtros desde la querystring
        $buscador   = trim((string) $request->input('buscador', ''));          // usuario o estado
        $desde      = trim((string) $request->input('desde', ''));      // dd/mm/aaaa
        $hasta      = trim((string) $request->input('hasta', ''));      // dd/mm/aaaa

        // 2) Normalizar fechas (la vista muestra dd/mm/aaaa)
        $desdeDate = null;
        $hastaDate = null;

        if ($desde !== '' && Carbon::hasFormat($desde, 'd/m/Y')) {
            $desdeDate = Carbon::createFromFormat('d/m/Y', $desde)->startOfDay();
        }

        if ($hasta !== '' && Carbon::hasFormat($hasta, 'd/m/Y')) {
            $hastaDate = Carbon::createFromFormat('d/m/Y', $hasta)->endOfDay();
        }

        // 3) Query base con relaciones que necesita tu tabla
        $query = Caja::query()
            ->with(['user:id,name'])
            ->orderByDesc('id_caja');

        // 4) Filtro por texto: usuario o estado
        if ($buscador !== '') {
            $query->where(function ($sub) use ($buscador) {
                $sub->whereHas('user', function ($u) use ($buscador) {
                        $u->where('nombre', 'like', "%{$buscador}%");
                    })
                    ->orWhere('estado', 'like', "%{$buscador}%"); // 'abierta' / 'cerrada'
            });
        }

        // 5) Filtro por rango de fechas
        //    Regla práctica:
        //    - desde: se compara con fecha_apertura >= desde
        //    - hasta: si la caja está cerrada, fecha_cierre <= hasta; si está abierta, fecha_apertura <= hasta
        if ($desdeDate) {
            $query->whereDate('fecha_apertura', '>=', $desdeDate->toDateString());
        }
        if ($hastaDate) {
            $query->where(function ($sub) use ($hastaDate) {
                $sub->whereNotNull('fecha_cierre')
                        ->whereDate('fecha_cierre', '<=', $hastaDate->toDateString())
                    ->orWhere(function ($s2) use ($hastaDate) {
                        $s2->whereNull('fecha_cierre')
                            ->whereDate('fecha_apertura', '<=', $hastaDate->toDateString());
                    });
            });
        }

        // 7) Paginación preservando filtros
        $cajas = $query->paginate(10)->withQueryString();

        // 8) Bandera para el botón "Abrir caja"
        $cajaAbierta = Caja::query()
            ->where('id_user', Auth::id())
            ->where('estado', 'abierta')
            ->latest('id_caja')
            ->first();

        // 9) Retornar a la vista con lo que tu Blade espera
        $view = match (auth()->user()->role) {
            'admin'    => 'admin.cajas.index',
            'empleado' => 'empleado.cajas.index',
            default    => abort(403),
        };
        return view($view, [
            'cajas'          => $cajas,
            'cajaAbierta'    => $cajaAbierta,           // <— modelo o null
            'hayCajaAbierta' => (bool) $cajaAbierta,    // <— opcional
            'buscador'       => $buscador,
            'desde'          => $desde,
            'hasta'          => $hasta,
        ]);
    }

    // Método store: para abrir la caja
    public function store(Request $request)
    {
        // Validación primero
        $data = $request->validate([
            'monto_apertura' => ['required', 'numeric', 'min:0'],
        ]);

        return DB::transaction(function () use ($data) {

            // 0) SERIALIZAR POR USUARIO: bloquea la fila del usuario
            //    Esto asegura que dos aperturas simultáneas del mismo usuario no pasen.
            $user = User::where('id', auth()->id())->lockForUpdate()->first();

            // 1) Re-chequear si ya hay caja abierta (dentro de la transacción)
            $cajaAbierta = Caja::where('id_user', $user->id)
                ->where('estado', 'abierta')
                ->lockForUpdate()       // bloquea si existe; evita que otra transacción la altere en paralelo
                ->latest('id_caja')
                ->first();

            if ($cajaAbierta) {
                // Importante: salimos de la transacción con un redirect temprano
                return back()->with(
                    'error',
                    'Ya tienes una caja abierta (#'.$cajaAbierta->id_caja.') con monto '.$cajaAbierta->monto_apertura
                );
            }

            // 2) Crear la nueva caja (aquí estamos aún dentro de la transacción)
            Caja::create([
                'id_user'        => $user->id,
                'monto_apertura' => round($data['monto_apertura'], 2),
                'monto_total'    => round($data['monto_apertura'], 2),
                'estado'         => 'abierta',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // 3) Redirigir según rol (puedes tener 'role' o 'rol')
            $role  = $user->role ?? $user->rol;
            $route = match ($role) {
                'admin'    => 'admin.cajas.index',
                'empleado' => 'empleado.cajas.index',
                default    => abort(403),
            };

            return redirect()->route($route)->with('success', 'Caja abierta exitosamente');
        });
    }


    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'monto_cierre' => ['required','numeric','min:0'],
        ]);

        // Rol para redirección
        $role = auth()->user()->role ?? 'admin';
        $route = $role === 'empleado' ? 'empleado.cajas.index' : 'admin.cajas.index';

        return DB::transaction(function () use ($id, $data, $route) {

            // 1) Releer la caja con candados: que esté abierta y sea del usuario (o ajusta a tu política)
            $caja = Caja::whereKey($id)
                ->where('estado', 'abierta')
                ->where('id_user', auth()->id())   // si un admin también cierra de otros: quita esta línea
                ->lockForUpdate()
                ->first();

            if (!$caja) {
                return back()->with('error', 'No se pudo cerrar: la caja no está abierta o no existe.');
            }

            // 2) (Opcional) Calcula diferencia vs lo que llevas en el modelo
            //    Usa tu lógica real si tienes movimientos:
            $montoSistema = $caja->monto_total ?? $caja->monto_apertura;
            $diferencia   = round($data['monto_cierre'] - $montoSistema, 2);

            // 3) Cerrar
            $caja->update([
                'estado'        => 'cerrada',
                'monto_cierre'  => $data['monto_cierre'],
                'diferencia'    => $diferencia,          // <-- crea la columna si la quieres
                'fecha_cierre'  => now(),                // <-- asegúrate de tener este campo
                'updated_at'    => now(),
            ]);

            return redirect()->route($route)->with('success', 'Caja cerrada exitosamente.');
        });
    }
}
