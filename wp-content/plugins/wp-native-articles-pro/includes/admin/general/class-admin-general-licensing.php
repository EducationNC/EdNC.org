<?php
/**
 * Admin setup for General.
 *
 * @since  1.0.0
 * @package wp-native-articles
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Extends the Admin Base and adds the General page and related content.
 *
 * @since 1.0.0
 */
class WPNA_Admin_General_Licensing extends WPNA_Admin_Base implements WPNA_Admin_Interface {

	/**
	 * The slug of the current page.
	 *
	 * Used for registering menu items and tabs.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $page_slug = 'wpna_general';

	/**
	 * Hooks registered in this class.
	 *
	 * This method is auto called from WPNA_Admin_Base.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_init',                      array( $this, 'setup_settings' ), 10, 0 );
		add_action( 'wpna_admin_general_tabs',         array( $this, 'setup_tabs' ), 10, 1 );
		add_action( 'wpna_activate_license',           array( $this, 'activate_license_callback' ), 10, 0 );
		add_action( 'wpna_deactivate_license',         array( $this, 'deactivate_license_callback' ), 10, 0 );
		add_action( 'load-toplevel_page_wpna_general', array( $this, 'force_licese_check' ), 10, 0 );

		// Form sanitization filters.
		add_filter( 'wpna_sanitize_option_license_key', 'sanitize_text_field', 10, 1 );
	}

	/**
	 * Registers a new tab with the tab helper for the Admin General - Licensing page.
	 *
	 * @access public
	 * @param object $tabs Tab manager object.
	 * @return void
	 */
	public function setup_tabs( $tabs ) {
		$tabs->register_tab(
			'licensing',
			esc_html__( 'License', 'wp-native-articles' ),
			$this->page_url(),
			array( $this, 'tab_callback' ),
			true
		);
	}

