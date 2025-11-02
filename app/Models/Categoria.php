<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias'; // Opcional, si el nombre de la tabla no sigue la convención plural
    protected $fillable = ['nombre', 'descripción'];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
