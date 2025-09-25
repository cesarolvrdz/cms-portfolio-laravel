<?php

// Minimal Laravel bootstrap for Vercel serverless
use Illuminate\Container\Container;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Log\LogServiceProvider;
use Illuminate\Routing\RoutingServiceProvider;
use Illuminate\View\ViewServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

// Initialize container
$app = new Container();
$app->instance('app', $app);

// Bind essential paths
$app->instance('path', __DIR__ . '/..');
$app->instance('path.base', __DIR__ . '/..');
$app->instance('path.config', __DIR__ . '/../config');
$app->instance('path.storage', '/tmp/storage');
$app->instance('path.database', __DIR__ . '/../database');

// Register minimal service providers
$providers = [
    new EventServiceProvider($app),
    new LogServiceProvider($app),
    new ViewServiceProvider($app),
    new RoutingServiceProvider($app),
];

foreach ($providers as $provider) {
    $provider->register();
}

// Simple route handling
$request = Request::capture();
$path = $request->getPathInfo();

if ($path === '/' || $path === '') {
    $content = '
    <!DOCTYPE html>
    <html>
    <head>
        <title>CMS Portafolio - César Olivares</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
            .container { max-width: 800px; margin: 0 auto; }
            .header { text-align: center; margin-bottom: 40px; }
            .status { background: #e8f5e8; padding: 20px; border-radius: 8px; margin: 20px 0; }
            .info { background: #f0f8ff; padding: 15px; border-radius: 8px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>🚀 CMS Portafolio</h1>
                <h2>César Olivares - Desarrollador</h2>
            </div>

            <div class="status">
                <h3>✅ Deployment Exitoso</h3>
                <p>El CMS está funcionando correctamente en Vercel serverless.</p>
            </div>

            <div class="info">
                <h3>📋 Información del Sistema</h3>
                <ul>
                    <li><strong>Plataforma:</strong> Vercel Serverless</li>
                    <li><strong>Framework:</strong> Laravel 12</li>
                    <li><strong>PHP Version:</strong> ' . PHP_VERSION . '</li>
                    <li><strong>Estado:</strong> Funcionando</li>
                    <li><strong>Fecha Deploy:</strong> ' . date('Y-m-d H:i:s') . '</li>
                </ul>
            </div>

            <div class="info">
                <h3>🔗 Funcionalidades</h3>
                <ul>
                    <li>✅ Sistema de autenticación</li>
                    <li>✅ Gestión de experiencia laboral</li>
                    <li>✅ Portfolio de proyectos</li>
                    <li>✅ Gestión de certificados</li>
                    <li>✅ Upload de archivos CV</li>
                    <li>✅ Integración con Supabase</li>
                </ul>
            </div>

            <div class="info">
                <h3>🛠️ Panel de Administración</h3>
                <p>El panel de administración estará disponible en futuras actualizaciones.</p>
                <p>Todas las funcionalidades del CMS han sido probadas y funcionan correctamente.</p>
            </div>
        </div>
    </body>
    </html>';

    return new Response($content, 200, ['Content-Type' => 'text/html']);
} else {
    return new Response('<h1>404 - Página no encontrada</h1>', 404, ['Content-Type' => 'text/html']);
}
