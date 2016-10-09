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
    public function __construct( array $args ) {

        $this->args = $args;

    	$this->plugin_dir = trailingslashit( plugin_dir_path( __FILE__ ) );

    	$this->requires();
        $this->hooks();
    }

    /**
     * [requires description]
     * @return [type] [description]
     */
    protected function requires() {
    	require_once( $this->plugin_dir . 'partials/class-social-profiles.php' );
    }

    /**
     * [hooks description]
     * @return [type] [description]
     */
    protected function hooks() {
        add_action( 'widgets_init', array( $this, 'setups' ) );
    }

    /**
     * [setups description]
     * @return [type] [description]
     */
    public function setups() {
        register_widget( new WidgetSocialProfiles( $this->args ) );
    }
}