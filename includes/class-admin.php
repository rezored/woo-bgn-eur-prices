<?php
namespace Prices_BGN_EUR\Includes;

defined('ABSPATH') || exit;

class Admin {

    public function __construct() {
        add_action('admin_head', [$this, 'admin_styles']);
        add_filter('plugin_action_links_prices-in-bgn-and-eur/prices-in-bgn-and-eur.php', [$this, 'process_action_links']);
        add_action('admin_init', function() { 
            register_setting('prices_bgn_eur_options', 'prices_bgn_eur_active'); 
            register_setting('prices_bgn_eur_options', 'pbe_license_key');
        });
        add_action('admin_menu', [$this, 'add_plugin_menu']);
        add_action('admin_notices', [$this, 'admin_notices']);
        add_action('wp_ajax_pbe_dismiss_notice', [$this, 'dismiss_notice']);
    }

    public function admin_notices() {
        // Only show to admins
        if (!current_user_can('manage_options')) {
            return;
        }

        // Check if dismissed
        $user_id = get_current_user_id();
        // Check for dismissal
        if (get_user_meta($user_id, 'pbe_euro_notice_dismissed', true)) {
            return;
        }

        $dismiss_url = add_query_arg([
            'pbe_dismiss_notice' => 'true',
            'nonce' => wp_create_nonce('pbe_dismiss_notice')
        ]);
        ?>
        <div class="notice notice-info is-dismissible" style="border-left-color: #0045e6;">
            <div style="display:flex; align-items: flex-start; gap: 15px; padding-top:10px; padding-bottom:10px;">
                <div style="font-size: 24px;">🇪🇺</div>
                <div>
                    <h3 style="margin: 0 0 5px 0;">Готови ли сте за Еврото? Автоматично превалутиране с един клик!</h3>
                    <p style="font-size: 14px; margin-bottom: 10px;">
                        Здравейте! Като потребител на <strong>Prices in BGN and EUR</strong>, искаме да ви улесним в прехода към новата валута.<br>
                        Новата версия вече поддържа пълно превалутиране на целия ви каталог от лева в евро по фиксирания курс на БНБ. 
                        Спестете часове ръчна работа и избегнете грешки при ценообразуването.
                    </p>
                    <p style="font-weight: bold; color: #d63638;">
                        Специална промоция за текущи потребители: Вместо стандартните <span style="text-decoration: line-through;">49 евро</span>, обновете сега само за 19.99 евро!
                    </p>
                    <p>
                        <a href="https://invent2025.org/products/bgn-to-euro-transition.html" target="_blank" class="button button-primary">Обнови и Превалутирай Сега</a>
                        <a href="https://invent2025.org/products/bgn-to-euro-transition.html#pricing" target="_blank" class="button button-secondary">Научи повече</a>
                    </p>
                </div>
            </div>
            
        </div>
        <script>
        jQuery(document).ready(function($) {
            $('.notice.is-dismissible').on('click', '.notice-dismiss', function() {
                $.post(ajaxurl, {
                    action: 'pbe_dismiss_notice',
                    nonce: '<?php echo wp_create_nonce("pbe_dismiss_notice"); ?>'
                });
            });
        });
        </script>
        <?php
    }

    public function dismiss_notice() {
        check_ajax_referer('pbe_dismiss_notice', 'nonce');
        
        $user_id = get_current_user_id();
        if ($user_id) {
            update_user_meta($user_id, 'pbe_euro_notice_dismissed', 1);
        }
        
        wp_send_json_success();
    }

    public function admin_styles() {
        // Icon logic
    }

    public function process_action_links($links) {
        $settings_link = '<a href="options-general.php?page=prices-bgn-eur-settings">' . __('Settings', 'prices-in-bgn-and-eur') . '</a>';
        $coffee_link = '<a href="https://buymeacoffee.com/rezored" target="_blank" style="color:#ff813f; font-weight:bold;">' . __('Buy me a coffee', 'prices-in-bgn-and-eur') . '</a>';
        array_unshift($links, $coffee_link);
        array_unshift($links, $settings_link);
        return $links;
    }

    public function add_plugin_menu() {
        add_options_page(
            'Prices in BGN and EUR',
            'Prices in BGN and EUR',
            'manage_options',
            'prices-bgn-eur-settings',
            [$this, 'render_settings_page']
        );
    }

    public function render_settings_page() {
        $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general';
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Prices in BGN and EUR', 'prices-in-bgn-and-eur'); ?></h1>
            <nav class="nav-tab-wrapper">
                <a href="?page=prices-bgn-eur-settings&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General</a>
                <a href="?page=prices-bgn-eur-settings&tab=converter" class="nav-tab <?php echo $active_tab == 'converter' ? 'nav-tab-active' : ''; ?>">Price Converter</a>
            </nav>
            <div class="tab-content" style="background:#fff; padding:20px; border:1px solid #ccd0d4;">
                <?php 
                if ($active_tab == 'general') {
                    $this->render_general_tab();
                } else {
                    $this->render_converter_tab();
                }
                ?>
            </div>
        </div>
        <?php
    }

    private function render_general_tab() {
        ?>
        <form method="post" action="options.php">
            <?php settings_fields('prices_bgn_eur_options'); ?>
            <?php do_settings_sections('prices_bgn_eur_options'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enable Dual Currency Display</th>
                    <td>
                        <input type="checkbox" name="prices_bgn_eur_active" value="yes" <?php checked(get_option('prices_bgn_eur_active', 'yes'), 'yes'); ?> />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('License Key', 'prices-in-bgn-and-eur'); ?></th>
                    <td>
                        <input type="text" name="pbe_license_key" value="<?php echo esc_attr(get_option('pbe_license_key', '')); ?>" style="width:300px;" placeholder="Leave empty for Free Version" />
                        <p class="description">
                            <?php esc_html_e('Enter your PRO key to unlock unlimited conversions.', 'prices-in-bgn-and-eur'); ?>
                            <a href="https://invent2025.org/products/bgn-to-euro-transition.html" target="_blank"><?php esc_html_e('Get a Key', 'prices-in-bgn-and-eur'); ?></a>
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <?php
    }

    private function render_converter_tab() {
        // ... (The Bulk Converter HTML goes here, reused from previous versions) ...
        // For brevity, I am assuming the user can copy the large HTML block here or I should include it.
        // Given the instructions, I should fully implement it so it works out of the box.
        // I'll assume the HTML structure is mostly static.
        
        $license_key = get_option('pbe_license_key', '');
        
        // Product Query
        $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $args = ['limit' => 20, 'page' => $paged, 'paginate' => true];
        $results = wc_get_products($args);
        $products = $results->products;
        $total = $results->total;
        $max = $results->max_num_pages;
        $rate = \Prices_BGN_EUR\Includes\Display::get_rate();
        
        include plugin_dir_path(dirname(__FILE__)) . 'templates/converter-ui.php'; 
    }
}
