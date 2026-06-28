<?php
/**
 * Front-end rendering: style registration, query building, and output
 *
 * @package Flex Posts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register style sheet
 */
function flex_posts_register_style() {
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_register_style(
		'flex-posts',
		FLEX_POSTS_URL . 'public/css/flex-posts' . $suffix . '.css',
		array(),
		FLEX_POSTS_VERSION
	);
}
add_action( 'init', 'flex_posts_register_style' );

/**
 * Get args for WP Query
 *
 * @param  array $instance Attributes.
 * @return array Args
 */
function flex_posts_get_query_args( $instance ) {
	$args['ignore_sticky_posts'] = true;
	$args['post_status']         = 'publish';
	$args['orderby']             = 'date';
	$args['order']               = 'desc';

	if ( ! empty( $instance['order_by'] ) ) {
		switch ( $instance['order_by'] ) {
			case 'oldest':
				$args['order'] = 'asc';
				break;
			case 'title':
				$args['orderby'] = 'title';
				$args['order']   = 'asc';
				break;
			case 'comments':
				$args['orderby'] = 'comment_count';
				break;
			case 'random':
				$args['orderby'] = 'rand';
				break;
			case 'modified':
				$args['orderby'] = 'modified';
				break;
			case 'menu_order':
				$args['orderby'] = 'menu_order';
				$args['order']   = 'asc';
				break;
		}
	}

	if ( ! empty( $instance['post_type'] ) ) {
		$args['post_type'] = $instance['post_type'];
	} else {
		$args['post_type'] = 'post';
	}

	if ( ! empty( $instance['cat'] ) ) {
		$args['cat'] = absint( $instance['cat'] );
	}

	if ( ! empty( $instance['tag'] ) ) {
		$tags = explode( ',', sanitize_text_field( wp_unslash( $instance['tag'] ) ) );

		$include_tag_id = array();
		$exclude_tag_id = array();
		foreach ( $tags as $tag ) {
			$tag = trim( $tag );
			if ( strpos( $tag, '-' ) === 0 ) {
				$tag  = substr( $tag, 1 );
				$term = get_term_by( 'slug', $tag, 'post_tag' );
				if ( ! empty( $term ) ) {
					$exclude_tag_id[] = $term->term_id;
				}
			} else {
				$term = get_term_by( 'slug', $tag, 'post_tag' );
				if ( ! empty( $term ) ) {
					$include_tag_id[] = $term->term_id;
				}
			}
		}

		if ( ! empty( $include_tag_id ) ) {
			$args['tag__in'] = $include_tag_id;
		}

		if ( ! empty( $exclude_tag_id ) ) {
			$args['tag__not_in'] = $exclude_tag_id;
		}
	}

	if ( isset( $instance['number'] ) ) {
		$args['posts_per_page'] = intval( $instance['number'] );
	}

	if ( ! empty( $instance['skip'] ) ) {
		$args['offset'] = absint( $instance['skip'] );
		if ( 'rand' === $args['orderby'] ) {
			// Make offset and order by random working together.
			$args2 = $args;
			unset( $args2['offset'] );

			$args2['posts_per_page'] = $args['offset'];
			$args2['orderby']        = 'date';
			$args2['order']          = 'desc';
			$args2['fields']         = 'ids';

			$query2 = new WP_Query( $args2 );

			if ( ! empty( $query2->posts ) ) {
				// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in -- Exclusion set is bounded by the small "skip" count, not an unbounded list.
				$args['post__not_in'] = $query2->posts;
				unset( $args['offset'] );
			}
		}
	}

	if ( ! empty( $instance['exclude_current'] ) ) {
		$args['post__not_in'][] = get_the_ID();
	}

	if ( empty( $instance['pagination'] ) ) {
		$args['no_found_rows'] = true;
	} else {
		$args['paged'] = flex_posts_get_current_page();
		if ( ! empty( $args['offset'] ) ) {
			// Modify offset value if pagination is active.
			$old_offset         = $args['offset'];
			$new_offset         = $old_offset + ( ( $args['paged'] - 1 ) * $args['posts_per_page'] );
			$args['offset']     = $new_offset;
			$args['old_offset'] = $old_offset;
		}
	}

	return $args;
}

/**
 * Front-end display of widget.
 *
 * @param array  $instance Attributes.
 * @param string $type     Widget type.
 */
