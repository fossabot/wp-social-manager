<?php
/**
 * Plugin Name: WP Social Manager
 * Plugin URI: http://github.com/tfirdaus/wp-social-manager
 * Description: Optimize your website social presence in social media.
 * Version: 1.0.0
 * Author: Thoriq Firdaus
 * Author URI: github.com/tfirdaus
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * Requires at least: 4.4
 * Tested up to: 4.6
 *
 * Text Domain:       wp-social-manager
 * Domain Path:       /languages
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @since 1.0.0
 *
 * @author Thoriq Firdaus <tfirdau@outlook.com>
 *
 * @package WPSocialManager
 */

namespace XCo\WPSocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * The plugin directory path.
 *
 * @var string
 */
$plugin_dir = plugin_dir_path( __FILE__ );

/*
 * Check if the website pass the requirement.
 * If not, deactivate the plugin and print an admin notice.
 */
require_once( $plugin_dir . 'includes/class-requirements.php' );

/**
 * Defines the plugin requirement.
 *
 * @since 1.0.0
 * @var Requirements
 */
$require = new Requirements( 'WP Social Manager',
	plugin_basename( __FILE__ ), array(
		'PHP' => '5.3.0',
		'WordPress' => '4.5',
	)
);

if ( false === $require->pass() ) { // If the requirement is not meet.
	$require->halt(); // Do not activate the plugin.
	return;
}

/*
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once( $plugin_dir . 'includes/class-core.php' );

/**
 * Begins execution of the plugin.
 *
 * @since 1.0.0
 */
function run() {
	new Core( array(
		'version' => '1.0.0',
		'plugin_name' => 'wp-social-manager', // Used for slug, and scripts and styles handle.
		'plugin_opts' => 'wp_social_manager', // Used for database name or meta key prefix.
	) );
}
run();
