<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://wpbeginner.com
 * @since      1.0.0
 *
 * @package    WpCallButton
 * @subpackage WpCallButton/Plugin
 */
namespace WpCallButton\Plugin;

class WpCallButtonPlugin {

	/**
	 * Holds the plugin name slug.
	 * @var string
	 */
	public $plugin_slug;

	/**
	 * Holds the plugin name.
	 * @var string
	 */
	public $plugin_name;

	/**
	 * Holds the plugin admin object.
	 * @var object
	 */
	public $plugin_admin;

	/**
	 * Holds the plugin settings array.
	 * @var object
	 */
	public $plugin_settings;

	/**
	 * Constructor.
	 */
	function __construct( $name, $slug ) {
		$this->plugin_slug = $slug;
		$this->plugin_name = $name;
		
		// Setup the plugin administration (for admin screen and settings).
		if ( is_admin() ) {
			new WpCallButtonAdmin( $this->plugin_name, $this->plugin_slug );
		}

		// Get the plugin settings.
		$this->plugin_settings = WpCallButtonHelpers::get_settings();

		// Setup initialization.
		$this->init();
	}

	/**
	 * Register all the necessary actions and perform setup.
	 */
	public function init() {
		// Print the call button in footer.
		add_action( 'wp_footer', array( $this, 'print_call_button' ) );

		// Print the call button styles in header.
		// Note: we could have printed the styles in the body just before
		// printing the call button HTML without worrying about FOUC (as it won't happen)
		// but we will skip it, just because.
		add_action( 'wp_head', array( $this, 'print_call_button_styles' ) );

		// Load text domain.
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		// Setup the Call button widget.
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		
		// Register the WP Call Button shortcode.
		add_shortcode( 'wp_call_button', array( $this, 'wp_call_button_shortcode_func' ) );
		
		// Check if Gutenberg available.
		if ( has_action( 'enqueue_block_editor_assets' ) ) {
			
			// Enqueue the scripts for the custom Call block.
			add_action( 'enqueue_block_editor_assets', array( $this, 'load_wpcb_block_files' ) );

			// Register the functions for rendering the Call Button dynamic block.
			add_action( 'init', array( $this, 'wp_call_button_block' ) );
		}
	}
	
	/**
	 * Enqueue guten block script.
	 */
	function load_wpcb_block_files() {
		// Scripts.
		wp_enqueue_script( 'wp-call-btn-guten-blocks-script', plugin_dir_url( WP_CALL_BUTTON_FILE ) . 'assets/js/blocks.js', array( 'wp-blocks', 'wp-i18n', 'wp-editor', 'wp-element', 'wp-components' ), WP_CALL_BUTTON_VERSION, true );
    wp_localize_script( 'wp-call-btn-guten-blocks-script', 'wpcallbtn_block_vars',
        array(
            'plugin_name' => $this->plugin_name,
						'call_btn_color' => __( 'Call Button Color', 'wp-call-button' ),
						'call_btn_text_color' => __( 'Text Color', 'wp-call-button' ),
						'color_label' => __( 'Select Color', 'wp-call-button' ),					
						'call_btn_phone_icon_hide' => __( 'Hide Phone Icon?', 'wp-call-button' ),
						'call_btn_center_btn' => __( 'Center Align Call Button?', 'wp-call-button' ),
						'data_call_btn_text' => $this->plugin_settings['wpcallbtn_button_text'],
						'call_btn_font_size' =>  __( 'Call Button Font Size', 'wp-call-button' ),
						'other_settings' => __( 'Other Settings', 'wp-call-button' )
        )
    );
		
		// Styles.
		wp_register_style( 'wp-call-btn-guten-blocks-style', plugins_url( '/assets/css/blocks.css', WP_CALL_BUTTON_FILE ), array(), WP_CALL_BUTTON_VERSION );
		wp_enqueue_style( 'wp-call-btn-guten-blocks-style' );
	}	
	
	/**
	 * Setup a render callback to handle dynamic save / output for the Block.
	 */	
	function wp_call_button_block() {
		if ( function_exists( 'register_block_type' ) ) {
			register_block_type( 'wp-call-button/wp-call-button-block', array(
					'editor_script' => 'wp-call-btn-guten-blocks-script',
					'render_callback' => array( $this, 'wp_call_btn_dynamic_render_callback' )
			) );
		}
	}

