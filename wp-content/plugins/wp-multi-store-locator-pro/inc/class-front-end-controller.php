<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}  
if(!class_exists('WPMSL_Maps_Frontend_Controller')){
	abstract class WPMSL_Maps_Frontend_Controller{
		public function info_window_content($map_id, $store_id, $content){
			$meta = get_post_meta($store_id);
			$single_options = get_option('store_locator_single');
			$working_hours = "";
			if(isset($meta['store_locator_days'][0]) && strpos($content,'{working_hours}') !== false){
				$metaDays = $meta['store_locator_days'][0];
		        $working_hours = "<table class='store_locator_working_hours'><tr><td colspan='3'>" . esc_html__("Working Hours", "store_locator") . "</td></tr>";
		        $metaDays = unserialize($metaDays);     
		        $days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
		        foreach ($days as $day) {
		            $working_hours .= "<tr class='".(($metaDays[$day]['status'] == "1") ?'store-locator-open':'store-locator-closed') ."'><td>" . esc_html($day) . "</td><td>" . (($metaDays[$day]['status'] == "1") ? __("Open", "store_locator") : __("Closed", "store_locator")) . "</td><td><span class='store_locator_start'>" . $metaDays[$day]['start'] . "</span><span class='store_locator_end'>" . $metaDays[$day]['end'] . "</span></td></tr>";
		        }
		        $working_hours .= "</table>";
		    }
	        if (has_post_thumbnail($store_id) ){ 
	         $images = wp_get_attachment_image_src( get_post_thumbnail_id($store_id), 'single-post-thumbnail' );
	         $image = '<div class="img-content" ><img  style="width:150px;" src="'.$images[0].'" /></div>';
	        } else {
	             $image = '';
	        }
	        if( $single_options['page'] )
	            $store_title = '<a href="'.esc_url(get_permalink($store_id)).'" target="_blank">' .esc_html(get_the_title($store_id)).'</a>';
	        else
				$store_title =  get_the_title($store_id);
				
			if(strpos($content,'{categories}') !== false){
				$store_categories=get_the_terms( $store_id, "store_locator_category");
		
				$categories="";
				foreach($store_categories as $store_category){
					$categories.= $store_category->name.", ";
				}
				$categories = trim(trim($categories," "), ",");
				$content = str_replace('{categories}',$categories,$content);
			}
	
			if(strpos($content,'{description}') !== false){					
				$content = str_replace('{description}',get_post_meta($store_id,'store_locator_description',true),$content);
			}

			
			$content = str_replace('{image}',$image,$content);
	        $content = str_replace('{name}',$store_title,$content);
	        
			$address_raw_arr = [get_post_meta($store_id,'store_locator_address',true),get_post_meta($store_id,'store_locator_city',true),get_post_meta($store_id,'store_locator_state',true),get_post_meta($store_id,'store_locator_country',true),get_post_meta($store_id,'store_locator_zipcode',true)];
			
			$address_raw = implode("+", address_raw_arr);
			
			$content = str_replace('{address}','<p class="info-window-address"><i class="fa fa-external-link-square"></i> '.get_post_meta($store_id,'store_locator_address',true),$content);
	        $content = str_replace('{city}',get_post_meta($store_id,'store_locator_city',true),$content);
	        $content = str_replace('{state}',get_post_meta($store_id,'store_locator_state',true),$content);
	        $content = str_replace('{country}',get_post_meta($store_id,'store_locator_country',true),$content);
	        $content = str_replace('{email}',get_post_meta($store_id,'store_locator_email',true),$content);
	        $content = str_replace('{zipcode}',get_post_meta($store_id,'store_locator_zipcode',true).'</p>',$content);
			
			$content = str_replace('{address_raw}', $address_raw, $content);
			
	        $content = str_replace('{phone}','<p><i class="fa fa-phone-square"></i> '.get_post_meta($store_id,'store_locator_phone',true).'</p>',$content);
			$content = str_replace('{working_hours}',$working_hours,$content); 
			$placeholder = get_post_meta($map_id,'placeholder_settings',true);
	        if(isset($placeholder['global']) && $placeholder['global']=='yes'){
	            $placeholder = get_option('placeholder_settings',true);
	        }
			$content = str_replace('{website}','<a href="'.esc_url(get_post_meta($store_id,'store_locator_website',true)).'" class="visit-website">'.esc_html__(((isset($placeholder['visit_website']) && !empty($placeholder['visit_website'])) ? $placeholder['visit_website'] : __("Visit Website","store_locator"))).'</a>',$content);
			return $content;
		}
		public function search_window($map_id, $map_options, $grid_options, $placeholder_settings){
			$dir_type=isset($map_options['direction']) ? $map_options['direction'] : 'detail'; ?>
			<div class="col-left leftsidebar slide-left <?php echo isset($grid_options['listing_position']) ? $grid_options['listing_position'].'-skip ' : 'below_map-skip ';  echo isset($grid_options['search_window_position']) ? $grid_options['search_window_position'] : 'wpml_left_map'; ?>">
				<div class="map-search-window">
	            <?php
            	$map_window_open = '';
	            if(isset($map_options['map_window_open'])){
	                $map_window_open = $map_options['map_window_open'];
	                if(!empty($map_window_open))
	                    $map_window_open = 'show_store_locator';
	            } ?>
			    <div class="search-options-btn"><a class="wpml-toggle-box"><span class="dashicons dashicons-menu"></span></a>
			    	<?php echo (!empty($placeholder_settings['search_options_btn'])) ? esc_html($placeholder_settings['search_options_btn']) : esc_html__('Search Options','store_locator'); ?>
			    </div>
			    <div class="store-search-fields <?php echo $map_window_open?>">
			        <form id="store_locator_search_form" >
			            <div class="store_locator_field <?php echo isset($map_options['search_field_get_my_location']) ? $map_options['search_field_get_my_location'] : ''; ?>">
			               <input id="store_locatore_get_btn" class=""  type="button" value="<?php echo (!empty($placeholder_settings['get_location_btn_txt'])) ? $placeholder_settings['get_location_btn_txt'] : _e('Get My Location','store_locator'); ?>" />
			            </div>
			            <div class="store_locator_field <?php echo isset($map_options['search_field_location']) ? $map_options['search_field_location'] : ''; ?>">
			        <input id="store_locatore_search_input"  class="wpsl_search_input " name="store_locatore_search_input" type="text" placeholder="<?php echo ($placeholder_settings['enter_location_txt'] == ''? _e('Location / Zip Code','store_locator') :$placeholder_settings['enter_location_txt']); ?>">
			            </div>
						<?php  $radius = ($map_options['radius'])?explode(",",trim($map_options['radius'])):false;
						$radius_default=0;
			             if($radius): ?>
			            <div class="store_locator_field <?php echo isset($map_options['search_field_radius']) ? $map_options['search_field_radius'] : ''; ?>">
			            <select id="store_locatore_search_radius" name="store_locatore_search_radius" class="wpsl_search_radius ">
			                <?php 
			                foreach ($radius as $option): ?>
			                    <?php
								$default = (preg_match("/\[[^\]]*\]/", $option))?true:false;
								$option = str_replace(array('[',']'), array('',''), $option);
								if($default){$radius_default=$option;} ?>
			                    <option value="<?php echo $option; ?>" <?php echo ($default)?"selected":"" ?> ><?php echo $option." ".$map_options['unit'] ; ?></option>
			                <?php endforeach; ?>
			            </select>
			        </div>
			        <?php endif; 
			        	$terms = get_terms( 'store_locator_category', array('hide_empty' => 0)); ?>
			        <div class="store_locator_field <?php echo isset($map_options['category']) ? $map_options['category'] : ''; ?>">
			            <select name="store_locator_category" id="wpsl_store_locator_category" class="wpsl_locator_category ">
			                <option value=""><?php echo !empty($placeholder_settings['select_category_txt']) ? esc_html($placeholder_settings['select_category_txt']) : esc_html__("Select Category","store_locator"); ?> </option>
							<?php foreach ( $terms as $term ) : 
								if(has_term( $term->term_id,'store_locator_category', absint($map_id) )){
								?>
			                    <option value="<?php echo esc_attr($term->term_id); ?>"> <?php echo esc_html($term->name); ?> </option>
			                <?php } endforeach; ?>
			            </select>
			        </div>
			        <?php
			        $terms = get_terms( 'store_locator_tag', array('hide_empty' => 0)); ?>
			        <div class="store_locator_field <?php echo isset($map_options['tag']) ? $map_options['tag'] : ''; ?>">
			            <select name="store_locator_tag[]" class="wpsl_locator_category " id="store_locator_tag" multiple="multiple" placeholder="<?php echo !empty($placeholder_settings['select_tags_txt']) ? esc_html($placeholder_settings['select_tags_txt']) : esc_html__('Select Tags','store_locator'); ?>">
							<?php foreach ( $terms as $term ) : ?>
			                    <option value="<?php echo esc_attr($term->term_id); ?>"> <?php echo esc_html($term->name); ?></option>
			                <?php endforeach; ?>
			            </select>
			        </div>
			        <div class="store_locator_field">
			        	<div class="map-btns">               
				        <span><input id="store_locatore_search_btn" type="submit" value="<?php echo !empty($placeholder_settings['search_options_btn']) ? esc_attr($placeholder_settings['search_options_btn']) : esc_html__("Search", 'store_locator'); ?>" /></span>
				        <?php if($dir_type=='detail'): ?>
				        <span><a class="map-directions"><?php esc_html_e('Get Directions','store_locator'); ?></a></span>
					    <?php endif; ?>
					    </div>
			    	</div>
		        </form>

				<div class="reset_map_field <?php echo isset($map_options['reset_map']) ? $map_options['reset_map'] : ''; ?>">
			        <div class="map-btns">               
				        <button class="reset_store_locator" onclick="reset_map(<?php echo $radius_default; ?>)">Reset Map</button>
					</div>
			    </div>

		        </div>
		    </div>
		    <?php if($dir_type=='detail'): ?>
			    <div class="map-direction-panel">
			    	<div class="search-options-btn"><a class="wpml-toggle-box"><span class="dashicons dashicons-menu"></span></a><?php esc_html_e('Directions','store_locator'); ?><a class="wpml-close-direction"><span class="dashicons dashicons-no-alt"></span></a></div>
			    	<div class="directions-toggle-panel">
			    	<div class="direction-toggle-addresses">
				    	<form role="form">
		        			<div class="form-group">
		        				<input type="text" class="" id="routeStart" value="">
		        				<a id="get-my-location"><span class="dashicons dashicons-location"></span></a>
		        			</div>
		        			<div class="form-group">
		        				<div class="input-group routeEnd-btn">
		        					<input type="text" class="" id="routeEnd">
		        					<span class="directions-btns">
		        						<button class="" type="button" id="get-directions" title="Get directions"><span class="dashicons dashicons-search"></span></button>
		        						<a class="wpml-close-direction">&larr; <?php echo esc_html__('Back to search','store_locator'); ?></a>
		        					</span>
		        				</div>
		        			</div>
		        		</form>
	        		</div>
			    	<div id="directionsPanel" style=""></div>
				    </div>
			    </div>
			<?php endif; ?>
	        </div>
			<?php
		}
		public function get_direction($meta, $type){
			if($type=='gmap'){
				return "<a href='https://www.google.com/maps?daddr=".$meta['store_locator_lat'].",".$meta['store_locator_lng']."' target='_blank'>".esc_html__('Get Direction','store_locator')."</a>";
			}else{
				return "<a class='store-direction' data-direction='".esc_attr($meta['store_locator_lat']).",".esc_attr($meta['store_locator_lng'])."' style='cursor:pointer;'>".esc_html__('Get Direction','store_locator')."</a>";
			}
		}
		public function distance($lat1, $lon1, $lat2, $lon2, $unit) {
		    $theta = floatval($lon1) - floatval($lon2);
		    $dist = sin(deg2rad(floatval($lat1))) * sin(deg2rad(floatval($lat2))) +  cos(deg2rad(floatval($lat1))) * cos(deg2rad(floatval($lat2))) * cos(deg2rad(floatval($theta)));
		    $dist = acos($dist);
		    $dist = rad2deg($dist);
		    $miles = $dist * 60 * 1.1515;
		    $unit = strtolower($unit);
		    if ($unit == "km") {
		        return ($miles * 1.609344);
		    } else if ($unit == "mile") {
		        return ($miles * 0.8684);
		    } else {
		        return $miles;
		    }
	    }
	    public function refresh_stores(){
	    	$args = array(
				'posts_per_page'   => -1,
				'orderby'          => 'date',
				'order'            => 'DESC',
				'post_type'        => 'store_locator',
				'post_status'      => 'publish',
				'fields'           => '',
			);
			$posts_array = get_posts( $args );
			$stores=array();
			if(!empty($posts_array)){
				foreach ($posts_array as $key => $store) {
					$temp=$meta=array();
					$temp= (array) $store;
					$term_ids='';
					if ( $terms = get_the_terms($store, 'store_locator_category' ) ) {
					    $term_ids = wp_list_pluck( $terms, 'term_id' );
					}
					$temp=array_merge($temp,array('categories'=> implode(',',$term_ids)));
					$term_ids='';
					if ( $terms = get_the_terms($store, 'store_locator_tag' )) {
					    $term_ids = wp_list_pluck( $terms, 'term_id' );
					}
					$temp=array_merge($temp,array('tags'=> $term_ids));
					$metaArr=get_post_meta($store->ID);
					foreach ($metaArr as $mkey => $mvalue) {
						$meta[$mkey]=$mvalue[0];
					}
					$temp=array_merge($temp,array('post_meta'=> $meta));
					$stores[$store->ID]=$temp;
				}
			}
			

			update_option('wp_multi_store_locator_stores', $stores);
			return $stores;
	    }
	}
}