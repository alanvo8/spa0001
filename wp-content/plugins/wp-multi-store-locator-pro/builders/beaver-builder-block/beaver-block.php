<?php

/**
 * This is an example module with only the basic
 * setup necessary to get it working.
 *
 * @class FLBasicExampleModule
 */
class FLBasicExampleModule extends FLBuilderModule {

    /** 
     * Constructor function for the module. You must pass the
     * name, description, dir and url in an array to the parent class.
     *
     * @method __construct
     */  
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __('WP Multi Store', 'fl-builder'),
            'description'   => __('WP Multi Store Locator module.', 'fl-builder'),
            'category'		=> __('Advanced Modules', 'fl-builder'),
            'dir'           => FL_MODULE_EXAMPLES_DIR . 'beaver-builder-block/',
            'url'           => FL_MODULE_EXAMPLES_URL . 'beaver-builder-block/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
        ));
    }
}

/**
 * Register the module and its form settings.
 */
    $nomaps=get_posts(array('post_type' => 'maps'));
    // Require new custom Element
    if(empty($nomaps)){
        FLBuilder::register_module('FLBasicExampleModule', array(
            'general'       => array( // Tab
                'title'         => __('General', 'fl-builder'), // Tab title
                'sections'      => array( // Tab Sections
                    'general'       => array( // Section
                        'title'         => __('Add Address and Radius', 'fl-builder'), // Section Title
                        'fields'        => array( // Section Fields
                            'text_field'     => array(
                                'type'          => 'text',
                                'label'         => __('Enter Address', 'fl-builder'),
                                'default'       => '',
                                'maxlength'     => '',
                                'size'          => '100',
                                'placeholder'   => 'Midtown, NY, United States ',
                                'class'         => 'my-css-class',
                                'description'   => 'Add address for WP Multi Store Locator to show multi stores',
                                'help'          => 'Cingebant haec pressa dei quisquis quia mentisque mutastis terris longo fixo ille tum sponte volucres ignea boreas origo satus.',
                                'preview'         => array(
                                    'type'             => 'css',
                                    'selector'         => '.fl-example-text',
                                    'property'         => 'font-size',
                                    'unit'             => 'px'
                                )
                            ),
                            'select_field' => array(
        					  'type'          => 'select',
                                'label'         => __('Select Radius', 'fl-builder'),
                                'default'       => '500',
                                'options'       => array(
                                    ''      => __('Select Radius', 'fl-builder'),
        							'5'      => __('5 KM', 'fl-builder'),
        							'10'      => __('10 KM', 'fl-builder'),
                                    '25'      => __('25 KM', 'fl-builder'),
        							'50'      => __('50 KM', 'fl-builder'),
        							'100'      => __('100 KM', 'fl-builder'),
        							'200'      => __('200 KM', 'fl-builder'),
        							'500'      => __('500 KM', 'fl-builder'),
                                )
                            ),
                            'color_field'    => array(
                                'type'          => 'color',
                                'label'         => __('Color Picker', 'fl-builder'),
                                'default'       => '333333',
                                'show_reset'    => true,
                                'preview'         => array(
                                    'type'            => 'css',
                                    'selector'        => '.fl-example-text',
                                    'property'        => 'color'
                                )
                            ),
                        )
                    )
                )
            )
        ));
    }else{
        $maps=get_posts(array('post_type' => 'maps','post_status'=>'publish','posts_per_page'=>-1));
        $maps_arr=array();
        if(!empty($maps)){
            foreach ($maps as $key => $value) {
                $id=$value->ID;
                $maps_arr[$value->ID]=$value->post_title;
            }
        }
        FLBuilder::register_module('FLBasicExampleModule', array(
            'general'       => array( // Tab
                'title'         => __('General', 'fl-builder'), // Tab title
                'sections'      => array( // Tab Sections
                    'general'       => array( // Section
                        'title'         => __('Add Map', 'fl-builder'), // Section Title
                        'fields'        => array( // Section Fields
                            'map_id' => array(
                              'type'          => 'select',
                                'label'         => __('Select Radius', 'fl-builder'),
                                'default'       => '500',
                                'options'       =>  $maps_arr
                            ),
                        )
                    )
                )
            )
        ));
    }