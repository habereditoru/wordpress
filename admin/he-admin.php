<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


class HE_Admin {

    /**
     * @var string The capability users should have to view the page
     */
    public $minimum_capability = 'manage_options';

    public function __construct() {

        add_action( 'admin_menu', array( $this, 'admin_menus') );
        add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu'), 2000 );
//        add_action( 'admin_head', array( $this, 'admin_head' ) );
//        add_action( 'admin_init', array( $this, 'init') );
        add_action( 'admin_enqueue_scripts', array($this, 'he_load_admin_scripts'), 100 );
        //add_action('admin_footer', array($this, 'admin_footer'));
        add_action( 'contextual_help', array($this, 'my_admin_add_help_tab'), 10, 3 );

    }


    function my_admin_add_help_tab () {
        $screen = get_current_screen();
        if ($screen->parent_base == 'habereditoru') {

            // Add my_help_tab if current screen is My Admin Page
            $screen->add_help_tab( array(
                'id'      => 'haber_editoru',
                'title'   => __( 'HaberEditörü Yardım', "habereditoru"),
                'content' => '<div style="text-align:center"><h2>HaberEditörü Nasıl Kullanılır ? </h2><iframe width="560" height="315" src="https://www.youtube.com/embed/VIvsnQEa0Hg" frameborder="0" allowfullscreen></iframe></div>',
            ) );
        }
    }

    public function he_load_admin_scripts() {

        $js_dir  = HE_PLUGIN_DIR_URL . 'assets/js/';
        $css_dir = HE_PLUGIN_DIR_URL . 'assets/css/';

        wp_enqueue_style( 'habereditoru', $css_dir.'style.css');
        wp_enqueue_script('habereditoru', $js_dir.'scripts.js', array('jquery'), HE_PLUGIN_VERSION);
        wp_localize_script( 'habereditoru', 'he', array(
            'ajax_url' => admin_url( 'admin-ajax.php' )
        ));

    }

    public function admin_menus() {

        // Main Page
        add_menu_page(
            __( 'Haber Botu', 'habereditoru' ),
            __( 'Haber Botu', 'habereditoru' ),
            $this->minimum_capability,
            'habereditoru',
            array( $this, 'load_page' ),
            HE_PLUGIN_DIR_URL.'/assets/img/icon.png'
        );

    }

    public function admin_bar_menu($wp_admin_bar)
    {
        $menu_id = 'habereditoru';
        $wp_admin_bar->add_menu(array('id' => $menu_id, 'title' => '<img style="vertical-align: text-bottom;" src="'.HE_PLUGIN_DIR_URL.'assets/img/icon.png'.'"></img> Haber Botu', 'href' => '#' , 'meta' => array('class' => '') ));
        $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Bot Yönetimi','habereditoru'), 'id' => 'he-robotlar', 'href' => admin_url( add_query_arg( array(  'page' => 'habereditoru', 't' => 'bot' ), 'admin.php' ) ), 'meta' => array('class' => 'first-toolbar-group')));
        $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Raporlar','habereditoru'), 'id' => 'he-raporlar', 'href' => admin_url( add_query_arg( array(  'page' => 'habereditoru', 't' => 'raporlar' ), 'admin.php' ) )));
        $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Aktiviteler','habereditoru'), 'id' => 'he-logs', 'href' => admin_url( add_query_arg( array(  'page' => 'habereditoru', 't' => 'logs' ), 'admin.php' ) )));
        $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Abonelik ve Ödeme','habereditoru'), 'id' => 'he-member', 'href' => admin_url( add_query_arg( array(  'page' => 'habereditoru', 't' => 'abonelik' ), 'admin.php' ) )));
    }



