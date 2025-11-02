<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;
    protected $table = 'ventas'; // Opcional, si el nombre de la tabla no es el plural automático del modelo
    protected $fillable = [
        'cliente_id',
        'user_id',
        'total',
        'metodo_pago_id'
    ];

    // Relaciones

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'id_venta');
    }
    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id', 'id');
    }
}
