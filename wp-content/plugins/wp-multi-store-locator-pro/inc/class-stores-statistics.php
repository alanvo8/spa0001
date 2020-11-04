<?php 
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}  
if(!class_exists('WPMSL_Stores_Statistics')){
	class WPMSL_Stores_Statistics{
		public function __construct(){
			// add submenu page
			add_action('admin_menu', array($this,'register_submenu_page'));
			// get Statistics Ajax
			add_action('wp_ajax_show_store_statistics', array($this,'show_store_statistics'));
		}
		public function register_submenu_page(){
			add_submenu_page('edit.php?post_type=store_locator', __('Statistics','store_locator'), __('Statistics','store_locator'), 'manage_options', 'statistics_submenu_page', array($this,'statistics_submenu_page_callback'));
		}
		public function statistics_submenu_page_callback() {
		    global $wpdb;
		    $args = array(
		        'post_type' => 'store_locator',
		        'post_status' => 'publish',
		        'posts_per_page' => -1
		    );
		    $stores = get_posts($args);
		    $posts_table = $wpdb->prefix . 'posts';
		    $transactions = $wpdb->get_results("SELECT ps.post_title as store, count(tr.post_id) as total_count FROM $posts_table ps LEFT JOIN store_locator_transactions tr ON tr.post_id=ps.ID WHERE ps.post_type='store_locator' AND ps.post_status='publish' GROUP BY ps.ID");
		    include STORE_LOCATOR_PLUGIN_PATH . 'views/statistics.php';
		}
		public function show_store_statistics() {
		    $store_id = NULL;
		    $transactions = array();
		    if(isset($_POST['store_id']) && $_POST['store_id']){
		        $store_id = $_POST['store_id'];
		        global $wpdb;
		        $transactions = $wpdb->get_results("SELECT DATE_FORMAT(date, '%Y-%m-%d') as date, COUNT(*) as total_count FROM store_locator_transactions WHERE post_id=".$store_id." GROUP BY DATE_FORMAT(date, '%Y-%m-%d')");
		        $piDataQuery = $wpdb->get_results("SELECT COUNT(*) as total_count, user_id as user FROM store_locator_transactions WHERE post_id=".$store_id." GROUP BY user_id");
		        $piData = array(array('user'=>'Visitor', 'total_count'=> 0),array('user'=>'Registered Users', 'total_count'=> 0));
		        foreach ($piDataQuery as $record) {
		            if($record->user == 0){
		                $piData[0]['total_count'] += $record->total_count;
		            }else{
		                $piData[1]['total_count'] += $record->total_count;
		            }
		        }
		        include STORE_LOCATOR_PLUGIN_PATH . 'views/statistics_single_store.php';
		    }
		    wp_die();
		}
	}
	new WPMSL_Stores_Statistics();
}