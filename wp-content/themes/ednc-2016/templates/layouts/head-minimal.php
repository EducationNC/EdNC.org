<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="fb:pages" content="384676381708957" />

    <?php if (strtotime(get_the_modified_date()) > strtotime(get_the_date())) { ?>
        <meta name="revised" content="<?php echo get_the_modified_date('l, F j, Y'); ?>">
    <?php } ?>

    <link rel="alternate" type="application/rss+xml" title="<?php echo get_bloginfo('name'); ?> Feed" href="<?php echo esc_url(get_feed_link()); ?>">
    <link href="//fonts.googleapis.com/css?family=Lato:300,300italic,400,400italic,700,700italic|Merriweather:300,300italic,400,400italic,700,700italic|Open+Sans+Condensed:300" rel="stylesheet" type="text/css" />

    <?php wp_head(); ?>

    <?php

    if (!is_user_logged_in()) {
        get_template_part('templates/components/analytics');
    }

    ?>
</head>
