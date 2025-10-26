<?php
/**
 * Tab for customizing the admin menu order.
 *
 * @file includes/tabs/class-atweaks-tab-menu.php
 * @package Admin_Tweak_Suite
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class ATweaks_Tab_Menu.
 *
 * Handles the "Menu" tab in the Admin Tweak Suite plugin.
 */
class ATweaks_Tab_Menu {

	/**
	 * Registers the tab.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_post_atweaks_save_menu', array( $this, 'handle_form' ) );
		add_action( 'admin_menu', array( $this, 'apply_menu_order' ), 999 );
	}

	/**
	 * Renders the tab content.
	 *
	 * @return void
	 */
	public function render(): void {
		$menu_items = $this->get_menu_items();

		echo '<h2>' . esc_html__( 'Menu Order', 'admin-tweak-suite' ) . '</h2>';
		echo '<noscript>';
		echo '<div class="notice notice-warning"><p>';
		esc_html_e( 'JavaScript is required to use the drag-and-drop functionality.', 'admin-tweak-suite' );
		echo '</p></div>';
		echo '</noscript>';

		echo '<p>' . esc_html__( 'Use drag-and-drop to reorder the menu items.', 'admin-tweak-suite' ) . '</p>';
		echo '<p><em>' . esc_html__( 'Note: This affects all users.', 'admin-tweak-suite' ) . '</em></p>';

		$this->render_form( $menu_items );
	}

	/**
	 * Retrieves the current menu items and their positions.
	 *
	 * @return array The menu items with their names, slugs, and positions.
	 */
	private function get_menu_items(): array {
		global $menu;

		$items = array();
		$added = array();

		foreach ( $menu as $i => $entry ) {
			$name = isset( $entry[0] ) ? atweaks_sanitize_input( $entry[0], 'text' ) : __( 'Unknown', 'admin-tweak-suite' );
			$slug = isset( $entry[2] ) ? atweaks_sanitize_input( $entry[2], 'text' ) : "separator_$i";
			$pos  = (int) round( get_option( ATWEAKS_DB_PREFIX . "_menu_position_$slug", $i + 1 ) );
			$name = preg_replace( '/\s*\d+.*$/', '', $name );

			$items[] = array(
				'name'         => trim( $name ),
				'slug'         => $slug,
				'position'     => $pos,
				'is_separator' => false,
			);
			$added[] = $slug;
		}

		foreach ( atweaks_get_option_names_by_prefix( ATWEAKS_DB_PREFIX . '_menu_position_separator_' ) as $sep ) {
			$slug = str_replace( ATWEAKS_DB_PREFIX . '_menu_position_', '', $sep );
			$pos  = (int) get_option( $sep, 0 );
			if ( ! in_array( $slug, $added, true ) ) {
				$items[] = array(
					'name'         => __( 'Separator', 'admin-tweak-suite' ),
					'slug'         => $slug,
					'position'     => $pos,
					'is_separator' => true,
				);
				$added[] = $slug;
			}
		}

		usort(
			$items,
			static function ( $a, $b ) {
				return (int) $a['position'] - (int) $b['position'];
			}
		);

		return $items;
	}

