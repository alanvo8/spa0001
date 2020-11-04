<?php
namespace HelloWorld;
use HelloWorld\Widgets\Wpmsl_Store_Locator;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Wpmsl_Store_Locator_Elementer {
	public function __construct() {
		$this->add_actions();
	}
	private function add_actions() {
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'on_widgets_registered' ] );

		add_action( 'elementor/frontend/after_register_scripts', function() {
			wp_register_script( 'wpmsl-store-locator', STORE_LOCATOR_PLUGIN_URL .'builders/elementor-block/assets/js/hello-world.js', [ 'jquery' ], false, true );
		} );
	}
	public function on_widgets_registered() {
		$this->includes();
		$this->register_widget();
	}
	private function includes() {
		$maps=get_posts(array('post_type' => 'maps'));
		$maps_arr=array();
		if(empty($maps)){
			require __DIR__ . '/widgets/wpmsl_widget.php';
		}else{
			require __DIR__ . '/widgets/wpmsl_multi_maps.php';
		}
	}
	private function register_widget() {
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Wpmsl_Store_Locator() );
	}
}
new Wpmsl_Store_Locator_Elementer();