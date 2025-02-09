<?php if (!defined('ABSPATH')) exit;

/**
 * includes/handlers/css-handler.php
 * Handles forms and actions for the "CSS" tab.
 */

/**
 * Hook to handle form submissions for frontend and admin CSS.
 */
add_action('admin_post_manage_frontend_css', fn() => atweaks_manage_css('frontend'));
add_action('admin_post_manage_admin_css', fn() => atweaks_manage_css('admin'));

/**
 * Manages saving or clearing custom CSS for a specified type.
 *
 * @param string $type The type of CSS to manage. Possible values: 'frontend' or 'admin'.
 * @return void
 */
function atweaks_manage_css($type) {

    // Control user capabilities
    if (!current_user_can('manage_options')) {
        atweaks_redirect_with_message('css', 'error', esc_html__('Access Denied: You do not have permission to perform this action.', 'admin-tweak-suite'));
    }

    // Control security nonce
    $nonce_value = isset($_POST["save_{$type}_css_nonce"]) ? sanitize_text_field(wp_unslash($_POST["save_{$type}_css_nonce"])) : '';

	if (!wp_verify_nonce($nonce_value, "save_{$type}_css_action")) {
		atweaks_redirect_with_message('css', 'error', esc_html__('Security check failed. Please try again.', 'admin-tweak-suite'));
	}

    // Sanitize and validate the custom CSS data
    $action = isset($_POST['submit_action']) ? sanitize_text_field(wp_unslash($_POST['submit_action'])) : '';

    switch ($action) {
        case 'save':
			// Sanitize and validate the custom CSS data
			$css = '';

			if (isset($_POST['custom_css'])) {
				// Unslash first, then sanitize
				$css = sanitize_textarea_field(wp_unslash($_POST['custom_css']));
			}

			if (empty($css)) {
				// translators: %s represents the CSS type for which no data was received.
				$message = sprintf(esc_html__('%s CSS data was not provided.', 'admin-tweak-suite'), esc_html(ucfirst($type)));
				atweaks_redirect_with_message('css', 'info', $message);
			}

		
			// Update the CSS option
			update_option(ATWEAKS_DB_PREFIX . "_custom_{$type}_css", $css);
		
			// translators: %s represents the CSS type that was successfully saved.
			$message = sprintf(esc_html__('%s CSS has been successfully saved.', 'admin-tweak-suite'), esc_html(ucfirst($type)));
			atweaks_redirect_with_message('css', 'success', $message);
			break;

			case 'clear':
				$option_name = ATWEAKS_DB_PREFIX . "_custom_{$type}_css";
			
				// Control if the CSS option exists
				if (get_option($option_name) === false) {
					atweaks_redirect_with_message('css', 'info', esc_html__('No CSS found to clear.', 'admin-tweak-suite'));
				}
			
				// Delete the CSS option
				delete_option($option_name);
			
				// translators: %s represents the CSS type that has been successfully cleared.
				$clear_message = sprintf(esc_html__('%s CSS has been successfully cleared.', 'admin-tweak-suite'), esc_html(ucfirst($type)));
			
				atweaks_redirect_with_message('css', 'success', $clear_message);
				break;

        default:
            atweaks_redirect_with_message('css', 'error', esc_html__('Unknown form action. Please try again.', 'admin-tweak-suite'));
            break;
    }
}