	/**
	 * Renders the form for customizing the menu order.
	 *
	 * @param array $menu_items The menu items to display.
	 * @return void
	 */
	private function render_form( array $menu_items ): void {
		?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="atweaks_save_menu">
			<?php wp_nonce_field( 'save_menu_action', 'save_menu_nonce' ); ?>

			<div style="display:flex;flex-direction:column;gap:1rem;max-width:356px;margin:20px 0;">
				<div style="display:flex;flex-direction:column;gap:0.5rem;">
					<label for="<?php echo esc_attr( ATWEAKS_DB_PREFIX . '_menu_hide_collapse_menu' ); ?>">
						<?php esc_html_e( 'Hide "Minimize Menu" button', 'admin-tweak-suite' ); ?>:
					</label>
					<input
						type="checkbox"
						id="<?php echo esc_attr( ATWEAKS_DB_PREFIX . '_menu_hide_collapse_menu' ); ?>"
						name="<?php echo esc_attr( ATWEAKS_DB_PREFIX . '_menu_hide_collapse_menu' ); ?>"
						value="1"
						<?php checked( (int) get_option( ATWEAKS_DB_PREFIX . '_menu_hide_collapse_menu', 0 ), 1 ); ?>
					>
				</div>

				<div style="display:flex;flex-direction:column;gap:0.5rem;">
					<label for="separator_height">
						<?php esc_html_e( 'Separator Height', 'admin-tweak-suite' ); ?>:
					</label>
					<div style="display:flex;gap:0.5rem;">
						<input
							class="wp-slider ui-slider"
							style="width:200px;"
							type="range"
							id="separator_height"
							name="<?php echo esc_attr( ATWEAKS_DB_PREFIX . '_menu_separator_height' ); ?>"
							min="5" max="50" step="1"
							value="<?php echo esc_attr( (int) get_option( ATWEAKS_DB_PREFIX . '_menu_separator_height', 5 ) ); ?>"
							oninput="document.getElementById('separator_height_value').textContent = this.value;"
						>
						<span id="separator_height_value" style="font-weight:bold;">
							<?php echo esc_html( (int) get_option( ATWEAKS_DB_PREFIX . '_menu_separator_height', 5 ) ); ?>
						</span>px
					</div>
				</div>
			</div>

			<input type="hidden" name="menu_items_json" id="menu_items_json" value="<?php echo esc_attr( wp_json_encode( $menu_items ) ); ?>">
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th class="drag-handle-column"></th>
						<th><?php esc_html_e( 'Menu', 'admin-tweak-suite' ); ?></th>
						<th><?php esc_html_e( 'Position', 'admin-tweak-suite' ); ?></th>
					</tr>
				</thead>
				<tbody id="menu-list">
					<?php foreach ( $menu_items as $item ) : ?>
						<tr>
							<td aria-grabbed="false" class="drag-handle">&#x21f5;</td>
							<td><?php echo $item['is_separator'] ? '' : esc_html( $item['name'] ); ?></td>
							<td>
								<input type="number" name="menu_items[<?php echo esc_attr( $item['slug'] ); ?>][position]" value="<?php echo esc_attr( (int) $item['position'] ); ?>" class="regular-text">
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<br>
			<div class="button-group submit">
				<input type="submit" name="save_menu" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'admin-tweak-suite' ); ?>">
				<input type="submit" name="add_separator" class="button" value="<?php esc_attr_e( 'Add Separator', 'admin-tweak-suite' ); ?>">
				<input type="submit" name="reset_menu" class="button" value="<?php esc_attr_e( 'Reset Menu', 'admin-tweak-suite' ); ?>">
			</div>
		</form>
		<?php
	}

	/**
	 * Handle menu form actions (save/add/reset).
	 *
	 * @return void
	 */
	public function handle_form(): void {
		if ( ! isset( $_POST['save_menu'] ) && ! isset( $_POST['add_separator'] ) && ! isset( $_POST['reset_menu'] ) ) {
			atweaks_redirect_with_message( 'menu', 'error', esc_html__( 'No action provided.', 'admin-tweak-suite' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			atweaks_redirect_with_message( 'menu', 'error', esc_html__( 'Access Denied: You do not have permission to perform this action.', 'admin-tweak-suite' ) );
		}

		if ( ! isset( $_POST['save_menu_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['save_menu_nonce'] ) ), 'save_menu_action' ) ) {
			atweaks_redirect_with_message( 'menu', 'error', esc_html__( 'Security check failed. Please try again.', 'admin-tweak-suite' ) );
		}

		// Safe local copy efter nonce-koll.
		$post = wp_unslash( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verifierad ovan.

		if ( isset( $post['save_menu'] ) ) {
			$menu_items       = isset( $post['menu_items'] ) && is_array( $post['menu_items'] ) ? array_map(
				static function ( $i ) {
					return is_array( $i ) ? array_map( 'sanitize_text_field', $i ) : array();
				},
				$post['menu_items']
			) : array();
			$hide_collapse    = isset( $post[ ATWEAKS_DB_PREFIX . '_menu_hide_collapse_menu' ] ) ? 1 : 0;
			$separator_height = isset( $post[ ATWEAKS_DB_PREFIX . '_menu_separator_height' ] ) ? absint( $post[ ATWEAKS_DB_PREFIX . '_menu_separator_height' ] ) : null;

			$this->save_menu_order( $menu_items, $hide_collapse, $separator_height );
		} elseif ( isset( $post['add_separator'] ) ) {
			$this->add_separator();
		} elseif ( isset( $post['reset_menu'] ) ) {
			$this->reset_menu();
		} else {
			atweaks_redirect_with_message( 'menu', 'error', esc_html__( 'Unknown action. Please try again.', 'admin-tweak-suite' ) );
		}

		atweaks_redirect_with_message( 'menu', 'success', esc_html__( 'Menu updated successfully.', 'admin-tweak-suite' ) );
		exit;
	}

	/**
	 * Save menu order and related options.
	 *
	 * @param array    $items         Slug=>[position=>int] map.
	 * @param int      $hide_collapse 0/1 flag for hide collapse button.
	 * @param int|null $height        Separator height or null when unchanged.
	 * @return void
	 */
	private function save_menu_order( array $items, int $hide_collapse, ?int $height ): void {
		if ( is_array( $items ) ) {
			foreach ( $items as $slug => $item ) {
				$slug = sanitize_title( (string) $slug );
				$pos  = isset( $item['position'] ) ? absint( $item['position'] ) : 0;
				if ( $pos > 0 ) {
					update_option( ATWEAKS_DB_PREFIX . "_menu_position_$slug", $pos );
				}
			}
		}

		update_option( ATWEAKS_DB_PREFIX . '_menu_hide_collapse_menu', $hide_collapse );

		if ( null !== $height ) {
			if ( 5 === $height ) { // Yoda.
				delete_option( ATWEAKS_DB_PREFIX . '_menu_separator_height' );
			} else {
				update_option( ATWEAKS_DB_PREFIX . '_menu_separator_height', $height );
			}
		}

		atweaks_flush_transients();
	}

	/**
	 * Add a new separator entry.
	 *
	 * @return void
	 */
	private function add_separator(): void {
		$slug = 'separator_' . time();
		update_option( ATWEAKS_DB_PREFIX . "_menu_position_$slug", 0 );
		atweaks_redirect_with_message( 'menu', 'success', __( 'New separator has been added.', 'admin-tweak-suite' ) );
		exit;
	}

	/**
	 * Reset menu customizations.
	 *
	 * @return void
	 */
	private function reset_menu(): void {
		$deleted = atweaks_delete_options_by_prefix( ATWEAKS_DB_PREFIX . '_menu_' );
		if ( empty( $deleted ) ) {
			atweaks_redirect_with_message( 'menu', 'info', __( 'No custom menu settings found to reset.', 'admin-tweak-suite' ) );
		} else {
			atweaks_redirect_with_message( 'menu', 'success', __( 'Menu has been reset.', 'admin-tweak-suite' ) );
		}
		exit;
	}

	/**
	 * Apply custom menu order in admin.
	 *
	 * @return void
	 */
	public function apply_menu_order(): void {
		global $menu;

		$default_separators = array(
			'separator1'     => 4,
			'separator2'     => 59,
			'separator-last' => 99,
		);

		$menu_items  = array();
		$added_slugs = array();

		foreach ( $menu as $index => $item ) {
			$slug         = isset( $item[2] ) ? sanitize_title( (string) $item[2] ) : "separator_$index";
			$name         = isset( $item[0] ) ? wp_strip_all_tags( (string) $item[0] ) : __( 'Unknown', 'admin-tweak-suite' );
			$position     = (int) round( get_option( ATWEAKS_DB_PREFIX . "_menu_position_$slug", $index + 1 ) );
			$is_separator = ( 0 === strpos( $slug, 'separator' ) ); // Yoda.

			if ( ! in_array( $slug, $added_slugs, true ) ) {
				$menu_items[]  = array(
					'name'         => $is_separator ? __( 'Separator', 'admin-tweak-suite' ) : $name,
					'slug'         => $slug,
					'position'     => $position,
					'is_separator' => $is_separator,
					'item'         => $is_separator
						? array( '', 'read', $slug, '', 'wp-menu-separator' )
						: $item,
				);
				$added_slugs[] = $slug;
			}
		}

		foreach ( atweaks_get_option_names_by_prefix( ATWEAKS_DB_PREFIX . '_menu_position_separator_' ) as $sep ) {
			$slug     = str_replace( ATWEAKS_DB_PREFIX . '_menu_position_', '', $sep );
			$position = (int) round( get_option( $sep, 0 ) );
			if ( ! in_array( $slug, $added_slugs, true ) ) {
				$menu_items[]  = array(
					'name'         => __( 'Separator', 'admin-tweak-suite' ),
					'slug'         => $slug,
					'position'     => $position,
					'is_separator' => true,
					'item'         => array( '', 'read', $slug, '', 'wp-menu-separator' ),
				);
				$added_slugs[] = $slug;
			}
		}

		foreach ( $default_separators as $slug => $position ) {
			$exists = array_filter(
				$menu_items,
				static function ( $it ) use ( $slug ) {
					return $it['slug'] === $slug;
				}
			);
			if ( empty( $exists ) ) {
				$menu_items[] = array(
					'name'         => __( 'Separator', 'admin-tweak-suite' ),
					'slug'         => $slug,
					'position'     => (int) $position,
					'is_separator' => true,
					'item'         => array( '', 'read', $slug, '', 'wp-menu-separator' ),
				);
			}
		}

		usort(
			$menu_items,
			static function ( $a, $b ) {
				return (int) $a['position'] - (int) $b['position'];
			}
		);

		// Bygg nytt $menu i rätt ordning.
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Vi måste skriva till globalen $menu för att påverka admin-menyns ordning.
		$menu = array();
		foreach ( $menu_items as $item ) {
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Se ovan; vi bygger arrayen i ordning.
			$menu[] = $item['item'];
		}
	}
}
