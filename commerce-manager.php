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


load_plugin_textdomain('commerce-manager', false, 'commerce-manager/languages');

if (!defined('HOME_URL')) { define('HOME_URL', get_option('home')); }
if (!defined('UTC_OFFSET')) { define('UTC_OFFSET', get_option('gmt_offset')); }
define('COMMERCE_MANAGER_URL', plugin_dir_url(__FILE__));

global $wpdb;
$orders_table_name = $wpdb->prefix.'commerce_manager_orders';
$products_table_name = $wpdb->prefix.'commerce_manager_products';

if (is_admin()) { include_once dirname(__FILE__).'/admin.php'; }

$commerce_manager_options = get_option('commerce_manager');


commerce_fix_url();


function add_order($order) {
global $orders_table_name, $products_table_name, $wpdb;
include 'tables.php';
foreach ($tables['orders'] as $key => $value) { $keys_list .= $key.","; $values_list .= "'".$order[$key]."',"; }
$results = $wpdb->query("INSERT INTO $orders_table_name (".substr($keys_list, 0, -1).") VALUES(".substr($values_list, 0, -1).")");
$_GET['order_data'] = $wpdb->get_row("SELECT * FROM $orders_table_name WHERE date = '".$order['date']."' AND product_id = '".$order['product_id']."' AND email_address = '".$order['email_address']."'", OBJECT);
$_GET['product_id'] = $order['product_id'];
foreach (add_order_fields() as $field) {
if (is_admin()) { $order[$field] = stripslashes(do_shortcode($order[$field])); }
else { $order[$field] = product_data($field); } }

if (is_numeric(product_data('available_quantity'))) {
$available_quantity = product_data('available_quantity') - $order['quantity'];
if ($available_quantity < 0) { $available_quantity = 0; } }
else { $available_quantity = 'unlimited'; }
$sales_count = product_data('sales_count') + $order['quantity'];
if ($order['status'] == 'refunded') { $refunds_count = product_data('refunds_count') + $order['quantity']; }
else { $refunds_count = product_data('refunds_count'); }
$results = $wpdb->query("UPDATE $products_table_name SET
	available_quantity = '".$available_quantity."',
	sales_count = '".$sales_count."',
	refunds_count = '".$refunds_count."' WHERE id = '".$order['product_id']."'");

if ($order['email_sent_to_customer'] == 'yes') {
$sender = $order['email_to_customer_sender'];
$receiver = $order['email_to_customer_receiver'];
$subject = $order['email_to_customer_subject'];
$body = $order['email_to_customer_body'];
$headers = 'From: '.$sender;
wp_mail($receiver, $subject, $body, $headers); }
if ($order['email_sent_to_seller'] == 'yes') {
$sender = $order['email_to_seller_sender'];
$receiver = $order['email_to_seller_receiver'];
$subject = $order['email_to_seller_subject'];
$body = $order['email_to_seller_body'];
$headers = 'From: '.$sender;
wp_mail($receiver, $subject, $body, $headers); }

include 'autoresponders.php';
include_once 'autoresponders-functions.php';
$_GET['autoresponder_subscription'] = '';
if ($order['customer_subscribed_to_autoresponder'] == 'yes') {
subscribe_to_autoresponder($order['customer_autoresponder'], $order['customer_autoresponder_list'], $order); } }


function add_order_fields() {
return array(
'email_sent_to_customer',
'email_to_customer_sender',
'email_to_customer_receiver',
'email_to_customer_subject',
'email_to_customer_body',
'email_sent_to_seller',
'email_to_seller_sender',
'email_to_seller_receiver',
'email_to_seller_subject',
'email_to_seller_body',
'customer_subscribed_to_autoresponder',
'customer_autoresponder',
'customer_autoresponder_list'); }


function commerce_data($atts) {
global $commerce_manager_options;
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; }
else { $field = $atts[0]; $default = $atts['default']; $filter = $atts['filter']; }
$field = str_replace('-', '_', commerce_format_nice_name($field));
if ($field == '') { $field = 'currency_code'; }
switch ($field) {
case 'email_to_customer_body': $data = get_option('commerce_manager_email_to_customer_body'); break;
case 'email_to_seller_body': $data = get_option('commerce_manager_email_to_seller_body'); break;
default: $data = $commerce_manager_options[$field]; }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = commerce_format_data($field, $data);
$data = commerce_filter_data($filter, $data);
return $data; }

