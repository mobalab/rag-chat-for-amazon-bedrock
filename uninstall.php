<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

define( 'RAGCHATAB_NAME', 'RAG Chat' );
define( 'RAGCHATAB_PLUGIN_FILE', __FILE__ );
define( 'RAGCHATAB_PLUGIN_BASE', plugin_basename( RAGCHATAB_PLUGIN_FILE ) );
define( 'RAGCHATAB_PLUGIN_DIR', plugin_dir_path( RAGCHATAB_PLUGIN_FILE ) );

require_once RAGCHATAB_PLUGIN_DIR . 'core/class-rag-chat-ab.php';
require_once RAGCHATAB_PLUGIN_DIR . 'core/includes/classes/class-rag-chat-ab-helpers.php';
require_once RAGCHATAB_PLUGIN_DIR . 'core/includes/classes/class-rag-chat-ab-page-main.php';
require_once RAGCHATAB_PLUGIN_DIR . 'core/includes/classes/class-rag-chat-ab-page-general-settings.php';
require_once RAGCHATAB_PLUGIN_DIR . 'core/includes/classes/class-rag-chat-ab-page-chat-ui.php';

function RAGCHATAB() {
	return Rag_Chat_Ab::instance();
}

$option_names = array(
	RAGCHATAB()::OPTION_NAME_FOR_AUTH_DATA,
	RAGCHATAB()->pages['general-settings']::OPTION_NAME,
	RAGCHATAB()->pages['chat-ui']::OPTION_NAME,
);
foreach ( $option_names as $option_name ) {
	delete_option( $option_name );
}
