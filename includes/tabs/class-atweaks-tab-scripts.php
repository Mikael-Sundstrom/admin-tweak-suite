<?php
/**
 * Tab for customizing script settings.
 *
 * @file includes/tabs/class-atweaks-tab-scripts.php
 * @package Admin_Tweak_Suite
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class ATweaks_Tab_Scripts.
 *
 * Handles the "Scripts" tab in the Admin Tweak Suite plugin.
 */
class ATweaks_Tab_Scripts {

	/**
	 * Registers the tab.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_post_atweaks_script_handler', array( $this, 'handle' ) );
	}

	/**
	 * Renders the tab content.
	 *
	 * @return void
	 */
	public function render() {
		$option = function ( $key ) {
			return get_option( ATWEAKS_DB_PREFIX . '_' . $key, false );
		};

		?>
		<h2><?php esc_html_e( 'Script Handler Settings', 'admin-tweak-suite' ); ?></h2>
		<p><?php esc_html_e( 'Manage script settings for the frontend. Use with caution.', 'admin-tweak-suite' ); ?></p>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display: flex; flex-direction: column; gap: 24px;">
			<input type="hidden" name="action" value="atweaks_script_handler">
			<?php wp_nonce_field( 'atweaks_script_handler_action', 'atweaks_script_handler_nonce' ); ?>

			<?php
			$this->render_checkbox_section(
				__( 'Emoji Settings', 'admin-tweak-suite' ),
				'disable_emojis',
				$option( 'disable_emojis' ),
				array(
					__( 'Prevent emoji-related JavaScript and styles from loading on the frontend.', 'admin-tweak-suite' ),
					__( 'Improve site performance by reducing unnecessary script and style loading.', 'admin-tweak-suite' ),
					__( 'Enhance GDPR compliance by minimizing third-party emoji-related tracking and cookie usage.', 'admin-tweak-suite' ),
				)
			);
			?>

			<?php
			$this->render_checkbox_section(
				__( 'Embed Settings', 'admin-tweak-suite' ),
				'disable_embeds',
				$option( 'disable_embeds' ),
				array(
					__( 'Remove oEmbed functionality (e.g., YouTube, Twitter) from the frontend and REST API.', 'admin-tweak-suite' ),
					__( 'Prevent embed-related scripts from loading.', 'admin-tweak-suite' ),
					__( 'Enhance GDPR compliance by reducing third-party tracking and cookie usage.', 'admin-tweak-suite' ),
					__( 'Boost performance metrics and PageSpeed Insights scores by reducing external resource loading.', 'admin-tweak-suite' ),
				)
			);
			?>

			<?php
			$this->render_checkbox_section(
				__( 'jQuery Migrate Settings', 'admin-tweak-suite' ),
				'disable_jquery_migrate',
				$option( 'disable_jquery_migrate' ),
				array(
					__( 'Improve site performance by removing outdated compatibility scripts.', 'admin-tweak-suite' ),
					__( 'Reduce JavaScript loading time and improve PageSpeed Insights scores.', 'admin-tweak-suite' ),
				)
			);
			?>

			<?php
			$this->render_checkbox_section(
				__( 'XML-RPC Settings', 'admin-tweak-suite' ),
				'disable_xmlrpc',
				$option( 'disable_xmlrpc' ),
				array(
					__( 'Prevent external XML-RPC requests and reduce the risk of DDoS and brute-force attacks.', 'admin-tweak-suite' ),
					__( 'Improve site security by closing an often unused attack vector.', 'admin-tweak-suite' ),
				)
			);
			?>

			<div>
				<h3><?php esc_html_e( 'Custom Inline Script', 'admin-tweak-suite' ); ?></h3>
				<p><?php esc_html_e( 'Add custom JavaScript code that will be included in the footer of your site\'s frontend.', 'admin-tweak-suite' ); ?></p>
				<textarea
					id="atweaks_custom_script"
					data-mode="javascript"
					class="atweaks-code-editor"
					name="custom_script"
					style="width: 100%;"
					placeholder="<?php esc_attr_e( 'Enter custom JavaScript here...', 'admin-tweak-suite' ); ?>"
				><?php echo esc_textarea( get_option( ATWEAKS_DB_PREFIX . '_custom_script', '' ) ); ?></textarea>
			</div>

			<div class="button-group submit">
				<button type="submit" class="button button-primary">
					<?php esc_html_e( 'Save Settings', 'admin-tweak-suite' ); ?>
				</button>
				<button type="submit" name="clear_script" value="1" class="button button-secondary">
					<?php esc_html_e( 'Reset to default', 'admin-tweak-suite' ); ?>
				</button>
			</div>
		</form>
		<?php
	}

