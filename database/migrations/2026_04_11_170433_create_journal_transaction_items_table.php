<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('journal_transaction_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('transaction_id');

            $table->unsignedBigInteger('ledger_id')->nullable();
            $table->string('ledger_name')->nullable();

            $table->enum('dr_cr', ['Dr', 'Cr']);

            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('credit', 18, 2)->default(0);

            $table->text('narration')->nullable();

            $table->timestamps();

           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_transaction_items');
    }
};