	/**
	 * Dynamic render callback for the Call Button Block.
	 */	
	function wp_call_btn_dynamic_render_callback( $attributes, $content ) {
		
		// Read the plugin settings.
		$settings = $this->plugin_settings;	
		
		// attributes 
		$attrs = $attributes;	

		// Set defaults from settings.
		if ( empty( $attrs['btn_text'] ) ) {
			$attrs['btn_text'] = $settings['wpcallbtn_button_text'];
		}
		if ( empty( $attrs['btn_color'] ) ) {
			$attrs['btn_color'] = $settings['wpcallbtn_button_color'];
		}
		if ( empty( $attrs['btn_txt_color'] ) ) {
			$attrs['btn_txt_color'] = '#fff';
		}		
		if ( empty( $attrs['hide_phone_icon'] ) ) {
			$attrs['hide_phone_icon'] = 'no';
		} else {
			if ( $attrs['hide_phone_icon'] ) {
				$attrs['hide_phone_icon'] = 'yes';
			} else {
				$attrs['hide_phone_icon'] = 'no';
			}
		}
		if ( empty( $attrs['btn_font_size'] ) ) {
			$attrs['btn_font_size'] = 16;
		}
		$btn_font_size = intval( esc_attr( $attrs['btn_font_size'] ) );
		
		// Get the call button.
		$call_button = WpCallButtonHelpers::get_call_button( $settings );
		
		// Get the call button text.
		$call_button_text = '<span>' . ( $attrs['hide_phone_icon'] == 'no' ? '<img style="' . ( $btn_font_size === 16 ? ' width: 50px; height: 20px; ' : ' width: 70px; height: 30px; ' ) . ' vertical-align: middle; border: 0 !important; box-shadow: none !important; -webkit-box-shadow: none !important;" src="' . WpCallButtonHelpers::get_phone_image( $attrs['btn_txt_color'] ) . '" />' : '' ) . esc_html( $attrs['btn_text'] ) . '</span>';
		
		// Get the google analytics click tracking.
		$click_tracking = $call_button['tracking'];
		
		// Build the styles for the call button.
		$call_button_markup = 'box-sizing: border-box; margin-bottom: 20px; display: inline-block; border-radius: 5px;' .
				'width: auto; text-align: center !important; font-size: ' . $btn_font_size . 'px !important; ' .
    		'font-weight: bold !important; ' .
				( $attrs['hide_phone_icon'] == 'no' ? 'padding: 15px 20px 15px ' . ( $btn_font_size === 16 ? 5 : 0 ) . 'px !important; ' : 'padding: 15px 20px !important;' ) . 
				'text-decoration: none !important;' .
				'background: ' . esc_attr( $attrs['btn_color'] ) . ' !important;' .
				'color: ' . esc_attr( $attrs['btn_txt_color'] ) . ' !important;';
		
		// Return the call button.
		if ( $settings['wpcallbtn_button_enabled'] == 'yes' && ! empty( $settings['wpcallbtn_phone_num'] ) ) {
			// build the button markup
			$btn_markup = '<a style="' . $call_button_markup . '" class="' . $this->plugin_slug . '-in-btn" href="tel:' . esc_attr( $settings['wpcallbtn_phone_num'] ) . '"' . $click_tracking . '>' . $call_button_text . '</a>';
			if ( ! empty( $attrs['btn_center_align'] ) && $attrs['btn_center_align'] ) {
				// wrap in align div if center align
				$btn_markup = '<div class="' . $this->plugin_slug . '-btn-center-container' . '" style="text-align: center;">' . $btn_markup . '</div>';
			}
			return $btn_markup;
		}
		return '';
	}	
	
	/**
	 * Register Call button plugin widget.
	 */
	function register_widgets() {
		register_widget( 'WpCallButton\Plugin\WpCallButtonWidget' );
	}	
	
	/**
	 * Function to render the Call button via the shortcode.
	 */	
	function wp_call_button_shortcode_func( $atts ) {
		// Read the plugin settings.
		$settings = $this->plugin_settings;	

		// Read the attributes and set defaults.
		$attrs = shortcode_atts( array(
			'btn_text' 				=> $settings['wpcallbtn_button_text'],
			'btn_color'				=> $settings['wpcallbtn_button_color'],
			'hide_phone_icon' => 'false'
		), $atts );

		// Set defaults from settings.
		if ( empty( $attrs['btn_text'] ) ) {
			$attrs['btn_text'] = $settings['wpcallbtn_button_text'];
		}
		if ( empty( $attrs['btn_color'] ) ) {
			$attrs['btn_color'] = $settings['wpcallbtn_button_color'];
		}
		if ( empty( $attrs['hide_phone_icon'] ) ) {
			$attrs['hide_phone_icon'] = 'no';
		}
		
		// Get the call button.
		$call_button = WpCallButtonHelpers::get_call_button( $settings );
		
		// Get the call button text.
		$call_button_text = '<span>' . ( $attrs['hide_phone_icon'] == 'no' ? '<img style="width: 70px; height: 30px; vertical-align: middle; border: 0 !important; box-shadow: none !important; -webkit-box-shadow: none !important;" src="' . WpCallButtonHelpers::get_phone_image() . '" />' : '' ) . esc_html( $attrs['btn_text'] ) . '</span>';
		
		// Get the google analytics click tracking.
		$click_tracking = $call_button['tracking'];
		
		// Build the styles for the call button.
		$call_button_markup = 'display: inline-block; box-sizing: border-box; border-radius: 5px;' .
				'color: white !important; width: auto; text-align: center !important; font-size: 24px !important; ' .
    		'font-weight: bold !important; ' .
				( $attrs['hide_phone_icon'] == 'no' ? 'padding: 15px 20px 15px 0 !important; ' : 'padding: 15px 20px !important;' ) . 
				'text-decoration: none !important;' .
				'background: ' . esc_attr( $attrs['btn_color'] ) . ' !important;';
		
		// Return the call button.
		return	( $settings['wpcallbtn_button_enabled'] == 'yes' && ! empty( $settings['wpcallbtn_phone_num'] ) ) ? '<a style="' . $call_button_markup . '" class="' . $this->plugin_slug . '-in-btn" href="tel:' . esc_attr( $settings['wpcallbtn_phone_num'] ) . '"' . $click_tracking . '>' . $call_button_text . '</a>' : '';
	}

