<?php
/**
 * WP RAG for Amazon Bedrock
 *
 * @package       WPRAGAB
 * @author        Mobalab, KK
 * @license       gplv3
 * @version       0.0.1
 *
 * @wordpress-plugin
 * Plugin Name:   WP RAG for Amazon Bedrock
 * Plugin URI:    https://github.com/mobalab/wp-rag-for-amazon-bedrock
 * Description:   A WordPress plugin for building RAG
 * Version:       0.0.1
 * Author:        Mobalab, KK
 * Author URI:    https://github.com/mobalab
 * Text Domain:   wp-rag-ab
 * Domain Path:   /languages
 * License:       GPLv3
 * License URI:   https://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with WP RAG for Amazon Bedrock. If not, see <https://www.gnu.org/licenses/gpl-3.0.html/>.
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
 * The function WPRAGAB() is the main function that you will be able to
 * use throughout your plugin to extend the logic. Further information
 * about that is available within the sub classes.
 *
 * HELPER COMMENT END
 */

// Plugin name
define( 'WPRAGAB_NAME', 'WP RAG for Amazon Bedrock' );

// Plugin version
define( 'WPRAGAB_VERSION', '0.0.1' );

// Plugin Root File
define( 'WPRAGAB_PLUGIN_FILE', __FILE__ );

// Plugin base
define( 'WPRAGAB_PLUGIN_BASE', plugin_basename( WPRAGAB_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'WPRAGAB_PLUGIN_DIR', plugin_dir_path( WPRAGAB_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'WPRAGAB_PLUGIN_URL', plugin_dir_url( WPRAGAB_PLUGIN_FILE ) );

/**
 * Load the main class for the core functionality
 */
require_once WPRAGAB_PLUGIN_DIR . 'core/class-wp-rag-ab.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  Kashima, Kazuo
 * @since   0.0.1
 * @return  object|Wp_Rag_Ab
 */
function WPRAGAB() {
	return Wp_Rag_Ab::instance();
}

WPRAGAB();
