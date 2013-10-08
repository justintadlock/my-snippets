<?php
/**
 * Plugin Name: My Snippets
 * Plugin URI: http://themehybrid.com/plugins/my-snippets
 * Description: Add custom snippets to your widget areas (sidebars) on a post-by-post basis.
 * Version: 0.2.0-alpha
 * Author: Justin Tadlock
 * Author URI: http://justintadlock.com
 *
 * The My Snippets plugin creates a meta box on the edit post screen in the WordPress admin
 * that allows users to add custom metadata to the post. This metadata is then displayed for
 * the singular view of the post on the frontend of the site by using the My Snippets widget.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write 
 * to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package   MySnippets
 * @version   0.2.0
 * @since     0.1.0
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2009 - 2013, Justin Tadlock
 * @link      http://themehybrid.com/plugins/my-snippets
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Sets up the plugin.
 *
 * @since 0.2.0
 */
final class My_Snippets_Plugin {

	/**
	 * Holds the instance of this class.
	 *
	 * @since  0.2.0
	 * @access private
	 * @var    object
	 */
	private static $instance;

	/**
	 * Stores the directory path for this plugin.
	 *
	 * @since  0.2.0
	 * @access private
	 * @var    string
	 */
	private $directory_path;

	/**
	 * Stores the directory URI for this plugin.
	 *
	 * @since  0.2.0
	 * @access private
	 * @var    string
	 */
	private $directory_uri;

	/**
	 * Plugin setup.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		/* Set the properties needed by the plugin. */
		add_action( 'plugins_loaded', array( $this, 'setup' ), 1 );

		/* Internationalize the text strings used. */
		add_action( 'plugins_loaded', array( $this, 'i18n' ), 2 );

		/* Load the functions files. */
		add_action( 'plugins_loaded', array( $this, 'includes' ), 3 );

		/* Load the admin files. */
		add_action( 'plugins_loaded', array( $this, 'admin' ), 4 );

		/* Register widgets. */
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
	}

	/**
	 * Defines the directory path and URI for the plugin.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function setup() {
		$this->directory_path = trailingslashit( plugin_dir_path( __FILE__ ) );
		$this->directory_uri  = trailingslashit( plugin_dir_url(  __FILE__ ) );
	}

	/**
	 * Loads the initial files needed by the plugin.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function includes() {
		require_once( "{$this->directory_path}widget-snippet.php" );
	}

	/**
	 * Loads the translation files.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function i18n() {

		/* Load the translation of the plugin. */
		load_plugin_textdomain( 'my-snippets', false, 'my-snippets/languages' );
	}

	/**
	 * Loads the admin functions and files.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function admin() {

		if ( is_admin() )
			require_once( "{$this->directory_path}admin/meta-box.php" );
	}

	/**
	 * Registers widgets.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function register_widgets() {
		register_widget( 'My_Snippets_Widget_Snippet' );
	}

	/**
	 * Returns the instance.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		if ( !self::$instance )
			self::$instance = new self;

		return self::$instance;
	}
}

My_Snippets_Plugin::get_instance();

?>