<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wp_Rag_Ab_Aws_SigV4
 *
 * @package     WPRAGAB
 * @subpackage  Classes/Wp_Rag_Ab_Aws_SigV4
 * @author      Kashima, Kazuo
 * @since       0.0.1
 */
class Wp_Rag_Ab_Aws_SigV4 {

	private $access_key;
	private $secret_key;
	private $region;
	private $service;

	/**
	 * @param string $access_key IAM access key.
	 * @param string $secret_key IAM secret key.
	 * @param string $region AWS region.
	 * @param string $service AWS service. Note that the service name and the 1st part of the endpoint URL are sometimes different.
	 */
	public function __construct( $access_key, $secret_key, $region = 'us-east-1', $service = 'bedrock' ) {
		$this->access_key = $access_key;
		$this->secret_key = $secret_key;
		$this->region     = $region;
		$this->service    = $service;
	}

	/**
	 * Generate AWS Signature V4 headers
	 *
	 * @param string $method HTTP method.
	 * @param string $uri e.g., https://bedrock-agent.us-east-1.amazonaws.com/knowledgebases/123ABC7890/datasources/ABC456DEFG .
	 * @param string $query_string Query string.
	 * @param string $payload Payload.
	 * @param array  $headers Request headers.
	 * @return array
	 */
	public function generate_signed_headers( $method, $uri, $query_string = '', $payload = '', $headers = array() ) {
		$datetime = gmdate( 'Ymd\THis\Z' );
		$date     = gmdate( 'Ymd' );

		$parts = wp_parse_url( $uri );
		$host  = $parts['host'];
		$path  = isset( $parts['path'] ) ? $parts['path'] : '/';
		// If $query_string is not passed and $uri contains a query string, use it.
		if ( '' === $query_string && isset( $parts['query'] ) ) {
			$query_string = $parts['query'];
		}

		// Default headers.
		$default_headers = array(
			'Host'         => strtolower( $host ),
			'X-Amz-Date'   => $datetime,
			'Content-Type' => 'application/json',
		);

		$normalized_headers = array_merge( $default_headers, $headers );

		// Step 1: Create canonical request.
		$canonical_request = $this->create_canonical_request( $method, $path, $query_string, $normalized_headers, $payload );

		// Step 2: Create string to sign.
		$credential_scope = $date . '/' . $this->region . '/' . $this->service . '/aws4_request';
		$string_to_sign   = "AWS4-HMAC-SHA256\n" . $datetime . "\n" . $credential_scope . "\n" . hash( 'sha256', $canonical_request );

		// Step 3: Calculate signature.
		$signature = $this->calculate_signature( $string_to_sign, $date );

		// Step 4: Add authorization header.
		$signed_headers = implode( ';', array_map( 'strtolower', array_keys( $normalized_headers ) ) );
		$authorization  = 'AWS4-HMAC-SHA256 Credential=' . $this->access_key . '/' . $credential_scope .
			', SignedHeaders=' . $signed_headers . ', Signature=' . $signature;

		$normalized_headers['Authorization'] = $authorization;

		return $normalized_headers;
	}

	private function create_canonical_request( $method, $uri, $query_string, $headers, $payload ) {
		// Canonical URI/
		$canonical_uri = $uri;

		// Canonical query string.
		$canonical_query_string = $query_string;

		// Canonical headers.
		$canonical_headers    = '';
		$signed_headers_array = array();

		ksort( $headers );
		foreach ( $headers as $key => $value ) {
			$key                    = strtolower( $key );
			$canonical_headers     .= $key . ':' . trim( $value ) . "\n";
			$signed_headers_array[] = $key;
		}

		$signed_headers = implode( ';', $signed_headers_array );

		// Payload hash.
		$payload_hash = hash( 'sha256', $payload );

		return $method . "\n" . $canonical_uri . "\n" . $canonical_query_string . "\n" .
			$canonical_headers . "\n" . $signed_headers . "\n" . $payload_hash;
	}

	/**
	 * @param string $string_to_sign
	 * @param string $date yyyyMMdd format.
	 * @return string
	 */
	private function calculate_signature( $string_to_sign, $date ) {
		$k_date    = hash_hmac( 'sha256', $date, 'AWS4' . $this->secret_key, true );
		$k_region  = hash_hmac( 'sha256', $this->region, $k_date, true );
		$k_service = hash_hmac( 'sha256', $this->service, $k_region, true );
		$k_signing = hash_hmac( 'sha256', 'aws4_request', $k_service, true );

		return hash_hmac( 'sha256', $string_to_sign, $k_signing );
	}
}
