<?php
/**
 * Category color functions
 *
 * @package Flex Posts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register category color meta
 */
function flex_posts_register_color_meta() {
	register_term_meta(
		'category',
		'fp_color',
		array(
			'type'              => 'string',
			'description'       => __( 'Category color', 'flex-posts' ),
			'single'            => true,
			'sanitize_callback' => 'sanitize_hex_color',
			'show_in_rest'      => true,
		)
	);
}
add_action( 'init', 'flex_posts_register_color_meta' );

/**
 * Add color field to category edit form
 *
 * @param obj $term Term object.
 */
function flex_posts_edit_color_field( $term ) {
	$color = get_term_meta( $term->term_id, 'fp_color', true );
	?>
	<tr class="form-field">
		<th scope="row"><label for="fp_color"><?php esc_html_e( 'Color', 'flex-posts' ); ?></label></th>
		<td>
			<input name="fp_color" id="fp_color" type="text" value="<?php echo esc_attr( $color ); ?>" class="wp-color-picker-field">
		</td>
	</tr>
	<?php
}
add_action( 'category_edit_form_fields', 'flex_posts_edit_color_field' );

/**
 * Add color field to category add form
 */
function flex_posts_add_color_field() {
	?>
	<div class="form-field">
		<label for="fp_color"><?php esc_html_e( 'Color', 'flex-posts' ); ?></label>
		<input name="fp_color" id="fp_color" type="text" value="" class="wp-color-picker-field">
	</div>
	<?php
}
add_action( 'category_add_form_fields', 'flex_posts_add_color_field' );

/**
 * Save category color meta
 *
 * @param int $term_id Term ID.
 */
function flex_posts_save_color_field( $term_id ) {
	if ( ! isset( $_POST['fp_color'] ) ) {
		return;
	}

	// Re-verify the nonce that core set for whichever term form was submitted.
	$verified = false;
	if ( isset( $_POST['_wpnonce'] ) ) {
		$verified = wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), 'update-tag_' . $term_id );
	} elseif ( isset( $_POST['_wpnonce_add-tag'] ) ) {
		$verified = wp_verify_nonce( sanitize_key( $_POST['_wpnonce_add-tag'] ), 'add-tag' );
	} elseif ( isset( $_POST['_ajax_nonce-add-tag-category'] ) ) {
		$verified = wp_verify_nonce( sanitize_key( $_POST['_ajax_nonce-add-tag-category'] ), 'add-tag' );
	}

	if ( ! $verified ) {
		return;
	}

	$color = sanitize_hex_color( wp_unslash( $_POST['fp_color'] ) );
	update_term_meta(
		$term_id,
		'fp_color',
		$color
	);
}
add_action( 'edited_category', 'flex_posts_save_color_field' );
add_action( 'create_category', 'flex_posts_save_color_field' );

/**
 * Enqueue color picker and initialize
 *
 * @param string $hook Hook.
 */
function flex_posts_enqueue_admin_color_picker( $hook ) {
	if ( 'edit-tags.php' !== $hook && 'term.php' !== $hook ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || 'category' !== $screen->taxonomy ) {
		return;
	}

	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );

	add_action(
		'admin_footer',
		function() {
			/**
			 * Filter: flex_posts_color_palettes
			 *
			 * If a theme or plugin wants to supply custom palettes, return a
			 * non-empty indexed array of hex color strings. If the filter
			 * returns null or an empty value, the picker will use WordPress's
			 * built-in default palettes.
			 *
			 * @param array|null $palettes Array of hex colors or null to use WP defaults.
			 */
			$palettes = apply_filters( 'flex_posts_color_palettes', null );

			$fp_palettes = array();
			if ( is_array( $palettes ) && ! empty( $palettes ) ) {
				foreach ( $palettes as $color ) {
					$color = sanitize_hex_color( $color );
					if ( ! empty( $color ) ) {
						$fp_palettes[] = $color;
					}
				}
			}
			?>
			<script type="text/javascript">
			jQuery(document).ready(function($){
				<?php if ( ! empty( $fp_palettes ) ) : ?>
					var fpPalettes = <?php echo wp_json_encode( $fp_palettes ); ?>;

					$('.wp-color-picker-field').wpColorPicker({
						palettes: fpPalettes
					});
				<?php else : ?>
					$('.wp-color-picker-field').wpColorPicker();
				<?php endif; ?>
			});
			</script>
			<?php
		}
	);
}
add_action( 'admin_enqueue_scripts', 'flex_posts_enqueue_admin_color_picker' );

/**
 * Add a "Color" column to the Categories list table in WP Admin.
 *
 * @param array $columns Existing columns.
 * @return array Modified columns with fp_color added.
 */
function flex_posts_add_category_columns( $columns ) {
	$columns['fp_color'] = __( 'Color', 'flex-posts' );
	return $columns;
}
add_filter( 'manage_edit-category_columns', 'flex_posts_add_category_columns' );

/**
 * Render the content for the custom "Color" category column.
 *
 * @param string $deprecated Deprecated (unused).
 * @param string $column Column name.
 * @param int    $term_id Term ID.
 */
function flex_posts_render_category_color_column( $deprecated, $column, $term_id ) {
	if ( 'fp_color' !== $column ) {
		return;
	}

	$color = get_term_meta( $term_id, 'fp_color', true );
	if ( empty( $color ) ) {
		echo ''; // no color set.
		return;
	}

	echo '<span style="display:inline-block;width:18px;height:18px;vertical-align:middle;margin-right:8px;border:1px solid #ddd;background:' . esc_attr( $color ) . ';"></span><span>' . esc_html( $color ) . '</span>';
}
add_action( 'manage_category_custom_column', 'flex_posts_render_category_color_column', 10, 3 );
