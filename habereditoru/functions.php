<?php

function HE_init(){
	
	$HaberEditoru = new HaberEditoru();

	$HaberEditoru->he_get_settings();
	
 	if ( !HE_IS_SETUP ) {
			
			// HE Domain Kayıtlı Değilse
			$he_domain_check = he_curl( HE_API_URL ."/get/check?d=".HE_DOMAIN);
			if ($he_domain_check != '{true}'){
				$HE_Tab = "abonelik";
			} 
			
	} else {
		
		$HaberEditoru->he_check_option();
		 		
		// Tab ayarları
		$HE_Tab="bot";
		if (isset($_GET['t'])){$HE_Tab=$_GET['t'];}
		if ($GLOBALS['HE_d_Tab']!="" ){$HE_Tab=$GLOBALS['HE_d_Tab'];}
	}
		
	// Header Eklentileri
	wp_enqueue_script('HaberEditoru-script', HE_PLUGIN_DIR_URL . 'js/scripts.js');
	wp_enqueue_style( 'HaberEditoru-style', HE_PLUGIN_DIR_URL.'css/style.css');
	
	
	
	//include_once "help.php";
 	echo '<div class="wrap">
	<h1><img style="height:32px;vertical-align:middle" src="'.HE_PLUGIN_DIR_URL.'img/logo.png"> Habereditoru.com <small style="float:right;color:rgba(0,0,0,.3);">WP Otomatik Haber Botu</small></h1>
	
	<div style="margin-bottom:0" class="wp-filter">
		<ul class="filter-links">
		<li><a href="'.get_admin_url().'?page=HaberEditoru&t=bot" class="'; if ($HE_Tab=="bot"){echo "current";} echo '">'. __("Bot Yönetimi","HaberEditoru"). '</a></li>
		<li><a href="'.get_admin_url().'?page=HaberEditoru&t=ayarlar" class="'; if ($HE_Tab=="ayarlar"){echo "current";} echo '" id="ayar">'. __("İçerik Kaynakları","HaberEditoru"). '</a></li>
		<li><a href="'.get_admin_url().'?page=HaberEditoru&t=kategori_eslestirme" class="'; if ($HE_Tab=="kategori_eslestirme"){echo "current";} echo '">'. __("Kategori Eşleştirme","HaberEditoru"). '</a></li>
		<li><a href="'.get_admin_url().'?page=HaberEditoru&t=logs" class="'; if ($HE_Tab=="logs"){echo "current";} echo '">'. __("Aktiviteler","HaberEditoru"). '</a></li>
		<li><a href="'.get_admin_url().'?page=HaberEditoru&t=abonelik" class="'; if ($HE_Tab=="abonelik"){echo "current ";} echo '">'. __("Abonelik","HaberEditoru"). '</a></li>
		<li class="he-time"><a href="'.get_admin_url().'?page=HaberEditoru&t=abonelik">'.he_lastdate_write(date_format(date_create(HE_END_DATE),"Y-m-d")).'</a></li>
		</ul>
		<div class="filter-count">
			<span title="'.__("Kaynak Adet / Robot Adet").'" class="count bot-count">'.get_option('HE_MAX_KAYNAK') . ' / ' . get_option('HE_MAX_ROBOT').'</span>
		</div>
	</div>';
	if ($HE_Tab == "bot"){
		include_once "inc/bot.php";
	}elseif($HE_Tab == "ayarlar"){
		include_once "inc/genel_ayarlar.php";
	}elseif($HE_Tab == "kategori_eslestirme"){
		include_once "inc/kategori_eslestirme.php";
	}elseif($HE_Tab == "logs"){
		include_once "inc/logs.php";
	}elseif($HE_Tab == "abonelik"){
		include_once "inc/abonelik.php";
	}
	
	
	$he_sistem_saati = date('H:i');
	$he_cron_sonraki_zaman = date('H:i', wp_next_scheduled('he_cron_event') ); 
	echo "<div class='clear'></div><br><br><hr>v.".HE_PLUGIN_VERSION." | <a href='http://www.habereditoru.com' target='_blank'>HaberEditoru.com</a> | <a href='mailto:".HE_PLUGIN_DESTEK_MAIL."'>".HE_PLUGIN_DESTEK_MAIL."</a> <span style='float:right' title='".get_option("HE_CONFIG_LAST_UPDATE")."'>SiteID : <b>".get_option("HE_SITE_ID")."</b> Domain : <b>". HE_DOMAIN ."</b>  ".__("Sistem Saatı","HaberEditoru")." : <b>" . $he_sistem_saati . "</b>";
	if (get_option('HE_OPT_CRON_AKTIF')){
		echo " " . __("Sonraki Haber Çekme Saatı","HaberEditoru") . " : <b>" . $he_cron_sonraki_zaman . "</b></span>" ;
	} else {
		echo '   <a href="'.get_admin_url().'?page=HaberEditoru&t=ayarlar">(!) ' . __("Otomatik Haber Çekme Kapalı","HaberEditoru") . '</a> </span>' ;
	}
	echo '</div>';
}

