<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_transactions', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('upload_id');

            $table->string('invoice_no')->nullable();
            $table->date('date')->nullable();

            $table->string('gst_no')->nullable();
            $table->string('party_name')->nullable();
            $table->string('place_of_supply')->nullable();

            $table->decimal('amount',15,2)->nullable();
            $table->decimal('total_amount',15,2)->nullable();

            $table->string('purchase_ledger')->nullable();
            $table->string('item_name')->nullable();

            $table->integer('quantity')->nullable();
            $table->decimal('rate',15,2)->nullable();

            $table->decimal('sgst',10,2)->nullable();
            $table->decimal('cgst',10,2)->nullable();
            $table->decimal('igst',10,2)->nullable();

            $table->string('vchType')->nullable();

            $table->integer('iPartyId')->nullable();

            $table->string('status')->default('pending');

            $table->timestamps();

            $table->foreign('upload_id')
                ->references('id')
                ->on('bulk_purchase_uploads')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_transactions');
    }
}