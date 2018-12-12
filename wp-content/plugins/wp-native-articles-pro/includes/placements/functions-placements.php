<?php
/**
 * Generic Placement functions.
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

if ( ! function_exists( 'wpna_get_placement' ) ) :

	/**
	 * Retrieves a placement from the DB.
	 *
	 * @param int $placement_id ID of the placement to retrieve.
	 * @return object WPNA_Placement
	 */
	function wpna_get_placement( $placement_id ) {
		$placement = new WPNA_Placement( $placement_id );
		return $placement;
	}
endif;

if ( ! function_exists( 'wpna_get_placements' ) ) :

	/**
	 * Retrieves all active placemetns
	 *
	 * @return array Array of WPNA_Placements
	 */
	function wpna_get_placements() {
		// Check the cache first.
		$placements = wp_cache_get( 'wpna_active_placements', 'wpna', false, $found );

		if ( ! $placements ) {
			global $wpdb;

			// @codingStandardsIgnoreStart
			$placements = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT id FROM {$wpdb->base_prefix}wpna_placements WHERE blog_id = %d
					AND start_date <= NOW()
					AND ( end_date >= NOW() OR end_date IS NULL OR end_date = '0000-00-00 00:00:00' )
					AND status = 'active'
					",
					get_current_blog_id()
				),
				0
			);
			// @codingStandardsIgnoreEnd

			// Get current time.
			$now = time();
			// Get the time at midnight.
			$tomorrow = strtotime( 'tomorrow 00:00:00' );
			// Get the remaining time in seconds.
			$seconds_until_midnight = $tomorrow - $now;
			// Workout the GTM offset in seconds.
			$gmt_offset = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
			// Take account of the GTM offset.
			$seconds_until_midnight = $seconds_until_midnight - $gmt_offset;

			// Set the cache. Placements are only date based so cache till midnight.
			wp_cache_set( 'wpna_active_placements', $placements, 'wpna', $seconds_until_midnight );
		}

		if ( ! empty( $placements ) ) {
			foreach ( $placements as $key => $placement_id ) {
				$placements[ $key ] = wpna_get_placement( $placement_id );
			}
		}

		return $placements;
	}
endif;

if ( ! function_exists( 'wpna_add_placement' ) ) :

	/**
	 * Inserts a placement into the DB.
	 *
	 * @param array $data Data to be inserted.
	 * @return mixed. ID on success, false on failure.
	 */
	function wpna_add_placement( $data ) {
		$placement = new WPNA_Placement();
		return $placement->add( $data );
	}
endif;

if ( ! function_exists( 'wpna_update_placement' ) ) :

	/**
	 * Updates a placement already in the DB.
	 *
	 * @param int   $placement_id ID of the placement to update.
	 * @param array $data Data to be inserted.
	 * @return mixed. ID on success, false on failure.
	 */
	function wpna_update_placement( $placement_id, $data ) {
		$placement = new WPNA_Placement( $placement_id );
		return $placement->update( $data );
	}
endif;

if ( ! function_exists( 'wpna_delete_placement' ) ) :

	/**
	 * Removes a placement completely.
	 *
	 * @param int $placement_id ID of the placement to update.
	 * @return bool
	 */
	function wpna_delete_placement( $placement_id ) {
		$placement = new WPNA_Placement( $placement_id );
		return $placement->delete( $placement_id );
	}
endif;

if ( ! function_exists( 'wpna_apply_placement' ) ) :

	/**
	 * Apply an individual placement to a post.
	 *
	 * @param WPNA_Placement $placement Placement to apply.
	 * @param string         $content The content to apply the placement to.
	 * @return string The new content with the placement applied.
	 */
	function wpna_apply_placement( WPNA_Placement $placement, $content ) {
		return $placement->run( $content );
	}
endif;
