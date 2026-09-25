<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained('workspaces')->cascadeOnDelete();
            $table->string('name');
            $table->enum('trigger_type', ['keyword', 'new_contact', 'tag_added', 'manual'])->default('keyword');
            $table->text('trigger_keywords')->nullable();
            $table->json('flow_data')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('execution_count')->default(0);
            $table->timestamps();

            $table->index(['workspace_id', 'is_active']);
        });

        Schema::create('flow_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained('workspaces')->cascadeOnDelete();
            $table->foreignId('flow_id')->constrained('flows')->cascadeOnDelete();
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->string('current_node_id')->nullable();
            $table->json('session_data')->nullable();
            $table->enum('status', ['running', 'waiting_input', 'completed', 'terminated'])->default('running');
            $table->timestamps();

            $table->index(['conversation_id', 'status']);
        });

        Schema::create('chatbot_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained('workspaces')->cascadeOnDelete();
            $table->text('keywords');
            $table->enum('match_type', ['exact', 'contains', 'starts_with'])->default('contains');
            $table->enum('reply_type', ['text', 'media', 'template', 'flow', 'ai'])->default('text');
            $table->json('reply_content');
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->timestamps();

            $table->index(['workspace_id', 'is_active']);
        });

        Schema::create('quick_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained('workspaces')->cascadeOnDelete();
            $table->string('shortcut', 50);
            $table->text('message');
            $table->string('category', 50)->nullable();
            $table->timestamps();

            $table->index(['workspace_id', 'shortcut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quick_replies');
        Schema::dropIfExists('chatbot_rules');
        Schema::dropIfExists('flow_sessions');
        Schema::dropIfExists('flows');
    }
};
