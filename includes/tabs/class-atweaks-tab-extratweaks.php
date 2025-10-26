<?php
/**
 * Tab for Extra Tweaks settings.
 * Handles the rendering of the Extra Tweaks settings tab.
 *
 * @file includes/tabs/class-atweaks-tab-extratweaks.php
 * @package Admin_Tweak_Suite
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class ATweaks_Tab_ExtraTweaks.
 *
 * Renders the Extra Tweaks settings tab.
 */
class ATweaks_Tab_ExtraTweaks {

	/**
	 * Register things for the tab. (No form handler here; handler is separate.)
	 *
	 * @return void
	 */
	public function register(): void {
		// Intentionally empty. Form submit hanteras av admin_post_atweaks_manage_extra_tweaks i handler-filen.
	}

	/**
	 * Renders the tab content.
	 *
	 * @return void
	 */
	public function render(): void {
		$roles       = $this->get_available_roles();
		$image_sizes = $this->get_image_sizes();
		?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="atweaks_manage_extra_tweaks">
			<?php wp_nonce_field( 'atweaks_save_extra_sizes', 'atweaks_extra_sizes_nonce' ); ?>

			<h3><?php esc_html_e( 'Extra Image Sizes', 'admin-tweak-suite' ); ?></h3>
			<p><?php esc_html_e( 'These custom image sizes will be generated for newly uploaded images.', 'admin-tweak-suite' ); ?></p>

			<?php foreach ( $image_sizes as $size_key => $size_data ) : ?>
				<?php
				$enabled_val = (int) get_option( $size_data['enable'], 0 );
				$crop_val    = (int) get_option( $size_data['crop'], 0 );
				?>
				<div style="display:flex;align-items:center;gap:16px;margin-bottom:12px;">
					<strong style="width:140px;"><?php echo esc_html( $size_data['label'] ); ?></strong>

					<label>
						<input type="checkbox"
							id="atweaks_enable_<?php echo esc_attr( $size_key ); ?>"
							name="<?php echo esc_attr( $size_data['enable'] ); ?>"
							value="1" <?php checked( $enabled_val, 1 ); ?> />
						<?php esc_html_e( 'Enable', 'admin-tweak-suite' ); ?>
					</label>

					<label>
						<input type="checkbox"
							id="atweaks_crop_<?php echo esc_attr( $size_key ); ?>"
							name="<?php echo esc_attr( $size_data['crop'] ); ?>"
							value="1" <?php checked( $crop_val, 1 ); ?> />
						<?php esc_html_e( 'Crop', 'admin-tweak-suite' ); ?>
					</label>

					<label>
						<?php esc_html_e( 'Width', 'admin-tweak-suite' ); ?>:
						<input type="number" min="1" max="4096"
							name="atweaks_image_size_<?php echo esc_attr( $size_key ); ?>_width"
							value="<?php echo esc_attr( $size_data['width'] ); ?>"
							class="small-text" <?php disabled( $enabled_val, 0 ); ?> /> px
					</label>

					<label>
						<?php esc_html_e( 'Height', 'admin-tweak-suite' ); ?>:
						<input type="number" min="1" max="4096"
							name="atweaks_image_size_<?php echo esc_attr( $size_key ); ?>_height"
							value="<?php echo esc_attr( $size_data['height'] ); ?>"
							class="small-text" <?php disabled( $enabled_val, 0 ); ?> /> px
					</label>
				</div>
			<?php endforeach; ?>

			<hr>

			<h3><?php esc_html_e( 'Shared Notes Settings', 'admin-tweak-suite' ); ?></h3>
			<?php $enable_shared_notes_val = (int) get_option( 'atweaks_enable_shared_notes', 0 ); ?>
			<label style="display:block;margin-bottom:12px;">
				<input type="checkbox" id="atweaks_enable_shared_notes" name="atweaks_enable_shared_notes"
					value="1" <?php checked( $enable_shared_notes_val, 1 ); ?> />
				<?php esc_html_e( 'Enable Shared Notes on Dashboard', 'admin-tweak-suite' ); ?>
			</label>

			<div style="margin-bottom:20px;">
				<?php
				global $wp_roles;
				foreach ( $roles as $role_key ) :
					$role_name = $wp_roles->roles[ $role_key ]['name'];
					$read_key  = 'atweaks_notes_read_' . $role_key;
					$write_key = 'atweaks_notes_write_' . $role_key;
					$read_val  = (int) get_option( $read_key, 0 );
					$write_val = (int) get_option( $write_key, 0 );
					?>
					<div style="display:flex;align-items:center;margin-bottom:8px;gap:16px;">
						<strong style="width:100px;"><?php echo esc_html( $role_name ); ?></strong>
						<label><input type="checkbox" name="<?php echo esc_attr( $read_key ); ?>" value="1" <?php checked( $read_val, 1 ); ?> /> <?php esc_html_e( 'Read', 'admin-tweak-suite' ); ?></label>
						<label><input type="checkbox" name="<?php echo esc_attr( $write_key ); ?>" value="1" <?php checked( $write_val, 1 ); ?> /> <?php esc_html_e( 'Write', 'admin-tweak-suite' ); ?></label>
					</div>
				<?php endforeach; ?>
			</div>

			<hr>

			<h3><?php esc_html_e( 'Admin Notification', 'admin-tweak-suite' ); ?></h3>
			<?php
			$widget_enabled_val = (int) get_option( 'atweaks_admin_notice_widget_enabled', 0 );
			$allow_editor_val   = (int) get_option( 'atweaks_admin_notice_allow_editor', 0 );
			?>
			<label style="display:block;margin-bottom:12px;">
				<input type="checkbox" name="atweaks_admin_notice_widget_enabled"
					value="1" <?php checked( $widget_enabled_val, 1 ); ?> />
				<?php esc_html_e( 'Enable Admin Notification', 'admin-tweak-suite' ); ?>
			</label>

			<label style="display:block;margin-bottom:12px;">
				<input type="checkbox" name="atweaks_admin_notice_allow_editor"
					value="1" <?php checked( $allow_editor_val, 1 ); ?> />
				<?php esc_html_e( 'Allow Editors to manage the Admin Notification', 'admin-tweak-suite' ); ?>
			</label>

			<?php submit_button( __( 'Save Settings', 'admin-tweak-suite' ) ); ?>
		</form>
		<?php
	}

