<?php

if ( realpath( __FILE__ ) === realpath( $_SERVER["SCRIPT_FILENAME"] ) ) {
	header("HTTP/1.0 404 Not Found");
	header("Status: 404 Not Found");
	exit( 'Do not access this file directly.' );
}

/** 
* Class for widget.
* 
* @since 1.0.0
* @package simple_pw_ads
*/
class spw_widget extends WP_Widget {
	
	/* constructor */
	public function spw_widget() {
		$widget_ops = array( 'classname'=>'spw_widget', 'description'=>'Display a Project Wonderful ad' );
		
		$this->WP_Widget( 'spw_widget', 'Simple PW Ad', $widget_ops );
	}
	
	/* widget output */
	public function widget( $args, $instance ) {
		global $spw_ads_instance;
		extract( $args );
			
		$title = apply_filters( 'widget_title', $instance['title'] );
		$ad = $instance['managed_ad'];
			
		echo $before_widget;
			
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		
		if ( empty( $ad ) ) {
			echo '<!--' . esc_html__( 'Simple PW widget set to display nothing', 'spw_ads' ) . '-->';
		} else {
			echo $spw_ads_instance->spw_ad_func( $ad );
		}
			
		echo $after_widget;
	}
		
	/* widget options update */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
			
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['managed_ad'] = ( isset( $new_instance['managed_ad'] ) && strlen( $new_instance['managed_ad'] ) == 1 && is_numeric( $new_instance['managed_ad'] ) ) ? $new_instance['managed_ad'] : '';
		
		return $instance;
	}
		
	/* widget control form */
	public function form( $instance ) {
		$defaults = array( 'title' => '', 'managed_ad' => '');
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = $instance['title'];
		$managed_ad = $instance['managed_ad'];
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title', 'spw_ads' ); ?>:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" type="text" style="width:100%;" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'managed_ad' ); ?>"><?php esc_html_e( 'Your ad:', 'spw_ads' ); ?>:</label><br />
		<?php 
		$options = get_option( 'saved_spw_ads' );
		if ( !empty( $options['ads'] ) && count( $options['ads'] ) > 0 ) { 
		?>
			<select name="<?php echo $this->get_field_name( 'managed_ad' ); ?>">
				<option value="" <?php selected( $managed_ad, '' ); ?>>Show nothing.</option>
				<?php foreach( $options['ads'] as $count => $option ) { ?>
				<?php $ad_number = ( isset( $option['ad_number'] ) ) ? $option['ad_number'] : $count; ?>
				<option value="<?php echo $ad_number; ?>" <?php selected( $managed_ad, $ad_number ); ?>"><?php echo 'Ad ' . $ad_number . ' - ' . $option['ad'] . ' - ' . $option['type']; ?></option>
				<?php } ?>
			</select>
		<?php } else { ?>
			<span><a href="<?php echo admin_url( 'options-general.php?page=simple_pw_ads' ); ?>"><?php esc_html_e( 'No ads. Please see the management page.', 'spw_ads' ); ?></a></span>
		<?php } ?>
		</p>
		<?php
	}
}