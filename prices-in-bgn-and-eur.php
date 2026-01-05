<?php
/**
 * Plugin Name: Prices in BGN and EUR
 * Description: Displays prices in BGN and EUR in WooCommerce using the fixed BNB exchange rate (appends EUR once per number).
 * Version: 1.7.1
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
            /**
             * IMPORTANT:
             * We only hook into wc_price so each numeric price gets EUR once.
             * (HTML-level hooks like woocommerce_get_price_html would cause duplication.)
             */
            add_filter('wc_price', [__CLASS__, 'display_price_in_multiple_currencies'], 10);

            // Emails / totals note
            add_filter('woocommerce_get_order_item_totals', [__CLASS__, 'add_rate_row_email'], 10, 2);
            add_action('woocommerce_cart_totals_after_order_total', [__CLASS__, 'show_cart_total_in_eur_and_note']);
            add_action('woocommerce_review_order_after_order_total', [__CLASS__, 'show_cart_total_in_eur_and_note']);

            // WooCommerce Blocks support (just CSS/JS, no duplication)
            add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_blocks_support_assets']);
        }
    }

    public static function get_rate()
    {
        return apply_filters('prices_bgn_eur_rate', 1.95583);
    }

    public static function convert_price($amount, $from_currency)
    {
        $rate = self::get_rate();
        if ($from_currency === 'BGN') {
            return floatval($amount) / $rate; // Convert to EUR
        } elseif ($from_currency === 'EUR') {
            return floatval($amount) * $rate; // Convert to BGN
        }
        return $amount;
    }

    private static function extract_numeric_price($price_html)
    {
        // Debug logging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('BGN-EUR Plugin: Extracting from - ' . $price_html);
        }

        // Try to grab a number next to a currency
        if (preg_match('/([0-9]+[.,]?[0-9]*)\s*(лв|ЛВ|лв\.|ЛВ\.|BGN|€|EUR)/i', $price_html, $m)) {
            $clean = self::normalize_number_format($m[1]);
            return floatval($clean);
        }

        // Fallback: strip everything but digits and separators
        $clean = preg_replace('/[^0-9.,]/', '', wp_strip_all_tags($price_html));
        $clean = self::normalize_number_format($clean);
        return floatval($clean);
    }

    private static function normalize_number_format($number_string)
    {
        $decimal_separator  = wc_get_price_decimal_separator();
        $thousand_separator = wc_get_price_thousand_separator();

        if ($thousand_separator === ',' && $decimal_separator === '.') {
            return str_replace(',', '', $number_string); // remove thousands, keep dot decimals
        }

        if ($thousand_separator === '.' && $decimal_separator === ',') {
            $number_string = str_replace('.', '', $number_string); // remove thousands
            return str_replace(',', '.', $number_string);          // make decimals dot
        }

        // Default
        return str_replace(',', '.', $number_string);
    }

    public static function display_price_in_multiple_currencies($price_html)
    {
        // Check if plugin is active in settings (default true)
        if (get_option('prices_bgn_eur_active', 'yes') !== 'yes') {
            return $price_html;
        }

        $current_currency = get_woocommerce_currency();

        // Only support BGN and EUR
        if (!in_array($current_currency, ['BGN', 'EUR'])) {
            return $price_html;
        }

        // Avoid infinite loops or double formatting
        if (
            strpos($price_html, 'amount-secondary') !== false || 
            ($current_currency === 'BGN' && strpos($price_html, '€') !== false) ||
            ($current_currency === 'EUR' && (strpos($price_html, 'лв') !== false || strpos($price_html, 'BGN') !== false))
        ) {
            return $price_html;
        }

        // Extract numeric
        $price = self::extract_numeric_price($price_html);
        if ($price <= 0) {
            return $price_html;
        }

        $converted_amount = self::convert_price($price, $current_currency);
        
        $formatted_converted = number_format(
            $converted_amount, 
            wc_get_price_decimals(), 
            wc_get_price_decimal_separator(), 
            wc_get_price_thousand_separator()
        );

        // Determine symbols
        if ($current_currency === 'BGN') {
            $secondary_display = $formatted_converted . ' €';
        } else {
            // Base is EUR, show BGN
            $secondary_display = $formatted_converted . ' лв.'; 
        }

        // Simple Output: Main Price (Secondary Price)
        return $price_html . ' <span class="amount-secondary" style="font-size:0.9em;color:#777;margin-left:5px;">(' . $secondary_display . ')</span>';
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
        if (get_option('prices_bgn_eur_active', 'yes') !== 'yes') return;

        $currency = get_woocommerce_currency();
        if (!in_array($currency, ['BGN', 'EUR'])) return;

        // Conditionals for pages...
        if (!is_cart() && !is_checkout() && !is_shop() && !is_product() && !is_product_category() && !is_product_tag()) return;

        // Enqueue CSS
        wp_enqueue_style('prices-bgn-eur-blocks', plugin_dir_url(__FILE__) . 'assets/css/blocks-support.css', [], '1.7.1');

        // Enqueue JavaScript
        wp_enqueue_script('prices-bgn-eur-blocks', plugin_dir_url(__FILE__) . 'assets/js/blocks-support.js', ['jquery'], '1.7.1', true);

        // Localize
        wp_localize_script(
            'prices-bgn-eur-blocks',
            'pricesBgnEurData',
            [
                'rate'           => self::get_rate(),
                'currency'       => $currency,
                'disclaimerText' => __('Цените се изчисляват по фиксирания курс на БНБ:', 'prices-in-bgn-and-eur'),
                'rateText'       => '1 EUR = 1.95583 BGN'
            ]
        );
    }
}

