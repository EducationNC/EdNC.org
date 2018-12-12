<?php
/**
 * Placement class.
 *
 * @package     wp-native-articles
 * @subpackage  Includes/Placements
 * @copyright   Copyright (c) 2017, WPArtisan
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Placement class. Saves, deletes, applies etc.
 */
class WPNA_Placement {

	/**
	 * The ID of the current placement.
	 *
	 * @var int
	 */
	public $ID;

	/**
	 * Hold the placement data.
	 *
	 * @var array
	 */
	public $data = array();

	/**
	 * Hold the placement meta.
	 *
	 * @var array
	 */
	public $meta = array();

	/**
	 * List of fields allowed to be set on the placement.
	 * Use the hook in allowed_fields() function to add / remove
	 * custom fields from this list.
	 *
	 * @var array
	 */
	protected $allowed_fields = array(
		'name',
		'blog_id',
		'user_id',
		'status',
		'content_type',
		'content',
		'start_date',
		'end_date',
		'sync_posts',
		'created',
	);

	/**
	 * Class constructor.
	 *
	 * @param int $id ID of the current placement.
	 */
	public function __construct( $id = null ) {
		if ( empty( $id ) ) {
			return false;
		}

		return $this->get( $id );
	}

	/**
	 * Runs the current placement against a post. Checks all filters to
	 * see if any match. If they do then applies this placement to the post
	 * content.
	 *
	 * @access public
	 * @param string $content Content to apply the palcement to.
	 * @return return
	 */
	public function run( $content ) {

		if ( $this->matches( get_the_ID() ) ) {
			$content = $this->apply( $content );
		}

		return $content;
	}

	/**
	 * Checks the current placement against a post to see whether it should be
	 * applied to it or not.
	 *
	 * @access public
	 * @param  int $post_id The ID of the post to check.
	 * @return bool
	 */
	public function matches( $post_id = null ) {
		// Use the global post ID if one isn't passed.
		if ( is_null( $post_id ) ) {
			$post_id = get_the_ID();
		}

		// Check the cache first. Use found to disambiguate a false return.
		$placement_matches = wp_cache_get( "wpna_placement_matches_{$this->ID}", 'wpna' );

		if ( $placement_matches && isset( $placement_matches[ $post_id ] ) ) {
			return $placement_matches[ $post_id ];
		}

		// Setup the base query.
		$args = array(
			'no_found_rows'       => true,
			'ignore_sticky_posts' => true,
			'post_status'         => 'publish',
			'fields'              => 'ids',
			'posts_per_page'      => 1,
			'post_type'           => wpna_allowed_post_types(),
			'post__in'            => array( $post_id ), // have to use post__in so other filters are applied.
		);

		if ( $this->meta( 'filter_category' ) ) {
			$args = array_merge( $args, $this->meta( 'filter_category' ) );
		}

		if ( $this->meta( 'filter_tag' ) ) {
			$args = array_merge( $args, $this->meta( 'filter_tag' ) );
		}

		if ( $this->meta( 'filter_author' ) ) {
			$args = array_merge( $args, $this->meta( 'filter_author' ) );
		}

		if ( $this->meta( 'filter_custom' ) ) {
			wp_parse_str( $this->meta( 'filter_custom' ), $filter_custom );
			$args = array_merge( $args, $filter_custom );
		}

		/**
		 * Filter the query to see if the placement matches.
		 *
		 * @var array $args Array of WP_Query arguments to check against.
		 * @var int $post_id ID of the post to check.
		 * @var object $this Current placement being checked.
		 */
		$args = apply_filters( 'wpna_placement_matches_args', $args, $post_id, $this );

		// Run the query.
		$query = new WP_Query( $args );

		// If the current post id is in the query results.
		$matches = in_array( $post_id, $query->posts, true );

		// Make sure the cache is an array.
		if ( ! is_array( $placement_matches ) ) {
			$placement_matches = array();
		}

		// Add the result to the placement matches cache.
		$placement_matches[ $post_id ] = $matches;

		// Cache it. Only for 24 hours as IA posts will change after that period.
		wp_cache_set( "wpna_placement_matches_{$this->ID}", $placement_matches, 'wpna', 24 * HOUR_IN_SECONDS );

		return $matches;
	}

