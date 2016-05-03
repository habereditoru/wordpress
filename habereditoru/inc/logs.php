<h2><?php echo  __("Aktiviteler","HaberEditoru") ?> <a style="float:right" class="page-title-action" href="?page=HaberEditoru&t=logs&temizle=1"><?php echo  __("AKTİVETELERİ SİL","HaberEditoru") ?></a></h2>
<div style="max-height:400px;overflow:auto">
<?php 

	if ( $_GET['temizle']=="1" ){
		delete_option('HE_LAST_CRON');
	}

	echo get_option('HE_LAST_CRON');
?>
</div>	