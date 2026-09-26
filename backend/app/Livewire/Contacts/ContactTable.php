<?php

namespace App\Livewire\Contacts;

use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Instance;
use App\Models\Phonebook;
use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.app')]
class ContactTable extends Component
{
    use WithPagination, WithFileUploads;

    public int $workspaceId;
    public string $activeTab = 'contacts'; // 'contacts', 'groups', 'fields', 'blacklist'
    public string $search = '';
    public ?int $phonebookFilter = null;
    public int $perPage = 15;

    // Bulk selection
    public array $selectedContacts = [];
    public bool $selectAll = false;
    public ?int $bulkPhonebookId = null;

    // Contact Modal Form
    public bool $showContactModal = false;
    public ?int $editingContactId = null;
    public string $name = '';
    public string $mobile = '';
    public string $email = '';
    public ?int $phonebook_id = null;
    public string $var1 = '';
    public string $var2 = '';
    public string $var3 = '';
    public string $var4 = '';
    public string $var5 = '';
    public string $var6 = '';
    public array $contactCustomFields = [];

    // Phonebook Modal Form
    public bool $showPhonebookModal = false;
    public ?int $editingPhonebookId = null;
    public string $newPhonebookName = '';
    public string $newPhonebookDescription = '';

    // Phonebook Delete Modal Form
    public bool $showDeletePhonebookModal = false;
    public ?int $confirmingDeletePhonebookId = null;
    public string $deletePhonebookMode = 'unassign'; // 'unassign' or 'purge'

    // CSV Import Form
    public bool $showImportModal = false;
    public $csvFile = null;
    public ?array $csvPreview = null;
    public ?int $importPhonebookId = null;
    public array $importErrors = [];

    // Device Sync Modal Form
    public bool $showDeviceSyncModal = false;
    public ?string $syncInstanceId = null;
    public ?int $syncPhonebookId = null;
    public ?array $syncSummary = null;

    // Custom Fields Management
    public array $customFields = [];
    public bool $showFieldModal = false;
    public string $newFieldKey = '';
    public string $newFieldLabel = '';
    public string $newFieldType = 'text';

