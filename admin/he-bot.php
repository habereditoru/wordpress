<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists('he_bot') ) :
	class he_bot
	{

		public static function output()
		{
			$he_robot = (isset($_GET['robotID'])) ? (intval($_GET['robotID'])) : '';

			if ($he_robot!=""){
				$newbot = new he_bot();
				$newbot->bot_ayar($he_robot);
			}
			else
			{
				$he_max_robot = get_option('HE_MAX_ROBOT') ;
				if ($he_max_robot == "" ){$he_max_robot = 3;}
				if ( HE_DEBUG ) {echo "MAX ROBOT -> ". $he_max_robot . "<br>";}

				//$he_bot_cron_url2 = HE_PLUGIN_DIR_URL . 'cron.php?SiteKey='.HE_API_KEY ; old
				$he_bot_cron_url2 = get_site_url(). '/wp-cron.php?SiteKey='.HE_API_KEY ;

				echo '
				<h2>'.__("Bot Yönetimi","habereditoru").'</h2>
				<div>
						<div class="ajaxMsg"></div>
						<table class="wp-list-table widefat striped plugins">
							<thead>
								<tr>
									<th width="20%">'.__("Bot","habereditoru").'</th>
									<th width="80%">'.__("İçerik Filtresi","habereditoru").'</th>
								</tr>
							</thead>
							<tbody id="the-list">';

				for ($he_counter=1;$he_counter<=$he_max_robot;$he_counter++){

					$he_bot_id			= "HE_BOT_".$he_counter."_";
					$he_bot_settings 	= get_option( $he_bot_id.'SETTINGS') ;
					$he_bot_heid 		= isset($he_bot_settings['HEID']) ? $he_bot_settings['HEID'] : 0;

					$he_bot_adi = $he_bot_settings['adi'] ;
					$he_bot_ajans_adlari = $he_bot_settings['ajanslar_str'];
					$he_bot_kategori_adlari = $he_bot_settings['kategoriler_str'];
					$he_bot_etiketler = $he_bot_settings['etiketler'];
					$he_bot_etiketler_negatif = $he_bot_settings['negatif_etiketler'];
					$he_bot_icerik_dili = $he_bot_settings['icerik_dili'];
					$he_bot_status = $he_bot_settings['aktif'];
					$he_bot_time_str = isset($he_bot_settings['son_icerik']) ? $he_bot_settings['son_icerik'] : '';
					$he_bot_time_last =isset($he_bot_settings['son_calisma']) ? $he_bot_settings['son_calisma'] : '';
					$he_bot_last_content = isset($he_bot_settings['son_icerik_str']) ? $he_bot_settings['son_icerik_str'] : '';
					$he_bot_time = intval(isset($he_bot_settings['son_icerik_zamani']) ? $he_bot_settings['son_icerik_zamani'] : 0);
					$he_now = current_time( 'timestamp' );
					$he_bot_timer = intval(abs($he_now - $he_bot_time) / 60 / 60) ;
					$he_bot_uyari = "";
					$he_bot_uyari_css = "";
					$he_bot_message = "";
//					$he_bot_cron_url = HE_PLUGIN_DIR_URL . 'cron.php?b='.$he_counter.'&SiteKey='.HE_API_KEY ;
					$he_bot_cron_url = get_site_url() . '/wp-cron.php?b='.$he_counter.'&SiteKey='.HE_API_KEY ;


					if (empty($he_bot_ajans_adlari)||empty($he_bot_kategori_adlari)){
						$he_bot_uyari = "1";
						$he_bot_uyari_css = "update";
						$he_bot_message = "(!) " . __("Lütfen Robot ayarlarınızı yapınız....","habereditoru");
						$he_bot_strval = "";
					}else{
						$he_bot_strval="1";
					}

					if ($he_bot_status=="1"){
						$he_bot_css="active";
						$he_bot_action_label="<span class=\"dashicons dashicons-controls-pause\"></span>" . __("Durdur","habereditoru");
					}else{
						$he_bot_css='inactive';
						$he_bot_action_label="<span class=\"dashicons dashicons-controls-play\"></span>" . __("Başlat","habereditoru");
					}

					if ($he_bot_time_str==""||$he_bot_timer > 24){
						$he_bot_uyari = "1";
						$he_bot_uyari_css = "";
						$he_bot_message = "(!) " . __("Son 24 saat içerisinde hiç haber çekilmemiş. Bot ayarlarınız doğru mu?","habereditoru");
					} else {
						$he_bot_time_str = $he_bot_time_str . " -> " . $he_bot_last_content ;
					}

					if ($he_bot_heid=="") {
						$he_bot_uyari = "1";
						$he_bot_uyari_css = "";
						$he_bot_message = "(!) " . __("Bot haber editörüne kayıt edilmemiş! Ayarlara girip KAYDET düğmesine tıklayınız...","habereditoru");
					}

					echo '<tr title="Haber Editoru ID : '.$he_bot_heid.'" id="robot'.$he_counter.'" class="'.$he_bot_css.' '.$he_bot_uyari_css.'">							
									<th class="check-column plugin-title column-primary">
										<big style="padding:8px">'.$he_bot_adi.'</big>
										<div style="padding:15px 5px" class="row-actions visible">
											<span class="edit"><a id="btn_durum_'.$he_counter.'" aktif="'.$he_bot_status.'" strval="'.$he_bot_strval.'" href="javascript:" class="edit" >'.$he_bot_action_label.'</a> </span>
											<br><span class="edit"><a href="?page=habereditoru&t=bot&robotID='.$he_counter.'" class="edit"><span class="dashicons dashicons-admin-generic"></span> '.__("Ayarlar","habereditoru").'</a></span>
											<br><span class="eidt"><a target="_blank" href="'.$he_bot_cron_url.'" class="edit"><span class="dashicons dashicons-plus-alt"></span> '.__("Çalıştır","habereditoru").'</a></span>
										</div>							
									</th>
									<td class="column-description desc">
										<div class="plugin-description"><p><b>'.__("Kaynaklar","habereditoru").' :</b> '.$he_bot_ajans_adlari.'<br>
										<b>'.__("Kategoriler","habereditoru").':</b> '.$he_bot_kategori_adlari.'<br>';
					if (!empty($he_bot_etiketler)) {echo '<b>'.__("Etiketler","habereditoru").':</b> '.$he_bot_etiketler.'<br>';}
					if (!empty($he_bot_etiketler_negatif)) {echo '<b>'.__("Negatif Etiketler","habereditoru").':</b> '.$he_bot_etiketler_negatif.'<br>';}
					if (!empty($he_bot_icerik_dili)) {echo '<b>'.__("İçerik Dili","habereditoru").':</b> '.$he_bot_icerik_dili;}
					echo '</p></div>
										<p class="notice notice-info">'.__("Son Çalışma","habereditoru").': <b>'.$he_bot_time_last.'</b> '.__("Son İçerik","habereditoru").':<b> '.$he_bot_time_str.'<b></p>';
					if ($he_bot_uyari){
						echo '<br><p class="notice notice-warning">'.$he_bot_message.'</p>';
					}
					echo '</td>
								</tr>';

				}
				echo '</tbody>
							
						</table>
						<div style="text-align:right;padding:10px"><b>Wordpress Cron URL : </b> <a target="_blank" href="'.HE_DOMAIN.'/wp-cron.php">'.HE_DOMAIN.'/wp-cron.php</a> <br><b>PHP Cron URL : </b> <a target="_blank" href="'.$he_bot_cron_url2.'">'.$he_bot_cron_url2.'</a></div>
				</div>';
			}
			?>
			<style>
				p.notice {padding:5px;display:inline-block}
			</style>

			<?php

		}

		private function bot_ayar($he_robot)
		{
			$he_bot_id			= "HE_BOT_".$he_robot."_";
			$he_bot_settings 	= get_option( $he_bot_id.'SETTINGS') ;

			$he_bot_adi = $he_bot_settings['adi'];
			$he_post_types=get_post_types('','names');
			$HE_CATS = get_option('HE_OPT_KAT_ESLESTIRME');
			$he_bot_ajanslar_get = get_option('HE_OPT_KAYNAKLAR');
			$he_bot_kategoriler_GET = get_option('HE_OPT_KATEGORILER');

			$he_bot_ajanslar_arr = $he_bot_settings['kaynaklar'];
			$he_bot_kategoriler_ARR = $he_bot_settings['kategoriler'];
			$he_bot_icerik_dili = $he_bot_settings['icerik_dili'];
			$he_bot_icerik_tipi = $he_bot_settings['icerik_tipi'];
			$he_bot_etiketler = $he_bot_settings['etiketler'];
			$he_bot_etiketler_negatif = $he_bot_settings['negatif_etiketler'];
			$HE_BOT_ICERIK_ONU = $he_bot_settings['icerigin_onune_ekle'];
			$HE_BOT_ICERIK_SONU = $he_bot_settings['icerigin_sonuna_ekle'];
			$he_bot_content_after_agency_name = isset($he_bot_settings['kaynak_adi']) ? $he_bot_settings['kaynak_adi'] : '';
			$he_bot_post_author = $he_bot_settings['site_editor'];
			$HE_BOT_POST_STATUS = $he_bot_settings['post_durumu'];
			$HE_BOT_POST_TYPE = $he_bot_settings['post_tipi'];
			$HE_BOT_POST_DATE = $he_bot_settings['post_date'];
			$HE_BOT_SPINNER = $he_bot_settings['post_spinner'];
			$HE_BOT_RESIM_OZEL_ALAN = $he_bot_settings['resim_ozel_alan_adi'];
			$he_item_if_not_image = $he_bot_settings['resimsiz_haber'];
			$he_bot_status = $he_bot_settings['aktif'];
			$he_a_kontrol=",";

			$he_xml 		= he_jsontoxml(he_curl(HE_API_URL."/get/agencies"));
			$he_xml_cats 	= he_jsontoxml(he_curl(HE_API_URL."/get/agencies_categories"));

			$he_r_ajanslar_arr = array();
			//$he_bot_ajanslar_arr = array();
			//$he_bot_kategoriler_ARR = array();


			foreach($he_xml->Agencies as $he_ajans_isim => $he_ajans_bilgi){
				if ( in_array($he_ajans_bilgi[0], $he_bot_ajanslar_get)){
					array_push($he_r_ajanslar_arr,array($he_ajans_bilgi[0],$he_ajans_bilgi[1],$he_ajans_bilgi[2],$he_ajans_bilgi[3],$he_ajans_bilgi[4],$he_ajans_bilgi[5],$he_ajans_bilgi[6],$he_ajans_bilgi[7]));
				}
			}
			?>


			<style type="text/css">
				.form-table select {min-width:250px;}
				.form-field {padding:0 0 0 0 !important;}
				.form-field select {width:100%}
				#post-body-content, .edit-form-section {
					margin-bottom:0;
				}
				.pull-right {float:right}
				.he_kaynaklar th {  vertical-align:top ; padding-top:17px;}
				.he_kaynaklar h2 { padding:0 !important; margin-top:10px !important;}
				.he_kaynaklar label { padding:0;display:inline-block;min-width:25%;}
				.he_kaynaklar label span { padding:0 8px;}
				.he_kaynaklar input[type=checkbox]:checked + span  {
					background-color: #ebeceb;
					color: #167616;
					font-weight: bold;
				}
				
			</style>
			<h2><span class="dashicons dashicons-edit"></span> <?php echo "". $he_bot_adi . ""; ?> <a class="page-title-action pull-right" href="javascript:history.back();"><?php echo __("Geri Dön","habereditoru") ?></a></h2>

			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<form id="he-form" name="he-form" onsubmit="return false">
						<input type="hidden" name="tip" value="ba">
						<input type="hidden" name="bot" value="<?php echo $he_robot?>">
						<div id="post-body-content" style="position: relative;">
							<div class="postbox">
								<h2 class="hndle"><span class="dashicons dashicons-filter"></span> <?php echo  __("İçerik Filtreleme","habereditoru") ?></h2>
								<div class="inside">
									<table style="border:0" class="widefat striped form-table">

										<tr id="ajanslar_ids" style="display:;">
											<th scope="row" width="20%"><?php echo  __("Kaynaklar","habereditoru") ?></th>
											<td width="80%" class="he_kaynaklar">
												<?php
												if (is_array($he_r_ajanslar_arr)){
													foreach($he_r_ajanslar_arr as $he_ajans){
														$he_selected="";
														if (is_array($he_bot_ajanslar_arr)) {
															if ( in_array($he_ajans[0], $he_bot_ajanslar_arr ) ){$he_selected="checked";$he_a_kontrol.=$he_ajans[1].",";}
														}
														$he_ajans_ID =  str_replace(".", "", $he_ajans[1]);
														echo '<label for="'.$he_ajans_ID.'"><input name="kaynaklar[]" type="checkbox" id="'.$he_ajans_ID.'" data-name="'.$he_ajans[1].'" value="'.$he_ajans[0].'" '.$he_selected.'><span>'.$he_ajans[1].'</span></label>';
													}}?>
											</td>
										</tr>
										<tr id="kategoriler_ids" style="display:;">
											<th scope="row"><?php echo  __("Kategoriler","habereditoru") ?></th>
											<td class="he_kaynaklar">
												<?php foreach($he_xml_cats->Agencies_Categories as $he_ajans_isim => $HE_CATS_ARR){
													if (strpos($he_a_kontrol, ",".$he_ajans_isim.",") === false){$HE_DISPLAY=" style='display:none' ";}else{$HE_DISPLAY="";}
													$he_ajans_isim_ID = str_replace(".", "", $he_ajans_isim);
													echo '<fieldset id="fs_'.$he_ajans_isim_ID.'" '.$HE_DISPLAY.'>
									<h2>'.$he_ajans_isim.'</h2>';
													foreach($HE_CATS_ARR as $HE_CAT){
														if (@$HE_CATS[$HE_CAT[1]]){
															if (is_array( $he_bot_kategoriler_ARR )) {
																$he_selected = !in_array($HE_CAT[1], $he_bot_kategoriler_ARR ) ? "" : "checked";
															}
															echo '<label for="k_'.$he_ajans_isim_ID.'_'.$HE_CAT[1].'">
										<input name="kategoriler[]" type="checkbox" id="k_'.$he_ajans_isim_ID.'_'.$HE_CAT[1].'" data-name="'.$HE_CAT[0].'" value="'.$HE_CAT[1].'" '.$he_selected.'>
											<span>'.$HE_CAT[0].'</span>
										</label>';
														}
													}
													echo '</fieldset>';
												}?>
											</td>
										</tr>
										<tr>
											<th scope="row"><?php echo  __("İçerik Dili","habereditoru") ?></th>
											<td>
												<select name="icerik_dili" id="icerik_dili">
													<option value="tr" <?php if ($he_bot_icerik_dili=="tr"){echo "selected";}?>><?php echo  __("Türkçe","habereditoru") ?></option>
													<option value="en" <?php if ($he_bot_icerik_dili=="en"){echo "selected";}?>><?php echo  __("İngilizce","habereditoru") ?></option>
												</select>
											</td>
										</tr>
										<tr>
											<th scope="row"><?php echo  __("İçerik Tipi","habereditoru") ?></th>
											<td>
												<select name="icerik_tipi" id="icerik_tipi">
													<option value="0" <?php if ($he_bot_icerik_tipi=="0"){echo "selected";}?>><?php echo  __("Tümü","habereditoru") ?></option>
													<option value="1" <?php if ($he_bot_icerik_tipi=="1"){echo "selected";}?>><?php echo  __("Sadece Haber","habereditoru") ?></option>
													<option value="2" <?php if ($he_bot_icerik_tipi=="2"){echo "selected";}?>><?php echo  __("Sadece Video","habereditoru") ?></option>
													<option value="3" <?php if ($he_bot_icerik_tipi=="3"){echo "selected";}?>><?php echo  __("Sadece Resim Galerisi","habereditoru") ?></option>
												</select>
											</td>
										</tr>
										<tr>
											<th scope="row"><?php echo  __("Resimsiz İçerik","habereditoru") ?></th>
											<td>
												<select name="resimsiz_haber" id="resimsiz_haber">
													<option value="1" <?php if ($he_item_if_not_image=="1"){echo "selected";}?>><?php echo  __("Resimsiz İçerikleri Ekle","habereditoru") ?></option>
													<option value="0" <?php if ($he_item_if_not_image=="0"){echo "selected";}?>><?php echo  __("Resimsiz İçerikleri Ekleme","habereditoru") ?></option>
												</select>
											</td>
										</tr>

										<tr>
											<th scope="row"><?php echo  __("Etiketler","habereditoru") ?></th>
											<td><input placeholder="Gaziantep, Internet" name="etiketler" id="etiketler" type="text" value="<?php if($he_bot_etiketler!=""){echo $he_bot_etiketler;}?>" class="regular-text">
												<br><small class="description"><?php echo  __("İçeriğin içerisinde mutlaka olmasını istediğiniz kelimer. (Örn. AKP, Gaziantep, doktor)","habereditoru") ?></small>
											</td>
										</tr>
										<tr>
											<th scope="row"><?php echo  __("Negatif Etiketler","habereditoru") ?></th>
											<td><input placeholder="Magazin, BDP" name="negatif_etiketler" id="negatif_etiketler" type="text" value="<?php if($he_bot_etiketler_negatif!=""){echo $he_bot_etiketler_negatif;}?>" class="regular-text">
												<br><small class="description"><?php echo  __("İçeriğin içerisinde bu kelimeler geçiyorsa ekleme. (Örn. Demirtaş, magazin, sibel can)","habereditoru") ?></small>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>

						<div id="postbox-container-1" class="postbox-container">
							<div id="side-sortables" class="meta-box-sortables ui-sortable">
								<div class="postbox">
									<h2 class="hndle"><span class="dashicons dashicons-admin-generic"></span> <?php echo  __("Seçenekler","habereditoru") ?></h2>
									<div class="inside">
										<div class="form-wrap">
											<div class="form-field">
												<label for="aktif"><input id="aktif" name="aktif" type="checkbox" value="1" <?php if ($he_bot_status=="1"){echo "checked";}?>> <?php echo  __("Robot Aktif","habereditoru") ?> </label>
											</div>
											<div class="form-field">
												<label for="post_durumu"><?php echo  __("İçerikleri","habereditoru") ?></label>
												<select name="post_durumu" id="post_durumu">
													<option value="publish" <?php if ($HE_BOT_POST_STATUS=="publish"){echo "selected";}?>><?php echo  __("Hemen Yayınla","habereditoru") ?></option>
													<option value="draft" <?php if ($HE_BOT_POST_STATUS=="draft"){echo "selected";}?>><?php echo  __("Onayımı Bekle","habereditoru") ?></option>
												</select>
											</div>
											<div class="form-field">
												<label for="site_editor"><?php echo  __("Editör","habereditoru") ?></label>
												<?php wp_dropdown_users(array('name' => 'site_editor' , 'selected' => $he_bot_post_author ,  'who'   => 'authors')); ?>
											</div>
											<div class="form-field">
												<label for="post_tipi"><?php echo  __("Post Tipi","habereditoru") ?></label>
												<select  name="post_tipi" id="post_tipi">
													<?
													foreach ($he_post_types as $post_type) {
														if ( $HE_BOT_POST_TYPE == $post_type ) { $he_selected="selected"; }else{$he_selected="";}
														echo '<option '.$he_selected.' value="'.$post_type.'">'.$post_type.'</option>';
													}
													?>
												</select>
											</div>
											<div class="form-field">
												<label for="post_durumu"><?php echo  __("İçerik Tarihi","habereditoru") ?></label>
												<select name="post_date" id="post_date">
													<option value="1" <?php if ($HE_BOT_POST_DATE=="1"){echo "selected";}?>><?php echo  __("İçerik Zamanını Kullan","habereditoru") ?></option>
													<option value="" <?php if ($HE_BOT_POST_DATE==""){echo "selected";}?>><?php echo  __("Eklenme Zamanını Kullan","habereditoru") ?></option>
												</select>
											</div>
											<div class="form-field">
												<label for="post_durumu"><a target="_blank" href="http://www.habereditoru.com/icerik-ozgunlestirme.html"><?php echo  __("İçerik Özgünleştirme (Spinner)","habereditoru") ?></a></label>
												<select name="post_spinner" id="post_spinner">
													<option value="" <?php if ($HE_BOT_SPINNER==""){echo "selected";}?>><?php echo  __("Orjinal Kalsın","habereditoru") ?></option>
													<option value="1" <?php if ($HE_BOT_SPINNER=="1"){echo "selected";}?>><?php echo  __("İçeriği Özgünleştir","habereditoru") ?></option>

												</select>
											</div>
											<div class="form-field">
												<label for="post_tipi"><?php echo  __("Resim Özel Alanı Adı","habereditoru") ?></label>

												<input style="width:100%" placeholder="<?php echo  __("resim_ozel_alan_adi","habereditoru") ?>" name="resim_ozel_alan_adi" id="resim_ozel_alan_adi" type="text" value="<?php if($HE_BOT_RESIM_OZEL_ALAN!=""){echo $HE_BOT_RESIM_OZEL_ALAN;}?>" class="regular-text">

											</div>
											<p class="submit"><input type="submit" name="submit" id="ba_submit" class="button button-primary" value="<?php echo  __("Değişiklikleri Kaydet","habereditoru") ?>"></p>
											<div class="ajaxMsg"></div>
										</div>
										<div class="clear"></div>
									</div>
								</div>
							</div>
						</div>
						<div id="postbox-container-2" class="postbox-container">
							<div class="postbox">
								<h2 class="hndle"><span class="dashicons dashicons-align-center"></span> <?php echo  __("İçerik Düzenleme","habereditoru") ?></h2>
								<div class="inside">
									<table  style="border:0" class="widefat striped">
										<tr>
											<th scope="row"><?php echo  __("İçeriğin Önüne Ekle","habereditoru") ?></th>
											<td><textarea placeholder="Örn:Reklam Kodunuz" style="width:100%" name="icerigin_onune_ekle" id="icerigin_onune_ekle" rows="3" cols="40"><?php if($HE_BOT_ICERIK_ONU!=""){echo $HE_BOT_ICERIK_ONU;}?></textarea></td>
										</tr>
										<tr>
											<th scope="row"><?php echo  __("İçeriğin Sonuna Ekle","habereditoru") ?></th>
											<td><textarea placeholder="Örn:Editör Adı" style="width:100%" name="icerigin_sonuna_ekle" id="icerigin_sonuna_ekle" rows="3" cols="40"><?php if($HE_BOT_ICERIK_SONU!=""){echo $HE_BOT_ICERIK_SONU;}?></textarea></td>
										</tr>
										<tr>
											<th scope="row"><?php echo  __("Haber Kaynağı","habereditoru") ?></th>
											<td>
												<label for="kaynak_adi">
													<input name="kaynak_adi" id="kaynak_adi" type="checkbox" value="1" <?php if ($he_bot_content_after_agency_name=="1"){echo "checked";}?>>
													<?php echo  __("İçeriğin altına haber kaynağının adını yaz","habereditoru") ?>
												</label>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="clear"></div>

			<?php
		}


	}
endif;
?>
