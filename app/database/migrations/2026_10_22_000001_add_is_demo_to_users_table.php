<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Marks the read-only sample company row. Isolation itself is keyed on
 * DEMO_PARTY_ID, so this column is informational and for reporting/queries.
 *
 * The repository's older migrations do not build the live schema; on the
 * existing SQL Server database you can run this migration on its own
 * (`php artisan migrate --path=database/migrations/2026_10_22_000001_add_is_demo_to_users_table.php`)
 * or use database/sql/demo_sample_company.sql.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'is_demo')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_demo')->default(false);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'is_demo')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_demo');
            });
        }
    }
};
