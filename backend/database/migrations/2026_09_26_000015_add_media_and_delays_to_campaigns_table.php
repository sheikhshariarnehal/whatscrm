<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            if (!Schema::hasColumn('campaigns', 'instance_id')) {
                $table->string('instance_id', 255)->nullable()->after('type');
            }
            if (!Schema::hasColumn('campaigns', 'media_type')) {
                $table->string('media_type', 50)->default('none')->after('template_name');
            }
            if (!Schema::hasColumn('campaigns', 'media_url')) {
                $table->text('media_url')->nullable()->after('media_type');
            }
            if (!Schema::hasColumn('campaigns', 'delay_min')) {
                $table->unsignedInteger('delay_min')->default(5)->after('target_id');
            }
            if (!Schema::hasColumn('campaigns', 'delay_max')) {
                $table->unsignedInteger('delay_max')->default(15)->after('delay_min');
            }
            if (!Schema::hasColumn('campaigns', 'random_suffix')) {
                $table->boolean('random_suffix')->default(false)->after('delay_max');
            }
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'instance_id',
                'media_type',
                'media_url',
                'delay_min',
                'delay_max',
                'random_suffix',
            ]);
        });
    }
};
