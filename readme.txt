=== Prices in BGN and EUR ===
Contributors: rezored
Tags: woocommerce, bgn, euro, currency, prices
Requires at least: 5.6
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.4.9
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display WooCommerce prices in BGN and EUR using the fixed BNB exchange rate. Compliant with Bulgarian law from 1 August 2025.

== Description ==

**Legal requirement from 1 August 2025:**  
All online stores in Bulgaria must display their prices in both BGN and EUR.

This lightweight plugin automatically adds EUR price display alongside BGN prices, using the fixed BNB exchange rate (1 EUR = 1.95583 BGN).

**Works on:**
- Product pages
- Cart and checkout (including WooCommerce Blocks)
- Mini cart
- Order emails

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
No. This plugin is designed only for stores with BGN as the primary currency.

= Can I change the exchange rate? =
No. The rate is fixed by law. For dynamic rates, use a multi-currency plugin.

= How can I support the developer? =
You can send a "thank you" through the link in the plugin settings ❤️

== Screenshots ==

1. Product page with prices in BGN and EUR
2. Checkout page showing both currencies

== Changelog ==

= 1.4.9 =
* Fixed edge case with comma handling in number formatting
* Improved price extraction to handle different thousand/decimal separator configurations
* Enhanced compatibility with various WooCommerce number formatting settings

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