add_shortcode('commerce-manager', 'commerce_data');


function commerce_date_picker_css() { ?>
<style type="text/css">
table.jCalendar {
  background: #c0c0c0;
  border: 1px solid #000000;
  border-collapse: separate;
  border-spacing: 2px;
}

table.jCalendar td {
  background: #e0e0e0;
  color: #000000;
  padding: 3px 5px;
  text-align: center;
}

table.jCalendar td.disabled, table.jCalendar td.unselectable, table.jCalendar td.unselectable:hover {
  background: #c0c0c0;
  color: #808080;
}

table.jCalendar td.dp-hover,
table.jCalendar td.selected,
table.jCalendar td.today,
table.jCalendar tr.activeWeekHover td,
table.jCalendar tr.selectedWeek td {
  background: #e0c040;
  color: #000000;
}

table.jCalendar td.other-month {
  background: #808080;
  color: #000000;
}

table.jCalendar th {
  background: #404040;
  color: #ffffff;
  font-weight: bold;
  padding: 3px 5px;
}

#dp-close {
  display: block;
  font-size: 12px;
  padding: 4px 0;
  text-align: center;
}

#dp-close:hover { text-decoration: underline; }

#dp-popup {
  position: absolute;
  z-index: 1000;
}

.dp-popup {
  background: #e0e0e0;
  font-family: Verdana, Geneva, Arial, Helvetica, Sans-Serif;
  font-size: 9px;
  line-height: 1.25em;
  padding: 2px;
  position: relative;
  width: 187px;
}

.dp-popup a {
  color: #000000;
  padding: 3px 2px 0;
  text-decoration: none;
}

.dp-popup a.disabled {
  color: #c0c0c0;
  cursor: default;
}

.dp-popup div.dp-nav-next {
  position: absolute;
  right: 4px;
  top: 2px;
  width: 100px;
}

.dp-popup div.dp-nav-next a { float: right; }

.dp-popup div.dp-nav-next a, .dp-popup div.dp-nav-prev a, .dp-popup td { cursor: pointer; }

.dp-popup div.dp-nav-next a.disabled, .dp-popup div.dp-nav-prev a.disabled, .dp-popup td.disabled { cursor: default; }

.dp-popup div.dp-nav-prev {
  left: 4px;
  position: absolute;
  top: 2px;
  width: 108px;
}

.dp-popup div.dp-nav-prev a { float: left; }

.dp-popup h2 {
  font-size: 12px;
  margin: 2px 0;
  padding: 0;
  text-align: center;
}
</style>
<?php }


function commerce_filter_data($filter, $data) {
if (is_string($filter)) { $filter = preg_split('#[^a-zA-Z0-9_]#', str_replace('-', '_', $filter)); }
if (is_array($filter)) { foreach ($filter as $function) { $data = commerce_string_map($function, $data); } }
return $data; }


function commerce_fix_url() {
$url = $_SERVER['REQUEST_URI'];
if (strstr($url, '&amp;')) { $url = str_replace('&amp;', '&', $url); $error = true; }
if ((strstr($url, '?')) && (!strstr($url, '/?')) && (!strstr($url, '.php?'))) { $url = str_replace('?', '/?', $url); $error = true; }
if (($error) && (!headers_sent())) { header('Location: '.$url); exit; } }


