<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\CampaignLog;
use App\Models\Contact;
use App\Services\WhatsApp\CloudApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCampaignBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $campaignId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $campaignId)
    {
        $this->campaignId = $campaignId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Must bypass WorkspaceScope when running in background queue worker
        $campaign = Campaign::withoutGlobalScopes()->find($this->campaignId);

        if (!$campaign || in_array($campaign->status, ['completed', 'failed', 'paused'])) {
            return;
        }

        $campaign->update([
            'status' => 'processing',
            'started_at' => $campaign->started_at ?? now(),
        ]);

        $cloudService = CloudApiService::forWorkspace($campaign->workspace_id);

        // Fetch contacts based on target_type
        $contactsQuery = Contact::withoutGlobalScopes()
            ->where('workspace_id', $campaign->workspace_id);

        if ($campaign->target_type === 'phonebook' && $campaign->target_id) {
            $contactsQuery->where('phonebook_id', $campaign->target_id);
        } elseif ($campaign->target_type === 'tags' && $campaign->target_id) {
            $contactsQuery->whereHas('tags', function ($q) use ($campaign) {
                $q->where('tags.id', $campaign->target_id);
            });
        }

        $contacts = $contactsQuery->get();

        if ($contacts->isEmpty()) {
            $campaign->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            return;
        }

        $campaign->update(['total_recipients' => $contacts->count()]);

        foreach ($contacts as $contact) {
            // Check if campaign was paused during processing
            $freshStatus = Campaign::withoutGlobalScopes()->where('id', $campaign->id)->value('status');
            if ($freshStatus === 'paused') {
                break;
            }

            // Resolve dynamic variables
            $variables = $this->resolveVariables($campaign->template_variables ?? [], $contact);

            $log = CampaignLog::create([
                'workspace_id' => $campaign->workspace_id,
                'campaign_id' => $campaign->id,
                'contact_id' => $contact->id,
                'phone' => $contact->phone,
                'variables_sent' => $variables,
                'status' => 'pending',
            ]);

            if (!$cloudService) {
                // Mock / Sandbox mode fallback when Meta credentials are not connected
                $log->update([
                    'status' => 'sent',
                    'external_message_id' => 'sim_wam_' . uniqid(),
                ]);
                $campaign->increment('sent_count');
                continue;
            }

            // Format Meta Cloud API template parameters
            $bodyParameters = [];
            foreach ($variables as $val) {
                $bodyParameters[] = [
                    'type' => 'text',
                    'text' => (string) $val,
                ];
            }

            $components = [];
            if (!empty($bodyParameters)) {
                $components[] = [
                    'type' => 'body',
                    'parameters' => $bodyParameters,
                ];
            }

            $result = $cloudService->sendTemplateMessage(
                $contact->phone,
                $campaign->template_name,
                $campaign->template_language ?? 'en',
                $components
            );

            if ($result['success'] ?? false) {
                $wamId = $result['data']['messages'][0]['id'] ?? ('wam_' . uniqid());
                $log->update([
                    'status' => 'sent',
                    'external_message_id' => $wamId,
                ]);
                $campaign->increment('sent_count');
            } else {
                $log->update([
                    'status' => 'failed',
                    'error_message' => $result['error'] ?? 'API dispatch failed',
                ]);
                $campaign->increment('failed_count');
            }
        }

        // Check if all finished
        $freshStatus = Campaign::withoutGlobalScopes()->where('id', $campaign->id)->value('status');
        if ($freshStatus !== 'paused') {
            $campaign->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }
    }

    /**
     * Resolve template variable mapping.
     */
    protected function resolveVariables(array $templateVariables, Contact $contact): array
    {
        $resolved = [];
        foreach ($templateVariables as $key => $mapping) {
            $field = is_array($mapping) ? ($mapping['field'] ?? '') : $mapping;

            if ($field === 'name') {
                $resolved[$key] = $contact->name ?? 'Valued Customer';
            } elseif ($field === 'phone') {
                $resolved[$key] = $contact->phone;
            } elseif ($field === 'first_name') {
                $parts = explode(' ', trim($contact->name ?? ''));
                $resolved[$key] = $parts[0] ?: 'Customer';
            } elseif (str_starts_with($field, 'custom.')) {
                $customKey = substr($field, 7);
                $resolved[$key] = $contact->custom_fields[$customKey] ?? '';
            } else {
                $resolved[$key] = (string) $field;
            }
        }

        return $resolved;
    }
}
