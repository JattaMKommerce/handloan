<?php

// Ensure that the script is being run in a Laravel context
require 'vendor/autoload.php'; // Load Composer dependencies

use Illuminate\Support\Facades\App;

// Get the storage path
$storagePath = __DIR__ . '/storage/framework/';
$downFile = $storagePath . 'down';

// Check if the "down" file already exists
if (!file_exists($downFile)) {
    // Create the "down" file
    file_put_contents($downFile, json_encode([
        'time' => date('Y-m-d H:i:s'), // Use PHP date function
        'message' => 'We are currently updating the website. Please check back later.', // Custom message
        'retry' => 60, // Retry after X seconds
        'allowed' => [], // Array of IPs allowed to bypass maintenance mode
    ]));

    echo 'Application is now in maintenance mode.';
} else {
    echo 'Application is already in maintenance mode.';
}
?>
