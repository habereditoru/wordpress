<?php
/*
	Plugin Name: HaberEditoru.com Haber Botu
	Plugin URI: http://www.habereditoru.com
	Version: 1.0
	Author: HaberEditoru.com
	Author URI: http://www.habereditoru.com
	Description: Haber Botu; abone olduğunuz haber ajanslarından veya çeşitli haber kaynaklarını derleyerek topladığı haber ve içerikleri web sitenize otomatik olarak ekleyen akıllı bir bot sistemidir. <a target="_blank" href="http://habereditoru.com/abonelik-paketleri/">Abonelik paketinize</a> göre belirli periyotlarda haber kaynaklarını tarayarak siteniz için uygun yeni içerikleri bulup web sitenize otomatik olarak ekler. Haber botumuzu 7 gün boyunca ücretsiz olarak deneyebilir dilerseniz <a href="?page=HaberEditoru&t=abonelik">Abonelik</a> bölümünden abone olabilirsiniz.
	Licence: GPLv2 or later
	Text Domain: HaberEditoru
*/
session_start();
if ( !function_exists( 'add_action') ) {
	echo __("Hişşttt!  Beni doğrudan çalıştıramazsınız....","HaberEditoru");
	exit;
}

/* Require needed files */
require_once( 'settings.php');


/* Create HaberEditoru instance */
$HaberEditoru = new HaberEditoru();

add_action( 'init', array( 'HaberEditoru', 'init' ) );


/* Load Cron */
register_activation_hook( __FILE__, array( 'HaberEditoruCron', 'he_cron_activation' ) );
register_deactivation_hook( __FILE__, array( 'HaberEditoruCron', 'he_cron_deactive' ) );

add_filter('cron_schedules',array( 'HaberEditoruCron', 'he_crons_new_times' ));
add_action('he_cron_event', array( 'HaberEditoruCron', 'he_cron_calistir' ) );

/* Load textdomain */

add_action('plugins_loaded', 'he_load_textdomain');
function he_load_textdomain() {
	load_plugin_textdomain('haberEditoru', false, dirname(plugin_basename( __FILE__ )).'/lang/'); 
}

?>