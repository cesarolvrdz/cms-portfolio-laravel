<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseServiceOptimized;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $supabase;

    public function __construct(SupabaseServiceOptimized $supabase)
    {
        $this->supabase = $supabase;
    }

    public function index()
    {
        try {
            // Obtener estadísticas del dashboard
            $stats = [
                'projects' => $this->getProjectsStats(),
                'profile' => $this->getProfileCompleteness(),
                'social_links' => $this->getSocialLinksCount(),
                'education' => $this->getEducationCount(),
                'experience' => $this->getExperienceCount(),
                'recent_activity' => $this->getRecentActivity()
            ];

            return view('admin.dashboard', compact('stats'));
        } catch (\Exception $e) {
            return view('admin.dashboard', ['stats' => $this->getEmptyStats()]);
        }
    }

    private function getProjectsStats()
    {
        $projects = $this->supabase->getProjects();

        return [
            'total' => count($projects),
            'featured' => count(array_filter($projects, function($p) { return $p['featured'] ?? false; })),
            'active' => count(array_filter($projects, function($p) { return ($p['status'] ?? '') === 'active'; })),
            'completed' => count(array_filter($projects, function($p) { return ($p['status'] ?? '') === 'completed'; }))
        ];
    }

    private function getProfileCompleteness()
    {
        $profile = $this->supabase->getProfile();
        if (!$profile) return ['percentage' => 0, 'missing' => []];

        $required_fields = ['name', 'title', 'bio', 'email', 'skills'];
        $optional_fields = ['location', 'phone', 'resume_url', 'avatar_url'];

        $completed_required = 0;
        $completed_optional = 0;
        $missing = [];

        foreach ($required_fields as $field) {
            if (!empty($profile[$field])) {
                $completed_required++;
            } else {
                $missing[] = $field;
            }
        }

        foreach ($optional_fields as $field) {
            if (!empty($profile[$field])) {
                $completed_optional++;
            }
        }

        $total_fields = count($required_fields) + count($optional_fields);
        $completed_fields = $completed_required + $completed_optional;
        $percentage = round(($completed_fields / $total_fields) * 100);

        return [
            'percentage' => $percentage,
            'completed_required' => $completed_required,
            'total_required' => count($required_fields),
            'completed_optional' => $completed_optional,
            'total_optional' => count($optional_fields),
            'missing' => $missing
        ];
    }

    private function getSocialLinksCount()
    {
        $links = $this->supabase->getSocialLinks();
        return [
            'total' => count($links),
            'active' => count(array_filter($links, function($link) { return $link['is_active'] ?? false; }))
        ];
    }

    private function getEducationCount()
    {
        try {
            $education = $this->supabase->getEducation();
            return [
                'total' => count($education),
                'featured' => count(array_filter($education, function($edu) { return $edu['is_featured'] ?? false; })),
                'current' => count(array_filter($education, function($edu) { return $edu['is_current'] ?? false; }))
            ];
        } catch (\Exception $e) {
            return ['total' => 0, 'featured' => 0, 'current' => 0];
        }
    }

    private function getExperienceCount()
    {
        try {
            $experience = $this->supabase->getWorkExperience();
            return [
                'total' => count($experience),
                'featured' => count(array_filter($experience, function($exp) { return $exp['is_featured'] ?? false; })),
                'current' => count(array_filter($experience, function($exp) { return $exp['is_current'] ?? false; }))
            ];
        } catch (\Exception $e) {
            return ['total' => 0, 'featured' => 0, 'current' => 0];
        }
    }

    private function getRecentActivity()
    {
        // Placeholder para actividad reciente
        return [
            ['action' => 'Perfil actualizado', 'time' => '2 horas'],
            ['action' => 'Nuevo proyecto agregado', 'time' => '1 día'],
            ['action' => 'Enlaces sociales actualizados', 'time' => '3 días']
        ];
    }

    private function getEmptyStats()
    {
        return [
            'projects' => ['total' => 0, 'featured' => 0, 'active' => 0, 'completed' => 0],
            'profile' => ['percentage' => 0, 'missing' => []],
            'social_links' => ['total' => 0, 'active' => 0],
            'education' => ['total' => 0, 'featured' => 0, 'current' => 0],
            'experience' => ['total' => 0, 'featured' => 0, 'current' => 0],
            'recent_activity' => []
        ];
    }
}
