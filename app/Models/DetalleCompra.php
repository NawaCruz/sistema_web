<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    use HasFactory;

    protected $table = 'detalle_compras';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_compra',
        'id_producto',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'id_compra');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    protected static function booted()
    {
        // Antes de guardar, asegurar que el subtotal sea consistente
        static::saving(function (DetalleCompra $detalle) {
            $detalle->subtotal = round(
                (float) $detalle->cantidad * (float) $detalle->precio_unitario,
                2
            );
        });

        // Al crear un detalle de compra, incrementar stock del producto
        static::created(function (DetalleCompra $detalle) {
            if ($producto = $detalle->producto) {
                $producto->increment('stock', (int) $detalle->cantidad);
            }

            $detalle->compra?->recalcularTotal();
        });

        // Al actualizar, ajustar stock segun la diferencia de cantidades
        static::updated(function (DetalleCompra $detalle) {
            $originalCantidad = (int) $detalle->getOriginal('cantidad');
            $nuevaCantidad = (int) $detalle->cantidad;
            $diff = $nuevaCantidad - $originalCantidad;

            if ($diff !== 0 && $producto = $detalle->producto) {
                $producto->increment('stock', $diff);
            }

            $detalle->compra?->recalcularTotal();
        });

        // Al eliminar, devolver el stock al producto
        static::deleted(function (DetalleCompra $detalle) {
            if ($producto = $detalle->producto) {
                $producto->decrement('stock', (int) $detalle->cantidad);
            }

            $detalle->compra?->recalcularTotal();
        });
    }
}
