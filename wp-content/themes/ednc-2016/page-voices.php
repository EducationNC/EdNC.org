<?php while (have_posts()) : the_post(); ?>
  <div class="container narrow">
    <div class="row">
      <div class="col-xs-12">
        <h3 class="section-header">Team</h3>
      </div>
    </div>

    <div class="row staff">
      <?php
      $args = array(
        'post_type' => 'bio',
        'post__in' => array(1647, 1663, 13081, 26641, 32468, 26684, 46947, 41796, 52642, 49249, 65207),   // Mebane, Alex, Nation, Liz, Nancy, Laura37074, , Molly, Caroline, Analisa, Yasmin, Robert, Rupen 
        'posts_per_page' => -1,
        'orderby' => 'post__in',
        'order' => 'ASC'
      );

      $staff = new WP_Query($args);

      if ($staff->have_posts()) : while ($staff->have_posts()) : $staff->the_post();
        $user = get_field('user');
        ?>

        <div class="col-sm-3 col-xs-6 block-person">
          <div class="position-relative">
            <a class="mega-link" href="<?php echo get_author_posts_url($user['ID']); ?>"></a>
            <div class="overflow-hidden">
              <?php the_post_thumbnail('bio-headshot'); ?>
            </div>
            <h4 class="post-title"><?php the_title(); ?></h4>
          </div>
        </div>

      <?php endwhile; endif; wp_reset_query(); ?>
    </div>

<div class="container narrow">
    <div class="row">
      <div class="col-xs-12">
        <h3 class="section-header">Specialists</h3>
      </div>
    </div>


    <div class="row specialists">
      <div class="col-xs-12">

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
                  'terms' => 'specialist'
                ),
                array(
                  'taxonomy' => 'author-year',
                  'field' => 'slug',
                  'terms' => '2018'
                )
              )
            );

 $specialists = new WP_Query($args);

            if ($specialists->have_posts()) : while ($specialists->have_posts()) : $specialists->the_post();
              $user = get_field('user');
              ?>

              <div class="col-sm-4 col-xs-6 block-person">
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
    <div class="row">

<div class="container narrow">
    <div class="row">
      <div class="col-xs-12">
        <h3 class="section-header">Correspondents</h3>
      </div>
    </div>


    <div class="row correspondents">
      <div class="col-xs-12">

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
                  'terms' => 'correspondent'
                ),
                array(
                  'taxonomy' => 'author-year',
                  'field' => 'slug',
                  'terms' => '2018'
                )
              )
            );

 $correspondents = new WP_Query($args);

            if ($correspondents->have_posts()) : while ($correspondents->have_posts()) : $correspondents->the_post();
              $user = get_field('user');
              ?>

              <div class="col-sm-4 col-xs-6 block-person">
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
  <div class="row">
      <div class="col-xs-12">
        <h3 class="section-header">Edambassadors</h3>
      </div>
    </div>


    <div class="row edambassadors">
      <div class="col-xs-12">

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

              <div class="col-sm-4 col-xs-6 block-person">
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
    <div class="row">

      <div class="col-xs-12">
        <h3 class="section-header">Contributors</h3>
      </div>
    </div>

    <div class="row contributors">
      <div class="col-xs-12">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active"><a href="#y2018" aria-controls="y2018" role="tab" data-toggle="tab">2018</a></li>
			<li role="presentation"><a href="#y2017" aria-controls="y2017" role="tab" data-toggle="tab">2017</a></li>
          <li role="presentation"><a href="#y2016" aria-controls="y2016" role="tab" data-toggle="tab">2016</a></li>
	  <li role="presentation"><a href="#y2015" aria-controls="y2015" role="tab" data-toggle="tab">2015</a></li>
        </ul>

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
                  'terms' => 'contributor'
                ),
                array(
                  'taxonomy' => 'author-year',
                  'field' => 'slug',
                  'terms' => '2018'
                )
              )
            );

 $contributors = new WP_Query($args);

            if ($contributors->have_posts()) : while ($contributors->have_posts()) : $contributors->the_post();
              $user = get_field('user');
              ?>

              <div class="col-sm-4 col-xs-6 block-person">
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
           <div role="tabpanel" class="tab-pane" id="y2017">
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
                  'terms' => 'contributor'
                ),
                array(
                  'taxonomy' => 'author-year',
                  'field' => 'slug',
                  'terms' => '2017'
                )
              )
            );

 $contributors = new WP_Query($args);

            if ($contributors->have_posts()) : while ($contributors->have_posts()) : $contributors->the_post();
              $user = get_field('user');
              ?>

              <div class="col-sm-4 col-xs-6 block-person">
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

          <div role="tabpanel" class="tab-pane" id="y2016">
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
                  'terms' => 'contributor'
                ),
                array(
                  'taxonomy' => 'author-year',
                  'field' => 'slug',
                  'terms' => '2016'
                )
              )
            );

     $contributors = new WP_Query($args);

            if ($contributors->have_posts()) : while ($contributors->have_posts()) : $contributors->the_post();
              $user = get_field('user');
              ?>

              <div class="col-sm-4 col-xs-6 block-person">
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

          <div role="tabpanel" class="tab-pane" id="y2015">
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
                  'terms' => 'contributor'
                ),
                array(
                  'taxonomy' => 'author-year',
                  'field' => 'slug',
                  'terms' => '2015'
                )
              )
            );

            $contributors = new WP_Query($args);

            if ($contributors->have_posts()) : while ($contributors->have_posts()) : $contributors->the_post();
              $user = get_field('user');
              ?>

              <div class="col-sm-4 col-xs-6 block-person">
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
        </div>
      </div>
    </div>
  </div>
<?php endwhile; ?>
<script>(function (c, i, t, y, z, e, n, x) { x = c.createElement(y), n = c.getElementsByTagName(y)[0]; x.async = 1; x.src = t; n.parentNode.insertBefore(x, n); i.czen = { pub: z, dom: e };})(document, window, '//app.cityzen.io/static/pub.js', 'script', 1114, 1);</script>
