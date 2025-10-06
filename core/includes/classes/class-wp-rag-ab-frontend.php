<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wp_Rag_Ab_Frontend
 *
 * This class is responsible for frontend pages.
 *
 * @package     WPRAGAB
 * @subpackage  Classes/Wp_Rag_Ab_Frontend
 * @author      Kashima, Kazuo
 * @since       0.0.1
 */
class Wp_Rag_Ab_Frontend {
	private $shortcode_used = false;

	/**
	 * Enqueue the frontend related scripts and styles for this plugin.
	 *
	 * @access  public
	 * @since   0.0.1
	 *
	 * @return  void
	 */
	public function enqueue_scripts_and_styles() {
		$chat_ui_options = get_option( WPRAGAB()->pages['chat-ui']::OPTION_NAME );

		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'wpragab-frontend-styles', WPRAGAB_PLUGIN_URL . 'core/includes/assets/css/frontend-styles.css', array(), WPRAGAB_VERSION, 'all' );
		wp_enqueue_script( 'wpragab-frontend-scripts', WPRAGAB_PLUGIN_URL . 'core/includes/assets/js/frontend-scripts.js', array( 'jquery' ), WPRAGAB_VERSION, false );
		wp_localize_script(
			'wpragab-frontend-scripts',
			'wpRagAb',
			array(
				'chat_ui_options' => $chat_ui_options,
				'ajaxurl'         => admin_url( 'admin-ajax.php' ),
				'security_nonce'  => wp_create_nonce( 'your-nonce-name' ),
			)
		);
	}

	/**
	 * @return string|void HTML for the chat window
	 */
	function show_chat_window() {
		// When the shortcode wasn't used, do nothing.
		if ( empty( $this->shortcode_used ) ) {
			return '';
		}

		$options          = get_option( Wp_Rag_Ab::instance()->pages['chat-ui']::OPTION_NAME );
		$title            = ! empty( $options['window_title'] ) ? $options['window_title'] : 'Chat';
		$placeholder      = ! empty( $options['input_placeholder_text'] ) ? $options['input_placeholder_text']
			: 'Enter your message here...';
		$send_button_text = ! empty( $options['send_button_text'] ) ? $options['send_button_text'] : 'Send';
		?>
		<div id="wp-rag-ab-chat-window" class="wp-rag-ab-chat">
			<div class="wp-rag-ab-chat__header">
				<span class="wp-rag-ab-chat__title"><?php echo esc_html( $title ); ?></span>
				<button type="button" class="wp-rag-ab-chat__minimize">
					<span class="dashicons dashicons-minus"></span>
				</button>
			</div>
			<div class="wp-rag-ab-chat__content">
				<div id="wp-rag-ab-chat-messages" class="wp-rag-ab-chat__messages"></div>
				<form id="wp-rag-ab-chat-form" class="wp-rag-ab-chat__form">
					<input type="text" id="wp-rag-ab-chat-input" class="wp-rag-ab-chat__input" placeholder="<?php echo esc_attr( $placeholder ); ?>">
					<button type="submit" class="wp-rag-ab-chat__submit">
						<span class="wp-rag-ab-chat__submit-text"><?php echo esc_html( $send_button_text ); ?></span>
						<span class="wp-rag-ab-chat__spinner"></span>
					</button>
				</form>
			</div>
		</div>
		<div id="wp-rag-ab-chat-icon" class="wp-rag-ab-chat-launcher wp-rag-ab--hidden">
			<span class="dashicons dashicons-admin-comments"></span>
			<span class="wp-rag-ab-chat-launcher__tooltip">Open <?php echo esc_html( $title ); ?></span>
		</div>
		<?php
	}

	/**
	 * @param array $body The response body from the Bedrock API's retrieveAndGenerate endpoint.
	 * @return array
	 */
	private function format_bedrock_response_body( $body ) {
		$formatted_response = array(
			'session_id' => $body['sessionId'],
			'answer'     => $body['output']['text'],
		);
		$contexts           = array();
		foreach ( $body['citations'] as $citation ) {
			foreach ( $citation['retrievedReferences'] as $reference ) {
				$contexts[] = array(
					'title' => $reference['metadata']['title'],
					'url'   => $reference['metadata']['url'],
					// 'content' => $reference['content']['text'],
				);
			}
		}
		// Remove duplicate contexts.
		$formatted_response['contexts'] = array_map( 'unserialize', array_unique( array_map( 'serialize', $contexts ) ) );
		return $formatted_response;
	}

	/**
	 * Receive the chat message and output the response.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function process_chat() {
		if ( empty( $_POST['message'] ) ) {
			return;
		}

		$message    = sanitize_text_field( wp_unslash( $_POST['message'] ) );
		$gs_options = get_option( WPRAGAB()->pages['general-settings']::OPTION_NAME );
		$client     = WPRAGAB()->helpers->get_bedrock_client();
		$client->set_model_arn( $gs_options['model_arn'] ?? null );
		$response = $client->retrieve_and_generate( sanitize_text_field( wp_unslash( $message ) ) );

		if ( 200 === $response['status_code'] ) {
			wp_send_json_success( $this->format_bedrock_response_body( $response['body'] ) );
		} else {
			wp_send_json_error( $response['body']['message'], $response['status_code'] );
		}

		wp_die();
	}

	/**
	 * @param $atts
	 *
	 * @return string|null
	 */
	public function shortcode( $atts ) {
		// Do nothing when REST API request.
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return '';
		}

		// Do nothing when AJAX request.
		if ( wp_doing_ajax() ) {
			return '';
		}

		// Do nothing on a review page.
		if ( is_admin() ) {
			return '';
		}

		// This global variable indicates whether the shortcode was used or not.
		$this->shortcode_used = true;

		return '';
	}

	/**
	 * Outputs the custom CSS.
	 *
	 * @since   0.0.1
	 */
	public function output_custom_css() {
		$options = get_option( Wp_Rag_Ab::instance()->pages['chat-ui']::OPTION_NAME );

		if ( ! empty( $options['custom_css'] ) ) {
			echo '<style lang="text/css">' . esc_html( $options['custom_css'] ) . '</style>';
		}
	}
}