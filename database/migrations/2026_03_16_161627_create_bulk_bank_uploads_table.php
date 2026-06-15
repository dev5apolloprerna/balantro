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
        Schema::create('bulk_bank_uploads', function (Blueprint $table) {
            $table->id();
            $table->integer('iPartyId')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->date('statement_date')->nullable();
            $table->date('synced_date')->nullable();
            $table->integer('total')->default(0);
            $table->integer('pending')->default(0);
            $table->integer('saved')->default(0);
            $table->integer('synced')->default(0);
            $table->string('status',50)->default('processing');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_bank_uploads');
    }
};
