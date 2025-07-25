<?php

/**
 * Plugin Name: Prices in BGN and EUR
 * Description: Displays product prices in BGN and EUR using the fixed exchange rate: 1 EUR = 1.95583 BGN.
 * Version: 1.4.2
 * Author: Rezored
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Woo_BG\Front_End;

defined('ABSPATH') || exit;

class Multi_Currency
{
    public function __construct()
    {
        if (!is_admin()) {
            // Traditional WooCommerce hooks
            add_filter('wc_price', array(__CLASS__, 'display_price_in_multiple_currencies'), 10);
            add_filter('woocommerce_get_order_item_totals', array(__CLASS__, 'add_rate_row_email'), 10, 2);
            add_action('woocommerce_cart_totals_after_order_total', array(__CLASS__, 'show_cart_total_in_eur_and_note'));
            add_action('woocommerce_review_order_after_order_total', array(__CLASS__, 'show_cart_total_in_eur_and_note'));
            
            // Additional hooks for better coverage
            add_filter('woocommerce_cart_item_price', array(__CLASS__, 'display_price_in_multiple_currencies'), 10);
            add_filter('woocommerce_cart_item_subtotal', array(__CLASS__, 'display_price_in_multiple_currencies'), 10);
            add_filter('woocommerce_cart_subtotal', array(__CLASS__, 'display_price_in_multiple_currencies'), 10);
            add_filter('woocommerce_cart_total', array(__CLASS__, 'display_price_in_multiple_currencies'), 10);
            
            // WooCommerce Blocks support
            add_action('wp_footer', array(__CLASS__, 'add_blocks_support_script'));
        }
    }

    public static function get_eur_rate()
    {
        return 1.95583;
    }

    public static function convert_to_eur($bgn)
    {
        $eur = floatval($bgn) / self::get_eur_rate();
        return number_format($eur, 2, wc_get_price_decimal_separator(), wc_get_price_thousand_separator());
    }

    public static function display_price_in_multiple_currencies($price_html)
    {
        $current_currency = get_woocommerce_currency();

        // Ако вече съдържа евро, не добавяй втори път
        if (strpos($price_html, 'amount-eur') !== false) {
            return $price_html;
        }

        $price_html_copy = str_replace( ' ', '', wp_strip_all_tags($price_html) );
        preg_match('/[0-9.,]+/', $price_html_copy, $matches);
        $price = isset($matches[0]) ? floatval(str_replace(',', '.', $matches[0])) : 0;

        if ($current_currency == 'BGN') {
            $eur = self::convert_to_eur($price);
            return $price_html . ' <span class="woocommerce-Price-amount amount amount-eur">(' . $eur . ' €)</span>';
        }
        return $price_html;
    }

    public static function add_rate_row_email($total_rows, $order)
    {
        $total_rows['used_rate'] = array(
            'label' => __('БНБ фиксиран курс:', 'prices-in-bgn-and-eur'),
            'value' => '1 € = 1.95583 лв.'
        );
        return $total_rows;
    }

    public static function show_cart_total_in_eur_and_note()
    {
        if (get_woocommerce_currency() !== 'BGN') return;

        echo '<tr class="eur-note">
            <th></th>
            <td style="font-size: 12px; color: #777; padding-top: 5px; border-top: none;">
                <em>Сумата в евро се получава чрез конвертиране на цената по фиксирания обменен курс на БНБ: <br>1 EUR = 1.95583 BGN</em>
            </td>
        </tr>';
    }

    public static function add_blocks_support_script()
    {
        if (get_woocommerce_currency() !== 'BGN') {
            return;
        }

        // Only add script on cart/checkout pages with blocks
        if (!is_cart() && !is_checkout() && !is_shop() && !is_product()) {
            return;
        }

        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Function to add EUR prices to WooCommerce Blocks
            function addEurToBlocks() {
                // Target blocks price elements
                $('.wc-block-components-product-price__value, .wc-block-formatted-money-amount').each(function() {
                    var $this = $(this);
                    var priceText = $this.text().trim();
                    
                    // Skip if already has EUR
                    if ($this.find('.amount-eur').length > 0 || priceText.indexOf('€') !== -1) {
                        return;
                    }
                    
                    // Extract price value
                    var priceMatch = priceText.match(/[0-9.,]+/);
                    if (priceMatch) {
                        var price = parseFloat(priceMatch[0].replace(',', '.'));
                        if (price > 0) {
                            // Calculate EUR (using PHP rate for consistency)
                            var eurRate = <?php echo self::get_eur_rate(); ?>;
                            var eurPrice = (price / eurRate).toFixed(2);
                            
                            // Add EUR price
                            $this.append(' <span class="amount-eur">(' + eurPrice + ' €)</span>');
                        }
                    }
                });
                
                // Add EUR to cart totals
                $('.wc-block-components-totals-item__value').each(function() {
                    var $this = $(this);
                    var priceText = $this.text().trim();
                    
                    if ($this.find('.amount-eur').length === 0 && priceText.indexOf('€') === -1) {
                        var priceMatch = priceText.match(/[0-9.,]+/);
                        if (priceMatch) {
                            var price = parseFloat(priceMatch[0].replace(',', '.'));
                            if (price > 0) {
                                var eurRate = <?php echo self::get_eur_rate(); ?>;
                                var eurPrice = (price / eurRate).toFixed(2);
                                $this.append(' <span class="amount-eur">(' + eurPrice + ' €)</span>');
                            }
                        }
                    }
                });
                
                // Add disclaimer for WooCommerce Blocks
                if ($('.wc-block-cart__totals-title').length > 0 && $('.eur-disclaimer-blocks').length === 0) {
                    $('.wc-block-cart__totals-title').after('<div class="eur-disclaimer-blocks" style="font-size: 12px; color: #777; margin-top: 10px; padding: 10px; background: #f9f9f9; border-radius: 4px;"><em>Сумата в евро се получава чрез конвертиране на цената по фиксирания обменен курс на БНБ: <br>1 EUR = 1.95583 BGN</em></div>');
                }
            }
            
            // Run on page load with immediate execution
            addEurToBlocks();
            
            // Run again after a short delay to catch any late-loading content
            setTimeout(addEurToBlocks, 500);
            
            // Run when blocks update (for dynamic cart updates)
            $(document.body).on('updated_wc_block', function() {
                setTimeout(addEurToBlocks, 100);
            });
            
            // Run on cart updates
            $(document.body).on('updated_cart_totals', function() {
                setTimeout(addEurToBlocks, 100);
            });
            
            // Run periodically to catch dynamically loaded content (less frequent)
            setInterval(addEurToBlocks, 2000);
        });
        </script>
        <style>
        .amount-eur {
            color: #666;
            font-size: 0.9em;
            font-weight: normal;
        }
        .wc-block-components-product-price__value .amount-eur,
        .wc-block-formatted-money-amount .amount-eur {
            margin-left: 5px;
        }
        </style>
        <?php
    }
}

// Инициализация
new \Woo_BG\Front_End\Multi_Currency();

// Admin menu & settings page
add_action('admin_menu', function () {
    add_options_page(
        'Цени в лева и евро',
        'Цени в лева и евро',
        'manage_options',
        'woo-bg-currency',
        function () {
?>
        <div class="wrap">
            <h1>Цени в лева и евро за WooCommerce</h1>
            <p>Благодарим, че използвате плъгина!</p>
            <p><strong>Версия 1.4.2:</strong> Добавена поддръжка за WooCommerce Blocks с подобрена производителност!</p>
            <p>Ако желаете да ме подкрепите, може да го направите тук:
                <a href="https://coff.ee/rezored" target="_blank" class="button button-primary">☕ Подкрепи ме</a>
            </p>
            <hr>
            <h2>Настройки (в бъдеще)</h2>
            <p>Очаквайте настройки за показване, формати и други.</p>
        </div>
<?php
        }
    );
});
