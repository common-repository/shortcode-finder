<?php 
require('../../../../wp-blog-header.php');
global $ms_shortcode;
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Shortcode Iframe</title>
	</head>
<body>
	<script type="text/javascript">
	//<![CDATA[
	window.shortcodefinder = <?php echo json_encode($ms_shortcode->get_shortcodes()); ?>;
	//]]>
	</script>
</body>
</html>