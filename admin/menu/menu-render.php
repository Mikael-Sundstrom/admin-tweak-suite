<?php if (!defined('ABSPATH')) exit;

/**
 * admin/tabs/menu/menu-render.php
 * Handles forms and actions for the Admin Menu tab.
 */
function atweaks_render_menu_form($menu_items) {

    ?>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="save_menu">
        <?php wp_nonce_field('save_menu_action', 'save_menu_nonce'); ?>

		<div style="display: flex; flex-direction: column; gap: 1rem; max-width: 356px; margin: 20px 0;">
			<!-- Checkbox for hiding the "Minimize Menu" button -->
			<div style="display: flex;flex-direction: column; gap: 0.5rem; justify-content: space-between;">
				<label for="<?php echo esc_attr(ATWEAKS_DB_PREFIX . '_menu_hide_collapse_menu'); ?>">
					<?php esc_html_e('Hide "Minimize Menu" button', 'admin-tweak-suite'); ?>:
				</label>
				<input 
					type="checkbox" 
					id="<?php echo esc_attr(ATWEAKS_DB_PREFIX . '_menu_hide_collapse_menu'); ?>" 
    				name="<?php echo esc_attr(ATWEAKS_DB_PREFIX . '_menu_hide_collapse_menu'); ?>"
					value="1" 
					<?php checked(get_option(ATWEAKS_DB_PREFIX . '_menu_hide_collapse_menu', false), 1); ?>
				>
			</div>
			<!-- Range input for adjusting the separator height -->
			<div style="display: flex;flex-direction: column;gap: 0.5rem; justify-content: space-between;">
				<label for="separator_height">
					<?php esc_html_e('Separator Height', 'admin-tweak-suite'); ?>:
				</label>
				<div style="display: flex; gap: 0.5rem;">
					<input
						class="wp-slider ui-slider"
						style="width: 150px;"
						type="range"
						id="separator_height"
						name="<?php echo esc_attr(ATWEAKS_DB_PREFIX . '_menu_separator_height'); ?>"
						min="5"
						max="25"
						step="1"
						value="<?php echo esc_attr(get_option(ATWEAKS_DB_PREFIX . '_menu_separator_height', 5)); ?>"
						oninput="document.getElementById('separator_height_value').textContent = this.value;"
					>
					<span id="separator_height_value" style="font-weight: bold;">
						<?php echo esc_html(get_option(ATWEAKS_DB_PREFIX . '_menu_separator_height', 5)); ?>
					</span>px
				</div>
			</div>
		</div>
        
		<input type="hidden" name="menu_items_json" id="menu_items_json" value="<?php echo esc_attr(json_encode($menu_items)); ?>">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th class="drag-handle-column"></th>
                    <th><?php esc_html_e('Menu', 'admin-tweak-suite'); ?></th>
                    <th><?php esc_html_e('Position', 'admin-tweak-suite'); ?></th>
                </tr>
            </thead>
            <tbody id="menu-list">
				<?php foreach ($menu_items as $item): ?>
					<tr>
						<td aria-grabbed="false" class="drag-handle">&#x21f5;</td>
						<td><?php echo $item['is_separator'] ? '' : esc_html($item['name']); ?></td>
						<td>
							<input type="number" name="menu_items[<?php echo esc_attr($item['slug']); ?>][position]" value="<?php echo esc_attr($item['position']); ?>" class="regular-text">
						</td>
					</tr>
				<?php endforeach; ?>
            </tbody>
        </table>
		<br>
		<div class="button-group submit">
			<input type="submit" name="save_menu" class="button button-primary" value="<?php esc_html_e('Save Changes', 'admin-tweak-suite'); ?>">
			<input type="submit" name="add_separator" class="button" value="<?php esc_html_e('Add Separator', 'admin-tweak-suite'); ?>">
        	<input type="submit" name="reset_menu" class="button" value="<?php esc_html_e('Reset Menu', 'admin-tweak-suite'); ?>">
		</div>
    </form>
    <?php
}