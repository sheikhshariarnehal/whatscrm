<div class="p-4 sm:p-6 lg:p-8 space-y-6">
    <!-- Header with Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Contacts & Phonebook Directory</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Manage audience phone numbers, groups, and audience segmentation</p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <button wire:click="$set('showPhonebookModal', true)" 
                    class="px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 text-xs font-semibold text-gray-700 dark:text-gray-200 shadow-sm transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>New Group</span>
            </button>

            <button wire:click="$set('showImportModal', true)" 
                    class="px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 text-xs font-semibold text-gray-700 dark:text-gray-200 shadow-sm transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span>Import CSV</span>
            </button>

            <button wire:click="openCreateContactModal" 
                    class="px-4 py-2 rounded-xl bg-primary hover:bg-primary-deep text-white text-xs font-semibold shadow-sm shadow-primary/20 transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Contact</span>
            </button>
        </div>
    </div>

    <!-- Alert Message -->
    @if (session()->has('message'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-medium flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Search & Filter Bar -->
    <div class="p-4 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <!-- Search -->
        <div class="relative flex-1 max-w-md">
            <svg class="w-4 h-4 absolute left-3 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.300ms="search" 
                   type="text" 
                   placeholder="Search contacts by name, mobile, email..." 
                   class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
        </div>

        <!-- Phonebook filter -->
        <div class="flex items-center gap-2">
            <label class="text-xs text-gray-500 dark:text-gray-400 font-medium">Group:</label>
            <select wire:model.live="phonebookFilter" 
                    class="text-xs font-semibold py-2 px-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-primary">
                <option value="">All Phonebooks</option>
                @foreach ($phonebooks as $pb)
                    <option value="{{ $pb->id }}">{{ $pb->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Contacts Table Card -->
    <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50/80 dark:bg-gray-800/40 border-b border-gray-200 dark:border-gray-800 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="py-3.5 px-4 sm:px-6">Name</th>
                        <th class="py-3.5 px-4">WhatsApp Phone</th>
                        <th class="py-3.5 px-4">Email</th>
                        <th class="py-3.5 px-4">Phonebook</th>
                        <th class="py-3.5 px-4">Source</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($contacts as $contact)
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/30 transition-colors">
                            <!-- Name & Initials Avatar -->
                            <td class="py-3 px-4 sm:px-6 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-primary/20 to-blue-200 dark:from-primary/30 dark:to-blue-900 flex items-center justify-center text-primary font-bold text-xs shrink-0">
                                    {{ substr($contact->name ?? $contact->mobile, 0, 1) }}
                                </div>
                                <span class="font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $contact->name ?? 'Unnamed Contact' }}
                                </span>
                            </td>

                            <!-- Phone -->
                            <td class="py-3 px-4 font-mono text-xs text-gray-800 dark:text-gray-200">
                                {{ $contact->mobile }}
                            </td>

                            <!-- Email -->
                            <td class="py-3 px-4 text-xs text-gray-500 dark:text-gray-400 truncate max-w-[150px]">
                                {{ $contact->email ?? '—' }}
                            </td>

                            <!-- Phonebook Group -->
                            <td class="py-3 px-4 text-xs">
                                @if ($contact->phonebook)
                                    <span class="px-2.5 py-0.5 rounded-full font-medium text-[11px] bg-blue-50 dark:bg-blue-950/60 text-primary border border-blue-100 dark:border-blue-900/40">
                                        {{ $contact->phonebook->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            <!-- Source -->
                            <td class="py-3 px-4 text-xs">
                                <span class="px-2 py-0.5 rounded font-mono text-[10px] uppercase font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                                    {{ $contact->source }}
                                </span>
                            </td>

                            <!-- Action Buttons -->
                            <td class="py-3 px-4 text-right space-x-1 shrink-0">
                                <a href="{{ route('inbox') }}" class="p-1.5 text-primary hover:bg-primary/10 rounded-lg inline-block transition-colors" title="Chat in Inbox">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                </a>
                                <button wire:click="editContact({{ $contact->id }})" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg inline-block transition-colors" title="Edit Contact">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <button wire:click="deleteContact({{ $contact->id }})" wire:confirm="Are you sure you want to delete this contact?" class="p-1.5 text-rose-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/20 rounded-lg inline-block transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400 text-xs">
                                No contacts found in this workspace. Add contacts or import a CSV file above.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="p-4 border-t border-gray-100 dark:border-gray-800">
            {{ $contacts->links() }}
        </div>
    </div>

    <!-- Create / Edit Contact Modal -->
    @if ($showContactModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-800 p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-base text-gray-900 dark:text-white">
                        {{ $editingContactId ? 'Edit Contact' : 'Create New Contact' }}
                    </h3>
                    <button wire:click="$set('showContactModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveContact" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">WhatsApp Phone Number *</label>
                        <input wire:model="mobile" 
                               type="text" 
                               placeholder="+15551234567" 
                               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        @error('mobile') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                        <input wire:model="name" 
                               type="text" 
                               placeholder="e.g. Jane Doe" 
                               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        @error('name') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                        <input wire:model="email" 
                               type="email" 
                               placeholder="jane@example.com" 
                               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Phonebook Group</label>
                        <select wire:model="phonebook_id" 
                                class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <option value="">No Group</option>
                            @foreach ($phonebooks as $pb)
                                <option value="{{ $pb->id }}">{{ $pb->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showContactModal', false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-primary hover:bg-primary-deep text-white shadow-sm shadow-primary/20">
                            Save Contact
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Create Phonebook Group Modal -->
    @if ($showPhonebookModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-800 p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-base text-gray-900 dark:text-white">Create Phonebook Group</h3>
                    <button wire:click="$set('showPhonebookModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="createPhonebook" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Group Name *</label>
                        <input wire:model="newPhonebookName" 
                               type="text" 
                               placeholder="e.g. VIP Clients, Summer Campaign" 
                               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        @error('newPhonebookName') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showPhonebookModal', false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-primary hover:bg-primary-deep text-white shadow-sm shadow-primary/20">
                            Create Group
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Import CSV Modal -->
    @if ($showImportModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-800 p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-base text-gray-900 dark:text-white">Import Contacts from CSV</h3>
                    <button wire:click="$set('showImportModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="importCsv" class="space-y-4">
                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-dashed border-gray-300 dark:border-gray-700 text-center space-y-2">
                        <input wire:model="csvFile" type="file" accept=".csv,text/csv" class="text-xs text-gray-500">
                        <p class="text-[11px] text-gray-400">CSV must have a header row with <code>name</code>, <code>mobile</code> (or <code>phone</code>), and optional <code>email</code>.</p>
                        @error('csvFile') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showImportModal', false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800">
                            Cancel
                        </button>
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                class="px-4 py-2 rounded-xl text-xs font-semibold bg-primary hover:bg-primary-deep text-white shadow-sm shadow-primary/20 flex items-center gap-1.5">
                            <span wire:loading.remove>Start Import</span>
                            <span wire:loading>Importing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