function flex_posts_display( $instance, $type = 'list' ) {
	/**
	 * Filter: flex_posts_{type}_args
	 *
	 * Dynamic filter applied to the WP_Query args before querying posts for
	 * a specific type (widget/block). Replace {type} with the actual type
	 * used when adding a callback (e.g. 'list').
	 *
	 * @param array $args     WP_Query args.
	 * @param array $instance Widget/block instance or attributes.
	 * @return array Modified WP_Query args.
	 *
	 * Example:
	 * add_filter( 'flex_posts_list_args', function( $args, $instance ) {
	 *     $args['post_status'] = 'private';
	 *     return $args;
	 * }, 10, 2 );
	 */
	$args = apply_filters( 'flex_posts_' . $type . '_args', flex_posts_get_query_args( $instance ), $instance );

	$query = new WP_Query( $args );

	$layout = 1;
	if ( ! empty( $instance['layout'] ) ) {
		$layout = absint( $instance['layout'] );
	}

	/**
	 * Filter: flex_posts_thumbnail_size
	 *
	 * Filter the thumbnail image size used for small thumbnails.
	 *
	 * @param string $size    Default image size slug.
	 * @param array  $context Instance/attributes that requested the size.
	 * @return string Image size slug.
	 *
	 * Example:
	 * add_filter( 'flex_posts_thumbnail_size', function( $size, $context ) {
	 *     return 'medium';
	 * }, 10, 2 );
	 */
	$thumbnail_size = apply_filters( 'flex_posts_thumbnail_size', 'thumbnail', $instance );

	/**
	 * Filter: flex_posts_medium_size
	 *
	 * Filter the medium image size used for larger thumbnails.
	 *
	 * @param string $size    Default image size slug.
	 * @param array  $context Instance/attributes that requested the size.
	 * @return string Image size slug.
	 */
	$medium_size = apply_filters( 'flex_posts_medium_size', '400x250-crop', $instance );

	if ( ! empty( $instance['image_size'] ) ) {
		$thumbnail_size = $instance['image_size'];
	}

	if ( ! empty( $instance['image_size2'] ) ) {
		$medium_size = $instance['image_size2'];
	}

	if ( ! isset( $instance['show_image'] ) ) {
		$instance['show_image'] = 'all';
	}

	if ( ! isset( $instance['show_title'] ) ) {
		$instance['show_title'] = true;
	}

	$excerpt_length = 15;
	if ( ! empty( $instance['excerpt_length'] ) ) {
		$excerpt_length = absint( $instance['excerpt_length'] );
	}

	$readmore_text = __( 'Read more', 'flex-posts' );
	if ( ! empty( $instance['readmore_text'] ) ) {
		$readmore_text = $instance['readmore_text'];
	}

	$file = "flex-posts-{$type}-{$layout}.php";

	/**
	 * Filter: flex_posts_template_directory
	 *
	 * Allow a theme to provide a directory prefix to locate templates.
	 *
	 * @param string $directory Directory prefix (can be empty).
	 * @return string Directory prefix to prepend to template filename.
	 *
	 * Example:
	 * add_filter( 'flex_posts_template_directory', function() {
	 *     return 'my-plugin-templates/';
	 * } );
	 */
	$directory = apply_filters( 'flex_posts_template_directory', '' );
	$template  = locate_template( $directory . $file );
	if ( empty( $template ) ) {
		/**
		 * Filter: flex_posts_template
		 *
		 * Filter the full template path that will be included if a theme
		 * does not provide an override. Callback signature: function( $path, $type, $layout ).
		 *
		 * @param string $path   Default template path.
		 * @param string $type   The render type (e.g. 'list').
		 * @param int    $layout Layout number.
		 * @return string Modified template path.
		 */
		$template = apply_filters( 'flex_posts_template', FLEX_POSTS_DIR . 'public/' . $file, $type, $layout );
	}

	if ( ! file_exists( $template ) ) {
		return;
	}

	if ( $query->have_posts() ) {
		if ( ! empty( $instance['show_excerpt'] ) ) {
			add_filter( 'excerpt_more', '__return_null' );
		}

		include $template;

		/**
		 * Action: flex_posts_end
		 *
		 * Fired at the end of the widget/block render. Useful to append
		 * pagination or other trailing content. Callback signature:
		 * function( $instance, $args, $max_num_pages, $found_posts ).
		 *
		 * @param array $instance Widget/block instance settings.
		 * @param array $args     WP_Query arguments used.
		 * @param int   $max_num_pages Maximum number of pages from the query.
		 * @param int   $found_posts Total posts found by the query.
		 *
		 * Example:
		 * add_action( 'flex_posts_end', function( $instance, $args, $max, $found ) {
		 *     // custom pagination
		 * }, 10, 4 );
		 */
		do_action( 'flex_posts_end', $instance, $args, $query->max_num_pages, $query->found_posts );
		wp_reset_postdata();

		if ( ! empty( $instance['show_excerpt'] ) ) {
			remove_filter( 'excerpt_more', '__return_null' );
		}
	}
}

/**
 * Get allowed html tags
 *
 * Shared escaping whitelist used when rendering the block title and the
 * pagination markup.
 *
 * @return array
 */
