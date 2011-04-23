<?php
/*
Plugin Name: Commerce Manager
Plugin URI: http://www.kleor-editions.com/commerce-manager
Description: Allows you to sell your products and manage your orders.
Version: 1.0
Author: Kleor
Author URI: http://www.kleor-editions.com
Text Domain: commerce-manager
*/


define('COMMERCE_MANAGER_URL', plugin_dir_url(__FILE__));
$orders_table_name = $wpdb->prefix.'commerce_manager_orders';
$products_table_name = $wpdb->prefix.'commerce_manager_products';

load_plugin_textdomain('commerce-manager', false, 'commerce-manager/languages');

function install_commerce_manager() { include_once dirname(__FILE__).'/install.php'; }

register_activation_hook(__FILE__, 'install_commerce_manager');

$commerce_manager_options = get_option('commerce_manager');

if (is_admin()) { include_once dirname(__FILE__).'/admin.php'; }


commerce_fix_url();


/* Début du code à terminer */

function purchase_button($atts) {
global $product_data, $wpdb;
extract(shortcode_atts(array('id' => '', 'src' => 0), $atts));
}

add_shortcode('purchase-button', 'purchase_button');

/* Fin du code à terminer */

function commerce_data($atts) {
global $commerce_manager_options;
if (is_string($atts)) { $field = $atts; } else { $field = $atts[0]; }
$field = str_replace('-', '_', commerce_format_nice_name($field));
if ($field == '') { $field = 'currency_code'; }
switch ($field) {
case 'email_to_customer_body': $data = get_option('commerce_manager_email_to_customer_body'); break;
case 'email_to_seller_body': $data = get_option('commerce_manager_email_to_seller_body'); break;
default: $data = $commerce_manager_options[$field]; }
$data = do_shortcode($data);
if ($data == '') { $data = $atts['default']; }
$data = str_replace('&', '&amp;', $data);
return $data; }

add_shortcode('commerce-manager', 'commerce_data');


function commerce_fix_url() {
$url = $_SERVER['REQUEST_URI'];
if (strstr($url, '&amp;')) { $url = str_replace('&amp;', '&', $url); $error = true; }
if ((strstr($url, '?')) && (!strstr($url, '/?'))) { $url = str_replace('?', '/?', $url); $error = true; }
if (($error) && (!headers_sent())) { header('Location: '.$url); exit; } }


function commerce_format_email_address($string) {
$string = strtolower(trim(strip_tags($string)));
$string = str_replace('à', '@', $string);
$string = str_replace(';', '.', $string);
$string = str_replace(' ', '', $string);
$string = commerce_strip_accents($string);
return $string; }


function commerce_format_email_address_js() { ?>
<script type="text/javascript">
function commerce_format_email_address(string) {
string = string.toLowerCase();
string = string.replace(/[à]/gi, "@");
string = string.replace(/[;]/gi, ".");
string = string.replace(/[ ]/gi, "");
string = commerce_strip_accents(string);
return string; }
</script>
<?php }


function commerce_format_name($string) {
$string = strtolower(trim(strip_tags($string)));
$string = str_replace(array(' ', '_'), '-', $string);
$strings = explode('-', $string);
$n = count($strings);
for ($i = 0; $i < $n; $i++) { $strings[$i] = ucfirst($strings[$i]); }
$string = implode('-', $strings);
return $string; }


function commerce_format_name_js() { ?>
<script type="text/javascript">
function commerce_format_name(string) {
string = string.toLowerCase();
string = string.replace(/[ _]/gi, "-");
var strings = string.split("-");
var n = strings.length;
var i = 0; while (i != n) { strings[i] = (strings[i]).substr(0, 1).toUpperCase()+(strings[i]).substr(1); i = i + 1; }
string = strings.join("-");
return string; }
</script>
<?php }

function commerce_format_nice_name($string) {
$string = commerce_strip_accents(strtolower(trim(strip_tags($string))));
$string = str_replace(' ', '_', $string);
$string = preg_replace('/[^a-zA-Z0-9_-]/', '', $string);
return $string; }


function commerce_format_nice_name_js() { ?>
<script type="text/javascript">
function commerce_format_nice_name(string) {
string = commerce_strip_accents(string.toLowerCase());
string = string.replace(/[ ]/gi, "_");
string = string.replace(/[^a-zA-Z0-9_-]/gi, "");
return string; }
</script>
<?php }


function commerce_strip_accents($string) {
return str_replace(
explode(' ', 'á à â ä ã å ç é è ê ë í ì î ï ñ ó ò ô ö õ ø ú ù û ü ý ÿ Á À Â Ä Ã Å Ç É È Ê Ë Í Ì Î Ï Ñ Ó Ò Ô Ö Õ Ø Ú Ù Û Ü Ý Ÿ'),
explode(' ', 'a a a a a a c e e e e i i i i n o o o o o o u u u u y y A A A A A A C E E E E I I I I N O O O O O O U U U U Y Y'),
$string); }


