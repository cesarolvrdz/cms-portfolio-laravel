<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

// Vercel Serverless Laravel Bootstrap

define('LARAVEL_START', microtime(true));

try {
    // Load autoloader with correct path
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoloadPath)) {
        throw new Exception('Composer autoload file not found at: ' . $autoloadPath);
    }
    require $autoloadPath;

    // Bootstrap Laravel application  
    $bootstrapPath = __DIR__ . '/../bootstrap/app.php';
    if (!file_exists($bootstrapPath)) {
        throw new Exception('Laravel bootstrap file not found at: ' . $bootstrapPath);
    }
    $app = require $bootstrapPath;
    
    // Ensure critical directories exist in serverless environment
    $criticalDirs = [
        '/tmp/storage/framework/cache/data',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/framework/views',
        '/tmp/storage/logs'
    ];
    
    foreach ($criticalDirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    
    // Set serverless-specific environment overrides BEFORE bootstrapping
    $serverlessEnv = [
        'APP_ENV' => 'production',
        'APP_KEY' => 'base64:' . base64_encode(random_bytes(32)),
        'APP_DEBUG' => 'false',
        'CACHE_STORE' => 'array',
        'SESSION_DRIVER' => 'array', 
        'LOG_CHANNEL' => 'single',
        'QUEUE_CONNECTION' => 'sync',
        'DB_CONNECTION' => 'sqlite',
        'DB_DATABASE' => '/tmp/database.sqlite',
        'VIEW_COMPILED_PATH' => '/tmp/storage/framework/views',
        'CACHE_PATH' => '/tmp/storage/framework/cache',
        'SESSION_PATH' => '/tmp/storage/framework/sessions'
    ];
    
    foreach ($serverlessEnv as $key => $value) {
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
    
    // Create SQLite database if it doesn't exist
    $dbPath = '/tmp/database.sqlite';
    if (!file_exists($dbPath)) {
        touch($dbPath);
        chmod($dbPath, 0666);
    }

    // Bootstrap Laravel application with Vercel-specific configuration
    $bootstrapPath = __DIR__ . '/../bootstrap/app-vercel.php';
    if (!file_exists($bootstrapPath)) {
        // Fallback to regular bootstrap
        $bootstrapPath = __DIR__ . '/../bootstrap/app.php';
    }
    
    if (!file_exists($bootstrapPath)) {
        throw new Exception('Laravel bootstrap file not found at: ' . $bootstrapPath);
    }
    $app = require $bootstrapPath;    // Handle HTTP request
    $kernel = $app->make(Kernel::class);
    $request = Request::capture();
    $response = $kernel->handle($request);

    $response->send();
    $kernel->terminate($request, $response);

} catch (Throwable $e) {
    http_response_code(500);
    
    // Show detailed error in debug mode, generic error in production
    if (($_ENV['APP_DEBUG'] ?? false) === true || ($_ENV['APP_DEBUG'] ?? false) === 'true') {
        echo '<h1>Laravel Application Error</h1>';
        echo '<p><b>Message:</b> ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<p><b>File:</b> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        echo '<h1>Server Error</h1><p>The application encountered an error and cannot continue.</p>';
    }
    
    // Always log errors for debugging
    error_log('Laravel Serverless Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
}
