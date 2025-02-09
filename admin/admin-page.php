<?php if (!defined('ABSPATH')) exit;

/**
 * admin/admin-page.php
 * Renders the main page for the admin panel
 *
 * Handles navigation and dynamically calls the appropriate function for the tab.
 */

/**
 * Renders the main admin page for the Admin Tweak Suite plugin.
 *
 * Displays the navigation tabs and dynamically calls the function for the active tab.
 *
 * @return void
 */
function atweaks_render_admin_page() {
    // Get the currently active tab
    $current_tab = atweaks_get_current_tab();

    echo '<div class="wrap" style="max-width: 1000px;">';
    echo '<h1>' . esc_html__('Admin Tweak Suite', 'admin-tweak-suite') . '</h1>';
    atweaks_render_tabs($current_tab);
    atweaks_render_tab_content($current_tab);
    echo '</div>';
}


/**
 * Renders navigation tabs dynamically.
 *
 * @param string $current_tab The currently active tab.
 * @param array $tabs List of tabs with their slugs and labels.
 * 
 * @return void
 */
function atweaks_render_tabs($current_tab) {
    $tabs = [
        'menu'     => esc_html__('Admin Menu', 'admin-tweak-suite'),
        'css'      => esc_html__('CSS', 'admin-tweak-suite'),
        'scripts'  => esc_html__('Scripts', 'admin-tweak-suite'),
        'about'    => esc_html__('About', 'admin-tweak-suite'),
    ];

    echo '<h2 class="nav-tab-wrapper">';

    foreach ($tabs as $tab => $label) {
        $url = add_query_arg([
            'page' => 'atweaks',
            'tab' => esc_attr($tab),
        ], admin_url('tools.php'));

        $class = ($tab === $current_tab) ? 'nav-tab nav-tab-active' : 'nav-tab';
        echo '<a href="' . esc_url($url) . '" class="' . esc_attr($class) . '">' . esc_html($label) . '</a>';
    }

    echo '</h2>';
}

/**
 * Dynamically loads the content for the active tab.
 *
 * @param string $current_tab The currently active tab.
 * 
 * @return void
 */
function atweaks_render_tab_content($current_tab) {
    switch ($current_tab) {
        case 'menu':
            atweaks_render_menu_tab();
            break;

		case 'css':
			atweaks_render_css_tab();
			break;

		case 'scripts':
			atweaks_render_scripts_tab();
			break;
	
		case 'about':
			atweaks_render_about_tab();
			break;

        default:
			// Visa ett felmeddelande direkt i adminpanelen
			echo '<div class="notice notice-error is-dismissible">';
			echo '<p>' . esc_html__('Invalid tab. Please select a valid tab.', 'admin-tweak-suite') . '</p>';
			echo '</div>';
			break;
	}
}

/**
 * Renders the "Menu" tab.
 *
 * @return void
 */
function atweaks_render_menu_tab() {
    $file_path = [
		ATWEAKS_PLUGIN_PATH . 'admin/menu/menu-data.php',
		ATWEAKS_PLUGIN_PATH . 'admin/menu/menu-render.php',
		ATWEAKS_PLUGIN_PATH . 'admin/menu/menu.php',
	];
    atweaks_safe_include($file_path);
}

/**
 * Renders the "CSS" tab.
 *
 * @return void
 */
function atweaks_render_css_tab() {
    $file_path = [
		ATWEAKS_PLUGIN_PATH . 'admin/css/css.php',
	];
    atweaks_safe_include($file_path);
}

/**
 * Renders the "Scripts" tab.
 *
 * @return void
 */
function atweaks_render_scripts_tab() {
    $file_path = [
		ATWEAKS_PLUGIN_PATH . 'admin/scripts/scripts.php',
	];
    atweaks_safe_include($file_path);
}

/**
 * Renders the "About" tab.
 *
 * @return void
 */
function atweaks_render_about_tab() {
	$file_path = [
		ATWEAKS_PLUGIN_PATH . 'admin/about/about.php',
	];
    atweaks_safe_include($file_path);
}