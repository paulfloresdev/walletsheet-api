<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type');                 // Tipo de transacción (income, expense, payment)
            $table->decimal('amount', 10, 2);       // Monto de la transacción
            $table->string('concept');              // Concepto de la transacción
            $table->date('transaction_date');       // Fecha real de la transacción
            $table->date('accounting_date');        // Fecha en la que se aplicará a la contabilidad
            $table->foreignId('category_id')->constrained(); // Relación con la categoría
            $table->foreignId('account_id')->constrained();  // Relación con la cuenta
            $table->string('place')->nullable();    // Lugar de la transacción
            $table->string('note')->nullable();     // Nota adicional
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
