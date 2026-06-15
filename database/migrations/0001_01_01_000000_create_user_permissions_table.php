<?php 
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->boolean('is_negative')->default(false); // True for negative override
            $table->timestamps();
            $table->unique(['user_id', 'permission_id']); // A user can only have one direct permission entry for a given permission
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }
};