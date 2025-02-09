<?php if (!defined('ABSPATH')) exit;

/**
 * includes/form-handlers/menu-handler.php
 * Handles forms and actions for the Admin Menu tab.
 */

add_action('admin_post_save_menu', 'atweaks_menu_handler');
add_action('admin_menu', 'atweaks_update_admin_menu', 999);

/**
 * Handles form submissions for the Admin Menu tab.
 * 
 * @return void
 */
function atweaks_menu_handler() {

    // Control and verify the form submission
    if (!isset($_POST['save_menu']) && !isset($_POST['add_separator']) && !isset($_POST['reset_menu'])) {
        atweaks_redirect_with_message('menu', 'error', esc_html__('No action provided.', 'admin-tweak-suite'));
    }

    // Control and verify the user's permissions
    if (!current_user_can('manage_options')) {
        atweaks_redirect_with_message('menu', 'error', esc_html__('Access Denied: You do not have permission to perform this action.', 'admin-tweak-suite'));
    }

    // Control and verify the nonce
    if (!isset($_POST['save_menu_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['save_menu_nonce'])), 'save_menu_action')) {
        atweaks_redirect_with_message('menu', 'error', esc_html__('Security check failed. Please try again.', 'admin-tweak-suite'));
    }

    // Handle the form actions
    if (isset($_POST['save_menu'])) {
		$menu_items = isset($_POST['menu_items']) ? sanitize_option('menu_items', wp_unslash($_POST['menu_items'])) : [];
		$menu_hide_collapse = isset($_POST[ATWEAKS_DB_PREFIX . '_menu_hide_collapse_menu']) ? 1 : 0;
		$menu_separator_height = isset($_POST[ATWEAKS_DB_PREFIX . '_menu_separator_height']) ? absint($_POST[ATWEAKS_DB_PREFIX . '_menu_separator_height']) : null;
	
        atweaks_save_menu_order($menu_items, $menu_hide_collapse, $menu_separator_height);

    } elseif (isset($_POST['add_separator'])) {
        atweaks_add_separator();

    } elseif (isset($_POST['reset_menu'])) {
        atweaks_reset_menu();

    } else {
        atweaks_redirect_with_message('menu', 'error', esc_html__('Unknown action. Please try again.', 'admin-tweak-suite'));
    }

    // If all succeeded, redirect back to the menu tab
    atweaks_redirect_with_message('menu', 'success', esc_html__('Menu updated successfully.', 'admin-tweak-suite'));
    exit;
}

/**
 * Saves the menu order based on the submitted form data.
 *
 * @param array $menu_items List of menu items.
 * @param int $hide_collapse_menu 1 if checkbox is checked, otherwise 0.
 * @param int|null $separator_height The separator height or null if not set.
 * 
 * @return void
 */
function atweaks_save_menu_order($menu_items, $hide_collapse_menu, $separator_height) {

    // Handle the menu items and sanitize the data
    if (is_array($menu_items)) {
        $menu_items = array_map(function ($item) {
            return array_map('sanitize_text_field', $item);
        }, $menu_items);

        foreach ($menu_items as $slug => $item) {
            $slug = sanitize_title($slug);
            $position = isset($item['position']) ? absint($item['position']) : 0;

            if ($position > 0) {
                update_option(ATWEAKS_DB_PREFIX . "_menu_position_{$slug}", $position);
            }
        }
    }

    // Save or delete the hide collapse menu option
	if ($hide_collapse_menu === 1) {
		update_option(ATWEAKS_DB_PREFIX . '_menu_hide_collapse_menu', 1);
	} else {
		delete_option(ATWEAKS_DB_PREFIX . '_menu_hide_collapse_menu');
	}

	// Save or delete the separator height option
	if (!is_null($separator_height)) {
		if ($separator_height === 5) {
			delete_option(ATWEAKS_DB_PREFIX . '_menu_separator_height');
		} else {
			update_option(ATWEAKS_DB_PREFIX . '_menu_separator_height', $separator_height);
		}
	}
}

/**
 * Adds a new separator to the menu.
 *
 * @return void
 */
function atweaks_add_separator() {
    global $wpdb;

    // Create a new separator slug and set its position
    $new_separator_slug = "separator_" . time();
    $new_position = 0;

    // Update the database with the new separator
    update_option(ATWEAKS_DB_PREFIX . "_menu_position_{$new_separator_slug}", $new_position);

	// Redirect back to the menu tab with a success message
    atweaks_redirect_with_message('menu', 'success', esc_html__('New separator has been added.', 'admin-tweak-suite'));
}

/**
 * Resets the menu to its default order and removes all customizations.
 *
 * @return void
 */
function atweaks_reset_menu() {
    // Delete all menu position options and get a list of deleted options
    $deleted_options = atweaks_delete_options_by_prefix(ATWEAKS_DB_PREFIX . '_menu_');

	// Redirect back to the menu tab with a message
    if (empty($deleted_options)) {
        atweaks_redirect_with_message('menu', 'info', esc_html__('No custom menu settings found to reset.', 'admin-tweak-suite'));
    } else {
        atweaks_redirect_with_message('menu', 'success', esc_html__('Menu has been reset.', 'admin-tweak-suite'));
    }
}

/**
 * Updates the admin menu based on the saved menu order.
 *
 * @return void
 */
function atweaks_update_admin_menu() {
    global $menu;

    // Standard separators and their default positions
    $default_separators = [
        'separator1' => 4,
        'separator2' => 59,
        'separator-last' => 99,
    ];

    // Get all menu items and their positions
    $menu_items = [];
    $added_slugs = []; // To prevent duplicates

    // Add all menu items to the menu items array
    foreach ($menu as $index => $item) {
        $menu_slug = isset($item[2]) ? sanitize_title($item[2]) : "separator_{$index}";
        $menu_name = isset($item[0]) ? wp_strip_all_tags($item[0]) : esc_html__('Unknown', 'admin-tweak-suite');
        $position = round(get_option(ATWEAKS_DB_PREFIX . "_menu_position_{$menu_slug}", $index + 1));
        $is_separator = strpos($menu_slug, 'separator') === 0;

        if (!in_array($menu_slug, $added_slugs)) {
            $menu_items[] = [
                'name'        => $is_separator ? esc_html__('Separator', 'admin-tweak-suite') : $menu_name,
                'slug'        => $menu_slug,
                'position'    => $position,
                'is_separator'=> $is_separator,
                'item'        => $is_separator ? ['', 'read', $menu_slug, '', 'wp-menu-separator'] : $item,
            ];
            $added_slugs[] = $menu_slug;
        }
    }

    // Get all custom separators using the helper function
    $custom_separators = atweaks_get_option_names_by_prefix(ATWEAKS_DB_PREFIX . '_menu_position_separator_');

    foreach ($custom_separators as $separator) {
        $menu_slug = str_replace(ATWEAKS_DB_PREFIX . '_menu_position_', '', $separator);
        $menu_position = round(intval(get_option($separator, 0)));

        if (!in_array($menu_slug, $added_slugs)) {
            $menu_items[] = [
                'name'        => __('Separator', 'admin-tweak-suite'),
                'slug'        => $menu_slug,
                'position'    => $menu_position,
                'is_separator'=> true,
                'item'        => ['', 'read', $menu_slug, '', 'wp-menu-separator'],
            ];
            $added_slugs[] = $menu_slug;
        }
    }

    // Control if any default separators are missing, and add them if needed
    foreach ($default_separators as $slug => $position) {
        if (!array_filter($menu_items, fn($item) => $item['slug'] === $slug)) {
            $menu_items[] = [
                'name'        => __('Separator', 'admin-tweak-suite'),
                'slug'        => $slug,
                'position'    => $position,
                'is_separator'=> true,
                'item'        => ['', 'read', $slug, '', 'wp-menu-separator'],
            ];
        }
    }

    // Sort the menu items by position
    usort($menu_items, function ($a, $b) {
        return $a['position'] - $b['position'];
    });

    // Update the global menu with the new order
    $menu = [];
    foreach ($menu_items as $menu_item) {
        $menu[] = $menu_item['item'];
    }
}
