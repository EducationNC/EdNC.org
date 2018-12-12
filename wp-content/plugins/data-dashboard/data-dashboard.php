<?php
/*
Plugin Name: Data Dashboard
Description: Reponsive, embeddable, sharable data dashboard. Uses Google Charts and CartoDB for visualizations.
Plugin URI:  https://github.com/isabisa/data-dashboard
Author:      Stringable Studio
Author URI:  http://www.stringablestudio.com
Version:     1.0
Text Domain: data-dash
License:     MIT License
License URI: http://opensource.org/licenses/MIT
*/

if (!class_exists('Data_Dashboard')) {
  class Data_Dashboard {
    /**
     * Construct the plugin object
     */
    public function __construct() {
      $includes = [
       '/lib/walker-data-nav.php', // Nav walker for template
       '/lib/custom-post-types.php', // Custom post types
       '/lib/assets.php', // Plugin assets
       '/lib/extras.php', // Custom functions
       '/lib/cmb-fields.php', // Create our own metaboxes and fields
       '/lib/ajax.php', // Ajax functions for caching and saving pngs
       '/vendor/cmb2/init.php', // Init CMB2 plugin
       '/vendor/cmb2-conditionals/cmb2-conditionals.php', // Init CMB2 Conditionals plugin
       '/vendor/cmb2-attached-posts/cmb2-attached-posts-field.php' // Init CMB2 Attached Posts plugin
      ];

      foreach ($includes as $file) {
        $filepath = dirname(__FILE__) . $file;
        if (!file_exists($filepath)) {
          trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $filepath), E_USER_ERROR);
        }

        require_once $filepath;
      }
      unset($file, $filepath);
    }

    /**
     * Activate the plugin
     */
    public static function activate() {
      $upload = wp_upload_dir();
      $upload_dir = $upload['basedir'];
      $upload_dir = $upload_dir . '/data-viz';
      if (! is_dir($upload_dir)) {
         mkdir( $upload_dir, 0755 );
      }
    }

    /**
     * Deactivate the plugin
     */
    public static function deactivate() {
      // Do nothing
    }
  }
}

if (class_exists('Data_Dashboard')) {
  // Installation and uninstallation hooks
  register_activation_hook(__FILE__, array('Data_Dashboard', 'activate'));
  register_deactivation_hook(__FILE__, array('Data_Dashboard', 'deactivate'));

  // instantiate the plugin class
  $data_dashboard = new Data_Dashboard();
}
