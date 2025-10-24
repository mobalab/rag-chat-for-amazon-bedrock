<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Rag_Chat_Ab_Helpers
 *
 * This class contains repetitive functions that
 * are used globally within the plugin.
 *
 * @package     RAGCHATAB
 * @subpackage  Classes/Rag_Chat_Ab_Helpers
 * @author      Kashima, Kazuo
 * @since       0.0.1
 */
class Rag_Chat_Ab_Helpers {


	/**
	 * ######################
	 * ###
	 * #### CALLABLE FUNCTIONS
	 * ###
	 * ######################
	 */

	/**
	 * HELPER COMMENT START
	 *
	 * Within this class, you can define common functions that you are
	 * going to use throughout the whole plugin.
	 *
	 * Down below you will find a demo function called output_text()
	 * To access this function from any other class, you can call it as followed:
	 *
	 * RAGCHATAB()->helpers->output_text( 'my text' );
	 */

	/**
	 * HELPER COMMENT END
	 */


	/**
	 * Logs error.
	 *
	 * @param $message
	 * @param $context
	 * @return void
	 */
	public function log_error( $message, $context = array() ) {
		$formatted_message = sprintf(
			'[%s] [%s] %s',
			RAGCHATAB_NAME,
			current_time( 'Y-m-d H:i:s' ),
			$message
		);

		if ( ! empty( $context ) ) {
			$formatted_message .= "\nContext: " . print_r( $context, true );
		}

		// @codingStandardsIgnoreLine
		error_log($formatted_message);
	}

	public function get_bedrock_client() {
		$options = get_option( Rag_Chat_Ab::instance()->pages['general-settings']::OPTION_NAME );
		if ( empty( $options['aws_region'] ) || empty( $options['aws_access_key'] ) || empty( $options['aws_secret_key'] ) || empty( $options['knowledge_base_id'] ) || empty( $options['data_source_id'] ) ) {
			return null;
		}

		return new Rag_Chat_Ab_Amazon_Bedrock_Client( $options['aws_access_key'], $options['aws_secret_key'], $options['aws_region'], $options['knowledge_base_id'], $options['data_source_id'] );
	}

	/**
	 * Sanitize array.
	 *
	 * @param array $raw_array
	 * @return array|string[]
	 */
	public function sanitize_array( $raw_array ) {
		return array_map(
			function ( $value ) {
				return sanitize_text_field( $value );
			},
			$raw_array
		);
	}

	/**
	 * Saves authentication data by serializing it and updating the specified option name.
	 *
	 * @param mixed $data The authentication data to be saved.
	 *
	 * @return void
	 */
	function save_auth_data( $data ) {
		$option_name     = Rag_Chat_Ab::OPTION_NAME_FOR_AUTH_DATA;
		$serialized_data = maybe_serialize( $data );
		update_option( $option_name, $serialized_data, 'no' );
	}

	/**
	 * Retrieves the authentication data, optionally filtered by a specific key.
	 *
	 * @param string|null $key Optional. The key to filter the authentication data. If not provided, the whole data set is returned.
	 *
	 * @return mixed The authentication data associated with the given key, or the entire data set if no key is provided.
	 */
	function get_auth_data( $key = null ) {
		$option_name     = Rag_Chat_Ab::OPTION_NAME_FOR_AUTH_DATA;
		$serialized_data = get_option( $option_name );
		if ( false === $serialized_data ) {
			return null;
		}
		$auth_data = maybe_unserialize( $serialized_data );
		if ( null === $key ) {
			return $auth_data;
		} else {
			return $auth_data[ $key ];
		}
	}

	/**
	 * Updates the authentication data with the provided key-value pair.
	 *
	 * @param string $key The key to update in the authentication data.
	 * @param mixed  $value The new value to associate with the specified key.
	 *
	 * @return void
	 */
	function update_auth_data( $key, $value ) {
		$data = $this->get_auth_data();
		if ( is_array( $data ) ) {
			$data[ $key ] = $value;
			$this->save_auth_data( $data );
		}
	}

	/**
	 * Deletes all the authentication data stored in wp_options table.
	 *
	 * @return void
	 */
	function delete_auth_data() {
		$option_name = Rag_Chat_Ab::OPTION_NAME_FOR_AUTH_DATA;
		delete_option( $option_name );
	}

	/**
	 * Deletes a specific key from the authentication data stored in wp_options table.
	 *
	 * @param string $key The key to be deleted from the authentication data.
	 *
	 * @return void
	 */
	function delete_key_from_auth_data( $key ) {
		$data = $this->get_auth_data();
		if ( is_array( $data ) ) {
			unset( $data[ $key ] );
			$this->save_auth_data( $data );
		}
	}
}
