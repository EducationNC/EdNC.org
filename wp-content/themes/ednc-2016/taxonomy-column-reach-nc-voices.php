<style>
	.entry-title{
		display:none;
	}
	.center-image{
		margin: 0 auto;
		text-align: center;
		margin-bottom: 20px;
		display: block;
	}
</style>

<?php
$term = get_queried_object();
$desc = category_description();
$cat_id = $term->term_id;

get_template_part('templates/components/category', 'header');
?>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div class="container">
  <div class="row">
    <div class="col-lg-8 col-md-9">
	<?php if ($desc && !isset($_GET['date'])) { ?>
        <div class="extra-bottom-margin">
          <?php echo $desc; ?>
        </div>
      <?php } ?>

    <div class="row hentry">
        <?php
        if (! empty($cat_id)) {
          $args = array(
            'post_type' => 'flash-cards',
            'posts_per_page' => -1,
            'cat' => $cat_id
          );

          $fc = new WP_Query($args);

          if ($fc->have_posts()) : while ($fc->have_posts()): $fc->the_post(); ?>

            <div class="col-sm-6">
              <div class="paperclip"></div>
              <?php get_template_part('templates/layouts/block', 'post'); ?>
            </div>

          <?php endwhile; endif; wp_reset_query();
        } ?>
	</div>
	
	<?php get_template_part('templates/layouts/archive', 'loop'); ?>
      
	
    </div>

    <div class="col-md-3 col-lg-push-1 sidebar">
    
    <!-- this is the sign up form -->
<div class="hidden-lg" style="height: 15px"></div>	 
	<div class="row " style="margin-bottom:30px; display:none;">
		<div class="col-md-6" style="text-align: center;">
			<a style="text-align: center;" href="https://twitter.com/share" class="twitter-share-button" data-show-count="false">Tweet</a><script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
		</div>	
		<div class="col-md-6">	
			<div  style="text-align: center;" class="fb-share-button" data-href="https://www.ednc.org/column/reach-nc-voices/" data-layout="button" data-size="small" data-mobile-iframe="true"><a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fwww.ednc.org%2Fcolumn%2Freach-nc-voices%2F&amp;src=sdkpreparse">Share</a></div>
		</div>	 
	</div>	 

	<h4 class="section-header" style="margin: 0 15px;">Get Updates</h4>
		<iframe id="embed21931" src="//app.cityzen.io/static/signup.html?pollId=3298&embedId=21931" width="100%" height="250" frameborder="0" scrolling="yes"></iframe><script type="text/javascript">(function (c, i, t, y, z, e, n, x) { x = c.createElement(y), n = c.getElementsByTagName(y)[0]; x.async = 1; x.src = t; n.parentNode.insertBefore(x, n); i.czEmpty = null;})(document, window, "//app.cityzen.io/Link?embedId=21931", "script");</script><div style="height: 10px"></div>
      <?php
      //get_template_part('templates/components/sidebar', 'category');

      if (is_tax('map-column')) {
        echo '<a href="/maps-archive" class="btn btn-default">Click here for an archive of all maps</a>';
      } ?>
	  
	  <?php
	    $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$author_id = 140;
		$args = array(
		'post_type' => 'bio',
		'meta_query' => array(
		  array(
			'key' => 'user',
			'value' => 140
		  )
		)
		);
		$bio = new WP_Query($args);
		
		if ($bio->have_posts()) : while ($bio->have_posts()) : $bio->the_post(); ?>
        <div class="row">
          <div class="col-sm-4 col-md-12">
            <?php if (
              $author->user_nicename != 'agranados' &&
              $author->user_nicename != 'lbell' &&
              $author->user_nicename != 'mrash' &&
              $author->user_nicename != 'nation-hahn' &&
              $author->user_nicename != 'todd-brantley' &&
              $author->user_nicename != 'staff'
            ) { ?>
              <div class="square-image center-image">
                <?php the_post_thumbnail('bio-headshot'); ?>
              </div>
            <?php } else {
              the_post_thumbnail('bio-headshot');
            } ?>
          </div>

          <div class="col-sm-8 col-md-12">
            <?php get_template_part('templates/components/author', 'excerpt'); ?>
          </div>
        </div>
		
        <div class="clearfix">
          <h3>Links</h3>
          <p><a class="btn btn-default" href="<?php echo get_author_feed_link($author_id); ?>"><span class="icon-rss"></span> Author RSS Feed</a></p>
		  <p><a class="btn btn-default" href="<?php echo get_category_feed_link($cat_id); ?>"><span class="icon-rss"></span> Category RSS Feed</a></p>
	
		  
		 <?php
          $extras = get_field('author_extras');
          if ($extras) {
            foreach ($extras as $e) {
              if ($e['acf_fc_layout'] == 'file') {
                echo '<p><a class="btn btn-default" href="' . $e['file']['url'] . '" target="_blank">';
                  echo $e['link_text'];
                echo '</a></p>';
              } elseif ($e['acf_fc_layout'] == 'link') {
                echo '<p><a class="btn btn-default" href="' . $e['url'] . '" target="_blank">';
                echo $e['link_text'];
                echo '</a></p>';
              }
            }
          }
          ?>
        </div>
      <?php endwhile; endif; wp_reset_query(); ?>

    </div>
  </div>
  <?php get_template_part('templates/components/social-share'); ?>
</div>

<script>(function (c, i, t, y, z, e, n, x) { x = c.createElement(y), n = c.getElementsByTagName(y)[0]; x.async = 1; x.src = t; n.parentNode.insertBefore(x, n); i.czen = { pub: z, dom: e };})(document, window, '//app.cityzen.io/static/pub.js', 'script', 1114, 1);</script>