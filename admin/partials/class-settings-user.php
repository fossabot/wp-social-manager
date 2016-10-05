<?php

namespace XCo\WPSocialManager;

class SettingsUser extends OptionUtilities {

	protected $plugin_name;

	protected $plugin_opts;

	public function __construct( array $args ) {

		$this->plugin_name = $args[ 'plugin_name' ];
		$this->plugin_opts = $args[ 'plugin_opts' ];

		$this->hooks();
	}

	protected function hooks() {

		add_action( 'show_user_profile', array( $this, 'add_social_profiles' ), -30 );
		add_action( 'edit_user_profile', array( $this, 'add_social_profiles' ), -30 );
		add_action( 'personal_options_update', array( $this, 'save_social_profiles' ), -30 );
		add_action( 'edit_user_profile_update', array( $this, 'save_social_profiles' ), -30 );
	}

	public function add_social_profiles( $user ) {

		$meta = get_the_author_meta( $this->plugin_opts, $user->ID );
		$profiles = self::get_social_profiles(); ?>

		<h2><?php echo esc_html__( 'Social Profiles', 'wp-social-manager' ); ?></h2>
		<p><?php echo esc_html__( 'Social profile or page connected to this user.', 'wp-social-manager' ); ?></p>
		<table class="form-table">

		<?php foreach ( $profiles as $key => $data ) :

			$key   = sanitize_key( $key );
			$value = isset( $meta[ $key ] ) ? $meta[ $key ] : '';
			$label = isset( $data[ 'label' ] ) ? $data[ 'label' ] : '';
			$descr = isset( $data[ 'description' ] ) ? $data[ 'description' ] : ''; ?>
			<tr>
				<th><label for="<?php echo "field-user-{$key}" ?>"><?php echo $label ?></label></th>
				<td>
					<input type="text" name="<?php echo esc_attr( "{$this->plugin_opts}[{$key}]" ); ?>" id="<?php echo "field-user-{$key}" ?>" value="<?php echo sanitize_text_field( $value ); ?>" class="regular-text code">
					<?php if ( $descr ) : ?>
					<p class="description"><?php echo wp_kses_post( $descr ); ?></p>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>

		</table>
	<?php }

	/**
	 * Save and update custom input in the "Profile" edit screen.
	 *
	 * @param  $user_id  The user ID who being edited in the Profile edit screen.
	 * @return void
	 */
	public function save_social_profiles( $user_id ) {

		$profiles = (array) $_POST[ $this->plugin_opts ];

		foreach ( $profiles as $key => $value ) {
			$key = sanitize_key( $key );
			$profiles[ $key ] = sanitize_text_field( $value );
		}
		if( current_user_can( 'edit_user' ) ) {
			update_user_meta( $user_id, $this->plugin_opts, $profiles );
		}
	}
}