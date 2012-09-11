<?php
/*
Plugin Name: Optin Manager
Plugin URI: http://www.kleor-editions.com/optin-manager
Description: Allows you to create and manage your forms and prospects.
Version: 4.7
Author: Kleor
Author URI: http://www.kleor-editions.com
Text Domain: optin-manager
*/


if (!defined('HOME_URL')) { define('HOME_URL', get_option('home')); }
if (!defined('UTC_OFFSET')) { define('UTC_OFFSET', get_option('gmt_offset')); }
define('OPTIN_MANAGER_URL', plugin_dir_url(__FILE__));
$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
define('OPTIN_MANAGER_VERSION', $plugin_data['Version']);

if (!function_exists('fix_url')) { include_once dirname(__FILE__).'/libraries/formatting-functions.php'; }
if (is_admin()) { include_once dirname(__FILE__).'/admin.php'; }

global $wpdb;
$optin_manager_options = get_option('optin_manager');
if (((is_multisite()) || ($optin_manager_options)) && ($optin_manager_options['version'] != OPTIN_MANAGER_VERSION)) {
include_once dirname(__FILE__).'/admin.php';
install_optin_manager(); }

fix_url();


function activate_prospect() {
global $wpdb;
if (isset($_GET['awt_email'])) {
$email_address = $_GET['awt_email'];
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_prospects WHERE email_address = '".$email_address."' ORDER BY date DESC LIMIT 1", OBJECT);
if (($result) && ($result->status != 'active')) {
$keys = array('prospect_id', 'prospect_data', 'optin_form_id', 'optin_form_data');
foreach ($keys as $key) { if (isset($_GET[$key])) { $original[$key] = $_GET[$key]; } }
$_GET['prospect_id'] = $result->id; $_GET['optin_form_id'] = $result->form_id;
$results = $wpdb->query("UPDATE ".$wpdb->prefix."optin_manager_prospects SET status = 'active' WHERE id = ".$_GET['prospect_id']);
if ((!defined('OPTIN_MANAGER_DEMO')) || (OPTIN_MANAGER_DEMO == false)) {
foreach (array('confirmation', 'notification') as $action) {
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = optin_form_data('activation_'.$action.'_email_'.$field); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); } }
if (optin_data('activation_custom_instructions_executed') == 'yes') {
eval(format_instructions(optin_data('activation_custom_instructions'))); } }
foreach ($keys as $key) { if (isset($original[$key])) { $_GET[$key] = $original[$key]; } } } } }

add_action('plugins_loaded', 'activate_prospect');


function add_optin_form_in_posts($content) {
global $post;
if ((is_single()) && (optin_data('automatic_display_enabled') == 'yes')) {
include_once dirname(__FILE__).'/forms.php';
$optin_form = optin_form(array('id' => optin_data('automatic_display_form_id')));
if (optin_data('automatic_display_location') == 'top') { $content = $optin_form.$content; }
else { $content .= $optin_form; } }
return $content; }

add_filter('the_content', 'add_optin_form_in_posts');


function add_prospect($prospect) { include dirname(__FILE__).'/add-prospect.php'; }


function optin_data($atts) {
global $optin_manager_options;
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $part = 0; }
else { $field = $atts[0]; $default = $atts['default']; $filter = $atts['filter']; $part = (int) $atts['part']; }
$field = str_replace('-', '_', format_nice_name($field));
if ($field == '') { $field = 'version'; }
if (($field == 'code') || (substr($field, -10) == 'email_body') || (substr($field, -19) == 'custom_instructions')) { $data = get_option('optin_manager_'.$field); }
else { $data = $optin_manager_options[$field]; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = optin_format_data($field, $data);
$data = optin_filter_data($filter, $data);
return $data; }


function optin_decrypt_url($url) {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$url = explode('?url=', $url);
$url = $url[1];
$url = base64_decode($url);
$url = trim(mcrypt_decrypt(MCRYPT_BLOWFISH, md5(optin_data('encrypted_urls_key')), $url, MCRYPT_MODE_ECB));
$url = explode('|', $url);
$T = $url[0];
$url = $url[1];
$S = time() - $T;
if ($S > 3600*optin_data('encrypted_urls_validity_duration')) { $url = HOME_URL; }
return $url; }


