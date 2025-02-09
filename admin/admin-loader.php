<?php if (!defined('ABSPATH')) exit;

/**
 * admin/admin-loader.php
 * Handles inclusion of globally hooked files and shared functionality.
 */

// Register the admin page as a submenu
add_action('admin_menu', function () {
    add_submenu_page(
        'tools.php',
		esc_html__( 'Admin Tweak Suite', 'admin-tweak-suite' ),
		esc_html__( 'Admin Tweak Suite', 'admin-tweak-suite' ),
		'manage_options',
		'atweaks',
		'atweaks_render_admin_page',
		90
	);
});

// Include global resources and hooks
$global_hooks_files = [
    'includes/hooks-admin.php',
    'includes/handlers/init.php',
];

foreach ($global_hooks_files as $file) {
    $file_path = ATWEAKS_PLUGIN_PATH . $file;
    if (file_exists($file_path)) {
        atweaks_safe_include($file_path);
    }
}

/**
 * Load admin page and resources dynamically based on the current tab.
 * 
 * @return void
 */
add_action('load-tools_page_atweaks', function () {
	atweaks_safe_include(ATWEAKS_PLUGIN_PATH . 'admin/admin-page.php');

	$current_tab = atweaks_get_current_tab();

	// Load codemirror resources for CSS and scripts tabs
	if ($current_tab === 'css' || $current_tab === 'scripts') {
		atweaks_safe_include(ATWEAKS_PLUGIN_PATH . 'includes/codemirror-loader.php');
	}
	
    // Load sortable resources for the menu tab
    if ($current_tab === 'about') {
		wp_enqueue_style('atweaks-about-style', ATWEAKS_PLUGIN_URL . 'assets/css/about.css', [], '1.0');
	}
    if ($current_tab === 'menu') {
		wp_enqueue_style('atweaks-menu-style', ATWEAKS_PLUGIN_URL . 'assets/css/menu.css', [], '1.0');
		wp_enqueue_script('sortable-js', ATWEAKS_PLUGIN_URL . 'assets/js/sortable.min.js', [], '1.15.6', true);
		wp_enqueue_script('init-sortable-js', ATWEAKS_PLUGIN_URL . 'assets/js/sortable.init.js', ['sortable-js'], '1.0', true);
    }
});