	/**
	 * Register general Facebook settings.
	 *
	 * Uses the settings API to create and register all the settings fields in
	 * the General tab of the Facebook admin. Uses the global wpna_sanitize_options()
	 * function to provide validation hooks based on each field name.
	 *
	 * The settings API replaces the entire global settings object with the new
	 * values. wpna_sanitize_options() takes any other fields found in the global
	 * settings array that aren't registered here and merges them in to ensure
	 * they're not lost.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function setup_settings() {
		register_setting( 'wpna_general-licensing', 'wpna_license_key', array( $this, 'validate_license_callback' ) );

		add_settings_section(
			'wpna_general-licensing',
			esc_html__( 'Licensing', 'wp-native-articles' ),
			array( $this, 'wpna_general_licensing_callback' ),
			$this->page_slug
		);

		add_settings_field(
			'wpna_license_key',
			'<label for="wpna_license_key">' . esc_html__( 'License Key', 'wp-native-articles' ) . '</label>',
			array( $this, 'license_key_field_callback' ),
			$this->page_slug,
			'wpna_general-licensing'
		);
	}

	/**
	 * Output the HTML for the General tab.
	 *
	 * Nothing fancy here. Just HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function tab_callback() {
		?>
		<form action="options.php" method="post">
			<?php settings_fields( 'wpna_general-licensing' ); ?>
			<?php do_settings_sections( $this->page_slug ); ?>
			<?php submit_button(); ?>
		</form>
		<?php
	}

	/**
	 * Displays output before the licensing section.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function wpna_general_licensing_callback() {
		// Intentionally left blank.
	}

	/**
	 * Displays the output for the license_key field,
	 *
	 * A simple text input.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param  array $args Any additional arguments passed though from when
	 *                     the field was registered.
	 * @return void
	 */
	public function license_key_field_callback( $args ) {
		// If it's a multisite install direct them to the Network admin.
		if ( is_multisite() && is_plugin_active_for_network( plugin_basename( WPNA_BASE_PATH . '/wp-native-articles.php' ) ) ) :
		?>
		<input type="text" readonly="readonly" id="license_key" value="" class="regular-text">
		<p class="description"><?php esc_html_e( 'This is a WordPress Multisite install.', 'wp-native-articles' ); ?></p>
		<p class="description">
			<?php
			echo sprintf(
				wp_kses(
					// translators: Placeholder is a link to the multisite licence page.
					__( 'Your License Key can be managed on the Network Settings page <a href="%s">here</a>.', 'wp-native-articles' ),
					array(
						'a' => array(
							'href'   => array(),
							'target' => array(),
						),
					)
				),
				esc_url( network_admin_url( '/admin.php?page=wpna_multisite_license' ) )
			);
			?>
		</p>
		<?php else :
			$license = get_option( 'wpna_license_key' );
			$status  = get_option( 'wpna_license_status' );
		?>
			<input type="text" id="license_key" name="wpna_license_key" value="<?php echo esc_attr( $license ); ?>" class="regular-text">
			<p class="description"><?php esc_html_e( 'Your WP Native Articles License Key.', 'wp-native-articles' ); ?></p>
			<p class="description"><?php esc_html_e( 'This is required for access to new updates, features and security.', 'wp-native-articles' ); ?></p>
			<p class="description">
				<?php
				echo sprintf(
					wp_kses(
						// translators: Placeholder is a link to their account page.
						__( 'You can find it on your account page <a href="%s" target="_blank">here</a>.', 'wp-native-articles' ),
						array(
							'a' => array(
								'href'   => array(),
								'target' => array(),
							),
						)
					),
					esc_url( 'https://wp-native-articles.com/account/#license' )
				);
				?>
			</p>

			<?php
			// If a license has been saved show the status of it.
			if ( false !== $license ) : ?>

				<tr valign="top">
					<th scope="row" valign="top">
						<?php esc_html_e( 'Activate License', 'wp-native-articles' ); ?>
					</th>
					<td>
						<?php
						// Active license. Show Deactivate button.
						if ( 'valid' === $status ) : ?>
							<span style="color:green;margin: 4px 5px 0 0;font-weight: bold;display:inline-block;"><?php esc_html_e( 'Active', 'wp-native-articles' ); ?></span>
							<?php wp_nonce_field( 'wpna_license-deactivate', '__wpna_license_nonce' ); ?>
							<input type="hidden" name="wpna-action" value="deactivate_license" />
							<input type="submit" class="button-secondary" name="deactivate_license" value="<?php esc_html_e( 'Deactivate License', 'wp-native-articles' ); ?>"/>
						<?php elseif ( 'expired' === $status ) : ?>
							<?php
							$renewal_url = esc_url(
								add_query_arg(
									array(
										'edd_license_key' => $license,
										'download_id'     => 48,
									),
									'https://wp-native-articles.com/checkout'
								)
							);
							?>
							<a target="_blank" href="<?php echo esc_url( $renewal_url ); ?>" class="button-primary"><?php esc_html_e( 'Renew Your License', 'wp-native-articles' ); ?></a>
							<p><span style="color:red;"><?php esc_html_e( 'Your license has expired, renew today to continue getting updates and support!', 'wp-native-articles' ); ?></span></p>
						<?php
						// Inactive license. Show Activate button.
						else : ?>
							<span style="color:black;margin: 4px 5px 0 0;font-weight: bold;display:inline-block;"><?php esc_html_e( 'Inactive', 'wp-native-articles' ); ?></span>
							<?php wp_nonce_field( 'wpna_license-activate', '__wpna_license_nonce' ); ?>
							<input type="hidden" name="wpna-action" value="activate_license" />
							<input type="submit" class="button-secondary" name="activate_license" value="<?php esc_html_e( 'Activate License', 'wp-native-articles' ); ?>"/>
						<?php endif; ?>
					</td>
				</tr>
			<?php endif; ?>

		<?php
		endif;
	}

