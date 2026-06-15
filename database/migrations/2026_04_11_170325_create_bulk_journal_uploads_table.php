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
        Schema::create('bulk_journal_uploads', function (Blueprint $table) {
            $table->id();
            $table->integer('iPartyId');
            $table->string('batch_id')->unique();
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->integer('total_rows')->default(0);
            $table->integer('processed_rows')->default(0);
            $table->integer('total')->default(0);
            $table->integer('saved')->default(0);
            $table->integer('pending')->default(0);
            $table->integer('synced')->default(0);
            $table->string('type')->nullable();
            $table->string('status')->default('pending'); // pending / processing / completed
            $table->date('statement_date')->nullable();
            $table->date('synced_date')->nullable();           
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_journal_uploads');
    }
};