function commerce_format_data($field, $data) {
$data = commerce_quotes_entities_decode(do_shortcode($data));
if ((strstr($field, 'date')) && ($data == '0000-00-00 00:00:00')) { $data = ''; }
if (strstr($field, 'email_address')) { $data = commerce_format_email_address($data); }
elseif (($field == 'url') || (strstr($field, '_url'))) { $data = commerce_format_url($data); }
switch ($field) {
case 'cookies_lifetime': case 'product_id': case 'quantity': $data = (int) $data; break;
case 'amount': case 'commission_amount': case 'commission_percentage':
case 'price': case 'shipping_cost': case 'tax': case 'tax_percentage':
case 'transaction_cost': $data = round(100*$data)/100; }
return $data; }


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
string = string.replace(/[à]/gi, '@');
string = string.replace(/[;]/gi, '.');
string = string.replace(/[ ]/gi, '');
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
string = string.replace(/[ _]/gi, '-');
var strings = string.split('-');
var n = strings.length;
var i = 0; while (i != n) { strings[i] = (strings[i]).substr(0, 1).toUpperCase()+(strings[i]).substr(1); i = i + 1; }
string = strings.join('-');
return string; }
</script>
<?php }


function commerce_format_nice_name($string) {
$string = commerce_strip_accents(strtolower(trim(strip_tags($string))));
$string = str_replace(' ', '-', $string);
$string = preg_replace('/[^a-zA-Z0-9_-]/', '', $string);
return $string; }


function commerce_format_nice_name_js() { ?>
<script type="text/javascript">
function commerce_format_nice_name(string) {
string = commerce_strip_accents(string.toLowerCase());
string = string.replace(/[ ]/gi, '-');
string = string.replace(/[^a-zA-Z0-9_-]/gi, '');
return string; }
</script>
<?php }


function commerce_format_url($string) {
if ($string != '') {
$string = trim(strip_tags($string));
$string = str_replace(' ', '-', $string);
if (!strstr($string, 'http')) {
if (substr($string, 0, 3) == 'www') { $string = 'http://'.$string; }
else { $string = 'http://'.$_SERVER['SERVER_NAME'].'/'.$string; } }
while (strstr($string, '//')) { $string = str_replace('//', '/', $string); }
$string = str_replace(':/', '://', $string); }
return $string; }


function commerce_i18n($string) {
$strings = array(
__('affiliate', 'commerce-manager'),
__('affiliator', 'commerce-manager'),
__('constant', 'commerce-manager'),
__('deferred', 'commerce-manager'),
__('first', 'commerce-manager'),
__('instant', 'commerce-manager'),
__('last', 'commerce-manager'),
__('no', 'commerce-manager'),
__('paid', 'commerce-manager'),
__('processed', 'commerce-manager'),
__('proportional', 'commerce-manager'),
__('refunded', 'commerce-manager'),
__('unpaid', 'commerce-manager'),
__('unprocessed', 'commerce-manager'),
__('yes', 'commerce-manager'));
return __(__($string), 'commerce-manager'); }


function commerce_instructions() {
global $post, $products_table_name, $wpdb;
if (is_page() || is_single()) {
$product_id = (int) do_shortcode(get_post_meta($post->ID, 'product_id', true));
if ($product_id > 0) { $_GET['product_id'] = $product_id; } } }


function commerce_quotes_entities($string) {
return str_replace(array("'", '"'), array("&apos;", '&quot;'), $string); }


function commerce_quotes_entities_decode($string) {
return str_replace(array("&apos;", '&quot;'), array("'", '"'), $string); }


