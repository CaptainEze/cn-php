<?php
/**
 * This file boots the application.
 * It loads dependencies, sets up CORS, initializes cache, 
 * and loads environment variables.
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Core\Classes\EnvLoader;

use Core\Classes\Cors;

use Core\Classes\Cache;



// Enable CORS for all origins.
Cors::enable();

// Initialize the cache.
Cache::init(null, 86400);

// Load environment variables from the .env file.
try {
  EnvLoader::loadEnv(file: $_SERVER['DOCUMENT_ROOT'] . '/.env');
} catch (Exception $e) {
  echo "error loading .env file: " . $e->getMessage();
}
