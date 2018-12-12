<style>

</style>


<?php

use Roots\Sage\Titles;

$term = get_queried_object();
$cat_id = $term->term_id;
$term_image = $term->term_image;
$term_image_src = wp_get_attachment_image_src($term_image, 'full');

if (isset($_GET['date'])) {
  $title = ": " . date('F j, Y', strtotime($_GET['date']));
}

if ( ! empty($term_image) ) { ?>
 
 <!-- OLD LAYOUT
 <header class="page-header photo-overlay" style="background-image: url('<?php// echo $term_image_src[0]; ?>')">
    <div class="article-title-overlay">
      <div class="container">
        <div class="row">
          <div class="col-md-12 col-centered">
            <h1 class="entry-title"> <?php // Titles\title() . $title; ?> <small><a href="feed"><span class="icon-rss"></span> RSS Feed</a></small></h1>
          </div>
        </div>
      </div>
    </div>
  </header>
  -->
  
  <header class="page-header page-header-auto background-purple photo-overlay no-padding" style="">
    <div class="category-header background-purple no-margin no-padding">
		<img src="<?php echo $term_image_src[0]; ?>" alt="<?= Titles\title() . $title; ?>" class="full-width">
    </div>
  </header>
  
<?php } else { ?>
  <div class="container page-header">
    <div class="row">
      <div class="col-md-12 col-centered">
        <h1 class="entry-title"><?= Titles\title() . $title; ?> <small><a href="feed"><span class="icon-rss"></span> RSS Feed</a></small></h1>
      </div>
    </div>
  </div>
<?php } ?>
  <div class="container">
	  <div class="row">
			<h1 class="h6 entry-title text-purple"><?= Titles\title() . $title; ?></h1>
	  </div>
  </div>