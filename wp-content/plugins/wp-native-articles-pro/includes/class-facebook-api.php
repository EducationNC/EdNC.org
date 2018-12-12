<?php
/**
 * Post class for syncing Facebook Instant Articles via API.
 *
 * @since  1.4.0
 * @package wp-native-articles
 */

/**
 * Post class for syncing Facebook Instant Articles via the API.
 * API sync is used in various places (CLI, Post Syncer, admin etc)
 * so this is a handy abstraction.
 */
class WPNA_Facebook_API {

	/**
	 * The Facebook SDK Class.
	 *
	 * @var object WPNA_Facebook
	 */
	public $facebook;

	/**
	 * The WordPress Post being acted upon.
	 *
	 * @var object WP_Post
	 */
	public $wp_post;

	/**
	 * ID of the Instant Article.
	 *
	 * @var int
	 */
	public $instant_article_id;

	/**
	 * Environment to sync the post to.
	 *
	 * @var string
	 */
	public $environment;

	/**
	 * Status of the article.
	 *
	 * @var string
	 */
	public $status;

	/**
	 * Facebook Page ID to sync to.
	 *
	 * @var int
	 */
	public $page_id;

	/**
	 * Setup the class. Optionally pass in the WP Post to deal with.
	 *
	 * @access public
	 * @param WP_Post $wp_post WordPress post we're dealing with.
	 * @return void
	 */
	public function __construct( $wp_post = null ) {
		$this->wp_post = get_post( $wp_post );
	}

