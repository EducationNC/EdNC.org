<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/components/page', 'header'); ?>
  <?php get_template_part('templates/layouts/content', 'page'); ?>
<?php endwhile; ?>
<script>(function (c, i, t, y, z, e, n, x) { x = c.createElement(y), n = c.getElementsByTagName(y)[0]; x.async = 1; x.src = t; n.parentNode.insertBefore(x, n); i.czen = { pub: z, dom: e };})(document, window, '//app.cityzen.io/static/pub.js', 'script', 1114, 1);</script>