<?php
/**
 * Plugin Name: Prices in BGN and EUR
 * Description: Displays prices in BGN and EUR in WooCommerce using the fixed BNB exchange rate.
 * Version: 1.4.6
 * Author: rezored
 * Requires at least: 5.6
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: prices-in-bgn-and-eur
 * Icon: icon-128x128.png
 */

namespace Prices_BGN_EUR\Front_End;

defined('ABSPATH') || exit;

class Multi_Currency {
    public function __construct() {
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

            // WooCommerce Blocks support
            add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_blocks_support_assets']);
        }
    }

    public static function get_eur_rate() {
        return apply_filters('prices_bgn_eur_rate', 1.95583);
    }

    public static function convert_to_eur($bgn) {
        $eur = floatval($bgn) / self::get_eur_rate();
        return number_format($eur, 2, wc_get_price_decimal_separator(), wc_get_price_thousand_separator());
    }

    private static function extract_numeric_price($price_html) {
        $clean = preg_replace('/[^0-9.,]/', '', wp_strip_all_tags($price_html));
        $clean = str_replace(',', '.', $clean);
        return floatval($clean);
    }

    public static function display_price_in_multiple_currencies($price_html) {
        $current_currency = get_woocommerce_currency();

        if (strpos($price_html, 'amount-eur') !== false || $current_currency !== 'BGN') {
            return $price_html;
        }

        $price = self::extract_numeric_price($price_html);
        if ($price <= 0) return $price_html;

        $eur = esc_html(self::convert_to_eur($price));
        $price_html .= ' <span class="woocommerce-Price-amount amount amount-eur">(' . $eur . ' €)</span>';
        return $price_html;
    }

    public static function add_rate_row_email($total_rows, $order) {
        $total_rows['used_rate'] = [
            'label' => __('БНБ фиксиран курс:', 'prices-in-bgn-and-eur'),
            'value' => '1 € = 1.95583 BGN'
        ];
        return $total_rows;
    }

    public static function show_cart_total_in_eur_and_note() {
        if (get_woocommerce_currency() !== 'BGN') return;

        echo '<tr class="eur-note">
            <th></th>
            <td style="font-size:12px;color:#777;padding-top:5px;border-top:none;">
                <em>' . esc_html__('Сумата в евро се получава чрез конвертиране на цената по фиксирания обменен курс на БНБ:', 'prices-in-bgn-and-eur') . ' <br>1 EUR = 1.95583 BGN</em>
            </td>
        </tr>';
    }

    public static function enqueue_blocks_support_assets() {
        if (get_woocommerce_currency() !== 'BGN') return;

        if (!is_cart() && !is_checkout() && !is_shop() && !is_product()) return;

        // Enqueue CSS
        wp_enqueue_style(
            'prices-bgn-eur-blocks',
            plugin_dir_url(__FILE__) . 'assets/css/blocks-support.css',
            [],
            '1.4.6'
        );

        // Enqueue JavaScript
        wp_enqueue_script(
            'prices-bgn-eur-blocks',
            plugin_dir_url(__FILE__) . 'assets/js/blocks-support.js',
            ['jquery'],
            '1.4.6',
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
    <p><strong><?php esc_html_e('Version 1.4.6:', 'prices-in-bgn-and-eur'); ?></strong>
        <?php esc_html_e('Fixed WordPress enqueue compliance and improved security.', 'prices-in-bgn-and-eur'); ?></p>
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