<?php	

if (isset($_GET['robotID'])){$HE_ROBOT=$_GET['robotID'];}else{$HE_ROBOT="";}

if ($HE_ROBOT!=""){
	include_once "bot_ayar.php";
}
else
{
	
	$HE_MAX_ROBOT = get_option('HE_MAX_ROBOT') ;
	if ($HE_MAX_ROBOT == "" ){$HE_MAX_ROBOT = 3;}
	if ( HE_DEBUG ) {echo "MAX ROBOT -> ". $HE_MAX_ROBOT . "<br>";}
	
	$HE_BOT_CRON_URL2 = HE_PLUGIN_DIR_URL . 'cron.php?SiteKey='.HE_API_KEY ;
	
	echo '
	<h2>'.__("Bot Yönetimi","HaberEditoru").'</h2>
	<div>
			<div class="ajaxMsg"></div>
			<table class="wp-list-table widefat striped plugins">
				<thead>
					<tr>
						<th width="20%">'.__("Bot","HaberEditoru").'</th>
						<th width="80%">'.__("İçerik Filtresi","HaberEditoru").'</th>
					</tr>
				</thead>
				<tbody id="the-list">';

				for ($HE_COUNTER=1;$HE_COUNTER<=$HE_MAX_ROBOT;$HE_COUNTER++){
					
					$HE_BOT_ID			= "HE_BOT_".$HE_COUNTER."_";
					$HE_BOT_SETTINGS 	= get_option( $HE_BOT_ID.'SETTINGS') ;
					$HE_BOT_HEID 		= $HE_BOT_SETTINGS['HEID'];
		
					$HE_BOT_ADI = $HE_BOT_SETTINGS['adi'] ;
					$HE_BOT_AJANS_ADLARI = $HE_BOT_SETTINGS['ajanslar_str'];
					$HE_BOT_KATEGORI_ADLARI = $HE_BOT_SETTINGS['kategoriler_str'];
					$HE_BOT_ETIKETLER = $HE_BOT_SETTINGS['etiketler'];
					$HE_BOT_ETIKETLER_NEGATIF = $HE_BOT_SETTINGS['negatif_etiketler'];
					$HE_BOT_ICERIK_DILI = $HE_BOT_SETTINGS['icerik_dili'];
					$HE_BOT_STATUS = $HE_BOT_SETTINGS['aktif'];
					$HE_BOT_HEID = $HE_BOT_SETTINGS['HEID'];					
					$HE_BOT_TIME_STR = $HE_BOT_SETTINGS['son_icerik'];					
					$HE_BOT_TIME_LAST = $HE_BOT_SETTINGS['son_calisma'];					
					$HE_BOT_LAST_CONTENT = $HE_BOT_SETTINGS['son_icerik_str'];					
					$HE_BOT_TIME = intval($HE_BOT_SETTINGS['son_icerik_zamani']);
					$HE_NOW = time();							
					$HE_BOT_TIMER = intval(abs($HE_NOW - $HE_BOT_TIME) / 60 / 60) ;
					$HE_BOT_UYARI = "";
					$HE_BOT_UYARI_CSS = "";
					$HE_BOT_MESSAGE = "";
					$HE_BOT_CRON_URL = HE_PLUGIN_DIR_URL . 'cron.php?b='.$HE_COUNTER.'&SiteKey='.HE_API_KEY ;
					
					
					if (empty($HE_BOT_AJANS_ADLARI)||empty($HE_BOT_KATEGORI_ADLARI)){
						$HE_BOT_UYARI = "1";
						$HE_BOT_UYARI_CSS = "update";
						$HE_BOT_MESSAGE = "(!) " . __("Lütfen Robot ayarlarınızı yapınız....","HaberEditoru");
						$HE_BOT_STRVAL = "";
					}else{
						$HE_BOT_STRVAL="1";
					}
					
					if ($HE_BOT_STATUS=="1"){
						$HE_BOT_CSS="active";
						$HE_BOT_ACTION_LABEL="<span class=\"dashicons dashicons-controls-pause\"></span>" . __("Durdur","HaberEditoru");
					}else{
						$HE_BOT_CSS='inactive';
						$HE_BOT_ACTION_LABEL="<span class=\"dashicons dashicons-controls-play\"></span>" . __("Başlat","HaberEditoru");
					}

					if ($HE_BOT_TIME_STR==""||$HE_BOT_TIMER > 24){
						$HE_BOT_UYARI = "1";
						$HE_BOT_UYARI_CSS = "";
						$HE_BOT_MESSAGE = "(!) " . __("Son 24 saat içerisinde hiç haber çekilmemiş. Bot ayarlarınız doğru mu?","HaberEditoru");
					} else {
						$HE_BOT_TIME_STR = $HE_BOT_TIME_STR . " -> " . $HE_BOT_LAST_CONTENT ;
					}
					
					if ($HE_BOT_HEID=="") {
						$HE_BOT_UYARI = "1";
						$HE_BOT_UYARI_CSS = "";
						$HE_BOT_MESSAGE = "(!) " . __("Bot haber editörüne kayıt edilmemiş! Ayarlara girip KAYDET düğmesine tıklayınız...","HaberEditoru");
					}

					echo '<tr title="Haber Editoru ID : '.$HE_BOT_HEID.'" id="robot'.$HE_COUNTER.'" class="'.$HE_BOT_CSS.' '.$HE_BOT_UYARI_CSS.'">							
						<th class="check-column plugin-title column-primary">
							<big style="padding:8px">'.$HE_BOT_ADI.'</big>
							<div style="padding:15px 5px" class="row-actions visible">
								<span class="edit"><a id="btn_durum_'.$HE_COUNTER.'" aktif="'.$HE_BOT_STATUS.'" strval="'.$HE_BOT_STRVAL.'" href="javascript:" class="edit" >'.$HE_BOT_ACTION_LABEL.'</a> </span>
								<br><span class="edit"><a href="?page=HaberEditoru&t=bot&robotID='.$HE_COUNTER.'" class="edit"><span class="dashicons dashicons-admin-generic"></span> '.__("Ayarlar","HaberEditoru").'</a></span>
								<br><span class="eidt"><a target="_blank" href="'.$HE_BOT_CRON_URL.'" class="edit"><span class="dashicons dashicons-plus-alt"></span> '.__("Çalıştır","HaberEditoru").'</a></span>
							</div>							
						</th>
						<td class="column-description desc">
							<div class="plugin-description"><p><b>'.__("Kaynaklar","HaberEditoru").' :</b> '.$HE_BOT_AJANS_ADLARI.'<br>
							<b>'.__("Kategoriler","HaberEditoru").':</b> '.$HE_BOT_KATEGORI_ADLARI.'<br>';
							if (!empty($HE_BOT_ETIKETLER)) {echo '<b>'.__("Etiketler","HaberEditoru").':</b> '.$HE_BOT_ETIKETLER.'<br>';} 
							if (!empty($HE_BOT_ETIKETLER_NEGATIF)) {echo '<b>'.__("Negatif Etiketler","HaberEditoru").':</b> '.$HE_BOT_ETIKETLER_NEGATIF.'<br>';} 
							if (!empty($HE_BOT_ICERIK_DILI)) {echo '<b>'.__("İçerik Dili","HaberEditoru").':</b> '.$HE_BOT_ICERIK_DILI;} 
							echo '</p></div>
							<p class="notice notice-info">'.__("Son Çalışma","HaberEditoru").': <b>'.$HE_BOT_TIME_LAST.'</b> '.__("Son İçerik","HaberEditoru").':<b> '.$HE_BOT_TIME_STR.'<b></p>';
							if ($HE_BOT_UYARI){
								echo '<br><p class="notice notice-warning">'.$HE_BOT_MESSAGE.'</p>';
							}
						echo '</td>
					</tr>';
					
				}
				echo '</tbody>
				
			</table>
			<div style="text-align:right;padding:10px"><b>Wordpress Cron URL : </b> <a target="_blank" href="'.HE_DOMAIN.'/wp-cron.php">'.HE_DOMAIN.'/wp-cron.php</a> <br><b>PHP Cron URL (Log Tutmaz) : </b> <a target="_blank" href="'.$HE_BOT_CRON_URL2.'">'.$HE_BOT_CRON_URL2.'</a></div>
	</div>';
}
?>
<style>
p.notice {padding:5px;display:inline-block}
</style>