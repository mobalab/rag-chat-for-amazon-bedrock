<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wp_Rag_Ab_Amazon_Bedrock_Client
 *
 * @package     WPRAGAB
 * @subpackage  Classes/Wp_Rag_Ab_Amazon_Bedrock_Client
 * @author      Kashima, Kazuo
 * @since       0.0.1
 */
class Wp_Rag_Ab_Amazon_Bedrock_Client {

	/**
	 * @var Wp_Rag_Ab_Aws_SigV4 $signer To sign the request.
	 */
	private $signer;
	private $region;
	private $knowledge_base_id;
	private $data_source_id;
	private $base_url;

	private $model_arn = null;

	/**
	 * @param string $access_key IAM access key.
	 * @param string $secret_key IAM secret key.
	 * @param string $region AWS region.
	 * @param string $knowledge_base_id Amazon Bedrock knowledge base ID.
	 * @param string $data_source_id Amazon Bedrock data source ID.
	 */
	public function __construct( $access_key, $secret_key, $region, $knowledge_base_id, $data_source_id ) {
		$this->signer            = new Wp_Rag_Ab_Aws_SigV4( $access_key, $secret_key, $region, 'bedrock' );
		$this->region            = $region;
		$this->knowledge_base_id = $knowledge_base_id;
		$this->data_source_id    = $data_source_id;
		$this->base_url          = "https://bedrock-agent.{$region}.amazonaws.com/knowledgebases/{$this->knowledge_base_id}/datasources/{$this->data_source_id}";
	}

	/**
	 * Ingest documents into Knowledge Base
	 *
	 * @param array $documents Array of KnowledgeBaseDocument.
	 * @return array
	 * @throws Exception
	 * @see https://docs.aws.amazon.com/bedrock/latest/APIReference/API_agent_IngestKnowledgeBaseDocuments.html
	 */
	public function ingest_documents( $documents ) {
		$uri     = '/documents';
		$payload = wp_json_encode( array( 'documents' => $documents ) );

		return $this->make_request( 'PUT', $uri, $payload );
	}

	/**
	 * Get Knowledge Base document
	 */
	public function get_document( $document_identifier ) {
		$uri = '/documents/' . rawurlencode( $document_identifier );

		return $this->make_request( 'GET', $uri );
	}

	/**
	 * List Knowledge Base documents
	 *
	 * TODO Not tested.
	 *
	 * @param int    $max_results
	 * @param string $next_token
	 * @return array
	 * @throws Exception
	 */
	public function list_documents( $max_results = 10, $next_token = null ) {
		$uri  = '/documents';
		$data = array( 'maxResults' => $max_results );

		if ( $next_token ) {
			$data['nextToken'] = $next_token;
		}

		$payload = wp_json_encode( $data );

		return $this->make_request( 'POST', $uri, $payload );
	}

	/**
	 * Delete Knowledge Base document
	 *
	 * @param string $document_identifier
	 * @return array
	 * @throws Exception
	 */
	public function delete_document( $document_identifier ) {
		$uri = '/documents/deleteDocuments';

		$data    = array(
			'documentIdentifiers' => array(
				array(
					'dataSourceType' => 'CUSTOM',
					'custom'         => array(
						'id' => (string) $document_identifier,
					),
				),
			),
		);
		$payload = wp_json_encode( $data );

		return $this->make_request( 'POST', $uri, $payload );
	}

	/**
	 * Make HTTP request with AWS Signature V4
	 *
	 * @param string $method HTTP method.
	 * @param string $path Relative path to the base URL.
	 * @param string $payload Request payload.
	 * @param string $query_string Query string.
	 * @return array
	 */
	private function make_request( $method, $path, $payload = '', $query_string = '' ) {
		$url = $this->base_url . $path;

		$headers = $this->signer->generate_signed_headers( $method, $url, $query_string, $payload );

		if ( ! empty( $query_string ) ) {
			$url .= '?' . $query_string;
		}

		// Prepare headers array for wp_remote_*.
		$wp_headers = array();
		foreach ( $headers as $key => $value ) {
			$wp_headers[ $key ] = $value;
		}

		$args = array(
			'method'      => $method,
			'headers'     => $wp_headers,
			'body'        => $payload,
			'timeout'     => 15,
			'redirection' => 0,
			'blocking'    => true,
		);

		$response = wp_remote_request( $url, $args );

		$http_code = wp_remote_retrieve_response_code( $response );
		$raw_body  = wp_remote_retrieve_body( $response );

		if ( is_wp_error( $response ) ) {
			$error = 'HTTP Request Error: ' . $response->get_error_message();
			return array(
				'status_code'  => $http_code,
				'body'         => $error,
				'raw_response' => $raw_body,
			);
		}

		$decoded_response = json_decode( $raw_body, true );

		return array(
			'status_code'  => $http_code,
			'body'         => $decoded_response,
			'raw_response' => $raw_body,
		);
	}

	/**
	 * Set the base URL for Agents for Amazon Bedrock Runtime.
	 *
	 * @return void
	 */
	private function set_base_url_for_runtime() {
		$this->base_url = "https://bedrock-agent-runtime.{$this->region}.amazonaws.com";
	}

	/**
	 * Set the model ARN.
	 *
	 * @param string $model_arn ARN of the model for retrieveAndGenerate.
	 * @return void
	 */
	public function set_model_arn( $model_arn ) {
		$this->model_arn = $model_arn;
	}

	/**
	 * Execute the `retrieve` API with the given query.
	 *
	 * @param string $query Query text.
	 * @return array Response from the API.
	 */
	public function retrieve( $query ) {
		$this->set_base_url_for_runtime();
		$data = array( 'retrievalQuery' => array( 'text' => $query ) );

		$uri     = "/knowledgebases/{$this->knowledge_base_id}/retrieve";
		$payload = wp_json_encode( $data );

		return $this->make_request( 'POST', $uri, $payload );
	}

	/**
	 * Execute the `retrieveAndGenerate` API with the given query.
	 * Call `set_model_arn` before calling this method.
	 *
	 * @param string $query Query text.
	 * @param string $session_id Session ID.
	 * @return array Response from the API.
	 * @see https://docs.aws.amazon.com/bedrock/latest/APIReference/API_agent-runtime_RetrieveAndGenerate.html .
	 */
	public function retrieve_and_generate( $query, $session_id = null ) {
		$this->set_base_url_for_runtime();

		if ( null === $this->model_arn ) {
			throw new Exception( 'Model ARN is not set.' );
		}

		$data = array(
			'input'                            => array( 'text' => $query ),
			'retrieveAndGenerateConfiguration' => array(
				'knowledgeBaseConfiguration' => array(
					'knowledgeBaseId' => $this->knowledge_base_id,
					'modelArn'        => $this->model_arn,
				),
				'type'                       => 'KNOWLEDGE_BASE',
			),
		);

		if ( null !== $session_id ) {
			$data['sessionId'] = $session_id;
		}
		$uri     = '/retrieveAndGenerate';
		$payload = wp_json_encode( $data );

		return $this->make_request( 'POST', $uri, $payload );
	}
}
