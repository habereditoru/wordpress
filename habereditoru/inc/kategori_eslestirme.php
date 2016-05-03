<?php

wp_enqueue_script( 'HaberEditoru-js-ms-css', HE_PLUGIN_DIR_URL.'js/jquery.multiselect.js', array('jquery'), 1, true );
wp_enqueue_style( 'HaberEditoru-js-ms', HE_PLUGIN_DIR_URL.'css/jquery.multiselect.css' );
		
$HE_SITE_KATEGORILER = get_categories('hide_empty=0&orderby=name');
$HE_OPT_KAYNAKLAR = get_option('HE_OPT_KAYNAKLAR');
$HE_OPT_KATEGORILER = get_option('HE_OPT_KATEGORILER');
$HE_OPT_KATEGORI_ESLESTIRME = get_option('HE_OPT_KAT_ESLESTIRME');
$HE_XML_CATS = he_jsontoxml(he_curl(HE_API_URL."/get/agencies_categories?selAgency=". implode(",",$HE_OPT_KAYNAKLAR)));
$HE_COUNTER=0;

if(!empty($HE_OPT_KAYNAKLAR)){sort($HE_OPT_KAYNAKLAR);}
if(!empty($HE_OPT_KATEGORILER)){sort($HE_OPT_KATEGORILER);}
if(!empty($HE_OPT_KATEGORI_ESLESTIRME)){ksort($HE_OPT_KATEGORI_ESLESTIRME);}


echo '	<form id="he-form-ke" name="he-form" onsubmit="return false">
		<input type="hidden" name="tip" value="ke">
		<div>
		<h2>Kategori Eşleştirme</h2>
		<table class="wp-list-table widefat striped" id="ke_table"><thead><tr><th>'.__("Haber Editörü Kategorileri","HaberEditoru").'</th><th>'.__("Sizin Kategoriler","HaberEditoru").'</th><th>'.__("İşlem","HaberEditoru").'</th></tr><thead><tbody>';

	if (is_array($HE_OPT_KATEGORI_ESLESTIRME)){
	foreach ($HE_OPT_KATEGORI_ESLESTIRME as $ke_x => $ke_l){
		$HE_COUNTER = $HE_COUNTER+1;
		$ke_l = ",".$ke_l.",";
		echo '<tr id="ke_tr_'.$HE_COUNTER.'"><td width="47%"><select style="width:100%;border:1px solid #999;" size="1" id="r_'.$HE_COUNTER.'" name="r_'.$HE_COUNTER.'">';
		foreach($HE_XML_CATS->Agencies_Categories as $a_isim => $k_array){
			echo '<optgroup label="'.$a_isim.'">';
				foreach($k_array as $k_bilgi){
					
						if ( in_array($k_bilgi[1], $HE_OPT_KATEGORILER) ){
							if ($ke_x == $k_bilgi[1]){$sel="selected";}else{$sel="";}
							echo '<option '.$sel.' value="'.$k_bilgi[1].'">'. $a_isim . ' > ' . $k_bilgi[0].'</option>';
						}
					
				}
				
				echo '</optgroup>';
		}
		echo '</select></td>
		<td width="47%"><select multiple="multiple" size="1" id="l_'.$HE_COUNTER.'" name="l_'.$HE_COUNTER.'">';
			foreach ($HE_SITE_KATEGORILER as $category){
				if (strpos($ke_l, ",".$category->term_id.",") !== false){$sel="selected";}else{$sel="";}
				echo '<option '.$sel.' value="'.$category->term_id.'">'.$category->cat_name . ' (' . $category->category_count . ')</option>';
			}
		echo '</select></td><td width="6%"><input type="button" value="'.__("Sil","HaberEditoru").'" onclick="remove_id(\'ke_tr_'.$HE_COUNTER.'\')"></td></tr>';
	}
	}else {update_option('HE_OK_KATEGORI_ESLESTIRME',0);}
	if ( get_option('HE_OK_KATEGORI_ESLESTIRME')==0  ) {
	$HE_COUNTER=$HE_COUNTER+1;
 	for ($k=$HE_COUNTER;$k<($HE_COUNTER+10);$k++){
		if ($k>$HE_COUNTER){$clss="none";}else{$clss="";};
		echo '<tr style="display:'.$clss.';" id="ke_tr_'.$k.'"><td width="47%"><select style="width:100%;border:1px solid #999;" size="1" id="r_'.$k.'" name="r_'.$k.'">';
		foreach($HE_XML_CATS->Agencies_Categories as $a_isim => $k_array){
			echo '<optgroup label="'.$a_isim.'">';
				foreach($k_array as $k_bilgi){
					
						if ( in_array($k_bilgi[1], $HE_OPT_KATEGORILER) && !array_key_exists($k_bilgi[1], $HE_OPT_KATEGORI_ESLESTIRME) ){
							echo '<option value="'.$k_bilgi[1].'">'.  $a_isim . ' > ' . $k_bilgi[0] . '</option>';
						} 
				}
				echo '</optgroup>';
		}
		echo '</select></td>
		<td width="47%"><select multiple="multiple" size="1" id="l_'.$k.'" name="l_'.$k.'">';
			foreach ($HE_SITE_KATEGORILER as $category)
				echo '<option value="'.$category->term_id.'">'.$category->cat_name . ' (' . $category->category_count . ')</option>';
		echo '</select></td><td width="6%"><input type="button" value="'.__("Sil","HaberEditoru").'" onclick="remove_id(\'ke_tr_'.$k.'\')"></td></tr>';
	}
	}

	
echo '</tbody></table>';
if ( !get_option('HE_OK_KATEGORI_ESLESTIRME')  ) { 
	echo '<p><input type="button" style="cursor:pointer" value="+" onclick="show_id(\'ke_tr_\')"></p>';
}
echo '<p class="submit"><input type="submit" name="submit" id="ke_submit" class="button button-primary" value="'.__("Kaydet","HaberEditoru").'"></p>
</form>
<div class="ajaxMsg"></div>
<script>jQuery(document).ready(function($) {$("#ke_table select[multiple]").multiselect({ placeholder: "'.__("Kategoriler","HaberEditoru").'" });});</script>
</div>';

?>