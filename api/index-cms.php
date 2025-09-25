<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

// Laravel CMS Bootstrap para Vercel - Versión Completa

define('LARAVEL_START', microtime(true));

try {
    // Configurar directorio de trabajo correcto
    chdir(__DIR__ . '/..');

    // Cargar autoloader
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoloadPath)) {
        throw new Exception('Composer autoload not found');
    }
    require $autoloadPath;

    // Configurar entorno antes del bootstrap
    $requiredEnvVars = [
        'APP_ENV' => 'production',
        'APP_DEBUG' => 'false',
        'APP_KEY' => 'base64:' . base64_encode('laravel-cms-vercel-key-32chars'),
        'APP_URL' => 'https://cms-portafolio-ces.vercel.app',
        'APP_TIMEZONE' => 'UTC',

        // Database
        'DB_CONNECTION' => 'sqlite',
        'DB_DATABASE' => '/tmp/database.sqlite',

        // Cache y Session para serverless
        'CACHE_STORE' => 'array',
        'SESSION_DRIVER' => 'array',
        'QUEUE_CONNECTION' => 'sync',

        // Logs
        'LOG_CHANNEL' => 'stderr',
        'LOG_LEVEL' => 'error',

        // Paths para serverless
        'VIEW_COMPILED_PATH' => '/tmp/storage/framework/views',
        'CACHE_PATH' => '/tmp/storage/framework/cache',

        // Filesystem
        'FILESYSTEM_DISK' => 'local',
    ];

    // Establecer variables de entorno
    foreach ($requiredEnvVars as $key => $value) {
        if (!isset($_ENV[$key])) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }

    // Variables de Supabase desde environment de Vercel
    $supabaseVars = ['SUPABASE_URL', 'SUPABASE_ANON_KEY', 'SUPABASE_SERVICE_ROLE_KEY'];
    foreach ($supabaseVars as $var) {
        if (isset($_ENV[$var])) {
            putenv("$var=" . $_ENV[$var]);
        }
    }

    // Crear directorios necesarios
    $directories = [
        '/tmp/storage/framework/cache/data',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/framework/views',
        '/tmp/storage/logs',
        '/tmp/storage/app/public'
    ];

    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    // Crear base de datos SQLite si no existe
    $dbPath = '/tmp/database.sqlite';
    if (!file_exists($dbPath)) {
        touch($dbPath);
        chmod($dbPath, 0666);
    }

    // Bootstrap Laravel application
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // Manejar la request
    $kernel = $app->make(Kernel::class);
    $request = Request::capture();

    // Override para rutas en Vercel
    if ($request->getPathInfo() === '/') {
        // Redireccionar a la página principal del CMS
        $request = Request::create('/admin/dashboard', 'GET');
    }

    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);

} catch (Throwable $e) {
    // Error handling mejorado
    http_response_code(500);

    $errorMessage = $e->getMessage();
    $errorFile = $e->getFile();
    $errorLine = $e->getLine();

    // Log error
    error_log("Laravel CMS Error: $errorMessage in $errorFile:$errorLine");

    // Mostrar error user-friendly
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>CMS - Error Temporal</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
            .error-container {
                max-width: 600px; margin: 0 auto; background: white;
                padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            .error-title { color: #e74c3c; font-size: 24px; margin-bottom: 20px; }
            .error-message { background: #fdf2f2; padding: 15px; border-radius: 5px; }
            .back-link {
                display: inline-block; margin-top: 20px; padding: 10px 20px;
                background: #3498db; color: white; text-decoration: none; border-radius: 5px;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1 class="error-title">🔧 CMS en Mantenimiento</h1>
            <div class="error-message">
                <p><strong>El sistema está configurándose...</strong></p>
                <p>Estamos solucionando un problema técnico. Por favor intenta nuevamente en unos minutos.</p>
            </div>
            <a href="/" class="back-link">← Reintentar</a>
        </div>
    </body>
    </html>';
}
