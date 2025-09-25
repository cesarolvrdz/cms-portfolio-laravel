#!/usr/bin/env php
<?php

echo "=== Vercel Build Debug ===\n";
echo "Current directory: " . getcwd() . "\n";
echo "PHP version: " . PHP_VERSION . "\n";

// Check if vendor exists
if (is_dir('vendor')) {
    echo "✅ vendor/ directory exists\n";
    if (is_dir('vendor/illuminate')) {
        echo "✅ vendor/illuminate/ exists\n";
    } else {
        echo "❌ vendor/illuminate/ NOT found\n";
    }
} else {
    echo "❌ vendor/ directory NOT found\n";
}

// Check composer files
if (file_exists('composer.json')) {
    echo "✅ composer.json exists\n";
} else {
    echo "❌ composer.json NOT found\n";
}

if (file_exists('composer.lock')) {
    echo "✅ composer.lock exists\n";
} else {
    echo "❌ composer.lock NOT found\n";
}

// Check if composer is available
$composerCheck = shell_exec('which composer 2>/dev/null');
if ($composerCheck) {
    echo "✅ Composer available at: " . trim($composerCheck) . "\n";

    // Try to run composer install
    echo "\n=== Running composer install ===\n";
    system('composer install --no-dev --optimize-autoloader 2>&1');

} else {
    echo "❌ Composer NOT available\n";
}

echo "\n=== End Debug ===\n";
