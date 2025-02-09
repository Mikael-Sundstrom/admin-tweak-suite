<?php if (!defined('ABSPATH')) exit;

/**
 * Includes/helpers/validation.php
 * Functions for validation and security checks.
 */

/**
 * Validates and sanitizes input data.
 *
 * @param mixed $data The data to validate and sanitize.
 * @param string $type The type of data: 'text', 'number', 'email', 'url', 'textarea', or 'boolean'. Default is 'text'.
 * @return mixed The sanitized data.
 */
function atweaks_sanitize_input($data, $type = 'text') {
    switch ($type) {
        case 'text':
            return sanitize_text_field($data);
        case 'number':
            return absint($data);
        case 'email':
            return sanitize_email($data);
        case 'url':
            return esc_url_raw($data);
        case 'textarea':
            return sanitize_textarea_field($data);
        case 'boolean':
            return filter_var($data, FILTER_VALIDATE_BOOLEAN);
        default:
            return sanitize_text_field($data);
    }
}
