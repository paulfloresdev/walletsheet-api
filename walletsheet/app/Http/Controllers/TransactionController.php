<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    /**
     * Muestra todas las transacciones.
     */
    public function index()
    {
        $transactions = Transaction::with(['category', 'account'])->get();
        return response()->json($transactions);
    }

    /**
     * Crea una nueva transacción.
     */
    public function store(Request $request)
    {
        // Validación de los datos de la transacción
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:income,expense,payment',
            'amount' => 'required|numeric|min:0.01',
            'concept' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'accounting_date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'account_id' => 'required|exists:accounts,id',
            'place' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $transaction = Transaction::create([
            'type' => $request->type,
            'amount' => ($request->type === 'income') ? $request->amount : $request->amount * (-1),
            'concept' => $request->concept,
            'transaction_date' => $request->transaction_date,
            'accounting_date' => $request->accounting_date,
            'category_id' => $request->category_id,
            'place' => $request->place,
            'note' => $request->note,
            'account_id' => $request->account_id,
        ]);

        return response()->json([
            'message' => 'Transacción creada exitosamente.',
            'transaction' => $transaction
        ]);
    }

    /**
     * Muestra una transacción específica.
     */
    public function show($id)
    {
        $transaction = Transaction::with(['category', 'account'])->findOrFail($id);
        return response()->json($transaction);
    }

    /**
     * Actualiza una transacción existente.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:income,expense,payment',
            'amount' => 'required|numeric|min:0.01',
            'concept' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'accounting_date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'account_id' => 'required|exists:accounts,id',
            'place' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $transaction = Transaction::findOrFail($id);
        $transaction->update([
            'type' => $request->type,
            'amount' => $request->amount,
            'concept' => $request->concept,
            'transaction_date' => $request->transaction_date,
            'accounting_date' => $request->accounting_date,
            'category_id' => $request->category_id,
            'place' => $request->place,
            'note' => $request->note,
            'account_id' => $request->account_id,
        ]);

        return response()->json([
            'message' => 'Transacción actualizada exitosamente.',
            'transaction' => $transaction
        ]);
    }

    /**
     * Elimina una transacción.
     */
    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return response()->json([
            'message' => 'Transacción eliminada exitosamente.',
        ]);
    }
}
