<?php if (isset($member)) {
global $wpdb;
foreach (array('admin-pages.php', 'tables.php') as $file) { include dirname(__FILE__).'/'.$file; }
if ($member['registration_without_form'] != 'yes') {
if (function_exists('add_affiliate')) {
if ((!is_admin()) && (affiliation_session())) { $_GET['affiliate_id'] = (int) affiliate_data('id'); }
$_GET['affiliate_id'] = (int) $_GET['affiliate_id'];
if ($_GET['affiliate_id'] == 0) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".$member['email_address']."'", OBJECT);
if (!$result) { $result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE paypal_email_address = '".$member['email_address']."'", OBJECT); }
if ($result) { $_GET['affiliate_data'] = (array) $result; $_GET['affiliate_id'] = $result->id; } } }
if (function_exists('add_client')) {
if ((!is_admin()) && (commerce_session())) { $_GET['client_id'] = (int) client_data('id'); }
$_GET['client_id'] = (int) $_GET['client_id'];
if ($_GET['client_id'] == 0) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_clients WHERE email_address = '".$member['email_address']."'", OBJECT);
if (!$result) { $result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_clients WHERE paypal_email_address = '".$member['email_address']."'", OBJECT); }
if ($result) { $_GET['client_data'] = (array) $result; $_GET['client_id'] = $result->id; } } }
$_GET['user_id'] = (int) $_GET['user_id'];
if ($_GET['user_id'] == 0) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->base_prefix."users WHERE user_email = '".$member['email_address']."'", OBJECT);
if ($result) { $_GET['user_data'] = (array) $result; $_GET['user_id'] = $result->ID; } } }
$sql = membership_sql_array($tables['members'], $member);
foreach ($tables['members'] as $key => $value) { if ($key != 'id') { $keys_list .= $key.","; $values_list .= $sql[$key].","; } }
$result = $wpdb->query("INSERT INTO ".$wpdb->prefix."membership_manager_members (".substr($keys_list, 0, -1).") VALUES(".substr($values_list, 0, -1).")");
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$member['login']."'", OBJECT);
$member['id'] = $result->id;
$_GET['member_data'] = $member;
$original_member = $member;
$members_areas = array_unique(preg_split('#[^0-9]#', $member['members_areas'], 0, PREG_SPLIT_NO_EMPTY));
if (count($members_areas) == 1) { $_GET['member_area_id'] = (int) $members_areas[0]; } else { unset($_GET['member_area_id']); }
foreach ($add_member_fields as $field) {
if ((is_admin()) && ($member['registration_without_form'] != 'yes')) { $member[$field] = stripslashes(do_shortcode($member[$field])); }
else { $member[$field] = member_area_data($field); } }

if ($member['registration_without_form'] == 'yes') {
$member['member_subscribed_to_affiliate_program'] = 'no';
$member['member_subscribed_as_a_client'] = 'no';
$member['member_subscribed_as_a_user'] = 'no'; }

