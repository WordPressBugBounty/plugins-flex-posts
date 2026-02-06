<?php
/**
 * Flex Posts List Block
 *
 * @package Flex Posts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register block
 */
function flex_posts_register_block() {
	$option = get_option( 'flex_posts' );

	wp_register_script(
		'flex-posts',
		plugins_url( 'block.js', __FILE__ ),
		array( 'wp-blocks', 'wp-element', 'wp-i18n' ),
		FLEX_POSTS_VERSION,
		true
	);

	$categories[] = array(
		'label' => __( 'All Categories', 'flex-posts' ),
		'value' => '',
	);

	$cats = get_categories();

	foreach ( $cats as $cat ) {
		$categories[] = array(
			'label' => $cat->name,
			'value' => $cat->term_id,
		);
	}

	$attributes = apply_filters( 'flex_posts_attributes', flex_posts_get_attributes() );

	$attributes2 = $attributes;
	unset( $attributes2['align'] );
	unset( $attributes2['className'] );

	wp_localize_script(
		'flex-posts',
		'flex_posts',
		array(
			'categories'  => $categories,
			'post_types'  => flex_posts_get_post_types(),
			'layouts'     => flex_posts_get_layouts(),
			'order_by'    => flex_posts_get_order_by(),
			'image_sizes' => flex_posts_get_image_sizes(),
			'taxonomies'  => flex_posts_get_taxonomies(),
			'title_el'    => flex_posts_get_title_elements_options(),
			'attributes'  => $attributes2,
		)
	);

	$args = array(
		'editor_script'   => 'flex-posts',
		'render_callback' => 'flex_posts_render_block',
		'attributes'      => $attributes,
	);

	if ( empty( $option['disable_css'] ) ) {
		$args['style']        = 'flex-posts';
		$args['editor_style'] = 'flex-posts';
	}

	register_block_type( 'flex-posts/list', $args );
}
add_action( 'init', 'flex_posts_register_block' );

/**
 * Render block
 *
 * @param  array $attributes Attributes.
 * @return string
 */
function flex_posts_render_block( $attributes ) {
	return flex_posts_render( $attributes );
}
