<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SheetController extends Controller
{
    /**
     * Método que obtiene el listado de meses calendaricos con transacciones realizadas por el usuario.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransactionMonths(Request $request)
    {
        // Validamos que el usuario esté autenticado
        $userId = $request->user()->id;

        // Obtenemos el listado de meses y años donde el usuario tiene transacciones
        $months = Transaction::whereHas('account', function ($query) use ($userId) {
            $query->where('user_id', $userId); // Filtra las cuentas asociadas a este usuario
        })
            ->selectRaw('YEAR(accounting_date) as year, MONTH(accounting_date) as month')
            ->groupBy('year', 'month')
            ->get()
            ->map(function ($item) {
                // Formateamos el mes y año como 'Mes Año'
                return [
                    'month' => Carbon::createFromDate($item->year, $item->month, 1)->format('F'),
                    'year' => $item->year,
                    'month_number' => $item->month,
                ];
            });

        return response()->json($months);
    }

    /**
     * Método que obtiene el listado de transacciones de un mes y año específico con sumatorias por tipo (income, expense, payment).
     *
     * @param Request $request
     * @param int $month
     * @param int $year
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransactionsByMonth(Request $request, $month, $year)
    {
        // Validamos que el usuario esté autenticado
        $userId = $request->user()->id;

        // Obtenemos las transacciones del mes y año proporcionados
        $transactions = Transaction::whereHas('account', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->whereYear('transaction_date', $year)  // Cambié 'date' por 'transaction_date'
            ->whereMonth('transaction_date', $month)
            ->with(['category', 'account'])
            ->get();

        // Sumatoria de los tipos de transacciones
        $sumByType = $transactions->groupBy('type')->map(function ($group) {
            return $group->sum('amount');
        });

        return response()->json([
            'transactions' => $transactions,
            'sum_by_type' => [
                'income' => $sumByType['income'] ?? 0,
                'expense' => $sumByType['expense'] ?? 0,
                'payment' => $sumByType['payment'] ?? 0,
            ],
        ]);
    }

    /**
     * Método que obtiene el saldo inicial y final de las cuentas de débito hasta el último día del mes anterior.
     * También obtiene el saldo final al final del mes seleccionado.
     *
     * @param Request $request
     * @param int $month
     * @param int $year
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBalanceForMonth(Request $request, $month, $year)
    {
        // Validamos que el usuario esté autenticado
        $userId = $request->user()->id;

        // Fecha del último día del mes anterior
        $lastDayOfPreviousMonth = Carbon::create($year, $month, 1)->subDay();

        // Fecha del último día del mes actual
        $lastDayOfCurrentMonth = Carbon::create($year, $month, 1)->endOfMonth();

        // Obtener las cuentas del usuario
        $accounts = Account::where('user_id', $userId)->get();

        $balances = [];

        foreach ($accounts as $account) {
            // Determinar los tipos de transacción a considerar según el tipo de cuenta
            if ($account->type === 'debit') {
                $transactionTypes = ['income', 'payment', 'expense'];
            } else { // credit
                $transactionTypes = ['income', 'payment'];
            }

            // Saldo inicial: transacciones hasta el último día del mes anterior
            $initialBalance = Transaction::where('account_id', $account->id)
                ->whereIn('type', $transactionTypes)
                ->where('transaction_date', '<=', $lastDayOfPreviousMonth)
                ->sum('amount');

            // Saldo final: transacciones hasta el último día del mes actual
            $finalBalance = Transaction::where('account_id', $account->id)
                ->whereIn('type', $transactionTypes)
                ->where('transaction_date', '<=', $lastDayOfCurrentMonth)
                ->sum('amount');

            // Agregar los balances al arreglo
            $balances[] = [
                'account_id' => $account->id,
                'account_name' => $account->name,  // Suponiendo que tienes un campo 'name' en la cuenta
                'initial_balance' => $initialBalance,
                'final_balance' => $finalBalance,
            ];
        }

        return response()->json($balances);
    }

    public function getMonthData(Request $request, $month, $year)
    {
        // Validamos que el usuario esté autenticado
        $userId = $request->user()->id;

        // Fechas clave
        $lastDayOfPreviousMonth = Carbon::create($year, $month, 1)->subDay();
        $lastDayOfCurrentMonth = Carbon::create($year, $month, 1)->endOfMonth();

        // Obtener las transacciones del mes y año proporcionados
        $transactions = Transaction::whereHas('account', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->with(['category', 'account'])
            ->get();

        // Sumatoria de los tipos de transacciones
        $sumByType = $transactions->groupBy('type')->map(fn($group) => $group->sum('amount'));

        // Obtener cuentas del usuario
        $accounts = Account::where('user_id', $userId)->get();

        // Calcular balances
        $balances = $accounts->map(function ($account) use ($lastDayOfPreviousMonth, $lastDayOfCurrentMonth) {
            $transactionTypes = $account->type === 'debit' ? ['income', 'payment', 'expense'] : ['income', 'payment'];

            $initialBalance = Transaction::where('account_id', $account->id)
                ->whereIn('type', $transactionTypes)
                ->where('transaction_date', '<=', $lastDayOfPreviousMonth)
                ->sum('amount');

            $finalBalance = Transaction::where('account_id', $account->id)
                ->whereIn('type', $transactionTypes)
                ->where('transaction_date', '<=', $lastDayOfCurrentMonth)
                ->sum('amount');

            return [
                'account_id' => $account->id,
                'account_name' => $account->bank_name,
                'initial_balance' => $initialBalance,
                'final_balance' => $finalBalance,
            ];
        });

        return response()->json([
            'period' => $year . '-' . $month,
            'message' => 'Consulta realizada correctamente.',
            'transactions' => $transactions,
            'sum_by_type' => [
                'income' => $sumByType['income'] ?? 0,
                'expense' => $sumByType['expense'] ?? 0,
                'payment' => $sumByType['payment'] ?? 0,
            ],
            'balances' => $balances,
        ]);
    }

}
