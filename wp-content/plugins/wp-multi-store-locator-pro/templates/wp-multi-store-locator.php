<!DOCTYPE html>
<html>
<head>
	<title><?php echo bloginfo( 'name' ); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body>
	<div class="wp_multi_store_locator_map">
	<?php 
		$current = trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/');
		$segments=explode('wp-multi-store-locator/', $current);
		$map=isset($segments[1]) ? absint($segments[1]) : '';
		if(!empty($map) && get_post_type($map)=='maps' && get_post_status($map)=='publish'){
			echo do_shortcode('[wp_multi_store_locator_map id='.$map.']');
		}else{
			echo '<div class="wp-map-error">'.__('There was an error.','store-locator').'</div>';
		}
	?>
	</div>
	<?php wp_footer(); ?>
</body>
</html>