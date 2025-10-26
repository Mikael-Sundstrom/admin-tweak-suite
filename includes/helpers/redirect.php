<?php
/**
 * Functions for handling redirections and admin notices.
 *
 * @file includes/helpers/redirect.php
 * @package Admin_Tweak_Suite
 */

defined( 'ABSPATH' ) || exit;

/**
 * Redirects to the specified tab with a message.
 *
 * @param string $tab The tab to redirect to. Default is 'about_atweaks'.
 * @param string $type The type of the message ('success', 'error', 'info', etc.). Default is 'info'.
 * @param string $message The message text to display. If empty, a default message is used.
 * @return void
 */
function atweaks_redirect_with_message( $tab = 'menu', $type = 'info', $message = '' ) {
	$allowed_types = array( 'success', 'error', 'info', 'warning' );
	$type          = in_array( $type, $allowed_types, true ) ? $type : 'info';

	if ( empty( $message ) ) {
		$default_messages = array(
			'success' => esc_html__( 'Operation completed successfully.', 'admin-tweak-suite' ),
			'error'   => esc_html__( 'An error occurred. Please try again.', 'admin-tweak-suite' ),
			'info'    => esc_html__( 'No settings were found to remove because none were configured.', 'admin-tweak-suite' ),
			'warning' => esc_html__( 'Action completed, but check for potential issues.', 'admin-tweak-suite' ),
		);
		$message          = isset( $default_messages[ $type ] ) ? $default_messages[ $type ] : esc_html__( 'Operation completed.', 'admin-tweak-suite' );
	}

	// Create a nonce for the redirect URL.
	$nonce = wp_create_nonce( 'atweaks_admin_notice' );

	$url = add_query_arg(
		array(
			'page'     => 'atweaks',
			'tab'      => sanitize_text_field( $tab ),
			'type'     => sanitize_text_field( $type ),
			'message'  => rawurlencode( sanitize_text_field( $message ) ),
			'_wpnonce' => $nonce,
		),
		admin_url( 'tools.php' )
	);

	wp_safe_redirect( $url );
	exit;
}

/**
 * Renders admin notices based on query parameters.
 *
 * Detects 'type' and 'message' parameters in the URL and displays an admin
 * notice with the appropriate style and text.
 *
 * @return void
 */
function atweaks_render_notices() {
	if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'atweaks_admin_notice' ) ) {
		return; // Stop if nonce verification fails.
	}

	if ( isset( $_GET['message'] ) && isset( $_GET['type'] ) ) {
		$allowed_types = array( 'success', 'error', 'warning', 'info' );

		// Unslash and sanitize the query parameters.
		$type = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : 'info';
		$type = in_array( $type, $allowed_types, true ) ? $type : 'info';

		$message = isset( $_GET['message'] ) ? sanitize_text_field( wp_unslash( $_GET['message'] ) ) : '';

		$classes = array(
			'success' => 'notice-success',
			'error'   => 'notice-error',
			'warning' => 'notice-warning',
			'info'    => 'notice-info',
		);

		$class = isset( $classes[ $type ] ) ? $classes[ $type ] : 'notice-info';
		echo '<div class="notice ' . esc_attr( $class ) . ' is-dismissible">';
		echo '<p>' . esc_html( $message ) . '</p>';
		echo '</div>';
	}
}
add_action( 'admin_notices', 'atweaks_render_notices' );
