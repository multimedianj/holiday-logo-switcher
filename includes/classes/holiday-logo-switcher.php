<?php
/**
 * Holiday Logo Switcher
 * 
 * By Nicolas Johnson - 2021
 * https://wpinclusion.com
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
		add_action( 'init', array( $this, 'whls_meta_boxes' ) );
		add_shortcode( 'hls', array( $this, 'whls_shortcode' ) );
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
	public function whls_meta_boxes() {
		include( dirname( __FILE__ ) . '/../views/mb-hls-options.php' );
	}

	/**
	 * Shortcode that displays holiday logo
	 */
	public function whls_shortcode() {
		$args = array(
            'post_type' => 'holiday_logos',
            'post_status' => 'publish'
        );

        $result = '';
		$query = new \WP_Query($args);
		$img = '';

		if ( get_option( 'whls_default_logo' ) ) {
			$img = '<a href="' . get_home_url() . '" id="whls__logo"><img src="' . get_option( 'whls_default_logo' ) . '" alt="' . get_bloginfo() . '"/></a>';
		}
		if ( get_option( 'whls_dark_logo' ) ) {
			$img_dark = '<a href="' . get_home_url() . '" id="whls__logo-dark" style="display:none;"><img src="' . get_option( 'whls_dark_logo' ) . '" alt="' . get_bloginfo() . '"/></a>';
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
					$img = '<a href="' . get_home_url() . '" id="whls__logo"><img src="' . get_the_post_thumbnail_url() . '" alt="' . $image_alt . '"/></a>';
				}
			}
		}
		$result .= $img;
		$result .= $img_dark;

		?>

		<style>
			body.iswpak_color_dark #whls__logo-dark { display: block !important; }
			body.iswpak_color_dark #whls__logo-dark img { filter: none; }
			body.iswpak_color_dark #whls__logo { display: none; }
		</style>

		<script>
			jQuery( document ).ready( function() {
				if ( window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ) {
					body.addClass( 'iswpak_color_dark' );
				}
			});
		</script>

		<?php
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
			'ls_logo_switcher',
			'',
			'',
			'ls_logo_switcher'
		);

		add_settings_field(
			'whls_default_logo',
			__( 'Default logo', 'holiday-logo-switcher' ),
			array( $this, 'default_logo_callback' ),
			'ls_logo_switcher',
			'ls_logo_switcher'
		);
		register_setting( 'ls_logo_switcher', 'whls_default_logo', array( 'type' => 'string' ) );

		add_settings_field(
			'whls_dark_logo',
			__( 'Dark mode logo', 'holiday-logo-switcher' ),
			array( $this, 'dark_logo_callback' ),
			'ls_logo_switcher',
			'ls_logo_switcher'
		);
		register_setting( 'ls_logo_switcher', 'whls_dark_logo', array( 'type' => 'string' ) );
	}

	/*
	* Callback functions for option page.
	*/
	function default_logo_callback() { include( dirname( __FILE__ ) . '/../views/opt_fields/default-logo.php' ); }
	function dark_logo_callback() { include( dirname( __FILE__ ) . '/../views/opt_fields/dark-logo.php' ); }

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
		delete_option( 'whls_default_logo' );
		delete_option( 'whls_dark_logo' );
	}
}