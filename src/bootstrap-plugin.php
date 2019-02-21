<?php
/**
 * Bootstraps the plugin.
 *
 * @package   MySnippets
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2009 - 2019, Justin Tadlock
 * @link      http://themehybrid.com/plugins/my-snippets
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace MySnippets;

use MySnippets\Admin\MetaBox;
use MySnippets\Widgets\Snippet;

// Load translations.
add_action( 'plugins_loaded', function() {
	load_plugin_textdomain( 'my-snippets', false, 'my-snippets/resources/lang' );
} );

// Load meta boxes.
add_action( 'admin_menu', function() {
	require_once( 'Admin/MetaBox.php' );

	$box = ( new MetaBox() )->boot();
} );

// Load widgets.
add_action( 'widgets_init', function() {
	require_once( 'Widgets/Snippet.php' );

	register_widget( Snippet::class );
} );
