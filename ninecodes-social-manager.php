<?php
/**
 * Plugin Name: Social Media Manager
 * Plugin URI: http://wordpress.org/plugins/ninecodes-social-manager
 * Description: Lightweight, clean and optimized social media plugin for WordPress.
 * Version: 2.0.0-alpha.3
 * Author: NineCodes
 * Author URI: https://profiles.wordpress.org/ninecodes
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Requires at least: 4.7
 * Tested up to: 4.7.3
 *
 * Text Domain: ninecodes-social-manager
 * Domain Path: /languages
 *
 * Copyright (c) 2017 NineCodes (https://ninecodes.com/)
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
	'Social Media Manager',
	plugin_basename( __FILE__ ), array(
		'PHP' => '5.3.0',
		'WordPress' => '4.7',
	)
);

if ( false === $require->pass() ) { // If the requirements are not meet.
	$require->halt(); // Do not activate the plugin.
	return;
}

require_once $path_dir . 'includes/class-plugin.php';
require_once $path_dir . 'includes/class-helpers.php';
require_once $path_dir . 'includes/class-languages.php';
require_once $path_dir . 'includes/class-options.php';
require_once $path_dir . 'includes/class-theme-support.php';
require_once $path_dir . 'includes/function-utilities.php';

/**
 * Begins execution of the plugin.
 *
 * This function is also useful to check if the plugin is activated
 * through the function_exists() function.
 */
function ninecodes_social_manager() {

	static $plugin;

	if ( is_null( $plugin ) ) {
		$plugin = new Plugin();
		$plugin->init();
	}

	return $plugin;
}
ninecodes_social_manager();
