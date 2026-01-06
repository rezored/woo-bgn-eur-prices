<?php
/**
 * Plugin Name: Prices in BGN and EUR & Bulk Converter
 * Description: Dual currency display and secure bulk conversion tool for the BGN to EUR transition.
 * Version: 1.8.5
 * Author: rezored
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Text Domain: prices-in-bgn-and-eur
 */

namespace Prices_BGN_EUR;

defined('ABSPATH') || exit;

// Autoloader (Manual)
require_once plugin_dir_path(__FILE__) . 'includes/class-api-client.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-display.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-converter.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-admin.php';

use Prices_BGN_EUR\Includes\Display;
use Prices_BGN_EUR\Includes\Converter;
use Prices_BGN_EUR\Includes\Admin;

// Initialize
add_action('plugins_loaded', function() {
    new Display();
    new Converter();
    
    if (is_admin()) {
        new Admin();
    }
});
