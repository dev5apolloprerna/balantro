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
        Schema::create('journal_transactions', function (Blueprint $table) {
            $table->id();

            $table->integer('iPartyId');
            $table->unsignedBigInteger('upload_id')->nullable();

            $table->string('journal_no')->nullable();
            $table->date('date')->nullable();

            $table->text('narration')->nullable();

            $table->decimal('total_debit', 18, 2)->default(0);
            $table->decimal('total_credit', 18, 2)->default(0);

            $table->string('status')->default('pending'); // pending / saved / submitted
            $table->boolean('is_delete')->default(0);

            $table->timestamps();

            // Optional FK
            // $table->foreign('upload_id')->references('id')->on('bulk_journal_uploads');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_transactions');
    }
};
