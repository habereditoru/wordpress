<?php
if( ! class_exists('he_ajax') ) :

class he_ajax {

    function __construct() {

        add_action( 'wp_ajax_he_query', array($this, 'he_query') );
        add_action( 'wp_ajax_nopriv_he_query', array($this, 'he_query') );

    }

    function he_query() {

        global $current_user;

        $kullanici = $current_user->ID;
        $he_islem = $_POST['tip'];

        // İçerik Kaynakları
        if ($he_islem == "ga"){

            $he_get_diller       = isset( $_POST['icerik_dili']) ? $_POST['icerik_dili'] : 'tr';
            $he_get_content_type = isset( $_POST['icerik_tipi']) ? $_POST['icerik_tipi'] : '0'  ;
            $he_get_kaynaklar    = isset( $_POST['kaynaklar'] )  ? $_POST['kaynaklar']   : array();
            $he_get_kategoriler  = isset( $_POST['kategoriler']) ? $_POST['kategoriler'] : array();
            $he_get_cron_minute  = isset( $_POST['cron_minute']) ? $_POST['cron_minute'] : HE_CRON_DEFAULT_MINUTE  ;


            $he_opt_diller = get_option('HE_OPT_ICERIK_DILLERI');
            $he_opt_content_type = get_option('HE_OPT_CONTENT_TYPE');

            // CRON AYARLARI
            if ( $he_get_cron_minute!="-" ) {
                wp_clear_scheduled_hook( 'he_scheduled_event');
                wp_schedule_event( time(), $he_get_cron_minute, 'he_scheduled_event');
                add_action( 'he_scheduled_event', array( 'HE_Cron', 'he_run_cron' ) );

                update_option('HE_OPT_CRON_AKTIF', true);
            } else {
                update_option('HE_OPT_CRON_AKTIF', false);
                wp_clear_scheduled_hook( 'he_scheduled_event');
            }

            update_option('HE_OPT_CRON_MINUTE', $he_get_cron_minute);

            if (!$he_opt_diller) {
                update_option( 'HE_OPT_ICERIK_DILLERI', $he_get_diller );
                $he_opt_diller = get_option('HE_OPT_ICERIK_DILLERI');
            }

            if ($he_opt_diller != $he_get_diller){

                update_option( 'HE_OPT_ICERIK_DILLERI', $he_get_diller );
                echo he_message("notice notice-success is-dismissible",__("İçerik dili değiştirildi. Lütfen İçerik kaynaklarını ve kategorilerini tekrar seçiniz...","habereditoru"));
//                echo '<script>setTimeout("window.location.href=\'?page=habereditoru&t=ayarlar\'",2000);</script>';

            } else if ($he_opt_content_type!=$he_get_content_type){
                update_option( 'HE_OPT_CONTENT_TYPE', $he_get_content_type );
                echo he_message("notice notice-success is-dismissible",__("İçerik tipi değiştirildi. Lütfen İçerik kaynaklarını ve kategorilerini tekrar seçiniz...","habereditoru"));
                echo '<script>setTimeout("window.location.href=\'?page=habereditoru&t=ayarlar\'",2000);</script>';
            } else {

                $he_xml_domain = he_jsontoxml(he_curl(HE_API_URL."/get/domain?d=".HE_DOMAIN."&k=".HE_API_KEY));
                $he_max_kaynak = intval($he_xml_domain->AgenciesCount);

                if (count($he_get_kaynaklar)>$he_max_kaynak){
                    echo he_message("notice notice-error is-dismissible", sprintf(__("En fazla %s adet haber kaynağı seçebilirsiniz","habereditoru"),$he_max_kaynak));
                    die();
                }

                if (count($he_get_kaynaklar)<1){
                    echo he_message("notice notice-error is-dismissible",__("En az 1 adet haber kaynağı seçmelisiniz...","habereditoru"));
                    die();
                }
                if (count($he_get_kategoriler)<1){
                    echo he_message("notice notice-error is-dismissible",__("En az 1 adet kategori seçmelisiniz...","habereditoru"));
                    die();
                }

                update_option('HE_OPT_KATEGORILER', $he_get_kategoriler );
                update_option('HE_OPT_KAYNAKLAR', $he_get_kaynaklar);

                echo he_message("notice notice-success is-dismissible",__("Tebrikler, şimdi kategori eşleştirmesi yapmanız için yönlendiriliyorsunuz...","habereditoru"));
                echo '<script>setTimeout("window.location.href=\'?page=habereditoru&t=kategori_eslestirme\'",2000);</script>';

                $he_get_kaynaklar_arr 	= implode(",",$he_get_kaynaklar) ;
                $he_get_kategoriler_arr = implode(",",$he_get_kategoriler) ;
                $he_post_data = "a=$he_get_kaynaklar_arr&c=$he_get_kategoriler_arr&l=$he_get_diller&t=$he_get_content_type";
                $he_post_url = HE_API_URL . "/set/settings?d=".HE_DOMAIN."&k=" . HE_API_KEY . "&" . $he_post_data ;
                //echo $he_post_url . "<br>";
                $he_xml = he_curl($he_post_url);
                //echo $he_xml ;

            }


          // kategori eşitleme kayıt
        }elseif($he_islem == "ke"){

            $he_get_kat_arr= array();


            for ($he_counter=1;$he_counter<=count($_POST);$he_counter++){
                if (isset($_POST['r_'.$he_counter]) && isset($_POST['l_'.$he_counter]) ){
                    $he_r_cat = $_POST['r_'.$he_counter];
                    $he_l_cat = $_POST['l_'.$he_counter];
                    $he_c_cat = explode(",", $he_r_cat);

                    foreach($he_c_cat as $he_get_cat){
                        $he_get_kat_arr[$he_get_cat] = $he_l_cat;
                    }
                }

            }

            update_option( 'HE_OPT_KAT_ESLESTIRME', $he_get_kat_arr);

            /*
            for ($k=1;$k<10;$k++){
                $he_get_id = $k;
                $he_bot_adi = "bot_".$he_get_id."_";
                delete_option( $he_bot_adi.'ajanslar');
                delete_option( $he_bot_adi.'ajanslar_str');
                delete_option( $he_bot_adi.'kategoriler');
                delete_option( $he_bot_adi.'kategoriler_str');
                delete_option( $he_bot_adi.'aktif');
            }*/

            echo he_message("notice notice-success is-dismissible",__('Tebrikler, değişiklikler kayıt edildi. Kategori kayıt işlemleriniz bitti ise lütfen <a href="?page=habereditoru&t=bot">Robot Ayarlarınızı</a> düzenleyiniz...',"habereditoru"));
            echo '<script>setTimeout("window.location.href=\'?page=habereditoru&t=kategori_eslestirme\'",2000);</script>';

            // bot ayarları kayıt
        }elseif($he_islem == "ba"){

            $he_get_id = isset( $_POST['bot'] ) ? intval($_POST['bot']) : 1 ;
            

            $he_bot_id			        = "HE_BOT_".$he_get_id."_";
            $he_bot_adi			        = "BOT ".$he_get_id;
            $he_bot_settings 	        = get_option( $he_bot_id.'SETTINGS') ;
            $he_bot_heid 		        = $he_bot_settings['HEID'];
            $he_bot_post_data           = $_POST;
            $he_bot_post_data['adi']    = $he_bot_adi ;

            if (!isset( $_POST['aktif'])){$he_bot_post_data['aktif']="0";}
            if (!isset( $_POST['icerik_dili'])){$he_bot_post_data['icerik_dili']="tr";}
            if (!isset( $_POST['icerik_tipi'])){$he_bot_post_data['icerik_tipi']="0";}
            if (!isset( $_POST['site_editor'])){$he_bot_post_data['site_editor']="1";}
            if (!isset( $_POST['post_durumu'])){$he_bot_post_data['post_durumu']="publish";}
            if (!isset( $_POST['post_tipi'])){$he_bot_post_data['post_tipi']="post";}
            if (!isset( $_POST['resimsiz_haber'])){$he_bot_post_data['resimsiz_haber']="0";}

            $he_get_kaynaklar_arr 	= implode(",",$he_bot_post_data['kaynaklar']) ;
            $he_get_kategoriler_arr = implode(",",$he_bot_post_data['kategoriler']) ;

            $he_post_data = "ID=$he_get_id&HEID=$he_bot_heid&Name=$he_bot_adi&ContentType=".$he_bot_post_data['icerik_tipi']."&Lang=".$he_bot_post_data['icerik_dili']."&Agencies=$he_get_kaynaklar_arr&Status=".$he_bot_post_data['aktif']."&Tags=".$he_bot_post_data['etiketler']."&NegativeTags=".$he_bot_post_data['negatif_etiketler']."&Categories=$he_get_kategoriler_arr&PostAuthor=".$he_bot_post_data['site_editor']."&PostStatus=".$he_bot_post_data['post_durumu']."&PostType=".$he_bot_post_data['post_tipi']."&Order=$he_get_id&NoImage=".$he_bot_post_data['resimsiz_haber'];
            $he_post_url = HE_API_URL . "/set/bot?d=".HE_DOMAIN."&k=" . HE_API_KEY . "&" . $he_post_data;
            //echo $he_post_url ;

            $he_xml = he_curl($he_post_url);
            $he_obj = json_decode($he_xml);

            if ( isset($he_obj->ERROR) ) {
                echo he_message('notice is-dismissible', $he_obj->ERROR);
                return false;
            }
                    $he_bot_heid = $he_obj->SUCCESS;
            if ( strlen($he_bot_heid)>2 ) {
                $he_bot_post_data['HEID'] = $he_bot_heid ;
            }

            update_option( $he_bot_id.'SETTINGS', $he_bot_post_data );

            echo he_message("notice notice-success is-dismissible",__("Bot ayarları kayıt edildi...","habereditoru"));
            echo '<script>setTimeout("window.location.href=\'?page=habereditoru&t=bot\'",2000);</script>';
            die();

            // bot etkinleştir
        }elseif($he_islem == "ba_e"){

            $he_get_id = $_POST['bot'];

            $he_bot_id = "HE_BOT_" . $he_get_id . "_";
            $he_bot_settings    = get_option( $he_bot_id . 'SETTINGS' );
            $he_get_status      = $_POST['aktif'];

            $he_bot_kaynaklar   = $he_bot_settings['kaynaklar'];
            $he_bot_kategoriler = $he_bot_settings['kategoriler'];
            $he_bot_dil         = $he_bot_settings['icerik_dili'];
            $he_bot_editor      = $he_bot_settings['site_editor'];
            $he_bot_post_tipi   = $he_bot_settings['post_tipi'];

            if (!empty($he_bot_kaynaklar) && !empty($he_bot_kategoriler) && !empty($he_bot_dil) && !empty($he_bot_editor) && !empty($he_bot_post_tipi)){
                $he_bot_settings['aktif'] = $he_get_status ;
                update_option( $he_bot_id.'SETTINGS', $he_bot_settings );
                echo he_message("notice notice-success is-dismissible",__("Bot Durumu Değiştirildi...","habereditoru"));
                echo '<script>setTimeout("window.location.href=\'?page=habereditoru&t=bot\'",2000);</script>';
            }else{
                echo he_message("notice notice-error is-dismissible",__("Bir hata oluştu. Lütfen tekrar deneyin...","habereditoru"));
            }

            die();
            // abonelik kayıt
        }elseif($he_islem == "ab"){

            $NameSurname = urlencode ( $_POST['adi'] );
            $email       = urlencode ( $_POST['eposta'] );
            $gsm         = urlencode ( $_POST['gsm']  );
            $langs       = urlencode ( $_POST['dil']);
            $country     = urlencode ( isset($_POST['ulke']) ? $_POST['ulke'] : '' );
            $ping_url    = urlencode ( "wp-cron.php" ) ;

            $PostURL = HE_API_URL . "/set/register?Domain=".HE_DOMAIN."&NameSurname=$NameSurname&EMail=$email&Contry=$country&GSM=$gsm&Langs=$langs&PingUrl=$ping_url" ;
            //echo $PostURL ;
            $he_xml_register = he_curl($PostURL);
            $he_obj_register = json_decode($he_xml_register) ;

            $he_api_key = $he_obj_register->{"API_KEY"} ;
            if ( strlen($he_api_key)>5 ) {
                update_option('HE_API_KEY',$he_obj_register->{"API_KEY"} ) ;
                update_option('HE_SITE_ID',$he_obj_register->{"SiteID"} ) ;
                echo he_message("notice notice-error is-dismissible",__("TEBRİKLER, Üyelik kaydınız yapıldı, İçerik Kaynakları bölümüne yönlendiriliyorsunuz...","habereditoru"));
                echo '<script>setTimeout("window.location.href=\'?page=habereditoru&t=ayarlar\'",2000);</script>';
                die();
            } else {
                if ( strlen($he_obj_register->{"SEND_KEY"})>5 ) {
                    echo $he_obj_register->{"SEND_KEY"} ;
                    update_option('HE_API_KEY',$he_obj_register->{"SEND_KEY"} ) ;
                    update_option('HE_IS_SETUP',true ) ;
                    echo '<script>setTimeout("window.location.href=\'?page=habereditoru&t=abonelik\'",2000);</script>';
                } else {
                    echo he_message("notice notice-error is-dismissible",__("ERROR") . " : " . $he_obj_register->{"ERROR"} );
                    die() ;
                }
                die();
            }

        }

        // return
        die();

    }

}

return new he_ajax();
endif;


