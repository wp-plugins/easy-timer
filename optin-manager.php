<?php
/*
Plugin Name: Optin Manager
Plugin URI: http://www.kleor-editions.com/optin-manager
Description: Allows you to create and manage your forms and prospects.
Version: 0.9
Author: Kleor
Author URI: http://www.kleor-editions.com
Text Domain: optin-manager
*/


load_plugin_textdomain('optin-manager', false, 'optin-manager/languages');

if (!defined('HOME_URL')) { define('HOME_URL', get_option('home')); }
if (!defined('UTC_OFFSET')) { define('UTC_OFFSET', get_option('gmt_offset')); }
define('OPTIN_MANAGER_URL', plugin_dir_url(__FILE__));
$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
define('OPTIN_MANAGER_VERSION', $plugin_data['Version']);

if (is_admin()) { include_once dirname(__FILE__).'/admin.php'; }

global $wpdb;
$optin_manager_options = get_option('optin_manager');
if (($optin_manager_options) && ($optin_manager_options['version'] != OPTIN_MANAGER_VERSION)) {
include_once dirname(__FILE__).'/admin.php';
install_optin_manager(); }


optin_fix_url();


function add_prospect($prospect) {
global $wpdb;
include 'tables.php';
foreach ($tables['prospects'] as $key => $value) { $keys_list .= $key.","; $values_list .= "'".$prospect[$key]."',"; }
$results = $wpdb->query("INSERT INTO ".$wpdb->prefix."optin_manager_prospects (".substr($keys_list, 0, -1).") VALUES(".substr($values_list, 0, -1).")");
$_GET['prospect_data'] = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_prospects WHERE email_address = '".$prospect['email_address']."' AND autoresponder = '".$prospect['autoresponder']."' AND autoresponder_list = '".$prospect['autoresponder_list']."'", OBJECT);
$_GET['optin_form_id'] = $prospect['form_id'];
if ($prospect['referrer'] != '') { $_GET['affiliate_data'] = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$prospect['referrer']."'", OBJECT); }
foreach (add_prospect_fields() as $field) {
if (is_admin()) { $prospect[$field] = stripslashes(do_shortcode($prospect[$field])); }
else { $prospect[$field] = optin_form_data($field); } }

if ((function_exists('add_member')) && ($prospect['prospect_subscribed_to_members_areas'] == 'yes')) {
if (membership_session('')) { update_member_members_areas($_SESSION['m_login'], $prospect['prospect_members_areas'], 'add'); }
else {
$member = $prospect;
$member['members_areas'] = $member['prospect_members_areas'];
$members_areas = array_unique(preg_split('#[^0-9]#', $member['members_areas'], 0, PREG_SPLIT_NO_EMPTY));
if (count($members_areas) == 1) { $_GET['member_area_id'] = (int) $members_areas[0]; } else { unset($_GET['member_area_id']); }
$member['category_id'] = member_area_data('members_initial_category_id');
$member['login'] = $member['email_address'];
$login = $member['login']; $result = true; $i = 1; while ($result) {
$result = $wpdb->get_results("SELECT login FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$member['login']."'", OBJECT);
if ($result) { $member['login'] = $login.$i; $i = $i + 1; } }
$member['password'] = substr(md5(mt_rand()), 0, 8);
$member['status'] = member_area_data('members_initial_status');
$member['registration_without_form'] = 'yes';
add_member($member); } }

if ((!defined('OPTIN_MANAGER_DEMO')) || (OPTIN_MANAGER_DEMO == false)) {
if ($prospect['registration_confirmation_email_sent'] == 'yes') {
$sender = $prospect['registration_confirmation_email_sender'];
$receiver = $prospect['registration_confirmation_email_receiver'];
$subject = $prospect['registration_confirmation_email_subject'];
$body = $prospect['registration_confirmation_email_body'];
$headers = 'From: '.$sender;
wp_mail($receiver, $subject, $body, $headers); }

if ($prospect['registration_notification_email_sent'] == 'yes') {
$sender = $prospect['registration_notification_email_sender'];
$receiver = $prospect['registration_notification_email_receiver'];
$subject = $prospect['registration_notification_email_subject'];
$body = $prospect['registration_notification_email_body'];
$headers = 'From: '.$sender;
wp_mail($receiver, $subject, $body, $headers); }

include 'autoresponders.php';
include_once 'autoresponders-functions.php';
$_GET['autoresponder_subscription'] = '';
subscribe_to_autoresponder($prospect['autoresponder'], $prospect['autoresponder_list'], $prospect);

if ($prospect['registration_custom_instructions_executed'] == 'yes') {
eval(optin_format_instructions($prospect['registration_custom_instructions'])); } } }


