<?php
/**
 * Plugin Name: Social Manager by NineCodes
 * Plugin URI: http://wordpress.org/plugins/ninecodes-social-manager
 * Description: Optimize your website presence in social media.
 * Version: 1.0.0-beta.1
 * Author: Thoriq Firdaus
 * Author URI: https://github.com/tfirdaus
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * Requires at least: 4.5
 * Tested up to: 4.6
 *
 * Text Domain: ninecodes-social-manager
 * Domain Path: /languages
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
 * @package SocialManager
 */

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

use NineCodes\SocialManager\Plugin;
use NineCodes\SocialManager\Requirements;

/**
 * Get the filesystem directory path (with trailing slash) for
 * the plugin __FILE__ passed in.
 *
 * @var string
 */
$path_dir = plugin_dir_path( __FILE__ );

/*
 * Check if the website pass the requirement.
 * If not, deactivate the plugin and print an admin notice.
 */
require_once $path_dir . 'includes/class-requirements.php';

/**
 * Defines the plugin requirement.
 *
 * @var Requirements
 */
$require = new Requirements(
	'Social Manager by NineCodes',
	plugin_basename( __FILE__ ), array(
		'PHP' => '5.3.0',
		'WordPress' => '4.5',
	)
);

if ( false === $require->pass() ) { // If the requirements are not meet.
	$require->halt(); // Do not activate the plugin.
	return;
}

/*
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once $path_dir . 'includes/class-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * This function is also useful to check if the plugin is activated
 * through the function_exists() function.
 */
function ninecodes_social_manager() {
	new Plugin();
}
ninecodes_social_manager();
