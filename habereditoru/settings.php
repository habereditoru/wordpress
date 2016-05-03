<?php 

error_reporting(E_ERROR | E_PARSE);

/* Define constants */
define('HE_PLUGIN_VERSION', '1.0.0');
define('HE_PLUGIN_DESTEK_MAIL', 'destek@habereditoru.com');
define('HE_MINIMUM_WP_VERSION', '3.2');
define('HE_API_URL','http://xml.habereditoru.com');
define('HE_SET_URL','http://set.habereditoru.com');
define('HE_CRON_DEFAULT_MINUTE',5);
define('HE_DEBUG',false);

define('HE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define('HE_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
define('HE_CLASS_DIR', plugin_dir_path( __FILE__ ) );
define('HE_DOMAIN', get_site_url() );
define('HE_API_KEY',get_option('HE_API_KEY'));
require_once( HE_CLASS_DIR . 'functions.php');
require_once( HE_CLASS_DIR . 'class.habereditoru.php');

date_default_timezone_set(get_option('timezone_string'));
 
?>