<?php

class HaberEditoru {
	
	private static $initiated = false;

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}

	private static function init_hooks() {
		self::$initiated = true;
		
		add_action('admin_menu', array( 'HaberEditoru', 'he_admin_menu' ));
		add_action('admin_bar_menu', array('HaberEditoru', 'he_admin_bar_menu'), 2000);

	}
	
	public static function he_check_option(){
	
		if (HE_API_KEY!="") {
			$HE_OPT_KAYNAKLAR 			= get_option('HE_OPT_KAYNAKLAR');
			$HE_OPT_KATEGORILER 		= get_option('HE_OPT_KATEGORILER');
			$HE_OPT_KATEGORI_ESLESTIRME = get_option('HE_OPT_KAT_ESLESTIRME');
			$HE_SET_CONTENT_TYPE 		= get_option('HE_OPT_CONTENT_TYPE');

			// KATEGORİ EŞLEŞTİRMESİ TAMAMMI?	
			update_option( 'HE_OK_KATEGORI_ESLESTIRME' , 1 );
			if ( !empty($HE_OPT_KATEGORILER) && !empty($HE_OPT_KATEGORI_ESLESTIRME)  ) {
				foreach (array_values($HE_OPT_KATEGORILER) as $temp) {
					if ( !array_key_exists($temp, $HE_OPT_KATEGORI_ESLESTIRME) ) {
						update_option( 'HE_OK_KATEGORI_ESLESTIRME' , 0 );
					}
				}
			}

			// 	Kategori Eşleştirme Yapılmışmı 
			if ( get_option('HE_OK_KATEGORI_ESLESTIRME') == 0 ) {	
				echo he_message('notice notice-error is-dismissible',__('<b>HATA : Kategori eşleştirmesi henüz tamamlanmamış!</b> Lütfen <a href="?page=HaberEditoru&t=kategori_eslestirme">Kategori Eşleştirme</a> ayarlarınızı kontrol ediniz...',"HaberEditoru")) ;
				$GLOBALS['HE_d_Tab'] = "kategori_eslestirme" ; 
			}
			
			// 	İçerik Kaynakları
			if ( empty($HE_OPT_KAYNAKLAR) ) {
				echo he_message('notice notice-error is-dismissible',__('<b>HATA :</b> <b>İçerik Kaynakları Seçilmemiş!</b> Lütfen içerik kaynaklarınızı düzenleyin...',"HaberEditoru")) ;
				$GLOBALS['HE_d_Tab'] = "ayarlar" ;
			} 
			if ( empty($HE_OPT_KATEGORILER) ) {	
				echo he_message('notice notice-error is-dismissible',__('<b>HATA : İçerik kategorileri seçilmemiş!</b> Lütfen içerik <a href="?page=HaberEditoru&t=ayarlar">kategorilerini seçin...</a>',"HaberEditoru")) ;
				$GLOBALS['HE_d_Tab'] = "ayarlar";
			} 
		}

	} 

	public static function he_get_settings(){
		if ( HE_DOMAIN != "" && HE_API_KEY!="" ) {
			
			$HE_XML_DOMAIN = he_curl(HE_API_URL."/get/domain?d=".HE_DOMAIN."&k=".HE_API_KEY);
			
			if ($HE_XML_DOMAIN==""){
				 update_option('HE_IS_SETUP',false) ;
			} else {	
				if ( $HE_XML_DOMAIN == "{false}" ) {
					update_option('HE_IS_SETUP',false) ;
					echo he_message('notice notice-error is-dismissible','<b>KRİTİK HATA :  </b> ALAN ADI veya API_KEY geçersiz. Lütfen bizimle <a target="_blank" href="http://habereditoru.com/iletisim/">iletişime</a> geçiniz. <br>
						Alan Adı : <b>'.HE_DOMAIN.'</b> API_KEY : <b>'.HE_API_KEY.'</b> ') ;
						$GLOBALS['HE_d_Tab'] = "abonelik";
				} else {
					$he_obj_domain = json_decode($HE_XML_DOMAIN) ;
					update_option('HE_CONFIG',$he_obj_domain) ; 
					update_option('HE_CONFIG_LAST_UPDATE',date("Y-m-d H:i", time())) ; 
					update_option('HE_SITE_ID',$he_obj_domain->{"SiteID"}) ;
					update_option('HE_DOMAIN',$he_obj_domain->{"Domain"}) ;
					update_option('HE_IS_SETUP',true) ;
					update_option('HE_MAX_ROBOT',$he_obj_domain->{"RobotCount"}) ;
					update_option('HE_MAX_KAYNAK',$he_obj_domain->{"AgenciesCount"}) ;
					update_option('HE_MAX_CRON',$he_obj_domain->{"Abonelik"}) ;
					define('HE_END_DATE', $he_obj_domain->{"EndDate"} );

					if ( strlen($he_obj_domain->{"Message"}) > 10 ) {
						echo he_message('notice is-dismissible',$he_obj_domain->{"Message"}) ;
					}

					if ( strtotime($he_obj_domain->{"EndDate"}) < time() ){
						echo he_message('notice notice-error is-dismissible',__('<b>HATA : Abonelik süreniz bitti.</b> Haber çekmeye devam etmek istiyorsanız lütfen <a href="?page=HaberEditoru&t=abonelik">abone olunuz...</a>',"HaberEditoru")) ;
						$GLOBALS['HE_d_Tab'] = "abonelik" ;
					}				
				}
			}
			
			if (HE_DEBUG) {echo HE_DOMAIN . " -> " . __("Ayarlar Alındı...","HaberEditoru"). "<br>" ; }
			
		} else {
			update_option('HE_IS_SETUP',false) ;
			$GLOBALS['HE_d_Tab'] ="abonelik";
			echo he_message('notice notice-error is-dismissible','<b>KRİTİK HATA :  </b> ALAN ADI veya API_KEY tanımlanmamış. Sitenizi kayıt etmediyseniz <a href="'.get_admin_url().'?page=HaberEditoru&t=abonelik">buradan kayıt edebilir</a> yada bizimle <a target="_blank" href="http://habereditoru.com/iletisim/">iletişime</a> geçebilirsiniz. <br>
						Alan Adı : <b>'.HE_DOMAIN.'</b>, API_KEY : <b>'.HE_API_KEY.'</b> ') ;
		}
	}
	
	

	public static function he_admin_bar_menu() {
		global $wp_admin_bar;
		$menu_id = 'HaberEditoru';
		$wp_admin_bar->add_menu(array('id' => $menu_id, 'title' => '<img style="vertical-align: text-bottom;" src="'.plugin_dir_url( __FILE__ ).'/img/icon.png'.'"></img> Haber Botu', 'href' => '#' , 'meta' => array('class' => '') ));
		$wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Bot Yönetimi','HaberEditoru'), 'id' => 'he-robotlar', 'href' => get_admin_url() . '?page=HaberEditoru&t=bot', 'meta' => array('class' => 'first-toolbar-group')));
		$wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('İçerik Kaynakları','HaberEditoru'), 'id' => 'he-websettings', 'href' => get_admin_url() .'?page=HaberEditoru&t=ayarlar'));
		$wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Kategori Eşleştirme','HaberEditoru'), 'id' => 'he-categories', 'href' => get_admin_url() .'?page=HaberEditoru&t=kategori_eslestirme'));
		$wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Aktiviteler','HaberEditoru'), 'id' => 'he-logs', 'href' => get_admin_url() .'?page=HaberEditoru&t=logs'));
		$wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Abonelik ve Ödeme','HaberEditoru'), 'id' => 'he-member', 'href' => get_admin_url() .'?page=HaberEditoru&t=abonelik'));
	}
	
	public function he_admin_menu(){
		add_menu_page( 'Haber Botu', 'Haber Botu', 'manage_options', 'HaberEditoru', 'HE_init', plugin_dir_url( __FILE__ ).'/img/icon.png');
	}

	

	public static function he_curl($url) {
		if (function_exists('curl_init')) {
			$ch = curl_init(($url));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_REFERER, $url);
			curl_setopt($ch, CURLOPT_HEADER,0);
			$sonuc =curl_exec($ch);
			$info = curl_getinfo($ch);
			$he_status_code = $info['http_code'] ; 
			$he_http_result = $sonuc ;
			curl_close($ch);
		} else {
			$sonuc = @file_get_contents(($url));
			$matches = array();
			preg_match('#HTTP/\d+\.\d+ (\d+)#', $http_response_header[0], $matches);
			$he_status_code = $matches[1] ; 
			$he_http_result = $sonuc ;
		}
		if($he_status_code!="200"){
			echo he_message('notice notice-error is-dismissible','<b>KRİTİK HATA (CURL) :  </b> BOT HaberEditoru.com sunucusuna erişemiyor... Bir kaç dakika sonra <a href="'.get_admin_url().'?page=HaberEditoru"> tekrar deneyiniz</a>, eğer düzelmez ise lütfen <a href="http://habereditoru.com/iletisim/">bize ulaşınız</a>.<br>
							Hata Kodu : <b>'.$info['http_code'].'</b> URL : <b><a target="_blank" href="'.$url.'">'.$url.'</a></b> ') ;
			die();
		} else {
			return $sonuc;
		}
}

	public static function get_ip_address() {
		return isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null;
	}
	
	private static function get_user_agent() {
		return isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : null;
	}

	private static function get_referer() {
		return isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : null;
	}


	public static function plugin_activation() {
		if ( version_compare( $GLOBALS['wp_version'], HE_MINIMUM_WP_VERSION, '<') ) {
			load_plugin_textdomain( 'HaberEditoru');
			return '<div class="notice notice-error is-dismissible"><p><strong>'.sprintf(esc_html__( 'HaberEditoru %s minimum WordPress %s üzerinde çalışabilir.' , 'HaberEditoru'), HE_PLUGIN_VERSION, HE_MINIMUM_WP_VERSION ).'</strong> '.sprintf(__('Lütfen <a href="%s">WordPress\'i güncelleyin</a>.', 'HaberEditoru'), 'https://codex.wordpress.org/Upgrading_WordPress').'</p></div>';
 		}
	}


	public static function plugin_deactivation( ) {
		
		
	}
	

}

