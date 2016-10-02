<?php

namespace XCo\WPSocialManager;

final class Widgets {

	/**
	 * [$plugin_dir description]
	 * @var [type]
	 */
	protected $plugin_dir;

	/**
	 * [__construct description]
	 */
    public function __construct() {

    	$this->plugin_dir = trailingslashit( plugin_dir_path( __FILE__ ) );
    	$this->requires();
    }

    /**
     * [requires description]
     * @return [type] [description]
     */
    protected function requires() {
    	require_once( $this->plugin_dir . 'partials/class-follow-us.php' );
    }
}