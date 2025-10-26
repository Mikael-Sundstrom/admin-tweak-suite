<?php
/**
 * Handles the "Custom CSS" tab in the Admin Tweak Suite plugin.
 *
 * @file includes/tabs/class-atweaks-tab-css.php
 * @package Admin_Tweak_Suite
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class ATweaks_Tab_Css.
 *
 * Handles the "Custom CSS" tab in the Admin Tweak Suite plugin.
 *
 * @package Admin_Tweak_Suite
 */
class ATweaks_Tab_Css {

	/**
	 * Registers the tab.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_post_manage_frontend_css', fn() => $this->handle_css( 'frontend' ) );
		add_action( 'admin_post_manage_admin_css', fn() => $this->handle_css( 'admin' ) );
	}

	/**
	 * Renders the tab content.
	 *
	 * @return void
	 */
	public function render() {
		$frontend_css = trim( get_option( ATWEAKS_DB_PREFIX . '_custom_frontend_css', '' ) );
		$admin_css    = trim( get_option( ATWEAKS_DB_PREFIX . '_custom_admin_css', '' ) );

		echo '<h2>' . esc_html__( 'Custom CSS Settings', 'admin-tweak-suite' ) . '</h2>';
		echo '<p>' . esc_html__( 'Use these sections to add custom inline CSS for the frontend and admin panel.', 'admin-tweak-suite' ) . '</p>';
		echo '<p><em>' . wp_kses(
			__( 'Note: The CSS added here will be directly inserted as inline styles in the respective parts of the website and <strong>affect all users of the website, regardless of their role</strong>.', 'admin-tweak-suite' ),
			array( 'strong' => array() )
		) . '</em></p>';

		echo '<h3>' . esc_html__( 'Frontend CSS', 'admin-tweak-suite' ) . '</h3>';
		$this->render_css_form( 'manage_frontend_css', 'save_frontend_css_action', 'save_frontend_css_nonce', $frontend_css, esc_html__( 'Save Frontend CSS', 'admin-tweak-suite' ), esc_html__( 'Clear Frontend CSS', 'admin-tweak-suite' ) );

		echo '<h3>' . esc_html__( 'Admin Panel CSS', 'admin-tweak-suite' ) . '</h3>';
		$this->render_css_form( 'manage_admin_css', 'save_admin_css_action', 'save_admin_css_nonce', $admin_css, esc_html__( 'Save Admin CSS', 'admin-tweak-suite' ), esc_html__( 'Clear Admin CSS', 'admin-tweak-suite' ) );
	}

	/**
	 * Renders the CSS form.
	 *
	 * @param string $action The form action.
	 * @param string $nonce_action The nonce action.
	 * @param string $nonce_name The nonce name.
	 * @param string $css_content The current CSS content.
	 * @param string $save_label The label for the save button.
	 * @param string $clear_label The label for the clear button.
	 * @return void
	 */
	private function render_css_form( $action, $nonce_action, $nonce_name, $css_content, $save_label, $clear_label ) {
		?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>">
			<?php wp_nonce_field( $nonce_action, $nonce_name ); ?>
			<textarea
				id="<?php echo esc_attr( $action ); ?>"
				class="atweaks-code-editor"
				data-mode="css"
				style="width: 100%;"
				name="custom_css"
				placeholder="<?php esc_attr_e( 'Enter custom CSS here...', 'admin-tweak-suite' ); ?>"
			><?php echo esc_textarea( $css_content ); ?></textarea>
			<br>
			<div class="button-group submit">
				<button type="submit" class="button button-primary" name="submit_action" value="save"><?php echo esc_html( $save_label ); ?></button>
				<button type="submit" class="button button-secondary" name="submit_action" value="clear"><?php echo esc_html( $clear_label ); ?></button>
			</div>
		</form>
		<?php
	}

	/**
	 * Handles the CSS form submission.
	 *
	 * @param string $type The type of CSS (frontend or admin).
	 * @return void
	 */
	private function handle_css( $type ) {
		// Security: only allow users with 'manage_options' capability.
		if ( ! current_user_can( 'manage_options' ) ) {
			atweaks_redirect_with_message( 'css', 'error', esc_html__( 'Access Denied: You do not have permission to perform this action.', 'admin-tweak-suite' ) );
		}

		// Validate the security nonce.
		$nonce_value = isset( $_POST[ "save_{$type}_css_nonce" ] )
			? sanitize_text_field( wp_unslash( $_POST[ "save_{$type}_css_nonce" ] ) )
			: '';

		if ( ! wp_verify_nonce( $nonce_value, "save_{$type}_css_action" ) ) {
			atweaks_redirect_with_message( 'css', 'error', esc_html__( 'Security check failed. Please try again.', 'admin-tweak-suite' ) );
		}

		// Determine whether the user clicked "Save" or "Clear".
		$action = isset( $_POST['submit_action'] )
			? sanitize_text_field( wp_unslash( $_POST['submit_action'] ) )
			: '';

		switch ( $action ) {
			case 'save':
				// Get the CSS input, or empty if not set.
				$css = isset( $_POST['custom_css'] )
					? sanitize_textarea_field( wp_unslash( $_POST['custom_css'] ) )
					: '';

				if ( '' === $css ) {
					// If the CSS is empty, treat it exactly like a "Clear".
					delete_option( ATWEAKS_DB_PREFIX . "_custom_{$type}_css" );
					delete_transient( "atweaks_cached_{$type}_css" );

					// translators: %s: CSS type (Frontend/Admin).
					$message = sprintf( esc_html__( '%s CSS has been successfully cleared.', 'admin-tweak-suite' ), ucfirst( $type ) );
					atweaks_redirect_with_message( 'css', 'success', $message );
				}

				// Otherwise, save the CSS.
				update_option( ATWEAKS_DB_PREFIX . "_custom_{$type}_css", $css );
				set_transient( "atweaks_cached_{$type}_css", $css );

				// translators: %s: CSS type (Frontend/Admin).
				$message = sprintf( esc_html__( '%s CSS has been successfully saved.', 'admin-tweak-suite' ), ucfirst( $type ) );
				atweaks_redirect_with_message( 'css', 'success', $message );
				break;

			case 'clear':
				// "Clear" button: always delete, regardless of current value.
				delete_option( ATWEAKS_DB_PREFIX . "_custom_{$type}_css" );
				delete_transient( "atweaks_cached_{$type}_css" );

				// translators: %s: CSS type (Frontend/Admin).
				$message = sprintf( esc_html__( '%s CSS has been successfully cleared.', 'admin-tweak-suite' ), ucfirst( $type ) );
				atweaks_redirect_with_message( 'css', 'success', $message );
				break;

			default:
				// Safety fallback: unknown submit action.
				atweaks_redirect_with_message( 'css', 'error', esc_html__( 'Unknown form action. Please try again.', 'admin-tweak-suite' ) );
				break;
		}
	}
}