function add_prospect_fields() {
return array(
'registration_confirmation_email_sent',
'registration_confirmation_email_sender',
'registration_confirmation_email_receiver',
'registration_confirmation_email_subject',
'registration_confirmation_email_body',
'registration_notification_email_sent',
'registration_notification_email_sender',
'registration_notification_email_receiver',
'registration_notification_email_subject',
'registration_notification_email_body',
'prospect_subscribed_to_members_areas',
'prospect_members_areas',
'registration_custom_instructions_executed',
'registration_custom_instructions'); }


function optin_content($atts, $content) {
global $wpdb;
extract(shortcode_atts(array('list' => ''), $atts));
$content = explode('[other]', do_shortcode($content));
$lists = explode('/', $list);
if (is_admin()) { if (in_array($_POST['autoresponder_list'], $lists)) { $n = 0; } else { $n = 1; } }
else {
if (count($lists) > 0) {
foreach ($lists as $list) { $search_criteria .= " OR autoresponder_list = '".$list."'"; }
$search_criteria = 'AND ('.substr($search_criteria, 4).')'; }
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."optin_manager_prospects WHERE ip_address = '".$_SERVER['REMOTE_ADDR']."' $search_criteria", OBJECT);
if ($result) { $n = 0; } else { $n = 1; } }
return $content[$n]; }

add_shortcode('optin-content', 'optin_content');
add_shortcode('optin-content0', 'optin_content');
add_shortcode('optin-content1', 'optin_content');
add_shortcode('optin-content2', 'optin_content');
add_shortcode('optin-content3', 'optin_content');
add_shortcode('optin-content4', 'optin_content');
add_shortcode('optin-content5', 'optin_content');
add_shortcode('optin-content6', 'optin_content');
add_shortcode('optin-content7', 'optin_content');
add_shortcode('optin-content8', 'optin_content');
add_shortcode('optin-content9', 'optin_content');
add_shortcode('optin-content10', 'optin_content');


function optin_counter($atts, $content) {
global $wpdb;
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
extract(shortcode_atts(array('data' => '', 'limit' => '', 'range' => ''), $atts));

$data = str_replace('_', '-', optin_format_nice_name($data));
switch ($data) {
case 'forms': $table = $wpdb->prefix.'optin_manager_forms'; $field = ''; break;
case 'forms-categories': $table = $wpdb->prefix.'optin_manager_forms_categories'; $field = ''; break;
case 'prospects': $table = $wpdb->prefix.'optin_manager_prospects'; $field = ''; break;
default: $table = $wpdb->prefix.'optin_manager_prospects'; $field = ''; }

$range = str_replace('_', '-', optin_format_nice_name($range));
if (is_numeric($range)) {
$end_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$start_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET - 86400*$range);
$date_criteria = "AND (date BETWEEN '".$start_date."' AND '".$end_date."')"; }
else { switch ($range) {
case 'previous-month':
$Y = (int) date('Y', time() + 3600*UTC_OFFSET);
$M = (int) date('n', time() + 3600*UTC_OFFSET);
if ($M == 1) { $m = 12; $y = $Y - 1; }
else { $m = $M - 1; $y = $Y; }
if ($M < 10) { $M = '0'.$M; }
if ($m < 10) { $m = '0'.$m; }
$start_date = $y.'-'.$m.'-01 00:00:00';
$end_date = $Y.'-'.$M.'-01 00:00:00';
$date_criteria = "AND (date BETWEEN '".$start_date."' AND '".$end_date."')"; break;
case 'previous-year':
$Y = (int) date('Y', time() + 3600*UTC_OFFSET);
$y = $Y - 1;
$start_date = $y.'-01-01 00:00:00';
$end_date = $y.'-12-31 23:59:59';
$date_criteria = "AND (date BETWEEN '".$start_date."' AND '".$end_date."')"; break;
default: $date_criteria = ''; } }

