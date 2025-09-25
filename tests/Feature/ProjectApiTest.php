<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_projects()
    {
        $response = $this->getJson('/api/v1/projects');
        $response->assertStatus(200)->assertJsonStructure(['data', 'links', 'meta']);
    }
}
