<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialCompra extends Model
{
    use HasFactory;
    protected $table = 'historial_compras'; // Opcional, si el nombre de la tabla no es el plural automático del modelo
    protected $fillable = [
        'cliente_id',
        'producto_id',
        'cantidad',
    ];

    // Relación con Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id');
    }

    // Relación con Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id');
    }
}
