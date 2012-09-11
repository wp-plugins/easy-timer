<?php $file = 'wp-load.php'; $i = 0;
while ((!file_exists($file)) && ($i < 8)) { $file = '../'.$file; $i = $i + 1; }
include_once $file;

switch ($_GET['action']) {
case 'activate':
global $wpdb;
if ($_GET['key'] == hash('sha256', $_GET['id'].affiliation_data('encrypted_urls_key'))) {
$_GET['affiliate_id'] = $_GET['id'];
$_GET['affiliate_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE id = ".$_GET['id'], OBJECT);
if ($_GET['affiliate_data']['status'] != 'active') {
$_GET['affiliate_data']['status'] = 'active';
$results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_affiliates SET status = 'active' WHERE id = ".$_GET['id']);
if ((!defined('AFFILIATION_MANAGER_DEMO')) || (AFFILIATION_MANAGER_DEMO == false)) {
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = affiliation_data('activation_confirmation_email_'.$field); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); }
if (affiliation_data('activation_custom_instructions_executed') == 'yes') {
eval(format_instructions(affiliation_data('activation_custom_instructions'))); } }
if (!headers_sent()) { header('Location: '.affiliation_data('activation_confirmation_url')); exit(); } }
elseif (!headers_sent()) { header('Location: '.HOME_URL); exit; } }
elseif (!headers_sent()) { header('Location: '.HOME_URL); exit; } break;
case 'check-login':
global $wpdb;
$login = format_nice_name($_GET['login']);
if (($login == '') || (is_numeric($login))) { $key = 'unavailable'; }
elseif ($login == $_SESSION['affiliation_login']) { $key = 'available'; }
else {
$result = $wpdb->get_results("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '$login'", OBJECT);
if ($result) { $key = 'unavailable'; } else { $key = 'available'; } }
$options = (array) get_option('affiliation_manager'.$_GET['form_id'].'_form');
$key .= '_login_indicator_message';
if ($options[$key] == '') { $message = affiliation_data($key); }
else { $message = quotes_entities_decode(do_shortcode($options[$key])); }
echo $message; break;
case 'logout': affiliation_logout(); if (!headers_sent()) { header('Location: '.HOME_URL); exit; } break;
default: if (isset($_GET['url'])) {
$url = affiliation_decrypt_url($_SERVER['REQUEST_URI']);
if (!headers_sent()) { header('Location: '.$url); exit(); } }
else { if (!headers_sent()) { header('Location: '.HOME_URL); exit(); } } }