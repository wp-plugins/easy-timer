<?php if (isset($client)) {
global $wpdb;
foreach (array('admin-pages.php', 'tables.php') as $file) { include dirname(__FILE__).'/'.$file; }
if ($client['registration_without_form'] != 'yes') {
if (function_exists('add_affiliate')) {
if ((!is_admin()) && (affiliation_session())) { $_GET['affiliate_id'] = (int) affiliate_data('id'); }
$_GET['affiliate_id'] = (int) $_GET['affiliate_id'];
if ($_GET['affiliate_id'] == 0) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".$client['email_address']."'", OBJECT);
if (!$result) { $result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE paypal_email_address = '".$client['email_address']."'", OBJECT); }
if ($result) { $_GET['affiliate_data'] = (array) $result; $_GET['affiliate_id'] = $result->id; } } }
if (function_exists('add_member')) {
if ((!is_admin()) && (membership_session(''))) { $_GET['member_id'] = (int) member_data('id'); }
$_GET['member_id'] = (int) $_GET['member_id'];
if ($_GET['member_id'] == 0) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members WHERE email_address = '".$client['email_address']."'", OBJECT);
if ($result) { $_GET['member_data'] = (array) $result; $_GET['member_id'] = $result->id; } } }
$_GET['user_id'] = (int) $_GET['user_id'];
if ($_GET['user_id'] == 0) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->base_prefix."users WHERE user_email = '".$client['email_address']."'", OBJECT);
if ($result) { $_GET['user_data'] = (array) $result; $_GET['user_id'] = $result->ID; } } }
$sql = commerce_sql_array($tables['clients'], $client);
foreach ($tables['clients'] as $key => $value) { if ($key != 'id') { $keys_list .= $key.","; $values_list .= $sql[$key].","; } }
$result = $wpdb->query("INSERT INTO ".$wpdb->prefix."commerce_manager_clients (".substr($keys_list, 0, -1).") VALUES(".substr($values_list, 0, -1).")");
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."commerce_manager_clients WHERE login = '".$client['login']."'", OBJECT);
$client['id'] = $result->id;
$_GET['client_data'] = $client;
$original_client = $client;
if (($client['referrer'] != '') && (!strstr($client['referrer'], '@'))) {
$_GET['affiliate_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$client['referrer']."'", OBJECT);
$_GET['referrer_data'] = $_GET['affiliate_data']; }
foreach ($add_client_fields as $field) {
if ((is_admin()) && ($client['registration_without_form'] != 'yes')) { $client[$field] = stripslashes(do_shortcode($client[$field])); }
else { $client[$field] = commerce_data($field); } }

$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_orders SET client_id = ".$client['id']." WHERE client_id = 0 AND email_address = '".$client['email_address']."'");
$orders = $wpdb->get_results("SELECT id FROM ".$wpdb->prefix."commerce_manager_orders WHERE client_id = ".$client['id'], OBJECT);
foreach ($orders as $order) { $results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_recurring_payments SET client_id = ".$client['id']." WHERE order_id = ".$order->id); }

if ($client['registration_without_form'] == 'yes') {
$client['client_subscribed_to_affiliate_program'] = 'no';
$client['client_subscribed_to_members_areas'] = 'no';
$client['client_subscribed_as_a_user'] = 'no'; }

if ((function_exists('add_affiliate')) && ($client['client_subscribed_to_affiliate_program'] == 'yes')) {
if ($_GET['affiliate_id'] > 0) {
if ($client['client_affiliate_category_id'] > 0) {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_affiliates SET category_id = ".$client['client_affiliate_category_id']." WHERE id = ".$_GET['affiliate_id']); } }
else {
$affiliate = $client;
$array = explode('@', $affiliate['login']);
$affiliate['login'] = format_nice_name($array[0]);
if (is_numeric($affiliate['login'])) { $affiliate['login'] .= '-'; }
$login = $affiliate['login']; $result = true; $i = 1; while ($result) {
$result = $wpdb->get_results("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$affiliate['login']."'", OBJECT);
if ($result) { $affiliate['login'] = $login.$i; $i = $i + 1; } }
if ($affiliate['paypal_email_address'] == '') { $affiliate['paypal_email_address'] = $affiliate['email_address']; }
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$affiliate['referrer']."' AND status = 'active'", OBJECT);
if (!$result) { $affiliate['referrer'] = ''; }
foreach (array('category_id', 'status') as $field) {
$affiliate[$field] = $client['client_affiliate_'.$field];
if ($affiliate[$field] == '') { $affiliate[$field] = affiliation_data('affiliates_initial_'.$field); } }
foreach (array('confirmation', 'notification') as $action) {
$affiliate['registration_'.$action.'_email_sent'] = $client['client_affiliation_registration_'.$action.'_email_sent'];
if ((!is_admin()) && ($affiliate['registration_'.$action.'_email_sent'] == '')) {
$affiliate['registration_'.$action.'_email_sent'] = affiliation_data('registration_'.$action.'_email_sent'); } }
$affiliate['registration_without_form'] = 'yes';
add_affiliate($affiliate); } }

if ((function_exists('add_member')) && ($client['client_subscribed_to_members_areas'] == 'yes')) {
if ($_GET['member_id'] > 0) {
update_member_members_areas($_GET['member_id'], $client['client_members_areas'], 'add');
if ($client['client_member_category_id'] > 0) {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_members SET category_id = ".$client['client_member_category_id']." WHERE id = ".$_GET['member_id']); } }
else {
if (isset($affiliate)) { $member = $affiliate; }
else { $member = $client; }
$member['members_areas'] = $client['client_members_areas'];
$members_areas = array_unique(preg_split('#[^0-9]#', $member['members_areas'], 0, PREG_SPLIT_NO_EMPTY));
if (count($members_areas) == 1) { $_GET['member_area_id'] = (int) $members_areas[0]; } else { unset($_GET['member_area_id']); }
$login = $member['login']; $result = true; $i = 1; while ($result) {
$result = $wpdb->get_results("SELECT login FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$member['login']."'", OBJECT);
if ($result) { $member['login'] = $login.$i; $i = $i + 1; } }
foreach (array('category_id', 'status') as $field) {
$member[$field] = $client['client_member_'.$field];
if ($member[$field] == '') { $member[$field] = member_area_data('members_initial_'.$field); } }
foreach (array('confirmation', 'notification') as $action) {
$member['registration_'.$action.'_email_sent'] = $client['client_membership_registration_'.$action.'_email_sent'];
if ((!is_admin()) && ($member['registration_'.$action.'_email_sent'] == '')) {
$member['registration_'.$action.'_email_sent'] = member_area_data('registration_'.$action.'_email_sent'); } }
$member['registration_without_form'] = 'yes';
add_member($member); } }

if ((!defined('COMMERCE_MANAGER_DEMO')) || (COMMERCE_MANAGER_DEMO == false)) {
if (($_GET['user_id'] == 0) && ($client['client_subscribed_as_a_user'] == 'yes')) {
if (isset($affiliate)) { $user = $affiliate; }
else { $user = $client; }
$user['role'] = $client['client_user_role'];
$login = $user['login']; $result = true; $i = 1; while ($result) {
$result = $wpdb->get_results("SELECT user_login FROM ".$wpdb->base_prefix."users WHERE user_login = '".$user['login']."'", OBJECT);
if ($result) { $user['login'] = $login.$i; $i = $i + 1; } }
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

foreach ($add_client_fields as $field) {
if ((is_admin()) && ($client['registration_without_form'] != 'yes')) {
$client[$field] = stripslashes(do_shortcode($original_client[$field])); }
else { $client[$field] = commerce_data($field); } }

if ($client['registration_without_form'] == 'yes') {
foreach (array('confirmation', 'notification') as $action) {
$client['registration_'.$action.'_email_sent'] = $original_client['registration_'.$action.'_email_sent']; }
$client['client_subscribed_to_autoresponder'] = 'no';
$client['registration_custom_instructions_executed'] = 'no'; }

foreach (array('confirmation', 'notification') as $action) {
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = str_replace(array("\\t", '\\', '&#91;', '&#93;'), array('	', '', '[', ']'), str_replace(array("\\r\\n", "\\n", "\\r"), '
', $client['registration_'.$action.'_email_'.$field])); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); } }

if ((function_exists('referrer_data')) && ($client['referrer'] != '') && (!strstr($client['referrer'], '@'))) {
if (affiliation_data('client_notification_email_deactivated') != 'yes') {
$_GET['referrer'] = $client['referrer'];
if (referrer_data('status') == 'active') {
$sent = referrer_data('client_notification_email_sent');
if ($sent == 'yes') {
foreach (array('sender', 'receiver', 'subject', 'body') as $field) {
$$field = str_replace(array('&#91;', '&#93;'), array('[', ']'), affiliation_data('client_notification_email_'.$field)); }
wp_mail($receiver, $subject, $body, 'From: '.$sender); } } } }

if ($client['client_subscribed_to_autoresponder'] == 'yes') {
if (!function_exists('subscribe_to_autoresponder')) { include_once dirname(__FILE__).'/libraries/autoresponders-functions.php'; }
subscribe_to_autoresponder($client['client_autoresponder'], $client['client_autoresponder_list'], $client); }

if ($client['registration_custom_instructions_executed'] == 'yes') {
eval(format_instructions($client['registration_custom_instructions'])); } } }