<?php
/**
 * Functions for handling file operations.
 *
 * @file includes/helpers/files.php
 * @package Admin_Tweak_Suite
 */

defined( 'ABSPATH' ) || exit;

/**
 * Safely includes a file or multiple files if they exist.
 *
 * @param string|array $file_paths The path(s) to the file(s).
 */
function atweaks_safe_include( $file_paths ) {
	if ( ! is_array( $file_paths ) ) {
		$file_paths = array( $file_paths );
	}

	foreach ( $file_paths as $file_path ) {
		$full_path = ATWEAKS_PLUGIN_PATH . $file_path;
		if ( file_exists( $full_path ) ) {
			require_once $full_path;
		}
	}
}
