<?php 
	
	
	$HE_BOT_ID			= "HE_BOT_".$HE_ROBOT."_";
	$HE_BOT_SETTINGS 	= get_option( $HE_BOT_ID.'SETTINGS') ;

	$HE_BOT_ADI = $HE_BOT_SETTINGS['adi'];
	$HE_POST_TYPES=get_post_types('','names');
	$HE_CATS = get_option('HE_OPT_KAT_ESLESTIRME');
	$HE_BOT_AJANSLAR_GET = get_option('HE_OPT_KAYNAKLAR');
	$HE_BOT_KATEGORILER_GET = get_option('HE_OPT_KATEGORILER');	
	
	$HE_BOT_AJANSLAR_ARR = $HE_BOT_SETTINGS['kaynaklar'];
	$HE_BOT_KATEGORILER_ARR = $HE_BOT_SETTINGS['kategoriler'];
	$HE_BOT_ICERIK_DILI = $HE_BOT_SETTINGS['icerik_dili'];
	$HE_BOT_ICERIK_TIPI = $HE_BOT_SETTINGS['icerik_tipi'];
	$HE_BOT_ETIKETLER = $HE_BOT_SETTINGS['etiketler'];
	$HE_BOT_ETIKETLER_NEGATIF = $HE_BOT_SETTINGS['negatif_etiketler'];
	$HE_BOT_ICERIK_ONU = $HE_BOT_SETTINGS['icerigin_onune_ekle'];
	$HE_BOT_ICERIK_SONU = $HE_BOT_SETTINGS['icerigin_sonuna_ekle'];
	$HE_BOT_CONTENT_AFTER_AGENCY_NAME = $HE_BOT_SETTINGS['kaynak_adi'];
	$HE_BOT_POST_AUTHOR = $HE_BOT_SETTINGS['site_editor'];
	$HE_BOT_POST_STATUS = $HE_BOT_SETTINGS['post_durumu'];
	$HE_BOT_POST_TYPE = $HE_BOT_SETTINGS['post_tipi'];	
	$HE_BOT_POST_DATE = $HE_BOT_SETTINGS['post_date'];	
	$HE_BOT_SPINNER = $HE_BOT_SETTINGS['post_spinner'];	
	$HE_BOT_RESIM_OZEL_ALAN = $HE_BOT_SETTINGS['resim_ozel_alan_adi'];	
	$HE_ITEM_IF_NOT_IMAGE = $HE_BOT_SETTINGS['resimsiz_haber'];	
	$HE_BOT_STATUS = $HE_BOT_SETTINGS['aktif'];	
	$HE_A_KONTROL=",";

	$HE_XML 		= he_jsontoxml(he_curl(HE_API_URL."/get/agencies"));
	$HE_XML_CATS 	= he_jsontoxml(he_curl(HE_API_URL."/get/agencies_categories"));

	$HE_R_AJANSLAR_ARR = array();
	//$HE_BOT_AJANSLAR_ARR = array();
	//$HE_BOT_KATEGORILER_ARR = array();
	
	
	foreach($HE_XML->Agencies as $HE_AJANS_ISIM => $HE_AJANS_BILGI){
		if ( in_array($HE_AJANS_BILGI[0], $HE_BOT_AJANSLAR_GET)){
			array_push($HE_R_AJANSLAR_ARR,array($HE_AJANS_BILGI[0],$HE_AJANS_BILGI[1],$HE_AJANS_BILGI[2],$HE_AJANS_BILGI[3],$HE_AJANS_BILGI[4],$HE_AJANS_BILGI[5],$HE_AJANS_BILGI[6],$HE_AJANS_BILGI[7]));
		}
	}
	

?>
<h2><span class="dashicons dashicons-edit"></span> <?php echo "". $HE_BOT_ADI . ""; ?> <a class="page-title-action pull-right" href="javascript:history.back();"><?php echo __("Geri Dön","HaberEditoru") ?></a></h2>

