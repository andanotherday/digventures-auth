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

    register_rest_route('ddt/v1', '/user/update-profile-image', [
      'methods' => 'POST',
      'callback' => array($this, 'update_user_profile_image')
    ]);

    /** Project */
    register_rest_route('ddt/v1', '/project/users', [
      'methods' => 'POST',
      'callback' => array($this, 'fetch_project_users')
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
        'message' => 'User updated',
        'status' => 'success',
        'data' => $this->user_data_response($user->ID)
      ];
    
      wp_send_json($response);
      die();
    }

    wp_send_json(['message' => 'No user found', 'status' => 'failed']);
    die();
  }


  public function update_user_profile_image(WP_REST_Request $request) {
    $body = $request->get_params();
    $files = $request->get_file_params();
    
    $id = (int) $body['id'];
    $user = (object) get_post($id);

    if (empty($user) || !empty($user) && $user->post_type !== 'ddt_users') {
      wp_send_json(['message' => 'No user found', 'status' => 'failed']);
      die();
    } else {
      $file_upload = $this->handle_file_upload($files['image']);

      if ($file_upload['success']) {
        set_post_thumbnail($user->ID, $file_upload['id']);

        /** Return data */
        $response = [
          'message' => 'Profile image updated',
          'status' => 'success',
          'data' => $this->user_data_response($user->ID)
        ];
      
        wp_send_json($response);
        die();
      } else {
        wp_send_json(['message' => 'Unable to upload image', 'status' => 'failed']);
        die();
      }
    }

    wp_send_json(['message' => 'No user found', 'status' => 'failed']);
    die();
  }


  /** Fetch project users */
  public function fetch_project_users(WP_REST_Request $request) {
    $body = $request->get_params();
    $project_id = $body['project_id'];

    $users = get_posts(array(
      'post_type' => 'ddt_users',
      'posts_per_page' => -1
    ));

    if (!empty($users) && is_array($users) && count($users) > 0) {
      $formatted_users = array();

      foreach ($users as $user) {
        $projects = get_field('ddt_projects', $user->ID) ?: array();

        if (in_array($project_id, $projects)) {
          array_push($formatted_users, $this->user_data_response($user->ID, array('biography')));
        }
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

  public function handle_file_upload($file) {
    $wordpress_upload_dir = wp_upload_dir();
    // $wordpress_upload_dir['path'] is the full server path to wp-content/uploads/2017/05, for multisite works good as well
    // $wordpress_upload_dir['url'] the absolute URL to the same folder, actually we do not need it, just to show the link to file
    $i = 1; // number of tries when the file with the same name is already exists

    $new_file_path = $wordpress_upload_dir['path'].'/'.time().'-'.$file['name'];
    $new_file_url = $wordpress_upload_dir['url'].'/'.time().'-'.$file['name'];

    $new_file_mime = mime_content_type($file['tmp_name']);

    if (empty($file)) {
      return [
        'success' => false,
        'message' => 'File is not selected'
      ];
    }

    if ($file['error']) {
      return [
        'success' => false,
        'message' => $file['error']
      ];
    }
      
    if ($file['size'] > wp_max_upload_size()) {
      return [
        'success' => false,
        'message' => 'File should be less than '.size_format(wp_max_upload_size(), 2)
      ];
    }

    if (!in_array($new_file_mime, get_allowed_mime_types())) {
      return [
        'success' => false,
        'message' => 'This file type is not allowed'
      ];
    }
      
    while (file_exists($new_file_path)) {
      $i++;
      $new_file_path = $wordpress_upload_dir['path'].'/'.time().'-'.$file['name'];
      $new_file_url = $wordpress_upload_dir['url'].'/'.time().'-'.$file['name'];
    }

    /** It looks like everything is OK */
    if (move_uploaded_file($file['tmp_name'], $new_file_path)) {
      $upload_id = wp_insert_attachment([
        'guid'           => $new_file_url,
        'post_mime_type' => $new_file_mime,
        'post_title'     => preg_replace('/\.[^.]+$/', '', $file['name']),
        'post_content'   => '',
        'post_status'    => 'inherit'
      ], $new_file_path);

      /** wp_generate_attachment_metadata() won't work if you do not include this file */
      require_once(ABSPATH . 'wp-admin/includes/image.php');

      /** Generate and save the attachment metas into the database */
      wp_update_attachment_metadata($upload_id, wp_generate_attachment_metadata($upload_id, $new_file_path));

      return [
        'success' => true,
        'id' => $upload_id
      ];
    }
  }
}