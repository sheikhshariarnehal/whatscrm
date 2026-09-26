<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add warmer timing config columns to the warmers table.
        // These are read by the Baileys warmer loop to control message send rate.
        Schema::table('warmers', function (Blueprint $table) {
            if (!Schema::hasColumn('warmers', 'min_sleep')) {
                $table->unsignedSmallInteger('min_sleep')->default(15)->after('is_active');
            }
            if (!Schema::hasColumn('warmers', 'max_sleep')) {
                $table->unsignedSmallInteger('max_sleep')->default(45)->after('min_sleep');
            }
            if (!Schema::hasColumn('warmers', 'max_daily')) {
                $table->unsignedSmallInteger('max_daily')->default(80)->after('max_sleep');
            }
        });
    }

    public function down(): void
    {
        Schema::table('warmers', function (Blueprint $table) {
            $table->dropColumn(['min_sleep', 'max_sleep', 'max_daily']);
        });
    }
};
