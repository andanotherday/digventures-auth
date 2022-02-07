<?php

class Digventures_Options_Page {
  public function __construct() {
    add_action('acf/init', array($this, 'digventures__add_settings_page'));
    add_action('acf/init', array($this, 'digventures__add_settings_page_options'));
  }

  public function digventures__add_settings_page() {
    if (function_exists('acf_add_options_page')) {
      acf_add_options_sub_page(array(
        'page_title' 	=> 'DigVentures Plugin Settings',
        'menu_title'	=> 'DigVentures Plugin Settings',
        'parent_slug'	=> 'options-general.php',
      ));
    }
  }

  public function digventures__add_settings_page_options() {
    if (function_exists('acf_add_local_field_group')) {
      acf_add_local_field_group(array(
        'key' => 'group_61fc0b00f32f3',
        'title' => 'DigVentures Plugin Settings',
        'fields' => array(
          array(
            'key' => 'field_61fc0b1561425',
            'label' => 'DDT Platform API URL',
            'name' => 'ddt_platform_api_url',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'maxlength' => '',
          ),
        ),
        'location' => array(
          array(
            array(
              'param' => 'options_page',
              'operator' => '==',
              'value' => 'acf-options-digventures-plugin-settings',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
      ));
    }
  }
}