<div id="poststuff">
	<div id="post-body" class="metabox-holder columns-2">
		<form id="he-form" name="he-form" onsubmit="return false">
		<input type="hidden" name="tip" value="ba">
		<input type="hidden" name="bot" value="<?php echo $HE_ROBOT?>">
		<div id="post-body-content" style="position: relative;">
			<div class="postbox">
				<h2 class="hndle"><span class="dashicons dashicons-filter"></span> <?php echo  __("İçerik Filtreleme","HaberEditoru") ?></h2>
				<div class="inside">
					<table style="border:0" class="widefat striped form-table">
						
						<tr id="ajanslar_ids" style="display:;">
							<th scope="row" width="20%"><?php echo  __("Kaynaklar","HaberEditoru") ?></th>
							<td width="80%" class="he_kaynaklar">
							<?php
							if (!empty($HE_R_AJANSLAR_ARR)){
							foreach($HE_R_AJANSLAR_ARR as $HE_AJANS){
								if ( !in_array($HE_AJANS[0], $HE_BOT_AJANSLAR_ARR ) ){$HE_SELECTED="";}else{$HE_SELECTED="checked";$HE_A_KONTROL.=$HE_AJANS[1].",";}
								$HE_AJANS_ID =  str_replace(".", "", $HE_AJANS[1]);
								echo '<label for="'.$HE_AJANS_ID.'"><input name="kaynaklar[]" type="checkbox" id="'.$HE_AJANS_ID.'" data-name="'.$HE_AJANS[1].'" value="'.$HE_AJANS[0].'" '.$HE_SELECTED.'><span>'.$HE_AJANS[1].'</span></label>';
							}}?>
							</td>
						</tr>
						<tr id="kategoriler_ids" style="display:;">
							<th scope="row"><?php echo  __("Kategoriler","HaberEditoru") ?></th>
							<td class="he_kaynaklar">
							<?php foreach($HE_XML_CATS->Agencies_Categories as $HE_AJANS_ISIM => $HE_CATS_ARR){
							if (strpos($HE_A_KONTROL, ",".$HE_AJANS_ISIM.",") === false){$HE_DISPLAY=" style='display:none' ";}else{$HE_DISPLAY="";}
							$HE_AJANS_ISIM_ID = str_replace(".", "", $HE_AJANS_ISIM);
							echo '<fieldset id="fs_'.$HE_AJANS_ISIM_ID.'" '.$HE_DISPLAY.'>
									<h2>'.$HE_AJANS_ISIM.'</h2>';
								foreach($HE_CATS_ARR as $HE_CAT){
									if (@$HE_CATS[$HE_CAT[1]]){
										if ( !in_array($HE_CAT[1], $HE_BOT_KATEGORILER_ARR ) ){$HE_SELECTED="";}else{$HE_SELECTED="checked";}
										echo '<label for="k_'.$HE_AJANS_ISIM_ID.'_'.$HE_CAT[1].'">
										<input name="kategoriler[]" type="checkbox" id="k_'.$HE_AJANS_ISIM_ID.'_'.$HE_CAT[1].'" data-name="'.$HE_CAT[0].'" value="'.$HE_CAT[1].'" '.$HE_SELECTED.'>
											<span>'.$HE_CAT[0].'</span>
										</label>';
									}
								}
								echo '</fieldset>';
							}?>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo  __("İçerik Dili","HaberEditoru") ?></th>
							<td>
								<select name="icerik_dili" id="icerik_dili">
									<option value="tr" <?php if ($HE_BOT_ICERIK_DILI=="tr"){echo "selected";}?>><?php echo  __("Türkçe","HaberEditoru") ?></option>
									<option value="en" <?php if ($HE_BOT_ICERIK_DILI=="en"){echo "selected";}?>><?php echo  __("İngilizce","HaberEditoru") ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo  __("İçerik Tipi","HaberEditoru") ?></th>
							<td>
								<select name="icerik_tipi" id="icerik_tipi">
									<option value="0" <?php if ($HE_BOT_ICERIK_TIPI=="0"){echo "selected";}?>><?php echo  __("Tümü","HaberEditoru") ?></option>
									<option value="1" <?php if ($HE_BOT_ICERIK_TIPI=="1"){echo "selected";}?>><?php echo  __("Sadece Haber","HaberEditoru") ?></option>
									<option value="2" <?php if ($HE_BOT_ICERIK_TIPI=="2"){echo "selected";}?>><?php echo  __("Sadece Video","HaberEditoru") ?></option>
									<option value="3" <?php if ($HE_BOT_ICERIK_TIPI=="3"){echo "selected";}?>><?php echo  __("Sadece Resim Galerisi","HaberEditoru") ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo  __("Resimsiz İçerik","HaberEditoru") ?></th>
							<td>
								<select name="resimsiz_haber" id="resimsiz_haber">
									<option value="1" <?php if ($HE_ITEM_IF_NOT_IMAGE=="1"){echo "selected";}?>><?php echo  __("Resimsiz İçerikleri Ekle","HaberEditoru") ?></option>
									<option value="0" <?php if ($HE_ITEM_IF_NOT_IMAGE=="0"){echo "selected";}?>><?php echo  __("Resimsiz İçerikleri Ekleme","HaberEditoru") ?></option>
								</select>
							</td>
						</tr>
												
						<tr>
							<th scope="row"><?php echo  __("Etiketler","HaberEditoru") ?></th>
							<td><input placeholder="Gaziantep, Internet" name="etiketler" id="etiketler" type="text" value="<?php if($HE_BOT_ETIKETLER!=""){echo $HE_BOT_ETIKETLER;}?>" class="regular-text">
								<br><small class="description"><?php echo  __("İçeriğin içerisinde mutlaka olmasını istediğiniz kelimer. (Örn. AKP, Gaziantep, doktor)","HaberEditoru") ?></small>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo  __("Negatif Etiketler","HaberEditoru") ?></th>
							<td><input placeholder="Magazin, BDP" name="negatif_etiketler" id="negatif_etiketler" type="text" value="<?php if($HE_BOT_ETIKETLER_NEGATIF!=""){echo $HE_BOT_ETIKETLER_NEGATIF;}?>" class="regular-text">
								<br><small class="description"><?php echo  __("İçeriğin içerisinde bu kelimeler geçiyorsa ekleme. (Örn. Demirtaş, magazin, sibel can)","HaberEditoru") ?></small>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		
		<div id="postbox-container-1" class="postbox-container">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<div class="postbox">
					<h2 class="hndle"><span class="dashicons dashicons-admin-generic"></span> <?php echo  __("Seçenekler","HaberEditoru") ?></h2>
					<div class="inside">
						<div class="form-wrap">
							<div class="form-field">
								<label for="aktif"><input id="aktif" name="aktif" type="checkbox" value="1" <?php if ($HE_BOT_STATUS=="1"){echo "checked";}?>> <?php echo  __("Robot Aktif","HaberEditoru") ?> </label>
							</div>
							<div class="form-field">
								<label for="post_durumu"><?php echo  __("İçerikleri","HaberEditoru") ?></label>
								<select name="post_durumu" id="post_durumu">
									<option value="publish" <?php if ($HE_BOT_POST_STATUS=="publish"){echo "selected";}?>><?php echo  __("Hemen Yayınla","HaberEditoru") ?></option>
									<option value="draft" <?php if ($HE_BOT_POST_STATUS=="draft"){echo "selected";}?>><?php echo  __("Onayımı Bekle","HaberEditoru") ?></option>
								</select>
							</div>
							<div class="form-field">
								<label for="site_editor"><?php echo  __("Editör","HaberEditoru") ?></label>
								<?php wp_dropdown_users(array('name' => 'site_editor' , 'selected' => $HE_BOT_POST_AUTHOR ,  'who'   => 'authors')); ?>
							</div>
							<div class="form-field">
								<label for="post_tipi"><?php echo  __("Post Tipi","HaberEditoru") ?></label>
								<select  name="post_tipi" id="post_tipi">
								<?
									foreach ($HE_POST_TYPES as $post_type) {
										if ( $HE_BOT_POST_TYPE == $post_type ) { $HE_SELECTED="selected"; }else{$HE_SELECTED="";}
										echo '<option '.$HE_SELECTED.' value="'.$post_type.'">'.$post_type.'</option>';
									}
								?>
								</select>
							</div>
							<div class="form-field">
								<label for="post_durumu"><?php echo  __("İçerik Tarihi","HaberEditoru") ?></label>
								<select name="post_date" id="post_date">
									<option value="1" <?php if ($HE_BOT_POST_DATE=="1"){echo "selected";}?>><?php echo  __("İçerik Zamanını Kullan","HaberEditoru") ?></option>
									<option value="" <?php if ($HE_BOT_POST_DATE==""){echo "selected";}?>><?php echo  __("Eklenme Zamanını Kullan","HaberEditoru") ?></option>
								</select>
							</div>
							<div class="form-field">
								<label for="post_durumu"><a target="_blank" href="http://www.habereditoru.com/icerik-ozgunlestirme.html"><?php echo  __("İçerik Özgünleştirme (Spinner)","HaberEditoru") ?></a></label>
								<select name="post_spinner" id="post_spinner">
									<option value="" <?php if ($HE_BOT_SPINNER==""){echo "selected";}?>><?php echo  __("Orjinal Kalsın","HaberEditoru") ?></option>
									<option value="1" <?php if ($HE_BOT_SPINNER=="1"){echo "selected";}?>><?php echo  __("İçeriği Özgünleştir","HaberEditoru") ?></option>
									
								</select>
							</div>
							<div class="form-field">
								<label for="post_tipi"><?php echo  __("Resim Özel Alanı Adı","HaberEditoru") ?></label>
								
								<input style="width:100%" placeholder="<?php echo  __("resim_ozel_alan_adi","HaberEditoru") ?>" name="resim_ozel_alan_adi" id="resim_ozel_alan_adi" type="text" value="<?php if($HE_BOT_RESIM_OZEL_ALAN!=""){echo $HE_BOT_RESIM_OZEL_ALAN;}?>" class="regular-text">
							
							</div>
							<p class="submit"><input type="submit" name="submit" id="ba_submit" class="button button-primary" value="<?php echo  __("Değişiklikleri Kaydet","HaberEditoru") ?>"></p>
							<div class="ajaxMsg"></div>
						</div>
						<div class="clear"></div>
					</div>
				</div>
			</div>
		</div>
		<div id="postbox-container-2" class="postbox-container">
			<div class="postbox">
				<h2 class="hndle"><span class="dashicons dashicons-align-center"></span> <?php echo  __("İçerik Düzenleme","HaberEditoru") ?></h2>
				<div class="inside">
					<table  style="border:0" class="widefat striped">	
						<tr>
							<th scope="row"><?php echo  __("İçeriğin Önüne Ekle","HaberEditoru") ?></th>
							<td><textarea placeholder="Örn:Reklam Kodunuz" style="width:100%" name="icerigin_onune_ekle" id="icerigin_onune_ekle" rows="3" cols="40"><?php if($HE_BOT_ICERIK_ONU!=""){echo $HE_BOT_ICERIK_ONU;}?></textarea></td>
						</tr>
						<tr>
							<th scope="row"><?php echo  __("İçeriğin Sonuna Ekle","HaberEditoru") ?></th>
							<td><textarea placeholder="Örn:Editör Adı" style="width:100%" name="icerigin_sonuna_ekle" id="icerigin_sonuna_ekle" rows="3" cols="40"><?php if($HE_BOT_ICERIK_SONU!=""){echo $HE_BOT_ICERIK_SONU;}?></textarea></td>
						</tr>
						<tr>
							<th scope="row"><?php echo  __("Haber Kaynağı","HaberEditoru") ?></th>
							<td>
								<label for="kaynak_adi">
									<input name="kaynak_adi" id="kaynak_adi" type="checkbox" value="1" <?php if ($HE_BOT_CONTENT_AFTER_AGENCY_NAME=="1"){echo "checked";}?>>
									<?php echo  __("İçeriğin altına haber kaynağının adını yaz","HaberEditoru") ?>
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

<style>
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