    public function load_page()
    {


        self::he_get_settings();

        $current_tab = 'bot';
        if (isset($_GET['t'])) $current_tab = (filter_input( INPUT_GET, 't' ));

        if ( get_option('HE_IS_SETUP') ) {
            /*
             * FIXED
             * Setup işlemim tamam ama domaine izin veriyormuyuz. ?
             */
            // HE Domain Kayıtlı Değilse
            $he_domain_check = he_curl( HE_API_URL ."/get/check?d=".HE_DOMAIN);
            if ($he_domain_check != '{true}'){
                $current_tab = "abonelik";
            }

        }

        self::he_check_option();
        // Tab ayarları
        if (!empty($GLOBALS['HE_d_Tab'])) $current_tab = $GLOBALS['HE_d_Tab'];



        $this->content_head();
        $this->tabs($current_tab);

        switch ( $current_tab ) {
            case 'ayarlar':
                require_once( HE_PLUGIN_DIR . 'admin/he-genel-ayarlar.php' );
                he_genel_ayarlar::output();
                break;
            case 'kategori_eslestirme':
                require_once( HE_PLUGIN_DIR . 'admin/he-kategori-eslestirme.php' );
                he_kategori_eslestirme::output();
                break;
            case 'abonelik':
                require_once( HE_PLUGIN_DIR . 'admin/he-abonelik.php' );
                he_abonelik::output();
                break;
            case 'raporlar':
                require_once( HE_PLUGIN_DIR . 'admin/he-raporlar.php' );
                he_raporlar::output();
                break;
            case 'logs':
                require_once( HE_PLUGIN_DIR . 'admin/he-logs.php' );
                he_logs::output();
                break;
            case 'bot':
            default:
                require_once( HE_PLUGIN_DIR . 'admin/he-bot.php' );
                he_bot::output();
                break;
        }
        $this->content_footer();
        return;

    }




    function he_overview()
    {

        $dayResults = he_curl( HE_API_URL . '/report/?d='.HE_DOMAIN.'&k='.HE_API_KEY.'&r=today&x=count');
        $weekResults = he_curl( HE_API_URL . '/report/?d='.HE_DOMAIN.'&k='.HE_API_KEY.'&r=today&x=count');
        $monthResults = he_curl( HE_API_URL . '/report/?d='.HE_DOMAIN.'&k='.HE_API_KEY.'&r=today&x=count');

        $dayResults= json_decode( $dayResults );
        $weekResults= json_decode( $weekResults );
        $monthResults= json_decode( $monthResults );

        ?>
        <div class="table" style="width:100%">

            <table>

                <tr class="first">
                    <td class="first b"><a href="#"><?php echo $dayResults->Count ?></a></td>
                    <td class="t"><a href="#"><?php echo __('Bugünkü çekilen içerik','habereditoru'); ?></a></td>
                </tr>

                <tr>
                    <td class="first b"><a href="#"><?php echo $weekResults->Count ?></a></td>
                    <td class="t"><a href="#"><?php echo __('Bu Haftanın çekilen içerik','habereditoru'); ?></a></td>
                </tr>

                <tr>
                    <td class="first b"><a href="#"><?php echo $monthResults->Count ?></a></td>
                    <td class="t"><a href="#"><?php echo __('Bu Ay çekilen içerik','habereditoru'); ?></a></td>
                </tr>

            </table>

        </div>


        <?php
    }

    function he_news() {

        echo '<div class="rss-widget">';
        $todayResults = he_curl( HE_API_URL . '/report/?d='.HE_DOMAIN.'&k='.HE_API_KEY.'&r=today');
        $r = json_decode( $todayResults );
        foreach ( $r->RESULT as $key => $item ) {
            echo '<li><a href="'.$item[6].'">'.$item[2].'</a></li>';
            if ($key > 9) break;
        }
        echo "</div>";

        echo "<style type='text/css'>#um-metaboxes-mainbox-1 a.rsswidget {font-weight: 400}#um-metaboxes-mainbox-1 .rss-widget span.rss-date{ color: #777; margin-left: 12px;}</style>";

    }

