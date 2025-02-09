<?php if (!defined('ABSPATH')) exit;

/**
 * admin/tabs/about/about.php
 * Renders the "About" tab content for the admin panel.
 */
?>

<!-- Om pluginet -->
<h2><?php esc_html_e('About Admin Tweak Suite', 'admin-tweak-suite'); ?></h2>
<p><?php esc_html_e('This plugin was initially created for my personal use, with the primary goal of customizing the WordPress admin menu to better fit my workflow.', 'admin-tweak-suite'); ?></p>
<p><?php esc_html_e('Over time, as new ideas and needs arose, it grew far beyond its initial purpose. Features like inline CSS and script injection, cache management, and other tools were added, making it a much more comprehensive solution than I had originally intended.', 'admin-tweak-suite'); ?></p>
<p><?php esc_html_e('Seeing its potential to help others, I decided to refine it into a proper plugin and share it with the WordPress community on WordPress.org.', 'admin-tweak-suite'); ?></p>
<br>

<!-- Donationssektion -->
<h3><?php esc_html_e('Support Development', 'admin-tweak-suite'); ?></h3>
<p>
	<?php esc_html_e('Thank you for using Admin Tweaks Suite! Your support makes it possible to maintain, improve, and expand this plugin. If you find it helpful, consider supporting its development.', 'admin-tweak-suite'); ?>
</p>
<p>
	<?php esc_html_e('Every contribution, whether big or small, helps cover development costs and ensures the plugin stays free and up-to-date for everyone.', 'admin-tweak-suite'); ?>
</p>
<p><strong><?php esc_html_e('Thank you for your generosity!', 'admin-tweak-suite'); ?></strong></p>
<div style="display: flex; flex-wrap: wrap; align-items: center; gap: 20px; margin-bottom: 16px;">
	<!-- Buy Me a Coffee-knapp -->
	<div style="display:flex;flex-direction:column;align-items:center;background-color:#f9f9f9;padding:14px;border-radius:8px;box-shadow: 1px 2px 3px rgba(0,0,0,0.1);">
		<a href="https://www.buymeacoffee.com/mikael_sundstrom" target="_blank">
			<div class="atweaks-bmc" style="height: 50px;"></div>
		</a>
		<br>
		<div class="atweaks-qr"></div>
	</div>
</div>
<!-- PayPal-knapp -->
<a href="https://www.paypal.com/donate/?hosted_button_id=8J3P56EYQWMPW" target="_blank" style="color: #555;">
	<div class="atweaks-pp"></div><?php esc_html_e('Donate With PayPal', 'admin-tweak-suite'); ?>
</a>

