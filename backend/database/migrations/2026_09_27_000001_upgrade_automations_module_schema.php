<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Upgrade flows table with description & source
        Schema::table('flows', function (Blueprint $table) {
            if (!Schema::hasColumn('flows', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('flows', 'source')) {
                $table->string('source', 100)->default('wa_chatbot')->after('trigger_type');
            }
        });

        // 2. Upgrade flow_sessions with channel info & variable context
        Schema::table('flow_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('flow_sessions', 'channel_type')) {
                $table->string('channel_type', 50)->default('meta')->after('contact_id');
            }
            if (!Schema::hasColumn('flow_sessions', 'channel_id')) {
                $table->string('channel_id', 191)->nullable()->after('channel_type');
            }
            if (!Schema::hasColumn('flow_sessions', 'variables')) {
                $table->json('variables')->nullable()->after('session_data');
            }
            if (!Schema::hasColumn('flow_sessions', 'visited_nodes')) {
                $table->json('visited_nodes')->nullable()->after('variables');
            }
            if (!Schema::hasColumn('flow_sessions', 'auto_reply_disabled_until')) {
                $table->timestamp('auto_reply_disabled_until')->nullable()->after('status');
            }
        });

        // 3. Create bot_bindings table (Multi-Channel Bot Routing Matrix)
        if (!Schema::hasTable('bot_bindings')) {
            Schema::create('bot_bindings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workspace_id')->constrained('workspaces')->cascadeOnDelete();
                $table->string('title');
                $table->string('channel', 50)->default('meta'); // meta, qr, telegram, instagram, messenger, webhook
                $table->string('origin_id', 191)->nullable();
                $table->json('origin_metadata')->nullable();
                $table->foreignId('flow_id')->constrained('flows')->cascadeOnDelete();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['workspace_id', 'channel', 'is_active']);
            });
        }

        // 4. Create wa_forms table (Meta WhatsApp Flows Studio)
        if (!Schema::hasTable('wa_forms')) {
            Schema::create('wa_forms', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workspace_id')->constrained('workspaces')->cascadeOnDelete();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('meta_flow_id', 191)->nullable();
                $table->string('flow_status', 50)->default('DRAFT'); // DRAFT, PUBLISHED, DEPRECATED
                $table->json('categories')->nullable();
                $table->json('fields_schema')->nullable();
                $table->timestamps();

                $table->index(['workspace_id', 'flow_status']);
            });
        }

        // 5. Create wa_form_submissions table
        if (!Schema::hasTable('wa_form_submissions')) {
            Schema::create('wa_form_submissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workspace_id')->constrained('workspaces')->cascadeOnDelete();
                $table->foreignId('wa_form_id')->nullable()->constrained('wa_forms')->nullOnDelete();
                $table->string('from_phone', 50);
                $table->string('flow_token', 191)->nullable();
                $table->json('submission_data');
                $table->timestamps();

                $table->index(['workspace_id', 'wa_form_id']);
                $table->index(['from_phone']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('wa_form_submissions');
        Schema::dropIfExists('wa_forms');
        Schema::dropIfExists('bot_bindings');

        Schema::table('flow_sessions', function (Blueprint $table) {
            $table->dropColumn(['channel_type', 'channel_id', 'variables', 'visited_nodes', 'auto_reply_disabled_until']);
        });

        Schema::table('flows', function (Blueprint $table) {
            $table->dropColumn(['description', 'source']);
        });
    }
};