function commerce_statistics_form_js() { ?>
<script type="text/javascript" src="<?php echo COMMERCE_MANAGER_URL; ?>jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="<?php echo COMMERCE_MANAGER_URL; ?>jquery-date-picker.js"></script>
<script type="text/javascript">
Date.dayNames = ['<?php _e('Sunday', 'commerce-manager'); ?>', '<?php _e('Monday', 'commerce-manager'); ?>', '<?php _e('Tuesday', 'commerce-manager'); ?>', '<?php _e('Wednesday', 'commerce-manager'); ?>', '<?php _e('Thursday', 'commerce-manager'); ?>', '<?php _e('Friday', 'commerce-manager'); ?>', '<?php _e('Saturday', 'commerce-manager'); ?>'];
Date.abbrDayNames = ['<?php _e('Sun', 'commerce-manager'); ?>', '<?php _e('Mon', 'commerce-manager'); ?>', '<?php _e('Tue', 'commerce-manager'); ?>', '<?php _e('Wed', 'commerce-manager'); ?>', '<?php _e('Thu', 'commerce-manager'); ?>', '<?php _e('Fri', 'commerce-manager'); ?>', '<?php _e('Sat', 'commerce-manager'); ?>'];
Date.monthNames = ['<?php _e('January', 'commerce-manager'); ?>', '<?php _e('February', 'commerce-manager'); ?>', '<?php _e('March', 'commerce-manager'); ?>', '<?php _e('April', 'commerce-manager'); ?>', '<?php _e('May', 'commerce-manager'); ?>', '<?php _e('June', 'commerce-manager'); ?>', '<?php _e('July', 'commerce-manager'); ?>', '<?php _e('August', 'commerce-manager'); ?>', '<?php _e('September', 'commerce-manager'); ?>', '<?php _e('October', 'commerce-manager'); ?>', '<?php _e('November', 'commerce-manager'); ?>', '<?php _e('December', 'commerce-manager'); ?>'];
Date.abbrMonthNames = ['<?php _e('Jan', 'commerce-manager'); ?>', '<?php _e('Feb', 'commerce-manager'); ?>', '<?php _e('Mar', 'commerce-manager'); ?>', '<?php _e('Apr', 'commerce-manager'); ?>', '<?php _e('May', 'commerce-manager'); ?>', '<?php _e('Jun', 'commerce-manager'); ?>', '<?php _e('Jul', 'commerce-manager'); ?>', '<?php _e('Aug', 'commerce-manager'); ?>', '<?php _e('Sep', 'commerce-manager'); ?>', '<?php _e('Oct', 'commerce-manager'); ?>', '<?php _e('Nov', 'commerce-manager'); ?>', '<?php _e('Dec', 'commerce-manager'); ?>'];
$.dpText = {
TEXT_PREV_YEAR : '<?php _e('Previous year', 'commerce-manager'); ?>',
TEXT_PREV_MONTH : '<?php _e('Previous month', 'commerce-manager'); ?>',
TEXT_NEXT_YEAR : '<?php _e('Next year', 'commerce-manager'); ?>',
TEXT_NEXT_MONTH : '<?php _e('Next month', 'commerce-manager'); ?>',
TEXT_CLOSE : '<?php _e('Close', 'commerce-manager'); ?>',
TEXT_CHOOSE_DATE : '<?php _e('Choose a date', 'commerce-manager'); ?>',
DATE_PICKER_ALT : '<?php _e('Date', 'commerce-manager'); ?>',
DATE_PICKER_URL : '<?php echo COMMERCE_MANAGER_URL; ?>images/date-picker.png',
HEADER_FORMAT : 'mmmm yyyy'
}; $(function(){ $('.date-pick').datePicker({startDate:'2011-01-01'}); });
</script>
<?php }


function commerce_string_map($function, $string) {
if (!function_exists($function)) { $function = 'commerce_'.$function; }
if (function_exists($function)) { $array = array_map($function, array($string)); $string = $array[0]; }
return $string; }


function commerce_strip_accents($string) {
return str_replace(
explode(' ', 'á à â ä ã å ç é è ê ë í ì î ï ñ ó ò ô ö õ ø ú ù û ü ý ÿ Á À Â Ä Ã Å Ç É È Ê Ë Í Ì Î Ï Ñ Ó Ò Ô Ö Õ Ø Ú Ù Û Ü Ý Ÿ'),
explode(' ', 'a a a a a a c e e e e i i i i n o o o o o o u u u u y y A A A A A A C E E E E I I I I N O O O O O O U U U U Y Y'),
$string); }


function commerce_strip_accents_js() { ?>
<script type="text/javascript">
function commerce_strip_accents(string) {
string = string.replace(/[áàâäãå]/gi, 'a');
string = string.replace(/[ç]/gi, 'c');
string = string.replace(/[éèêë]/gi, 'e');
string = string.replace(/[íìîï]/gi, 'i');
string = string.replace(/[ñ]/gi, 'n');
string = string.replace(/[óòôöõø]/gi, 'o');
string = string.replace(/[úùûü]/gi, 'u');
string = string.replace(/[ýÿ]/gi, 'y');
string = string.replace(/[ÁÀÂÄÃÅ]/gi, 'A');
string = string.replace(/[Ç]/gi, 'C');
string = string.replace(/[ÉÈÊË]/gi, 'E');
string = string.replace(/[ÍÌÎÏ]/gi, 'I');
string = string.replace(/[Ñ]/gi, 'N');
string = string.replace(/[ÓÒÔÖÕØ]/gi, 'O');
string = string.replace(/[ÚÙÛÜ]/gi, 'U');
string = string.replace(/[ÝŸ]/gi, 'Y');
return string; }
</script>
<?php }


