<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pending self-registrations (Full Name + Email + Mobile).
 *
 * The user account is created only after the mobile OTP is verified, so an
 * unverified person never has a `users` row and cannot log in. Only a hash of
 * the OTP is stored; the password is generated after verification.
 *
 * Run on its own (the older migrations in this repo do not build the schema):
 *   php artisan migrate --path=database/migrations/2026_10_27_000001_create_registration_requests_table.php
 * or use database/sql/registration_requests.sql.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('registration_requests')) {
            Schema::create('registration_requests', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('mobile', 10)->index();
                $table->string('token_hash', 64);
                $table->string('otp_hash')->nullable();
                $table->timestamp('otp_expires_at')->nullable();
                $table->unsignedSmallInteger('otp_attempts')->default(0);
                $table->unsignedSmallInteger('otp_sends')->default(0);
                $table->timestamp('last_sent_at')->nullable();
                $table->timestamp('locked_until')->nullable();
                $table->string('ip', 45)->nullable();
                $table->timestamps();
            });
        }

        // Tables created by the first version of this script had password columns.
        foreach (['password_hash', 'password_emailed_at'] as $column) {
            if (Schema::hasColumn('registration_requests', $column)) {
                Schema::table('registration_requests', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }

        if (Schema::hasTable('profiles') && ! Schema::hasColumn('profiles', 'mobile_verified_at')) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->timestamp('mobile_verified_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_requests');

        if (Schema::hasTable('profiles') && Schema::hasColumn('profiles', 'mobile_verified_at')) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->dropColumn('mobile_verified_at');
            });
        }
    }
};