	/**
	 * Applys content from the current placement in the positions specified.
	 *
	 * @since 1.3.0
	 *
	 * @access public
	 * @param  string $content The post content to trasnform.
	 * @return string The post content.
	 */
	public function apply( $content ) {

		// After x paragraphs.
		if ( $this->meta( 'position_paragraph' ) ) {
			// Explode content on end of paragraphs.
			$content_exploded = explode( '</p>', $content );

			// Loop over each paragraph.
			foreach ( $content_exploded as $key => $content ) {

				// Check the paragraph against the positoning.
				if ( in_array( $key, $this->meta( 'position_paragraph' ), true ) ) {
					// If it matches prepend it to the content. This work because arrays start at 0
					// well paragraphs start at 1, so we're actaully checking the paragraph after.
					$content_exploded[ $key ] = PHP_EOL . $this->get_content() . $content;
				}
			}

			// Stringify content.
			$content = implode( '</p>', $content_exploded );
		}

		// After x words.
		if ( $this->meta( 'position_words' ) ) {

			$words_positions = $this->meta( 'position_words' );

			$total_words_count = 0;

			// Explode content on paragraphs.
			$content_exploded = explode( '<p>', $content );

			foreach ( $content_exploded as $key => $content ) {

				$total_words_count += str_word_count( strip_tags( $content ) );

				// Images count for 70 words in Facebook.
				$image_count = substr_count( $content, '<img' );

				if ( $image_count > 0 ) {
					$total_words_count += ( 70 * $image_count );
				}

				// Check the paragraph against the positoning.
				foreach ( $words_positions as $words_key => $words_count ) {
					if ( $total_words_count >= $words_count ) {
						$content_exploded[ $key ] .= $this->get_content() . PHP_EOL;
						unset( $words_positions[ $words_key ] );
					}
				}
			}

			// Stringify content.
			$content = implode( '<p>', $content_exploded );
		}

		if ( $this->meta( 'position_top' ) ) {
			$content = $this->get_content() . $content;
		}

		if ( $this->meta( 'position_bottom' ) ) {
			$content = $content . $this->get_content();
		}

		return $content;
	}

