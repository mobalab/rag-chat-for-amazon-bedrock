<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Rag_Chat_Ab_Frontend
 *
 * This class is responsible for frontend pages.
 *
 * @package     RAGCHATAB
 * @subpackage  Classes/Rag_Chat_Ab_Frontend
 * @author      Kashima, Kazuo
 * @since       0.0.1
 */
class Rag_Chat_Ab_Frontend {
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
		$chat_ui_options = get_option( RAGCHATAB()->pages['chat-ui']::OPTION_NAME );

		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'ragchatab-frontend-styles', RAGCHATAB_PLUGIN_URL . 'core/includes/assets/css/frontend-styles.css', array(), RAGCHATAB_VERSION, 'all' );
		wp_enqueue_script( 'ragchatab-frontend-scripts', RAGCHATAB_PLUGIN_URL . 'core/includes/assets/js/frontend-scripts.js', array( 'jquery' ), RAGCHATAB_VERSION, false );
		wp_localize_script(
			'ragchatab-frontend-scripts',
			'ragChatAb',
			array(
				'chat_ui_options' => $chat_ui_options,
				'ajaxurl'         => admin_url( 'admin-ajax.php' ),
				'security_nonce'  => wp_create_nonce( 'rag_chat_ab_chat_nonce' ),
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

		$options          = get_option( Rag_Chat_Ab::instance()->pages['chat-ui']::OPTION_NAME );
		$title            = ! empty( $options['window_title'] ) ? $options['window_title'] : 'Chat';
		$placeholder      = ! empty( $options['input_placeholder_text'] ) ? $options['input_placeholder_text']
			: 'Enter your message here...';
		$send_button_text = ! empty( $options['send_button_text'] ) ? $options['send_button_text'] : 'Send';
		?>
		<div id="rag-chat-ab-chat-window" class="rag-chat-ab-chat">
			<div class="rag-chat-ab-chat__header">
				<span class="rag-chat-ab-chat__title"><?php echo esc_html( $title ); ?></span>
				<div class="rag-chat-ab-chat__header-buttons">
					<button type="button" class="rag-chat-ab-chat__clear" title="Clear chat history">
						<span class="dashicons dashicons-trash"></span>
					</button>
					<button type="button" class="rag-chat-ab-chat__minimize" title="Minimize chat">
						<span class="dashicons dashicons-minus"></span>
					</button>
				</div>
			</div>
			<div class="rag-chat-ab-chat__content">
				<div id="rag-chat-ab-chat-messages" class="rag-chat-ab-chat__messages"></div>
				<form id="rag-chat-ab-chat-form" class="rag-chat-ab-chat__form">
					<input type="text" id="rag-chat-ab-chat-input" class="rag-chat-ab-chat__input" placeholder="<?php echo esc_attr( $placeholder ); ?>">
					<button type="submit" class="rag-chat-ab-chat__submit">
						<span class="rag-chat-ab-chat__submit-text"><?php echo esc_html( $send_button_text ); ?></span>
						<span class="rag-chat-ab-chat__spinner"></span>
					</button>
				</form>
			</div>
		</div>
		<div id="rag-chat-ab-chat-icon" class="rag-chat-ab-chat-launcher rag-chat-ab--hidden">
			<span class="dashicons dashicons-admin-comments"></span>
			<span class="rag-chat-ab-chat-launcher__tooltip">Open <?php echo esc_html( $title ); ?></span>
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
		$formatted_response['contexts'] = array_values( array_map( 'unserialize', array_unique( array_map( 'serialize', $contexts ) ) ) );
		return $formatted_response;
	}

	/**
	 * Receive the chat message and output the response.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function process_chat() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'rag_chat_ab_chat_nonce' ) ) {
			wp_send_json_error( __( 'Security check failed. Please refresh the page and try again.', 'rag-chat-ab' ), 403 );
		}

		if ( empty( $_POST['message'] ) ) {
			return;
		}

		$message    = sanitize_text_field( wp_unslash( $_POST['message'] ) );
		$session_id = ! empty( $_POST['session_id'] ) ? sanitize_text_field( wp_unslash( $_POST['session_id'] ) ) : null;
		$gs_options = get_option( RAGCHATAB()->pages['general-settings']::OPTION_NAME );
		$client     = RAGCHATAB()->helpers->get_bedrock_client();
		$client->set_model_arn( $gs_options['model_arn'] ?? null );
		$response = $client->retrieve_and_generate( $message, $session_id );

		if ( 200 === $response['status_code'] ) {
			wp_send_json_success( $this->format_bedrock_response_body( $response['body'] ) );
		} else {
			wp_send_json_error( $response['body'], $response['status_code'] );
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
}