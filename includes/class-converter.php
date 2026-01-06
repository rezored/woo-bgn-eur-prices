<?php
namespace Prices_BGN_EUR\Includes;

defined('ABSPATH') || exit;

class Converter
{

    public function __construct()
    {
        add_action('wp_ajax_prices_bgn_eur_convert_selected', [$this, 'handle_ajax_conversion']);
    }

    public function handle_ajax_conversion()
    {
        check_ajax_referer('prices_bgn_eur_convert_nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }

        $ids = isset($_POST['product_ids']) ? (array) $_POST['product_ids'] : [];
        $is_global = isset($_POST['select_all_global']) && $_POST['select_all_global'] === 'true';
        $rule = isset($_POST['rounding_rule']) ? sanitize_text_field($_POST['rounding_rule']) : 'math';
        $key = get_option('pbe_license_key', '');

        if ($is_global) {
            // Fetch ALL product IDs
            $args = [
                'limit' => -1,
                'return' => 'ids',
                'status' => 'publish', // or any status? 
            ];
            // Use wc_get_products if available, or direct DB query for speed if huge. 
            // wc_get_products is safer.
            $ids = wc_get_products($args);
        }

        if (empty($ids)) {
            wp_send_json_error(['message' => 'No products selected']);
        }

        // Prepare items for API
        $items_payload = [];
        foreach ($ids as $pid) {
            $product = wc_get_product($pid);
            if ($product) {
                // Handle Variable Parents differently? 
                // Creating a simplified flat list for API.
                $items_payload[] = [
                    'id' => $pid,
                    'regular_price' => $product->get_regular_price(),
                    'sale_price' => $product->get_sale_price()
                ];

                // If variable, we should really send children too. 
                // For this refactor, we stick to the provided IDs logic.
                if ($product->is_type('variable')) {
                    foreach ($product->get_children() as $child_id) {
                        $child = wc_get_product($child_id);
                        if ($child) {
                            $items_payload[] = [
                                'id' => $child_id,
                                'regular_price' => $child->get_regular_price(),
                                'sale_price' => $child->get_sale_price()
                            ];
                        }
                    }
                }
            }
        }

        // CALL API
        $result = API_Client::convert_batch($items_payload, $key, $rule);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        // Apply Results
        $count = 0;
        if (isset($result['results']) && is_array($result['results'])) {
            foreach ($result['results'] as $res) {
                $pid = $res['id'];
                $changed = false;

                if (isset($res['new_regular']) && $res['new_regular'] !== null) {
                    update_post_meta($pid, '_regular_price', $res['new_regular']);
                    $changed = true;
                }
                if (isset($res['new_sale']) && $res['new_sale'] !== null) {
                    update_post_meta($pid, '_sale_price', $res['new_sale']);
                    $changed = true;
                }

                if ($changed) {
                    // Update main price for sorting/display
                    $r = get_post_meta($pid, '_regular_price', true);
                    $s = get_post_meta($pid, '_sale_price', true);
                    $final = (is_numeric($s) && $s > 0 && $s < $r) ? $s : $r;
                    update_post_meta($pid, '_price', $final);
                    wc_delete_product_transients($pid);
                }

                update_post_meta($pid, '_bgn_eur_converted_date', current_time('mysql'));
                $count++;
            }
        }

        $remaining_msg = isset($result['remaining']) ? " ({$result['remaining']} remaining)" : "";
        wp_send_json_success(['message' => "Successfully processed {$count} items." . $remaining_msg]);
    }
}
