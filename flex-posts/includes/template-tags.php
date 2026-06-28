<?php
/**
 * Functions used in template files
 *
 * @package Flex Posts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'flex_posts_title' ) ) {
	/**
	 * Display post title.
	 *
	 * @param array $instance Widget settings.
	 */
	function flex_posts_title( $instance ) {
		if ( empty( $instance['show_title'] ) ) {
			return;
		}
		$el = 'h4';
		if ( ! empty( $instance['post_title_el'] ) ) {
			if ( in_array( $instance['post_title_el'], flex_posts_get_title_elements(), true ) ) {
				$el = $instance['post_title_el'];
			}
		}
		?>
		<<?php echo sanitize_key( $el ); ?> class="fp-title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</<?php echo sanitize_key( $el ); ?>>
		<?php
	}
}

if ( ! function_exists( 'flex_posts_meta' ) ) {
	/**
	 * Display meta information.
	 *
	 * @param array $instance Widget settings.
	 */
	function flex_posts_meta( $instance ) {
		/**
		 * Action: flex_posts_meta_start
		 *
		 * Fired at the start of meta output for a post inside the widget/block.
		 * Example:
		 * add_action( 'flex_posts_meta_start', function() { echo '<span>Start</span>'; } );
		 *
		 * @param none
		 */
		do_action( 'flex_posts_meta_start' );

		if ( ! empty( $instance['show_author'] ) || ! empty( $instance['show_avatar'] ) ) {
			flex_posts_author_meta( $instance );
		}
		if ( ! empty( $instance['show_date'] ) ) {
			flex_posts_date_meta();
		}
		if ( ! empty( $instance['show_comments'] ) ) {
			flex_posts_comments_meta();
		}

		/**
		 * Action: flex_posts_meta_end
		 *
		 * Fired at the end of meta output for a post inside the widget/block.
		 * Useful for adding extra meta content.
		 *
		 * @param none
		 */
		do_action( 'flex_posts_meta_end' );
	}
}

if ( ! function_exists( 'flex_posts_author_meta' ) ) {
	/**
	 * Display author meta.
	 *
	 * @param array $instance Widget settings.
	 */
	function flex_posts_author_meta( $instance = array() ) {
		$author_id    = get_the_author_meta( 'ID' );
		$author_url   = get_author_posts_url( $author_id );
		$author_title = sprintf(
			/* translators: %s: Author's display name. */
			__( 'Posts by %s', 'flex-posts' ),
			get_the_author()
		);

		/**
		 * Filter: flex_posts_author_image_size
		 *
		 * Filter the size (in pixels) used for author avatars in meta output.
		 *
		 * @param int $size Default avatar size in pixels.
		 * @return int Modified avatar size.
		 *
		 * Example:
		 * add_filter( 'flex_posts_author_image_size', function( $size ) { return 32; } );
		 */
		$image_size = apply_filters( 'flex_posts_author_image_size', 24 );
		?>
		<span class="fp-author">
			<span class="author vcard">
				<?php if ( ! empty( $instance['show_avatar'] ) ) : ?>
					<a class="author-image" href="<?php echo esc_url( $author_url ); ?>" rel="author" title="<?php echo esc_attr( $author_title ); ?>">
						<?php echo get_avatar( $author_id, $image_size ); ?>
					</a>
				<?php endif; ?>
				<?php if ( ! empty( $instance['show_author'] ) ) : ?>
					<a class="url fn n" href="<?php echo esc_url( $author_url ); ?>" rel="author">
						<span><?php the_author(); ?></span>
					</a>
				<?php endif; ?>
			</span>
		</span>
		<?php
	}
}

if ( ! function_exists( 'flex_posts_date_meta' ) ) {
	/**
	 * Display date meta.
	 */
	function flex_posts_date_meta() {
		?>
		<span class="fp-date">
			<a href="<?php the_permalink(); ?>" rel="bookmark">
				<time class="entry-date published" datetime="<?php the_date( 'c' ); ?>">
					<?php echo esc_html( get_the_date() ); ?>
				</time>
			</a>
		</span>
		<?php
	}
}

