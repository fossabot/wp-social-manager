<?php
/**
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              github.com/tfirdaus
 * @since             1.0.0
 * @package           WP_Social_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       WP Social Manager
 * Plugin URI:        http://github.com/tfirdaus/wp-social-manager
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Thoriq Firdaus
 * Author URI:        github.com/tfirdaus
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-social-manager
 * Domain Path:       /languages
 */

namespace XCo\WPSocialManager;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$plugin_dir = trailingslashit( plugin_dir_path( __FILE__ ) );

/**
 * Check if the website pass the requirement.
 * If not, deactivate the plugin and print an admin notice.
 */
require_once( $plugin_dir . 'includes/class-requirements.php' );

/**
 * [$require description]
 * @var Requirements
 */
$require = new Requirements( 'WP Social Manager',
	plugin_basename( __FILE__ ), array(
		'PHP' => '5.3.0',
		'WordPress' => '4.5',
	)
);

if ( false === $require->pass() ) {
	$require->halt();
	return;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once( $plugin_dir . 'includes/class-core.php' );

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run() {
	new Core( array(
		'version' => '0.1.0',
		'plugin_name' => 'wp-social-manager',
		'plugin_opts' => 'wp_social_manager'
	) );
}
run();
