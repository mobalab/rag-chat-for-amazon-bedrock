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
		$sync_service = new Wp_Rag_Ab_SyncService();
		$stats        = $sync_service->get_sync_status_summary();
		?>
		<div class="wrap">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<form method="post" action="">
				<h2>Post Sync Controls</h2>
				<p>Posts and pages with published dates within the specified range will be exported to Amazon Bedrock.</p>
				<?php
				settings_fields( 'wp_rag_ab_options' );

				echo '<table class="form-table" role="presentation">';
				do_settings_fields( 'wp-rag-ab-content-management', 'export_posts_section' );
				echo '</table>';

				submit_button( __( 'Export Posts to Amazon Bedrock', 'wp-rag-ab' ), 'primary', 'wp_rag_ab_export_submit' );
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
						Number of posts not synced with Amazon Bedrock
					</th>
					<td>
						<?php echo esc_html( $stats->not_synced ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						Number of posts synced with Amazon Bedrock
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
			'export_to',
			'To date:',
			array( $this, 'export_to_field_render' ),
			'wp-rag-ab-content-management',
			'export_posts_section'
		);

		add_settings_field(
			'export_type',
			'Export type',
			array( $this, 'export_type_field_render' ),
			'wp-rag-ab-content-management',
			'export_posts_section'
		);
	}


	public function export_from_field_render() {
		?>
		<input type="date" name="wp_rag_ab_export_from" value="" />
		<?php
	}

	public function export_to_field_render() {
		?>
		<input type="date" name="wp_rag_ab_export_to" value="" />
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

		$post_type = isset( $_POST['wp_rag_ab_export_type'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_rag_ab_export_type'] ) ) : array( 'post', 'page' );

		$args = array(
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		// <input type="date" /> sends a date of ISO 8601 format.
		$timezone = wp_timezone();

		if ( ! empty( $_POST['wp_rag_ab_export_from'] ) || ! empty( $_POST['wp_rag_ab_export_to'] ) ) {
			$date_query = array(
				'inclusive' => true,
				'column'    => 'post_date_gmt',  // Use UTC.
			);

			if ( ! empty( $_POST['wp_rag_ab_export_from'] ) ) {
				$date_str            = sanitize_text_field( wp_unslash( $_POST['wp_rag_ab_export_from'] ) );
				$date                = new DateTime( $date_str, $timezone );
				$date_query['after'] = $date->format( 'Y-m-d\TH:i:s' );
			}

			if ( ! empty( $_POST['wp_rag_ab_export_to'] ) ) {
				$date_str             = sanitize_text_field( wp_unslash( $_POST['wp_rag_ab_export_to'] ) );
				$date                 = new DateTime( $date_str, $timezone );
				$date_query['before'] = $date->format( 'Y-m-d\TH:i:s' );
			}

			$args['date_query'] = array( $date_query );
		}

		$posts = get_posts( $args );

		$sync_service = new Wp_Rag_Ab_SyncService();
		$sync_service->send_posts_to_bedrock( $posts );
	}
}