	/**
	 * Activates a license so the site can recieve updates.
	 *
	 * Uses the global helper function to validate the license against the
	 * remote server. Redirects back to the license settings page with any
	 * appropriate notices.
	 *
	 * @return void
	 */
	public function activate_license_callback() {

		// Check the form has been submitted.
		if ( ! filter_input( INPUT_POST, 'activate_license', FILTER_SANITIZE_STRING ) ) {
			return;
		}

		// Check user has the correct permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Misleading name, validate nonce.
		check_admin_referer( 'wpna_license-activate', '__wpna_license_nonce' );

		// Get the licecne to check.
		$license = get_option( 'wpna_license_key' );

		// Check the license against the remote server.
		$license_details = wpna_activate_license( $license );

		// License is invalid for some reason.
		if ( 'invalid' === $license_details['status'] || ! empty( $license_details['message'] ) ) {

			// Redirect back with a notice flag.
			wp_safe_redirect(
				add_query_arg(
					array(
						'page'         => $this->page_slug,
						'tab'          => 'licensing',
						'wpna-message' => 'license_activate_error',
						'message'      => rawurlencode( $license_details['message'] ),
					),
					admin_url( 'admin.php' )
				)
			);

			exit;
		}

		// License is valid.
		update_option( 'wpna_license_status', $license_details['status'] );

		// Redirect back with a notice flag.
		wp_safe_redirect(
			add_query_arg(
				array(
					'page'         => $this->page_slug,
					'tab'          => 'licensing',
					'wpna-message' => 'license_activate_success',
				),
				admin_url( 'admin.php' )
			)
		);

		exit;
	}

	/**
	 * Activates a license so the site can recieve updates.
	 *
	 * Uses the global helper function to validate the license against the
	 * remote server. Redirects back to the license settings page with any
	 * appropriate notices.
	 *
	 * @return void
	 */
	public function deactivate_license_callback() {

		// Check the form has been submitted.
		if ( ! filter_input( INPUT_POST, 'deactivate_license', FILTER_SANITIZE_STRING ) ) {
			return;
		}

		// Check user has the correct permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Misleading name, validate nonce.
		check_admin_referer( 'wpna_license-deactivate', '__wpna_license_nonce' );

		// Get the license to check.
		$license = get_option( 'wpna_license_key' );

		// Check the license against the remote server.
		$license_details = wpna_deactivate_license( $license );

		// License is invalid for some reason.
		if ( 'invalid' === $license_details['status'] || ! empty( $license_details['message'] ) ) {

			// Redirect back with a notice flag.
			wp_safe_redirect(
				add_query_arg(
					array(
						'page'         => $this->page_slug,
						'tab'          => 'licensing',
						'wpna-message' => 'wpna_deactivate_license_error',
						'message'      => rawurlencode( $license_details['message'] ),
					),
					admin_url( 'admin.php' )
				)
			);

			exit;
		}

		// License is valid.
		delete_option( 'wpna_license_status' );

		// Redirect back with a notice flag.
		wp_safe_redirect(
			add_query_arg(
				array(
					'page'         => $this->page_slug,
					'tab'          => 'licensing',
					'wpna-message' => 'license_deactivate_success',
				),
				admin_url( 'admin.php' )
			)
		);

		exit;
	}

	/**
	 * Force a license recheck on this page.
	 *
	 * @since 1.4.0
	 *
	 * @access public
	 * @return void
	 */
	public function force_licese_check() {
		wpna_license_check( true );
	}

	/**
	 * Validate a new site license.
	 *
	 * If the new license is different to the old license then clear the
	 * license status param.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param  string $new The new license key.
	 * @return string
	 */
	public function validate_license_callback( $new ) {

		$old = get_option( 'wpna_license_key' );

		if ( $old && $old !== $new ) {
			// New license has been entered, must reactivate & get new status.
			delete_option( 'wpna_license_status' );
		}

		$new = sanitize_text_field( $new );

		return $new;
	}

}
