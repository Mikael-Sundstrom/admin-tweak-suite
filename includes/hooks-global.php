<?php
/**
 * Hooks that should always be loaded (both admin and frontend).
 *
 * @file includes/hooks-global.php
 * @package Admin_Tweak_Suite
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register custom image sizes if enabled.
 */
function atweaks_register_custom_image_sizes() {
	$on_small  = (int) get_option( 'atweaks_enable_custom_small', 0 );
	$on_medium = (int) get_option( 'atweaks_enable_custom_medium', 0 );
	$on_large  = (int) get_option( 'atweaks_enable_custom_large', 0 );

	// Tidigt avbrott om inget är aktiverat.
	if ( ! $on_small && ! $on_medium && ! $on_large ) {
		return;
	}

	if ( $on_small ) {
		$w = max( 1, (int) get_option( 'atweaks_image_size_small_width', 64 ) );
		$h = max( 1, (int) get_option( 'atweaks_image_size_small_height', 64 ) );
		$c = (bool) get_option( 'atweaks_crop_custom_small', 0 );
		add_image_size( 'atweaks_custom_small', $w, $h, $c );
	}

	if ( $on_medium ) {
		$w = max( 1, (int) get_option( 'atweaks_image_size_medium_width', 128 ) );
		$h = max( 1, (int) get_option( 'atweaks_image_size_medium_height', 128 ) );
		$c = (bool) get_option( 'atweaks_crop_custom_medium', 0 );
		add_image_size( 'atweaks_custom_medium', $w, $h, $c );
	}

	if ( $on_large ) {
		$w = max( 1, (int) get_option( 'atweaks_image_size_large_width', 512 ) );
		$h = max( 1, (int) get_option( 'atweaks_image_size_large_height', 512 ) );
		$c = (bool) get_option( 'atweaks_crop_custom_large', 0 );
		add_image_size( 'atweaks_custom_large', $w, $h, $c );
	}
}
add_action( 'after_setup_theme', 'atweaks_register_custom_image_sizes', 100 );

/**
 * Visa endast aktiverade storlekar i editorns rullista.
 */
add_filter(
	'image_size_names_choose',
	function ( $sizes ) {
		if ( (int) get_option( 'atweaks_enable_custom_small', 0 ) ) {
			$sizes['atweaks_custom_small'] = __( 'Custom Small Size', 'admin-tweak-suite' );
		}
		if ( (int) get_option( 'atweaks_enable_custom_medium', 0 ) ) {
			$sizes['atweaks_custom_medium'] = __( 'Custom Medium Size', 'admin-tweak-suite' );
		}
		if ( (int) get_option( 'atweaks_enable_custom_large', 0 ) ) {
			$sizes['atweaks_custom_large'] = __( 'Custom Large Size', 'admin-tweak-suite' );
		}
		return $sizes;
	}
);
