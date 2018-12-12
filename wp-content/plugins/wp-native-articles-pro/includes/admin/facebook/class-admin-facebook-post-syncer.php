<?php
/**
 * Facebook Post Syncer admin class.
 * Shoutout to Pippin for writing the code this was absed off.
 *
 * @link https://pippinsplugins.com/batch-processing-for-big-data/
 *
 * @since 1.4.0
 * @package wp-native-articles
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sets up the Facebook Instant Article Post Syncer class.
 *
 * Registers a settings tab in the admin Facebook IA Page.
 *
 * @since  1.0.0
 */
class WPNA_Admin_Facebook_Post_Syncer extends WPNA_Admin_Base implements WPNA_Admin_Interface {

	/**
	 * The slug of the current page.
	 *
	 * Used for registering menu items and tabs.
	 *
	 * @access public
	 * @var string
	 */
	public $page_slug = 'wpna_facebook';

	/**
	 * The current batch of post being acted upon.
	 *
	 * @access public
	 * @var int
	 */
	public $step = 0;

	/**
	 * The action to perform on all the articles.
	 *
	 * @access public
	 * @var string
	 */
	public $action;

	/**
	 * Environment to import the posts to.
	 *
	 * @access public
	 * @var string
	 */
	public $environment;

	/**
	 * Status to import the post as.
	 *
	 * @access public
	 * @var string
	 */
	public $status;

	/**
	 * Number of posts to porcess per batch.
	 *
	 * @access public
	 * @var int
	 */
	public $posts_per_batch = 10;

	/**
	 * Total number of posts found to to sync.
	 *
	 * @access public
	 * @var int
	 */
	public $total = 0;

	/**
	 * Restrict posts to these categories.
	 *
	 * @access public
	 * @var null
	 */
	public $categories = null;

	/**
	 * Restrict posts to these authors.
	 *
	 * @access public
	 * @var null
	 */
	public $authors = null;

	/**
	 * Restrict posts newer than this start date.
	 *
	 * @access public
	 * @var null
	 */
	public $start_date = null;

	/**
	 * Restrict posts older than this end date.
	 *
	 * @access public
	 * @var null
	 */
	public $end_date = null;