	/**
	 * Retrieves the available user roles.
	 *
	 * @return array
	 */
	private function get_available_roles(): array {
		global $wp_roles;
		$roles = array_keys( $wp_roles->roles ?? array() );
		return array_filter( $roles, static fn( $r ) => 'administrator' !== $r ); // Yoda.
	}

	/**
	 * Retrieves the custom image sizes config.
	 *
	 * @return array
	 */
	private function get_image_sizes(): array {
		return array(
			'small'  => array(
				'label'  => __( 'Custom Small Size', 'admin-tweak-suite' ),
				'enable' => 'atweaks_enable_custom_small',
				'crop'   => 'atweaks_crop_custom_small',
				'width'  => (int) get_option( 'atweaks_image_size_small_width', 64 ),
				'height' => (int) get_option( 'atweaks_image_size_small_height', 64 ),
			),
			'medium' => array(
				'label'  => __( 'Custom Medium Size', 'admin-tweak-suite' ),
				'enable' => 'atweaks_enable_custom_medium',
				'crop'   => 'atweaks_crop_custom_medium',
				'width'  => (int) get_option( 'atweaks_image_size_medium_width', 128 ),
				'height' => (int) get_option( 'atweaks_image_size_medium_height', 128 ),
			),
			'large'  => array(
				'label'  => __( 'Custom Large Size', 'admin-tweak-suite' ),
				'enable' => 'atweaks_enable_custom_large',
				'crop'   => 'atweaks_crop_custom_large',
				'width'  => (int) get_option( 'atweaks_image_size_large_width', 512 ),
				'height' => (int) get_option( 'atweaks_image_size_large_height', 512 ),
			),
		);
	}
}