<?php if (!defined('ABSPATH')) exit;

/**
 * includes/form-handlers/menu-handler.php
 * Handles forms and actions for the Admin Menu tab.
 */

add_action('admin_post_atweaks_script_handler', 'atweaks_scripts_handler');

function atweaks_scripts_handler() {

    // Control and verify the form submission
    if (!isset($_POST['clear_script']) && !isset($_POST['disable_emojis']) && !isset($_POST['disable_embeds']) && !isset($_POST['custom_script'])) {
        atweaks_redirect_with_message('scripts', 'error', esc_html__('No action provided.', 'admin-tweak-suite'));
    }

    // Control user capabilities
    if (!current_user_can('manage_options')) {
        atweaks_redirect_with_message('scripts', 'error', esc_html__('Access Denied: You do not have permission to perform this action.', 'admin-tweak-suite'));
    }

    // Control and verify the nonce
    if (!isset($_POST['atweaks_script_handler_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['atweaks_script_handler_nonce'])), 'atweaks_script_handler_action')) {
        atweaks_redirect_with_message('scripts', 'error', esc_html__('Security check failed. Please try again.', 'admin-tweak-suite'));
    }

    // Define the option names
    $option_script = ATWEAKS_DB_PREFIX . '_custom_script';
    $option_embeds = ATWEAKS_DB_PREFIX . '_disable_embeds';
    $option_emojis = ATWEAKS_DB_PREFIX . '_disable_emojis';

    // Handle setting for clearing all script settings
    if (isset($_POST['clear_script'])) {
        $options_to_clear = [$option_script, $option_embeds, $option_emojis];
        $found = false;

        foreach ($options_to_clear as $option) {
            if (get_option($option) !== false) {
                delete_option($option);
                $found = true;
            }
        }

        if (!$found) {
            atweaks_redirect_with_message('scripts', 'info', esc_html__('No script settings found to clear.', 'admin-tweak-suite'));
        }

        atweaks_redirect_with_message('scripts', 'success', esc_html__('All script settings have been reset.', 'admin-tweak-suite'));
    }

    // Handle setting for disabling emojis
    if (isset($_POST['disable_emojis'])) {
        update_option($option_emojis, 1);
    } else {
        delete_option($option_emojis);
    }

    // Handle setting for disabling embeds
    if (isset($_POST['disable_embeds'])) {
        update_option($option_embeds, 1);
    } else {
        delete_option($option_embeds);
    }

    // Handle custom script setting if provided
    $custom_script = isset($_POST['custom_script']) ? sanitize_textarea_field(wp_unslash($_POST['custom_script'])) : '';

    if ($custom_script !== '') {
        update_option($option_script, $custom_script);
        atweaks_redirect_with_message('scripts', 'success', esc_html__('Custom script has been successfully saved.', 'admin-tweak-suite'));
    } else {
        if (get_option($option_script) === false) {
            atweaks_redirect_with_message('scripts', 'info', esc_html__('No custom script found to clear.', 'admin-tweak-suite'));
        }
        delete_option($option_script);
        atweaks_redirect_with_message('scripts', 'success', esc_html__('Custom script has been successfully cleared.', 'admin-tweak-suite'));
    }
}
