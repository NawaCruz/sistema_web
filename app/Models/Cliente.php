<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    protected $table = 'clientes'; // Opcional, si el nombre de la tabla no es el plural automático del modelo
    protected $fillable = [
        'dni',
        'nombre',
        'apellido',
        'correo',
        'telefono',
        'direccion'
    ];

    // Relaciones
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'cliente_id');
    }
    public function historicoCompras()
    {
        return $this->hasMany(Compra::class, 'cliente_id');
    }
    public function recomendaciones()
    {
        return $this->hasMany(Recomendacion::class, 'cliente_id');
    }
    public function segmentacionClientes()
    {
        return $this->hasMany(SegmentacionCliente::class, 'cliente_id');
    }
}
