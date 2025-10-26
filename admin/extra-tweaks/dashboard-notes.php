<?php
/**
 * Renders the "Dashboard Notes" widget for the admin panel.
 *
 * @file admin/extra-tweaks/dashboard-notes.php
 * @package Admin_Tweak_Suite
 */

defined( 'ABSPATH' ) || exit;

/**
 * Renders the "Dashboard Notes" widget for the admin panel.
 *
 * @return void
 */
function atweaks_render_dashboard_notes_widget() {

	// Feature toggle.
	if ( ! get_option( 'atweaks_enable_shared_notes', 0 ) ) {
		echo esc_html__( 'Dashboard notes are currently disabled.', 'admin-tweak-suite' );
		return;
	}

	$current_user = wp_get_current_user();
	$user_roles   = (array) $current_user->roles;

	$can_read  = false;
	$can_write = false;

	if ( in_array( 'administrator', $user_roles, true ) ) {
		$can_read  = true;
		$can_write = true;
	} else {
		foreach ( $user_roles as $role ) {
			if ( get_option( 'atweaks_notes_read_' . $role, 0 ) ) {
				$can_read = true;
			}
			if ( get_option( 'atweaks_notes_write_' . $role, 0 ) ) {
				$can_write = true;
			}
		}
	}

	if ( ! $can_read ) {
		echo esc_html__( 'You do not have permission to view the shared notes.', 'admin-tweak-suite' );
		return;
	}

	// Current note.
	$shared_note = get_option( 'atweaks_shared_note', '' );

	// Build read/write role labels.
	global $wp_roles;
	$roles = ( isset( $wp_roles ) && is_object( $wp_roles ) ) ? $wp_roles->roles : array();

	$read_roles  = array();
	$write_roles = array();

	foreach ( $roles as $role_key => $role ) {
		if ( 'administrator' === $role_key ) {
			continue;
		}
		if ( get_option( 'atweaks_notes_read_' . $role_key, 0 ) ) {
			$read_roles[] = $role['name'];
		}
		if ( get_option( 'atweaks_notes_write_' . $role_key, 0 ) ) {
			$write_roles[] = $role['name'];
		}
	}

	// Editor UI.
	if ( $can_write ) { ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="atweaks_save_dashboard_note">
			<?php wp_nonce_field( 'atweaks_save_dashboard_note_action', 'atweaks_save_dashboard_note_nonce' ); ?>
			<textarea name="shared_note" rows="8" style="width:100%;"><?php echo esc_textarea( $shared_note ); ?></textarea>
			<p>
				<button type="submit" class="button button-primary">
					<?php esc_html_e( 'Save Note', 'admin-tweak-suite' ); ?>
				</button>
				<span style="float: right;">
					<?php if ( ! empty( $read_roles ) ) : ?>
						<span class="dashicons dashicons-visibility"
							style="cursor: pointer; margin-right: 8px;"
							title="<?php echo esc_attr__( 'Read access: ', 'admin-tweak-suite' ) . esc_attr( implode( ', ', $read_roles ) ); ?>">
						</span>
					<?php endif; ?>
					<?php if ( ! empty( $write_roles ) ) : ?>
						<span class="dashicons dashicons-edit"
							style="cursor: pointer;"
							title="<?php echo esc_attr__( 'Write access: ', 'admin-tweak-suite' ) . esc_attr( implode( ', ', $write_roles ) ); ?>">
						</span>
					<?php endif; ?>
				</span>
			</p>
		</form>
	<?php } else { ?>
		<div><?php echo nl2br( esc_html( $shared_note ) ); ?></div>
		<?php
	}
}
