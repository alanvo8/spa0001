<?php
/*
Element Description: VC Info Box
*/
// Element Class 
class vcWPMultistore extends WPBakeryShortCode {
    // Element Init
    function __construct() {
        add_action( 'wp_loaded', array( $this, 'vc_wpmulti_store_locator' ) );
        add_shortcode( 'vc_wpmsl', array( $this, 'vc_infobox_html' ) );
    }
	// Element Mapping
	public function vc_wpmulti_store_locator() {
		// Stop all if VC is not enabled
		if ( !defined( 'WPB_VC_VERSION' ) ) {
				return;
		}
		// Map the block with vc_map()
		vc_map( 
			array(
				'name' => __('WP Multi Store', 'text-domain'),
				'base' => 'vc_wpmsl',
				'description' => __('WP Multi Store VC box', 'text-domain'), 
				'category' => __('WP Multi Store', 'text-domain'),   
				'icon' => plugin_dir_url( __FILE__ ) .'img/Wp-multi-store-locator.png',            
				'params' => array(   
					array(
						'type' => 'textfield',
						'class' => 'title-class',
						'heading' => __( 'Address', 'text-domain' ),
						'param_name' => 'location',
						'value' => __( 'Carol Stream, IL, USA', 'text-domain' ),
						'description' => __( 'Eg: Carol Stream, IL, USA', 'text-domain' ),
						'admin_label' => false,
						'weight' => 0,
						'group' => 'General',
					),  
					array(
						  "type"        => "dropdown",
						  "heading"     => __("Select Radius For Map"),
						  "param_name"  => "radiusmap",
						  "admin_label" => true,
						  "value"       => array(
												'5 km'   => '5',
												'10 km'   => '10',
												'25 km' => '25',
												'50 km'  => '50',
												'100 km'  => '100',
												'200 km'  => '200',
												'500 km'  => '500'
												), //value
						'description' => __( 'Select radius for map ', 'text-domain' ),
						'admin_label' => false,
						'weight' => 0,
						'group' => 'General',
					)
						 
				)
			)
		);                                
	}
	// Element HTML
    public function vc_infobox_html( $atts ) {
    // Params extraction
    extract(
        shortcode_atts(
            array(
                'location'   => '',
                'radiusmap' => '',
            ), 
            $atts
        )
    );
    // Display front end wpmsl by shortcode
    $doshort = do_shortcode('[store_locator_show location="'.$location.'" radius="'.$radiusmap.'" ]');
    return $doshort;    
    }
} // End Element Class
// Element Class Init
new vcWPMultistore();    
