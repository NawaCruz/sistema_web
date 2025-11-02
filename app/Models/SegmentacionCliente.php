<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegmentacionCliente extends Model
{
    use HasFactory;
    protected $table = 'segmentacion_clientes'; // Opcional, si el nombre de la tabla no es el plural automático del modelo
    protected $fillable = [
        'cliente_id',
        'edad',
        'genero',
        'ubicacion',
        'frecuencia_compra',
        'categoria_id'
    ];

    // Relación con Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id');
    }
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'id');
    }
}
