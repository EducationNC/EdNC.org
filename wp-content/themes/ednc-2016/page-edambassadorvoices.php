<?php while (have_posts()) : the_post(); ?>
 <div class="wrap" role="document">
      <div class="content clearfix">
        <main class="main">
            <div class="container">
    
<div class="page-header row">
  <div class="col-md-12 col-centered">
    <h1>EdAmbassadors</h1>
  </div>
</div>
 <div class="row">
  <div class="col-md-3 col-md-push-9">
    <a class="twitter-timeline" data-width="400" data-height="1000" href="https://twitter.com/EdAmbassadorsNC?ref_src=twsrc%5Etfw">Tweets by EdAmbassadorsNC</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>         
  </div>    
  <div class="row edambassadors">
 <div class="col-md-9 col-lg-8 col-md-pull-3">     
   
        <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="y2018">
            <?php
            $args = array(
              'post_type' => 'bio',
              'posts_per_page' => -1,
              'order' => 'ASC',
              'orderby' => 'meta_value title',
              'meta_key' => 'last_name_to_sort_by',
              'tax_query' => array(
                array(
                  'taxonomy' => 'author-type',
                  'field' => 'slug',
                  'terms' => 'edambassador'
                ),
                array(
                  'taxonomy' => 'author-year',
                  'field' => 'slug',
                  'terms' => '2018'
                )
              )
            );

 $edambassadors = new WP_Query($args);

            if ($edambassadors->have_posts()) : while ($edambassadors->have_posts()) : $edambassadors->the_post();
              $user = get_field('user');
              ?>

              <div class="col-sm-6 col-xs-6 block-person">
                <div class="position-relative">
                  <a class="mega-link" href="<?php echo get_author_posts_url($user['ID']); ?>"></a>
                  <div class="row">
                    <div class="col-sm-5">
                      <div class="circle-image"><?php the_post_thumbnail('bio-headshot'); ?></a></div>
                    </div>
                    <div class="col-sm-7"><h4 class="post-title"><?php the_title(); ?></h4></div>
                  </div>
                </div>
              </div>

            <?php endwhile; endif; wp_reset_query(); ?>
          </div>

          
    <?php endwhile; ?>
    