	/**
	 * Sync an Instant Articles to Facebook.
	 *
	 * @access public
	 * @return void
	 */
	public function sync() {
		// ID of the Facebook Page to sync to.
		$page_id = $this->get_page_id();
		// Instant Article HTML to sync.
		$instant_article = $this->get_instant_article();
		// The environment to sync to.
		$development_mode = 'production' === $this->get_environment() ? false : true;
		// Development mode can only have draft posts.
		if ( $development_mode ) {
			$published = false;
		} else {
			// Whether it's a draft or live.
			$published = $this->get_status();
		}

		/**
		 * Fired before an article is synced.
		 *
		 * @param int ID of the WordPress Post
		 * @param object $this This class instance.
		 */
		do_action( 'wpna_facebook_api_pre_syc_article', $this->wp_post->ID, $this );

		try {
			$fb_data = $this->facebook()->api( "/{$page_id}/instant_articles",
				'POST',
				array(
					'published'        => $published,
					'html_source'      => $instant_article,
					'development_mode' => $development_mode,
				)
			);

			// Update the post with the import ID.
			if ( $fb_data && ! empty( $fb_data['id'] ) ) {
				update_post_meta( $this->wp_post->ID, '_wpna_fbia_import_id', $fb_data['id'] );
			}
		} catch ( Exception $e ) {

			// Log error here.
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				// @codingStandardsIgnoreLine
				trigger_error( esc_html( $e->getMessage() ) );
			}
		}
	}

	/**
	 * Delete an Instant Article from Facebook.
	 *
	 * @access public
	 * @return string
	 */
	public function delete() {
		// ID of the article on Facebook.
		$article_id = $this->get_instant_article_id();

		// Make sure there is actually an article ID.
		if ( ! $article_id || is_wp_error( $article_id ) ) {
			return;
		}

		/**
		 * Fired before an article is deleted.
		 *
		 * @param int ID of the WordPress Post
		 * @param int $article_id ID of the Instant Article.
		 * @param object $this This class instance.
		 */
		do_action( 'wpna_facebook_api_pre_delete_article', $this->wp_post->ID, $article_id, $this );

		try {
			$fb_data = $this->facebook()->api( "/{$article_id}", 'DELETE' );

			if ( ! empty( $fb_data['success'] ) && true === (boolean) $fb_data['success'] ) {
				delete_post_meta( $this->wp_post->ID, '_wpna_fbia_id' );
				delete_post_meta( $this->wp_post->ID, '_wpna_fbia_import_id' );
				delete_post_meta( $this->wp_post->ID, '_wpna_development_fbia_id' );
			}
		} catch ( Exception $e ) {
			// Handle error.
			if ( WP_DEBUG ) {
				// @codingStandardsIgnoreLine
				trigger_error( esc_html( $e->getMessage() ) );
			}
		}
	}

	/**
	 * Gets the import status of a post in Facebook.
	 *
	 * Takes a WordPress post id, tries to workout the Facebook import_id
	 * (different from the Facebook article ID) and gets the import status
	 * plus any errors.
	 *
	 * @access public
	 * @return int|WP_Error
	 */
	public function get_instant_article_import_status() {
		if ( ! $fbia_import_id = get_post_meta( $this->wp_post->ID, '_wpna_fbia_import_id', true ) ) {
			return new WP_Error( 'wpna', esc_html__( 'This article does not have an import ID', 'wp-native-articles' ) );
		}

		try {
			// @link https://developers.facebook.com/docs/instant-articles/api#importstatus
			// html_source & instant_article don't encode properly as JSON, probably the quotes.
			// Don't need them anyway.
			$fb_data = $this->facebook()->api( "{$fbia_import_id}?fields=errors,status" );

			return $fb_data;
		} catch ( Exception $e ) {
			return new WP_Error( 'wpna', $e->getMessage() );
		}
	}

	/**
	 * Retrives the Facebook article ID.
	 *
	 * Uses the canonical URL to retrieve the ID from Facebook and store it
	 * in the post meta for future use.
	 *
	 * @access public
	 * @return int|WP_Error
	 */
	public function get_instant_article_id() {
		// Workout the environment.
		if ( 'production' === $this->get_environment() ) {
			$environment = 'instant_article';
			$meta_key    = '_wpna_fbia_id';
		} else {
			$environment = 'development_instant_article';
			$meta_key    = '_wpna_development_fbia_id';
		}

		// Check if we've got it before and saved it.
		if ( $fbia_id = get_post_meta( $this->wp_post->ID, $meta_key, true ) ) {
			return $fbia_id;
		}

		// Grab the URL to check.
		$canonical_url = get_permalink( $this->wp_post->ID );

		/**
		 * Filter the canonical URL used to retrieve the Facebook ID.
		 *
		 * @since 1.0.0
		 * @var string $canonical_url The URL to check against.
		 * @var int    $this->wp_post->ID       The ID of the post to check.
		 */
		$canonical_url = apply_filters( 'wpna_facebook_api_article_id_canonical', $canonical_url, $this->wp_post->ID );

		try {
			$fb_data = $this->facebook()->api( "?id={$canonical_url}&fields={$environment}" );

			if ( ! isset( $fb_data[ $environment ]['id'] ) ) {
				return new WP_Error( 'wpna', esc_html__( 'Could not find Instant Article', 'wp-native-articles' ) );
			}

			// Save the post ID for this environment.
			update_post_meta( $this->wp_post->ID, $meta_key, $fb_data[ $environment ]['id'] );

			return $fb_data[ $environment ]['id'];

		} catch ( FacebookApiException $e ) {
			// Log the error.
			if ( WP_DEBUG ) {
				// @codingStandardsIgnoreLine
				trigger_error( esc_html( $e ) );
			}

			// Return WP_Error.
			return new WP_Error( 'wpna', $e );
		}
	}

	/**
	 * Returns the Facebook connection.
	 *
	 * @access public
	 * @return object Facebook API connection class
	 */
	public function facebook() {
		// Temporary. Needs moving.
		$this->facebook = wpna()->admin_facebook_api->facebook();

		return $this->facebook;
	}

	/**
	 * Check whether Facebook is setup and ready to go or not.
	 *
	 * Todo: Check permissions.
	 *
	 * @access public
	 * @return boolean Whether the Facebook API is setup or not.
	 */
	public function is_connected() {
		// Check Faceook has been connected.
		if ( is_wp_error( $this->facebook() ) ) {
			return false;
		}

		// Check a user has been set.
		if ( ! $this->facebook()->getUser() ) {
			return false;
		}

		// Good to go.
		return true;
	}

	/**
	 * Get the Instant Articles version of a WP Post.
	 *
	 * @access public
	 * @return string
	 */
	public function get_instant_article() {

		if ( $this->wp_post ) {
			return wpna_get_fbia_post( $this->wp_post );
		}

		return false;
	}

	/**
	 * Get the Instant Articles Facebook Page ID to sync to.
	 *
	 * @access public
	 * @return string
	 */
	public function get_page_id() {

		if ( $this->page_id ) {
			return $this->page_id;
		}

		return wpna_get_option( 'fbia_page_id' );
	}

	/**
	 * Get the Instant Articles environment to use.
	 *
	 * @access public
	 * @return string
	 */
	public function get_environment() {

		if ( $this->environment ) {
			return $this->environment;
		}

		return wpna_get_option( 'fbia_enviroment' );
	}

	/**
	 * Get the Instant Articles status to import as.
	 *
	 * @access public
	 * @return string
	 */
	public function get_status() {

		if ( $this->status ) {
			return $this->status;
		}

		// Default to true (live).
		$this->status = true;

		// If the post isn't published set to draft.
		if ( 'publish' !== get_post_status( $this->wp_post->ID ) ) {
			$this->status = false;
		}

		// If the post status is set and it isn't set to publish, then set to draft.
		if ( $post_ia_status = get_post_meta( $this->wp_post->ID, '_wpna_fbia_status', true ) ) {
			if ( 'published' !== $post_ia_status ) {
				$this->status = false;
			}
		}

		// If it's globally set to use the API as draft then set to draft.
		if ( wpna_get_option( 'fbia_import_as_drafts' ) ) {
			$this->status = false;
		}

		return $this->status;
	}

}