    // Blacklist Management
    public array $blacklist = [];
    public bool $showBlacklistModal = false;
    public string $newBlacklistNumber = '';
    public string $newBlacklistReason = 'Requested STOP opt-out';

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->workspaceId = session('current_workspace_id') ?? Auth::user()->workspaces()->first()?->id ?? 1;
        $this->loadCustomFields();
        $this->loadBlacklist();
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPhonebookFilter()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'phonebookFilter', 'selectedContacts', 'selectAll']);
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedContacts = Contact::where('workspace_id', $this->workspaceId)
                ->when($this->search, function ($q) {
                    $search = '%' . $this->search . '%';
                    $q->where(function ($sub) use ($search) {
                        $sub->where('name', 'like', $search)
                            ->orWhere('mobile', 'like', $search)
                            ->orWhere('email', 'like', $search)
                            ->orWhere('var1', 'like', $search)
                            ->orWhere('var2', 'like', $search)
                            ->orWhere('var3', 'like', $search)
                            ->orWhere('var4', 'like', $search)
                            ->orWhere('var5', 'like', $search);
                    });
                })
                ->when($this->phonebookFilter, fn($q) => $q->where('phonebook_id', $this->phonebookFilter))
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedContacts = [];
        }
    }

    public function bulkDelete()
    {
        if (empty($this->selectedContacts)) {
            return;
        }

        $count = count($this->selectedContacts);
        Contact::where('workspace_id', $this->workspaceId)
            ->whereIn('id', $this->selectedContacts)
            ->delete();

        $this->selectedContacts = [];
        $this->selectAll = false;
        session()->flash('message', "Successfully deleted {$count} contacts.");
    }

    public function bulkAssignGroup()
    {
        if (empty($this->selectedContacts) || ! $this->bulkPhonebookId) {
            return;
        }

        $count = count($this->selectedContacts);
        Contact::where('workspace_id', $this->workspaceId)
            ->whereIn('id', $this->selectedContacts)
            ->update(['phonebook_id' => $this->bulkPhonebookId]);

        $this->selectedContacts = [];
        $this->selectAll = false;
        $this->bulkPhonebookId = null;
        session()->flash('message', "Assigned {$count} contacts to the selected group.");
    }

    public function openCreateContactModal()
    {
        $this->reset([
            'editingContactId', 'name', 'mobile', 'email', 'phonebook_id',
            'var1', 'var2', 'var3', 'var4', 'var5', 'var6', 'contactCustomFields'
        ]);
        $this->showContactModal = true;
        $this->dispatch('open-modal', 'contact-modal');
    }

    public function editContact(int $id)
    {
        $contact = Contact::where('workspace_id', $this->workspaceId)->findOrFail($id);
        $this->editingContactId = $contact->id;
        $this->name = $contact->name ?? '';
        $this->mobile = $contact->mobile;
        $this->email = $contact->email ?? '';
        $this->phonebook_id = $contact->phonebook_id;
        $this->var1 = $contact->var1 ?? '';
        $this->var2 = $contact->var2 ?? '';
        $this->var3 = $contact->var3 ?? '';
        $this->var4 = $contact->var4 ?? '';
        $this->var5 = $contact->var5 ?? '';
        $this->var6 = $contact->var6 ?? '';
        $this->contactCustomFields = $contact->custom_fields ?? [];
        $this->showContactModal = true;
        $this->dispatch('open-modal', 'contact-modal');
    }

    public function saveContact()
    {
        $this->validate([
            'mobile' => 'required|string|min:7|max:25',
            'name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'phonebook_id' => 'nullable|exists:phonebooks,id',
            'var1' => 'nullable|string|max:500',
            'var2' => 'nullable|string|max:500',
            'var3' => 'nullable|string|max:500',
            'var4' => 'nullable|string|max:500',
            'var5' => 'nullable|string|max:500',
            'var6' => 'nullable|string|max:500',
        ]);

        $cleanMobile = preg_replace('/[^0-9]/', '', $this->mobile);

        $payload = [
            'name' => $this->name ?: null,
            'mobile' => $cleanMobile,
            'email' => $this->email ?: null,
            'phonebook_id' => $this->phonebook_id ?: null,
            'var1' => $this->var1 ?: null,
            'var2' => $this->var2 ?: null,
            'var3' => $this->var3 ?: null,
            'var4' => $this->var4 ?: null,
            'var5' => $this->var5 ?: null,
            'var6' => $this->var6 ?: null,
            'custom_fields' => $this->contactCustomFields,
        ];

        if ($this->editingContactId) {
            $contact = Contact::where('workspace_id', $this->workspaceId)->findOrFail($this->editingContactId);
            $contact->update($payload);
            session()->flash('message', 'Contact updated successfully.');
        } else {
            $payload['workspace_id'] = $this->workspaceId;
            $payload['source'] = 'manual';
            Contact::create($payload);
            session()->flash('message', 'New contact added successfully.');
        }

        $this->showContactModal = false;
        $this->dispatch('close-modal', 'contact-modal');
        $this->reset([
            'editingContactId', 'name', 'mobile', 'email', 'phonebook_id',
            'var1', 'var2', 'var3', 'var4', 'var5', 'var6', 'contactCustomFields'
        ]);
    }

    public function deleteContact(int $id)
    {
        $contact = Contact::where('workspace_id', $this->workspaceId)->find($id);
        if ($contact) {
            $contact->delete();
            session()->flash('message', 'Contact deleted.');
        }
    }

    public function openCreatePhonebookModal()
    {
        $this->reset(['editingPhonebookId', 'newPhonebookName', 'newPhonebookDescription']);
        $this->showPhonebookModal = true;
        $this->dispatch('open-modal', 'phonebook-modal');
    }

    public function editPhonebook(int $id)
    {
        $pb = Phonebook::where('workspace_id', $this->workspaceId)->findOrFail($id);
        $this->editingPhonebookId = $pb->id;
        $this->newPhonebookName = $pb->name;
        $this->showPhonebookModal = true;
        $this->dispatch('open-modal', 'phonebook-modal');
    }

    public function createPhonebook()
    {
        $this->validate([
            'newPhonebookName' => 'required|string|max:50',
        ]);

        if ($this->editingPhonebookId) {
            $pb = Phonebook::where('workspace_id', $this->workspaceId)->findOrFail($this->editingPhonebookId);
            $pb->update(['name' => $this->newPhonebookName]);
            session()->flash('message', 'Phonebook group updated.');
        } else {
            Phonebook::create([
                'workspace_id' => $this->workspaceId,
                'name' => $this->newPhonebookName,
            ]);
            session()->flash('message', 'Phonebook group created.');
        }

        $this->reset(['editingPhonebookId', 'newPhonebookName', 'newPhonebookDescription']);
        $this->showPhonebookModal = false;
        $this->dispatch('close-modal', 'phonebook-modal');
    }

    public function confirmDeletePhonebook(int $id)
    {
        $this->confirmingDeletePhonebookId = $id;
        $this->deletePhonebookMode = 'unassign';
        $this->showDeletePhonebookModal = true;
        $this->dispatch('open-modal', 'delete-phonebook-modal');
    }

    public function deletePhonebookConfirmed()
    {
        if (!$this->confirmingDeletePhonebookId) {
            return;
        }

        $pb = Phonebook::where('workspace_id', $this->workspaceId)->find($this->confirmingDeletePhonebookId);
        if ($pb) {
            if ($this->deletePhonebookMode === 'purge') {
                // Permanently delete all contacts in this phonebook (WhatsCRM v6.1.0 behavior)
                $deletedCount = Contact::where('workspace_id', $this->workspaceId)
                    ->where('phonebook_id', $pb->id)
                    ->delete();
                $pb->delete();
                session()->flash('message', "Phonebook '{$pb->name}' and all {$deletedCount} contacts permanently deleted.");
            } else {
                // Safe default: unassign contacts
                Contact::where('workspace_id', $this->workspaceId)
                    ->where('phonebook_id', $pb->id)
                    ->update(['phonebook_id' => null]);
                $pb->delete();
                session()->flash('message', "Phonebook '{$pb->name}' deleted and contacts unassigned.");
            }
        }

        $this->showDeletePhonebookModal = false;
        $this->dispatch('close-modal', 'delete-phonebook-modal');
        $this->confirmingDeletePhonebookId = null;
    }

    /**
     * Download the official sample CSV file for contact importing.
     */
    public function downloadSampleCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="whatscrm_contacts_sample.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['name', 'mobile', 'var1', 'var2', 'var3', 'var4', 'var5']);
            fputcsv($handle, ['John Doe', '8801700000001', 'VIP', 'Dhaka', 'Premium Plan', 'Follow up Monday', 'Retail']);
            fputcsv($handle, ['Jane Smith', '8801700000002', 'Lead', 'Chittagong', 'Standard Plan', 'Interested in demo', 'Wholesale']);
            fputcsv($handle, ['Acme Corp', '8801700000003', 'Enterprise', 'Sylhet', 'Custom API', 'Approved quote', 'Tech']);
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Livewire hook: triggered when a user drops or selects a CSV file.
     * Parses metadata, counts rows, detects columns, and displays instant UI preview.
     */
    public function updatedCsvFile()
    {
        $this->importErrors = [];
        $this->csvPreview = null;

        if (!$this->csvFile) {
            return;
        }

        try {
            $this->validate([
                'csvFile' => 'required|file|mimes:csv,txt|max:10240',
            ]);

            $path = $this->csvFile->getRealPath();
            $handle = fopen($path, 'r');
            if ($handle === false) {
                $this->addError('csvFile', 'Unable to open uploaded file.');
                return;
            }

            $rawHeader = fgetcsv($handle);
            if (!$rawHeader || empty(array_filter($rawHeader))) {
                fclose($handle);
                $this->addError('csvFile', 'CSV file appears to be empty or missing headers.');
                return;
            }

            // Remove UTF-8 BOM if present (e.g. from Excel exports)
            $rawHeader[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $rawHeader[0]);
            $cleanHeader = array_map(fn($c) => strtolower(trim((string) $c)), $rawHeader);

            $hasMobile = false;
            foreach (['mobile', 'phone', 'whatsapp', 'number', 'phone number', 'contact'] as $candidate) {
                if (in_array($candidate, $cleanHeader)) {
                    $hasMobile = true;
                    break;
                }
            }

            $rowCount = 0;
            $sampleRows = [];
            while (($row = fgetcsv($handle)) !== false) {
                if (!empty(array_filter($row))) {
                    $rowCount++;
                    if (count($sampleRows) < 3) {
                        $sampleRows[] = array_slice($row, 0, 6);
                    }
                }
            }
            fclose($handle);

            $this->csvPreview = [
                'filename' => $this->csvFile->getClientOriginalName(),
                'size' => $this->formatFileSize($this->csvFile->getSize()),
                'headers' => array_slice($cleanHeader, 0, 8),
                'total_rows' => $rowCount,
                'has_mobile_col' => $hasMobile,
                'sample_rows' => $sampleRows,
            ];

            if (!$hasMobile) {
                $this->addError('csvFile', 'Warning: CSV must contain a "mobile" or "phone" column header.');
            }
        } catch (\Throwable $e) {
            $this->csvPreview = null;
            $this->addError('csvFile', 'Invalid CSV file: ' . $e->getMessage());
        }
    }

    public function clearCsvFile()
    {
        $this->reset(['csvFile', 'csvPreview', 'importErrors']);
        $this->dispatch('csv-reset');
    }

    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * Import contacts from CSV adhering to WhatsCRM v6.1.0 format.
     */
    public function importCsv()
    {
        $this->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:10240',
            'importPhonebookId' => 'nullable|exists:phonebooks,id',
        ]);

        $this->importErrors = [];

        $path = $this->csvFile->getRealPath();
        $handle = fopen($path, 'r');
        if ($handle === false) {
            $this->addError('csvFile', 'Unable to open file for import.');
            return;
        }

        $rawHeader = fgetcsv($handle);
        if (!$rawHeader || empty(array_filter($rawHeader))) {
            fclose($handle);
            $this->addError('csvFile', 'CSV file appears to be empty or has no header row.');
            return;
        }

        $rawHeader[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $rawHeader[0]);
        $header = array_map(fn($c) => strtolower(trim((string) $c)), $rawHeader);

        // Find indices
        $nameIndex = array_search('name', $header);
        $mobileIndex = false;
        foreach (['mobile', 'phone', 'whatsapp', 'number', 'phone number', 'contact'] as $candidate) {
            $pos = array_search($candidate, $header);
            if ($pos !== false) {
                $mobileIndex = $pos;
                break;
            }
        }

        $var1Index = array_search('var1', $header);
        $var2Index = array_search('var2', $header);
        $var3Index = array_search('var3', $header);
        $var4Index = array_search('var4', $header);
        $var5Index = array_search('var5', $header);

        if ($mobileIndex === false) {
            fclose($handle);
            $this->addError('csvFile', 'CSV must contain a "mobile", "phone", or "whatsapp" header column.');
            return;
        }

        $imported = 0;
        $invalidRows = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if (empty(array_filter($row))) {
                continue;
            }

            $rawMobile = $row[$mobileIndex] ?? '';
            $cleaned = preg_replace('/[^0-9]/', '', (string) $rawMobile);

            if (empty($cleaned) || strlen($cleaned) < 7 || strlen($cleaned) > 20) {
                $invalidRows[] = [
                    'row' => $rowNumber,
                    'name' => ($nameIndex !== false && isset($row[$nameIndex])) ? $row[$nameIndex] : '',
                    'mobile' => $rawMobile,
                    'reason' => empty($cleaned) ? 'Empty phone number' : 'Invalid digits',
                ];
                continue;
            }

            Contact::updateOrCreate(
                ['workspace_id' => $this->workspaceId, 'mobile' => $cleaned],
                [
                    'name' => ($nameIndex !== false && !empty($row[$nameIndex])) ? trim($row[$nameIndex]) : null,
                    'phonebook_id' => $this->importPhonebookId ?: null,
                    'var1' => ($var1Index !== false && isset($row[$var1Index])) ? trim($row[$var1Index]) : null,
                    'var2' => ($var2Index !== false && isset($row[$var2Index])) ? trim($row[$var2Index]) : null,
                    'var3' => ($var3Index !== false && isset($row[$var3Index])) ? trim($row[$var3Index]) : null,
                    'var4' => ($var4Index !== false && isset($row[$var4Index])) ? trim($row[$var4Index]) : null,
                    'var5' => ($var5Index !== false && isset($row[$var5Index])) ? trim($row[$var5Index]) : null,
                    'source' => 'import',
                ]
            );
            $imported++;
        }
        fclose($handle);

        if (!empty($invalidRows)) {
            $this->importErrors = $invalidRows;
            session()->flash('message', "Imported {$imported} contacts. " . count($invalidRows) . " invalid rows skipped.");
        } else {
            $this->reset(['csvFile', 'csvPreview', 'importPhonebookId', 'importErrors']);
            $this->showImportModal = false;
            $this->dispatch('close-modal', 'import-modal');
            session()->flash('message', "Successfully imported all {$imported} contacts.");
        }
    }

    public function openImportModal()
    {
        $this->reset(['csvFile', 'csvPreview', 'importPhonebookId', 'importErrors']);
        $this->showImportModal = true;
        $this->dispatch('open-modal', 'import-modal');
        $this->dispatch('csv-reset');
    }

    /**
     * Export contacts filtered by active search and group filters.
     */
    public function exportCsv(): StreamedResponse
    {
        $query = Contact::where('workspace_id', $this->workspaceId)->with('phonebook');

        if (!empty($this->search)) {
            $search = '%' . $this->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('mobile', 'like', $search)
                  ->orWhere('email', 'like', $search)
                  ->orWhere('var1', 'like', $search)
                  ->orWhere('var2', 'like', $search)
                  ->orWhere('var3', 'like', $search)
                  ->orWhere('var4', 'like', $search)
                  ->orWhere('var5', 'like', $search);
            });
        }

        if ($this->phonebookFilter) {
            $query->where('phonebook_id', $this->phonebookFilter);
        }

        $contacts = $query->orderBy('id', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="contacts_export_' . now()->format('Ymd_His') . '.csv"',
        ];

        return response()->stream(function () use ($contacts) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['name', 'mobile', 'phonebook_name', 'var1', 'var2', 'var3', 'var4', 'var5', 'createdAt']);

            foreach ($contacts as $c) {
                fputcsv($handle, [
                    $c->name ?? '',
                    $c->mobile,
                    $c->phonebook?->name ?? '',
                    $c->var1 ?? '',
                    $c->var2 ?? '',
                    $c->var3 ?? '',
                    $c->var4 ?? '',
                    $c->var5 ?? '',
                    $c->created_at?->toIso8601String() ?? '',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Open device contacts sync modal.
     */
    public function openDeviceSyncModal()
    {
        $firstInstance = Instance::where('uid', (string) $this->workspaceId)
            ->where('status', 'ACTIVE')
            ->first();

        $this->syncInstanceId = $firstInstance?->uniqueId;
        $this->syncPhonebookId = null;
        $this->syncSummary = null;
        $this->showDeviceSyncModal = true;
        $this->dispatch('open-modal', 'device-sync-modal');
    }

    /**
     * Sync and merge contacts from connected Baileys WhatsApp instance.
     */
    public function syncFromDevice()
    {
        $this->validate([
            'syncInstanceId' => 'required|string',
            'syncPhonebookId' => 'nullable|exists:phonebooks,id',
        ]);

        $instance = Instance::where('uid', (string) $this->workspaceId)
            ->where('uniqueId', $this->syncInstanceId)
            ->firstOrFail();

        // 1. Fetch conversations belonging to this instance
        $conversations = Conversation::where('workspace_id', $this->workspaceId)
            ->where(function ($q) use ($instance) {
                $q->where('instance_id', $instance->uniqueId)
                  ->orWhere('channel', 'baileys');
            })
            ->get();

        $imported = 0;
        $updated = 0;

        foreach ($conversations as $conv) {
            $cleanMobile = preg_replace('/[^0-9]/', '', $conv->sender_mobile ?: $conv->chat_id);
            if (empty($cleanMobile) || strlen($cleanMobile) < 7) {
                continue;
            }

            $contact = Contact::where('workspace_id', $this->workspaceId)
                ->where('mobile', $cleanMobile)
                ->first();

            if ($contact) {
                // Safe merge: preserve existing name if already set
                $updates = [];
                if (empty($contact->name) && !empty($conv->sender_name)) {
                    $updates['name'] = $conv->sender_name;
                }
                if ($this->syncPhonebookId && empty($contact->phonebook_id)) {
                    $updates['phonebook_id'] = $this->syncPhonebookId;
                }
                if (!empty($updates)) {
                    $contact->update($updates);
                    $updated++;
                }
            } else {
                Contact::create([
                    'workspace_id' => $this->workspaceId,
                    'phonebook_id' => $this->syncPhonebookId ?: null,
                    'name' => $conv->sender_name ?: $cleanMobile,
                    'mobile' => $cleanMobile,
                    'source' => 'whatsapp_session',
                ]);
                $imported++;
            }
        }

        $this->syncSummary = [
            'imported' => $imported,
            'updated' => $updated,
            'instance_name' => $instance->name ?: $instance->number,
        ];

        session()->flash('message', "WhatsApp Device Sync Complete: {$imported} contacts imported, {$updated} updated.");
    }

    public function loadCustomFields()
    {
        $workspace = Workspace::find($this->workspaceId);
        $settings = $workspace?->settings ?? [];

        if (isset($settings['custom_fields']) && is_array($settings['custom_fields'])) {
            $this->customFields = $settings['custom_fields'];
        } else {
            $this->customFields = [
                ['key' => 'company_name', 'label' => 'Company Name', 'type' => 'text', 'placeholder' => 'Acme Corp'],
                ['key' => 'city', 'label' => 'Customer City', 'type' => 'text', 'placeholder' => 'New York'],
                ['key' => 'vip_tier', 'label' => 'VIP Loyalty Tier', 'type' => 'select', 'placeholder' => 'Gold, Silver, Platinum'],
                ['key' => 'order_id', 'label' => 'Latest Order #', 'type' => 'text', 'placeholder' => 'ORD-9821'],
            ];
        }
    }

    public function openFieldModal()
    {
        $this->reset(['newFieldKey', 'newFieldLabel', 'newFieldType']);
        $this->showFieldModal = true;
        $this->dispatch('open-modal', 'custom-field-modal');
    }

    public function addCustomField()
    {
        $this->validate([
            'newFieldKey' => 'required|string|regex:/^[a-zA-Z0-9_]+$/|max:30',
            'newFieldLabel' => 'required|string|max:50',
            'newFieldType' => 'required|in:text,number,date,select',
        ]);

        $this->customFields[] = [
            'key' => strtolower($this->newFieldKey),
            'label' => $this->newFieldLabel,
            'type' => $this->newFieldType,
            'placeholder' => '',
        ];

        $this->persistCustomFields();
        $this->reset(['newFieldKey', 'newFieldLabel', 'newFieldType', 'showFieldModal']);
        $this->dispatch('close-modal', 'custom-field-modal');
        session()->flash('message', 'Custom attribute field added.');
    }

    public function deleteCustomField(string $key)
    {
        $this->customFields = array_values(array_filter($this->customFields, fn($f) => $f['key'] !== $key));
        $this->persistCustomFields();
        session()->flash('message', 'Custom field removed.');
    }

    private function persistCustomFields()
    {
        $workspace = Workspace::find($this->workspaceId);
        if ($workspace) {
            $settings = $workspace->settings ?? [];
            $settings['custom_fields'] = $this->customFields;
            $workspace->update(['settings' => $settings]);
        }
    }

    public function loadBlacklist()
    {
        $workspace = Workspace::find($this->workspaceId);
        $settings = $workspace?->settings ?? [];

        if (isset($settings['blacklist']) && is_array($settings['blacklist'])) {
            $this->blacklist = $settings['blacklist'];
        } else {
            $this->blacklist = [
                ['number' => '15550199999', 'reason' => 'Reply STOP on broadcast', 'added_at' => '2026-03-15'],
                ['number' => '447911000000', 'reason' => 'Requested manual unsubscription', 'added_at' => '2026-03-18'],
            ];
        }
    }

    public function openBlacklistModal()
    {
        $this->reset(['newBlacklistNumber', 'newBlacklistReason']);
        $this->showBlacklistModal = true;
        $this->dispatch('open-modal', 'blacklist-modal');
    }

    public function addToBlacklist()
    {
        $this->validate([
            'newBlacklistNumber' => 'required|string|min:8',
            'newBlacklistReason' => 'required|string|max:100',
        ]);

        $cleanNumber = preg_replace('/[^0-9]/', '', $this->newBlacklistNumber);

        $this->blacklist[] = [
            'number' => $cleanNumber,
            'reason' => $this->newBlacklistReason,
            'added_at' => date('Y-m-d'),
        ];

        $this->persistBlacklist();
        $this->reset(['newBlacklistNumber', 'newBlacklistReason', 'showBlacklistModal']);
        $this->dispatch('close-modal', 'blacklist-modal');
        session()->flash('message', 'Number added to campaign blacklist / opt-out registry.');
    }

    public function removeFromBlacklist(string $number)
    {
        $this->blacklist = array_values(array_filter($this->blacklist, fn($b) => $b['number'] !== $number));
        $this->persistBlacklist();
        session()->flash('message', 'Number removed from blacklist.');
    }

    private function persistBlacklist()
    {
        $workspace = Workspace::find($this->workspaceId);
        if ($workspace) {
            $settings = $workspace->settings ?? [];
            $settings['blacklist'] = $this->blacklist;
            $workspace->update(['settings' => $settings]);
        }
    }

    public function render()
    {
        $query = Contact::where('workspace_id', $this->workspaceId)
            ->with(['phonebook']);

        if (! empty($this->search)) {
            $search = '%' . $this->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('mobile', 'like', $search)
                  ->orWhere('email', 'like', $search)
                  ->orWhere('var1', 'like', $search)
                  ->orWhere('var2', 'like', $search)
                  ->orWhere('var3', 'like', $search)
                  ->orWhere('var4', 'like', $search)
                  ->orWhere('var5', 'like', $search);
            });
        }

        if ($this->phonebookFilter) {
            $query->where('phonebook_id', $this->phonebookFilter);
        }

        $contacts = $query->orderBy('id', 'desc')->paginate($this->perPage);
        $phonebooks = Phonebook::where('workspace_id', $this->workspaceId)
            ->withCount('contacts')
            ->get();
        $totalContacts = Contact::where('workspace_id', $this->workspaceId)->count();
        $activeInstances = Instance::where('uid', (string) $this->workspaceId)
            ->where('status', 'ACTIVE')
            ->get();

        return view('livewire.contacts.contact-table', [
            'contacts' => $contacts,
            'phonebooks' => $phonebooks,
            'totalContacts' => $totalContacts,
            'activeInstances' => $activeInstances,
        ]);
    }
}
