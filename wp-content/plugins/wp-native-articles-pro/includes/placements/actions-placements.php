<?php
/**
 * Actions that apply the placements and clear the cache.
 *
 * @package     wp-native-articles
 * @subpackage  Includes/Placements
 * @copyright   Copyright (c) 2017, WPArtisan
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.5.0
 */

if ( ! function_exists( 'wpna_apply_placements' ) ) :

	/**
	 * Run through the placements and try and apply them to the post.
	 *
	 * @param string $content Post content to apply the placement to.
	 * @return string New coontent with placement applied.
	 */
	function wpna_apply_placements( $content ) {
		// Get all the active placements.
		$placements = wpna_get_placements();

		if ( ! empty( $placements ) ) {
			foreach ( $placements as $placement ) {
				$content = wpna_apply_placement( $placement, $content );
			}
		}

		return $content;
	}
endif;
add_filter( 'wpna_facebook_article_content_after_transform', 'wpna_apply_placements', 99, 1 );

if ( ! function_exists( 'wpna_placements_clear_post_cache' ) ) :

	/**
	 * Clear the 'matches' cache for every placement when a published post
	 * is updated.
	 *
	 * @param mixed $post Post or Post ID to clear the caache for.
	 * @return void
	 */
	function wpna_placements_clear_post_cache( $post ) {

		// Don't bother if autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Get all the palcements.
		$placements = wpna_get_placements();

		if ( ! empty( $placements ) ) {
			foreach ( $placements as $placement ) {
				// Grab the matches cache for the placement.
				$placement_matches = wp_cache_get( 'wpna_placement_matches_' . $placement->ID, 'wpna' );

				// Unset the result for this post id.
				unset( $placement_matches[ $post->ID ] );

				// Reset the cache.
				wp_cache_set( 'wpna_placement_matches_' . $placement->ID, $placement_matches, 'wpna', HOUR_IN_SECONDS );
			}
		}
	}
endif;
add_action( 'publish_to_publish', 'wpna_placements_clear_post_cache', 10, 1 );
