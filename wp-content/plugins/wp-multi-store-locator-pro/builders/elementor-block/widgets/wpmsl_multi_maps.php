<?php
namespace HelloWorld\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Wpmsl_Store_Locator extends Widget_Base {
	public function get_name() {
		return 'wpmsl-store-locator';
	}
	public function get_title() {
		return esc_html__( 'WP Multi Store Locator', 'store-locator');
	}
	public function get_icon() {
		return 'eicon-google-maps';
	}
	public function get_categories() {
		return [ 'general-elements' ];
	}
	public function get_script_depends() {
		return [ 'wpmsl-store-locator' ];
	}
	protected function _register_controls() {
		$maps=get_posts(array('post_type' => 'maps','post_status'=>'publish','posts_per_page'=>-1));
		$maps_arr=array();
		if(!empty($maps)){
			foreach ($maps as $key => $value) {
				$id=$value->ID;
				$maps_arr[$value->ID]=$value->post_title;
			}
		}
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'WP Store Locator', 'store-locator'),
			]
		);
		$this->add_control(
				'mapid',
				[
						'label' => __( 'Map', 'store-locator' ),
						'type' => Controls_Manager::SELECT,
						'default' => '',
						'options' => $maps_arr,
						'selectors' => [
								'{{WRAPPER}} .address' => 'text-transform: {{VALUE}};',
						],
				]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Style', 'store-locator'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->end_controls_section();	
	}
	protected function render() {
		$settings = $this->get_settings();
		echo '<div class="mapid">';
		 $mapid = $settings['mapid'];
		echo '</div>';
			$display_store =  do_shortcode('[wp_multi_store_locator_map id="'.$mapid.'"]');	 
			echo $display_store;
	}
	protected function _content_template() {
		$address = "{{{ settings.address }}}";
	}
}
