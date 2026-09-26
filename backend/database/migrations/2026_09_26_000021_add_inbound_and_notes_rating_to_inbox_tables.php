<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('conversations', 'last_inbound_at')) {
                $table->timestamp('last_inbound_at')->nullable()->after('last_message_at');
            }
        });

        Schema::table('conversation_notes', function (Blueprint $table) {
            if (!Schema::hasColumn('conversation_notes', 'rating')) {
                $table->unsignedTinyInteger('rating')->default(0)->after('note');
            }
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            if (Schema::hasColumn('conversations', 'last_inbound_at')) {
                $table->dropColumn('last_inbound_at');
            }
        });

        Schema::table('conversation_notes', function (Blueprint $table) {
            if (Schema::hasColumn('conversation_notes', 'rating')) {
                $table->dropColumn('rating');
            }
        });
    }
};
