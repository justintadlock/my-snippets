<?php
/**
 * Creates a custom meta box for the plugin.
 *
 * @package   MySnippets
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2009 - 2013, Justin Tadlock
 * @link      http://themehybrid.com/plugins/my-snippets
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

final class My_Snippets_Meta_Boxes {

	/**
	 * Holds the instance of this class.
	 *
	 * @since  0.2.0
	 * @access private
	 * @var    object
	 */
	private static $instance;

	/**
	 * Sets up the needed actions for adding and saving the meta boxes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' )        );
		add_action( 'save_post',      array( $this, 'save_post'      ), 10, 2 );
	}

	/**
	 * Adds the meta box.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function add_meta_boxes( $post_type ) {

		$post_type_object = get_post_type_object( $post_type );

		if ( 'page' !== $post_type && false === $post_type_object->publicly_queryable )
			return;

		add_meta_box( 
			'my-snippets', 
			__( 'My Snippets', 'my-snippets' ), 
			array( $this, 'snippet_meta_box' ), 
			$post_type, 
			'normal', 
			'low' 
		);
	}

	/**
	 * Displays the "my snippets" meta box.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  object  $object  Current post object.
	 * @param  array   $box
	 * @return void
	 */
	public function snippet_meta_box( $post, $box ) { 

		wp_nonce_field( plugin_basename( __FILE__ ), 'my_snippets_meta_nonce' );

		$snippet_title   = get_post_meta( $post->ID, 'Snippet Title', true );
		$snippet_content = get_post_meta( $post->ID, 'Snippet', true ); ?>

		<p>
			<label for="snippet-title"><?php _e( 'Snippet Title', 'my-snippets' ); ?></label> 
			<input class="widefat" type="text" name="snippet-title" id="snippet-title" value="<?php echo esc_attr( $snippet_title ); ?>" />
		</p>

		<p>
			<label for="snippet"><?php _e( 'Snippet Content', 'my-snippets'); ?></label>
			<textarea class="widefat" name="snippet-content" id="snippet-content" cols="60" rows="4"><?php echo esc_textarea( $snippet_content ); ?></textarea>
			<span class="description"><?php _e( 'Add text, <acronym title="Hypertext Markup Language">HTML</acronym>, and/or shortcodes.', 'my-snippets' ); ?></span>
		</p><?php
	}

	/**
	 * Saves the custom post meta for the meta boxes.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  int     $post_id
	 * @param  object  $post
	 * @return void
	 */
	public function save_post( $post_id, $post ) {

		/* Verify the nonce. */
		if ( !isset( $_POST['my_snippets_meta_nonce'] ) || !wp_verify_nonce( $_POST['my_snippets_meta_nonce'], plugin_basename( __FILE__ ) ) )
			return;

		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );

		/* Check if the current user has permission to edit the post. */
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;

		/* Don't save if the post is only a revision. */
		if ( 'revision' == $post->post_type )
			return;

		$meta = array(
			'Snippet Title' => strip_tags( $_POST['snippet-title'] )
		);

		if ( current_user_can('unfiltered_html') )
			$meta['Snippet'] =  $_POST['snippet-content'];
		else
			$meta['Snippet'] = stripslashes( wp_filter_post_kses( addslashes( $_POST['snippet-content'] ) ) );

		foreach ( $meta as $meta_key => $new_meta_value ) {

			/* Get the meta value of the custom field key. */
			$meta_value = get_post_meta( $post_id, $meta_key, true );

			/* If a new meta value was added and there was no previous value, add it. */
			if ( $new_meta_value && '' == $meta_value )
				add_post_meta( $post_id, $meta_key, $new_meta_value, true );

			/* If the new meta value does not match the old value, update it. */
			elseif ( $new_meta_value && $new_meta_value != $meta_value )
				update_post_meta( $post_id, $meta_key, $new_meta_value );

			/* If there is no new meta value but an old value exists, delete it. */
			elseif ( '' == $new_meta_value && $meta_value )
				delete_post_meta( $post_id, $meta_key, $meta_value );
		}
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

My_Snippets_Meta_Boxes::get_instance();

?>