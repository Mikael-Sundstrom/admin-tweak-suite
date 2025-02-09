<?php if (!defined('ABSPATH')) exit;

/**
 * includes/admin-hooks.php
 * Handles hooks and logic specific to the WordPress admin panel.
 */

if (is_admin()) {
	/**
	 * Block embeds in Gutenberg if the setting is enabled.
	 * Remove the embed blocks from the allowed block types.
	 * Unregister the embed blocks in the editor.
	 */
	if (get_option(ATWEAKS_DB_PREFIX . '_disable_embeds', false)) {
		
		// PHP: Remove the embed blocks from the allowed block types
		add_filter('allowed_block_types_all', function ($allowed_blocks, $editor_context) {
			if (!is_array($allowed_blocks)) {
				$registered_blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();
				$allowed_blocks = array_keys($registered_blocks);
			}
			return array_filter($allowed_blocks, function ($block) {
				return strpos($block, 'core/embed') === false;
			});
		}, 10, 2);

		// PHP: Unregister the embed blocks in the editor
		add_action('enqueue_block_editor_assets', function () {
			// Register a new script that will be empty, but can be used to add inline scripts
			wp_register_script('remove-embed-blocks-inline', '', [], '1.0.0', true);
		
			// Add the inline script to unregister the embed blocks
			wp_add_inline_script(
				'remove-embed-blocks-inline',
				"wp.domReady(() => {
					wp.blocks.unregisterBlockType('core/embed');
					wp.blocks.unregisterBlockType('core/embed/youtube');
					wp.blocks.unregisterBlockType('core/embed/twitter');
					console.log('Embed blocks unregistered');
				});"
			);
		
			// Enqueue the script
			wp_enqueue_script('remove-embed-blocks-inline');
		});
	}
	
	/**
	 * Control if any custom style is set.
	 * Hide the collapse menu button if the setting is enabled.
	 * Set the height of the menu separator if the setting is enabled.
	 */
	if (!empty(get_option(ATWEAKS_DB_PREFIX . '_custom_admin_css', '')) || 
	get_option(ATWEAKS_DB_PREFIX . '_menu_hide_collapse_menu', false) || 
	get_option(ATWEAKS_DB_PREFIX . '_menu_separator_height', false)) {

		// PHP: Add custom CSS to the admin panel
		add_action('admin_head', function () {
			// Begin with an empty string
			$css = '';

			// Get the custom CSS from the database
			$custom_css = get_option(ATWEAKS_DB_PREFIX . '_custom_admin_css', '');
			if (!empty($custom_css)) {
				$css .= wp_kses_post($custom_css);
			}
			// Add CSS to hide the collapse menu button if the setting is enabled
			if (get_option(ATWEAKS_DB_PREFIX . '_menu_hide_collapse_menu', false)) {
				$css .= '#collapse-menu { display: none !important; }';
			}

			// Add CSS for the menu separator height
			$separator_height = get_option(ATWEAKS_DB_PREFIX . '_menu_separator_height', 15); // Standardhöjd
			if ($separator_height) {
				$css .= '.wp-menu-separator{height:' . intval($separator_height) . 'px !important;}';
			}

			// If there is any CSS, add it to the admin panel
			if (!empty($css)) {
				echo '<style>' . esc_attr($css) . '</style>';
			}
		});
	}
}
