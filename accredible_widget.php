<?php // The widget class
class Accredible_Widget extends WP_Widget {
	// Main constructor
	public function __construct() {
		parent::__construct(
			'accredible_widget',
			__( 'Accredible Widget', 'text_domain' ),
			array(
				'customize_selective_refresh' => true,
			)
		);
	}
	// The widget form (for the backend )
	public function form( $instance ) {
		// Set widget defaults
		$defaults = array(
			'title'    => '',
			'text'     => '',
			'textarea' => '',
			'checkbox' => '',
			'select'   => '',
		);
		
		// Parse current settings with defaults
		extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>

		<?php // Widget Title ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Widget Title', 'text_domain' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<?php // Text Field ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php _e( 'Text:', 'text_domain' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" type="text" value="<?php echo esc_attr( $text ); ?>" />
		</p>

		<?php // Textarea Field ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'textarea' ) ); ?>"><?php _e( 'Textarea:', 'text_domain' ); ?></label>
			<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'textarea' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'textarea' ) ); ?>"><?php echo wp_kses_post( $textarea ); ?></textarea>
		</p>

		<?php // Checkbox ?>
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'checkbox' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'checkbox' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $checkbox ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'checkbox' ) ); ?>"><?php _e( 'Checkbox', 'text_domain' ); ?></label>
		</p>

		<?php // Dropdown ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'select' ); ?>"><?php _e( 'Select', 'text_domain' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'select' ); ?>" id="<?php echo $this->get_field_id( 'select' ); ?>" class="widefat">
			<?php
			// Your options array
			$options = array(
				''        => __( 'Select', 'text_domain' ),
				'option_1' => __( 'Option 1', 'text_domain' ),
				'option_2' => __( 'Option 2', 'text_domain' ),
				'option_3' => __( 'Option 3', 'text_domain' ),
			);
			// Loop through options and add each one to the select dropdown
			foreach ( $options as $key => $name ) {
				echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" '. selected( $select, $key, false ) . '>'. $name . '</option>';
			} ?>
			</select>
		</p>

	<?php }
	// Update widget settings
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']    = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['text']     = isset( $new_instance['text'] ) ? wp_strip_all_tags( $new_instance['text'] ) : '';
		$instance['textarea'] = isset( $new_instance['textarea'] ) ? wp_kses_post( $new_instance['textarea'] ) : '';
		$instance['checkbox'] = isset( $new_instance['checkbox'] ) ? 1 : false;
		$instance['select']   = isset( $new_instance['select'] ) ? wp_strip_all_tags( $new_instance['select'] ) : '';
		return $instance;
	}
	// Display the widget
	public function widget( $args, $instance ) {
		extract( $args );
		// Check the widget options
		$title    = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
		$text     = isset( $instance['text'] ) ? $instance['text'] : '';
		$textarea = isset( $instance['textarea'] ) ?$instance['textarea'] : '';
		$select   = isset( $instance['select'] ) ? $instance['select'] : '';
		$checkbox = ! empty( $instance['checkbox'] ) ? $instance['checkbox'] : false;
		// WordPress core before_widget hook (always include )
		echo $before_widget;
		// Display the widget
		echo '<div class="widget-text wp_widget_plugin_box">';
			// Display widget title if defined
			if ( $title ) {
				echo $before_title . $title . $after_title;
			}
			// Display text field
			if ( $text ) {
				echo '<p>' . $text . '</p>';
			}
			// Display textarea field
			if ( $textarea ) {
				echo '<p>' . $textarea . '</p>';
			}
			// Display select field
			if ( $select ) {
				echo '<p>' . $select . '</p>';
			}
			// Display something if checkbox is true
			if ( $checkbox ) {
				echo '<p>Something awesome</p>';
			}
		echo '</div>';
		// WordPress core after_widget hook (always include )
		echo $after_widget;
	}
}
// Register the widget
function register_accredible_custom_widget() {
	register_widget( 'Accredible_Widget' );
}
add_action( 'widgets_init', 'register_accredible_custom_widget' );


// [accredible_credential image="true" limit="10" style="true"]
function accredible_credential_shortcode($atts = [], $content = null, $tag = ''){

	// normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
 
    // override default attributes with user attributes
    $atts_to_consume = shortcode_atts([
                                     'image' => 'true',
                                     'limit' => '10',
                                     'style' => 'true'
                                 ], $atts, $tag);

	$current_user = wp_get_current_user();
	
	if (0 == $current_user->ID) {
	    // We don't have a user to get credentials for.
	} else {
		$accredible = new Accredible_Certificate;
		$credentials = $accredible->get_credentials_for_email($current_user->user_email);
		if($credentials->credentials){
			foreach ($credentials->credentials as $key => $credential) {

				// The user can set a limit on the number of credentials displayed
				if($key >= (int)$atts_to_consume['limit']){
					break;
				}

				// The user can choose between image or link.
				if($atts_to_consume['image'] == "false"){
					echo '<a href="' . $credential->url . '" target="_blank">' . $credential->name . '</a>';
				} else {
					// The user can choose to remove the default styling
					if($atts_to_consume['style'] == "false"){
						echo '<div class="accredible_credential">';
					} else {
						echo '<div style="width: 300px; height: 200px; margin: 0 30px 30px 0; text-align: center; display: inline-block;" class="accredible_credential">';
					}
					echo '<a href="' . $credential->url . '">';
					echo '<img src="' . $credential->seo_image . '" style="max-width:100%; max-height:100%; margin: 0 auto;">';
					echo '</a>';
					echo '</div>';
				}
			}
		}
	}
}

add_shortcode( 'accredible_credential', 'accredible_credential_shortcode');
?>