function inventory_counter($atts, $content) {
global $products_table_name, $wpdb;
if ((isset($_GET['product_id'])) && ($_GET['product_data']->id != $_GET['product_id'])) {
$_GET['product_data'] = $wpdb->get_row("SELECT * FROM $products_table_name WHERE id = '".$_GET['product_id']."'", OBJECT); }
$product_data = $_GET['product_data'];
extract(shortcode_atts(array('id' => 0, 'field' => '', 'limit' => ''), $atts));
$field = str_replace('-', '_', commerce_format_nice_name($field));
if (($field == '') || ($field == 'sales')) { $field = 'sales_count'; }
elseif ($field == 'refunds') { $field = 'refunds_count'; }
if ($limit == '') { $limit = '0'; }
else { $limit = '0/'.$limit; }
$limit = preg_split('#[^0-9]#', $limit);
$n = count($limit);

if (($id == 0) || ($id == $product_data->id)) { $data = $product_data->$field; }
else {
if (isset($_GET['product_id'])) { $original_product_id = $_GET['product_id']; }
if (isset($_GET['product_data'])) { $original_product_data = $_GET['product_data']; }
$product_data = $wpdb->get_row("SELECT * FROM $products_table_name WHERE id = '$id'", OBJECT);
$_GET['product_id'] = $id; $_GET['product_data'] = $product_data;
$data = $product_data->$field; }

$i = 0; while (($i < $n) && ($limit[$i] <= $data)) { $k = $i; $i = $i + 1; }
if ($i < $n) { $remaining_number = $limit[$i] - $data; $total_remaining_number = $limit[$n - 1] - $data; }
else { $i = $n - 1; $remaining_number = 0; $total_remaining_number = 0; }

$content = explode('[after]', do_shortcode($content));
$content[$k] = str_ireplace('[limit]', $limit[$i], $content[$k]);
$content[$k] = str_ireplace('[total-limit]', $limit[$n - 1], $content[$k]);
$content[$k] = str_ireplace('[number]', $data - $limit[$k], $content[$k]);
$content[$k] = str_ireplace(array('[total-number]', '['.str_replace('_', '-', $field).']'), $data, $content[$k]);
$content[$k] = str_ireplace('[remaining-number]', $remaining_number, $content[$k]);
$content[$k] = str_ireplace('[total-remaining-number]', $total_remaining_number, $content[$k]);

if (isset($original_product_id)) { $_GET['product_id'] = $original_product_id; }
if (isset($original_product_data)) { $_GET['product_data'] = $original_product_data; }
return $content[$k]; }

add_shortcode('inventory-counter', 'inventory_counter');


function order_data($atts) {
global $orders_table_name, $wpdb;
if ((isset($_GET['order_id'])) && ($_GET['order_data']->id != $_GET['order_id'])) {
$_GET['order_data'] = $wpdb->get_row("SELECT * FROM $orders_table_name WHERE id = '".$_GET['order_id']."'", OBJECT); }
$order_data = $_GET['order_data'];
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $id = 0; }
else { $field = $atts[0]; $default = $atts['default']; $filter = $atts['filter']; $id = (int) $atts['id']; }
$field = str_replace('-', '_', commerce_format_nice_name($field));
if ($field == '') { $field = 'first_name'; }
if (($id == 0) || ($id == $order_data->id)) { $data = $order_data->$field; }
else {
if (isset($_GET['order_id'])) { $original_order_id = $_GET['order_id']; }
if (isset($_GET['order_data'])) { $original_order_data = $_GET['order_data']; }
$order_data = $wpdb->get_row("SELECT * FROM $orders_table_name WHERE id = '$id'", OBJECT);
$_GET['order_id'] = $id; $_GET['order_data'] = $order_data;
$data = $order_data->$field; }
$data = (string) $data;
if ($data == '') { $data = $default; }
$data = commerce_format_data($field, $data);
$data = commerce_filter_data($filter, $data);
if (isset($original_order_id)) { $_GET['order_id'] = $original_order_id; }
if (isset($original_order_data)) { $_GET['order_data'] = $original_order_data; }
return $data; }

