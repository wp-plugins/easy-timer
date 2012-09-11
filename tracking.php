<?php $referrer = $_GET[$a];
if (is_numeric($referrer)) {
$referrer = preg_replace('/[^0-9]/', '', $referrer);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE id = $referrer AND status = 'active'", OBJECT);
if ($result) { $referrer = $result->login; } else { $referrer = ''; } }
elseif (strstr($referrer, '@')) {
$referrer = format_email_address($referrer);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE paypal_email_address = '$referrer' AND status = 'active'", OBJECT);
if ($result) { $referrer = $result->login; }
else { $result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '$referrer' AND status = 'active'", OBJECT);
if ($result) { $referrer = $result->login; } } }
else {
$referrer = format_nice_name($referrer);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '$referrer' AND status = 'active'", OBJECT);
if (!$result) { $referrer = ''; } }
if ($referrer != '') {
if (($_COOKIE[AFFILIATION_COOKIES_NAME] == '') || (affiliation_data('winner_affiliate') == 'last')) {
setcookie(AFFILIATION_COOKIES_NAME, $referrer, time() + 86400*affiliation_data('cookies_lifetime'), '/'); }
if (affiliation_data('clicks_registration_enabled') == 'yes') {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$click = array(
'referrer' => $referrer,
'url' => $_SERVER['REQUEST_URI'],
'ip_address' => $_SERVER['REMOTE_ADDR'],
'user_agent' => $_SERVER['HTTP_USER_AGENT'],
'referring_url' => $_SERVER['HTTP_REFERER'],
'date' => date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET),
'date_utc' => date('Y-m-d H:i:s'));
foreach ($click as $key => $value) { $keys_list .= $key.","; $values_list .= "'".$value."',"; }
$results = $wpdb->query("INSERT INTO ".$wpdb->prefix."affiliation_manager_clicks (".substr($keys_list, 0, -1).") VALUES(".substr($values_list, 0, -1).")");
$maximum_clicks_quantity = affiliation_data('maximum_clicks_quantity');
if (is_numeric($maximum_clicks_quantity)) {
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."affiliation_manager_clicks", OBJECT);
$clicks_quantity = (int) $row->total;
$n = $clicks_quantity - $maximum_clicks_quantity;
if ($n > 0) { $results = $wpdb->query("DELETE FROM ".$wpdb->prefix."affiliation_manager_clicks ORDER BY date ASC LIMIT $n"); } } }
if ((!defined('AFFILIATION_MANAGER_DEMO')) || (AFFILIATION_MANAGER_DEMO == false)) {
if (affiliation_data('click_custom_instructions_executed') == 'yes') {
eval(format_instructions(affiliation_data('click_custom_instructions'))); } } }
if (!headers_sent()) {
$url = str_replace(array('?'.$a.'='.$_GET[$a].'&', '?'.$e.'='.$_GET[$e].'&'), '?', $_SERVER['REQUEST_URI']);
$url = str_replace(array('?'.$a.'='.$_GET[$a], '?'.$e.'='.$_GET[$e], '&'.$a.'='.$_GET[$a], '&'.$e.'='.$_GET[$e]), '', $url);
header('Location: '.$url); exit; }