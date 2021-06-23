<?php
/**
 * Plugin Name: Holiday Logo Switcher
 * Plugin URI: https://wpinclusion.com/plugins/logo-switcher
 * Description: Display a different logo depending on the holiday.
 * Author: WP inclusion
 * Version: 1.0.1
 * Author URI: https://wpinclusion.com
 * Text Domain: holiday-logo-switcher
 */

Namespace HolidayLogoSwitcher;

defined( 'ABSPATH' ) or die( 'No direct access call...' );


// When plugin is installed
function activate_plugin() {
	\HolidayLogoSwitcher\Includes\Classes\Holiday_Logo_Switcher::activate();
}

// When plugin is deactivated
function deactivate_plugin() {
	\HolidayLogoSwitcher\Includes\Classes\Holiday_Logo_Switcher::deactivate();
}

// When plugin is uninstalled
function uninstall_plugin() {
	\HolidayLogoSwitcher\Includes\Classes\Holiday_Logo_Switcher::uninstall();
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\activate_plugin' );
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\deactivate_plugin' );
register_uninstall_hook( __FILE__, __NAMESPACE__ . '\uninstall_plugin' );

require_once( 'includes/classes/holiday-logo-switcher.php' );

// Initialisation
$wpiatk = new \HolidayLogoSwitcher\Includes\Classes\Holiday_Logo_Switcher();
$wpiatk->run();
