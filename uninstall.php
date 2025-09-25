<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

define( 'WPRAGAB_NAME', 'WP RAG' );
define( 'WPRAGAB_PLUGIN_FILE', __FILE__ );
define( 'WPRAGAB_PLUGIN_BASE', plugin_basename( WPRAG_PLUGIN_FILE ) );
define( 'WPRAGAB_PLUGIN_DIR', plugin_dir_path( WPRAG_PLUGIN_FILE ) );

require_once WPRAGAB_PLUGIN_DIR . 'core/class-wp-rag-ab.php';
require_once WPRAGAB_PLUGIN_DIR . 'core/includes/classes/class-wp-rag-ab-helpers.php';
require_once WPRAGAB_PLUGIN_DIR . 'core/includes/classes/class-wp-rag-page-main.php';
require_once WPRAGAB_PLUGIN_DIR . 'core/includes/classes/class-wp-rag-page-general-settings.php';
require_once WPRAGAB_PLUGIN_DIR . 'core/includes/classes/class-wp-rag-page-chat-ui.php';

function WPRAGAB() {
	return Wp_Rag_Ab::instance();
}

$option_names = array(
	WPRAGAB()::OPTION_NAME_FOR_AUTH_DATA,
	WPRAGAB()->pages['general-settings']::OPTION_NAME,
	WPRAGAB()->pages['ai-configuration']::OPTION_NAME,
	WPRAGAB()->pages['chat-ui']::OPTION_NAME,
);
foreach ( $option_names as $option_name ) {
	delete_option( $option_name );
}
