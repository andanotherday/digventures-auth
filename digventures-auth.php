<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://andanotherday.com
 * @since             1.0.0
 * @package           Digventures_Auth
 *
 * @wordpress-plugin
 * Plugin Name:       DigVentures Auth
 * Plugin URI:        https://github.com/andanotherday/digventures-auth
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            AndAnotherDay
 * Author URI:        https://andanotherday.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       digventures-auth
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'DIGVENTURES_AUTH_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-digventures-auth-activator.php
 */
function activate_digventures_auth() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-digventures-auth-activator.php';
	Digventures_Auth_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-digventures-auth-deactivator.php
 */
function deactivate_digventures_auth() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-digventures-auth-deactivator.php';
	Digventures_Auth_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_digventures_auth' );
register_deactivation_hook( __FILE__, 'deactivate_digventures_auth' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-digventures-auth.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_digventures_auth() {

	$plugin = new Digventures_Auth();
	$plugin->run();

}
run_digventures_auth();
