<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('conversations')) {
            Schema::table('conversations', function (Blueprint $table) {
                if (! Schema::hasColumn('conversations', 'deal_value')) {
                    $table->decimal('deal_value', 12, 2)->default(0)->after('kanban_stage');
                }
                if (! Schema::hasColumn('conversations', 'priority')) {
                    $table->string('priority', 20)->default('medium')->after('deal_value');
                }
                if (! Schema::hasColumn('conversations', 'company')) {
                    $table->string('company', 100)->nullable()->after('sender_name');
                }
                if (! Schema::hasColumn('conversations', 'expected_close_at')) {
                    $table->date('expected_close_at')->nullable()->after('priority');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('conversations')) {
            Schema::table('conversations', function (Blueprint $table) {
                $table->dropColumn(['deal_value', 'priority', 'company', 'expected_close_at']);
            });
        }
    }
};
