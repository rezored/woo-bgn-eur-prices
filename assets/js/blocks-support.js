jQuery(function ($) {
    function addSecondaryCurrencyToBlocks() {
        var rate = pricesBgnEurData.rate;
        var currency = pricesBgnEurData.currency; // 'BGN' or 'EUR'

        function appendSecondaryPrice($el) {
            // Prevent double processing
            if ($el.find('.amount-secondary').length || $el.text().includes('(') && $el.text().includes(')')) return;

            // Check if this element is inside a price element that already has secondary price
            if ($el.closest('.price').find('.amount-secondary').length > 0) return;

            var text = $el.text();

            // Regex to find number
            var match = text.match(/([0-9]+[.,]?[0-9]*)/);
            if (!match) return;

            // Simple cleanup
            var priceStr = match[1].replace(',', '.');
            // Handle cases like 1.234,56 vs 1,234.56 - this is simple heuristics, 
            // reliable extraction happened in PHP, here is just visual enhancement for blocks

            // Better heuristic: remove non-numeric except last dot/comma treating as decimal
            // But for simple display, standard float parse often works if format is standard
            var price = parseFloat(priceStr);

            if (price > 0 && !isNaN(price)) {
                var secondaryPrice = 0;
                var symbol = '';

                if (currency === 'BGN') {
                    // Convert to EUR
                    secondaryPrice = (price / rate).toFixed(2);
                    symbol = '€';
                } else {
                    // Convert to BGN
                    secondaryPrice = (price * rate).toFixed(2);
                    symbol = 'лв.';
                }

                $el.append(' <span class="amount-secondary">(' + secondaryPrice + ' ' + symbol + ')</span>');
            }
        }

        // Target only WooCommerce Blocks elements that PHP doesn't handle
        $('.wc-block-components-product-price__value, .wc-block-formatted-money-amount, .wc-block-components-totals-item__value').each(function () {
            appendSecondaryPrice($(this));
        });

        // Add info to Cart Totals block if missing
        if ($('.wc-block-cart__totals-title').length && !$('.eur-disclaimer-blocks').length) {
            $('.wc-block-cart__totals-title').after(
                '<div class="eur-disclaimer-blocks" style="font-size:12px;color:#777;margin-top:10px;padding:10px;background:#f9f9f9;border-radius:4px;">' +
                '<em>' + pricesBgnEurData.disclaimerText + ' <br>' + pricesBgnEurData.rateText + '</em>' +
                '</div>'
            );
        }
    }

    addSecondaryCurrencyToBlocks();
    setTimeout(addSecondaryCurrencyToBlocks, 500);

    $(document.body).on('updated_wc_block updated_cart_totals', function () {
        setTimeout(addSecondaryCurrencyToBlocks, 100);
    });

    new MutationObserver(addSecondaryCurrencyToBlocks).observe(document.body, { childList: true, subtree: true });
});