	/**
	 * Outputs the Call Button Styles on the website
	 */
	public function print_call_button_styles() {

		// Get the call button.
		$call_button = WpCallButtonHelpers::get_call_button( $this->plugin_settings );

		// Get the settings.
		$settings = $call_button['settings'];

		// Proceed if the call button should be shown.
		if ( $call_button['show_call_button'] ) {

			// Build the position style.
			$position = '';
			if ( $settings['wpcallbtn_button_position'] == 'bottom-left' ) {
				$position = ' left: 20px; ';
			} else if ( $settings['wpcallbtn_button_position'] == 'bottom-right' ) {
				$position = ' right: 20px; ';
			} else if ( $settings['wpcallbtn_button_position'] == 'bottom-center' ) {
				$position = ' left: 50%; margin-left: -30px; ';
			}

			$call_button_markup = '.' . $this->plugin_slug . '{display: block; position: fixed; text-decoration: none; z-index: 9999999999;' .
    		'width: 60px; height: 60px; border-radius: 50%;' .
    		'/*transform: scale(0.8);*/ ' . $position;

			// Special case for full width button.
			if ( $settings['wpcallbtn_button_position'] == 'bottom-full' ) {
				$call_button_markup .= 'background: ' . $settings['wpcallbtn_button_color'] . ' !important;' .
					' color: white !important; border-radius: 0; width: 100%; text-align: center !important; font-size: 24px !important; ' .
    			' font-weight: bold !important; padding: 17px 0 0 0 !important; text-decoration: none !important;  bottom: 0; ';
			} else {
				$call_button_markup .= ' bottom: 20px; background: url( ' . WpCallButtonHelpers::get_phone_image() . ' ) center/30px 30px no-repeat ' . $settings['wpcallbtn_button_color'] . ' !important;';
			}

			// Finish markup.
			$call_button_markup .=	'}';

			// Append media styles if displaying button only for mobile devices.
			if ( $settings['wpcallbtn_button_mobile_only'] == 'yes' ) {
				$call_button_markup = '.' . $this->plugin_slug . '{ display: none; } ' . '@media screen and (max-width: 650px) { ' . $call_button_markup . ' }';
			}

			// Print the styles.
			// TODO: l9n
			echo '<!-- This website uses the ' . $this->plugin_name .' plugin to generate more leads. --><style type="text/css">' . $call_button_markup . '</style>';
		}
	}

	/**
	 * Outputs the Call Button on the website.
	 */
	public function print_call_button() {
		// Get the settings.
		$settings = $this->plugin_settings;
		
		// Get the call button.
		$call_button = WpCallButtonHelpers::get_call_button( $settings );

		// Proceed if the call button should be shown.
		if ( $call_button['show_call_button'] ) {

			// Get the google analytics click tracking.
			$click_tracking = $call_button['tracking'];

			// Get the call button text.
			$call_button_text = ( isset( $settings['wpcallbtn_button_position'] ) && $settings['wpcallbtn_button_position'] == 'bottom-full' ) ? '<span>' . esc_html( $settings['wpcallbtn_button_text'] ) . '</span>' : '';

			// Prepend the Call button if the Button style is full width.
			if ( $settings['wpcallbtn_button_position'] == 'bottom-full' ) {
				$call_button_text = '<img style="width: 70px; height: 30px; vertical-align: middle; border: 0 !important; box-shadow: none !important; -webkit-box-shadow: none !important;" src="' . WpCallButtonHelpers::get_phone_image() . '" />' . $call_button_text;
			}

			// Build the call button html.
			echo	'<a class="' . $this->plugin_slug . '" href="tel:' . esc_attr( $settings['wpcallbtn_phone_num'] ) . '"' . $click_tracking . '>' . $call_button_text . '</a>';
		}
	}

	/**
	 * Load the text domain.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( $this->plugin_slug, false, dirname( plugin_basename( WP_CALL_BUTTON_FILE ) ) . '/languages/' );
	}
}
