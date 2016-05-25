<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists('he_genel_ayarlar') ) :
class he_genel_ayarlar
{

	public static function output() { ?>
		
	<h2><?php echo __("İçerik Kaynakları","habereditoru")?> <a style="float:right" class="page-title-action" href="javascript:history.back();"><?php echo __("Geri Dön","habereditoru")?></a></h2>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content" style="position: relative;">
				<form id="he-form-ayarlar" name="he-form" onsubmit="return false">
					<input type="hidden" name="tip" value="ga">
					<?php

					$HE_SET_LANGS = get_option('HE_OPT_ICERIK_DILLERI');
					$he_set_content_type = get_option('HE_OPT_CONTENT_TYPE');
					if ($HE_SET_LANGS == ""){$HE_SET_LANGS = "tr";}
					if ($he_set_content_type == ""){$he_set_content_type = "0";}
					$HE_CRON_MINUTE = get_option('HE_OPT_CRON_MINUTE');

					$HE_OPT_KAYNAKLAR = get_option('HE_OPT_KAYNAKLAR');
					$HE_OPT_KATEGORILER = get_option('HE_OPT_KATEGORILER');

					$HE_XML_KAYNAKLAR = he_jsontoxml(he_curl(HE_API_URL."/get/agencies?selLanguage=".$HE_SET_LANGS."&selContentType=".$he_set_content_type));
					$HE_XML_KATEGORILER = he_jsontoxml(he_curl(HE_API_URL."/get/agencies_categories?selLanguage=".$HE_SET_LANGS."&selContentType=".$he_set_content_type));
					$HE_KAYNAKLAR_AJANS_ARR = array();
					$HE_KAYNAKLAR_WEBSITE_ARR = array();
					$HE_A_KONTROL=",";
					foreach($HE_XML_KAYNAKLAR->Agencies as $HE_AJANS_ISIM => $HE_AJANS_BILGI){
						if (strpos($HE_AJANS_BILGI[4], $HE_SET_LANGS) !== false){
							if ($HE_AJANS_BILGI[2] == "1"){
								array_push($HE_KAYNAKLAR_AJANS_ARR,array($HE_AJANS_BILGI[0],$HE_AJANS_BILGI[1],$HE_AJANS_BILGI[2],$HE_AJANS_BILGI[3],$HE_AJANS_BILGI[4],$HE_AJANS_BILGI[5],$HE_AJANS_BILGI[6],$HE_AJANS_BILGI[7],$HE_AJANS_BILGI[8]));
							}else{
								array_push($HE_KAYNAKLAR_WEBSITE_ARR,array($HE_AJANS_BILGI[0],$HE_AJANS_BILGI[1],$HE_AJANS_BILGI[2],$HE_AJANS_BILGI[3],$HE_AJANS_BILGI[4],$HE_AJANS_BILGI[5],$HE_AJANS_BILGI[6],$HE_AJANS_BILGI[7],$HE_AJANS_BILGI[8]));
							}
						}
					}


				echo '
				<div class="postbox">
				<h2>'.__("İçerik Kaynakları ve Kategorileri","habereditoru").'</h2>
				<table id="IcerikKaynaklari" class="widefat striped he_kaynaklar">
					<tr>
						<th nowrap scope="row">'.__("İçerik Dili","habereditoru").'</th>
						<td width="100%" >
							<select name="icerik_dili" id="icerik_dili" onchange="dildegistir(\''.$HE_SET_LANGS.'\',this.value);">
								<option value="tr" ';if ($HE_SET_LANGS=="tr"){echo "selected";} echo '>'.__("Türkçe","habereditoru").'</option>
								<option value="en" ';if ($HE_SET_LANGS=="en"){echo "selected";} echo '>'.__("İngilizce","habereditoru").'</option>
							</select>
						</td>
					</tr>
					<tr>
						<th nowrap scope="row">'.__("İçerik Tipi","habereditoru").'</th>
						<td>
							<select name="icerik_tipi" id="icerik_tipi">
								<option value="0" ';if ($he_set_content_type=="0"){echo "selected";} echo '>'.__("Hepsi","habereditoru").'</option>
								<option value="1" ';if ($he_set_content_type=="1"){echo "selected";} echo '>'.__("Haber","habereditoru").'</option>
							</select>
						</td>
					</tr>
					<tr id="ajanslar_ids" style="display:;">
						<th nowrap scope="row">'. __("Kaynaklar","habereditoru").'</th>
						<td><h2>'. __("Ulusal Ajanslar","habereditoru").'</h2>';

					foreach($HE_KAYNAKLAR_AJANS_ARR as $HE_AJANS){
						$HE_SELECTED = "";
						if (is_array($HE_OPT_KAYNAKLAR)) {
							if ( !in_array($HE_AJANS[0], $HE_OPT_KAYNAKLAR ) ){$HE_SELECTED="";}else{$HE_SELECTED="checked";$HE_A_KONTROL.=$HE_AJANS[1].",";}
						}
						$HE_AJANS_ID =  str_replace(".", "", $HE_AJANS[1]);
						echo '<label for="'.$HE_AJANS_ID.'"><input name="kaynaklar[]" type="checkbox" id="'.$HE_AJANS_ID.'" value="'.$HE_AJANS[0].'" '.$HE_SELECTED.'><span>'.$HE_AJANS[1].'</span></label> ';
					}

					echo '<h2>'. __("Web Siteleri","habereditoru").'</h2>';
					foreach($HE_KAYNAKLAR_WEBSITE_ARR as $HE_AJANS2){
						$HE_SELECTED = "";
						if (is_array($HE_OPT_KAYNAKLAR)) {
							if ( !in_array($HE_AJANS2[0], $HE_OPT_KAYNAKLAR ) ){$HE_SELECTED="";}else{$HE_SELECTED="checked";$HE_A_KONTROL.=$HE_AJANS2[1].",";}
						}
						$HE_AJANS_ID =  str_replace(".", "", $HE_AJANS2[1]);
						echo '<label for="'.$HE_AJANS_ID.'"><input name="kaynaklar[]" type="checkbox" id="'.$HE_AJANS_ID.'" value="'.$HE_AJANS2[0].'" '.$HE_SELECTED.'><span>'.$HE_AJANS2[1].'</span></label> ';
					}
					echo '</td>
					</tr>
					<tr id="kategoriler_ids" style="display:;">
						<th scope="row">'.__("Kategoriler","habereditoru").'</th>
						<td>';
					foreach($HE_XML_KATEGORILER->Agencies_Categories as $HE_AJANS_ISIM => $HE_CATS_ARR){
						$HE_DISPLAY = (strpos($HE_A_KONTROL, ",".$HE_AJANS_ISIM.",") === false) ? " style=\"display:none\" " : "";
						$HE_AJANS_ISIM_ID = str_replace(".", "", $HE_AJANS_ISIM);
						echo '<div id="fs_'.$HE_AJANS_ISIM_ID.'" '.$HE_DISPLAY.'>
								<h2>'.$HE_AJANS_ISIM.'</h2>';
						foreach($HE_CATS_ARR as $HE_CAT){

							if (is_array( $HE_OPT_KATEGORILER )) {
								$HE_SELECTED = in_array($HE_CAT[1], $HE_OPT_KATEGORILER) ? "checked" : "";
							}
							echo '<label for="k_'.$HE_AJANS_ISIM_ID.'_'.$HE_CAT[1].'">
								<input name="kategoriler[]" type="checkbox" id="k_'.$HE_AJANS_ISIM_ID.'_'.$HE_CAT[1].'" value="'.$HE_CAT[1].'" '.$HE_SELECTED.'>
									<span>'.$HE_CAT[0].'</span>
								</label>';

						}
						echo '</div>';
					}
					echo '</td>
					</tr>
				</table>
			</div>';
					?>
			</div>
			<div id="postbox-container-1" class="postbox-container">
				<div id="side-sortables" class="meta-box-sortables ui-sortable">
					<div class="postbox">
						<h2 class="hndle"><span class="dashicons dashicons-admin-generic"></span> <?php echo __("Seçenekler","habereditoru")?></h2>
						<div class="inside">
							<div class="form-wrap">
								<div class="form-field">
									<label for="cron_minute"><?php echo __("Otomatik Haber Çekme","habereditoru")?></label><?php

									echo '
									 <select name="cron_minute" id="cron_minute"">
										<option value="-" ';if ($HE_CRON_MINUTE=="-"){echo "selected";} echo '>'.__("KAPALI","habereditoru").'</option>
										<option value="5m" ';if ($HE_CRON_MINUTE=="5m"){echo "selected";} echo '>'.__("5 Dakikada bir","habereditoru").'</option>
										<option value="10m" ';if ($HE_CRON_MINUTE=="10m"){echo "selected";} echo '>'.__("10 Dakikada bir","habereditoru").'</option>
										<option value="15m" ';if ($HE_CRON_MINUTE=="15m"){echo "selected";} echo '>'.__("15 Dakikada bir","habereditoru").'</option>
										<option value="30m" ';if ($HE_CRON_MINUTE=="30m"){echo "selected";} echo '>'.__("30 Dakikada bir","habereditoru").'</option>
										<option value="hourly" ';if ($HE_CRON_MINUTE=="hourly"){echo "selected";} echo '>'.__("Saatte bir").'</option>
										<option value="120m" ';if ($HE_CRON_MINUTE=="120m"){echo "selected";} echo '>'.__("2 Saatte bir","habereditoru").'</option>
										<option value="180m" ';if ($HE_CRON_MINUTE=="180m"){echo "selected";} echo '>'.__("3 Saatte bir","habereditoru").'</option>
										<option value="360m" ';if ($HE_CRON_MINUTE=="360m"){echo "selected";} echo '>'.__("6 Saatte bir","habereditoru").'</option>
										<option value="twicedaily" ';if ($HE_CRON_MINUTE=="twicedaily"){echo "selected";} echo '>'.__("Günde iki kere").'</option>
										<option value="daily" ';if ($HE_CRON_MINUTE=="daily"){echo "selected";} echo '>'.__("Günde bir").'</option>
									</select>';
									?>
								</div>
								<p class="submit"><input type="submit" name="submit" id="ga_submit" class="button button-primary" value="<?php echo __("Değişiklikleri Kaydet","habereditoru")?>"></p>
								<div class="ajaxMsg"></div>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
			</form>
		</div>
		<style>
			.he_kaynaklar th {  vertical-align:top ; padding-top:17px;}
			.he_kaynaklar h2 { padding:0 !important; margin-top:10px !important;}
			.he_kaynaklar label { padding:0;display:inline-block;min-width:19%;}
			.he_kaynaklar label span { padding:0 8px;}
			.he_kaynaklar input[type=checkbox]:checked + span  {
				background-color: #ebeceb;
				color: #167616;
				font-weight: bold;
			}
		</style>
	<?php
	}
}
endif;
?>