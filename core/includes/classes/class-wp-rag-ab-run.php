<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * HELPER COMMENT START
 *
 * This class is used to bring your plugin to life.
 * All the other registered classed bring features which are
 * controlled and managed by this class.
 *
 * Within the add_hooks() function, you can register all of
 * your WordPress related actions and filters as followed:
 *
 * add_action( 'my_action_hook_to_call', array( $this, 'the_action_hook_callback', 10, 1 ) );
 * or
 * add_filter( 'my_filter_hook_to_call', array( $this, 'the_filter_hook_callback', 10, 1 ) );
 * or
 * add_shortcode( 'my_shortcode_tag', array( $this, 'the_shortcode_callback', 10 ) );
 *
 * Once added, you can create the callback function, within this class, as followed:
 *
 * public function the_action_hook_callback( $some_variable ){}
 * or
 * public function the_filter_hook_callback( $some_variable ){}
 * or
 * public function the_shortcode_callback( $attributes = array(), $content = '' ){}
 *
 *
 * HELPER COMMENT END
 */

/**
 * Class Wp_Rag_Run
 *
 * Thats where we bring the plugin to life
 *
 * @package     WPRAGAB
 * @subpackage  Classes/Wp_Rag_Ab_Run
 * @author      Kashima, Kazuo
 * @since       0.0.1
 */
class Wp_Rag_Ab_Run {

	/**
	 * Our Wp_Rag_Ab_Run constructor
	 * to run the plugin logic.
	 *
	 * @since 0.0.1
	 */
	public function __construct() {
		$this->add_hooks();
	}

	/**
	 * ######################
	 * ###
	 * #### WordPress HOOKS
	 * ###
	 * ######################
	 */

	/**
	 * Registers all WordPress and plugin related hooks
	 *
	 * @access  private
	 * @since   0.0.1
	 * @return  void
	 */
	private function add_hooks() {

		add_action( 'plugin_action_links_' . WPRAGAB_PLUGIN_DIR, array( $this, 'add_plugin_action_link' ), 20 );
		add_action( 'wp_enqueue_scripts', array( WPRAGAB()->frontend, 'enqueue_scripts_and_styles' ), 20 );

		add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 20 );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

		add_action( 'wp_ajax_wp_rag_ab_process_chat', array( WPRAGAB()->frontend, 'process_chat' ) );
		add_action( 'wp_ajax_nopriv_wp_rag_ab_process_chat', array( WPRAGAB()->frontend, 'process_chat' ) );

		add_action( 'wp_head', array( WPRAGAB()->frontend, 'output_custom_css' ) );

		add_shortcode( 'wp_rag_ab_chat', array( WPRAGAB()->frontend, 'shortcode' ) );
		// Render the chat window after the footer.
		add_action( 'wp_footer', array( WPRAGAB()->frontend, 'show_chat_window' ) );