function he_message($he_type,$he_message){
	return '<div class="'.$he_type.'"><p>'.$he_message.'</p></div>' ; 

}

function he_curl($url) {
		//echo $url ;
		if (function_exists('curl_init')) {
			$ch = curl_init(str_replace(" ","%20",$url));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_REFERER, $url);
			curl_setopt($ch, CURLOPT_HEADER,0);
			$sonuc =curl_exec($ch);
			$info = curl_getinfo($ch);
			$he_status_code = $info['http_code'] ; 
			$he_http_result = $sonuc ;
			curl_close($ch);
		} else {
			$sonuc = @file_get_contents(str_replace(" ","%20",$url));
			$matches = array();
			preg_match('#HTTP/\d+\.\d+ (\d+)#', $http_response_header[0], $matches);
			$he_status_code = $matches[1] ; 
			$he_http_result = $sonuc ;
		}
		if($he_status_code!="200"){
			echo he_message('notice notice-error is-dismissible', '<b>' . __("KRİTİK HATA","HaberEditoru") . ' (CURL) :  </b> BOT HaberEditoru.com sunucusuna erişemiyor... Bir kaç dakika sonra <a href="'.get_admin_url().'?page=HaberEditoru"> tekrar deneyiniz</a>, eğer düzelmez ise lütfen <a href="http://habereditoru.com/iletisim/">bize ulaşınız</a>.<br>
							Hata Kodu : <b>'.$info['http_code'].'</b> URL : <b><a target="_blank" href="'.$url.'">'.$sonuc .'</a></b> ') ;
			die();
		} else {
			return $sonuc;
		}
}

function he_tarihFark($tarih1,$tarih2,$ayrac){
	list($y1,$a1,$g1) = explode($ayrac,$tarih1);
	list($y2,$a2,$g2) = explode($ayrac,$tarih2);
	$t1_timestamp = mktime('0','0','0',$a1,$g1,$y1);
	$t2_timestamp = mktime('0','0','0',$a2,$g2,$y2);
	if ($t1_timestamp > $t2_timestamp)	{
		$result = ($t1_timestamp - $t2_timestamp) / 86400;
	}elseif ($t2_timestamp > $t1_timestamp)	{
		$result = ($t2_timestamp - $t1_timestamp) / 86400;
	}
	return $result;
}
 
function he_lastdate_write($tarih) {
	$bugun = date('Y-m-d');
	//$tarih = "2011-09-28";
	echo $bugun . " - " . $tarih ."<br>";

	$gun = intval(he_tarihFark($tarih,$bugun,'-'));
	 
	if ($bugun == $tarih ) 
			return "<span class=\"he-time-end\">".__("Bugün bitiyor...","HaberEditoru")."</span>"; 
	elseif ($bugun > $tarih )
			return "<span class=\"he-time-end\">".sprintf(__("%s gün geçti","HaberEditoru"),$gun) ."</span>";
	else
			return "<span class=\"he-time-wait\">".sprintf(__("%s gün kaldı","HaberEditoru"),$gun) ."</span>";
	
}
	
