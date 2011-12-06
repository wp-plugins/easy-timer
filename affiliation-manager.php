<?php
/*
Plugin Name: Affiliation Manager
Plugin URI: http://www.kleor-editions.com/affiliation-manager
Description: Allows you to create and manage your affiliate program.
Version: 1.9
Author: Kleor
Author URI: http://www.kleor-editions.com
Text Domain: affiliation-manager
*/


load_plugin_textdomain('affiliation-manager', false, 'affiliation-manager/languages');

if (!defined('HOME_URL')) { define('HOME_URL', get_option('home')); }
if (!defined('UTC_OFFSET')) { define('UTC_OFFSET', get_option('gmt_offset')); }
define('AFFILIATION_MANAGER_URL', plugin_dir_url(__FILE__));
$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
define('AFFILIATION_MANAGER_VERSION', $plugin_data['Version']);

if (is_admin()) { include_once dirname(__FILE__).'/admin.php'; }

global $wpdb;
$affiliation_manager_options = get_option('affiliation_manager');
if (($affiliation_manager_options) && ($affiliation_manager_options['version'] != AFFILIATION_MANAGER_VERSION)) {
include_once dirname(__FILE__).'/admin.php';
install_affiliation_manager(); }

$commerce_manager_options = get_option('commerce_manager');

define('AFFILIATION_COOKIES_NAME', affiliation_data('cookies_name'));
define('AFFILIATION_URL_VARIABLE_NAME', affiliation_data('url_variable_name'));
define('AFFILIATION_URL_VARIABLE_NAME2', affiliation_data('url_variable_name2'));


affiliation_fix_url();
affiliation_session();


if (!is_admin()) {
$a = AFFILIATION_URL_VARIABLE_NAME;
$e = AFFILIATION_URL_VARIABLE_NAME2;
if (($_GET[$a] == '') && ($_GET[$e] != '')) { $_GET[$a] = $_GET[$e]; }
if ($_GET[$a] != '') {
$referrer = $_GET[$a];
if (is_numeric($referrer)) {
$referrer = preg_replace('/[^0-9]/', '', $referrer);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE id = '$referrer' AND status = 'active'", OBJECT);
if ($result) { $referrer = $result->login; } else { $referrer = ''; } }
elseif (strstr($referrer, '@')) {
$referrer = affiliation_format_email_address($referrer);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE paypal_email_address = '$referrer' AND status = 'active'", OBJECT);
if ($result) { $referrer = $result->login; }
else { $result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '$referrer' AND status = 'active'", OBJECT);
if ($result) { $referrer = $result->login; } } }
else {
$referrer = affiliation_format_nice_name($referrer);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '$referrer' AND status = 'active'", OBJECT);
if (!$result) { $referrer = ''; } }
if ($referrer != '') {
if (($_COOKIE[AFFILIATION_COOKIES_NAME] == '') || (affiliation_data('winner_affiliate') == 'last')) {
setcookie(AFFILIATION_COOKIES_NAME, $referrer, time() + 86400*affiliation_data('cookies_lifetime'), '/'); }
if ((strstr($_SERVER['REQUEST_URI'], '/?')) && (affiliation_data('clicks_registration_enabled') == 'yes')) {
$click['referrer'] = $referrer;
$click['url'] = $_SERVER['REQUEST_URI'];
$click['ip_address'] = $_SERVER['REMOTE_ADDR'];
$click['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
$click['referring_url'] = $_SERVER['HTTP_REFERER'];
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$click['date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$click['date_utc'] = date('Y-m-d H:i:s');
include 'tables.php';
foreach ($tables['clicks'] as $key => $value) { $keys_list .= $key.","; $values_list .= "'".$click[$key]."',"; }
$results = $wpdb->query("INSERT INTO ".$wpdb->prefix."affiliation_manager_clicks (".substr($keys_list, 0, -1).") VALUES(".substr($values_list, 0, -1).")");
$maximum_clicks_quantity = affiliation_data('maximum_clicks_quantity');
if (is_numeric($maximum_clicks_quantity)) {
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."affiliation_manager_clicks", OBJECT);
$clicks_quantity = (int) $row->total;
$n = $clicks_quantity - $maximum_clicks_quantity;
if ($n > 0) { $results = $wpdb->query("DELETE FROM ".$wpdb->prefix."affiliation_manager_clicks ORDER BY date ASC LIMIT $n"); } } } } }
if ((isset($_GET[$a])) || (isset($_GET[$e]))) { affiliation_cloaking(); }
affiliation_instructions();
$_GET['referrer'] = $_COOKIE[AFFILIATION_COOKIES_NAME]; }


function add_affiliate($affiliate) {
global $wpdb;
include 'tables.php';
foreach ($tables['affiliates'] as $key => $value) { $keys_list .= $key.","; $values_list .= "'".($key == 'password' ? hash('sha256', $affiliate[$key]) : $affiliate[$key])."',"; }
$results = $wpdb->query("INSERT INTO ".$wpdb->prefix."affiliation_manager_affiliates (".substr($keys_list, 0, -1).") VALUES(".substr($values_list, 0, -1).")");
$_GET['affiliate_data'] = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$affiliate['login']."'", OBJECT);
$_GET['affiliate_data']->password = $affiliate['password'];
foreach (add_affiliate_fields() as $field) {
if (is_admin()) { $affiliate[$field] = stripslashes(do_shortcode($affiliate[$field])); }
else { $affiliate[$field] = affiliation_data($field); } }

if ((function_exists('add_member')) && ($affiliate['affiliate_subscribed_to_members_areas'] == 'yes')) {
if (membership_session('')) { update_member_members_areas($_SESSION['m_login'], $affiliate['affiliate_members_areas'], 'add'); }
else {
$member = $affiliate;
$member['members_areas'] = $member['affiliate_members_areas'];
$members_areas = array_unique(preg_split('#[^0-9]#', $member['members_areas'], 0, PREG_SPLIT_NO_EMPTY));
if (count($members_areas) == 1) { $_GET['member_area_id'] = (int) $members_areas[0]; } else { unset($_GET['member_area_id']); }
$member['category_id'] = member_area_data('members_initial_category_id');
$login = $member['login']; $result = true; $i = 1; while ($result) {
$result = $wpdb->get_results("SELECT login FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$member['login']."'", OBJECT);
if ($result) { $member['login'] = $login.$i; $i = $i + 1; } }
$member['status'] = member_area_data('members_initial_status');
$member['registration_without_form'] = 'yes';
add_member($member); } }

if ((!defined('AFFILIATION_MANAGER_DEMO')) || (AFFILIATION_MANAGER_DEMO == false)) {
if ($affiliate['registration_confirmation_email_sent'] == 'yes') {
$sender = $affiliate['registration_confirmation_email_sender'];
$receiver = $affiliate['registration_confirmation_email_receiver'];
$subject = $affiliate['registration_confirmation_email_subject'];
$body = $affiliate['registration_confirmation_email_body'];
$headers = 'From: '.$sender;
wp_mail($receiver, $subject, $body, $headers); }

if ($affiliate['registration_notification_email_sent'] == 'yes') {
$sender = $affiliate['registration_notification_email_sender'];
$receiver = $affiliate['registration_notification_email_receiver'];
$subject = $affiliate['registration_notification_email_subject'];
$body = $affiliate['registration_notification_email_body'];
$headers = 'From: '.$sender;
wp_mail($receiver, $subject, $body, $headers); }

if ($affiliate['affiliate_subscribed_to_autoresponder'] == 'yes') {
include 'autoresponders.php';
include_once 'autoresponders-functions.php';
$_GET['autoresponder_subscription'] = '';
subscribe_to_autoresponder($affiliate['affiliate_autoresponder'], $affiliate['affiliate_autoresponder_list'], $affiliate); }

if ($affiliate['registration_custom_instructions_executed'] == 'yes') {
eval(affiliation_format_instructions($affiliate['registration_custom_instructions'])); } } }


function add_affiliate_fields() {
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
'affiliate_subscribed_to_autoresponder',
'affiliate_autoresponder',
'affiliate_autoresponder_list',
'affiliate_subscribed_to_members_areas',
'affiliate_members_areas',
'registration_custom_instructions_executed',
'registration_custom_instructions'); }


