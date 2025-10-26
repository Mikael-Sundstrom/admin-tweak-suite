<?php
/**
 * Handles frontend-specific hooks with conditional logic.
 *
 * @file includes/hooks-frontend.php
 * @package Admin_Tweak_Suite
 */

defined( 'ABSPATH' ) || exit;

/**
 * Disable emoji-related scripts and styles on the frontend.
 */
function atweaks_disable_emojis() {
	if ( get_option( 'atweaks_disable_emojis', false ) ) {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
	}
}

/**
 * Disable oEmbed functionality and block iframes.
 */
function atweaks_disable_embeds() {
	if ( get_option( 'atweaks_disable_embeds', false ) ) {
		// Remove oEmbed features from WordPress.
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		remove_action( 'rest_api_init', 'wp_oembed_register_route' );
		add_filter( 'embed_oembed_discover', '__return_false' );
		remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
		remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );

		// Remove oEmbed endpoints from REST API.
		add_filter(
			'rest_endpoints',
			static function ( $endpoints ) {
				unset( $endpoints['/oembed/1.0/embed'], $endpoints['/oembed/1.0/proxy'] );
				return $endpoints;
			}
		);

		// Block rendering of oEmbed blocks and inline iframes.
		add_filter(
			'render_block',
			static function ( $block_content, $block ) {
				if ( isset( $block['blockName'] ) && is_string( $block['blockName'] ) && strpos( $block['blockName'], 'core/embed' ) === 0 ) {
					return '<!-- oEmbeds and iframe embeds are disabled -->';
				}
				if ( is_string( $block_content ) && strpos( $block_content, '<iframe' ) !== false ) {
					return '<!-- oEmbeds and iframe embeds are disabled -->';
				}
				return $block_content;
			},
			10,
			2
		);

		// Block inline iframes in post content.
		add_filter(
			'the_content',
			static function ( $content ) {
				if ( is_string( $content ) && strpos( $content, '<iframe' ) !== false ) {
					return preg_replace( '/<iframe[^>]*?>.*?<\/iframe>/is', '<!-- Inline iframe blocked -->', $content );
				}
				return $content;
			},
			10
		);

		// Remove the WordPress embed script.
		add_action(
			'wp_enqueue_scripts',
			static function () {
				wp_dequeue_script( 'wp-embed' );
			},
			20
		);
	}
}

/**
 * Disable jQuery Migrate on the frontend.
 */
function atweaks_disable_jquery_migrate() {
	if ( get_option( 'atweaks_disable_jquery_migrate', false ) ) {
		add_filter(
			'wp_default_scripts',
			static function ( $scripts ) {
				if ( isset( $scripts->registered['jquery'] ) ) {
					$jquery = $scripts->registered['jquery'];
					if ( $jquery->deps ) {
						$jquery->deps = array_diff( $jquery->deps, array( 'jquery-migrate' ) );
					}
				}
			}
		);
	}
}

/**
 * Disable XML-RPC functionality in WordPress.
 */
function atweaks_disable_xmlrpc() {
	if ( get_option( 'atweaks_disable_xmlrpc', false ) ) {
		add_filter( 'xmlrpc_enabled', '__return_false' );
		remove_action( 'wp_head', 'rsd_link' );

		// Remove pingback header.
		add_filter(
			'wp_headers',
			static function ( $headers ) {
				unset( $headers['X-Pingback'] );
				return $headers;
			}
		);

		// Block XML-RPC requests directly.
		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
			if ( strpos( $request_uri, 'xmlrpc.php' ) !== false ) {
				wp_die(
					esc_html__( 'XML-RPC services are disabled on this site.', 'admin-tweak-suite' ),
					esc_html__( 'XML-RPC Disabled', 'admin-tweak-suite' ),
					array( 'response' => 403 )
				);
			}
		}
	}
}

/**
 * Output custom CSS in <head>.
 */
function atweaks_add_custom_css() {
	$custom_css = get_transient( 'atweaks_cached_frontend_css' );
	if ( false === $custom_css ) {
		$custom_css = get_option( 'atweaks_custom_frontend_css', '' );
		set_transient( 'atweaks_cached_frontend_css', $custom_css ); // TTL hanteras via flush i dina save-handlers.
	}
	if ( '' !== $custom_css ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS är admin-kontrollerat.
		echo '<style>' . $custom_css . '</style>';
	}
}

/**
 * Output custom JS in footer.
 */
function atweaks_add_custom_script() {
	$custom_script = get_transient( 'atweaks_cached_frontend_script' );
	if ( false === $custom_script ) {
		$custom_script = get_option( 'atweaks_custom_script', '' );
		set_transient( 'atweaks_cached_frontend_script', $custom_script ); // TTL hanteras via flush i dina save-handlers.
	}
	if ( '' !== $custom_script ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JS är admin-kontrollerat.
		echo '<script>' . $custom_script . '</script>';
	}
}

/**
 * Hooks
 */
add_action( 'init', 'atweaks_disable_emojis' );
add_action( 'init', 'atweaks_disable_embeds' );
add_action( 'init', 'atweaks_disable_jquery_migrate', 20 );
add_action( 'init', 'atweaks_disable_xmlrpc', 10 );
add_action( 'wp_head', 'atweaks_add_custom_css' );
add_action( 'wp_footer', 'atweaks_add_custom_script' );
