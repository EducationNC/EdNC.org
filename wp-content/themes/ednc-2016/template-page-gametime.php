<?php
/*
Template Name: Gametime
*/

use Roots\Sage\Titles;
?>
<style>
	.text-purple{
		color:#5c1043 !important;
	}
	.text-grey{
		color:#4d4c4d !important;
	}
	.text-white {
		color:#fff !important;
	}
	.bg-purple, .wrap{
		background-color:#5c1043 !important;
	}
	.bg-white{
		background-color:#fff !important;
	}
	.entry-title{
		text-align:center;
		text-shadow: none !important;
		margin-bottom:120px !important;
		font-size: 65px !important;
	}
	.entry-title span{
		display:block;
		font-size: 40px !important;
		line-height: 30px;
	}
	.page-header.photo-overlay {
		margin-bottom: 0em !important;
	}
	.page-header.photo-overlay:after{
		display:none;
	}
	.stylish-border {
		padding: 10px 20px;
		font-size: 30px;
		border-radius: 30px 30px 30px 0px;
	}
	.above-footer {
		padding: 0em 0 4em;
		display:none;
	}
	.float-container {
		position: relative;
		margin-top: -30px;
		margin-bottom: 80px;
	}
	.padding-md {
		padding: 50px 40px 0px;
	}
	.email-signup-form{
		background-image:none !important;
		background-color:#dcdfe6;
		padding:0px;
	}
	.email-signup-form .mc_embed_signup{
		background-color:#dcdfe6;
	}
	.email-signup-form .form-inline {
		margin: 30px 0px;
	}
	.full-width{
		width:100% !important;
	}
	.no-padding-right{
		padding-right:0px;
	}
	.no-padding-left{
		padding-left:0px;
	}
	
	.margin-top-20pc{
		margin-top:20%;
	}
	.image-center{
		margin:0px auto;
		display:block;
		text-align:center;
	}
	
	@media (max-width: 980px){
		.page-header.photo-overlay {
			height: 230px;
		}
		.entry-title {
			margin-bottom: 90px !important;
		}
	}
	@media (max-width: 780px){
		.margin-top-20pc{
			margin-top:0%;
		}
		.col-centered{
			padding:0px;
		}
		.no-padding-left, .no-padding-right{
			padding-left: 10px;
			padding-right: 10px;
		}
	}
	@media (max-width: 640px){
		.page-header.photo-overlay .entry-title {
			font-size: 3rem !important;
		}
		.page-header.photo-overlay .entry-title span{
			font-size: 1.5rem !important;
		}
		.stylish-border {
			padding: 8px 16px;
			font-size: 20px;
		}
		.col-centered {
			padding: 10px;
		}
	}
</style>

<?php while (have_posts()) : the_post(); ?>
<?php //get_template_part('templates/components/page', 'header'); ?>

<?php

	$source = '';
	if (isset($_GET['utm_source'])) {
	  $source = $_GET['utm_source'];
	}
	
	$image_id = get_post_thumbnail_id();
	$featured_image_lg = wp_get_attachment_image_src($image_id, 'large');
	?>

	<?php if (has_post_thumbnail() && !is_search()) { ?>
	  <header class="page-header photo-overlay" style="background-image: url('<?php echo $featured_image_lg[0]; ?>')">
		<div class="article-title-overlay">
		  <div class="container">
			<div class="row">
			  <div class="col-md-8 col-centered">
				<?= Titles\title(); ?>
			  </div>
			</div>
		  </div>
		</div>
	  </header>
	<?php } else { ?>
	  <div class="container page-header">
		<div class="row">
		  <div class="col-md-8 col-centered">
			<?= Titles\title(); ?>
		  </div>
		</div>
	  </div>
	<?php } ?>

  
  <?php //get_template_part('templates/layouts/content', 'page'); ?>
  
	<div class="container ">
	  <div class="row">
		<div class="col-md-8 col-centered">
		  <?php the_content(); ?>
		  <?php get_template_part('templates/components/email-signup-for-gametime-page'); ?>
		  
		  <?php wp_link_pages(['before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']); ?>
		</div>
	  </div>
	</div>

<?php endwhile; ?>
