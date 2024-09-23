<?php
/**
 * Flex Posts Options
 *
 * @package Flex Posts
 */

namespace Flex_Posts;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Flex Posts options class
 */
class Options {

	/**
	 * Holds the values to be used in the fields callbacks
	 *
	 * @var array
	 */
	private $options;

	/**
	 * Options page slug
	 *
	 * @var string
	 */
	private $page = 'flex-posts';

	/**
	 * Options group name
	 *
	 * @var string
	 */
	private $group = 'flex_posts_group';

	/**
	 * Option name
	 *
	 * @var string
	 */
	private $option_name = 'flex_posts';

	/**
	 * Sets options page and menu.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'admin_menu', array( $this, 'add_menu_page' ), 11 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue scripts & styles
	 */
	public function enqueue() {
		$screen = get_current_screen();
		if ( 'settings_page_flex-posts' !== $screen->id ) {
			return;
		}

		wp_enqueue_style(
			'flex-posts-options',
			FLEX_POSTS_URL . 'admin/css/options.css',
			array(),
			FLEX_POSTS_VERSION
		);
	}

	/**
	 * Add options page
	 */
	public function add_menu_page() {
		add_options_page(
			__( 'Flex Posts', 'flex-posts' ),
			__( 'Flex Posts', 'flex-posts' ),
			'manage_options',
			$this->page,
			array( $this, 'settings_page' )
		);
	}

	/**
	 * Add settings
	 */
	public function settings_init() {
		register_setting(
			$this->group,
			$this->option_name,
			array(
				$this,
				'sanitize',
			)
		);

		add_settings_section( 'flex_posts_section_1', __( 'Settings', 'flex-posts' ), '', $this->page );

		add_settings_field(
			'disable_css',
			__( 'Disable CSS', 'flex-posts' ),
			array( $this, 'field_disable_css' ),
			$this->page,
			'flex_posts_section_1'
		);

		add_settings_field(
			'disable_image_size',
			__( 'Disable Image Size', 'flex-posts' ),
			array( $this, 'field_disable_image_size' ),
			$this->page,
			'flex_posts_section_1'
		);
	}

	/**
	 * Display field
	 */
	public function field_disable_css() {
		$val = empty( $this->options['disable_css'] ) ? false : true;
		?>
		<label>
			<?php
			flex_posts_input(
				array(
					'type'    => 'checkbox',
					'name'    => $this->option_name . '[disable_css]',
					'class'   => 'checkbox',
					'value'   => 1,
					'checked' => $val,
				)
			);
			?>
			<span><?php esc_html_e( 'Disable CSS', 'flex-posts' ); ?></span>
			<p class="description"><?php esc_html_e( 'This plugin uses a single CSS file (flex-posts.css). If this option is on, the CSS file will not be loaded.', 'flex-posts' ); ?></p>
		</label>
		<?php
	}

	/**
	 * Display field
	 */
	public function field_disable_image_size() {
		$val = empty( $this->options['disable_image_size'] ) ? false : true;
		?>
		<label>
			<?php
			flex_posts_input(
				array(
					'type'    => 'checkbox',
					'name'    => $this->option_name . '[disable_image_size]',
					'class'   => 'checkbox',
					'value'   => 1,
					'checked' => $val,
				)
			);
			?>
			<span><?php esc_html_e( 'Disable Additional Image Size', 'flex-posts' ); ?></span>
			<p class="description"><?php esc_html_e( 'This plugin adds a new custom image size (400×250 cropped). If this option is on, this image size will not be generated.', 'flex-posts' ); ?></p>
		</label>
		<?php
	}

	/**
	 * Sanitize input data
	 *
	 * @param  array $input Input data.
	 * @return array
	 */
	public function sanitize( $input ) {
		$new_input['disable_css']        = empty( $input['disable_css'] ) ? false : true;
		$new_input['disable_image_size'] = empty( $input['disable_image_size'] ) ? false : true;
		return $new_input;
	}

	/**
	 * Display settings page
	 */
	public function settings_page() {
		$this->options = get_option( $this->option_name );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( $this->group );
				do_settings_sections( $this->page );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}

new Options();