	/**
	 * Renders a checkbox section.
	 *
	 * @param string $title     The title of the section.
	 * @param string $field_name The name of the checkbox field.
	 * @param bool   $checked    Whether the checkbox is checked.
	 * @param array  $points     Additional points to display.
	 * @return void
	 */
	private function render_checkbox_section( $title, $field_name, $checked, $points = array() ) {
		?>
		<div>
			<h3><?php echo esc_html( $title ); ?></h3>
			<ul class="ul-disc">
				<?php
				foreach ( $points as $point ) {
					echo '<li>' . esc_html( $point ) . '</li>';
				}
				?>
			</ul>
			<label>
				<input type="checkbox" name="<?php echo esc_attr( $field_name ); ?>" value="1" <?php checked( $checked ); ?>>
				<?php echo esc_html__( 'Disable', 'admin-tweak-suite' ) . ' ' . esc_html( $title ); ?>
			</label>
		</div>
		<?php
	}

	/**
	 * Handles the form submission.
	 *
	 * @return void
	 */
	public function handle() {
		if ( ! current_user_can( 'manage_options' ) ) {
			atweaks_redirect_with_message( 'scripts', 'error', esc_html__( 'Access Denied: You do not have permission to perform this action.', 'admin-tweak-suite' ) );
		}

		if ( ! isset( $_POST['atweaks_script_handler_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['atweaks_script_handler_nonce'] ) ), 'atweaks_script_handler_action' ) ) {
			atweaks_redirect_with_message( 'scripts', 'error', esc_html__( 'Security check failed. Please try again.', 'admin-tweak-suite' ) );
		}

		$options = array(
			'disable_emojis',
			'disable_embeds',
			'disable_jquery_migrate',
			'disable_xmlrpc',
		);

		$cleared = false;

		if ( isset( $_POST['clear_script'] ) ) {
			foreach ( $options as $field ) {
				$option   = ATWEAKS_DB_PREFIX . "_$field";
				$cleared |= delete_option( $option );
			}
			delete_option( ATWEAKS_DB_PREFIX . '_custom_script' );
			delete_transient( 'atweaks_cached_frontend_script' );

			atweaks_redirect_with_message( 'scripts', 'success', esc_html__( 'All script settings have been reset.', 'admin-tweak-suite' ) );
		}

		// Save checkboxes.
		foreach ( $options as $field ) {
			$option = ATWEAKS_DB_PREFIX . "_$field";
			isset( $_POST[ $field ] ) ? update_option( $option, 1 ) : delete_option( $option );
		}

		// Save script.
		$option_script = ATWEAKS_DB_PREFIX . '_custom_script';
		$custom_script = isset( $_POST['custom_script'] ) ? wp_unslash( $_POST['custom_script'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JavaScript must allow special characters, escaped on output.

		if ( '' !== $custom_script ) {
			update_option( $option_script, $custom_script );
			set_transient( 'atweaks_cached_frontend_script', $custom_script );
			atweaks_redirect_with_message( 'scripts', 'success', esc_html__( 'Custom script has been successfully saved.', 'admin-tweak-suite' ) );
		} else {
			delete_option( $option_script );
			delete_transient( 'atweaks_cached_frontend_script' );
			atweaks_redirect_with_message( 'scripts', 'success', esc_html__( 'Custom script has been successfully cleared.', 'admin-tweak-suite' ) );
		}
	}
}
