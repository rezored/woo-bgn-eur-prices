jQuery(function ($) {
    function addEurToBlocks() {
        var eurRate = pricesBgnEurData.eurRate;

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

        $('.wc-block-components-product-price__value, .wc-block-formatted-money-amount, .wc-block-components-totals-item__value').each(function () {
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