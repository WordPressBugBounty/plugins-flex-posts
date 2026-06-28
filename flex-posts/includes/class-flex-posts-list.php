<?php
/**
 * Flex Posts List Widget
 *
 * @package Flex Posts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Flex Posts List widget class
 */
class Flex_Posts_List extends Flex_Posts_Widget {

	/**
	 * Set base ID, name & options
	 */
	public function __construct() {
		parent::__construct(
			'flex-posts-list',
			esc_html__( 'Flex Posts', 'flex-posts' ),
			array(
				'description' => esc_html__( 'Displays posts list.', 'flex-posts' ),
			)
		);

		$this->enqueue();
	}

	/**
	 * Get form fields
	 */
	public function get_fields() {
		/**
		 * Filter: flex_posts_list_fields
		 *
		 * Filter the form fields definition for the list widget. Callback
		 * receives the default parent fields and should return an array of
		 * field definitions.
		 *
		 * @param array $fields Default fields from parent::get_fields().
		 * @return array Modified fields.
		 *
		 * Example:
		 * add_filter( 'flex_posts_list_fields', function( $fields ) {
		 *     $fields['custom'] = array( 'type' => 'text', 'label' => 'Custom' );
		 *     return $fields;
		 * } );
		 */
		return apply_filters( 'flex_posts_list_fields', parent::get_fields() );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @param array $instance Saved values from database.
	 */
	public function front( $instance ) {
		flex_posts_display( $instance );
	}
}