if ((function_exists('add_affiliate')) && ($member['member_subscribed_to_affiliate_program'] == 'yes')) {
if ($_GET['affiliate_id'] > 0) {
if ($member['member_affiliate_category_id'] > 0) {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_affiliates SET category_id = ".$member['member_affiliate_category_id']." WHERE id = ".$_GET['affiliate_id']); } }
else {
$affiliate = $member;
$array = explode('@', $affiliate['login']);
$affiliate['login'] = format_nice_name($array[0]);
if (is_numeric($affiliate['login'])) { $affiliate['login'] .= '-'; }
$login = $affiliate['login']; $result = true; $i = 1; while ($result) {
$result = $wpdb->get_results("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$affiliate['login']."'", OBJECT);
if ($result) { $affiliate['login'] = $login.$i; $i = $i + 1; } }
if (!isset($affiliate['paypal_email_address'])) { $affiliate['paypal_email_address'] = $affiliate['email_address']; }
$affiliate['referrer'] = $_GET['referrer'];
if ($affiliate['referrer'] == '') {
$result = $wpdb->get_row("SELECT referrer FROM ".$wpdb->prefix."affiliation_manager_clicks WHERE ip_address = '".$affiliate['ip_address']."' ORDER BY date DESC LIMIT 1", OBJECT);
if ($result) { $affiliate['referrer'] = $result->referrer; } }
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$affiliate['referrer']."' AND status = 'active'", OBJECT);
if (!$result) { $affiliate['referrer'] = ''; }
foreach (array('category_id', 'status') as $field) {
$affiliate[$field] = $member['member_affiliate_'.$field];
if ($affiliate[$field] == '') { $affiliate[$field] = affiliation_data('affiliates_initial_'.$field); } }
foreach (array('confirmation', 'notification') as $action) {
$affiliate['registration_'.$action.'_email_sent'] = $member['affiliation_registration_'.$action.'_email_sent'];
if ((!is_admin()) && ($affiliate['registration_'.$action.'_email_sent'] == '')) {
$affiliate['registration_'.$action.'_email_sent'] = affiliation_data('registration_'.$action.'_email_sent'); } }
$affiliate['registration_without_form'] = 'yes';
add_affiliate($affiliate); } }

if ((function_exists('add_client')) && ($member['member_subscribed_as_a_client'] == 'yes')) {
if ($_GET['client_id'] > 0) {
if ($member['member_client_category_id'] > 0) {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_clients SET category_id = ".$member['member_client_category_id']." WHERE id = ".$_GET['client_id']); } }
else {
if (isset($affiliate)) { $client = $affiliate; }
else { $client = $member; }
$login = $client['login']; $result = true; $i = 1; while ($result) {
$result = $wpdb->get_results("SELECT login FROM ".$wpdb->prefix."commerce_manager_clients WHERE login = '".$client['login']."'", OBJECT);
if ($result) { $client['login'] = $login.$i; $i = $i + 1; } }
if (function_exists('add_affiliate')) {
$client['referrer'] = $_GET['referrer'];
if ($client['referrer'] == '') {
$result = $wpdb->get_row("SELECT referrer FROM ".$wpdb->prefix."affiliation_manager_clicks WHERE ip_address = '".$client['ip_address']."' ORDER BY date DESC LIMIT 1", OBJECT);
if ($result) { $client['referrer'] = $result->referrer; } }
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$client['referrer']."' AND status = 'active'", OBJECT);
if (!$result) { $client['referrer'] = ''; } }
else { $client['referrer'] = ''; }
foreach (array('category_id', 'status') as $field) {
$client[$field] = $member['member_client_'.$field];
if ($client[$field] == '') { $client[$field] = commerce_data('clients_initial_'.$field); } }
foreach (array('confirmation', 'notification') as $action) {
$client['registration_'.$action.'_email_sent'] = $member['commerce_registration_'.$action.'_email_sent'];
if ((!is_admin()) && ($client['registration_'.$action.'_email_sent'] == '')) {
$client['registration_'.$action.'_email_sent'] = commerce_data('registration_'.$action.'_email_sent'); } }
$client['registration_without_form'] = 'yes';
add_client($client); } }

if ((!defined('MEMBERSHIP_MANAGER_DEMO')) || (MEMBERSHIP_MANAGER_DEMO == false)) {
if (($_GET['user_id'] == 0) && ($member['member_subscribed_as_a_user'] == 'yes')) {
if (isset($affiliate)) { $user = $affiliate; }
elseif (isset($client)) { $user = $client; }
else { $user = $member; }
$user['role'] = $member['member_user_role'];
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

foreach ($add_member_fields as $field) {
if ((is_admin()) && ($member['registration_without_form'] != 'yes')) {
$member[$field] = stripslashes(do_shortcode($original_member[$field])); }
else { $member[$field] = member_area_data($field); } }

if ($member['registration_without_form'] == 'yes') {
foreach (array('confirmation', 'notification') as $action) {
$member['registration_'.$action.'_email_sent'] = $original_member['registration_'.$action.'_email_sent']; }
$member['member_subscribed_to_autoresponder'] = 'no';
$member['registration_custom_instructions_executed'] = 'no'; }

foreach (array('confirmation', 'notification') as $action) {
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = str_replace(array("\\t", '\\', '&#91;', '&#93;'), array('	', '', '[', ']'), str_replace(array("\\r\\n", "\\n", "\\r"), '
', $member['registration_'.$action.'_email_'.$field])); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); } }

if ($member['member_subscribed_to_autoresponder'] == 'yes') {
if (!function_exists('subscribe_to_autoresponder')) { include_once dirname(__FILE__).'/libraries/autoresponders-functions.php'; }
subscribe_to_autoresponder($member['member_autoresponder'], $member['member_autoresponder_list'], $member); }

if ($member['registration_custom_instructions_executed'] == 'yes') {
eval(format_instructions($member['registration_custom_instructions'])); } } }