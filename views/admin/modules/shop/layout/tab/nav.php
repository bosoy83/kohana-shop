<?php defined('SYSPATH') or die('No direct access allowed.');

	echo '<li>', HTML::anchor('#tab-'.$code, $title, array(
		'data-toggle' => 'tab'
	)), '</li>';

?>
	<script type="text/javascript">
	$(function(){
		$('.kr-aside [href="#tab-<?php echo $code; ?>"]').on('click', function(e){
			e.preventDefault();
			$('.nav-tabs [href="#tab-<?php echo $code; ?>"]')
				.tab('show');
		});
	});
	</script>