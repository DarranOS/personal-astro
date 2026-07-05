<?php
/*
Plugin Name: Seat Builder 3.0 by Jarilo
Plugin URI: https://jarilo.co.uk/
Description: Next generation custom seat builder.
Author: Jarilo Design
Version: 0.2.0
Author URI: https://jarilo.co.uk/
*/

defined('ABSPATH') || exit;

/**
 * ==================================================================
 * CONFIGURATION
 * ==================================================================
 */

/**
 * Label map (single source of truth).
 * Keys are the exact payload keys sent from the JS frontend.
 */
function dos3_label_map() {
    return [
        'SKU'               => 'SKU',
        'Make'              => 'Make',
        'Model'             => 'Model',
        'Year'              => 'Year',
        'sideColor'         => 'Side Color',
        'topColor'          => 'Top Color',
        'reinforcedSides'   => 'Reinforced Sides',
        'ribMode'           => 'Rib Mode',
        'ribColor'          => 'Rib Color',
        'topLogo'           => 'Top Logo',
        'sideLogo'          => 'Side Logo',
        'waterproofSealant' => 'Waterproof',
        'raceNumber'        => 'Rider Number',
        'raceNumberColor'   => 'Rider Number Color',
        'uploadedImage'     => 'Uploaded Image',
        'imageName'         => 'Image Name',
        'badgeType'         => 'Badge Type',
        'badgeNumber'       => 'Badge Number',
        'badgeName'         => 'Badge Name',
        'nameFont'          => 'Badge Name Font',
        'numFont'           => 'Badge Number Font',

    ];
}

/**
 * Canonical display order (by LABEL).
 * Anything not listed gets appended at the end in payload order.
 */
function dos3_display_order() {
    return [
        'SKU',
        'Make',
        'Model',
        'Year',
        'Side Color',
        'Top Color',
        'Rib Mode',
        'Rib Color',
        'Rib Color 1',
        'Rib Color 2',
        'Rib Color 3',
        'Rib Color 4',
        'Rib Color 5',
        'Rib Color 6',
        'Side Logo',
        'Top Logo',
        'Reinforced Sides',
        'Waterproof',
        'Rider Number',
        'Rider Number Color',
        'Badge Type',
        'Badge Number',
        'Badge Name',
        'Badge Name Font',
        'Badge Number Font',
        'Image Name',
        'Uploaded Image',
    ];
}

/**
 * Labels hidden from customer-facing views (cart, checkout, emails, My Account).
 * Still saved to the order and visible in wp-admin.
 */
function dos3_customer_hidden_labels() {
    return ['SKU'];
}

/**
 * Payload keys that are control values, never displayed anywhere.
 */
function dos3_never_display_keys() {
    return ['customPrice'];
}

/**
 * ==================================================================
 * HELPERS
 * ==================================================================
 */

/**
 * Resolve a payload key to a display label.
 * Handles static map + dynamic ribColor{N} multi-rib keys.
 */
function dos3_resolve_label($key, array $labels, array $labels_ci) {
    if (isset($labels[$key]))                return $labels[$key];
    if (isset($labels_ci[strtolower($key)])) return $labels_ci[strtolower($key)];

    // Dynamic multi-rib: ribColor1, ribColor2, ...
    if (preg_match('/^ribColor(\d+)$/i', $key, $m)) {
        return 'Rib Color ' . (int) $m[1];
    }

    return ucfirst($key);
}

/**
 * Sort rows of [['key' => Label, 'value' => ...], ...] by canonical order.
 */
function dos3_sort_by_display_order(array $rows) {
    $order = array_flip(dos3_display_order());
    $tail  = count($order);

    usort($rows, function ($a, $b) use ($order, $tail) {
        return ($order[$a['key']] ?? $tail) <=> ($order[$b['key']] ?? $tail);
    });

    return $rows;
}

/**
 * ==================================================================
 * CART: capture payload
 * ==================================================================
 */
add_filter('woocommerce_add_cart_item_data', function ($cart_item_data, $product_id, $variation_id) {

    if (empty($_REQUEST['custom_fields'])) {
        return $cart_item_data;
    }

    $payload = json_decode(stripslashes($_POST['custom_fields']), true);
    if (!is_array($payload)) {
        return $cart_item_data;
    }

    $fields = isset($payload['fields']) && is_array($payload['fields'])
        ? $payload['fields']
        : [];

    // customPrice is nested inside `fields` in the JS payload.
    // Tolerate a top-level value too in case the contract changes.
    $custom_price = null;
    if (isset($fields['customPrice']) && is_numeric($fields['customPrice'])) {
        $custom_price = (float) $fields['customPrice'];
    } elseif (isset($payload['customPrice']) && is_numeric($payload['customPrice'])) {
        $custom_price = (float) $payload['customPrice'];
    }

    $cart_item_data['dos3'] = [
        'price'  => $custom_price,
        'fields' => $fields,
    ];

    // Prevent cart item merging
    $cart_item_data['dos3_uid'] = function_exists('wp_generate_uuid4')
        ? wp_generate_uuid4()
        : md5(microtime(true) . rand());

    return $cart_item_data;
}, 10, 3);

