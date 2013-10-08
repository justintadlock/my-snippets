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

/* Set constant path to the my_snippets plugin directory. */
define( MY_SNIPPETS_DIR, plugin_dir_path( __FILE__ ) );

/* Launch the plugin. */
add_action( 'plugins_loaded', 'my_snippets_plugin_init' );

/**
 * Initialize the plugin.  This function loads the required files needed for the plugin
 * to run in the proper order and adds needed functions to the required hooks.
 *
 * @since 0.1
 */
function my_snippets_plugin_init() {

	/* Load the translation of the plugin. */
	load_plugin_textdomain( 'my-snippets', false, 'my-snippets/languages' );

	/* Load global functions for the WordPress admin. */
	if ( is_admin() )
		require_once( MY_SNIPPETS_DIR . '/meta-box.php' );

	add_action( 'widgets_init', 'my_snippets_load_widgets' );
}

/**
 * Loads the widgets packaged with the My Snippets plugin.
 *
 * @since 0.1
 */
function my_snippets_load_widgets() {
	require_once( MY_SNIPPETS_DIR . '/widget-snippet.php' );
	register_widget( 'My_Snippets_Widget_Snippet' );
}

?>