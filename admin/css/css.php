<?php if (!defined('ABSPATH')) exit;

/**
 * admin/tabs/css/css.php
 * Renders the "CSS" tab content for the admin panel.
 */

// Get the current saved CSS content
$frontend_css = trim(get_option(ATWEAKS_DB_PREFIX . '_custom_frontend_css', ''));
$admin_css = trim(get_option(ATWEAKS_DB_PREFIX . '_custom_admin_css', ''));
?>

<!-----------------------------------------------------------------------------
START OF CSS-TAB

Here we will add the form to manage custom CSS for the frontend and admin panel.
------------------------------------------------------------------------------>
<h2><?php esc_html_e('Custom CSS Settings', 'admin-tweak-suite'); ?></h2>
<p><?php esc_html_e('Use these sections to add custom inline CSS for the frontend and admin panel.', 'admin-tweak-suite'); ?></p>
<p><em>
    <?php 
    // Keep the HTML tags in the translation string
    echo wp_kses(
        __('Note: The CSS added here will be directly inserted as inline styles in the respective parts of the website and <strong>affect all users of the website, regardless of their role</strong>.', 'admin-tweak-suite'),
        array( 'strong' => array() )
    );
    ?>
</em></p>

<!-- Form to manage frontend CSS -->
<h3><?php esc_html_e('Frontend CSS', 'admin-tweak-suite'); ?></h3>
<?php
render_css_form(
    'manage_frontend_css',                                 // The action to handle the form submission (used in admin-post.php)
    'save_frontend_css_action',                            // The nonce action name for security
    'save_frontend_css_nonce',                             // The nonce field name for security
    $frontend_css,                                         // The current CSS content to be displayed in the textarea
    esc_html__('Save Frontend CSS', 'admin-tweak-suite'),  // The label for the save button
    esc_html__('Clear Frontend CSS', 'admin-tweak-suite')  // The label for the clear button
);
?>

<!-- Form to manage admin panel CSS -->
<h3><?php esc_html_e('Admin Panel CSS', 'admin-tweak-suite'); ?></h3>
<?php
render_css_form(
    'manage_admin_css',                                 // The action to handle the form submission (used in admin-post.php)
    'save_admin_css_action',                            // The nonce action name for security
    'save_admin_css_nonce',                             // The nonce field name for security
    $admin_css,                                         // The current CSS content to be displayed in the textarea
    esc_html__('Save Admin CSS', 'admin-tweak-suite'),  // The label for the save button
    esc_html__('Clear Admin CSS', 'admin-tweak-suite')  // The label for the clear button
);
?>
<!-----------------------------------------------------------------------------
END OF CSS-TAB

The following function is used to render the CSS form.
------------------------------------------------------------------------------>
<?php
/**
 * Renders the CSS form.
 *
 * @param string $action       The action to be performed.
 * @param string $nonce_action The nonce action.
 * @param string $nonce_name   The nonce name.
 * @param string $css_content  The CSS content to be displayed.
 * @param string $save_label   The label for the save button.
 * @param string $clear_label  The label for the clear button.
 * @return void
 */
function render_css_form($action, $nonce_action, $nonce_name, $css_content, $save_label, $clear_label) {
    ?>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
		<input type="hidden" name="action" value="<?php echo esc_attr($action); ?>">
		<?php wp_nonce_field($nonce_action, $nonce_name); ?>

		<!-- Add the CodeMirror editor textarea -->
		<textarea
			id="<?php echo esc_html($action) ?>"
			class="atweaks-code-editor"
			data-mode="css"
			style="width: 100%;"
			name="custom_css"
			placeholder="<?php esc_html_e('Enter custom CSS here...', 'admin-tweak-suite'); ?>"
		><?php echo esc_textarea($css_content); ?></textarea>
		<br>
		<div class="button-group submit">
			<button type="submit" class="button button-primary" name="submit_action" value="save">
				<?php echo esc_html($save_label); ?>
			</button>
			<button type="submit" class="button button-secondary" name="submit_action" value="clear">
				<?php echo esc_html($clear_label); ?>
			</button>
		</div>
	</form>
    <?php
}