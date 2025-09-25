<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Set up Vercel-specific environment
if (!isset($_ENV['APP_KEY']) || empty($_ENV['APP_KEY'])) {
    $_ENV['APP_KEY'] = 'base64:' . base64_encode('32-char-key-for-vercel-deploy12');
    putenv('APP_KEY=' . $_ENV['APP_KEY']);
}

// Override configuration for serverless
$serverlessConfig = [
    'cache.default' => 'array',
    'session.driver' => 'array',
    'view.compiled' => '/tmp/storage/framework/views',
    'filesystems.disks.local.root' => '/tmp/storage/app',
    'database.default' => 'sqlite',
    'database.connections.sqlite.database' => '/tmp/database.sqlite',
];

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Registrar middleware de autenticación personalizado
        $middleware->alias([
            'auth.admin' => \App\Http\Middleware\EnsureAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
