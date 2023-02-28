	<script src="<?php echo asset_url('office/js/BeePOS.js') ?>?<?php echo date('YmdHis');?>"></script>
	<script src="<?php echo asset_url('office/js/main.js') ?>?<?php echo date('YmdHis');?>"></script>
	<script>
		BeePOS.options = {
			ajaxUrl: '<?php echo App::url("ajax.php") ?>',
			lang: <?php echo json_encode(trans('main.js')) ?>,
			debug: <?php echo Config::get('app.debug')?1:0 ?>,
			
		};
	</script>

