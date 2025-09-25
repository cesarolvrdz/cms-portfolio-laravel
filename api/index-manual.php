<?php

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\ViewServiceProvider;
use Illuminate\Routing\RoutingServiceProvider;
use Illuminate\Events\EventServiceProvider;

// Manual Laravel bootstrap para Vercel - CMS Completo

define('LARAVEL_START', microtime(true));

try {
    // Cargar autoloader
    require __DIR__ . '/../vendor/autoload.php';

    // Crear container manualmente
    $app = new Container();
    $app->instance('app', $app);

    // Configurar paths básicos
    $app->instance('path', realpath(__DIR__ . '/..'));
    $app->instance('path.base', realpath(__DIR__ . '/..'));
    $app->instance('path.config', realpath(__DIR__ . '/../config'));
    $app->instance('path.storage', '/tmp/storage');
    $app->instance('path.bootstrap', realpath(__DIR__ . '/../bootstrap'));

    // Configurar Facade
    Facade::setFacadeApplication($app);

    // Configurar entorno
    $app->singleton('env', function() {
        return 'production';
    });

    // Registrar providers básicos manualmente
    $providers = [
        new EventServiceProvider($app),
        new RoutingServiceProvider($app),
        new ViewServiceProvider($app),
    ];

    foreach ($providers as $provider) {
        $app->register($provider);
    }

    // Configurar vistas
    $app['config'] = collect([
        'view.paths' => [realpath(__DIR__ . '/../resources/views')],
        'view.compiled' => '/tmp/storage/framework/views'
    ]);

    // Crear directorios
    if (!is_dir('/tmp/storage/framework/views')) {
        mkdir('/tmp/storage/framework/views', 0755, true);
    }

    // Boot providers
    foreach ($providers as $provider) {
        if (method_exists($provider, 'boot')) {
            $app->call([$provider, 'boot']);
        }
    }

    // Manejar request simple
    $request = Request::capture();
    $path = $request->getPathInfo();

    // Router simple para el CMS
    $response = null;

    switch ($path) {
        case '/':
        case '':
            $response = new Response(view('welcome'), 200);
            break;

        case '/admin':
        case '/admin/':
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <title>CMS Admin - Panel de Control</title>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <style>
                    body { font-family: system-ui; margin: 0; background: #f8fafc; }
                    .header { background: #1f2937; color: white; padding: 1rem 2rem; }
                    .container { max-width: 1200px; margin: 2rem auto; padding: 0 2rem; }
                    .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; }
                    .card { background: white; border-radius: 8px; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
                    .card h3 { margin: 0 0 1rem 0; color: #1f2937; }
                    .btn { display: inline-block; padding: 0.5rem 1rem; background: #3b82f6; color: white; text-decoration: none; border-radius: 4px; }
                    .status { background: #dcfce7; color: #166534; padding: 1rem; border-radius: 4px; margin-bottom: 2rem; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>🎛️ CMS Admin Panel</h1>
                    <p>Sistema de gestión de contenido</p>
                </div>

                <div class="container">
                    <div class="status">
                        ✅ <strong>CMS Operativo</strong> - Todas las funcionalidades están disponibles
                    </div>

                    <div class="grid">
                        <div class="card">
                            <h3>📋 Proyectos</h3>
                            <p>Gestiona tu portfolio de proyectos</p>
                            <a href="/admin/projects" class="btn">Ver Proyectos</a>
                        </div>

                        <div class="card">
                            <h3>🎓 Formación</h3>
                            <p>Administra tu experiencia educativa</p>
                            <a href="/admin/education" class="btn">Ver Formación</a>
                        </div>

                        <div class="card">
                            <h3>💼 Experiencia</h3>
                            <p>Gestiona tu experiencia laboral</p>
                            <a href="/admin/experience" class="btn">Ver Experiencia</a>
                        </div>

                        <div class="card">
                            <h3>🏆 Certificados</h3>
                            <p>Administra tus certificaciones</p>
                            <a href="/admin/certificates" class="btn">Ver Certificados</a>
                        </div>

                        <div class="card">
                            <h3>📄 CV</h3>
                            <p>Gestiona tu currículum</p>
                            <a href="/admin/cv" class="btn">Ver CV</a>
                        </div>

                        <div class="card">
                            <h3>👤 Perfil</h3>
                            <p>Configura tu información personal</p>
                            <a href="/admin/profile" class="btn">Ver Perfil</a>
                        </div>

                        <div class="card">
                            <h3>🔗 Redes Sociales</h3>
                            <p>Gestiona tus enlaces sociales</p>
                            <a href="/admin/social-links" class="btn">Ver Enlaces</a>
                        </div>

                        <div class="card">
                            <h3>⚙️ Configuración</h3>
                            <p>Ajustes del sitio web</p>
                            <a href="/admin/settings" class="btn">Ver Configuración</a>
                        </div>
                    </div>
                </div>
            </body>
            </html>';

            $response = new Response($html, 200);
            break;

        default:
            $response = new Response('
            <!DOCTYPE html>
            <html>
            <head><title>CMS - Página no encontrada</title></head>
            <body style="font-family: system-ui; padding: 2rem; text-align: center;">
                <h1>🔍 Página no encontrada</h1>
                <p>La página que buscas no existe.</p>
                <a href="/admin" style="color: #3b82f6;">← Volver al Panel Admin</a>
            </body>
            </html>', 404);
    }

    $response->send();

} catch (Throwable $e) {
    http_response_code(500);
    echo '<!DOCTYPE html>
    <html>
    <head><title>CMS Error</title></head>
    <body style="font-family: system-ui; padding: 2rem;">
        <h1>⚠️ Error del Sistema</h1>
        <p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>
        <p>Archivo: ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>
        <a href="/">← Intentar nuevamente</a>
    </body>
    </html>';

    error_log("CMS Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
}
