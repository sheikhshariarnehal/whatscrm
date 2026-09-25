<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('instance')) {
            Schema::create('instance', function (Blueprint $table) {
                $table->id();
                $table->string('uid', 255)->nullable()->index();
                $table->string('title', 255)->nullable();
                $table->string('number', 255)->nullable();
                $table->string('uniqueId', 255)->nullable()->index();
                $table->longText('qr')->nullable();
                $table->longText('data')->nullable();
                $table->longText('other')->nullable();
                $table->string('status', 100)->nullable()->index();
                $table->timestamp('createdAt')->useCurrent();
            });
        }

        if (!Schema::hasTable('warmer_script')) {
            Schema::create('warmer_script', function (Blueprint $table) {
                $table->id();
                $table->string('uid', 255)->nullable()->index();
                $table->longText('message')->nullable();
                $table->timestamp('createdAt')->useCurrent();
            });

            // Seed initial warmer conversation scripts
            DB::table('warmer_script')->insert([
                ['uid' => 'default', 'message' => 'Hey there, how is your day going?'],
                ['uid' => 'default', 'message' => 'Everything is going great! How about you?'],
                ['uid' => 'default', 'message' => 'Working on the new product launch today.'],
                ['uid' => 'default', 'message' => 'Awesome, let me know if you need any assistance.'],
                ['uid' => 'default', 'message' => 'Will do! Talk to you soon.'],
                ['uid' => 'default', 'message' => 'Thanks, have a productive week ahead!'],
            ]);
        }

        if (!Schema::hasTable('warmers')) {
            Schema::create('warmers', function (Blueprint $table) {
                $table->id();
                $table->string('uid', 255)->nullable()->index();
                $table->longText('instances')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('createdAt')->useCurrent();
            });
        }

        if (!Schema::hasTable('auth')) {
            Schema::create('auth', function (Blueprint $table) {
                $table->id();
                $table->string('session', 255);
                $table->string('id_key', 255);
                $table->text('data');
                $table->unique(['session', 'id_key']);
            });
        }

        if (!Schema::hasTable('web_private')) {
            Schema::create('web_private', function (Blueprint $table) {
                $table->id();
                $table->string('qr_storage', 50)->default('local');
                $table->string('mongodb_string', 500)->nullable();
            });

            DB::table('web_private')->insert([
                'qr_storage' => 'local',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_private');
        Schema::dropIfExists('auth');
        Schema::dropIfExists('warmers');
        Schema::dropIfExists('warmer_script');
        Schema::dropIfExists('instance');
    }
};
