<?php
// Set up variables
$id = get_the_ID();
$title = get_the_title();
$permalink = get_the_permalink();
$post_name = $post->post_name;
$prefix = '_dd_';
$d = [
  'type' => get_post_meta( $id, $prefix . 'type', true),
  'data_source' => get_post_meta( $id, $prefix . 'data_source', true),
  'options' => get_post_meta( $id, $prefix . 'options', true),
  'columns' => get_post_meta( $id, $prefix . 'columns', true),
  'cartodb_url' => get_post_meta( $id, $prefix . 'cartodb_url', true),
  'static_map' => get_post_meta( $id, $prefix . 'static_map_image', true),
  'text-based_data' => get_post_meta( $id, $prefix . 'text-based_data', true),
  'notes' => get_post_meta( $id, $prefix . 'notes', true),
  'source' => get_post_meta( $id, $prefix . 'source', true),
];
$content = $d['text-based_data'];

// Create plain text source. Clean up HTML and add link URLs to end of each link text inside square brackets
$source_html = $d['source'];
preg_match_all('/https?\:\/\/[^\"\' \n]+/i', $source_html, $matches);
// Loop through resulting matches
foreach ($matches[0] as $match) {
  // Get location of URL
  $url_pos = strpos($source_html, $match);
  // Find first occurance of </a> after URL
  $end_a_pos = strpos($source_html, '</a>', $url_pos);
  $source_html = substr($source_html, 0, $end_a_pos) . ' [' . $match . '] ' . substr($source_html, $end_a_pos);
}
$source_plain = trim(strip_tags($source_html));

/**
 * For sections that use Google Charts API
 */
if ($d['type'] == 'bar_chart' || $d['type'] == 'scatter_chart' || $d['type'] == 'pie_chart' || $d['type'] == 'table') {

  // Create array to hold everything that will be passed to JS
  $vars = [
    'title' => $title,
    'post_name' => str_replace('-', '_', $post_name),
    'source' => $source_plain,
    'd' => $d
  ];

  // Set chart type var
  switch ($d['type']) {
    case 'bar_chart':
      $vars['type'] = 'ColumnChart';
      break;
    case 'pie_chart':
      $vars['type'] = 'PieChart';
      break;
    case 'scatter_chart':
      $vars['type'] = 'ScatterChart';
      break;
    case 'table':
      $vars['type'] = 'Table';
      break;
    default:
      $vars['type'] = '';
  }

  // JSON encode variables to pass to JS
  $json_vars = json_encode($vars);
} else {
  $vars['type'] = '';
}
?>

<div class="row data-section well <?php if (!empty($vars['type'])) echo 'has-data-viz'; ?>" id="<?php echo $post_name; ?>">
  <div class="col-md-3">
    <p class="no-bottom-margin"><?php the_title(); ?></p>
    <?php
    if (is_embed()) {
      $site_title = sprintf(
        '<a class="embed-credit" href="%s" target="_blank"><img src="%s" srcset="%s 2x" width="64" height="64" alt="%s" /></a>',
        esc_url( get_the_permalink() ),
        esc_url( get_site_icon_url( 64, admin_url( 'images/w-logo-blue.png' ) ) ),
        esc_url( get_site_icon_url( 128, admin_url( 'images/w-logo-blue.png' ) ) ),
        esc_html( get_bloginfo( 'name' ) )
      );

      /**
       * Filter the site title HTML in the embed footer.
       *
       * @since 4.4.0
       *
       * @param string $site_title The site title HTML.
       */
      echo apply_filters( 'embed_site_title_html', $site_title );
    }
    ?>
  </div>

  <div class="col-md-9 panel panel-default">
    <div class="panel-body">
      <?php if ($d['type'] == 'bar_chart' || $d['type'] == 'scatter_chart' || $d['type'] == 'pie_chart' || $d['type'] == 'table') {
        if (!empty($d['data_source'])) {
          ?>

          <div class="loading print" id="viz_png_<?php echo $vars['post_name']; ?>">
            <?php
            $upload_dir = wp_upload_dir();
            $filename = '/data-viz/' . str_replace('-', '_', $post_name) . '.png';
            if (file_exists($upload_dir['basedir'] . $filename)) {
              echo '<img src="' . $upload_dir['baseurl'] . $filename . '" />';
            }
            ?>
            <div class="loader hidden-print"></div>
          </div>
          <div class="hidden-print print-no" id="viz_lg_<?php echo $vars['post_name']; ?>"></div>
          <div class="hidden-print print-no data-viz-chart" id="viz_<?php echo $vars['post_name']; ?>"></div>

          <script type="text/javascript">
            // <![CDATA[
              var <?php echo $vars['post_name']; ?> = <?php echo $json_vars; ?>
            // ]]>
          </script>

          <?php
        }
        $tweet = 'Explore ' . $title . ' + more -> ';

      } elseif ($d['type'] == 'cartodb_map') {

        echo '<div class="entry-content-asset hidden-print print-no">' . wp_oembed_get($d['cartodb_url']) . '</div>';
        echo '<img class="visible-print-block" src="' . $d['static_map'] . '" />';
        $tweet = 'Explore ' . $title . ' + more -> ';

      } elseif ($d['type'] == 'text') {

        echo apply_filters('the_content', $d['text-based_data']);

        if (!stristr($content, 'wp-embedded-content') && !stristr($content, '<img')) {
          $tweet = $title . ': ' . trim(strip_tags($content)) . '. More -> ';
        } else {
          $tweet = $title . ' + more -> ';
        }
      } ?>
      <div class="meta">
        <?php echo wpautop($d['notes']); ?>
      </div>

      <?php if (!empty($d['source'])) { ?>
        <button class="btn btn-default hidden-print print-no" data-toggle="popover" data-container="body" data-placement="top" data-trigger="focus" title="Source" data-html="true" data-content="<?php echo str_replace('"', '\'', $d['source']); ?>">Explore this data</button>
      <?php } ?>

      <p class="meta visible-print-block">Source: <?php echo $source_plain; ?></p>

      <?php include('social-share-embed.php'); ?>

    </div>
  </div>
</div>
