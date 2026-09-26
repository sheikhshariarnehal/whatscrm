<div class="p-4 sm:p-6 lg:p-8 space-y-6">
    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Team & Agent Management</h1>
                <x-tag color="primary" class="font-bold">{{ count($members) }} Members</x-tag>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Manage workspace collaborators, assign inbox permissions, and track individual agent performance.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <x-button wire:click="openInviteModal" variant="solid" size="md">
                <x-ph-icon name="user-plus" weight="bold" class="text-base mr-1.5" />
                <span>Add Team Member</span>
            </x-button>
        </div>
    </div>

    <!-- Flash message -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-xs font-semibold border border-emerald-200 dark:border-emerald-800/50 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('info'))
        <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 text-xs font-semibold border border-blue-200 dark:border-blue-800/50">
            <span>{{ session('info') }}</span>
        </div>
    @endif

    <!-- Sub-Navbar Tabs -->
    <x-tabs>
        <x-tab-item wire:click="setTab('members')" :active="$activeTab === 'members'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span>Team Members ({{ count($members) }})</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('permissions')" :active="$activeTab === 'permissions'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <span>Roles & Permissions</span>
        </x-tab-item>
        <x-tab-item wire:click="setTab('performance')" :active="$activeTab === 'performance'">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span>Agent Performance</span>
        </x-tab-item>
    </x-tabs>

    <!-- TAB 1: MEMBERS LIST -->
    @if($activeTab === 'members')
        <x-card gutterless class="overflow-hidden">
            <x-table hoverable>
                <thead>
                    <tr>
                        <th>Agent Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Active Chats</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $m)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <x-avatar :name="$m->user->name ?? 'User'" size="sm" />
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-xs">{{ $m->user->name ?? 'User' }}</div>
                                        <div class="text-[11px] text-gray-400">Joined {{ $m->created_at->format('M Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-xs font-mono text-gray-600 dark:text-gray-300">
                                {{ $m->user->email ?? '—' }}
                            </td>
                            <td>
                                @if($m->role === 'owner')
                                    <x-tag color="purple" class="font-bold">
                                        👑 Workspace Owner
                                    </x-tag>
                                @elseif($m->role === 'admin')
                                    <x-tag color="primary" class="font-bold">
                                        🛡️ Administrator
                                    </x-tag>
                                @else
                                    <x-tag color="gray" class="font-medium">
                                        💬 Support Agent
                                    </x-tag>
                                @endif
                            </td>
                            <td class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                                {{ $stats[$m->id]['assigned_chats'] ?? 0 }} chats
                            </td>
                            <td>
                                @if($m->role === 'owner')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Online
                                    </span>
                                @else
                                    <button wire:click="toggleMemberStatus({{ $m->id }})" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium transition-colors {{ $m->is_active ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100' : 'bg-gray-100 dark:bg-gray-800 text-gray-400 hover:bg-gray-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $m->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                        {{ $m->is_active ? 'Active' : 'Deactivated' }}
                                    </button>
                                @endif
                            </td>
                            <td class="text-right">
                                @if($m->role !== 'owner')
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button wire:click="editMember({{ $m->id }})" class="p-1.5 rounded-lg text-gray-400 hover:text-primary hover:bg-primary/10 transition-colors" title="Edit Member">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button wire:click="removeMember({{ $m->id }})" wire:confirm="Remove this agent from the workspace?" class="p-1.5 rounded-lg text-gray-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors" title="Remove Member">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Owner</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>
        </x-card>
    @endif

    <!-- TAB 2: ROLES & PERMISSION MATRIX -->
    @if($activeTab === 'permissions')
        <x-card bodyClass="p-6">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Role Permissions Matrix</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Overview of granular access privileges across workspace roles.</p>

            <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-800">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 dark:bg-gray-800/40 uppercase font-semibold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-800">
                        <tr>
                            <th class="px-6 py-3.5">Permission Capability</th>
                            <th class="px-6 py-3.5 text-center">Support Agent</th>
                            <th class="px-6 py-3.5 text-center">Administrator</th>
                            <th class="px-6 py-3.5 text-center">Workspace Owner</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        <tr>
                            <td class="px-6 py-3.5 font-medium text-gray-800 dark:text-gray-200">Reply to assigned customer conversations</td>
                            <td class="px-6 py-3.5 text-center text-emerald-500 font-bold">✓</td>
                            <td class="px-6 py-3.5 text-center text-emerald-500 font-bold">✓</td>
                            <td class="px-6 py-3.5 text-center text-emerald-500 font-bold">✓</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3.5 font-medium text-gray-800 dark:text-gray-200">View all unassigned & team conversations</td>
                            <td class="px-6 py-3.5 text-center text-gray-400 font-bold">—</td>
                            <td class="px-6 py-3.5 text-center text-emerald-500 font-bold">✓</td>
                            <td class="px-6 py-3.5 text-center text-emerald-500 font-bold">✓</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3.5 font-medium text-gray-800 dark:text-gray-200">Launch mass broadcast campaigns</td>
                            <td class="px-6 py-3.5 text-center text-gray-400 font-bold">—</td>
                            <td class="px-6 py-3.5 text-center text-emerald-500 font-bold">✓</td>
                            <td class="px-6 py-3.5 text-center text-emerald-500 font-bold">✓</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3.5 font-medium text-gray-800 dark:text-gray-200">Export contacts and customer lists (CSV)</td>
                            <td class="px-6 py-3.5 text-center text-gray-400 font-bold">—</td>
                            <td class="px-6 py-3.5 text-center text-emerald-500 font-bold">✓</td>
                            <td class="px-6 py-3.5 text-center text-emerald-500 font-bold">✓</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3.5 font-medium text-gray-800 dark:text-gray-200">Create & configure chatbot automation rules</td>
                            <td class="px-6 py-3.5 text-center text-gray-400 font-bold">—</td>
                            <td class="px-6 py-3.5 text-center text-emerald-500 font-bold">✓</td>
                            <td class="px-6 py-3.5 text-center text-emerald-500 font-bold">✓</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3.5 font-medium text-gray-800 dark:text-gray-200">Generate developer API tokens & webhooks</td>
                            <td class="px-6 py-3.5 text-center text-gray-400 font-bold">—</td>
                            <td class="px-6 py-3.5 text-center text-emerald-500 font-bold">✓</td>
                            <td class="px-6 py-3.5 text-center text-emerald-500 font-bold">✓</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3.5 font-medium text-gray-800 dark:text-gray-200">Billing, plan upgrade & delete workspace</td>
                            <td class="px-6 py-3.5 text-center text-gray-400 font-bold">—</td>
                            <td class="px-6 py-3.5 text-center text-gray-400 font-bold">—</td>
                            <td class="px-6 py-3.5 text-center text-emerald-500 font-bold">✓</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-card>
    @endif

    <!-- TAB 3: AGENT PERFORMANCE -->
    @if($activeTab === 'performance')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($members as $m)
                <x-card bodyClass="p-6 flex flex-col justify-between h-full">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <x-avatar :name="$m->user->name ?? 'User'" size="md" />
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white text-sm">{{ $m->user->name }}</h3>
                                <span class="text-xs text-gray-400 capitalize">{{ $m->role }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                                <span class="text-[11px] text-gray-500 dark:text-gray-400 font-medium block mb-0.5">Active Chats</span>
                                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $stats[$m->id]['assigned_chats'] ?? 0 }}</span>
                            </div>
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                                <span class="text-[11px] text-gray-500 dark:text-gray-400 font-medium block mb-0.5">Outbound Replies</span>
                                <span class="text-lg font-bold text-primary">{{ $stats[$m->id]['messages_sent'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 mt-4 border-t border-gray-100 dark:border-gray-800 text-xs text-gray-500 dark:text-gray-400 flex items-center justify-between">
                        <span>Status</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">98% Satisfaction</span>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif

    <!-- MODAL: INVITE MEMBER -->
    @if($showInviteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-bold text-gray-900 dark:text-white text-base">Add New Team Member</h3>
                    <button wire:click="$set('showInviteModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="space-y-4 pt-1">
                    <x-form-item label="Full Name" :required="true" :error="$errors->first('inviteName')">
                        <x-input wire:model="inviteName" placeholder="e.g. Sarah Miller" prefix-icon="contacts" :invalid="$errors->has('inviteName')" />
                    </x-form-item>

                    <x-form-item label="Email Address" :required="true" :error="$errors->first('inviteEmail')">
                        <x-input type="email" wire:model="inviteEmail" placeholder="sarah@acme.com" prefix-icon="broadcast" :invalid="$errors->has('inviteEmail')" />
                    </x-form-item>

                    <x-form-item label="Password" :required="true" :error="$errors->first('invitePassword')">
                        <x-input type="password" wire:model="invitePassword" placeholder="Minimum 8 characters" prefix-icon="settings" :invalid="$errors->has('invitePassword')" />
                    </x-form-item>

                    <x-form-item label="Workspace Role">
                        <x-select wire:model="inviteRole">
                            <option value="agent">Support Agent (Assigned chats only)</option>
                            <option value="admin">Administrator (Full CRM access)</option>
                        </x-select>
                    </x-form-item>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <x-button wire:click="$set('showInviteModal', false)" variant="default" size="sm">Cancel</x-button>
                    <x-button wire:click="inviteMember" variant="solid" size="sm">Add Member</x-button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: EDIT MEMBER -->
    @if($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-xl max-w-sm w-full p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-bold text-gray-900 dark:text-white text-base">Edit Member Role</h3>
                    <button wire:click="$set('showEditModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="space-y-4 pt-1">
                    <x-form-item label="Role">
                        <x-select wire:model="editRole">
                            <option value="agent">Support Agent</option>
                            <option value="admin">Administrator</option>
                        </x-select>
                    </x-form-item>

                    <div class="pt-1">
                        <x-checkbox wire:model="editIsActive" id="editIsActive" label="Active Member" />
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <x-button wire:click="$set('showEditModal', false)" variant="default" size="sm">Cancel</x-button>
                    <x-button wire:click="updateMember" variant="solid" size="sm">Save Changes</x-button>
                </div>
            </div>
        </div>
    @endif
</div>
