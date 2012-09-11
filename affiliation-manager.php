<?php
/*
Plugin Name: Affiliation Manager
Plugin URI: http://www.kleor-editions.com/affiliation-manager
Description: Allows you to create and manage your affiliate program.
Version: 4.7
Author: Kleor
Author URI: http://www.kleor-editions.com
Text Domain: affiliation-manager
*/


if (!defined('HOME_URL')) { define('HOME_URL', get_option('home')); }
if (!defined('UTC_OFFSET')) { define('UTC_OFFSET', get_option('gmt_offset')); }
define('AFFILIATION_MANAGER_URL', plugin_dir_url(__FILE__));
$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
define('AFFILIATION_MANAGER_VERSION', $plugin_data['Version']);

if (!function_exists('fix_url')) { include_once dirname(__FILE__).'/libraries/formatting-functions.php'; }
if (is_admin()) { include_once dirname(__FILE__).'/admin.php'; }

global $wpdb;
$affiliation_manager_options = array_merge((array) get_option('affiliation_manager'), (array) get_option('affiliation_manager_instant_notifications'));
if (((is_multisite()) || ($affiliation_manager_options)) && ($affiliation_manager_options['version'] != AFFILIATION_MANAGER_VERSION)) {
include_once dirname(__FILE__).'/admin.php';
install_affiliation_manager(); }

foreach (array('cookies_name', 'url_variable_name', 'url_variable_name2') as $option) {
define('AFFILIATION_'.strtoupper($option), affiliation_data($option)); }

fix_url();
affiliation_session();


if (!is_admin()) {
$a = AFFILIATION_URL_VARIABLE_NAME;
$e = AFFILIATION_URL_VARIABLE_NAME2;
if (($_GET[$a] == '') && ($_GET[$e] != '')) { $_GET[$a] = $_GET[$e]; }
if ($_GET[$a] != '') { include dirname(__FILE__).'/tracking.php'; }
add_shortcode('affiliation-redirection', create_function('$atts', 'include_once dirname(__FILE__)."/shortcodes.php"; return affiliation_redirection($atts);'));
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
if (POST_ID > 0) { do_shortcode(get_post_meta(POST_ID, 'affiliation', true)); }
if (affiliation_session()) {
if ($_GET['affiliate_data']['login'] != $_SESSION['affiliation_login']) {
$_GET['affiliate_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_SESSION['affiliation_login']."'", OBJECT); }
if ((function_exists('commerce_session')) && (!commerce_session())) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_clients WHERE email_address = '".$_GET['affiliate_data']['email_address']."' AND status = 'active'", OBJECT);
if ($result) { $_GET['client_data'] = (array) $result; $_SESSION['commerce_login'] = $result->login; } }
if ((function_exists('membership_session')) && (!membership_session(''))) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members WHERE email_address = '".$_GET['affiliate_data']['email_address']."' AND status = 'active'", OBJECT);
if ($result) { $_GET['member_data'] = (array) $result; $_SESSION['membership_login'] = $result->login; } } }
$_GET['referrer'] = $_COOKIE[AFFILIATION_COOKIES_NAME]; }


function add_affiliate($affiliate) { include dirname(__FILE__).'/add-affiliate.php'; }


function affiliates_categories_list($id) {
global $wpdb;
$id = (int) $id;
$list = array($id);
while ($id > 0) {
$category = $wpdb->get_row("SELECT category_id FROM ".$wpdb->prefix."affiliation_manager_affiliates_categories WHERE id = $id", OBJECT);
if ($category) { $id = $category->category_id; }
if ((!$category) || (in_array($id, $list))) { $id = 0; }
$list[] = $id; }
return $list; }


function affiliation_comments_array() { return array(); }

function affiliation_comments_open() { return false; }

function affiliation_post_comments() {
global $post;
foreach (array('comments_array', 'comments_open') as $function) { remove_filter($function, 'affiliation_'.$function); }
do_shortcode(get_post_meta($post->ID, 'affiliation', true)); }

add_filter('the_post', 'affiliation_post_comments');


