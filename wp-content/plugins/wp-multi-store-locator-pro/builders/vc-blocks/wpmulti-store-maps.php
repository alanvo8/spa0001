<?php
/*
Element Description: VC Info Box
*/
// Element Class 
class vcWPMultistore extends WPBakeryShortCode {
    // Element Init
    function __construct() {
        add_action( 'wp_loaded', array( $this, 'vc_wpmulti_store_locator' ) );
        add_shortcode( 'vc_wp_multi_store_locator', array( $this, 'vc_infobox_html' ),10,1 );
    }
	// Element Mapping
	public function vc_wpmulti_store_locator() {
		// Stop all if VC is not enabled
		if ( !defined( 'WPB_VC_VERSION' ) ) {
				return;
		}
		$maps=get_posts(array('post_type' => 'maps','post_status'=>'publish','posts_per_page'=>-1));
		$maps_arr=array();
		if(!empty($maps)){
			foreach ($maps as $key => $value) {
				$id=$value->ID;
				$maps_arr[$value->post_title]=$value->ID;
			}
		}
		vc_map( 
			array(
				'name' => __('WP Multi Store', 'text-domain'),
				'base' => 'vc_wp_multi_store_locator',
				'description' => __('WP Multi Store VC box', 'text-domain'), 
				'category' => __('WP Multi Store', 'text-domain'),   
				'icon' => plugin_dir_url( __FILE__ ) .'img/Wp-multi-store-locator.png',            
				'params' => array(   
					array(
						  "type"        => "dropdown",
						  "heading"     => __("Select Map"),
						  "param_name"  => "mapid",
						  "admin_label" => true,
						  "value"       => $maps_arr, //value
						'description' => __( 'Select Map ', 'text-domain' ),
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
	                'mapid'   => '',
	            ), 
	            $atts
	        )
	    );
	    // Display front end wpmsl by shortcode
	    $doshort = do_shortcode('[wp_multi_store_locator_map id='.$mapid.']');
	    return $doshort;    
    }
} // End Element Class
// Element Class Init
new vcWPMultistore();    