add_shortcode('customer', 'order_data');
add_shortcode('order', 'order_data');


function product_data($atts) {
global $products_table_name, $wpdb;
if ((isset($_GET['product_id'])) && ($_GET['product_data']->id != $_GET['product_id'])) {
$_GET['product_data'] = $wpdb->get_row("SELECT * FROM $products_table_name WHERE id = '".$_GET['product_id']."'", OBJECT); }
$product_data = $_GET['product_data'];
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $id = 0; }
else { $field = $atts[0]; $default = $atts['default']; $filter = $atts['filter']; $id = (int) $atts['id']; }
$field = str_replace('-', '_', commerce_format_nice_name($field));
if ($field == '') { $field = 'name'; }
if (($id == 0) || ($id == $product_data->id)) { $data = $product_data->$field; }
else {
if (isset($_GET['product_id'])) { $original_product_id = $_GET['product_id']; }
if (isset($_GET['product_data'])) { $original_product_data = $_GET['product_data']; }
$product_data = $wpdb->get_row("SELECT * FROM $products_table_name WHERE id = '$id'", OBJECT);
$_GET['product_id'] = $id; $_GET['product_data'] = $product_data;
$data =  $product_data->$field; }
if ($data != '') { $data = commerce_format_data($field, $data); }
$data = (string) $data;
if ($data == '') { switch ($field) {
case 'affiliation_enabled': case 'commission_amount': case 'commission_payment':
case 'commission_percentage': case 'commission_type': case 'first_sale_winner':
case 'registration_required': if (function_exists('affiliate_data')) { $data = affiliate_data($field); } break;
default: $data = commerce_data($field); } }
$data = (string) $data;
if ($data == '') { $data = $default; }
$data = commerce_format_data($field, $data);
$data = commerce_filter_data($filter, $data);
if (isset($original_product_id)) { $_GET['product_id'] = $original_product_id; }
if (isset($original_product_data)) { $_GET['product_data'] = $original_product_data; }
return $data; }

add_shortcode('product', 'product_data');


function purchase_button($atts) {
extract(shortcode_atts(array('id' => 0, 'src' => '', 'alt' => '', 'gateway' => 'paypal', 'quantity' => 1), $atts));
$quantity = (int) $quantity;
$gateway = str_replace('_', '-', commerce_format_nice_name($gateway));
$id = product_data(array(0 => 'id', 'id' => $id));
if ($src == '') { $src = product_data(array(0 => 'purchase_button_url', 'id' => $id)); }
if ($alt == '') { $alt = product_data(array(0 => 'purchase_button_text', 'id' => $id)); }
return '<form method="post" action="'.COMMERCE_MANAGER_URL.'?action=order">
<p><input type="hidden" name="code" value="'.$_GET['code'].'" />
<input type="hidden" name="gateway" value="'.$gateway.'" />
<input type="hidden" name="product_id" value="'.$id.'" />
<input type="hidden" name="quantity" value="'.$quantity.'" />
<input type="hidden" name="referring_url" value="'.htmlspecialchars($_SERVER['HTTP_REFERER']).'" />
<input type="image" name="purchase" src="'.htmlspecialchars($src).'" alt="'.$alt.'" /></p>
</form>'; }

add_shortcode('purchase-button', 'purchase_button');


function refunds_counter($atts, $content) {
$atts['field'] = 'refunds_count';
return inventory_counter($atts, $content); }

add_shortcode('refunds-counter', 'refunds_counter');


function sales_counter($atts, $content) {
$atts['field'] = 'sales_count';
return inventory_counter($atts, $content); }

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