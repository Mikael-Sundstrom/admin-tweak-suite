<?php if (!defined('ABSPATH')) exit;

/**
 * frontend-hooks.php
 * Handles frontend-specific hooks with conditional logic.
 */

// Execute only on the frontend (not in the admin panel)
if (!is_admin()) {
    // Hooks
    add_action('init', 'atweaks_disable_emojis'); // Disable emoji-related scripts and styles
    add_action('wp_head', 'atweaks_add_custom_css'); // Add custom CSS to the <head> section
    add_action('wp_footer', 'atweaks_add_custom_script'); // Add custom JavaScript to the <footer> section
    add_action('init', 'atweaks_disable_embeds'); // Disable oEmbeds functionality

    /**
     * Disables emoji-related scripts and styles on the frontend.
     *
     * Checks if the `atweaks_disable_emojis` option is enabled in the database. 
     * If true, removes all actions and filters responsible for loading emojis 
     * on both the frontend and admin panel.
     *
     * @return void
     */
    function atweaks_disable_emojis() {
        if (get_option('atweaks_disable_emojis', false)) {
            // Remove emoji detection script from the <head> section
            remove_action('wp_head', 'print_emoji_detection_script', 7);

            // Remove emoji-related styles from the frontend
            remove_action('wp_print_styles', 'print_emoji_styles');

            // Remove emoji detection script from the admin panel
            remove_action('admin_print_scripts', 'print_emoji_detection_script');

            // Remove emoji-related styles from the admin panel
            remove_action('admin_print_styles', 'print_emoji_styles');
        }
    }

    /**
	 * Disable oEmbed functionality and block iframe usage.
	 *
	 * Removes various oEmbed-related functionalities, filters, and scripts,
	 * enhancing performance and privacy by preventing unnecessary resource usage.
	 */
	function atweaks_disable_embeds() {
		if (get_option('atweaks_disable_embeds', false)) {

			// Remove oEmbed features from WordPress
			remove_action('wp_head', 'wp_oembed_add_discovery_links'); // Removes oEmbed discovery links
			remove_action('wp_head', 'wp_oembed_add_host_js'); // Removes oEmbed JavaScript from the header
			remove_action('rest_api_init', 'wp_oembed_register_route'); // Removes oEmbed routes from REST API
			add_filter('embed_oembed_discover', '__return_false'); // Disables oEmbed discovery
			remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10); // Removes oEmbed data parsing filter
			remove_action('wp_head', 'rest_output_link_wp_head', 10); // Removes REST API links for oEmbed

			// Remove oEmbed endpoints from REST API
			add_filter('rest_endpoints', function ($endpoints) {
				if (isset($endpoints['/oembed/1.0/embed'])) {
					unset($endpoints['/oembed/1.0/embed']); // Removes the oEmbed endpoint for embedding
				}
				if (isset($endpoints['/oembed/1.0/proxy'])) {
					unset($endpoints['/oembed/1.0/proxy']); // Removes the oEmbed proxy endpoint
				}
				return $endpoints;
			});

			/**
			 * Block rendering of oEmbed blocks and iframes.
			 *
			 * Prevents oEmbed blocks and inline iframes from being rendered
			 * in the block editor and frontend content.
			 */
			add_filter('render_block', function ($block_content, $block) {
				// Check if the block is an oEmbed block and block it
				if (
					isset($block['blockName']) && 
					is_string($block['blockName']) && 
					strpos($block['blockName'], 'core/embed') === 0
				) {
					return '<!-- oEmbeds and iframe embeds are disabled -->';
				}
				// Check if the block content contains an iframe and block it
				if (is_string($block_content) && strpos($block_content, '<iframe') !== false) {
					return '<!-- oEmbeds and iframe embeds are disabled -->';
				}
				return $block_content;
			}, 10, 2);

			/**
			 * Block inline iframes in post content.
			 *
			 * Filters the content to prevent rendering of any inline iframes
			 * by replacing them with a comment.
			 */
			add_filter('the_content', function ($content) {
				if (is_string($content) && strpos($content, '<iframe') !== false) {
					return preg_replace('/<iframe[^>]*?>.*?<\/iframe>/i', '<!-- Inline iframe blocked -->', $content);
				}
				return $content;
			}, 10);

			/**
			 * Remove the WordPress embed script.
			 *
			 * Prevents the `wp-embed.js` script from being enqueued,
			 * which is responsible for handling embeds on the frontend.
			 */
			add_action('wp_enqueue_scripts', function () {
				wp_dequeue_script('wp-embed');
			}, 20);
		}
	}

   /**
	 * Outputs custom CSS directly into the frontend.
	 *
	 * Fetches custom CSS saved in the database and echoes it within a `<style>` tag.
	 * This allows administrators to add inline CSS for the frontend without editing theme files.
	 *
	 * @return void
	 */
	function atweaks_add_custom_css() {
		// Retrieve custom CSS from the database
		$custom_css = get_option('atweaks_custom_frontend_css', '');

		// If custom CSS is not empty, output it within a <style> tag
		if (!empty($custom_css)) {
			echo '<style>' . wp_kses($custom_css, ['style']) . '</style>';
		}
	}

	/**
	 * Outputs custom JavaScript into the footer of the frontend.
	 *
	 * Fetches custom JavaScript saved in the database and echoes it within a `<script>` tag.
	 * This allows administrators to add inline scripts for the frontend without modifying theme files.
	 *
	 * @return void
	 */
	function atweaks_add_custom_script() {
		// Retrieve custom JavaScript from the database
		$custom_script = get_option('atweaks_custom_script', '');

		// If custom JavaScript is not empty, output it within a <script> tag
		if (!empty($custom_script)) {
			echo '<script>' . wp_kses_post($custom_script) . '</script>';
		}
	}
}
