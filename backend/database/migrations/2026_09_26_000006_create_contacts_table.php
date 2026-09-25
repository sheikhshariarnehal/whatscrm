<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained('workspaces')->cascadeOnDelete();
            $table->foreignId('phonebook_id')->nullable()->constrained('phonebooks')->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('mobile', 50);
            $table->string('email')->nullable();
            $table->string('source', 50)->default('manual');
            $table->json('custom_fields')->nullable();
            $table->string('whatsapp_jid')->nullable();
            $table->text('avatar_url')->nullable();
            $table->timestamps();

            $table->unique(['workspace_id', 'mobile']);
            $table->index(['workspace_id', 'phonebook_id']);
            $table->index(['workspace_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
