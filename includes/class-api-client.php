<?php
namespace Prices_BGN_EUR\Includes;

defined('ABSPATH') || exit;

class API_Client {
    private const API_URL = 'https://api.invent2025.org/api/v1/quota-check';

    /**
     * Sends products to the remote API for conversion.
     *
     * @param array  $items         Array of ['id' => 1, 'regular_price' => 10, 'sale_price' => 8]
     * @param string $license_key   The user's PRO key or empty for FREE
     * @param string $rounding_rule The selected rounding rule
     * @return array|WP_Error       Result array on success, WP_Error on failure
     */
    public static function convert_batch($items, $license_key, $rounding_rule) {
        $body = [
            'api_key'       => $license_key ?: 'FREE',
            'site_url'      => get_site_url(),
            'rounding_rule' => $rounding_rule,
            'items'         => $items
        ];

        $response = wp_remote_post(self::API_URL, [
            'body'    => json_encode($body),
            'headers' => [
                'Content-Type'  => 'application/json',
                'User-Agent'    => 'WordPress/' . get_bloginfo('version') . '; Prices-BGN-EUR/1.8'
            ],
            'timeout' => 20 // Give the API time to process math
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code($response);
        $raw_body = wp_remote_retrieve_body($response);
        $data = json_decode($raw_body, true);

        if ($code !== 200) {
            return new \WP_Error('api_error', isset($data['message']) ? $data['message'] : 'Remote server error: ' . $code);
        }

        if (isset($data['allowed']) && $data['allowed'] === false) {
            return new \WP_Error('quota_exceeded', isset($data['message']) ? $data['message'] : 'Quota exceeded');
        }

        return $data;
    }
}