function affiliation_data($atts) {
global $affiliation_manager_options;
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $part = 0; }
else { $field = $atts[0]; $default = $atts['default']; $filter = $atts['filter']; $part = (int) $atts['part']; }
$field = str_replace('-', '_', format_nice_name($field));
if ($field == '') { $field = 'version'; }
if ((substr($field, -10) == 'email_body') || (substr($field, -19) == 'custom_instructions')) { $data = get_option('affiliation_manager_'.$field); }
else { $data = $affiliation_manager_options[$field]; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = affiliation_format_data($field, $data);
$data = affiliation_filter_data($filter, $data);
return $data; }


function affiliation_date_picker_css() {
global $post;
if (strstr($post->post_content, '[affiliation-statistics-form')) {
echo do_shortcode(get_option('affiliation_manager_date_picker_css')); } }

add_action('wp_head', 'affiliation_date_picker_css');


function affiliation_date_picker_js() {
echo do_shortcode(get_option('affiliation_manager_date_picker_js')); }


function affiliation_decrypt_url($url) {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$url = explode('?url=', $url);
$url = $url[1];
$url = base64_decode($url);
$url = trim(mcrypt_decrypt(MCRYPT_BLOWFISH, md5(affiliation_data('encrypted_urls_key')), $url, MCRYPT_MODE_ECB));
$url = explode('|', $url);
$T = $url[0];
$url = $url[1];
$S = time() - $T;
if ($S > 3600*affiliation_data('encrypted_urls_validity_duration')) { $url = HOME_URL; }
return $url; }


function affiliation_encrypt_url($url) {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$url = time().'|'.$url;
$url = mcrypt_encrypt(MCRYPT_BLOWFISH, md5(affiliation_data('encrypted_urls_key')), $url, MCRYPT_MODE_ECB);
$url = base64_encode($url);
$url = AFFILIATION_MANAGER_URL.'?url='.$url;
return $url; }


function affiliation_filter_data($filter, $data) {
if (is_string($filter)) { $filter = preg_split('#[^a-zA-Z0-9_]#', str_replace('-', '_', $filter), 0, PREG_SPLIT_NO_EMPTY); }
if (is_array($filter)) { foreach ($filter as $function) { $data = affiliation_string_map($function, $data); } }
return $data; }


function affiliation_format_data($field, $data) {
$data = quotes_entities_decode(do_shortcode($data));
if ((strstr($field, 'date')) && ($data == '0000-00-00 00:00:00')) { $data = ''; }
elseif (substr($field, -13) == 'email_address') { $data = format_email_address($data); }
elseif (substr($field, -12) == 'instructions') { $data = format_instructions($data); }
elseif ((($field == 'url') || (strstr($field, '_url'))) && (!strstr($field, 'urls'))) { $data = format_url($data); }
switch ($field) {
case 'payments_number': if ($data != 'unlimited') { $data = (int) $data; } break;
case 'cookies_lifetime': $data = (int) $data; if ($data < 1) { $data = 1; } break;
case 'amount': case 'commission_amount': case 'commission_percentage': case 'commission2_amount':
case 'commission2_percentage': case 'encrypted_urls_validity_duration': case 'first_payment_amount':
case 'payments_amount': case 'price': case 'shipping_cost': case 'tax': case 'tax_percentage':
case 'transaction_cost': $data = round(100*$data)/100; }
return $data; }


function affiliation_i18n($string) {
load_plugin_textdomain('affiliation-manager', false, 'affiliation-manager/languages');
return __(__($string), 'affiliation-manager'); }


function affiliation_user_data($atts) { include dirname(__FILE__).'/user-data.php'; return $data; }


function affiliation_item_data($type, $atts) { include dirname(__FILE__).'/item-data.php'; return $data; }


function affiliate_data($atts) {
if ((is_array($atts)) && (!isset($atts['id'])) && (isset($atts['category']))) { return affiliate_category_data($atts); }
else { return affiliation_item_data('affiliate', $atts); } }


function affiliate_category_data($atts) {
return affiliation_item_data('affiliate_category', $atts); }


function click_data($atts) {
return affiliation_item_data('click', $atts); }


function commission_data($atts) {
return affiliation_item_data('commission', $atts); }


function message_commission_data($atts) {
return affiliation_item_data('message_commission', $atts); }


function prospect_commission_data($atts) {
return affiliation_item_data('prospect_commission', $atts); }


function recurring_commission_data($atts) {
return affiliation_item_data('recurring_commission', $atts); }


function referrer_data($atts) {
return affiliation_item_data('referrer', $atts); }


