<?php 
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}  
if(!class_exists('WPMSL_Import_Export_Stores')){
	class WPMSL_Import_Export_Stores{
		public function __construct(){
			add_action('admin_menu', array($this,'register_submenu_page'));
			// Do update addresses coordinates Ajax
			add_action('wp_ajax_update_coordinates', array($this,'import_store_locator_page_callback'));
			add_action('wp_ajax_import_js_stores_action', array($this,'import_js_stores_function'));
			add_action('wp_ajax_clear_imported_stores_action', array($this,'clear_imported_stores_function'));
			
			// export csv
			add_action('admin_post_printStores.csv', array($this,'export_store_locator_csv'));
			add_action('admin_post_printSales.csv', array($this,'export_sales_manager_csv'));
		}
		public function register_submenu_page(){
    		add_submenu_page('edit.php?post_type=store_locator', 
    			__('Import/Export','store_locator'), 
    			__('Import/Export','store_locator'), 
    			'manage_options', 
    			'import-export-submenu-page-partner', 
    			array($this,'import_store_locator_page_callback'));
		}

		public function clear_imported_stores_function() {

			if (isset($_POST['clear_imported_stores']) && $_POST['clear_imported_stores']==true) {
				$cleared=update_option('my_import_stores',array());

				if($cleared){
					echo "true";
				}
				else{
					echo "false";
				}
				die;
			}
			echo "false";
			die;
		}


		public function import_store_locator_page_callback() {
		    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		        if (isset($_POST['settings'])) {
		            update_option('partner_locator_bg', $_POST['partner_locator_bg']);
		            echo '<div class="updated notice notice-success below-h2" id="store_locator_message"><p>'.esc_html__("Settings Updated Successfully.","store_locator").'</p></div>';
		        }
		       
		        // Import sales CSV
		        if (isset($_POST['import_sales_button'])) {
		            require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
		            $csv_mimetypes = array(
		                'text/csv',
		                'text/plain',
		                'text/comma-separated-values',
		                'application/excel',
		                'application/vnd.ms-excel',
		                'application/vnd.msexcel',
		                'application/csv',
		                'application/octet-stream',
		                'application/txt',
		            );
		            if (!$_FILES['csv-file']['tmp_name'] || !(in_array($_FILES['csv-file']['type'], $csv_mimetypes)) || $_FILES['csv-file']['size'] == 0) {
		                ?>
		                <div class="error notice notice-success below-h2" id="message"><p><?php esc_html_e('Please select CSV file.','store_locator'); ?></p></div>
		                <?php
		            } else {
		                $csv = array_map('str_getcsv', file($_FILES['csv-file']['tmp_name']));
		                $header = array_shift($csv);
		                $sales = array();
		                foreach ($csv as $row) {
		                    $sales[] = array_combine($header, $row);
		                }
		                global $user_ID;
		                global $wpdb;
		                $postmeta_table = $wpdb->prefix . 'postmeta';
		                foreach ($sales as $partner) {
		                    $args = array(
		                        'post_type' => 'sales_manager',
		                        'status' => 'publish',
		                        'meta_query' => array(
		                            array(
		                                'key' => 'sales_manager_id',
		                                'value' => $partner['Code'],
		                                'compare' => '='
		                            )
		                        )
		                    );
		                    $exist_post = get_posts($args);
		                    if($exist_post &&  $partner['Code']){
		                        //edit existing post
		                        $edit_post = array(
		                            'ID' => $exist_post[0]->ID,
		                            'post_title' => $partner['Name'],
		                            'post_name' => $partner['Name'] . uniqid(),
		                        );
		                        $post_id = wp_update_post($edit_post);
		                        $wpdb->delete($postmeta_table, array('post_id' => $post_id) );
		                    }else{
		                        //add new post
		                        $new_post = array(
		                            'post_title' => $partner['Name'],
		                            'post_name' => $partner['Name'] . uniqid(),
		                            'post_status' => 'publish',
		                            'post_date' => date('Y-m-d H:i:s'),
		                            'post_author' => $user_ID,
		                            'post_type' => 'sales_manager'
		                        );
		                        $post_id = wp_insert_post($new_post);
		                    }
		                    $valuesArr = array();
		                    $valuesArr[] = array($post_id, 'sales_manager_name', $partner['Name']);
		                    $valuesArr[] = array($post_id, 'sales_manager_id', $partner['Identification']);
		                    $valuesArr[] = array($post_id, 'sales_manager_phone', $partner['Phone']);
		                    $valuesArr[] = array($post_id, 'sales_manager_email', $partner['Email']);
		                    $valuesArr[] = array($post_id, 'sales_manager_title', $partner['Title']);
		                    foreach ($valuesArr as $value) {
		                        update_post_meta( $value[0], $value[1], $value[2] );
		                    }
		                }
		                ?>
		                <div class="updated notice notice-success below-h2" id="partner_message"><p><?php esc_html_e('Sales Managers Imported Successfully!','store_locator'); ?></p></div>
		                <?php
		            }
		        }
		        // Import stores CSV
		        if (isset($_POST['import_stores_button'])) {
		            require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
		            $csv_mimetypes = array(
		                'text/csv',
		                'text/plain',
		                'text/comma-separated-values',
		                'application/excel',
		                'application/vnd.ms-excel',
		                'application/vnd.msexcel',
		                'application/csv',
		                'application/octet-stream',
		                'application/txt',
		            );
		            if (!$_FILES['csv-file']['tmp_name'] || !(in_array($_FILES['csv-file']['type'], $csv_mimetypes)) || $_FILES['csv-file']['size'] == 0) {
		                ?>
		                <div class="error notice notice-success below-h2" id="message"><p><?php esc_html_e('Please select CSV file.','store_locator'); ?></p></div>
		                <?php
		            } else {
		                ?>
		                <script>var addresses = [];</script>
		                <?php
		                $csv = array_map('str_getcsv', file($_FILES['csv-file']['tmp_name']));
		                $header = array_shift($csv);
						$stores = $records = array();
		                foreach ($csv as $row) {
		                    $row[6] = preg_replace("/[^0-9,.]/", "",$row[6]);   
		                    $string = str_replace(' ', '-', $row[1]); // Replaces all spaces with hyphens.
		                    $row[1] = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
		                    $row[1] = $string = str_replace('-',' ', $row[1]); 
		                    $row[1] = preg_replace('/\s+/', ' ',$row[1]);
		                    $strings = str_replace(' ', '-', $row[2]); // Replaces all spaces with hyphens.
		                    $row[2] =  $strings; // Removes special chars. addresss
		                    $row[2] = $strings = str_replace('-',' ', $row[2]); 
		                    $row[2] = preg_replace('/\s+/', ' ',$row[2]);
		                    if(!empty($row[1])){
								$row[0]=sanitize_text_field($row[0]);
								$row[1]=sanitize_text_field($row[1]);
								$row[2]=sanitize_text_field($row[2]);
								$row[3]=sanitize_text_field($row[3]);
								$row[4]=sanitize_text_field($row[4]);
								$row[5]=sanitize_text_field($row[5]);
								$row[6]=sanitize_text_field($row[6]);
								$row[7]=sanitize_text_field($row[7]);
								$row[8]=sanitize_text_field($row[8]);
								$row[9]=sanitize_text_field($row[9]);
								$row[10]=sanitize_text_field($row[10]);
								$row[11]=sanitize_text_field($row[11]);
								$row[12]=sanitize_text_field($row[12]);
								$row[13]=sanitize_text_field($row[13]);
								$row[14]=sanitize_text_field($row[14]);
		                        $stores[] =  array_combine($header, $row);
		                    }
						}
						
		                // import store's csv to db
		                update_option('my_import_stores',$stores);
		                ?>
		                <div class="updated notice notice-success below-h2" id="store_locator_message"></div>
		                <script>
		                    var addressesPerBatch = 1;
		                    var timeoutPerBatch = 1000;
		                    var result = new Array();
		                    function updateCoordinates() {
		                        if (addresses.length > 0) {
		                            setTimeout(updateCoordinates, timeoutPerBatch);
		                        } else {
		                            // update database with LatLng 
		                            jQuery('#store_locator_message').html("<p>Uploading...</p>");
		                            setTimeout(function () {
		                                 jQuery('#store_locator_message').html("<p>Stores uploaded.</p>");
		                            }, 2000);
		                        }
		                    }
		                    jQuery(document).ready(function () {
		                        updateCoordinates();
		                    });
		                </script>
		                <?php
		            }
		        }
		    }
		    ?>
		    <div class="wrap">
		        <h2><?php echo __('Import/Export => Stores/Sales Managers','store_locator'); ?></h2>
		        <div id="dashboard-widgets" class="metabox-holder">
		            <div class="postbox-container" >
		                <div class="postbox" >
		                    <div class="handlediv"><br></div>
		                    <h3 style="cursor: auto;" class="hndle">
		                        <span><?php echo __("Upload Stores", 'store_locator'); ?><small>(.csv)</small></span>
		                        <a href="<?php echo STORE_LOCATOR_PLUGIN_URL . 'sample-data/Sample_Stores.csv'; ?>" style="float: right;text-decoration: none;" download><?php echo __("Download sample ", 'store_locator'); ?></a>
		                    </h3>

		                    <div class="inside">
		                        <p>
		                        <form method="post" name="upload_form" enctype="multipart/form-data">
		                            <input type="file"  name="csv-file" />
		                            <input class="button"  name="import_stores_button" type="submit" value="<?php echo __('Upload','store_locator'); ?>" />
		                        </form>
		                        </p>

		                    </div>
		                </div>
		            </div>
		            <div class="postbox-container" >
		                <div class="postbox" >
		                    <div class="handlediv"><br></div>
		                    <h3 style="cursor: auto;" class="hndle">
		                        <a href="<?php echo STORE_LOCATOR_PLUGIN_URL . 'sample-data/Sample_Sales_Managers.csv'; ?>" style="float: right;text-decoration: none;" download><?php echo __("Download sample ", 'store_locator'); ?></a>
		                        <span><?php echo __("Import Sales Managers ", 'store_locator'); ?><small>(.csv)</small></span>
		                    </h3>
		                    <div class="inside">
		                        <p>
		                        <form method="post" name="upload_form" enctype="multipart/form-data">
		                            <input type="file"  name="csv-file" />
		                            <input class="button"  name="import_sales_button" type="submit" value="<?php echo __('Import','store_locator'); ?>" />
		                        </form>
		                        </p>

		                    </div>
		                </div>
		            </div>
		        </div>
		        
		        <div id="dashboard-widgets" class="metabox-holder">
		            <div class="postbox-container" >
		                <div class="postbox" >
		                    <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php echo __("Export Stores ", 'store_locator'); ?><small>(.csv)</small></span></h3>
		                    <div class="inside">
		                        <p>
		                        <form method="post" name="export_form">
		                            <a class="button" id="export_stores_by_cat" href="<?php echo admin_url('admin-post.php?action=printStores.csv'); ?>"  ><?php echo __("Export", 'store_locator'); ?></a>
									<select onchange="export_stores_by_cat(this.value)">
									<option value="0">All Categories</option>
									<?php
										$terms = get_terms( array(
											'taxonomy' => 'store_locator_category',
											'hide_empty' => true,
										) );
											
										foreach($terms as $term){
											?><option value="<?php echo $term->term_id ?>"><?php echo $term->name ?> </option> <?php
										}
									?>
									</select>
		                        </form>
								<script>
									function export_stores_by_cat(cat){

										var href_val='<?php echo admin_url('admin-post.php?action=printStores.csv'); ?>';
									
										href_val=href_val+"&export-by-cat="+cat;
										document.getElementById("export_stores_by_cat").href = href_val;
									}
								</script>
		                        </p>

		                    </div>
		                </div>
					
		            </div>
		            <div class="postbox-container" >
		                <div class="postbox" >
		                    <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php echo __("Export Sales Managers ", 'store_locator'); ?><small>(.csv)</small></span></h3>
		                    <div class="inside">
		                        <p>
		                        <form method="post" name="export_form">
		                            <a class="button" href="<?php echo admin_url('admin-post.php?action=printSales.csv'); ?>"  ><?php echo __("Export", 'store_locator'); ?></a>
		                        </form>
		                        </p>

		                    </div>
		                </div>
		            </div>
		        </div>
		        <div id="dashboard-widgets" class="metabox-holder">
		        <div class="postbox-container" >
		                <div class="postbox" >
		                <div class="inside">
		                        
		                        <form method="post" name="upload_form" enctype="multipart/form-data">
		                            <a class="button import_js_stores"><?php echo __('Click here To Import New/Pending Stores','store_locator'); ?> </a>
		                        </form>
								<?php
									$my_import_stores = get_option('my_import_stores');

									if(!empty($my_import_stores)){
										?>
										<form method="post" name="clear_stores_form" enctype="multipart/form-data">
											<a class="button clear_imported_stores"><?php echo __('Clear Stores','store_locator'); ?> </a>
										</form>
										<?php
									}
								?>
		                        <h5>
		                             <?php echo __('Note : Make sure all your address,country,city,state and zip code columns are properly organized in order to save marker location with google map APi. Also Please make sure the Code column value is unique for each row.','store_locator'); ?>
		                        </h5>
		                        <h3 style="
		    margin-top: 30px;
		    font-weight: bolder;
		    background: #f5fff6;
		    padding: 10px;
		" > <span id="current-count">0</span> of  <span id="tolalcount">0</span><?php echo __('have been imported','store_locator'); ?> <img class="irc_mi_wp_str" src="<?php echo apply_filters('wpmsl_loading_img',STORE_LOCATOR_PLUGIN_URL.'assets/img/loader2.png'); ?>" width="25" height="25"></h3>
		                        
		            <?php
		            
		            
		            if(!empty($my_import_stores)){
		                            
		                foreach($my_import_stores as $storess){
		                        global $wpdb;
		                        $postmeta_table = $wpdb->prefix . 'postmeta';
		                        $args = array(
		                            'post_type' => 'store_locator',
		                            'status' => 'publish',
		                            'meta_query' => array(
		                                array(
		                                    'key' => 'store_locator_code',
		                                    'value' => $storess['Code'],
		                                    'compare' => '='
		                                )
		                            )
		                        );
		                    $exist_post = get_posts($args);
		                    $store_locator_lat = get_post_meta( @$exist_post[0]->ID, 'store_locator_lat',true );        
		                    $store_locator_lng = get_post_meta( @$exist_post[0]->ID, 'store_locator_lng',true );        
		                            
		                            if(empty($store_locator_lat) and empty($store_locator_lng)){
		                                $newstores[] = $storess;
		                            }
		                }
		                $my_import_stores = "";
		                if(!empty($newstores)){
		                    update_option('my_import_stores',$newstores);
		                    $my_import_stores = $newstores;
		                }
		                
		            }
		            if(is_array($my_import_stores)){
		                echo '<br>';
		                $count = 1;
		                ?> <script>stores_json_encoded = <?php echo json_encode($my_import_stores); ?>;</script>
		                <div class='stores_div'>
		                <?php
		                foreach($my_import_stores as $key => $stores){
		                    echo '<div class="store_id_'.$stores['Code'].'">'.$count.' '.$stores['Name'].'</div><br>';
		                    $count++;
		                }
						echo '</div>';

		            }
		            ?>
		            </p>
		        </div>
		        </div>
		        </div>
		        </div>
		    </div>
		    <?php
		}
		public function import_js_stores_function() {
            if (isset($_POST['import_js_stores_post'])) {
            $import_js_stores_post = json_decode(str_replace('\\', '', $_POST['import_js_stores_post']), true);
            if(!empty($import_js_stores_post['Code'])){
            global $user_ID;
            global $wpdb;
            $postmeta_table = $wpdb->prefix . 'postmeta';
                $args = array(
                    'post_type' => 'store_locator',
                    'status' => 'publish',
                    'meta_query' => array(
                        array(
                            'key' => 'store_locator_code',
                            'value' => $import_js_stores_post['Code'],
                            'compare' => '='
                        )
                    )
                );
                $exist_post = get_posts($args);
                if($exist_post &&  $import_js_stores_post['Code']){
                    //edit existing post
                    $edit_post = array(
                        'ID' => $exist_post[0]->ID,
                        'post_title' => $import_js_stores_post['Name'],
                        'post_name' => $import_js_stores_post['Name'] . uniqid(),
                    );
                    $post_id = wp_update_post($edit_post);
                    $wpdb->delete($postmeta_table, array('post_id' => $post_id) );
                }else{
                    //add new post
                    $new_post = array(
                        'post_title' => $import_js_stores_post['Name'],
                        'post_name' => $import_js_stores_post['Name'] . uniqid(),
                        'post_status' => 'publish',
                        'post_author' => $user_ID,
                        'post_type' => 'store_locator'
                    );
                    $post_id = wp_insert_post($new_post);
                }
                $valuesArr = array();
                $sql = "INSERT INTO " . $postmeta_table . " (`post_id`, `meta_key`, `meta_value`) VALUES ";
                // update post meta
                $valuesArr[] = array($post_id, 'store_locator_name', $import_js_stores_post['Name']);
                $valuesArr[] = array($post_id, 'store_locator_address', $import_js_stores_post['Address']);
                $valuesArr[] = array($post_id, 'store_locator_country', $import_js_stores_post['Country']);
                $valuesArr[] = array($post_id, 'store_locator_state', $import_js_stores_post['State']);
                $valuesArr[] = array($post_id, 'store_locator_city', $import_js_stores_post['City']);
                //$valuesArr[] = array($post_id, 'store_locator_phone', $import_js_stores_post['Phone']);
                $valuesArr[] = array($post_id, 'store_locator_phone', str_replace(' ', '', $import_js_stores_post['Phone']));
                $valuesArr[] = array($post_id, 'store_locator_fax', $import_js_stores_post['Fax']);
                $valuesArr[] = array($post_id, 'store_locator_website', $import_js_stores_post['Website']);
                $valuesArr[] = array($post_id, 'store_locator_zipcode', $import_js_stores_post['Zipcode']);
				$valuesArr[] = array($post_id, 'store_locator_code', $import_js_stores_post['Code']);
				$valuesArr[] = array($post_id, 'store_locator_lng', $import_js_stores_post['Longitude']);
				$valuesArr[] = array($post_id, 'store_locator_lat', $import_js_stores_post['Latitude']);
				$valuesArr[] = array($post_id, 'store_locator_email', $import_js_stores_post['Email']);
				$valuesArr[] = array($post_id, 'store_locator_description', $import_js_stores_post['Description']);

                foreach ($valuesArr as $value) {
                    update_post_meta( $value[0], $value[1], $value[2] );
                    $sql .= " ('" . implode("', '", $value) . "'),";
                }
                
                if(!empty($import_js_stores_post['Category'])){
                    $temp=''; $temp2=''; $cat_ids=array();
                    $taxonomies=explode('|', $import_js_stores_post['Category']);
                    foreach ($taxonomies as $key => $group) {
                        $terms=explode(',', $group);
                        $terms=array_reverse($terms);
                        foreach ($terms as $newkey => $term) {
                            $temp=term_exists( $term, 'store_locator_category' );
                            if($temp){
                                $temp2=$temp['term_id'];
                            }
                            else
                            {
                                $new=!empty($temp2) ? wp_insert_term( $term, 'store_locator_category',array('parent'=>$temp2)) : wp_insert_term( $term, 'store_locator_category');
                                $temp2=$new['term_id'];
                            }

                        }
                        // set term to post 
                        $cat_ids[]=$temp2;
                        $temp2='';
                    }
                    $cat_ids = array_map( 'intval', $cat_ids );
                    $cat_ids = array_unique( $cat_ids );
                    $term_taxonomy_ids = wp_set_object_terms($post_id, $cat_ids, 'store_locator_category', true );
                    clean_term_cache($cat_ids, 'store_locator_category', true);
                }
                
                
                
               if(!empty($post_id) and !empty($import_js_stores_post['Code'])){
                    $my_import_stores = get_option('my_import_stores');
                    if(is_array($my_import_stores)){
                            foreach($my_import_stores as $arry){
                                if($arry['Code'] != $import_js_stores_post['Code']){
                                            $arrayss[] = $arry; 
                                }
                        
                            }
                            
                        }
                    if(is_array($my_import_stores)){
                        
	                $store_locator_API_KEY = get_option('store_locator_API_KEY');                                                                                                                   
	                $records = array('post_id' => $post_id, 'error_msg'=> @$responsedecoded->error_message, 'Code' => $import_js_stores_post['Code'] , 'Name' => $import_js_stores_post['Name'] , 'address' => $import_js_stores_post['Address'] . " " . $import_js_stores_post['Country'] . " " . $import_js_stores_post['City'] . " " . $import_js_stores_post['State'] . " " . $import_js_stores_post['Zipcode']);
	                                                                                                                                                                                            
	                $responsedecoded = json_decode(file_get_contents("https://maps.google.com/maps/api/geocode/json?address=".urlencode($records['address'])."&sensor=false&key=".$store_locator_API_KEY));
	                
	                if (empty($responsedecoded)) {
	                    "responsedecoded Error #:" . $err;
	                    update_post_meta($post_id,'responsedecoded_Error_while_getting_lng_and_lat',$err);
	                } else {
	                    if($responsedecoded->status == "OK"){
	                        update_post_meta($post_id,'store_locator_lat',$responsedecoded->results[0]->geometry->location->lat);
	                        update_post_meta($post_id,'store_locator_lng',$responsedecoded->results[0]->geometry->location->lng);
	                        $records['lat'] = $responsedecoded->results[0]->geometry->location->lat;
	                        $records['lng'] = $responsedecoded->results[0]->geometry->location->lng;
	                        $array = array();
	                        
	                    } else {
	                        $records['lat'] = 'empty';
	                        $records['lng'] = 'empty';
	                        update_post_meta($post_id,'cURL_Error_while_getting_lng_and_lat',$responsedecoded);
	                    }
	                }
	                echo json_encode($records);
	                die();
	                }
                }
                }
            }
		}
		public function export_store_locator_csv() {
		    if (!current_user_can('manage_options'))
				return;


			$args = array(
				'post_type' => 'store_locator',
				'post_status' => 'publish',
				'posts_per_page' => -1
			);
			if(isset($_GET['export-by-cat']) && $_GET['export-by-cat'] != 0){
				$args = array(
					'post_type' => 'store_locator',
					'post_status' => 'publish',
					'posts_per_page' => -1,
					'tax_query' => array(
						array(
							'taxonomy' => 'store_locator_category',
							'field'    => 'term_id',
							'terms'    => absint($_GET['export-by-cat']),
						),
					),
				);
			}

		    header('Content-Type: application/csv');
		    header('Content-Disposition: attachment; filename=Stores.csv');
		    header('Pragma: no-cache');
		    $output_handle = @fopen('php://output', 'w');
		   
		    $csv_fields = array('Code', 'Name', 'Address', 'Country', 'City', 'State', 'Phone', 'Fax', 'Website', 'Zipcode','Category','Longitude','Latitude','Email','Description');
		    fputcsv($output_handle, $csv_fields);
		    $my_query = new WP_Query($args);
		    if ($my_query->have_posts()) {
		        while ($my_query->have_posts()) : $my_query->the_post();
		            global $post;
		            $terms=get_the_terms($post->ID,'store_locator_category');
		            $categories='';
		                if(is_array($terms)):
		                    $x=0;
		                    foreach ($terms as $key => $term):
		                        if($x==0)
		                        $categories.=$term->name;
		                        else
		                         $categories.='|'.$term->name;
		                        if($term->parent!==0){
		                            $ancestors=get_ancestors( $term->parent, 'store_locator_category','taxonomy' );
		                            $categories.=','.get_term_by('id', $term->parent, 'store_locator_category')->name;
		                            if(is_array($ancestors) && count($ancestors)){
		                                foreach ($ancestors as $ancestor) {
		                                    $categories.=','.get_term_by('id', $ancestor, 'store_locator_category')->name;
		                                }
		                            }
		                        }
		                        $x++;
		                    endforeach;
		                endif;
		            $csv_fields = array(
		                get_post_meta($post->ID, 'store_locator_code', true),
		                get_post_meta($post->ID, 'store_locator_name', true),
		                get_post_meta($post->ID, 'store_locator_address', true),
		                get_post_meta($post->ID, 'store_locator_country', true),
		                get_post_meta($post->ID, 'store_locator_city', true),
		                get_post_meta($post->ID, 'store_locator_state', true),
		                get_post_meta($post->ID, 'store_locator_phone', true),
		                get_post_meta($post->ID, 'store_locator_fax', true),
		                get_post_meta($post->ID, 'store_locator_website', true),
		                get_post_meta($post->ID, 'store_locator_zipcode', true),
						$categories,
						get_post_meta($post->ID, 'store_locator_lng', true),
						get_post_meta($post->ID, 'store_locator_lat', true),
						get_post_meta($post->ID, 'store_locator_email', true),
		                get_post_meta($post->ID, 'store_locator_description', true),
		            );
		            fputcsv($output_handle, $csv_fields);
		        endwhile;
			}

		    wp_reset_query();
		    fclose($output_handle);
		    die();
		}
		public function export_sales_manager_csv() {
		    if (!current_user_can('manage_options'))
		        return;

		    header('Content-Type: application/csv');
		    header('Content-Disposition: attachment; filename=Sales_Managers.csv');
		    header('Pragma: no-cache');
		    $output_handle = @fopen('php://output', 'w');

		    $args = array(
		        'post_type' => 'sales_manager',
		        'post_status' => 'publish',
		        'posts_per_page' => -1
		    );
		    $csv_fields = array('Code', 'Title', 'Name', 'Phone','Email');
		    fputcsv($output_handle, $csv_fields);
		    $my_query = new WP_Query($args);
		    if ($my_query->have_posts()) {
		        while ($my_query->have_posts()) : $my_query->the_post();
		            global $post;
		            $csv_fields = array(
		                get_post_meta($post->ID, 'sales_manager_id', true),
		                get_post_meta($post->ID, 'sales_manager_title', true),
		                get_post_meta($post->ID, 'sales_manager_name', true),
		                get_post_meta($post->ID, 'sales_manager_phone', true),
		                get_post_meta($post->ID, 'sales_manager_email', true),
		            );
		            fputcsv($output_handle, $csv_fields);
		        endwhile;
		    }
		    wp_reset_query();

		    // Close output file stream
		    fclose($output_handle);

		    die();
		}
	}
	new WPMSL_Import_Export_Stores();
}