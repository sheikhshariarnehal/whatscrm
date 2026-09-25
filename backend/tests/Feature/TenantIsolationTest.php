<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_workspace_scope_isolates_contacts_by_active_tenant(): void
    {
        $acme = Workspace::where('slug', 'acme-corp')->firstOrFail();
        $beta = Workspace::where('slug', 'beta-agency')->firstOrFail();

        // 1. Set active workspace to Acme Corp
        App::instance('current_workspace_id', $acme->id);

        $acmeContacts = Contact::all();
        $this->assertGreaterThan(0, $acmeContacts->count());
        foreach ($acmeContacts as $contact) {
            $this->assertEquals($acme->id, $contact->workspace_id);
            $this->assertNotEquals('Bruce Wayne', $contact->name);
        }

        // 2. Set active workspace to Beta Agency
        App::instance('current_workspace_id', $beta->id);

        $betaContacts = Contact::all();
        $this->assertGreaterThan(0, $betaContacts->count());
        foreach ($betaContacts as $contact) {
            $this->assertEquals($beta->id, $contact->workspace_id);
            $this->assertEquals('Bruce Wayne', $contact->name);
        }
    }

    public function test_new_record_automatically_inherits_current_workspace_id(): void
    {
        $acme = Workspace::where('slug', 'acme-corp')->firstOrFail();
        App::instance('current_workspace_id', $acme->id);

        $newContact = Contact::create([
            'name' => 'Auto Tenant Contact',
            'mobile' => '+15559998888',
        ]);

        $this->assertEquals($acme->id, $newContact->workspace_id);
    }

    public function test_admin_can_bypass_scope_explicitly(): void
    {
        $allContacts = Contact::withoutGlobalScopes()->get();
        $workspaceIds = $allContacts->pluck('workspace_id')->unique();

        $this->assertGreaterThan(1, $workspaceIds->count());
    }
}
