<?php
/**
 * Renders the "Extra Tweaks" tab content for the admin panel.
 *
 * @file admin/extra-tweaks/extra-tweaks.php
 * @package Admin_Tweak_Suite
 */

defined( 'ABSPATH' ) || exit;

// Roles (excluding administrator).
global $wp_roles;
$roles_obj       = ( isset( $wp_roles ) && is_object( $wp_roles ) ) ? $wp_roles : null;
$role_keys       = $roles_obj ? array_keys( $roles_obj->roles ) : array();
$available_roles = array_filter(
	$role_keys,
	static function ( $role ) {
		return 'administrator' !== $role;
	}
);

// Custom sizes (prefixed).
$size_small_w  = (int) get_option( 'atweaks_image_size_small_width', 64 );
$size_small_h  = (int) get_option( 'atweaks_image_size_small_height', 64 );
$size_medium_w = (int) get_option( 'atweaks_image_size_medium_width', 128 );
$size_medium_h = (int) get_option( 'atweaks_image_size_medium_height', 128 );
$size_large_w  = (int) get_option( 'atweaks_image_size_large_width', 512 );
$size_large_h  = (int) get_option( 'atweaks_image_size_large_height', 512 );

// Configuration for three sizes (only prefixed keys).
$image_sizes = array(
	'small'  => array(
		'label'  => __( 'Custom Small Size', 'admin-tweak-suite' ),
		'enable' => 'atweaks_enable_custom_small',
		'crop'   => 'atweaks_crop_custom_small',
		'width'  => $size_small_w,
		'height' => $size_small_h,
	),
	'medium' => array(
		'label'  => __( 'Custom Medium Size', 'admin-tweak-suite' ),
		'enable' => 'atweaks_enable_custom_medium',
		'crop'   => 'atweaks_crop_custom_medium',
		'width'  => $size_medium_w,
		'height' => $size_medium_h,
	),
	'large'  => array(
		'label'  => __( 'Custom Large Size', 'admin-tweak-suite' ),
		'enable' => 'atweaks_enable_custom_large',
		'crop'   => 'atweaks_crop_custom_large',
		'width'  => $size_large_w,
		'height' => $size_large_h,
	),
);
?>

<!-------------------------------------------------------------------------------
START OF EXTRA-TWEAKS-TAB
------------------------------------------------------------------------------->

