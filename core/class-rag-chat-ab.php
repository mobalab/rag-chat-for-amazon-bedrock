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
 * 3. Assign the class within the instance() function ( as e.g. self::$instance->helpers = new Wp_Rag_Ab_Helpers();)
 * 4. Register the class you added to core/includes/classes within the includes() function
 *
 * HELPER COMMENT END
 */

if ( ! class_exists( 'Wp_Rag_Ab' ) ) :

	/**
	 * Main Wp_Rag_Ab Class.
	 *
	 * @package     WPRAGAB
	 * @subpackage  Classes/Wp_Rag_Ab
	 * @since       0.0.1
	 * @author      Kashima, Kazuo
	 */
	final class Wp_Rag_Ab {

		const OPTION_NAME_FOR_AUTH_DATA = 'wp_rag_ab_auth_data';


		/**
		 * The real instance
		 *
		 * @access  private
		 * @since   0.0.1
		 * @var     object|Wp_Rag_Ab
		 */
		private static $instance;

		/**
		 * WPRAGAB helpers object.
		 *
		 * @access  public
		 * @since   0.0.1
		 * @var     object|Wp_Rag_Ab_Helpers
		 */
		public $helpers;

		/**
		 * WPRAGAB settings object.
		 *
		 * @access  public
		 * @since   0.0.1
		 * @var     object|Wp_Rag_Ab_Settings
		 */
		public $settings;

		/**
		 * @access  public
		 * @since   0.0.1
		 * @var     array
		 */
		public $pages;

		/**
		 * WPRAGAB frontend object.
		 *
		 * @access  public
		 * @since   0.0.1
		 * @var     object|Wp_Rag_Ab_Frontend
		 */
		public $frontend;

		/**
		 * WPRAGAB PostHooks object.
		 *
		 * @access  public
		 * @since   0.0.3
		 * @var     object|Wp_Rag_Ab_PostHooks
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
			_doing_it_wrong( __FUNCTION__, esc_html__( 'You are not allowed to clone this class.', 'wp-rag-ab' ), '0.0.1' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @access  public
		 * @since   0.0.1
		 * @return  void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'You are not allowed to unserialize this class.', 'wp-rag-ab' ), '0.0.1' );
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
		 * @return      object|Wp_Rag_Ab   The one true Wp_Rag
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Wp_Rag_Ab ) ) {
				self::$instance = new Wp_Rag_Ab();
				self::$instance->base_hooks();
				self::$instance->includes();
				self::$instance->helpers   = new Wp_Rag_Ab_Helpers();
				self::$instance->settings  = new Wp_Rag_Ab_Settings();
				self::$instance->pages     = array(
					'main'               => new Wp_Rag_Ab_Page_Main(),
					'general-settings'   => new Wp_Rag_Ab_Page_GeneralSettings(),
					'content-management' => new Wp_Rag_Ab_Page_ContentManagement(),
					'chat-ui'            => new Wp_Rag_Ab_Page_ChatUI(),
				);
				self::$instance->frontend  = new Wp_Rag_Ab_Frontend();
				self::$instance->posthooks = new Wp_Rag_Ab_PostHooks();

				// Fire the plugin logic.
				new Wp_Rag_Ab_Run();

				/**
				 * Fire a custom action to allow dependencies
				 * after the successful plugin setup
				 */
				do_action( 'WPRAGAB/plugin_loaded' );
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
			require_once WPRAGAB_PLUGIN_DIR . 'core/includes/classes/class-wp-rag-ab-helpers.php';
			require_once WPRAGAB_PLUGIN_DIR . 'core/includes/classes/class-wp-rag-ab-settings.php';

			require_once WPRAGAB_PLUGIN_DIR . 'core/includes/classes/class-wp-rag-ab-page-main.php';
			require_once WPRAGAB_PLUGIN_DIR . 'core/includes/classes/class-wp-rag-ab-page-general-settings.php';
			require_once WPRAGAB_PLUGIN_DIR . 'core/includes/classes/class-wp-rag-ab-page-content-management.php';
			require_once WPRAGAB_PLUGIN_DIR . 'core/includes/classes/class-wp-rag-ab-page-chat-ui.php';

			require_once WPRAGAB_PLUGIN_DIR . 'core/includes/classes/class-wp-rag-ab-frontend.php';
			require_once WPRAGAB_PLUGIN_DIR . 'core/includes/classes/class-wp-rag-ab-posthooks.php';

			require_once WPRAGAB_PLUGIN_DIR . 'core/includes/classes/class-wp-rag-ab-aws-sigv4.php';
			require_once WPRAGAB_PLUGIN_DIR . 'core/includes/classes/class-wp-rag-ab-amazon-bedrock-client.php';
			require_once WPRAGAB_PLUGIN_DIR . 'core/includes/classes/class-wp-rag-ab-sync-service.php';

			require_once WPRAGAB_PLUGIN_DIR . 'core/includes/classes/class-wp-rag-ab-run.php';
		}

		/**
		 * Add base hooks for the core functionality
		 *
		 * @access  private
		 * @since   0.0.1
		 * @return  void
		 */
		private function base_hooks() {
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access  public
		 * @since   0.0.1
		 * @return  void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'wp-rag-ab', false, dirname( plugin_basename( WPRAGAB_PLUGIN_FILE ) ) . '/languages/' );
		}
	}

endif; // End if class_exists check.