    public function tabs($sel)
    {

        //        $sel = empty($sel) ? 'bot' : $sel;
        ?>

        <div style="margin-bottom:0" class="wp-filter">
            <ul class="filter-links">

                <li><a href="<?php echo esc_url( admin_url( add_query_arg( array(  'page' => 'habereditoru', 't' => 'bot' ), 'admin.php' ) ) ); ?>"
                       class="<?php echo ($sel == "bot") ? 'current' : '' ?>"><?php _e("Bot Yönetimi", "habereditoru") ?></a>
                </li>
                <li><a href="<?php echo esc_url( admin_url( add_query_arg( array(  'page' => 'habereditoru', 't' => 'ayarlar' ), 'admin.php' ) ) ); ?>"
                       class="<?php echo ($sel == "ayarlar") ? 'current' : '' ?>" id="ayar"><?php _e("İçerik Ayarları", "habereditoru") ?></a></li>

                <li><a href="<?php echo esc_url( admin_url( add_query_arg( array(  'page' => 'habereditoru', 't' => 'kategori_eslestirme' ), 'admin.php' ) ) ); ?>"
                       class="<?php echo ($sel == "kategori_eslestirme") ? 'current' : '' ?>"><?php _e("Kategori Eşleştirme", "habereditoru") ?></a>
                </li>
                <li><a href="<?php echo esc_url( admin_url( add_query_arg( array(  'page' => 'habereditoru', 't' => 'raporlar' ), 'admin.php' ) ) ); ?>"
                       class="<?php echo ($sel == "raporlar") ? 'current' : '' ?>"><?php _e("Raporlar", "habereditoru") ?></a>
                </li>
                <li><a href="<?php echo esc_url( admin_url( add_query_arg( array(  'page' => 'habereditoru', 't' => 'logs' ), 'admin.php' ) ) ); ?>"
                       class="<?php echo ($sel == "logs") ? 'current' : '' ?>"><?php _e("Aktiviteler", "habereditoru") ?></a>
                </li>
                <li><a href="<?php echo esc_url( admin_url( add_query_arg( array(  'page' => 'habereditoru', 't' => 'abonelik' ), 'admin.php' ) ) ); ?>"
                       class="<?php echo ($sel == "abonelik") ? 'current' : '' ?>"><?php _e("Abonelik", "habereditoru") ?></a>
                </li>
                <?php if (get_option('HE_END_DATE')): ?>
                <li class="he-time"><a  href="<?php echo esc_url( admin_url( add_query_arg( array(  'page' => 'habereditoru', 't' => 'abonelik' ), 'admin.php' ) ) ); ?>">
                        <?php echo he_lastdate_write(date_format(date_create(get_option('HE_END_DATE')), "Y-m-d")) ?>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            <?php if (get_option('HE_MAX_KAYNAK')): ?>
            <div class="filter-count">
                <span title="<?php _e("Kaynak Adet / Robot Adet") ?>"
                      class="count bot-count"><?php echo get_option('HE_MAX_KAYNAK') . ' / ' . get_option('HE_MAX_ROBOT') ?></span>
            </div>
            <?php endif; ?>
        </div>

        <?php
    }


