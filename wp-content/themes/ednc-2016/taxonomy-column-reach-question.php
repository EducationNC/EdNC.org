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

<div class="container ">
  <div class="row">
    <div class="col-lg-8 col-md-9 col-centered">
	<?php if ($desc && !isset($_GET['date'])) { ?>
        <div class="extra-bottom-margin">
          <?php echo $desc; ?>
        </div>
      <?php } ?>

    <div class="row hentry reach-nc-polls">
	</div>
	
	<?php get_template_part('templates/layouts/archive', 'reachquestion'); ?>
      
	
    </div>

  </div>
  <?php get_template_part('templates/components/social-share'); ?>
</div>

<script>(function (c, i, t, y, z, e, n, x) { x = c.createElement(y), n = c.getElementsByTagName(y)[0]; x.async = 1; x.src = t; n.parentNode.insertBefore(x, n); i.czen = { pub: z, dom: e };})(document, window, '//app.cityzen.io/static/pub.js', 'script', 1114, 1);</script>