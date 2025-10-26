<?php
/**
 * Handles the submission of shared dashboard notes.
 *
 * @file admin/extra-tweaks/dashboard-notes-handler.php
 * @package Admin_Tweak_Suite
 */

defined( 'ABSPATH' ) || exit;

/**
 * Handles the submission of shared dashboard notes.
 *
 * @return void
 */
function atweaks_handle_dashboard_note_submission() {

	// Permission.
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die( esc_html__( 'You do not have permission to perform this action.', 'admin-tweak-suite' ) );
	}

	// Nonce.
	if (
		! isset( $_POST['atweaks_save_dashboard_note_nonce'] ) ||
		! wp_verify_nonce(
			sanitize_text_field( wp_unslash( $_POST['atweaks_save_dashboard_note_nonce'] ) ),
			'atweaks_save_dashboard_note_action'
		)
	) {
		wp_die( esc_html__( 'Security check failed. Please try again.', 'admin-tweak-suite' ) );
	}

	// Save note.
	if ( isset( $_POST['shared_note'] ) ) {
		$note = sanitize_textarea_field( wp_unslash( $_POST['shared_note'] ) );

		update_option( 'atweaks_shared_note', $note );
		set_transient( 'atweaks_cached_dashboard_note', $note );
	}

	wp_safe_redirect( admin_url() );
	exit;
}
add_action( 'admin_post_atweaks_save_dashboard_note', 'atweaks_handle_dashboard_note_submission' );
