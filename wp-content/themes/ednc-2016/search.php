<?php get_template_part('templates/components/page', 'header'); ?>

<div class="container">
  <div class="row">
    <div class="col-md-8 col-centered">
      <?php if (!have_posts()) : ?>
        <div class="alert alert-warning">
          <?php _e('Sorry, no results were found.', 'sage'); ?>
        </div>
        <?php get_search_form(); ?>
      <?php endif; ?>

      <?php while (have_posts()) : the_post(); ?>
	  
		<?php 
			$value = get_field( 'reach_privacy', get_the_ID() );
		if (  is_user_logged_in() || ($value != 'Private' || $value == null || $value == '') ){ 
		?>
			<?php if ($post->ID == 0) {
			  get_template_part('templates/layouts/content', 'search');
			} else {
			  get_template_part('templates/layouts/block', 'post-side'); 
			}?>
		<?php } else{
			
			 echo '<meta name="robots" content="noindex,nofollow">'; 
			
		}?>
		
      <?php endwhile; ?>

      <nav class="post-nav">
        <?php wp_pagenavi(); ?>
      </nav>
    </div>
  </div>
</div>