function affiliation_jquery_js() {
if (!defined('KLEOR_JQUERY_LOADED')) { define('KLEOR_JQUERY_LOADED', true); ?>
<script type="text/javascript" src="<?php echo AFFILIATION_MANAGER_URL; ?>libraries/jquery.js"></script>
<?php } }


function affiliation_login_through_wordpress() {
global $user_email, $wpdb;
if ((!affiliation_session()) && ($_SESSION['affiliation_login'] != 'NONE') && (is_user_logged_in())) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".$user_email."'", OBJECT);
if ($result) { $_GET['affiliate_data'] = (array) $result; $_SESSION['affiliation_login'] = $result->login; }
else { $_SESSION['affiliation_login'] = 'NONE'; } } }

add_action('plugins_loaded', 'affiliation_login_through_wordpress', 1, 0);


function affiliation_logout_through_wordpress() {
foreach (array('affiliation', 'commerce', 'membership') as $prefix) {
unset($_SESSION[$prefix.'_login']);
setcookie($prefix.'_login', '', time() - 86400, '/'); } }

add_action('wp_logout', 'affiliation_logout_through_wordpress', 10, 0);


function affiliation_logout() {
if (!headers_sent()) { session_start(); }
if (affiliation_session()) {
if ((!defined('AFFILIATION_MANAGER_DEMO')) || (AFFILIATION_MANAGER_DEMO == false)) {
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = affiliation_data('logout_notification_email_'.$field); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); }
if (affiliation_data('logout_custom_instructions_executed') == 'yes') {
eval(format_instructions(affiliation_data('logout_custom_instructions'))); } } }
include_once ABSPATH.'wp-includes/pluggable.php';
if (function_exists('wp_logout')) { wp_logout(); }
affiliation_logout_through_wordpress(); }


function affiliation_session() {
global $wpdb;
if (!headers_sent()) { session_start(); }
if ((isset($_COOKIE['affiliation_login'])) && ((!isset($_SESSION['affiliation_login'])) || ($_SESSION['affiliation_login'] == 'NONE'))) {
$login = substr($_COOKIE['affiliation_login'], 0, -64);
if (substr($_COOKIE['affiliation_login'], -64) == hash('sha256', $login.AUTH_KEY)) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$login."' AND status = 'active'", OBJECT);
if ($result) { $_GET['affiliate_data'] = (array) $result; $_SESSION['affiliation_login'] = $login; }
else { setcookie('affiliation_login', '', time() - 86400, '/'); } } }	
if ((isset($_SESSION['affiliation_login'])) && ($_SESSION['affiliation_login'] != 'NONE')) { return true; } else { return false; } }


function affiliation_sql_array($table, $array) {
foreach ($table as $key => $value) {
$sql[$key] = ($key == 'password' ? hash('sha256', $array[$key]) : $array[$key]);
if ($value['type'] == 'int') { $sql[$key] = (int) $sql[$key]; }
elseif ((strstr($value['type'], 'dec')) && (!is_numeric($sql[$key]))) { $sql[$key] = round(100*$sql[$key])/100; }
elseif (($value['type'] == 'text') || ($value['type'] == 'datetime')) { $sql[$key] = "'".$sql[$key]."'"; } }
return $sql; }


function affiliation_string_map($function, $string) {
if (!function_exists($function)) { $function = 'affiliation_'.$function; }
if (function_exists($function)) { $array = array_map($function, array($string)); $string = $array[0]; }
return $string; }


function award_commission() {
$level = 1; $type = '';
include dirname(__FILE__).'/award-commission.php'; }


function award_commission2() {
$level = 2; $type = '';
include dirname(__FILE__).'/award-commission.php'; }


function award_message_commission() {
$level = 1; $type = 'message';
include dirname(__FILE__).'/award-commission.php'; }


function award_message_commission2() {
$level = 2; $type = 'message';
include dirname(__FILE__).'/award-commission.php'; }


function award_prospect_commission() {
$level = 1; $type = 'prospect';
include dirname(__FILE__).'/award-commission.php'; }


function award_prospect_commission2() {
$level = 2; $type = 'prospect';
include dirname(__FILE__).'/award-commission.php'; }


