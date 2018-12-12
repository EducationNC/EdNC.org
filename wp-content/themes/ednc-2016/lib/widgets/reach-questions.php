<?php

namespace Roots\Sage\Widgets;

class Reach_Questions extends \WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
    parent::__construct(
			'reach_questions', // Base ID
			__( 'Reach Questions', 'sage' ), // Name
			array( 'description' => __( 'Displays grid of most recent polls', 'sage' ), ) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title']); 
		$msg = apply_filters( 'widget_title', $instance['msg']); 
		$bg_url = apply_filters( 'widget_title', $instance['bg_url']); 
		$before_widget = $args['before_widget'];
		$after_widget = $args['after_widget'];

		echo $before_widget;
		
		include(locate_template('templates/widgets/reach-questions.php'));
		
		echo $after_widget;
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) || isset( $instance[ 'msg' ] ) || isset( $instance[ 'bg_url' ] ) ) {
			$title = $instance[ 'title' ];
			$msg = $instance[ 'msg' ];
			$bg_url = $instance[ 'bg_url' ];
		} else {
			$title = __( 'New title', 'reach_questions' );
			$msg = __( 'New Message', 'reach_questions' );
			$bg_url = __( 'New Background Url', 'reach_questions' );
		}
			// Widget admin form
		?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'msg' ); ?>"><?php _e( 'Message:' ); ?></label> 
				<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id( 'msg' ); ?>" name="<?php echo $this->get_field_name( 'msg' ); ?>" ><?php echo esc_attr( $msg ); ?></textarea>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'bg_url' ); ?>"><?php _e( 'Background Url:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'bg_url' ); ?>" name="<?php echo $this->get_field_name( 'bg_url' ); ?>" type="text" value="<?php echo esc_attr( $bg_url ); ?>" />
			</p>
		<?php 
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['msg'] = ( ! empty( $new_instance['msg'] ) ) ? strip_tags( $new_instance['msg'] ) : '';
		$instance['bg_url'] = ( ! empty( $new_instance['bg_url'] ) ) ? strip_tags( $new_instance['bg_url'] ) : '';
		return $instance;
	}
}