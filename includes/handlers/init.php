<?php if (!defined('ABSPATH')) exit;

/**
 * Includes/form-handlers/init.php
 * Loads all form-handlers for the plugin.
 */

// Lista över form-handlers
$handlers = [
    'about-handler.php',
    'css-handler.php',
    'menu-handler.php',
    'scripts-handler.php',
];

foreach ($handlers as $handler) {
    $file_path = ATWEAKS_PLUGIN_PATH . 'includes/handlers/' . $handler;

    atweaks_safe_include($file_path);
}
