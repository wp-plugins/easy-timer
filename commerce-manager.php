<?php
/*
Plugin Name: Commerce Manager
Plugin URI: http://www.kleor-editions.com/commerce-manager
Description: Allows you to sell your products and manage your orders.
Version: 4.7
Author: Kleor
Author URI: http://www.kleor-editions.com
Text Domain: commerce-manager
*/


if (!defined('HOME_URL')) { define('HOME_URL', get_option('home')); }
if (!defined('UTC_OFFSET')) { define('UTC_OFFSET', get_option('gmt_offset')); }
define('COMMERCE_MANAGER_URL', plugin_dir_url(__FILE__));
$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
define('COMMERCE_MANAGER_VERSION', $plugin_data['Version']);

if (!function_exists('fix_url')) { include_once dirname(__FILE__).'/libraries/formatting-functions.php'; }
if (is_admin()) { include_once dirname(__FILE__).'/admin.php'; }

global $wpdb;
$commerce_manager_options = array_merge((array) get_option('commerce_manager'), (array) get_option('commerce_manager_clients_accounts'));
if (((is_multisite()) || ($commerce_manager_options)) && ($commerce_manager_options['version'] != COMMERCE_MANAGER_VERSION)) {
include_once dirname(__FILE__).'/admin.php';
install_commerce_manager(); }

fix_url();
commerce_session();
if (!is_admin()) {
add_shortcode('commerce-redirection', create_function('$atts', 'include_once dirname(__FILE__)."/shortcodes.php"; return commerce_redirection($atts);'));
if (!defined('POST_ID')) {
$root_url = explode('/', str_replace('//', '||', HOME_URL));
$root_url = str_replace('||', '//', $root_url[0]);
$path = explode('?', strtolower($_SERVER['REQUEST_URI']));
$path = explode('#', $path[0]);
$path = str_replace(HOME_URL, '', $root_url.$path[0]);
while (substr($path, 0, 1) == '/') { $path = substr($path, 1); }
while (substr($path, -1) == '/') { $path = substr($path, 0, -1); }
$post = get_page_by_path($path);
$_GET['post_data'] = (array) $post;
$id = (int) $post->ID;
if ($id == 0) { $id = (int) $_GET['page_id']; }
define('POST_ID', $id); }
if (POST_ID > 0) {
do_shortcode(get_post_meta(POST_ID, 'commerce', true));
$product_id = (int) do_shortcode(get_post_meta(POST_ID, 'product_id', true)); }
if ($product_id > 0) { $_GET['product_id'] = $product_id; }
if (commerce_session()) {
if ($_GET['client_data']['login'] != $_SESSION['commerce_login']) {
$_GET['client_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_clients WHERE login = '".$_SESSION['commerce_login']."'", OBJECT); }
if ((function_exists('affiliation_session')) && (!affiliation_session())) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".$_GET['client_data']['email_address']."' AND status = 'active'", OBJECT);
if ($result) { $_GET['affiliate_data'] = (array) $result; $_SESSION['affiliation_login'] = $result->login; } }
if ((function_exists('membership_session')) && (!membership_session(''))) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members WHERE email_address = '".$_GET['client_data']['email_address']."' AND status = 'active'", OBJECT);
if ($result) { $_GET['member_data'] = (array) $result; $_SESSION['membership_login'] = $result->login; } } } }


function add_client($client) { include dirname(__FILE__).'/add-client.php'; }


function add_order($order) { include dirname(__FILE__).'/add-order.php'; }


function clients_categories_list($id) {
global $wpdb;
$id = (int) $id;
$list = array($id);
while ($id > 0) {
$category = $wpdb->get_row("SELECT category_id FROM ".$wpdb->prefix."commerce_manager_clients_categories WHERE id = $id", OBJECT);
if ($category) { $id = $category->category_id; }
if ((!$category) || (in_array($id, $list))) { $id = 0; }
$list[] = $id; }
return $list; }


function commerce_comments_array() { return array(); }

