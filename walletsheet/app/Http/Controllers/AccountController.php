<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    // 🔹 Obtener todas las cuentas del usuario autenticado
    public function index()
    {
        $userId = Auth::id();  // Usamos Auth::id() para obtener el ID del usuario autenticado
        $accounts = Account::where('user_id', $userId)->get();

        // Verificamos si el usuario tiene cuentas, en caso contrario retornamos un mensaje adecuado
        if ($accounts->isEmpty()) {
            return response()->json(['message' => 'No cuentas encontradas para este usuario.'], 404);
        }

        return response()->json($accounts);
    }

    // 🔹 Filtrar cuentas según el parámetro recibido
    public function filterByType($filter)
    {
        $userId = Auth::id();  // Obtener el ID del usuario autenticado

        if ($filter == 1) {
            // Retornar solo cuentas de débito
            $accounts = Account::where('user_id', $userId)
                ->where('type', 'debit')
                ->get();
        } else if ($filter == 2) {
            // Retornar cuentas de débito y crédito
            $accounts = Account::where('user_id', $userId)
                ->whereIn('type', ['debit', 'credit'])
                ->get();
        } else if ($filter == 3) {
            $debit = Account::where('user_id', $userId)
                ->where('type', 'debit')
                ->get();
            $credit = Account::where('user_id', $userId)
                ->where('type', 'credit')
                ->get();
            return response()->json(["filter" => $filter, "data" => ["debit" => $debit, "credit" => $credit]]);
        } else if ($filter == 4) {
            // Retornar ninguna
            $accounts = [];
        }

        // Verificar si se encontraron cuentas
        if ($accounts->isEmpty()) {
            return response()->json(['message' => 'No cuentas encontradas para este filtro.'], 404);
        }

        return response()->json(["filter" => $filter, "data" => $accounts]);
    }


    // 🔹 Crear una nueva cuenta
    public function store(Request $request)
    {
        $userId = Auth::id();  // Usamos Auth::id() para obtener el ID del usuario autenticado

        // Validamos los datos de la solicitud
        $request->validate([
            'type' => 'required|in:debit,credit',
            'bank_name' => 'required|string|max:255',
        ]);

        // Creamos la nueva cuenta
        $account = Account::create([
            'user_id' => $userId,
            'type' => $request->type,
            'bank_name' => $request->bank_name,
        ]);

        // Retornamos la cuenta creada con código de estado 201 (creado)
        return response()->json($account, 201);
    }

    // 🔹 Mostrar una cuenta específica
    public function show($id)
    {
        $userId = Auth::id();  // Usamos Auth::id() para obtener el ID del usuario autenticado

        $account = Account::where('id', $id)->where('user_id', $userId)->first();

        // Si no se encuentra la cuenta, retornamos un mensaje de error
        if (!$account) {
            return response()->json(['message' => 'Cuenta no encontrada.'], 404);
        }

        return response()->json($account);
    }

    // 🔹 Actualizar una cuenta
    public function update(Request $request, $id)
    {
        $userId = Auth::id();  // Usamos Auth::id() para obtener el ID del usuario autenticado

        $account = Account::where('id', $id)->where('user_id', $userId)->first();

        // Si no se encuentra la cuenta, retornamos un mensaje de error
        if (!$account) {
            return response()->json(['message' => 'Cuenta no encontrada.'], 404);
        }

        // Validamos los datos de la solicitud
        $request->validate([
            'bank_name' => 'string|max:255',
        ]);

        // Actualizamos la cuenta con los nuevos datos
        $account->update($request->only(['bank_name']));

        return response()->json($account);
    }

    // 🔹 Eliminar una cuenta
    public function destroy($id)
    {
        $userId = Auth::id();  // Usamos Auth::id() para obtener el ID del usuario autenticado

        $account = Account::where('id', $id)->where('user_id', $userId)->first();

        // Si no se encuentra la cuenta, retornamos un mensaje de error
        if (!$account) {
            return response()->json(['message' => 'Cuenta no encontrada.'], 404);
        }

        // Eliminamos la cuenta
        $account->delete();

        return response()->json(['message' => 'Cuenta eliminada correctamente']);
    }
}
