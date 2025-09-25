<?php

// Vercel PHP Runtime Entry Point for Laravel

try {
    // Ensure we're in the right directory
    if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
        http_response_code(500);
        die('Vendor autoload not found');
    }

    require __DIR__ . '/../vendor/autoload.php';

    // Create Laravel application
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // Handle the request
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $request = Illuminate\Http\Request::capture();
    $response = $kernel->handle($request);

    $response->send();
    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    http_response_code(500);

    // In production, don't show detailed errors to users
    if ($_ENV['APP_DEBUG'] ?? false) {
        echo "Error: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    } else {
        echo "Internal Server Error";
    }

    // Log the error (Vercel will capture this)
    error_log("Laravel Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
}
