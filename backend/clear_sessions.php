<?php
// Clear stuck flow sessions so keyword triggers fire correctly
$count = App\Models\FlowSession::whereIn('status', ['waiting_input', 'running'])->count();
App\Models\FlowSession::whereIn('status', ['waiting_input', 'running'])->update(['status' => 'completed']);
echo "Cleared {$count} stuck flow session(s). Keyword triggers will now work.\n";
