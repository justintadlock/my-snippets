<?php
/**
 * My Snippets Widget Class
 *
 * The My Snippets Widget displays custom post metadata that is input on singular posts (pages,
 * other post types, etc.). The widget only displays if metadata is available.  Otherwise, it displays
 * nothing.  The Default Title field only displays in the instance that no My Snippets Title is entered.
 *
 * @package   MySnippets
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2009 - 2013, Justin Tadlock
 * @link      http://themehybrid.com/plugins/my-snippets
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class My_Snippets_Widget_Snippet extends WP_Widget {

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 *
	 * @since  0.2.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		/* Set up the widget options. */
		$widget_options = array(
			'classname'   => 'snippet',
			'description' => esc_html__( 'Displays custom post snippets on a post-by-post basis.', 'my-snippets' )
		);

		/* Set up the widget control options. */
		$control_options = array(
			'width'  => 200,
			'height' => 350
		);

		/* Create the widget. */
		$this->WP_Widget(
			'snippet',                          // $this->id_base
			__( 'My Snippets', 'my-snippets' ), // $this->name
			$widget_options,                    // $this->widget_options
			$control_options                    // $this->control_options
		);

		/* Apply filters to the snippet content. */
		add_filter( 'my_snippets_content', 'wptexturize' );
		add_filter( 'my_snippets_content', 'convert_smilies' );
		add_filter( 'my_snippets_content', 'convert_chars' );
		add_filter( 'my_snippets_content', 'wpautop' );
		add_filter( 'my_snippets_content', 'shortcode_unautop' );
		add_filter( 'my_snippets_content', 'do_shortcode' );
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function widget( $sidebar, $instance ) {
		extract( $sidebar );

		/* If not viewing a single post, bail. */
		if ( !is_singular() )
			return;

		/* Get the current post ID. */
		$post_id = get_queried_object_id();

		/* Get the snippet content and title. */
		$snippet_content = get_post_meta( $post_id, 'Snippet',       true );
		$snippet_title   = get_post_meta( $post_id, 'Snippet Title', true );

		/* If there's no snippet content, bail. */
		if ( empty( $snippet_content ) )
			return false;

		/* If there's a custom snippet title, use it. Otherwise, default to the widget title. */
		$instance['title'] = !empty( $snippet_title ) ? $snippet_title : $instance['title'];

		/* Output the theme's widget wrapper. */
		echo $before_widget;

		/* If a title was input by the user, display it. */
		if ( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		/* Output the snippet. */
		printf( 
			'<div class="snippet-content">%s</div>', 
			apply_filters( 'my_snippets_content', $snippet_content )
		);

		/* Close the theme's widget wrapper. */
		echo $after_widget;
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 *
	 * @since  0.1.0
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
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function form( $instance ) {

		/* Set up the default form values. */
		$defaults = array(
			'title' => __('Snippet', 'my-snippets')
		);

		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Default Title:', 'my-snippets'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
	<?php
	}
}

?>