<?php

class he_kategori_eslestirme
{

	private static function enqueue_assets() {
		wp_enqueue_script( 'habereditoru-ms', HE_PLUGIN_DIR_URL.'assets/js/jquery.multiselect.js', array('jquery'), HE_PLUGIN_VERSION, true );
		wp_enqueue_style( 'habereditoru-ms', HE_PLUGIN_DIR_URL.'assets/css/jquery.multiselect.css' );
	}


	public static function output() {

		self::enqueue_assets();

		$he_site_kategoriler = get_categories('hide_empty=0&orderby=name');
		$he_opt_kaynaklar = get_option('HE_OPT_KAYNAKLAR');
		$he_opt_kategoriler = get_option('HE_OPT_KATEGORILER');
		$he_opt_kategori_eslestirme = get_option('HE_OPT_KAT_ESLESTIRME');
		$he_xml_cats = he_jsontoxml(he_curl(HE_API_URL."/get/agencies_categories?selAgency=". implode(",",$he_opt_kaynaklar)));
		$he_counter=0;

		if(!empty($he_opt_kaynaklar)){sort($he_opt_kaynaklar);}
		if(!empty($he_opt_kategoriler)){sort($he_opt_kategoriler);}
		if(!empty($he_opt_kategori_eslestirme)){ksort($he_opt_kategori_eslestirme);}


		echo '	<form id="he-form-ke" name="he-form" onsubmit="return false">
		<input type="hidden" name="tip" value="ke">
		<div>
		<h2>Kategori Eşleştirme</h2>
		<table class="wp-list-table widefat striped" id="ke_table"><thead><tr><th>'.__("Haber Editörü Kategorileri","habereditoru").'</th><th>'.__("Sizin Kategoriler","habereditoru").'</th><th>'.__("İşlem","habereditoru").'</th></tr><thead><tbody>';
		$eslestirmeStatus = 0;
		if (is_array($he_opt_kategori_eslestirme))  $eslestirmeStatus = count($he_opt_kategori_eslestirme);
		if ($eslestirmeStatus > 0){

			foreach ($he_opt_kategori_eslestirme as $ke_x => $ke_l){
				$he_counter = $he_counter+1;
				$ke_l = ",".$ke_l.",";
				echo '<tr id="ke_tr_'.$he_counter.'"><td width="47%"><select style="width:100%;border:1px solid #999;" size="1" id="r_'.$he_counter.'" name="r_'.$he_counter.'">';

				foreach($he_xml_cats->Agencies_Categories as $a_isim => $k_array){
					echo '<optgroup label="'.$a_isim.'">';
					
					foreach($k_array as $k_bilgi){

						if ( in_array($k_bilgi[1], $he_opt_kategoriler) ){
							$sel = ($ke_x == $k_bilgi[1]) ? "selected" : "";
							echo '<option '.$sel.' value="'.$k_bilgi[1].'">'. $a_isim . ' > ' . $k_bilgi[0].'</option>';
						}

					}

					echo '</optgroup>';
				}
				echo '</select></td>
		<td width="47%"><select multiple="multiple" size="1" id="l_'.$he_counter.'" name="l_'.$he_counter.'">';
				foreach ( $he_site_kategoriler as $category){
					if (strpos($ke_l, ",".$category->term_id.",") !== false){$sel="selected";}else{$sel="";}
					echo '<option '.$sel.' value="'.$category->term_id.'">'.$category->cat_name . ' (' . $category->category_count . ')</option>';
				}
				echo '</select></td><td width="6%"><input type="button" value="'.__("Sil","habereditoru").'" onclick="remove_id(\'ke_tr_'.$he_counter.'\')"></td></tr>';
			}
		}else {update_option('HE_OK_KATEGORI_ESLESTIRME',0);}
		if ( get_option('HE_OK_KATEGORI_ESLESTIRME')==0  ) {
			$he_counter=$he_counter+1;
			for ($k=$he_counter;$k<($he_counter+10);$k++){
				if ($k>$he_counter){$clss="none";}else{$clss="";};
				echo '<tr style="display:'.$clss.';" id="ke_tr_'.$k.'"><td width="47%"><select style="width:100%;border:1px solid #999;" size="1" id="r_'.$k.'" name="r_'.$k.'">';
				foreach($he_xml_cats->Agencies_Categories as $a_isim => $k_array){
					echo '<optgroup label="'.$a_isim.'">';
					foreach($k_array as $k_bilgi){

						if ( in_array($k_bilgi[1], $he_opt_kategoriler) && !array_key_exists($k_bilgi[1], $he_opt_kategori_eslestirme) ){
							echo '<option value="'.$k_bilgi[1].'">'.  $a_isim . ' > ' . $k_bilgi[0] . '</option>';
						}
					}
					echo '</optgroup>';
				}
				echo '</select></td>
		<td width="47%"><select multiple="multiple" size="1" id="l_'.$k.'" name="l_'.$k.'">';
				foreach ( $he_site_kategoriler as $category)
					echo '<option value="'.$category->term_id.'">'.$category->cat_name . ' (' . $category->category_count . ')</option>';
				echo '</select></td><td width="6%"><input type="button" value="'.__("Sil","habereditoru").'" onclick="remove_id(\'ke_tr_'.$k.'\')"></td></tr>';
			}
		}


		echo '</tbody></table>';
		if ( !get_option('HE_OK_KATEGORI_ESLESTIRME')  ) {
			echo '<p><input type="button" style="cursor:pointer" value="+" onclick="show_id(\'ke_tr_\')"></p>';
		}
		?>
			<p class="submit"><input type="submit" name="submit" id="ke_submit" class="button button-primary" value="<?php _e("Kaydet","habereditoru") ?>"></p>
		</form>
		<div class="ajaxMsg"></div>
		<script>
			jQuery(document).ready(function($) {
				$("#ke_table select[multiple]").multiselect({ placeholder: "<?php _e("Kategoriler","habereditoru")?>" });
			});
		</script>
		</div>
		<?php
		return;
	}

}


?>