	/**
	 * Hooks registered in this class.
	 *
	 * This method is auto called from WPNA_Admin_Base.
	 *
	 * @access public
	 * @return void
	 */
	public function hooks() {
		add_action( 'wpna_admin_facebook_tabs', array( $this, 'setup_tabs' ), 10, 1 );

		add_action( 'wp_ajax_wpna_do_post_sync', array( $this, 'do_ajax_post_sync' ), 10, 0 );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ), 10, 1 );
	}

	/**
	 * Load admin JS files.
	 *
	 * Targets the new and edit posts screens and loads in the javascript
	 * required for setting up the meta boxes.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param  string $hook The current page hook.
	 * @return void
	 */
	public function scripts( $hook ) {
		if ( 'native-articles_page_wpna_facebook' === $hook ) {
			wp_enqueue_script( 'wpna-post-syncer', plugins_url( '/assets/js/post-syncer.js', WPNA_BASE_FILE ), array( 'jquery', 'wp-util' ), WPNA_VERSION, true );

			wp_localize_script( 'wpna-post-syncer', 'wpnaPostSyncer', array(
				'nonce' => wp_create_nonce( 'wpna_post_syncer_ajax_nonce' ),
			));
		}
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
			'post_syncer',
			esc_html__( 'Post Syncer', 'wp-native-articles' ),
			$this->page_url(),
			array( $this, 'post_syncer_tab_callback' )
		);
	}

	/**
	 * Output the HTML for the post_syncer tab.
	 *
	 * Uses the settings API and outputs the fields registered.
	 * settings_fields() requries the name of the group of settings to ouput.
	 * do_settings_sections() requires the unique page slug for this settings form.
	 *
	 * @access public
	 * @return void
	 */
	public function post_syncer_tab_callback() {
		// Setup an instance of the Facebook API.
		$facebook_api = new WPNA_Facebook_API();
		?>
		<form class="wpna-sync-posts-form" action="" method="post">

			<?php
			// Show an error if the API isn't connected.
			if ( ! $facebook_api->is_connected() ) : ?>
				<p style="margin-top: 20px;">
					<span class="wpna-label wpna-label-warning"><?php esc_html_e( 'Warning', 'wp-native-articles' ); ?></span>
					<?php echo wp_kses(
						sprintf(
							// translators: Placeholder is the URL to the API settings page.
							__( 'Your site is not connected to Facebook via the API. Please visit the <a href="%s">API Settings Tab</a> first and connect your site.', 'wp-native-articles' ),
							esc_url(
								add_query_arg(
									array(
										'page' => 'wpna_facebook',
										'tab'  => 'api',
									),
									admin_url( '/admin.php' )
								)
							)
						),
						array( 'a' => array( 'href' => true ) )
					); ?>
				</p>
				<hr />
			<?php endif; ?>

			<h4>
				<?php esc_html_e( 'Mass sync posts to Facebook Instant Articles through the API.', 'wp-native-articles' ); ?>
			</h4>
			<p>
				<?php esc_html_e( 'A quick and easy way to convert all your old posts to Instant Articles. Also useful if you’ve made changes and want to mass update all your current Instant Articles.', 'wp-native-articles' ); ?>
			</p>

			<h3><?php esc_html_e( 'Instant Article Options', 'wp-native-articles' ); ?></h3>

			<ul>

				<?php do_action( 'wpna_post_syncer_form_before_action' ); ?>
				<li>
					<label>
						<span class="label-responsive"><?php esc_html_e( 'Action', 'wp-native-articles' ); ?>:</span>
						<select name="action" id="action" class="postform">
							<option value="update"><?php esc_html_e( 'Update', 'wp-native-articles' ); ?></option>
							<option value="delete"><?php esc_html_e( 'Delete', 'wp-native-articles' ); ?></option>
						</select>
					</label>
				</li>

				<?php do_action( 'wpna_post_syncer_form_before_environment' ); ?>
				<li>
					<label>
						<span class="label-responsive"><?php esc_html_e( 'Environment', 'wp-native-articles' ); ?>:</span>
						<select name="environment" id="environment" class="postform">
							<option value="development"><?php esc_html_e( 'Development', 'wp-native-articles' ); ?></option>
							<option value="production"><?php esc_html_e( 'Production', 'wp-native-articles' ); ?></option>
						</select>
					</label>
				</li>

				<?php do_action( 'wpna_post_syncer_form_before_status' ); ?>
				<li style="display:none;">
					<label>
						<span class="label-responsive"><?php esc_html_e( 'Status', 'wp-native-articles' ); ?>:</span>
						<select name="draft" id="draft" class="postform">
							<option value="false"><?php esc_html_e( 'Draft', 'wp-native-articles' ); ?></option>
							<option value="true"><?php esc_html_e( 'Live', 'wp-native-articles' ); ?></option>
						</select>
					</label>
				</li>

			</ul>

			<h3><?php esc_html_e( 'Post filters', 'wp-native-articles' ); ?></h3>

			<ul>
				<?php do_action( 'wpna_post_syncer_form_before_categories' ); ?>
				<li>
					<label>
						<span class="label-responsive"><?php esc_html_e( 'Categories', 'wp-native-articles' ); ?>:</span>
						<select name="categories[]" id="categories" class="postform">
							<option value="0" selected="selected"><?php esc_html_e( 'All', 'wp-native-articles' ); ?></option>
							<?php $categories = get_categories( array( 'hide_empty' => false ) ); ?>
							<?php foreach ( $categories as $category ) : ?>
								<option value="<?php echo esc_attr( $category->term_id ); ?>"><?php echo esc_html( $category->name ); ?> (<?php echo esc_html( $category->count ); ?>)</option>
							<?php endforeach; ?>
						</select>
					</label>
				</li>

				<?php do_action( 'wpna_post_syncer_form_before_authors' ); ?>
				<li>
					<span class="label-responsive"><?php esc_html_e( 'Authors', 'wp-native-articles' ); ?>:</span>
					<select name="authors[]" id="authors" class="postform">
						<option value="0" selected="selected"><?php esc_html_e( 'All', 'wp-native-articles' ); ?></option>
						<?php $users = get_users(
							array(
								'orderby' => 'nicename',
								'fields'  => array( 'ID', 'display_name' ),
							)
						); ?>
						<?php foreach ( $users as $user ) : ?>
							<option value="<?php echo esc_attr( $user->ID ); ?>"><?php echo esc_html( $user->display_name ); ?></option>
						<?php endforeach; ?>
					</select>
				</li>

				<?php do_action( 'wpna_post_syncer_form_before_date' ); ?>
				<li>
					<fieldset>
						<legend class="screen-reader-text"><?php esc_html_e( 'Date range:', 'wp-native-articles' ); ?></legend>
						<label for="post-start-date" class="label-responsive"><?php esc_html_e( 'Start date', 'wp-native-articles' ); ?>:</label>
						<input type="date" id="post-start-date" name="post_start_date" value="" />
						<label for="post-end-date" class="label-responsive"><?php esc_html_e( 'End date', 'wp-native-articles' ); ?>:</label>
						<input type="date" id="post-end-date" name="post_end_date" value="" />
					</fieldset>
				</li>

			</ul>

			<?php if ( ! $facebook_api->is_connected() ) : ?>
				<?php submit_button( esc_html__( 'Sync Posts', 'wp-native-articles' ), 'secondary post-syncer-btn', null, null, array( 'disabled' => true ) ); ?>
			<?php else : ?>
				<?php submit_button( esc_html__( 'Sync Posts', 'wp-native-articles' ), 'secondary post-syncer-btn', null, null, array() ); ?>
			<?php endif; ?>

			<div class="wpna-status">
				<div>
					<span class="processed">0</span>
					<?php esc_html_e( 'of', 'wp-native-articles' ); ?>
					<span class="total">0</span>
					<?php esc_html_e( 'posts processed', 'wp-native-articles' ); ?>
				</div>
			</div>

		</form>
		<?php
	}

	/**
	 * Mass syncs posts to Facebook.
	 *
	 * @access public
	 * @return void
	 */
	public function do_ajax_post_sync() {
		// Check it's an AJAX request.
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			wp_die();
		}

		// @codingStandardsIgnoreLine
		parse_str( $_POST['form'], $form );

		// Check the nonce is valid.
		check_ajax_referer( 'wpna_post_syncer_ajax_nonce' );

		// Check they're an admin.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		if ( ! empty( $_POST['step'] ) ) {
			$this->step = absint( $_POST['step'] );
		}

		$this->action      = isset( $form['action'] ) && 'update' === $form['action'] ? 'update' : 'delete';
		$this->environment = isset( $form['environment'] ) && 'production' === $form['environment'] ? 'production' : 'development';
		$this->status      = isset( $form['status'] ) && 'true' === $form['status'] ? true : false;

		// If there are categories.
		if ( ! empty( $form['categories'] ) ) {
			$cats = array_filter( $form['categories'] );
			if ( ! empty( $cats ) ) {
				$this->categories = array_map( 'absint', $cats );
			}
		}

		// If there are authors.
		if ( ! empty( $form['authors'] ) ) {
			$authors = array_filter( $form['authors'] );
			if ( ! empty( $authors ) ) {
				$this->authors = array_map( 'absint', $authors );
			}
		}

		if ( ! empty( $form['post_start_date'] ) && wpna_valid_date( $form['post_start_date'] ) ) {
			$this->start_date = $form['post_start_date'];
		}

		if ( ! empty( $form['post_end_date'] ) && wpna_valid_date( $form['post_end_date'] ) ) {
			$this->end_date = $form['post_end_date'];
		}

		$ret = $this->process_step();

		$response = array();

		// If the sync isn't finished.
		if ( $ret ) {

			$response['step']       = $this->step + 1;
			$response['percentage'] = $this->get_percentage_complete();
			$response['total']      = $this->total;
			$response['processed']  = $this->step * $this->posts_per_batch;

			if ( $response['processed'] > $this->total ) {
				$response['processed'] = $response['total'];
			}
		} else {

			$args = array_merge( $_REQUEST, array(
				'step' => $this->step,
			) );

			$response['step']       = 'done';
			$response['percentage'] = 100;
			$response['total']      = $this->total;
			$response['processed']  = $this->total;

			$redirect_args = array(
				'page'         => 'wpna_facebook',
				'tab'          => 'post_syncer',
				'wpna-message' => 'post_sync_complete',
			);

			$response['url'] = add_query_arg( $redirect_args, admin_url( 'admin.php' ) );
		}

		wp_send_json_success( $response );
		die;
	}

	/**
	 * Batch process post syncing.
	 *
	 * @access public
	 * @return void
	 */
	public function process_step() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		ignore_user_abort( true );

		// @codingStandardsIgnoreStart
		if ( ! ini_get( 'safe_mode' ) ) {
			@set_time_limit( 0 );
		}

		$query_args = array(
			'ignore_sticky_posts' => true,
			'has_password'        => false,
			'posts_per_page'      => $this->posts_per_batch,
			'paged'               => $this->step,
			'orderby'             => 'ID',
			'order'               => 'ASC',
			'status'              => 'publish',
			'post_type'           => wpna_allowed_post_types(),
			'meta_query'          => array(
				'relation' => 'OR',
				array(
					'key'     => '_wpna_fbia_status',
					'compare' => 'NOT EXISTS',
					'value'   => '', // This is ignored, but is necessary...
				),
				array(
					'key'   => '_wpna_fbia_status',
					'value' => 'published',
				),
			),
		);
		// @codingStandardsIgnoreEnd

		// Set the categories.
		if ( $this->categories ) {
			$query_args['category__in'] = (array) $this->categories;
		}

		// Set the authors.
		if ( $this->authors ) {
			$query_args['author__in'] = (array) $this->authors;
		}

		// Set the date query.
		if ( $this->start_date || $this->end_date ) {

			$query_args['date_query'] = array();

			if ( $this->start_date ) {
				$query_args['date_query'][0]['after'] = $this->start_date;
			}

			if ( $this->end_date ) {
				$query_args['date_query'][0]['before'] = $this->end_date;
			}

			// Include these dates.
			$query_args['date_query'][0]['inclusive'] = true;
		}

		$query = new WP_Query( $query_args );

		if ( $query->have_posts() ) {

			// Set the total post count.
			$this->total = $query->found_posts;

			foreach ( $query->posts as $post ) {

				// Check if this post should be converted.
				if ( ! wpna_should_convert_post_ia( $post ) ) {
					continue;
				}

				$facebook_api = new WPNA_Facebook_API( $post );

				// Set the environment.
				$facebook_api->environment = $this->environment;

				// Set the status.
				$facebook_api->status = $this->status;

				// Perform the action.
				if ( 'delete' === $this->action ) {
					// Delete the article.
					$facebook_api->delete();
				} elseif ( 'update' === $this->action ) {
					// Sync the article.
					$facebook_api->sync();

					// Sleep for a bit to avoid Facebook API limits.
					usleep( 100000 ); // 100ms
				}
			}

			// We have remaining articles to sync.
			// Return true to continue batch processing.
			return true;

		} else {

			// No more articles found. Finish up.
			return false;
		}

	}

	/**
	 * Work out the percentage complete based on the total posts found
	 * and the current step.
	 *
	 * @access public
	 * @return int
	 */
	public function get_percentage_complete() {
		// Default too 100%.
		$percentage = 100;

		if ( $this->total > 0 ) {
			$percentage = ( ( $this->posts_per_batch * $this->step ) / $this->total ) * 100;
		}

		if ( $percentage > 100 ) {
			$percentage = 100;
		}

		return $percentage;
	}

}
