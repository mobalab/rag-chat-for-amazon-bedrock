<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Rag_Chat_Ab_Page_GeneralSettings
 *
 * This class handles rendering of the general settings page.
 *
 * @package     RAGCHATAB
 * @subpackage  Classes/Rag_Chat_Ab_Page_GeneralSettings
 * @author      Kashima, Kazuo
 * @since       0.0.1
 */
class Rag_Chat_Ab_Page_GeneralSettings {

	const OPTION_NAME = 'rag_chat_ab_general_settings';

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
				settings_fields( 'rag_chat_ab_options' );
				do_settings_sections( 'rag-chat-ab-general-settings' );
				submit_button( __( 'Save Settings', 'rag-chat-ab' ) );
				?>
			</form>
		</div>
		<?php
	}

	public function add_wordpress_authentication_section_and_fields() {
		add_settings_section(
			'wordpress_authentication_section', // Section ID
			'WordPress Configuration', // Title
			array( $this, 'wordpress_authentication_section_callback' ), // Callback
			'rag-chat-ab-general-settings' // Slug of the page
		);

		add_settings_field(
			'rag_chat_ab_wordpress_username', // Field ID
			'WordPress username', // Title
			array( $this, 'wordpress_user_field_render' ), // callback
			'rag-chat-ab-general-settings', // Page slug
			'wordpress_authentication_section' // Section this field belongs to
		);

		add_settings_field(
			'rag_chat_ab_wordpress_password', // Field ID
			'WordPress password', // Title
			array( $this, 'wordpress_password_field_render' ), // callback
			'rag-chat-ab-general-settings', // Page slug
			'wordpress_authentication_section' // Section this field belongs to
		);
	}

	function wordpress_authentication_section_callback() {
		echo 'Configure WordPress authentication settings here.';
	}

	function wordpress_user_field_render() {
		$options = get_option( self::OPTION_NAME );
		?>
		<input type="text" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[wordpress_username]"
				value="<?php echo esc_attr( $options['wordpress_username'] ?? '' ); ?>"
		/>
		<p class="description">
			WordPress user with permission to access posts and pages.
		</p>
		<?php
	}

	function wordpress_password_field_render() {
		$options = get_option( self::OPTION_NAME );
		?>
		<input type="text" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[wordpress_password]"
				value="<?php echo esc_attr( $options['wordpress_password'] ?? '' ); ?>"
		/>
		<p class="description">
			Password for the WordPress user above.
		</p>
		<?php
	}

	public function add_aws_section_and_fields() {
		add_settings_section(
			'aws_section', // Section ID
			'AWS Configuration', // Title
			array( $this, 'aws_section_callback' ), // Callback
			'rag-chat-ab-general-settings' // Slug of the page
		);

		add_settings_field(
			'rag_chat_ab_aws_region', // Field ID
			'Region', // Title
			array( $this, 'aws_region_field_render' ), // callback
			'rag-chat-ab-general-settings', // Page slug
			'aws_section' // Section this field belongs to
		);

		add_settings_field(
			'rag_chat_ab_aws_access_key', // Field ID
			'IAM Access Key', // Title
			array( $this, 'aws_access_key_field_render' ), // callback
			'rag-chat-ab-general-settings', // Page slug
			'aws_section' // Section this field belongs to
		);

		add_settings_field(
			'rag_chat_ab_aws_secret_key', // Field ID
			'IAM Secret Key', // Title
			array( $this, 'aws_secret_key_field_render' ), // callback
			'rag-chat-ab-general-settings', // Page slug
			'aws_section' // Section this field belongs to
		);

		add_settings_field(
			'rag_chat_ab_bedorck_knowledge_base_id', // Field ID
			'Knowledge Base ID', // Title
			array( $this, 'knowledge_base_id_field_render' ), // callback
			'rag-chat-ab-general-settings', // Page slug
			'aws_section' // Section this field belongs to
		);

		add_settings_field(
			'rag_chat_ab_bedorck_data_source_id', // Field ID
			'Data source ID', // Title
			array( $this, 'data_source_id_field_render' ), // callback
			'rag-chat-ab-general-settings', // Page slug
			'aws_section' // Section this field belongs to
		);

		add_settings_field(
			'rag_chat_ab_model_arn',
			'Model ARN',
			array( $this, 'model_arn_field_render' ),
			'rag-chat-ab-general-settings',
			'aws_section'
		);
	}

	function aws_section_callback() {
		echo 'Enter your AWS credentials and Bedrock configuration details below.';
	}

	function aws_region_field_render() {
		$options = get_option( self::OPTION_NAME );
		?>
		<input type="text" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[aws_region]"
				value="<?php echo esc_attr( $options['aws_region'] ?? '' ); ?>"
		/>
		<p class="description">
			AWS region where your Bedrock resources are located (e.g., us-east-1, us-west-2).
		</p>
		<?php
	}

	function aws_access_key_field_render() {
		$options = get_option( self::OPTION_NAME );
		?>
		<input type="text" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[aws_access_key]"
				value="<?php echo esc_attr( $options['aws_access_key'] ?? '' ); ?>"
		/>
		<p class="description">
			IAM user access key with Bedrock permissions.
		</p>
		<?php
	}

	function aws_secret_key_field_render() {
		$options = get_option( self::OPTION_NAME );
		?>
		<input type="password" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[aws_secret_key]"
				value="<?php echo esc_attr( $options['aws_secret_key'] ?? '' ); ?>"
		/>
		<p class="description">
			Secret key corresponding to the access key above.
		</p>
		<?php
	}

	function knowledge_base_id_field_render() {
		$options = get_option( self::OPTION_NAME );
		?>
		<input type="text" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[knowledge_base_id]"
				value="<?php echo esc_attr( $options['knowledge_base_id'] ?? '' ); ?>"
		/>
		<p class="description">
			Unique identifier of your Bedrock Knowledge Base.
		</p>
		<?php
	}

	function data_source_id_field_render() {
		$options = get_option( self::OPTION_NAME );
		?>
		<input type="text" name="<?php echo esc_html( self::OPTION_NAME ); ?>[data_source_id]"
				value="<?php echo esc_attr( $options['data_source_id'] ?? '' ); ?>"
		/>
		<p class="description">
			Data source ID within your Knowledge Base where content will be stored.
		</p>
		<?php
	}

	function model_arn_field_render() {
		$options = get_option( self::OPTION_NAME );
		?>
		<input type="text" name="<?php echo esc_html( self::OPTION_NAME ); ?>[model_arn]"
				value="<?php echo esc_attr( $options['model_arn'] ?? '' ); ?>"
				class="widefat"
		/>
		<p class="description">
			ARN of the model to use for RAG.
		</p>
		<?php
	}
}
