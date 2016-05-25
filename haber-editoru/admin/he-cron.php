<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class HE_Cron {

    public function __construct() {
        add_filter( 'cron_schedules', array( $this, 'add_schedules'   ) );
//        add_action( 'wp',             array( $this, 'schedule_Events' ) );
    }

    public function add_schedules( $schedules = array() ) {
        // Adds once weekly to the existing schedules.
        $schedules['5m'] = array('interval' => 300,'display' => __( '5 Dakikada bir', 'habereditoru'));
        $schedules['10m'] = array('interval' => 600,'display' => __( '10 Dakikada bir', 'habereditoru'));
        $schedules['15m'] = array('interval' => 900,'display' => __( '15 Dakikada bir', 'habereditoru'));
        $schedules['30m'] = array('interval' => 1800,'display' => __( '30 Dakikada bir', 'habereditoru'));
        $schedules['120m'] = array('interval' => 7200,'display' => __( '2 Saatte bir', 'habereditoru'));
        $schedules['180m'] = array('interval' => 10800,'display' => __( '3 Saatte bir', 'habereditoru'));
        $schedules['360m'] = array('interval' => 21600,'display' => __( '6 Saatte bir', 'habereditoru'));
        return $schedules;
    }

    public static function schedule_Events() {
        update_option('HE_OPT_CRON_MINUTE', HE_CRON_DEFAULT_MINUTE);
        if ( !wp_next_scheduled( 'he_scheduled_event') ) {
            wp_schedule_event( time(), HE_CRON_DEFAULT_MINUTE, 'he_scheduled_event');
        }
    }

    public function remove_Events()
    {
        wp_clear_scheduled_hook( 'he_scheduled_event');
    }

    function he_run_cron() {
        $HE_CronContent = he_curl( get_site_url(). '/wp-cron.php?SiteKey=' . HE_API_KEY );
    }
}

return new HE_Cron();

