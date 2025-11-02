<?php

namespace App\Exports;

use App\Models\DetalleVenta;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DetalleVentasExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(private array $filtros = [])
    {
    }

    public function query()
    {
        $q = DetalleVenta::query()
            ->with([
                'venta.cliente:id,nombre,apellido,dni',
                'venta.user:id,name',
                'venta.metodoPago:id,nombre',
                'producto:id,nombre,descripcion'
            ])
            ->orderByDesc('id');

        // Filtros opcionales (útiles si los envías por querystring o formulario):
        // fecha_desde=YYYY-MM-DD, fecha_hasta=YYYY-MM-DD, cliente_id, usuario_id, producto (texto)
        if (!empty($this->filtros['fecha_desde'])) {
            $q->whereHas('venta', fn($s) => $s->whereDate('created_at', '>=', $this->filtros['fecha_desde']));
        }
        if (!empty($this->filtros['fecha_hasta'])) {
            $q->whereHas('venta', fn($s) => $s->whereDate('created_at', '<=', $this->filtros['fecha_hasta']));
        }
        if (!empty($this->filtros['cliente_id'])) {
            $q->whereHas('venta', fn($s) => $s->where('cliente_id', $this->filtros['cliente_id']));
        }
        if (!empty($this->filtros['usuario_id'])) {
            $q->whereHas('venta', fn($s) => $s->where('user_id', $this->filtros['usuario_id']));
        }
        if (!empty($this->filtros['producto'])) {
            $txt = trim($this->filtros['producto']);
            $q->whereHas('producto', function ($s) use ($txt) {
                $s->where(function ($w) use ($txt) {
                    $w->where('nombre', 'like', "%{$txt}%")
                      ->orWhere('descripcion', 'like', "%{$txt}%");
                });
            });
        }

        return $q;
    }

    public function headings(): array
    {
        return [
            'ID Detalle',
            'ID Venta',
            'Fecha Venta',
            'Cliente',
            'Vendedor',
            'Método Pago',
            'Producto',
            'Cantidad',
            'Precio Unitario',
            'Descuento',
            'Sub Total',
        ];
    }

    public function map($d): array
    {
        $venta    = $d->venta;
        $cliente  = $venta?->cliente;
        $usuario  = $venta?->user;
        $metPago  = $venta?->metodoPago;

        return [
            $d->id,
            $venta?->id ?? '—',
            optional($venta?->created_at)->format('Y-m-d H:i:s'),
            $cliente ? trim(($cliente->nombre ?? '').' '.($cliente->apellido ?? '')).' ('.$cliente->dni.')' : '—',
            $usuario?->name ?? '—',
            $metPago?->nombre ?? '—',
            $d->producto?->nombre ?? '—',
            (float) $d->cantidad,
            (float) $d->precio_unitario,
            sprintf('%.2f', $d->descuento ?? 0),
            (float) $d->subtotal,
        ];
    }

    public function columnFormats(): array
    {
        // Dos decimales para cantidades y montos (ajústalo si quieres formato moneda específico)
        return [
            'G' => NumberFormat::FORMAT_NUMBER_00, // Cantidad
            'H' => NumberFormat::FORMAT_NUMBER_00, // Precio Unit.
            'I' => NumberFormat::FORMAT_NUMBER_00, // Descuento
            'J' => NumberFormat::FORMAT_NUMBER_00, // Sub Total
        ];
    }
}
