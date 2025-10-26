<?php
/**
 * Handles the "Extra Tweaks" tab content for the admin panel.
 *
 * @file admin/extra-tweaks/extra-tweaks-handler.php
 * @package Admin_Tweak_Suite
 */

defined( 'ABSPATH' ) || exit;

/**
 * Handles the "Extra Tweaks" tab content for the admin panel.
 *
 * @return void
 */
function atweaks_manage_extra_tweaks() {
	if ( ! current_user_can( 'manage_options' ) ) {
		atweaks_redirect_with_message( 'extra-tweaks', 'error', esc_html__( 'Access Denied.', 'admin-tweak-suite' ) );
	}

	$nonce_value = isset( $_POST['atweaks_extra_sizes_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['atweaks_extra_sizes_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce_value, 'atweaks_save_extra_sizes' ) ) {
		atweaks_redirect_with_message( 'extra-tweaks', 'error', esc_html__( 'Security check failed.', 'admin-tweak-suite' ) );
	}

	$post = isset( $_POST ) ? wp_unslash( $_POST ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing

	$cb = static function ( array $p, string $key ): int {
		return isset( $p[ $key ] ) ? 1 : 0;
	};

	// --- Extra image sizes ---
	update_option( 'atweaks_enable_custom_small', $cb( $post, 'atweaks_enable_custom_small' ) );
	update_option( 'atweaks_enable_custom_medium', $cb( $post, 'atweaks_enable_custom_medium' ) );
	update_option( 'atweaks_enable_custom_large', $cb( $post, 'atweaks_enable_custom_large' ) );

	update_option( 'atweaks_crop_custom_small', $cb( $post, 'atweaks_crop_custom_small' ) );
	update_option( 'atweaks_crop_custom_medium', $cb( $post, 'atweaks_crop_custom_medium' ) );
	update_option( 'atweaks_crop_custom_large', $cb( $post, 'atweaks_crop_custom_large' ) );

	foreach ( array(
		'atweaks_image_size_small_width',
		'atweaks_image_size_small_height',
		'atweaks_image_size_medium_width',
		'atweaks_image_size_medium_height',
		'atweaks_image_size_large_width',
		'atweaks_image_size_large_height',
	) as $opt ) {
		if ( isset( $post[ $opt ] ) ) {
			update_option( $opt, max( 1, absint( $post[ $opt ] ) ) );
		}
	}

	// --- Shared notes ---
	update_option( 'atweaks_enable_shared_notes', $cb( $post, 'atweaks_enable_shared_notes' ) );

	global $wp_roles;
	if ( isset( $wp_roles ) && is_object( $wp_roles ) ) {
		foreach ( $wp_roles->roles as $role_key => $role ) {
			if ( 'administrator' === $role_key ) {
				continue;
			}
			update_option( 'atweaks_notes_read_' . $role_key, $cb( $post, 'atweaks_notes_read_' . $role_key ) );
			update_option( 'atweaks_notes_write_' . $role_key, $cb( $post, 'atweaks_notes_write_' . $role_key ) );
		}
	}

	// --- Admin notification (dashboard-widget + editorbehörighet) ---
	$widget_enabled = $cb( $post, 'atweaks_admin_notice_widget_enabled' );
	update_option( 'atweaks_admin_notice_widget_enabled', $widget_enabled );
	update_option( 'atweaks_admin_notice_allow_editor', $cb( $post, 'atweaks_admin_notice_allow_editor' ) );

	// If the widget is disabled in the settings: also hide the banner.
	if ( 0 === $widget_enabled ) {
		update_option( 'atweaks_admin_notice_visible', 0 );
	}

	// Clear any cache and return.
	if ( function_exists( 'atweaks_flush_transients' ) ) {
		atweaks_flush_transients();
	}
	atweaks_redirect_with_message( 'extra-tweaks', 'success', esc_html__( 'Settings saved.', 'admin-tweak-suite' ) );
}
add_action( 'admin_post_atweaks_manage_extra_tweaks', 'atweaks_manage_extra_tweaks' );
