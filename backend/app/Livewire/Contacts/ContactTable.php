<?php

namespace App\Livewire\Contacts;

use App\Models\Contact;
use App\Models\Phonebook;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ContactTable extends Component
{
    use WithPagination, WithFileUploads;

    public int $workspaceId;
    public string $search = '';
    public ?int $phonebookFilter = null;

    // Contact Modal Form
    public bool $showContactModal = false;
    public ?int $editingContactId = null;
    public string $name = '';
    public string $mobile = '';
    public string $email = '';
    public ?int $phonebook_id = null;

    // Phonebook Modal Form
    public bool $showPhonebookModal = false;
    public string $newPhonebookName = '';

    // CSV Import Form
    public bool $showImportModal = false;
    public $csvFile = null;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->workspaceId = session('current_workspace_id') ?? Auth::user()->workspaces()->first()?->id ?? 1;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPhonebookFilter()
    {
        $this->resetPage();
    }

    public function openCreateContactModal()
    {
        $this->reset(['editingContactId', 'name', 'mobile', 'email', 'phonebook_id']);
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

        if ($this->editingContactId) {
            $contact = Contact::where('workspace_id', $this->workspaceId)->findOrFail($this->editingContactId);
            $contact->update([
                'name' => $this->name,
                'mobile' => $cleanMobile,
                'email' => $this->email ?: null,
                'phonebook_id' => $this->phonebook_id,
            ]);
        } else {
            Contact::create([
                'workspace_id' => $this->workspaceId,
                'name' => $this->name,
                'mobile' => $cleanMobile,
                'email' => $this->email ?: null,
                'phonebook_id' => $this->phonebook_id,
                'source' => 'manual',
            ]);
        }

        $this->showContactModal = false;
        $this->reset(['editingContactId', 'name', 'mobile', 'email', 'phonebook_id']);
    }

    public function deleteContact(int $id)
    {
        $contact = Contact::where('workspace_id', $this->workspaceId)->find($id);
        if ($contact) {
            $contact->delete();
        }
    }

    public function createPhonebook()
    {
        $this->validate([
            'newPhonebookName' => 'required|string|max:50',
        ]);

        Phonebook::create([
            'workspace_id' => $this->workspaceId,
            'name' => $this->newPhonebookName,
        ]);

        $this->newPhonebookName = '';
        $this->showPhonebookModal = false;
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
                    'source' => 'import',
                ]
            );
            $imported++;
        }

        $this->reset('csvFile');
        $this->showImportModal = false;
        session()->flash('message', "Successfully imported {$imported} contacts.");
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
        $phonebooks = Phonebook::where('workspace_id', $this->workspaceId)->get();

        return view('livewire.contacts.contact-table', [
            'contacts' => $contacts,
            'phonebooks' => $phonebooks,
        ]);
    }
}
