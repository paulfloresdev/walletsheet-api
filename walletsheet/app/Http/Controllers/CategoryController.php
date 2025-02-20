<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Obtener todas las categorías
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories); // Devuelve las categorías como JSON
    }

    // Crear una nueva categoría
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon_name' => 'required|string|max:255', // Validación para el nombre del ícono
        ]);

        $category = Category::create($request->all()); // Crear la categoría con los datos enviados
        return response()->json($category, 201); // Retorna la categoría creada
    }

    // Actualizar una categoría
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'icon_name' => 'required|string|max:255', // Validación para el nombre del ícono
        ]);

        $category->update($request->all());
        return response()->json($category);
    }

    // Eliminar una categoría
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json(null, 204); // Respuesta sin contenido
    }
}