if (is_string($table)) {
if ($field == '') {
$row = $wpdb->get_row("SELECT count(*) as total FROM $table WHERE id > 0 $date_criteria", OBJECT);
$data = (int) $row->total; }
else {
$row = $wpdb->get_row("SELECT SUM($field) AS total FROM $table WHERE id > 0 $date_criteria", OBJECT);
$data = round(100*$row->total)/100; } }

else {
$data = 0; foreach ($table as $table_name) {
$row = $wpdb->get_row("SELECT SUM($field) AS total FROM $table_name WHERE id > 0 $date_criteria", OBJECT);
$data = $data + round(100*$row->total)/100; } }

if ($limit == '') { $limit = '0'; }
else { $limit = '0/'.$limit; }
$limit = preg_split('#[^0-9]#', $limit, 0, PREG_SPLIT_NO_EMPTY);
$n = count($limit);

$i = 0; while (($i < $n) && ($limit[$i] <= $data)) { $k = $i; $i = $i + 1; }
if ($i < $n) { $remaining_number = $limit[$i] - $data; $total_remaining_number = $limit[$n - 1] - $data; }
else { $i = $n - 1; $remaining_number = 0; $total_remaining_number = 0; }

$content = explode('[after]', do_shortcode($content));
$content[$k] = str_ireplace('[limit]', $limit[$i], $content[$k]);
$content[$k] = str_ireplace('[total-limit]', $limit[$n - 1], $content[$k]);
$content[$k] = str_ireplace('[number]', $data - $limit[$k], $content[$k]);
$content[$k] = str_ireplace('[total-number]', $data, $content[$k]);
$content[$k] = str_ireplace('[remaining-number]', $remaining_number, $content[$k]);
$content[$k] = str_ireplace('[total-remaining-number]', $total_remaining_number, $content[$k]);

return $content[$k]; }

add_shortcode('optin-counter', 'optin_counter');
add_shortcode('optin-data-counter', 'optin_counter');


function optin_data($atts) {
global $optin_manager_options;
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $part = 0; }
else { $field = $atts[0]; $default = $atts['default']; $filter = $atts['filter']; $part = (int) $atts['part']; }
$field = str_replace('-', '_', optin_format_nice_name($field));
if ($field == '') { $field = 'version'; }
if ((strstr($field, 'email_body')) || ($field == 'code') || ($field == 'registration_custom_instructions')) { $data = get_option('optin_manager_'.$field); }
else { $data = $optin_manager_options[$field]; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = optin_format_data($field, $data);
$data = optin_filter_data($filter, $data);
return $data; }

add_shortcode('optin-manager', 'optin_data');


function optin_date_picker_css() { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo OPTIN_MANAGER_URL; ?>libraries/date-picker.css" />
<?php }


