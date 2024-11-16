<?php
// Path to Laravel's "down" file
$downFile = __DIR__ . '/storage/framework/down';
//echo $downFile;
// Check if the "down" file exists
if (file_exists($downFile)) {
    // Delete the "down" file to bring the app back online
    unlink($downFile);

    echo 'Application is now live.';
} else {
    echo 'Application is already live.';
}
?>
