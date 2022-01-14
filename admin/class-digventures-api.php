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

    /** Fetch all users */
    register_rest_route('ddt/v1', '/users/fetch', [
      'methods' => 'GET',
      'callback' => array($this, 'fetch_users')
    ]);

    /** Update user */
    register_rest_route('ddt/v1', '/user/update', [
      'methods' => 'POST',
      'callback' => array($this, 'update_user')
    ]);
  }

  public function user_data_response($id = false, $unset = array()) {
    $response = array();

    if ($id) {
      $firstname = (!empty(get_field('first_name', $id))) ? get_field('first_name', $id) : null;
      $lastname = (!empty(get_field('last_name', $id))) ? get_field('last_name', $id) : null;
      $email = get_the_title($id);
      $role = (!empty(get_field('role', $id))) ? get_field('role', $id) : 'user';
      $projects = get_field('ddt_projects', $id) ?: array();
      $dark_mode = !empty(get_field('dark_mode', $id)) ? get_field('dark_mode', $id) : false;
      $biography = !empty(get_field('biography', $id)) ? get_field('biography', $id) : '';
      $profile_image = get_the_post_thumbnail_url($id, 'full') ?: false;

      $response = array(
        'id' => $id,
        'email' => $email,
        'first_name' => $firstname,
        'last_name' => $lastname,
        'role' => $role,
        'profile_image' => $profile_image,
        'projects' => $projects,
        'dark_mode' => $dark_mode,
        'biography' => $biography
      );

      if (!empty($unset) && count($unset) > 0) {
        foreach ($unset as $property) {
          unset($response[$property]);
        }
      }
    }

    return $response;
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
      $response = [
        'message' => 'User already exists',
        'status' => 'failed',
        'data' => $this->user_data_response($post->ID)
      ];
  
      wp_send_json($response);
      die();
    }

    if ($post_id) {
      $response = [
        'message' => 'User created',
        'status' => 'success',
        'data' => $this->user_data_response($post_id)
      ];
  
      wp_send_json($response);
      die();
    } else {
      wp_send_json(array('message' => 'User not created', 'status' => 'failed'));
      die();
    }
  }


  public function fetch_user($params) {
    $user = (object) get_post($params['id']);

    if (empty($user) || !empty($user) && $user->post_type !== 'ddt_users') {
      wp_send_json(['message' => 'No user found', 'status' => 'failed']);
      die();
    } else {
      $response = [
        'message' => 'User Fetched',
        'status' => 'success',
        'data' => $this->user_data_response($user->ID)
      ];
    
      wp_send_json($response);
      die();
    }

    wp_send_json(['message' => 'No user found', 'status' => 'failed']);
    die();
  }


  public function fetch_users() {
    $users = get_posts(array(
      'post_type' => 'ddt_users',
      'posts_per_page' => -1
    ));

    if (!empty($users) && is_array($users) && count($users) > 0) {
      $formatted_users = array();

      foreach ($users as $user) {
        array_push($formatted_users, $this->user_data_response($user->ID, array('biography')));
      }

      $response = [
        'message' => 'Users Fetched',
        'status' => 'success',
        'data' => $formatted_users
      ];
    
      wp_send_json($response);
      die();
    }

    wp_send_json(['message' => 'No users found', 'status' => 'failed']);
    die();
  }


  public function update_user(WP_REST_Request $request) {
    $body = $request->get_params();

    $id = (int) $body['id'];
    $user = (object) get_post($id);

    if (empty($user) || !empty($user) && $user->post_type !== 'ddt_users') {
      wp_send_json(['message' => 'No user found', 'status' => 'failed']);
      die();
    } else {
      $fields = json_decode($body['fields'], true);

      if (!empty($fields) && is_array($fields) && count($fields) > 0) {
        foreach ($fields as $key => $value) {
          if ($key == 'dark_mode') {
            $value = (int) $value;
          }

          update_field($key, $value, $user->ID);
        }
      } else {
        wp_send_json(['message' => 'Unable to update user', 'status' => 'failed']);
        die();
      }

      /** Return data */
      $response = [
        'message' => 'User Updated',
        'status' => 'success',
        'data' => $this->user_data_response($user->ID)
      ];
    
      wp_send_json($response);
      die();
    }

    wp_send_json(['message' => 'No user found', 'status' => 'failed']);
    die();
  }
}