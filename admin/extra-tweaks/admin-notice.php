<?php
/**
 * Renders the "Admin Notification" widget for the dashboard.
 *
 * @file admin/extra-tweaks/admin-notice.php
 * @package Admin_Tweak_Suite
 */

defined( 'ABSPATH' ) || exit;

/**
 * Renders the admin notification settings form.
 *
 * @return void
 */
function atweaks_render_admin_notice_widget() {

	$notice_title   = get_option( 'atweaks_admin_notice_title', '' );
	$notice_content = get_option( 'atweaks_admin_notice_content', '' );
	$notice_type    = get_option( 'atweaks_admin_notice_type', 'info' );
	$notice_scope   = get_option( 'atweaks_admin_notice_scope', 'global' );
	?>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">

		<input type="hidden" name="action" value="save_admin_notice">
		<?php wp_nonce_field( 'save_admin_notice_action', 'save_admin_notice_nonce' ); ?>

		<p><label for="atweaks-admin-notice-title"><?php esc_html_e( 'Notification Title:', 'admin-tweak-suite' ); ?></label></p>
		<input id="atweaks-admin-notice-title" type="text" name="admin_notice_title" value="<?php echo esc_attr( $notice_title ); ?>" style="width:100%;">

		<p><label for="atweaks-admin-notice-content"><?php esc_html_e( 'Notification Content:', 'admin-tweak-suite' ); ?></label></p>
		<textarea id="atweaks-admin-notice-content" name="admin_notice_content" rows="5" style="width:100%;"><?php echo esc_textarea( $notice_content ); ?></textarea>

		<p>
			<label>
				<input type="checkbox" name="admin_notice_visible" value="1" <?php checked( get_option( 'atweaks_admin_notice_visible', 1 ), 1 ); ?> />
				<?php esc_html_e( 'Enable notification', 'admin-tweak-suite' ); ?>
			</label>
		</p>

		<div style="display: flex; gap: 24px; align-items: flex-start; margin-top: 16px;">

			<!-- Notification Type -->
			<div>
				<p><strong><?php esc_html_e( 'Notification Type', 'admin-tweak-suite' ); ?></strong></p>
				<select name="admin_notice_type">
					<option value="default" <?php selected( $notice_type, 'default' ); ?>><?php esc_html_e( 'Default', 'admin-tweak-suite' ); ?></option>
					<option value="success" <?php selected( $notice_type, 'success' ); ?>><?php esc_html_e( 'Confirmed', 'admin-tweak-suite' ); ?></option>
					<option value="info"    <?php selected( $notice_type, 'info' ); ?>><?php esc_html_e( 'Info', 'admin-tweak-suite' ); ?></option>
					<option value="warning" <?php selected( $notice_type, 'warning' ); ?>><?php esc_html_e( 'Attention', 'admin-tweak-suite' ); ?></option>
					<option value="error"   <?php selected( $notice_type, 'error' ); ?>><?php esc_html_e( 'Alert', 'admin-tweak-suite' ); ?></option>
				</select>
			</div>

			<!-- Display Location -->
			<div>
				<p><strong><?php esc_html_e( 'Display Location', 'admin-tweak-suite' ); ?></strong></p>
				<select name="admin_notice_scope">
					<option value="global"    <?php selected( $notice_scope, 'global' ); ?>><?php esc_html_e( 'All admin pages', 'admin-tweak-suite' ); ?></option>
					<option value="dashboard" <?php selected( $notice_scope, 'dashboard' ); ?>><?php esc_html_e( 'Dashboard only', 'admin-tweak-suite' ); ?></option>
					<option value="pages"     <?php selected( $notice_scope, 'pages' ); ?>><?php esc_html_e( 'Pages only', 'admin-tweak-suite' ); ?></option>
					<option value="posts"     <?php selected( $notice_scope, 'posts' ); ?>><?php esc_html_e( 'Posts only', 'admin-tweak-suite' ); ?></option>
					<option value="media"     <?php selected( $notice_scope, 'media' ); ?>><?php esc_html_e( 'Media only', 'admin-tweak-suite' ); ?></option>
					<option value="comments"  <?php selected( $notice_scope, 'comments' ); ?>><?php esc_html_e( 'Comments only', 'admin-tweak-suite' ); ?></option>
				</select>
			</div>

			<!-- Visibility Options -->
			<div>
				<p><strong><?php esc_html_e( 'Notification Visibility', 'admin-tweak-suite' ); ?></strong></p>
				<?php
				$roles_obj  = wp_roles();
				$role_names = ( is_object( $roles_obj ) && method_exists( $roles_obj, 'get_names' ) )
					? $roles_obj->get_names()          // ska redan vara översatta
					: ( $roles_obj->role_names ?? [] );// fallback

				foreach ( $role_names as $role_key => $role_label ) :
					if ( 'administrator' === $role_key ) {
						continue;
					}

					// Säkerställ översättning även om $role_label är engelskt:
					$role_label = translate_user_role( $role_label );

					$visibility_option = 'atweaks_admin_notice_access_' . $role_key;
					?>
					<label style="display:block;margin-bottom:4px;">
						<input type="checkbox"
							name="<?php echo esc_attr( $visibility_option ); ?>"
							value="1" <?php checked( get_option( $visibility_option, 0 ), 1 ); ?> />
						<?php echo esc_html( $role_label ); ?>
					</label>
				<?php endforeach; ?>
			</div>
		</div>

		<p>
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Notification', 'admin-tweak-suite' ); ?></button>
		</p>
	</form>
	<?php
}