		add_action( 'pre_post_update', array( WPRAGAB()->posthooks, 'store_previous_status' ), 10, 2 );
		add_action( 'save_post', array( WPRAGAB()->posthooks, 'handle_post_save' ), 10, 3 );
		add_action( 'wp_trash_post', array( WPRAGAB()->posthooks, 'handle_post_delete' ), 10, 1 );
		add_action( 'before_delete_post', array( WPRAGAB()->posthooks, 'handle_post_delete' ), 10, 1 );
	}

	/**
	 * ######################
	 * ###
	 * #### WordPress HOOK CALLBACKS
	 * ###
	 * ######################
	 */

	/**
	 * Adds action links to the plugin list table
	 *
	 * @access   public
	 * @since    0.0.1
	 *
	 * @param    array $links An array of plugin action links.
	 *
	 * @return   array   An array of plugin action links.
	 */
	public function add_plugin_action_link( $links ) {

		$links['our_shop'] = sprintf( '<a href="%s" target="_blank title="Documentation" style="font-weight:700;">%s</a>', 'https://github.com/mobalab/wp-rag-for-amazon-bedrock', __( 'Documentation', 'wp-rag-ab' ) );

		return $links;
	}


	/**
	 * ####################
	 * ### WP Webhooks
	 * ####################
	 */

	/**
	 * @param $tabs
	 *
	 * @return void
	 */
	public function add_admin_menu( $tabs ) {
		add_menu_page(
			'WP RAG for Amazon Bedrock',
			'WP RAG Bedrock',
			'manage_options',
			'wp-rag-ab-main',
			array( WPRAGAB()->pages['main'], 'render_main_page' ),
			'dashicons-admin-generic',
			100
		);

		add_submenu_page(
			'wp-rag-ab-main',
			'WP RAG for Amazon Bedrock General Settings', // Page title
			'General Settings', // Title on the left menu
			'manage_options', // Capability
			'wp-rag-ab-general-settings', // Menu slug
			array( WPRAGAB()->pages['general-settings'], 'page_content' ) // Callback function
		);

		add_submenu_page(
			'wp-rag-ab-main',
			'WP RAG Content Management',
			'Content Management',
			'manage_options',
			'wp-rag-ab-content-management',
			array( WPRAGAB()->pages['content-management'], 'page_content' )
		);

		add_submenu_page(
			'wp-rag-ab-main',
			'WP RAG for Amazon Bedrock Chat UI',
			'Chat UI',
			'manage_options',
			'wp-rag-ab-chat-ui',
			array( WPRAGAB()->pages['chat-ui'], 'page_content' )
		);
	}

	public function admin_notices() {
		settings_errors( 'general' ); // Show default message(s).
		settings_errors( 'wp_rag_ab_messages' );
	}


	/**
	 * Initializes the admin pages.
	 *
	 * @return void
	 */
	function settings_init() {
		$current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

		if ( isset( $_POST['_wp_http_referer'] ) ) {
			$_wp_http_referer = sanitize_text_field( wp_unslash( $_POST['_wp_http_referer'] ) );
			$referer_page     = wp_unslash( $_wp_http_referer );
			$referer_query    = parse_url( $referer_page, PHP_URL_QUERY );
			parse_str( $referer_query, $params );
			$referer_page = $params['page'];
		} else {
			$referer_page = null;
		}

		if ( 'wp-rag-ab-main' === $current_page || 'wp-rag-ab-main' === $referer_page ) {
			// TODO Check nonce.
			$cls = WPRAGAB()->pages['main'];

			$cls->enqueue_scripts_and_styles();

			if ( isset( $_POST['wp_rag_ab_import_submit'] ) ) {
				$cls->handle_import_form_submission();
			}
			if ( isset( $_POST['wp_rag_ab_query_submit'] ) ) {
				$cls->handle_query_form_submission();
			}
		} elseif ( 'wp-rag-ab-general-settings' === $current_page || 'wp-rag-ab-general-settings' === $referer_page ) {
			$cls = WPRAGAB()->pages['general-settings'];

			register_setting(
				'wp_rag_ab_options',
				$cls::OPTION_NAME,
			);

			$cls->add_wordpress_authentication_section_and_fields();
			$cls->add_aws_section_and_fields();
		} elseif ( 'wp-rag-ab-content-management' === $current_page || 'wp-rag-ab-content-management' === $referer_page ) {
			$cls = WPRAGAB()->pages['content-management'];

			if ( isset( $_POST['wp_rag_ab_import_submit'] ) ) {
				$cls->handle_import_form_submission();
			}
			if ( isset( $_POST['wp_rag_ab_generate_submit'] ) ) {
				$cls->handle_generate_form_submission();
			}

			$cls->add_import_posts_section_and_fields();
		} elseif ( 'wp-rag-ab-chat-ui' === $current_page || 'wp-rag-ab-chat-ui' === $referer_page ) {
			$cls = WPRAGAB()->pages['chat-ui'];

			register_setting(
				'wp_rag_ab_options',
				$cls::OPTION_NAME,
			);

			$cls->add_appearance_section_and_fields();
			$cls->add_windows_settings_section_and_fields();
			$cls->add_input_and_button_labels_section_and_fields();
			$cls->add_participant_names_section_and_fields();
			$cls->add_display_options_section_and_fields();
		}
	}
}
