jQuery(function ($) {
    function addEurToBlocks() {
        var eurRate = pricesBgnEurData.eurRate;

        function appendEur($el) {
            if ($el.find('.amount-eur').length || $el.text().includes('€')) return;

            // Improved price extraction - look for numbers followed by currency symbols
            var text = $el.text();
            var match = text.match(/([0-9]+[.,]?[0-9]*)\s*(лв|ЛВ|лв\.|ЛВ\.|BGN|€|EUR)/i);
            if (!match) {
                // Try without space between number and currency
                match = text.match(/([0-9]+[.,]?[0-9]*)(лв|ЛВ|лв\.|ЛВ\.|BGN|€|EUR)/i);
            }
            if (!match) return;

            var priceStr = match[1].replace(',', '.');
            var price = parseFloat(priceStr);

            if (price > 0 && !isNaN(price)) {
                var eurPrice = (price / eurRate).toFixed(2);
                $el.append(' <span class="amount-eur">(' + eurPrice + ' €)</span>');
            }
        }

        // Target both WooCommerce Blocks and traditional WooCommerce elements
        $('.wc-block-components-product-price__value, .wc-block-formatted-money-amount, .wc-block-components-totals-item__value, .price, .woocommerce-Price-amount, .woocommerce-loop-product__title, .product .price, .woocommerce div.product p.price, .woocommerce div.product span.price, .woocommerce ul.products li.product .price').each(function () {
            appendEur($(this));
        });

        if ($('.wc-block-cart__totals-title').length && !$('.eur-disclaimer-blocks').length) {
            $('.wc-block-cart__totals-title').after(
                '<div class="eur-disclaimer-blocks" style="font-size:12px;color:#777;margin-top:10px;padding:10px;background:#f9f9f9;border-radius:4px;">' +
                '<em>' + pricesBgnEurData.disclaimerText + ' <br>' + pricesBgnEurData.rateText + '</em>' +
                '</div>'
            );
        }
    }

    addEurToBlocks();
    setTimeout(addEurToBlocks, 500);

    $(document.body).on('updated_wc_block updated_cart_totals', function () {
        setTimeout(addEurToBlocks, 100);
    });

    new MutationObserver(addEurToBlocks).observe(document.body, { childList: true, subtree: true });
}); 