<?php

namespace App\Livewire\Contacts;

use App\Models\Contact;
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
    public array $contactCustomFields = [];

    // Phonebook Modal Form
    public bool $showPhonebookModal = false;
    public ?int $editingPhonebookId = null;
    public string $newPhonebookName = '';
    public string $newPhonebookDescription = '';

    // CSV Import Form
    public bool $showImportModal = false;
    public $csvFile = null;
    public ?int $importPhonebookId = null;

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
                            ->orWhere('email', 'like', $search);
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
        $this->reset(['editingContactId', 'name', 'mobile', 'email', 'phonebook_id', 'contactCustomFields']);
        $this->showContactModal = true;
    }

    public function editContact(int $id)
    {
        $contact = Contact::where('workspace_id', $this->workspaceId)->findOrFail($id);
        $this->editingContactId = $contact->id;
        $this->name = $contact->name ?? '';
        $this->mobile = $contact->mobile;
        $this->email = $contact->email ?? '';
        $this->phonebook_id = $contact->phonebook_id;
        $this->contactCustomFields = $contact->custom_fields ?? [];
        $this->showContactModal = true;
    }

    public function saveContact()
    {
        $this->validate([
            'mobile' => 'required|string|min:7|max:25',
            'name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'phonebook_id' => 'nullable|exists:phonebooks,id',
        ]);

        $cleanMobile = '+' . preg_replace('/[^0-9]/', '', $this->mobile);

        $payload = [
            'name' => $this->name,
            'mobile' => $cleanMobile,
            'email' => $this->email ?: null,
            'phonebook_id' => $this->phonebook_id,
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
        $this->reset(['editingContactId', 'name', 'mobile', 'email', 'phonebook_id', 'contactCustomFields']);
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
    }

    public function editPhonebook(int $id)
    {
        $pb = Phonebook::where('workspace_id', $this->workspaceId)->findOrFail($id);
        $this->editingPhonebookId = $pb->id;
        $this->newPhonebookName = $pb->name;
        $this->showPhonebookModal = true;
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
    }

    public function deletePhonebook(int $id)
    {
        $pb = Phonebook::where('workspace_id', $this->workspaceId)->find($id);
        if ($pb) {
            // Unassign contacts
            Contact::where('phonebook_id', $id)->update(['phonebook_id' => null]);
            $pb->delete();
            session()->flash('message', 'Phonebook group deleted and contacts unassigned.');
        }
    }

    public function importCsv()
    {
        $this->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $path = $this->csvFile->getRealPath();
        $rows = array_map('str_getcsv', file($path));

        if (count($rows) < 2) {
            $this->addError('csvFile', 'CSV file appears to be empty.');
            return;
        }

        $header = array_map('trim', array_map('strtolower', array_shift($rows)));
        $nameIndex = array_search('name', $header);
        $mobileIndex = array_search('mobile', $header) !== false ? array_search('mobile', $header) : array_search('phone', $header);
        $emailIndex = array_search('email', $header);

        if ($mobileIndex === false) {
            $this->addError('csvFile', 'CSV must contain a "mobile" or "phone" header column.');
            return;
        }

        $imported = 0;
        foreach ($rows as $row) {
            if (empty($row[$mobileIndex])) {
                continue;
            }

            $rawMobile = $row[$mobileIndex];
            $cleanMobile = '+' . preg_replace('/[^0-9]/', '', $rawMobile);

            if (strlen($cleanMobile) < 7) {
                continue;
            }

            Contact::updateOrCreate(
                ['workspace_id' => $this->workspaceId, 'mobile' => $cleanMobile],
                [
                    'name' => ($nameIndex !== false && ! empty($row[$nameIndex])) ? trim($row[$nameIndex]) : null,
                    'email' => ($emailIndex !== false && ! empty($row[$emailIndex])) ? trim($row[$emailIndex]) : null,
                    'phonebook_id' => $this->importPhonebookId ?: null,
                    'source' => 'import',
                ]
            );
            $imported++;
        }

        $this->reset(['csvFile', 'importPhonebookId']);
        $this->showImportModal = false;
        session()->flash('message', "Successfully imported {$imported} contacts.");
    }

    public function exportCsv(): StreamedResponse
    {
        $contacts = Contact::where('workspace_id', $this->workspaceId)
            ->with('phonebook')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="whatscrm_contacts_' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($contacts) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Phone Number', 'Email Address', 'Group', 'Source', 'Created At']);

            foreach ($contacts as $c) {
                fputcsv($handle, [
                    $c->id,
                    $c->name ?? '',
                    $c->mobile,
                    $c->email ?? '',
                    $c->phonebook?->name ?? 'Unassigned',
                    $c->source ?? 'manual',
                    $c->created_at?->format('Y-m-d H:i:s') ?? '',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
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
                ['number' => '+15550199999', 'reason' => 'Reply STOP on broadcast', 'added_at' => '2026-03-15'],
                ['number' => '+447911000000', 'reason' => 'Requested manual unsubscription', 'added_at' => '2026-03-18'],
            ];
        }
    }

    public function addToBlacklist()
    {
        $this->validate([
            'newBlacklistNumber' => 'required|string|min:8',
            'newBlacklistReason' => 'required|string|max:100',
        ]);

        $cleanNumber = '+' . preg_replace('/[^0-9]/', '', $this->newBlacklistNumber);

        $this->blacklist[] = [
            'number' => $cleanNumber,
            'reason' => $this->newBlacklistReason,
            'added_at' => date('Y-m-d'),
        ];

        $this->persistBlacklist();
        $this->reset(['newBlacklistNumber', 'newBlacklistReason', 'showBlacklistModal']);
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
                  ->orWhere('email', 'like', $search);
            });
        }

        if ($this->phonebookFilter) {
            $query->where('phonebook_id', $this->phonebookFilter);
        }

        $contacts = $query->orderBy('name', 'asc')->paginate(15);
        $phonebooks = Phonebook::where('workspace_id', $this->workspaceId)
            ->withCount('contacts')
            ->get();
        $totalContacts = Contact::where('workspace_id', $this->workspaceId)->count();

        return view('livewire.contacts.contact-table', [
            'contacts' => $contacts,
            'phonebooks' => $phonebooks,
            'totalContacts' => $totalContacts,
        ]);
    }
}
