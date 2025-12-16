<?php
/**
 * Lightweight bootstrap that prefers Composer's autoloader when present
 * and falls back to a simple PSR-4 autoloader for the App namespace.
 */
$composerAutoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

require_once __DIR__ . '/src/autoload.php';

