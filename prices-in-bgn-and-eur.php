<?php

/**
 * Plugin Name: Prices in BGN and EUR
 * Description: Displays prices in BGN and EUR in WooCommerce using the fixed BNB exchange rate.
 * Version: 1.5.4
 * Author: rezored
 * Requires at least: 5.6
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: prices-in-bgn-and-eur
 */

namespace Prices_BGN_EUR\Front_End;

defined('ABSPATH') || exit;

class Multi_Currency
{
    public function __construct()
    {
        if (!\is_admin()) {
            // Traditional WooCommerce hooks
            add_filter('wc_price', [__CLASS__, 'display_price_in_multiple_currencies'], 10);
            add_filter('woocommerce_get_order_item_totals', [__CLASS__, 'add_rate_row_email'], 10, 2);
            add_action('woocommerce_cart_totals_after_order_total', [__CLASS__, 'show_cart_total_in_eur_and_note']);
            add_action('woocommerce_review_order_after_order_total', [__CLASS__, 'show_cart_total_in_eur_and_note']);

            // Additional hooks for better coverage
            add_filter('woocommerce_cart_item_price', [__CLASS__, 'display_price_in_multiple_currencies'], 10);
            add_filter('woocommerce_cart_item_subtotal', [__CLASS__, 'display_price_in_multiple_currencies'], 10);
            add_filter('woocommerce_cart_subtotal', [__CLASS__, 'display_price_in_multiple_currencies'], 10);
            add_filter('woocommerce_cart_total', [__CLASS__, 'display_price_in_multiple_currencies'], 10);
            
            // Product page specific hooks
            add_filter('woocommerce_get_price_html', [__CLASS__, 'display_price_in_multiple_currencies'], 10);
            add_filter('woocommerce_product_get_price_html', [__CLASS__, 'display_price_in_multiple_currencies'], 10);
            add_filter('woocommerce_variable_price_html', [__CLASS__, 'display_price_in_multiple_currencies'], 10);
            add_filter('woocommerce_variation_prices_price', [__CLASS__, 'display_price_in_multiple_currencies'], 10);
            add_filter('woocommerce_variation_prices_regular_price', [__CLASS__, 'display_price_in_multiple_currencies'], 10);
            add_filter('woocommerce_variation_prices_sale_price', [__CLASS__, 'display_price_in_multiple_currencies'], 10);
            
            // Product archive/category hooks
            add_filter('woocommerce_loop_product_price', [__CLASS__, 'display_price_in_multiple_currencies'], 10);

            // WooCommerce Blocks support
            add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_blocks_support_assets']);
        }
    }

    public static function get_eur_rate()
    {
        return apply_filters('prices_bgn_eur_rate', 1.95583);
    }

    public static function convert_to_eur($bgn)
    {
        $eur = floatval($bgn) / self::get_eur_rate();
        return number_format($eur, 2, wc_get_price_decimal_separator(), wc_get_price_thousand_separator());
    }

    // Debug method to test conversion
    public static function debug_conversion($bgn)
    {
        $rate = self::get_eur_rate();
        $eur = floatval($bgn) / $rate;
        return [
            'bgn' => $bgn,
            'rate' => $rate,
            'eur' => $eur,
            'formatted' => number_format($eur, 2, '.', ',')
        ];
    }

    private static function extract_numeric_price($price_html)
    {
        // Debug logging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('BGN-EUR Plugin: Extracting from - ' . $price_html);
        }
        
