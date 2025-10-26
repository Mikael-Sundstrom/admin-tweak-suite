<?php
/**
 * Hooks and logic specific to the WordPress admin panel.
 *
 * @file includes/hooks-admin.php
 * @package Admin_Tweak_Suite
 */

defined( 'ABSPATH' ) || exit;

/**
 * Disable core/embed block family in the editor if setting is enabled.
 */
add_action(
	'init',
	function () {
		if ( ! get_option( ATWEAKS_DB_PREFIX . '_disable_embeds', false ) ) {
			return;
		}

		// Remove the embed blocks from the allowed list.
		add_filter(
			'allowed_block_types_all',
			function ( $allowed_blocks ) {
				if ( ! is_array( $allowed_blocks ) ) {
					$registered_blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();
					$allowed_blocks    = array_keys( $registered_blocks );
				}

				return array_filter(
					$allowed_blocks,
					static function ( $block ) {
						return false === strpos( $block, 'core/embed' );
					}
				);
			},
			10,
			2
		);

		// Unregister embed blocks in the editor via inline script.
		add_action(
			'enqueue_block_editor_assets',
			function () {
				wp_register_script( 'atweaks-remove-embed-blocks', '', array(), ATWEAKS_PLUGIN_VERSION ?? '1.0.0', true );

				wp_add_inline_script(
					'atweaks-remove-embed-blocks',
					"wp.domReady(() => {
						const unregister = ( name ) => { try { wp.blocks.unregisterBlockType(name); } catch(e) {} };
						unregister('core/embed');
						unregister('core/embed/youtube');
						unregister('core/embed/twitter');
					});"
				);

				wp_enqueue_script( 'atweaks-remove-embed-blocks' );
			}
		);
	}
);

/**
 * Output inline CSS in admin if any admin CSS/customizations are set.
 * (Option checks happen inside the hook — no top-level logic.)
 */
add_action(
	'admin_head',
	function () {
		$has_custom_css      = ( '' !== (string) get_option( ATWEAKS_DB_PREFIX . '_custom_admin_css', '' ) );
		$hide_collapse_menu  = (bool) get_option( ATWEAKS_DB_PREFIX . '_menu_hide_collapse_menu', false );
		$separator_height_px = (int) get_option( ATWEAKS_DB_PREFIX . '_menu_separator_height', 15 );

		if ( ! $has_custom_css && ! $hide_collapse_menu && $separator_height_px <= 0 ) {
			return;
		}

		$css = get_transient( 'atweaks_cached_admin_css' );

		if ( false === $css ) {
			$css = '';

			if ( $has_custom_css ) {
				$css .= (string) get_option( ATWEAKS_DB_PREFIX . '_custom_admin_css', '' );
			}

			if ( $hide_collapse_menu ) {
				$css .= '#collapse-menu{display:none!important;}';
			}

			if ( $separator_height_px > 0 ) {
				$css .= '.wp-menu-separator{height:' . $separator_height_px . 'px!important;}';
			}

			// Låter transients vara utan TTL — du rensar dem vid uppdatering.
			set_transient( 'atweaks_cached_admin_css', $css );
		}

		if ( '' !== $css ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS behöver inte HTML-escapas.
			echo '<style id="atweaks-admin-inline-css">' . $css . '</style>';
		}
	}
);

/**
 * Dashboard widgets setup (Shared Notes + Admin Notification editor UI).
 */
add_action(
	'wp_dashboard_setup',
	function () {
		// Shared Notes: show widget if feature enabled and user has read access.
		if ( 1 === (int) get_option( 'atweaks_enable_shared_notes', 0 ) ) {
			$show_notes = current_user_can( 'manage_options' );
			if ( ! $show_notes ) {
				$current_user = wp_get_current_user();
				foreach ( (array) $current_user->roles as $role ) {
					if ( 1 === (int) get_option( 'atweaks_notes_read_' . $role, 0 ) ) {
						$show_notes = true;
						break;
					}
				}
			}
			if ( $show_notes ) {
				wp_add_dashboard_widget(
					'atweaks_dashboard_notes',
					__( 'Shared Notes', 'admin-tweak-suite' ),
					'atweaks_render_dashboard_notes_widget'
				);
			}
		}

		// Admin Notification editor widget: show only for permitted users.
		if ( 1 === (int) get_option( 'atweaks_admin_notice_widget_enabled', 0 ) ) {
			$allow_editors    = (int) get_option( 'atweaks_admin_notice_allow_editor', 0 );
			$has_write_access = current_user_can( 'manage_options' ) || ( 1 === $allow_editors && current_user_can( 'edit_others_posts' ) );

			if ( $has_write_access ) {
				wp_add_dashboard_widget(
					'atweaks_dashboard_notice',
					__( 'Admin Notification', 'admin-tweak-suite' ),
					'atweaks_render_admin_notice_widget'
				);
			}
		}
	}
);

/**
 * Global admin notice renderer (reads visibility & scope).
 * The actual banner is controlled by atweaks_admin_notice_visible.
 */
