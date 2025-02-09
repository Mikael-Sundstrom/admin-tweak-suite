<?php if (!defined('ABSPATH')) exit;

// Control that the plugin is uninstalled through the WordPress interface
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Plugin prefix for database wp_options
if (!defined('ATWEAKS_DB_PREFIX')) {
    define('ATWEAKS_DB_PREFIX', 'atweaks');
}

$prefix = ATWEAKS_DB_PREFIX . '_';
global $wpdb;

// Get all options with the plugin prefix and delete them
$all_options = wp_load_alloptions();
foreach ($all_options as $option_name => $option_value) {
    if (strpos($option_name, $prefix) === 0) {
        delete_option($option_name);
    }
}