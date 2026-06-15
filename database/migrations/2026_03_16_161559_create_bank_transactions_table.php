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
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('upload_id')->nullable();
            $table->integer('iPartyId')->nullable();
            $table->date('txn_date')->nullable();
            $table->string('narration',500)->nullable();
            $table->string('ref_no',150)->nullable();
            $table->decimal('debit',15,2)->default(0);
            $table->decimal('credit',15,2)->default(0);
            $table->decimal('balance',15,2)->default(0);
            $table->string('ledger_name',255)->nullable();
            $table->string('status',50)->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};
