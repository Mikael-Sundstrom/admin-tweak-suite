<?php
/**
 * Uninstall script for Admin Tweak Suite.
 *
 * @file uninstall.php
 * @package Admin_Tweak_Suite
 */

defined( 'ABSPATH' ) || exit;
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

if ( ! defined( 'ATWEAKS_DB_PREFIX' ) ) {
	define( 'ATWEAKS_DB_PREFIX', 'atweaks' );
}

global $wpdb;
$prefix = ATWEAKS_DB_PREFIX . '_';

/**
 * 1) Clear all options that start with 'atweaks_'
 *    (both autoloaded and non-autoloaded).
 */
$like_opt = $wpdb->esc_like( $prefix ) . '%';
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$opt_names = $wpdb->get_col(
	$wpdb->prepare(
		"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
		$like_opt
	)
);
foreach ( (array) $opt_names as $opt ) {
	delete_option( $opt );
}

/**
 * 2) Clear transients (site-specific).
 */
$like_trans   = $wpdb->esc_like( '_transient_' . $prefix ) . '%';
$like_timeout = $wpdb->esc_like( '_transient_timeout_' . $prefix ) . '%';
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$trans_names = $wpdb->get_col(
	$wpdb->prepare(
		"SELECT option_name FROM {$wpdb->options}
		 WHERE option_name LIKE %s OR option_name LIKE %s",
		$like_trans,
		$like_timeout
	)
);
foreach ( (array) $trans_names as $name ) {
	$transient = preg_replace( '/^_transient_timeout_|^_transient_/', '', $name );
	if ( null !== $transient && '' !== $transient ) {
		delete_transient( $transient );
	}
}

/**
 * 3) Multisite: Clear site-options and site-transients.
 */
if ( is_multisite() ) {
	// Site options with prefix.
	$like_site_opt = $wpdb->esc_like( $prefix ) . '%';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$site_opt_names = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT meta_key FROM {$wpdb->sitemeta} WHERE meta_key LIKE %s",
			$like_site_opt
		)
	);
	foreach ( (array) $site_opt_names as $key ) {
		delete_site_option( $key );
	}

	// Site transients.
	$like_site_trans   = $wpdb->esc_like( '_site_transient_' . $prefix ) . '%';
	$like_site_timeout = $wpdb->esc_like( '_site_transient_timeout_' . $prefix ) . '%';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$site_trans_names = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT meta_key FROM {$wpdb->sitemeta}
			 WHERE meta_key LIKE %s OR meta_key LIKE %s",
			$like_site_trans,
			$like_site_timeout
		)
	);
	foreach ( (array) $site_trans_names as $key ) {
		$transient = preg_replace( '/^_site_transient_timeout_|^_site_transient_/', '', $key );
		if ( null !== $transient && '' !== $transient ) {
			delete_site_transient( $transient );
		}
	}
}
