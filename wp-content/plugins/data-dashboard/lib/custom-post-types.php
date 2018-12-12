<?php

namespace Stringable\DataDash\CPT;

add_action( 'init', __NAMESPACE__ . '\\register_post_types');
function register_post_types() {
	register_post_type( 'data',
		array('labels' => array(
				'name' => 'Data Dashboard',
				'singular_name' => 'Data Dashboard',
				'add_new' => 'Add New Section',
				'add_new_item' => 'Add New Dashboard Section',
				'edit' => 'Edit',
				'edit_item' => 'Edit Dashboard Section',
				'new_item' => 'New Dashboard Section',
				'view_item' => 'View Dashboard Section',
				'search_items' => 'Search Dashboard Sections',
				'not_found' =>  'Nothing found in the Database.',
				'not_found_in_trash' => 'Nothing found in Trash',
				'parent_item_colon' => ''
			), /* end of arrays */
			'public' => true,
			'exclude_from_search' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_nav_menus' => false,
			'menu_position' => 8,
			//'menu_icon' => get_stylesheet_directory_uri() . '/library/images/custom-post-icon.png',
			'capability_type' => 'post',
			'hierarchical' => true,
			'supports' => array( 'title', 'revisions', 'page-attributes'),
			'has_archive' => true,
			'rewrite' => true,
			'query_var' => true
		)
	);

	register_post_type( 'data-viz',
		array('labels' => array(
				'name' => 'Data Viz',
				'singular_name' => 'Data Viz',
				'add_new' => 'Add New Data Viz',
				'add_new_item' => 'Add New Data Viz',
				'edit' => 'Edit',
				'edit_item' => 'Edit Data Viz',
				'new_item' => 'New Data Viz',
				'view_item' => 'View Data Viz',
				'search_items' => 'Search Data Viz',
				'not_found' =>  'Nothing found in the Database.',
				'not_found_in_trash' => 'Nothing found in Trash',
				'parent_item_colon' => ''
			), /* end of arrays */
			'public' => true,
			'exclude_from_search' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_nav_menus' => false,
			'menu_position' => 8,
			//'menu_icon' => get_stylesheet_directory_uri() . '/library/images/custom-post-icon.png',
			'capability_type' => 'post',
			// 'capabilities' => array(
			//   'edit_post'          => 'edit_data_viz',
			//   'read_post'          => 'read_data_viz',
			//   'delete_post'        => 'delete_data_viz',
			//   'edit_posts'         => 'edit_data_vizs',
			//   'edit_others_posts'  => 'edit_others_data_vizs',
			//   'publish_posts'      => 'publish_data_vizs',
			//   'read_private_posts' => 'read_private_data_vizs',
			//   'create_posts'       => 'create_data_vizs',
			// ),
			// 'map_meta_cap' => true,
			'hierarchical' => true,
			'supports' => array( 'title', 'revisions'),
			'has_archive' => false,
			'rewrite' => true,
			'query_var' => true
		)
	);
}

/**
 * Default template for archive page
 */
add_filter( 'archive_template', function($template) {
	global $post;
	if ( is_post_type_archive ( 'data' ) && (!$template || strpos($template, 'archive.php') !== false) ) {
		$template = realpath(__DIR__ . '/..') . '/templates/archive-data.php';
	}
	return $template;
});

/**
 * Default template for single data section & single data viz
 */
add_filter('single_template', function($template) {
	global $post;
	$post_type = get_post_type(get_queried_object_id());
  if ( ($post_type == 'data' || $post_type == 'data-viz') && (!$template || strpos($template, 'single.php') !== false) ) {
    $template = realpath(__DIR__ . '/..') . '/templates/redirect-single.php';
  }
  return $template;
});

/**
 * Default template for data viz embed
 */
add_filter( 'template_include', function($template) {
  global $post;
	if ('data-viz' == get_post_type(get_queried_object_id()) && is_embed() && (is_object($template)) || strpos($template, 'wp-includes' || strpos($template, 'themes'))) {
    $template = realpath(__DIR__ . '/..') . '/templates/embed-data-viz.php';
  }
	return $template;
}, 110 );