        // First try to extract price using a more specific pattern that handles both spaced and non-spaced currency symbols
        if (preg_match('/([0-9]+[.,]?[0-9]*)\s*(лв|ЛВ|лв\.|ЛВ\.|BGN|€|EUR)/i', $price_html, $matches)) {
            $clean = self::normalize_number_format($matches[1]);
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('BGN-EUR Plugin: Matched pattern 1 - ' . $matches[1] . ' -> ' . $clean);
            }
            return floatval($clean);
        }
        
        // Try pattern without space between number and currency
        if (preg_match('/([0-9]+[.,]?[0-9]*)(лв|ЛВ|лв\.|ЛВ\.|BGN|€|EUR)/i', $price_html, $matches)) {
            $clean = self::normalize_number_format($matches[1]);
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('BGN-EUR Plugin: Matched pattern 2 - ' . $matches[1] . ' -> ' . $clean);
            }
            return floatval($clean);
        }
        
        // Fallback to the original method
        $clean = preg_replace('/[^0-9.,]/', '', wp_strip_all_tags($price_html));
        $clean = self::normalize_number_format($clean);
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('BGN-EUR Plugin: Fallback - ' . $clean);
        }
        return floatval($clean);
    }

    private static function normalize_number_format($number_string)
    {
        // Debug logging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('BGN-EUR Plugin: Normalizing - ' . $number_string);
        }
        
        // Handle different number formats more intelligently
        $decimal_separator = wc_get_price_decimal_separator();
        $thousand_separator = wc_get_price_thousand_separator();
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('BGN-EUR Plugin: WooCommerce separators - decimal: ' . $decimal_separator . ', thousand: ' . $thousand_separator);
        }
        
        // For Bulgarian format: comma is decimal separator, no thousand separator typically
        // Check if this looks like a Bulgarian price (e.g., "269,00" or "1.234,56")
        if (preg_match('/^[0-9]+,[0-9]{2}$/', $number_string) || 
            preg_match('/^[0-9]+\.[0-9]{3},[0-9]{2}$/', $number_string)) {
            // This is Bulgarian format: comma is decimal separator
            $result = str_replace(',', '.', $number_string);
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('BGN-EUR Plugin: Bulgarian format detected - ' . $number_string . ' -> ' . $result);
            }
            return $result;
        }
        
        // If thousand separator is comma and decimal separator is dot
        if ($thousand_separator === ',' && $decimal_separator === '.') {
            // Remove thousand separators first, then ensure decimal is dot
            $number_string = str_replace(',', '', $number_string);
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('BGN-EUR Plugin: Thousand separator removal - ' . $number_string);
            }
            return $number_string;
        }
        
        // If thousand separator is dot and decimal separator is comma
        if ($thousand_separator === '.' && $decimal_separator === ',') {
            // Replace dots with empty string, then replace comma with dot
            $number_string = str_replace('.', '', $number_string);
            $number_string = str_replace(',', '.', $number_string);
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('BGN-EUR Plugin: Dot/comma conversion - ' . $number_string);
            }
            return $number_string;
        }
        
        // Default: just replace comma with dot (backward compatibility)
        $result = str_replace(',', '.', $number_string);
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('BGN-EUR Plugin: Default conversion - ' . $number_string . ' -> ' . $result);
        }
        return $result;
    }

    public static function display_price_in_multiple_currencies($price_html)
    {
        $current_currency = get_woocommerce_currency();

        if (strpos($price_html, 'amount-eur') !== false || $current_currency !== 'BGN') {
            return $price_html;
        }

        // Check if this is a price range (contains dash or en-dash)
        if (strpos($price_html, '–') !== false || strpos($price_html, '-') !== false) {
            return self::handle_price_range($price_html);
        }

        // Handle single price
        $price = self::extract_clean_price($price_html);
        if ($price <= 0) return $price_html;

        $eur = esc_html(self::convert_to_eur($price));
        // Check if EUR is already present to avoid duplication
        if (strpos($price_html, '€') === false) {
            $price_html .= ' <span class="woocommerce-Price-amount amount amount-eur">(' . $eur . ' €)</span>';
        }
        
        return $price_html;
    }

    // Debug function to show debug info on frontend
    public static function debug_price_extraction($price_html)
    {
        $debug_info = [];
        $debug_info[] = "Original HTML: " . htmlspecialchars($price_html);
        
        // Test extraction
        $price = self::extract_numeric_price($price_html);
        $debug_info[] = "Extracted price: " . $price;
        
        // Test conversion
        $eur = self::convert_to_eur($price);
        $debug_info[] = "EUR conversion: " . $eur;
        
        return '<div style="background: #f0f0f0; padding: 10px; margin: 10px 0; border: 1px solid #ccc; font-family: monospace; font-size: 12px;">' . 
               '<strong>DEBUG INFO:</strong><br>' . 
               implode('<br>', $debug_info) . 
               '</div>';
    }

    private static function handle_price_range($price_html)
    {
        // Split the range into two parts using both dash types
        $parts = preg_split('/[–\-]/', $price_html);
        if (count($parts) !== 2) {
            return $price_html; // Not a valid range, return as-is
        }

        $first_part = trim($parts[0]);
        $second_part = trim($parts[1]);

        // Process each part individually to get clean prices
        $first_clean_price = self::extract_clean_price($first_part);
        $second_clean_price = self::extract_clean_price($second_part);

        if ($first_clean_price <= 0 || $second_clean_price <= 0) {
            return $price_html; // Invalid prices, return as-is
        }

        // Convert both prices to EUR
        $first_eur = esc_html(self::convert_to_eur($first_clean_price));
        $second_eur = esc_html(self::convert_to_eur($second_clean_price));

        // Reconstruct the range with EUR conversions
        $new_price_html = $first_part . ' (' . $first_eur . ' €) – ' . $second_part . ' (' . $second_eur . ' €)';
        
        return $new_price_html;
    }

    private static function extract_clean_price($price_text)
    {
        // Remove HTML tags and get clean text
        $clean_text = wp_strip_all_tags($price_text);
        
        // Look for Bulgarian price pattern: number,comma,number followed by currency
        if (preg_match('/([0-9]+,[0-9]{2})\s*(лв|ЛВ|лв\.|ЛВ\.|BGN)/i', $clean_text, $matches)) {
            // This is Bulgarian format: comma is decimal separator
            $price_str = str_replace(',', '.', $matches[1]);
            return floatval($price_str);
        }
        
        // Fallback: try to extract any number with comma
        if (preg_match('/([0-9]+,[0-9]+)/', $clean_text, $matches)) {
            $price_str = str_replace(',', '.', $matches[1]);
            return floatval($price_str);
        }
        
        // Final fallback: extract any number
        if (preg_match('/([0-9]+)/', $clean_text, $matches)) {
            return floatval($matches[1]);
        }
        
        return 0;
    }

    public static function add_rate_row_email($total_rows, $order)
    {
        $total_rows['used_rate'] = [
            'label' => __('БНБ фиксиран курс:', 'prices-in-bgn-and-eur'),
            'value' => '1 € = 1.95583 BGN'
        ];
        return $total_rows;
    }

    public static function show_cart_total_in_eur_and_note()
    {
        if (get_woocommerce_currency() !== 'BGN') return;

        echo '<tr class="eur-note">
            <th></th>
            <td style="font-size:12px;color:#777;padding-top:5px;border-top:none;">
                <em>' . esc_html__('Сумата в евро се получава чрез конвертиране на цената по фиксирания обменен курс на БНБ:', 'prices-in-bgn-and-eur') . ' <br>1 EUR = 1.95583 BGN</em>
            </td>
        </tr>';
    }

    public static function enqueue_blocks_support_assets()
    {
        if (get_woocommerce_currency() !== 'BGN') return;

        if (!is_cart() && !is_checkout() && !is_shop() && !is_product()) return;

        // Enqueue CSS
        wp_enqueue_style(
            'prices-bgn-eur-blocks',
            plugin_dir_url(__FILE__) . 'assets/css/blocks-support.css',
            [],
            '1.5.2'
        );

        // Enqueue JavaScript
        wp_enqueue_script(
            'prices-bgn-eur-blocks',
            plugin_dir_url(__FILE__) . 'assets/js/blocks-support.js',
            ['jquery'],
            '1.5.2',
            true
        );

        // Localize script with data
        wp_localize_script(
            'prices-bgn-eur-blocks',
            'pricesBgnEurData',
            [
                'eurRate' => self::get_eur_rate(),
                'disclaimerText' => __('Сумата в евро се получава чрез конвертиране на цената по фиксирания обменен курс на БНБ:', 'prices-in-bgn-and-eur'),
                'rateText' => '1 EUR = 1.95583 BGN'
            ]
        );
    }
}

