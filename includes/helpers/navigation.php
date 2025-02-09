<?php if (!defined('ABSPATH')) exit;

/**
 * Includes/helpers/navigation.php
 * Functions for handling navigation and tabs.
 */

/**
 * Get the currently active admin tab.
 *
 * @return string The current tab, defaults to 'menu'.
 */
function atweaks_get_current_tab() {
    return isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'menu'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}