if ( ! function_exists( 'flex_posts_comments_meta' ) ) {
	/**
	 * Display comments meta.
	 */
	function flex_posts_comments_meta() {
		?>
		<span class="fp-comments">
			<?php comments_popup_link(); ?>
		</span>
		<?php
	}
}

if ( ! function_exists( 'flex_posts_categories_meta' ) ) {
	/**
	 * Display categories meta.
	 */
	function flex_posts_categories_meta() {
		$categories = get_the_category();
		if ( ! empty( $categories ) ) {
			$i = 0;
			echo '<span class="fp-categories">';
			foreach ( $categories as $category ) {
				if ( 0 < $i ) {
					echo ', ';
				}
				$color = get_term_meta( $category->term_id, 'fp_color', true );
				$style = $color ? "--fp-color: $color" : '';
				echo '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" rel="category tag" style="' . esc_attr( $style ) . '">' . esc_html( $category->name ) . '</a>';
				++$i;
			}
			echo '</span>';
		}
	}
}

if ( ! function_exists( 'flex_posts_thumbnail' ) ) {
	/**
	 * Display post thumbnail.
	 *
	 * @param string $size         Image size.
	 * @param array  $instance     Widget settings.
	 * @param int    $current_post Post index number.
	 */
	function flex_posts_thumbnail( $size, $instance = array(), $current_post = 0 ) {
		if ( isset( $instance['show_image'] ) ) {
			if ( 'none' === $instance['show_image'] ) {
				return;
			}
			if ( 'first' === $instance['show_image'] && $current_post > 0 ) {
				return;
			}
		}

		/**
		 * Filter: flex_posts_default_image
		 *
		 * Filter the default image URL used when a post has no featured image.
		 *
		 * @param string $url Default image URL.
		 * @return string Modified image URL.
		 *
		 * Example:
		 * add_filter( 'flex_posts_default_image', function( $url ) { return get_stylesheet_directory_uri() . '/img/default.png'; } );
		 */
		$default_image = apply_filters( 'flex_posts_default_image', FLEX_POSTS_URL . 'public/images/default.png' );
		?>
		<div class="fp-media">
			<a class="fp-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php the_post_thumbnail( $size ); ?>
				<?php else : ?>
					<img src="<?php echo esc_url( $default_image ); ?>" class="size-<?php echo esc_attr( $size ); ?>" alt="">
				<?php endif; ?>
			</a>
			<?php
			/**
			 * Action: flex_posts_media
			 *
			 * Action hook inside the media block (thumbnail/link) where additional
			 * markup can be injected. Signature: function( $instance ).
			 *
			 * @param array $instance Widget settings.
			 *
			 * Example:
			 * add_action( 'flex_posts_media', function( $instance ) { echo '<span class="badge">New</span>'; } );
			 */
			do_action( 'flex_posts_media', $instance );
			?>
		</div>
		<?php
	}
}

$flex_posts_excerpt_length = 15;

/**
 * Callback for the excerpt_length filter
 *
 * @return int
 */
function flex_posts_get_excerpt_length() {
	global $flex_posts_excerpt_length;
	return $flex_posts_excerpt_length;
}

if ( ! function_exists( 'flex_posts_excerpt' ) ) {
	/**
	 * Display excerpt.
	 *
	 * @param int $length Number of words.
	 */
	function flex_posts_excerpt( $length = 15 ) {
		global $flex_posts_excerpt_length;
		$flex_posts_excerpt_length = $length;
		add_filter( 'excerpt_length', 'flex_posts_get_excerpt_length' );
		echo get_the_excerpt(); // phpcs:ignore WordPress.Security.EscapeOutput
		remove_filter( 'excerpt_length', 'flex_posts_get_excerpt_length' );
	}
}
