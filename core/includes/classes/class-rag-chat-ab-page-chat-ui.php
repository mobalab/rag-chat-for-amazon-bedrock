<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Rag_Chat_Ab_Page_ChatUI
 *
 * This class handles rendering of the chat UI
 *
 * @package     RAGCHATAB
 * @subpackage  Classes/Rag_Chat_Ab_Page_ChatUI
 * @author      Kashima, Kazuo
 * @since       0.0.1
 */
class Rag_Chat_Ab_Page_ChatUI {
	const OPTION_NAME = 'rag_chat_ab_chat_ui';

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
	}

	/**
	 * @since 0.0.1
	 */
	public function enqueue_admin_styles( $hook ) {
		wp_add_inline_style(
			'wp-admin',
			'.wrap.rag-chat-ab-settings h3 {
				font-size: 1.2em;
				margin: 1em 0 1em;
			}
			/* Space between sections */
			.wrap.rag-chat-ab-settings .form-table {
				/* margin-left: 1em; */
			}
			/* Margin above the first h3 */
			.wrap.rag-chat-ab-settings h2 + h3 {
				margin-top: 1em;
			}'
		);
	}

	public function page_content() {
		$customizer_url = admin_url( 'customize.php?autofocus[section]=custom_css' );
		?>
		<div class="wrap rag-chat-ab-settings">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<div class="notice notice-info">
				<p><strong>Customizing Chat Window Styles:</strong></p>
				<ul>
					<li><strong>Classic Themes:</strong> Use Additional CSS in <a href="<?php echo esc_url( $customizer_url ); ?>" target="_blank">the Customizer</a></li>
					<li><strong>Block Themes:</strong> Adjust styles via theme.json or your child theme's CSS</li>
				</ul>
			</div>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'rag_chat_ab_options' );
				do_settings_sections( 'rag-chat-ab-chat-ui' );
				submit_button( __( 'Save Settings', 'rag-chat-ab' ) );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * @since 0.0.1
	 */
	public function add_windows_settings_section_and_fields() {
		$section_id = 'windows_settings_section';
		add_settings_section(
			$section_id,
			'Labels & Messages', // This is the first "subsection", so show the title of the parent section.
			array( $this, 'windows_settings_section_callback' ),
			'rag-chat-ab-chat-ui'
		);

		add_settings_field(
			'initial_message',
			'Initial Message',
			array( $this, 'initial_message_field_render' ),
			'rag-chat-ab-chat-ui',
			$section_id
		);

		add_settings_field(
			'window_title',
			'Window Title',
			array( $this, 'window_title_field_render' ),
			'rag-chat-ab-chat-ui',
			$section_id
		);
	}

	/**
	 * @since 0.0.1
	 */
	public function windows_settings_section_callback() {
		echo '<h3>Window Settings</h3>';
	}

	public function initial_message_field_render() {
		$options = get_option( self::OPTION_NAME );
		?>
		<input type="text" name="<?php echo esc_html( self::OPTION_NAME ); ?>[initial_message]"
				value="<?php echo esc_attr( $options['initial_message'] ?? '' ); ?>"
		/>
		<?php
	}

	/**
	 * @since 0.0.1
	 */
	public function window_title_field_render() {
		$options = get_option( self::OPTION_NAME );
		?>
		<input type="text" name="<?php echo esc_html( self::OPTION_NAME ); ?>[window_title]"
				value="<?php echo esc_attr( $options['window_title'] ?? '' ); ?>"
		/>
		<?php
	}

	/**
	 * @since 0.0.1
	 */
	public function add_input_and_button_labels_section_and_fields() {
		$section_id = 'input_and_button_labels_section';
		add_settings_section(
			$section_id,
			'', // Show nothing here, but in the callback with <h3>.
			array( $this, 'input_and_button_labels_section_callback' ),
			'rag-chat-ab-chat-ui'
		);

		add_settings_field(
			'input_placeholder_text',
			'Input Placeholder Text',
			array( $this, 'input_placeholder_text_field_render' ),
			'rag-chat-ab-chat-ui',
			$section_id
		);

		add_settings_field(
			'send_button_text',
			'Send Button Text',
			array( $this, 'send_button_text_field_render' ),
			'rag-chat-ab-chat-ui',
			$section_id
		);
	}

	/**
	 * @since 0.0.1
	 */
	public function input_and_button_labels_section_callback() {
		echo '<h3>Input & Button Labels</h3>';
	}

	/**
	 * @since 0.0.1
	 */
	public function input_placeholder_text_field_render() {
		$options = get_option( self::OPTION_NAME );
		?>
		<input type="text" name="<?php echo esc_html( self::OPTION_NAME ); ?>[input_placeholder_text]"
				value="<?php echo esc_attr( $options['input_placeholder_text'] ?? '' ); ?>"
		/>
		<?php
	}

	/**
	 * @since 0.0.1
	 */
	public function send_button_text_field_render() {
		$options = get_option( self::OPTION_NAME );
		?>
		<input type="text" name="<?php echo esc_html( self::OPTION_NAME ); ?>[send_button_text]"
				value="<?php echo esc_attr( $options['send_button_text'] ?? '' ); ?>"
		/>
		<?php
	}

	/**
	 * @since 0.0.1
	 */
	public function add_participant_names_section_and_fields() {
		$section_id = 'participant_names_section';
		add_settings_section(
			$section_id,
			'', // Show nothing here, but in the callback with <h3>.
			array( $this, 'participant_names_section_callback' ),
			'rag-chat-ab-chat-ui',
			array(
				'after_section' => '<hr />',
			)
		);

		add_settings_field(
			'bot_name',
			'Bot Name',
			array( $this, 'bot_name_field_render' ),
			'rag-chat-ab-chat-ui',
			$section_id
		);

		add_settings_field(
			'user_name',
			'User Name',
			array( $this, 'user_name_field_render' ),
			'rag-chat-ab-chat-ui',
			$section_id
		);
	}

	/**
	 * @since 0.0.1
	 */
	public function participant_names_section_callback() {
		echo '<h3>Participant Names</h3>';
	}

	/**
	 * @since 0.0.1
	 */
	public function bot_name_field_render() {
		$options = get_option( self::OPTION_NAME );
		?>
		<input type="text" name="<?php echo esc_html( self::OPTION_NAME ); ?>[bot_name]"
				value="<?php echo esc_attr( $options['bot_name'] ?? '' ); ?>"
		/>
		<?php
	}

	/**
	 * @since 0.0.1
	 */
	public function user_name_field_render() {
		$options = get_option( self::OPTION_NAME );
		?>
		<input type="text" name="<?php echo esc_html( self::OPTION_NAME ); ?>[user_name]"
				value="<?php echo esc_attr( $options['user_name'] ?? '' ); ?>"
		/>
		<?php
	}

	public function add_display_options_section_and_fields() {
		add_settings_section(
			'display_options_section',
			'Display Options',
			array( $this, 'display_options_section_callback' ),
			'rag-chat-ab-chat-ui'
		);

		add_settings_field(
			'display_context_links',
			'Display context links',
			array( $this, 'display_context_links_field_render' ),
			'rag-chat-ab-chat-ui',
			'display_options_section'
		);
	}

	function display_options_section_callback() {
		echo '';
	}

	function display_context_links_field_render() {
		$options = get_option( self::OPTION_NAME );
		$value   = $options['display_context_links'] ?? 'no';
		?>
		<input type="radio" name="<?php echo esc_html( self::OPTION_NAME ); ?>[display_context_links]" value="no"
				<?php
				if ( 'no' === $value ) {
					echo 'checked="checked"';
				}
				?>

		/>No
		<input type="radio" name="<?php echo esc_html( self::OPTION_NAME ); ?>[display_context_links]" value="yes"
			<?php
			if ( 'yes' === $value ) {
				echo 'checked="checked"';
			}
			?>

		/>Yes
		<?php
	}
}