function commerce_comments_open() { return false; }

function commerce_post_comments() {
global $post;
foreach (array('comments_array', 'comments_open') as $function) { remove_filter($function, 'commerce_'.$function); }
do_shortcode(get_post_meta($post->ID, 'commerce', true)); }

add_filter('the_post', 'commerce_post_comments');


function commerce_data($atts) {
global $commerce_manager_options;
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $part = 0; }
else { $field = $atts[0]; $default = $atts['default']; $filter = $atts['filter']; $part = (int) $atts['part']; }
$field = str_replace('-', '_', format_nice_name($field));
if ($field == '') { $field = 'version'; }
if (($field == 'code') || (substr($field, -10) == 'email_body') || (substr($field, -19) == 'custom_instructions')) { $data = get_option('commerce_manager_'.$field); }
else { $data = $commerce_manager_options[$field]; }
if (($data == '') && (is_numeric(substr($field, -1)))) { $data = $commerce_manager_options[substr($field, 0, -1)]; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = commerce_format_data($field, $data);
$data = commerce_filter_data($filter, $data);
return $data; }


function commerce_date_picker_css() {
global $post;
if (strstr($post->post_content, '[commerce-statistics-form')) {
echo do_shortcode(get_option('commerce_manager_date_picker_css')); } }

add_action('wp_head', 'commerce_date_picker_css');


function commerce_date_picker_js() {
echo do_shortcode(get_option('commerce_manager_date_picker_js')); }


function commerce_decrypt_url($url) {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$url = explode('?url=', $url);
$url = $url[1];
$url = base64_decode($url);
$url = trim(mcrypt_decrypt(MCRYPT_BLOWFISH, md5(commerce_data('encrypted_urls_key')), $url, MCRYPT_MODE_ECB));
$url = explode('|', $url);
$T = $url[0];
$url = $url[1];
$S = time() - $T;
if ($S > 3600*commerce_data('encrypted_urls_validity_duration')) { $url = HOME_URL; }
return $url; }


function commerce_encrypt_url($url) {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$url = time().'|'.$url;
$url = mcrypt_encrypt(MCRYPT_BLOWFISH, md5(commerce_data('encrypted_urls_key')), $url, MCRYPT_MODE_ECB);
$url = base64_encode($url);
$url = COMMERCE_MANAGER_URL.'?url='.$url;
return $url; }


function commerce_filter_data($filter, $data) {
if (is_string($filter)) { $filter = preg_split('#[^a-zA-Z0-9_]#', str_replace('-', '_', $filter), 0, PREG_SPLIT_NO_EMPTY); }
if (is_array($filter)) { foreach ($filter as $function) { $data = commerce_string_map($function, $data); } }
return $data; }


function commerce_format_data($field, $data) {
$data = quotes_entities_decode(do_shortcode($data));
if ((strstr($field, 'date')) && ($data == '0000-00-00 00:00:00')) { $data = ''; }
elseif (substr($field, -13) == 'email_address') { $data = format_email_address($data); }
elseif (substr($field, -12) == 'instructions') { $data = format_instructions($data); }
elseif ((($field == 'url') || (strstr($field, '_url'))) && (!strstr($field, 'urls'))) { $data = format_url($data); }
switch ($field) {
case 'default_payment_option': $data = (int) $data; break;
case 'available_quantity': case 'payments_number': case 'payments_number1':
case 'payments_number2': case 'payments_number3': if ($data != 'unlimited') { $data = (int) $data; } break;
case 'default_product_id': case 'default_quantity':
case 'first_payment_period_quantity': case 'first_payment_period_quantity1':
case 'first_payment_period_quantity2': case 'first_payment_period_quantity3':
case 'payments_period_quantity': case 'payments_period_quantity1':
case 'payments_period_quantity2': case 'payments_period_quantity3': $data = (int) $data; if ($data < 1) { $data = 1; } break;
case 'amount': case 'commission_amount': case 'commission_percentage': case 'commission2_amount':
case 'commission2_percentage': case 'encrypted_urls_validity_duration':
case 'first_payment_amount': case 'first_payment_amount1': case 'first_payment_amount2': case 'first_payment_amount3':
case 'first_payment_normal_amount': case 'first_payment_normal_amount1': case 'first_payment_normal_amount2': case 'first_payment_normal_amount3':
case 'payments_amount': case 'payments_amount1': case 'payments_amount2': case 'payments_amount3':
case 'payments_normal_amount': case 'payments_normal_amount1': case 'payments_normal_amount2': case 'payments_normal_amount3':
case 'price': case 'normal_price': case 'shipping_cost': case 'tax': case 'tax_percentage': case 'transaction_cost':
case 'weight': $data = round(100*$data)/100; }
return $data; }


