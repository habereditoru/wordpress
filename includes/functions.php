<?php
if ( ! defined( 'ABSPATH' ) ) exit;

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
//    echo $bugun . " - " . $tarih ."<br>";

    $gun = intval(he_tarihFark($tarih,$bugun,'-'));

    if ($bugun == $tarih )
        return "<span class=\"he-time-end\">".__("Bugün bitiyor...","habereditoru")."</span>";
    elseif ($bugun > $tarih )
        return "<span class=\"he-time-end\">".sprintf(__("%s gün geçti","habereditoru"),$gun) ."</span>";
    else
        return "<span class=\"he-time-wait\">".sprintf(__("%s gün kaldı","habereditoru"),$gun) ."</span>";

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
        echo he_message('notice notice-error is-dismissible', '<b>' . __("KRİTİK HATA","habereditoru") . ' (CURL) :  </b> BOT habereditoru.com sunucusuna erişemiyor... Bir kaç dakika sonra <a href="'.get_admin_url().'?page=habereditoru"> tekrar deneyiniz</a>, eğer düzelmez ise lütfen <a href="http://habereditoru.com/iletisim/">bize ulaşınız</a>.<br>
							Hata Kodu : <b>'.$info['http_code'].'</b> URL : <b><a target="_blank" href="'.$url.'">'.$sonuc .'</a></b> ') ;
        die();
    } else {
        return $sonuc;
    }
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

function he_message($he_type,$he_message){
    return '<div class="'.$he_type.'"><p>'.$he_message.'</p></div>' ;

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
    wp_redirect( $adres ); exit();
    echo "<script>window.location.href='$adres';</script>";
}

function logToFile($msg)
{
    // open file
    $fd = fopen(HE_PLUGIN_DIR.'/log.txt', "a");
    // append date/time to message
    $str = "[" . date("Y/m/d h:i:s") . "] " . $msg;
    // write string
    fwrite($fd, $str . "\n");
    // close file
    fclose($fd);
}