<?php
/**
 * Renders the main page for the admin panel.
 *
 * @file admin/admin-page.php
 * @package Admin_Tweak_Suite
 */

defined( 'ABSPATH' ) || exit;

/**
 * Renders the admin page.
 *
 * @return void
 */
function atweaks_render_admin_page() {
	$current_tab = atweaks_get_current_tab();

	echo '<div class="wrap" style="max-width: 700px;">';
	echo '<h1>' . esc_html__( 'Admin Tweak Suite', 'admin-tweak-suite' ) . '</h1>';
	atweaks_render_tabs( $current_tab );
	atweaks_render_tab_content( $current_tab );
	echo '</div>';
}

/**
 * Renders the admin tabs.
 *
 * @param string $current_tab The current active tab.
 * @return void
 */
function atweaks_render_tabs( $current_tab ) {
	$tabs = array(
		'menu'         => esc_html__( 'Admin Menu', 'admin-tweak-suite' ),
		'css'          => esc_html__( 'CSS', 'admin-tweak-suite' ),
		'scripts'      => esc_html__( 'Scripts', 'admin-tweak-suite' ),
		'extra-tweaks' => esc_html__( 'Extra Tweaks', 'admin-tweak-suite' ),
		'about'        => esc_html__( 'About', 'admin-tweak-suite' ),
	);

	echo '<h2 class="nav-tab-wrapper">';
	foreach ( $tabs as $tab => $label ) {
		$url   = add_query_arg(
			array(
				'page' => 'atweaks',
				'tab'  => esc_attr( $tab ),
			),
			admin_url( 'tools.php' )
		);
		$class = ( $tab === $current_tab ) ? 'nav-tab nav-tab-active' : 'nav-tab';
		echo '<a href="' . esc_url( $url ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $label ) . '</a>';
	}
	echo '</h2>';
}

/**
 * Renders the content for the active tab.
 *
 * @param string $current_tab The current active tab.
 * @return void
 */
function atweaks_render_tab_content( $current_tab ) {
	switch ( $current_tab ) {
		case 'menu':
			$tab = new ATweaks_Tab_Menu();
			break;

		case 'css':
			$tab = new ATweaks_Tab_Css();
			break;

		case 'scripts':
			$tab = new ATweaks_Tab_Scripts();
			break;

		case 'extra-tweaks':
			$tab = new ATweaks_Tab_ExtraTweaks();
			break;

		case 'about':
			$tab = new ATweaks_Tab_About();
			break;

		default:
			echo '<div class="notice notice-error is-dismissible">';
			echo '<p>' . esc_html__( 'Invalid tab. Please select a valid tab.', 'admin-tweak-suite' ) . '</p>';
			echo '</div>';
			return;
	}

	// Run render() for the current class.
	$tab->render();
}