function commerce_forms_categories_list($id) {
global $wpdb;
$id = (int) $id;
$list = array($id);
while ($id > 0) {
$category = $wpdb->get_row("SELECT category_id FROM ".$wpdb->prefix."commerce_manager_forms_categories WHERE id = $id", OBJECT);
if ($category) { $id = $category->category_id; }
if ((!$category) || (in_array($id, $list))) { $id = 0; }
$list[] = $id; }
return $list; }


function commerce_i18n($string) {
load_plugin_textdomain('commerce-manager', false, 'commerce-manager/languages');
return __(__($string), 'commerce-manager'); }


function commerce_user_data($atts) { include dirname(__FILE__).'/user-data.php'; return $data; }


function commerce_item_data($type, $atts) { include dirname(__FILE__).'/item-data.php'; return $data; }


function client_data($atts) {
if ((is_array($atts)) && (!isset($atts['id'])) && (isset($atts['category']))) { return client_category_data($atts); }
else { return commerce_item_data('client', $atts); } }


function client_category_data($atts) {
return commerce_item_data('client_category', $atts); }


function commerce_form_data($atts) {
if ((is_array($atts)) && (!isset($atts[0]))) { include_once dirname(__FILE__).'/forms.php'; return commerce_form($atts); }
elseif ((is_array($atts)) && (!isset($atts['id'])) && (isset($atts['category']))) { return commerce_form_category_data($atts); }
else { return commerce_item_data('commerce_form', $atts); } }


function commerce_form_category_data($atts) {
return commerce_item_data('commerce_form_category', $atts); }


function order_data($atts) {
return commerce_item_data('order', $atts); }


function product_data($atts) {
if ((is_array($atts)) && (!isset($atts['id'])) && (isset($atts['category']))) { return product_category_data($atts); }
else { return commerce_item_data('product', $atts); } }


function product_category_data($atts) {
return commerce_item_data('product_category', $atts); }


function recurring_payment_data($atts) {
return commerce_item_data('recurring_payment', $atts); }


function commerce_jquery_js() {
if (!defined('KLEOR_JQUERY_LOADED')) { define('KLEOR_JQUERY_LOADED', true); ?>
<script type="text/javascript" src="<?php echo COMMERCE_MANAGER_URL; ?>libraries/jquery.js"></script>
<?php } }


function commerce_login_through_wordpress() {
global $user_email, $wpdb;
if ((!commerce_session()) && ($_SESSION['commerce_login'] != 'NONE') && (is_user_logged_in())) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_clients WHERE email_address = '".$user_email."'", OBJECT);
if ($result) { $_GET['client_data'] = (array) $result; $_SESSION['commerce_login'] = $result->login; }
else { $_SESSION['commerce_login'] = 'NONE'; } } }

add_action('plugins_loaded', 'commerce_login_through_wordpress', 1, 0);


function commerce_logout_through_wordpress() {
foreach (array('affiliation', 'commerce', 'membership') as $prefix) {
unset($_SESSION[$prefix.'_login']);
setcookie($prefix.'_login', '', time() - 86400, '/'); } }

add_action('wp_logout', 'commerce_logout_through_wordpress', 10, 0);


