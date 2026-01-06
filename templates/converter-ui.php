<?php defined('ABSPATH') || exit; ?>
<h2>Bulk Price Converter (BGN &rarr; EUR)</h2>
<p class="description">
    <?php esc_html_e('This tool permanently converts product prices from BGN to EUR using the secure API.', 'prices-in-bgn-and-eur'); ?>
</p>

<style>
    .converted-row { background-color: #e6ffea !important; }
    .variation-row td.column-primary { padding-left: 30px !important; position: relative; }
    .variation-row td.column-primary:before { content: "↳"; position: absolute; left: 10px; color: #999; }
    
    /* Unified Alert Box Styles */
    #alert-box {
        margin: 20px 0;
        padding: 15px;
        border-radius: 4px;
        border-left: 4px solid #d63638;
        background: #fff;
        box-shadow: 0 1px 1px rgba(0,0,0,.04);
    }
    #alert-box.quota-error {
        border-color: #d63638;
        background: #fff5f5;
        text-align: center;
        padding: 30px;
    }
    .flash-error {
        animation: flash-red 0.5s ease-in-out;
    }
    /* Loading Overlay */
    #loading-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(255, 255, 255, 0.9);
        z-index: 99999;
        display: none;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }
    .spinner-large {
        width: 50px; height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #2271b1;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 20px;
    }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
</style>

<!-- Unified Alert Box -->
<div id="alert-box">
    <h3 style="margin:0 0 5px 0; color:#d63638;"><?php esc_html_e('⚠️ CRITICAL WARNING: IRREVERSIBLE ACTION', 'prices-in-bgn-and-eur'); ?></h3>
    <p style="margin:0 0 10px 0; font-size:13px;">
        <?php esc_html_e('This tool will PERMANENTLY overwrite your product prices. There is NO UNDO button.', 'prices-in-bgn-and-eur'); ?>
    </p>
    <label style="display:block; font-weight:bold; cursor:pointer;">
        <input type="checkbox" id="accept-risk-cb"> 
        <?php esc_html_e('I understand that this is irreversible and I use it at my own risk.', 'prices-in-bgn-and-eur'); ?>
    </label>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay">
    <div class="spinner-large"></div>
    <h2 style="color:#2271b1;"><?php esc_html_e('Processing Conversions...', 'prices-in-bgn-and-eur'); ?></h2>
    <p style="font-size:16px; color:#555;"><?php esc_html_e('Please do not close this window.', 'prices-in-bgn-and-eur'); ?></p>
    <p style="font-size:14px; color:#999; margin-top:10px;"><?php esc_html_e('Connecting to secure API server...', 'prices-in-bgn-and-eur'); ?></p>
</div>

