<?php
/**
 * Admin pages class.
 *
 * @package Zakra
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Zakra_Admin
 */
class Zakra_Admin {

	/**
	 * Zakra_Admin constructor.
	 */
	public function __construct() {

		$this->add_about_page();
		add_action( 'wp_loaded', array( $this, 'admin_notice' ), 20 );
		add_action( 'wp_loaded', array( $this, 'hide_notices' ), 15 );
		add_action( 'wp_ajax_import_button', array( $this, 'tg_ajax_import_button_handler' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'tg_ajax_enqueue_scripts' ) );

	}

	/**
	 * Localize array for import button AJAX request.
	 */
	public function tg_ajax_enqueue_scripts() {

		$translation_array = array(
			'uri'      => esc_url( admin_url( '/themes.php?page=demo-importer&browse=all&zakra-hide-notice=welcome' ) ),
			'btn_text' => esc_html__( 'Processing...', 'zakra' ),
			'nonce'    => wp_create_nonce( 'zakra_demo_import_nonce' ),
		);
		wp_localize_script( 'zakra-plugin-install-helper', 'zakra_redirect_demo_page', $translation_array );

	}

	/**
	 * Handle the AJAX process while import or get started button clicked.
	 */
	public function tg_ajax_import_button_handler() {

		check_ajax_referer( 'zakra_demo_import_nonce', 'security' );

		$state = '';
		if ( is_plugin_active( 'themegrill-demo-importer/themegrill-demo-importer.php' ) ) {
			$state = 'activated';
		} elseif ( file_exists( WP_PLUGIN_DIR . '/themegrill-demo-importer/themegrill-demo-importer.php' ) ) {
			$state = 'installed';
		}

		if ( 'activated' === $state ) {
			$response['redirect'] = admin_url( '/themes.php?page=demo-importer&browse=all&zakra-hide-notice=welcome' );
		} elseif ( 'installed' === $state ) {
			$response['redirect'] = admin_url( '/themes.php?page=demo-importer&browse=all&zakra-hide-notice=welcome' );
			if ( current_user_can( 'activate_plugin' ) ) {
				$result = activate_plugin( 'themegrill-demo-importer/themegrill-demo-importer.php' );

				if ( is_wp_error( $result ) ) {
					$response['errorCode']    = $result->get_error_code();
					$response['errorMessage'] = $result->get_error_message();
				}
			}
		} else {
			wp_enqueue_style( 'plugin-install' );
			wp_enqueue_script( 'plugin-install' );
			$response['redirect'] = admin_url( '/themes.php?page=demo-importer&browse=all&zakra-hide-notice=welcome' );
			/**
			 * Install Plugin.
			 */
			include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

			$api = plugins_api( 'plugin_information', array(
				'slug'   => sanitize_key( wp_unslash( 'themegrill-demo-importer' ) ),
				'fields' => array(
					'sections' => false,
				),
			) );

			$skin     = new WP_Ajax_Upgrader_Skin();
			$upgrader = new Plugin_Upgrader( $skin );
			$result   = $upgrader->install( $api->download_link );
			if ( $result ) {
				$response['installed'] = 'succeed';
			} else {
				$response['installed'] = 'failed';
			}

			// Activate plugin.
			if ( current_user_can( 'activate_plugin' ) ) {
				$result = activate_plugin( 'themegrill-demo-importer/themegrill-demo-importer.php' );

				if ( is_wp_error( $result ) ) {
					$response['errorCode']    = $result->get_error_code();
					$response['errorMessage'] = $result->get_error_message();
				}
			}
		}

		wp_send_json( $response );

		exit();

	}

