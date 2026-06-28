<?php
/**
 * Data/options getters used by the widget admin UI and blocks
 *
 * @package Flex Posts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get number of layouts
 *
 * @return int
 */
function flex_posts_get_layouts() {
	/**
	 * Filter: flex_posts_layouts
	 *
	 * Filter the number of available layouts. Return an integer.
	 *
	 * @param int $count Default number of layouts.
	 * @return int Modified number of layouts.
	 */
	return apply_filters( 'flex_posts_layouts', 5 );
}

/**
 * Get layout SVG icons passed from PHP filter.
 *
 * Default SVGs are defined in JavaScript (blocks/list/block.js).
 * This function returns only additional/override SVGs from the PHP filter.
 *
 * @return array
 */
function flex_posts_get_layout_svgs() {
	/**
	 * Filter: flex_posts_layout_svgs
	 *
	 * Add or override layout SVG icons. Default SVGs are defined in JavaScript
	 * (blocks/list/block.js). Use this filter to add new layouts or override
	 * existing SVGs. PHP-provided SVGs take precedence over JS defaults.
	 *
	 * @param array $svgs Empty array by default. Add SVGs to override or extend.
	 * @return array SVG icons to merge with JS defaults.
	 *
	 * Example:
	 * add_filter( 'flex_posts_layout_svgs', function( $svgs ) {
	 *     // Add custom layout SVG for a new layout 7
	 *     $svgs[7] = '<svg viewBox="0 0 80 50" xmlns="http://www.w3.org/2000/svg">...</svg>';
	 *     // Override existing layout 1 SVG
	 *     $svgs[1] = '<svg viewBox="0 0 80 50" xmlns="http://www.w3.org/2000/svg">...</svg>';
	 *     return $svgs;
	 * } );
	 */
	return apply_filters( 'flex_posts_layout_svgs', array() );
}

/**
 * Get post types
 *
 * @param  string $block Block.
 * @return array
 */
function flex_posts_get_post_types( $block = true ) {
	$post_types['post'] = __( 'Post', 'flex-posts' );
	$post_types['page'] = __( 'Page', 'flex-posts' );

	$get_post_types = get_post_types(
		array(
			'public'   => true,
			'_builtin' => false,
		),
		'objects'
	);

	foreach ( $get_post_types as $post_type ) {
		$post_types[ $post_type->name ] = $post_type->labels->singular_name;
	}

	$post_types['any'] = __( 'Any', 'flex-posts' );

	if ( ! $block ) {
		return $post_types;
	}

	foreach ( $post_types as $value => $label ) {
		$post_types2[] = array(
			'label' => $label,
			'value' => $value,
		);
	}
	return $post_types2;
}

/**
 * Get taxonomies
 *
 * @return array
 */
function flex_posts_get_taxonomies() {
	$post_types = get_post_types(
		array(
			'public'   => true,
			'_builtin' => false,
		),
		'names'
	);

	$taxonomies['post'] = get_object_taxonomies( 'post' );
	$taxonomies['page'] = get_object_taxonomies( 'page' );
	foreach ( $post_types as $post_type ) {
		$taxonomies[ $post_type ] = get_object_taxonomies( $post_type );
	}
	return $taxonomies;
}

/**
 * Get order by data
 *
 * @param  string $block Block.
 * @return array
 */
function flex_posts_get_order_by( $block = true ) {
	/**
	 * Filter: flex_posts_order_by
	 *
	 * Filter the list of "order by" options available in the widget/block
	 * and admin UI. Expected to return an associative array mapping keys
	 * to labels.
	 *
	 * @param array $options Default order-by options.
	 * @return array Modified options.
	 */
	$data = apply_filters(
		'flex_posts_order_by',
		array(
			'newest'     => esc_html__( 'Newest', 'flex-posts' ),
			'oldest'     => esc_html__( 'Oldest', 'flex-posts' ),
			'comments'   => esc_html__( 'Most commented', 'flex-posts' ),
			'title'      => esc_html__( 'Alphabetical', 'flex-posts' ),
			'random'     => esc_html__( 'Random', 'flex-posts' ),
			'modified'   => esc_html__( 'Modified date', 'flex-posts' ),
			'menu_order' => esc_html__( 'Page order', 'flex-posts' ),
		)
	);

	if ( ! $block ) {
		return $data;
	}

	$data2 = array();
	foreach ( $data as $value => $label ) {
		$data2[] = array(
			'label' => $label,
			'value' => $value,
		);
	}
	return $data2;
}

/**
 * Get image sizes data
 *
 * @param  string $block Block.
 * @return array
 */
function flex_posts_get_image_sizes( $block = true ) {
	$image_sizes[''] = __( 'Default', 'flex-posts' );

	foreach ( get_intermediate_image_sizes() as $size ) {
		$image_sizes[ $size ] = $size;
	}

	if ( ! $block ) {
		return $image_sizes;
	}

	$image_sizes2 = array();
	foreach ( $image_sizes as $value => $label ) {
		$image_sizes2[] = array(
			'label' => $label,
			'value' => $value,
		);
	}
	return $image_sizes2;
}

/**
 * Get html elements allowed for title
 *
 * @return array
 */
