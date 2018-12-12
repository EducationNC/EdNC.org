<?php

namespace Stringable\DataDash\Assets;

/**
 * Get paths for assets
 */
class JsonManifest {
  private $manifest;

  public function __construct($manifest_path) {
    if (file_exists($manifest_path)) {
      $this->manifest = json_decode(file_get_contents($manifest_path), true);
    } else {
      $this->manifest = [];
    }
  }

  public function get() {
    return $this->manifest;
  }

  public function getPath($key = '', $default = null) {
    $collection = $this->manifest;
    if (is_null($key)) {
      return $collection;
    }
    if (isset($collection[$key])) {
      return $collection[$key];
    }
    foreach (explode('.', $key) as $segment) {
      if (!isset($collection[$segment])) {
        return $default;
      } else {
        $collection = $collection[$segment];
      }
    }
    return $collection;
  }
}

function asset_path($filename) {
  $dist_path = plugin_dir_url(dirname(__FILE__)) . 'dist/';
  $directory = dirname($filename) . '/';
  $file = basename($filename);
  static $manifest;

  if (empty($manifest)) {
    $manifest_path = plugin_dir_path(dirname(__FILE__)) . 'dist/' . 'assets.json';
    $manifest = new namespace\JsonManifest($manifest_path);
  }

  if (array_key_exists($file, $manifest->get())) {
    return $dist_path . $directory . $manifest->get()[$file];
  } else {
    return $dist_path . $directory . $file;
  }
}

// Enqueue admin styles
add_action( 'admin_enqueue_scripts', function($hook) {
  if( $hook != 'post.php' && $hook != 'post-new.php' )
    return;

  wp_enqueue_style( 'dd_admin_style', namespace\asset_path('styles/admin.css') );
});

// Enqueue assets
function enqueue_scripts() {
  if (is_post_type_archive('data') || is_singular('data-viz')) {
    wp_enqueue_style( 'dd_main_style', namespace\asset_path('styles/main.css') );
    wp_enqueue_script('dd_google_charts', '//www.gstatic.com/charts/loader.js', [], null, false);
    wp_enqueue_script('dd_main_scripts', namespace\asset_path('scripts/main.js'), ['jquery', 'dd_google_charts'], null, true);
    wp_localize_script( 'dd_main_scripts', 'dd_ajax', array(
      'ajaxurl' => admin_url('admin-ajax.php'),
      'security' => wp_create_nonce('data-viz-ajax-nonce')
    ));
  }
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_scripts', 100);
add_action('enqueue_embed_scripts', __NAMESPACE__ . '\\enqueue_scripts', 100);
remove_action( 'embed_head', 'print_emoji_detection_script' );
remove_action( 'embed_head', 'print_emoji_styles' );
remove_action( 'embed_head', 'print_embed_styles' );

// Add social media metadata to head of dashboard
function social_meta() {
  if (is_post_type_archive('data')) {
    include_once(plugin_dir_path(dirname(__FILE__)) . 'templates/social-meta.php');
  }
}
add_action('wp_head', __NAMESPACE__ . '\\social_meta');

// Enqueue Bootstrap Modal if it's not already defined
function bootstrap_modal() {
  if (is_post_type_archive('data') || is_singular('data-viz')) {
    ?>
    <script>
    jQuery(document).ready(function($) {
      if(typeof(jQuery.fn.modal) === 'undefined') {
        $('body').append($('<script src="<?php echo namespace\asset_path('scripts/bootstrap.modal.js'); ?>"><\/script>'))
      }
    });</script>
    <?php
  }
}
// add_action('wp_footer', __NAMESPACE__ . '\\bootstrap_modal', 30);
// add_action('embed_footer', __NAMESPACE__ . '\\bootstrap_modal', 30);

/**
 * Replace default inline embed scripts to remove default share fn code and allow links to open in new tabs
 */
remove_action( 'embed_footer', 'print_embed_scripts' );
add_action( 'embed_footer', function() {
	?>
	<script type="text/javascript" id="new">
	 <?php readfile( namespace\asset_path('scripts/wp-embed-template.js') ); ?>
	</script>
	<?php
} );
