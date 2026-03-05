<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HierarchyPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_hierarchy_page_returns_successful_response(): void
    {
        $response = $this->get('/hierarchy');

        $response->assertStatus(200);
        $response->assertViewIs('hierarchy');
    }

    public function test_hierarchy_page_contains_org_roles_tab(): void
    {
        $response = $this->get('/hierarchy');

        $response->assertSee('Org Roles');
        $response->assertSee('tab-org-roles');
        $response->assertSee('panel-org-roles');
    }

    public function test_hierarchy_page_contains_divisions_tab(): void
    {
        $response = $this->get('/hierarchy');

        $response->assertSee('Divisions');
        $response->assertSee('tab-divisions');
        $response->assertSee('panel-divisions');
    }

    public function test_hierarchy_page_has_tab_roles(): void
    {
        $response = $this->get('/hierarchy');

        $response->assertSee('role="tablist"', false);
        $response->assertSee('role="tab"', false);
        $response->assertSee('role="tabpanel"', false);
    }

    public function test_hierarchy_page_divisions_panel_starts_hidden(): void
    {
        $response = $this->get('/hierarchy');

        $response->assertSee('id="panel-divisions"', false);
        // Divisions panel should carry the 'hidden' class on initial render
        $response->assertSee('panel-divisions', false);
        $content = $response->getContent();
        $this->assertStringContainsString('panel-divisions', $content);
        $this->assertMatchesRegularExpression('/id="panel-divisions"[^>]*class="[^"]*hidden/', $content);
    }
}
