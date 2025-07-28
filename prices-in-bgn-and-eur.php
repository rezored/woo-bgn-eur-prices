<?php
/**
 * Plugin Name: Prices in BGN and EUR
 * Description: Displays prices in BGN and EUR in WooCommerce using the fixed BNB exchange rate.
 * Version: 1.4.5
 * Author: rezored
 * Requires at least: 5.6
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: prices-in-bgn-and-eur
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
            add_action('wp_footer', [__CLASS__, 'add_blocks_support_script']);
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

    public static function add_blocks_support_script() {
        if (get_woocommerce_currency() !== 'BGN') return;

        if (!is_cart() && !is_checkout() && !is_shop() && !is_product()) return;

        ?>
        <script type="text/javascript">
            jQuery(function($){
                function addEurToBlocks() {
                    var eurRate = <?php echo esc_js(self::get_eur_rate()); ?>;

                    function appendEur($el) {
                        if ($el.find('.amount-eur').length || $el.text().includes('€')) return;
                        var match = $el.text().match(/[0-9.,]+/);
                        if (!match) return;
                        var price = parseFloat(match[0].replace(',', '.'));
                        if (price > 0) {
                            var eurPrice = (price / eurRate).toFixed(2);
                            $el.append(' <span class="amount-eur">(' + eurPrice + ' €)</span>');
                        }
                    }

                    $('.wc-block-components-product-price__value, .wc-block-formatted-money-amount, .wc-block-components-totals-item__value').each(function(){
                        appendEur($(this));
                    });

                    if ($('.wc-block-cart__totals-title').length && !$('.eur-disclaimer-blocks').length) {
                        $('.wc-block-cart__totals-title').after(
                            '<div class="eur-disclaimer-blocks" style="font-size:12px;color:#777;margin-top:10px;padding:10px;background:#f9f9f9;border-radius:4px;">' +
                            '<em><?php echo esc_js(__('Сумата в евро се получава чрез конвертиране на цената по фиксирания обменен курс на БНБ:', 'prices-in-bgn-and-eur')); ?> <br>1 EUR = 1.95583 BGN</em>' +
                            '</div>'
                        );
                    }
                }

                addEurToBlocks();
                setTimeout(addEurToBlocks, 500);

                $(document.body).on('updated_wc_block updated_cart_totals', function() {
                    setTimeout(addEurToBlocks, 100);
                });

                new MutationObserver(addEurToBlocks).observe(document.body, {childList: true, subtree: true});
            });
        </script>
        <style>
            .amount-eur { color:#666; font-size:0.9em; font-weight:normal; }
            .wc-block-components-product-price__value .amount-eur,
            .wc-block-formatted-money-amount .amount-eur { margin-left:5px; }
        </style>
        <?php
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
                <p><strong><?php esc_html_e('Version 1.4.4:', 'prices-in-bgn-and-eur'); ?></strong> <?php esc_html_e('Improved security, formatting and WooCommerce Blocks support.', 'prices-in-bgn-and-eur'); ?></p>
                <p><?php esc_html_e('If you would like to support me, you can do so here:', 'prices-in-bgn-and-eur'); ?>
                    <a href="<?php echo esc_url('https://coff.ee/rezored'); ?>" target="_blank" class="button button-primary">☕ <?php esc_html_e('Support me', 'prices-in-bgn-and-eur'); ?></a>
                </p>
                <hr>
                <h2><?php esc_html_e('Settings (in the future)', 'prices-in-bgn-and-eur'); ?></h2>
                <p><?php esc_html_e('Expect settings for display, formats and more.', 'prices-in-bgn-and-eur'); ?></p>
            </div>
        <?php }
    );
});
