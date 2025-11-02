<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recomendacion extends Model
{
    use HasFactory;
    protected $table = 'recomendaciones'; // Opcional, si el nombre de la tabla no es el plural automático del modelo
    protected $fillable = [
        'cliente_id',
        'producto_id',
        'producto_id',
        'motivo',
        'nivel_recomendacion',
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
