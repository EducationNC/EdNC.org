<?php
/*
 * Template Name: Page Minimal
 * Template Post Type: post, page, product
 */

?>
<?php while (have_posts()) : the_post(); ?>
    <?php get_template_part('templates/components/page', 'header'); ?>
    <?php get_template_part('templates/layouts/content', 'page'); ?>
<?php endwhile; ?>

