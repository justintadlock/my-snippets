<?php
/**
 * Snippet widget.
 *
 * @package   MySnippets
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2009 - 2019, Justin Tadlock
 * @link      http://themehybrid.com/plugins/my-snippets
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace MySnippets\Widgets;

use WP_Widget;

/**
 * Widget class.
 *
 * @since  1.0.0
 * @access public
 */
class Snippet extends WP_Widget {

	/**
	 * Set up the widget's name, ID, class, description, and other options.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		// Set up the widget options.
		$widget_options = [
			'classname'                   => 'snippet',
			'description'                 => __( 'Displays custom post snippets on a post-by-post basis.', 'my-snippets' ),
			'customize_selective_refresh' => true
		];

		// Create the widget.
		parent::__construct( 'snippet', __( 'My Snippets', 'my-snippets' ), $widget_options );

		// Apply filters to the snippet content.
		add_filter( 'my_snippets_content', 'wptexturize'       );
		add_filter( 'my_snippets_content', 'convert_smilies'   );
		add_filter( 'my_snippets_content', 'convert_chars'     );
		add_filter( 'my_snippets_content', 'wpautop'           );
		add_filter( 'my_snippets_content', 'shortcode_unautop' );
		add_filter( 'my_snippets_content', 'do_shortcode'      );
	}

	/**
	 * Outputs the widget.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function widget( $sidebar, $instance ) {

		// Bail if not on a single post.
		if ( ! is_singular() ) {
			return;
		}

		// Get the current post ID.
		$post_id = get_queried_object_id();

		// Get the snippet content and title.
		$snippet_content = get_post_meta( $post_id, 'Snippet',       true );
		$snippet_title   = get_post_meta( $post_id, 'Snippet Title', true );

		// If there's no snippet content, bail.
		if ( empty( $snippet_content ) ) {
			return;
		}

		// If there's a custom snippet title, use it. Otherwise, default to the widget title.
		$instance['title'] = ! empty( $snippet_title ) ? $snippet_title : $instance['title'];

		// Output the theme's widget wrapper.
		echo $sidebar['before_widget'];

		// If a title was input by the user, display it.
		if ( !empty( $instance['title'] ) ) {
			echo $sidebar['before_title'] . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $sidebar['after_title'];
		}

		// Output the snippet.
		printf(
			'<div class="snippet-content">%s</div>',
			apply_filters( 'my_snippets_content', $snippet_content )
		);

		// Close the theme's widget wrapper. */
		echo $sidebar['after_widget'];
	}

	/**
	 * Updates the widget control options.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array  $new_instance
	 * @param  array  $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function form( $instance ) {

		// Set up the default form values.
		$defaults = [
			'title' => __( 'Snippet', 'my-snippets' )
		];

		// Merge the user-selected arguments with the defaults.
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label>
				<?php esc_html_e('Default Title:', 'my-snippets'); ?>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
			</label>
		</p>
	<?php }
}
