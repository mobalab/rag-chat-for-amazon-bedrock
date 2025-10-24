<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * HELPER COMMENT START
 *
 * This is the main class that is responsible for registering
 * the core functions, including the files and setting up all features.
 *
 * To add a new class, here's what you need to do:
 * 1. Add your new class within the following folder: core/includes/classes
 * 2. Create a new variable you want to assign the class to (as e.g. public $helpers)
 * 3. Assign the class within the instance() function ( as e.g. self::$instance->helpers = new Rag_Chat_Ab_Helpers();)
 * 4. Register the class you added to core/includes/classes within the includes() function
 *
 * HELPER COMMENT END
 */

if ( ! class_exists( 'Rag_Chat_Ab' ) ) :

	/**
	 * Main Rag_Chat_Ab Class.
	 *
	 * @package     RAGCHATAB
	 * @subpackage  Classes/Rag_Chat_Ab
	 * @since       0.0.1
	 * @author      Kashima, Kazuo
	 */
	final class Rag_Chat_Ab {

		const OPTION_NAME_FOR_AUTH_DATA = 'rag_chat_ab_auth_data';


		/**
		 * The real instance
		 *
		 * @access  private
		 * @since   0.0.1
		 * @var     object|Rag_Chat_Ab
		 */
		private static $instance;

		/**
		 * RAGCHATAB helpers object.
		 *
		 * @access  public
		 * @since   0.0.1
		 * @var     object|Rag_Chat_Ab_Helpers
		 */
		public $helpers;

		/**
		 * RAGCHATAB settings object.
		 *
		 * @access  public
		 * @since   0.0.1
		 * @var     object|Rag_Chat_Ab_Settings
		 */
		public $settings;

		/**
		 * @access  public
		 * @since   0.0.1
		 * @var     array
		 */
		public $pages;

		/**
		 * RAGCHATAB frontend object.
		 *
		 * @access  public
		 * @since   0.0.1
		 * @var     object|Rag_Chat_Ab_Frontend
		 */
		public $frontend;

		/**
		 * RAGCHATAB PostHooks object.
		 *
		 * @access  public
		 * @since   0.0.3
		 * @var     object|Rag_Chat_Ab_PostHooks
		 */
		public $posthooks;

		/**
		 * Throw error on object clone.
		 *
		 * Cloning instances of the class is forbidden.
		 *
		 * @access  public
		 * @since   0.0.1
		 * @return  void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'You are not allowed to clone this class.', 'rag-chat-ab' ), '0.0.1' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @access  public
		 * @since   0.0.1
		 * @return  void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'You are not allowed to unserialize this class.', 'rag-chat-ab' ), '0.0.1' );
		}

		/**
		 * Main Wp_Rag Instance.
		 *
		 * Insures that only one instance of Wp_Rag exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @access      public
		 * @since       0.0.1
		 * @static
		 * @return      object|Rag_Chat_Ab   The one true Wp_Rag
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Rag_Chat_Ab ) ) {
				self::$instance = new Rag_Chat_Ab();
				self::$instance->base_hooks();
				self::$instance->includes();
				self::$instance->helpers   = new Rag_Chat_Ab_Helpers();
				self::$instance->settings  = new Rag_Chat_Ab_Settings();
				self::$instance->pages     = array(
					'main'               => new Rag_Chat_Ab_Page_Main(),
					'general-settings'   => new Rag_Chat_Ab_Page_GeneralSettings(),
					'content-management' => new Rag_Chat_Ab_Page_ContentManagement(),
					'chat-ui'            => new Rag_Chat_Ab_Page_ChatUI(),
				);
				self::$instance->frontend  = new Rag_Chat_Ab_Frontend();
				self::$instance->posthooks = new Rag_Chat_Ab_PostHooks();

				// Fire the plugin logic.
				new Rag_Chat_Ab_Run();

				/**
				 * Fire a custom action to allow dependencies
				 * after the successful plugin setup
				 */
				do_action( 'RAGCHATAB/plugin_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Include required files.
		 *
		 * @access  private
		 * @since   0.0.1
		 * @return  void
		 */
		private function includes() {
			require_once RAGCHATAB_PLUGIN_DIR . 'core/includes/classes/class-rag-chat-ab-helpers.php';
			require_once RAGCHATAB_PLUGIN_DIR . 'core/includes/classes/class-rag-chat-ab-settings.php';

			require_once RAGCHATAB_PLUGIN_DIR . 'core/includes/classes/class-rag-chat-ab-page-main.php';
			require_once RAGCHATAB_PLUGIN_DIR . 'core/includes/classes/class-rag-chat-ab-page-general-settings.php';
			require_once RAGCHATAB_PLUGIN_DIR . 'core/includes/classes/class-rag-chat-ab-page-content-management.php';
			require_once RAGCHATAB_PLUGIN_DIR . 'core/includes/classes/class-rag-chat-ab-page-chat-ui.php';

			require_once RAGCHATAB_PLUGIN_DIR . 'core/includes/classes/class-rag-chat-ab-frontend.php';
			require_once RAGCHATAB_PLUGIN_DIR . 'core/includes/classes/class-rag-chat-ab-posthooks.php';

			require_once RAGCHATAB_PLUGIN_DIR . 'core/includes/classes/class-rag-chat-ab-aws-sigv4.php';
			require_once RAGCHATAB_PLUGIN_DIR . 'core/includes/classes/class-rag-chat-ab-amazon-bedrock-client.php';
			require_once RAGCHATAB_PLUGIN_DIR . 'core/includes/classes/class-rag-chat-ab-sync-service.php';

			require_once RAGCHATAB_PLUGIN_DIR . 'core/includes/classes/class-rag-chat-ab-run.php';
		}

		/**
		 * Add base hooks for the core functionality
		 *
		 * @access  private
		 * @since   0.0.1
		 * @return  void
		 */
		private function base_hooks() {
		}
	}

endif; // End if class_exists check.
