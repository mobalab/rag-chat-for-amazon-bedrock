<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wp_Rag_Ab_PostHooks
 *
 * This class contains hooks called when a post/page is saved.
 *
 * @package     WPRAGAB
 * @subpackage  Classes/Wp_Rag_Ab_PostHooks
 * @author      Kashima, Kazuo
 * @since       0.0.1
 */
class Wp_Rag_Ab_PostHooks {
	/**
	 * @var array wp_post_id => status string
	 */
	private $previous_status = array();

	/**
	 * Stores the previous status of the post/page before saving it.
	 *
	 * @param $post_id
	 * @param $data
	 * @return void
	 */
	public function store_previous_status( $post_id, $data ) {
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$post = get_post( $post_id );
		if ( $post && in_array( $post->post_type, array( 'post', 'page' ) ) ) {
			$this->previous_status[ $post_id ] = $post->post_status;
		}
	}

	/**
	 * Called when a post/page is saved.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post Post object.
	 * @param bool    $update Whether updating or not (creating).
	 */
	public function handle_post_save( int $post_id, WP_Post $post, bool $update ) {
		// Do not process revisions.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Do not process autosaves.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Do not process posts of other types than "post" and "page".
		if ( ! in_array( $post->post_type, array( 'post', 'page' ) ) ) {
			return;
		}

		$new_status = $post->post_status;
		$old_status = isset( $this->previous_status[ $post_id ] )
			? $this->previous_status[ $post_id ]
			: 'new';  // previous_status doesn't exist when creating a new post.

		if ( ( 'draft' === $old_status || 'auto-draft' === $old_status ) && 'publish' === $new_status ) {
			// Draft to publish -> new post.
			$this->send_to_bedrock( $post );
		} elseif ( 'publish' === $old_status && 'publish' === $new_status ) {
			// Publish to publish -> updating an existing post.
			$this->send_to_bedrock( $post );
		} elseif ( 'publish' === $old_status && 'draft' === $new_status ) {
			// Published to draft -> deleting the post.
			$this->delete_from_bedrock( $post_id );
		}

		// Remove the status once the process is complete.
		unset( $this->previous_status[ $post_id ] );
	}

	/**
	 * Called when a post is deleted.
	 *
	 * @param int $post_id Post ID.
	 */
	public function handle_post_delete( int $post_id ) {
		// Do not process revisions.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$post = get_post( $post_id );

		if ( ! $post || ! in_array( $post->post_type, array( 'post', 'page' ) ) ) {
			return;
		}

		if ( 'publish' === $post->post_status ) {
			$this->delete_from_bedrock( $post_id );
		}
	}

	private function send_to_bedrock( WP_Post $post ) {
		$client = WPRAGAB()->helpers->get_bedrock_client();

		$documents = array(
			array(
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
			),
		);

		try {
			$respopnse = $client->ingest_documents( $documents );
			WPRAGAB()->helpers->log_error( wp_json_encode( $respopnse ) );
		} catch ( Exception $e ) {
			WPRAGAB()->helpers->log_error( 'API Error (Create): ' . $e->getMessage() );
		}
	}

	private function delete_from_bedrock( int $post_id ) {
		$client = WPRAGAB()->helpers->get_bedrock_client();

		try {
			$response = $client->delete_document( (string) $post_id );
			WPRAGAB()->helpers->log_error( wp_json_encode( $response ) );
		} catch ( Exception $e ) {
			WPRAGAB()->helpers->log_error( 'Error (Remove): ' . $e->getMessage() );
		}
	}
}
