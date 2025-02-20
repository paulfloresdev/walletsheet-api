<?php

// Migration para la tabla `categories`
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // Nombre de la categoría
            $table->string('icon_name');  // Nombre del icono
            $table->timestamps();
        });

        // Insertar categorías predeterminadas
        DB::table('categories')->insert([
            ['name' => 'Servicios', 'icon_name' => ''],
            ['name' => 'Comida', 'icon_name' => ''],
            ['name' => 'Transporte', 'icon_name' => ''],
            ['name' => 'Salud', 'icon_name' => ''],
            ['name' => 'Educación', 'icon_name' => ''],
            ['name' => 'Entretenimiento', 'icon_name' => ''],
            ['name' => 'Ocio', 'icon_name' => ''],
            ['name' => 'Otros', 'icon_name' => ''],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
