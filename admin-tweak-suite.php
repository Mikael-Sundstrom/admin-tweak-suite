<?php if (!defined('ABSPATH')) exit;

/**
 * Plugin Name: Admin Tweak Suite
 * Plugin URI: https://github.com/Mikael-Sundstrom/admin-tweak-suite
 * Description: A suite of tools to customize and optimize the WordPress admin panel. Features include admin menu customization, custom CSS and JavaScript management, and advanced script handling for improved performance.
 * Version: 1.0
 * Author: Mikael Sundström
 * Author URI: https://github.com/Mikael-Sundstrom
 * Text Domain: admin-tweak-suite
 * Domain Path: /languages
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Requires PHP: 7.4
 * Requires at least: 5.9
 * Tested up to: 6.7
 *
 * @package admin-tweak-suite
 * @version 1.0
 */

// Define global constants
if (!defined('ATWEAKS_PLUGIN_VERSION')) define('ATWEAKS_PLUGIN_VERSION', '1.0');
if (!defined('ATWEAKS_PLUGIN_PATH')) define('ATWEAKS_PLUGIN_PATH', plugin_dir_path(__FILE__));
if (!defined('ATWEAKS_PLUGIN_URL')) define('ATWEAKS_PLUGIN_URL', plugin_dir_url(__FILE__));
if (!defined('ATWEAKS_DB_PREFIX')) define('ATWEAKS_DB_PREFIX', 'atweaks');

// Load language files
add_action('plugins_loaded', 'atweaks_load_textdomain');
function atweaks_load_textdomain() {
    load_plugin_textdomain('admin-tweak-suite', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

// A list of required files to load globally
$required_files = [
    'includes/helpers/init.php',    // Load helper functions
    'includes/hooks-frontend.php',  // Load helper functions
    'admin/admin-loader.php',       // Load admin panel
];

// Load all required files
foreach ($required_files as $file) {
	$file_path = ATWEAKS_PLUGIN_PATH . $file;
	if (file_exists($file_path)) {
		require_once $file_path;
	}
}

// error_log("Deleted menu options: " . print_r($deleted_options, true), 3, ABSPATH . 'wp-content/debug.log');