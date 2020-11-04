<?php

/**
 * This file should be used to render each module instance.
 * You have access to two variables in this file: 
 * 
 * $module An instance of your module class.
 * $settings The module's settings.
 *
 * Example: 
 */
$maps=get_posts(array('post_type' => 'maps'));
if(empty($maps)){
?>
<div class="fl-example-text">
<?php $address = $settings->text_field; ?>
    <?php $storesradius = $settings->select_field; ?>
	<?php
	$display_store =  do_shortcode('[store_locator_show location="'.$address.'" radius="'.$storesradius.'"]');	 
			echo $display_store;
	?>
</div>
<?php }else{ ?>
	<div class="fl-example-text">
    <?php $map_id = $settings->map_id; 
	$display_store =  do_shortcode('[wp_multi_store_locator_map id="'.$map_id.'"]');	 
			echo $display_store;
	?>
	</div>
	<?php } ?>