function optin_date_picker_js() { ?>
<script type="text/javascript" src="<?php echo OPTIN_MANAGER_URL; ?>libraries/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="<?php echo OPTIN_MANAGER_URL; ?>libraries/jquery-date-picker.js"></script>
<script type="text/javascript">
Date.dayNames = ['<?php _e('Sunday', 'optin-manager'); ?>', '<?php _e('Monday', 'optin-manager'); ?>', '<?php _e('Tuesday', 'optin-manager'); ?>', '<?php _e('Wednesday', 'optin-manager'); ?>', '<?php _e('Thursday', 'optin-manager'); ?>', '<?php _e('Friday', 'optin-manager'); ?>', '<?php _e('Saturday', 'optin-manager'); ?>'];
Date.abbrDayNames = ['<?php _e('Sun', 'optin-manager'); ?>', '<?php _e('Mon', 'optin-manager'); ?>', '<?php _e('Tue', 'optin-manager'); ?>', '<?php _e('Wed', 'optin-manager'); ?>', '<?php _e('Thu', 'optin-manager'); ?>', '<?php _e('Fri', 'optin-manager'); ?>', '<?php _e('Sat', 'optin-manager'); ?>'];
Date.monthNames = ['<?php _e('January', 'optin-manager'); ?>', '<?php _e('February', 'optin-manager'); ?>', '<?php _e('March', 'optin-manager'); ?>', '<?php _e('April', 'optin-manager'); ?>', '<?php _e('May', 'optin-manager'); ?>', '<?php _e('June', 'optin-manager'); ?>', '<?php _e('July', 'optin-manager'); ?>', '<?php _e('August', 'optin-manager'); ?>', '<?php _e('September', 'optin-manager'); ?>', '<?php _e('October', 'optin-manager'); ?>', '<?php _e('November', 'optin-manager'); ?>', '<?php _e('December', 'optin-manager'); ?>'];
Date.abbrMonthNames = ['<?php _e('Jan', 'optin-manager'); ?>', '<?php _e('Feb', 'optin-manager'); ?>', '<?php _e('Mar', 'optin-manager'); ?>', '<?php _e('Apr', 'optin-manager'); ?>', '<?php _e('May', 'optin-manager'); ?>', '<?php _e('Jun', 'optin-manager'); ?>', '<?php _e('Jul', 'optin-manager'); ?>', '<?php _e('Aug', 'optin-manager'); ?>', '<?php _e('Sep', 'optin-manager'); ?>', '<?php _e('Oct', 'optin-manager'); ?>', '<?php _e('Nov', 'optin-manager'); ?>', '<?php _e('Dec', 'optin-manager'); ?>'];
$.dpText = {
TEXT_PREV_YEAR : '<?php _e('Previous year', 'optin-manager'); ?>',
TEXT_PREV_MONTH : '<?php _e('Previous month', 'optin-manager'); ?>',
TEXT_NEXT_YEAR : '<?php _e('Next year', 'optin-manager'); ?>',
TEXT_NEXT_MONTH : '<?php _e('Next month', 'optin-manager'); ?>',
TEXT_CLOSE : '<?php _e('Close', 'optin-manager'); ?>',
TEXT_CHOOSE_DATE : '<?php _e('Choose a date', 'optin-manager'); ?>',
DATE_PICKER_ALT : '<?php _e('Date', 'optin-manager'); ?>',
DATE_PICKER_URL : '<?php echo OPTIN_MANAGER_URL; ?>images/date-picker.png',
HEADER_FORMAT : 'mmmm yyyy'
}; $(function(){ $('.date-pick').datePicker({startDate:'2011-01-01'}); });
</script>
<?php }


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


function optin_fix_url() {
$url = $_SERVER['REQUEST_URI'];
if (strstr($url, '&amp;')) { $url = str_replace('&amp;', '&', $url); $error = true; }
if ((strstr($url, '?')) && (!strstr($url, '/?')) && (!strstr($url, '.php?'))) { $url = str_replace('?', '/?', $url); $error = true; }
if (($error) && (!headers_sent())) { header('Location: '.$url); exit; } }