/**
 * ==================================================================
 * CART: override price
 * ==================================================================
 */
add_action('woocommerce_before_calculate_totals', function ($cart) {

    if (is_admin() && !defined('DOING_AJAX')) return;

    foreach ($cart->get_cart() as $cart_item) {
        if (!empty($cart_item['dos3']['price'])) {
            $cart_item['data']->set_price($cart_item['dos3']['price']);
        }
    }
});

/**
 * ==================================================================
 * CART / CHECKOUT: customer-facing display
 * ==================================================================
 */
add_filter('woocommerce_get_item_data', function ($item_data, $cart_item) {

    if (empty($cart_item['dos3']['fields'])) return $item_data;

    $labels    = dos3_label_map();
    $labels_ci = array_change_key_case($labels, CASE_LOWER);
    $hidden    = dos3_customer_hidden_labels();
    $never     = dos3_never_display_keys();

    $rows = [];

    foreach ($cart_item['dos3']['fields'] as $key => $value) {
        if ($value === '' || $value === null) continue;
        if (in_array($key, $never, true))     continue;

        $label = dos3_resolve_label($key, $labels, $labels_ci);

        if (in_array($label, $hidden, true)) continue;

        $value = is_array($value) ? implode(', ', $value) : (string) $value;

        if ($key === 'uploadedImage') {
            $value = '<span class="cart-uploaded-image-thumb" data-src="' . esc_url($value) . '"></span>';
        } else {
            $value = esc_html($value);
        }

        $rows[] = ['key' => esc_html($label), 'value' => $value];
    }

    $rows = dos3_sort_by_display_order($rows);

    return array_merge($item_data, $rows);

}, 10, 2);

/**
 * ==================================================================
 * ORDER: persist meta in canonical order
 * Admin sees everything (including SKU). Custom price stored as hidden meta.
 * ==================================================================
 */
add_action('woocommerce_checkout_create_order_line_item', function ($item, $cart_item_key, $values, $order) {

    if (empty($values['dos3']['fields'])) return;

    $labels    = dos3_label_map();
    $labels_ci = array_change_key_case($labels, CASE_LOWER);
    $never     = dos3_never_display_keys();
    $order_map = array_flip(dos3_display_order());
    $tail      = count($order_map);

    $pairs = [];
    foreach ($values['dos3']['fields'] as $key => $value) {
        if ($value === '' || $value === null) continue;
        if (in_array($key, $never, true))     continue;

        $label = dos3_resolve_label($key, $labels, $labels_ci);
        $value = is_array($value) ? implode(', ', $value) : (string) $value;

        $pairs[] = ['label' => $label, 'value' => $value];
    }

    usort($pairs, function ($a, $b) use ($order_map, $tail) {
        return ($order_map[$a['label']] ?? $tail) <=> ($order_map[$b['label']] ?? $tail);
    });

    foreach ($pairs as $pair) {
        $item->add_meta_data($pair['label'], $pair['value'], true);
    }

    if (!empty($values['dos3']['price'])) {
        $item->add_meta_data('_dos3_price', (float) $values['dos3']['price'], true);
    }

}, 10, 4);




/**
 * ==================================================================
 * ORDER: hide SKU from customer-facing order views
 * (order received, customer emails, My Account → View Order)
 * Staff new-order email is exempt — SKU remains visible.
 * ==================================================================
 */
add_filter('woocommerce_order_item_get_formatted_meta_data', function ($formatted_meta, $item) {

    if (is_admin()) return $formatted_meta;

    // Allow SKU through on the new_order email (staff-facing)
    $current_email_id = WC()->mailer()->get_emails()
        ? ( isset( $GLOBALS['dos3_current_email_id'] ) ? $GLOBALS['dos3_current_email_id'] : '' )
        : '';

    $hidden = dos3_customer_hidden_labels();

    // SKU is exempt when rendering the new_order email
    if ($current_email_id === 'new_order') {
        $hidden = array_filter($hidden, fn($label) => $label !== 'SKU');
    }

    foreach ($formatted_meta as $meta_id => $meta) {
        if (in_array($meta->display_key, $hidden, true) || in_array($meta->key, $hidden, true)) {
            unset($formatted_meta[$meta_id]);
        }
    }

    return $formatted_meta;
}, 10, 2);

