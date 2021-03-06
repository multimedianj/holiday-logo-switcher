<?php
/**
 * Holiday Logo Switcher
 * 
 * By Nicolas Johnson - 2019
 * https://njohnson.pro
 * 
 */

Namespace HolidayLogoSwitcher\Includes\Classes;

/**
 * Holiday Logo Switcher class
 */
class Holiday_Logo_Switcher {
	
	// Plugin directory URL
	public $plugin_dir_url = null;

	/**
	 * Constructor
	 */
	public function __construct() {
		$basename = plugin_basename( __FILE__ );
		$plugins_dir_url = plugin_dir_url( dirname( dirname( __FILE__ ) . '../' ) );
		
		$this->plugin_dir_url = $plugins_dir_url;
	}

    /**
	 * Code to execute
	 */
	public function run() {
		$this->define_hooks();
		$this->define_admin_hooks();
  	}
	
	/**
	 * Hooks on the front-end
	 */
	public function define_hooks() {
		add_action( 'init', array( $this, 'holiday_logos' ), 0 );
		add_action( 'init', array( $this, 'hls_meta_boxes' ) );
		add_shortcode( 'hls', array( $this, 'hls_shortcode' ) );
	}

	/**
	 * Custom post type that contains holiday logos
	 */
	public function holiday_logos() {
		include( dirname( __FILE__ ) . '/../views/cpt-holiday-logos.php' );
	}

	/**
	 * Meta boxes
	 */
	public function hls_meta_boxes() {
		include( dirname( __FILE__ ) . '/../views/mb-hls-options.php' );
	}

	/**
	 * Shortcode that displays holiday logo
	 */
	public function hls_shortcode() {
		$args = array(
            'post_type' => 'holiday_logos',
            'post_status' => 'publish'
        );

        $result = '';
		$query = new \WP_Query($args);
		$img = '';

		if ( get_option( 'hls_default_logo' ) ) {
			$img = '<a href="' . get_home_url() . '" id="hls__logo"><img src="' . get_option( 'hls_default_logo' ) . '" alt="' . get_bloginfo() . '"/></a>';
		}
		
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
				$query->the_post();

				$date_today = date('Y-m-d');
				$date_today = date( 'Y-m-d', strtotime($date_today) );
				$date_from = strtr( get_post_meta( get_the_ID(), 'logo_options_from_date_', true ), '/', '-' );
				$date_from = date( 'Y-m-d', strtotime($date_from) );
				$date_to = strtr( get_post_meta( get_the_ID(), 'logo_options_to_date_', true ), '/', '-' );
				$date_to = date( 'Y-m-d', strtotime($date_to) );
				$image_alt = get_post_meta( get_the_ID(), 'logo_options_image_alt', true );

				if ( ($date_today >= $date_from) && ($date_today <= $date_to ) ) {
					$img = '<a href="' . get_home_url() . '" id="hls__logo"><img src="' . get_the_post_thumbnail_url() . '" alt="' . $image_alt . '"/></a>';
				}
			}
		}
		$result .= $img;

		wp_reset_postdata();
		return $result;
	}

	/**
	 * Admin hooks
	 */
	protected function define_admin_hooks() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
  }
    
	/**
	 * Admin menus
	 */
	public function admin_menu() {
		add_options_page( __( 'Holiday Logo Switcher', 'holiday-logo-switcher' ), 
			__( 'Holiday Logo Switcher', 'holiday-logo-switcher' ), 
			'manage_options', 
			'holiday-logo-switcher', 
			array( $this, 'options_page' )
		);
    }

	/**
	 * Options page
	 */
	public function options_page() {
		include( dirname( __FILE__ ) . '/../views/options-page.php' );
	}

	/**
	 * Register Settings
	 */
	public function register_settings() {
		add_settings_section(
			'nj_logo_switcher',
			'',
			'',
			'nj_logo_switcher'
		);

		add_settings_field(
			'hls_default_logo',
			__( 'Default logo', 'holiday-logo-switcher' ),
			array( $this, 'elems_callback' ),
			'nj_logo_switcher',
			'nj_logo_switcher'
		);
		register_setting( 'nj_logo_switcher', 'hls_default_logo', array( 'type' => 'string' ) );
	}

	/*
	* Callback functions for option page.
	*/
	function elems_callback() 				{ include( dirname( __FILE__ ) . '/../views/elems.php' ); }

	/**
	 * Plugin activation
	 */
	public static function activate() {
		flush_rewrite_rules();
	}

	/**
	 * Plugin deactivation
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}

	/**
	 * Plugin uninstall
	 */
	public static function uninstall() {
		delete_option( 'hls_default_logo' );
	}
}