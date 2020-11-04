<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}  
if(!class_exists('WPMSL_Multi_Maps_Frontend')){
	class WPMSL_Multi_Maps_Frontend extends WPMSL_Maps_Frontend_Controller{
		public function __construct(){
			//if(!is_admin())
			add_shortcode('wp_multi_store_locator_map',array($this, 'wp_multi_store_locator_map_callback'));

			add_action('wp_ajax_make_search_request_maps',array($this, 'make_search_request_maps_callback'));
			add_action('wp_ajax_nopriv_make_search_request_maps',array($this, 'make_search_request_maps_callback'));
			
			add_action('wp_ajax_make_search_request_maps_regular',array($this, 'make_search_request_maps_regular_callback'));
			add_action('wp_ajax_nopriv_make_search_request_maps_regular',array($this, 'make_search_request_maps_regular_callback'));
			
			add_action('wp_ajax_make_search_request_custom_maps',array($this, 'make_search_request_custom_maps_callback'));
			add_action('wp_ajax_nopriv_make_search_request_custom_maps',array($this, 'make_search_request_custom_maps_callback'));

			add_filter('template_include', array($this,'load_map_iframe_template'),10,1);

			add_action( 'trashed_post',array($this, 'refresh_stores_on_delete_restore') );
			add_action( 'untrashed_post',array($this, 'refresh_stores_on_delete_restore') );
		}

		function refresh_stores_on_delete_restore( $post_id ) {
            if ( 'store_locator' == get_post_type( $post_id )){
				$this->refresh_stores();
			}
         }
         


		public function wp_multi_store_locator_map_callback($atts){
			 $map = shortcode_atts( array(
		        'id' => '',
		    ), $atts );
			 $map_id=$map['id'];
			$layout=get_post_meta($map_id,'map_layouts',true);
			if(isset($layout['layout'])){
				wp_enqueue_style( 'dashicons' );
    			switch ($layout['layout']) {
    				case 'fullscreen':
    					return $this->full_screen_multiple_map($map_id);
    					break;
    				case 'regular':
    					echo $this->regular_multiple_map($map_id);
    				break;
    				case 'custom':
    					return  $this->custom_multiple_map($map_id);
    				break;
    				default:
						return $this->custom_multiple_map($map_id);
    				break;
    			}
			}
			else{
			     echo do_shortcode('[store_locator_show map_id='.$map_id.']');
			}
		}
		public function full_screen_multiple_map($map_id){
			wp_enqueue_style('store_maps_style');
		    wp_enqueue_script('store_frontend_map');
			ob_start();
			$map_options = get_post_meta($map_id,'store_locator_map',true);
			if(isset($map_options['global']) && $map_options['global']=='yes'){
				 $map_options = get_option('store_locator_map',true);
			}
		   $grid_options = get_post_meta($map_id,'store_locator_grid',true);
		   if(isset($grid_options['global']) && $grid_options['global']=='yes'){
		       $grid_options = get_option('store_locator_grid');
		   }
           $placeholder_settings = get_post_meta($map_id,'placeholder_settings',true);
           if(isset($placeholder_settings['global']) && $placeholder_settings['global']=='yes'){
                $placeholder_settings = get_option('placeholder_settings',true);
            }
    	   $map_landing_address=get_post_meta($map_id,'map_landing_address',true);
           if(isset($map_landing_address['global']) && $map_landing_address['global']=='yes'){
               	$map_landing_address=get_option('map_landing_address',true); 
           }
		    $radius = ($map_options['radius'])?explode(",",trim($map_options['radius'])):false;
		    $tag = isset($map_options['tag']) ? $map_options['tag'] : '';
		    $category = isset($map_options['category']) ? $map_options['category'] : '';
		    $map_options['marker1'] = STORE_LOCATOR_PLUGIN_URL . "assets/img/" . ((isset($map_options['marker1']) && !empty($map_options['marker1'])) ? $map_options['marker1'] : "blue.png");
		    $map_options['marker2'] = STORE_LOCATOR_PLUGIN_URL . "assets/img/" . ((isset($map_options['marker2']) && !empty($map_options['marker2'])) ? $map_options['marker2'] : "red.png");
		    if(!empty($map_options['marker1_custom'])) {
		        $map_options['marker1'] = $map_options['marker1_custom'];
		    }
		    if(!empty($map_options['marker2_custom'])) {
		        $map_options['marker2'] = $map_options['marker2_custom'];
		    }
		    $default_radius = 500;
		    if(!empty($map_options['radius'])){
		    preg_match("/^(.*\[)(.*)(\])/", $map_options['radius'], $find);
		    $default_radius = trim($find[2]);
		    }
		    // Attributes
		    if (is_ssl()) {
				if(isset($placeholder_settings['get_location_btn_txt'])){
					$btn = $placeholder_settings['get_location_btn_txt'];
				}
		        if(empty($btn));
		            $btn=esc_html__('Get my location','store_locator');
		        $display = '';
		    } else {
		        $btn=esc_html__('Get my location ssl must be activated','store_locator');
		        $display = 'style="display:none;"';
		    }
		    $PublicIP = $this->get_client_ip(); 
            $json  = @file_get_contents("https://freegeoip.net/json/$PublicIP",null,null);
            $json  =  json_decode($json ,true);
            if(!empty($json)){
	            $country =  $json['country_name'];
	            $region= $json['region_name'];
	            $city = $json['city'];
	            $addressUser=$city.' '.$region.' '.$country;
	        }else{
	            $addressUser='';//'California, United States';
	        }
		    $atts['city'] = isset($map_landing_address['city']) ? $map_landing_address['city'] : '';
		    $atts['state'] = isset($map_landing_address['country']) ? $map_landing_address['country'] : '';
		    $atts['location']=isset($map_landing_address['address']) ? $map_landing_address['address'] : '';
		    $atts['radius'] = isset($map_landing_address['radius']) ? $map_landing_address['radius'] : $default_radius;
		    $state = (!empty($atts['state'])) ? ', ' . $atts['state']  : '';
		    $address = (!empty($addressUser))? $addressUser : $atts['location'] .' '. $atts['city'] . $state;
		    ?>
		    <div class="wp_store_locator_multiplemaps fullscreenMap" >
		    <script type='text/javascript' src='<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/js/fullscreen.js'; ?>'></script>
		    <script>
		        var store_locator_map_options  =  <?php echo json_encode($map_options); ?>;
		        var store_locator_grid_options =  <?php echo json_encode($grid_options); ?>;
		        setTimeout(function() {
		            wpmsl_update_map('<?php echo $address ?>','<?php echo $atts['radius']?>');
		            jQuery('#store_locatore_search_input').val('<?php echo $address?>');
		          
		        },300);
		    </script>
		    <?php
		    $map_width = 'width:100%;';
			$map_height='height: 100%;';
		    ?>
		    <div class="ob_stor-relocator" id="store-locator-id" style="<?php echo $map_height . $map_width?>">
				<input id="store_locatore_map_id" name="store_locatore_map_id" type="hidden" value="<?php echo esc_attr($map_id); ?>">
		        <input id="store_locatore_direction_Addr" name="store_locatore_direction_Addr" type="hidden" value="<?php echo esc_attr($address); ?>">
                <input id="store_locatore_search_lat" name="store_locatore_search_lat" type="hidden" value="<?php echo isset($map_landing_address['lng']) ? $map_landing_address['lat'] : ''; ?>">
                <input id="store_locatore_search_lng" name="store_locatore_search_lng" type="hidden" value="<?php echo isset($map_landing_address['lng']) ? $map_landing_address['lng'] : ''; ?>">
		        <?php
		        echo do_action('wpmsl_before_map');?>
		        <div class="loader"><div>
		        	<?php if(!empty($map_options['default_search'])){ ?>
                    <!-- search options-->       
	                <?php  $this->search_window($map_id, $map_options, $grid_options, $placeholder_settings); ?>          
		            <?php }   // searchbox ends ?>
		         <div class="col-right right-sidebar">
		        <div class="wp_multi_store_locator_search_container">
	                <div class="wp_multi_store_locator_map_list_item store-locator-item" style="display: none;">
	                </div>
	                <div class="wp_multi_store_locator_map">
	                	<input type="hidden" value="<?php echo $map_id; ?>" id="wp_multi_store_locator_map_id">
	                    <div id="map-container" style="height: 100vh"  class="">
	                      <div id="store_locatore_search_results" ></div>
	                    </div>
	                </div><!-- wp_multi_store_locator_map -->
	   	            </div><!-- wp_multi_store_locator_search_container -->
		            <div class="wp_multi_store_locator_map_list_item_mobile" style="display: none;">
					</div>
		            <div class="map-listings ">
		            </div>
		        </div>
			    </div>
	            <script>
	                jQuery( ".ob_stor-relocator" ).addClass( "full_width_div" );
		                jQuery( ".loader" ).append( '<img class="load-img" src="<?php echo apply_filters('wpmsl_loading_img',STORE_LOCATOR_PLUGIN_URL.'assets/img/loader.gif'); ?>" width="350" height="350" >' );
		                jQuery( ".ob_stor-relocator" ).append( '<div class="overlay-store"></div>' );
		                jQuery(document).ready(function () {
		                    jQuery( ".closesidebar" ).click(function() {
		                        jQuery( '.leftsidebar' ).toggleClass( "slide-left" );
		                        jQuery( this ).toggleClass( "arrow_right" );
		                    });
		                });
	            </script>
			        </div>
			    </div>
			</div>
		    <?php
		    //do_action('wpmsl_end_shortcode',$address,$atts['radius']);
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}
		public function get_client_ip() {
            $ipaddress = '';
            if (isset($_SERVER['HTTP_CLIENT_IP']))
                $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
            else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
                $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            else if(isset($_SERVER['HTTP_X_FORWARDED']))
                $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
            else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
                $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
            else if(isset($_SERVER['HTTP_FORWARDED']))
                $ipaddress = $_SERVER['HTTP_FORWARDED'];
            else if(isset($_SERVER['REMOTE_ADDR']))
                $ipaddress = $_SERVER['REMOTE_ADDR'];
            else
                $ipaddress = 'UNKNOWN';
            return $ipaddress;
        }
		public function make_search_request_maps_callback(){
			global $wpdb;
			$map_id   = isset($_POST['map_id']) ? $_POST['map_id'] : '';
		    $map_options = get_post_meta($map_id,'store_locator_map',true);
			if(isset($map_options['global']) && $map_options['global']=='yes'){
				 $map_options = get_option('store_locator_map',true);
			}
			$grid_options = get_post_meta($map_id,'store_locator_grid',true);
			if(isset($grid_options['global']) && $grid_options['global']=='yes'){
				 $grid_options = get_option('store_locator_grid',true);
			}
			$placeholder = get_post_meta($map_id,'placeholder_settings',true);
	        if(isset($placeholder['global']) && $placeholder['global']=='yes'){
	            $placeholder = get_option('placeholder_settings',true);
			}
			
		    $center_lat   = $_POST['lat'];
		    $center_lng   = $_POST['lng'];
		    $cat_ids = isset($_POST['cat_ids']) ? explode(',', $_POST['cat_ids']) : '';
			$unit         = ( strtolower($map_options['unit']) == 'km') ? 'km' : 'mile';
		    $term_ids=array();
			if ( $terms = get_the_terms($map_id, 'store_locator_category' ) ) {
			    $term_ids = wp_list_pluck( $terms, 'term_id' );
			}
			$cat_posted= !empty($_POST['store_locator_category']) ? absint($_POST['store_locator_category']) : '';
			if (($catkey = array_search($cat_posted, $term_ids)) !== false) {
			   $term_ids=array($cat_posted);
			}
		    $stores       = array();
		    $radius= (isset($_POST["store_locatore_search_radius"])) ? absint($_POST["store_locatore_search_radius"]) : "50";
		        // Check if we need to filter the results by category.
		    $cat_filter =$list_store= '';
		    $locations=array();
		    $dir_type=isset($map_options['direction']) ? $map_options['direction'] : 'detail';
			$stores=get_option('wp_multi_store_locator_stores');
			$total_markers = isset($grid_options['total_markers']) && !empty($grid_options['total_markers']) ? $grid_options['total_markers'] : 0 ;
		    if(empty($stores))
		    	$stores=$this->refresh_stores();
			if ($stores) {
			    global $user_ID;
			    global $wpdb;
			    $single_options = get_option('store_locator_single');
			    $locations['center'] = array('lat' => $center_lat, 'lng' => $center_lng);
			    $listing_counter=0;
			    foreach ($stores as $store_id => $store) {
			        $meta = $store['post_meta'];
			        $cats=explode(',', $store['categories']);
			    	if(empty(array_intersect($cats,$term_ids))){
			    		continue; 
			    	}
			    	$meta = $store['post_meta'];
			    	$distance=$this->distance($center_lat,$center_lng,$meta['store_locator_lat'],$meta['store_locator_lng'],$unit);
			    	if($radius<absint($distance)){
			    		continue;
			    	}
			        $catMarker='';
			        if(isset($map_options['category_icons_or_user']) && $map_options['category_icons_or_user']==1){
			        $catsObject=get_the_terms($store_id, 'store_locator_category',true);
			            if(!empty($catsObject)){
			               foreach ($catsObject as $keyTerm => $catTerm) {
			                   $image=get_term_meta($catTerm->term_id,'catImage',true);
			                   if(!empty($image)){
			                    $catMarker=$image;
			                    break;
			                   }
			               }
			            }
			        }
					$markers_location = array(
			            'lat' => $meta['store_locator_lat'], 
			            'lng' => $meta['store_locator_lng'], 
			            'catimage'=>$catMarker, 
			            'list-item'=>$listing_counter
					);
					$infowindow = "";
					$markers_location = apply_filters('wpmsl_markers_location',$markers_location,$infowindow,$store);
					$locations['locations'][] = $markers_location;
			        $locations['terms'][]=isset($store['category']) ? $store['category'] : '';

			        // infobox creation
			        $list_store.='<div class="store-locator-item" data-store-id="'.$store_id.'" data-marker="'. ($listing_counter) .'" id="list-item-'. ($listing_counter) .'" >';
							do_action('wpmls_before_list_item',$store,$listing_counter);
		                    $list_store.='<div class="close-list-item"><span class="dashicons dashicons-no-alt"></span></div>';
		                    $address = $meta['store_locator_address'];
		                    $list_store.='<div class="single-store-list-details">';
		                    $list_store.='<div class="store-list-address">';
							if( $single_options['page'] )
								$store_title = '<a href="'.get_permalink( $store_id ).'" target="_blank">' . get_the_title($store_id) . '</a>';
							else
								$store_title =  get_the_title($store_id);
		                    $list_store.='<input type="hidden" id="pano-address-'.$store_id.'" class="pano-address" value="'.$address.'" />';
		                    $list_content = '<div class="wpsl-name">' . $store_title . '</div>';
		                    $list_content .='<div class="wpsl-distance">'.number_format($distance, 2) . ' '.$unit.'</div>';
		                    if(has_post_thumbnail($store['ID']))
				            {
				            	$list_content .= '<div class="wpsl-image">'.get_the_post_thumbnail($store['ID'], 'post-thumbnail', '' ).'</div>';
				            }
		                    $list_content .='<div class="wpsl-address">'. $address . '</div>';
		                    $list_content .='<div class="wpsl-city">'.$meta['store_locator_city']. ', ' .$meta['store_locator_state'] . ' ' . $meta['store_locator_zipcode'].'</div>';
							if(!empty($meta['store_locator_phone'])){
								$list_content.= '<div class="wpsl-phone"> <a href="tel:'.$meta['store_locator_phone'].'">'.$meta['store_locator_phone'].'</a> </div>';
							}
							$list_store.= apply_filters('wpmsl_list_item',$list_content,$store,$unit);
							ob_start();
		                    do_action('wpmsl_listing_list_item',$store);
		                    $list_store.=ob_get_clean();
							$weblink = $meta['store_locator_website'];
							if(!empty($weblink)){
								
							  $list_store.='<div class="wpsl-wesite-link"><a href="'.$weblink.'" target="_blank">'.esc_html__(((isset($placeholder['visit_website']) && !empty($placeholder['visit_website'])) ? $placeholder['visit_website'] : __("Visit Website","store_locator"))).'</a></div>';
							}
							$list_store.='<div class="store_days_time">';
							if(isset($meta['store_locator_days']) && !empty(isset($meta['store_locator_days']))){
								$store_locator_days = unserialize($meta['store_locator_days']);
							
								$schedule='';
								foreach( $store_locator_days as $key => $value ) {
									if( !empty($value['start']) ) {
										$schedule.='<p class="days"><b>'.$key.'</b></p>';
										foreach( $value as $k => $v ) {
											if($k == 'start')
											$schedule.='<p class="time"><i class="fa fa-clock-o" aria-hidden="true"></i> ' . $v;
											else if($k == 'end')
											$schedule.=' - ' . $v . '</p>';
										}
									}
								}
								$list_store.=$schedule;
							}
							
		                    $list_store.='</div>';
							$list_store.='</div>';
							$list_store.=$this->get_direction($meta, $dir_type);
							$list_store.='</div>';
		                    ob_start();
							do_action('wpmls_after_list_item',$store);
							$list_store.=ob_get_clean();
		                $list_store.='</div>';
					$listing_counter++;
					if($listing_counter == $total_markers){
						break;
					}
			    }
			}

			$locations['unit']=$unit;
			$locations['radius']=absint($radius);
			$locations['zoomlevel']=(isset($map_options['zoomlevel']) && !empty($map_options['zoomlevel']) ? absint($map_options['zoomlevel']) : 0);
			$locations['markerzoom']=(isset($map_options['markerzoom']) && !empty($map_options['markerzoom']) ? absint($map_options['markerzoom']) : 0);
			$locations['fit_screen']=(isset($map_options['fit_screen']) && !empty($map_options['fit_screen']) ? $map_options['fit_screen'] : 0);
			$locations['fill_radius']=(isset($map_options['fill_radius']) && !empty($map_options['fill_radius']) ? $map_options['fill_radius'] : 0);
			$locations['fill_color']=(isset($map_options['fill_color']) && !empty($map_options['fill_color']) ? $map_options['fill_color'] : '#ccc');
			$locations['mappoi']=(isset($map_options['mappoi']) && !empty($map_options['mappoi']) ? $map_options['mappoi'] : 0);

			if(!isset($_POST['store_locatore_search_lat']) && $locations['markerzoom'] > 0 && $locations['markerzoom'] < 17){
				$locations['zoomlevel']=$locations['markerzoom'];
			}

			if(empty($locations['locations'])){
				 $locations= array('center' => array('lat' => $center_lat, 'lng' => $center_lng), 'locations' => array());
			}
			?>
			<!-- Show Map -->
			<?php $width = '100%'; ?>
				<div id="store_locatore_search_map"  style="height: 100vh;width: <?php echo $width?>;position:absolute;" class=""></div>
			    <script>
			    	setTimeout(function(){
			        var locations = <?php echo json_encode($locations); ?>;
			        store_locator_map_initialize(locations);
			    },1000);
			    </script>
			<?php do_action('store_locations',$locations); ?>
			<!-- Show Grid -->
			<?php if (true): ?>
		        <div class="store-locator-item-container">
		            <div class="wpsl-list-title"><?php _e('Store List','store_locator');?></div>
		                <?php
		                if($stores){
		                
		                echo $list_store;
		                }else{
		                    echo "<tr><td style='text-align: center;' colspan='".count($columns)."'><div class='store-locator-not-found'><i class='fa fa-map-marker' aria-hidden='true'></i><p>". apply_filters('wpmsl_no_stores_found',__('No Store found','store_locator')) ."</p></div>" ."</td></tr>";
		                }
		                ?>
		        </div>
			    </div>
			<?php endif; ?>
			<?php
			die();
		}
		public function regular_multiple_map($map_id){
		ob_start();
		    // Attributes
    	$map_landing_address=get_post_meta($map_id,'map_landing_address',true);
        if(isset($map_landing_address['global']) && $map_landing_address['global']=='yes'){
            $map_landing_address=get_option('map_landing_address',true);
        }
        $placeholder_setting = get_post_meta($map_id,'placeholder_settings',true);
        if(isset($placeholder_setting['global']) && $placeholder_setting['global']=='yes'){
            $placeholder_setting = get_option('placeholder_settings',true);
        }
        $map_options = get_post_meta($map_id,'store_locator_map',true);
        if(isset($map_options['global']) && $map_options['global']=='yes'){
            $map_options = get_option('store_locator_map',true);
        }
        $grid_options = get_post_meta($map_id,'store_locator_grid',true);
        if(isset($grid_options['global']) && $grid_options['global']=='yes'){
            $grid_options = get_option('store_locator_grid',true);
        }
		    $radius = ($map_options['radius'])?explode(",",trim($map_options['radius'])):false;
		    $tag = isset($map_options['tag']) ? $map_options['tag'] : '';
		    $category = isset($map_options['category']) ? $map_options['category'] : '';

		    $map_options['marker1'] = STORE_LOCATOR_PLUGIN_URL . "assets/img/" . ((isset($map_options['marker1']) && !empty($map_options['marker1'])) ? $map_options['marker1'] : "blue.png");
		    $map_options['marker2'] = STORE_LOCATOR_PLUGIN_URL . "assets/img/" . ((isset($map_options['marker2']) && !empty($map_options['marker2'])) ? $map_options['marker2'] : "red.png");

		    if(!empty($map_options['marker1_custom'])) {
		        $map_options['marker1'] = $map_options['marker1_custom'];
		    }

		    if(!empty($map_options['marker2_custom'])) {
		        $map_options['marker2'] = $map_options['marker2_custom'];
		    }

		    $default_radius = 50;
		    if(!empty($map_options['radius'])){
		    preg_match("/^(.*\[)(.*)(\])/", $map_options['radius'], $find);
		    $default_radius = trim($find[2]);
		    }
		   //$atts['radius']=$default_radius;
		    if (is_ssl()) {
				
				if(isset($placeholder_setting['get_location_btn_txt'])){
		         $btn = $placeholder_setting['get_location_btn_txt'];
				}
				
		        if(empty($btn));
		            $btn=__('Get my location','store_locator');
		        $display = '';
		    } else {
		        $btn=__('Get my location ssl must be activated','store_locator');
		        $display = 'style="display:none;"';
		    }
		    $city=isset($map_landing_address['city']) ? $map_landing_address['city'] : '';
		    $state = isset($map_landing_address['state']) ? $map_landing_address['state'] : '';
		    $location = isset($map_landing_address['location']) ? $map_landing_address['location'] : '';
		    $address = $location .' '. $city . $state;
		    $terms = wp_get_post_terms($map_id, 'store_locator_category', array() ); 
		    ?>
		    <div class="regularMap" id="regularMap">
		    	<input type="hidden" value="<?php echo $map_id; ?>" id="wp_multi_store_locator_map_id">
		    <script>
		        var store_locator_map_options  =  <?php echo json_encode($map_options); ?>;
		        var store_locator_grid_options =  <?php echo json_encode($grid_options); ?>;
		        var placeholder_location =  <?php echo json_encode($placeholder_setting['location_not_found']); ?>;
		        setTimeout(function() {
		            wpmsl_update_map('<?php echo $address ?>','<?php echo $default_radius; ?>','<?php echo $map_id; ?>');
		            jQuery('#store_locatore_search_input').val('<?php echo $address?>');
		          
		        },1000);
		    </script>
		    <script type='text/javascript' src='<?php echo STORE_LOCATOR_PLUGIN_URL . '/assets/js/regular_script.js'; ?>'></script>
		    <?php
		    $map_height = 'height:774px;';
		    if(isset($map_options['height']) && !empty($map_options['height'])) {
		        $map_height = 'height:' . $map_options['height'].$map_options['heightunit'].';'; 
		    }
		    $map_width = 'width:100%;';
		    if(isset($map_options['width']) && !empty($map_options['width'])) {
		        $map_width = 'width:' . $map_options['width'].$map_options['widthunit'].';'; 
		    } ?>
		    <div class="row ob_stor-relocator" id="store-locator-id" style="<?php echo $map_height . $map_width?>">
	            <input id="store_locatore_search_lat" name="store_locatore_search_lat" type="hidden" value="<?php echo isset($map_landing_address['lng']) ? $map_landing_address['lat'] : ''; ?>">
	            <input id="store_locatore_search_lng" name="store_locatore_search_lng" type="hidden" value="<?php echo isset($map_landing_address['lng']) ? $map_landing_address['lng'] : ''; ?>">
		        <?php
		        echo do_action('wpmsl_before_map');?>
		        <div class="loader">
		        <div>
		            <?php $placeholder_settings = get_option('placeholder_settings');  ?>
		                <?php if(!empty($map_options['default_search'])){ ?>
		                    <?php  $this->search_window($map_id, $map_options, $grid_options, $placeholder_settings); ?>  
		                <?php }?>
		                <div class="col-right right-sidebar">
		                	<!-- <div id="map-container" style="position: relative;width: 100%;right: 0%;" class="<?php //echo @$grid_options['listing_position']?>"> -->
		                    <div id="map-container" style="position: relative;width: 100%;right: 0%;">
		                    <div id="store_locatore_search_results"></div>
		                    </div>
		                    <?php 
		                    if( !empty($grid_options['enable'] ) && isset($grid_options['listing_position']) && $grid_options['listing_position']!='below_map') {                       
		                    ?>
	                        <div class="map-listings <?php echo $grid_options['listing_position']?>" style="height: <?php echo $map_options['height']?>px">
	                        </div>
		                    <?php } ?>      
		                </div>
		            </div>
		        </div>
            <script>
            	jQuery( ".ob_stor-relocator" ).addClass( "full_width_div" );
                jQuery( ".loader" ).append( '<img class="load-img" src="<?php echo apply_filters('wpmsl_loading_img',STORE_LOCATOR_PLUGIN_URL.'assets/img/loader.gif'); ?>" width="350" height="350" >' );
                jQuery( ".ob_stor-relocator" ).append( '<div class="overlay-store"></div>' );
                jQuery(document).ready(function () {
                    jQuery( ".closesidebar" ).click(function() {
                        jQuery( '.leftsidebar' ).toggleClass( "slide-left" );
                        jQuery( this ).toggleClass( "arrow_right" );
                    });
                });
            </script>
        </div> 
        	<?php if( !empty( $grid_options['enable'] ) && isset($grid_options['listing_position']) && $grid_options['listing_position']=='below_map') { ?>
                    <div class="map-listings <?php echo $grid_options['listing_position']?>" style="height: <?php echo $map_options['height']?>px">
                    </div>
                <?php } ?>   
    </div>
		    <?php
		    do_action('wpmsl_end_shortcode',$address,$default_radius);
		    return ob_get_clean();
		}
		public function make_search_request_maps_regular_callback(){
			global $wpdb;
		    $map_id = isset($_POST['map_id']) ? absint($_POST['map_id']) : '';
		    if(empty($map_id))
		    	wp_send_json_error( __('Oops! something went wrong','store_locator'));

		    $map_options = get_post_meta($map_id,'store_locator_map',true);
			if(isset($map_options['global']) && $map_options['global']=='yes'){
				 $map_options = get_option('store_locator_map',true);
			}
			$grid_options = get_post_meta($map_id,'store_locator_grid',true);
			if(isset($grid_options['global']) && $grid_options['global']=='yes'){
				 $grid_options = get_option('store_locator_grid',true);
			}
			$center_lat   = $_POST['lat'];
		    $center_lng   = $_POST['lng'];
		    $cat_ids = isset($_POST['cat_ids']) ? explode(',', $_POST['cat_ids']) : '';
		    $unit         = ( $map_options['unit'] == 'km' ) ? 'km' : 'mile';
		    $term_ids=array();
			if ( $terms = get_the_terms($map_id, 'store_locator_category' ) ) {
			    $term_ids = wp_list_pluck( $terms, 'term_id' );
			}
			$cat_posted= !empty($_POST['store_locator_category']) ? absint($_POST['store_locator_category']) : '';
			if (($catkey = array_search($cat_posted, $term_ids)) !== false) {
			   $term_ids=array($cat_posted);
			}
			
		    $stores       = array();
		    $radius= (isset($_POST["store_locatore_search_radius"])) ? absint($_POST["store_locatore_search_radius"]) : "50";
		        // Check if we need to filter the results by category.
		    $cat_filter =$list_store= '';
		    $locations=array();
		    $stores=get_option('wp_multi_store_locator_stores');
		    if(empty($stores))
		    	$stores=$this->refresh_stores();
		    $counter = 0;
			if ($stores) {
			    global $user_ID;
			    global $wpdb;
			    $single_options = get_option('store_locator_single');
			    $locations['center'] = array('lat' => $center_lat, 'lng' => $center_lng);
			    $APKI_KEY = get_option('store_locator_street_API_KEY');
			    foreach ($stores as $store_id => $store) {
			    	$cats=explode(',', $store['categories']);
			    	if(empty(array_intersect($cats,$term_ids))){
			    		continue; 
			    	}
			        $meta = $store['post_meta'];
			        $distance=$this->distance($center_lat,$center_lng,$meta['store_locator_lat'],$meta['store_locator_lng'],$unit);
			    	if($radius<absint($distance)){
			    		continue;
			    	}
			        $options = $map_options;
			        $radius_unit = $options['unit'];
			        $infowindow_content = $this->info_window_content($map_id,$store_id,$options['infowindow']);			        
			        $infowindow = '<div class="store-infowindow">';
			        $infowindow_content .= '<div><a class="wpsl-zoom-to" data-direction="'.$meta['store_locator_lat'].','.$meta['store_locator_lng'].'">'.__('Zoom','store_locator').'</a>';
			        $infowindow_content .= '<a class="store-direction" data-direction="'.$meta['store_locator_lat'].','.$meta['store_locator_lng'].'">'.__('Directions','store_locator').'</a></div>';
			        $infowindow .= apply_filters('wpmsl_infowindow_content',$infowindow_content,$store);
			        $infowindow .= '</div>';
			        $catMarker='';
			        if(isset($map_options['global']) && $map_options['global']=='no' && isset($map_options['category_icons_or_user']) && $map_options['category_icons_or_user']==1){
			        $catsObject=get_the_terms($store_id, 'store_locator_category',true);
			            if(!empty($catsObject)){
			               foreach ($catsObject as $keyTerm => $catTerm) {
			                   $image=get_term_meta($catTerm->term_id,'catImage',true);
			                   if(!empty($image)){
			                    $catMarker=$image;
			                    break;
			                   }
			               }
			            }
					}
					
					
			        $markers_location = array(
			            'lat' => $meta['store_locator_lat'], 
			            'lng' => $meta['store_locator_lng'], 
			            'infowindow' => $infowindow,
			            'catimage'=>$catMarker
			            );
			        $markers_location = apply_filters('wpmsl_markers_location',$markers_location,$infowindow,$store);
			        $locations['locations'][] = $markers_location;
			        $locations['terms'][]=isset($store['category']) ? $store['category'] : '';

			        // listings 
			        $cat_term_id= isset($store['category']) ? $store['category'] : '';
                    $list_store.= '<div class="store-locator-item '.$accordion.' rel_cat_'.$cat_term_id.'" data-store-id="'.$store_id.'" data-category_id="'.$cat_term_id.'" data-marker="'. ($counter-1) .'" id="list-item-'. ($counter-1) .'" >';
                    ob_start();
                    do_action('wpmls_before_list_item',$store,$listing_counter);
                    $list_store.=ob_get_clean();
                    $listing_counter++;
                    $address = $met['store_locator_address'];
                    $city = $met['store_locator_city'];
                    $country = $met['store_locator_country'];
                    $list_store.='<div class="store-list-details">';
                    $list_store.='<div class="store-list-address">';
                    if( $single_options['page'] )
                        $store_title = '<a href="'.get_permalink( $store_id ).'" target="_blank">' . get_the_title($store_id) . '</a>';
                    else
                        $store_title =  get_the_title($store_id);
                    $list_store.='<input type="hidden" id="pano-address-'.$store_id.'" class="pano-address" value="'.$address.'" />';
                    $list_content = '<div class="wpsl-address" style="display: none;">'.$address.' '.$city.' '.$country.'</div>';
                    $list_content .= '<div class="wpsl-name">' .'<em class="fa fa-map-marker"></em> '.$store_title . '</div>';
                    $list_content .= '<div class="wpsl-distance">'.number_format($distance, 2) . ' '.$radius_unit.'</div>';
                    $list_store.= apply_filters('wpmsl_list_item',$list_content,$store,$radius_unit);
                    ob_start();
                    do_action('wpmsl_listing_list_item',$store);
                    $list_store.=ob_get_clean();
                    $list_store.='</div>';
                    $list_store.='</div>';
                    $index++;
                    ob_start();
                    do_action('wpmls_after_list_item',$store);
                    $list_store.=ob_get_clean();
	                $list_store.='</div>';
			        $counter++;
			    }
			} 
			if(empty($locations['locations'])){
				 $locations= array('center' => array('lat' => $center_lat, 'lng' => $center_lng), 'locations' => array());
			}
			$width = '100%';
			if ($map_options['enable']): $map_options['enable'];?>
			    <div id="store_locatore_search_map" style="height: <?php echo $map_options['height'] . $map_options['heightunit']; ?>;width: <?php echo $width?>;position:absolute" class="<?php echo $map_options['listing_position']?>"></div>
			    <script>
			        var locations = <?php echo json_encode($locations); ?>;
			        store_locator_map_initialize(locations);
			    </script>
			<?php
			    do_action('store_locations',$locations);
			 endif; ?>
			 <!-- Show Grid -->
			<?php if ($grid_options['enable']): ?>
		        <div class="store-locator-item-container">

		            <div class="direction-toggle-addresses" style="display: none;">
		                <form role="form">
		        			<div class="form-group">
		        				<label><?php _e('Directions','store_locator'); ?></label>
		        				<input type="text" class="" id="routeStart" value="">
		        			</div>
		        			<div class="form-group">
		        				<div class="input-group routeEnd-btn">
		        					<input type="text" class="" id="routeEnd">
		        					<span class="">
		        						<button class="" type="button" id="get-directions" title="Get directions"><span class="dashicons dashicons-search"></span></button>
		        					</span>
		        				</div>
		        			</div>
		        		</form>
		            </div>
		            <div id="directionsPanel" class="panel" style="">
		             </div>
		            <div class="current-location-zoom">
		                <div class="current-location-holder">
		                <span class="cross-icon-holder"><i class="fa fa-crosshairs"></i></span>
		                <?php _e('Locations','store_locator'); ?>
		                </div>
		                <div class="regular_map_filters">
		        		<div class="direction-filter regular-filter">
	                         <span class="dashicons dashicons-location-alt"></span>
	                         <span class="tooltiptext tooltip-bottom"><?php _e('Directions','store_locator'); ?></span>
                        </div>
		        		<div class="mile-km-btns-switcher">
							<label class="btn-km-mile <?php echo (isset($map_options['unit']) && $map_options['unit']=='km') ? 'active' :''; ?>">
								<input type="radio" name="unit" id="unit-km" value="Km"> km
							</label>
							<label class="btn-km-mile <?php echo (isset($map_options['unit']) && $map_options['unit']=='mile') ? 'active' :''; ?>">
								<input type="radio" name="unit" id="unit-mi" value="mile"> mi
							</label>
						</div>
		        	</div>
		            </div>
	                <?php
	                if($stores){
	                echo $list_store;
	                }else{
	                    echo "<tr><td style='text-align: center;' colspan='".count($columns)."'><div class='store-locator-not-found'><i class='fa fa-map-marker' aria-hidden='true'></i><p>". apply_filters('wpmsl_no_stores_found',__('No Store found','store_locator')) ."</p></div>" ."</td></tr>";
	                }
	            ?>
		        </div>
		    </div>
			<?php endif;
			die();
		}
		public function custom_multiple_map($map_id){
			//wp_enqueue_script('store_frontend_map');
		    ob_start();
		    $map_landing_address=get_post_meta($map_id,'map_landing_address',true);
	        if(isset($map_landing_address['global']) && $map_landing_address['global']=='yes'){
	            $map_landing_address=get_option('map_landing_address',true);
	        }
	        $placeholder_setting = get_post_meta($map_id,'placeholder_settings',true);
	        if(isset($placeholder_setting['global']) && $placeholder_setting['global']=='yes'){
	            $placeholder_setting = get_option('placeholder_settings',true);
	        }
	        $map_options = get_post_meta($map_id,'store_locator_map',true);
	        if(isset($map_options['global']) && $map_options['global']=='yes'){
	            $map_options = get_option('store_locator_map',true);
	        }
	        $grid_options = get_post_meta($map_id,'store_locator_grid',true);
	        if(isset($grid_options['global']) && $grid_options['global']=='yes'){
	            $grid_options = get_option('store_locator_grid',true);
	        }
		    $radius = ($map_options['radius'])?explode(",",trim($map_options['radius'])):false;
		    $tag = isset($map_options['tag']) ? $map_options['tag'] : '';
		    $category = isset($map_options['category']) ? $map_options['category'] : '';
		    $map_options['marker1'] = STORE_LOCATOR_PLUGIN_URL . "assets/img/" . ((isset($map_options['marker1']) && !empty($map_options['marker1'])) ? $map_options['marker1'] : "blue.png");
		    $map_options['marker2'] = STORE_LOCATOR_PLUGIN_URL . "assets/img/" . ((isset($map_options['marker2']) && !empty($map_options['marker2'])) ? $map_options['marker2'] : "red.png");
		    if(!empty($map_options['marker1_custom'])) {
		        $map_options['marker1'] = $map_options['marker1_custom'];
		    }
		    if(!empty($map_options['marker2_custom'])) {
		        $map_options['marker2'] = $map_options['marker2_custom'];
		    }
		    $default_radius = 50;
		    if(isset($map_options['radius'])){
			    preg_match("/^(.*\[)(.*)(\])/", $map_options['radius'], $find);
			    $default_radius = trim($find[2]);
		    }
		    // Attributes
		    $atts['location'] = isset($map_landing_address['address']) ? $map_landing_address['address'] : 'United States';
		    $atts['radius'] = $default_radius;
		    $atts['city'] = isset($map_landing_address['city']) ? $map_landing_address['city'] : '';
		    $atts['state'] = isset($map_landing_address['country']) ? $map_landing_address['country'] : '';
		     if (is_ssl()) {
				 
				if(isset($placeholder_setting['get_location_btn_txt'])){
		          $btn = $placeholder_setting['get_location_btn_txt'];
				}
				
				if(empty($btn)){
		            $btn=esc_html__('Get my location','store_locator');
		        } 
		        $display = 'style="display:block;"';
		    } else {
		        $btn=esc_html__('Get my location ssl must be activated','store_locator');
		        $display = 'style="display:none;"';
		    }
		    $state = (!empty($atts['state'])) ? ', ' . $atts['state']  : '';
		    $address = $atts['location'] .' '. $atts['city'] . $state;
		    ?>
		    <div class="custom">
		    <script>
		        var store_locator_map_options  =  <?php echo json_encode($map_options); ?>;
		        var store_locator_grid_options =  <?php echo json_encode($grid_options); ?>;
		        var placeholder_location =  '<?php echo @json_encode($placeholder_setting['location_not_found']); ?>';
		        setTimeout(function() {
		            wpmsl_update_map('<?php echo $address ?>','<?php echo $atts['radius']?>');
		            jQuery('#store_locatore_search_input').val('<?php echo $address?>');
		            jQuery('#store_locatore_search_radius option[value="<?php echo $atts['radius']?>"]').prop('selected', true);
		            if (jQuery.fn.niceSelect) {  
		                jQuery('#store_locatore_search_radius').niceSelect('update'); 
		            }
		        },1000);
		    </script>
		    <script type='text/javascript' src='<?php echo STORE_LOCATOR_PLUGIN_URL . '/assets/js/custom_script.js'; ?>'></script>
		    <?php
		    $map_height = 'height:774px;';
		    if(isset($map_options['height']) && !empty($map_options['height'])) {
		        $map_height = 'height:' . $map_options['height'].$map_options['heightunit'].';'; 
		    }
		    $map_width = 'width:100%;';
		    if(isset($map_options['width']) && !empty($map_options['width'])) {
		        $map_width = 'width:' . $map_options['width'].$map_options['widthunit'].';'; 
		    }
		    ?>
		    <div class="row ob_stor-relocator" id="store-locator-id" style="<?php echo $map_height . $map_width?>">
		           <?php $map_landing_address=get_option('map_landing_address') ?>
		           		<input id="store_locatore_map_id" name="store_locatore_map_id" type="hidden" value="<?php echo esc_attr($map_id); ?>">
		           		<input id="store_locatore_direction_Addr" name="store_locatore_direction_Addr" type="hidden" value="<?php echo esc_attr($address); ?>">
		                <input id="store_locatore_search_lat" name="store_locatore_search_lat" type="hidden" value="<?php echo isset($map_landing_address['lng']) ? $map_landing_address['lat'] : ''; ?>">
		                <input id="store_locatore_search_lng" name="store_locatore_search_lng" type="hidden" value="<?php echo isset($map_landing_address['lng']) ? $map_landing_address['lng'] : ''; ?>">
		        <?php
		        echo do_action('wpmsl_before_map');?>
		        <div class="loader"><div>
		            <?php  ?>
		                <?php if(!empty($map_options['default_search'])){ ?>
		                    <?php  $this->search_window($map_id, $map_options,$grid_options, $placeholder_setting); ?>  
		                <?php }?>
		                <div class="col-right right-sidebar">
		                	<!-- <div id="map-container" style="position: relative;width: 100%;right: 0%;" class="<?php //echo @$grid_options['listing_position']?>"> -->
		                    <div id="map-container" style="position: relative;width: 100%;right: 0%;">
		                    <div id="store_locatore_search_results"></div>
		                    </div>
		                    <?php 
		                    if( !empty($grid_options['enable'] ) && isset($grid_options['listing_position']) && $grid_options['listing_position']!='below_map') {   
							$listings = $map_options['height'];
		                    ?>
	                        <div class="map-listings <?php echo $grid_options['listing_position']?>" style="height: <?php echo $listings;?>px">
	                        </div>
		                    <?php } ?>      
		                </div>
		            </div>
		            <script>
		                // adding class in content div
		                jQuery( ".ob_stor-relocator" ).addClass( "full_width_div" );
		                jQuery( ".loader" ).append( '<img class="load-img" src="<?php echo apply_filters('wpmsl_loading_img',STORE_LOCATOR_PLUGIN_URL.'assets/img/loader.gif'); ?>" width="350" height="350" >' );
		                jQuery( ".ob_stor-relocator" ).append( '<div class="overlay-store"></div>' );
		                jQuery(document).ready(function () {
		                    jQuery( ".closesidebar" ).click(function() {
		                        jQuery( '.leftsidebar' ).toggleClass( "slide-left" );
		                        jQuery( this ).toggleClass( "arrow_right" );
		                    });
		                });
		            </script>
		        </div></div>
		          <?php if( !empty( $grid_options['enable'] ) && isset($grid_options['listing_position']) && $grid_options['listing_position']=='below_map') { ?>
                    <div class="map-listings <?php echo $grid_options['listing_position']?>" style="height: <?php echo $map_options['height']?>px">
                    </div>
                <?php } ?>   
            </div>
		    <?php
		    do_action('wpmsl_end_shortcode',$address,$atts['radius']);
			
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}
		public function make_search_request_custom_maps_callback() {
			global $wpdb;
		    $map_id = isset($_POST['map_id']) ? absint($_POST['map_id']) : '';
		    if(empty($map_id))
		    	wp_send_json_error( __('Oops! something went wrong','store_locator'));
		    $radius= (isset($_POST["store_locatore_search_radius"])) ? absint($_POST["store_locatore_search_radius"]) : "50";
			$map_options = get_post_meta($map_id,'store_locator_map',true);
			
			if(isset($map_options['global']) && $map_options['global']=='yes'){
				 $map_options = get_option('store_locator_map',true);
			}

			$placeholder_setting = get_post_meta($map_id,'placeholder_settings',true);
	        if(isset($placeholder_setting['global']) && $placeholder_setting['global']=='yes'){
	            $placeholder_setting = get_option('placeholder_settings',true);
	        }


			$grid_options = get_post_meta($map_id,'store_locator_grid',true);
			if(isset($grid_options['global']) && $grid_options['global']=='yes'){
				 $grid_options = get_option('store_locator_grid',true);
			}
			$term_ids=array();
			if ( $terms = get_the_terms($map_id, 'store_locator_category' ) ) {
			    $term_ids = wp_list_pluck( $terms, 'term_id' );
			}
			$cat_posted= !empty($_POST['store_locator_category']) ? absint($_POST['store_locator_category']) : '';
			if (($catkey = array_search($cat_posted, $term_ids)) !== false) {
			   $term_ids=array($cat_posted);
			}
			$unit         = ( strtolower($map_options['unit']) == 'km') ? 'km' : 'mile';
			
		    $center_lat   = isset($_POST['lat']) ? $_POST['lat'] : '';
		    $center_lng   = isset($_POST['lng']) ? $_POST['lng'] : '';
			$stores=get_option('wp_multi_store_locator_stores');
			
			$tag_ids = array();
			if(isset($_POST['store_locator_tag']) && !empty($_POST['store_locator_tag'])){
				$tag_ids=$_POST['store_locator_tag'];
			}
			
			$total_markers = isset($grid_options['total_markers']) && !empty($grid_options['total_markers']) ? $grid_options['total_markers'] : 0 ;
				
		    if(empty($stores) || wp_count_posts( 'store_locator' )->publish != $stores)
		    	$stores=$this->refresh_stores();
		    $locations=array();
		    if(!empty($stores)){
		    	$counter = 0;
				$map_list_items='';
			    $single_options = get_option('store_locator_single');
			    $locations['center'] = array('lat' => $center_lat, 'lng' => $center_lng);
				if(defined('ICL_LANGUAGE_CODE')){
					foreach ($stores as $store) {
						$my_post_language_details = apply_filters( 'wpml_post_language_details', NULL, $store['ID']) ;
						if(!empty($my_post_language_details['language_code']) 
							&& 
							$my_post_language_details['language_code'] == ICL_LANGUAGE_CODE){
							$filter_stores[] = $store;
						}               
					}
			    }
			    if(!empty($filter_stores) and is_array($filter_stores)){
			        $stores = $filter_stores;
			    } 
			    foreach ($stores as $store_id => $store) {
			    	$cats=explode(',', $store['categories']);
			    	if(empty(array_intersect($cats,$term_ids))){
			    		continue; 
					}

					if(!empty($tag_ids)){
					$tags=$store['tags'];
			    		if(empty(array_intersect($tags,$tag_ids))){
			    			continue; 
						}
					}
					
			    	//$radius_unit = $options['unit'];
			    	$meta = $store['post_meta'];
			    	$distance=$this->distance($center_lat,$center_lng,$meta['store_locator_lat'],$meta['store_locator_lng'],$unit);
			    	if($radius<absint($distance)){
			    		continue;
					}
					/*
					if(empty($map_options['infowindow'])){
						$map_options['infowindow'] = "<div><div>{image}</div><h3>{name}</h3><p>{address} {city}, {state} {country} {zipcode}</p><span>{phone}</span><span>{website}</span><div>";
					} */
			        $infowindow_content = $this->info_window_content($map_id,$store_id,$map_options['infowindow']);
			        $infowindow = '<div class="store-infowindow">';
			        $infowindow .= apply_filters('wpmsl_infowindow_content',$infowindow_content,$store);
					$infowindow .= '</div>';
					
					$catMarker='';
			        if(isset($map_options['global']) && $map_options['global']=='no' && isset($map_options['category_icons_or_user']) && $map_options['category_icons_or_user']==1){
						
			        $catsObject=get_the_terms($store_id, 'store_locator_category',true);
			            if(!empty($catsObject)){
			               foreach ($catsObject as $keyTerm => $catTerm) {
			                   $image=get_term_meta($catTerm->term_id,'catImage',true);
			                   if(!empty($image)){
			                    $catMarker=$image;
			                    break;
			                   }
			               }
			            }
					}
					
					$markers_location = array('lat' => $meta['store_locator_lat'], 'lng' => $meta['store_locator_lng'], 'infowindow' => $infowindow, 'catimage'=>$catMarker);
					$markers_location = apply_filters('wpmsl_markers_location',$markers_location,$infowindow,$store);
					$locations['locations'][] = $markers_location;
			        $map_list_items.=$this->custom_map_list_items($store, $counter,$map_options,$distance,$placeholder_setting);
					$counter++;
					
					if($counter == $total_markers){
						break;
					}
			    }
			}
			
			$locations['unit']=$unit;
			$locations['radius']=absint($radius);
			$locations['zoomlevel']=(isset($map_options['zoomlevel']) && !empty($map_options['zoomlevel']) ? absint($map_options['zoomlevel']) : 0);
			$locations['markerzoom']=(isset($map_options['markerzoom']) && !empty($map_options['markerzoom']) ? absint($map_options['markerzoom']) : 0);
			$locations['fit_screen']=(isset($map_options['fit_screen']) && !empty($map_options['fit_screen']) ? $map_options['fit_screen'] : 0);
			$locations['fill_radius']=(isset($map_options['fill_radius']) && !empty($map_options['fill_radius']) ? $map_options['fill_radius'] : 0);
			$locations['fill_color']=(isset($map_options['fill_color']) && !empty($map_options['fill_color']) ? $map_options['fill_color'] : '#ccc');
			$locations['mappoi']=(isset($map_options['mappoi']) && !empty($map_options['mappoi']) ? $map_options['mappoi'] : 0);

			if(!isset($_POST['store_locatore_search_lat']) && $locations['markerzoom'] > 0 && $locations['markerzoom'] < 17){
				$locations['zoomlevel']=$locations['markerzoom'];
			}
			
			if(empty($locations)){
				$locations = array('center' => array('lat' => $center_lat, 'lng' => $center_lng), 'locations' => array());
			} ?>
		    	<!-- Show Map -->
		<?php
		$width = '100%';
		
		if ($map_options['enable']): $map_options['enable']; ?>
			<div id="store_locatore_search_map" style="height: <?php echo $map_options['height'] . $map_options['heightunit']; ?>;width: <?php echo $width?>;position:absolute" class="<?php echo $grid_options['listing_position']?>">
			</div>
		    <script>
		    	setTimeout(function(){
		        var locations = <?php echo json_encode($locations); ?>;
		        store_locator_map_initialize(locations);
		    },300);
		    </script>
		<?php
			do_action('store_locations',$locations);
		else:?>
			<div id="store_locatore_search_map" style="height: <?php echo $map_options['height'] . $map_options['heightunit']; ?>;width: <?php echo $width?>;position:absolute" class="<?php echo $grid_options['listing_position']?>">
			<h3 class="map-disabled-message">This map is currently not available<h3> </div>
			<?php
		 endif; ?>
		<!-- Show Grid -->
		<?php if ($grid_options['enable']): 
            $store_list_text=((isset($placeholder_setting['store_list']) && !empty($placeholder_setting['store_list'])) ? $placeholder_setting['store_list'] : __("Locations","store_locator"));?>
		        <div class="store-locator-item-container">
		            <div class="wpsl-list-title"><?php echo $store_list_text; ?></div>
		        <?php
	                if($counter>0){
	                	ob_start();
	                	echo $map_list_items;
	                	echo ob_get_clean();
	                }else{
	                    echo "<div class='store-locator-not-found'><i class='fa fa-map-marker' aria-hidden='true'></i><p>". apply_filters('wpmsl_no_stores_found',__('No Stores found','store_locator')) ."</p></div>";
	                } 
		            ?>
		        </div>
		<?php endif; ?>
    	<?php
    	die();
		}
		public function custom_map_list_items($store,$counter,$map_options,$distance,$placeholder_setting){
			$meta=$store['post_meta'];
            $accordion = '';
            $dir_type=!empty($map_options['direction']) ? $map_options['direction'] : 'detail';
            if(isset($map_options['show_accordion']))
                $accordion = 'accordion-show';
			$list_item='';
			$list_item.='<div class="store-locator-item '.$accordion.'" data-distance="'.$distance.'" data-store-id="'.$store['ID'].'" data-marker="'. ($counter) .'" id="list-item-'. ($counter) .'" >';
			ob_start();
			do_action('wpmls_before_list_item',$store,$counter);
			$list_item.=ob_get_clean();
			$list_item.='<div class="circle-count">';
			$list_item.=apply_filters('wpmsl_list_counter',$counter+1,$store);
			$list_item.='</div>';
			$single_options = get_option('store_locator_single');
			
			$radius_unit = $map_options['unit'];
            $address = $meta['store_locator_address'];
            $list_item.='<div class="store-list-details">';
            $list_item.='<div class="store-list-address">';
			if( $single_options['page'] )
				$store_title = '<a href="'.esc_url($store['guid']).'" target="_blank">' . $store['post_title'] . '</a>';
			else
				$store_title =  $store['post_title'];
            $list_item.='<input type="hidden" id="pano-address-'.$store['ID'].'" class="pano-address" value="'.esc_attr($address).'" />';
            $list_content = '<div class="wpsl-name">' . $store_title . '</div>';
            if(has_post_thumbnail($store['ID']))
            {
            	$list_content .= '<div class="wpsl-image">'.get_the_post_thumbnail($store['ID'], 'post-thumbnail', '' ).'</div>';
            }
            $list_content .= '<div class="wpsl-distance">'.number_format($distance, 2) . ' '.$radius_unit.'</div>';
            $list_content .= '<div class="wpsl-address">'. $address . '</div>';
            $list_content .= '<div class="wpsl-city">'.$meta['store_locator_city']. ', ' .$meta['store_locator_state']. ' ' . $meta['store_locator_zipcode'] .'</div>';
			$store_locator_phone = $meta['store_locator_phone'];
			if(!empty($store_locator_phone)){
				$list_content .= '<div class="wpsl-phone"> <a href="tel:'.esc_attr($meta['store_locator_phone']).'">'.$meta['store_locator_phone'].'</a> </div>';
			}
			$list_data = apply_filters('wpmsl_list_item',$list_content,$store,$radius_unit);
			$list_item.=$list_data;
			$weblink = $meta['store_locator_website'];
			
			if(!empty($weblink)){
				$list_item.='<a href="'.$weblink.'" target="_blank">'.((isset($placeholder_setting['visit_website']) && !empty($placeholder_setting['visit_website'])) ? $placeholder_setting['visit_website'] : __("Visit Website","store_locator")).'</a> ';
            }
            $list_item.=$this->get_direction($meta, $dir_type);
			$list_item.='</div>';
			ob_start();
			do_action('wpmls_after_list_item',$store);
			$list_item.=ob_get_clean();
        	$list_item.='</div></div>';
			return $list_item;
		}
		public function load_map_iframe_template($template){
			$url_path = trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/');
			if (strpos($url_path, 'wp-multi-store-locator')!==false) {
		        global $wp_query;
		        $wp_query->is_404=false;
		        status_header(200);
		        $template=STORE_LOCATOR_PLUGIN_PATH.'templates/wp-multi-store-locator.php';
			}
			return $template;
		}
	}
	new WPMSL_Multi_Maps_Frontend();
}