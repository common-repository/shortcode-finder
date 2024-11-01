<?php global $ms_shortcode; ?>
<div id="<?php echo $ms_shortcode->popupID; ?>">
	<iframe id="mssc-iframe" class="mssc-iframe" src="<?php echo get_site_url().'/wp-content/plugins/shortcode-finder/includes/shortcode-iframe.php'; ?>" onload="mssc.popupIframeLoad(this)" seamless width="1" height="1"></iframe>
	<form id="mssc-post-form">
		<div id="mssc-select-wrapper" class="mssc-select-wrapper">
			<h2>Pick a shortcode:</h2>
			<ul></ul>
		</div><div id="mssc-attr-wrapper" class="mssc-attr-wrapper">
			<h2>Pick your attributes:</h2>
		</div>
		<button class="button button-primary button-large mssc-submit" id="mssc-submit" disabled>Insert into page</button>
	</form>
	<script>
		//Can't wait till WordPress abandons ThickBox
		jQuery('body').addClass('mssc-modal');
	</script>
</div>
