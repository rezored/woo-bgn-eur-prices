=== Prices in BGN and EUR ===
Contributors: rezored
Tags: woocommerce, bgn, euro, currency, prices
Plugin URI: https://wordpress.org/plugins/prices-in-bgn-and-eur/
Requires at least: 5.6
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.7.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://coff.ee/rezored

Display WooCommerce prices in BGN and EUR using the fixed BNB exchange rate. Compliant with Bulgarian law from 1 August 2025.

== Description ==

**Legal requirement from 1 August 2025:**  
All online stores in Bulgaria must display their prices in both BGN and EUR.

This lightweight plugin automatically adds the secondary price display alongside your main prices, using the fixed BNB exchange rate (1 EUR = 1.95583 BGN).

**How It Works:**
*   **If your store is in BGN:** The plugin displays the calculated price in **EUR**.
*   **If your store is in EUR:** The plugin displays the calculated price in **BGN** (Leva).
*   **Settings Control:** If you turn the setting **OFF**, only one price (your current store currency) will be shown.

**Works on:**
- Product pages
- Cart and checkout (including WooCommerce Blocks)
- Mini cart
- Order emails

== Support ==

If you find this plugin useful, please consider buying me a coffee to support the development!
https://coff.ee/rezored

== Features ==
- ✅ Automatic EUR price calculation from BGN  
- ✅ Uses fixed BNB exchange rate: 1 EUR = 1.95583 BGN  
- ✅ Works on all key pages and emails  
- ✅ No additional configuration required  
- ✅ Lightweight and fast  

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`.
2. Activate it from the **Plugins** menu in WordPress.
3. That’s it – the plugin works immediately!

== Frequently Asked Questions ==

= Will it work with currencies other than BGN? =
Yes! As of version 1.7.0, the plugin supports both BGN and EUR stores. If your store is in BGN, it shows EUR prices. If your store is in EUR, it shows BGN prices.

= Can I change the exchange rate? =
No. The rate is fixed by law. For dynamic rates, use a multi-currency plugin.

= How can I support the developer? =
You can send a "thank you" through the link in the plugin settings ❤️

== Screenshots ==

1. Product page with prices in BGN and EUR
2. Checkout page showing both currencies

== Changelog ==

= 1.7.1 =
* Tested up to WordPress 6.9
* Refined EUR to BGN support (Bidirectional)
* Added "Support me" link in Plugins list
* Added Donate link for WordPress directory

= 1.6.0 =
* CRITICAL FIX for duplicate EUR prices - Resolved problem with duplicate EUR prices
* Simplified architecture - Uses only wc_price hook to prevent duplication
* Removed HTML-level hooks that were causing duplication on price ranges
* Cleaner logic - One EUR per numeric price, no duplication

= 1.5.5 =
* Fixed double EUR price issue - Resolved problem with duplicate EUR prices
* Improved detection logic - More strict checking to prevent duplication
* Optimized processing - More efficient price processing logic

= 1.5.4 =
* CRITICAL FIX for WooCommerce 10.1.2 - Resolved missing EUR prices on product pages
* Added specific hooks for WooCommerce 10.1.2+ compatibility
* Added direct action hooks for more reliable product page coverage
* Enhanced debug logging for easier troubleshooting

= 1.5.3 =
* Fixed missing EUR prices on product pages and category listings
* Added missing WooCommerce hooks for complete product page coverage
* Enhanced JavaScript selectors for better product page targeting
* Added debug logging for easier troubleshooting

= 1.4.9 =
* Fixed edge case with comma handling in number formatting
* Improved price extraction to handle different thousand/decimal separator configurations
* Enhanced compatibility with various WooCommerce number formatting settings

= 1.7.0 =
* NEW: Bidirectional support - Automatically detects if your store is in BGN or EUR and displays the alternative
* NEW: Settings page - Enable/Disable the dual currency display
* Improved price display - Clean formatting for both currencies

= 1.4.8 =
* Fixed incorrect Euro conversion calculations
* Improved price extraction for both traditional WooCommerce and WooCommerce Blocks elements

= 1.4.6 =
* Fixed WordPress enqueue compliance – removed inline scripts/styles
* Enhanced compatibility – using proper WordPress functions for resource loading
* Improved security – proper script localization with `wp_localize_script()`
* Enhanced performance – CSS and JS files are separated and cached

= 1.4.3 =
* Improved security – all outputs are properly escaped
* Improved internationalization – all texts are translatable
* WordPress standards compliance

= 1.4.0 =
* Added WooCommerce Blocks support
* Improved compatibility with latest WordPress versions

= 1.3.7 =
* Added explanation under totals in cart and checkout
* Added readiness for admin page

= 1.3.0 =
* EUR price display on all pages and emails

== Upgrade Notice ==

= 1.3.7 =
Recommended update – additional information added in checkout and admin panel.
