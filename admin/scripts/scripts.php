<?php if (!defined('ABSPATH')) exit;

/**
 * admin/tabs/scripts/scripts.php
 * Renders the "Scripts" tab content for the admin panel.
 */
?>
<h2><?php esc_html_e('Script Handler Settings', 'admin-tweak-suite'); ?></h2>
<p><?php esc_html_e('Manage script settings for the frontend. Use with caution.', 'admin-tweak-suite'); ?></p>

<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display: flex; flex-direction: column; gap: 24px;">
    <input type="hidden" name="action" value="atweaks_script_handler">
    <?php wp_nonce_field('atweaks_script_handler_action', 'atweaks_script_handler_nonce'); ?>

    <!-- Emoji Settings Section -->
    <div>
        <h3><?php esc_html_e('Emoji Settings', 'admin-tweak-suite'); ?></h3>
        <p><?php esc_html_e('Control the use of emojis on your site. Disabling emojis will:', 'admin-tweak-suite'); ?></p>
        <ul class="ul-disc">
            <li><?php esc_html_e('Prevent emoji-related JavaScript and styles from loading on the frontend.', 'admin-tweak-suite'); ?></li>
            <li><?php esc_html_e('Improve site performance by reducing unnecessary script and style loading.', 'admin-tweak-suite'); ?></li>
            <li><?php esc_html_e('Enhance GDPR compliance by minimizing third-party emoji-related tracking and cookie usage.', 'admin-tweak-suite'); ?></li>
        </ul>
        <p>
            <em><?php esc_html_e('This option is useful for sites that do not rely on emojis and aim for a faster, more privacy-focused experience.', 'admin-tweak-suite'); ?></em>
        </p>
        <label>
            <input type="checkbox" name="disable_emojis" value="1" 
                <?php checked(get_option(ATWEAKS_DB_PREFIX . '_disable_emojis', false)); ?>>
            <?php esc_html_e('Disable all emoji-related scripts and styles', 'admin-tweak-suite'); ?>
        </label>
    </div>

    <!-- Embed Settings Section -->
    <div>
        <h3><?php esc_html_e('Embed Settings', 'admin-tweak-suite'); ?></h3>
        <p><?php esc_html_e('Control how embeds are handled on your site. Disabling embeds will:', 'admin-tweak-suite'); ?></p>
        <ul class="ul-disc">
            <li><?php esc_html_e('Remove oEmbed functionality (e.g., YouTube, Twitter) from the frontend and REST API.', 'admin-tweak-suite'); ?></li>
            <li><?php esc_html_e('Prevent embed-related scripts from loading.', 'admin-tweak-suite'); ?></li>
            <li><?php esc_html_e('Enhance GDPR compliance by reducing third-party tracking and cookie usage.', 'admin-tweak-suite'); ?></li>
            <li><?php esc_html_e('Boost performance metrics and PageSpeed Insights scores by reducing external resource loading.', 'admin-tweak-suite'); ?></li>
        </ul>
        <p>
            <em><?php esc_html_e('This option is ideal for sites that want better performance and stricter privacy.', 'admin-tweak-suite'); ?></em>
        </p>
        <label>
            <input type="checkbox" name="disable_embeds" value="1" 
                <?php checked(get_option(ATWEAKS_DB_PREFIX . '_disable_embeds', false)); ?>>
            <?php esc_html_e('Disable oEmbeds and related functionality', 'admin-tweak-suite'); ?>
        </label>
    </div>

    <!-- Custom Inline Script Section -->
    <div>
        <h3><?php esc_html_e('Custom Inline Script', 'admin-tweak-suite'); ?></h3>
        <p>
            <?php esc_html_e('Add custom JavaScript code that will be included in the footer of your site\'s frontend. Use with caution to avoid security and performance issues.', 'admin-tweak-suite'); ?>
        </p>
        <textarea id="atweaks_custom_script" data-mode="javascript" class="atweaks-code-editor" name="custom_script" style="width: 100%;"><?php echo esc_textarea(get_option(ATWEAKS_DB_PREFIX . '_custom_script', '')); ?></textarea>
    </div>

    <div class="button-group submit">
        <button type="submit" class="button button-primary">
            <?php esc_html_e('Save Settings', 'admin-tweak-suite'); ?>
        </button>
        <button type="submit" name="clear_script" value="1" class="button button-secondary">
            <?php esc_html_e('Reset to default', 'admin-tweak-suite'); ?>
        </button>
    </div>
</form>