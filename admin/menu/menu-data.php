<?php if (!defined('ABSPATH')) exit;

// includes/helpers/menu-data.php
function atweaks_get_menu_items() {
    global $menu, $wpdb;

    $menu_items = [];
    $added_slugs = [];

    foreach ($menu as $index => $item) {
        $menu_name = isset($item[0]) ? atweaks_sanitize_input($item[0], 'text') : esc_html__('Unknown', 'admin-tweak-suite');
        $menu_slug = isset($item[2]) ? atweaks_sanitize_input($item[2], 'text') : "separator_{$index}";
        $menu_position = round(get_option(ATWEAKS_DB_PREFIX . "_menu_position_{$menu_slug}", $index + 1));
        $menu_name = preg_replace('/\s*\d+.*$/', '', $menu_name);

        $menu_items[] = [
            'name' => trim($menu_name),
            'slug' => $menu_slug,
            'position' => $menu_position,
            'is_separator' => false,
        ];
        $added_slugs[] = $menu_slug;
    }

    // Hämta alla separatorer med en hjälpfunktion istället för direkt SQL
	$custom_separators = atweaks_get_option_names_by_prefix(ATWEAKS_DB_PREFIX . '_menu_position_separator_');

	foreach ($custom_separators as $separator) {
		$menu_slug = atweaks_sanitize_input(str_replace(ATWEAKS_DB_PREFIX . '_menu_position_', '', $separator), 'text');
		$menu_position = atweaks_sanitize_input(get_option($separator, 0), 'number');

		if (!in_array($menu_slug, $added_slugs)) {
			$menu_items[] = [
				'name' => esc_html__('Separator', 'admin-tweak-suite'),
				'slug' => $menu_slug,
				'position' => $menu_position,
				'is_separator' => true,
			];
			$added_slugs[] = $menu_slug;
		}
	}

    usort($menu_items, function ($a, $b) {
        return $a['position'] - $b['position'];
    });

    return $menu_items;
}