// Initialize
new \Prices_BGN_EUR\Front_End\Multi_Currency();

add_action('admin_head', function () {
    // Replace 'prices-in-bgn-and-eur' with your plugin's folder slug
    $plugin_slug = 'prices-in-bgn-and-eur';

    // URL to your custom icon
    $icon_url = plugin_dir_url(__FILE__) . 'assets/icon_new.png';

    echo '<style>
        tr[data-slug="' . esc_attr($plugin_slug) . '"] .plugin-icon {
            background-image: url("' . esc_url($icon_url) . '") !important;
            background-size: cover !important;
            background-position: center !important;
        }
    </style>';
});

// Admin menu & settings page
add_action('admin_menu', function () {
    add_options_page(
        'Prices in BGN and EUR',
        'Prices in BGN and EUR',
        'manage_options',
        'prices-bgn-eur-settings',
        function () { ?>
<div class="wrap">
    <h1><?php esc_html_e('Prices in BGN and EUR for WooCommerce', 'prices-in-bgn-and-eur'); ?></h1>
    <p><?php esc_html_e('Thank you for using the plugin!', 'prices-in-bgn-and-eur'); ?></p>
    <p><strong><?php esc_html_e('Version 1.5.2:', 'prices-in-bgn-and-eur'); ?></strong>
        <?php esc_html_e('FIXED: Price ranges now properly convert both prices. "269,00 лв – 559,00 лв" now shows both EUR conversions correctly.', 'prices-in-bgn-and-eur'); ?></p>
    <p><?php esc_html_e('If you would like to support me, you can do so here:', 'prices-in-bgn-and-eur'); ?>
        <a href="<?php echo esc_url('https://coff.ee/rezored'); ?>" target="_blank" class="button button-primary">☕
            <?php esc_html_e('Support me', 'prices-in-bgn-and-eur'); ?></a>
    </p>
    <hr>
    <h2><?php esc_html_e('Settings (in the future)', 'prices-in-bgn-and-eur'); ?></h2>
    <p><?php esc_html_e('Expect settings for display, formats and more.', 'prices-in-bgn-and-eur'); ?></p>
</div>
<?php }
    );
});