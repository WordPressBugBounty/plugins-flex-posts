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
		'flex-posts-helpers',
		FLEX_POSTS_URL . 'blocks/helpers.js',
		array(
			'wp-blocks',
			'wp-block-editor',
			'wp-components',
			'wp-data',
			'wp-element',
			'wp-i18n',
			'wp-server-side-render',
		),
		FLEX_POSTS_VERSION,
		true
	);

	wp_register_script(
		'flex-posts',
		plugins_url( 'block.js', __FILE__ ),
		array(
			'wp-i18n',
			'flex-posts-helpers',
		),
		FLEX_POSTS_VERSION,
		true
	);

	$attributes = flex_posts_get_attributes();

	wp_localize_script(
		'flex-posts',
		'flex_posts',
		array(
			'layouts'     => flex_posts_get_layouts(),
			'layout_svgs' => flex_posts_get_layout_svgs(),
			'order_by'    => flex_posts_get_order_by(),
			'image_sizes' => flex_posts_get_image_sizes(),
			'title_el'    => flex_posts_get_title_elements_options(),
			'attributes'  => $attributes,
			'category'    => flex_posts_get_block_category(),
		)
	);

	$args = array(
		'editor_script'   => 'flex-posts',
		'render_callback' => 'flex_posts_render_block',
		'attributes'      => $attributes,
		'api_version'     => 3,
	);

	if ( empty( $option['disable_css'] ) ) {
		$args['style'] = 'flex-posts';
	}

	// Register admin CSS for block editor.
	wp_register_style(
		'flex-posts-block-editor',
		FLEX_POSTS_URL . 'admin/css/block-editor.css',
		array(),
		FLEX_POSTS_VERSION
	);

	$args['editor_style'] = 'flex-posts-block-editor';

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

/**
 * Prevent custom attributes for ServerSideRender
 *
 * @param mixed  $result  Response to replace the requested version with.
 * @param object $server  Server instance.
 * @param object $request Request used to generate the response.
 * @return array
 */
function flex_posts_prevent_custom_attributes( $result, $server, $request ) {
	$route = $request->get_route();

	if ( strpos( $route, '/block-renderer/flex-posts/list' ) !== false ) {
		$attributes = $request->get_param( 'attributes' );

		if ( is_array( $attributes ) ) {
			$defined_attributes = flex_posts_get_attributes();
			$request->set_param( 'attributes', array_intersect_key( $attributes, $defined_attributes ) );
		}
	}

	return $result;
}
add_filter( 'rest_pre_dispatch', 'flex_posts_prevent_custom_attributes', 10, 3 );