function flex_posts_get_title_elements() {
	/**
	 * Filter: flex_posts_allowed_title_elements
	 *
	 * Filter the set of allowed HTML elements that can be used for titles.
	 * Expected to return an indexed array of element tags.
	 *
	 * @param array $elements Default allowed elements.
	 * @return array Modified elements.
	 */
	$elements = apply_filters(
		'flex_posts_allowed_title_elements',
		array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'p', 'span' )
	);
	return $elements;
}

/**
 * Get title elements options
 *
 * @param  string $block Block.
 * @return array
 */
function flex_posts_get_title_elements_options( $block = true ) {
	$options[''] = __( 'Default', 'flex-posts' );

	foreach ( flex_posts_get_title_elements() as $el ) {
		$options[ $el ] = $el;
	}

	if ( ! $block ) {
		return $options;
	}

	$options2 = array();
	foreach ( $options as $value => $label ) {
		$options2[] = array(
			'label' => $label,
			'value' => $value,
		);
	}
	return $options2;
}

/**
 * Get the block category slug used when registering Flex Posts blocks.
 *
 * Defaults to the core "widgets" category. Flex Posts Pro hooks the
 * `flex_posts_block_category` filter to move every Flex Posts block into a
 * dedicated category when the add-on is active.
 *
 * @return string Block category slug.
 */
function flex_posts_get_block_category() {
	/**
	 * Filter: flex_posts_block_category
	 *
	 * @param string $category Block category slug. Default 'widgets'.
	 * @return string
	 */
	return apply_filters( 'flex_posts_block_category', 'widgets' );
}

/**
 * Get block attributes
 *
 * @param string $type Block type.
 * @return array
 */
function flex_posts_get_attributes( $type = '' ) {
	$attributes = array(
		'title'           => array(
			'type'    => 'string',
			'default' => '',
		),
		'title_url'       => array(
			'type'    => 'string',
			'default' => '',
		),
		'title_cat'       => array(
			'type'    => 'boolean',
			'default' => false,
		),
		'title_url_cat'   => array(
			'type'    => 'boolean',
			'default' => false,
		),
		'layout'          => array(
			'type'    => 'number',
			'default' => 1,
		),
		'post_type'       => array(
			'type'    => 'string',
			'default' => 'post',
		),
		'cat'             => array(
			'type'    => 'string',
			'default' => '',
		),
		'tag'             => array(
			'type'    => 'string',
			'default' => '',
		),
		'order_by'        => array(
			'type'    => 'string',
			'default' => 'newest',
		),
		'number'          => array(
			'type'    => 'number',
			'default' => 4,
		),
		'skip'            => array(
			'type'    => 'number',
			'default' => 0,
		),
		'exclude_current' => array(
			'type'    => 'boolean',
			'default' => false,
		),
		'show_image'      => array(
			'type'    => 'string',
			'default' => 'all',
		),
		'image_size'      => array(
			'type'    => 'string',
			'default' => '',
		),
		'image_size2'     => array(
			'type'    => 'string',
			'default' => '',
		),
		'show_title'      => array(
			'type'    => 'boolean',
			'default' => true,
		),
		'show_categories' => array(
			'type'    => 'boolean',
			'default' => false,
		),
		'show_author'     => array(
			'type'    => 'boolean',
			'default' => false,
		),
		'show_avatar'     => array(
			'type'    => 'boolean',
			'default' => false,
		),
		'show_date'       => array(
			'type'    => 'boolean',
			'default' => true,
		),
		'show_comments'   => array(
			'type'    => 'boolean',
			'default' => true,
		),
		'show_excerpt'    => array(
			'type'    => 'boolean',
			'default' => false,
		),
		'excerpt_length'  => array(
			'type'    => 'number',
			'default' => 15,
		),
		'show_readmore'   => array(
			'type'    => 'boolean',
			'default' => false,
		),
		'readmore_text'   => array(
			'type'    => 'string',
			'default' => '',
		),
		'pagination'      => array(
			'type'    => 'boolean',
			'default' => false,
		),
		'align'           => array(
			'type'    => 'string',
			'default' => '',
		),
		'block_title_el'  => array(
			'type'    => 'string',
			'default' => '',
		),
		'post_title_el'   => array(
			'type'    => 'string',
			'default' => '',
		),
		'className'       => array(
			'type'    => 'string',
			'default' => '',
		),
	);

	/**
	 * Filter: flex_posts_attributes, flex_posts_{type}_attributes
	 *
	 * Filter the block attributes used when registering the block. Expected
	 * to return an associative array conforming to block attribute definitions.
	 *
	 * @param array $attributes Default attributes array.
	 * @return array Modified attributes array.
	 *
	 * Example:
	 * add_filter( 'flex_posts_attributes', function( $attrs ) {
	 *     $attrs['custom'] = array( 'type' => 'string', 'default' => '' );
	 *     return $attrs;
	 * } );
	 */
	if ( empty( $type ) ) {
		$attributes = apply_filters( 'flex_posts_attributes', $attributes );
	} else {
		$attributes = apply_filters( 'flex_posts_' . $type . '_attributes', $attributes );
	}
	return $attributes;
}
