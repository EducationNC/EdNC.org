<?php
/**
 * Facebook crawler ingestion admin class.
 *
 * @since 1.3.0
 * @package wp-native-articles
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sets up the Facebook Instant Article Crawler Ingestion.
 *
 * Registers a settings tab in the admin Facebook IA Page.
 *
 * @since  1.0.0
 */
class WPNA_Admin_Facebook_Crawler_Ingestion extends WPNA_Admin_Base implements WPNA_Admin_Interface {

	/**
	 * The slug of the current page.
	 *
	 * Used for registering menu items and tabs.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $page_slug = 'wpna_facebook';

	/**
	 * Hooks registered in this class.
	 *
	 * This method is auto called from WPNA_Admin_Base.
	 *
	 * @access public
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_init',               array( $this, 'setup_settings' ), 10, 0 );
		add_action( 'admin_init',               array( $this, 'setup_settings' ), 10, 0 );
		add_action( 'wpna_admin_facebook_tabs', array( $this, 'setup_tabs' ), 10, 1 );
		add_action( 'wp_head',                  array( $this, 'add_markup_meta_tag' ), 10, 0 );
		add_action( 'wp',                       array( $this, 'do_ia_version' ), 10, 0 );

		// Form sanitization filters.
		add_filter( 'wpna_sanitize_option_fbia_crawler_ingestion', 'wpna_switchval', 10, 1 );
	}

	/**
	 * Register Facebook feed settings.
	 *
	 * Uses the settings API to create and register all the settings fields in
	 * the Feed tab of the Facebook admin. Uses the global wpna_sanitize_options()
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

		// Group name. Used for nonces etc.
		$option_group = 'wpna_facebook-crawler-ingestion';

		register_setting( $option_group, 'wpna_options', 'wpna_sanitize_options' );

		add_settings_section(
			'wpna_facebook-crawler_ingestion_section_1',
			esc_html__( 'Crawler Ingestion', 'wp-native-articles' ),
			array( $this, 'section_1_callback' ),
			$option_group // This needs to be unique to this tab + Match the one called in do_settings_sections.
		);

		add_settings_field(
			'fbia_crawler_ingestion',
			'<label for="fbia_crawler_ingestion">' . esc_html__( 'Enable Crawler Ingestion', 'wp-native-articles' ) . '</label>',
			array( $this, 'crawler_ingestion_callback' ),
			$option_group,
			'wpna_facebook-crawler_ingestion_section_1'
		);

	}

	/**
	 * Registers a tab in the Facebook admin.
	 *
	 * Uses the tabs helper class.
	 *
	 * @access public
	 * @param object $tabs Tab helper class.
	 * @return void
	 */
	public function setup_tabs( $tabs ) {
		$tabs->register_tab(
			'crawler_ingestion',
			esc_html__( 'Crawler Ingestion', 'wp-native-articles' ),
			$this->page_url(),
			array( $this, 'crawler_ingestion_tab_callback' )
		);
	}

	/**
	 * Output the HTML for the crawler_ingestion tab.
	 *
	 * Uses the settings API and outputs the fields registered.
	 * settings_fields() requries the name of the group of settings to ouput.
	 * do_settings_sections() requires the unique page slug for this settings form.
	 *
	 * @access public
	 * @return void
	 */
	public function crawler_ingestion_tab_callback() {
		?>
		<form action="options.php" method="post">
			<?php settings_fields( 'wpna_facebook-crawler-ingestion' ); ?>
			<?php do_settings_sections( 'wpna_facebook-crawler-ingestion' ); ?>
			<?php

			submit_button();

			?>
		</form>
		<?php
	}

	/**
	 * Outputs the HTML displayed at the top of the settings section.
	 *
	 * @access public
	 * @return void
	 */
	public function section_1_callback() {
		?>
		<h4>
			<?php esc_html_e( 'A great way to quickly enable Instant Articles for every post on your site.', 'wp-native-articles' ); ?>
		</h4>
		<p>
			<?php printf(
				// translators: Placeholder is example code.
				esc_html__( 'The first time a post is shared on Facebook, the crawler will visit your post and check for the %s meta tag. If present, it will follow the link provided and ingest the Instant Article from content.', 'wp-native-articles' ),
				'<code>ia:markup_url</code>'
			); ?>
		</p>
		<p>
			<?php esc_html_e( 'Read more about it in the official documentation here:', 'wp-native-articles' ); ?>
			<a href="https://developers.facebook.com/docs/instant-articles/crawler-ingestion" target="_blank">https://developers.facebook.com/docs/instant-articles/crawler-ingestion</a>
		</p>
		<?php
	}