	/**
	 * Works out the correct content to show for the placement.
	 *
	 * @access public
	 * @return string The placement content.
	 */
	public function get_content() {

		// Grab the placement content.
		$content = wp_unslash( $this->content );

		// @todo better way of doing this?
		$content_placeholders = array(
			'name'                 => get_bloginfo( 'name' ),
			'description'          => get_bloginfo( 'description' ),
			'url'                  => get_bloginfo( 'url' ),
			'stylesheet_directory' => get_bloginfo( 'stylesheet_directory' ),
			'template_url'         => get_bloginfo( 'template_url' ),
			'plugins_url'          => plugins_url(),
			'content_url'          => content_url(),
			'post_permalink'       => get_permalink(),
			'post_title'           => get_the_title(),
		);

		/**
		 * Filter the content placements. Add any custom ones.
		 *
		 * @var array $content_placeholders Placeholders to apply.
		 * @var string $content The content for this placement.
		 * @var object $this The current placement.
		 */
		$content_placeholders = apply_filters( 'wpna_placement_content_placeholders', $content_placeholders, $content, $this );

		// Replace all possible bloginfo placeholders.
		foreach ( $content_placeholders as $placeholder => $replacement ) {
			$content = str_replace( sprintf( '{%s}', $placeholder ), $replacement, $content );
		}

		// Ad posts content type.
		if ( 'ad' === $this->content_type ) {
			// Check if ads are enabled for the article.
			if ( wpna_switch_to_boolean( wpna_get_post_option( get_the_ID(), 'fbia_enable_ads' ) ) ) {
				// Get the converted post.
				$wpna_post = new WPNA_Facebook_Post( get_the_ID() );
				// Set the content to the ads.
				$content = $wpna_post->get_ads();
			}
		}

		// Embed media.
		if ( 'embed' === $this->content_type ) {
			$content = sprintf( '<figure class="op-interactive"><iframe src="%s"></iframe></figure>', esc_url( $this->meta( 'video_url' ) ) );
		}

		// Related posts content type.
		if ( 'related_posts' === $this->content_type ) {
			// Get the converted post.
			$wpna_post = new WPNA_Facebook_Post( get_the_ID() );

			$related_articles_loop = $wpna_post->get_related_articles();

			$content = '';

			// @todo Switch to template for related posts.
			if ( $related_articles_loop->have_posts() ) {

				// Check if a related posts title was set.
				$related_posts_title = $this->meta( 'related_posts_title' );

				if ( $related_posts_title ) {
					$content .= sprintf( '<ul class="op-related-articles" title="%s">', esc_attr( $related_posts_title ) ) . PHP_EOL;
				} else {
					$content .= '<ul class="op-related-articles">' . PHP_EOL;
				}

				foreach ( $related_articles = $related_articles_loop->get_posts() as $related_article ) {

					$attrs = '';
					if ( wpna_switch_to_boolean( wpna_get_post_option( $related_article->ID, 'fbia_sponsored' ) ) ) {
						$attrs = ' data-sponsored="true"';
					}

					/**
					 * Filter any attributes applied to the <li> element
					 * of the related articles. e.g. sponsored.
					 *
					 * @since 1.0.0
					 * @param $attrs List of attributes to add
					 * @param $related_article The current related articles
					 * @param $post The current post
					 */
					$attrs = apply_filters( 'wpna_facebook_article_related_articles_attributes', $attrs, $related_article );

					$content .= sprintf( '<li%s><a href="%s"></a></li>' . PHP_EOL, $attrs, esc_url( get_permalink( $related_article ) ) );
				}

				$content .= '</ul>' . PHP_EOL;
			}

			return $content;
		}

		// Default content type is 'custom'.
		return $content;
	}

	/**
	 * Gets a placement.
	 *
	 * @access public
	 * @param  int $id Placement id.
	 * @return object WPNA_Placement
	 */
	public function get( $id ) {
		global $wpdb;

		// Check the cache first.
		$placement = wp_cache_get( 'wpna_placement_' . $id, 'wpna' );

		if ( ! $placement ) {
			// Grab the placement from the DB.
			// @codingStandardsIgnoreStart
			$placement = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT * FROM {$wpdb->base_prefix}wpna_placements WHERE id = %d",
					$id
				),
				ARRAY_A
			);
			// @codingStandardsIgnoreEnd

