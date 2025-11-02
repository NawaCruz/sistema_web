<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;
    // Opcional pero recomendable si el nombre de tabla no es el plural automático del modelo
    protected $table = 'compras';
    protected $fillable = [
        'user_id',
        'proveedor_id',
        'total',
        'metodo_pago_id'
    ];

    //relaciones
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class, 'id_compra');
    }
    public function recalcularTotal(): void
    {
        $suma = (float) $this->detalles()->sum('subtotal'); // suma de líneas
        $this->update(['total' => round($suma, 2)]);
    }
    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class);
    }
}
