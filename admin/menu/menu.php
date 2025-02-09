<?php if (!defined('ABSPATH')) exit;

// Get menu items from menu-data.php
$menu_items = atweaks_get_menu_items();
?>

<h2><?php esc_html_e('Menu Order', 'admin-tweak-suite'); ?></h2>
<noscript>
    <div class="notice notice-warning">
        <p><?php esc_html_e('JavaScript is required to use the drag-and-drop functionality. Please enable JavaScript.', 'admin-tweak-suite'); ?></p>
    </div>
</noscript>

<p><?php esc_html_e('Use the drag-and-drop functionality to reorder the menu items.', 'admin-tweak-suite'); ?></p>
<p><em><?php esc_html_e('Note: This feature will affect all users of the website, regardless of their role.', 'admin-tweak-suite'); ?></em></p>

<?php
// Render the menu form with the menu items
atweaks_render_menu_form($menu_items);