function he_insert_attachment($post_file,$post_id,$IcerikBaslik='',$custom_name='file') {
	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	require_once(ABSPATH . "wp-admin" . '/includes/media.php');
	$tmp = download_url( $post_file );
	preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG|mpg|MPG|mp4|MP4)/', $post_file, $matches);
	$file_array['name'] = basename($matches[0]);
	$file_array['tmp_name'] = $tmp;
	
	if ( is_wp_error( $tmp ) ) {
		@unlink($file_array['tmp_name']);
		$file_array['tmp_name'] = '';
	}
	$e_post_data = array();
	$e_post_data['post_title'] = $IcerikBaslik ;
	$e_post_data['post_excerpt'] = $IcerikBaslik ;
	$e_attach_id = media_handle_sideload( $file_array, $post_id, $IcerikBaslik,$e_post_data );
	add_post_meta($post_id,$custom_name,wp_get_attachment_url($e_attach_id));
	return $e_attach_id;
	
}

function he_resim_kontrol($resim){
	$photo = new WP_Http();
	$photo = $photo->request(  $resim  );
	if( $photo['response']['code'] != 200 ){
		return false;
	}else{
		return true;
	}
}
function he_addImages( $postid, $photo_name,$parent_id,$baslik='') {
	
	$post = get_post( $postid );
	if( empty( $post ) )
		return false;
	$photo = new WP_Http();
	$photo = $photo->request(  $photo_name  );
	if( $photo['response']['code'] != 200 )
		return false;
    $baslik = ($baslik);
	$attachment = wp_upload_bits( $baslik . '.jpg', null, $photo['body'], date("Y-m", strtotime( $photo['headers']['last-modified'] ) ) );
	if( !empty( $attachment['error'] ) )
		return false;
	$filetype = wp_check_filetype( basename( $attachment['file'] ), null );
	$postinfo = array(
		'post_mime_type'	=> $filetype['type'],
		'post_title'		=> $post->post_name,
        'post_name'			=> $post->post_name,
		'post_content'		=> '',
		'post_status'		=> 'inherit',
        'post_parent'		=> $parent_id,
	);
	$filename = $attachment['file'];
	$attach_id = wp_insert_attachment( $postinfo, $filename, $postid );
	$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
	wp_update_attachment_metadata( $attach_id,  $attach_data );
    unset ($photo);
	return $attach_id;
}
 
function he_jsontoxml($icerik){
	$xml = json_decode($icerik);
	return $xml;
}
function he_xmltojson($icerik){
	$xml = simplexml_load_string($icerik);
	return json_encode($xml);
}
function he_curltoxml($icerik){
	$xml = simplexml_load_string($icerik, null, LIBXML_NOCDATA);
	return $xml;
}
function he_xmlclear($xml){
	$xml = str_replace( ":encoded", "", $xml );
	return $xml;
}
function he_getEditorler($sel="") {
	$Selected = $sel ;
	global $wpdb;
	$order = 'user_nicename';
	$user_ids = $wpdb->get_col("SELECT ID FROM $wpdb->users ORDER BY $order");
	echo '<select style="width:100%" name="site_editor" id="site_editor">';
	foreach ($user_ids as $user_id) {
		$user = get_userdata($user_id);
		if ( $user->user_level > 0 ) {
			$option = '<option ';
			if ( $Selected == $user_id ) { $option .= ' selected="selected" '; };
				$option .= ' value="'.$user_id.'">'.$user->display_name . '</option>';	
			echo $option; 
		}
	}
	echo '</select>';
}
function he_redirect($adres){
	header("location:$adres");exit();
	echo "<script>window.location.href='$adres';</script>";
}
?>