<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Definir los campos que se pueden asignar masivamente
    protected $fillable = [
        'name',       // Nombre de la categoría
        'icon_name',  // Nombre del ícono de Material-UI
    ];

    // Si quieres hacer alguna relación, puedes agregarla aquí
}