<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<input type="hidden" name="action" value="atweaks_manage_extra_tweaks">
	<?php wp_nonce_field( 'atweaks_save_extra_sizes', 'atweaks_extra_sizes_nonce' ); ?>

	<h3><?php esc_html_e( 'Extra Image Sizes', 'admin-tweak-suite' ); ?></h3>
	<p>
		<?php esc_html_e( 'These custom image sizes will be generated for newly uploaded images. The selection of image sizes on the frontend depends on your active theme and block settings.', 'admin-tweak-suite' ); ?>
	</p>
	<p>
		<?php esc_html_e( 'Here you can enable and configure additional custom image sizes. The default WordPress sizes (Thumbnail, Medium, Large) can be adjusted on the ', 'admin-tweak-suite' ); ?>
		<a href="<?php echo esc_url( admin_url( 'options-media.php' ) ); ?>">
			<?php esc_html_e( 'Media Settings page.', 'admin-tweak-suite' ); ?>
		</a>
	</p>

	<?php foreach ( $image_sizes as $size_key => $size_data ) : ?>
		<?php
		$enabled_val = (int) get_option( $size_data['enable'], 0 );
		$crop_val    = (int) get_option( $size_data['crop'], 0 );
		?>
		<div style="display: flex; align-items: center; gap: 16px; margin-bottom: 12px;">
			<strong style="width: 140px;"><?php echo esc_html( $size_data['label'] ); ?></strong>

			<label>
				<input
					type="checkbox"
					id="atweaks_enable_<?php echo esc_attr( $size_key ); ?>"
					name="<?php echo esc_attr( $size_data['enable'] ); ?>"
					value="1" <?php checked( $enabled_val, 1 ); ?> />
				<?php esc_html_e( 'Enable', 'admin-tweak-suite' ); ?>
			</label>

			<label>
				<input
					type="checkbox"
					id="atweaks_crop_<?php echo esc_attr( $size_key ); ?>"
					name="<?php echo esc_attr( $size_data['crop'] ); ?>"
					value="1" <?php checked( $crop_val, 1 ); ?> />
				<?php esc_html_e( 'Crop', 'admin-tweak-suite' ); ?>
			</label>

			<label>
				<?php esc_html_e( 'Width', 'admin-tweak-suite' ); ?>:
				<input
					type="number"
					min="1"
					max="4096"
					name="atweaks_image_size_<?php echo esc_attr( $size_key ); ?>_width"
					value="<?php echo esc_attr( $size_data['width'] ); ?>"
					class="small-text"
					<?php disabled( $enabled_val, 0 ); ?> /> px
			</label>

			<label>
				<?php esc_html_e( 'Height', 'admin-tweak-suite' ); ?>:
				<input
					type="number"
					min="1"
					max="4096"
					name="atweaks_image_size_<?php echo esc_attr( $size_key ); ?>_height"
					value="<?php echo esc_attr( $size_data['height'] ); ?>"
					class="small-text"
					<?php disabled( $enabled_val, 0 ); ?> /> px
			</label>
		</div>
	<?php endforeach; ?>

	<hr>

	<h3><?php esc_html_e( 'Shared Notes Settings', 'admin-tweak-suite' ); ?></h3>
	<p><?php esc_html_e( 'Enable shared notes and configure which user roles can read or write.', 'admin-tweak-suite' ); ?></p>

	<?php $enable_shared_notes_val = (int) get_option( 'atweaks_enable_shared_notes', 0 ); ?>

	<label style="display: block; margin-bottom: 12px;">
		<input
			type="checkbox"
			id="atweaks_enable_shared_notes"
			name="atweaks_enable_shared_notes"
			value="1" <?php checked( $enable_shared_notes_val, 1 ); ?> />
		<?php esc_html_e( 'Enable Shared Notes on Dashboard', 'admin-tweak-suite' ); ?>
	</label>

	<div style="margin-bottom: 20px;">
		<?php foreach ( $available_roles as $role_key ) : ?>
			<?php
			$role_name = $roles_obj ? $roles_obj->roles[ $role_key ]['name'] : $role_key;
			$read_key  = 'atweaks_notes_read_' . $role_key;
			$write_key = 'atweaks_notes_write_' . $role_key;
			$read_val  = (int) get_option( $read_key, 0 );
			$write_val = (int) get_option( $write_key, 0 );
			?>
			<div style="display: flex; align-items: center; margin-bottom: 8px; gap: 16px;">
				<strong style="width: 100px;"><?php echo esc_html( $role_name ); ?></strong>
				<label>
					<input
						type="checkbox"
						class="shared-notes-permission"
						name="<?php echo esc_attr( $read_key ); ?>"
						value="1" <?php checked( $read_val, 1 ); ?> />
					<?php esc_html_e( 'Read', 'admin-tweak-suite' ); ?>
				</label>
				<label>
					<input
						type="checkbox"
						class="shared-notes-permission"
						name="<?php echo esc_attr( $write_key ); ?>"
						value="1" <?php checked( $write_val, 1 ); ?> />
					<?php esc_html_e( 'Write', 'admin-tweak-suite' ); ?>
				</label>
			</div>
		<?php endforeach; ?>
	</div>

	<hr>

	<h3><?php esc_html_e( 'Admin Notification', 'admin-tweak-suite' ); ?></h3>
	<p><?php esc_html_e( 'Enable a global admin notification that can be managed directly from the dashboard.', 'admin-tweak-suite' ); ?></p>

	<?php
	$widget_enabled_val = (int) get_option( 'atweaks_admin_notice_widget_enabled', 0 );
	$allow_editor_val   = (int) get_option( 'atweaks_admin_notice_allow_editor', 0 );
	?>

	<label style="display: block; margin-bottom: 12px;">
		<input
			type="checkbox"
			name="atweaks_admin_notice_widget_enabled"
			value="1" <?php checked( $widget_enabled_val, 1 ); ?> />
		<?php esc_html_e( 'Enable Admin Notification (Dashboard widget)', 'admin-tweak-suite' ); ?>
	</label>

	<label style="display: block; margin-bottom: 12px;">
		<input
			type="checkbox"
			name="atweaks_admin_notice_allow_editor"
			value="1" <?php checked( $allow_editor_val, 1 ); ?> />
		<?php esc_html_e( 'Allow Editors to manage the Admin Notification', 'admin-tweak-suite' ); ?>
	</label>

	<?php submit_button( __( 'Save Settings', 'admin-tweak-suite' ) ); ?>
</form>

<!-------------------------------------------------------------------------------
END OF EXTRA-TWEAKS-TAB
------------------------------------------------------------------------------->
