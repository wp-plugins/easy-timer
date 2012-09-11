<?php $file = 'wp-load.php'; $i = 0;
while ((!file_exists($file)) && ($i < 8)) { $file = '../'.$file; $i = $i + 1; }
include_once $file;

switch ($_GET['action']) {
case 'activate':
global $wpdb;
if ($_GET['key'] == hash('sha256', $_GET['id'].membership_data('encrypted_urls_key'))) {
$_GET['member_id'] = $_GET['id'];
$_GET['member_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members WHERE id = ".$_GET['id'], OBJECT);
if ($_GET['member_data']['status'] != 'active') {
$_GET['member_data']['status'] = 'active';
$results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_members SET status = 'active' WHERE id = ".$_GET['id']);
$members_areas = array_unique(preg_split('#[^0-9]#', $_GET['member_data']['members_areas'], 0, PREG_SPLIT_NO_EMPTY));
if (count($members_areas) == 1) { $_GET['member_area_id'] = (int) $members_areas[0]; } else { unset($_GET['member_area_id']); }
if ((!defined('MEMBERSHIP_MANAGER_DEMO')) || (MEMBERSHIP_MANAGER_DEMO == false)) {
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = member_area_data('activation_confirmation_email_'.$field); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); }
if (membership_data('activation_custom_instructions_executed') == 'yes') {
eval(format_instructions(membership_data('activation_custom_instructions'))); } }
if (!headers_sent()) { header('Location: '.member_area_data('activation_confirmation_url')); exit(); } }
elseif (!headers_sent()) { header('Location: '.HOME_URL); exit; } }
elseif (!headers_sent()) { header('Location: '.HOME_URL); exit; } break;
case 'check-login':
global $wpdb;
$login = format_email_address($_GET['login']);
if (($login == '') || (is_numeric($login))) { $key = 'unavailable'; }
elseif ($login == $_SESSION['membership_login']) { $key = 'available'; }
else {
$result = $wpdb->get_results("SELECT login FROM ".$wpdb->prefix."membership_manager_members WHERE login = '$login'", OBJECT);
if ($result) { $key = 'unavailable'; } else { $key = 'available'; } }
$options = (array) get_option('membership_manager'.$_GET['form_id'].'_form');
$key .= '_login_indicator_message';
if ($options[$key] == '') { $message = membership_data($key); }
else { $message = quotes_entities_decode(do_shortcode($options[$key])); }
echo $message; break;
case 'logout': membership_logout(); if (!headers_sent()) { header('Location: '.HOME_URL); exit; } break;
default: if (isset($_GET['url'])) {
$url = membership_decrypt_url($_SERVER['REQUEST_URI']);
if (!headers_sent()) { header('Location: '.$url); exit(); } }
else { if (!headers_sent()) { header('Location: '.HOME_URL); exit(); } } }