function commerce_strip_accents_js() { ?>
<script type="text/javascript">
function commerce_strip_accents(string) {
string = string.replace(/[áàâäãå]/gi, "a");
string = string.replace(/[ç]/gi, "c");
string = string.replace(/[éèêë]/gi, "e");
string = string.replace(/[íìîï]/gi, "i");
string = string.replace(/[ñ]/gi, "n");
string = string.replace(/[óòôöõø]/gi, "o");
string = string.replace(/[úùûü]/gi, "u");
string = string.replace(/[ýÿ]/gi, "y");
string = string.replace(/[ÁÀÂÄÃÅ]/gi, "A");
string = string.replace(/[Ç]/gi, "C");
string = string.replace(/[ÉÈÊË]/gi, "E");
string = string.replace(/[ÍÌÎÏ]/gi, "I");
string = string.replace(/[Ñ]/gi, "N");
string = string.replace(/[ÓÒÔÖÕØ]/gi, "O");
string = string.replace(/[ÚÙÛÜ]/gi, "U");
string = string.replace(/[ÝŸ]/gi, "Y");
return string; }
</script>
<?php }


function order_data($atts) {
global $order_data, $orders_table_name, $wpdb;
if (is_string($atts)) { $field = $atts; } else { $field = $atts[0]; }
$field = str_replace('-', '_', commerce_format_nice_name($field));
if ($field == '') { $field = 'first_name'; }
$id = (int) $atts['id'];
if (($id == 0) || ($id == $order_data->id)) { $data = $order_data->$field; }
else {
$local_order_data = $wpdb->get_row("SELECT * FROM $orders_table_name WHERE id = '$id'", OBJECT);
$data = $local_order_data->$field; }
if ($data == '') { $data = $atts['default']; }
$data = str_replace('&', '&amp;', $data);
return $data; }

add_shortcode('customer', 'order_data');
add_shortcode('order', 'order_data');


function product_data($atts) {
global $product_data, $products_table_name, $wpdb;
if (is_string($atts)) { $field = $atts; } else { $field = $atts[0]; }
$field = str_replace('-', '_', commerce_format_nice_name($field));
if ($field == '') { $field = 'name'; }
$id = (int) $atts['id'];
if (($id == 0) || ($id == $product_data->id)) { $data = $product_data->$field; }
else {
$local_product_data = $wpdb->get_row("SELECT * FROM $products_table_name WHERE id = '$id'", OBJECT);
$data = $local_product_data->$field; }
$data = do_shortcode($data);
if ($data == '') { switch ($field) {
case 'affiliation_enabled': case 'commission_amount': case 'commission_payment':
case 'commission_percentage': case 'commission_type': case 'first_sale_winner':
case 'registration_required': if (function_exists('affiliate_data')) { $data = affiliate_data($field); } break;
default: $data = commerce_data($field); } }
if ($data == '') { $data = $atts['default']; }
$data = str_replace('&', '&amp;', $data);
return $data; }

add_shortcode('product', 'product_data');


function sales_counter($atts, $content) {
global $product_data, $products_table_name, $wpdb;
extract(shortcode_atts(array('id' => 0, 'limit' => ''), $atts));
if ($limit == '') { $limit = '0'; }
else { $limit = '0/'.$limit; }
$limit = preg_split('#[^0-9]#', $limit);
$n = count($limit);

if (($id == 0) || ($id == $product_data->id)) { $sales_count = $product_data->sales_count; }
else {
$local_product_data = $wpdb->get_row("SELECT * FROM $products_table_name WHERE id = '$id'", OBJECT);
$sales_count = $local_product_data->sales_count; }

$i = 0; while (($i < $n) && ($limit[$i] <= $sales_count)) { $k = $i; $i = $i + 1; }
if ($i < $n) { $remaining_number = $limit[$i] - $sales_count; $total_remaining_number = $limit[$n - 1] - $sales_count; }
else { $i = $n - 1; $remaining_number = 0; $total_remaining_number = 0; }

$content = explode('[after]', do_shortcode($content));
$content[$k] = str_ireplace('[limit]', $limit[$i], $content[$k]);
$content[$k] = str_ireplace('[total-limit]', $limit[$n - 1], $content[$k]);
$content[$k] = str_ireplace('[number]', $sales_count - $limit[$k], $content[$k]);
$content[$k] = str_ireplace(array('[total-number]', '[sales-count]'), $sales_count, $content[$k]);
$content[$k] = str_ireplace('[remaining-number]', $remaining_number, $content[$k]);
$content[$k] = str_ireplace('[total-remaining-number]', $total_remaining_number, $content[$k]);

return $content[$k]; }

add_shortcode('sales-counter', 'sales_counter');


add_filter('get_the_excerpt', 'do_shortcode');
add_filter('get_the_title', 'do_shortcode');
add_filter('single_post_title', 'do_shortcode');
add_filter('the_excerpt', 'do_shortcode');
add_filter('the_excerpt_rss', 'do_shortcode');
add_filter('the_title', 'do_shortcode');
add_filter('the_title_attribute', 'do_shortcode');
add_filter('the_title_rss', 'do_shortcode');
add_filter('widget_text', 'do_shortcode');