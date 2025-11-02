<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    protected $table = 'caja';
    protected $primaryKey = 'id_caja';

    protected $fillable = [
        'id_user',
        'monto_apertura',
        'monto_total',
        'monto_cierre',
        'estado',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
