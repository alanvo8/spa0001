<?php
class DS_Custom_Module_WPStore_Locator extends ET_Builder_Module {
 	function init() {
		$this->name = esc_html__( 'WP Store Locator', 'et_builder' );
		$this->slug = 'et_pb_wp_stores_locator';
		$this->whitelisted_fields = array(
			'add_address',
			'add_radius',
		);
 		$this->fields_defaults = array(
			'add_address' => array( 'Midtown USA', 'add_default_setting' ),
			'add_radius' => array( '500 KM', 'add_default_setting' ),
		);
 	}
	function get_fields() {
		 $fields = array(
			 'add_address' => array(
			 'label' => esc_html__( 'Enter Address', 'et_builder' ),
			 'type' => 'text',
			 'option_category' => 'configuration',
			 'description' => esc_html__( 'Enter Location to display stores', 'et_builder' ),
			 ),
			 'add_radius' => array(
				 'label' => esc_html__( 'Select Radius', 'et_builder' ),
				 'type' => 'select',
				 'option_category' => 'configuration',
				 'options' => array(
					 '5' => esc_html__( '5 KM', 'et_builder' ),
					 '10' => esc_html__( '10 KM', 'et_builder' ),
					 '25' => esc_html__( '25 KM', 'et_builder' ),
					 '50' => esc_html__( '50 KM', 'et_builder' ),
					 '100' => esc_html__( '100 KM', 'et_builder' ),
					 '200' => esc_html__( '200 KM', 'et_builder' ),
					 '500' => esc_html__( '500 KM', 'et_builder' ),
				 ),
				 'affects' => array(
				 	'#et_pb_show_more',
				 ),
				 'description' => esc_html__( 'Choose Radius and see Stores ', 'et_builder' ),
		 	),
		 );
		 return $fields;
 	}
	function shortcode_callback( $atts, $content = null, $function_name ) {
		$add_address = $this->shortcode_atts['add_address'];
		$add_radius = $this->shortcode_atts['add_radius'];
		$display_store =  do_shortcode('[store_locator_show location="'.$add_address.'" radius="'.$add_radius.'"]');
		return $display_store;
	}
}
new DS_Custom_Module_WPStore_Locator;