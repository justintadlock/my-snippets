<?php
/**
 * Creates a custom meta box for the plugin.
 *
 * @package   MySnippets
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2009 - 2019, Justin Tadlock
 * @link      http://themehybrid.com/plugins/my-snippets
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace MySnippets\Admin;

/**
 * Meta box class.
 *
 * @since  1.0.0
 * @access public
 */
class MetaBox {

	/**
	 * Sets up the needed actions for adding and saving the meta boxes.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function boot() {

		add_action( 'add_meta_boxes', [ $this, 'addMetaBoxes' ] );

		add_action( 'save_post', [ $this, 'saveMeta' ], 10, 2 );
	}

	/**
	 * Adds the meta box.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function addMetaBoxes( $post_type ) {

		$post_type_object = get_post_type_object( $post_type );

		if ( 'page' !== $post_type && false === $post_type_object->publicly_queryable ) {
			return;
		}

		add_meta_box(
			'my-snippets',
			__( 'My Snippets', 'my-snippets' ),
			[ $this, 'displayMetaBox' ],
			$post_type,
			'normal',
			'low'
		);
	}

	/**
	 * Displays the "my snippets" meta box.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $object  Current post object.
	 * @param  array   $box
	 * @return void
	 */
	public function displayMetaBox( $post, $box ) {

		wp_nonce_field( plugin_basename( __FILE__ ), 'my_snippets_meta_nonce' );

		$snippet_title   = get_post_meta( $post->ID, 'Snippet Title', true );
		$snippet_content = get_post_meta( $post->ID, 'Snippet', true ); ?>

		<p>
			<label>
				<?php esc_html_e( 'Snippet Title', 'my-snippets' ); ?>
				<input class="widefat" type="text" name="snippet-title" value="<?php echo esc_attr( $snippet_title ); ?>" />
			</label>
		</p>

		<p>
			<label><?php esc_html_e( 'Snippet Content', 'my-snippets'); ?></label>
			<textarea class="widefat" name="snippet-content" id="snippet-content" cols="60" rows="4"><?php echo esc_textarea( $snippet_content ); ?></textarea>
			<span class="description"><?php _e( 'Add text, <abbr title="Hypertext Markup Language">HTML</abbr>, and/or shortcodes.', 'my-snippets' ); ?></span>
		</p><?php
	}

	/**
	 * Saves the custom post meta for the meta boxes.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  int     $post_id
	 * @param  object  $post
	 * @return void
	 */
	public function saveMeta( $post_id, $post ) {

		// Verify the nonce.
		if ( ! isset( $_POST['my_snippets_meta_nonce'] ) || ! wp_verify_nonce( $_POST['my_snippets_meta_nonce'], plugin_basename( __FILE__ ) ) ) {
			return;
		}

		// Get the post type object.
		$post_type = get_post_type_object( $post->post_type );

		// Check if the current user has permission to edit the post.
		if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
			return;
		}

		// Don't save if the post is only a revision.
		if ( 'revision' == $post->post_type ) {
			return;
		}

		$meta = [
			'Snippet Title' => strip_tags( wp_unslash( $_POST['snippet-title'] ) )
		];

		if ( current_user_can('unfiltered_html') ) {
			$meta['Snippet'] = wp_unslash( $_POST['snippet-content'] );
		} else {
			$meta['Snippet'] = wp_filter_post_kses( wp_unslash( $_POST['snippet-content'] ) );
		}

		foreach ( $meta as $meta_key => $new_meta_value ) {

			// Get the meta value of the custom field key.
			$meta_value = get_post_meta( $post_id, $meta_key, true );

			// If a new meta value was added and there was no previous value, add it.
			if ( $new_meta_value && '' == $meta_value ) {
				add_post_meta( $post_id, $meta_key, $new_meta_value, true );

			// If the new meta value does not match the old value, update it.
			} elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
				update_post_meta( $post_id, $meta_key, $new_meta_value );

			// If there is no new meta value but an old value exists, delete it.
			} elseif ( '' == $new_meta_value && $meta_value ) {
				delete_post_meta( $post_id, $meta_key, $meta_value );
			}
		}
	}
}
