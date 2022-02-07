<?php

class Digventures_Cpt {
  public function __construct() {
    /** CPT */
    add_action('init', array($this, 'create_auth_cpt'));

    /** ACF */
    add_action('acf/init', array($this, 'create_user_details'));
    add_filter('acf/prepare_field/name=ddt_projects', array($this, 'acf_load_ddt_projects_field_choices'));

    add_theme_support('post-thumbnails', array('ddt_users'));
  }

  public static function create_auth_cpt() {
    register_post_type('ddt_users', array(
      'labels' => array(
        'name'                  => __( 'DDT Users', 'digventures' ),
        'singular_name'         => __( 'DDT User', 'digventures' ),
        'menu_name'             => __( 'DDT Users', 'digventures' ),
        'name_admin_bar'        => __( 'DDT User', 'digventures' ),
        'add_new'               => __( 'Add New', 'digventures' ),
        'add_new_item'          => __( 'Add New DDT User', 'digventures' ),
        'new_item'              => __( 'New DDT User', 'digventures' ),
        'edit_item'             => __( 'Edit DDT User', 'digventures' ),
        'view_item'             => __( 'View DDT User', 'digventures' ),
        'all_items'             => __( 'All DDT Users', 'digventures' ),
        'search_items'          => __( 'Search DDT Users', 'digventures' ),
        'parent_item_colon'     => __( 'Parent DDT Users:', 'digventures' ),
        'not_found'             => __( 'No DDT Users found.', 'digventures' ),
        'not_found_in_trash'    => __( 'No DDT Users found in Trash.', 'digventures' ),
        'featured_image'        => __( 'DDT User Cover Image', 'digventures' ),
        'archives'              => __( 'DDT User archives', 'digventures' ),
        'insert_into_item'      => __( 'Insert into DDT User', 'digventures' ),
        'uploaded_to_this_item' => __( 'Uploaded to this DDT User', 'digventures' ),
        'filter_items_list'     => __( 'Filter DDT Users list', 'digventures' ),
        'items_list_navigation' => __( 'DDT Users list navigation', 'digventures' ),
        'items_list'            => __( 'DDT Users list', 'digventures' ),
      ),
      'public'             => true,
      'publicly_queryable' => true,
      'show_ui'            => true,
      'show_in_menu'       => true,
      'query_var'          => true,
      'rewrite'            => array( 'slug' => 'ddt-users' ),
      'capability_type'    => 'post',
      'has_archive'        => true,
      'hierarchical'       => false,
      'menu_position'      => null,
      'menu_icon'          => 'dashicons-admin-users',
      'supports'           => array( 'title', 'thumbnail' ),
    ));
  }

  public function create_user_details() {
    if (function_exists('acf_add_local_field_group')) {
      acf_add_local_field_group(array(
        'key' => 'group_6193ed9f0e8d6',
        'title' => 'DDT Users',
        'fields' => array(
          array(
            'key' => 'field_61b9c43d77c60',
            'label' => 'First Name',
            'name' => 'first_name',
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
          array(
            'key' => 'field_61b9c44877c61',
            'label' => 'Last Name',
            'name' => 'last_name',
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
          array(
            'key' => 'field_6193ee16caad4',
            'label' => 'Role',
            'name' => 'role',
            'type' => 'select',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'choices' => array(
              'user' => 'User',
              'staff' => 'Staff',
              'admin' => 'Admin',
            ),
            'default_value' => 'user',
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 1,
            'ajax' => 0,
            'return_format' => 'value',
            'placeholder' => '',
          ),
          array(
            'key' => 'field_6193ee4ecaad5',
            'label' => 'Projects',
            'name' => 'ddt_projects',
            'type' => 'checkbox',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'choices' => array(
            ),
            'allow_custom' => 0,
            'default_value' => array(
            ),
            'layout' => 'vertical',
            'toggle' => 0,
            'return_format' => 'value',
            'save_custom' => 0,
          ),
          array(
            'key' => 'field_6194fd1921ec2',
            'label' => 'Dark Mode',
            'name' => 'dark_mode',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'message' => '',
            'default_value' => 0,
            'ui' => 1,
            'ui_on_text' => '',
            'ui_off_text' => '',
          ),
          array(
            'key' => 'field_61fc020c344bc',
            'label' => 'Visibility',
            'name' => 'visibility',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'message' => '',
            'default_value' => 1,
            'ui' => 1,
            'ui_on_text' => '',
            'ui_off_text' => '',
          ),
          array(
            'key' => 'field_61b9c347ea2b7',
            'label' => 'Biography',
            'name' => 'biography',
            'type' => 'textarea',
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
            'maxlength' => '',
            'rows' => '',
            'new_lines' => '',
          ),
        ),
        'location' => array(
          array(
            array(
              'param' => 'post_type',
              'operator' => '==',
              'value' => 'ddt_users',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'acf_after_title',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
      ));
    }
  }

  /** Fetch projects from DDT and provide them as checkboxes in ACF field */
  function acf_load_ddt_projects_field_choices($field) {
      
    /** Reset choices */
    $field['choices'] = [];

    /** Get the URL from optons */
    $platform_api_url = get_field('ddt_platform_api_url', 'option');

    /** Get projects from DDT DB */
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $platform_api_url.'/projects/getProjects.php');
    $result = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($result, true);

    if (!empty($data) && is_array($data) && count($data) > 0) {
      foreach ($data as $project) {
        /** Append fetched choices */
        $field['choices'][$project['project_id']] = $project['project_long_name'];
      }
    }

    return $field;
  }
}