// Initialize
new \Prices_BGN_EUR\Front_End\Multi_Currency();

/**
 * Admin: plugin row icon
 */
add_action('admin_head', function () {
    $plugin_slug = 'prices-in-bgn-and-eur';
    $icon_url    = plugin_dir_url(__FILE__) . 'assets/icon_new.png';

    echo '<style>
        tr[data-slug="' . esc_attr($plugin_slug) . '"] .plugin-icon {
            background-image: url("' . esc_url($icon_url) . '") !important;
            background-size: cover !important;
            background-position: center !important;
        }
    </style>';
});

/**
 * Admin: Add setup/support links to plugins page
 */
add_filter('plugin_action_links_prices-in-bgn-and-eur/prices-in-bgn-and-eur.php', function ($links) {
    $settings_link = '<a href="options-general.php?page=prices-bgn-eur-settings">' . __('Settings', 'prices-in-bgn-and-eur') . '</a>';
    $support_link  = '<a href="https://coff.ee/rezored" target="_blank" style="color:#d63638;font-weight:bold;">☕ ' . __('Support me', 'prices-in-bgn-and-eur') . '</a>';
    
    array_unshift($links, $settings_link);
    $links[] = $support_link;
    
    return $links;
});

/**
 * (Optional) Settings page shell
 */
// Register Setting
add_action('admin_init', function() {
    register_setting('prices_bgn_eur_options', 'prices_bgn_eur_active');
});

/**
 * Admin: Settings Page
 */
add_action('admin_menu', function () {
    add_options_page(
        'Prices in BGN and EUR',
        'Prices in BGN and EUR',
        'manage_options',
        'prices-bgn-eur-settings',
        function () { 
            ?>
            <div class="wrap">
                <h1><?php esc_html_e('Prices in BGN and EUR', 'prices-in-bgn-and-eur'); ?></h1>
                
                <form method="post" action="options.php">
                    <?php settings_fields('prices_bgn_eur_options'); ?>
                    <?php do_settings_sections('prices_bgn_eur_options'); ?>
                    
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e('Enable Dual Currency Display', 'prices-in-bgn-and-eur'); ?></th>
                            <td>
                                <input type="checkbox" name="prices_bgn_eur_active" value="yes" <?php checked(get_option('prices_bgn_eur_active', 'yes'), 'yes'); ?> />
                                <p class="description"><?php esc_html_e('If unchecked, the plugin will not modify any prices.', 'prices-in-bgn-and-eur'); ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <?php submit_button(); ?>
                </form>

                <hr>
                
                <h3><?php esc_html_e('Information', 'prices-in-bgn-and-eur'); ?></h3>
                <p>
                    <?php esc_html_e('This plugin automatically detects your WooCommerce currency.', 'prices-in-bgn-and-eur'); ?><br>
                    <strong><?php esc_html_e('If your currency is BGN:', 'prices-in-bgn-and-eur'); ?></strong> <?php esc_html_e('It will display ~ EUR prices (divide by 1.95583).', 'prices-in-bgn-and-eur'); ?><br>
                    <strong><?php esc_html_e('If your currency is EUR:', 'prices-in-bgn-and-eur'); ?></strong> <?php esc_html_e('It will display ~ BGN prices (multiply by 1.95583).', 'prices-in-bgn-and-eur'); ?>
                </p>

                <p>
                    <a href="<?php echo esc_url('https://coff.ee/rezored'); ?>" target="_blank" class="button">☕ <?php esc_html_e('Support the developer', 'prices-in-bgn-and-eur'); ?></a>
                </p>
            </div>
            <?php 
        }
    );
});