function flex_posts_get_allowed_html() {
	$attr = array(
		'class' => array(),
		'title' => array(),
	);

	/**
	 * Filter: flex_posts_allowed_html
	 *
	 * Allow modification of allowed HTML tags used by the plugin when
	 * rendering pagination and other safe markup. Expected to return an
	 * array in the same shape as wp_kses_allowed_html.
	 *
	 * @param array $allowed_html Default allowed HTML tags/attributes.
	 * @return array Modified allowed HTML tags/attributes.
	 *
	 * Example:
	 * add_filter( 'flex_posts_allowed_html', function( $allowed ) {
	 *     $allowed['img'] = array( 'src' => true, 'alt' => true );
	 *     return $allowed;
	 * } );
	 */
	$allowed_html = apply_filters(
		'flex_posts_allowed_html',
		array(
			'a'    => $attr + array( 'href' => array() ),
			'ul'   => $attr,
			'li'   => $attr,
			'span' => $attr + array( 'aria-current' => array() ),
		)
	);
	return $allowed_html;
}

/**
 * Render block
 *
 * @param  array  $attributes Attributes.
 * @param  string $type       Block type.
 * @return string
 */
function flex_posts_render( $attributes, $type = 'list' ) {
	ob_start();

	$class = 'widget widget_flex-posts-' . $type;
	if ( ! empty( $attributes['align'] ) ) {
		$class .= ' align' . $attributes['align'];
	}

	$extra_attr = array( 'class' => $class );

	/**
	 * Filter: flex_posts_extra_attr
	 *
	 * Filter the block wrapper attributes. Signature:
	 * function( $extra_attr, $attributes ).
	 *
	 * @param array $extra_attr Extra html attributes.
	 * @param array $attributes Block attributes.
	 * @return array Modified extra html attributes.
	 */
	$extra_attr = apply_filters( 'flex_posts_extra_attr', $extra_attr, $attributes );

	$attr = get_block_wrapper_attributes( $extra_attr );

	echo '<section ' . $attr . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	/**
	 * Action: flex_posts_block_title_before
	 *
	 * Triggered before rendering the block title. Receives block attributes.
	 *
	 * @param array $attributes Block attributes.
	 */
	do_action( 'flex_posts_block_title_before', $attributes );

	if ( ! empty( $attributes['title'] ) || ! empty( $attributes['title_cat'] ) ) {
		/**
		 * Filter: flex_posts_block_title
		 *
		 * Filter the block title before it is rendered. Signature:
		 * function( $title, $attributes, $id ).
		 *
		 * @param string $title      Title text.
		 * @param array  $attributes Block attributes.
		 * @param string $id         Block identifier.
		 * @return string Modified title.
		 */
		$title = apply_filters( 'flex_posts_block_title', $attributes['title'], $attributes, 'flex-posts-' . $type );

		$el = 'h2';
		if ( ! empty( $attributes['block_title_el'] ) ) {
			if ( in_array( $attributes['block_title_el'], flex_posts_get_title_elements(), true ) ) {
				$el = $attributes['block_title_el'];
			}
		}

		echo '<' . sanitize_key( $el ) . ' class="widget-title">';
		echo wp_kses( $title, flex_posts_get_allowed_html() );
		echo '</' . sanitize_key( $el ) . '>';
	}

	/**
	 * Action: flex_posts_block_title_after
	 *
	 * Triggered after rendering the block title. Receives block attributes.
	 *
	 * @param array $attributes Block attributes.
	 */
	do_action( 'flex_posts_block_title_after', $attributes );

	flex_posts_display( $attributes, $type );

	echo '</section>';
	$display = ob_get_clean();
	return $display;
}

/**
 * Add link to widget title
 *
 * @param  string $title    Title.
 * @param  array  $instance Instance.
 * @param  string $id_base  ID Base.
 * @return string
 */
function flex_posts_widget_title( $title, $instance = array(), $id_base = '' ) {
	if ( is_string( $id_base ) && strpos( $id_base, 'flex-posts' ) === 0 ) {
		if ( ! empty( $instance['title_cat'] ) ) {
			$title = empty( $instance['cat'] ) ? '' : get_cat_name( $instance['cat'] );
		}

		if ( $title ) {
			$title_url = '';
			if ( ! empty( $instance['title_url_cat'] ) ) {
				$category_id = (int) $instance['cat'];
				$title_url   = empty( $category_id ) ? '' : get_term_link( $category_id );
			} elseif ( ! empty( $instance['title_url'] ) ) {
				$title_url = $instance['title_url'];
			}

			if ( $title_url ) {
				$title = '<a href="' . esc_url( $title_url ) . '">' . $title . '</a>';
			}
		}
	}
	return $title;
}
add_filter( 'widget_title', 'flex_posts_widget_title', 10, 3 );
add_filter( 'flex_posts_block_title', 'flex_posts_widget_title', 10, 3 );
