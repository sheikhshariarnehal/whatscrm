<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('conversations') && ! Schema::hasColumn('conversations', 'kanban_stage')) {
            Schema::table('conversations', function (Blueprint $table) {
                $table->string('kanban_stage', 50)->default('lead')->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('conversations') && Schema::hasColumn('conversations', 'kanban_stage')) {
            Schema::table('conversations', function (Blueprint $table) {
                $table->dropColumn('kanban_stage');
            });
        }
    }
};
