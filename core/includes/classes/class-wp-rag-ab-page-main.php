<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wp_Rag_Ab_Page_Main
 *
 * This class handles rendering of the main page.
 *
 * @package     WPRAGAB
 * @subpackage  Classes/Wp_Rag_Ab_Page_Main
 * @author      Kashima, Kazuo
 * @since       0.0.1
 */
class Wp_Rag_Ab_Page_Main {

	private $response = array();

	public function enqueue_scripts_and_styles() {
	}

	/**
	 * Renders the main page
	 *
	 * @return void
	 */
	public function render_main_page() {
		$sync_service = new Wp_Rag_Ab_SyncService();
		$stats        = $sync_service->get_sync_status_summary();
		?>
		<div class="wrap">
			<h2><?php echo esc_html( WPRAGAB()->settings->get_plugin_name() ); ?></h2>
			<h3>System Status</h3>
			<ul>
				<li><?php echo $stats->synced > 0 ? '✅' : '❌'; ?>: Number of the posts exported to Amazon Bedrock is <?php echo esc_html( $stats->synced ); ?>.</li>
			</ul>
			<h3>Operations</h3>
			<form method="post" action="">
				<?php wp_nonce_field( 'wp_rag_ab_operation_submit', 'wp_rag_ab_nonce' ); ?>
				<input type="submit" name="wp_rag_ab_import_submit" class="button button-primary" value="Import Posts">
			</form>
			<h3>Test Query</h3>
			<form method="post" action="">
				<?php wp_nonce_field( 'wp_rag_ab_query_submit', 'wp_rag_ab_nonce' ); ?>
				<input type="text" name="wp_rag_ab_question" />
				<input type="submit" name="wp_rag_ab_query_submit" class="button button-primary" value="Query">
			</form>
			<?php if ( ! empty( $this->response ) ) : ?>
				<?php if ( 200 === $this->response['status_code'] ) : ?>
					<p>Question: <?php echo esc_html( wp_unslash( $_POST['wp_rag_ab_question'] ) ); ?></p>
					<p>Answer: <?php echo esc_html( $this->response['body']['output']['text'] ); ?></p>
					Context posts:
					<ul>
						<?php foreach ( $this->response['body']['citations'] as $citation ) : ?>
							<?php foreach ( $citation['retrievedReferences'] as $reference ) : ?>
								<li><a href="<?php echo esc_url( $reference['metadata']['url'] ); ?>"><?php echo esc_html( $reference['metadata']['title'] ); ?></a></li>
							<?php endforeach; ?>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<p>Error: <?php esc_attr( $this->response['body']['message'] ); ?></p>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * @return void
	 */
	function handle_import_form_submission() {
	}

	/**
	 * Handles the query  form submission, validates the nonce, processes the posted data, and calls the API.
	 *
	 * @return void
	 */
	function handle_query_form_submission() {
		check_admin_referer( 'wp_rag_ab_query_submit', 'wp_rag_ab_nonce' );
		if ( empty( $_POST['wp_rag_ab_question'] ) ) {
			return;
		}
		$gs_options = get_option( WPRAGAB()->pages['general-settings']::OPTION_NAME );
		$client     = WPRAGAB()->helpers->get_bedrock_client();
		$client->set_model_arn( $gs_options['model_arn'] ?? null );
		$response = $client->retrieve_and_generate( sanitize_text_field( wp_unslash( $_POST['wp_rag_ab_question'] ) ) );

		$this->response = $response;
	}
}