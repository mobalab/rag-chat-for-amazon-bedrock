<?php
/**
 * RAG Chat for Amazon Bedrock
 *
 * @package       RAGCHATAB
 * @author        Mobalab, KK
 * @license       gplv3
 * @version       0.0.1
 *
 * @wordpress-plugin
 * Plugin Name:   RAG Chat for Amazon Bedrock
 * Plugin URI:    https://github.com/mobalab/wp-rag-for-amazon-bedrock
 * Description:   Integrates WordPress content with Amazon Bedrock Knowledge Bases to create an intelligent RAG (Retrieval-Augmented Generation) chatbot system with automatic content synchronization.
 * Version:       0.0.1
 * Author:        Mobalab, KK
 * Author URI:    https://github.com/mobalab
 * Text Domain:   rag-chat-ab
 * Domain Path:   /languages
 * License:       GPLv3
 * License URI:   https://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with RAG Chat for Amazon Bedrock. If not, see <https://www.gnu.org/licenses/gpl-3.0.html/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * HELPER COMMENT START
 *
 * This file contains the main information about the plugin.
 * It is used to register all components necessary to run the plugin.
 *
 * The comment above contains all information about the plugin
 * that are used by WordPress to differenciate the plugin and register it properly.
 * It also contains further PHPDocs parameter for a better documentation
 *
 * The function RAGCHATAB() is the main function that you will be able to
 * use throughout your plugin to extend the logic. Further information
 * about that is available within the sub classes.
 *
 * HELPER COMMENT END
 */

// Plugin name
define( 'RAGCHATAB_NAME', 'RAG Chat for Amazon Bedrock' );

// Plugin version
define( 'RAGCHATAB_VERSION', '0.0.1' );

// Plugin Root File
define( 'RAGCHATAB_PLUGIN_FILE', __FILE__ );

// Plugin base
define( 'RAGCHATAB_PLUGIN_BASE', plugin_basename( RAGCHATAB_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'RAGCHATAB_PLUGIN_DIR', plugin_dir_path( RAGCHATAB_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'RAGCHATAB_PLUGIN_URL', plugin_dir_url( RAGCHATAB_PLUGIN_FILE ) );

/**
 * Load the main class for the core functionality
 */
require_once RAGCHATAB_PLUGIN_DIR . 'core/class-rag-chat-ab.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  Kashima, Kazuo
 * @since   0.0.1
 * @return  object|Rag_Chat_Ab
 */
function RAGCHATAB() {
	return Rag_Chat_Ab::instance();
}

RAGCHATAB();
