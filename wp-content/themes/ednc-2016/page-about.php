<?php while (have_posts()) : the_post(); ?>
  <div class="container">
    <?php get_template_part('templates/components/page', 'header-wide'); ?>

    <?php get_template_part('templates/layouts/content', 'page-right-aside'); ?>

    <div class="row">
      <div class="col-xs-12">
        <h3 class="section-header">EdNC Staff</h3>
      </div>
    </div>

    <div class="row">
      <?php
      $args = array(
        'post_type' => 'bio',
        'post__in' => array(1647, 1663, 13081, 26641, 32468, 26684, 46947, 41796, 52642, 49249,  65207),   // Mebane, Alex, Nation, Liz, Nancy, Laura37074, , Molly, Caroline, Analisa, Yasmin, Robert, Rupen
        'posts_per_page' => -1,
        'orderby' => 'post__in',
        'order' => 'ASC'
      );

      $about = new WP_Query($args);

      if ($about->have_posts()) : while ($about->have_posts()) : $about->the_post(); ?>

      <div class="col-md-6 extra-bottom-margin-author">
        <div class="row">
          <div class="col-xs-5">
            <a href="<?php the_permalink(); ?>">
              <?php the_post_thumbnail('bio-headshot'); ?>
            </a>
          </div>
          <div class="col-xs-7">
            <h3 class="no-top-margin"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <h5 class="no-margin"><?php the_field('title'); ?></h5>
            <p class="caption"><?php the_field('tagline'); ?></p>
            <div class="excerpt">
              <?php the_advanced_excerpt(); ?>
              <a href="<?php the_permalink(); ?>" class="more">Read more &raquo;</a>
            </div>
          </div>
        </div>
      </div>

      <?php endwhile; endif; wp_reset_query(); ?>
    </div>
  </div>
<?php endwhile; ?>
