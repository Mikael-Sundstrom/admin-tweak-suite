<?php
/**
 * Plugin Name: Admin Tweak Suite
 * Plugin URI: https://github.com/Mikael-Sundstrom/admin-tweak-suite
 * Description: Effortlessly clean up, optimize and customize your WordPress admin — menu order, CSS, scripts and dashboard tweaks in one lightweight toolkit.
 * Version: 1.0
 * Author: Mikael Sundström
 * Author URI: https://github.com/Mikael-Sundstrom
 * Text Domain: admin-tweak-suite
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 5.9
 * Tested up to: 6.8
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package Admin_Tweak_Suite
 * @version 1.0
 */

defined( 'ABSPATH' ) || exit;

// Define plugin-wide constants.
if ( ! defined( 'ATWEAKS_PLUGIN_VERSION' ) ) {
	define( 'ATWEAKS_PLUGIN_VERSION', '1.0' );
}
if ( ! defined( 'ATWEAKS_PLUGIN_PATH' ) ) {
	define( 'ATWEAKS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'ATWEAKS_PLUGIN_URL' ) ) {
	define( 'ATWEAKS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'ATWEAKS_DB_PREFIX' ) ) {
	define( 'ATWEAKS_DB_PREFIX', 'atweaks' );
}

// Load file system helper immediately (used by atweaks_safe_include).
require_once plugin_dir_path( __FILE__ ) . 'includes/helpers/files.php';

// Load global helpers used across both frontend and admin.
atweaks_safe_include(
	array(
		'includes/helpers/validation.php',
		'includes/helpers/wp-options.php',
		'includes/hooks-global.php',
	)
);

// Load admin-specific functionality if in the admin area, otherwise load frontend hooks and functionality.
if ( is_admin() ) {

	atweaks_safe_include(
		array(
			'includes/helpers/redirect.php',
			'includes/helpers/navigation.php',
			'includes/hooks-admin.php',
			'includes/tabs/class-atweaks-tab-menu.php',
			'includes/tabs/class-atweaks-tab-css.php',
			'includes/tabs/class-atweaks-tab-scripts.php',
			'includes/tabs/class-atweaks-tab-extratweaks.php',
			'includes/tabs/class-atweaks-tab-about.php',
			'admin/extra-tweaks/extra-tweaks-handler.php',
			'admin/extra-tweaks/admin-notice.php',
			'admin/extra-tweaks/admin-notice-handler.php',
			'admin/extra-tweaks/dashboard-notes.php',
			'admin/extra-tweaks/dashboard-notes-handler.php',
			'admin/admin-loader.php',
		)
	);

	// Register admin tab actions (form submission handlers etc.).
	$atweaks_menu_tab         = new ATweaks_Tab_Menu();
	$atweaks_css_tab          = new ATweaks_Tab_Css();
	$atweaks_scripts_tab      = new ATweaks_Tab_Scripts();
	$atweaks_extra_tweaks_tab = new ATweaks_Tab_ExtraTweaks();
	$atweaks_about_tab        = new ATweaks_Tab_About();
	$atweaks_menu_tab->register();
	$atweaks_css_tab->register();
	$atweaks_scripts_tab->register();
	$atweaks_extra_tweaks_tab->register();
	$atweaks_about_tab->register();
} else {

	atweaks_safe_include(
		array(
			'includes/hooks-frontend.php',
		)
	);
}
