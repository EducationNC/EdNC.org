<?php
/**
 * Sage includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/sage/pull/1042
 */
$sage_includes = [
  'lib/setup.php',              // Theme setup
  'lib/assets.php',             // Scripts and stylesheets
  'lib/admin.php',              // WP-Admin customizations
  'lib/custom-post-types.php',  // Custom post types and custom taxonomies
  'lib/acf-fields.php',         // ACF custom fields
  'lib/custom-pub-date.php',    // Add custom field for updated date
  'lib/extras.php',             // Custom functions
  'lib/facebook-auth.php',      // Facebook auth - PRIVATE
  'lib/feeds.php',              // Custom RSS feeds
  'lib/media.php',              // Image and other media functions
  'lib/resize.php',             // Magic image resizer
  'lib/shortcodes.php',         // Shortcodes and UI
  'lib/titles.php',             // Page titles
  'lib/nav.php',                // Clean up nav menus
  'lib/nav-data-dashboard.php', // Data dashboard nav walker
  'lib/nav-widgets.php',        // Widgetize nav menus
  'lib/wrapper.php',            // Theme wrapper class
  'lib/customizer.php',         // Theme customizer
  'lib/widgets/register.php',   // Register widgets
  'lib/social-share-count.php'  // Social share counts
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);

add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}


function basic_wp_seo() {
	global $post;
	// $default_keywords = 'wordpress, plugins, themes, design, dev, development, security, htaccess, apache, php, sql, html, css, jquery, javascript, tutorials'; // customize
	$output = '';

	// keywords
	$keys = get_post_meta($post->ID, 'mm_seo_keywords', true);
	$cats = get_the_category();
	$tags = get_the_tags();
	if (empty($keys)) {
		if (!empty($cats)) foreach($cats as $cat) $keys .= $cat->name . ', ';
		// if (!empty($tags)) foreach($tags as $tag) $keys .= $tag->name . ', ';
		$keys .= $default_keywords;
	}
	$output .= "\t\t" . '<meta name="categories" content="' . esc_attr($keys) . '">' . "\n";

	return $output;
}


function add_author_meta() {

    if (is_single()){
        global $post;
        $author = get_the_author_meta('display_name', $post->post_author);
        echo "<meta name=\"author\" content=\"$author\">";
    }
}
// add_action( 'wp_enqueue_scripts', 'add_author_meta' );
















/*ADMIN DASHBOARD CSS*/
function admin_style() {
  wp_enqueue_style('admin-styles', get_template_directory_uri().'/admin.css');
}
add_action('admin_enqueue_scripts', 'admin_style');

/*ADMIN DASHBOARD AUTO SELECT TAXONOMY*/
function set_default_object_terms( $post_id, $post ) {
	if ( 'publish' === $post->post_status && $post->post_type === 'reach-nc-poll' ) {
		$defaults = array(
			'column' => array( 'Reach NC Polls' )
			);
		$taxonomies = get_object_taxonomies( $post->post_type );
		foreach ( (array) $taxonomies as $taxonomy ) {
			$terms = wp_get_post_terms( $post_id, $taxonomy );
			if ( empty( $terms ) && array_key_exists( $taxonomy, $defaults ) ) {
				wp_set_object_terms( $post_id, $defaults[$taxonomy], $taxonomy );
			}
		}
	}
}
add_action( 'save_post', 'set_default_object_terms', 0, 2 );

function posts_orderby_lastname ($orderby_statement)
{
	$orderby_statement = "RIGHT(post_title, LOCATE(' ', REVERSE(post_title)) - 1) ASC";
    return $orderby_statement;
}


function my_recent_posts_shortcode($atts){
	 ob_start();
	$a = shortcode_atts( array(
		'item' => 'item',
	), $atts );


	 $q = new WP_Query(
	   array( 'orderby' => 'date', 'posts_per_page' => $a['item'],)
	 );


	while($q->have_posts()) : $q->the_post();

		 get_template_part('templates/layouts/block', 'post-shortcode');

	endwhile;

	wp_reset_query();


	$output = ob_get_clean();  return $output;
}

add_shortcode('recent-posts', 'my_recent_posts_shortcode');


//exclude private
add_filter( 'pre_get_posts', 'exclude_private_post' );
function exclude_private_post( $query ) {
		$excluded_ids  = array();
		$id;

    if ( !is_user_logged_in() && ($query->is_search() || $query->is_main_query()) ) {

		/*
        $query->set( 'post_type', array( 'reach-nc-poll' ) );
        $query->set( 'meta_query', array(
			'relation'		=> 'OR',
            array(
				'key'	 	=> 'reach_privacy',
				'value'	  	=> 'Public',
				'compare' 	=> 'LIKE',
            ),
			array(
				'key'		=> 'reach_privacy',
				'value'		=> 'Featured',
				'compare'	=> 'LIKE'
			)
        ) );

		$args = array( 'post_type' => 'reach-nc-poll', 'meta_key' => 'reach_privacy', 'meta_value'	=> 'Private');
		$loop = new WP_Query( $args );
		while ( $loop->have_posts() ) : $loop->the_post();
			$excluded_ids[] = get_the_ID() ;
		endwhile;
		$query->set( 'post__not_in',$excluded_ids );
		*/
    }
    else if ( is_user_logged_in() && ($query->is_search() || $query->is_main_query()) ) {

		/*
        $query->set( 'post_type', array( 'reach-nc-poll' ) );
        $query->set( 'meta_query', array(
			'relation'		=> 'OR',
            array(
				'key'	 	=> 'reach_privacy',
				'value'	  	=> 'Public',
				'compare' 	=> 'LIKE',
            ),
			array(
				'key'		=> 'reach_privacy',
				'value'		=> 'Featured',
				'compare'	=> 'LIKE'
			),
			array(
				'key'		=> 'reach_privacy',
				'value'		=> 'Private',
				'compare'	=> 'LIKE'
			)
        ) );
		DO NOTHING
		*/
    }

    return $query;
}
