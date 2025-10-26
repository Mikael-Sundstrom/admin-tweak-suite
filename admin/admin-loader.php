<?php
/**
 * Handles inclusion of globally hooked files and shared functionality.
 *
 * @file admin/admin-loader.php
 * @package Admin_Tweak_Suite
 */

defined( 'ABSPATH' ) || exit;

// Register the admin page as a submenu.
add_action(
	'admin_menu',
	function () {
		add_submenu_page(
			'tools.php',
			esc_html__( 'Admin Tweak Suite', 'admin-tweak-suite' ),
			esc_html__( 'Admin Tweak Suite', 'admin-tweak-suite' ),
			'manage_options',
			'atweaks',
			'atweaks_render_admin_page',
			90
		);
	}
);

/**
 * Initializes the Admin Tweak Suite admin interface.
 *
 * Loads the correct admin tab content and enqueues tab-specific resources
 * based on the active tab. Hooked to 'admin_init' to ensure scripts and styles
 * can be registered before 'admin_enqueue_scripts' körs.
 *
 * Only runs when the current admin page is 'tools.php?page=atweaks'.
 *
 * @return void
 */
add_action(
	'admin_init',
	function () {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only checking page slug, not processing form data.
		if ( isset( $_GET['page'] ) && 'atweaks' === $_GET['page'] ) {
			$current_tab = atweaks_get_current_tab();

			// Load admin page.
			atweaks_safe_include( array( 'admin/admin-page.php' ) );

			// Load resources before admin_enqueue_scripts.
			switch ( $current_tab ) {
				case 'css':
					atweaks_safe_include( array( 'includes/helpers/codemirror-loader.php' ) );
					break;

				case 'scripts':
					atweaks_safe_include( array( 'includes/helpers/codemirror-loader.php' ) );
					break;

				case 'extra-tweaks':
					add_action(
						'admin_enqueue_scripts',
						function () {
							wp_enqueue_script( 'extra-tweaks-js', ATWEAKS_PLUGIN_URL . 'assets/js/extra-tweaks.js', array(), filemtime( ATWEAKS_PLUGIN_PATH . 'assets/js/extra-tweaks.js' ), true );
						}
					);
					break;

				case 'about':
					add_action(
						'admin_enqueue_scripts',
						function () {
							wp_enqueue_style( 'atweaks-about-style', ATWEAKS_PLUGIN_URL . 'assets/css/about.css', array(), '1.0' );
						}
					);
					break;

				case 'menu':
					add_action(
						'admin_enqueue_scripts',
						function () {
							wp_enqueue_style( 'atweaks-menu-style', ATWEAKS_PLUGIN_URL . 'assets/css/menu.css', array(), '1.0' );
							wp_enqueue_script( 'sortable-js', ATWEAKS_PLUGIN_URL . 'assets/js/sortable.min.js', array(), '1.15.6', true );
							wp_enqueue_script( 'init-sortable-js', ATWEAKS_PLUGIN_URL . 'assets/js/sortable.init.js', array( 'sortable-js' ), '1.0', true );
						}
					);
					break;
			}
		}
	}
);
