<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained('workspaces')->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->string('chat_id');
            $table->string('channel', 50)->default('whatsapp_cloud');
            $table->string('instance_id')->nullable();
            $table->string('sender_name')->nullable();
            $table->string('sender_mobile', 50)->nullable();
            $table->text('profile_url')->nullable();
            $table->text('last_message')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->unsignedInteger('unread_count')->default(0);
            $table->enum('status', ['open', 'closed', 'pending'])->default('open');
            $table->foreignId('assigned_member_id')->nullable()->constrained('workspace_members')->nullOnDelete();
            $table->integer('kanban_order')->default(0);
            $table->timestamps();

            $table->index(['workspace_id', 'updated_at']);
            $table->index(['workspace_id', 'channel']);
            $table->index(['workspace_id', 'assigned_member_id']);
            $table->index(['workspace_id', 'status']);
            $table->index(['workspace_id', 'chat_id']);
        });

        Schema::create('conversation_tags', function (Blueprint $table) {
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
            $table->primary(['conversation_id', 'tag_id']);
        });

        Schema::create('conversation_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained('workspaces')->cascadeOnDelete();
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('note');
            $table->timestamps();

            $table->index(['workspace_id', 'conversation_id']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained('workspaces')->cascadeOnDelete();
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->enum('direction', ['inbound', 'outbound']);
            $table->string('type', 50)->default('text');
            $table->text('content')->nullable();
            $table->text('media_url')->nullable();
            $table->string('media_mime_type', 100)->nullable();
            $table->text('caption')->nullable();
            $table->enum('status', ['pending', 'sent', 'delivered', 'read', 'failed'])->default('pending');
            $table->string('channel', 50)->default('whatsapp_cloud');
            $table->string('external_id')->nullable();
            $table->json('context')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('sent_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
            $table->index(['workspace_id', 'created_at']);
            $table->index('external_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversation_notes');
        Schema::dropIfExists('conversation_tags');
        Schema::dropIfExists('conversations');
    }
};