add_action(
	'admin_notices',
	function () {
		if ( 1 !== (int) get_option( 'atweaks_admin_notice_visible', 0 ) ) {
			return;
		}

		// Access control: admin always, otherwise role-specific access.
		$has_access = current_user_can( 'manage_options' );
		if ( ! $has_access ) {
			$current_user = wp_get_current_user();
			foreach ( (array) $current_user->roles as $role ) {
				if ( 1 === (int) get_option( 'atweaks_admin_notice_access_' . $role, 0 ) ) {
					$has_access = true;
					break;
				}
			}
		}

		if ( ! $has_access ) {
			return;
		}

		// Scope filtering.
		$scope  = (string) get_option( 'atweaks_admin_notice_scope', 'global' );
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		$is_dashboard = $screen && 'dashboard' === $screen->base;
		$is_pages     = $screen && isset( $screen->post_type ) && 'page' === $screen->post_type;
		$is_posts     = $screen && isset( $screen->post_type ) && 'post' === $screen->post_type;
		$is_media     = $screen && 'upload' === $screen->base;
		$is_comments  = $screen && 'edit-comments' === $screen->base;

		switch ( $scope ) {
			case 'dashboard':
				if ( ! $is_dashboard ) {
					return;
				}
				break;
			case 'pages':
				if ( ! $is_pages ) {
					return;
				}
				break;
			case 'posts':
				if ( ! $is_posts ) {
					return;
				}
				break;
			case 'media':
				if ( ! $is_media ) {
					return;
				}
				break;
			case 'comments':
				if ( ! $is_comments ) {
					return;
				}
				break;
			case 'global':
			default:
				// Show everywhere in admin.
				break;
		}

		// Content.
		$notice_title   = (string) get_option( 'atweaks_admin_notice_title', '' );
		$notice_content = (string) get_option( 'atweaks_admin_notice_content', '' );
		$notice_type    = (string) get_option( 'atweaks_admin_notice_type', 'info' );

		$notice_class = 'notice notice-alt notice-' . sanitize_html_class( $notice_type ) . ' is-dismissible atweaks-hidden';

		echo '<div id="atweaks-admin-notice" class="' . esc_attr( $notice_class ) . '">';
		if ( '' !== $notice_title ) {
			echo '<h2 style="margin-bottom:0;">' . esc_html( $notice_title ) . '</h2><br>';
		}
		echo '<p>' . nl2br( esc_html( $notice_content ) ) . '</p>';
		echo '</div>';
	}
);

/**
 * Add custom and standard image sizes with pixel labels to Media modal.
 *
 * @param array $sizes Existing size labels.
 * @return array Modified size labels.
 */
function atweaks_add_sizes_with_dimensions( $sizes ) {
	// Standard sizes with px.
	$standard_sizes = array(
		'thumbnail' => esc_html__( 'Thumbnail', 'admin-tweak-suite' ),
		'medium'    => esc_html__( 'Medium', 'admin-tweak-suite' ),
		'large'     => esc_html__( 'Large', 'admin-tweak-suite' ),
		'full'      => esc_html__( 'Full size', 'admin-tweak-suite' ),
	);

	foreach ( $standard_sizes as $key => $label ) {
		if ( 'full' === $key ) {
			$sizes[ $key ] = sprintf( '%s (%s)', $label, esc_html__( 'Original', 'admin-tweak-suite' ) );
		} else {
			$width         = absint( get_option( "{$key}_size_w" ) );
			$height        = absint( get_option( "{$key}_size_h" ) );
			$sizes[ $key ] = sprintf( '%s (%dx%d px)', $label, $width, $height );
		}
	}

	// Custom sizes (prefixed).
	$custom_sizes = array(
		'atweaks_custom_small'  => array(
			'enabled' => (int) get_option( 'atweaks_enable_custom_small', 0 ),
			'width'   => absint( get_option( 'atweaks_image_size_small_width', 64 ) ),
			'height'  => absint( get_option( 'atweaks_image_size_small_height', 64 ) ),
			'label'   => esc_html__( 'Custom Small', 'admin-tweak-suite' ),
		),
		'atweaks_custom_medium' => array(
			'enabled' => (int) get_option( 'atweaks_enable_custom_medium', 0 ),
			'width'   => absint( get_option( 'atweaks_image_size_medium_width', 128 ) ),
			'height'  => absint( get_option( 'atweaks_image_size_medium_height', 128 ) ),
			'label'   => esc_html__( 'Custom Medium', 'admin-tweak-suite' ),
		),
		'atweaks_custom_large'  => array(
			'enabled' => (int) get_option( 'atweaks_enable_custom_large', 0 ),
			'width'   => absint( get_option( 'atweaks_image_size_large_width', 512 ) ),
			'height'  => absint( get_option( 'atweaks_image_size_large_height', 512 ) ),
			'label'   => esc_html__( 'Custom Large', 'admin-tweak-suite' ),
		),
	);

	foreach ( $custom_sizes as $key => $data ) {
		if ( 1 === $data['enabled'] ) {
			$sizes[ $key ] = sprintf( '%s (%dx%d px)', $data['label'], $data['width'], $data['height'] );
		}
	}

	return $sizes;
}
add_filter( 'image_size_names_choose', 'atweaks_add_sizes_with_dimensions' );

/**
 * Enqueue admin JS.
 */
add_action(
	'admin_enqueue_scripts',
	function () {
		wp_enqueue_script(
			'admin-tweak-suite-js',
			ATWEAKS_PLUGIN_URL . 'assets/js/admin-tweak-suite.js',
			array(),
			defined( 'ATWEAKS_PLUGIN_VERSION' ) ? ATWEAKS_PLUGIN_VERSION : '1.0.0',
			true
		);
	}
);

/**
 * Small admin CSS helper class.
 */
add_action(
	'admin_head',
	static function () {
		echo '<style>.atweaks-hidden{display:none!important;}</style>';
	}
);
