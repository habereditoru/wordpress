<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists('he_abonelik') ) :
	class he_abonelik
	{

		public static function output()
		{
			?>
			<?php
			if (get_option('HE_IS_SETUP')){
				?>
				<div>
					<iframe frameborder="0" allowtransparency="true" style="width:100%;height:700px;border:0;" src="<?php echo HE_API_URL;?>/member/?d=<?php echo HE_DOMAIN ?>"></iframe>
				</div>
			<?php } else {
				$current_user = wp_get_current_user();
				?>

				<div class="wrap">
					<h1><?php echo  __("Web Sitenizi Kayıt Edin!","habereditoru") ?></h1>

					<form id="he-form-register" name="he-form" onsubmit="return false">
						<input type="hidden" name="tip" value="ab">
						<table class="form-table">
							<tr>
								<th scope="row"><?php echo  __("Domain","habereditoru") ?></th>
								<td><?php echo HE_DOMAIN?></td>
							</tr>
							<tr>
								<th scope="row"><?php echo  __("Adınız Soyadınız","habereditoru") ?></th>
								<td><input type="text" id="adi" name="adi" value="<?php echo $current_user->user_firstname?> <?php echo $current_user->user_lastname?>" size="30"  maxlength="60" placeholder="<?php echo  __("Adınız Soyadınız","habereditoru") ?>"></td>
							</tr>
							<tr>
								<th scope="row"><?php echo  __("E-Posta Adresiniz","habereditoru") ?></th>
								<td><input type="email" id="eposta" name="eposta" value="<?php echo $current_user->user_email?>" size="30" maxlength="60" placeholder="<?php echo  __("eposta@adresim.com","habereditoru") ?>"></td>
							</tr>
							<tr>
								<th scope="row"><?php echo  __("Cep Telefonu","habereditoru") ?></th>
								<td><input type="text" id="gsm" name="gsm" size="30" maxlength="15" placeholder="5051234567"></td>
							</tr>
							<tr>
								<th scope="row"><?php echo  __("Dil","habereditoru") ?></th>
								<td><select id="dil" name="dil">
										<option value="tr"><?php echo  __("Türkçe","habereditoru") ?></option>
										<option value="en"><?php echo  __("English","habereditoru") ?></option>
									</select>
								</td>
							</tr>
						</table><div class="ajaxMsg"></div>
						<p class="submit"><input type="submit" name="submit" id="ab_submit" class="button button-primary" value="<?php echo  __("Kaydet","habereditoru") ?>"><span id="ab_status"></span></p>
					</form>

				</div>
			<?php }?>

			<?php
		}
	}
endif;
?>
