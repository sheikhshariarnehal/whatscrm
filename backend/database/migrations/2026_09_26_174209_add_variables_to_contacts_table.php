<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('var1', 500)->nullable()->after('custom_fields');
            $table->string('var2', 500)->nullable()->after('var1');
            $table->string('var3', 500)->nullable()->after('var2');
            $table->string('var4', 500)->nullable()->after('var3');
            $table->string('var5', 500)->nullable()->after('var4');
            $table->string('var6', 500)->nullable()->after('var5');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['var1', 'var2', 'var3', 'var4', 'var5', 'var6']);
        });
    }
};
