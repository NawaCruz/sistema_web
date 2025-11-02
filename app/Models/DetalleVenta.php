<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;
    protected $table = 'detalle_ventas'; // Opcional, si el nombre de la tabla no es el plural automático del modelo
    protected $fillable = [
        'id_venta',
        'id_producto',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'descuento'
    ];

    // Relación con Venta
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id');
    }

}
