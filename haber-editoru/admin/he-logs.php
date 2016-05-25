<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists('he_logs') ) :
class he_logs
{

	public static function output()
	{
		?>
		<h2><?php echo __("Aktiviteler", "habereditoru") ?>
			<a style="float:right" class="page-title-action" href="<?php echo esc_url( admin_url( add_query_arg( array(  'page' => 'habereditoru', 't' => 'logs', 'temizle' => '1'), 'admin.php' ) ) ); ?>">
				<?php echo __("AKTİVETELERİ SİL", "habereditoru") ?>
			</a>
		</h2>

		<div style="max-height:400px;overflow:auto">
			<?php
			if (isset($_GET['temizle'])) {
				if (intval($_GET['temizle']) == "1") {
					delete_option('HE_LAST_CRON');
				}
			}
			echo get_option('HE_LAST_CRON');
		?>
		</div>
		<?php
	}
}
endif;

?>
