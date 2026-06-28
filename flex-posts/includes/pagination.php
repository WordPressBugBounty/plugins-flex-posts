<?php
/**
 * Pagination output and helpers
 *
 * @package Flex Posts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get current page for pagination
 *
 * @return int
 */
function flex_posts_get_current_page() {
	if ( get_query_var( 'paged' ) ) {
		$current_page = get_query_var( 'paged' );
	} elseif ( get_query_var( 'page' ) ) {
		$current_page = get_query_var( 'page' );
	} else {
		$current_page = 1;
	}
	return $current_page;
}

/**
 * Display pagination
 *
 * @param int $total The total amount of pages.
 */
function flex_posts_pagination( $total ) {
	if ( $total > 1 ) {
		/**
		 * Filter: flex_posts_pagination_args
		 *
		 * Allow modification of the arguments passed to paginate_links() used
		 * by the plugin. Expected return is an array of paginate_links args.
		 *
		 * @param array $args Default paginate_links args.
		 * @return array Modified paginate_links args.
		 *
		 * Example:
		 * add_filter( 'flex_posts_pagination_args', function( $args ) {
		 *     $args['mid_size'] = 2;
		 *     return $args;
		 * } );
		 */
		$links = paginate_links(
			apply_filters(
				'flex_posts_pagination_args',
				array(
					'total'     => $total,
					'current'   => flex_posts_get_current_page(),
					'mid_size'  => 1,
					'prev_text' => '<span class="screen-reader-text">' . __( 'Previous', 'flex-posts' ) . '</span> <span aria-hidden="true">&laquo;</span>',
					'next_text' => '<span class="screen-reader-text">' . __( 'Next', 'flex-posts' ) . '</span> <span aria-hidden="true">&raquo;</span>',
				)
			)
		);

		if ( $links ) {
			echo '<div class="fp-pagination">';
			echo '<span class="screen-reader-text">';
			echo esc_html__( 'Page', 'flex-posts' );
			echo ': </span>';
			echo wp_kses( $links, flex_posts_get_allowed_html() );
			echo '</div>';
		}
	}
}

/**
 * Add pagination at the end of widget
 *
 * @param array $instance      Widget settings.
 * @param array $args          Query arguments.
 * @param int   $max_num_pages Total number of pages.
 * @param int   $found_posts   Total number of posts found.
 */
function flex_posts_end( $instance, $args, $max_num_pages, $found_posts ) {
	if ( ! empty( $instance['pagination'] ) ) {
		if ( ! empty( $args['old_offset'] ) ) {
			// Modify max_num_pages value if offset is set.
			$found_posts   = $found_posts - $args['old_offset'];
			$max_num_pages = ceil( $found_posts / $args['posts_per_page'] );
		}
		flex_posts_pagination( $max_num_pages );
	}
}
add_action( 'flex_posts_end', 'flex_posts_end', 10, 4 );