    /**
     * Render the admin page head for the HE Plugin
     */
    public function content_head() {
      ?>
        <div class="wrap">
        <h1><img style="height:32px;vertical-align:middle" src="<?php echo HE_PLUGIN_DIR_URL .'assets/img/logo.png'?>"> Habereditoru.com <small style="float:right;color:rgba(0,0,0,.3);">WP Otomatik Haber Botu</small></h1>
    <?php
    }
    /**
     * Render the admin page head for the HE Plugin
     */
    public function content_footer() {
//        echo '</div>';
        $he_sistem_saati = date( 'H:i', HE_TIMESTAMP );
        $he_cron_sonraki_zaman = date('H:i', wp_next_scheduled('he_scheduled_event') + get_option('gmt_offset') * 3600);
        echo "<div class='clear'></div><br><br><hr>v.".HE_PLUGIN_VERSION." | <a href='http://www.habereditoru.com' target='_blank'>habereditoru.com</a> | <a href='mailto:".HE_PLUGIN_DESTEK_MAIL."'>".HE_PLUGIN_DESTEK_MAIL."</a> <span style='float:right' title='".get_option("HE_CONFIG_LAST_UPDATE")."'>SiteID : <b>".get_option("HE_SITE_ID")."</b> Domain : <b>". HE_DOMAIN ."</b>  ".__("Sistem Saatı","habereditoru")." : <b>" . $he_sistem_saati . "</b>";
        if (get_option('HE_OPT_CRON_AKTIF')){
            echo " " . __("Sonraki Haber Çekme Saatı","habereditoru") . " : <b>" . $he_cron_sonraki_zaman . "</b></span>" ;
        } else {
            echo '   <a href="'.get_admin_url().'?page=habereditoru&t=ayarlar">(!) ' . __("Otomatik Haber Çekme Kapalı","habereditoru") . '</a> </span>' ;
        }
        echo '</div><div class="clear"></div>';


    }