			// Save the result to the cache.
			wp_cache_set( 'wpna_placement_' . $id, $placement, 'wpna' );
		}

		// Setup the placement data.
		if ( ! empty( $placement ) ) {
			$this->ID = $id;
			$this->prepare( $placement );
		}

		// Check the cache first.
		$placement_meta = wp_cache_get( 'wpna_placement_meta_' . $id, 'wpna' );

		if ( ! $placement_meta ) {
			// Get the placement meta.
			// @codingStandardsIgnoreStart
			$placement_meta = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT meta_key, meta_value FROM {$wpdb->base_prefix}wpna_placements_meta WHERE placement_id = %d",
					$id
				)
			);
			// @codingStandardsIgnoreEnd

			// Save the result to the cache.
			wp_cache_set( 'wpna_placement_meta_' . $id, $placement_meta, 'wpna' );
		}

		if ( ! empty( $placement_meta ) ) {
			// Setup the meta.
			foreach ( $placement_meta as $key => $row ) {
				// @codingStandardsIgnoreLine
				$this->meta[ $row->meta_key ] = maybe_unserialize( $row->meta_value );
			}
		}

		return $this;
	}

	/**
	 * Adds a new placement.
	 *
	 * If an ID is set then updates instead.
	 *
	 * @since 1.3.0
	 *
	 * @access public
	 * @param  array $data The placement data.
	 * @return mixed bool on failure, ID on success.
	 */
	public function add( $data = null ) {
		global $wpdb;

		// Format and setup the class.
		if ( $data ) {
			$this->prepare( $data );
		}

		// If an ID is set then update.
		if ( ! empty( $this->ID ) ) {
			return $this->update( $data );
		}

		$placement = array();

		// Construct the insert array.
		foreach ( $this->allowed_fields() as $field ) {
			$placement[ $field ] = $this->$field;
		}

		/**
		 * Filters the placement data before being inserted into the database.
		 *
		 * @since 1.3.0
		 *
		 * @param array $data Placement $data.
		 */
		$placement = apply_filters( 'wpna_insert_placement_data', $placement, $this );

		/**
		 * Fires before the placement has been added to the database.
		 *
		 * @since 1.3.0
		 *
		 * @param array $data Placement $data.
		 */
		do_action( 'wpna_pre_insert_placement', $placement, $this );

		// Insert into the db.
		// @codingStandardsIgnoreStart
		if ( $wpdb->insert( "{$wpdb->base_prefix}wpna_placements", $placement ) ) {
			$this->ID = $wpdb->insert_id;
		}

		/**
		 * Fires after the placement has been added to the database.
		 *
		 * @since 1.3.0
		 *
		 * @param array $data Placement $data.
		 * @param int   $ID ID of the placement inserted.
		 */
		do_action( 'wpna_post_insert_placement', $placement, $this );

		// Now try and update the placement meta.
		// Takes care of it's own formatting.
		$this->update_meta( $data );

		return $this->ID;
	}

	/**
	 * Updates an existing placement.
	 *
	 * @since 1.3.0
	 *
	 * @access public
	 * @param  array $data The placement data.
	 * @return mixed bool on failure, ID on success.
	 */
	public function update( $data ) {
		global $wpdb;

		// Format and setup the class.
		if ( $data ) {
			$this->prepare( $data );
		}

		// If an ID is not set then add.
		if ( empty( $this->ID ) ) {
			return $this->add( $data );
		}

		$placement = array();

		// Construct the insert array.
		foreach ( $this->allowed_fields() as $field ) {
			$placement[ $field ] = stripslashes( $this->$field );
		}

		// Special rule for the end date.
		// If it's been removed then there is no end to the campaign.
		if ( ! isset( $placement['end_date'] ) ) {
			$placement['end_date'] = null;
		}

		/**
		 * Filters the placement data before being inserted into the database.
		 *
		 * @since 1.3.0
		 *
		 * @param array $data Placement $data.
		 */
		$placement = apply_filters( 'wpna_update_placement_data', $placement, $this );

		/**
		 * Fires before the placement has been added to the database.
		 *
		 * @since 1.3.0
		 *
		 * @param array $data Placement $data.
		 */
		do_action( 'wpna_pre_update_placement', $placement, $this );

		// Update into the db.
		// @codingStandardsIgnoreLine
		$wpdb->update( "{$wpdb->base_prefix}wpna_placements", $placement, array( 'id' => $this->ID ) );

		// Clear the placement cache.
		$this->clear_cache();

		/**
		 * Fires after the placement has been added to the database.
		 *
		 * @since 1.3.0
		 *
		 * @param array $data Placement $data.
		 * @param int   $ID ID of the placement inserted.
		 */
		do_action( 'wpna_post_update_placement', $placement, $this );

		// Now try and update the placement meta.
		// Takes care of it's own formatting.
		$this->update_meta();

		return $this->ID;
	}

	/**
	 * Updates the meta for a placement.
	 * REMOVES ALL EXISTING META, and replaces with the new stuff.
	 *
	 * @access public
	 * @param  array $meta key => value meta fields to add.
	 * @return void
	 */
	public function update_meta( $meta = null ) {
		global $wpdb;

		// If no placement is loaded then bail.
		if ( ! $this->ID ) {
			return;
		}

		if ( ! empty( $meta ) ) {
			$this->prepare_meta( $meta );
		}

		/**
		 * Fires before the placement meta has been updated.
		 *
		 * @since 1.3.0
		 *
		 * @param int   $ID ID of the placement inserted.
		 * @param array $meta The placement meta to update.
		 */
		do_action( 'wpna_pre_update_placement_meta', $this, $meta );

		// Remove all existing meta.
		// @codingStandardsIgnoreLine
 		$status = $wpdb->delete( "{$wpdb->base_prefix}wpna_placements_meta", array( 'placement_id' => $this->ID ), array( '%d' ) );

		// If no meta is passed then don't update.
		if ( ! empty( $this->meta ) ) {
			// Remove any empty rows.
			$meta = array_filter( $this->meta );

			// Add in all the new meta.
			foreach ( $meta as $key => $value ) {
				// @codingStandardsIgnoreStart
				$wpdb->insert(
					"{$wpdb->base_prefix}wpna_placements_meta",
					array( 'placement_id' => $this->ID, 'meta_key' => $key, 'meta_value' => maybe_serialize( $value ) ),
					array( '%d', '%s', '%s' )
				);
				// @codingStandardsIgnoreEnd
			}
		}

		// Clear the placement cache.
		$this->clear_cache();

		/**
		 * Fires after the placement meta has been updated.
		 *
		 * @since 1.3.0
		 *
		 * @param int   $ID ID of the placement inserted.
		 * @param array $meta The placement meta to update.
		 */
		do_action( 'wpna_post_update_placement_meta', $this, $meta );

		return true;
	}

	/**
	 * Deletes a placement.
	 *
	 * Removes it from the database with all associated meta.
	 *
	 * @access public
	 * @return bool
	 */
	public function delete() {
		global $wpdb;

		// If no placement is loaded then bail.
		if ( empty( $this->ID ) ) {
			return false;
		}

		/**
		 * Fires before the placement has been deleted from the database.
		 *
		 * @since 1.3.0
		 *
		 * @param int $ID ID of the placement inserted.
		 */
		do_action( 'wpna_pre_delete_placement', $this->ID );

		// Remove the placement.
		// @codingStandardsIgnoreStart
 		$status = $wpdb->delete( "{$wpdb->base_prefix}wpna_placements", array( 'id' => $this->ID ), array( '%d' ) );

		// Remove the placement meta.
		if ( $status ) {
			// @codingStandardsIgnoreStart
			$wpdb->delete( "{$wpdb->base_prefix}wpna_placements_meta", array( 'placement_id' => $this->ID ), array( '%d' ) );
		}

		// Clear all the cache.
		$this->clear_cache();

		/**
		 * Fires after the placement has been deleted from the database.
		 *
		 * @since 1.3.0
		 *
		 * @param int  $ID ID of the placement inserted.
		 * @param bool $status Whether the lacement was deleted or not.
		 */
		do_action( 'wpna_post_delete_placement', $this->ID, $status );

		return $status;
	}

	/**
	 * Constructs and formats default data and fields for placements.
	 *
	 * Stores it all in the $data var, accessed through magic methods.
	 * Checks it against the list of allowed fields.
	 *
	 * @since 1.3.0
	 *
	 * @access public
	 * @param  array $data The data to prepare.
	 * @return void|false
	 */
	protected function prepare( $data = array() ) {

		// Make sure data actaully exists.
		if ( ! is_array( $data ) || array() === $data ) {
			return false;
		}

		// Checked the pasted data against the allowed fields.
		foreach ( $this->allowed_fields() as $field ) {
			// Isset is important. We can pass in null or empty string.
			if ( isset( $data[ $field ] ) ) {
				$this->data[ $field ] = $data[ $field ];
			}
		}

		// Now prepare the meta.
		$this->prepare_meta( $data );
	}

	/**
	 * Prepares the placement meta.
	 *
	 * Checks it against a list of allowed meta fields.
	 *
	 * @access public
	 * @param  array $data Data to add as meta fields.
	 * @return void|false
	 */
	public function prepare_meta( $data = array() ) {

		if ( ! is_array( $data ) || array() === $data ) {
			return false;
		}

		$meta = array();

		// Check for the custom filter.
		if ( ! empty( $data['related_posts_title'] )  ) {
			$meta['related_posts_title'] = sanitize_text_field( $data['related_posts_title'] );
		}

		// Check for the video url.
		if ( ! empty( $data['video_url'] )  ) {
			$meta['video_url'] = esc_url_raw( $data['video_url'] );
		}

		// Check for top position.
		if ( ! empty( $data['position_top'] ) ) {
			$meta['position_top'] = true;
		}

		// Check for bottom position.
		if ( ! empty( $data['position_bottom'] ) ) {
			$meta['position_bottom'] = true;
		}

		// Check for paragraph position.
		if ( ! empty( $data['position_paragraph_toggle'] ) && ! empty( $data['position_paragraph'] ) ) {
			$meta['position_paragraph'] = array( absint( $data['position_paragraph'] ) );
		}

		// Check for words position.
		if ( ! empty( $data['position_words_toggle'] ) && ! empty( $data['position_words'] ) ) {
			$meta['position_words'] = array( absint( $data['position_words'] ) );
		}

		// Check for the category filter.
		if ( ! empty( $data['filter_category'] ) ) {
			$meta['filter_category'] = array();
			if ( ! empty( $data['category__in'] ) ) {
				$meta['filter_category']['category__in'] = array_filter( array_map( 'absint', (array) $data['category__in'] ) );
			}
			if ( ! empty( $data['category__not_in'] ) ) {
				$meta['filter_category']['category__not_in'] = array_filter( array_map( 'absint', (array) $data['category__not_in'] ) );
			}
		}

		// Check for the tag filter.
		if ( ! empty( $data['filter_tag'] ) ) {
			$meta['filter_tag'] = array();
			if ( ! empty( $data['tag__in'] ) ) {
				$meta['filter_tag']['tag__in'] = array_filter( array_map( 'absint', (array) $data['tag__in'] ) );
			}
			if ( ! empty( $data['tag__not_in'] ) ) {
				$meta['filter_tag']['tag__not_in'] = array_filter( array_map( 'absint', (array) $data['tag__not_in'] ) );
			}
		}

		// Check for the author filter.
		if ( ! empty( $data['filter_author'] ) ) {
			$meta['filter_author'] = array();
			if ( ! empty( $data['author__in'] ) ) {
				$meta['filter_author']['author__in'] = array_filter( array_map( 'absint', (array) $data['author__in'] ) );
			}
			if ( ! empty( $data['author__not_in'] ) ) {
				$meta['filter_author']['author__not_in'] = array_filter( array_map( 'absint', (array) $data['author__not_in'] ) );
			}
		}

		// Check for the custom filter.
		if ( ! empty( $data['filter_custom_enable'] ) && ! empty( $data['filter_custom'] ) ) {
			$meta['filter_custom'] = sanitize_text_field( $data['filter_custom'] );
		}

		/**
		 * Use this filter to add more meta fields to the placement.
		 *
		 * @var array $meta Meta fields to save.
		 * @var array $data POST data from Add/Edit meta page.
		 * @var object $this Current placement.
		 */
		$meta = apply_filters( 'wpna_placement_prepare_meta', $meta, $data, $this );

		$this->meta = $meta;
	}

	/**
	 * Return a list of allowed fields for the placement.
	 *
	 * @access public
	 * @return array
	 */
	public function allowed_fields() {
		/**
		 * Use this hook to add any new placement meta fields.
		 *
		 * @since 1.3.0
		 *
		 * @param array  $allowed_fields All meta fields allowed for this placement.
		 * @param object $this The current class instance.
		 */
		return apply_filters( 'wpna_placement_allowed_fields', $this->allowed_fields, $this );
	}

	/**
	 * Grab a meta value by key for this placement.
	 *
	 * @access public
	 * @param string $name Meta key to retrieve.
	 * @return mixed
	 */
	public function meta( $name ) {
		if ( ! empty( $this->meta[ $name ] ) ) {
			return $this->meta[ $name ];
		}

		return null;
	}

	/**
	 * Override the blog_id attribute.
	 *
	 * If it's not set, default to the current blog.
	 *
	 * @access public
	 * @return int
	 */
	public function get_blog_id_field() {
		if ( empty( $this->data['blog_id'] ) ) {
			return get_current_blog_id();
		}

		return $this->data['blog_id'];
	}

	/**
	 * Override the user_id attribute.
	 *
	 * If it's not set, default to the current user.
	 *
	 * @access public
	 * @return int
	 */
	public function get_user_id_field() {
		if ( empty( $this->data['user_id'] ) ) {
			return get_current_user_id();
		}

		return $this->data['user_id'];
	}

	/**
	 * Override the code_type attribute.
	 *
	 * If it's not set, default to 'custom'
	 *
	 * @access public
	 * @return string
	 */
	public function get_code_type_field() {
		if ( empty( $this->data['code_type'] ) ) {
			return 'custom';
		}

		return $this->data['code_type'];
	}

	/**
	 * Override the content attribute.
	 *
	 * If it's not set, default to 'custom'
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_field() {
		if ( empty( $this->data['content'] ) ) {
			return null;
		}

		return $this->data['content'];
	}

	/**
	 * Override the status attribute.
	 *
	 * If it's not set, default to 'inactive'
	 *
	 * @access public
	 * @return int
	 */
	public function get_status_field() {
		if ( empty( $this->data['status'] ) ) {
			return 'inactive';
		}

		return $this->data['status'];
	}

	/**
	 * Override the start_date attribute.
	 *
	 * If it's not set, default to the current date.
	 *
	 * @access public
	 * @return string
	 */
	public function get_start_date_field() {
		if ( empty( $this->data['start_date'] ) ) {
			return date( 'c' );
		}

		return $this->data['start_date'];
	}

	/**
	 * Override the end_date attribute.
	 *
	 * If it's not set, default to the current date.
	 *
	 * @access public
	 * @return string
	 */
	public function get_end_date_field() {
		if ( empty( $this->data['end_date'] ) || '0000-00-00 00:00:00' === $this->data['end_date'] ) {
			return null;
		}

		return $this->data['end_date'];
	}

	/**
	 * Override the sync_posts attribute.
	 *
	 * If it's not set, default to 0.
	 *
	 * @access public
	 * @return int
	 */
	public function get_sync_posts_field() {
		if ( isset( $this->data['sync_posts'] ) && 1 === absint( $this->data['sync_posts'] ) ) {
			return 1;
		}

		return 0;
	}

	/**
	 * Override the created attribute.
	 *
	 * If it's not set, default to the current date.
	 *
	 * @access public
	 * @return string
	 */
	public function get_created_field() {
		if ( empty( $this->data['created'] ) ) {
			return date( 'c' );
		}

		return $this->data['created'];
	}

	/**
	 * Magic method for getting attributes.
	 *
	 * - First checks if a named method has been created.
	 *   (Follows "get_{$name}_field" naming syntax)
	 * - Then tries to return the raw value.
	 * - Finally return null;
	 *
	 * @param  string $name Attribute name to get.
	 * @return mixed
	 */
	public function __get( $name ) {
		$get_method_name = 'get_' . $name . '_field';

		if ( method_exists( $this, $get_method_name ) ) {
			return $this->$get_method_name();
		} elseif ( ! empty( $this->data[ $name ] ) ) {
			return $this->data[ $name ];
		} else {
			return null;
		}
	}

	/**
	 * Clear all the caches when the placement is updated or deleted.
	 *
	 * @return bool true
	 */
	public function clear_cache() {
		// If no placement is loaded then bail.
		if ( empty( $this->ID ) ) {
			return false;
		}

		// Clear the cache.
		wp_cache_delete( 'wpna_active_placements', 'wpna' );
		wp_cache_delete( 'wpna_placement_' . $this->ID, 'wpna' );
		wp_cache_delete( 'wpna_placement_meta_' . $this->ID, 'wpna' );
		wp_cache_delete( 'wpna_placement_matches_' . $this->ID, 'wpna' );

		return true;
	}

}
