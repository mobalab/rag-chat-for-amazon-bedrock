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
        $status = array( 'post_count' => 0 );
		?>
		<div class="wrap">
			<h2><?php echo esc_html( WPRAGAB()->settings->get_plugin_name() ) ?></h2>
			<h3>System Status</h3>
			<ul>
				<li><?php echo $status['post_count'] > 0 ? '✅' : '❌'; ?>: Number of the posts imported to Amazon Bedrock is <?php echo esc_html( $status['post_count'] ); ?>.</li>
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
				<p>Question: <?php echo esc_html( wp_unslash( $_POST['wp_rag_ab_question'] ) ); ?></p>
				<p>Answer: <?php echo esc_html( $this->response['response']['answer'] ); ?></p>
				Context posts:
				<ul>
					<?php foreach ( $this->response['response']['context_posts'] as $post ) : ?>
						<li><a href="<?php echo esc_attr( $post['url'] ); ?>" target="_blank"><?php echo esc_html( $post['title'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
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
		$data     = array( 'question' => sanitize_text_field( wp_unslash( $_POST['wp_rag_ab_question'] ) ) );
		//$response = WPRAG()->helpers->call_api_for_site( '/posts/query', 'POST', $data );

		//$this->response = $response;
	}
}