    public static function he_get_settings(){
        if ( HE_DOMAIN != "" && HE_API_KEY != "" ) {

            $he_xml_domain = he_curl(HE_API_URL."/get/domain?d=".HE_DOMAIN."&k=".HE_API_KEY);
//            echo $he_xml_domain;

            if ($he_xml_domain==""){
                update_option('HE_IS_SETUP',false) ;
            } else {
                if ( $he_xml_domain == "{false}" ) {
                    update_option('HE_IS_SETUP',false);
                    echo he_message('notice notice-error is-dismissible','<b>KRİTİK HATA :  </b> ALAN ADI veya API_KEY geçersiz. Lütfen bizimle <a target="_blank" href="http://habereditoru.com/iletisim/">iletişime</a> geçiniz. <br>
						Alan Adı : <b>'.HE_DOMAIN.'</b> API_KEY : <b>'.HE_API_KEY.'</b> ') ;
                    $GLOBALS['HE_d_Tab'] = "abonelik";
                } else {
                    $he_obj_domain = json_decode($he_xml_domain) ;

                    update_option('HE_CONFIG', $he_obj_domain) ;
                    update_option('HE_CONFIG_LAST_UPDATE',date("Y-m-d H:i", HE_TIMESTAMP)) ;
                    update_option('HE_SITE_ID', $he_obj_domain->{"SiteID"}) ;
                    update_option('HE_DOMAIN', $he_obj_domain->{"Domain"}) ;
                    update_option('HE_IS_SETUP',true) ;
                    update_option('HE_MAX_ROBOT', $he_obj_domain->{"RobotCount"}) ;
                    update_option('HE_MAX_KAYNAK', $he_obj_domain->{"AgenciesCount"}) ;
                    update_option('HE_MAX_CRON', $he_obj_domain->{"Abonelik"}) ;
                    update_option('HE_END_DATE', $he_obj_domain->{"EndDate"} );
                    update_option('HE_SEND_STATUS', $he_obj_domain->{"HE_Status"} );

                    if ( $he_obj_domain->{"HE_Status"} == 0 ) {
                        echo he_message('notice notice-error is-dismissible',__('<b>HATA : Habereditörü yönetimi haber gönderimini durdurdu!</b> Detaylar için lütfen <a href="http://www.habereditoru.com/iletisim/">bize ulaşınız...</a>',"habereditoru")) ;
                    }

                    if ( strlen($he_obj_domain->{"Message"}) > 10 ) {
                        echo he_message('notice is-dismissible',$he_obj_domain->{"Message"}) ;
                    }


                    if ( strtotime($he_obj_domain->{"EndDate"}) < HE_TIMESTAMP ){
                        echo he_message('notice notice-error is-dismissible',__('<b>HATA : Abonelik süreniz bitti.</b> Haber çekmeye devam etmek istiyorsanız lütfen <a href="?page=habereditoru&t=abonelik">abone olunuz...</a>',"habereditoru")) ;
                        $GLOBALS['HE_d_Tab'] = "abonelik" ;
                    }
                }
            }

            if (HE_DEBUG) {echo HE_DOMAIN . " -> " . __("Ayarlar Alındı...","habereditoru"). "<br>" ; }

        } else {
            update_option('HE_IS_SETUP',false) ;
            $GLOBALS['HE_d_Tab'] ="abonelik";
            echo he_message('notice notice-error is-dismissible','<b>KRİTİK HATA :  </b> ALAN ADI veya API_KEY tanımlanmamış. Sitenizi kayıt etmediyseniz <a href="'.get_admin_url().'?page=habereditoru&t=abonelik">buradan kayıt edebilir</a> yada bizimle <a target="_blank" href="http://habereditoru.com/iletisim/">iletişime</a> geçebilirsiniz. <br>
						Alan Adı : <b>'.HE_DOMAIN.'</b>, API_KEY : <b>'.HE_API_KEY.'</b> ') ;
        }
    }

    public static function he_check_option(){

        if ( HE_API_KEY != "" ) {

            if (isset($GLOBALS['HE_d_Tab']))
               if ($GLOBALS['HE_d_Tab'] == 'abonelik') return false;


            $he_opt_kaynaklar 			= get_option('HE_OPT_KAYNAKLAR');
            $he_opt_kategoriler 		= get_option('HE_OPT_KATEGORILER');
            $he_opt_kategori_eslestirme = get_option('HE_OPT_KAT_ESLESTIRME');
          //  $he_set_content_type 		= get_option('HE_OPT_CONTENT_TYPE');

//            $he_opt_kategori_eslestirme = is_array($he_opt_kategori_eslestirme) ? count($he_opt_kategori_eslestirme) : $he_opt_kategori_eslestirme;

            // KATEGORİ EŞLEŞTİRMESİ TAMAMMI?

            update_option( 'HE_OK_KATEGORI_ESLESTIRME' , 1 );

            if ( !empty($he_opt_kategoriler)) {

                if (!empty($he_opt_kategori_eslestirme)) {
                    foreach (array_values($he_opt_kategoriler) as $temp) {

                        if ( !array_key_exists($temp, $he_opt_kategori_eslestirme) ) {
                            update_option( 'HE_OK_KATEGORI_ESLESTIRME' , 0 );
                        }
                    }
                }else {
                    update_option( 'HE_OK_KATEGORI_ESLESTIRME' , 0 );
                }
            }

            // 	Kategori Eşleştirme Yapılmışmı
            if ( get_option('HE_OK_KATEGORI_ESLESTIRME') == 0 ) {
                echo he_message('notice notice-error is-dismissible',__('<b>HATA : Kategori eşleştirmesi henüz tamamlanmamış!</b> Lütfen <a href="?page=habereditoru&t=kategori_eslestirme">Kategori Eşleştirme</a> ayarlarınızı kontrol ediniz...',"habereditoru")) ;
                $GLOBALS['HE_d_Tab'] = "kategori_eslestirme" ;
            }

            // 	İçerik Kaynakları
            if ( empty($he_opt_kaynaklar) || count($he_opt_kaynaklar) < 1 ) {
                echo he_message('notice notice-error is-dismissible',__('<b>HATA :</b> <b>İçerik Kaynakları Seçilmemiş!</b> Lütfen içerik kaynaklarınızı düzenleyin...',"habereditoru")) ;
                $GLOBALS['HE_d_Tab'] = "ayarlar" ;
            }
            if ( empty($he_opt_kategoriler) || count($he_opt_kategoriler) < 1 ) {
                echo he_message('notice notice-error is-dismissible',__('<b>HATA : İçerik kategorileri seçilmemiş!</b> Lütfen içerik <a href="?page=habereditoru&t=ayarlar">kategorilerini seçin...</a>',"habereditoru")) ;
                $GLOBALS['HE_d_Tab'] = "ayarlar";
            }
        }

    }

}
return new HE_Admin();
