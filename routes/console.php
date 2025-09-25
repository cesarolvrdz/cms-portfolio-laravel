<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Services\SupabaseService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('supabase:test', function () {
    $service = new SupabaseService();
    $projects = $service->getProjects();
    if (is_array($projects)) {
        $this->info('Conexión exitosa. Proyectos encontrados: ' . count($projects));
    } else {
        $this->error('No se pudo conectar a Supabase o no hay proyectos.');
    }
})->purpose('Prueba la conexión con Supabase');

Artisan::command('supabase:seed', function () {
    $service = new \App\Services\SupabaseService();
    $data = [
        [
            'title' => 'Portfolio Website',
            'description' => 'Sitio web personal desarrollado con Astro y Laravel.',
            'tech' => json_encode(['Astro', 'Laravel', 'Supabase']),
            'link' => 'https://tu-dominio-astro.com',
            'image' => 'https://via.placeholder.com/300x200.png?text=Portfolio',
            'status' => 'completed',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ],
        [
            'title' => 'Blog Engine',
            'description' => 'Motor de blog con Markdown y gestión de tags.',
            'tech' => json_encode(['Laravel', 'Filament', 'Supabase']),
            'link' => null,
            'image' => 'https://via.placeholder.com/300x200.png?text=Blog',
            'status' => 'in-progress',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]
    ];
    foreach ($data as $project) {
        $service->createProject($project);
    }
    $this->info('Datos de prueba insertados en Supabase.');
})->purpose('Inserta datos de prueba en Supabase');
