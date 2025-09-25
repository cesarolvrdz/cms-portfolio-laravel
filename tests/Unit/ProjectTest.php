<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Project;

class ProjectTest extends TestCase
{
    public function test_project_has_tech_tags_relation()
    {
        $project = new Project();
        $this->assertTrue(method_exists($project, 'techTags'));
    }
}
