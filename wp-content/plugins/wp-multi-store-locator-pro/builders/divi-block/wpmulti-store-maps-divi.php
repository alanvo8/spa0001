<?php
class DS_Custom_Module_WPStore_Locator extends ET_Builder_Module {
 	function init() {
		$this->name = esc_html__( 'WP Store Locator', 'et_builder' );
		$this->slug = 'et_pb_wp_stores_locator';
		$this->whitelisted_fields = array(
			'add_map_id'
		);
 		$this->fields_defaults = array(
			'add_map_id' => array( '0', 'add_default_setting' ),
		);
 	}
	function get_fields() {
		$maps=get_posts(array('post_type' => 'maps','post_status'=>'publish','posts_per_page'=>-1));
		$maps_arr=array();
		if(!empty($maps)){
			foreach ($maps as $key => $value) {
				$id=$value->ID;
				$maps_arr[$value->ID]=$value->post_title;
			}
		}
		 $fields = array(
			 'add_map_id' => array(
				 'label' => esc_html__( 'Select Map', 'et_builder' ),
				 'type' => 'select',
				 'option_category' => 'configuration',
				 'options' =>$maps_arr,
				 'affects' => array(
				 	'#et_pb_show_more',
				 ),
				 'description' => esc_html__( 'Choose from Maps', 'et_builder' ),
		 	),
		 );
		 return $fields;
 	}
	function shortcode_callback( $atts, $content = null, $function_name ) {
		$add_map_id = $this->shortcode_atts['add_map_id'];
		$display_store =  do_shortcode('[wp_multi_store_locator_map id="'.$add_map_id.'"]');
		return $display_store;
	}
}
new DS_Custom_Module_WPStore_Locator;