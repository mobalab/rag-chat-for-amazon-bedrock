<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wp_Rag_Ab_Page_GeneralSettings
 *
 * This class handles rendering of the general settings page.
 *
 * @package     WPRAGAB
 * @subpackage  Classes/Wp_Rag_Ab_Page_GeneralSettings
 * @author      Kashima, Kazuo
 * @since       0.0.1
 */
class Wp_Rag_Ab_Page_GeneralSettings {
	const OPTION_NAME = 'wp_rag_ab_options';

	/**
	 * Executed before saving the options.
	 *
	 * @param $input
	 *
	 * @return mixed
	 */
	function save_config_api( $input ) {
	}

	public function page_content() {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( self::OPTION_NAME );
				do_settings_sections( 'wp-rag-ab-general-settings' );
                submit_button( __( 'Save Settings' ) );
				?>
			</form>
		</div>
		<?php
	}

	public function add_config_section_and_fields() {
		add_settings_section(
			'wordpress_authentication_section', // Section ID
			'WP RAG for Amazon Bedrock Configuration', // Title
			array( $this, 'config_section_callback' ), // Callback
			'wp-rag-ab-general-settings' // Slug of the page
		);

		add_settings_field(
			'wp_rag_ab_wordpress_username', // Field ID
			'WordPress user', // Title
			array( $this, 'wordpress_user_field_render' ), // callback
			'wp-rag-ab-general-settings', // Page slug
			'wordpress_authentication_section' // Section this field belongs to
		);

		add_settings_field(
			'wp_rag_ab_wordpress_password', // Field ID
			'WordPress password', // Title
			array( $this, 'wordpress_password_field_render' ), // callback
			'wp-rag-ab-general-settings', // Page slug
			'wordpress_authentication_section' // Section this field belongs to
		);
	}

	function config_section_callback() {
		echo 'Configure your plugin settings here.';
	}

	function wordpress_user_field_render() {
		$options = get_option( self::OPTION_NAME );
		?>
		<input type="text" name="<?php echo self::OPTION_NAME; ?>[wordpress_username]"
				value="<?php echo esc_attr( $options['wordpress_username'] ?? '' ); ?>"
			<?php WPRAG()->form->disabled_unless_verified(); ?>
		/>
		<?php
	}

	function wordpress_password_field_render() {
		$options = get_option( self::OPTION_NAME );
		?>
		<input type="text" name="<?php echo self::OPTION_NAME; ?>[wordpress_password]"
				value="<?php echo esc_attr( $options['wordpress_password'] ?? '' ); ?>"
			<?php WPRAG()->form->disabled_unless_verified(); ?>
		/>
		<?php
	}

	public function terms_pp_section_callback() {
		// Empty for separator.
	}
}
