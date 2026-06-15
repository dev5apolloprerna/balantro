<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_user_card_preferences.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_card_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('party_id');
            $table->json('selected_groups'); // Store selected group IDs
            $table->timestamps();

            $table->unique(['user_id', 'party_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_card_preferences');
    }
};