<form id="prices-bgn-eur-converter-form">
    <!-- Toolbar -->
    <div class="tablenav top" style="height:auto; min-height:30px; margin-bottom:10px;">
        <div class="alignleft actions bulkactions" style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">

            <!-- Select All -->
            <button type="button" id="select-all-btn" class="button"><?php esc_html_e('Select All', 'prices-in-bgn-and-eur'); ?></button>
            
            <!-- Rounding -->
            <label for="rounding_rule" style="margin-left:5px;"><?php esc_html_e('Rounding:', 'prices-in-bgn-and-eur'); ?></label>
            <select id="rounding_rule" name="rounding_rule">
                <option value="math"><?php esc_html_e('Standard (2 decimals)', 'prices-in-bgn-and-eur'); ?></option>
                <option value="0.05"><?php esc_html_e('Nearest 0.05', 'prices-in-bgn-and-eur'); ?></option>
                <option value="0.10"><?php esc_html_e('Nearest 0.10', 'prices-in-bgn-and-eur'); ?></option>
                <option value="0.50"><?php esc_html_e('Nearest 0.50', 'prices-in-bgn-and-eur'); ?></option>
                <option value="1.00"><?php esc_html_e('Nearest 1.00 (Integer)', 'prices-in-bgn-and-eur'); ?></option>
                <option value="ceil"><?php esc_html_e('Round Up to Integer (e.g. 59.10 -> 60.00)', 'prices-in-bgn-and-eur'); ?></option>
                <option value="5.00"><?php esc_html_e('Round Up to Nearest 5.00 (e.g. 81.00 -> 85.00)', 'prices-in-bgn-and-eur'); ?></option>
            </select>

            <!-- Action -->
            <button type="button" id="convert-selected-btn" class="button button-primary" disabled>
                <?php esc_html_e('Convert Selected', 'prices-in-bgn-and-eur'); ?>
            </button>
            
            <!-- Counter -->
            <span id="selection-count" style="font-weight:bold; color:#666;"></span>
        </div>
        
        <!-- Global Select Message -->
        <div id="global-select-message" style="width:100%; display:none; background:#e6f7ff; border:1px solid #1890ff; padding:8px; margin-top:5px; clear:both;"></div>

        <!-- Pagination -->
        <div class="tablenav-pages">
            <span class="displaying-num"><?php echo esc_html($total) . ' ' . __('items', 'prices-in-bgn-and-eur'); ?></span>
            <?php
            $page_links = paginate_links([
                'base' => add_query_arg('paged', '%#%'),
                'format' => '',
                'prev_text' => __('&laquo;'),
                'next_text' => __('&raquo;'),
                'total' => $max,
                'current' => $paged
            ]);
            if ($page_links) echo '<span class="pagination-links">' . $page_links . '</span>';
            ?>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped table-view-list">
        <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column"><input type="checkbox" id="cb-select-all-1"></td>
                <th scope="col" class="manage-column column-primary"><?php esc_html_e('Product', 'prices-in-bgn-and-eur'); ?></th>
                <th scope="col" class="manage-column"><?php esc_html_e('Type', 'prices-in-bgn-and-eur'); ?></th>
                <th scope="col" class="manage-column"><?php esc_html_e('Current (BGN)', 'prices-in-bgn-and-eur'); ?></th>
                <th scope="col" class="manage-column"><?php esc_html_e('Preview (EUR)', 'prices-in-bgn-and-eur'); ?></th>
                <th scope="col" class="manage-column"><?php esc_html_e('Converted (EUR)', 'prices-in-bgn-and-eur'); ?></th>
                <th scope="col" class="manage-column"><?php esc_html_e('Status', 'prices-in-bgn-and-eur'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($products)): ?>
                <?php 
                foreach ($products as $product): 
                    $pid = $product->get_id();
                    $price = $product->get_price();
                    $converted_date = get_post_meta($pid, '_bgn_eur_converted_date', true);
                    $row_class = $converted_date ? 'converted-row' : '';
                    
                    $col_bgn = '-';
                    $col_preview = '-';
                    $col_converted = '-';
                    $status_html = '<span style="color:#999;">' . __('Pending', 'prices-in-bgn-and-eur') . '</span>';
                    
                    if ($converted_date) {
                         $status_html = '<strong style="color:green;">' . sprintf(__('Converted: %s', 'prices-in-bgn-and-eur'), date_i18n(get_option('date_format'), strtotime($converted_date))) . '</strong>';
                         if (is_numeric($price)) $col_converted = number_format($price, 2) . ' €';
                    } else {
                        if (is_numeric($price)) {
                            $col_bgn = $price . ' лв.'; 
                            $col_preview = '<span class="preview-eur-cell" data-original-bgn="' . esc_attr($price) . '">' . number_format($price / $rate, 2) . ' €</span>';
                        }
                    }
                    ?>
                    <tr class="<?php echo esc_attr($row_class); ?>">
                         <th scope="row" class="check-column"><input type="checkbox" name="product_ids[]" value="<?php echo esc_attr($pid); ?>" <?php disabled(!empty($converted_date)); ?>></th>
                         <td class="column-primary"><strong><?php echo esc_html($product->get_name()); ?></strong></td>
                         <td><?php echo esc_html($product->get_type()); ?></td>
                         <td><?php echo esc_html($col_bgn); ?></td>
                         <td><?php echo $col_preview; ?></td>
                         <td><?php echo esc_html($col_converted); ?></td>
                         <td><?php echo $status_html; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7"><?php esc_html_e('No products found.', 'prices-in-bgn-and-eur'); ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</form>

