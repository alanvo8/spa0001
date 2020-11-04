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
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'WP Store Locator', 'store-locator'),
			]
		);
		$this->add_control(
			'address',
			[
				'label' => __( 'Address', 'store-locator'),
				'type' => Controls_Manager::TEXT,
			]
		);
		$this->add_control(
				'storesradius',
				[
						'label' => __( 'Radius', 'store-locator' ),
						'type' => Controls_Manager::SELECT,
						'default' => '500 KM',
						'options' => [
								'10' => esc_html__( '10 KM', 'store-locator' ),
								'25' => esc_html__( '25 KM', 'store-locator' ),
								'50' => esc_html__( '50 KM', 'store-locator' ),
								'100' => esc_html__( '100 KM', 'store-locator' ),
								'200' => esc_html__( '200 KM', 'store-locator' ),
								'500' => esc_html__( '500 KM', 'store-locator' ),
						],
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
		echo '<div class="address">';
		 $address = $settings['address'];
		echo '</div>';
		echo '<div class="storesradius">';
		 $storesradius = $settings['storesradius'];
		echo '</div>';
			$display_store =  do_shortcode('[store_locator_show location="'.$address.'" radius="'.$storesradius.'"]');	 
			echo $display_store;
	}
	protected function _content_template() {
		$address = "{{{ settings.address }}}";
		$storesradius = "{{{ settings.storesradius }}}";
	}
}
