<?php
/**
 * Plugin Name: WebDevStudios Widget Boilerplate
 * Plugin URI: http://webdevstudios.com
 * Description: A boilerplate for building both simple and complex widgets.
 * Author: WebDevStudios
 * Author URI: http://webdevstudios.com
 * Version: 1.0.0
 * License: GPLv2
 */

// Exit if accessed directly
if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

class WDS_Widget_Boilerplate extends WP_Widget {


	/**
	 * Unique identifier for this widget.
	 *
	 * Will also serve as the widget class.
	 *
	 * @var string
	 */
	protected $widget_slug = 'wds-widget-boilerplate-slug';

	/**
	 * Widget description displayed in Widgets dashboard.
	 * Set in __construct since __() shouldn't take a variable.
	 *
	 * @var string
	 */
	protected $widget_name = '';


	/**
	 * Contruct widget.
	 */
	public function __construct() {

		$this->widget_name = __( '(Client Name) Widget Boilerplate Title', 'wds-some-textdomain' );

		parent::__construct(
			$this->widget_slug,
			$this->widget_name,
			array(
				'classname'   => $this->widget_slug,
				'description' => __( 'A widget boilerplate description.', 'wds-some-textdomain' ),
			)
		);

		add_action( 'save_post',    array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}


	/**
	 * Delete this widget's cache.
	 *
	 * Note: Could also delete any transients
	 * delete_transient( 'some-transient-generated-by-this-widget' );
	 */
	public function flush_widget_cache() {
		wp_cache_delete( $this->widget_slug, 'widget' );
	}


	/**
	 * Front-end display of widget.
	 *
	 * @param  array  $args      The widget arguments set up when a sidebar is registered.
	 * @param  array  $instance  The widget settings as set by user.
	 */
	public function widget( $args, $instance ) {

		// Set up variables
		$before_widget = $args['before_widget'];
		$after_widget  = $args['after_widget'];
		$before_title  = $args['before_title'];
		$after_title   = $args['after_title'];
		$title         = $instance['title'];
		$text          = $instance['text'];

		// Before widget hook
		echo $before_widget;

		// Title
		echo ( $title ) ? $before_title . esc_html( $title ) . $after_title : '';

		?>

		<p><?php echo wp_kses_post( $text ); ?></p>

		<?php

		// After widget hook
		echo $after_widget;

	}


	/**
	 * Update form values as they are saved.
	 *
	 * @param  array  $new_instance  New settings for this instance as input by the user.
	 * @param  array  $old_instance  Old settings for this instance.
	 * @return array  Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {

		// Previously saved values
		$instance = $old_instance;

		// Sanitize title before saving to database
		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		// Sanitize text before saving to database
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['text'] = force_balance_tags( $new_instance['text'] );
		} else {
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes( $new_instance['text'] ) ) );
		}

		// Flush cache
		$this->flush_widget_cache();

		return $instance;
	}


	/**
	 * Back-end widget form with defaults.
	 *
	 * @param  array  $instance  Current settings.
	 */
	public function form( $instance ) {

		// If there are no settings, set up defaults
		$instance = wp_parse_args( (array) $instance,
			array(
				'title' => $this->widget_name,
				'text'  => '',
			)
		);

		// Set up variables
		$title = $instance['title'];
		$text  = $instance['text'];

		?>

		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'wds-some-textdomain' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_html( $title ); ?>" placeholder="optional" /></p>

		<p><label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( 'Text:', 'wds-some-textdomain' ); ?></label>
		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>"><?php echo esc_textarea( $text ); ?></textarea></p>
		<p class="description"><?php _e( 'Basic HTML tags are allowed.', 'wds-some-textdomain' ); ?></p>

		<?php
	}
}


/**
 * Register this widget with WordPress.
 */
function register_wds_widget_boilerplate() {
	register_widget( 'WDS_Widget_Boilerplate' );
}
add_action( 'widgets_init', 'register_wds_widget_boilerplate' );
