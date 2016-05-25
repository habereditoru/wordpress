<?php
if ( ! defined( 'ABSPATH' ) ) exit;


function he_wp_loaded() {

    $HE_CronContentA = get_option('HE_LAST_CRON');
    $HE_CronContentB = '';

    $now = microtime(true);
    if ( $now - get_option('last_do_work_time') > 3 ) { #I set to 3 seconds to test
        update_option('last_do_work_time',$now);
    }

    $Islem = (filter_input( INPUT_GET, 'Islem' )) ? (filter_input( INPUT_GET, 'Islem' )) : '';
    $HE_GET_API_KEY = filter_input( INPUT_GET, 'SiteKey' );
    $he_api_key	= HE_API_KEY;

    if ($Islem=="version" ) {
        echo  HE_PLUGIN_VERSION ;
        return false;
        exit ;
    }

    if ($he_api_key!=$HE_GET_API_KEY or empty( $he_api_key )) {
        $HE_CronContentB .=  __("API_KEY Geçersiz...","habereditoru") ;
        return false;
    }


    $HE_ABONELIK	= get_option('HE_OPT_CRON_MINUTE') ;
    $he_bot_id = isset($_GET['b']) ? intval(filter_input( INPUT_GET, 'b' )) : 0 ;
    $HE_NOW = HE_TIMESTAMP;

    $HE_CronContentB .= "<hr><b>".__("Çalışma Zamanı","habereditoru")." : ". date('Y-m-d H:i:s', HE_TIMESTAMP ) ." " . __("Otomatik Çalışma","habereditoru") . " : ";

    $HE_CRON_AKTIF = get_option('HE_OPT_CRON_AKTIF');
    if ( $HE_CRON_AKTIF != false ) {
        $HE_CRON_AKTIF = true ;
        $HE_CronContentB .= sprintf(__("Evet %s","habereditoru"),$HE_ABONELIK) ;
    } else {
        $HE_CronContentB .= __("Hayır","habereditoru") . " " ;
    }

    if (get_option('HE_SEND_STATUS') == 0) {
        $HE_CronContentB .= he_message('notice notice-error is-dismissible',__('<b>HATA : Habereditörü yönetimi haber gönderimini durdurdu!</b> Detaylar için lütfen <a href="http://www.habereditoru.com/iletisim/">bize ulaşınız...</a>',"habereditoru")) ;
        exit;
    }

     $HE_CronContentB .= " </b><br>";

    if ( $he_api_key !="" && $HE_CRON_AKTIF ){

        /*
        $HE_LAST_CHECK = get_option('HE_LAST_CHECK');
        $HE_CRON_SN = intval($HE_ABONELIK)*60;
        $HE_CRON_UPDATE = intval($HE_LAST_CHECK)+$HE_CRON_SN;
        if (time() < $HE_CRON_UPDATE){
            $HE_CronContentB .= "(!) Otomatik haber çekme " ;
            $HE_CronContentB .= intval(($HE_CRON_UPDATE-time()) /60);
            $HE_CronContentB .= "dk sonra çalışacak... " ;
            return false;
            exit;
        }
        */

        ob_flush(); flush();

        $he_max_robot	= get_option('HE_MAX_ROBOT');
        $he_counter=1;

        if ($he_bot_id!=0 ){$he_counter = $he_bot_id ; 	$he_max_robot = $he_bot_id;	}
        for ($he_counter; $he_counter<=$he_max_robot; $he_counter++){


            $he_bot_id			= "HE_BOT_".$he_counter."_";
            $he_bot_settings 	= get_option( $he_bot_id.'SETTINGS') ;
            $he_bot_heid 		= isset($he_bot_settings['HEID']) ? $he_bot_settings['HEID'] : 0;



            $HE_CronContentB .= "<i>BOT ".$he_counter." - " .  date('Y-m-d H:i', HE_TIMESTAMP ) ."</i><br>";

            if ($he_bot_settings['aktif']=="1"){

                // FİLTRE UYGULANIYOR
                $HE_DOMAIN	=  str_replace("https://","",str_replace("http://","",get_option('HE_DOMAIN')));
                $HE_BOT_AJANSLAR_ARR = implode(",",$he_bot_settings['kaynaklar']);
                $he_bot_kategoriler_ARR = implode(",",$he_bot_settings['kategoriler']);
                $HE_BOT_ICERIK_DILI = $he_bot_settings['icerik_dili'];
                $HE_BOT_ICERIK_TIPI = $he_bot_settings['icerik_tipi'];
                $HE_BOT_ETIKETLER = $he_bot_settings['etiketler'];
                $HE_BOT_ETIKETLER_NEGATIF = $he_bot_settings['negatif_etiketler'];
                $HE_BOT_SPINNER = $he_bot_settings['post_spinner'];


                $HE_GET_URL = HE_API_URL."/$HE_DOMAIN/$he_api_key/?HEID=$he_bot_heid&a=$HE_BOT_AJANSLAR_ARR&c=$he_bot_kategoriler_ARR&Lang=$HE_BOT_ICERIK_DILI&ContentType=$HE_BOT_ICERIK_TIPI&Tags=$HE_BOT_ETIKETLER&NegativeTags=$HE_BOT_ETIKETLER_NEGATIF&Spinner=$HE_BOT_SPINNER" ;

//                $HE_CronContentB .= '<a target="_blank" href="'.$HE_GET_URL.'">'.$HE_GET_URL.'</a><br>';

                ob_flush();flush();

                $HE_XML = he_xmlclear(he_curl($HE_GET_URL));

                if ( strpos($HE_XML,'"ERROR"') === false  ){

                    $HE_XML = he_curltoxml($HE_XML);

                    $HE_SITE_ID	= get_option('HE_SITE_ID');
                    $HE_CATS_ARR = get_option('HE_OPT_KAT_ESLESTIRME');
                    $HE_BOT_ICERIK_ONU = $he_bot_settings['icerigin_onune_ekle'];
                    $HE_BOT_ICERIK_SONU = $he_bot_settings['icerigin_sonuna_ekle'];
                    $HE_BOT_CONTENT_AFTER_AGENCY_NAME = isset($he_bot_settings['kaynak_adi']) ? $he_bot_settings['kaynak_adi'] : '';
                    $HE_BOT_POST_AUTHOR = $he_bot_settings['site_editor'];
                    $HE_BOT_POST_STATUS = $he_bot_settings['post_durumu'];
                    $HE_BOT_POST_TYPE = $he_bot_settings['post_tipi'];
                    $HE_BOT_POST_DATE = $he_bot_settings['post_date'];
                    $HE_BOT_RESIM_OZEL_ALAN = $he_bot_settings['resim_ozel_alan_adi'];
                    $HE_ITEM_IF_NOT_IMAGE = $he_bot_settings['resimsiz_haber'];


                    foreach($HE_XML->channel->item as $HE_XML_ITEM){

                        /*
                        <item id="3048559" status="1" lang="tr" editorID="26" type="1" agencyID="13" agencyName="AA" >
                            <title url="zonguldak-ta-feto-pdy-nin-11-sirketine-kayyum-atandi"><![CDATA[Zonguldak'ta FETÖ/PDY'nin 11 şirketine kayyum atandı]]></title>
                            <description><![CDATA[Zonguldak merkez, Ereğli ve Çaycuma ilçelerinde Fetullahçı Terör Örgütü/Paralel Devlet Yapılanmasına (FETÖ/PDY) yönelik soruşturma kapsamında örgüte finansal destek sağladığı iddiasıyla 11 şirkete kayyum atandı.]]></description>
                            <content:encoded><![CDATA[<p><span>Zonguldak Cumhuriyet Başsavcılığınca yürütülen soruşturma kapsamında Sulh Ceza Hakimliğince, Fatih Koleji, Fem-Anafen Dershanesi, Ereğli Fem-Anafen Dershanesi, Ereğli Fatih Koleji, Çaycuma Anafen Dershanesi ile aralarında iki otomotiv firması bayisinin de yer aldığı 6 şirket olmak üzere 11 şirkete</span> kayyum<span> atanmasına karar verildi.</span><br></p><p>Şirketlere bağlı eğitim kurumu ve iş yerlerinde önlem alan polis ekipleri, ilgili yöneticilere kararı tebliğ ederek binalara girmelerine izin vermedi. </p><p>FETÖ/PDY'ye finansman desteği sağlandığı iddiasıyla atanan 6 kayyumun, Emniyet Müdürlüğü Kaçakçılık ve Organize Suçlarla Mücadele Şubesi ekipleriyle şirketlerin bulunduğu binalara gelerek bugün çalışmalara başlayacağı öğrenildi.</p>]]></content:encoded>
                            <tags><![CDATA[Zonguldak,FETÖ,Kayyum,Atanma]]></tags>
                            <image>http://aa.com.tr/uploads/Contents/2016/04/09/thumbs_b_c_1da5ba841df708ddd3f4e0054737016e.jpg</image>
                            <pubDate update="2016-04-09 10:47:06">2016-04-09 10:40:35</pubDate>
                            <guid>http://www.aa.com.tr/tr/turkiye/zonguldakta-feto-pdynin-11-sirketine-kayyum-atandi/551889</guid>
                            <category id="36">Gündem</category>
                            <files>
                                <file type="image" url="htpp://www.file.com/file.jpg">Dosya Adı</file>
                                <file type="video" url="htpp://www.file.com/file.jpg">Dosya Adı</file>
                                <file type="swf" url="htpp://www.file.com/file.jpg">Dosya Adı</file>
                                <file type="other" url="htpp://www.file.com/file.jpg">Dosya Adı</file>
                            </files>
                            <loadTime>109,375 ms</loadTime>
                        </item>
                        */

                        $HE_ITEM_ADD		= "0";
                        $HE_ITEM_ID 		= (int)$HE_XML_ITEM['id'];
                        $HE_ITEM_CAT_ID 	= (int)$HE_XML_ITEM->category['id'];
                        $HE_ITEM_CAT_NAME 	= (string)$HE_XML_ITEM->category;
                        $HE_CAT_ID 			= @$HE_CATS_ARR[$HE_ITEM_CAT_ID];
                        $HE_ITEM_PREVIEW_IMG= (string)$HE_XML_ITEM->image;
                        $HE_ITEM_GUID 		= (string)$HE_XML_ITEM->guid;
                        $HE_ITEM_TITLE 		= (string)$HE_XML_ITEM->title;
                        $HE_ITEM_DESC 		= (string)$HE_XML_ITEM->description;
                        $HE_ITEM_CONTENT 	= (string)$HE_XML_ITEM->content;
                        $HE_ITEM_TAGS 		= (string)$HE_XML_ITEM->tags;
                        $HE_ITEM_PUB_DATE 	= (string)$HE_XML_ITEM->pubDate;
                        $HE_ITEM_AGANCY_NAME= (string)$HE_XML_ITEM['agencyName'];
                        $HE_ITEM_FILES 		= $HE_XML_ITEM->files->file;

                        if ($HE_ITEM_PREVIEW_IMG!=""){
                            $HE_IS_IMAGE_URL = he_resim_kontrol($HE_ITEM_PREVIEW_IMG);
                        }else{
                            $HE_IS_IMAGE_URL=false;
                        }

                        if ($HE_IS_IMAGE_URL==false && $HE_ITEM_IF_NOT_IMAGE!="1"){$HE_ITEM_ADD="1";}
                        if ($HE_BOT_POST_DATE!="1"){$HE_ITEM_PUB_DATE = date('Y-m-d H:i', HE_TIMESTAMP) ;}
                        if ($HE_BOT_ICERIK_ONU!=""){$HE_ITEM_CONTENT = $HE_BOT_ICERIK_ONU.'<br>'.$HE_ITEM_CONTENT;}
                        if ($HE_BOT_ICERIK_SONU!=""){$HE_ITEM_CONTENT = $HE_ITEM_CONTENT.'<br>'.$HE_BOT_ICERIK_SONU;}
                        if ($HE_BOT_CONTENT_AFTER_AGENCY_NAME=="1"){$HE_ITEM_CONTENT = $HE_ITEM_CONTENT."<p>Kaynak: ".$HE_ITEM_AGANCY_NAME."</p>";}


                        $HE_IS_TITLE = get_page_by_title($HE_ITEM_TITLE, OBJECT,$HE_BOT_POST_TYPE);
                        if ( @$HE_IS_TITLE->ID > 0 ) {
                            $HE_POST_ID = $HE_IS_TITLE->ID ;
                            $HE_ITEM_ADD="1";
                        }

                        //POST ADD
                        if ( $HE_ITEM_ADD=="1" && $HE_POST_ID > 0 ) {
                            $HE_CronContentB .= "<b>(*)</b> <a target='_blank' href='".get_admin_url()."post.php?action=edit&post=".$HE_POST_ID."'>".$HE_ITEM_TITLE."</a> --> ";
                            $he_post_url = HE_SET_URL . "/$HE_SITE_ID/$he_bot_heid/$HE_ITEM_ID/0/3/$HE_POST_ID/?rURL=" . get_permalink($HE_POST_ID) ;
                            //$HE_CronContentB .=  $he_post_url  ;
                            $HE_CronContentB .= he_curl($he_post_url) . "<br>";
                        }

                        if ($HE_ITEM_ADD!="1"){

                            $HE_POST = array(
                                'post_category' => array($HE_CAT_ID),
                                'post_title' => $HE_ITEM_TITLE,
                                'post_status' => $HE_BOT_POST_STATUS,
                                'post_type' => $HE_BOT_POST_TYPE,
                                'post_excerpt' => $HE_ITEM_DESC,
                                'post_content' => $HE_ITEM_CONTENT,
                                'post_author' => $HE_BOT_POST_AUTHOR,
                                'post_date' => $HE_ITEM_PUB_DATE,
                            );

                            $HE_POST_ID = wp_insert_post($HE_POST);

                            if ($HE_POST_ID > 0 ) {
                                wp_set_post_tags( $HE_POST_ID, $HE_ITEM_TAGS, true );
                                $HE_ITEM_THUMB_ID = he_addImages($HE_POST_ID,$HE_ITEM_PREVIEW_IMG,$HE_POST_ID,uniqid());
                                add_post_meta($HE_POST_ID, '_thumbnail_id', $HE_ITEM_THUMB_ID, true);
                                if ( $HE_BOT_RESIM_OZEL_ALAN!="" ){
                                    add_post_meta($HE_POST_ID, $HE_BOT_RESIM_OZEL_ALAN ,get_the_post_thumbnail( $HE_POST_ID, 'full') );
                                }

                                // Diğer Dosyalar Ekleniyor
                                $HE_FILES = False ;
                                foreach( $HE_ITEM_FILES as $HE_ITEM_FILE ){
                                    $HE_ITEM_FILE_url = $HE_ITEM_FILE["url"];
                                    $HE_ITEM_FILE_type = $HE_ITEM_FILE->getAttribute("type");
                                    $HE_ITEM_FILE_desc = urldecode($HE_ITEM_FILE->nodeValue);
                                    $HE_attacmentID = he_insert_attachment($HE_ITEM_FILE_url,$HE_POST_ID,$HE_ITEM_FILE_desc,$HE_ITEM_FILE_type);
                                    if ($HE_attacmentID>0){
                                        $HE_POST['ID'] = $HE_POST_ID ;
                                        $HE_POST['post_content'] = $HE_ITEM_CONTENT . "[gallery]" ;
                                        wp_update_post( $HE_POST );
                                    }

                                }

                                add_post_meta( $HE_POST_ID, 'HE_INFO','{"HE_ITEM_ID":'.$HE_ITEM_ID.', "HE_ITEM_CAT_ID":'.$HE_ITEM_CAT_ID.', "HE_ITEM_CAT_NAME":"'.$HE_ITEM_CAT_NAME.'", "HE_ITEM_PUB_DATE":"'.$HE_ITEM_PUB_DATE.'", "HE_ITEM_GUID":"'.$HE_ITEM_GUID.'"}');
                                //add_post_meta( $HE_POST_ID,'HE_INFO',json_encode($HE_XML_ITEM));
                                $he_bot_settings['son_icerik'] = date("Y-m-d H:i", HE_TIMESTAMP ) ;
                                $he_bot_settings['son_icerik_zamani'] = HE_TIMESTAMP;
                                $he_bot_settings['son_icerik_str'] = "<a target='_blank' href='".get_admin_url()."post.php?action=edit&post=".$HE_POST_ID."'>".$HE_ITEM_TITLE."</a>" ;
                                update_option( $he_bot_id.'SETTINGS', $he_bot_settings );

                                $HE_CronContentB .= "<b>(+)</b> <a target='_blank' href='".get_admin_url()."post.php?action=edit&post=".$HE_POST_ID."'>".$HE_ITEM_TITLE."</a> --> ";
                                $he_post_url = HE_SET_URL . "/$HE_SITE_ID/$he_bot_heid/$HE_ITEM_ID/0/1/$HE_POST_ID/?rURL=" . get_permalink($HE_POST_ID) ;
                                //$HE_CronContentB .=  $he_post_url  ;
                                $HE_CronContentB .= he_curl($he_post_url) . "<br>";
                            } else {
                                $HE_CronContentB .= "<b>(?)</b> " . $HE_ITEM_TITLE. " -> " . __("HATA OLUŞTU","habereditoru") . "<br>";
                            }

                            ob_flush();flush();

                        }
                        ob_flush();flush();
                    }
                } else {

                    $HE_CronContentB .= "<b>(!)</b> $HE_XML<br>" ;
                    ob_flush();flush();
                }

                $he_bot_settings['son_calisma'] = date("Y-m-d H:i", HE_TIMESTAMP  ) ;
                update_option( $he_bot_id.'SETTINGS', $he_bot_settings );
            } else {
                $HE_CronContentB .= "<b>(!)</b> " . __("BOT Pasif Durumda...","habereditoru") ."<br>";
            }

        }

    }
    update_option('HE_LAST_CHECK', HE_TIMESTAMP );

    update_option( 'HE_LAST_CRON', substr( $HE_CronContentB.$HE_CronContentA, 0, 10000 )   );
    echo $HE_CronContentB;


}

function he_insert_attachment($post_file,$post_id,$IcerikBaslik='',$custom_name='file') {
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

    if (!function_exists( 'wp_generate_attachment_metadata' ))
        require ( ABSPATH . 'wp-admin/includes/image.php' );

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


?>