	/**
	 * Setup config for about page.
	 */
	public function add_about_page() {

		$support_link = ( zakra_is_zakra_pro_active() ) ? 'https://zakratheme.com/support-ticket/' : 'https://wordpress.org/support/theme/zakra/';

		$config = array(
			'menu_name'       => apply_filters( 'zakra_about_page_filter', __( 'About Zakra', 'zakra' ), 'menu_name' ),
			'page_name'       => apply_filters( 'zakra_about_page_filter', __( 'About Zakra', 'zakra' ), 'page_name' ),
			'welcome_content' => apply_filters( 'zakra_about_page_filter', __( 'Zakra is beautifully designed clean WordPress blog theme. Easy to setup and has a nice set of features that make your site stand out. It is suitable for personal, fashion, food, travel, business, professional, niche and any kind of blogging sites. Comes with various demos for various purposes, which you can easily import with the help of ThemeGrill Demo Importer plugin.', 'zakra' ), 'page_name' ),
			/* translators: s - theme name */
			'welcome_title'   => apply_filters( 'zakra_about_page_filter', sprintf( __( 'Welcome to %s! : Version ', 'zakra' ), 'Zakra' ), 'welcome_title' ),
			'tabs'            => array(
				'site_library'        => __( 'Site Library', 'zakra' ),
				'getting_started'     => __( 'Getting Started', 'zakra' ),
				'recommended_plugins' => __( 'Recommended Plugins', 'zakra' ),
				'support'             => __( 'Support', 'zakra' ),
				'changelog'           => __( 'Changelog', 'zakra' ),
			),

			'site_library' => array(
				'one' => array(),
			),

			'getting_started' => array(
				'one'   => array(
					'title'          => esc_html__( 'One click demo import', 'zakra' ),
					'text'           => esc_html__( 'Get whole site on just one click.', 'zakra' ),
					'button_label'   => esc_html__( 'Install and Activate', 'zakra' ),
					'button_link'    => 'themegrill-demo-importer',
					'is_button'      => true,
					'install_button' => true,
				),
				'four'  => array(
					'title'        => esc_html__( 'Theme Customizer', 'zakra' ),
					'text'         => esc_html__( 'All Theme Options are available via Customize screen.', 'zakra' ),
					'button_label' => esc_html__( 'Customizer', 'zakra' ),
					'button_link'  => esc_url( admin_url( 'customize.php' ) ),
					'is_button'    => true,
				),
				'three' => array(
					'title'        => esc_html__( 'Documentation', 'zakra' ),
					'text'         => esc_html__( 'Please view our documentation page to setup the theme.', 'zakra' ),
					'button_label' => esc_html__( 'Documentation', 'zakra' ),
					'button_link'  => 'https://docs.zakratheme.com/',
					'is_button'    => false,
				),
				'two'   => array(
					'title'        => esc_html__( 'Recommended plugins', 'zakra' ),
					'text'         => esc_html__( 'Boost your site adding plugins.', 'zakra' ),
					'button_label' => esc_html__( 'Recommended plugins', 'zakra' ),
					'button_link'  => esc_url( '#recommended_plugins' ),
					'is_button'    => false,
				),
			),

			'recommended_plugins' => array(
				'content' => array(
					array(
						'slug' => 'themegrill-demo-importer',
					),
					array(
						'slug' => 'elementor',
					),
					array(
						'slug' => 'everest-forms',
					),
					array(
						'slug' => 'optimole-wp',
					),
					array(
						'slug' => 'social-icons',
					),
					array(
						'slug' => 'easy-social-sharing',
					),
				),
			),

			'support' => array(

				'one' => array(
					'title'        => esc_html__( 'Documentation', 'zakra' ),
					'text'         => esc_html__( 'Please view our documentation page to setup the theme.', 'zakra' ),
					'button_label' => esc_html__( 'Documentation', 'zakra' ),
					'button_link'  => 'https://docs.zakratheme.com/',
					'is_button'    => false,
				),
				'two' => array(
					'title'        => esc_html__( 'Got theme support question?', 'zakra' ),
					'text'         => esc_html__( 'Please put it in our dedicated support forum.', 'zakra' ),
					'button_label' => esc_html__( 'Support Forum', 'zakra' ),
					'button_link'  => $support_link,
					'is_button'    => false,
				),

			),

		);

		// Process the config for about page.
		Zakra_About_Page::init( apply_filters( 'zakra_about_page_array', $config ) );

	}


	/**
	 * Add admin notice.
	 */
	public function admin_notice() {

		wp_enqueue_style( 'zakra-message', get_template_directory_uri() . '/inc/admin/css/message.css', array(), ZAKRA_THEME_VERSION );

		// Let's bail on theme activation.
		$notice_nag = get_option( 'zakra_admin_notice_welcome' );
		if ( ! $notice_nag ) {
			add_action( 'admin_notices', array( $this, 'welcome_notice' ) );
		}

	}

