<?php if (!defined('ABSPATH')) exit;

/**
 * Includes/helpers/files.php
 * Functions for handling file operations.
 */

/**
 * Safely includes a file or multiple files if they exist.
 *
 * @param string|array $file_paths The path(s) to the file(s).
 * @param string $error_message Optional error message if a file is missing.
 */
function atweaks_safe_include($file_paths) {
    if (!is_array($file_paths)) {
        $file_paths = [$file_paths]; // Convert to array if it's a string
    }

    foreach ($file_paths as $file_path) {
        if (file_exists($file_path)) {
            require_once $file_path;
        }
    }
}
