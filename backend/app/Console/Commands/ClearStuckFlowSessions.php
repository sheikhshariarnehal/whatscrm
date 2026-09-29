<?php

namespace App\Console\Commands;

use App\Models\FlowSession;
use Illuminate\Console\Command;

class ClearStuckFlowSessions extends Command
{
    protected $signature   = 'flows:clear-sessions {--conversation= : Only clear sessions for a specific conversation ID}';
    protected $description = 'Mark all stuck (running/waiting_input) flow sessions as completed so keyword triggers fire correctly';

    public function handle(): int
    {
        $query = FlowSession::whereIn('status', ['running', 'waiting_input']);

        if ($convId = $this->option('conversation')) {
            $query->where('conversation_id', (int) $convId);
        }

        $count = $query->count();

        if ($count === 0) {
            $this->info('✅ No stuck flow sessions found.');
            return self::SUCCESS;
        }

        $query->update(['status' => 'completed']);

        $this->info("✅ Cleared {$count} stuck flow session(s). Keyword triggers will now fire correctly.");
        return self::SUCCESS;
    }
}
