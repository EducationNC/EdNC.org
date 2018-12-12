<?php

use Roots\Sage\Assets;
use Roots\Sage\Media;
use Roots\Sage\Resize;

if ($post->post_type == 'post') {
  $video = has_post_format('video');

  $author_id = get_the_author_meta('ID');
  $author_bio = get_posts(array('post_type' => 'bio', 'meta_key' => 'user', 'meta_value' => $author_id));
  $author_avatar = get_field('avatar', $author_bio[0]->ID);
  $author_avatar_sized = Resize\mr_image_resize($author_avatar, 140, null, false, '', false);
}

if ( function_exists( 'coauthors_posts_links' ) ) {
  $authors = get_coauthors();
  foreach ($authors as $a) {
    $classes[] = $a->user_nicename;
  }
} else {
  $classes[] = get_the_author_meta('user_nicename');
}

$featured_image = Media\get_featured_image('medium');
$title_overlay = get_field('title_overlay');

$target = '';
if (is_embed()) {
  $target = 'target="_blank"';
}
?>

<article <?php post_class('block-post ' . implode($classes, ' ')); ?>>
	<div class="polls-box">
		<div class="polls-box-child polls-child-dev" style="background-image:url('<?php echo $featured_image; ?>');">
		</div>
		<div class="polls-box-container">
			<h2 class="text-white text-center"><?php the_title(); ?></h2>
			<?php
			    $content = get_the_content('Read more');
				//print $content;
				$parts = preg_split("/\\r\\n|\\r|\\n/", $content);
			?>
			<p class="text-white text-center"><?php echo $parts[0]; ?></p>
			<p><a href="<?php the_permalink(); ?>" class="btn-capitalize btn btn-primary btn-center">Get Started.</a>
			</p>
		</div>
	</div>
</article>
