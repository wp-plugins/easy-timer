<?php if (isset($order)) {
global $wpdb;
foreach (array('admin-pages.php', 'tables.php') as $file) { include dirname(__FILE__).'/'.$file; }
$order['client_id'] = (int) $order['client_id'];
if ($order['client_id'] == 0) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_clients WHERE email_address = '".$order['email_address']."'", OBJECT);
if (!$result) { $result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_clients WHERE paypal_email_address = '".$order['email_address']."'", OBJECT); }
if ($result) { $_GET['client_data'] = (array) $result; $order['client_id'] = $result->id; } }
$_GET['client_id'] = (int) $order['client_id'];
if (function_exists('add_affiliate')) {
$order['affiliate_id'] = (int) $order['affiliate_id'];
if ($order['affiliate_id'] == 0) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".$order['email_address']."'", OBJECT);
if (!$result) { $result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE paypal_email_address = '".$order['email_address']."'", OBJECT); }
if ($result) { $_GET['affiliate_data'] = (array) $result; $order['affiliate_id'] = $result->id; } }
$_GET['affiliate_id'] = (int) $order['affiliate_id']; }
if (function_exists('add_member')) {
$order['member_id'] = (int) $order['member_id'];
if ($order['member_id'] == 0) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members WHERE email_address = '".$order['email_address']."'", OBJECT);
if ($result) { $_GET['member_data'] = (array) $result; $order['member_id'] = $result->id; } }
$_GET['member_id'] = (int) $order['member_id']; }
$order['user_id'] = (int) $order['user_id'];
if ($order['user_id'] == 0) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->base_prefix."users WHERE user_email = '".$order['email_address']."'", OBJECT);
if ($result) { $_GET['user_data'] = (array) $result; $order['user_id'] = $result->ID; } }
$_GET['user_id'] = (int) $order['user_id'];
$sql = commerce_sql_array($tables['orders'], $order);
foreach ($tables['orders'] as $key => $value) { if ($key != 'id') { $keys_list .= $key.","; $values_list .= $sql[$key].","; } }
$result = $wpdb->query("INSERT INTO ".$wpdb->prefix."commerce_manager_orders (".substr($keys_list, 0, -1).") VALUES(".substr($values_list, 0, -1).")");
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."commerce_manager_orders WHERE date = '".$order['date']."' AND product_id = ".$order['product_id']." AND email_address = '".$order['email_address']."'", OBJECT);
$order['id'] = $result->id;
$_GET['order_data'] = $order;
$original_order = $order;
$_GET['product_id'] = $order['product_id'];
$_GET['commerce_form_id'] = $order['form_id'];
if (($order['referrer'] != '') && (!strstr($order['referrer'], '@'))) {
$_GET['affiliate_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$order['referrer']."'", OBJECT);
$_GET['referrer_data'] = $_GET['affiliate_data']; }
foreach ($add_order_fields as $field) {
if (is_admin()) { $order[$field] = stripslashes(do_shortcode($order[$field])); }
else { $order[$field] = product_data($field); } }

if (is_numeric(product_data('available_quantity'))) {
$available_quantity = product_data('available_quantity') - $order['quantity'];
if ($available_quantity < 0) { $available_quantity = 0; } }
else { $available_quantity = 'unlimited'; }
$sales_count = product_data('sales_count') + $order['quantity'];
if ($order['status'] == 'refunded') { $refunds_count = product_data('refunds_count') + $order['quantity']; }
else { $refunds_count = product_data('refunds_count'); }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_products SET
	available_quantity = '".$available_quantity."',
	sales_count = ".$sales_count.",
	refunds_count = ".$refunds_count." WHERE id = ".$order['product_id']);

if ($order['form_id'] > 0) {
$displays_count = commerce_form_data('displays_count');
$orders_count = commerce_form_data('orders_count') + 1;
if ($displays_count < $orders_count) { $displays_count = $orders_count; }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_forms SET
	displays_count = ".$displays_count.",
	orders_count = ".$orders_count." WHERE id = ".$order['form_id']); }

if ((function_exists('add_affiliate')) && ($order['customer_subscribed_to_affiliate_program'] == 'yes')) {
if ($_GET['affiliate_id'] > 0) {
if ($order['customer_affiliate_category_id'] > 0) {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_affiliates SET category_id = ".$order['customer_affiliate_category_id']." WHERE id = ".$_GET['affiliate_id']); } }
else {
$affiliate = $order;
if (!isset($affiliate['login'])) { $affiliate['login'] = $affiliate['email_address']; }
$array = explode('@', $affiliate['login']);
$affiliate['login'] = format_nice_name($array[0]);
if (is_numeric($affiliate['login'])) { $affiliate['login'] .= '-'; }
$login = $affiliate['login']; $result = true; $i = 1; while ($result) {
$result = $wpdb->get_results("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$affiliate['login']."'", OBJECT);
if ($result) { $affiliate['login'] = $login.$i; $i = $i + 1; } }
if (!isset($affiliate['password'])) { $affiliate['password'] = substr(md5(mt_rand()), 0, 8); }
if (!isset($affiliate['paypal_email_address'])) { $affiliate['paypal_email_address'] = $affiliate['email_address']; }
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$affiliate['referrer']."' AND status = 'active'", OBJECT);
if (!$result) { $affiliate['referrer'] = ''; }
foreach (array('category_id', 'status') as $field) {
$affiliate[$field] = $order['customer_affiliate_'.$field];
if ($affiliate[$field] == '') { $affiliate[$field] = affiliation_data('affiliates_initial_'.$field); } }
foreach (array('confirmation', 'notification') as $action) {
$affiliate['registration_'.$action.'_email_sent'] = $order['affiliation_registration_'.$action.'_email_sent'];
if ((!is_admin()) && ($affiliate['registration_'.$action.'_email_sent'] == '')) {
$affiliate['registration_'.$action.'_email_sent'] = affiliation_data('registration_'.$action.'_email_sent'); } }
$affiliate['registration_without_form'] = 'yes';
add_affiliate($affiliate); } }

if ($order['customer_subscribed_as_a_client'] == 'yes') {
if ($_GET['client_id'] > 0) {
if ($order['customer_client_category_id'] > 0) {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_clients SET category_id = ".$order['customer_client_category_id']." WHERE id = ".$_GET['client_id']); } }
else {
if (isset($affiliate)) { $client = $affiliate; }
else { $client = $order; }
if (!isset($client['login'])) { $client['login'] = $client['email_address']; }
$login = $client['login']; $result = true; $i = 1; while ($result) {
$result = $wpdb->get_results("SELECT login FROM ".$wpdb->prefix."commerce_manager_clients WHERE login = '".$client['login']."'", OBJECT);
if ($result) { $client['login'] = $login.$i; $i = $i + 1; } }
if (!isset($client['password'])) { $client['password'] = substr(md5(mt_rand()), 0, 8); }
if (function_exists('add_affiliate')) {
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$client['referrer']."' AND status = 'active'", OBJECT);
if (!$result) { $client['referrer'] = ''; } }
else { $client['referrer'] = ''; }
foreach (array('category_id', 'status') as $field) {
$client[$field] = $order['customer_client_'.$field];
if ($client[$field] == '') { $client[$field] = commerce_data('clients_initial_'.$field); } }
foreach (array('confirmation', 'notification') as $action) {
$client['registration_'.$action.'_email_sent'] = $order['commerce_registration_'.$action.'_email_sent'];
if ((!is_admin()) && ($client['registration_'.$action.'_email_sent'] == '')) {
$client['registration_'.$action.'_email_sent'] = commerce_data('registration_'.$action.'_email_sent'); } }
$client['registration_without_form'] = 'yes';
add_client($client);
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_orders SET client_id = ".client_data('id')." WHERE id = ".order_data('id')); } }
	
if ((function_exists('add_member')) && ($order['customer_subscribed_to_members_areas'] == 'yes')) {
if ($_GET['member_id'] > 0) {
update_member_members_areas($_GET['member_id'], $order['customer_members_areas'], 'add');
if ($order['customer_member_category_id'] > 0) {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_members SET category_id = ".$order['customer_member_category_id']." WHERE id = ".$_GET['member_id']); } }
else {
if (isset($affiliate)) { $member = $affiliate; }
elseif (isset($client)) { $member = $client; }
else { $member = $order; }
$member['members_areas'] = $order['customer_members_areas'];
$members_areas = array_unique(preg_split('#[^0-9]#', $member['members_areas'], 0, PREG_SPLIT_NO_EMPTY));
if (count($members_areas) == 1) { $_GET['member_area_id'] = (int) $members_areas[0]; } else { unset($_GET['member_area_id']); }
if (!isset($member['login'])) { $member['login'] = $member['email_address']; }
$login = $member['login']; $result = true; $i = 1; while ($result) {
$result = $wpdb->get_results("SELECT login FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$member['login']."'", OBJECT);
if ($result) { $member['login'] = $login.$i; $i = $i + 1; } }
if (!isset($member['password'])) { $member['password'] = substr(md5(mt_rand()), 0, 8); }
foreach (array('category_id', 'status') as $field) {
$member[$field] = $order['customer_member_'.$field];
if ($member[$field] == '') { $member[$field] = member_area_data('members_initial_'.$field); } }
foreach (array('confirmation', 'notification') as $action) {
$member['registration_'.$action.'_email_sent'] = $order['membership_registration_'.$action.'_email_sent'];
if ((!is_admin()) && ($member['registration_'.$action.'_email_sent'] == '')) {
$member['registration_'.$action.'_email_sent'] = member_area_data('registration_'.$action.'_email_sent'); } }
$member['registration_without_form'] = 'yes';
add_member($member);
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_orders SET member_id = ".member_data('id')." WHERE id = ".order_data('id')); } }

if ((!defined('COMMERCE_MANAGER_DEMO')) || (COMMERCE_MANAGER_DEMO == false)) {
if (($_GET['user_id'] == 0) && ($order['customer_subscribed_as_a_user'] == 'yes')) {
if (isset($affiliate)) { $user = $affiliate; }
elseif (isset($client)) { $user = $client; }
elseif (isset($member)) { $user = $member; }
else { $user = $order; }
$user['role'] = $order['customer_user_role'];
if (!isset($user['login'])) { $user['login'] = $user['email_address']; }
$login = $user['login']; $result = true; $i = 1; while ($result) {
$result = $wpdb->get_results("SELECT user_login FROM ".$wpdb->base_prefix."users WHERE user_login = '".$user['login']."'", OBJECT);
if ($result) { $user['login'] = $login.$i; $i = $i + 1; } }
if (!isset($user['password'])) { $user['password'] = substr(md5(mt_rand()), 0, 8); }
unset($user['ID']);
$user['user_login'] = $user['login'];
$user['user_pass'] = $user['password'];
$user['user_email'] = $user['email_address'];
$user['user_url'] = $user['website_url'];
$user['user_registered'] = $user['date_utc'];
$user['display_name'] = $user['first_name'];
$user['ID'] = wp_insert_user($user);
$_GET['user_id'] = $user['ID'];
$_GET['user_data'] = $user; }

foreach ($add_order_fields as $field) {
if (is_admin()) { $order[$field] = stripslashes(do_shortcode($original_order[$field])); }
else { $order[$field] = product_data($field); } }

foreach (array('confirmation', 'notification') as $action) {
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = str_replace(array("\\t", '\\', '&#91;', '&#93;'), array('	', '', '[', ']'), str_replace(array("\\r\\n", "\\n", "\\r"), '
', $order['order_'.$action.'_email_'.$field])); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); } }

if ((function_exists('referrer_data')) && ($order['referrer'] != '') && (!strstr($order['referrer'], '@'))) {
if (affiliation_data('order_notification_email_deactivated') != 'yes') {
$_GET['referrer'] = $order['referrer'];
if (referrer_data('status') == 'active') {
$sent = referrer_data('order_notification_email_sent');
if (($sent == 'yes') || (($sent == 'if commission') && ($order['commission_amount'] > 0))) {
foreach (array('sender', 'receiver', 'subject', 'body') as $field) {
$$field = str_replace(array('&#91;', '&#93;'), array('[', ']'), affiliation_data('order_notification_email_'.$field)); }
wp_mail($receiver, $subject, $body, 'From: '.$sender); } } } }

if ($order['customer_subscribed_to_autoresponder'] == 'yes') {
if (!function_exists('subscribe_to_autoresponder')) { include_once dirname(__FILE__).'/libraries/autoresponders-functions.php'; }
subscribe_to_autoresponder($order['customer_autoresponder'], $order['customer_autoresponder_list'], $order); }

if ($order['order_custom_instructions_executed'] == 'yes') {
eval(format_instructions($order['order_custom_instructions'])); } } }