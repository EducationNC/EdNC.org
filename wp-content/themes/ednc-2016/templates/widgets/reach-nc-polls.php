<style>

	/*.max-width-300{
		width:300px;
	}
	.widget_reach_nc_polls{
		padding:0px !important;
	}
	.widget_reach_nc_polls #home-reach-nc-poll-block .full-width-btn{
		width:90% !important;
		position: absolute;
		bottom: 25px;
	}
	*/
	.widget_reach_nc_polls #home-reach-nc-poll-block .overlay{
		background: rgba(0, 0, 0, 0.5);
	}
	/*
	.widget_reach_nc_polls #home-reach-nc-poll-block .overlay{
		padding:2em 0 4em;
	}
	.widget_reach_nc_polls #home-reach-nc-poll-block .overlay h3{
		font-size: 44px;
	}
	.widget_reach_nc_polls #home-reach-nc-poll-block .overlay .row{
		padding:2em 0;
	}
	.widget_reach_nc_polls #home-reach-nc-poll-block .overlay article{
		z-index: 9;
		position: relative;
	}
	.widget_reach_nc_polls #home-reach-nc-poll-block .overlay .shadow{
		position: absolute;
		box-shadow: 0px 0px 80px #000;
		width: 95%;
		height: 95%;
		top: 6px;
		z-index: 0;
	}
	.widget_reach_nc_polls #home-reach-nc-poll-block .overlay .polls-box{
		margin-bottom: 0px;
	}
	.widget_reach_nc_polls #home-reach-nc-poll-block .overlay .polls-box .polls-box-container{
		min-height:320px;
	}
	.widget_reach_nc_polls #home-reach-nc-poll-block .overlay .col-sm-4{
		margin-bottom:30px;
	}
	.widget_reach_nc_polls #home-reach-nc-poll-block .overlay .col-sm-4:first-child article{
		clip-path: polygon(0 2%, 100% 0, 100% 100%, 0% 98%);
	}
	.widget_reach_nc_polls #home-reach-nc-poll-block .overlay .col-sm-4:last-child article{
		clip-path: polygon(0% 0%, 100% 2%, 100% 98%, 0% 100%);
	}*/
	p.lead{
		font-size: 18px;
	}

</style>
<?php

use Roots\Sage\Assets;
use Roots\Sage\Media;
use Roots\Sage\Resize;

global $featured_ids;

if (empty($featured_ids)) {
  $featured_ids = array();
}

$featured_image = Media\get_featured_image('medium');
$title_overlay = get_field('title_overlay');

//$args = array( 'post_type' => 'reach-nc-poll', 'posts_per_page' => 1, 'category_name' => 'Featured Reach');
$args = array( 'post_type' => 'reach-nc-poll', 'posts_per_page' => 1, 'meta_key'		=> 'reach_privacy', 'meta_value'	=> 'Featured');
$loop = new WP_Query( $args );

function limit_text($text, $limit) {
      if (str_word_count($text, 0) > $limit) {
          $words = str_word_count($text, 2);
          $pos = array_keys($words);
          $text = substr($text, 0, $pos[$limit]) . '...';
      }
      return $text;
    }
?>

<div id="home-reach-nc-poll-block" class="" style="background-image:url('<?php echo $bg_url; ?>');background-size: cover; background-position:50% 50%;">
	<div class="overlay">
		<div class="container ">
		  <h3 class="text-white text-center"><?php echo  $title ; ?></h3>
		 <p class="text-white text-center"><?php echo  $msg ; ?></p>
		  <div class="row">
			<?php 
			 while ( $loop->have_posts() ) : $loop->the_post();  ?>
			  <div class="col-sm-12">
				<article class="homepage-reach-nc-polls">
				<h2 class="text-white text-center"><?php the_title(); ?></h2>
				<div class="well">
					<?php						
						$content = get_the_content('Read more');
						print $content;
					?>
				</div>				
				</article>
				<div class="shadow"></div>
			  </div>
			  <?php  endwhile; ?>
		  </div>
			
		   <a class="btn-capitalize btn btn-primary btn-center max-width-300" href="column/reach-nc-polls/">See More</a>
		   
		</div>
	</div>
</div>


<!-- Older version. We might keep this. I think it's better, but the client wants th current version.  -->