function commerce_logout() {
if (!headers_sent()) { session_start(); }
if (commerce_session()) {
if ((!defined('COMMERCE_MANAGER_DEMO')) || (COMMERCE_MANAGER_DEMO == false)) {
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = commerce_data('logout_notification_email_'.$field); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); }
if (commerce_data('logout_custom_instructions_executed') == 'yes') {
eval(format_instructions(commerce_data('logout_custom_instructions'))); } } }
include_once ABSPATH.'wp-includes/pluggable.php';
if (function_exists('wp_logout')) { wp_logout(); }
commerce_logout_through_wordpress(); }


function commerce_session() {
global $wpdb;
if (!headers_sent()) { session_start(); }
if ((isset($_COOKIE['commerce_login'])) && ((!isset($_SESSION['commerce_login'])) || ($_SESSION['commerce_login'] == 'NONE'))) {
$login = substr($_COOKIE['commerce_login'], 0, -64);
if (substr($_COOKIE['commerce_login'], -64) == hash('sha256', $login.AUTH_KEY)) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_clients WHERE login = '".$login."' AND status = 'active'", OBJECT);
if ($result) { $_GET['client_data'] = (array) $result; $_SESSION['commerce_login'] = $login; }
else { setcookie('commerce_login', '', time() - 86400, '/'); } } }	
if ((isset($_SESSION['commerce_login'])) && ($_SESSION['commerce_login'] != 'NONE')) { return true; } else { return false; } }


function commerce_sql_array($table, $array) {
foreach ($table as $key => $value) {
$sql[$key] = ($key == 'password' ? hash('sha256', $array[$key]) : $array[$key]);
if ($value['type'] == 'int') { $sql[$key] = (int) $sql[$key]; }
elseif ((strstr($value['type'], 'dec')) && (!is_numeric($sql[$key]))) { $sql[$key] = round(100*$sql[$key])/100; }
elseif (($value['type'] == 'text') || ($value['type'] == 'datetime')) { $sql[$key] = "'".$sql[$key]."'"; } }
return $sql; }


function commerce_string_map($function, $string) {
if (!function_exists($function)) { $function = 'commerce_'.$function; }
if (function_exists($function)) { $array = array_map($function, array($string)); $string = $array[0]; }
return $string; }


function products_categories_list($id) {
global $wpdb;
$id = (int) $id;
$list = array($id);
while ($id > 0) {
$category = $wpdb->get_row("SELECT category_id FROM ".$wpdb->prefix."commerce_manager_products_categories WHERE id = $id", OBJECT);
if ($category) { $id = $category->category_id; }
if ((!$category) || (in_array($id, $list))) { $id = 0; }
$list[] = $id; }
return $list; }


class Commerce_Meta_Widget extends WP_Widget {
function __construct() {
parent::WP_Widget('commerce-meta-widget', __('Meta (Commerce)', 'commerce-manager'), array('description' => __('Log in/out', 'commerce-manager'))); }

function form($instance) {
include 'initial-options.php';
$instance = wp_parse_args($instance, $initial_options['meta_widget']);
$title = esc_attr($instance['title']);
$content = esc_attr($instance['content']); ?>
<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'commerce-manager'); ?>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</label></p>
<p><label for="<?php echo $this->get_field_id('content'); ?>"><?php _e('Content:', 'commerce-manager'); ?>
<textarea class="widefat" id="<?php echo $this->get_field_id('content'); ?>" name="<?php echo $this->get_field_name('content'); ?>" cols="20" rows="10"><?php echo $content; ?></textarea>
</label></p><?php }

function update($instance, $old_instance) {
include 'initial-options.php';
foreach ($instance as $key => $value) { if ($value == '') { $instance[$key] = $initial_options['meta_widget'][$key]; } }
return $instance; }

function widget($args, $instance) {
extract($args);
include 'initial-options.php';
foreach ($instance as $key => $value) { if ($value == '') { $instance[$key] = $initial_options['meta_widget'][$key]; } }
$title = apply_filters('widget_title', $instance['title']);
$content = apply_filters('widget_text', $instance['content']);
echo $before_widget.$before_title.$title.$after_title.$content.$after_widget; }
}

