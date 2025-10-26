<?php
/**
 * Handles the submission of the admin notification.
 *
 * @file admin/extra-tweaks/admin-notice-handler.php
 * @package Admin_Tweak_Suite
 */

defined( 'ABSPATH' ) || exit;

/**
 * Migrera äldre (icke-prefixade) option-nycklar till prefixade atweaks_… en gång.
 */
function atweaks_migrate_admin_notice_options() {

	// 1) Direkta nycklar.
	$map = array(
		'admin_notice_allow_editor' => 'atweaks_admin_notice_allow_editor',
		'admin_notice_visible'      => 'atweaks_admin_notice_visible',
		'admin_notice_scope'        => 'atweaks_admin_notice_scope',
	);

	foreach ( $map as $old => $new ) {
		$val_old = get_option( $old, null );
		if ( null !== $val_old && null === get_option( $new, null ) ) {
			update_option( $new, $val_old );
			delete_option( $old );
		}
	}

	// 2) Roll-baserade nycklar.
	if ( isset( $GLOBALS['wp_roles'] ) && is_object( $GLOBALS['wp_roles'] ) ) {
		foreach ( $GLOBALS['wp_roles']->roles as $role_key => $role ) {
			if ( 'administrator' === $role_key ) {
				continue;
			}
			$old = 'admin_notice_access_' . $role_key;
			$new = 'atweaks_admin_notice_access_' . $role_key;

			$val_old = get_option( $old, null );
			if ( null !== $val_old && null === get_option( $new, null ) ) {
				update_option( $new, $val_old );
				delete_option( $old );
			}
		}
	}
}
add_action( 'plugins_loaded', 'atweaks_migrate_admin_notice_options', 5 );

add_action( 'admin_post_save_admin_notice', 'atweaks_handle_admin_notice_submission' );

/**
 * Handles the form submission for saving the admin notice settings.
 *
 * @return void
 */
function atweaks_handle_admin_notice_submission() {
	if (
		! isset( $_POST['save_admin_notice_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['save_admin_notice_nonce'] ) ), 'save_admin_notice_action' )
	) {
		wp_die( esc_html__( 'Security check failed. Please try again.', 'admin-tweak-suite' ) );
	}

	// Access: admin always; possibly editor if allowed.
	$is_admin          = current_user_can( 'manage_options' );
	$allow_editors     = (int) get_option( 'atweaks_admin_notice_allow_editor', 0 );
	$editor_cap_ok     = current_user_can( 'edit_others_posts' );
	$can_manage_notice = ( $is_admin || ( $allow_editors && $editor_cap_ok ) );

	if ( ! $can_manage_notice ) {
		wp_die( esc_html__( 'You do not have permission to perform this action.', 'admin-tweak-suite' ) );
	}

	// Input.
	$title   = isset( $_POST['admin_notice_title'] ) ? sanitize_text_field( wp_unslash( $_POST['admin_notice_title'] ) ) : '';
	$content = isset( $_POST['admin_notice_content'] ) ? sanitize_textarea_field( wp_unslash( $_POST['admin_notice_content'] ) ) : '';
	$type    = isset( $_POST['admin_notice_type'] ) ? sanitize_text_field( wp_unslash( $_POST['admin_notice_type'] ) ) : 'info';
	$scope   = isset( $_POST['admin_notice_scope'] ) ? sanitize_text_field( wp_unslash( $_POST['admin_notice_scope'] ) ) : 'global';

	if ( ! in_array( $type, array( 'default', 'success', 'info', 'warning', 'error' ), true ) ) {
		$type = 'info';
	}

	if ( ! in_array( $scope, array( 'global', 'dashboard', 'pages', 'posts', 'media', 'comments' ), true ) ) {
		$scope = 'global';
	}

	// Save.
	update_option( 'atweaks_admin_notice_title', $title );
	update_option( 'atweaks_admin_notice_content', $content );
	update_option( 'atweaks_admin_notice_type', $type );
	update_option( 'atweaks_admin_notice_scope', $scope );

	$visible = isset( $_POST['admin_notice_visible'] ) ? 1 : 0;
	update_option( 'atweaks_admin_notice_visible', $visible );

	// Role visibility.
	if ( isset( $GLOBALS['wp_roles'] ) ) {
		foreach ( $GLOBALS['wp_roles']->roles as $role_key => $role ) {
			if ( 'administrator' === $role_key ) {
				continue;
			}
			$visibility_option = 'atweaks_admin_notice_access_' . $role_key;
			update_option( $visibility_option, isset( $_POST[ $visibility_option ] ) ? 1 : 0 );
		}
	}

	wp_safe_redirect( admin_url() );
	exit;
}