function optin_encrypt_url($url) {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$url = time().'|'.$url;
$url = mcrypt_encrypt(MCRYPT_BLOWFISH, md5(optin_data('encrypted_urls_key')), $url, MCRYPT_MODE_ECB);
$url = base64_encode($url);
$url = OPTIN_MANAGER_URL.'?url='.$url;
return $url; }


function optin_filter_data($filter, $data) {
if (is_string($filter)) { $filter = preg_split('#[^a-zA-Z0-9_]#', str_replace('-', '_', $filter), 0, PREG_SPLIT_NO_EMPTY); }
if (is_array($filter)) { foreach ($filter as $function) { $data = optin_string_map($function, $data); } }
return $data; }


function optin_format_data($field, $data) {
$data = quotes_entities_decode(do_shortcode($data));
if ((strstr($field, 'date')) && ($data == '0000-00-00 00:00:00')) { $data = ''; }
elseif (substr($field, -13) == 'email_address') { $data = format_email_address($data); }
elseif (substr($field, -12) == 'instructions') { $data = format_instructions($data); }
elseif ((($field == 'url') || (strstr($field, '_url'))) && (!strstr($field, 'urls'))) { $data = format_url($data); }
switch ($field) {
case 'maximum_prospects_quantity': if ($data != 'unlimited') { $data = (int) $data; } break;
case 'commission_amount': case 'commission2_amount': case 'encrypted_urls_validity_duration': $data = round(100*$data)/100; }
return $data; }


function optin_forms_categories_list($id) {
global $wpdb;
$id = (int) $id;
$list = array($id);
while ($id > 0) {
$category = $wpdb->get_row("SELECT category_id FROM ".$wpdb->prefix."optin_manager_forms_categories WHERE id = $id", OBJECT);
if ($category) { $id = $category->category_id; }
if ((!$category) || (in_array($id, $list))) { $id = 0; }
$list[] = $id; }
return $list; }


function optin_i18n($string) {
load_plugin_textdomain('optin-manager', false, 'optin-manager/languages');
return __(__($string), 'optin-manager'); }


function optin_user_data($atts) { include dirname(__FILE__).'/user-data.php'; return $data; }


function optin_item_data($type, $atts) { include dirname(__FILE__).'/item-data.php'; return $data; }


function optin_form_data($atts) {
if ((is_array($atts)) && (!isset($atts[0]))) { include_once dirname(__FILE__).'/forms.php'; return optin_form($atts); }
elseif ((is_array($atts)) && (!isset($atts['id'])) && (isset($atts['category']))) { return optin_form_category_data($atts); }
else { return optin_item_data('optin_form', $atts); } }


function optin_form_category_data($atts) {
return optin_item_data('optin_form_category', $atts); }


function prospect_data($atts) {
return optin_item_data('prospect', $atts); }


function optin_jquery_js() {
if (!defined('KLEOR_JQUERY_LOADED')) { define('KLEOR_JQUERY_LOADED', true); ?>
<script type="text/javascript" src="<?php echo OPTIN_MANAGER_URL; ?>libraries/jquery.js"></script>
<?php } }


function optin_sql_array($table, $array) {
foreach ($table as $key => $value) {
$sql[$key] = $array[$key];
if ($value['type'] == 'int') { $sql[$key] = (int) $sql[$key]; }
elseif ((strstr($value['type'], 'dec')) && (!is_numeric($sql[$key]))) { $sql[$key] = round(100*$sql[$key])/100; }
elseif (($value['type'] == 'text') || ($value['type'] == 'datetime')) { $sql[$key] = "'".$sql[$key]."'"; } }
return $sql; }


function optin_string_map($function, $string) {
if (!function_exists($function)) { $function = 'optin_'.$function; }
if (function_exists($function)) { $array = array_map($function, array($string)); $string = $array[0]; }
return $string; }



for ($i = 0; $i < 4; $i++) {
foreach (array('optin-content', 'optin-counter', 'optin-form-counter') as $tag) {
add_shortcode($tag.($i == 0 ? '' : $i), create_function('$atts, $content', 'include_once dirname(__FILE__)."/shortcodes.php"; return '.str_replace('-', '_', $tag).'($atts, $content);')); } }
add_shortcode('user', 'optin_user_data');
add_shortcode('optin-manager', 'optin_data');
add_shortcode('optin-form', 'optin_form_data');
add_shortcode('prospect', 'prospect_data');


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