<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBulkPurchaseUploadsTable extends Migration
{
    public function up()
    {
        Schema::create('bulk_purchase_uploads', function (Blueprint $table) {

            $table->id();

            $table->string('file_name');
            $table->string('file_path');

            $table->string('type')->nullable();

            $table->date('statement_date')->nullable();
            $table->date('synced_date')->nullable();

            $table->integer('total')->default(0);
            $table->integer('pending')->default(0);
            $table->integer('saved')->default(0);
            $table->integer('synced')->default(0);

            $table->string('status')->default('processing');

            $table->integer('iPartyId')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bulk_purchase_uploads');
    }
}