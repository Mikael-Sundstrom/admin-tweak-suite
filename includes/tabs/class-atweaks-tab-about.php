<?php
/**
 * Handles the "About" tab in the Admin Tweak Suite plugin.
 *
 * @file includes/class-atweaks-tab-about.php
 * @package Admin_Tweak_Suite
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class ATweaks_Tab_About
 *
 * Handles the "About" tab in the Admin Tweak Suite plugin.
 * Provides information about the plugin and options to support its development.
 *
 * @return void
 */
class ATweaks_Tab_About {

	/**
	 * Registers the tab.
	 *
	 * @return void
	 */
	public function register() {
		// Nothing to register for this tab yet.
	}

	/**
	 * Renders the tab content.
	 *
	 * @return void
	 */
	public function render() {
		?>
		<h2><?php esc_html_e( 'About Admin Tweak Suite', 'admin-tweak-suite' ); ?></h2>
		<p><?php esc_html_e( 'This plugin was originally created for my personal use, focusing on improving performance and simplifying the WordPress admin experience.', 'admin-tweak-suite' ); ?></p>
		<p><?php esc_html_e( 'Over time, the plugin has evolved to include features like custom image size management, script and embed control, and shared dashboard notes with role-based permissions.', 'admin-tweak-suite' ); ?></p>
		<p><?php esc_html_e( 'It has grown into a lightweight toolset designed to optimize loading times and provide useful tweaks for the WordPress admin environment.', 'admin-tweak-suite' ); ?></p>
		<p><?php esc_html_e( 'Seeing its potential to help others, I decided to refine it into a proper plugin and share it with the WordPress community on WordPress.org.', 'admin-tweak-suite' ); ?></p>
		<br>

		<h3><?php esc_html_e( 'Support Development', 'admin-tweak-suite' ); ?></h3>
		<p><?php esc_html_e( 'Thank you for using Admin Tweaks Suite! Your support makes it possible to maintain, improve, and expand this plugin. If you find it helpful, consider supporting its development.', 'admin-tweak-suite' ); ?></p>
		<p><?php esc_html_e( 'Every contribution, whether big or small, helps cover development costs and ensures the plugin stays free and up-to-date for everyone.', 'admin-tweak-suite' ); ?></p>
		<p><strong><?php esc_html_e( 'Thank you for your generosity!', 'admin-tweak-suite' ); ?></strong></p>

		<div style="display: flex; flex-wrap: wrap; align-items: center; gap: 20px; margin-bottom: 16px;">
			<div style="display:flex;flex-direction:column;align-items:center;background-color:#f9f9f9;padding:14px;border-radius:8px;box-shadow: 1px 2px 3px rgba(0,0,0,0.1);">
				<a href="https://www.buymeacoffee.com/mikael_sundstrom" target="_blank" rel="noopener noreferrer">
					<div class="atweaks-bmc" style="height: 50px;"></div>
				</a>
				<br>
				<div class="atweaks-qr"></div>
			</div>
		</div>

		<a href="https://www.paypal.com/donate/?hosted_button_id=8J3P56EYQWMPW" target="_blank" rel="noopener noreferrer" style="color: #555;">
			<div class="atweaks-pp"></div><?php esc_html_e( 'Donate With PayPal', 'admin-tweak-suite' ); ?>
		</a>
		<?php
	}
}
