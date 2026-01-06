<?php
namespace Prices_BGN_EUR\Includes;

defined('ABSPATH') || exit;

class Display {
    
    public function __construct() {
        if (!is_admin()) {
            add_filter('wc_price', [$this, 'display_price_in_multiple_currencies'], 10);
            add_filter('woocommerce_get_order_item_totals', [$this, 'add_rate_row_email'], 10, 2);
            add_action('woocommerce_cart_totals_after_order_total', [$this, 'show_cart_total_in_eur_and_note']);
            add_action('woocommerce_review_order_after_order_total', [$this, 'show_cart_total_in_eur_and_note']);
            add_action('wp_enqueue_scripts', [$this, 'enqueue_blocks_support_assets']);
        }
    }

    public static function get_rate() {
        return apply_filters('prices_bgn_eur_rate', 1.95583);
    }

    public static function convert_price($amount, $from_currency) {
        $rate = self::get_rate();
        if ($from_currency === 'BGN') return floatval($amount) / $rate;
        if ($from_currency === 'EUR') return floatval($amount) * $rate;
        return $amount;
    }

    private function extract_numeric_price($price_html) {
        if (preg_match('/([0-9]+[.,]?[0-9]*)\s*(лв|ЛВ|лв\.|ЛВ\.|BGN|€|EUR)/i', $price_html, $m)) {
            return floatval(str_replace(',', '.', $m[1])); // Improved simple normalization
        }
        return floatval(str_replace(',', '.', preg_replace('/[^0-9.,]/', '', wp_strip_all_tags($price_html))));
    }

    public function display_price_in_multiple_currencies($price_html) {
        if (get_option('prices_bgn_eur_active', 'yes') !== 'yes') return $price_html;

        $currency = get_woocommerce_currency();
        if (!in_array($currency, ['BGN', 'EUR'])) return $price_html;

        // Prevention of double display
        if (strpos($price_html, 'amount-secondary') !== false) return $price_html;

        $price = $this->extract_numeric_price($price_html);
        if ($price <= 0) return $price_html;

        $converted = self::convert_price($price, $currency);
        $fmt = number_format($converted, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator());

        $secondary = ($currency === 'BGN') ? $fmt . ' €' : $fmt . ' лв.';

        // Goal: Always display EUR first, BGN second. i.e. "XX € (YY лв.)"
        
        // CASE 1: Store is BGN. $secondary is EUR. $price_html is BGN.
        // We want Secondary (Original).
        if ($currency === 'BGN') {
             return '<span class="amount-eu">' . $secondary . '</span> <span class="amount-bgn" style="font-size:0.9em; color:#777; margin-left:5px;">(' . strip_tags($price_html) . ')</span>';
        }

        // CASE 2: Store is EUR. $secondary is BGN. $price_html is EUR.
        // We want Original (Secondary).
        return '<span class="amount-eu">' . strip_tags($price_html) . '</span> <span class="amount-bgn" style="font-size:0.9em; color:#777; margin-left:5px;">(' . $secondary . ')</span>';
    }

    public function add_rate_row_email($total_rows, $order) {
        $total_rows['used_rate'] = [
            'label' => __('БНБ фиксиран курс:', 'prices-in-bgn-and-eur'),
            'value' => '1 € = 1.95583 BGN'
        ];
        return $total_rows;
    }

    public function show_cart_total_in_eur_and_note() {
        if (get_woocommerce_currency() !== 'BGN') return;
        echo '<tr class="eur-note"><th></th><td style="font-size:12px;color:#777;"><em>' . esc_html__('1 EUR = 1.95583 BGN', 'prices-in-bgn-and-eur') . '</em></td></tr>';
    }

    public function enqueue_blocks_support_assets() {
        if (get_option('prices_bgn_eur_active', 'yes') !== 'yes') return;
        // Enqueue assets logic (simplified for brevity, assume assets exist)
        wp_enqueue_style('prices-bgn-eur-blocks', plugin_dir_url(dirname(__FILE__)) . 'assets/css/blocks-support.css', [], '1.8.5');
    }
}
