<?php
/**
* Plugin Name: WP IMDB Shortcode
* Description: Will be show imdb moview and artist name details with imdb data
* Version: 1.0
* Author: Ocean Infotech
* Author URI: https://www.xeeshop.com
* Copyright: 2019 
*/

if (!defined('ABSPATH')) {
    die('-1');
}
if (!defined('IMDB_PLUGIN_NAME')) {
    define('IMDB_PLUGIN_NAME', 'Wp IMDB');
}
if (!defined('IMDB_PLUGIN_VERSION')) {
    define('IMDB_PLUGIN_VERSION', '1.0.0');
}
if (!defined('IMDB_PLUGIN_FILE')) {
    define('IMDB_PLUGIN_FILE', __FILE__);
}
if (!defined('IMDB_PLUGIN_DIR')) {
    define('IMDB_PLUGIN_DIR',plugins_url('', __FILE__));
}
if (!defined('IMDB_DOMAIN')) {
    define('IMDB_DOMAIN', 'ocimdb');
}


if (!class_exists('IMDBMAIN')) {

  class IMDBMAIN {

    protected static $instance;

    //Load all includes files
    function includes() {
      include_once('admin/ocimdb_backend.php');
      include_once('front/ocimdb_frontend.php');
      //include_once('front/backup_front.php');
    }

    function init() {
      add_action( 'admin_enqueue_scripts', array($this, 'IMDB_load_admin_script_style'));
      add_action( 'wp_enqueue_scripts',  array($this, 'IMDB_load_script_style'));
    }

    //Add JS and CSS on Backend
    function IMDB_load_admin_script_style() {
      wp_enqueue_style( 'ocimdb_backcss', IMDB_PLUGIN_DIR . '/includes/css/ocimdb_back_style.css', false, '1.0.0' );
      wp_enqueue_script( 'ocimdb_backjs', IMDB_PLUGIN_DIR .'/includes/js/ocimdb_back_script.js', false, '1.0.0' );
     
    }

    //Add JS and CSS on Frontend
    function IMDB_load_script_style() {
      wp_enqueue_style( 'ocimdb_frontcss', IMDB_PLUGIN_DIR . '/includes/css/ocimdb_front_style.css', false, '1.0.0' );
      wp_enqueue_script( 'ocimdb_frontjs', IMDB_PLUGIN_DIR . '/includes/js/ocimdb_front_script.js', false, '1.0.0',true );  
    }

    

    //Plugin Rating
    public static function do_activation() {
      set_transient('ocimdb-first-rating', true, MONTH_IN_SECONDS);
    }

    public static function instance() {
      if (!isset(self::$instance)) {
        self::$instance = new self();
        self::$instance->init();
        self::$instance->includes();
      }
      return self::$instance;
    }

  }

  add_action('plugins_loaded', array('IMDBMAIN', 'instance'));

  register_activation_hook(IMDB_PLUGIN_FILE, array('IMDBMAIN', 'do_activation'));
}
