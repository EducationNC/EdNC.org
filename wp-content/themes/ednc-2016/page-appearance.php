<?php
/*
 * Template Name: Page Minimal
 * Template Post Type: post, page, product
 */

?>
<div class="container">
  <div class="row">
<?php while (have_posts()) : the_post(); ?>
    <?php get_template_part('templates/components/page', 'header'); ?>
    <?php get_template_part('templates/layouts/content', 'page'); ?>
<?php endwhile; ?>
<?php

 $oldest = get_posts( 'post_type=post&post_status=publish&posts_per_page=1&order=ASC' );
    $oldest_date = $oldest[0]->post_date;

    $first_date = date('Y', strtotime($oldest_date));
    $todays_date = date('Y');

    $year_range = range($todays_date, $first_date);

    foreach ($year_range as $year) { // dynamic year-based tables
        echo '<h2>' . $year . '</h2>';
        $terms = get_terms('appearance');

        $term_slugs = array();

        if ( !empty( $terms ) && !is_wp_error( $terms ) ) { // table body

            foreach ( $terms as $key=>$term){
                $term_slugs[$key] = $term->slug;
            }

            echo '
                <table class="statistics">
                <tbody>
                ';
            echo '
                <thead>
                    <tr>
                        <td>Taxonomy Term</td>
                        <td class="chart-count">Count</td>
                    </tr>
                </thead>
                ';

            $posts_count = array(); // Holds all term post counts in an array
            $terms_array = array();  // Holds all term names in an array

                $args = array(
                    'posts_per_page'    => -1,
                    'post_type'         => 'post',
                    'post_status'       => 'publish',
                    'year'              => $year,
                    'tax_query' => array(
                        array(
                            'taxonomy'          => 'appearance',
                            'field'             => 'slug',
                            'terms'             => $term_slugs,
                            'include_children'  => false
                        ),
                    ),
                );

                $yearly_posts_per_term = new WP_Query($args);
                    $posts_count[] = $yearly_posts_per_term->post_count; //Collects post counts and send them to an array

                if($yearly_posts_per_term->have_posts()):
                    while($yearly_posts_per_term->have_posts()): $yearly_posts_per_term->the_post();

                        $terms = get_the_terms( $post->ID, 'appearance' );

                        if ( $terms && ! is_wp_error( $terms ) ) {
                            foreach ( $terms as $term ) {
                                $terms_array[] = $term->slug;
                            }
                        }

                    endwhile;
                endif;

        }

        $total_posts = array_sum($posts_count); //Use array_sum to add up all the separate post counts

        $result = array_count_values($terms_array);

        foreach ($result as $term_name=>$count) {

            $percentage = round( (($count / $total_posts)*100), 2 ); //Calculate the percentages of each term post cound to total year post count

            echo '
                    <tr>
                        <td class="chart-item">'.$term_name.'</td>
                        <td class="chart-count">'.$count.'</td>
                    </tr>
                ';
        }

            echo '
                <tfoot>
                    <tr>
                    <td colspan="2">Posts total</td>
                    <td class="chart-count">'.$total_posts.'</td>
                    </tr>
                </tfoot>
                ';

            echo '
                </tbody>
                </table>
                ';
    } // end of year-based list

?>
 </div>
  </div>