function affiliate_category_data($atts) {
global $wpdb;
$_GET['affiliate_category_data'] = (array) $_GET['affiliate_category_data'];
if ((isset($_GET['affiliate_category_id'])) && ($_GET['affiliate_category_data']['id'] != $_GET['affiliate_category_id'])) {
$_GET['affiliate_category_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates_categories WHERE id = '".$_GET['affiliate_category_id']."'", OBJECT); }
$affiliate_category_data = $_GET['affiliate_category_data'];
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $id = 0; $part = 0; }
else {
$field = $atts[0];
$default = $atts['default'];
unset($atts['default']);
$filter = $atts['filter'];
unset($atts['filter']);
$id = (int) do_shortcode(str_replace(array('(', ')'), array('[', ']'), $atts['category']));
$part = (int) $atts['part']; }
$field = str_replace('-', '_', affiliation_format_nice_name($field));
if ($field == '') { $field = 'name'; }
if (($id == 0) || ($id == $affiliate_category_data['id'])) { $data = $affiliate_category_data[$field]; }
else {
if ($_GET['affiliate_category'.$id.'_data']['id'] != $id) {
$_GET['affiliate_category'.$id.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates_categories WHERE id = '$id'", OBJECT); }
$affiliate_category_data = $_GET['affiliate_category'.$id.'_data'];
$data = $affiliate_category_data[$field]; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
$data = (string) $data;
if ($data != '') { $data = affiliation_format_data($field, $data); }
$data = (string) $data;
if (($data == '') && ($affiliate_category_data['category_id'] > 0)) {
if (is_string($atts)) { $atts = array($field); }
$atts['category'] = $affiliate_category_data['category_id'];
$data = affiliate_category_data($atts); }
elseif ($data == '') { $data = affiliation_data($atts); }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = affiliation_format_data($field, $data);
$data = affiliation_filter_data($filter, $data);
return $data; }


function affiliate_data($atts) {
if ((is_array($atts)) && (!isset($atts['id'])) && (isset($atts['category']))) { return affiliate_category_data($atts); }
else {
global $wpdb;
$_GET['affiliate_data'] = (array) $_GET['affiliate_data'];
if ((!is_admin()) && ($_GET['action'] != 'order') && ($_GET['action'] != 'commerce_notification') && (isset($_SESSION['a_login'])) && ($_GET['affiliate_data']['login'] != $_SESSION['a_login'])) {
$_GET['affiliate_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_SESSION['a_login']."'", OBJECT); }
$affiliate_data = $_GET['affiliate_data'];
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $id = 0; $part = 0; }
else {
$field = $atts[0];
$default = $atts['default'];
unset($atts['default']);
$filter = $atts['filter'];
unset($atts['filter']);
$id = (int) do_shortcode(str_replace(array('(', ')'), array('[', ']'), $atts['id']));
$part = (int) $atts['part']; }
$field = str_replace('-', '_', affiliation_format_nice_name($field));
if ($field == '') { $field = 'login'; }
if (($id == 0) || ($id == $affiliate_data['id'])) { $data = $affiliate_data[$field]; }
else {
if ($_GET['affiliate'.$id.'_data']['id'] != $id) {
$_GET['affiliate'.$id.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE id = '$id'", OBJECT); }
$affiliate_data = $_GET['affiliate'.$id.'_data'];
$data = $affiliate_data[$field]; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
$data = (string) $data;
if ($data != '') { $data = affiliation_format_data($field, $data); }
$data = (string) $data;
if (($data == '') && ($affiliate_data['category_id'] > 0)) {
if (is_string($atts)) { $atts = array($field); }
$atts['category'] = $affiliate_data['category_id'];
$data = affiliate_category_data($atts); }
elseif ($data == '') { $data = affiliation_data($atts); }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = affiliation_format_data($field, $data);
$data = affiliation_filter_data($filter, $data);
return $data; } }

add_shortcode('affiliate', 'affiliate_data');


function affiliates_categories_list($id) {
global $wpdb;
$id = (int) $id;
$list = array($id);
while ($id > 0) {
$category = $wpdb->get_row("SELECT category_id FROM ".$wpdb->prefix."affiliation_manager_affiliates_categories WHERE id = '$id'", OBJECT);
if ($category) { $id = $category->category_id; }
if ((!$category) || (in_array($id, $list))) { $id = 0; }
$list[] = $id; }
return $list; }


function affiliation_bonus_proposal_form() {
if (affiliation_session()) {
global $wpdb;
foreach ($_POST as $key => $value) { if (substr($key, 0, 2) == 'a_') { $_POST[substr($key, 2)] = $value; } }
if (isset($_POST['submit'])) {
$_GET['affiliate_data'] = (array) $_GET['affiliate_data'];
if ((isset($_SESSION['a_login'])) && ($_GET['affiliate_data']['login'] != $_SESSION['a_login'])) {
$_GET['affiliate_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_SESSION['a_login']."'", OBJECT); }
$_POST['bonus_download_url'] = affiliation_format_url($_POST['bonus_download_url']);
$_GET['affiliate_data']['bonus_download_url'] = $_POST['bonus_download_url'];
$_GET['affiliate_data']['bonus_instructions'] = $_POST['bonus_instructions'];
$sender = affiliation_data('bonus_proposal_email_sender');
$receiver = affiliation_data('bonus_proposal_email_receiver');
$subject = affiliation_data('bonus_proposal_email_subject');
$body = affiliation_data('bonus_proposal_email_body');
$headers = 'From: '.$sender;
wp_mail($receiver, $subject, $body, $headers);
$content .= '<p class="valid">'.__('Your bonus proposal has been sent successfully.', 'affiliation-manager').'</p>'; }

$content .= '
<form style="text-align: center;" method="post" action="'.htmlspecialchars($_SERVER['REQUEST_URI']).'">
<p class="bonus-download-url"><label><strong>'.__('Bonus download URL:', 'affiliation-manager').'</strong><br />
<input type="text" name="a_bonus_download_url" id="a_bonus_download_url" size="60" value="'.$_POST['bonus_download_url'].'" /></label></p>
<p class="bonus-instructions"><label><strong>'.__('Instructions to the customer:', 'affiliation-manager').'</strong><br />
<textarea name="a_bonus_instructions" id="a_bonus_instructions" cols="60" rows="5">'.$_POST['bonus_instructions'].'</textarea></label></p>
<div><input type="submit" name="a_submit" value="'.__('Send the proposal', 'affiliation-manager').'" /></div>
</form>';

return $content; } }

add_shortcode('affiliation-bonus-proposal-form', 'affiliation_bonus_proposal_form');


function affiliation_clicks_statistics() {
if (affiliation_session()) {
global $wpdb;
foreach ($_POST as $key => $value) { if (substr($key, 0, 2) == 'a_') { $_POST[substr($key, 2)] = $value; } }
if (isset($_POST['submit'])) {
$start_date = trim(mysql_real_escape_string(strip_tags($_POST['start_date'])));
if (strlen($start_date) == 10) { $start_date .= ' 00:00:00'; }
$end_date = trim(mysql_real_escape_string(strip_tags($_POST['end_date'])));
if (strlen($end_date) == 10) { $end_date .= ' 23:59:59'; }
$content .= '<h3 id="clicks-statistics">'.__('Clicks Statistics', 'affiliation-manager').'</h3>'; }
else {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$end_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$start_date = substr($end_date, 0, 8).'01 00:00:00';
$content .= '<h3 id="clicks-statistics">'.__('Clicks Monthly Statistics', 'affiliation-manager').'</h3>'; }

$clicks = $wpdb->get_results("SELECT date, referring_url, url FROM ".$wpdb->prefix."affiliation_manager_clicks WHERE referrer = '".$_SESSION['a_login']."' AND (date BETWEEN '$start_date' AND '$end_date') ORDER BY date DESC", OBJECT);
if ($clicks) {
$content .= '<table style="width: 100%;">
<tr style="vertical-align: top;"><th>'.__('Date', 'affiliation-manager').'</th><th>'.__('URL', 'affiliation-manager').'</th><th>'.__('Referring URL', 'affiliation-manager').'</th></tr>';
foreach ($clicks as $click) {
$url = str_replace('&', '&amp;', $click->url);
$referring_url = str_replace('&', '&amp;', $click->referring_url);
$content .= '<tr style="vertical-align: top;">
<td>'.$click->date.'</td>
<td><a href="'.$url.'">'.$url.'</a></td>
<td><a href="'.$referring_url.'">'.$referring_url.'</a></td></tr>'; }
$content .= '</table>'; }
else { $content .= '<p>'.__('No clicks', 'affiliation-manager').'</p>'; }

return $content; } }

add_shortcode('affiliation-clicks-statistics', 'affiliation_clicks_statistics');


function affiliation_cloaking() {
$a = AFFILIATION_URL_VARIABLE_NAME;
$e = AFFILIATION_URL_VARIABLE_NAME2;
if (((isset($_GET[$a])) || (isset($_GET[$e]))) && (!headers_sent())) {
$url = str_replace(array('?'.$a.'='.$_GET[$a], '?'.$e.'='.$_GET[$e], '&'.$a.'='.$_GET[$a], '&'.$e.'='.$_GET[$e]), '', $_SERVER['REQUEST_URI']);
if (!strstr($url, '?')) { $url = str_replace('/&', '/?', $url); }
if (!headers_sent()) { header('Location: '.$url); exit; } } }


function affiliation_commissions_statistics($atts) {
if (affiliation_session()) {
extract(shortcode_atts(array('level' => 0, 'type' => ''), $atts));
switch ($level) { case 1: case 2: break; default: $level = 0; }
$type = str_replace('_', '-', affiliation_format_nice_name($type));
switch ($type) { case 'recurring': case 'non-recurring': break; default: $type = ''; }
$leveltype = $level.$type;
switch ($leveltype) {
case '0': $content = affiliation_commissions1_statistics().affiliation_recurring_commissions1_statistics().affiliation_commissions2_statistics().affiliation_recurring_commissions2_statistics(); break;
case '0recurring': $content = affiliation_recurring_commissions1_statistics().affiliation_recurring_commissions2_statistics(); break;
case '0non-recurring': $content = affiliation_commissions1_statistics().affiliation_commissions2_statistics(); break;
case '1': $content = affiliation_commissions1_statistics().affiliation_recurring_commissions1_statistics(); break;
case '1recurring': $content = affiliation_recurring_commissions1_statistics(); break;
case '1non-recurring': $content = affiliation_commissions1_statistics(); break;
case '2': $content = affiliation_commissions2_statistics().affiliation_recurring_commissions2_statistics(); break;
case '2recurring': $content = affiliation_recurring_commissions2_statistics(); break;
case '2non-recurring': $content = affiliation_commissions2_statistics(); }
return $content; } }

add_shortcode('affiliation-commissions-statistics', 'affiliation_commissions_statistics');


function affiliation_commissions1_statistics() {
if (affiliation_session()) {
global $commerce_manager_options, $wpdb;
$currency_code = do_shortcode($commerce_manager_options['currency_code']);
foreach ($_POST as $key => $value) { if (substr($key, 0, 2) == 'a_') { $_POST[substr($key, 2)] = $value; } }
if (isset($_POST['submit'])) {
$start_date = trim(mysql_real_escape_string(strip_tags($_POST['start_date'])));
if (strlen($start_date) == 10) { $start_date .= ' 00:00:00'; }
$end_date = trim(mysql_real_escape_string(strip_tags($_POST['end_date'])));
if (strlen($end_date) == 10) { $end_date .= ' 23:59:59'; }
$content .= '<h3 id="commissions-statistics">'.__('Commissions Statistics', 'affiliation-manager').' '.__('(Level 1)', 'affiliation-manager').'</h3>';	}
else {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$end_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$start_date = substr($end_date, 0, 8).'01 00:00:00';
$content .= '<h3 id="commissions-statistics">'.__('Commissions Monthly Statistics', 'affiliation-manager').' '.__('(Level 1)', 'affiliation-manager').'</h3>'; }

$orders = $wpdb->get_results("SELECT commission_amount, commission_status, date, product_id FROM ".$wpdb->prefix."commerce_manager_orders WHERE commission_amount > 0 AND referrer = '".$_SESSION['a_login']."' AND (date BETWEEN '$start_date' AND '$end_date') ORDER BY date DESC", OBJECT);
if ($orders) {		
$content .= '<table style="width: 100%;">
<tr style="vertical-align: top;"><th>'.__('Date', 'affiliation-manager').'</th><th>'.__('Product', 'affiliation-manager').'</th><th>'.__('Amount', 'affiliation-manager').'</th><th>'.__('Status', 'affiliation-manager').'</th></tr>';
foreach ($orders as $order) {
if (function_exists('product_data')) {
$product_name = product_data(array('name', id => $order->product_id));
$product_url = product_data(array('url', id => $order->product_id)); }
if ($order->commission_status == 'paid') { $commission_status = __('Paid', 'affiliation-manager'); }
else { $commission_status = __('Unpaid', 'affiliation-manager'); }
$content .= '<tr style="vertical-align: top;">
<td>'.$order->date.'</td>
<td>'.($product_url == '' ? $product_name : '<a href="'.$product_url.'">'.$product_name.'</a>').'</td>
<td>'.$order->commission_amount.' '.$currency_code.'</td>
<td class="'.$order->commission_status.'">'.$commission_status.'</td></tr>'; }
$content .= '</table>'; }
else { $content .= '<p>'.__('No commissions', 'affiliation-manager').'</p>'; }

return $content; } }


function affiliation_commissions2_statistics() {
if (affiliation_session()) {
global $commerce_manager_options, $wpdb;
$currency_code = do_shortcode($commerce_manager_options['currency_code']);
foreach ($_POST as $key => $value) { if (substr($key, 0, 2) == 'a_') { $_POST[substr($key, 2)] = $value; } }
if (isset($_POST['submit'])) {
$start_date = trim(mysql_real_escape_string(strip_tags($_POST['start_date'])));
if (strlen($start_date) == 10) { $start_date .= ' 00:00:00'; }
$end_date = trim(mysql_real_escape_string(strip_tags($_POST['end_date'])));
if (strlen($end_date) == 10) { $end_date .= ' 23:59:59'; }
$content .= '<h3 id="commissions2-statistics">'.__('Commissions Statistics', 'affiliation-manager').' '.__('(Level 2)', 'affiliation-manager').'</h3>';	}
else {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$end_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$start_date = substr($end_date, 0, 8).'01 00:00:00';
$content .= '<h3 id="commissions2-statistics">'.__('Commissions Monthly Statistics', 'affiliation-manager').' '.__('(Level 2)', 'affiliation-manager').'</h3>'; }

$orders = $wpdb->get_results("SELECT commission2_amount, commission2_status, date, product_id FROM ".$wpdb->prefix."commerce_manager_orders WHERE commission2_amount > 0 AND referrer2 = '".$_SESSION['a_login']."' AND (date BETWEEN '$start_date' AND '$end_date') ORDER BY date DESC", OBJECT);
if ($orders) {	
$content .= '<table style="width: 100%;">
<tr style="vertical-align: top;"><th>'.__('Date', 'affiliation-manager').'</th><th>'.__('Product', 'affiliation-manager').'</th><th>'.__('Amount', 'affiliation-manager').'</th><th>'.__('Status', 'affiliation-manager').'</th></tr>';
foreach ($orders as $order) {
if (function_exists('product_data')) {
$product_name = product_data(array('name', id => $order->product_id));
$product_url = product_data(array('url', id => $order->product_id)); }
if ($order->commission2_status == 'paid') { $commission2_status = __('Paid', 'affiliation-manager'); }
else { $commission2_status = __('Unpaid', 'affiliation-manager'); }
$content .= '<tr style="vertical-align: top;">
<td>'.$order->date.'</td>
<td>'.($product_url == '' ? $product_name : '<a href="'.$product_url.'">'.$product_name.'</a>').'</td>
<td>'.$order->commission2_amount.' '.$currency_code.'</td>
<td class="'.$order->commission2_status.'">'.$commission2_status.'</td></tr>'; }
$content .= '</table>'; }
else { $content .= '<p>'.__('No commissions', 'affiliation-manager').'</p>'; }

return $content; } }


function affiliation_content($atts, $content) {
$content = explode('[other]', do_shortcode($content));
if (affiliation_session()) { $n = 0; } else { $n = 1; }
return $content[$n]; }

add_shortcode('affiliation-content', 'affiliation_content');


function affiliation_counter($atts, $content) {
global $wpdb;
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
extract(shortcode_atts(array('data' => '', 'limit' => '', 'range' => ''), $atts));

$data = str_replace('_', '-', affiliation_format_nice_name($data));
switch ($data) {
case 'affiliates': $table = $wpdb->prefix.'affiliation_manager_affiliates'; $field = ''; break;
case 'affiliates-categories': $table = $wpdb->prefix.'affiliation_manager_affiliates_categories'; $field = ''; break;
case 'clicks': $table = $wpdb->prefix.'affiliation_manager_clicks'; $field = ''; break;
case 'commission-amount': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments', $wpdb->prefix.'optin_manager_prospects'); $field = 'commission_amount'; break;
case 'commission2-amount': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments', $wpdb->prefix.'optin_manager_prospects'); $field = 'commission2_amount'; break;
case 'orders-commission-amount': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'commission_amount'; break;
case 'orders-commission2-amount': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'commission2_amount'; break;
case 'prospects': $table = $wpdb->prefix.'optin_manager_prospects'; $field = ''; break;
case 'prospects-commission-amount': $table = $wpdb->prefix.'optin_manager_prospects'; $field = 'commission_amount'; break;
case 'prospects-commission2-amount': $table = $wpdb->prefix.'optin_manager_prospects'; $field = 'commission2_amount'; break;
case 'recurring-payments-commission-amount': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'commission_amount'; break;
case 'recurring-payments-commission2-amount': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'commission2_amount'; break;
default: $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments', $wpdb->prefix.'optin_manager_prospects'); $field = 'commission_amount'; }

$range = str_replace('_', '-', affiliation_format_nice_name($range));
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

add_shortcode('affiliation-counter', 'affiliation_counter');
add_shortcode('affiliation-data-counter', 'affiliation_counter');


function affiliation_data($atts) {
global $affiliation_manager_options;
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $part = 0; }
else { $field = $atts[0]; $default = $atts['default']; $filter = $atts['filter']; $part = (int) $atts['part']; }
$field = str_replace('-', '_', affiliation_format_nice_name($field));
if ($field == '') { $field = 'version'; }
if ((strstr($field, 'email_body')) || ($field == 'registration_custom_instructions')) { $data = get_option('affiliation_manager_'.$field); }
else { $data = $affiliation_manager_options[$field]; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = affiliation_format_data($field, $data);
$data = affiliation_filter_data($filter, $data);
return $data; }

add_shortcode('affiliation-manager', 'affiliation_data');


function affiliation_date_picker_css() {
global $post;
if ((strstr($post->post_content, '[affiliation-statistics-form'))
 || ($_GET['page'] == 'affiliation-manager-affiliate')
 || ($_GET['page'] == 'affiliation-manager-affiliate-category')
 || ($_GET['page'] == 'affiliation-manager-affiliates')
 || ($_GET['page'] == 'affiliation-manager-affiliates-categories')
 || ($_GET['page'] == 'affiliation-manager-clicks')
 || ($_GET['page'] == 'affiliation-manager-commissions')
 || ($_GET['page'] == 'affiliation-manager-payment')
 || ($_GET['page'] == 'affiliation-manager-prospects-commissions')
 || ($_GET['page'] == 'affiliation-manager-recurring-commissions')
 || ($_GET['page'] == 'affiliation-manager-statistics')) { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo AFFILIATION_MANAGER_URL; ?>libraries/date-picker.css" />
<?php } }

add_action('wp_head', 'affiliation_date_picker_css');


function affiliation_date_picker_js() { ?>
<script type="text/javascript" src="<?php echo AFFILIATION_MANAGER_URL; ?>libraries/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="<?php echo AFFILIATION_MANAGER_URL; ?>libraries/jquery-date-picker.js"></script>
<script type="text/javascript">
Date.dayNames = ['<?php _e('Sunday', 'affiliation-manager'); ?>', '<?php _e('Monday', 'affiliation-manager'); ?>', '<?php _e('Tuesday', 'affiliation-manager'); ?>', '<?php _e('Wednesday', 'affiliation-manager'); ?>', '<?php _e('Thursday', 'affiliation-manager'); ?>', '<?php _e('Friday', 'affiliation-manager'); ?>', '<?php _e('Saturday', 'affiliation-manager'); ?>'];
Date.abbrDayNames = ['<?php _e('Sun', 'affiliation-manager'); ?>', '<?php _e('Mon', 'affiliation-manager'); ?>', '<?php _e('Tue', 'affiliation-manager'); ?>', '<?php _e('Wed', 'affiliation-manager'); ?>', '<?php _e('Thu', 'affiliation-manager'); ?>', '<?php _e('Fri', 'affiliation-manager'); ?>', '<?php _e('Sat', 'affiliation-manager'); ?>'];
Date.monthNames = ['<?php _e('January', 'affiliation-manager'); ?>', '<?php _e('February', 'affiliation-manager'); ?>', '<?php _e('March', 'affiliation-manager'); ?>', '<?php _e('April', 'affiliation-manager'); ?>', '<?php _e('May', 'affiliation-manager'); ?>', '<?php _e('June', 'affiliation-manager'); ?>', '<?php _e('July', 'affiliation-manager'); ?>', '<?php _e('August', 'affiliation-manager'); ?>', '<?php _e('September', 'affiliation-manager'); ?>', '<?php _e('October', 'affiliation-manager'); ?>', '<?php _e('November', 'affiliation-manager'); ?>', '<?php _e('December', 'affiliation-manager'); ?>'];
Date.abbrMonthNames = ['<?php _e('Jan', 'affiliation-manager'); ?>', '<?php _e('Feb', 'affiliation-manager'); ?>', '<?php _e('Mar', 'affiliation-manager'); ?>', '<?php _e('Apr', 'affiliation-manager'); ?>', '<?php _e('May', 'affiliation-manager'); ?>', '<?php _e('Jun', 'affiliation-manager'); ?>', '<?php _e('Jul', 'affiliation-manager'); ?>', '<?php _e('Aug', 'affiliation-manager'); ?>', '<?php _e('Sep', 'affiliation-manager'); ?>', '<?php _e('Oct', 'affiliation-manager'); ?>', '<?php _e('Nov', 'affiliation-manager'); ?>', '<?php _e('Dec', 'affiliation-manager'); ?>'];
$.dpText = {
TEXT_PREV_YEAR : '<?php _e('Previous year', 'affiliation-manager'); ?>',
TEXT_PREV_MONTH : '<?php _e('Previous month', 'affiliation-manager'); ?>',
TEXT_NEXT_YEAR : '<?php _e('Next year', 'affiliation-manager'); ?>',
TEXT_NEXT_MONTH : '<?php _e('Next month', 'affiliation-manager'); ?>',
TEXT_CLOSE : '<?php _e('Close', 'affiliation-manager'); ?>',
TEXT_CHOOSE_DATE : '<?php _e('Choose a date', 'affiliation-manager'); ?>',
DATE_PICKER_ALT : '<?php _e('Date', 'affiliation-manager'); ?>',
DATE_PICKER_URL : '<?php echo AFFILIATION_MANAGER_URL; ?>images/date-picker.png',
HEADER_FORMAT : 'mmmm yyyy'
}; $(function(){ $('.date-pick').datePicker({startDate:'2011-01-01'}); });
</script>
<?php }


function affiliation_decrypt_url($url) {
if (function_exists('commerce_decrypt_url')) { return commerce_decrypt_url($url); } }

function affiliation_encrypt_url($url) {
if (function_exists('commerce_encrypt_url')) { return commerce_encrypt_url($url); } }


function affiliation_filter_data($filter, $data) {
if (is_string($filter)) { $filter = preg_split('#[^a-zA-Z0-9_]#', str_replace('-', '_', $filter), 0, PREG_SPLIT_NO_EMPTY); }
if (is_array($filter)) { foreach ($filter as $function) { $data = affiliation_string_map($function, $data); } }
return $data; }


function affiliation_fix_url() {
$url = $_SERVER['REQUEST_URI'];
if (strstr($url, '&amp;')) { $url = str_replace('&amp;', '&', $url); $error = true; }
if ((strstr($url, '?')) && (!strstr($url, '/?')) && (!strstr($url, '.php?'))) { $url = str_replace('?', '/?', $url); $error = true; }
if (($error) && (!headers_sent())) { header('Location: '.$url); exit; } }


function affiliation_format_data($field, $data) {
$data = affiliation_quotes_entities_decode(do_shortcode($data));
if ((strstr($field, 'date')) && ($data == '0000-00-00 00:00:00')) { $data = ''; }
elseif (strstr($field, 'email_address')) { $data = affiliation_format_email_address($data); }
elseif (substr($field, -12) == 'instructions') { $data = affiliation_format_instructions($data); }
elseif (($field == 'url') || (strstr($field, '_url'))) { $data = affiliation_format_url($data); }
switch ($field) {
case 'payments_number': if ($data != 'unlimited') { $data = (int) $data; } break;
case 'cookies_lifetime': $data = (int) $data; if ($data < 1) { $data = 1; } break;
case 'amount': case 'commission_amount': case 'commission_percentage':
case 'commission2_amount': case 'commission2_percentage': case 'first_payment_amount':
case 'payments_amount': case 'price': case 'shipping_cost': case 'tax': case 'tax_percentage':
case 'transaction_cost': $data = round(100*$data)/100; }
return $data; }


function affiliation_format_email_address($string) {
$string = strtolower(trim(strip_tags($string)));
$string = str_replace('à', '@', $string);
$string = str_replace(';', '.', $string);
$string = str_replace(' ', '-', $string);
$string = affiliation_strip_accents($string);
$string = preg_replace('/[^a-zA-Z0-9_@.-]/', '', $string);
return $string; }


function affiliation_format_email_address_js() { ?>
<script type="text/javascript">
function affiliation_format_email_address(string) {
string = string.toLowerCase();
string = string.replace(/[à]/gi, '@');
string = string.replace(/[;]/gi, '.');
string = string.replace(/[ ]/gi, '-');
string = affiliation_strip_accents(string);
string = string.replace(/[^a-zA-Z0-9_@.-]/gi, '');
return string; }
</script>
<?php }


function affiliation_format_instructions($string) {
$string = str_replace('<? ', '<?php ', trim($string));
if (substr($string, 0, 5) == '<?php') { $string = substr($string, 5); }
if (substr($string, -2) == '?>') { $string = substr($string, 0, -2); }
$string = trim($string);
return $string; }


function affiliation_format_medium_nice_name($string) {
$string = affiliation_strip_accents(trim(strip_tags($string)));
$string = str_replace(' ', '-', $string);
$string = preg_replace('/[^a-zA-Z0-9_-]/', '', $string);
return $string; }


function affiliation_format_name($string) {
$string = strtolower(trim(strip_tags($string)));
$string = str_replace(array(' ', '_'), '-', $string);
$strings = explode('-', $string);
$n = count($strings);
for ($i = 0; $i < $n; $i++) { $strings[$i] = ucfirst($strings[$i]); }
$string = implode('-', $strings);
return $string; }


function affiliation_format_name_js() { ?>
<script type="text/javascript">
function affiliation_format_name(string) {
string = string.toLowerCase();
string = string.replace(/[ _]/gi, '-');
var strings = string.split('-');
var n = strings.length;
var i = 0; while (i != n) { strings[i] = (strings[i]).substr(0, 1).toUpperCase()+(strings[i]).substr(1); i = i + 1; }
string = strings.join('-');
return string; }
</script>
<?php }


function affiliation_format_nice_name($string) {
$string = affiliation_strip_accents(strtolower(trim(strip_tags($string))));
$string = str_replace(' ', '-', $string);
$string = preg_replace('/[^a-zA-Z0-9_-]/', '', $string);
return $string; }


function affiliation_format_nice_name_js() { ?>
<script type="text/javascript">
function affiliation_format_nice_name(string) {
string = affiliation_strip_accents(string.toLowerCase());
string = string.replace(/[ ]/gi, '-');
string = string.replace(/[^a-zA-Z0-9_-]/gi, '');
return string; }
</script>
<?php }


function affiliation_format_url($string) {
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


function affiliation_global_statistics() {
if (affiliation_session()) {
foreach ($_POST as $key => $value) { if (substr($key, 0, 2) == 'a_') { $_POST[substr($key, 2)] = $value; } }
if (isset($_POST['submit'])) {
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
return '<h3 id="global-statistics">'.__('Global Statistics', 'affiliation-manager').'</h3>
'.affiliation_statistics_between($start_date, $end_date); }
else {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$end_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$start_date = substr($end_date, 0, 8).'01 00:00:00';
return '<h3 id="global-statistics">'.__('Global Monthly Statistics', 'affiliation-manager').'</h3>
'.affiliation_statistics_between($start_date, $end_date); } } }

add_shortcode('affiliation-global-statistics', 'affiliation_global_statistics');


function affiliation_i18n($string) {
$strings = array(
__('active', 'affiliation-manager'),
__('affiliate', 'affiliation-manager'),
__('affiliator', 'affiliation-manager'),
__('constant', 'affiliation-manager'),
__('day', 'affiliation-manager'),
__('deferred', 'affiliation-manager'),
__('first', 'affiliation-manager'),
__('inactive', 'affiliation-manager'),
__('instant', 'affiliation-manager'),
__('last', 'affiliation-manager'),
__('month', 'affiliation-manager'),
__('no', 'affiliation-manager'),
__('paid', 'affiliation-manager'),
__('processed', 'affiliation-manager'),
__('proportional', 'affiliation-manager'),
__('received', 'affiliation-manager'),
__('refunded', 'affiliation-manager'),
__('unlimited', 'affiliation-manager'),
__('unpaid', 'affiliation-manager'),
__('unprocessed', 'affiliation-manager'),
__('unsubscribed', 'affiliation-manager'),
__('week', 'affiliation-manager'),
__('year', 'affiliation-manager'),
__('yes', 'affiliation-manager'));
return __(__($string), 'affiliation-manager'); }


function affiliation_instructions() {
add_shortcode('affiliation-redirection', 'affiliation_redirection');
$root_url = explode('/', str_replace('//', '||', HOME_URL));
$root_url = str_replace('||', '//', $root_url[0]);
$path = explode('?', strtolower($_SERVER['REQUEST_URI']));
$path = explode('#', $path[0]);
$path = str_replace(HOME_URL, '', $root_url.$path[0]);
while (substr($path, 0, 1) == '/') { $path = substr($path, 1); }
while (substr($path, -1) == '/') { $path = substr($path, 0, -1); }
$post = get_page_by_path($path);
$id = (int) $post->ID;
if ($id == 0) { $id = (int) $_GET['page_id']; }
if ($id > 0) { do_shortcode(get_post_meta($id, 'affiliation', true)); } }


function affiliation_login_form() {
if (!affiliation_session()) {
global $post, $wpdb;
add_action('wp_footer', 'affiliation_strip_accents_js');
add_action('wp_footer', 'affiliation_format_nice_name_js');
add_action('wp_footer', 'affiliation_login_form_js');
foreach ($_POST as $key => $value) { if (substr($key, 0, 2) == 'a_') { $_POST[substr($key, 2)] = $value; } }
if (isset($_POST['submit'])) {
$_POST['login'] = affiliation_format_nice_name(trim(mysql_real_escape_string(strip_tags($_POST['login']))));
$_POST['password'] = trim(mysql_real_escape_string(strip_tags($_POST['password'])));
$result = $wpdb->get_row("SELECT login, status FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['login']."' AND password = '".hash('sha256', $_POST['password'])."'", OBJECT);
if (!$result) { $error .= __('Invalid login or password', 'affiliation-manager'); }
elseif ($result->status == 'inactive') { $error .= __('Your account is inactive.', 'affiliation-manager'); }
if ($error == '') {
session_start();
$_SESSION['a_login'] = $_POST['login'];
if (isset($_POST['remember'])) {
$T = time() + 90*86400;
if (!headers_sent()) { setcookie('a_login', $_POST['login'].hash('sha256', $_POST['login'].AUTH_KEY), $T, '/'); }
else {
$expiration_date = date('D', $T).', '.date('d', $T).' '.date('M', $T).' '.date('Y', $T).' '.date('H:i:s', $T).' UTC';
$content .= '<script type="text/javascript">document.cookie="a_login='.$_POST['login'].hash('sha256', $_POST['login'].AUTH_KEY).'; expires='.$expiration_date.'; path=/";</script>'; } }
if (!headers_sent()) { header('Location: '.$_SERVER['REQUEST_URI']); exit; }
else { $content .= '<script type="text/javascript">window.location = \''.htmlspecialchars($_SERVER['REQUEST_URI']).'\';</script>'; } } }

if ($error != '') { $content .= '<p class="error">'.$error.'</p>'; }

$content .= '
<form method="post" action="'.htmlspecialchars($_SERVER['REQUEST_URI']).'" onsubmit="return validate_affiliation_login_form(this);">
<table style="width: 100%;">
<tr class="login" style="vertical-align: top;"><td style="width: 40%;"><strong><label for="a_login">'.__('Login name', 'affiliation-manager').'</label></strong></td>
<td style="width: 60%;"><input type="text" name="a_login" id="a_login" size="20" value="'.$_POST['login'].'" onchange=\'document.getElementById("a_login").value = affiliation_format_nice_name(document.getElementById("a_login").value);\' /><br />
<span class="error" id="a_login_error"></span></td></tr>
<tr class="password" style="vertical-align: top;"><td style="width: 40%;"><strong><label for="a_password">'.__('Password', 'affiliation-manager').'</label></strong></td>
<td style="width: 60%;"><input type="password" name="a_password" id="a_password" size="20" /><br />
<span class="error" id="a_password_error"></span></td></tr>
</table>
<p style="margin: 0.75em; text-align: center;"><label><input type="checkbox" name="a_remember" id="a_remember" value="yes"'.(isset($_POST['remember']) ? ' checked="checked"' : '').' /> '.__('Remember me', 'affiliation-manager').'</label></p>
<div style="text-align: center;"><input type="submit" name="a_submit" value="'.__('Login', 'affiliation-manager').'" /></div>
</form>';

return $content; } }

add_shortcode('affiliation-login-form', 'affiliation_login_form');


function affiliation_login_form_js() { ?>
<script type="text/javascript">
function validate_affiliation_login_form(form) {
var error = false;
form.a_login.value = affiliation_format_nice_name(form.a_login.value);
if (form.a_login.value == '') {
document.getElementById('a_login_error').innerHTML = '<?php _e('This field is required.', 'affiliation-manager'); ?>';
error = true; }
if (form.a_password.value == '') {
document.getElementById('a_password_error').innerHTML = '<?php _e('This field is required.', 'affiliation-manager'); ?>';
error = true; }
return !error; }
</script>
<?php }


function affiliation_logout() {
session_start();
unset($_SESSION['a_login']);
setcookie('a_login', '', time() - 86400, '/'); }


function affiliation_password_reset_form() {
if (!affiliation_session()) {
global $wpdb;
add_action('wp_footer', 'affiliation_strip_accents_js');
add_action('wp_footer', 'affiliation_format_email_address_js');
add_action('wp_footer', 'affiliation_password_reset_form_js');
foreach ($_POST as $key => $value) { if (substr($key, 0, 2) == 'a_') { $_POST[substr($key, 2)] = $value; } }
if (isset($_POST['submit'])) {
$_POST['email_address'] = affiliation_format_email_address(trim(mysql_real_escape_string(strip_tags($_POST['email_address']))));
$result = $wpdb->get_row("SELECT email_address FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".$_POST['email_address']."'", OBJECT);
$result2 = $wpdb->get_row("SELECT paypal_email_address FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE paypal_email_address = '".$_POST['email_address']."'", OBJECT);
if ((!$result) && (!$result2)) { $error .= __('This email address does not match an affiliate account.', 'affiliation-manager'); $content .= '<p class="error">'.$error.'</p>'; }
else {
$_POST['password'] = substr(md5(mt_rand()), 0, 8);
if ($result) { $results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_affiliates SET password = '".hash('sha256', $_POST['password'])."' WHERE email_address = '".$_POST['email_address']."'");
$_GET['affiliate_data'] = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".$_POST['email_address']."'", OBJECT); }
elseif ($result2) { $results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_affiliates SET password = '".hash('sha256', $_POST['password'])."' WHERE paypal_email_address = '".$_POST['email_address']."'");
$_GET['affiliate_data'] = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE paypal_email_address = '".$_POST['email_address']."'", OBJECT); }
$_GET['affiliate_data']->password = $_POST['password'];
$_GET['affiliate_data']->email_address = $_POST['email_address'];
$sender = affiliation_data('password_reset_email_sender');
$receiver = affiliation_data('password_reset_email_receiver');
$subject = affiliation_data('password_reset_email_subject');
$body = affiliation_data('password_reset_email_body');
$headers = 'From: '.$sender;
wp_mail($receiver, $subject, $body, $headers);
$content .= '<p class="valid">'.__('Your password has been reset successfully.', 'affiliation-manager').'</p>'; } }

$content .= '
<form style="text-align: center;" method="post" action="'.htmlspecialchars($_SERVER['REQUEST_URI']).'" onsubmit="return validate_affiliation_password_reset_form(this);">
<p class="email-address"><label><strong>'.__('Your email address:', 'affiliation-manager').'</strong><br />
<input type="text" name="a_email_address" id="a_email_address" size="40" value="'.$_POST['email_address'].'" onchange=\'document.getElementById("a_email_address").value = affiliation_format_email_address(document.getElementById("a_email_address").value);\' /><br /></label>
<span class="error" id="a_email_address_error"></span></p>
<div><input type="submit" name="a_submit" value="'.__('Reset', 'affiliation-manager').'" /></div>
</form>';

return $content; } }

add_shortcode('affiliation-password-reset-form', 'affiliation_password_reset_form');


function affiliation_password_reset_form_js() { ?>
<script type="text/javascript">
function validate_affiliation_password_reset_form(form) {
var error = false;
form.a_email_address.value = affiliation_format_email_address(form.a_email_address.value);
if ((form.a_email_address.value.indexOf('@') == -1) || (form.a_email_address.value.indexOf('.') == -1)) {
document.getElementById('a_email_address_error').innerHTML = '<?php _e('This email address appears to be invalid.', 'affiliation-manager'); ?>';
error = true; }
if (form.a_email_address.value == '') {
document.getElementById('a_email_address_error').innerHTML = '<?php _e('This field is required.', 'affiliation-manager'); ?>';
error = true; }
return !error; }
</script>
<?php }


function affiliation_profile_form() {
if (affiliation_session()) {
global $wpdb;
$_GET['affiliate_data'] = (array) $_GET['affiliate_data'];
if ((isset($_SESSION['a_login'])) && ($_GET['affiliate_data']['login'] != $_SESSION['a_login'])) {
$_GET['affiliate_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_SESSION['a_login']."'", OBJECT); }
$affiliate_data = (object) $_GET['affiliate_data'];
add_action('wp_footer', 'affiliation_strip_accents_js');
add_action('wp_footer', 'affiliation_format_name_js');
add_action('wp_footer', 'affiliation_format_email_address_js');
add_action('wp_footer', 'affiliation_profile_form_js');
$minimum_password_length = affiliation_data('minimum_password_length');
$maximum_password_length = affiliation_data('maximum_password_length');
foreach ($_POST as $key => $value) { if (substr($key, 0, 2) == 'a_') { $_POST[substr($key, 2)] = $value; } }
if (isset($_POST['submit'])) {
$_POST = array_map('strip_tags', $_POST);
$_POST = array_map('affiliation_quotes_entities', $_POST);
$_POST = array_map('mysql_real_escape_string', $_POST);
$_POST = array_map('stripslashes', $_POST);
$_POST = array_map('trim', $_POST);
$_POST['first_name'] = affiliation_format_name($_POST['first_name']);
$_POST['last_name'] = affiliation_format_name($_POST['last_name']);
$_POST['email_address'] = affiliation_format_email_address($_POST['email_address']);
$_POST['paypal_email_address'] = affiliation_format_email_address($_POST['paypal_email_address']);
$_POST['website_url'] = affiliation_format_url($_POST['website_url']);
if ($_POST['password'] != '') {
if (strlen($_POST['password']) < $minimum_password_length) {
$error .= ' '.sprintf(__('Your password must contain at least %d characters.', 'affiliation-manager'), $minimum_password_length); }
if (strlen($_POST['password']) > $maximum_password_length) {
$error .= ' '.sprintf(__('Your password must contain at most %d characters.', 'affiliation-manager'), $maximum_password_length); }
if ($error == '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_affiliates SET password = '".hash('sha256', $_POST['password'])."' WHERE login = '".$_SESSION['a_login']."'"); } }
if ($_POST['first_name'] != '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_affiliates SET first_name = '".$_POST['first_name']."' WHERE login = '".$_SESSION['a_login']."'"); }
if ($_POST['last_name'] != '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_affiliates SET last_name = '".$_POST['last_name']."' WHERE login = '".$_SESSION['a_login']."'"); }
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".$_POST['email_address']."'", OBJECT);
if (($result) && ($result->login != $_SESSION['a_login'])) { $error .= ' '.__('This email address is not available.', 'affiliation-manager'); }
elseif ($_POST['email_address'] != '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_affiliates SET email_address = '".$_POST['email_address']."' WHERE login = '".$_SESSION['a_login']."'"); }
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE paypal_email_address = '".$_POST['paypal_email_address']."'", OBJECT);
if (($result) && ($result->login != $_SESSION['a_login'])) { $error .= ' '.__('This PayPal email address is not available.', 'affiliation-manager'); }
elseif ($_POST['paypal_email_address'] != '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_affiliates SET paypal_email_address = '".$_POST['paypal_email_address']."' WHERE login = '".$_SESSION['a_login']."'"); }
if (($_POST['first_name'] == '') || ($_POST['last_name'] == '') || ($_POST['email_address'] == '') || ($_POST['paypal_email_address'] == '')) {
$error .= ' '.__('Please fill out the required fields.', 'affiliation-manager'); }

$results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_affiliates SET
	website_name = '".$_POST['website_name']."',
	website_url = '".$_POST['website_url']."',
	address = '".$_POST['address']."',
	postcode = '".$_POST['postcode']."',
	town = '".$_POST['town']."',
	country = '".$_POST['country']."',
	phone_number = '".$_POST['phone_number']."' WHERE login = '".$_SESSION['a_login']."'");

$_GET['affiliate_data'] = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_SESSION['a_login']."'", OBJECT);
$affiliate_data = $_GET['affiliate_data'];

if ($error != '') { $content .= '<p class="error">'.$error.'</p>'; }
else { $content .= '<p class="valid">'.__('Your profile has been changed successfully.', 'affiliation-manager').'</p>'; } }

$content .= '
<form method="post" action="'.htmlspecialchars($_SERVER['REQUEST_URI']).'" onsubmit="return validate_affiliation_profile_form(this);">
<table style="width: 100%;">
<tr class="login" style="vertical-align: top;"><td><strong><label for="a_login">'.__('Login name', 'affiliation-manager').'</label></strong>*</td>
<td><input type="text" name="a_login" id="a_login" size="30" value="'.$affiliate_data->login.'" disabled="disabled" /><br />
<span class="description">'.__('Your login name can not be changed.', 'affiliation-manager').'</span></td></tr>
<tr class="password" style="vertical-align: top;"><td><strong><label for="a_password">'.__('Password', 'affiliation-manager').'</label></strong></td>
<td><input type="password" name="a_password" id="a_password" size="30" value="" /><br />
<span class="description">'.__('(if you want to change it)', 'affiliation-manager').'</span><br />
<span class="error" id="a_password_error"></span></td></tr>
<tr class="first-name" style="vertical-align: top;"><td><strong><label for="a_first_name">'.__('First name', 'affiliation-manager').'</label></strong>*</td>
<td><input type="text" name="a_first_name" id="a_first_name" size="30" value="'.$affiliate_data->first_name.'" /><br />
<span class="error" id="a_first_name_error"></span></td></tr>
<tr class="last-name" style="vertical-align: top;"><td><strong><label for="a_last_name">'.__('Last name', 'affiliation-manager').'</label></strong>*</td>
<td><input type="text" name="a_last_name" id="a_last_name" size="30" value="'.$affiliate_data->last_name.'" /><br />
<span class="error" id="a_last_name_error"></span></td></tr>
<tr class="email-address" style="vertical-align: top;"><td><strong><label for="a_email_address">'.__('Email address', 'affiliation-manager').'</label></strong>*</td>
<td><input type="text" name="a_email_address" id="a_email_address" size="30" value="'.$affiliate_data->email_address.'" onchange=\'document.getElementById("a_email_address").value = affiliation_format_email_address(document.getElementById("a_email_address").value);\' /><br />
<span class="error" id="a_email_address_error"></span></td></tr>
<tr class="paypal-email-address" style="vertical-align: top;"><td><strong><label for="a_paypal_email_address">'.__('PayPal email address', 'affiliation-manager').'</label></strong>*</td>
<td><input type="text" name="a_paypal_email_address" id="a_paypal_email_address" size="30" value="'.$affiliate_data->paypal_email_address.'" onchange=\'document.getElementById("a_paypal_email_address").value = affiliation_format_email_address(document.getElementById("a_paypal_email_address").value);\' /><br />
<span class="error" id="a_paypal_email_address_error"></span></td></tr>
<tr class="website-name" style="vertical-align: top;"><td><strong><label for="a_website_name">'.__('Website name', 'affiliation-manager').'</label></strong></td>
<td><input type="text" name="a_website_name" id="a_website_name" size="30" value="'.$affiliate_data->website_name.'" /></td></tr>
<tr class="website-url" style="vertical-align: top;"><td><strong><label for="a_website_url">'.__('Website URL', 'affiliation-manager').'</label></strong></td>
<td><input type="text" name="a_website_url" id="a_website_url" size="30" value="'.$affiliate_data->website_url.'" /></td></tr>
<tr class="address" style="vertical-align: top;"><td><strong><label for="a_address">'.__('Address', 'affiliation-manager').'</label></strong></td>
<td><input type="text" name="a_address" id="a_address" size="30" value="'.$affiliate_data->address.'" /></td></tr>
<tr class="postcode" style="vertical-align: top;"><td><strong><label for="a_postcode">'.__('Postcode', 'affiliation-manager').'</label></strong></td>
<td><input type="text" name="a_postcode" id="a_postcode" size="30" value="'.$affiliate_data->postcode.'" /></td></tr>
<tr class="town" style="vertical-align: top;"><td><strong><label for="a_town">'.__('Town', 'affiliation-manager').'</label></strong></td>
<td><input type="text" name="a_town" id="a_town" size="30" value="'.$affiliate_data->town.'" /></td></tr>
<tr class="country" style="vertical-align: top;"><td><strong><label for="a_country">'.__('Country', 'affiliation-manager').'</label></strong></td>
<td><input type="text" name="a_country" id="a_country" size="30" value="'.$affiliate_data->country.'" /></td></tr>
<tr class="phone-number" style="vertical-align: top;"><td><strong><label for="a_phone_number">'.__('Phone number', 'affiliation-manager').'</label></strong></td>
<td><input type="text" name="a_phone_number" id="a_phone_number" size="30" value="'.$affiliate_data->phone_number.'" /></td></tr>
</table>
<p id="a_form_error"></p>
<div style="text-align: center;"><input type="submit" name="a_submit" value="'.__('Modify', 'affiliation-manager').'" /></div>
</form>';

return $content; } }

add_shortcode('affiliation-profile-form', 'affiliation_profile_form');


function affiliation_profile_form_js() { ?>
<script type="text/javascript">
function validate_affiliation_profile_form(form) {
var error = false;
form.a_first_name.value = affiliation_format_name(form.a_first_name.value);
form.a_last_name.value = affiliation_format_name(form.a_last_name.value);
form.a_email_address.value = affiliation_format_email_address(form.a_email_address.value);
form.a_paypal_email_address.value = affiliation_format_email_address(form.a_paypal_email_address.value);
if (form.a_first_name.value == '') {
document.getElementById('a_first_name_error').innerHTML = '<?php _e('This field is required.', 'affiliation-manager'); ?>';
error = true; }
if (form.a_last_name.value == '') {
document.getElementById('a_last_name_error').innerHTML = '<?php _e('This field is required.', 'affiliation-manager'); ?>';
error = true; }
if ((form.a_email_address.value.indexOf('@') == -1) || (form.a_email_address.value.indexOf('.') == -1)) {
document.getElementById('a_email_address_error').innerHTML = '<?php _e('This email address appears to be invalid.', 'affiliation-manager'); ?>';
error = true; }
if (form.a_email_address.value == '') {
document.getElementById('a_email_address_error').innerHTML = '<?php _e('This field is required.', 'affiliation-manager'); ?>';
error = true; }
if ((form.a_paypal_email_address.value.indexOf('@') == -1) || (form.a_paypal_email_address.value.indexOf('.') == -1)) {
document.getElementById('a_paypal_email_address_error').innerHTML = '<?php _e('This email address appears to be invalid.', 'affiliation-manager'); ?>';
error = true; }
if (form.a_paypal_email_address.value == '') {
document.getElementById('a_paypal_email_address_error').innerHTML = '<?php _e('This field is required.', 'affiliation-manager'); ?>';
error = true; }
if (error) { document.getElementById('a_form_error').innerHTML = '<?php _e('An error has occurred. Please check the fields and resubmit the form.', 'affiliation-manager'); ?>'; }
return !error; }
</script>
<?php }


function affiliation_prospects_commissions_statistics($atts) {
if (affiliation_session()) {
extract(shortcode_atts(array('level' => 0), $atts));
switch ($level) { case 1: case 2: break; default: $level = 0; }
switch ($level) {
case '0': $content = affiliation_prospects_commissions1_statistics().affiliation_prospects_commissions2_statistics(); break;
case '1': $content = affiliation_prospects_commissions1_statistics(); break;
case '2': $content = affiliation_prospects_commissions2_statistics(); }
return $content; } }

add_shortcode('affiliation-prospects-commissions-statistics', 'affiliation_prospects_commissions_statistics');


function affiliation_prospects_commissions1_statistics() {
if (affiliation_session()) {
global $commerce_manager_options, $optin_manager_options, $wpdb;
$currency_code = do_shortcode($commerce_manager_options['currency_code']);
foreach ($_POST as $key => $value) { if (substr($key, 0, 2) == 'a_') { $_POST[substr($key, 2)] = $value; } }
if (isset($_POST['submit'])) {
$start_date = trim(mysql_real_escape_string(strip_tags($_POST['start_date'])));
if (strlen($start_date) == 10) { $start_date .= ' 00:00:00'; }
$end_date = trim(mysql_real_escape_string(strip_tags($_POST['end_date'])));
if (strlen($end_date) == 10) { $end_date .= ' 23:59:59'; }
$content .= '<h3 id="prospects-commissions-statistics">'.__('Prospects Commissions Statistics', 'affiliation-manager').' '.__('(Level 1)', 'affiliation-manager').'</h3>';	}
else {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$end_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$start_date = substr($end_date, 0, 8).'01 00:00:00';
$content .= '<h3 id="prospects-commissions-statistics">'.__('Prospects Commissions Monthly Statistics', 'affiliation-manager').' '.__('(Level 1)', 'affiliation-manager').'</h3>'; }

$prospects = $wpdb->get_results("SELECT commission_amount, commission_status, date, form_id FROM ".$wpdb->prefix."optin_manager_prospects WHERE commission_amount > 0 AND referrer = '".$_SESSION['a_login']."' AND (date BETWEEN '$start_date' AND '$end_date') ORDER BY date DESC", OBJECT);
if ($prospects) {		
$content .= '<table style="width: 100%;">
<tr style="vertical-align: top;"><th>'.__('Date', 'affiliation-manager').'</th><th>'.__('Form', 'affiliation-manager').'</th><th>'.__('Amount', 'affiliation-manager').'</th><th>'.__('Status', 'affiliation-manager').'</th></tr>';
foreach ($prospects as $prospect) {
if (function_exists('optin_form_data')) {
$form_name = optin_form_data(array('name', id => $prospect->form_id)); }
if ($prospect->commission_status == 'paid') { $commission_status = __('Paid', 'affiliation-manager'); }
else { $commission_status = __('Unpaid', 'affiliation-manager'); }
$content .= '<tr style="vertical-align: top;">
<td>'.$prospect->date.'</td>
<td>'.$form_name.'</td>
<td>'.$prospect->commission_amount.' '.$currency_code.'</td>
<td class="'.$prospect->commission_status.'">'.$commission_status.'</td></tr>'; }
$content .= '</table>'; }
else { $content .= '<p>'.__('No commissions', 'affiliation-manager').'</p>'; }

return $content; } }


function affiliation_prospects_commissions2_statistics() {
if (affiliation_session()) {
global $commerce_manager_options, $optin_manager_options, $wpdb;
$currency_code = do_shortcode($commerce_manager_options['currency_code']);
foreach ($_POST as $key => $value) { if (substr($key, 0, 2) == 'a_') { $_POST[substr($key, 2)] = $value; } }
if (isset($_POST['submit'])) {
$start_date = trim(mysql_real_escape_string(strip_tags($_POST['start_date'])));
if (strlen($start_date) == 10) { $start_date .= ' 00:00:00'; }
$end_date = trim(mysql_real_escape_string(strip_tags($_POST['end_date'])));
if (strlen($end_date) == 10) { $end_date .= ' 23:59:59'; }
$content .= '<h3 id="prospects-commissions2-statistics">'.__('Prospects Commissions Statistics', 'affiliation-manager').' '.__('(Level 2)', 'affiliation-manager').'</h3>';	}
else {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$end_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$start_date = substr($end_date, 0, 8).'01 00:00:00';
$content .= '<h3 id="prospects-commissions2-statistics">'.__('Prospects Commissions Monthly Statistics', 'affiliation-manager').' '.__('(Level 2)', 'affiliation-manager').'</h3>'; }

$prospects = $wpdb->get_results("SELECT commission2_amount, commission2_status, date, form_id FROM ".$wpdb->prefix."optin_manager_prospects WHERE commission2_amount > 0 AND referrer = '".$_SESSION['a_login']."' AND (date BETWEEN '$start_date' AND '$end_date') ORDER BY date DESC", OBJECT);
if ($prospects) {		
$content .= '<table style="width: 100%;">
<tr style="vertical-align: top;"><th>'.__('Date', 'affiliation-manager').'</th><th>'.__('Form', 'affiliation-manager').'</th><th>'.__('Amount', 'affiliation-manager').'</th><th>'.__('Status', 'affiliation-manager').'</th></tr>';
foreach ($prospects as $prospect) {
if (function_exists('optin_form_data')) {
$form_name = optin_form_data(array('name', id => $prospect->form_id)); }
if ($prospect->commission2_status == 'paid') { $commission_status = __('Paid', 'affiliation-manager'); }
else { $commission_status = __('Unpaid', 'affiliation-manager'); }
$content .= '<tr style="vertical-align: top;">
<td>'.$prospect->date.'</td>
<td>'.$form_name.'</td>
<td>'.$prospect->commission2_amount.' '.$currency_code.'</td>
<td class="'.$prospect->commission2_status.'">'.$commission2_status.'</td></tr>'; }
$content .= '</table>'; }
else { $content .= '<p>'.__('No commissions', 'affiliation-manager').'</p>'; }

return $content; } }


function affiliation_quotes_entities($string) {
return str_replace(array("'", '"'), array("&apos;", '&quot;'), $string); }


function affiliation_quotes_entities_decode($string) {
return str_replace(array("&apos;", '&quot;'), array("'", '"'), $string); }


function affiliation_recurring_commissions1_statistics() {
if (affiliation_session()) {
global $commerce_manager_options, $wpdb;
$currency_code = do_shortcode($commerce_manager_options['currency_code']);
foreach ($_POST as $key => $value) { if (substr($key, 0, 2) == 'a_') { $_POST[substr($key, 2)] = $value; } }
if (isset($_POST['submit'])) {
$start_date = trim(mysql_real_escape_string(strip_tags($_POST['start_date'])));
if (strlen($start_date) == 10) { $start_date .= ' 00:00:00'; }
$end_date = trim(mysql_real_escape_string(strip_tags($_POST['end_date'])));
if (strlen($end_date) == 10) { $end_date .= ' 23:59:59'; }
$content .= '<h3 id="recurring-commissions-statistics">'.__('Recurring Commissions Statistics', 'affiliation-manager').' '.__('(Level 1)', 'affiliation-manager').'</h3>';	}
else {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$end_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$start_date = substr($end_date, 0, 8).'01 00:00:00';
$content .= '<h3 id="recurring-commissions-statistics">'.__('Recurring Commissions Monthly Statistics', 'affiliation-manager').' '.__('(Level 1)', 'affiliation-manager').'</h3>'; }

$recurring_payments = $wpdb->get_results("SELECT commission_amount, commission_status, date, product_id FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE commission_amount > 0 AND referrer = '".$_SESSION['a_login']."' AND (date BETWEEN '$start_date' AND '$end_date') ORDER BY date DESC", OBJECT);
if ($recurring_payments) {		
$content .= '<table style="width: 100%;">
<tr style="vertical-align: top;"><th>'.__('Date', 'affiliation-manager').'</th><th>'.__('Product', 'affiliation-manager').'</th><th>'.__('Amount', 'affiliation-manager').'</th><th>'.__('Status', 'affiliation-manager').'</th></tr>';
foreach ($recurring_payments as $recurring_payment) {
if (function_exists('product_data')) {
$product_name = product_data(array('name', id => $recurring_payment->product_id));
$product_url = product_data(array('url', id => $recurring_payment->product_id)); }
if ($recurring_payment->commission_status == 'paid') { $commission_status = __('Paid', 'affiliation-manager'); }
else { $commission_status = __('Unpaid', 'affiliation-manager'); }
$content .= '<tr style="vertical-align: top;">
<td>'.$recurring_payment->date.'</td>
<td>'.($product_url == '' ? $product_name : '<a href="'.$product_url.'">'.$product_name.'</a>').'</td>
<td>'.$recurring_payment->commission_amount.' '.$currency_code.'</td>
<td class="'.$recurring_payment->commission_status.'">'.$commission_status.'</td></tr>'; }
$content .= '</table>'; }
else { $content .= '<p>'.__('No commissions', 'affiliation-manager').'</p>'; }

return $content; } }


function affiliation_recurring_commissions2_statistics() {
if (affiliation_session()) {
global $commerce_manager_options, $wpdb;
$currency_code = do_shortcode($commerce_manager_options['currency_code']);
foreach ($_POST as $key => $value) { if (substr($key, 0, 2) == 'a_') { $_POST[substr($key, 2)] = $value; } }
if (isset($_POST['submit'])) {
$start_date = trim(mysql_real_escape_string(strip_tags($_POST['start_date'])));
if (strlen($start_date) == 10) { $start_date .= ' 00:00:00'; }
$end_date = trim(mysql_real_escape_string(strip_tags($_POST['end_date'])));
if (strlen($end_date) == 10) { $end_date .= ' 23:59:59'; }
$content .= '<h3 id="recurring-commissions2-statistics">'.__('Recurring Commissions Statistics', 'affiliation-manager').' '.__('(Level 2)', 'affiliation-manager').'</h3>';	}
else {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$end_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$start_date = substr($end_date, 0, 8).'01 00:00:00';
$content .= '<h3 id="recurring-commissions2-statistics">'.__('Recurring Commissions Monthly Statistics', 'affiliation-manager').' '.__('(Level 2)', 'affiliation-manager').'</h3>'; }

$recurring_payments = $wpdb->get_results("SELECT commission2_amount, commission2_status, date, product_id FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE commission2_amount > 0 AND referrer2 = '".$_SESSION['a_login']."' AND (date BETWEEN '$start_date' AND '$end_date') ORDER BY date DESC", OBJECT);
if ($recurring_payments) {		
$content .= '<table style="width: 100%;">
<tr style="vertical-align: top;"><th>'.__('Date', 'affiliation-manager').'</th><th>'.__('Product', 'affiliation-manager').'</th><th>'.__('Amount', 'affiliation-manager').'</th><th>'.__('Status', 'affiliation-manager').'</th></tr>';
foreach ($recurring_payments as $recurring_payment) {
if (function_exists('product_data')) {
$product_name = product_data(array('name', id => $recurring_payment->product_id));
$product_url = product_data(array('url', id => $recurring_payment->product_id)); }
if ($recurring_payment->commission2_status == 'paid') { $commission2_status = __('Paid', 'affiliation-manager'); }
else { $commission2_status = __('Unpaid', 'affiliation-manager'); }
$content .= '<tr style="vertical-align: top;">
<td>'.$recurring_payment->date.'</td>
<td>'.($product_url == '' ? $product_name : '<a href="'.$product_url.'">'.$product_name.'</a>').'</td>
<td>'.$recurring_payment->commission2_amount.' '.$currency_code.'</td>
<td class="'.$recurring_payment->commission2_status.'">'.$commission2_status.'</td></tr>'; }
$content .= '</table>'; }
else { $content .= '<p>'.__('No commissions', 'affiliation-manager').'</p>'; }

return $content; } }


function affiliation_redirection($atts) {
extract(shortcode_atts(array('action' => '', 'condition' => '', 'url' => ''), $atts));
$action = strtolower($action);
$condition = strtolower($condition);
if ($url == '') { $url = '../'; }
switch ($condition) {
case 'session': if (affiliation_session()) {
if ($action == 'logout') { affiliation_logout(); }
if (!headers_sent()) { header('Location: '.$url); exit; } } else { $url = ''; } break;
case '!session': if (!affiliation_session()) {
if (!headers_sent()) { header('Location: '.$url); exit; } } else { $url = ''; } break;
default: if (($action == 'logout') && (affiliation_session())) { affiliation_logout(); }
if (!headers_sent()) { header('Location: '.$url); exit; } }
return $url; }

add_shortcode('affiliation-redirection', 'affiliation_redirection');


function affiliation_registration_compact_form($atts) {
$atts['size'] = 'compact';
return affiliation_registration_form($atts); }

add_shortcode('affiliation-registration-compact-form', 'affiliation_registration_compact_form');


function affiliation_registration_form($atts) {
if (!affiliation_session()) {
global $wpdb;
extract(shortcode_atts(array('size' => ''), $atts));
$size = affiliation_format_nice_name($size);
add_action('wp_footer', 'affiliation_strip_accents_js');
add_action('wp_footer', 'affiliation_format_nice_name_js');
add_action('wp_footer', 'affiliation_format_name_js');
add_action('wp_footer', 'affiliation_format_email_address_js');
add_action('wp_footer', 'affiliation_registration_form_js');
$minimum_login_length = affiliation_data('minimum_login_length');
$maximum_login_length = affiliation_data('maximum_login_length');
$minimum_password_length = affiliation_data('minimum_password_length');
$maximum_password_length = affiliation_data('maximum_password_length');
foreach ($_POST as $key => $value) { if (substr($key, 0, 2) == 'a_') { $_POST[substr($key, 2)] = $value; } }
if (!isset($_POST['referring_url'])) { $_POST['referring_url'] = htmlspecialchars($_SERVER['HTTP_REFERER']); }
if (isset($_POST['submit'])) {
$_POST = array_map('strip_tags', $_POST);
$_POST = array_map('affiliation_quotes_entities', $_POST);
$_POST = array_map('mysql_real_escape_string', $_POST);
$_POST = array_map('stripslashes', $_POST);
$_POST = array_map('trim', $_POST);
$_POST['login'] = affiliation_format_nice_name($_POST['login']);
$_POST['first_name'] = affiliation_format_name($_POST['first_name']);
$_POST['last_name'] = affiliation_format_name($_POST['last_name']);
$_POST['email_address'] = affiliation_format_email_address($_POST['email_address']);
$_POST['paypal_email_address'] = affiliation_format_email_address($_POST['paypal_email_address']);
$_POST['website_url'] = affiliation_format_url($_POST['website_url']);
$_POST['referring_url'] = html_entity_decode($_POST['referring_url']);
if (is_numeric($_POST['login'])) { $error .= __('Your login name must be a non-numeric string.', 'affiliation-manager'); }
if (strlen($_POST['login']) < $minimum_login_length) {
$error .= ' '.sprintf(__('Your login name must contain at least %d characters.', 'affiliation-manager'), $minimum_login_length); }
if (strlen($_POST['login']) > $maximum_login_length) {
$error .= ' '.sprintf(__('Your login name must contain at most %d characters.', 'affiliation-manager'), $maximum_login_length); }
$result = $wpdb->get_results("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['login']."'", OBJECT);
if ($result) { $error .= ' '.__('This login name is not available.', 'affiliation-manager'); }
if (strlen($_POST['password']) < $minimum_password_length) {
$error .= ' '.sprintf(__('Your password must contain at least %d characters.', 'affiliation-manager'), $minimum_password_length); }
if (strlen($_POST['password']) > $maximum_password_length) {
$error .= ' '.sprintf(__('Your password must contain at most %d characters.', 'affiliation-manager'), $maximum_password_length); }
$result = $wpdb->get_results("SELECT email_address FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".$_POST['email_address']."'", OBJECT);
if ($result) { $error .= ' '.__('This email address is not available.', 'affiliation-manager'); }
$result = $wpdb->get_results("SELECT paypal_email_address FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE paypal_email_address = '".$_POST['paypal_email_address']."'", OBJECT);
if ($result) { $error .= ' '.__('This PayPal email address is not available.', 'affiliation-manager'); }
if (($_POST['login'] == '') || ($_POST['first_name'] == '') || ($_POST['last_name'] == '') || ($_POST['email_address'] == '') || ($_POST['paypal_email_address'] == '')) {
$error .= ' '.__('Please fill out the required fields.', 'affiliation-manager'); }

if ($error == '') {
$_POST['id'] = '';
$_POST['category_id'] = affiliation_data('affiliates_initial_category_id');
$_POST['status'] = affiliation_data('affiliates_initial_status');
$_POST['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
$_POST['ip_address'] = $_SERVER['REMOTE_ADDR'];
$_POST['referrer'] = $_COOKIE[AFFILIATION_COOKIES_NAME];
if ($_POST['referrer'] == '') {
$result = $wpdb->get_row("SELECT referrer FROM ".$wpdb->prefix."affiliation_manager_clicks WHERE ip_address = '".$_SERVER['REMOTE_ADDR']."' ORDER BY date DESC LIMIT 1", OBJECT);
if ($result) { $_POST['referrer'] = $result->referrer; } }
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['referrer']."' AND status = 'active'", OBJECT);
if (!$result) { $_POST['referrer'] = ''; }
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$_POST['date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['date_utc'] = date('Y-m-d H:i:s');
$_POST['bonus_download_url'] = '';
$_POST['bonus_instructions'] = '';
$_POST['commission_type'] = '';
$_POST['commission_amount'] = '';
$_POST['commission_percentage'] = '';
$_POST['commission_payment'] = '';
$_POST['first_sale_winner'] = '';
$_POST['commission2_enabled'] = '';
$_POST['commission2_type'] = '';
$_POST['commission2_amount'] = '';
$_POST['commission2_percentage'] = '';
add_affiliate($_POST);

if ($_GET['autoresponder_subscription'] == '') {
if (!headers_sent()) { header('Location: '.affiliation_data('registration_confirmation_url')); exit; }
else { $content .= '<script type="text/javascript">window.location = \''.affiliation_data('registration_confirmation_url').'\';</script>'; } }
else { $content .= '<div><img alt="" src="'.$_GET['autoresponder_subscription'].'" /></div><script type="text/javascript">setTimeout("window.location=\''.affiliation_data('registration_confirmation_url').'\'", 3000);</script>'; } } }

if ($error != '') { $content .= '<p class="error">'.$error.'</p>'; }

if ($size == 'compact') {
$content .= '
<form method="post" action="'.htmlspecialchars($_SERVER['REQUEST_URI']).'" onsubmit="return validate_affiliation_registration_form(this);">
<table style="width: 100%;">
<tr class="login" style="vertical-align: top;"><td><strong><label for="a_login">'.__('Login name', 'affiliation-manager').'</label></strong></td>
<td><input type="text" name="a_login" id="a_login" size="30" value="'.$_POST['login'].'" onchange=\'document.getElementById("a_login").value = affiliation_format_nice_name(document.getElementById("a_login").value); $.get("'.AFFILIATION_MANAGER_URL.'?action=check-login",{ login: $("#a_login").val() } ,function(data){ $("#a_login_available").html(data); });\' /> 
<span id="a_login_available">'.(strstr($error, __('login name', 'affiliation-manager')) ? '<span class="error">'.__('Unavailable', 'affiliation-manager').'</span>' : '').'</span><br />
<span class="description">'.__('Letters, numbers, hyphens and underscores only', 'affiliation-manager').'<br />
'.__('Your login name will be included in your affiliate links.', 'affiliation-manager').'</span><br />
<span class="error" id="a_login_error"></span></td></tr>
<tr class="password" style="vertical-align: top;"><td><strong><label for="a_password">'.__('Password', 'affiliation-manager').'</label></strong></td>
<td><input type="password" name="a_password" id="a_password" size="30" value="'.$_POST['password'].'" /> <span class="description">'.sprintf(__('at least %d characters', 'affiliation-manager'), $minimum_password_length).'</span><br />
<span class="error" id="a_password_error"></span></td></tr>
<tr class="first-name" style="vertical-align: top;"><td><strong><label for="a_first_name">'.__('First name', 'affiliation-manager').'</label></strong></td>
<td><input type="text" name="a_first_name" id="a_first_name" size="30" value="'.$_POST['first_name'].'" /><br />
<span class="error" id="a_first_name_error"></span></td></tr>
<tr class="last-name" style="vertical-align: top;"><td><strong><label for="a_last_name">'.__('Last name', 'affiliation-manager').'</label></strong></td>
<td><input type="text" name="a_last_name" id="a_last_name" size="30" value="'.$_POST['last_name'].'" /><br />
<span class="error" id="a_last_name_error"></span></td></tr>
<tr class="email-address" style="vertical-align: top;"><td><strong><label for="a_email_address">'.__('Email address', 'affiliation-manager').'</label></strong></td>
<td><input type="text" name="a_email_address" id="a_email_address" size="30" value="'.$_POST['email_address'].'" onchange=\'document.getElementById("a_email_address").value = affiliation_format_email_address(document.getElementById("a_email_address").value);\' /><br />
<span class="error" id="a_email_address_error"></span></td></tr>
<tr class="paypal-email-address" style="vertical-align: top;"><td><strong><label for="a_paypal_email_address">'.__('PayPal email address', 'affiliation-manager').'</label></strong></td>
<td><input type="text" name="a_paypal_email_address" id="a_paypal_email_address" size="30" value="'.$_POST['paypal_email_address'].'" onchange=\'document.getElementById("a_paypal_email_address").value = affiliation_format_email_address(document.getElementById("a_paypal_email_address").value);\' /><br />
<span class="error" id="a_paypal_email_address_error"></span></td></tr>
</table>
<p id="a_form_error"></p>
<div style="text-align: center;"><input type="hidden" name="a_referring_url" value="'.$_POST['referring_url'].'" />
<input type="submit" name="a_submit" value="'.__('Register', 'affiliation-manager').'" /></div>
</form>'; }

else {
$content .= '
<form method="post" action="'.htmlspecialchars($_SERVER['REQUEST_URI']).'" onsubmit="return validate_affiliation_registration_form(this);">
<table style="width: 100%;">
<tr class="login" style="vertical-align: top;"><td><strong><label for="a_login">'.__('Login name', 'affiliation-manager').'</label></strong>*</td>
<td><input type="text" name="a_login" id="a_login" size="30" value="'.$_POST['login'].'" onchange=\'document.getElementById("a_login").value = affiliation_format_nice_name(document.getElementById("a_login").value); $.get("'.AFFILIATION_MANAGER_URL.'?action=check-login",{ login: $("#a_login").val() } ,function(data){ $("#a_login_available").html(data); });\' /> 
<span id="a_login_available">'.(strstr($error, __('login name', 'affiliation-manager')) ? '<span class="error">'.__('Unavailable', 'affiliation-manager').'</span>' : '').'</span><br />
<span class="description">'.__('Letters, numbers, hyphens and underscores only', 'affiliation-manager').'<br />
'.__('Your login name will be included in your affiliate links.', 'affiliation-manager').'</span><br />
<span class="error" id="a_login_error"></span></td></tr>
<tr class="password" style="vertical-align: top;"><td><strong><label for="a_password">'.__('Password', 'affiliation-manager').'</label></strong>*</td>
<td><input type="password" name="a_password" id="a_password" size="30" value="'.$_POST['password'].'" /> <span class="description">'.sprintf(__('at least %d characters', 'affiliation-manager'), $minimum_password_length).'</span><br />
<span class="error" id="a_password_error"></span></td></tr>
<tr class="first-name" style="vertical-align: top;"><td><strong><label for="a_first_name">'.__('First name', 'affiliation-manager').'</label></strong>*</td>
<td><input type="text" name="a_first_name" id="a_first_name" size="30" value="'.$_POST['first_name'].'" /><br />
<span class="error" id="a_first_name_error"></span></td></tr>
<tr class="last-name" style="vertical-align: top;"><td><strong><label for="a_last_name">'.__('Last name', 'affiliation-manager').'</label></strong>*</td>
<td><input type="text" name="a_last_name" id="a_last_name" size="30" value="'.$_POST['last_name'].'" /><br />
<span class="error" id="a_last_name_error"></span></td></tr>
<tr class="email-address" style="vertical-align: top;"><td><strong><label for="a_email_address">'.__('Email address', 'affiliation-manager').'</label></strong>*</td>
<td><input type="text" name="a_email_address" id="a_email_address" size="30" value="'.$_POST['email_address'].'" onchange=\'document.getElementById("a_email_address").value = affiliation_format_email_address(document.getElementById("a_email_address").value);\' /><br />
<span class="error" id="a_email_address_error"></span></td></tr>
<tr class="paypal-email-address" style="vertical-align: top;"><td><strong><label for="a_paypal_email_address">'.__('PayPal email address', 'affiliation-manager').'</label></strong>*</td>
<td><input type="text" name="a_paypal_email_address" id="a_paypal_email_address" size="30" value="'.$_POST['paypal_email_address'].'" onchange=\'document.getElementById("a_paypal_email_address").value = affiliation_format_email_address(document.getElementById("a_paypal_email_address").value);\' /><br />
<span class="error" id="a_paypal_email_address_error"></span></td></tr>
<tr class="website-name" style="vertical-align: top;"><td><strong><label for="a_website_name">'.__('Website name', 'affiliation-manager').'</label></strong></td>
<td><input type="text" name="a_website_name" id="a_website_name" size="30" value="'.$_POST['website_name'].'" /></td></tr>
<tr class="website-url" style="vertical-align: top;"><td><strong><label for="a_website_url">'.__('Website URL', 'affiliation-manager').'</label></strong></td>
<td><input type="text" name="a_website_url" id="a_website_url" size="30" value="'.$_POST['website_url'].'" /></td></tr>
<tr class="address" style="vertical-align: top;"><td><strong><label for="a_address">'.__('Address', 'affiliation-manager').'</label></strong></td>
<td><input type="text" name="a_address" id="a_address" size="30" value="'.$_POST['address'].'" /></td></tr>
<tr class="postcode" style="vertical-align: top;"><td><strong><label for="a_postcode">'.__('Postcode', 'affiliation-manager').'</label></strong></td>
<td><input type="text" name="a_postcode" id="a_postcode" size="30" value="'.$_POST['postcode'].'" /></td></tr>
<tr class="town" style="vertical-align: top;"><td><strong><label for="a_town">'.__('Town', 'affiliation-manager').'</label></strong></td>
<td><input type="text" name="a_town" id="a_town" size="30" value="'.$_POST['town'].'" /></td></tr>
<tr class="country" style="vertical-align: top;"><td><strong><label for="a_country">'.__('Country', 'affiliation-manager').'</label></strong></td>
<td><input type="text" name="a_country" id="a_country" size="30" value="'.$_POST['country'].'" /></td></tr>
<tr class="phone-number" style="vertical-align: top;"><td><strong><label for="a_phone_number">'.__('Phone number', 'affiliation-manager').'</label></strong></td>
<td><input type="text" name="a_phone_number" id="a_phone_number" size="30" value="'.$_POST['phone_number'].'" /></td></tr>
</table>
<p id="a_form_error"></p>
<div style="text-align: center;"><input type="hidden" name="a_referring_url" value="'.$_POST['referring_url'].'" />
<input type="submit" name="a_submit" value="'.__('Register', 'affiliation-manager').'" /></div>
</form>'; }

return $content; } }

add_shortcode('affiliation-registration-form', 'affiliation_registration_form');


function affiliation_registration_form_js() { ?>
<script type="text/javascript">
function validate_affiliation_registration_form(form) {
var error = false;
form.a_login.value = affiliation_format_nice_name(form.a_login.value);
form.a_first_name.value = affiliation_format_name(form.a_first_name.value);
form.a_last_name.value = affiliation_format_name(form.a_last_name.value);
form.a_email_address.value = affiliation_format_email_address(form.a_email_address.value);
form.a_paypal_email_address.value = affiliation_format_email_address(form.a_paypal_email_address.value);
if (<?php $minimum_login_length = affiliation_data('minimum_login_length'); echo $minimum_login_length; ?> > form.a_login.value.length) {
document.getElementById('a_login_error').innerHTML = '<?php sprintf(__('Your login name must contain at least %d characters.', 'affiliation-manager'), $minimum_login_length); ?>';
error = true; }
if (form.a_login.value.length > <?php $maximum_login_length = affiliation_data('maximum_login_length'); echo $maximum_login_length; ?>) {
document.getElementById('a_login_error').innerHTML = '<?php sprintf(__('Your login name must contain at most %d characters.', 'affiliation-manager'), $maximum_login_length); ?>';
error = true; }
if (form.a_login.value == '') {
document.getElementById('a_login_error').innerHTML = '<?php _e('This field is required.', 'affiliation-manager'); ?>';
error = true; }
if (<?php $minimum_password_length = affiliation_data('minimum_password_length'); echo $minimum_password_length; ?> > form.a_password.value.length) {
document.getElementById('a_password_error').innerHTML = '<?php printf(__('Your password must contain at least %d characters.', 'affiliation-manager'), $minimum_password_length); ?>';
error = true; }
if (form.a_password.value.length > <?php $maximum_password_length = affiliation_data('maximum_password_length'); echo $maximum_password_length; ?>) {
document.getElementById('a_password_error').innerHTML = '<?php printf(__('Your password must contain at most %d characters.', 'affiliation-manager'), $maximum_password_length); ?>';
error = true; }
if (form.a_password.value == '') {
document.getElementById('a_password_error').innerHTML = '<?php _e('This field is required.', 'affiliation-manager'); ?>';
error = true; }
if (form.a_first_name.value == '') {
document.getElementById('a_first_name_error').innerHTML = '<?php _e('This field is required.', 'affiliation-manager'); ?>';
error = true; }
if (form.a_last_name.value == '') {
document.getElementById('a_last_name_error').innerHTML = '<?php _e('This field is required.', 'affiliation-manager'); ?>';
error = true; }
if ((form.a_email_address.value.indexOf('@') == -1) || (form.a_email_address.value.indexOf('.') == -1)) {
document.getElementById('a_email_address_error').innerHTML = '<?php _e('This email address appears to be invalid.', 'affiliation-manager'); ?>';
error = true; }
if (form.a_email_address.value == '') {
document.getElementById('a_email_address_error').innerHTML = '<?php _e('This field is required.', 'affiliation-manager'); ?>';
error = true; }
if ((form.a_paypal_email_address.value.indexOf('@') == -1) || (form.a_paypal_email_address.value.indexOf('.') == -1)) {
document.getElementById('a_paypal_email_address_error').innerHTML = '<?php _e('This email address appears to be invalid.', 'affiliation-manager'); ?>';
error = true; }
if (form.a_paypal_email_address.value == '') {
document.getElementById('a_paypal_email_address_error').innerHTML = '<?php _e('This field is required.', 'affiliation-manager'); ?>';
error = true; }
if (error) { document.getElementById('a_form_error').innerHTML = '<?php _e('An error has occurred. Please check the fields and resubmit the form.', 'affiliation-manager'); ?>'; }
return !error; }
</script>
<script type="text/javascript" src="<?php echo AFFILIATION_MANAGER_URL; ?>libraries/jquery-1.5.1.min.js"></script>
<?php }


function affiliation_session() {
session_start();
if ((isset($_COOKIE['a_login'])) && (!isset($_SESSION['a_login']))) {
$login = substr($_COOKIE['a_login'], 0, -64);
if (substr($_COOKIE['a_login'], -64) == hash('sha256', $login.AUTH_KEY)) { $_SESSION['a_login'] = $login; } }	
if (isset($_SESSION['a_login'])) { return true; } else { return false; } }


function affiliation_statistics_between($start_date, $end_date) {
global $commerce_manager_options, $wpdb;
$start_date = trim(mysql_real_escape_string(strip_tags($start_date)));
if (strlen($start_date) == 10) { $start_date .= ' 00:00:00'; }
$end_date = trim(mysql_real_escape_string(strip_tags($end_date)));
if (strlen($end_date) == 10) { $end_date .= ' 23:59:59'; }
$query = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."affiliation_manager_clicks WHERE referrer = '".$_SESSION['a_login']."' AND (date BETWEEN '$start_date' AND '$end_date')", OBJECT);
$clicks_number = (int) $query->total;
$row = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE referrer = '".$_SESSION['a_login']."' AND (date BETWEEN '$start_date' AND '$end_date')", OBJECT);
$commissions_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE referrer = '".$_SESSION['a_login']."' AND (date BETWEEN '$start_date' AND '$end_date')", OBJECT);
$recurring_commissions_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM ".$wpdb->prefix."optin_manager_prospects WHERE referrer = '".$_SESSION['a_login']."' AND (date BETWEEN '$start_date' AND '$end_date')", OBJECT);
$prospects_commissions_total_amount = round(100*$row->total)/100;
$commissions_total_amount = $commissions_total_amount + $recurring_commissions_total_amount + $prospects_commissions_total_amount;
$row = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE referrer = '".$_SESSION['a_login']."' AND commission_status = 'paid' AND (date BETWEEN '$start_date' AND '$end_date')", OBJECT);
$paid_commissions_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE referrer = '".$_SESSION['a_login']."' AND commission_status = 'paid' AND (date BETWEEN '$start_date' AND '$end_date')", OBJECT);
$paid_recurring_commissions_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM ".$wpdb->prefix."optin_manager_prospects WHERE referrer = '".$_SESSION['a_login']."' AND commission_status = 'paid' AND (date BETWEEN '$start_date' AND '$end_date')", OBJECT);
$paid_prospects_commissions_total_amount = round(100*$row->total)/100;
$paid_commissions_total_amount = $paid_commissions_total_amount + $paid_recurring_commissions_total_amount + $paid_prospects_commissions_total_amount;
$unpaid_commissions_total_amount = round(100*($commissions_total_amount - $paid_commissions_total_amount))/100;
$row = $wpdb->get_row("SELECT SUM(commission2_amount) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE referrer2 = '".$_SESSION['a_login']."' AND (date BETWEEN '$start_date' AND '$end_date')", OBJECT);
$commissions2_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(commission2_amount) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE referrer2 = '".$_SESSION['a_login']."' AND (date BETWEEN '$start_date' AND '$end_date')", OBJECT);
$recurring_commissions2_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(commission2_amount) AS total FROM ".$wpdb->prefix."optin_manager_prospects WHERE referrer2 = '".$_SESSION['a_login']."' AND (date BETWEEN '$start_date' AND '$end_date')", OBJECT);
$prospects_commissions2_total_amount = round(100*$row->total)/100;
$commissions2_total_amount = $commissions2_total_amount + $recurring_commissions2_total_amount + $prospects_commissions2_total_amount;
$row = $wpdb->get_row("SELECT SUM(commission2_amount) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE referrer2 = '".$_SESSION['a_login']."' AND commission2_status = 'paid' AND (date BETWEEN '$start_date' AND '$end_date')", OBJECT);
$paid_commissions2_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(commission2_amount) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE referrer2 = '".$_SESSION['a_login']."' AND commission2_status = 'paid' AND (date BETWEEN '$start_date' AND '$end_date')", OBJECT);
$paid_recurring_commissions2_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(commission2_amount) AS total FROM ".$wpdb->prefix."optin_manager_prospects WHERE referrer2 = '".$_SESSION['a_login']."' AND commission2_status = 'paid' AND (date BETWEEN '$start_date' AND '$end_date')", OBJECT);
$paid_prospects_commissions2_total_amount = round(100*$row->total)/100;
$paid_commissions2_total_amount = $paid_commissions2_total_amount + $paid_recurring_commissions2_total_amount + $paid_prospects_commissions2_total_amount;
$unpaid_commissions2_total_amount = round(100*($commissions2_total_amount - $paid_commissions2_total_amount))/100;
$currency_code = do_shortcode($commerce_manager_options['currency_code']);
return '
<table style="width: 100%;"><tbody>
<tr style="vertical-align: top;"><td><strong>'.__('Number of clicks', 'affiliation-manager').'</strong></td>
<td>'.$clicks_number.'</td></tr>
<tr style="vertical-align: top;"><td><strong>'.__('Commissions total amount', 'affiliation-manager').' '.__('(level 1)', 'affiliation-manager').'</strong></td>
<td>'.$commissions_total_amount.' '.$currency_code.'</td></tr>
<tr style="vertical-align: top;"><td><strong>'.__('Paid commissions total amount', 'affiliation-manager').' '.__('(level 1)', 'affiliation-manager').'</strong></td>
<td>'.$paid_commissions_total_amount.' '.$currency_code.'</td></tr>
<tr style="vertical-align: top;"><td><strong>'.__('Unpaid commissions total amount', 'affiliation-manager').' '.__('(level 1)', 'affiliation-manager').'</strong></td>
<td>'.$unpaid_commissions_total_amount.' '.$currency_code.'</td></tr>
<tr style="vertical-align: top;"><td><strong>'.__('Commissions total amount', 'affiliation-manager').' '.__('(level 2)', 'affiliation-manager').'</strong></td>
<td>'.$commissions2_total_amount.' '.$currency_code.'</td></tr>
<tr style="vertical-align: top;"><td><strong>'.__('Paid commissions total amount', 'affiliation-manager').' '.__('(level 2)', 'affiliation-manager').'</strong></td>
<td>'.$paid_commissions2_total_amount.' '.$currency_code.'</td></tr>
<tr style="vertical-align: top;"><td><strong>'.__('Unpaid commissions total amount', 'affiliation-manager').' '.__('(level 2)', 'affiliation-manager').'</strong></td>
<td>'.$unpaid_commissions2_total_amount.' '.$currency_code.'</td></tr>
</tbody></table>'; }


function affiliation_statistics_form() {
if (affiliation_session()) {
add_action('wp_footer', 'affiliation_date_picker_js');
foreach ($_POST as $key => $value) { if (substr($key, 0, 2) == 'a_') { $_POST[substr($key, 2)] = $value; } }
if (isset($_POST['submit'])) { $start_date = $_POST['start_date']; $end_date = $_POST['end_date']; }
else { $end_date = date('Y-m-d'); $start_date = substr($end_date, 0, 8).'01'; }
$start_date = trim(mysql_real_escape_string(strip_tags($start_date)));
if (strlen($start_date) == 10) { $start_date .= ' 00:00:00'; }
$end_date = trim(mysql_real_escape_string(strip_tags($end_date)));
if (strlen($end_date) == 10) { $end_date .= ' 23:59:59'; }
return '
<form style="text-align: center;" method="post" action="#statistics">
<p><label style="margin-left: 3em;"><strong>'.__('Start', 'affiliation-manager').'</strong>
<input class="date-pick" style="margin: 0.5em;" type="text" name="start_date" id="start_date" size="10" value="'.$start_date.'" /></label>
<label style="margin-left: 3em;"><strong>'.__('End', 'affiliation-manager').'</strong>
<input class="date-pick" style="margin: 0.5em;" type="text" name="end_date" id="end_date" size="10" value="'.$end_date.'" /></label></p>
<div><input type="submit" name="a_submit" value="'.__('Display', 'affiliation-manager').'" /></div>
</form>'; } }

add_shortcode('affiliation-statistics-form', 'affiliation_statistics_form');


function affiliation_string_map($function, $string) {
if (!function_exists($function)) { $function = 'affiliation_'.$function; }
if (function_exists($function)) { $array = array_map($function, array($string)); $string = $array[0]; }
return $string; }


function affiliation_strip_accents($string) {
return str_replace(
explode(' ', 'á à â ä ã å ç é è ê ë í ì î ï ñ ó ò ô ö õ ø ú ù û ü ý ÿ Á À Â Ä Ã Å Ç É È Ê Ë Í Ì Î Ï Ñ Ó Ò Ô Ö Õ Ø Ú Ù Û Ü Ý Ÿ'),
explode(' ', 'a a a a a a c e e e e i i i i n o o o o o o u u u u y y A A A A A A C E E E E I I I I N O O O O O O U U U U Y Y'),
$string); }


function affiliation_strip_accents_js() { ?>
<script type="text/javascript">
function affiliation_strip_accents(string) {
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


function award_commission() {
global $wpdb;
if (isset($_GET['recurring_payments_profile_number'])) { $price = $_GET['price']; $_GET['quantity'] = 1; }
else { $price = product_data('price'); }
$_GET['affiliation_enabled'] = product_data('affiliation_enabled');
$_GET['commission_payment'] = product_data('commission_payment');
if (($_GET['affiliation_enabled'] == 'no') || ((strstr($_GET['referrer'], '@')) && ($_GET['commission_payment'] == 'deferred'))) { $_GET['sale_winner'] = 'affiliator'; $_GET['commission_amount'] = 0; }
else {
if (!strstr($_GET['referrer'], '@')) { $_GET['affiliate_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_GET['referrer']."'", OBJECT); }
if ($_GET['affiliate_data']['status'] == 'inactive') { $_GET['sale_winner'] = 'affiliator'; $_GET['commission_amount'] = 0; }
else {
$fields = array(
'commission_amount',
'commission_percentage',
'commission_type',
'first_sale_winner',
'registration_required');
foreach ($fields as $field) { $_GET[$field] = product_data($field); }
if ($_GET['commission_payment'] == 'deferred') {
$_GET['sale_winner'] = 'affiliator';
if ($_GET['commission_type'] == 'constant') { $_GET['commission_amount'] = $_GET['quantity']*$_GET['commission_amount']; }
elseif ($_GET['commission_type'] == 'proportional') { $_GET['commission_amount'] = round($_GET['quantity']*$_GET['commission_percentage']*$price)/100; } }
else {
if ((strstr($_GET['referrer'], '@')) && ($_GET['registration_required'] == 'yes')) { $_GET['sale_winner'] = 'affiliator'; }
else {
if ($_GET['commission_type'] == 'constant') { $_GET['commission_percentage'] = 100*($_GET['commission_amount'])/$price; }
if ($_GET['commission_percentage'] == 0) { $_GET['sale_winner'] = 'affiliator'; }
if ($_GET['commission_percentage'] > 0) {
$row = $wpdb->get_row("SELECT SUM(price) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE product_id = '".$_GET['product_id']."' AND status != 'refunded' AND referrer = '".$_GET['referrer']."'", OBJECT);
$total_price = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(price) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE product_id = '".$_GET['product_id']."' AND status != 'refunded' AND referrer = '".$_GET['referrer']."'", OBJECT);
$total_price = $total_price + round(100*$row->total)/100;
if ($total_price == 0) {
if ($_GET['first_sale_winner'] == 'affiliate') { $_GET['sale_winner'] = 'affiliate'; }
else { $_GET['sale_winner'] = 'affiliator'; } }
if ($total_price > 0) {
$row = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE product_id = '".$_GET['product_id']."' AND referrer = '".$_GET['referrer']."'", OBJECT);
$commissions_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE product_id = '".$_GET['product_id']."' AND referrer = '".$_GET['referrer']."'", OBJECT);
$commissions_total_amount = $commissions_total_amount + round(100*$row->total)/100;
if ($_GET['first_sale_winner'] == 'affiliate') {
if ($_GET['commission_percentage'] >= 100*$commissions_total_amount/$total_price) { $_GET['sale_winner'] = 'affiliate'; }
else { $_GET['sale_winner'] = 'affiliator'; } }
if ($_GET['first_sale_winner'] == 'affiliator') {
if ($_GET['commission_percentage'] > 100*$commissions_total_amount/$total_price) { $_GET['sale_winner'] = 'affiliate'; }
else { $_GET['sale_winner'] = 'affiliator'; } } } } }
if ($_GET['sale_winner'] == 'affiliator') { $_GET['commission_amount'] = 0; } } } } }


function award_commission2() {
global $wpdb;
if (($_GET['referrer'] != '') && (!strstr($_GET['referrer'], '@'))) {
if (isset($_GET['recurring_payments_profile_number'])) { $price = $_GET['price']; $_GET['quantity'] = 1; }
else { $price = product_data('price'); }
$_GET['affiliation_enabled'] = product_data('affiliation_enabled');
$original_referrer = $_GET['referrer'];
$_GET['affiliate_data'] = (array) $_GET['affiliate_data'];
if (isset($_GET['affiliate_data'])) { $original_affiliate_data = $_GET['affiliate_data']; }
$result = $wpdb->get_row("SELECT referrer FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_GET['referrer']."' AND status = 'active'", OBJECT);
if ($result) { $_GET['referrer'] = $result->referrer; $_GET['referrer2'] = $_GET['referrer']; }
if (($_GET['affiliation_enabled'] == 'yes') && ($_GET['referrer2'] != '')) {
$_GET['affiliate_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_GET['referrer2']."'", OBJECT);
if ($_GET['affiliate_data']['status'] == 'inactive') { $_POST['commission2_amount'] = 0; }
else {
$fields = array(
'commission2_amount',
'commission2_enabled',
'commission2_percentage',
'commission2_type');
foreach ($fields as $field) { $_GET[$field] = product_data($field); }
if ($_GET['commission2_enabled'] == 'no') { $_GET['commission2_amount'] = 0; }
else {
if ($_GET['commission2_type'] == 'constant') { $_GET['commission2_amount'] = $_GET['quantity']*$_GET['commission2_amount']; }
elseif ($_GET['commission2_type'] == 'proportional') { $_GET['commission2_amount'] = round($_GET['quantity']*$_GET['commission2_percentage']*$price)/100; }
if ($_GET['commission2_amount'] > 0) { $_GET['commission2_status'] = 'unpaid'; } } } }
$_GET['referrer'] = $original_referrer;
if (isset($original_affiliate_data)) { $_GET['affiliate_data'] = $original_affiliate_data; } } }


function award_prospect_commission() {
global $wpdb;
$_GET['affiliation_enabled'] = optin_form_data('affiliation_enabled');
if (($_GET['affiliation_enabled'] == 'no') || (strstr($_GET['referrer'], '@'))) { $_POST['commission_amount'] = 0; }
else {
if (!strstr($_GET['referrer'], '@')) { $_GET['affiliate_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_GET['referrer']."'", OBJECT); }
if ($_GET['affiliate_data']['status'] == 'inactive') { $_POST['commission_amount'] = 0; }
else { $_POST['commission_amount'] = optin_form_data('commission_amount'); } }
$_POST['referrer'] = $_GET['referrer'];
if ($_POST['commission_amount'] > 0) { $_POST['commission_status'] = 'unpaid'; } }


function award_prospect_commission2() {
global $wpdb;
$_GET['affiliation_enabled'] = optin_form_data('affiliation_enabled');
if (($_GET['affiliation_enabled'] == 'yes') || ($_GET['referrer'] != '') && (!strstr($_GET['referrer'], '@'))) {
$original_referrer = $_GET['referrer'];
$_GET['affiliate_data'] = (array) $_GET['affiliate_data'];
if (isset($_GET['affiliate_data'])) { $original_affiliate_data = $_GET['affiliate_data']; }
$result = $wpdb->get_row("SELECT referrer FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_GET['referrer']."' AND status = 'active'", OBJECT);
if ($result) { $_GET['referrer'] = $result->referrer; $_GET['referrer2'] = $_GET['referrer']; }
if ($_GET['referrer2'] != '') {
$_GET['affiliate_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_GET['referrer2']."'", OBJECT);
if ($_GET['affiliate_data']['status'] == 'inactive') { $_POST['commission2_amount'] = 0; }
else {
$fields = array(
'commission2_amount',
'commission2_enabled');
foreach ($fields as $field) { $_POST[$field] = optin_form_data($field); }
if ($_POST['commission2_enabled'] == 'no') { $_POST['commission2_amount'] = 0; } } }
$_POST['referrer2'] = $_GET['referrer2'];
if ($_POST['commission2_amount'] > 0) { $_POST['commission2_status'] = 'unpaid'; }
$_GET['referrer'] = $original_referrer;
if (isset($original_affiliate_data)) { $_GET['affiliate_data'] = $original_affiliate_data; } } }


function click_data($atts) {
global $wpdb;
$_GET['click_data'] = (array) $_GET['click_data'];
if ((isset($_GET['click_id'])) && ($_GET['click_data']['id'] != $_GET['click_id'])) {
$_GET['click_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_clicks WHERE id = '".$_GET['click_id']."'", OBJECT); }
$click_data = $_GET['click_data'];
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $id = 0; $part = 0; }
else {
$field = $atts[0];
$default = $atts['default'];
$filter = $atts['filter'];
$id = (int) do_shortcode(str_replace(array('(', ')'), array('[', ']'), $atts['id']));
$part = (int) $atts['part']; }
$field = str_replace('-', '_', affiliation_format_nice_name($field));
if ($field == '') { $field = 'referrer'; }
if (($id == 0) || ($id == $click_data['id'])) { $data = $click_data[$field]; }
else {
if (isset($_GET['click_id'])) { $original_click_id = $_GET['click_id']; }
if (isset($_GET['click_data'])) { $original_click_data = $_GET['click_data']; }
if ($_GET['click'.$id.'_data']['id'] != $id) {
$_GET['click'.$id.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_clicks WHERE id = '$id'", OBJECT); }
$click_data = $_GET['click'.$id.'_data'];
$_GET['click_id'] = $id; $_GET['click_data'] = $click_data;
$data = $click_data[$field]; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = affiliation_format_data($field, $data);
$data = affiliation_filter_data($filter, $data);
if (isset($original_click_id)) { $_GET['click_id'] = $original_click_id; }
if (isset($original_click_data)) { $_GET['click_data'] = $original_click_data; }
return $data; }

add_shortcode('click', 'click_data');


function commission_data($atts) {
global $wpdb;
$_GET['commission_data'] = (array) $_GET['commission_data'];
if ((isset($_GET['commission_id'])) && ($_GET['commission_data']['id'] != $_GET['commission_id'])) {
$_GET['commission_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_orders WHERE id = '".$_GET['commission_id']."'", OBJECT); }
$commission_data = $_GET['commission_data'];
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $id = 0; $part = 0; }
else {
$field = $atts[0];
$default = $atts['default'];
$filter = $atts['filter'];
$id = (int) do_shortcode(str_replace(array('(', ')'), array('[', ']'), $atts['id']));
$part = (int) $atts['part']; }
$field = str_replace('-', '_', affiliation_format_nice_name($field));
if ($field == '') { $field = 'referrer'; }
if (($id == 0) || ($id == $commission_data['id'])) { $data = $commission_data[$field]; }
else {
if (isset($_GET['commission_id'])) { $original_commission_id = $_GET['commission_id']; }
if (isset($_GET['commission_data'])) { $original_commission_data = $_GET['commission_data']; }
if ($_GET['commission'.$id.'_data']['id'] != $id) {
$_GET['commission'.$id.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_orders WHERE id = '$id'", OBJECT); }
$commission_data = $_GET['commission'.$id.'_data'];
$_GET['commission_id'] = $id; $_GET['commission_data'] = $commission_data;
$data = $commission_data[$field]; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = affiliation_format_data($field, $data);
$data = affiliation_filter_data($filter, $data);
if (isset($original_commission_id)) { $_GET['commission_id'] = $original_commission_id; }
if (isset($original_commission_data)) { $_GET['commission_data'] = $original_commission_data; }
return $data; }

add_shortcode('commission', 'commission_data');


function prospect_commission_data($atts) {
global $wpdb;
$_GET['prospect_commission_data'] = (array) $_GET['prospect_commission_data'];
if ((isset($_GET['prospect_commission_id'])) && ($_GET['prospect_commission_data']['id'] != $_GET['prospect_commission_id'])) {
$_GET['prospect_commission_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_prospects WHERE id = '".$_GET['prospect_commission_id']."'", OBJECT); }
$prospect_commission_data = $_GET['prospect_commission_data'];
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $id = 0; $part = 0; }
else {
$field = $atts[0];
$default = $atts['default'];
$filter = $atts['filter'];
$id = (int) do_shortcode(str_replace(array('(', ')'), array('[', ']'), $atts['id']));
$part = (int) $atts['part']; }
$field = str_replace('-', '_', affiliation_format_nice_name($field));
if ($field == '') { $field = 'referrer'; }
if (($id == 0) || ($id == $prospect_commission_data['id'])) { $data = $prospect_commission_data[$field]; }
else {
if (isset($_GET['prospect_commission_id'])) { $original_prospect_commission_id = $_GET['prospect_commission_id']; }
if (isset($_GET['prospect_commission_data'])) { $original_prospect_commission_data = $_GET['prospect_commission_data']; }
if ($_GET['prospect_commission'.$id.'_data']['id'] != $id) {
$_GET['prospect_commission'.$id.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_prospects WHERE id = '$id'", OBJECT); }
$prospect_commission_data = $_GET['prospect_commission'.$id.'_data'];
$_GET['prospect_commission_id'] = $id; $_GET['prospect_commission_data'] = $prospect_commission_data;
$data = $prospect_commission_data[$field]; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = affiliation_format_data($field, $data);
$data = affiliation_filter_data($filter, $data);
if (isset($original_prospect_commission_id)) { $_GET['prospect_commission_id'] = $original_prospect_commission_id; }
if (isset($original_prospect_commission_data)) { $_GET['prospect_commission_data'] = $original_prospect_commission_data; }
return $data; }

add_shortcode('prospect-commission', 'prospect_commission_data');


function recurring_commission_data($atts) {
global $wpdb;
$_GET['recurring_commission_data'] = (array) $_GET['recurring_commission_data'];
if ((isset($_GET['recurring_commission_id'])) && ($_GET['recurring_commission_data']['id'] != $_GET['recurring_commission_id'])) {
$_GET['recurring_commission_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE id = '".$_GET['recurring_commission_id']."'", OBJECT); }
$recurring_commission_data = $_GET['recurring_commission_data'];
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $id = 0; $part = 0; }
else {
$field = $atts[0];
$default = $atts['default'];
$filter = $atts['filter'];
$id = (int) do_shortcode(str_replace(array('(', ')'), array('[', ']'), $atts['id']));
$part = (int) $atts['part']; }
$field = str_replace('-', '_', affiliation_format_nice_name($field));
if ($field == '') { $field = 'referrer'; }
if (($id == 0) || ($id == $recurring_commission_data['id'])) { $data = $recurring_commission_data[$field]; }
else {
if (isset($_GET['recurring_commission_id'])) { $original_recurring_commission_id = $_GET['recurring_commission_id']; }
if (isset($_GET['recurring_commission_data'])) { $original_recurring_commission_data = $_GET['recurring_commission_data']; }
if ($_GET['recurring_commission'.$id.'_data']['id'] != $id) {
$_GET['recurring_commission'.$id.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE id = '$id'", OBJECT); }
$recurring_commission_data = $_GET['recurring_commission'.$id.'_data'];
$_GET['recurring_commission_id'] = $id; $_GET['recurring_commission_data'] = $recurring_commission_data;
$data = $recurring_commission_data[$field]; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = affiliation_format_data($field, $data);
$data = affiliation_filter_data($filter, $data);
if (isset($original_recurring_commission_id)) { $_GET['recurring_commission_id'] = $original_recurring_commission_id; }
if (isset($original_recurring_commission_data)) { $_GET['recurring_commission_data'] = $original_recurring_commission_data; }
return $data; }

add_shortcode('recurring-commission', 'recurring_commission_data');


function referrer_affiliate_data($atts) {
global $wpdb;
$_GET['referrer_data'] = (array) $_GET['referrer_data'];
if ((isset($_GET['referrer'])) && (!strstr($_GET['referrer'], '@')) && ($_GET['referrer_data']['login'] != $_GET['referrer'])) {
$_GET['referrer_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_GET['referrer']."'", OBJECT); }
$referrer_data = $_GET['referrer_data'];
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $part = 0; }
else {
$field = $atts[0];
$default = $atts['default'];
unset($atts['default']);
$filter = $atts['filter'];
unset($atts['filter']);
$part = (int) $atts['part']; }
$field = str_replace('-', '_', affiliation_format_nice_name($field));
if ($field == '') { $field = 'login'; }
$data = $referrer_data[$field];
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
$data = (string) $data;
if ($data != '') { $data = affiliation_format_data($field, $data); }
$data = (string) $data;
if (($data == '') && ($referrer_data['category_id'] > 0)) {
if (is_string($atts)) { $atts = array($field); }
$atts['category'] = $referrer_data['category_id'];
$data = affiliate_category_data($atts); }
elseif ($data == '') { $data = affiliation_data($atts); }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = affiliation_format_data($field, $data);
$data = affiliation_filter_data($filter, $data);
return $data; }

add_shortcode('referrer-affiliate', 'referrer_affiliate_data');


function referrer_counter($atts, $content) {
global $wpdb;
$_GET['affiliate_data'] = (array) $_GET['affiliate_data'];
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
extract(shortcode_atts(array('data' => '', 'limit' => '', 'range' => '', 'referrer' => ''), $atts));
if ($referrer == '') {
if ((affiliation_session()) || (is_admin())) {
$referrer = $_GET['affiliate_data']['login'];
if ($referrer == '') { $referrer = $_SESSION['a_login']; } }
else { $referrer = $_GET['referrer']; } }
elseif (strstr($referrer, '@')) { $referrer = affiliation_format_email_address($referrer); }
else { $referrer = affiliation_format_nice_name($referrer); }

if ($referrer == '') { $data = 0; }
else {
$data = str_replace('_', '-', affiliation_format_nice_name($data));
switch ($data) {
case 'amount': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments'); $field = 'amount'; break;
case 'affiliates': $table = $wpdb->prefix.'affiliation_manager_affiliates'; $field = ''; break;
case 'clicks': $table = $wpdb->prefix.'affiliation_manager_clicks'; $field = ''; break;
case 'commission-amount': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments', $wpdb->prefix.'optin_manager_prospects'); $field = 'commission_amount'; break;
case 'commission2-amount': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments', $wpdb->prefix.'optin_manager_prospects'); $field = 'commission2_amount'; break;
case 'orders': $table = $wpdb->prefix.'commerce_manager_orders'; $field = ''; break;
case 'orders-amount': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'amount'; break;
case 'orders-commission-amount': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'commission_amount'; break;
case 'orders-commission2-amount': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'commission2_amount'; break;
case 'orders-price': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'price'; break;
case 'orders-quantity': case 'sales': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'quantity'; break;
case 'price': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments'); $field = 'price'; break;
case 'prospects': $table = $wpdb->prefix.'optin_manager_prospects'; $field = ''; break;
case 'prospects-commission-amount': $table = $wpdb->prefix.'optin_manager_prospects'; $field = 'commission_amount'; break;
case 'prospects-commission2-amount': $table = $wpdb->prefix.'optin_manager_prospects'; $field = 'commission2_amount'; break;
case 'recurring-payments-amount': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'amount'; break;
case 'recurring-payments-commission-amount': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'commission_amount'; break;
case 'recurring-payments-commission2-amount': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'commission2_amount'; break;
case 'recurring-payments-price': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'price'; break;
default: $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments'); $field = 'amount'; }
if ($field == 'commission2_amount') { $referrer_field = 'referrer2'; } else { $referrer_field = 'referrer'; }

$range = str_replace('_', '-', affiliation_format_nice_name($range));
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
$row = $wpdb->get_row("SELECT count(*) as total FROM $table WHERE $referrer_field = '$referrer' $date_criteria", OBJECT);
$data = (int) $row->total; }
else {
$row = $wpdb->get_row("SELECT SUM($field) AS total FROM $table WHERE $referrer_field = '$referrer' $date_criteria", OBJECT);
$data = round(100*$row->total)/100; } }

else {
$data = 0; foreach ($table as $table_name) {
$row = $wpdb->get_row("SELECT SUM($field) AS total FROM $table_name WHERE $referrer_field = '$referrer' $date_criteria", OBJECT);
$data = $data + round(100*$row->total)/100; } } }

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

add_shortcode('referrer-counter', 'referrer_counter');
add_shortcode('referrer-data-counter', 'referrer_counter');


add_filter('get_the_excerpt', 'do_shortcode');
add_filter('get_the_title', 'do_shortcode');
add_filter('single_post_title', 'do_shortcode');
add_filter('the_excerpt', 'do_shortcode');
add_filter('the_excerpt_rss', 'do_shortcode');
add_filter('the_title', 'do_shortcode');
add_filter('the_title_attribute', 'do_shortcode');
add_filter('the_title_rss', 'do_shortcode');
add_filter('widget_text', 'do_shortcode');