add_action('widgets_init', create_function('', 'return register_widget("Commerce_Meta_Widget");'));


for ($i = 0; $i < 4; $i++) {
foreach (array(
'refunds' => '$atts["data"] = "refunds_count"; ',
'sales' => '$atts["data"] = "sales_count"; ') as $key => $value) {
add_shortcode($key.'-counter'.($i == 0 ? '' : $i), create_function('$atts, $content', $value.'include_once dirname(__FILE__)."/shortcodes.php"; return inventory_counter($atts, $content);')); }
foreach (array('client-counter', 'commerce-content', 'commerce-counter', 'commerce-form-counter', 'product-counter', 'purchase-content', 'purchase-form-counter') as $tag) {
add_shortcode($tag.($i == 0 ? '' : $i), create_function('$atts, $content', 'include_once dirname(__FILE__)."/shortcodes.php"; return '.($tag == 'purchase-form-counter' ? 'commerce_form_counter' : str_replace('-', '_', $tag)).'($atts, $content);')); } }

foreach (array(
'login-' => '$atts["type"] = "login"; ',
'login-compact-' => '$atts["size"] = "compact"; $atts["type"] = "login"; ',
'login-widget-' => '$atts["size"] = "compact"; $atts["type"] = "login"; ',
'password-reset-' => '$atts["type"] = "password_reset"; ',
'profile-' => '$atts["type"] = "profile"; ',
'profile-edit-' => '$atts["type"] = "profile"; ',
'redelivery-' => '$atts["type"] = "redelivery"; ',
'registration-' => '$atts["type"] = "registration"; ',
'registration-compact-' => '$atts["size"] = "compact"; $atts["type"] = "registration"; ',
'statistics-' => '$atts["type"] = "statistics"; ') as $key => $value) {
add_shortcode('commerce-'.$key.'form', create_function('$atts', $value.'include_once dirname(__FILE__)."/forms.php"; return commerce_form($atts);')); }

foreach (array(
'' => '',
'global-' => '$atts["type"] = "global"; ',
'orders-' => '$atts["type"] = "orders"; ',
'recurring-payments-' => '$atts["type"] = "recurring_payments"; ') as $key => $value) {
add_shortcode('commerce-'.$key.'statistics', create_function('$atts', $value.'include_once dirname(__FILE__)."/statistics.php"; return commerce_statistics($atts);')); }

foreach (array('commerce-activation-url', 'commerce-comments') as $tag) {
add_shortcode($tag, create_function('$atts', 'include_once dirname(__FILE__)."/shortcodes.php"; return '.str_replace('-', '_', $tag).'($atts);')); }

foreach (array('commerce-payment-mode-selector', 'commerce-product-selector') as $tag) {
add_shortcode($tag, create_function('$atts', 'include_once dirname(__FILE__)."/forms.php"; return '.str_replace('-', '_', $tag).'($atts);')); }
foreach (array('button', 'link') as $tag) {
add_shortcode('purchase-'.$tag, create_function('$atts', 'include_once dirname(__FILE__)."/shortcodes.php"; return purchase_'.$tag.'($atts);')); }
add_shortcode('user', 'commerce_user_data');
add_shortcode('commerce-manager', 'commerce_data');
foreach (array('commerce', 'purchase') as $string) { add_shortcode($string.'-form', 'commerce_form_data'); }
add_shortcode('commerce-user', 'client_data');
foreach (array('client', 'order', 'product', 'recurring-payment') as $tag) { add_shortcode($tag, str_replace('-', '_', $tag).'_data'); }
add_shortcode('customer', 'order_data');


foreach (array(
'get_the_excerpt',
'get_the_title',
'single_post_title',
'the_excerpt',
'the_excerpt_rss',
'the_title',
'the_title_attribute',
'the_title_rss',
'widget_text',
'widget_title') as $function) { add_filter($function, 'do_shortcode'); }