<div id="converter-status" style="margin-top:20px; display:none;"></div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    var selectAllGlobal = false;
    var totalItems = <?php echo intval($total); ?>;
    var exchangeRate = <?php echo floatval($rate); ?>;

    function updateCount() {
        var count = $('input[name="product_ids[]"]:checked').length;
        var text = selectAllGlobal ? totalItems + ' selected (Global)' : (count > 0 ? count + ' selected' : '');
        $('#selection-count').text(text);
    }

    // Safety Logic
    $('#accept-risk-cb').on('change', function() {
        $('#convert-selected-btn').prop('disabled', !this.checked);
    });

    // Select All
    $('#cb-select-all-1, #select-all-btn').on('click', function() {
        var checked = true;
        if (this.id === 'cb-select-all-1') checked = $(this).prop('checked');
        $('input[name="product_ids[]"]:not(:disabled)').prop('checked', checked);
        
        if (!checked) {
            selectAllGlobal = false;
            $('#global-select-message').hide();
        }
        
        updateCount();
        checkGlobalSelectOption();
    });

    $('input[name="product_ids[]"]').on('change', function() { 
        if (!$(this).prop('checked')) {
            selectAllGlobal = false;
             $('#global-select-message').hide();
        }
        updateCount();
        checkGlobalSelectOption();
    });

    function checkGlobalSelectOption() {
         var visibleCount = $('input[name="product_ids[]"]').length;
         var checkedCount = $('input[name="product_ids[]"]:checked').length;
         
         if (checkedCount === visibleCount && totalItems > visibleCount && !selectAllGlobal) {
             $('#global-select-message').show().html(
                 'All ' + visibleCount + ' items on this page are selected. ' + 
                 '<a href="#" id="select-global-link" style="font-weight:bold;">Select all ' + totalItems + ' items in database</a>'
             );
         } else if (!selectAllGlobal) {
             $('#global-select-message').hide();
         }
    }

    $(document).on('click', '#select-global-link', function(e) {
        e.preventDefault();
        selectAllGlobal = true;
        $('#global-select-message').html('<span class="dashicons dashicons-yes"></span> All ' + totalItems + ' items are selected.');
        updateCount();
    });

    // Rounding Preview
    $('#rounding_rule').on('change', function() {
        var rule = $(this).val();
        $('.preview-eur-cell').each(function() {
            var bgn = parseFloat($(this).data('original-bgn'));
            if(isNaN(bgn)) return;
            var val = bgn / exchangeRate;
            
            if (rule === 'math') val = Math.round(val * 100) / 100;
            else if (rule === '0.05') val = Math.round(val * 20) / 20;
            else if (rule === '0.10') val = Math.round(val * 10) / 10;
            else if (rule === '0.50') val = Math.round(val * 2) / 2;
            else if (rule === '1.00') val = Math.round(val);
            else if (rule === 'ceil') val = Math.ceil(val);
            else if (rule === '5.00') val = Math.ceil(val / 5) * 5;
            
            $(this).text(val.toFixed(2) + ' €');
        });
    });

    // Convert Action
    $('#convert-selected-btn').on('click', function() {
        if (!$('#accept-risk-cb').is(':checked')) {
            // Flash generic animation if they somehow clicked it
            $('#alert-box').addClass('flash-error');
            setTimeout(function(){ $('#alert-box').removeClass('flash-error'); }, 500);
            return;
        }

        var items = [];
        $('input[name="product_ids[]"]:checked').each(function() { items.push($(this).val()); });

        if (items.length === 0 && !selectAllGlobal) {
            alert('Please select at least one product.');
            return;
        }

        if (!confirm('<?php esc_html_e('Final Confirmation: Convert these products to EUR?', 'prices-in-bgn-and-eur'); ?>')) return;

        // SHOW LOADER
        $('#loading-overlay').css('display', 'flex');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'prices_bgn_eur_convert_selected',
                product_ids: items,
                rounding_rule: $('#rounding_rule').val(),
                select_all_global: selectAllGlobal,
                _ajax_nonce: '<?php echo wp_create_nonce('prices_bgn_eur_convert_nonce'); ?>'
            },
            success: function(response) {
                // HIDE LOADER
                $('#loading-overlay').hide();

                if (response.success) {
                    $('#converter-status').show().html('<p style="color:green; font-weight:bold;">' + response.data.message + '</p>');
                    setTimeout(function() { location.reload(); }, 2000);
                } else {
                    var msg = (response.data.message || 'Unknown error');
                    
                    // Specific Handling for Quota
                    if (msg.indexOf('Quota') !== -1 || msg.indexOf('quota') !== -1) {
                        // REPLACE THE ALERT BOX CONTENT
                        $('#alert-box').addClass('quota-error').html(
                            '<h3 style="color:#d63638; margin-top:0;">' + msg + '</h3>' +
                            '<p style="font-size:16px;">You have reached the free tier limit of 20 conversions.</p>' +
                            '<a href="https://invent2025.org/products/bgn-to-euro-transition.html" target="_blank" class="button button-primary button-hero" style="margin-top:10px;">Get Unlimited License &raquo;</a>'
                        );
                        // Hide generic status to avoid clutter
                        $('#converter-status').hide();
                        
                        // Scroll to top
                        $('html, body').animate({ scrollTop: 0 }, 'fast');
                        
                    } else {
                        $('#converter-status').show().html('<p style="color:red; font-weight:bold;">Error: ' + msg + '</p>');
                    }
                }
            },
            error: function() {
                // HIDE LOADER
                $('#loading-overlay').hide();
                $('#converter-status').show().html('<p style="color:red;">Server communication error.</p>');
            }
        });
    });
});
</script>