	/**
	 * Outputs the HTML for the 'fbia_crawler_ingestion' settings field.
	 *
	 * @access public
	 * @return void
	 */
	public function crawler_ingestion_callback() {
		?>
		<label for="fbia_crawler_ingestion">
			<input type="hidden" name="wpna_options[fbia_crawler_ingestion]" value="off">
<?php  ?>
<?php  ?>
			<input type="checkbox" name="wpna_options[fbia_crawler_ingestion]" id="fbia_crawler_ingestion" class="" value="on"<?php checked( wpna_switch_to_boolean( wpna_get_option( 'fbia_crawler_ingestion' ) ) ); ?> />
<?php  ?>
			<i><?php esc_html_e( 'Uses the environment set on the API tab.', 'wp-native-articles' ); ?></i>
		</label>

		<?php
		// Show a notice if the option has been overridden.
		wpna_option_overridden_notice( 'fbia_crawler_ingestion' );
		?>

		<?php
	}

	/**
	 * Outputs the ia:markup_url meta tag on single articles if enabled.
	 *
	 * @access public
	 * @return void
	 */
	public function add_markup_meta_tag() {
		// Only add to single pages.
		if ( ! is_single() ) {
			return;
		}

		$wp_post = get_post();

		// Ensure there's a post.
		if ( ! $wp_post ) {
			return;
		}

		$crawler_ingestion = wpna_get_option( 'fbia_crawler_ingestion' );

		// Ensure the feature is enabled.
		if ( ! wpna_switch_to_boolean( $crawler_ingestion ) ) {
			return;
		}

		// Check if this post should be converted.
		if ( ! wpna_should_convert_post_ia( $wp_post ) ) {
			return;
		}

		// Grab the post status.
		$post_status = get_post_meta( $wp_post->ID, '_wpna_fbia_status', true );

		// Check if the post should be published.
		// Check it exists first for backwards compatibility.
		if ( ! empty( $post_status ) && 'published' !== $post_status ) {
			return;
		}

		// Get the URL of the current post.
		$url = get_permalink( $wp_post );

		// Add the ia_markup query argument.
		$url = add_query_arg( 'wpna_ia_markup', 'true', $url );

		// Get the environment set.
		$environment = wpna_get_option( 'fbia_enviroment' );

		if ( 'production' === $environment ) {
			$meta_name = 'ia:markup_url';
		} else {
			$meta_name = 'ia:markup_url_dev';
		}

		// @codingStandardsIgnoreLine
		printf( PHP_EOL . '<meta property="%s" content="%s">' . PHP_EOL, esc_attr( $meta_name ), esc_url( $url ) );
	}

	/**
	 * Outputs the IA markup for an individual post if the query param is set.
	 *
	 * @access public
	 * @return void
	 */
	public function do_ia_version() {

		// @codingStandardsIgnoreLine
		if ( empty( $_GET['wpna_ia_markup'] ) ) {
			return;
		}

		// Only add to single pages.
		if ( ! is_single() ) {
			return;
		}

		$crawler_ingestion = wpna_get_option( 'fbia_crawler_ingestion' );

		// Ensure the feature is enabled.
		if ( ! wpna_switch_to_boolean( $crawler_ingestion ) ) {
			return;
		}

		// Get the global post.
		$post = get_post();

		// Ensure there's a post.
		if ( ! $post ) {
			return;
		}

		// Check if this post should be converted.
		if ( ! wpna_should_convert_post_ia( $post ) ) {
			return;
		}

		// Grab the post status.
		$fbia_status = get_post_meta( $post->ID, '_wpna_fbia_status', true );

		// Backwards compatibility. Removed in v1.5.0.
		if ( $deprecated_sync_articles = get_post_meta( $post->ID, '_wpna_fbia_sync_articles', true ) ) {
			// Remove the old post meta field.
			delete_post_meta( $post->ID, '_wpna_fbia_sync_articles' );

			// Workout the new status.
			$fbia_status = ( 'off' === $deprecated_sync_articles ? 'draft' : 'published' );

			// Save the new status.
			update_post_meta( $post->ID, '_wpna_fbia_status', $fbia_status );
		}

		// Backwards compatibility. Removed in v1.5.0.
		if ( $deprecated_import_as_draft = get_post_meta( $post->ID, '_wpna_fbia_import_as_drafts', true ) ) {
			// Remove the old post meta field.
			delete_post_meta( $post->ID, '_wpna_fbia_import_as_drafts' );

			// Workout the new status.
			$fbia_status = ( 'draft' === $deprecated_import_as_draft ? 'draft' : 'published' );

			// Save the new status.
			update_post_meta( $post->ID, '_wpna_fbia_status', $fbia_status );
		}

		// Check if the post should be published.
		// Check it's not empty first for backwards compatibility.
		if ( ! empty( $fbia_status ) && ( 'published' !== $fbia_status ) ) {
			return;
		}

		/**
		 * Runs before the crawler ingestion post is displayed.
		 *
		 * @since 1.3.3
		 * @var WP_Post $post The post getting shown.
		 */
		do_action( 'wpna_pre_crawler_ingestion_post', $post );

		// Generate the article and output the HTML.
		// @codingStandardsIgnoreLine
		echo wpna_get_fbia_post( $post );
		die;
	}

}
