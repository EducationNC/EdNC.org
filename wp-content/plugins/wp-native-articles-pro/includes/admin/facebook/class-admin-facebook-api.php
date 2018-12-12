<?php
/**
 * Facebook api admin class.
 *
 * @since 1.0.0
 * @package wp-native-articles
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sets up the Facebook Instant Article API integration.
 *
 * Registers a settings tab in the admin Facebook IA Page.
 * Registers widgets on the dashboard and post to display stats from the API.
 * Syncs articles between Facebook and WP using the API.
 *
 * @todo Split dashboard & post widgets into seperate classes
 *
 * == FB FLOW ==
 * @todo check FB allowed permissions
 * @todo check got access token
 *
 * @since  1.0.0
 */
class WPNA_Admin_Facebook_API extends WPNA_Admin_Base implements WPNA_Admin_Interface {

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
	 * The Facebook class SDK.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var object
	 */
	public $facebook = null;

	/**
	 * The ID of the authorised Facebook user.
	 *
	 * @since 1.1.6
	 * @access public
	 * @var int|null
	 */
	public $facebook_user_id = null;

	/**
	 * The user permissions to ask for from Facebook.
	 *
	 * @since 1.1.6
	 * @access public
	 * @var array Permissions to ask for.
	 */
	public $permissions_scope = array( 'pages_manage_instant_articles', 'pages_show_list', 'read_insights' );

