<?php

/**
 * Plugin Name: Prices in BGN and EUR
 * Description: Displays product prices in BGN and EUR using the fixed exchange rate: 1 EUR = 1.95583 BGN.
 * Version: 1.3.8
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
            add_filter('wc_price', array(__CLASS__, 'display_price_in_multiple_currencies'), 10);
            add_filter('woocommerce_get_order_item_totals', array(__CLASS__, 'add_rate_row_email'), 10, 2);
            add_action('woocommerce_cart_totals_after_order_total', array(__CLASS__, 'show_cart_total_in_eur_and_note'));
            add_action('woocommerce_review_order_after_order_total', array(__CLASS__, 'show_cart_total_in_eur_and_note'));
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
