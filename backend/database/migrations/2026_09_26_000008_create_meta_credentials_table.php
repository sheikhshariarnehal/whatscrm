<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meta_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained('workspaces')->cascadeOnDelete();
            $table->string('phone_number_id');
            $table->string('waba_id');
            $table->string('display_phone_number', 50)->nullable();
            $table->string('verified_name')->nullable();
            $table->string('quality_rating', 50)->default('UNKNOWN');
            $table->text('access_token');
            $table->string('verify_token')->nullable();
            $table->string('app_id')->nullable();
            $table->text('app_secret')->nullable();
            $table->enum('status', ['connected', 'disconnected', 'expired'])->default('connected');
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->index('workspace_id');
            $table->index('phone_number_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meta_credentials');
    }
};
