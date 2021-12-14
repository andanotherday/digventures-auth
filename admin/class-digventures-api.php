<?php

class Digventures_Api {
  public function __construct() {
    add_action('rest_api_init', array($this, 'register_routes'));
  }

  public function register_routes() {
    /** Create user */
    register_rest_route('ddt/v1', '/user/create', [
      'methods' => 'POST',
      'callback' => array($this, 'create_user')
    ]);

    /** Fetch user */
    register_rest_route('ddt/v1', '/user/(?P<id>\d+)', [
      'methods' => 'GET',
      'callback' => array($this, 'fetch_user')
    ]);
  }

  public function create_user(WP_REST_Request $request) {
    $body = $request->get_params();

    $first_name = $body['first_name'];
    $last_name = $body['last_name'];
    $email = $body['email'];
    $role = $body['role'];

    if (empty($body) || empty($email)) {
      wp_send_json(array(
        'message' => 'No data provided',
        'status' => 'failed'
      ));
      die();
    }
    
    /** Check if that user already exists */
    $post = get_page_by_title($email, OBJECT, 'ddt_users');

    /** If they don't - create them */
    if ($post == null) {
      $post_id = wp_insert_post([
        'post_type' => 'ddt_users',
        'post_title' => $email,
        'post_status' => 'publish',
        'comment_status' => 'closed',
        'ping_status' => 'closed'
      ]);

      /** Update with other details */
      update_field('first_name', $first_name, $post_id);
      update_field('last_name', $last_name, $post_id);
      update_field('role', $role, $post_id);
    } else {
      /** Else return them */
      $post_id = $post->ID;
    }

    if ($post_id) {
      $firstname = (!empty(get_field('first_name', $post_id))) ? get_field('first_name', $post_id) : null;
      $lastname = (!empty(get_field('last_name', $post_id))) ? get_field('last_name', $post_id) : null;
      $role = (!empty(get_field('role', $post_id))) ? get_field('role', $post_id) : 'user';
  
      $response = [
        'message' => 'User created',
        'status' => 'success',
        'id' => $post_id,
        'email' => $email,
        'first_name' => $firstname,
        'last_name' => $lastname,
        'role' => $role
      ];
  
      wp_send_json($response);
      die();
    } else {
      wp_send_json(array('message' => 'User not created', 'status' => 'failed'));
      die();
    }
  }


  public function fetch_user($params) {
    $user = get_post($params['id']);

    if (empty($user) || !empty($user) && $user->post_type !== 'ddt_users') {
      wp_send_json(['message' => 'No user found', 'status' => 'failed']);
      die();
    } else {
      $firstname = (!empty(get_field('first_name', $user->ID))) ? get_field('first_name', $user->ID) : null;
      $lastname = (!empty(get_field('last_name', $user->ID))) ? get_field('last_name', $user->ID) : null;
      $role = (!empty(get_field('role', $user->ID))) ? get_field('role', $user->ID) : 'user';

      $response = [
        'message' => 'User Fetched',
        'status' => 'success',
        'first_name' => $firstname,
        'last_name' => $lastname,
        'role' => $role,
        'profile_image' => get_the_post_thumbnail_url($user->ID, 'full') ?: false
      ];
    
      wp_send_json($response);
      die();
    }

    wp_send_json(['message' => 'No user found', 'status' => 'failed']);
    die();
  }
}