	/**
	 * Hooks registered in this class.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_init',               array( $this, 'setup_settings' ), 10, 0 );
		add_action( 'wpna_facebook_login',      array( $this, 'facebook_login' ), 10, 0 );
		add_action( 'wpna_facebook_logout',     array( $this, 'facebook_logout' ), 10, 0 );
		add_action( 'wpna_admin_facebook_tabs', array( $this, 'setup_tabs' ), 10, 1 );
		add_action( 'load-native-articles_page_' . $this->page_slug,  array( $this, 'facebook' ), 15, 0 );

		// These actions are only applied if Instant Articles is enabled.
		if ( wpna_switch_to_boolean( wpna_get_option( 'fbia_enable' ) ) ) {

			add_action( 'admin_enqueue_scripts',     array( $this, 'scripts' ), 10, 1 );
			add_action( 'admin_enqueue_scripts',     array( $this, 'styles' ), 10, 1 );
			add_action( 'wp_dashboard_setup',        array( $this, 'register_dashboard_widget' ), 10, 0 );
			add_action( 'admin_footer-post.php',     array( $this, 'post_meta_box_import_errors_template' ), 10, 0 );
			add_action( 'admin_footer-post-new.php', array( $this, 'post_meta_box_import_errors_template' ), 10, 0 );
			add_action( 'wp_ajax_wpna-dashboard-widget-facebook-stats',      array( $this, 'ajax_dashboard_widget_facebook_stats' ), 10, 0 );
			add_action( 'wp_ajax_wpna-post-meta-box-facebook-stats',         array( $this, 'ajax_post_meta_box_facebook_stats' ), 10, 0 );
			add_action( 'wp_ajax_wpna-post-meta-box-facebook-import-status', array( $this, 'ajax_post_meta_box_facebook_import_status' ), 10, 0 );
		}

		add_action( 'save_post',          array( $this, 'schedule_instant_article_sync' ), 999, 3 );
		add_action( 'wpna_article_sync',  array( $this, 'instant_article_sync' ), 10, 1 );
		add_action( 'trash_post',         array( $this, 'instant_article_delete' ), 10, 1 );
		add_action( 'before_delete_post', array( $this, 'instant_article_delete' ), 10, 1 );

		// Form sanitization filters.
		add_filter( 'wpna_sanitize_option_fbia_app_id',        'sanitize_text_field', 10, 1 );
		add_filter( 'wpna_sanitize_option_fbia_app_secret',    'sanitize_text_field', 10, 1 );
		add_filter( 'wpna_sanitize_option_fbia_page_id',       'sanitize_text_field', 10, 1 );
		add_filter( 'wpna_sanitize_option_fbia_sync_articles', 'wpna_switchval', 10, 1 );
		add_filter( 'wpna_sanitize_option_fbia_environment',    array( $this, 'sanitize_fbia_environment' ), 10, 1 );

		// Add tabs to post edit screen.
		add_filter( 'wpna_post_meta_box_content_tabs', array( $this, 'post_meta_box_facebook_analytics' ), 5, 1 );
		add_filter( 'wpna_post_meta_box_content_tabs', array( $this, 'post_meta_box_facebook_status' ), 15, 1 );
	}

	/**
	 * Load admin JS files.
	 *
	 * Targets the new & edit posts screens and the dashboard and loads in the javascript
	 * required for setting up the meta boxes and stats widgets.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param  string $hook The current page hook.
	 * @return void
	 */
	public function scripts( $hook ) {
		// Register the script but don't enqueue it yet.
		wp_register_script( 'chart-js', plugins_url( '/assets/js/Chart-custom.min.js', WPNA_BASE_FILE ), null, '2.6.0', true );

		// JS for dashboard meta box reporting.
		if ( 'index.php' === $hook ) {
			wp_enqueue_script( 'wpna-dashboard-widget', plugins_url( '/assets/js/dashboard-widget.js', WPNA_BASE_FILE ), array( 'chart-js', 'jquery', 'wp-util' ), WPNA_VERSION, true );

			wp_localize_script( 'wpna-dashboard-widget', 'wpnaDashboardWidget', array(
				'nonce'        => wp_create_nonce( 'wpna_dashboard_widget_ajax_nonce' ),
				'errorMessage' => esc_html__( 'An error occured. Please ensure the Facebook API is connected', 'wp-native-articles' ),
			));
		}

		// JS for post meta box analytics.
		if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			if ( in_array( get_post_type(), wpna_allowed_post_types(), true ) ) {
				wp_enqueue_script( 'wpna-post-analytics',  plugins_url( '/assets/js/post-analytics.js', WPNA_BASE_FILE ), array( 'chart-js', 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'wp-util' ), WPNA_VERSION, true );

				wp_localize_script( 'wpna-post-analytics', 'wpnaPostAnalytics', array(
					'nonce'        => wp_create_nonce( 'wpna_post_ajax_nonce' ),
					'post_id'      => get_the_ID(),
					'errorMessage' => esc_html__( 'An error occured. Please ensure the Facebook API is connected', 'wp-native-articles' ),
				));
			}
		}
	}

	/**
	 * Load admin CSS files.
	 *
	 * Targets the new and edit posts screens and loads in the CSS for jQuery
	 * datepicker.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param  string $hook Page name the hook is being called on.
	 * @return void
	 */
	public function styles( $hook ) {
		if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			if ( in_array( get_post_type(), wpna_allowed_post_types(), true ) ) {
				wp_enqueue_style( 'jquery-ui-datepicker', plugins_url( '/assets/css/jquery-ui.min.css', WPNA_BASE_FILE ), null, '1.11.4' );
			}
		}
	}

	/**
	 * Registers the dashboard widget for the WordPress landing page.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function register_dashboard_widget() {
		wp_add_dashboard_widget(
			'wpna-facebook-instant-articles',
			esc_html__( 'Facebook Instant Articles Insights', 'wp-native-articles' ),
			array( $this, 'dashboard_widget_callback' )
		);
	}

	/**
	 * Output the  HTML for dashboard widget.
	 *
	 * For performance resons the dashboard widget is lazily constructed using JS.
	 * This is just a loading spinner and canvas element to enable that.
	 *
	 * If you wish to modify this, just deregister the widget and re-register
	 * your own with the same content.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function dashboard_widget_callback() {
		?>
		<div class="main">
			<div class="wpna-loading-spinner" style="width: auto; text-align: center;">
				<style type='text/css'>@-webkit-keyframes uil-default-anim { 0% { opacity: 1} 100% {opacity: 0} }@keyframes uil-default-anim { 0% { opacity: 1} 100% {opacity: 0} }.uil-default-css > div:nth-of-type(1){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: -0.5s;animation-delay: -0.5s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}.uil-default-css > div:nth-of-type(2){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: -0.4166666666666667s;animation-delay: -0.4166666666666667s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}.uil-default-css > div:nth-of-type(3){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: -0.33333333333333337s;animation-delay: -0.33333333333333337s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}.uil-default-css > div:nth-of-type(4){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: -0.25s;animation-delay: -0.25s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}.uil-default-css > div:nth-of-type(5){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: -0.16666666666666669s;animation-delay: -0.16666666666666669s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}.uil-default-css > div:nth-of-type(6){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: -0.08333333333333331s;animation-delay: -0.08333333333333331s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}.uil-default-css > div:nth-of-type(7){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: 0s;animation-delay: 0s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}.uil-default-css > div:nth-of-type(8){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: 0.08333333333333337s;animation-delay: 0.08333333333333337s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}.uil-default-css > div:nth-of-type(9){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: 0.16666666666666663s;animation-delay: 0.16666666666666663s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}.uil-default-css > div:nth-of-type(10){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: 0.25s;animation-delay: 0.25s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}.uil-default-css > div:nth-of-type(11){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: 0.33333333333333337s;animation-delay: 0.33333333333333337s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}.uil-default-css > div:nth-of-type(12){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: 0.41666666666666663s;animation-delay: 0.41666666666666663s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}</style><div class='uil-default-css' style='transform:scale(0.18);'><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(0deg) translate(0,-60px);transform:rotate(0deg) translate(0,-60px);border-radius:10px;position:absolute;'></div><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(30deg) translate(0,-60px);transform:rotate(30deg) translate(0,-60px);border-radius:10px;position:absolute;'></div><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(60deg) translate(0,-60px);transform:rotate(60deg) translate(0,-60px);border-radius:10px;position:absolute;'></div><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(90deg) translate(0,-60px);transform:rotate(90deg) translate(0,-60px);border-radius:10px;position:absolute;'></div><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(120deg) translate(0,-60px);transform:rotate(120deg) translate(0,-60px);border-radius:10px;position:absolute;'></div><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(150deg) translate(0,-60px);transform:rotate(150deg) translate(0,-60px);border-radius:10px;position:absolute;'></div><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(180deg) translate(0,-60px);transform:rotate(180deg) translate(0,-60px);border-radius:10px;position:absolute;'></div><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(210deg) translate(0,-60px);transform:rotate(210deg) translate(0,-60px);border-radius:10px;position:absolute;'></div><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(240deg) translate(0,-60px);transform:rotate(240deg) translate(0,-60px);border-radius:10px;position:absolute;'></div><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(270deg) translate(0,-60px);transform:rotate(270deg) translate(0,-60px);border-radius:10px;position:absolute;'></div><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(300deg) translate(0,-60px);transform:rotate(300deg) translate(0,-60px);border-radius:10px;position:absolute;'></div><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(330deg) translate(0,-60px);transform:rotate(330deg) translate(0,-60px);border-radius:10px;position:absolute;'></div></div>
			</div>

			<canvas id="wpna-facebook-analytics-chart"></canvas>

		</div>
		<?php
	}

	/**
	 * Gets stats for the dashboard admin widget.
	 *
	 * If the API is connected it pings it for stats, transform them into a
	 * sutible structure then outputs as JSON. This AJAX method is only
	 * available on the admin side.
	 *
	 * @since 1.0.0
	 * @todo Make FB data transform generic.
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_dashboard_widget_facebook_stats() {

		// Check it's an AJAX request.
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			wp_die();
		}

		// Check the nonce is valid.
		check_ajax_referer( 'wpna_dashboard_widget_ajax_nonce' );

		if ( is_wp_error( $this->facebook() ) ) {
			wp_send_json_error( esc_html__( 'Facebook API not connected', 'wp-native-articles' ) );
		}

		// Get stats.
		$page_id = wpna_get_option( 'fbia_page_id' );

		// Gotta be PHP 5.2 before you get all fancy.
		$from  = strtotime( '-7 days' );
		$until = strtotime( 'now' );

		// Try and grab the analytics from Facebook.
		try {
			$fb_data = $this->facebook()->api( "/v2.11/{$page_id}?fields=instant_articles_insights.metric(all_views).period(day).since({$from}).until({$until}).breakdown(platform)" );
		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}

		// Labels, values & formatting for the X axis.
		$labels = array();

		$ios_values = array(
			'label'           => esc_html__( 'iOS', 'wp-native-articles' ),
			'backgroundColor' => 'rgba(255,99,132,0.2)',
			'borderColor'     => 'rgba(255,99,132,1)',
			'borderWidth'     => 1,
			'data'            => array(),
		);

		$android_values = array(
			'label'           => esc_html__( 'Android', 'wp-native-articles' ),
			'backgroundColor' => 'rgba(0,115,170,0.2)',
			'borderColor'     => 'rgba(0,115,170,1)',
			'borderWidth'     => 1,
			'data'            => array(),
		);

		// Create an empty array to hold the the vales.
		$values = array(
			'IOS'     => array(),
			'ANDROID' => array(),
		);

		// I think Facebook starts the day at 08:00:00 so our from date is actually
		// one day less as they parse it.
		$current = strtotime( '+1 day', $from );

		// Loop over the difference between the dates and set default values.
		while ( $current <= $until ) {

			// Format the label with the date.
			$labels[] = date_i18n( 'j M y', $current );

			// Add 0 as the default value for the platform breakdown.
			// Add the date as the key so the correct value can be set from
			// the FB data.
			$values['IOS'][ date( 'Y-m-d', $current ) ]     = 0;
			$values['ANDROID'][ date( 'Y-m-d', $current ) ] = 0;

			$current = strtotime( '+1 day', $current );
		}

		// If Facebook data was returned add it into the values.
		if ( ! empty( $fb_data['instant_articles_insights'] ) ) {
			foreach ( $fb_data['instant_articles_insights']['data'] as $row ) {

				// Format the FB date into the same format as the keys above.
				// For some reason FB returns an ISO 8601 with the time set to 08:00.
				$time = date( 'Y-m-d', strtotime( $row['time'] ) );

				// Update the values.
				$values[ $row['breakdowns']['platform'] ][ $time ] = absint( $row['value'] );
			}
		}

		// Strip the keys and set the values.
		$ios_values['data']     = array_values( $values['IOS'] );
		$android_values['data'] = array_values( $values['ANDROID'] );

		// Structure the response.
		$stats = array(
			'labels'   => $labels,
			'datasets' => array( $ios_values, $android_values ),
		);

		/**
		 * Modify the data before returning to the dashboard widget.
		 *
		 * @since 1.0.0
		 * @var array  $stats The data to return.
		 * @var array  $fb_data Original FB data.
		 * @var object $this->facebook() The FB api class.
		 */
		$stats = apply_filters( 'wpna_dashboard_widget_facebook_stats', $stats, $fb_data, $this->facebook() );

		wp_send_json_success( $stats );
	}

	/**
	 * Register the Facebook analytics tab for use in the post meta box.
	 *
	 * Just a filter that enables modification of the $tabs array.
	 * Would be better switched to a function.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param  array $tabs Existing tabs.
	 * @return array
	 */
	public function post_meta_box_facebook_analytics( $tabs ) {

		$tabs[] = array(
			'key'      => 'fbia_analytics',
			'title'    => esc_html__( 'Analytics', 'wp-native-articles' ),
			'callback' => array( $this, 'post_meta_box_facebook_analytics_callback' ),
		);

		return $tabs;
	}

	/**
	 * Output HTML for the Facebook analytics post meta box tab.
	 *
	 * Ads filter boxes at the top (metric, time period etc) then ads in
	 * a loading spinner and canvas element afterwards. Stats are loaded in
	 * via javascript.
	 *
	 * Only shows the canvas for published posts.
	 *
	 * @since 1.0.0
	 * @todo Check Facebook is connected
	 * @todo Hooks and filters
	 * @todo Compare to aggregated results
	 * @todo Information about each metric
	 *
	 * @access public
	 * @param WP_Post $post Global post object.
	 * @return void
	 */
	public function post_meta_box_facebook_analytics_callback( $post ) {
		?>
		<h3><?php esc_html_e( 'Instant Article Analytics', 'wp-native-articles' ); ?></h3>

		<div class="wpna-filter-wrapper">

			<label>
				<?php esc_html_e( 'Since', 'wp-native-articles' ); ?>
				<input type="text" data-wpna-datepicker="true" data-wpna-redraw-analytics="true" id="wpna-fbia-analytics-since" value="<?php echo esc_attr( date( 'Y-m-01' ) ); ?>" />
			</label>

			<label>
				<?php esc_html_e( 'Until', 'wp-native-articles' ); ?>
				<input type="text" data-wpna-datepicker="true" data-wpna-redraw-analytics="true" id="wpna-fbia-analytics-until" value="<?php echo esc_attr( date( 'Y-m-j' ) ); ?>" />
			</label>

			<label>
				<?php esc_html_e( 'Metric', 'wp-native-articles' ); ?>
				<select data-wpna-redraw-analytics="true" id="wpna-fbia-analytics-metric">
					<option value="all_views"><?php esc_html_e( 'All Views', 'wp-native-articles' ); ?></option>
					<option value="all_view_durations"><?php esc_html_e( 'All View Durations', 'wp-native-articles' ); ?></option>
					<option value="all_view_durations_average"><?php esc_html_e( 'All View Durations Average', 'wp-native-articles' ); ?></option>
					<option value="all_scrolls"><?php esc_html_e( 'All Scrolls', 'wp-native-articles' ); ?></option>
					<option value="all_scrolls_average"><?php esc_html_e( 'All Scrolls Average', 'wp-native-articles' ); ?></option>
				</select>
			</label>

			<?php
			/**
			 * Output more filter metrics if you like.
			 *
			 * @since 1.0.0
			 * @param WP_Post $post The current post.
			 */
			do_action( 'wpna_post_meta_box_facebook_analytics_filters', $post );
			?>

		</div>

		<?php if ( 'publish' !== get_post_status() ) : ?>
			<i><?php esc_html_e( 'A post has to be published before we can get stats', 'wp-native-articles' ); ?></i>
		<?php else : ?>

			<div class="wpna-loading-spinner" style="width: auto; text-align: center;">
				<style type='text/css'>@-webkit-keyframes uil-default-anim { 0% { opacity: 1} 100% {opacity: 0} }@keyframes uil-default-anim { 0% { opacity: 1} 100% {opacity: 0} }.uil-default-css > div:nth-of-type(1){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: -0.5s;animation-delay: -0.5s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}.uil-default-css > div:nth-of-type(2){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: -0.4166666666666667s;animation-delay: -0.4166666666666667s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}.uil-default-css > div:nth-of-type(3){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: -0.33333333333333337s;animation-delay: -0.33333333333333337s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}.uil-default-css > div:nth-of-type(4){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: -0.25s;animation-delay: -0.25s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}.uil-default-css > div:nth-of-type(5){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: -0.16666666666666669s;animation-delay: -0.16666666666666669s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}.uil-default-css > div:nth-of-type(6){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: -0.08333333333333331s;animation-delay: -0.08333333333333331s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}.uil-default-css > div:nth-of-type(7){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: 0s;animation-delay: 0s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}.uil-default-css > div:nth-of-type(8){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: 0.08333333333333337s;animation-delay: 0.08333333333333337s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}.uil-default-css > div:nth-of-type(9){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: 0.16666666666666663s;animation-delay: 0.16666666666666663s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}.uil-default-css > div:nth-of-type(10){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: 0.25s;animation-delay: 0.25s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}.uil-default-css > div:nth-of-type(11){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: 0.33333333333333337s;animation-delay: 0.33333333333333337s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}.uil-default-css > div:nth-of-type(12){-webkit-animation: uil-default-anim 1s linear infinite;animation: uil-default-anim 1s linear infinite;-webkit-animation-delay: 0.41666666666666663s;animation-delay: 0.41666666666666663s;}.uil-default-css { display:inline-block; position: relative;background:none;width:35px;height:35px;}</style><div class='uil-default-css' style='transform:scale(0.18);'><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(0deg) translate(0,-60px);transform:rotate(0deg) translate(0,-60px);border-radius:10px;position:absolute;'></div><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(30deg) translate(0,-60px);transform:rotate(30deg) translate(0,-60px);border-radius:10px;position:absolute;'></div><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(60deg) translate(0,-60px);transform:rotate(60deg) translate(0,-60px);border-radius:10px;position:absolute;'></div><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(90deg) translate(0,-60px);transform:rotate(90deg) translate(0,-60px);border-radius:10px;position:absolute;'></div><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(120deg) translate(0,-60px);transform:rotate(120deg) translate(0,-60px);border-radius:10px;position:absolute;'></div><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(150deg) translate(0,-60px);transform:rotate(150deg) translate(0,-60px);border-radius:10px;position:absolute;'></div><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(180deg) translate(0,-60px);transform:rotate(180deg) translate(0,-60px);border-radius:10px;position:absolute;'></div><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(210deg) translate(0,-60px);transform:rotate(210deg) translate(0,-60px);border-radius:10px;position:absolute;'></div><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(240deg) translate(0,-60px);transform:rotate(240deg) translate(0,-60px);border-radius:10px;position:absolute;'></div><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(270deg) translate(0,-60px);transform:rotate(270deg) translate(0,-60px);border-radius:10px;position:absolute;'></div><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(300deg) translate(0,-60px);transform:rotate(300deg) translate(0,-60px);border-radius:10px;position:absolute;'></div><div style='top:80px;left:93px;width:14px;height:40px;background:#ff6384;-webkit-transform:rotate(330deg) translate(0,-60px);transform:rotate(330deg) translate(0,-60px);border-radius:10px;position:absolute;'></div></div>
			</div>

			<canvas id="wpna-fbia-chart"></canvas>

		<?php endif; ?>

		<?php
	}

	/**
	 * Gets stats for the post meta widget.
	 *
	 * If the API is connected it pings it for stats, transform them into a
	 * sutible structure then outputs as JSON. This AJAX method is only
	 * available on the admin side.
	 *
	 * @since 1.0.0
	 * @todo Make FB data transform generic
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_post_meta_box_facebook_stats() {

		// Check it's an AJAX request.
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			wp_die();
		}

		// Check the nonce is valid.
		check_ajax_referer( 'wpna_post_ajax_nonce' );

		if ( is_wp_error( $this->facebook() ) ) {
			wp_send_json_error( esc_html__( 'Facebook API not connected', 'wp-native-articles' ) );
		}

		// Get the post ID.
		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );

		if ( ! $post_id ) {
			wp_send_json_error( esc_html__( 'Invalid post ID', 'wp-native-articles' ) );
		}

		// @todo Duplicate of select. Move to class params.
		$metrics = array( 'all_views', 'all_view_durations', 'all_view_durations_average', 'all_scrolls', 'all_scrolls_average' );

		// Setup the variables needed.
		$since = $until = $metric = null;

		if ( $since = filter_input( INPUT_POST, 'since', FILTER_SANITIZE_STRING ) ) {
			$since = sanitize_text_field( wp_unslash( $since ) );
		}

		if ( $until = filter_input( INPUT_POST, 'until', FILTER_SANITIZE_STRING ) ) {
			$until = sanitize_text_field( wp_unslash( $until ) );
		}

		if ( $metric = filter_input( INPUT_POST, 'metric', FILTER_SANITIZE_STRING ) ) {
			$metric = sanitize_text_field( wp_unslash( $metric ) );
		}

		// Setup params for the FB call.
		// https://developers.facebook.com/docs/graph-api/reference/v2.11/instant-article-insights.
		$canonical_url = get_permalink( $post_id );

		$since  = wpna_valid_date( $since ) ? $since : strtotime( date( 'Y-m-01' ) );
		$until  = wpna_valid_date( $until ) ? $until : strtotime( 'now' );
		$metric = in_array( $metric, $metrics, true ) ? $metric : 'all_views';
		$period = 'all_views' === $metric ? 'day' : 'week';

		try {
			$fb_data = $this->facebook()->api( "/v2.11/?fields=instant_article{insights.metric({$metric}).period({$period}).since({$since}).until({$until}).breakdown(platform)}&id={$canonical_url}" );
		} catch ( Exception $e ) {
			$error = $e;
		}

		$labels = array();

		$ios_values = array(
			'label'           => esc_html__( 'iOS', 'wp-native-articles' ),
			'backgroundColor' => 'rgba(255,99,132,0.2)',
			'borderColor'     => 'rgba(255,99,132,1)',
			'borderWidth'     => 1,
			'data'            => array(),
		);

		$android_values = array(
			'label'           => esc_html__( 'Android', 'wp-native-articles' ),
			'backgroundColor' => 'rgba(0,115,170,0.2)',
			'borderColor'     => 'rgba(0,115,170,1)',
			'borderWidth'     => 1,
			'data'            => array(),
		);

		// Create an empty array to hold the the vales.
		$values = array(
			'IOS'     => array(),
			'ANDROID' => array(),
		);

		// I think Facebook starts the day at 08:00:00 so our from date is actually
		// one day less as they parse it.
		$current = strtotime( '+1 day', strtotime( $since ) );

		$until = strtotime( $until );

		// Loop over the difference between the dates and set default values.
		while ( $current <= $until ) {

			// Format the label with the date.
			$labels[] = date_i18n( 'j M y', $current );

			// Add 0 as the default value for the platform breakdown.
			// Add the date as the key so the correct value can be set from
			// the FB data.
			$values['IOS'][ date( 'Y-m-d', $current ) ]     = 0;
			$values['ANDROID'][ date( 'Y-m-d', $current ) ] = 0;

			$current = strtotime( '+1 day', $current );
		}

		// If Facebook data was returned add it into the values.
		if ( isset( $fb_data['instant_article']['insights']['data'] ) ) {
			foreach ( $fb_data['instant_article']['insights']['data'] as $row ) {

				// Format the FB date into the same format as the keys above.
				// For some reason FB returns an ISO 8601 with the time set to 08:00.
				$time = date( 'Y-m-d', strtotime( $row['time'] ) );

				// Update the values.
				$values[ $row['breakdowns']['platform'] ][ $time ] = absint( $row['value'] );
			}
		}

		// Strip the keys and set the values.
		$ios_values['data']     = array_values( $values['IOS'] );
		$android_values['data'] = array_values( $values['ANDROID'] );

		// Structure the response.
		$stats = array(
			'labels'   => $labels,
			'datasets' => array( $ios_values, $android_values ),
		);

		/**
		 * Modify the data before returning to the posts widget.
		 *
		 * @since 1.0.0
		 * @var array  $stats The data to return.
		 * @var array  $fb_data Original FB data.
		 * @var object $this->facebook() The FB api class.
		 */
		$stats = apply_filters( 'wpna_post_meta_box_facebook_stats', $stats, $fb_data, $this->facebook() );

		wp_send_json_success( $stats );
	}

	/**
	 * Register the Facebook stats tab for use in the post meta box.
	 *
	 * Just a filter that enables modification of the $tabs array.
	 * Would be better switched to a function.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param  array $tabs Existing tabs.
	 * @return array
	 */
	public function post_meta_box_facebook_status( $tabs ) {

		$tabs[] = array(
			'key'      => 'fbia_status',
			'title'    => esc_html__( 'Status', 'wp-native-articles' ),
			'callback' => array( $this, 'post_meta_box_facebook_status_callback' ),
		);

		return $tabs;
	}

	/**
	 * Output HTML for the Facebook status post meta box tab.
	 *
	 * Just a heading. Stats are loaded in via javascript.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param  WP_Post $post The post currently being edited.
	 * @return void
	 */
	public function post_meta_box_facebook_status_callback( $post ) {
		?>
		<h3><?php esc_html_e( 'Instant Article Status', 'wp-native-articles' ); ?></h3>

		<?php
		/**
		 * Add extra fields using this action. Or deregister this method
		 * altogether and register your own.
		 *
		 * @since 1.2.3
		 */
		do_action( 'wpna_post_meta_box_facebook_status_footer' );
	}

	/**
	 * Output the HTML for the import errors template.
	 *
	 * The template used to display any import errors about a post. Used on the
	 * 'Stats' tab. Uses the WordPress javascript template lib.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function post_meta_box_import_errors_template() {
		?>
		<script type="text/html" id="tmpl-wpna-fbia-import-status">
			<h4>
				<?php esc_html_e( 'Status:', 'wp-native-articles' ); ?>
				<span class="wpna-{{ data.status.toLowerCase() }}">{{ data.status }}</span>
			</h4>

			<# if ( data.errors ) { #>

				<table class="pure-table pure-table-horizontal" style="table-layout:fixed;width:100%;border-collapse: collapse;">
					<thead>
						<tr>
							<th style="width:70px;"><?php esc_html_e( 'Level', 'wp-native-articles' ); ?></th>
							<th style="width:auto;"><?php esc_html_e( 'Message', 'wp-native-articles' ); ?></th>
						</tr>
					</thead>

					<tbody>
						<# _.each(data.errors, function(error) { #>
						<tr>
							<td style="width:70px;">
								<span class="wpna-{{ error.level.toLowerCase() }}">
								{{ error.level }}
								</span>
							</td>
							<td style="width:auto;word-wrap:break-word;">{{ error.message }}</td>
						</tr>
						<# }) #>
					</tbody>
				</table>

			<# } #>
		</script>
		<?php
	}

	/**
	 * Retrives the status of an article in Facebook instant article.
	 *
	 * Gets an article status (including errors) from Facebook and outputs it
	 * as JSON.
	 *
	 * n.b. You can only get an article's status if you have an import ID. This
	 * is different to the article ID and only generated when an article is
	 * updated or created.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_post_meta_box_facebook_import_status() {
		// Check it's an AJAX request.
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			wp_die();
		}

		// Check the nonce is valid.
		check_ajax_referer( 'wpna_post_ajax_nonce' );

		if ( is_wp_error( $this->facebook() ) ) {
			wp_send_json_error( esc_html__( 'Facebook API not connected', 'wp-native-articles' ) );
		}

		// Get the post ID.
		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );

		if ( ! $post_id ) {
			wp_send_json_error( esc_html__( 'Invalid post ID', 'wp-native-articles' ) );
		}

		$facebook_api  = new WPNA_Facebook_API( $post_id );
		$import_status = $facebook_api->get_instant_article_import_status();

		if ( is_wp_error( $import_status ) ) {
			wp_send_json_error( $import_status->get_error_message() );
		}

		/**
		 * Modify the data before returning to the posts widget.
		 *
		 * @since 1.0.0
		 * @var array  $import_status The status of the article (including errors).
		 * @var array  $post_id       The WP ID of the post to get the import status for.
		 */
		$import_status = apply_filters( 'wpna_post_meta_box_facebook_import_status', $import_status, $post_id );

		$import_status['html_source']                    = null;
		$import_status['instant_article']['html_source'] = null;

		wp_send_json_success( $import_status );
	}

	/**
	 * Checks whether to update an article in the background via CRON or call
	 * the update function immediately.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 * @param int  $post_id The post ID.
	 * @param post $wp_post The post object.
	 * @param bool $update Whether this is an existing post being updated or not.
	 * @return void
	 */
	public function schedule_instant_article_sync( $post_id, $wp_post, $update ) {

		// Don't bother if autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$fbia_enabled = wpna_get_option( 'fbia_enable' );

		// If FBIA functionality is disabled then bail.
		if ( ! wpna_switch_to_boolean( $fbia_enabled ) ) {
			return;
		}

		// If API functionality is disabled then bail.
		$sync_article = wpna_get_option( 'fbia_sync_articles' );

		// If sync is disabled globally or on a per-post basis then don't bother.
		if ( ! wpna_switch_to_boolean( $sync_article ) ) {
			return;
		}

		// If CRON sync is enabled schedule the CRON event.
		if ( wpna_get_option( 'fbia_sync_cron' ) ) {

			// Schedule the sync to run in the background.
			if ( ! wp_next_scheduled( 'wpna_article_sync', array( $post_id ) ) ) {
				wp_schedule_single_event( time(), 'wpna_article_sync', array( $post_id ) );
			}

			// Ping the cron to ensure it runs.
			// Only if WP CRON is enabled (some custom CRONS need it disabled).
			if ( ! defined( 'DISABLE_WP_CRON' ) || ! DISABLE_WP_CRON ) {
				if ( function_exists( 'vip_safe_wp_remote_get' ) ) {
					vip_safe_wp_remote_get(
						get_home_url( get_current_blog_id(), 'wp-cron.php' ), false, 1, 1, 10, array( 'blocking' => false )
					);
				} else {
					// @codingStandardsIgnoreStart
					wp_remote_get(
						get_home_url( get_current_blog_id(), 'wp-cron.php' ),
						array( 'blocking' => false )
					);
					// @codingStandardsIgnoreEnd
				}
			}
		} else {
			// Else update the post straight away.
			$this->instant_article_sync( $wp_post );
		}
	}

	/**
	 * Syncs the post with Facebook.
	 *
	 * When a published article is updated or created it syncs it with Facebook
	 * using the AP if it's authorized.
	 *
	 * The article get's syncd if:
	 *   - if aricle sync is enabled in the settings.
	 *   - if it's not a revision or autosave.
	 *
	 * Uses the WPNA_Facebook_Post() class to transform the post content to be
	 * instant article friendly.
	 *
	 * * Formally 'sync_instant_article'.
	 *
	 * @since 1.0.0
	 * @todo Log errors better
	 *
	 * @access public
	 * @param mixed $wp_post The ID or post object of the post to sync.
	 * @return void
	 */
	public function instant_article_sync( $wp_post ) {

		// Check if this post should be converted.
		if ( ! wpna_should_convert_post_ia( $wp_post ) ) {
			return;
		}

		// If the API isn't connected do nothing.
		if ( is_wp_error( $this->facebook() ) ) {
			return;
		}

		$facebook_api = new WPNA_Facebook_API( $wp_post );
		$facebook_api->sync();
	}

	/**
	 * Deletes posts from Facebook.
	 *
	 * When a post is trashed or deleted make sure it's removed in Facebook.
	 * To stop this deregister this function from 'trash_post' and 'before_delete_post'.
	 *
	 * Formally 'delete_instant_article'.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param  int $post_id ID of the post to delete.
	 * @return void
	 */
	public function instant_article_delete( $post_id ) {

		$fbia_enabled = wpna_get_option( 'fbia_enable' );

		// If FBIA functionality is disabled then bail.
		if ( ! wpna_switch_to_boolean( $fbia_enabled ) ) {
			return;
		}

		$sync_article = wpna_get_post_option( $post_id, 'fbia_sync_articles', 'on' );

		// If sync is disabled globally or on a per-post basis then don't bother.
		if ( ! wpna_switch_to_boolean( $sync_article ) ) {
			return;
		}

		// If the API isn't connected do nothing.
		if ( is_wp_error( $this->facebook() ) ) {
			return;
		}

		$facebook_api = new WPNA_Facebook_API( $post_id );
		$facebook_api->delete();
	}

	/**
	 * Register Facebook api settings.
	 *
	 * Uses the settings API to create and register all the settings fields in
	 * the API tab of the Facebook admin. Uses the global wpna_sanitize_options()
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
		$option_group = 'wpna_facebook-api';

		register_setting( $option_group, 'wpna_options', 'wpna_sanitize_options' );

		add_settings_section(
			'wpna_facebook-api_section_1',
			esc_html__( 'Facebook Auth', 'wp-native-articles' ),
			array( $this, 'section_1_callback' ),
			$option_group // This needs to be unique to this tab + Match the one called in do_settings_sections.
		);

		add_settings_field(
			'fbia_app_id',
			'<label for="fbia_app_id">' . esc_html__( 'App ID', 'wp-native-articles' ) . '</label>',
			array( $this, 'app_id_callback' ),
			$option_group,
			'wpna_facebook-api_section_1'
		);

		add_settings_field(
			'fbia_app_secret',
			'<label for="fbia_app_secret">' . esc_html__( 'App Secret', 'wp-native-articles' ) . '</label>',
			array( $this, 'app_secret_callback' ),
			$option_group,
			'wpna_facebook-api_section_1'
		);

		add_settings_field(
			'fbia_fb_user',
			esc_html__( 'Facebook User', 'wp-native-articles' ),
			array( $this, 'fb_user_callback' ),
			$option_group,
			'wpna_facebook-api_section_1'
		);

		add_settings_field(
			'fbia_page_id',
			'<label for="fbia_page_id">' . esc_html__( 'Page ID', 'wp-native-articles' ) . '</label>',
			array( $this, 'page_id_callback' ),
			$option_group,
			'wpna_facebook-api_section_1'
		);

		// DOESN'T SAVE ANYTHING
		// Just using the hook.
		add_settings_field(
			'fbia_status',
			esc_html__( 'Facebook Status', 'wp-native-articles' ),
			array( $this, 'status_callback' ),
			$option_group,
			'wpna_facebook-api_section_1'
		);

		add_settings_field(
			'fbia_sync_articles',
			'<label for="fbia-sync-articles">' . esc_html__( 'Sync Articles', 'wp-native-articles' ) . '</label>',
			array( $this, 'sync_articles_callback' ),
			$option_group,
			'wpna_facebook-api_section_1'
		);

		add_settings_field(
			'fbia_sync_cron',
			'<label for="fbia-sync-cron">' . esc_html__( 'CRON Sync', 'wp-native-articles' ) . '</label>',
			array( $this, 'sync_cron_callback' ),
			$option_group,
			'wpna_facebook-api_section_1'
		);

		add_settings_field(
			'fbia_enviroment',
			'<label for="fbia-environment">' . esc_html__( 'Environment', 'wp-native-articles' ) . '</label>',
			array( $this, 'environment_callback' ),
			$option_group,
			'wpna_facebook-api_section_1'
		);

		add_settings_field(
			'fbia_import_as_drafts',
			'<label for="fbia-import-as-drafts">' . esc_html__( 'Import As Draft', 'wp-native-articles' ) . '</label>',
			array( $this, 'fbia_import_as_drafts_callback' ),
			$option_group,
			'wpna_facebook-api_section_1'
		);

	}

	/**
	 * Registers a tab in the Facebook admin.
	 *
	 * Uses the tabs helper class.
	 *
	 * @access public
	 * @param object $tabs Tabs helper class.
	 * @return void
	 */
	public function setup_tabs( $tabs ) {
		$tabs->register_tab(
			'api',
			esc_html__( 'API', 'wp-native-articles' ),
			$this->page_url(),
			array( $this, 'api_tab_callback' )
		);
	}

	/**
	 * Output the HTML for the API tab.
	 *
	 * Uses the settings API and outputs the fields registered.
	 * settings_fields() requries the name of the group of settings to ouput.
	 * do_settings_sections() requires the unique page slug for this settings form.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function api_tab_callback() {
		?>
		<form action="options.php" method="post">
			<?php settings_fields( 'wpna_facebook-api' ); ?>
			<?php do_settings_sections( 'wpna_facebook-api' ); ?>
			<?php submit_button(); ?>
		</form>
		<?php
	}

	/**
	 * Outputs the HTML displayed at the top of the settings section.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function section_1_callback() {
		?>
		<p>
			<?php esc_html_e( 'These settings apply to the Facebook API.', 'wp-native-articles' ); ?>
			<br />
			<?php esc_html_e( 'Unlike the RSS feed the API is in real time, articles will be updated on Facebook the same time they are updated on WordPress.', 'wp-native-articles' ); ?>
		</p>
		<?php
	}

	/**
	 * Outputs the HTML for the 'fbia_app_id' settings field.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function app_id_callback() {
		?>
		<input type="text" name="wpna_options[fbia_app_id]" id="fbia_app_id" class="regular-text" value="<?php echo esc_attr( wpna_get_option( 'fbia_app_id' ) ); ?>">
		<p class="description"><?php esc_html_e( 'Your Facebook App ID', 'wp-native-articles' ); ?></p>

		<?php
		// Show a notice if the option has been overridden.
		wpna_option_overridden_notice( 'fbia_app_id' );
		?>

		<?php
	}

	/**
	 * Outputs the HTML for the 'fbia_app_secret' settings field.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function app_secret_callback() {
		?>
		<input type="text" autocomplete="off" name="wpna_options[fbia_app_secret]" id="fbia_app_secret" class="regular-text" value="<?php echo esc_attr( wpna_get_option( 'fbia_app_secret' ) ); ?>">
		<p class="description"><?php esc_html_e( 'Your Facebook App Secret', 'wp-native-articles' ); ?></p>

		<?php
		// Show a notice if the option has been overridden.
		wpna_option_overridden_notice( 'fbia_app_secret' );
		?>

		<?php
	}

	/**
	 * Outputs the HTML for connecting to Facebook.
	 *
	 * - If no app ID or App secret is set show and error.
	 * - If a Facebook user has been successfully auth'd shows a logout button and
	 * their profile picture.
	 * - If a Facebook user hasn't been auth'd shows a Login button.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function fb_user_callback() {
		if ( is_wp_error( $this->facebook() ) ) {
			return;
		}

		// Check to see if a user has been auth'd with Facebook.
		// Don't use the facebook_user_id() function as we want to check permissions
		// manually here.
		if ( $user_id = $this->facebook()->getUser() ) : ?>

			<?php
				$logout_params = array(
					'access_token' => $this->facebook()->getAccessToken(),
					'next'         => $this->page_url(
						array(
							'tab'         => 'api',
							'wpna-action' => 'facebook_logout',
						)
					),
				);
			?>

			<div>
				<img src="<?php echo esc_url( sprintf( 'https://graph.facebook.com/%s/picture', $user_id ) ); ?>">
				<a href="<?php echo esc_url( $this->facebook()->getLogoutUrl( $logout_params ) ); ?>" class="button button-secondary">
					<?php esc_html_e( 'Logout', 'wp-native-articles' ); ?>
				</a>
			</div>

			<?php
			$permissions_missing = null;

			// Check the user has granted the correct permissions.
			try {
				// Get the actual permissions the user has granted.
				$permissions = $this->facebook()->api( "/{$user_id}/permissions" );

				if ( ! empty( $permissions['data'] ) ) {

					// These are the permissions required.
					$permissions_missing = array_flip( $this->permissions_scope );

					// Loop through the granted permissions and check all the ones
					// required have actually been granted.
					foreach ( $permissions['data'] as $permission ) {
						if ( isset( $permissions_missing[ $permission['permission'] ] ) && 'granted' === $permission['status'] ) {
							unset( $permissions_missing[ $permission['permission'] ] );
						}
					}
				}
			} catch ( Exception $e ) {
				// An error occurred. Format it and store it.
				$error = new WP_Error( 'wpna_facebook_api_permissions_error', $e->getMessage() );
				wpna_add_facebook_notification( $error );
			}

			if ( $permissions_missing ) :
				?>
				<div style="margin-top: 15px;">
					<p>
						<span class="wpna-label wpna-label-warning"><?php esc_html_e( 'Warning', 'wp-native-articles' ); ?></span>
						<i><b><?php esc_html_e( 'Missing Facebook Permissions', 'wp-native-articles' ); ?></b></i>
					</p>
					<p><?php esc_html_e( 'Facebook has been authorized but not all the required permissions have been enabled, this means some features may not work. The following permissions are missing, you can re-authorise Facebook using the button below.', 'wp-native-articles' ); ?></p>
					<p>
						<?php foreach ( $permissions_missing as $permission => $value ) : ?>
							<b><?php echo esc_html( $permission ); ?></b><br />
						<?php endforeach; ?>
					</p>
						<?php
						$login_params = array(
							'redirect_uri' => $this->page_url(
								array(
									'tab'         => 'api',
									'wpna-action' => 'facebook_login',
								)
							),
							'scope'        => implode( ',', $this->permissions_scope ),
							'auth_type'    => 'rerequest',
						);
						?>
					<p>
						<a href="<?php echo esc_url( $this->facebook()->getLoginUrl( $login_params ) ); ?>" class="button button-primary">
							<?php esc_html_e( 'Reauthorize', 'wp-native-articles' ); ?>
						</a>
					</p>
				</div>
				<?php
			endif;

			// No user auth'd, show a Login button.
			else : ?>

			<?php
			$login_params = array(
				'redirect_uri' => $this->page_url(
					array(
						'tab'         => 'api',
						'wpna-action' => 'facebook_login',
					)
				),
				'scope'        => implode( ',', $this->permissions_scope ),
			);
			?>

			<a href="<?php echo esc_url( $this->facebook()->getLoginUrl( $login_params ) ); ?>" class="button button-primary">
				<?php esc_html_e( 'Login', 'wp-native-articles' ); ?>
			</a>

			<?php
				// If they've tried to Auth but come across an error code.
				$error_code = filter_input( INPUT_GET, 'error_code', FILTER_SANITIZE_STRING );
			?>

			<?php if ( $error_code ) : ?>

				<?php
				// 1349048 -> Strict Mode disabled but URL not included
				// 1349168 -> Strict Mode nabled but URL not included
				if ( in_array( $error_code, array( '1349048', '1349168' ), true ) ) : ?>
					<p style="margin-top: 20px;"><span class="wpna-label wpna-label-warning"><?php esc_html_e( 'Warning', 'wp-native-articles' ); ?></span><p>
					<p><?php esc_html_e( 'Your domain is not included in your Valid OAuth redirect URIs.', 'wp-native-articles' ); ?></p>
					<p>
						<?php echo wp_kses(
							sprintf(
								// translators: Placement is the ID of their Facebook page.
								__( 'Please go to your <a href="https://developers.facebook.com/apps/%d/fb-login/" target="_blank">Facebook App</a> and add the URL below to the <b>Valid OAuth redirect URIs</b> field.', 'wp-native-articles' ),
								wpna_get_option( 'fbia_app_id' )
							),
							array(
								'b' => true,
								'a' => array(
									'href'   => true,
									'target' => true,
								),
							)
						); ?>
					</p>
					<p>
						<code style="font-size: 10px;">
							<?php echo esc_url(
								$this->page_url(
									array(
										'tab'         => 'api',
										'wpna-action' => 'facebook_login',
									)
								)
							); ?>
						</code>
					</p>
				<?php endif ?>

			<?php endif; ?>

		<?php endif;
	}

	/**
	 * Outputs the HTML for the 'fbia_page_id' settings field.
	 *
	 * If Facebook has been auth'd shows a dropdown select of all Facebook
	 * pages the authorised user has permissions on. If not shows and error
	 * message.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function page_id_callback() {
		// If Facebook isn't connected or there's no user bail early.
		if ( is_wp_error( $this->facebook_user_id() ) ) {
			return;
		}

		$pages_managed = null;

		// Get the pages managed by the user.
		try {
			// Get the logged in user.
			$user_id = $this->facebook_user_id();

			// Don't bother with pagination. Pass an unlikley big number.
			$pages_managed = $this->facebook()->api( "/{$user_id}/accounts?limit=9999999&summary=total_count" );

			// Check we've got a response.
			if ( ! empty( $pages_managed['data'] ) ) {
				// Order alphabetically.
				usort( $pages_managed['data'], array( $this, 'order_by_name' ) );
			}
		} catch ( Exception $e ) {
			// An error occurred. Format it and store it.
			$error = new WP_Error( 'wpna_facebook_api_page_count_error', $e->getMessage() );
			wpna_add_facebook_notification( $error );
		}

		?>

		<?php if ( $pages_managed ) : ?>

			<select name="wpna_options[fbia_page_id]" id="fbia_page_id">
				<option></option>
				<?php foreach ( $pages_managed['data'] as $page ) : ?>
				<option value="<?php echo esc_attr( $page['id'] ); ?>" <?php selected( wpna_get_option( 'fbia_page_id' ), $page['id'] ); ?>><?php echo esc_html( $page['name'] ); ?></option>
				<?php endforeach; ?>
			</select>

			<?php // translators: Placeholder is the number of Facebook Pages found. ?>
			<p class="description"><?php printf( esc_html( _n( '%s page found', '%s pages found', absint( $pages_managed['summary']['total_count'] ), 'wp-native-articles' ) ), absint( $pages_managed['summary']['total_count'] ) ); ?></p>

		<?php else : ?>
			<p class="description"><?php echo esc_html__( 'No pages found. Please check your permissions.', 'wp-native-articles' ); ?></p>
		<?php endif; ?>

		<?php
	}

	/**
	 * Output the Facebook connection status.
	 *
	 * Doesn't actually save anything, just outputs the connection status with
	 * a total article count for the selected page.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function status_callback() {

		// Check Facebook is connected.
		if ( is_wp_error( $this->facebook_user_id() ) ) : ?>

			<p>
				<span class="wpna-label wpna-label-danger">
					<?php esc_html_e( 'Disconnected', 'wp-native-articles' ); ?>
				</span>
			</p>

		<?php else : ?>

			<p>
				<span class="wpna-label wpna-label-success">
					<?php esc_html_e( 'Connected', 'wp-native-articles' ); ?>
				</span>
			</p>

			<?php
			// Check there's a page ID to sync articles to.
			if ( ! $page_id = wpna_get_option( 'fbia_page_id' ) ) : ?>

				<p class="description">
					<strong><?php esc_html_e( 'Missing Page ID.', 'wp-native-articles' ); ?></strong>
					<?php esc_html_e( 'Please select a Facebook Page to sync articles to.', 'wp-native-articles' ); ?>
				</p>

			<?php else : ?>

				<?php
				$instant_articles_count = 0;

				try {
					$articles = $this->facebook()->api( "/{$page_id}/instant_articles?limit=0&summary=total_count" );

					// Get instant articles count for page.
					if ( isset( $articles['summary']['total_count'] ) ) {
						$instant_articles_count = $articles['summary']['total_count'];
					}
				} catch ( Exception $e ) {
					// An error occurred. Format it and store it.
					$error = new WP_Error( 'wpna_facebook_api_ia_count_error', $e->getMessage() );

					wpna_add_facebook_notification( $error );
				}
				?>

				<?php // translators: Placeholder is the nuber of Instant Articles found. ?>
				<p class="description"><?php printf( esc_html( _n( '%s Instant Article found on this page', '%s Instant Articles found on this page', absint( $instant_articles_count ), 'wp-native-articles' ) ), absint( $instant_articles_count ) ); ?>

			<?php endif; ?>

		<?php endif; ?>

		<?php
	}

	/**
	 * Outputs the HTML for the 'fbia_sync_articles' settings field.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function sync_articles_callback() {
		?>
		<label for="fbia-sync-articles">
			<select name="wpna_options[fbia_sync_articles]" id="fbia-sync-articles">
				<option value="off"<?php selected( wpna_get_option( 'fbia_sync_articles' ), 'off' ); ?>><?php esc_html_e( 'Disabled', 'wp-native-articles' ); ?></option>
				<option value="on"<?php selected( wpna_get_option( 'fbia_sync_articles' ), 'on' ); ?>><?php esc_html_e( 'Enabled', 'wp-native-articles' ); ?></option>
			</select>
			<?php esc_html_e( 'Auto publish, update & delete Instant Articles in sync with WordPress posts', 'wp-native-articles' ); ?>
		</label>

		<?php
		// Show a notice if the option has been overridden.
		wpna_option_overridden_notice( 'fbia_sync_articles' );
		?>

		<?php
	}

	/**
	 * Outputs the HTML for the 'fbia_sync_cron' settings field.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 * @return void
	 */
	public function sync_cron_callback() {
		?>
		<label for="fbia-sync-cron">
			<input type="hidden" name="wpna_options[fbia_sync_cron]" value="0">
			<input type="checkbox" name="wpna_options[fbia_sync_cron]" id="fbia-sync-cron" class="" value="true"<?php checked( (bool) wpna_get_option( 'fbia_sync_cron' ) ); ?> />
			<?php esc_html_e( 'Use background CRON to sync posts. Can speed up post saving time.', 'wp-native-articles' ); ?>
		</label>

		<?php
		// Show a notice if the option has been overridden.
		wpna_option_overridden_notice( 'fbia_sync_cron' );
		?>

		<?php
	}

	/**
	 * Outputs the HTML for the 'fbia_environment' settings field.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function environment_callback() {
		?>
		<label for="fbia-environment">
			<select name="wpna_options[fbia_enviroment]" id="fbia-environment">
				<option value="development"<?php selected( wpna_get_option( 'fbia_enviroment' ), 'development' ); ?>><?php esc_html_e( 'Development', 'wp-native-articles' ); ?></option>
				<option value="production"<?php selected( wpna_get_option( 'fbia_enviroment' ), 'production' ); ?>><?php esc_html_e( 'Production', 'wp-native-articles' ); ?></option>
			</select>
		</label>

		<?php
		// Show a notice if the option has been overridden.
		wpna_option_overridden_notice( 'fbia_environment' );
		?>

		<?php
	}

	/**
	 * Outputs the HTML for the 'fbia_import_as_drafts' settings field.
	 *
	 * @since 1.2.3
	 *
	 * @access public
	 * @return void
	 */
	public function fbia_import_as_drafts_callback() {
		$instant_articles_count = false;

		try {
			$page_id = wpna_get_option( 'fbia_page_id' );

			if ( $page_id ) {
				$articles = $this->facebook()->api( "/{$page_id}/instant_articles?limit=0&summary=total_count" );
				// Get instant articles count for page.
				if ( isset( $articles['summary']['total_count'] ) ) {
					$instant_articles_count = $articles['summary']['total_count'];
				}
			}
		// @codingStandardsIgnoreLine
		} catch ( Exception $e ) {
			// Blank.
		}

		?>
		<label for="fbia-import-as-drafts">
				<input type="hidden" name="wpna_options[fbia_import_as_drafts]" value="0">
				<input type="checkbox" name="wpna_options[fbia_import_as_drafts]" id="fbia-import-as-drafts" class="" value="true"<?php checked( (bool) wpna_get_option( 'fbia_import_as_drafts' ) ); ?> />
				<?php esc_html_e( 'Only applies to the API method. Forces all articles to be imported as draft.', 'wp-native-articles' ); ?>
		</label>

		<?php if ( $instant_articles_count && $instant_articles_count < 10 ) : ?>
			<p class="description">
				<?php esc_html_e( 'You currently have fewer than 10 Instant Articles on your Facebook Page.', 'wp-native-articles' ); ?>
				<br />
				<?php esc_html_e( 'This means you can only import posts as “drafts” into your Production environment as your Facebook page has not been through the review process and approved for Instant Articles.', 'wp-native-articles' ); ?>
			</p>
			<br />
			<p class="description">
				<?php echo wp_kses(
					sprintf(
						// translators: Placeholder is a link to the Post Syncer Page.
						__( 'You can use the <a href="%s">Mass Post Syncer</a> to quickly convert all your old WordPress posts to Instant articles.', 'wp-native-articles' ),
						esc_url(
							add_query_arg(
								array(
									'page' => 'wpna_facebook',
									'tab'  => 'post_syncer',
								),
								admin_url( '/admin.php' )
							)
						)
					),
					array(
						'b' => true,
						'a' => array(
							'href'   => true,
							'target' => true,
						),
					)
				); ?>
			</p>
		<?php endif; ?>

		<?php
		// Show a notice if the option has been overridden.
		wpna_option_overridden_notice( 'fbia_import_as_drafts' );
		?>

		<?php
	}

	/**
	 * Facebook login action.
	 *
	 * After a user has authorised an account save the access token.
	 * Swaps the standard access token for a long life one and saves it in
	 * transients for 60 days (the length of time it is valid for but it gets
	 * extended everytime the it is used).
	 *
	 * @since 1.4.0
	 *
	 * @access public
	 * @return void
	 */
	public function facebook_login() {

		// Setup default query args.
		$query_args = array(
			'page' => 'wpna_facebook',
			'tab'  => 'api',
		);

		// Check if there's an error code.
		$error_code = filter_input( INPUT_GET, 'error_code', FILTER_SANITIZE_STRING );

		if ( $error_code ) {
			// Forward the Facebook Error code as well.
			// Used to display specific messages.
			$query_args['error_code'] = $error_code;

			// Set the error notification.
			$query_args['wpna-message'] = 'facebook_api_login_error';

			wp_safe_redirect( esc_url_raw( add_query_arg( $query_args, admin_url( '/admin.php' ) ) ) );
			exit;

		} else {

			$this->facebook = new WPNA_Facebook( array(
				'appId'          => wpna_get_option( 'fbia_app_id' ),
				'secret'         => wpna_get_option( 'fbia_app_secret' ),
				'trustForwarded' => true,
			));

			// Tell Facebook we want an extended one.
			$this->facebook->setExtendedAccessToken();

			// Generate a new access token,and set it.
			if ( $access_token = $this->facebook->getAccessToken() ) {
				set_transient( 'wpna_fb_access_token', $access_token, 60 * DAY_IN_SECONDS );

				$this->facebook->setAccessToken( $access_token );
			}

			// Check for any logged errors.
			$facebook_errors = wpna_get_facebook_notifications();

			if ( $facebook_errors ) {

				// Format each facebook error into a settings error.
				foreach ( $facebook_errors as $error ) {
					add_settings_error( 'general', $error->get_error_code(), sprintf( '%s (%s)', $error->get_error_message(), $error->get_error_code() ), 'error' );
				}

				// Save to the transient.
				set_transient( 'wpna_notices', get_settings_errors(), 30 );

				// Set the general error notification.
				$query_args['wpna-message'] = 'facebook_api_login_error';

			} else {
				// Set the success notification.
				$query_args['wpna-message'] = 'facebook_api_login_success';
			}
		}

		// Redirect with notifications.
		wp_safe_redirect( esc_url_raw( add_query_arg( $query_args, admin_url( '/admin.php' ) ) ) );
		exit;
	}

	/**
	 * Facebook logout action.
	 *
	 * Destroys the Facebook session when the user disconnects using the logout
	 * button in the admin.
	 *
	 * @since 1.4.0
	 *
	 * @access public
	 * @return void
	 */
	public function facebook_logout() {
		// Destroy the Facebook session.
		$this->facebook()->destroySession();

		// Remove the access token.
		delete_transient( 'wpna_fb_access_token' );

		// Redirect with success notification.
		wp_safe_redirect(
			esc_url_raw(
				add_query_arg(
					array(
						'page'         => 'wpna_facebook',
						'tab'          => 'api',
						'wpna-message' => 'facebook_api_logout_success',
					),
					admin_url( '/admin.php' )
				)
			)
		);

		exit;
	}

	/**
	 * Creates and initializes the Facebook class.
	 *
	 * If the App ID and App secret are set it creates a new instance of the
	 * Facebook class. If a long live access token has been create it uses that
	 * to authorise the app.
	 *
	 * On the settings page actions are used to initialize this early on in the
	 * execution order. This is because it uses SESSIONS and it avoids the
	 * "Headers Already Sent" error. All other times it can just be lazy loaded
	 * when needed.
	 *
	 * todo: Move to WPNA_Facebook_API class.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return Facebook|WP_Error
	 */
	public function facebook() {
		// If we've already set it up just return that.
		if ( $this->facebook ) {
			return $this->facebook;
		} elseif (
			// Set an error in no app_id defined.
			! wpna_get_option( 'fbia_app_id' )
		) {
			$this->facebook = new WP_Error( 'wpna_facebook_api_invalid_credentials', esc_html__( 'Missing Facebook app ID', 'wp-native-articles' ) );
		} elseif (
			// Set an error in no app_secret defined.
			! wpna_get_option( 'fbia_app_secret' )
		) {
			$this->facebook = new WP_Error( 'wpna_facebook_api_invalid_credentials', esc_html__( 'Missing Facebook app secret', 'wp-native-articles' ) );
		} else {
			// Initialize Facebook.
			$this->facebook = new WPNA_Facebook( array(
				'appId'          => wpna_get_option( 'fbia_app_id' ),
				'secret'         => wpna_get_option( 'fbia_app_secret' ),
				'trustForwarded' => true,
			));

			// If there's a long life access token then set it.
			if ( $access_token = get_transient( 'wpna_fb_access_token' ) ) {
				$this->facebook->setAccessToken( $access_token );
			}
		}

		return $this->facebook;
	}

	/**
	 * Get's the logged in Facebook user.
	 *
	 * Checks to make sure Facebook is connected successfully and that the
	 * user has all the correct permissions. If it does it returns the user id.
	 * Returns any errors as WP_Error class.
	 *
	 * @return WP_Error|int
	 */
	public function facebook_user_id() {
		// If we've already set it up just return that.
		if ( $this->facebook_user_id ) {
			return $this->facebook_user_id;
		}

		// First check Facebook has been setup.
		if ( is_wp_error( $this->facebook() ) ) {
			return $this->facebook();
		}

		// Return false if the user couldn't be auth'd.
		if ( ! $user_id = $this->facebook()->getUser() ) {
			return new WP_Error( 'wpna_facebook_api_invalid_credentials', esc_html__( 'User could not be authenticated', 'wp-native-articles' ) );
		}

		// These are the permissions required.
		$permissions_to_check = array_flip( $this->permissions_scope );

		// These are the permissions granted.
		try {
			// Try and get the permissions.
			$permissions = $this->facebook()->api( "/{$user_id}/permissions" );

			if ( ! empty( $permissions['data'] ) ) {
				// Loop through all the permissions.
				foreach ( $permissions['data'] as $permission ) {
					// Check if they've been granted.
					if ( isset( $permissions_to_check[ $permission['permission'] ] ) && 'granted' === $permission['status'] ) {
						unset( $permissions_to_check[ $permission['permission'] ] );
					}
				}
			}

			// We have missing permissions, return false.
			if ( ! empty( $permissions_to_check ) ) {
				return new WP_Error( 'wpna_facebook_api_invalid_credentials', esc_html__( 'Missing Facebook permissions', 'wp-native-articles' ) );
			}
		} catch ( Exception $e ) {
			return new WP_Error( 'wpna_facebook_api_invalid_credentials', $e->getMessage() );
		}

		$this->facebook_user_id = $user_id;

		return $this->facebook_user_id;
	}

	/**
	 * Sanitizes the environment variable.
	 *
	 * A custom validation method for the environment field.
	 * Ensures it matches either 'production' or 'development'.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param  string $input The input string to sanitize.
	 * @return string
	 */
	public function sanitize_fbia_environment( $input ) {
		return 'production' === $input ? 'production' : 'development';
	}

	/**
	 * Used for ordering Facebook pages by name for the select box.
	 *
	 * @param  array $a First value to compare.
	 * @param  array $b Second value to compare.
	 * @return array
	 */
	public function order_by_name( $a, $b ) {
		return strcmp( $a['name'], $b['name'] );
	}
}