/**
 * Track which email is currently being sent so the meta filter above
 * can make context-aware decisions.
 */
add_action('woocommerce_email_before_order_table', function ($order, $sent_to_admin, $plain_text, $email) {
    $GLOBALS['dos3_current_email_id'] = $email->id ?? '';
}, 10, 4);

add_action('woocommerce_email_after_order_table', function () {
    $GLOBALS['dos3_current_email_id'] = '';
}, 10, 0);

/**
 * ==================================================================
 * ADMIN / EMAILS: show custom price line
 * ==================================================================
 */
add_action('woocommerce_order_item_meta_end', function ($item_id, $item) {

    $price = $item->get_meta('_dos3_price');
    if (!$price) return;

    echo '<p><strong>Custom Price:</strong> ' . wc_price($price) . '</p>';

}, 10, 2);

/**
 * ==================================================================
 * SHORTCODE: render the 3D scene
 * ==================================================================
 */


add_shortcode('dos3_new', function () {

    static $loaded = false;

    if ($loaded) return ''; // only one scene per page

    wp_enqueue_style(
        'dos3-new',
        plugins_url('assets/css/index-2733adb2.css', __FILE__),
        [],
        '0.2.0'
    );

    $loaded = true;

    return '<div id="scene-3d"></div>
        <script type="module" crossorigin src="' . esc_url(plugins_url('/assets/js/index-2fd3f9c7.js', __FILE__)) . '"></script>';
});

/**
 * ==================================================================
 * UPLOADED IMAGE: render thumbnail + download button in admin
 * ==================================================================
 *
 * Per-item unique ID so multi-line-item orders don't clash.
 * Output buffer is captured and returned via the value filter.
 */
add_filter('woocommerce_order_item_display_meta_value', function ($display_value, $meta, $item) {

    if ($meta->key !== 'Uploaded Image') return $display_value;

    $url = $meta->value;
    if (!$url) return $display_value;

    // Unique per meta row so multiple items on one order render independently
    $uid = 'dos3-img-' . (int) $item->get_id() . '-' . (int) $meta->id;

    // Infer filename extension
    $ext = 'jpg';
    if (stripos($url, 'png')  !== false) $ext = 'png';
    elseif (stripos($url, 'webp') !== false) $ext = 'webp';

    ob_start(); ?>
    <div class="dos3-uploaded-image-wrap">
        <img id="<?php echo esc_attr($uid); ?>"
             data-src="<?php echo esc_attr($url); ?>"
             data-ext="<?php echo esc_attr($ext); ?>"
             style="cursor:pointer;max-width:128px;height:auto;display:block;margin-bottom:6px;"
             alt="" />
        <button type="button"
                class="button dos3-download-btn"
                data-target="<?php echo esc_attr($uid); ?>">Download</button>
    </div>
    <?php
    return ob_get_clean();
}, 10, 3);

/**
 * Enqueue the small bit of JS that hydrates dos3 image thumbnails
 * and wires up download buttons. Loads once in admin order screens.
 */
add_action('admin_footer', function () {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen) return;

    // Load on classic Edit Order screens AND HPOS Orders pages
    $is_order_screen = ($screen->id === 'shop_order')
        || (isset($screen->id) && strpos($screen->id, 'wc-orders') !== false);

    if (!$is_order_screen) return;
    ?>
    <script type="text/javascript">
    (function () {
        function hydrate() {
            document.querySelectorAll('.dos3-uploaded-image-wrap img[data-src]').forEach(function (img) {
                if (img.src) return;
                img.src = img.dataset.src;
            });

            document.querySelectorAll('.dos3-download-btn').forEach(function (btn) {
                if (btn.dataset.bound) return;
                btn.dataset.bound = '1';

                btn.addEventListener('click', function () {
                    var targetId = btn.dataset.target;
                    var img = document.getElementById(targetId);
                    if (!img) return;

                    try {
                        btn.disabled = true;
                        var link = document.createElement('a');
                        link.href = img.dataset.src;
                        link.download = 'seat-image.' + (img.dataset.ext || 'jpg');
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    } catch (err) {
                        console.error('dos3 download error:', err);
                        alert('Failed to download image.');
                    } finally {
                        btn.disabled = false;
                    }
                });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', hydrate);
        } else {
            hydrate();
        }
    })();
    </script>
    <?php
});