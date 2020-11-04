<?php

// Before Visual Composer block 
add_action( 'vc_before_init', 'vc_wpmsl_init_actions' );
function vc_wpmsl_init_actions() {
	$nomaps=get_posts(array('post_type' => 'maps'));
    // Require new custom Element
    if(empty($nomaps))
    	require_once( plugin_dir_path( __FILE__ ).'/vc-blocks/wpmulti-store-element.php' ); 
	else
		require_once( plugin_dir_path( __FILE__ ).'/vc-blocks/wpmulti-store-maps.php' );
}   
// Before Visual Composer block end

// Divi Builder Block code here 
function DS_Custom_Modules(){
	if(class_exists("ET_Builder_Module")){
		$nomaps=get_posts(array('post_type' => 'maps'));
	    if(empty($nomaps))
 			include("divi-block/custom-pb-wpmsl-module.php");
	 	else
	 		include("divi-block/wpmulti-store-maps-divi.php");
 	}
}

function Prep_DS_Custom_Modules(){
 global $pagenow;

$is_admin = is_admin();
 $action_hook = $is_admin ? 'wp_loaded' : 'wp';
 $required_admin_pages = array( 'edit.php', 'post.php', 'post-new.php', 'admin.php', 'customize.php', 'edit-tags.php', 'admin-ajax.php', 'export.php' ); // list of admin pages where we need to load builder files
 $specific_filter_pages = array( 'edit.php', 'admin.php', 'edit-tags.php' );
 $is_edit_library_page = 'edit.php' === $pagenow && isset( $_GET['post_type'] ) && 'et_pb_layout' === $_GET['post_type'];
  $is_role_editor_page = 'admin.php' === $pagenow && isset( $_GET['page'] ) && 'et_divi_role_editor' === $_GET['page'];
 $is_import_page = 'admin.php' === $pagenow && isset( $_GET['import'] ) && 'wordpress' === $_GET['import'];
 $is_edit_layout_category_page = 'edit-tags.php' === $pagenow && isset( $_GET['taxonomy'] ) && 'layout_category' === $_GET['taxonomy'];

if ( ! $is_admin || ( $is_admin && in_array( $pagenow, $required_admin_pages ) && ( ! in_array( $pagenow, $specific_filter_pages ) || $is_edit_library_page || $is_role_editor_page || $is_edit_layout_category_page || $is_import_page ) ) ) {
 add_action($action_hook, 'DS_Custom_Modules', 9789);
 }	
}
Prep_DS_Custom_Modules();

// Divi Builder Block code here end //

//*****************************************************//
/////////////--- Elementor Block -----////////////////////
function obj_wpmsl_load() {
	//if( class_exists( 'elementor' ) ) {

	// Load localization file
	load_plugin_textdomain( 'hello-world' );

	// Notice if the Elementor is not active
	if ( ! did_action( 'elementor/loaded' ) ) {
		if( function_exists('hello_world_fail_load') ){
			add_action( 'admin_notices', 'hello_world_fail_load' );
		}
		return;
	}

	// Check version required
	$elementor_version_required = '1.0';
	if ( ! version_compare( ELEMENTOR_VERSION, $elementor_version_required, '>=' ) ) {
		add_action( 'admin_notices', 'hello_world_fail_load_out_of_date' );
		return;
	}

	// Require the main plugin file
	require('elementor-block/plugin.php' );
	// Plugin is active
	//}
}
add_action( 'plugins_loaded', 'obj_wpmsl_load' );
//*****************************************************//
/////////////--- Elementor Block -----////////////////////

///---------------- Beaver Builder Block code --------////
define( 'FL_MODULE_EXAMPLES_DIR', plugin_dir_path( __FILE__ ) );
define( 'FL_MODULE_EXAMPLES_URL', plugins_url( '/', __FILE__ ) );

/**
 * Custom modules
 */
function fl_load_module_examples() {
	if ( class_exists( 'FLBuilder' ) ) {
	    require_once 'beaver-builder-block/beaver-block.php';
	    //require_once 'beaver-builder-block/example/example.php';
	}
}
add_action( 'init', 'fl_load_module_examples' );

/**
 * Custom fields
 */
function fl_my_custom_field( $name, $value, $field ) {
    echo '<input type="text" class="text text-full" name="' . $name . '" value="' . $value . '" />';
}
add_action( 'fl_builder_control_my-custom-field', 'fl_my_custom_field', 1, 3 );

/**
 * Custom field styles and scripts
 */
function fl_my_custom_field_assets() {
    if ( class_exists( 'FLBuilderModel' ) && FLBuilderModel::is_builder_active() ) {
        wp_enqueue_style( 'my-custom-fields', FL_MODULE_EXAMPLES_URL . 'assets/css/fields.css', array(), '' );
        wp_enqueue_script( 'my-custom-fields', FL_MODULE_EXAMPLES_URL . 'assets/js/fields.js', array(), '', true );
    }
}
add_action( 'wp_enqueue_scripts', 'fl_my_custom_field_assets' );