	/**
	 * Hide a notice if the GET variable is set.
	 */
	public static function hide_notices() {

		if ( isset( $_GET['zakra-hide-notice'] ) && isset( $_GET['_zakra_notice_nonce'] ) ) { // WPCS: input var ok.
			if ( ! wp_verify_nonce( wp_unslash( $_GET['_zakra_notice_nonce'] ), 'zakra_hide_notices_nonce' ) ) { // phpcs:ignore WordPress.VIP.ValidatedSanitizedInput.InputNotSanitized
				wp_die( __( 'Action failed. Please refresh the page and retry.', 'zakra' ) ); // WPCS: xss ok.
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'Cheatin&#8217; huh?', 'zakra' ) ); // WPCS: xss ok.
			}

			$hide_notice = sanitize_text_field( wp_unslash( $_GET['zakra-hide-notice'] ) );
			update_option( 'zakra_admin_notice_' . $hide_notice, 1 );

			// Hide.
			if ( 'welcome' === $_GET['zakra-hide-notice'] ) {
				update_option( 'zakra_admin_notice_' . $hide_notice, 1 );
			} else { // Show.
				delete_option( 'zakra_admin_notice_' . $hide_notice );
			}
		}

	}

	/**
	 * Return or echo `Get started/Import button` HTML.
	 *
	 * @param bool   $return    Return or echo.
	 * @param string $slug      PLugin slug to install.
	 * @param string $text      Text string for button.
	 * @param string $css_class CSS class list for button link.
	 *
	 */
	public static function import_button_html( $return = false, $slug = 'themegrill-demo-importer', $text, $css_class = 'btn-get-started button button-primary button-hero' ) {

		if ( true === $return ) {
			return '<a class="' . esc_attr( $css_class ) . '"
		   href="#" data-name="' . esc_attr( $slug ) . '" data-slug="' . esc_attr( $slug ) . '" aria-label="' . esc_attr__( 'Get started with Zakra', 'zakra' ) . '">' . esc_html( $text ) . '</a>';
		} else {
			echo '<a class="' . esc_attr( $css_class ) . '"
					href="#" data-name="' . esc_attr( $slug ) . '" data-slug="' . esc_attr( $slug ) . '" aria-label="' . esc_attr__( 'Get started with Zakra', 'zakra' ) . '">' . esc_html( $text ) . '</a>';
		}

	}

	/**
	 * Show welcome notice.
	 */
	public function welcome_notice() {
		?>

		<div id="message" class="updated notice-info zakra-message">
			<a class="zakra-message-close notice-dismiss"
			   href="<?php echo esc_url( wp_nonce_url( remove_query_arg( array( 'activated' ), add_query_arg( 'zakra-hide-notice', 'welcome' ) ), 'zakra_hide_notices_nonce', '_zakra_notice_nonce' ) ); ?>">
				<?php esc_html_e( 'Dismiss', 'zakra' ); ?>
			</a>
			<div class="zakra-message-wrapper">
				<img class="zakra-screenshot" src="<?php echo get_template_directory_uri(); ?>/screenshot.jpg" alt="<?php esc_html_e( 'Zakra', 'zakra' ); ?>" /><?php // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped, Squiz.PHP.EmbeddedPhp.SpacingBeforeClose ?>

				<div class="zakra-getting-started-notice">
					<h2>
						<?php
						printf(
						/* translators: 1: welcome page link starting html tag, 2: welcome page link ending html tag. */
							esc_html__( 'Welcome! Thank you for choosing Zakra! To fully take advantage of the best our theme can offer, please make sure you visit our %1$swelcome page%2$s.', 'zakra' ), '<a href="' . esc_url( admin_url( 'themes.php?page=zakra-about' ) ) . '">', '</a>' );
						?>
					</h2>

					<p class="plugin-install-notice"><?php esc_html_e( 'Clicking the button below will install and activate the ThemeGrill demo importer plugin.', 'zakra' ); ?></p>

					<?php self::import_button_html( false, '', esc_html__( 'Get started with Zakra', 'zakra' ) ); ?>
				</div>
			</div>
		</div>
		<?php
	}

}

$admin = new Zakra_Admin();
