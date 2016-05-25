<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists('he_raporlar') ) :
class he_raporlar
{

	public static function output()
	{
		?>
		<h2><?php echo  __("Raporlar", "habereditoru") ?> </h2>
		<p>
			<a class="page-title-action" href="admin.php?page=habereditoru&t=raporlar&r=today"><?php echo  __("Bugün", "habereditoru") ?></a>
			<a class="page-title-action" href="admin.php?page=habereditoru&t=raporlar&r=weekly"><?php echo  __("Bu Hafta", "habereditoru") ?></a>
			<a class="page-title-action" href="admin.php?page=habereditoru&t=raporlar&r=monthly"><?php echo  __("Bu Ay", "habereditoru") ?></a>
		</p>
		<div style="max-height:400px;overflow:auto">
			<?php
			$get_r = filter_input( INPUT_GET, 'r' );
			if ($get_r == "weekly" ){
				$r = "weekly" ;
			}
			else if ( $get_r =="monthly" ){
				$r = "monthly" ;

			}else{
				$r = "today" ;
			}

			$HE_XML = he_curl(HE_API_URL."/report/?d=".HE_DOMAIN."&k=".HE_API_KEY."&r=".$r);
			$obj = json_decode($HE_XML) ;
			if (!isset($obj->{"RESULT"})) {
				echo he_message('notice error is-dismissible', 'Veriler alınırken hata oluştu.');
				return;
			}
			$data = $obj->{"RESULT"};

			//print_r($data);

			if (count($data)) {
				echo '<table class="wp-list-table widefat striped plugins"><thead>';
				echo '<th>#</th>';
				echo '<th>BOT</th>';
				echo '<th>ID</th>';
				echo '<th>Başlık</th>';
				echo '<th>Eklenme Tarihi</th>';
				echo '</thead><tbody>';
				$i=1;
				foreach ($data as $stand) {
					echo '<tr>';
					echo '<td>'.$i.'</td>';
					echo '<td>'.$stand[0].'</td>';
					echo '<td>'.$stand[5].'</td>';
					echo '<td><a target="_blank" href="post.php?action=edit&post='.$stand[5].'">'.$stand[2].'</a></td>';
					echo '<td>'.$stand[3].'</td>';
					echo '</tr>';
					$i++;
				}
				echo '</tbody></table>';
			}
			?>
		</div>
		<?php

	}
}
endif;
?>
