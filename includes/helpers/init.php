<?php if (!defined('ABSPATH')) exit;

/**
 * Includes/helpers/init.php
 * Loads all helper files for the plugin.
 */

// List of helper files to load
$helper_files = [
    'files.php',
    'navigation.php',
    'redirect.php',
    'validation.php',
    'wp-options.php',
];

// Load each helper file
foreach ($helper_files as $file) {
    $file_path = ATWEAKS_PLUGIN_PATH . 'includes/helpers/' . $file;
    if (file_exists($file_path)) {
        require_once $file_path;
    }
}