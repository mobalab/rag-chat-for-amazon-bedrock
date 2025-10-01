<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wp_Rag_Ab_Page_ContentManagement
 *
 * This class handles rendering of the content management section
 *
 * @package     WPRAGAB
 * @subpackage  Classes/Wp_Rag_Ab_Page_ContentManagement
 * @author      Kashima, Kazuo
 * @since       0.0.1
 */
class Wp_Rag_Ab_Page_ContentManagement {
	public function page_content() {
		// TODO: Move this to a separate class.q
		global $wpdb;
		$posts_table    = $wpdb->prefix . 'posts';
		$postmeta_table = $wpdb->prefix . 'postmeta';
		$meta_key       = '_wpragab_sync_status';

		$stats = $wpdb->get_row(
			"
        SELECT
            COUNT(DISTINCT p.ID) as total,
            SUM(CASE
                WHEN pm.meta_value IS NULL OR pm.meta_value = '0' THEN 1
                ELSE 0
            END) as not_synced,
            SUM(CASE WHEN pm.meta_value = '1' THEN 1 ELSE 0 END) as synced,
            SUM(CASE WHEN pm.meta_value = '2' THEN 1 ELSE 0 END) as error
        FROM $posts_table p
        LEFT JOIN $postmeta_table pm
            ON p.ID = pm.post_id
            AND pm.meta_key = '$meta_key'
        WHERE p.post_status = 'publish'
            AND p.post_type IN ('post', 'page')
    "
		);

		?>
		<div class="wrap">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<form method="post" action="">
				<h2>Post Sync Controls</h2>
				<?php
				settings_fields( 'wp_rag_ab_options' );

				echo '<table class="form-table" role="presentation">';
				do_settings_fields( 'wp-rag-ab-content-management', 'export_posts_section' );
				echo '</table>';

				submit_button( __( 'Export Posts to Amazon Bedrock' ), 'primary', 'wp_rag_ab_export_submit' );
				?>
			</form>
			<hr />

			<h2>Content Status</h2>
			<table class="" role="presentation">
				<tr>
					<th scope="row">
						Total published posts
					</th>
					<td>
						<?php echo esc_html( $stats->total ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						Number of the posts not synced with Amazon Bedrock
					</th>
					<td>
						<?php echo esc_html( $stats->not_synced ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						Number of the posts synced with Amazon Bedrock
					</th>
					<td>
						<?php echo esc_html( $stats->synced ); ?>
					</td>
				</tr>
			</table>
		</div>
		<?php
	}

	public function add_export_posts_section_and_fields() {
		add_settings_section(
			'export_posts_section',
			'Export posts', // Not used.
			null,
			'wp-rag-ab-content-management'
		);

		add_settings_field(
			'export_from',
			'From date:',
			array( $this, 'export_from_field_render' ),
			'wp-rag-ab-content-management',
			'export_posts_section'
		);

		add_settings_field(
			'import_type',
			'Import type',
			array( $this, 'import_type_field_render' ),
			'wp-rag-ab-content-management',
			'import_posts_section'
		);
	}


	public function export_from_field_render() {
		?>
		<input type="date" name="wp_rag_ab_export_from" value="" />
		<?php
	}

	public function export_type_field_render() {
		?>
		<input id="wp_rag_ab_export_type_post" type="radio" name="wp_rag_ab_export_type" value="post" />
		<label for="wp_rag_ab_export_type_post">
			Post
		</label>
		<input id="wp_rag_ab_export_type_page" type="radio" name="wp_rag_ab_export_type" value="page" />
		<label for="wp_rag_ab_export_type_page">
			Page
		</label>
		<?php
	}


	/**
	 * @return void
	 */
	function handle_export_form_submission() {
		check_admin_referer( 'wp_rag_ab_options-options' );
		$post_type = isset( $_POST['wp_rag_ab_export_type'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_rag_ab_export_type'] ) ) : 'post';
		$params    = array( 'post_type' => $post_type );
		if ( ! empty( $_POST['wp_rag_ab_import_from'] ) ) {
			// <input type="date" /> sends a date of ISO 8601 format.
			$timezone = wp_timezone();
			$date_str = sanitize_text_field( wp_unslash( $_POST['wp_rag_ab_export_from'] ) );
			$date     = new DateTime( $date_str, $timezone );

			$params['modified_after'] = $date->format( 'Y-m-d\TH:i:s' );
		}

		$data     = array(
			'task_type' => 'ImportWordpressPosts',
			'params'    => $params,
		);
		$response = WPRAG()->helpers->call_api_for_site( '/tasks', 'POST', $data );

		if ( 202 === $response['httpCode'] ) {
			$type    = 'success';
			$message = 'Successfully launch the import task.';
		} else {
			$type    = 'error';
			$message = 'API call failed.';
		}

		$messages = Wp_Rag_AdminMessages::get_instance();
		$messages->add_message(
			$message,
			$response,
			$type
		);
	}
}