<style>
	/*
	.max-width-300{
		width:300px;
	}
	.widget_reach_nc_polls{
		padding:0px !important;
	}
	.widget_reach_nc_polls #home-reach-nc-poll-block .full-width-btn{
		width:90% !important;
		position: absolute;
		bottom: 25px;
	}
	.widget_reach_nc_polls #home-reach-nc-poll-block .overlay{
		background: rgba(0, 0, 0, 0.5);
	}
	.widget_reach_nc_polls #home-reach-nc-poll-block .overlay{
		padding:2em 0 4em;
	}
	.widget_reach_nc_polls #home-reach-nc-poll-block .overlay h3{
		font-size: 44px;
	}
	.widget_reach_nc_polls #home-reach-nc-poll-block .overlay .row{
		padding:2em 0;
	}
	.widget_reach_nc_polls #home-reach-nc-poll-block .overlay article{
		z-index: 9;
		position: relative;
	}
	.widget_reach_nc_polls #home-reach-nc-poll-block .overlay .shadow{
		position: absolute;
		box-shadow: 0px 0px 80px #000;
		width: 95%;
		height: 95%;
		top: 6px;
		z-index: 0;
	}
	.widget_reach_nc_polls #home-reach-nc-poll-block .overlay .polls-box{
		margin-bottom: 0px;
	}
	.widget_reach_nc_polls #home-reach-nc-poll-block .overlay .polls-box .polls-box-container{
		min-height:320px;
	}
	.widget_reach_nc_polls #home-reach-nc-poll-block .overlay .col-sm-4{
		margin-bottom:30px;
	}
	.widget_reach_nc_polls #home-reach-nc-poll-block .overlay .col-sm-4:first-child article{
		clip-path: polygon(0 2%, 100% 0, 100% 100%, 0% 98%);
	}
	.widget_reach_nc_polls #home-reach-nc-poll-block .overlay .col-sm-4:last-child article{
		clip-path: polygon(0% 0%, 100% 2%, 100% 98%, 0% 100%);
	}
	p.lead{
		font-size: 18px;
	}
	*/

</style>




<?php

/*

use Roots\Sage\Assets;
use Roots\Sage\Media;
use Roots\Sage\Resize;

global $featured_ids;

if (empty($featured_ids)) {
  $featured_ids = array();
}

$featured_image = Media\get_featured_image('medium');
$title_overlay = get_field('title_overlay');

$args = array( 'post_type' => 'reach-nc-poll', 'posts_per_page' => 3, 'category_name' => 'Featured Reach');
$loop = new WP_Query( $args );

function limit_text($text, $limit) {
      if (str_word_count($text, 0) > $limit) {
          $words = str_word_count($text, 2);
          $pos = array_keys($words);
          $text = substr($text, 0, $pos[$limit]) . '...';
      }
      return $text;
    }
?>

<div id="home-reach-nc-poll-block" class="" style="background-image:url('<?php echo $bg_url; ?>');background-size: cover; background-position:50% 50%;">
	<div class="overlay">
		<div class="container">
		  <h3 class="text-white text-center"><?php echo  $title ; ?></h3>
		 <p class="text-white text-center"><?php echo  $msg ; ?></p>
		  <div class="row">
			<?php 
			 while ( $loop->have_posts() ) : $loop->the_post();  ?>
			  <div class="col-sm-4">
				<article class="homepage-reach-nc-polls">
					<div class="polls-box">
						<div class="polls-box-child polls-child-dev" style="background-image:url('<?php the_post_thumbnail_url( 'full' ); ?>');">
						</div>
						<div class="polls-box-container">
							<h2 class="text-white text-center"><?php the_title(); ?></h2>
							<?php
								$content = get_the_content('Read more');
								//print $content;
								$parts = preg_split("/\\r\\n|\\r|\\n/", $content);
							?>
								<?php echo limit_text($parts[0], 10); ?>
							<a href="<?php the_permalink(); ?>" class="full-width-btn btn-capitalize btn btn-primary btn-center">Get Started.</a>
							
						</div>
					</div>
				</article>
				<div class="shadow"></div>
			  </div>
			  <?php  endwhile; ?>
		  </div>
			
		   <a class="btn-capitalize btn btn-primary btn-center max-width-300" href="column/reach-nc-polls/">See More</a>
		    <!--
		    <h2 class="text-center text-white h1 mb-4">Share Your Voice</h2>
		    
		    <div class="well">
		    <h3 class="text-center h4">Raleigh and Washington D.C.</h3>
		    <p class="lead">This questionnaire covers a broad set of topics to help us more completely understand beliefs about policy and governance.</p>
		    <iframe id="embed61971" src="//app.cityzen.io/display/?projId=1790&embedId=61971" width="100%" height="425" frameborder="0" scrolling="yes"></iframe><script type="text/javascript">(function (c, i, t, y, z, e, n, x) { x = c.createElement(y), n = c.getElementsByTagName(y)[0]; x.async = 1; x.src = t; n.parentNode.insertBefore(x, n); })(document, window, "https://app.cityzen.io/Link?embedId=61971", "script");</script>
		    </div>-->
		</div>
	</div>
</div>
/*/ 
?>

