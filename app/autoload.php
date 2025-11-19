<?php
/**
 * Simple Autoloader untuk Class OOP
 * 
 * Usage:
 * require_once __DIR__ . '/../app/autoload.php';
 */

spl_autoload_register(function ($class) {
    // Base directory untuk models
    $base_dir = __DIR__ . '/models/';
    
    // File path
    $file = $base_dir . $class . '.php';
    
    // Jika file exists, require it
    if (file_exists($file)) {
        require_once $file;
    }
});

