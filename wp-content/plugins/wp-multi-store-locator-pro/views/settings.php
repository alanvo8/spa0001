<link rel="stylesheet" href="<?php echo STORE_LOCATOR_PLUGIN_URL . '/assets/css/jquery-ui.css'; ?>">
<div class="store_locator_settings_div">
    <?php if($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
        <div class="updated below-h2"><p><?php echo __("Settings updated.", 'store_locator'); ?></p></div>
    <?php endif; ?>

</div>
<?php 
if(!class_exists('WP_Multi_Store_Locator_Settings')){
class WP_Multi_Store_Locator_Settings
{
    public function __construct(){
        $current = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'basic-settings';
        $tabs = array(
        'basic-settings'   => __( 'Initialize', 'store_locator' ), 
        'map-settings'  => __( 'Map Settings', 'store_locator' ),
        'dynamic-text'  => __('Placeholder Settings','store_locator'),
        'grid-settings' => __('Grid Settings','store_locator'),
        'single-page-settings' => __('Single Page Settings','store_locator'),
        );
        $this->init_tabs(apply_filters('wpml_setting_tabs',$tabs));
        $this->current_tab(apply_filters('wpml_current_tab',$current));
    }
    public function init_tabs($tabs=array()){
        $current = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'basic-settings';
        $html = '<h2 class="nav-tab-wrapper">';
        foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? 'nav-tab-active' : '';
        $html .= '<a class="nav-tab ' . $class . '" href="edit.php?post_type=store_locator&page=store_locator_settings_page&tab=' . $tab . '">' . $name . '</a>';
        }
        $html .= '</h2>';
        echo $html;

    }
    public function current_tab($current='basic-settings'){
        switch ($current) {
        case 'basic-settings':
            $this->initialize_settings();
            break;
        case 'map-settings':
            $this->map_settings();
            break;
        case 'dynamic-text':
            $this->dynamic_text_settings();
            break;
        case 'grid-settings':
            $this->grid_settings();
            break; 
        case 'single-page-settings':
            $this->single_page_settings();
            break;    
        default:
            $this->initialize_settings();
            break;
        }
    }
    public function initialize_settings(){
        $_POST = array_map( 'stripslashes_deep', $_POST );
        if (isset($_POST['api-settings'])) {
            update_option('store_locator_API_KEY', $_POST['store_locator_API_KEY']);
          //  update_option('store_locator_street_API_KEY', $_POST['store_locator_street_API_KEY']);
            if(isset($_POST['map_landing_address']))
            update_option('map_landing_address', $_POST['map_landing_address']);
            
        }
        $store_locator_API_KEY  = get_option('store_locator_API_KEY');
         $map_landing_address  = get_option('map_landing_address');
        ?>
        <div class="wrap">
        <div class="metabox-holder">
            <div style="width: 75%;">
        <!-- Single page settings -->
                <form method="POST" >
                    <div class="postbox" >
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php echo __("Google Maps Api", 'store_locator'); ?></span></h3>
                        <div class="inside store_locator_singel_page_settings">


                            <p>
                                <label  for="store_locator_API_KEY"><?php echo __("API KEY", 'store_locator'); ?>:</label>
                                <input value="<?php print_r($store_locator_API_KEY); ?>" type="text" id="store_locator_API_KEY" name="store_locator_API_KEY" >
                            </p>
                           
                            <div class="default_Address_landing">
                                <h3><?php echo __("Map Landing Address", 'store_locator'); ?></h3>
                                 <?php if(!empty($store_locator_API_KEY)): ?>
                            <table class="widefat" style="border: 0px;">
                        <tbody>
                        <tr>
                            <td><?php echo __("Address", 'store_locator'); ?></td>
                            <td>
                                <input id="store_locator_address" class="regular-text" type="text" value="<?php echo isset($map_landing_address['address']) ? $map_landing_address['address'] : ''; ?>" name="map_landing_address[address] "/>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo __("Country", 'store_locator'); ?></td>
                            <td>
                                <select class="regular-text" name="map_landing_address[country]" id="store_locator_country">
                                    <option value="" ></option>
                                    <?php
                                    global $wpdb;
                                    $allCountries = $wpdb->get_results("SELECT * FROM store_locator_country");
                                   $selectedCountry =  isset($map_landing_address['country']) ? $map_landing_address['country'] : '';
                                    foreach ($allCountries as $country) {
                                        ?>
                                        <option value="<?php echo $country->name; ?>" <?php  echo ($selectedCountry == $country->name) ? "selected" : ""; ?>><?php echo $country->name; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr <?php echo ($selectedCountry != "United States")?"style='display: none;'":""; ?> >
                            <td><?php echo __("State", 'store_locator'); ?></td>
                            <td>
                                <select class="regular-text" name="map_landing_address[state]" id="store_locator_state">
                                    <option value="" ></option>
                                    <?php
                                    global $wpdb;
                                    $allStates = $wpdb->get_results("SELECT * FROM store_locator_state");
                                    $selectedState = isset($map_landing_address['state']) ? $map_landing_address['state'] : '';
                                    foreach ($allStates as $state) {
                                        ?>
                                        <option value="<?php echo $state->name; ?>" <?php echo ($selectedState == $state->name) ? "selected" : ""; ?>><?php echo $state->name; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo __("City", 'store_locator'); ?></td>
                            <td>
                                <input id="store_locator_city" type="text" class="regular-text" value="<?php echo isset($map_landing_address['city']) ? $map_landing_address['city'] : ''; ?>" name="map_landing_address[city]"/>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo __("Postal Code", 'store_locator'); ?></td>
                            <td>
                                <input id="store_locator_zipcode" class="regular-text" type="text" value="<?php echo isset($map_landing_address['zipcode']) ? $map_landing_address['zipcode'] : ''; ?>" name="map_landing_address[zipcode]"/>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <p><?php _e('Select default location for marker from bottom','store_locator'); ?></p>
                    <input type="hidden" value="<?php echo isset($map_landing_address['lat']) ? $map_landing_address['lat'] : ''; ?>" name="map_landing_address[lat]" id="store_locator_lat"/>
                    <input type="hidden" value="<?php echo isset($map_landing_address['lng']) ? $map_landing_address['lng'] : ''; ?>" name="map_landing_address[lng]" id="store_locator_lng"/>
                                <div id="map-container" style="position: relative;">

                                <div id="map_loader" style="z-index: 9;width: 100%; height: 200px;position: absolute;background-color: #fff;"><div class="uil-ripple-css" style="transform: scale(0.6); margin-left: auto; margin-right: auto;"><div></div><div></div></div></div>
                                <div id="map-canvas" style="height: 200px;width: 100%;"></div>
                            </div>

                            <script>
                                jQuery(document).ready(function (jQuery) {
                                      initializeMapBackend();
                                });
                            </script>
                              <?php else: ?>
                                <?php _e('To set map landing address please add API key first.','store_locator'); ?>
                            <?php endif; ?>
                            </div>

                            <p class="submit">
                                <input type="submit" class="button-primary" name="api-settings" value="<?php echo __("Save Changes", 'store_locator'); ?>">
                            </p>
                        </div>
                    </div>
                </form>
             </div>
        </div>
    </div>
        <?php
    }
    public function map_settings(){
        

        if (isset($_POST['map-settings'])) {
            $_POST['store_locator_map']['custom_style']=isset($_POST['store_locator_map']['custom_style']) ? stripslashes($_POST['store_locator_map']['custom_style']) : '';
            update_option('store_locator_map', $_POST['store_locator_map']);
        }
        $map_options  = get_option('store_locator_map');
        ?>
        <div class="wrap">
        <div class="metabox-holder">
            <div style="width: 75%;">
         <!-- Map settings -->
                <form method="POST">
                    <div class="postbox" >
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php echo __("Map Settings", 'store_locator'); ?></span></h3>
                        <div class="inside store_locator_map_settings">
                            <table>
                                <tbody>
                            <tr>
                                <td><label title="Enable the display of map on the frontend" for="store_locator_map_enable"><?php echo __("Show map on frontend", 'store_locator'); ?>?</label></td>
                                <td><input value="0" type="hidden" name="store_locator_map[enable]" >
                                <input <?php echo ($map_options['enable'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_enable" name="store_locator_map[enable]" ></td>
                            </tr>

                            <tr>
                                <td><label title="Select Map Width pixels" for="store_locator_map_width"><?php echo __('Map Width', 'store_locator'); ?>:</label></td>
                                <td><input value="<?php echo $map_options['width']; ?>" type="text" id="store_locator_map_width" name="store_locator_map[width]" size="4">
                                <select name="store_locator_map[widthunit]" id="store_locator_map_widthunit" >
                                    <option <?php  ($map_options['widthunit'] == 'px') ?"selected=": ""; ?> selected value="px">PX</option>
                                    <option <?php  echo ($map_options['widthunit'] == '%') ?"selected=": ""; ?>  value="%">%</option>
                                    <?php /* <option <?php  ($map_options['widthunit'] == '%') ?"selected=": ""; ?> value="px">% in future</option> */ ?>
                                </select></td>
                            </tr>
                            <tr>
                                <td><label title="Select Map Height pixels. Min height 500px" for="store_locator_map_height"><?php echo __("Map Height ", 'store_locator'); ?>:</label></td>
                                <td><input value="<?php echo $map_options['height']; ?>" type="text" id="store_locator_map_height"  min="550" max="800" name="store_locator_map[height]" size="4" >
                                <select name="store_locator_map[heightunit]" id="store_locator_map_heightunit" >
                                    <option <?php  echo ($map_options['heightunit'] == 'px') ?"selected=": ""; ?>  value="px">PX</option>           
                                    <?php /* <option <?php  ($map_options['heightunit'] == '%') ?"selected=": ""; ?> value="px">% in future</option> */?>
                                </select></td>
                            </tr>
                            <tr>
                                <td><label title="Select Map Type" for="store_locator_map_type"><?php echo __("Map Type", 'store_locator'); ?>:</label></td>
                                <td><select name="store_locator_map[type]" id="store_locator_map_type">
                                    <option <?php echo ($map_options['type'] == 'roadmap') ?"selected=": ""; ?> value="roadmap"><?php _e('Roadmap','store_locator');?></option>
                                    <option <?php echo ($map_options['type'] == 'hybrid') ?"selected=": ""; ?> value="hybrid"><?php _e('Hybrid','store_locator');?></option>
                                    <option <?php echo ($map_options['type'] == 'satellite') ?"selected=": ""; ?> value="satellite"><?php _e('Satellite','store_locator');?></option>
                                    <option <?php echo ($map_options['type'] == 'terrain') ?"selected=": ""; ?> value="terrain"><?php _e('Terrain','store_locator');?></option>
                                </select></td>
                            </tr>
                            <tr>
                                <td><label title="Choose the unit of search km/mile" for="store_locator_map_unit"><?php echo __("Search Unit", 'store_locator'); ?>:</label></td>
                                <td><select name="store_locator_map[unit]" id="store_locator_map_unit">
                                    <option <?php echo ($map_options['unit'] == 'km') ?"selected=": ""; ?> value="km">Km</option>
                                    <option <?php echo ($map_options['unit'] == 'mile') ?"selected=": ""; ?> value="mile">Mile</option>
                                </select></td>
                            </tr>
                            <tr>
                                <td>
                                <label title="<?php _e('Default Zoom Level','store_locator'); ?>" for="store_locator_map_zoom"><?php echo __("Default Map Zoom Level:","store_locator"); ?></label>
                                </td>
                                <td><input value="<?php echo isset($map_options['zoomlevel']) ? esc_attr($map_options['zoomlevel']) : 8; ?>" type="number" id="store_locator_map_zoom" name="store_locator_map[zoomlevel]">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                <label title="<?php _e('Default Zoom Level','store_locator'); ?>" for="store_locator_map_zoom"><?php echo __("Location Search Zoom Level:","store_locator"); ?></label>
                                </td>
                                <td><input value="<?php echo isset($map_options['markerzoom']) ? esc_attr($map_options['markerzoom']) : 8; ?>" type="number" id="store_locator_map_zoom" name="store_locator_map[markerzoom]">
                                </td>
                            </tr>
                            <tr>
                                <td><label title="Choose search options here. the default one will be between square brakets" for="store_locator_map_radius"><?php echo __("Search radius options", 'store_locator'); ?>:</label></td>
                                <td><input value="<?php echo $map_options['radius']; ?>" type="text" id="store_locator_map_radius" name="store_locator_map[radius]" >
                                <div class="store_locator_tip">e.g: 5,10,[25],50,100,200,500</div></td>
                            </tr>
                            <tr>
                                <td><label title="Show street control on the map in frontend" for="store_locator_map_streetViewControl"><?php echo __("Show street view control", 'store_locator'); ?>?</label></td>
                                <td><input value="0" type="hidden" name="store_locator_map[streetViewControl]" >
                                <input <?php echo ($map_options['streetViewControl'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_streetViewControl" name="store_locator_map[streetViewControl]" ></td>
                            </tr>
                            <tr>
                               <td> <label title="Enable disable clusters on map" for="store_locator_map_cluster"><?php echo __("Show Map Cluster", 'store_locator'); ?>?</label></td>
                                <td><input <?php echo (isset($map_options['mapcluster']) && ($map_options['mapcluster'])==1)?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_cluster" name="store_locator_map[mapcluster]" ></td>
                            </tr>
                            <tr>
                                <td><label title="Scroll Map to screen after search" for="store_locator_map_scroll_to_top"><?php echo __("Scroll to map top after search", 'store_locator'); ?>?</label></td>
                                <td><input <?php echo (isset($map_options['mapscrollsearch']) && ($map_options['mapscrollsearch'])==1)?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_scroll_to_top" name="store_locator_map[mapscrollsearch]" ></td>
                            </tr>
                            <tr>
                                <td><label title="Enable the user to change the map type from the frontend" for="store_locator_map_mapTypeControl"><?php echo __("Show map type control", 'store_locator'); ?>?</label></td>
                                <td><input value="0" type="hidden" name="store_locator_map[mapTypeControl]" >
                                <input <?php echo ($map_options['mapTypeControl'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_mapTypeControl" name="store_locator_map[mapTypeControl]" ></td>
                            </tr>
                            <tr>
                                <td><label title="Enable/Disable zoom by scroll on map" for="store_locator_map_scroll"><?php echo __('Zoom by scroll on map', 'store_locator'); ?>?</label></td>
                                <td><input value="0" type="hidden" name="store_locator_map[scroll]" >
                                <input <?php echo ($map_options['scroll'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_scroll" name="store_locator_map[scroll]" ></td>
                            </tr>
                            <tr>
                                <td><label title="Display default Map Search" for="store_locator_default_search"><?php echo __('Show Map Search options', 'store_locator'); ?></label></td>
                                <td><input value="0" type="hidden" name="store_locator_map[default_search]" >
                                <input <?php echo ($map_options['default_search'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_default_search" name="store_locator_map[default_search]" ></td>
                            </tr>
                            <tr>
                                <td><label title="Hide Field Options" for="store_locator_default_search"><?php echo __('Hide Fields for Search', 'store_locator'); ?></label></td>
                                <td><ul class="hide_fields">
                                    <li><input <?php echo (isset($map_options['search_field_get_my_location']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[search_field_get_my_location]" ><?php _e('Get My Location','store_locator');?></li>
                                    <li>
            <input <?php echo (isset($map_options['search_field_location']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[search_field_location]" ><?php _e('Location Field','store_locator');?>
                                    </li>
                                    <li><input <?php echo (isset($map_options['search_field_radius']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[search_field_radius]" ><?php _e('Radius Field','wpmsl');?></li>
                                    <li><input <?php echo (isset($map_options['category']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[category]" ><?php _e('Category Field','store_locator');?></li>
                                    <li><input <?php echo (isset($map_options['tag']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[tag]" ><?php _e('Tags Field','store_locator');?></li>
                                </ul></td>
                            </tr>
                            <tr>
                                <td><label title="Map Search Open as Default" for="map_window_open"><?php echo __("Map Search Open as Default", 'store_locator'); ?></label></td>
                                <td><input <?php echo (isset($map_options['map_window_open']))?'checked':''; ?> value="1" type="checkbox" id="map_window_open" name="store_locator_map[map_window_open]" ></td>
                            </tr>
                            <tr>
                                <td><label title="Switch To RTL" for="rtl_enabled"><?php echo __("Switch To RTL", 'store_locator'); ?></label></td>
                                <td><input <?php echo (isset($map_options['rtl_enabled']))?'checked':''; ?> value="1" type="checkbox" id="rtl_enabled" name="store_locator_map[rtl_enabled]" ></td>
                            </tr>
                            <tr><td colspan="2"><b><?php _e('Map Styles','store_locator');?></b></td></tr>
                            <tr><td colspan="2">
                            <div class="map_Styles_div">
                            <p>
                                <label title="Standard Map" for="store_locator_map_style1"><?php echo __("Standard Map", 'store_locator'); ?></label>
                                <input <?php echo ($map_options['map_style'] == 1)?'checked':''; ?> value="1" type="radio" id="store_locator_map_style1" name="store_locator_map[map_style]" >
                                <img src="<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/staticmap.png'; ?>" />
                            </p>
                            <p>
                                <label title="Silver Map" for="store_locator_map_style2"><?php echo __("Silver Map", 'store_locator'); ?></label>
                                <input <?php echo ($map_options['map_style'] == 2)?'checked':''; ?> value="2" type="radio" id="store_locator_map_style2" name="store_locator_map[map_style]" >
                                <img src="<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/silver.png'; ?>" />
                            </p>
                            <p>
                                <label title="Retro Map" for="store_locator_map_style2"><?php echo __("Retro Map", 'store_locator'); ?></label>
                                <input <?php echo ($map_options['map_style'] == 3)?'checked':''; ?> value="3" type="radio" id="store_locator_map_style2" name="store_locator_map[map_style]" >
                                <img src="<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/retro.png'; ?>" />
                            </p>
                            <p>
                                <label title="Dark Map" for="store_locator_map_style2"><?php echo __("Dark Map", 'store_locator'); ?></label>
                                <input <?php echo ($map_options['map_style'] == 4)?'checked':''; ?> value="4" type="radio" id="store_locator_map_style2" name="store_locator_map[map_style]" >
                                <img src="<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/dark.png'; ?>" />
                            </p>
                            <p>
                                <label title="Night Map" for="store_locator_map_style2"><?php echo __("Night Map", 'store_locator'); ?></label>
                                <input <?php echo ($map_options['map_style'] == 5)?'checked':''; ?> value="5" type="radio" id="store_locator_map_style2" name="store_locator_map[map_style]" >
                                <img src="<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/night.png'; ?>" />
                            </p>

                            <p>
                                <label title="Aubergine Map" for="store_locator_map_style2"><?php echo __("Aubergine Map", 'store_locator'); ?></label>
                                <input <?php echo ($map_options['map_style'] == 6)?'checked':''; ?> value="6" type="radio" id="store_locator_map_style2" name="store_locator_map[map_style]" >
                                <img src="<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/aubergine.png'; ?>" />
                            </p>

                            <p>
                                <label title="Basic Map" for="store_locator_map_style2"><?php echo __("Basic Map", 'store_locator'); ?></label>
                                <input <?php echo ($map_options['map_style'] == 7)?'checked':''; ?> value="7" type="radio" id="store_locator_map_style2" name="store_locator_map[map_style]" >
                                <img src="<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/basic.png'; ?>" />
                            </p>
                        </div></td>
                        </tr>
                            <tr>
                            <td>
                                <div style="clear: both;"></div>
                                <label title="Choose the color of user marker" for="store_locator_map_type"><?php echo __("User Marker", 'store_locator'); ?>:</label>
                            </td>
                            <td><ul class="default_markers">
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/blue.png'; ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'blue.png')?'checked':''; ?> type="radio" value="blue.png" name="store_locator_map[marker1]" />
                                </li>
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/red.png'; ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'red.png')?'checked':''; ?> type="radio" value="red.png" name="store_locator_map[marker1]" />
                                </li>
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/green.png'; ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'green.png')?'checked':''; ?> type="radio" value="green.png" name="store_locator_map[marker1]" />
                                </li>
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/orange.png'; ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'orange.png')?'checked':''; ?> type="radio" value="orange.png" name="store_locator_map[marker1]" />
                                </li>
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/purple.png'; ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'purple.png')?'checked':''; ?> type="radio" value="purple.png" name="store_locator_map[marker1]" />
                                </li>
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/yellow.png'; ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'yellow.png')?'checked':''; ?> type="radio" value="yellow.png" name="store_locator_map[marker1]" />
                                </li>
                            </ul></td>
                            </tr>
                            <tr>
                              <td><?php _e('or add custom marker url','store_locator');?></td>
                              <?php 
                                    if(isset($map_options['marker1_custom']) && !empty($map_options['marker1_custom'])){
                                        $marker1=$map_options['marker1_custom'];
                                        $class='wpmsl_custom_marker';
                                        $uploadRemove=__('Remove','store_locator');
                                    }
                                    else{
                                        $marker1=STORE_LOCATOR_PLUGIN_URL . 'assets/img/upload.png';
                                        $class='wpmsl_custom_marker_upload'; 
                                        $uploadRemove =__('Upload','store_locator');
                                    }

                                ?>
                               <td><div class="<?php echo $class; ?>">
                                <img src="<?php echo $marker1; ?>" width="50px" height="50px">
                                  <input type="hidden" value="<?php echo ($class=='wpmsl_custom_marker') ? $marker1 : ''; ?>" name="store_locator_map[marker1_custom]" />
                                  <p><?php echo $uploadRemove; ?></p>
                              </div></td>
                            </tr>
                            <tr>
                            <td>
                                <label title="Choose the color of store marker" for="store_locator_map_type"><?php echo __("Store Marker", 'store_locator'); ?>:</label>
                            </td>
                            <td>
                            <ul class="default_markers">
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/blue.png'; ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'blue.png')?'checked':''; ?> type="radio" value="blue.png" name="store_locator_map[marker2]" />
                                </li>
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/red.png'; ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'red.png')?'checked':''; ?> type="radio" value="red.png" name="store_locator_map[marker2]" />
                                </li>
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/green.png'; ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'green.png')?'checked':''; ?> type="radio" value="green.png" name="store_locator_map[marker2]" />
                                </li>
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/orange.png'; ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'orange.png')?'checked':''; ?> type="radio" value="orange.png" name="store_locator_map[marker2]" />
                                </li>
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/purple.png'; ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'purple.png')?'checked':''; ?> type="radio" value="purple.png" name="store_locator_map[marker2]" />
                                </li>
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/yellow.png'; ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'yellow.png')?'checked':''; ?> type="radio" value="yellow.png" name="store_locator_map[marker2]" />
                                </li>
                            </ul></td>
                            </tr>
                            <tr>
                               <td><?php _e('or add custom marker url','store_locator');?></td>
                                <?php 
                                    if(isset($map_options['marker2_custom']) && !empty($map_options['marker2_custom'])){
                                        $marker2=$map_options['marker2_custom'];
                                        $class='wpmsl_custom_marker';
                                        $uploadRemove=__('Remove','store_locator');
                                    }
                                    else{
                                        $marker2=STORE_LOCATOR_PLUGIN_URL . 'assets/img/upload.png';
                                        $class='wpmsl_custom_marker_upload'; 
                                        $uploadRemove =__('Upload','store_locator');
                                    }

                                ?>
                               <td><div class="<?php echo $class; ?>">
                                <img src="<?php echo $marker2; ?>" width="50px" height="50px">
                                  <input type="hidden" value="<?php echo ($class=='wpmsl_custom_marker') ? $marker2 : ''; ?>" name="store_locator_map[marker2_custom]" />
                                  <p><?php echo $uploadRemove; ?></p>
                              </div>
                              </td>
                            </tr>
                            <?php
                            echo do_action('wpmsl_private_marker_settings');
                            ?>
                            <tr>
                                <td colspan="2"><label title="<?php echo __('You can customise the contetnt of the info window here by adding HTML if you need. Also you can use the below variables in the content','store_locator'); ?>" for="store_locator_map_infowindow"><b><?php echo __("Info Window Content", 'store_locator'); ?></b>: <br/><span class="store_locator_tip">placeholders: {image} {address} {city} {state} {country} {zipcode} {name} {phone} {website} {working_hours}</span></label>
                                <textarea name="store_locator_map[infowindow]" rows="10" cols="70" id="store_locator_map_infowindow"><?php echo $map_options['infowindow']; ?></textarea></td>
                            </tr>
                            <tr>
                                <td colspan="2"><label title="<?php echo __('You can customize the look of the map by adding styles here','store_locator'); ?>" for="store_locator_map_style"><b><?php echo __("Customised Map Style", 'store_locator'); ?></b>: <span class="store_locator_tip"><?php _e('You can get some styles from','store_locator');?><a target="_blanck" href="https://snazzymaps.com"> <?php _e('Snazzy Maps','store_locator');?></a></span> </label>
                                <textarea name="store_locator_map[custom_style]"  rows="10" cols="70" id="store_locator_map_style"><?php echo stripslashes($map_options['custom_style']); ?></textarea></td>
                            </tr>
                            <tr class="submit">
                                <td colspan="2"><input type="submit" class="button-primary" name="map-settings" value="<?php echo __("Save Changes", 'store_locator'); ?>"></td>
                            </tr>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
        <?php
    }
    public function dynamic_text_settings(){
         //handle save single page settings
        if (isset($_POST['placeholder-setting'])) {
            $placeholders = array();
            $placeholders['get_location_btn_txt'] = $_POST['get_location_btn_txt'];
            $placeholders['enter_location_txt'] = $_POST['enter_location_txt'];
            $placeholders['select_category_txt'] = $_POST['select_category_txt'];
            $placeholders['select_tags_txt'] = $_POST['select_tags_txt'];
            $placeholders['search_options_btn']=$_POST['search_options_btn'];
            $placeholders['location_not_found'] = $_POST['location_not_found'];
            $placeholders['store_list'] = $_POST['store_list'];
            $placeholders['visit_website'] = $_POST['visit_website'];
            update_option('placeholder_settings',$placeholders);
        }
        $placeholder_setting = get_option('placeholder_settings');
        ?>
        <div class="wrap">
        <div class="metabox-holder">
            <div style="width: 75%;">
        <form method="POST" >
                    <div class="postbox" >
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php echo __("Placeholder Text", 'store_locator'); ?></span></h3>
                        <div class="inside store_locator_grid_settings">
                        <table>
                            <tbody>
                            <tr><th><?php _e('Get Location Text Button','store_locator');?></th>
                            <td><input type="text" name="get_location_btn_txt" value="<?php echo $placeholder_setting['get_location_btn_txt']?>"/></td>
                            </tr>
                            <tr><th><?php _e('Enter Location Text','store_locator');?></th>
                            <td><input type="text" name="enter_location_txt" value="<?php echo $placeholder_setting['enter_location_txt']?>" /></td>
                            </tr>
                            <tr><th><?php _e('Select Category','store_locator');?></th>
                            <td><input type="text" name="select_category_txt" value="<?php echo $placeholder_setting['select_category_txt']?>"/></td>
                            </tr>
                            <tr><th><?php _e('Select Tags','store_locator');?></th>
                            <td><input type="text" name="select_tags_txt" value="<?php echo $placeholder_setting['select_tags_txt']?>"/></td>
                            </tr>
                            <tr><th><?php _e('Search Options Button Text','store_locator');?></th>
                            <td><input type="text" name="search_options_btn" value="<?php echo @$placeholder_setting['search_options_btn']?>"/></td>
                            </tr>
                             <tr><th><?php _e('Location not found text','store_locator');?></th>
                            <td><input type="text" name="location_not_found" value="<?php echo @$placeholder_setting['location_not_found']?>"/></td>
                            </tr>
                            <tr><th><?php _e('Store list text','store_locator');?></th>
                            <td><input type="text" name="store_list" value="<?php echo isset($placeholder_setting['store_list']) ? $placeholder_setting['store_list'] : '' ;?>"/></td>
                            </tr>
                            <tr><th><?php _e('Visit Website text','store_locator');?></th>
                            <td><input type="text" name="visit_website" value="<?php echo isset($placeholder_setting['visit_website']) ? $placeholder_setting['visit_website'] : '' ;?>"/></td>
                            </tr>
                            <tr class="submit">
                                <td colspan="2"><input type="submit" class="button-primary" name="placeholder-setting" value="<?php echo __("Save Changes", 'store_locator'); ?>"></td>
                            </tr>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </form>
              </div>
        </div>
    </div>
        <?php
    }
    public function grid_settings(){
        //handle save grid settings
        if (isset($_POST['grid-settings'])) {
            $_POST['store_locator_grid']['columns'] = explode(",", $_POST['store_locator_grid']['columns']);
            update_option('store_locator_grid', $_POST['store_locator_grid']);
        }
        $grid_options = get_option('store_locator_grid');
        ?>
        <div class="wrap">
        <div class="metabox-holder">
            <div style="width: 75%;">
         <!-- Grid settings -->
                <form method="POST" >
                    <div class="postbox" >
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle">
                            <span><?php echo __("Grid Settings", 'store_locator'); ?></span></h3>
                        <div class="inside store_locator_grid_settings">
                            <table style="text-align: left;">
                            <tr><th>
                                <label title="<?php echo __("Show the results in grid in the frontend","store_locator"); ?>" for="store_locator_grid_enable"><?php echo __("Show grid on frontend", 'store_locator'); ?>?</label>
                            </th><td>
                                <input value="0" type="hidden" name="store_locator_grid[enable]" >
                                <input <?php echo ($grid_options['enable'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_grid_enable" name="store_locator_grid[enable]" >
                           </td> </tr>

                            <tr><th>
                                <label title="<?php _e("Maximum number of markers to be displayed","store_locator") ?>" for="store_locator_grid_number"><?php echo __("Maximum number of markers to be displayed", 'store_locator'); ?>:</label>
                                </th><td>
                                <input value="<?php echo isset($grid_options['total_markers']) ? trim($grid_options['total_markers']) : '-1'; ?>" type="text" id="store_locator_grid_number" name="store_locator_grid[total_markers]" >
                            </td> </tr>

                            <tr><th>
                                <label title="<?php _e("Enable/Disable autoload results when scroll down","store_locator") ?>" for="store_locator_grid_scroll"><?php echo __("Autoload results on scroll", 'store_locator'); ?>?</label>
                                </th><td>
                                <input value="0" type="hidden" name="store_locator_grid[scroll]" >
                                <input <?php echo ($grid_options['scroll'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_grid_scroll" name="store_locator_grid[scroll]" >
                            </td> </tr>

                            <tr><th>
                                <label title="<?php _e("Select the displayed column in the grid in the frontend by order","store_locator") ?>" for="store_locator_grid_columns"><?php echo __("Displayed Columns", 'store_locator'); ?>:<span class="store_locator_tip"><?php echo __("Select columns with order to be displayed on frontend", 'store_locator'); ?></span></label>
                                </th><td>
                                <select  id="store_locator_grid_columns" multiple="multiple">
                                    <?php
                                    if(isset($grid_options['columns']) && $grid_options['columns']){
                                        $selectedColumns  = $grid_options['columns'];
                                    }else{
                                        $selectedColumns  = array();
                                    }
                                    ?>
                                    <?php
                                    $columns = array("name", "address", "city", "state", "country", "zipcode", "website", "full_address", "managers", "phone", "working_hours", "fax");
                                    if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
                                        $columns[] = 'gravity_form';
                                    }
                                    $columns = array_diff($columns, $selectedColumns);
                                    $columns = array_merge($selectedColumns, $columns);
                                    ?>
                                    <?php foreach ($columns as $column): ?>
                                        <?php if ($column): ?>
                                            <option value="<?php echo $column; ?>" <?php echo (in_array($column, $selectedColumns)) ? "selected" : ""; ?>><?php echo $column; ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <input name="store_locator_grid[columns]" type="hidden" value="<?php echo implode(",", $selectedColumns); ?>">
                            </td> </tr>
                            <tr><th>
                                <label title="Map Result Show on" for="store_locator_map_type"><?php echo __("Map Result Show on", 'store_locator'); ?>:</label>
                                </th><td>
                                <ul class='listing_postions_grid_settings'>
                                    <li>
                                        <label style="width: 26px;"><?php _e('On Map Left Side','store_locator');?>
                                        <input <?php echo (isset($grid_options['listing_position']) && $grid_options['listing_position'] == 'left')?'checked':''; ?> type="radio" value="left" name="store_locator_grid[listing_position]" /></label>
                                    </li>
                                    <li>
                                        <label style="width: 26px;"><?php _e('On Map Right Side','store_locator');?>
                                        <input <?php echo (isset($grid_options['listing_position']) && $grid_options['listing_position'] == 'right')?'checked':''; ?> type="radio" value="right" name="store_locator_grid[listing_position]" /></label>
                                    </li>
                                    <li>
                                        <label style="width: 26px;"><?php _e('Below Map','store_locator');?>
                                        <input <?php echo (isset($grid_options['listing_position']) && $grid_options['listing_position'] == 'below_map')?'checked':''; ?> type="radio" value="below_map" name="store_locator_grid[listing_position]" /></label>
                                    </li>
                                </ul>
                                </td> </tr>
                            
                            <tr><th>
                                <label title="Map Search Options Window Show on" for="store_locator_map_type"><?php echo __("Map Search Options Window Show on", 'store_locator'); ?>:</label>
                            </th><td>
                                <ul class='listing_postions_grid_settings'>
                                    <li>
                                        <label style="width: 26px;"><?php _e('On Map Left Side','store_locator');?>
                                        <input <?php echo (isset($grid_options['search_window_position']) && $grid_options['search_window_position'] == 'left')?'checked':''; ?> type="radio" value="left" name="store_locator_grid[search_window_position]" /></label>
                                    </li>
                                    <li>
                                        <label style="width: 26px;"><?php _e('On Map Right Side','store_locator');?>
                                        <input <?php echo (isset($grid_options['search_window_position']) && $grid_options['search_window_position'] == 'wpml_search_right')?'checked':''; ?> type="radio" value="wpml_search_right" name="store_locator_grid[search_window_position]" /></label>
                                    </li>
                                    <li>
                                        <label style="width: 26px;"><?php _e('Above Map','store_locator');?>
                                        <input <?php echo (isset($grid_options['search_window_position']) && $grid_options['search_window_position'] == 'wpml_above_map')?'checked':''; ?> type="radio" value="wpml_above_map" name="store_locator_grid[search_window_position]" /></label>
                                    </li>
                                </ul>
                           </td> </tr>
                           <tr><td colspan="2">
                            <p class="submit">
                                <input type="submit" class="button-primary" name="grid-settings" value="<?php echo __('Save Changes', 'store_locator'); ?>">
                            </p>
                            </td> </tr>
                        </table>
                        </div>
                    </div>
                </form>
             </div>
        </div>
    </div>
        <?php
    }
    public function single_page_settings(){
        //handle save single page settings
        if (isset($_POST['single-settings'])) {
            $_POST['store_locator_single']['items'] = explode(",", $_POST['store_locator_single']['items']);
            update_option('store_locator_single', $_POST['store_locator_single']);
        }
        $single_options = get_option('store_locator_single');
        ?>
        <div class="wrap">
        <div class="metabox-holder">
            <div style="width: 75%;">
         <!-- Single page settings -->
                <form method="POST" >
                    <div class="postbox" >
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php echo __("Single Page Settings", 'store_locator'); ?></span></h3>
                        <div class="inside store_locator_singel_page_settings">

                            <table style="text-align: left;">
                            <tr><th>
                                <label title="<?php _e('Enable/Disable when click on store goto single page for more details','store_locator') ?>" for="store_locator_single_page"><?php echo __("Link store to a single page", 'store_locator'); ?>?</label>
                                </th><td>
                                <input value="0" type="hidden" name="store_locator_single[page]" >
                                <input <?php echo ($single_options['page'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_single_page" name="store_locator_single[page]" >
                             </td></tr>
                            
                            <tr><th>
                                <label title="<?php _e('Enter Unique Slug Name','store_locator') ?>" for="store_locator_slug"><?php echo __("Enter Unique Slug Name", 'store_locator'); ?></label>   </th><td>                             
                                <input <?php echo ($single_options['store_locator_slug'] != '')? $single_options['store_locator_slug']:''; ?> placeholder="store-locator" value="<?php echo $single_options['store_locator_slug']?>" type="text" id="store_locator_slug" name="store_locator_single[store_locator_slug]" >
                            </td></tr>

                            <tr><th>
                                <label title="<?php _e('Enable/Disable the display of feature image of the store in the inner page','store_locator') ?>" for="store_locator_single_image"><?php echo __("Show feature image of the store?", 'store_locator'); ?>?</label>
                                </th><td>
                                <input value="0" type="hidden" name="store_locator_single[image]" >
                                <input <?php echo ($single_options['image'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_single_image" name="store_locator_single[image]" >
                            </td></tr>

                            <tr><th>
                                <label title="<?php _e('Enable/Disable showing map in the inner page of the store','store_locator') ?>" for="store_locator_single_map"><?php echo __("Show map on page?", 'store_locator'); ?>?</label>
                                </th><td>
                                <input value="0" type="hidden" name="store_locator_single[map]" >
                                <input <?php echo ($single_options['map'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_single_map" name="store_locator_single[map]" >
                           </td></tr>

                            <tr><th>
                                <label title="<?php _e('Select the displayed column in the page in the frontend by order','store_locator') ?>" for="store_locator_single_items"><?php echo __("Displayed Columns", 'store_locator'); ?>:<span class="store_locator_tip"><?php echo __("Select details you want to display on the page", 'store_locator'); ?></span></label>
                                </th><td>
                                <select  id="store_locator_single_items" multiple="multiple">
                                    <?php
                                    if(isset($single_options['items']) && $single_options['items']){
                                        $selectedItems  = $single_options['items'];
                                    }else{
                                        $selectedItems  = array();
                                    }
                                    ?>
                                    <?php
                                    $items = array("name", "website", "full_address", "managers", "phone", "working_hours", "fax", "description");
                                    $items = array_diff($items, $selectedItems);
                                    $items = array_merge($selectedItems, $items);
                                    ?>
                                    <?php foreach ($items as $item): ?>
                                        <?php if ($item): ?>
                                            <option value="<?php echo $item; ?>" <?php echo (in_array($item, $selectedItems)) ? "selected" : ""; ?>><?php echo $item; ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>

                                <input name="store_locator_single[items]" type="hidden" value="<?php echo implode(",", $selectedItems); ?>">
                            </td></tr>
                            <tr><td colspan="2">
                            <p class="submit">
                                <input type="submit" class="button-primary" name="single-settings" value="<?php echo __("Save Changes", 'store_locator'); ?>">
                            </p>
                            </td></tr>
                        </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
        <?php
    }
}
new WP_Multi_Store_Locator_Settings();
}
?>

<style>
    .ui-widget-content{
        background: #0073AA url("images/ui-bg_flat_75_ffffff_40x100.png") 50% 50% repeat-x;
        color: white;
    }
    .ui-tooltip:after {
        content: "\f142";
        font-family: dashicons;
        font-size: 30px;
        top: -11px;
        position: absolute;
        color: #0073AA;
    }
    .ui-tooltip{
        box-shadow: none;
        border-width: 0px !important;
    }</style>