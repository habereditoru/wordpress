<?php
/*
	Plugin Name: HaberEditoru.com - Haber Botu
	Plugin URI: http://www.habereditoru.com
	Version: 1.0.0
	Author: habereditoru.com
	Author URI: http://www.habereditoru.com
	Description: Haber Botu; abone olduğunuz haber ajanslarından veya çeşitli haber kaynaklarını derleyerek topladığı haber ve içerikleri web sitenize otomatik olarak ekleyen akıllı bir bot sistemidir. <a target="_blank" href="http://habereditoru.com/abonelik-paketleri/">Abonelik paketinize</a> göre belirli periyotlarda haber kaynaklarını tarayarak siteniz için uygun yeni içerikleri bulup web sitenize otomatik olarak ekler. Haber botumuzu 7 gün boyunca ücretsiz olarak deneyebilir dilerseniz <a href="?page=habereditoru&t=abonelik">Abonelik</a> bölümünden abone olabilirsiniz.
	Licence: GPLv2 or later
	Text Domain: habereditoru
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'habereditoru' ) ) :


final class habereditoru
{

    private static $_instance;


    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();

        }
        return self::$_instance;
    }

    public function __construct() {

        $this->define_constants();
        $this->includes();
        $this->init_hooks();

    }


    private function define_constants() {

        $this->define( 'HE_PLUGIN_VERSION', '1.0.0' );
        $this->define( 'HE_PLUGIN_DESTEK_MAIL', 'destek@habereditoru.com' );
        $this->define( 'HE_MINIMUM_WP_VERSION', '3.2' );
        $this->define( 'HE_API_URL', 'http://xml.habereditoru.com' );
        $this->define( 'HE_SET_URL', 'http://set.habereditoru.com' );
        $this->define( 'HE_TIMESTAMP', current_time('timestamp') );
        $this->define( 'HE_CRON_DEFAULT_MINUTE', '30m' );
        $this->define( 'HE_DEBUG', false );

        $this->define( 'HE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        $this->define( 'HE_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
        $this->define( 'HE_CLASS_DIR', plugin_dir_path( __FILE__ ) );
        $this->define( 'HE_DOMAIN', get_site_url()  );
        $this->define( 'HE_API_KEY', get_option('HE_API_KEY') );
    }

    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    public function is_request( $type ) {
        switch ( $type ) {
            case 'admin' :
                return is_admin();
            case 'ajax' :
                return defined( 'DOING_AJAX' );
            case 'cron' :
                return defined( 'DOING_CRON' );
            case 'frontend' :
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }

    private function includes() {

        include_once( HE_PLUGIN_DIR.'includes/functions.php');

        if ( $this->is_request( 'admin' ) ) {
            include_once( HE_PLUGIN_DIR.'admin/he-admin.php');
            include_once( HE_PLUGIN_DIR.'admin/he-ajax.php');

        }
        include_once( HE_PLUGIN_DIR.'admin/he-cron.php');
        include_once( HE_PLUGIN_DIR.'includes/cron.php');

    }

    public function init_hooks()
    {
        register_activation_hook( __FILE__, array('HE_Cron', 'schedule_Events'));
        register_deactivation_hook( __FILE__, array('HE_Cron', 'remove_Events'));

        add_action( 'init', array( $this, 'init' ), 0 );
        add_action( 'wp_loaded', 'he_wp_loaded');

    }

    public function init() {
      $this->load_textdomain();
      add_action( 'he_scheduled_event', array( 'HE_Cron', 'he_run_cron' ) );
        // Load class instances
    }

    function load_textdomain( ) {
        load_plugin_textdomain( 'habereditoru', false, plugin_basename( dirname( __FILE__ ) ).'/lang' );
    }

}

endif; // End if class_exists check.

function HE() {
    return habereditoru::instance();
}

// Get habereditoru Running.
HE();