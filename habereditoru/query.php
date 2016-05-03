<?php
$path = preg_replace('/wp-content(?!.*wp-content).*/','',dirname(__FILE__));
require_once $path."/wp-load.php";

require_once 'settings.php';

if( !is_user_logged_in() ) {
	
	wp_he_redirect("/index.php");
	
}else{
	
	$kullanici = $current_user->ID;
	$HE_ISLEM = $_POST['tip'];

	// İçerik Kaynakları
	if ($HE_ISLEM == "ga"){
		
		if (!isset( $_POST['icerik_dili'])){$HE_GET_DILLER="tr";}else {$HE_GET_DILLER=$_POST['icerik_dili'];}
		if (!isset( $_POST['icerik_tipi'])){$HE_GET_CONTENT_TYPE="0";}else {$HE_GET_CONTENT_TYPE=$_POST['icerik_tipi'];}
		if (!isset( $_POST['kaynaklar'])){$HE_GET_KAYNAKLAR=array();}else {$HE_GET_KAYNAKLAR=$_POST['kaynaklar'];}
		if (!isset( $_POST['kategoriler'])){$HE_GET_KATEGORILER=array();}else {$HE_GET_KATEGORILER=$_POST['kategoriler'];}
		if (!isset( $_POST['cron_minute'])){$HE_GET_CRON_MINUTE=HE_CRON_DEFAULT_MINUTE;}else {$HE_GET_CRON_MINUTE=$_POST['cron_minute'];}
		
		
		$HE_OPT_DILLER = get_option('HE_OPT_ICERIK_DILLERI');
		$HE_OPT_CONTENT_TYPE = get_option('HE_OPT_CONTENT_TYPE');

		// CRON AYARLARI			
		if ( $HE_GET_CRON_MINUTE!="-" ) {
			wp_clear_scheduled_hook( 'he_cron_event');
			wp_schedule_event( time() ,$HE_GET_CRON_MINUTE, 'he_cron_event');
			add_action('he_cron_event', array( 'HaberEditoruCron', 'he_cron_calistir' ) );
			update_option('HE_OPT_CRON_AKTIF', true);	
		} else {
			update_option('HE_OPT_CRON_AKTIF', false);	
			wp_clear_scheduled_hook( 'he_cron_event');
		}
		
		update_option('HE_OPT_CRON_MINUTE', $HE_GET_CRON_MINUTE);	
		
		if ($HE_OPT_DILLER!=$HE_GET_DILLER){
			update_option( 'HE_OPT_ICERIK_DILLERI', $HE_GET_DILLER );
			echo he_message("notice notice-success is-dismissible",__("İçerik dili değiştirildi. Lütfen İçerik kaynaklarını ve kategorilerini tekrar seçiniz...","HaberEditoru"));
			echo '<script>setTimeout("window.location.href=\'?page=HaberEditoru&t=ayarlar\'",2000);</script>';
		} else if ($HE_OPT_CONTENT_TYPE!=$HE_GET_CONTENT_TYPE){
			update_option( 'HE_OPT_CONTENT_TYPE', $HE_GET_CONTENT_TYPE );
			echo he_message("notice notice-success is-dismissible",__("İçerik tipi değiştirildi. Lütfen İçerik kaynaklarını ve kategorilerini tekrar seçiniz...","HaberEditoru"));
			echo '<script>setTimeout("window.location.href=\'?page=HaberEditoru&t=ayarlar\'",2000);</script>';
		} else {
			
			$HE_XML_DOMAIN = he_jsontoxml(he_curl(HE_API_URL."/get/domain?d=".HE_DOMAIN."&k=".HE_API_KEY));
			$HE_MAX_KAYNAK = intval($HE_XML_DOMAIN->AgenciesCount);
			
			if (count($HE_GET_KAYNAKLAR)>$HE_MAX_KAYNAK){
				echo he_message("notice notice-error is-dismissible", sprintf(__("En fazla %s adet haber kaynağı seçebilirsiniz","HaberEditoru"),$HE_MAX_KAYNAK));
				return false;
			}
			
			if (count($HE_GET_KAYNAKLAR)<1){
				echo he_message("notice notice-error is-dismissible",__("En az 1 adet haber kaynağı seçmelisiniz...","HaberEditoru"));
				return false;
			}
			if (count($HE_GET_KATEGORILER)<1){
				echo he_message("notice notice-error is-dismissible",__("En az 1 adet kategori seçmelisiniz...","HaberEditoru"));
				return false;
			}
			
			update_option('HE_OPT_KATEGORILER', $HE_GET_KATEGORILER );
			update_option('HE_OPT_KAYNAKLAR', $HE_GET_KAYNAKLAR);
		
			echo he_message("notice notice-success is-dismissible",__("Tebrikler, şimdi kategori eşleştirmesi yapmanız için yönlendiriliyorsunuz...","HaberEditoru"));
			echo '<script>setTimeout("window.location.href=\'?page=HaberEditoru&t=kategori_eslestirme\'",2000);</script>';
			
			$HE_GET_KAYNAKLAR_ARR 	= implode(",",$HE_GET_KAYNAKLAR) ;
			$HE_GET_KATEGORILER_ARR = implode(",",$HE_GET_KATEGORILER) ;
			$HE_POST_DATA = "a=$HE_GET_KAYNAKLAR_ARR&c=$HE_GET_KATEGORILER_ARR&l=$HE_GET_DILLER&t=$HE_GET_CONTENT_TYPE";
			$HE_POST_URL = HE_API_URL . "/set/settings?d=".HE_DOMAIN."&k=" . HE_API_KEY . "&" . $HE_POST_DATA ; 
			//echo $HE_POST_URL . "<br>";
			$HE_XML = he_curl($HE_POST_URL);
			//echo $HE_XML ;
			
		}

		
	// kategori eşitleme kayıt
	}elseif($HE_ISLEM == "ke"){
		
		$HE_GET_KAT_ARR= array();
	 
		for ($HE_COUNTER=1;$HE_COUNTER<=count($_POST);$HE_COUNTER++){
			if (isset($_POST['r_'.$HE_COUNTER]) && isset($_POST['l_'.$HE_COUNTER]) ){
				$HE_R_CAT = $_POST['r_'.$HE_COUNTER];
				$HE_L_CAT = $_POST['l_'.$HE_COUNTER];
				$HE_C_CAT = explode(",", $HE_R_CAT);
				foreach($HE_C_CAT as $HE_GET_CAT){
					$HE_GET_KAT_ARR[$HE_GET_CAT]=$HE_L_CAT;
				}
			}
		}
		
		update_option( 'HE_OPT_KAT_ESLESTIRME', $HE_GET_KAT_ARR);
		/*
		for ($k=1;$k<10;$k++){
			$HE_GET_ID = $k;
			$HE_BOT_ADI = "bot_".$HE_GET_ID."_";
			delete_option( $HE_BOT_ADI.'ajanslar');
			delete_option( $HE_BOT_ADI.'ajanslar_str');
			delete_option( $HE_BOT_ADI.'kategoriler');
			delete_option( $HE_BOT_ADI.'kategoriler_str');
			delete_option( $HE_BOT_ADI.'aktif');
		}*/
		
		echo he_message("notice notice-success is-dismissible",__('Tebrikler, değişiklikler kayıt edildi. Kategori kayıt işlemleriniz bitti ise lütfen <a href="?page=HaberEditoru&t=bot">Robot Ayarlarınızı</a> düzenleyiniz...',"HaberEditoru"));
		echo '<script>setTimeout("window.location.href=\'?page=HaberEditoru&t=kategori_eslestirme\'",2000);</script>';
		
	// bot ayarları kayıt
	}elseif($HE_ISLEM == "ba"){
		if (!isset( $_POST['bot'])){$HE_GET_ID="1";}else {$HE_GET_ID=$_POST['bot'];}
		$HE_BOT_ID			= "HE_BOT_".$HE_GET_ID."_";
		$HE_BOT_ADI			= "BOT ".$HE_GET_ID;
		$HE_BOT_SETTINGS 	= get_option( $HE_BOT_ID.'SETTINGS') ;
		$HE_BOT_HEID 		= $HE_BOT_SETTINGS['HEID'];
		$HE_BOT_POST_DATA = $_POST ;
		$HE_BOT_POST_DATA['adi'] = $HE_BOT_ADI ;
		
		if (!isset( $_POST['aktif'])){$HE_BOT_POST_DATA['aktif']="0";}
		if (!isset( $_POST['icerik_dili'])){$HE_BOT_POST_DATA['icerik_dili']="tr";}
		if (!isset( $_POST['icerik_tipi'])){$HE_BOT_POST_DATA['icerik_tipi']="0";}
		if (!isset( $_POST['site_editor'])){$HE_BOT_POST_DATA['site_editor']="1";}
		if (!isset( $_POST['post_durumu'])){$HE_BOT_POST_DATA['post_durumu']="publish";}
		if (!isset( $_POST['post_tipi'])){$HE_BOT_POST_DATA['post_tipi']="post";}
		if (!isset( $_POST['resimsiz_haber'])){$HE_BOT_POST_DATA['resimsiz_haber']="0";}

		$HE_GET_KAYNAKLAR_ARR 	= implode(",",$HE_BOT_POST_DATA['kaynaklar']) ;
		$HE_GET_KATEGORILER_ARR = implode(",",$HE_BOT_POST_DATA['kategoriler']) ;
		
		$HE_POST_DATA = "ID=$HE_GET_ID&HEID=$HE_BOT_HEID&Name=$HE_BOT_ADI&ContentType=".$HE_BOT_POST_DATA['icerik_tipi']."&Lang=".$HE_BOT_POST_DATA['icerik_dili']."&Agencies=$HE_GET_KAYNAKLAR_ARR&Status=".$HE_BOT_POST_DATA['aktif']."&Tags=".$HE_BOT_POST_DATA['etiketler']."&NegativeTags=".$HE_BOT_POST_DATA['negatif_etiketler']."&Categories=$HE_GET_KATEGORILER_ARR&PostAuthor=".$HE_BOT_POST_DATA['site_editor']."&PostStatus=".$HE_BOT_POST_DATA['post_durumu']."&PostType=".$HE_BOT_POST_DATA['post_tipi']."&Order=$HE_GET_ID&NoImage=".$HE_BOT_POST_DATA['resimsiz_haber'];
		$HE_POST_URL = HE_API_URL . "/set/bot?d=".HE_DOMAIN."&k=" . HE_API_KEY . "&" . $HE_POST_DATA ; 
 		//echo $HE_POST_URL ;

		$HE_XML = he_curl($HE_POST_URL);
		$HE_OBJ = json_decode($HE_XML) ;
		$HE_BOT_HEID = $HE_OBJ->{"SUCCESS"} ;
		if ( strlen($HE_BOT_HEID)>2 ) {
			$HE_BOT_POST_DATA['HEID'] = $HE_BOT_HEID ;
 		} 
		
		update_option( $HE_BOT_ID.'SETTINGS', $HE_BOT_POST_DATA );
		
		echo he_message("notice notice-success is-dismissible",__("Bot ayarları kayıt edildi...","HaberEditoru"));
		echo '<script>setTimeout("window.location.href=\'?page=HaberEditoru&t=bot\'",2000);</script>';
	
	// bot etkinleştir
	}elseif($HE_ISLEM == "ba_e"){

		$HE_GET_ID = $_POST['bot'];
		
		$HE_BOT_ID			= "HE_BOT_".$HE_GET_ID."_";
		$HE_BOT_SETTINGS 	= get_option( $HE_BOT_ID.'SETTINGS') ;
		$HE_GET_STATUS = $_POST['aktif'];

		$HE_BOT_KAYNAKLAR = $HE_BOT_SETTINGS['kaynaklar'];
		$HE_BOT_KATEGORILER = $HE_BOT_SETTINGS['kategoriler'];
		$HE_BOT_DIL = $HE_BOT_SETTINGS['icerik_dili'];
		$HE_BOT_EDITOR = $HE_BOT_SETTINGS['site_editor'];
		$HE_BOT_POST_TIPI = $HE_BOT_SETTINGS['post_tipi'];
		
		if (!empty($HE_BOT_KAYNAKLAR) && !empty($HE_BOT_KATEGORILER) && !empty($HE_BOT_DIL) && !empty($HE_BOT_EDITOR) && !empty($HE_BOT_POST_TIPI)){
			$HE_BOT_SETTINGS['aktif'] = $HE_GET_STATUS ;
			update_option( $HE_BOT_ID.'SETTINGS', $HE_BOT_SETTINGS );
			echo he_message("notice notice-success is-dismissible",__("Bot Durumu Değiştirildi...","HaberEditoru"));
			echo '<script>setTimeout("window.location.href=\'?page=HaberEditoru&t=bot\'",2000);</script>';
		}else{
			echo he_message("notice notice-error is-dismissible",__("Bir hata oluştu. Lütfen tekrar deneyin...","HaberEditoru"));
		}

	// abonelik kayıt
	}elseif($HE_ISLEM == "ab"){	

		$NameSurname = urlencode ( $_POST['adi'] );
		$EMail = urlencode ($_POST['eposta']);
		$GSM = urlencode ($_POST['gsm']);
		$Langs = urlencode ($_POST['dil']);
		$PingURL = urlencode( str_replace(array(HE_DOMAIN ."/"), "", plugin_dir_url( __FILE__ )."cron.php" ) ) ;
		
		$PostURL = HE_API_URL . "/set/register?Domain=".HE_DOMAIN."&NameSurname=$NameSurname&EMail=$EMail&Contry=$Contry&GSM=$GSM&Langs=$Langs&PingUrl=$PingURL" ; 
		//echo $PostURL ;
		$HE_XML_REGISTER = he_curl($PostURL);
		$HE_OBJ_REGISTER = json_decode($HE_XML_REGISTER) ;
		
		$HE_API_KEY = $HE_OBJ_REGISTER->{"API_KEY"} ;
		if ( strlen($HE_API_KEY)>5 ) {
			update_option('HE_API_KEY',$HE_OBJ_REGISTER->{"API_KEY"} ) ; 
			update_option('HE_SITE_ID',$HE_OBJ_REGISTER->{"SiteID"} ) ; 
			echo he_message("notice notice-error is-dismissible",__("TEBRİKLER, Üyelik kaydınız yapıldı, İçerik Kaynakları bölümüne yönlendiriliyorsunuz...","HaberEditoru"));
			echo '<script>setTimeout("window.location.href=\'?page=HaberEditoru&t=ayarlar\'",2000);</script>';
		} else {
			if ( strlen($HE_OBJ_REGISTER->{"SEND_KEY"})>5 ) {
				echo $HE_OBJ_REGISTER->{"SEND_KEY"} ;
				update_option('HE_API_KEY',$HE_OBJ_REGISTER->{"SEND_KEY"} ) ;
				update_option('HE_IS_SETUP',true ) ;
				echo '<script>setTimeout("window.location.href=\'?page=HaberEditoru&t=abonelik\'",2000);</script>';
			} else {
				echo he_message("notice notice-error is-dismissible",__("ERROR") . " : " . $HE_OBJ_REGISTER->{"ERROR"} );
				return false ;
			}
		}

	} 
}
?>