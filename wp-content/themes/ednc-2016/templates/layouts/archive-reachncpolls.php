<?php if (!have_posts()) : ?>
  <div class="alert alert-warning">
    Sorry, no results were found.
  </div>
  <?php get_search_form(); ?>
<?php endif; ?>

<?php
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : '1';

if ( is_user_logged_in() ) {
$args = array( 'post_type' => 'reach-nc-poll', 'paged' => $paged, 'posts_per_page' => 10,
		'meta_query'	=> array(
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
			) ,
			array(
				'key'		=> 'reach_privacy',
				'value'		=> 'Private',
				'compare'	=> 'LIKE'
			) 
			)
		);
}else{
$args = array( 'post_type' => 'reach-nc-poll', 'paged' => $paged, 'posts_per_page' => 10,
		'meta_query'	=> array(
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
			)
		);
}
$loop = new WP_Query( $args );
?>

<?php //while (have_posts()) : the_post(); ?>
<?php while ( $loop->have_posts() ) : $loop->the_post();  ?>
  <?php get_template_part('templates/layouts/block', 'reachncpolls'); ?>
<?php endwhile; ?>

<?php if ($loop->max_num_pages > 1) : ?>
  <nav class="post-nav">
    <?php wp_pagenavi(); ?>
  </nav>
<?php endif; ?>