class HaberEditoruCron {
	
	function he_cron_calistir() {
		$HE_CronContent = he_curl( plugins_url('cron.php?SiteKey=' . HE_API_KEY ,__FILE__) );
		$HE_CronContentA = get_option('HE_LAST_CRON') ;
		update_option( 'HE_LAST_CRON', substr( $HE_CronContent . $HE_CronContentA,0, 10000 )   );
	}
	
	public function he_cron_deactive() {
		wp_clear_scheduled_hook( 'he_cron_event');
	} 

	public function he_cron_activation() {
		update_option('HE_OPT_CRON_MINUTE', HE_CRON_DEFAULT_MINUTE);
		if ( !wp_next_scheduled( 'he_cron_event') ) {
		  wp_schedule_event( time(), HE_CRON_DEFAULT_MINUTE.'m', 'he_cron_event');
		}
	}
	
	public function he_crons_new_times( $schedules ) {
		$schedules['5m'] = array('interval' => 300,'display' => __( '5 Dakikada bir','HaberEditoru'));
		$schedules['10m'] = array('interval' => 600,'display' => __( '10 Dakikada bir','HaberEditoru'));
		$schedules['15m'] = array('interval' => 900,'display' => __( '15 Dakikada bir','HaberEditoru'));
		$schedules['30m'] = array('interval' => 1800,'display' => __( '30 Dakikada bir','HaberEditoru'));
		$schedules['120m'] = array('interval' => 7200,'display' => __( '2 Saatte bir','HaberEditoru'));
		$schedules['180m'] = array('interval' => 10800,'display' => __( '3 Saatte bir','HaberEditoru'));
		$schedules['360m'] = array('interval' => 21600,'display' => __( '6 Saatte bir','HaberEditoru'));
		return $schedules;
	}
	
}



