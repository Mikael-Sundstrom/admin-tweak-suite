<?php
/**
 * Functions for handling database wp_options.
 *
 * @file includes/helpers/wp-options.php
 * @package Admin_Tweak_Suite
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get all options that start with a certain prefix.
 * Default: plugin prefix (ATWEAKS_DB_PREFIX . '_').
 *
 * @param string $prefix Prefix to search for (including trailing underscore).
 * @return array List of matching option names.
 */
function atweaks_get_option_names_by_prefix( $prefix = ATWEAKS_DB_PREFIX . '_' ) {
	global $wpdb;

	$cache_group  = 'atweaks_cache';
	$cache_key    = 'atweaks_options_list_' . md5( $prefix );
	$option_names = wp_cache_get( $cache_key, $cache_group );

	if ( false === $option_names ) { // Yoda.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$option_names = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
				$prefix . '%'
			)
		);

		wp_cache_set( $cache_key, $option_names, $cache_group, DAY_IN_SECONDS );
	}

	return is_array( $option_names ) ? $option_names : array();
}

/**
 * Delete all options that start with a certain prefix. Returns deleted names.
 * Default: plugin prefix.
 *
 * @param string $prefix Prefix to delete (including trailing underscore).
 * @return array List over deleted option names.
 */
function atweaks_delete_options_by_prefix( $prefix = ATWEAKS_DB_PREFIX . '_' ) {
	$option_names    = atweaks_get_option_names_by_prefix( $prefix );
	$deleted_options = array();

	if ( ! empty( $option_names ) ) {
		foreach ( $option_names as $option_name ) {
			if ( delete_option( $option_name ) ) {
				$deleted_options[] = $option_name;
			}
		}

		// Rensa cachen för listan.
		wp_cache_delete( 'atweaks_options_list_' . md5( $prefix ), 'atweaks_cache' );
	}

	return $deleted_options;
}

/**
 * Delete all transients that belong to Admin Tweak Suite.
 * Match _transient_atweaks_% and _transient_timeout_atweaks_%.
 *
 * @return void
 */
function atweaks_flush_transients() {
	global $wpdb;

	$base         = ATWEAKS_DB_PREFIX . '_'; // "atweaks_".
	$like_main    = $wpdb->esc_like( '_transient_' . $base ) . '%';
	$like_timeout = $wpdb->esc_like( '_transient_timeout_' . $base ) . '%';

	$cache_group = 'atweaks_cache';
	$cache_key   = 'atweaks_transient_keys_' . md5( $base );

	$transient_keys = wp_cache_get( $cache_key, $cache_group );

	if ( false === $transient_keys ) { // Yoda.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$transient_keys = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name
				 FROM {$wpdb->options}
				 WHERE option_name LIKE %s
				    OR option_name LIKE %s",
				$like_main,
				$like_timeout
			)
		);

		wp_cache_set( $cache_key, $transient_keys, $cache_group, HOUR_IN_SECONDS );
	}

	if ( ! empty( $transient_keys ) ) {
		foreach ( $transient_keys as $key ) {
			delete_option( $key );
		}
	}

	// Reassurance: also clear the cache key after deletion.
	wp_cache_delete( $cache_key, $cache_group );
}
