<?php
/*
Template Name: Join Voices Template
*/
?>
<style>
	body{
		background-color:#904477;
		color:#fff;
	}
	
	.h3, h3{
		color:#fff;
	}

	.page-header.photo-overlay{
		background-position: center top;
		height: 450px;
		margin-bottom: 0em;
	}
	.page-header.photo-overlay .article-title-overlay{
		top: 15%;
		text-align: center;
	}
	 .above-footer{
		display:none;
	}
	
	.main .container{
		width:100%;
		margin-top: -15px;
	}
	.main #joinvoices{
		background-image:url('https://www.ednc.org/wp-content/uploads/2017/02/EDNC-Join-NC-Voices.jpg');
		background-position:top center;
		background-repeat:no-repeat;
		background-size:cover;
	}
	
	.page-form-joinvoices{
		background-color:#5b1143;
		max-width: 800px;
		width: 80%;
		margin: 0 auto;
		height: 250px;
		position: relative;
		margin-top: -200px;
		z-index: 9999;
		margin-bottom: 20px;
	}
	
	.page-form-joinvoices h2{
		color:#fff;
		font-weight:bold;
		font-size:48px;
		padding:30px 0px 0px;
	}
	
	.chat-button{
		max-width:615px;
	}
	
	@media (max-width: 980px){
		.page-header.photo-overlay{
			height: 350px;
		}
		.chat-button{
			max-width: 75%;
		}
	}
	@media (max-width: 760px){
		.page-header.photo-overlay{
			height: 250px;
		}
		.page-form-joinvoices{
			height: 350px;
		}
	}
	@media (max-width: 560px){
		.page-form-joinvoices{
			height: 370px;
		}
		.page-form-joinvoices{
			margin-top: -120px;
		}
	}
	
</style>


<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/components/page', 'header'); ?>
  
	<div id="joinvoices" class="container">
	  <div class="row">
		<div class="col-md-12 col-centered">
		  <?php the_content(); ?>
		  <?php wp_link_pages(['before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']); ?>
		</div>
	  </div>
	</div>

<?php endwhile; ?>


  <script type="text/javascript">
	setTimeout(function() {
		var $head = jQuery("#embed21933").contents().find("head");                
		$head.append(jQuery("<link/>", 
			{ rel: "stylesheet", href: "file://path/to/style.css", type: "text/css" }));
	}, 10); 
    </script>

