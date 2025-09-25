<?php
// Environment check for Vercel deployment

echo "<h1>Environment Check</h1>";

echo "<h2>PHP Version</h2>";
echo "PHP Version: " . phpversion() . "<br>";

echo "<h2>File System</h2>";
echo "Current directory: " . getcwd() . "<br>";
echo "API directory: " . __DIR__ . "<br>";
echo "Vendor exists: " . (file_exists(__DIR__ . '/../vendor/autoload.php') ? 'YES' : 'NO') . "<br>";
echo "Bootstrap exists: " . (file_exists(__DIR__ . '/../bootstrap/app.php') ? 'YES' : 'NO') . "<br>";

echo "<h2>Environment Variables</h2>";
$envVars = [
    'APP_ENV',
    'APP_DEBUG', 
    'APP_KEY',
    'SUPABASE_URL',
    'SUPABASE_KEY',
    'DB_CONNECTION'
];

foreach ($envVars as $var) {
    $value = $_ENV[$var] ?? 'NOT SET';
    if ($var === 'APP_KEY' || $var === 'SUPABASE_KEY') {
        $value = $value !== 'NOT SET' ? 'SET (hidden)' : 'NOT SET';
    }
    echo "$var: $value<br>";
}

echo "<h2>Directory Contents</h2>";
echo "<strong>Root directory:</strong><br>";
$files = scandir(__DIR__ . '/..');
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        echo "- $file<br>";
    }
}

try {
    require __DIR__ . '/../vendor/autoload.php';
    echo "<h2>Laravel Test</h2>";
    echo "Autoload: SUCCESS<br>";
    
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    echo "Bootstrap: SUCCESS<br>";
    
} catch (\Exception $e) {
    echo "<h2>Laravel Test - ERROR</h2>";
    echo "Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
}