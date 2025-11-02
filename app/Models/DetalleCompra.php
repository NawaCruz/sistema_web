<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    use HasFactory;

    protected $table = 'detalle_compras'; // Tabla de la BD
    protected $primaryKey = 'id'; // o 'id_detalle_compra' si tú lo definiste así

    protected $fillable = [
        'id_compra',
        'id_producto',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    // Relación con Compra
    public function compra()
    {
        return $this->belongsTo(Compra::class, 'id_compra');
    }

    protected static function booted()
    {
        // Antes de guardar, asegúrate que sub_total esté correcto
        static::saving(function ($d) {
            $d->subtotal = round(((float)$d->cantidad) * ((float)$d->precio_unitario), 2);
        });

        // Después de crear/actualizar, recalcula total de la compra
        static::saved(function ($d) {
            $d->compra?->recalcularTotal();
        });

        // Después de eliminar, recalcula total de la compra
        static::deleted(function ($d) {
            $d->compra?->recalcularTotal();
        });
    }

    // Relación con Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}
