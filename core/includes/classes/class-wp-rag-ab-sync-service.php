<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wp_Rag_Ab_SyncService
 *
 * @package     WPRAGAB
 * @subpackage  Classes/Wp_Rag_Ab_SyncService
 * @author      Kashima, Kazuo
 * @since       0.0.1
 */
class Wp_Rag_Ab_SyncService {
	/**
	 * @return stdClass Available properties are total, not_synced and synced.
	 */
	public function get_sync_status_summary() {
		global $wpdb;
		$posts_table    = $wpdb->prefix . 'posts';
		$postmeta_table = $wpdb->prefix . 'postmeta';
		$meta_key       = '_wpragab_sync_status';

		return $wpdb->get_row(
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
	}

	/**
	 * Convert a post to an array that can be sent to Bedrock.
	 *
	 * @param WP_Post $post post object.
	 * @return array[] equivalent to KnowledgeBaseDocument.
	 * @see https://docs.aws.amazon.com/bedrock/latest/APIReference/API_agent_IngestKnowledgeBaseDocuments.html .
	 */
	private function convert_post_to_bedrock_document( WP_Post $post ) {
		return array(
			'content'  => array(
				'dataSourceType' => 'CUSTOM',
				'custom'         => array(
					'customDocumentIdentifier' => array(
						'id' => (string) $post->ID,
					),
					'inlineContent'            => array(
						'type'        => 'TEXT',
						'textContent' => array(
							// TODO Should add the title?
							'data' => $post->post_content,
						),
					),
					'sourceType'               => 'IN_LINE',
				),
			),
			'metadata' => array(
				'type'             => 'IN_LINE_ATTRIBUTE',
				'inlineAttributes' => array(
					array(
						'key'   => 'title',
						'value' => array(
							'type'        => 'STRING',
							'stringValue' => $post->post_title,
						),
					),
					array(
						'key'   => 'url',
						'value' => array(
							'type'        => 'STRING',
							'stringValue' => get_permalink( $post ),
						),
					),
				),
			),
		);
	}


	/**
	 * Send the post to Bedrock. If the post is already in Bedrock, update it.
	 *
	 * @param WP_Post $post post object.
	 * @return void
	 */
	public function send_post_to_bedrock( WP_Post $post ) {
		$client = WPRAGAB()->helpers->get_bedrock_client();

		$documents = array(
			$this->convert_post_to_bedrock_document( $post ),
		);

		try {
			$response    = $client->ingest_documents( $documents );
			$sync_status = 202 === $response['status_code'] ? 1 : 2; // 1: success, 2: error.
			if ( 2 === $sync_status ) {
				WPRAGAB()->helpers->log_error( 'Error (Save): ' . wp_json_encode( $response ) );
			}
			update_post_meta( $post->ID, '_wpragab_sync_status', $sync_status );
		} catch ( Exception $e ) {
			WPRAGAB()->helpers->log_error( 'Error (Save): ' . $e->getMessage() );
		}
	}

	/**
	 * Delete the post from Bedrock.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function delete_post_from_bedrock( int $post_id ) {
		$client = WPRAGAB()->helpers->get_bedrock_client();

		try {
			$response = $client->delete_document( (string) $post_id );
			if ( 202 === $response['status_code'] ) {
				delete_post_meta( $post_id, '_wpragab_sync_status' );
			} else {
				WPRAGAB()->helpers->log_error( 'Error (Remove): ' . wp_json_encode( $response ) );
			}
		} catch ( Exception $e ) {
			WPRAGAB()->helpers->log_error( 'Error (Remove): ' . $e->getMessage() );
		}
	}
}
