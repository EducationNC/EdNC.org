<?php
/**
 * Template for the Add Placement Page.
 *
 * @package     wp-native-articles
 * @subpackage  Includes/Placements
 * @copyright   Copyright (c) 2017, WPArtisan
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get the placement to edit.
// @codingStandardsIgnoreLine
if ( ! empty( $_GET['placement'] ) ) {
	$placement_id = absint( $_GET['placement'] );
} else {
	wp_die( esc_html__( 'Something went wrong.', 'wp-native-articles' ) );
}

// Load the placement.
$placement = wpna_get_placement( $placement_id );

?>
<div class="wrap">

<h2><?php esc_html_e( 'Edit Placement', 'wp-native-articles' ); ?> - <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpna_placements' ) ); ?>" class="button-secondary"><?php esc_html_e( 'Go Back', 'wp-native-articles' ); ?></a></h2>

<form id="wpna-add-placement" action="" method="POST">

	<?php do_action( 'wpna_add_palcement_form_top' ); ?>

	<table class="form-table">
		<tbody>

			<?php do_action( 'wpna_add_placement_form_before_name' ); ?>
			<tr>
				<th scope="row" valign="top">
					<label for="wpna-placement-name"><?php esc_html_e( 'Name', 'wp-native-articles' ); ?></label>
				</th>
				<td>
					<input name="name" required="required" id="wpna-placement-name" class="regular-text" type="text" value="<?php echo esc_attr( $placement->name ); ?>" />
					<p class="description"><?php esc_html_e( 'The name of this placement', 'wp-native-articles' ); ?></p>
				</td>
			</tr>

			<?php do_action( 'wpna_add_placement_form_before_status' ); ?>
			<tr>
				<th scope="row" valign="top">
					<label for="wpna-placement-status"><?php esc_html_e( 'Status', 'wp-native-articles' ); ?></label>
				</th>
				<td>
					<select required="required" name="status" id="wpna-placement-status">
						<option value="active"<?php selected( 'active', $placement->status ); ?>><?php esc_html_e( 'Active', 'wp-native-articles' ); ?></option>
						<option value="inactive"<?php selected( 'inactive', $placement->status ); ?>><?php esc_html_e( 'Inactive', 'wp-native-articles' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Whether the placement is active or not', 'wp-native-articles' ); ?></p>
				</td>
			</tr>

			<?php do_action( 'wpna_add_placement_form_before_code_type' ); ?>
			<tr>
				<th scope="row" valign="top">
					<label for="wpna-name"><?php esc_html_e( 'Code Type', 'wp-native-articles' ); ?></label>
				</th>
				<td>
					<select name="content_type" class="wpna-placement-select-toggle">
						<option value="ad"<?php selected( 'ad', $placement->content_type ); ?>><?php esc_html_e( 'Ad', 'wp-native-articles' ); ?></option>
						<option value="embed"<?php selected( 'embed', $placement->content_type ); ?>><?php esc_html_e( 'Embed (Youtube, Vimeo etc)', 'wp-native-articles' ); ?></option>
						<option value="custom"<?php selected( 'custom', $placement->content_type ); ?>><?php esc_html_e( 'Custom', 'wp-native-articles' ); ?></option>
						<option value="related_posts"<?php selected( 'related_posts', $placement->content_type ); ?>><?php esc_html_e( 'Related Posts', 'wp-native-articles' ); ?></option>
						<!-- <option value=""></option> -->
					</select>

					<br /><br />

					<?php do_action( 'wpna_add_placement_form_before_content' ); ?>

					<div id="wpna-placement-ad" class="wpna-placement-content-form <?php echo ( 'ad' !== $placement->content_type ) ? 'hidden' : ''; ?>">
						<p class="description">
							<?php echo wp_kses(
								sprintf(
									// translators: Placeholder is a link to the ads settings page.
									__( 'Uses the ad code defined on the <a href="%s" target="_blank">Ads page</a>. You <strong>must</strong> have ads enabled.', 'wp-native-articles' ),
									esc_url(
										add_query_arg(
											array(
												'page'    => 'wpna_facebook',
												'section' => 'ads',
											),
											admin_url( '/admin.php' )
										)
									)
								),
								array(
									'a'      => array(
										'href'   => true,
										'target' => true,
									),
									'strong' => array(),
								)
							); ?>
						</p>
						<p class="description">
							<?php echo wp_kses(
								sprintf(
									// translators: Placeholder is a link to the Facebook ads documentation.
									__( 'This is useful if you want to manually place your ads. Be aware, Facebook has strict rules. Please familiarise yourself <a href="%s" target="_blank">with them</a> first.', 'wp-native-articles' ),
									esc_url( 'https://developers.facebook.com/docs/instant-articles/monetization' )
								),
								array(
									'a' => array(
										'href'   => true,
										'target' => true,
									),
								)
							); ?>
						</p>
					</div>

					<div id="wpna-placement-custom" class="wpna-placement-content-form <?php echo ( 'custom' !== $placement->content_type ) ? 'hidden' : ''; ?>">
						<label>
							<textarea id="wpna-placement-custom-content" name="content" class="large-text code" rows="10" cols="50" ><?php echo esc_textarea( wp_unslash( $placement->content ) ); ?></textarea>
							<p class="description">
								<?php echo wp_kses(
									__( 'The code you wish to insert. This should be in <strong>valid</strong> Instant Article format. Available template tags:', 'wp-native-articles' ),
									array( 'strong' => array() )
								); ?>
								<br />
								{name} - <?php esc_html_e( 'Your site name', 'wp-native-articles' ); ?>
								<br />
								{description} - <?php esc_html_e( 'Your site description', 'wp-native-articles' ); ?>
								<br />
								{url} - <?php esc_html_e( 'Your site URL', 'wp-native-articles' ); ?>
								<br />
								{stylesheet_directory} - <?php esc_html_e( 'URL to the directory of your active theme (child themes take precedence)', 'wp-native-articles' ); ?>
								<br />
								{template_url} - <?php esc_html_e( 'URL to the directory of your active theme (parent theme if using a child theme)', 'wp-native-articles' ); ?>
								<br />
								{plugins_url} - <?php esc_html_e( 'URL to your plugins directory', 'wp-native-articles' ); ?>
								<br />
								{content_url} - <?php esc_html_e( 'URL to your wp-content directory', 'wp-native-articles' ); ?>
								<br />
								{post_permalink} - <?php esc_html_e( 'Permalink of the current post', 'wp-native-articles' ); ?>
								<br />
								{post_title} - <?php esc_html_e( 'Title of the current post', 'wp-native-articles' ); ?>
							</p>
						</label>
						<hr />
					</div>

					<div id="wpna-placement-embed" class="wpna-placement-content-form <?php echo ( 'embed' !== $placement->content_type ) ? 'hidden' : ''; ?>">
						<label>
							<input id="wpna-placement-embed" placeholder="<?php esc_html_e( 'Embed URL', 'wp-native-articles' ); ?>" type="text" name="video_url" class="regular-text" value="<?php echo esc_attr( $placement->meta( 'video_url' ) ); ?>" />
							<p class="description">
								<?php esc_html_e( 'URL of the video embed.', 'wp-native-articles' ); ?>
							</p>
						</label>
					</div>

					<div id="wpna-placement-related-posts" class="wpna-placement-content-form <?php echo ( 'related_posts' !== $placement->content_type ) ? 'hidden' : ''; ?>">
						<label>
							<input id="wpna-placement-related-posts-title" placeholder="<?php esc_html_e( 'Related Posts Title', 'wp-native-articles' ); ?>" type="text" name="related_posts_title" class="regular-text" value="<?php echo esc_attr( $placement->meta( 'related_posts_title' ) ); ?>"/>
							<p class="description">
								<?php esc_html_e( 'An (optional) title for the Related Posts block', 'wp-native-articles' ); ?>
							</p>
						</label>
					</div>

				</td>
			</tr>

			<?php do_action( 'wpna_add_placement_form_before_position' ); ?>
			<tr>
				<th scope="row" valign="top">
					<label for="wpna-name"><?php esc_html_e( 'Position', 'wp-native-articles' ); ?></label>
				</th>
				<td>
					<div id="wpna-placement-position-top-wrap">
						<label>
							<input type="checkbox" name="position_top" id="wpna-placement-position-top" class="wpna-placement-position" value="true" <?php checked( (bool) $placement->meta( 'position_top' ) ); ?> />
							<?php esc_html_e( 'Top', 'wp-native-articles' ); ?>
						</label>
					</div>

					<div id="wpna-placement-position-bottom-wrap">
						<label>
							<input type="checkbox" name="position_bottom" id="wpna-placement-position-bottom" class="wpna-placement-position" value="true" <?php checked( (bool) $placement->meta( 'position_bottom' ) ); ?> />
							<?php esc_html_e( 'Bottom', 'wp-native-articles' ); ?>
						</label>
					</div>


					<div id="wpna-placement-position-paragraph-wrap">
						<label>
							<input type="checkbox" name="position_paragraph_toggle" id="wpna-placement-position-paragraph-toggle" class="wpna-placement-toggle" value="true" <?php checked( (bool) $placement->meta( 'position_paragraph' ) ); ?> />
							<?php esc_html_e( 'After Paragraph', 'wp-native-articles' ); ?>
						</label>

						<div id="wpna-placement-position-paragraph-form" class="hidden">
							<h3><?php esc_html_e( 'After Paragraph', 'wp-native-articles' ); ?></h3>
							<label>
								<p><?php esc_html_e( 'Insert after this paragraph', 'wp-native-articles' ); ?></p>
								<?php
								$paragraph = 0;
								if ( (bool) $placement->meta( 'position_paragraph' ) ) {
									$paragraph = $placement->meta( 'position_paragraph' );
									// Patch until we support multiple paragraphs.
									if ( is_array( $paragraph ) ) {
										$paragraph = $paragraph[0];
									}
								}
								?>
								<input type="number" name="position_paragraph" id="wpna-placement-position-paragraph" class="wpna-placement-position" value="<?php echo absint( $paragraph ); ?>" min="0" step="0" />
							</label>
						</div>
					</div>

					<div id="wpna-placement-position-words-wrap">
						<label>
							<input type="checkbox" name="position_words_toggle" id="wpna-placement-position-words-toggle" class="wpna-placement-toggle" value="true" <?php checked( (bool) $placement->meta( 'position_words' ) ); ?> />
							<?php esc_html_e( 'After Words', 'wp-native-articles' ); ?>
						</label>

						<div id="wpna-placement-position-words-form" class="hidden">
							<h3><?php esc_html_e( 'After Words', 'wp-native-articles' ); ?></h3>
							<label>
								<p><?php esc_html_e( 'Will be rounded to the nearest paragraph.', 'wp-native-articles' ); ?></p>
								<?php
								$words = 0;
								if ( (bool) $placement->meta( 'position_words' ) ) {
									$words = $placement->meta( 'position_words' );
									// Patch until we support multiple words.
									if ( is_array( $words ) ) {
										$words = $words[0];
									}
								}
								?>
								<input type="number" name="position_words" id="wpna-placement-position-words" class="wpna-placement-position" value="<?php echo absint( $words ); ?>" min="0" step="0" />
							</label>
						</div>
					</div>

					<?php do_action( 'wpna_edit_placement_form_position' ); ?>

					<p class="description"><?php esc_html_e( 'Where the code is positioned within the Instant Article.', 'wp-native-articles' ); ?></p>
				</td>
			</tr>

			<?php do_action( 'wpna_add_placement_form_before_filters' ); ?>
			<tr>
				<th scope="row" valign="top">
					<label for="wpna-placement-filters"><?php esc_html_e( 'Add Filter', 'wp-native-articles' ); ?></label>
				</th>
				<td>
					<div id="wpna-placement-filter-category-wrap">

						<label>
							<?php
							$filter_category = $placement->meta( 'filter_category' );
							?>
							<input type="checkbox" name="filter_category" id="wpna-placement-filter-category-toggle" class="wpna-placement-toggle" value="true" <?php checked( (bool) $filter_category ); ?> />
							<?php esc_html_e( 'Add Category Filter', 'wp-native-articles' ); ?>
						</label>

						<div id="wpna-placement-filter-category-form" class="hidden">

							<?php $terms = get_categories( array( 'hide_empty' => false ) ); ?>

							<h3><?php esc_html_e( 'Categories Filter', 'wp-native-articles' ); ?></h3>

							<?php
							if ( ! empty( $filter_category['category__in'] ) ) {
								$selected = $filter_category['category__in'];
							} else {
								$selected = array();
							}
							?>

							<label>
								<p><?php esc_html_e( 'Include Categories', 'wp-native-articles' ); ?></p>
								<select name="category__in[]" multiple="multiple" class="select2" style="width:25em;">
									<?php foreach ( $terms as $term ) : ?>
										<option value="<?php echo esc_attr( $term->term_id ); ?>"
											<?php selected( in_array( $term->term_id, $selected, true ) ); ?>
										>
											<?php echo esc_html( $term->name ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</label>

							<p class="description">
								<?php echo wp_kses(
									__( 'Only insert this placement into posts that <strong>HAVE</strong> these categories.', 'wp-native-articles' ),
									array( 'strong' => array() )
								); ?>
							</p>

							<br />
							<br />

							<?php
							if ( ! empty( $filter_category['category__not_in'] ) ) {
								$selected = $filter_category['category__not_in'];
							} else {
								$selected = array();
							}
							?>

							<label>
								<p><?php esc_html_e( 'Exclude Categories', 'wp-native-articles' ); ?></p>
								<select name="category__not_in[]" multiple="multiple" class="select2" style="width:25em;">
									<?php foreach ( $terms as $term ) : ?>
										<option value="<?php echo esc_attr( $term->term_id ); ?>"
											<?php selected( in_array( $term->term_id, $selected, true ) ); ?>
										>
											<?php echo esc_html( $term->name ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</label>

							<p class="description">
								<?php echo wp_kses(
									__( 'Only insert this placement into posts that <strong>DO NOT HAVE</strong> these categories.', 'wp-native-articles' ),
									array( 'strong' => array() )
								); ?>
							</p>

							<hr />

						</div>

					</div> <!-- ./wpna-placement-filter-category-wrap -->

					<div class="wpna-placement-filter-tag-wrap">
						<label>
							<?php
							$filter_tag = $placement->meta( 'filter_tag' );
							?>
							<input type="checkbox" name="filter_tag" id="wpna-placement-filter-tag-toggle" class="wpna-placement-toggle" value="true" <?php checked( (bool) $filter_tag ); ?> />
							<?php esc_html_e( 'Add Tag Filter', 'wp-native-articles' ); ?>
						</label>

						<div id="wpna-placement-filter-tag-form" class="hidden">

							<?php $terms = get_tags( array( 'hide_empty' => false ) ); ?>

							<h3><?php esc_html_e( 'Tag Filter', 'wp-native-articles' ); ?></h3>

							<?php
							if ( ! empty( $filter_tag['tag__in'] ) ) {
								$selected = $filter_tag['tag__in'];
							} else {
								$selected = array();
							}
							?>

							<label>
								<p><?php esc_html_e( 'Include Tags', 'wp-native-articles' ); ?></p>
								<select name="tag__in[]" multiple="multiple" class="select2" style="width:25em;">
									<?php foreach ( $terms as $term ) : ?>
										<option value="<?php echo esc_attr( $term->term_id ); ?>"
											<?php selected( in_array( $term->term_id, $selected, true ) ); ?>
										>
											<?php echo esc_html( $term->name ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</label>

							<p class="description">
								<?php echo wp_kses(
									__( 'Only insert this placement into posts that <strong>HAVE</strong> these tags.', 'wp-native-articles' ),
									array( 'strong' => array() )
								); ?>
							</p>

							<br />
							<br />

							<?php
							if ( ! empty( $filter_tag['tag__not_in'] ) ) {
								$selected = $filter_tag['tag__not_in'];
							} else {
								$selected = array();
							}
							?>

							<label>
								<p><?php esc_html_e( 'Exclude Tags', 'wp-native-articles' ); ?></p>
								<select name="tag__not_in[]" multiple="multiple" class="select2" style="width:25em;">
									<?php foreach ( $terms as $term ) : ?>
										<option value="<?php echo esc_attr( $term->term_id ); ?>"
											<?php selected( in_array( $term->term_id, $selected, true ) ); ?>
										>
											<?php echo esc_html( $term->name ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</label>

							<p class="description">
								<?php echo wp_kses(
									__( 'Only insert this placement into posts that <strong>DO NOT HAVE</strong> these tags.', 'wp-native-articles' ),
									array( 'strong' => array() )
								); ?>
							</p>

							<hr />

						</div>

					</div> <!-- ./wpna-placement-filter-tag-wrap -->

					<div class="wpna-placement-filter-author-wrap">
						<label>
							<?php
							$filter_author = $placement->meta( 'filter_author' );
							?>
							<input type="checkbox" name="filter_author" id="wpna-placement-filter-author-toggle" class="wpna-placement-toggle" value="true" <?php checked( (bool) $filter_author ); ?> />
							<?php esc_html_e( 'Add Author Filter', 'wp-native-articles' ); ?>
						</label>

						<div id="wpna-placement-filter-author-form" class="hidden">

							<?php $terms = get_users(
								array(
									'orderby' => 'nicename',
									'fields'  => array( 'ID', 'display_name' ),
								)
							); ?>

							<h3><?php esc_html_e( 'Author Filter', 'wp-native-articles' ); ?></h3>

							<?php
							if ( ! empty( $filter_author['author__in'] ) ) {
								$selected = $filter_author['author__in'];
							} else {
								$selected = array();
							}
							?>

							<label>
								<p><?php esc_html_e( 'Include Authors', 'wp-native-articles' ); ?></p>
								<select name="author__in[]" multiple="multiple" class="select2" style="width:25em;">
									<?php foreach ( $terms as $term ) : ?>
										<option value="<?php echo esc_attr( $term->ID ); ?>"
											<?php selected( in_array( (int) $term->ID, $selected, true ) ); ?>
										>
											<?php echo esc_html( $term->display_name ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</label>

							<p class="description">
								<?php echo wp_kses(
									__( 'Only insert this placement into posts that <strong>HAVE</strong> these authors.', 'wp-native-articles' ),
									array( 'strong' => array() )
								); ?>
							</p>

							<br />
							<br />

							<?php
							if ( ! empty( $filter_author['author__not_in'] ) ) {
								$selected = $filter_author['author__not_in'];
							} else {
								$selected = array();
							}
							?>

							<label>
								<p><?php esc_html_e( 'Exclude Authors', 'wp-native-articles' ); ?></p>
								<select name="author__not_in[]" multiple="multiple" class="select2" style="width:25em;">
									<?php foreach ( $terms as $term ) : ?>
										<option value="<?php echo esc_attr( $term->ID ); ?>"
											<?php selected( in_array( (int) $term->ID, $selected, true ) ); ?>
										>
											<?php echo esc_html( $term->display_name ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</label>

							<p class="description">
								<?php echo wp_kses(
									__( 'Only insert this placement into posts that <strong>DO NOT HAVE</strong> these authors.', 'wp-native-articles' ),
									array( 'strong' => array() )
								); ?>
							</p>

							<hr />

						</div>

					</div> <!-- ./wpna-placement-filter-author-wrap -->

					<div class="wpna-placement-filter-author-wrap">

						<?php
						$filter_custom = $placement->meta( 'filter_custom' );
						?>

						<label>
							<input type="checkbox" name="filter_custom_enable" id="wpna-placement-filter-custom-toggle" class="wpna-placement-toggle" value="true" <?php checked( (bool) $filter_custom ); ?> />
							<?php esc_html_e( 'Add Custom Filter', 'wp-native-articles' ); ?>
						</label>

						<div id="wpna-placement-filter-custom-form" class="hidden">

							<h3><?php esc_html_e( 'Custom Filter', 'wp-native-articles' ); ?></h3>

							<input type="text" name="filter_custom" class="regular-text" placeholder="e.g. post_type=movie&amp;tag_slug__in=action" value="<?php echo esc_attr( $filter_custom ); ?>" />
							<p class="description"><?php esc_html_e( 'Use this field to create a filter from custom WP_Query parameters. It is very powerful but be careful.', 'wp-native-articles' ); ?></p>

							<hr />

						</div>

					</div> <!-- ./wpna-placement-filter-custom-wrap -->

					<?php do_action( 'wpna_add_placement_form_filters' ); ?>

					<p class="description"><?php esc_html_e( 'Add filters to your placement to restrict the posts that it applies to.', 'wp-native-articles' ); ?></p>

				</td>
			</tr>

			<?php do_action( 'wpna_add_placement_form_before_start_date' ); ?>
			<tr>
				<th scope="row" valign="top">
					<label for="wpna-placement-start-date"><?php esc_html_e( 'Start Date', 'wp-native-articles' ); ?></label>
				</th>
				<td>
					<input type="date" id="wpna-placement-start-date" name="start_date" value="<?php echo esc_attr( date( 'Y-m-d', strtotime( $placement->start_date ) ) ); ?>" />
					<p class="description">
						<?php printf(
							// translators: Placeholder is the example date format.
							esc_html__( 'Enter the date when this placement becomes active in the format %s.', 'wp-native-articles' ),
							'<strong>dd/mm/yyyy</strong>'
						); ?>
					</p>
				</td>
			</tr>

			<?php do_action( 'wpna_add_placement_form_before_end_date' ); ?>
			<tr>
				<th scope="row" valign="top">
					<label for="wpna-placement-end-date"><?php esc_html_e( 'End Date', 'wp-native-articles' ); ?></label>
				</th>
				<td>
					<?php
					$end_date = '';
					if ( $placement->end_date ) {
						$end_date = date( 'Y-m-d', strtotime( $placement->end_date ) );
					}
					?>
					<input type="date" id="wpna-placement-end-date" name="end_date" value="<?php echo esc_attr( $end_date ); ?>" />
					<p class="description">
						<?php printf(
							// translators: Placeholder is the example date format.
							esc_html__( 'Enter the date when this placement becomes inactive in the format %s. For no end, leave blank.', 'wp-native-articles' ),
							'<strong>dd/mm/yyyy</strong>'
						); ?>
					</p>
				</td>
			</tr>

		</tbody>
	</table>

	<?php do_action( 'wpna_add_placement_form_bottom' ); ?>

	<p class="submit">
		<input type="hidden" name="placement_id" value="<?php echo absint( $placement->ID ); ?>" />
		<input type="hidden" name="wpna-action" value="edit_placement" />
		<input type="hidden" name="wpna-redirect" value="<?php echo esc_url( admin_url( 'admin.php?page=wpna_placements' ) ); ?>" />
		<input type="hidden" name="wpna-placement-nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpna_placement_nonce' ) ); ?>" />
		<input type="submit" name="submit" value="<?php esc_html_e( 'Update Placement', 'wp-native-articles' ); ?>" class="button-primary" />
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpna_placements' ) ); ?>" class="button-secondary"><?php esc_html_e( 'Cancel', 'wp-native-articles' ); ?></a>
	</p>

</form>
</div>
