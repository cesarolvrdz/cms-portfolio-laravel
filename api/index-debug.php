<?php

// Vercel PHP Runtime Entry Point for Laravel - DEBUG VERSION

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "DEBUG: Starting Laravel bootstrap...\n";

try {
    // Check if autoload exists
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoloadPath)) {
        die("ERROR: Autoload file not found at: " . $autoloadPath);
    }
    echo "DEBUG: Autoload found\n";

    require $autoloadPath;
    echo "DEBUG: Autoload loaded\n";

    // Check if bootstrap exists
    $bootstrapPath = __DIR__ . '/../bootstrap/app.php';
    if (!file_exists($bootstrapPath)) {
        die("ERROR: Bootstrap file not found at: " . $bootstrapPath);
    }
    echo "DEBUG: Bootstrap found\n";

    $app = require_once $bootstrapPath;
    echo "DEBUG: App bootstrapped\n";

    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "DEBUG: Kernel created\n";

    $request = Illuminate\Http\Request::capture();
    echo "DEBUG: Request captured\n";

    $response = $kernel->handle($request);
    echo "DEBUG: Response handled\n";

    $response->send();

    $kernel->terminate($request, $response);
    echo "DEBUG: Kernel terminated\n";

} catch (\Exception $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
} catch (\Error $e) {
    echo "FATAL PHP ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