class Affiliation_Meta_Widget extends WP_Widget {
function __construct() {
parent::WP_Widget('affiliation-meta-widget', __('Meta (Affiliation)', 'affiliation-manager'), array('description' => __('Log in/out', 'affiliation-manager'))); }

function form($instance) {
include 'initial-options.php';
$instance = wp_parse_args($instance, $initial_options['meta_widget']);
$title = esc_attr($instance['title']);
$content = esc_attr($instance['content']); ?>
<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'affiliation-manager'); ?>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</label></p>
<p><label for="<?php echo $this->get_field_id('content'); ?>"><?php _e('Content:', 'affiliation-manager'); ?>
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

add_action('widgets_init', create_function('', 'return register_widget("Affiliation_Meta_Widget");'));


for ($i = 0; $i < 4; $i++) {
foreach (array('affiliation-content', 'affiliation-counter', 'referrer-counter') as $tag) {
add_shortcode($tag.($i == 0 ? '' : $i), create_function('$atts, $content', 'include_once dirname(__FILE__)."/shortcodes.php"; return '.str_replace('-', '_', $tag).'($atts, $content);')); } }

foreach (array(
'' => '',
'bonus-proposal-' => '$atts["type"] = "bonus_proposal"; ',
'instant-notifications-' => '$atts["type"] = "instant_notifications"; ',
'login-' => '$atts["type"] = "login"; ',
'login-compact-' => '$atts["size"] = "compact"; $atts["type"] = "login"; ',
'login-widget-' => '$atts["size"] = "compact"; $atts["type"] = "login"; ',
'password-reset-' => '$atts["type"] = "password_reset"; ',
'profile-' => '$atts["type"] = "profile"; ',
'profile-edit-' => '$atts["type"] = "profile"; ',
'registration-' => '$atts["type"] = "registration"; ',
'registration-compact-' => '$atts["size"] = "compact"; $atts["type"] = "registration"; ',
'statistics-' => '$atts["type"] = "statistics"; ') as $key => $value) {
add_shortcode('affiliation-'.$key.'form', create_function('$atts', $value.'include_once dirname(__FILE__)."/forms.php"; return affiliation_form($atts);')); }

foreach (array(
'' => '',
'affiliates-' => '$atts["type"] = "affiliates"; ',
'clients-' => '$atts["type"] = "clients"; ',
'clicks-' => '$atts["type"] = "clicks"; ',
'commissions1-' => '$atts["type"] = "commissions1"; ',
'commissions2-' => '$atts["type"] = "commissions2"; ',
'global-' => '$atts["type"] = "global"; ',
'messages-' => '$atts["type"] = "messages"; ',
'messages-commissions1-' => '$atts["type"] = "messages_commissions1"; ',
'messages-commissions2-' => '$atts["type"] = "messages_commissions2"; ',
'orders-' => '$atts["type"] = "orders"; ',
'prospects-' => '$atts["type"] = "prospects"; ',
'prospects-commissions1-' => '$atts["type"] = "prospects_commissions1"; ',
'prospects-commissions2-' => '$atts["type"] = "prospects_commissions2"; ',
'recurring-commissions1-' => '$atts["type"] = "recurring_commissions1"; ',
'recurring-commissions2-' => '$atts["type"] = "recurring_commissions2"; ',
'recurring-payments-' => '$atts["type"] = "recurring_payments"; ') as $key => $value) {
add_shortcode('affiliation-'.$key.'statistics', create_function('$atts', $value.'include_once dirname(__FILE__)."/statistics.php"; return affiliation_statistics($atts);')); }

foreach (array('affiliation-messages-commissions-statistics', 'affiliation-prospects-commissions-statistics', 'affiliation-commissions-statistics') as $tag) {
add_shortcode($tag, create_function('$atts', 'include_once dirname(__FILE__)."/statistics.php"; return '.str_replace('-', '_', $tag).'($atts);')); }

foreach (array('affiliation-activation-url', 'affiliation-comments') as $tag) {
add_shortcode($tag, create_function('$atts', 'include_once dirname(__FILE__)."/shortcodes.php"; return '.str_replace('-', '_', $tag).'($atts);')); }

add_shortcode('user', 'affiliation_user_data');
add_shortcode('affiliation-manager', 'affiliation_data');
add_shortcode('referrer-affiliate', 'referrer_data');
add_shortcode('affiliation-user', 'affiliate_data');
foreach (array('affiliate', 'click', 'commission', 'message-commission', 'prospect-commission', 'recurring-commission', 'referrer') as $tag) { add_shortcode($tag, str_replace('-', '_', $tag).'_data'); }


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