function optin_form_category_data($atts) {
global $wpdb;
$_GET['optin_form_category_data'] = (array) $_GET['optin_form_category_data'];
if ((isset($_GET['optin_form_category_id'])) && ($_GET['optin_form_category_data']['id'] != $_GET['optin_form_category_id'])) {
$_GET['optin_form_category_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_forms_categories WHERE id = '".$_GET['optin_form_category_id']."'", OBJECT); }
$optin_form_category_data = $_GET['optin_form_category_data'];
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $id = 0; $part = 0; }
else {
$field = $atts[0];
$default = $atts['default'];
unset($atts['default']);
$filter = $atts['filter'];
unset($atts['filter']);
$id = (int) do_shortcode(str_replace(array('(', ')'), array('[', ']'), $atts['category']));
$part = (int) $atts['part']; }
$field = str_replace('-', '_', optin_format_nice_name($field));
if ($field == '') { $field = 'name'; }
if (($id == 0) || ($id == $optin_form_category_data['id'])) { $data = $optin_form_category_data[$field]; }
else {
if ($_GET['optin_form_category'.$id.'_data']['id'] != $id) {
$_GET['optin_form_category'.$id.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_forms_categories WHERE id = '$id'", OBJECT); }
$optin_form_category_data = $_GET['optin_form_category'.$id.'_data'];
$data =  $optin_form_category_data[$field]; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
$data = (string) $data;
if ($data != '') { $data = optin_format_data($field, $data); }
$data = (string) $data;
if (($data == '') && ($optin_form_category_data['category_id'] > 0)) {
if (is_string($atts)) { $atts = array($field); }
$atts['category'] = $optin_form_category_data['category_id'];
$data = optin_form_category_data($atts); }
elseif ($data == '') { $data = optin_data($atts); }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = optin_format_data($field, $data);
$data = optin_filter_data($filter, $data);
return $data; }


function optin_form_data($atts) {
if ((is_array($atts)) && (!isset($atts['id'])) && (isset($atts['category']))) { return optin_form_category_data($atts); }
else {
global $wpdb;
$_GET['optin_form_data'] = (array) $_GET['optin_form_data'];
if ((isset($_GET['optin_form_id'])) && ($_GET['optin_form_data']['id'] != $_GET['optin_form_id'])) {
$_GET['optin_form_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_forms WHERE id = '".$_GET['optin_form_id']."'", OBJECT); }
$optin_form_data = $_GET['optin_form_data'];
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $id = 0; $part = 0; }
else {
$field = $atts[0];
$default = $atts['default'];
unset($atts['default']);
$filter = $atts['filter'];
unset($atts['filter']);
$id = (int) do_shortcode(str_replace(array('(', ')'), array('[', ']'), $atts['id']));
$part = (int) $atts['part']; }
$field = str_replace('-', '_', optin_format_nice_name($field));
if ($field == '') { $field = 'name'; }
if (($id == 0) || ($id == $optin_form_data['id'])) { $data = $optin_form_data[$field]; }
else {
if (isset($_GET['optin_form_id'])) { $original_optin_form_id = $_GET['optin_form_id']; }
if (isset($_GET['optin_form_data'])) { $original_optin_form_data = $_GET['optin_form_data']; }
if ($_GET['optin_form'.$id.'_data']['id'] != $id) {
$_GET['optin_form'.$id.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_forms WHERE id = '$id'", OBJECT); }
$optin_form_data = $_GET['optin_form'.$id.'_data'];
$_GET['optin_form_id'] = $id; $_GET['optin_form_data'] = $optin_form_data;
$data =  $optin_form_data[$field]; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
$data = (string) $data;
if ($data != '') { $data = optin_format_data($field, $data); }
$data = (string) $data;
if (($data == '') && ($optin_form_data['category_id'] > 0)) {
if (is_string($atts)) { $atts = array($field); }
$atts['category'] = $optin_form_data['category_id'];
$data = optin_form_category_data($atts); }
elseif ($data == '') { $data = optin_data($atts); }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = optin_format_data($field, $data);
$data = optin_filter_data($filter, $data);
if (isset($original_optin_form_id)) { $_GET['optin_form_id'] = $original_optin_form_id; }
if (isset($original_optin_form_data)) { $_GET['optin_form_data'] = $original_optin_form_data; }
return $data; } }

add_shortcode('optin-form', 'optin_form_data');


function optin_format_data($field, $data) {
$data = optin_quotes_entities_decode(do_shortcode($data));
if ((strstr($field, 'date')) && ($data == '0000-00-00 00:00:00')) { $data = ''; }
elseif (strstr($field, 'email_address')) { $data = optin_format_email_address($data); }
elseif (substr($field, -12) == 'instructions') { $data = optin_format_instructions($data); }
elseif ((($field == 'url') || (strstr($field, '_url'))) && (!strstr($field, 'urls'))) { $data = optin_format_url($data); }
switch ($field) {
case 'commission_amount': case 'commission2_amount': case 'encrypted_urls_validity_duration': $data = round(100*$data)/100; }
return $data; }


function optin_format_email_address($string) {
$string = strtolower(trim(strip_tags($string)));
$string = str_replace('à', '@', $string);
$string = str_replace(';', '.', $string);
$string = str_replace(' ', '-', $string);
$string = optin_strip_accents($string);
$string = preg_replace('/[^a-zA-Z0-9_@.-]/', '', $string);
return $string; }


function optin_format_email_address_js() { ?>
<script type="text/javascript">
function optin_format_email_address(string) {
string = string.toLowerCase();
string = string.replace(/[à]/gi, '@');
string = string.replace(/[;]/gi, '.');
string = string.replace(/[ ]/gi, '-');
string = optin_strip_accents(string);
string = string.replace(/[^a-zA-Z0-9_@.-]/gi, '');
return string; }
</script>
<?php }


function optin_format_instructions($string) {
$string = str_replace('<? ', '<?php ', trim($string));
if (substr($string, 0, 5) == '<?php') { $string = substr($string, 5); }
if (substr($string, -2) == '?>') { $string = substr($string, 0, -2); }
$string = trim($string);
return $string; }


function optin_format_medium_nice_name($string) {
$string = optin_strip_accents(trim(strip_tags($string)));
$string = str_replace(' ', '-', $string);
$string = preg_replace('/[^a-zA-Z0-9_-]/', '', $string);
return $string; }


function optin_format_name($string) {
$string = strtolower(trim(strip_tags($string)));
$string = str_replace(array(' ', '_'), '-', $string);
$strings = explode('-', $string);
$n = count($strings);
for ($i = 0; $i < $n; $i++) { $strings[$i] = ucfirst($strings[$i]); }
$string = implode('-', $strings);
return $string; }


function optin_format_name_js() { ?>
<script type="text/javascript">
function optin_format_name(string) {
string = string.toLowerCase();
string = string.replace(/[ _]/gi, '-');
var strings = string.split('-');
var n = strings.length;
var i = 0; while (i != n) { strings[i] = (strings[i]).substr(0, 1).toUpperCase()+(strings[i]).substr(1); i = i + 1; }
string = strings.join('-');
return string; }
</script>
<?php }


function optin_format_nice_name($string) {
$string = optin_strip_accents(strtolower(trim(strip_tags($string))));
$string = str_replace(' ', '-', $string);
$string = preg_replace('/[^a-zA-Z0-9_-]/', '', $string);
return $string; }


function optin_format_nice_name_js() { ?>
<script type="text/javascript">
function optin_format_nice_name(string) {
string = optin_strip_accents(string.toLowerCase());
string = string.replace(/[ ]/gi, '-');
string = string.replace(/[^a-zA-Z0-9_-]/gi, '');
return string; }
</script>
<?php }


function optin_format_url($string) {
if ($string != '') {
$string = trim(strip_tags($string));
$string = str_replace(' ', '-', $string);
if ((!strstr($string, 'http://')) && (!strstr($string, 'https://'))) {
$strings = explode('/', $string);
if (strstr($strings[0], '.')) { $string = 'http://'.$string; }
else { $string = 'http://'.$_SERVER['SERVER_NAME'].'/'.$string; } }
while (strstr($string, '//')) { $string = str_replace('//', '/', $string); }
$string = str_replace(':/', '://', $string); }
return $string; }


function optin_forms_categories_list($id) {
global $wpdb;
$id = (int) $id;
$list = array($id);
while ($id > 0) {
$category = $wpdb->get_row("SELECT category_id FROM ".$wpdb->prefix."optin_manager_forms_categories WHERE id = '$id'", OBJECT);
if ($category) { $id = $category->category_id; }
if ((!$category) || (in_array($id, $list))) { $id = 0; }
$list[] = $id; }
return $list; }


function optin_i18n($string) {
$strings = array(
__('active', 'optin-manager'),
__('inactive', 'optin-manager'),
__('no', 'optin-manager'),
__('paid', 'optin-manager'),
__('unpaid', 'optin-manager'),
__('unsubscribed', 'optin-manager'),
__('yes', 'optin-manager'));
return __(__($string), 'optin-manager'); }


function optin_quotes_entities($string) {
return str_replace(array("'", '"'), array("&apos;", '&quot;'), $string); }


function optin_quotes_entities_decode($string) {
return str_replace(array("&apos;", '&quot;'), array("'", '"'), $string); }


function optin_string_map($function, $string) {
if (!function_exists($function)) { $function = 'optin_'.$function; }
if (function_exists($function)) { $array = array_map($function, array($string)); $string = $array[0]; }
return $string; }


function optin_strip_accents($string) {
return str_replace(
explode(' ', 'á à â ä ã å ç é è ê ë í ì î ï ñ ó ò ô ö õ ø ú ù û ü ý ÿ Á À Â Ä Ã Å Ç É È Ê Ë Í Ì Î Ï Ñ Ó Ò Ô Ö Õ Ø Ú Ù Û Ü Ý Ÿ'),
explode(' ', 'a a a a a a c e e e e i i i i n o o o o o o u u u u y y A A A A A A C E E E E I I I I N O O O O O O U U U U Y Y'),
$string); }


function optin_strip_accents_js() { ?>
<script type="text/javascript">
function optin_strip_accents(string) {
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


function prospect_data($atts) {
global $wpdb;
$_GET['prospect_data'] = (array) $_GET['prospect_data'];
if ((isset($_GET['prospect_id'])) && ($_GET['prospect_data']['id'] != $_GET['prospect_id'])) {
$_GET['prospect_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_prospects WHERE id = '".$_GET['prospect_id']."'", OBJECT); }
$prospect_data = $_GET['prospect_data'];
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $id = 0; $part = 0; }
else {
$field = $atts[0];
$default = $atts['default'];
$filter = $atts['filter'];
$id = (int) do_shortcode(str_replace(array('(', ')'), array('[', ']'), $atts['id']));
$part = (int) $atts['part']; }
$field = str_replace('-', '_', optin_format_nice_name($field));
if ($field == '') { $field = 'first_name'; }
if (($id == 0) || ($id == $prospect_data['id'])) { $data = $prospect_data[$field]; }
else {
if (isset($_GET['prospect_id'])) { $original_prospect_id = $_GET['prospect_id']; }
if (isset($_GET['prospect_data'])) { $original_prospect_data = $_GET['prospect_data']; }
if ($_GET['prospect'.$id.'_data']['id'] != $id) {
$_GET['prospect'.$id.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_prospects WHERE id = '$id'", OBJECT); }
$prospect_data = $_GET['prospect'.$id.'_data'];
$_GET['prospect_id'] = $id; $_GET['prospect_data'] = $prospect_data;
$data = $prospect_data[$field]; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = optin_format_data($field, $data);
$data = optin_filter_data($filter, $data);
if (isset($original_prospect_id)) { $_GET['prospect_id'] = $original_prospect_id; }
if (isset($original_prospect_data)) { $_GET['prospect_data'] = $original_prospect_data; }
return $data; }

add_shortcode('prospect', 'prospect_data');


add_filter('get_the_excerpt', 'do_shortcode');
add_filter('get_the_title', 'do_shortcode');
add_filter('single_post_title', 'do_shortcode');
add_filter('the_excerpt', 'do_shortcode');
add_filter('the_excerpt_rss', 'do_shortcode');
add_filter('the_title', 'do_shortcode');
add_filter('the_title_attribute', 'do_shortcode');
add_filter('the_title_rss', 'do_shortcode');
add_filter('widget_text', 'do_shortcode');