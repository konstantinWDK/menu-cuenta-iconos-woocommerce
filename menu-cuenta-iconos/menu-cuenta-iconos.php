<?php
/*
Plugin Name: Iconos imagen menú Mi Cuenta WooCommerce
Description: Añade imágenes como iconos y opción de ocultar enlaces del menú de cuenta de WooCommerce desde el backoffice. Incluye tamaños, márgenes, padding y botón de restablecer valores.
Version: 1.4
Author: Konstantin WDK
*/

if (!defined('ABSPATH')) exit;

class WC_MyAccount_Menu_Customizer {

    private $option_name = 'wc_myaccount_menu_customizations';

    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_media_uploader']);
        add_filter('woocommerce_account_menu_items', [$this, 'filter_account_menu_items']);
        add_action('woocommerce_before_account_navigation', [$this, 'enqueue_custom_styles_and_icons']);
    }

    public function add_admin_page() {
        add_theme_page(
            'Menú cuenta WooCommerce',
            'Menú cuenta WooCommerce',
            'manage_options',
            'wc-myaccount-menu',
            [$this, 'admin_page_html']
        );
    }

    public function register_settings() {
        register_setting('wc_myaccount_menu_group', $this->option_name);
    }

    public function enqueue_media_uploader($hook) {
        if ($hook !== 'appearance_page_wc-myaccount-menu') return;
        wp_enqueue_media();
        add_action('admin_footer', function () {
        ?>
        <script>
        jQuery(document).ready(function($){
            // Media uploader
            $('.upload-icon-button').on('click', function(e) {
                e.preventDefault();
                var target = $(this).data('target');
                var frame = wp.media({
                    title: 'Seleccionar imagen del icono',
                    button: { text: 'Usar esta imagen' },
                    multiple: false
                });
                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#icon_input_' + target).val(attachment.url);
                    $('#preview_' + target).attr('src', attachment.url).show();
                });
                frame.open();
            });
            $('.remove-icon-button').on('click', function(e) {
                e.preventDefault();
                var target = $(this).data('target');
                $('#icon_input_' + target).val('');
                $('#preview_' + target).hide();
            });
            // Pill toggles
            $('.pill-button').on('click', function() {
                var tgt = $(this).data('target');
                $('#' + tgt).toggleClass('active');
            });
            // Reset defaults
            $('.reset-button').on('click', function(e) {
                e.preventDefault();
                var base = $(this).data('base');
                $('input[name="'+base+'[size]"]').val(20);
                $('input[name="'+base+'[height]"]').val(20);
                ['top','right','bottom','left'].forEach(function(side){
                    $('input[name="'+base+'[margin_'+side+']"]').val(side==='right'?8:0);
                    $('input[name="'+base+'[padding_'+side+']"]').val(0);
                });
            });
        });
        </script>
        <?php
        });
    }

    public function admin_page_html() {
        if (!current_user_can('manage_options')) return;
        $options    = get_option($this->option_name, []);
        $menu_items = wc_get_account_menu_items();
        $all_keys   = array_unique(array_merge(array_keys($menu_items), array_keys($options)));
        ?>
        <div class="wrap">
            <h1>Personalizar menú de cuenta de WooCommerce</h1>
            <form method="post" action="options.php">
                <?php settings_fields('wc_myaccount_menu_group'); ?>
                <style>
                    .icon-preview { max-width:40px; max-height:40px; vertical-align:middle; }
                    .pill-buttons { display:flex; gap:8px; margin:12px 0; }
                    .pill-button {
                        background:#f0f0f0; padding:6px 12px; border:1px solid #ccc;
                        border-radius:20px; font-size:12px; cursor:pointer;
                        transition:background .2s;
                    }
                    .pill-button:hover { background:#e0e0e0; }
                    .pill-fields { display:none; margin-top:8px; gap:10px; flex-wrap:wrap; }
                    .pill-fields.active { display:flex; }
                    .pill-fields-title {
                        width:100%; font-weight:bold; margin-bottom:4px; font-size:13px;
                    }
                    .pill-fields label { font-size:12px; margin-right:10px; }
                    .pill-fields input { width:60px; }
                    .icon-settings-preview { display:flex; align-items:center; gap:10px; }
                    .reset-button {
                        background:transparent; color:#d63638; border:1px solid #d63638;
                        padding:3px 8px; border-radius:3px; font-size:11px; cursor:pointer;
                        margin-left:10px; transition:background .2s,color .2s;
                    }
                    .reset-button:hover { background:#d63638; color:#fff; }
                </style>

                <table class="form-table">
                <?php foreach ($all_keys as $endpoint):
                    $label     = $menu_items[$endpoint] ?? ucfirst(str_replace('-', ' ', $endpoint));
                    $icon_url  = $options[$endpoint]['icon'] ?? '';
                    $is_hidden = !empty($options[$endpoint]['hide']);
                    $size      = $options[$endpoint]['size'] ?? 20;
                    $height    = $options[$endpoint]['height'] ?? $size;
                    $base      = $this->option_name . '[' . $endpoint . ']';
                ?>
                    <tr>
                        <th scope="row">
                            <?php echo esc_html($label); ?>
                            <small style="font-style:italic;">
                                (<?php echo esc_html($endpoint); ?>)
                                <?php if($is_hidden): ?>
                                    <span style="color:#c00;"> – actualmente oculto</span>
                                <?php endif; ?>
                            </small>
                        </th>
                        <td>
                            <div class="icon-settings-preview">
                                <img id="preview_<?php echo esc_attr($endpoint); ?>"
                                     src="<?php echo esc_url($icon_url); ?>"
                                     class="icon-preview"
                                     style="display:<?php echo $icon_url?'inline':'none'; ?>;">
                                <input type="hidden"
                                       name="<?php echo $base; ?>[icon]"
                                       id="icon_input_<?php echo esc_attr($endpoint); ?>"
                                       value="<?php echo esc_attr($icon_url); ?>">
                                <button type="button"
                                        class="button upload-icon-button"
                                        data-target="<?php echo esc_attr($endpoint); ?>">
                                    Subir imagen
                                </button>
                                <button type="button"
                                        class="button remove-icon-button"
                                        data-target="<?php echo esc_attr($endpoint); ?>">
                                    Eliminar
                                </button>
                                <button class="reset-button"
                                        data-base="<?php echo esc_attr($base); ?>">
                                    Restablecer
                                </button>
                            </div>

                            <div class="pill-buttons">
                                <div class="pill-button" data-target="size_<?php echo esc_attr($endpoint); ?>">
                                    Personalizar tamaño
                                </div>
                                <div class="pill-button" data-target="margin_<?php echo esc_attr($endpoint); ?>">
                                    Ajustar márgenes
                                </div>
                                <div class="pill-button" data-target="padding_<?php echo esc_attr($endpoint); ?>">
                                    Ajustar padding
                                </div>
                            </div>

                            <div id="size_<?php echo esc_attr($endpoint); ?>" class="pill-fields">
                                <span class="pill-fields-title">Tamaño del icono (px):</span>
                                <label>Ancho:
                                    <input type="number" name="<?php echo $base; ?>[size]" value="<?php echo esc_attr($size); ?>">
                                </label>
                                <label>Alto:
                                    <input type="number" name="<?php echo $base; ?>[height]" value="<?php echo esc_attr($height); ?>">
                                </label>
                            </div>

                            <div id="margin_<?php echo esc_attr($endpoint); ?>" class="pill-fields">
                                <span class="pill-fields-title">Valores de márgenes (px):</span>
                                <?php foreach (['top','right','bottom','left'] as $side): ?>
                                    <label><?php echo ucfirst($side); ?>:
                                        <input type="number"
                                               name="<?php echo $base; ?>[margin_<?php echo $side; ?>]"
                                               value="<?php echo esc_attr($options[$endpoint]["margin_$side"] ?? ($side==='right'?8:0)); ?>">
                                    </label>
                                <?php endforeach; ?>
                            </div>

                            <div id="padding_<?php echo esc_attr($endpoint); ?>" class="pill-fields">
                                <span class="pill-fields-title">Valores de padding (px):</span>
                                <?php foreach (['top','right','bottom','left'] as $side): ?>
                                    <label><?php echo ucfirst($side); ?>:
                                        <input type="number"
                                               name="<?php echo $base; ?>[padding_<?php echo $side; ?>]"
                                               value="<?php echo esc_attr($options[$endpoint]["padding_$side"] ?? 0); ?>">
                                    </label>
                                <?php endforeach; ?>
                            </div>

                            <p>
                                <label>
                                    <input type="checkbox"
                                           name="<?php echo $base; ?>[hide]"
                                           value="1" <?php checked($is_hidden); ?>>
                                    Ocultar este enlace
                                </label>
                            </p>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function filter_account_menu_items($items) {
        $options = get_option($this->option_name, []);
        foreach ($options as $endpoint => $data) {
            if (!empty($data['hide']) && isset($items[$endpoint])) {
                unset($items[$endpoint]);
            }
        }
        return $items;
    }

    public function enqueue_custom_styles_and_icons() {
        $options = get_option($this->option_name, []);
        if (empty($options)) return;
        echo '<style>
            .woocommerce-MyAccount-navigation ul li a img.icon-account {
                vertical-align: middle;
                display: inline-block;
            }
        </style>';
        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            const icons = ' . json_encode($options) . ';
            for (let endpoint in icons) {
                const data = icons[endpoint];
                if (data.icon && !data.hide) {
                    const link = document.querySelector(".woocommerce-MyAccount-navigation-link--" + endpoint + " a");
                    if (link) {
                        const img = document.createElement("img");
                        img.src = data.icon;
                        img.className = "icon-account";
                        img.alt = "";
                        // tamaño
                        img.style.width  = (data.size   || 20) + "px";
                        img.style.height = (data.height || data.size || 20) + "px";
                        // márgenes
                        img.style.marginTop    = (data.margin_top    || 0) + "px";
                        img.style.marginRight  = (data.margin_right  || 0) + "px";
                        img.style.marginBottom = (data.margin_bottom || 0) + "px";
                        img.style.marginLeft   = (data.margin_left   || 0) + "px";
                        // padding
                        img.style.paddingTop    = (data.padding_top    || 0) + "px";
                        img.style.paddingRight  = (data.padding_right  || 0) + "px";
                        img.style.paddingBottom = (data.padding_bottom || 0) + "px";
                        img.style.paddingLeft   = (data.padding_left   || 0) + "px";
                        link.prepend(img);
                    }
                }
            }
        });
        </script>';
    }
}

new WC_MyAccount_Menu_Customizer();
