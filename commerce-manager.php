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


if (!defined('HOME_URL')) { define('HOME_URL', get_option('home')); }
if (!defined('UTC_OFFSET')) { define('UTC_OFFSET', get_option('gmt_offset')); }
define('COMMERCE_MANAGER_URL', plugin_dir_url(__FILE__));

global $wpdb;
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
global $wpdb;
extract(shortcode_atts(array('id' => '', 'src' => 0), $atts));
}

add_shortcode('purchase-button', 'purchase_button');

/* Fin du code à terminer */

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
$data = commerce_format_data($field, $data);
switch ($atts['filter']) {
case 'htmlentities': $data = htmlentities($data); break;
case 'htmlspecialchars': $data = htmlspecialchars($data); break; }
return $data; }

add_shortcode('commerce-manager', 'commerce_data');


function commerce_fix_url() {
$url = $_SERVER['REQUEST_URI'];
if (strstr($url, '&amp;')) { $url = str_replace('&amp;', '&', $url); $error = true; }
if ((strstr($url, '?')) && (!strstr($url, '/?')) && (!strstr($url, '.php?'))) { $url = str_replace('?', '/?', $url); $error = true; }
if (($error) && (!headers_sent())) { header('Location: '.$url); exit; } }


function commerce_format_data($field, $data) {
$data = commerce_quotes_entities_decode(do_shortcode($data));
if (strstr($field, 'email_address')) { $data = commerce_format_email_address($data); }
elseif (($field == 'url') || (strstr($field, '_url'))) { $data = commerce_format_url($data); }
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


function commerce_format_url($string) {
if ($string != '') {
$string = trim(strip_tags($string));
$string = str_replace(' ', '_', $string);
if (!strstr($string, 'http')) {
if (substr($string, 0, 3) == 'www') { $string = 'http://'.$string; }
else { $string = HOME_URL.'/'.$string; } }
while (strstr($string, '//')) { $string = str_replace('//', '/', $string); }
$string = str_replace(':/', '://', $string); }
return $string; }


function commerce_instructions() {
global $post, $products_table_name, $wpdb;
if (is_page() || is_single()) {
$product_id = (int) do_shortcode(get_post_meta($post->ID, 'product_id', true));
if ($product_id > 0) {
$_GET['product_id'] = $product_id;
$_GET['product_data'] = $wpdb->get_row("SELECT * FROM $products_table_name WHERE id = '$product_id'", OBJECT); } } }


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
if (!isset($_GET['product_id'])) { $_GET['product_id'] = $id; }
$product_data = $wpdb->get_row("SELECT * FROM $products_table_name WHERE id = '$id'", OBJECT);
if (!isset($_GET['product_data'])) { $_GET['product_data'] = $product_data; }
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

return $content[$k]; }

add_shortcode('inventory-counter', 'inventory_counter');


function order_data($atts) {
global $orders_table_name, $wpdb;
if ((isset($_GET['order_id'])) && ($_GET['order_data']->id != $_GET['order_id'])) {
$_GET['order_data'] = $wpdb->get_row("SELECT * FROM $orders_table_name WHERE id = '".$_GET['order_id']."'", OBJECT); }
$order_data = $_GET['order_data'];
if (is_string($atts)) { $field = $atts; } else { $field = $atts[0]; }
$field = str_replace('-', '_', commerce_format_nice_name($field));
if ($field == '') { $field = 'first_name'; }
$id = (int) $atts['id'];
if (($id == 0) || ($id == $order_data->id)) { $data = $order_data->$field; }
else {
if (!isset($_GET['order_id'])) { $_GET['order_id'] = $id; }
$order_data = $wpdb->get_row("SELECT * FROM $orders_table_name WHERE id = '$id'", OBJECT);
if (!isset($_GET['order_data'])) { $_GET['order_data'] = $order_data; }
$data = $order_data->$field; }
if ($data == '') { $data = $atts['default']; }
$data = commerce_format_data($field, $data);
switch ($atts['filter']) {
case 'htmlentities': $data = htmlentities($data); break;
case 'htmlspecialchars': $data = htmlspecialchars($data); break; }
return $data; }

add_shortcode('customer', 'order_data');
add_shortcode('order', 'order_data');


function product_data($atts) {
global $products_table_name, $wpdb;
if ((isset($_GET['product_id'])) && ($_GET['product_data']->id != $_GET['product_id'])) {
$_GET['product_data'] = $wpdb->get_row("SELECT * FROM $products_table_name WHERE id = '".$_GET['product_id']."'", OBJECT); }
$product_data = $_GET['product_data'];
if (is_string($atts)) { $field = $atts; } else { $field = $atts[0]; }
$field = str_replace('-', '_', commerce_format_nice_name($field));
if ($field == '') { $field = 'name'; }
$id = (int) $atts['id'];
if (($id == 0) || ($id == $product_data->id)) { $data = $product_data->$field; }
else {
if (!isset($_GET['product_id'])) { $_GET['product_id'] = $id; }
$product_data = $wpdb->get_row("SELECT * FROM $products_table_name WHERE id = '$id'", OBJECT);
if (!isset($_GET['product_data'])) { $_GET['product_data'] = $product_data; }
$data = $product_data->$field; }
$data = do_shortcode($data);
if ($data == '') { switch ($field) {
case 'affiliation_enabled': case 'commission_amount': case 'commission_payment':
case 'commission_percentage': case 'commission_type': case 'first_sale_winner':
case 'registration_required': if (function_exists('affiliate_data')) { $data = affiliate_data($field); } break;
default: $data = commerce_data($field); } }
if ($data == '') { $data = $atts['default']; }
$data = commerce_format_data($field, $data);
switch ($atts['filter']) {
case 'htmlentities': $data = htmlentities($data); break;
case 'htmlspecialchars': $data = htmlspecialchars($data); break; }
return $data; }

add_shortcode('product', 'product_data');


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