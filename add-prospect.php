<?php if (isset($prospect)) {
global $wpdb;
foreach (array('admin-pages.php', 'tables.php') as $file) { include dirname(__FILE__).'/'.$file; }
$_GET['optin_form_id'] = $prospect['form_id'];
if (function_exists('add_affiliate')) {
if ((!is_admin()) && (affiliation_session())) { $_GET['affiliate_id'] = (int) affiliate_data('id'); }
$_GET['affiliate_id'] = (int) $_GET['affiliate_id'];
if ($_GET['affiliate_id'] == 0) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".$prospect['email_address']."'", OBJECT);
if (!$result) { $result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE paypal_email_address = '".$prospect['email_address']."'", OBJECT); }
if ($result) { $_GET['affiliate_data'] = (array) $result; $_GET['affiliate_id'] = $result->id; } } }
if (function_exists('add_client')) {
if ((!is_admin()) && (commerce_session())) { $_GET['client_id'] = (int) client_data('id'); }
$_GET['client_id'] = (int) $_GET['client_id'];
if ($_GET['client_id'] == 0) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_clients WHERE email_address = '".$prospect['email_address']."'", OBJECT);
if (!$result) { $result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_clients WHERE paypal_email_address = '".$prospect['email_address']."'", OBJECT); }
if ($result) { $_GET['client_data'] = (array) $result; $_GET['client_id'] = $result->id; } } }
if (function_exists('add_member')) {
if ((!is_admin()) && (membership_session(''))) { $_GET['member_id'] = (int) member_data('id'); }
$_GET['member_id'] = (int) $_GET['member_id'];
if ($_GET['member_id'] == 0) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members WHERE email_address = '".$prospect['email_address']."'", OBJECT);
if ($result) { $_GET['member_data'] = (array) $result; $_GET['member_id'] = $result->id; } } }
$_GET['user_id'] = (int) $_GET['user_id'];
if ($_GET['user_id'] == 0) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->base_prefix."users WHERE user_email = '".$prospect['email_address']."'", OBJECT);
if ($result) { $_GET['user_data'] = (array) $result; $_GET['user_id'] = $result->ID; } }
if ((is_admin()) || (optin_form_data('prospects_registration_enabled') == 'yes')) {
$sql = optin_sql_array($tables['prospects'], $prospect);
foreach ($tables['prospects'] as $key => $value) { if ($key != 'id') { $keys_list .= $key.","; $values_list .= $sql[$key].","; } }
$result = $wpdb->query("INSERT INTO ".$wpdb->prefix."optin_manager_prospects (".substr($keys_list, 0, -1).") VALUES(".substr($values_list, 0, -1).")");
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."optin_manager_prospects WHERE email_address = '".$prospect['email_address']."' AND autoresponder = '".$prospect['autoresponder']."' AND autoresponder_list = '".$prospect['autoresponder_list']."'", OBJECT);
$prospect['id'] = $result->id;
if (!is_admin()) {
$maximum_prospects_quantity = optin_data('maximum_prospects_quantity');
if (is_numeric($maximum_prospects_quantity)) {
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."optin_manager_prospects", OBJECT);
$prospects_quantity = (int) $row->total;
$n = $prospects_quantity - $maximum_prospects_quantity;
if ($n > 0) { $results = $wpdb->query("DELETE FROM ".$wpdb->prefix."optin_manager_prospects ORDER BY date ASC LIMIT $n"); } }
$maximum_prospects_quantity = optin_form_data('maximum_prospects_quantity');
if (is_numeric($maximum_prospects_quantity)) {
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."optin_manager_prospects WHERE form_id = ".$prospect['form_id'], OBJECT);
$prospects_quantity = (int) $row->total;
$n = $prospects_quantity - $maximum_prospects_quantity;
if ($n > 0) { $results = $wpdb->query("DELETE FROM ".$wpdb->prefix."optin_manager_prospects WHERE form_id = ".$prospect['form_id']." ORDER BY date ASC LIMIT $n"); } } } }
$_GET['prospect_data'] = $prospect;
$original_prospect = $prospect;
if ($prospect['referrer'] != '') {
$_GET['affiliate_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$prospect['referrer']."'", OBJECT);
$_GET['referrer_data'] = $_GET['affiliate_data']; }
foreach ($add_prospect_fields as $field) {
if (is_admin()) { $prospect[$field] = stripslashes(do_shortcode($prospect[$field])); }
else { $prospect[$field] = optin_form_data($field); } }

if ($prospect['form_id'] > 0) {
$displays_count = optin_form_data('displays_count');
$prospects_count = optin_form_data('prospects_count') + 1;
if ($displays_count < $prospects_count) { $displays_count = $prospects_count; }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."optin_manager_forms SET
	displays_count = ".$displays_count.",
	prospects_count = ".$prospects_count." WHERE id = ".$prospect['form_id']); }

if ((function_exists('add_affiliate')) && ($prospect['prospect_subscribed_to_affiliate_program'] == 'yes')) {
if ($_GET['affiliate_id'] > 0) {
if ($prospect['prospect_affiliate_category_id'] > 0) {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_affiliates SET category_id = ".$prospect['prospect_affiliate_category_id']." WHERE id = ".$_GET['affiliate_id']); } }
else {
$affiliate = $prospect;
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
$affiliate[$field] = $prospect['prospect_affiliate_'.$field];
if ($affiliate[$field] == '') { $affiliate[$field] = affiliation_data('affiliates_initial_'.$field); } }
foreach (array('confirmation', 'notification') as $action) {
$affiliate['registration_'.$action.'_email_sent'] = $prospect['affiliation_registration_'.$action.'_email_sent'];
if ((!is_admin()) && ($affiliate['registration_'.$action.'_email_sent'] == '')) {
$affiliate['registration_'.$action.'_email_sent'] = affiliation_data('registration_'.$action.'_email_sent'); } }
$affiliate['registration_without_form'] = 'yes';
add_affiliate($affiliate); } }

if ((function_exists('add_client')) && ($prospect['prospect_subscribed_as_a_client'] == 'yes')) {
if ($_GET['client_id'] > 0) {
if ($prospect['prospect_client_category_id'] > 0) {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_clients SET category_id = ".$prospect['prospect_client_category_id']." WHERE id = ".$_GET['client_id']); } }
else {
if (isset($affiliate)) { $client = $affiliate; }
else { $client = $prospect; }
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
$client[$field] = $prospect['prospect_client_'.$field];
if ($client[$field] == '') { $client[$field] = commerce_data('clients_initial_'.$field); } }
foreach (array('confirmation', 'notification') as $action) {
$client['registration_'.$action.'_email_sent'] = $prospect['commerce_registration_'.$action.'_email_sent'];
if ((!is_admin()) && ($client['registration_'.$action.'_email_sent'] == '')) {
$client['registration_'.$action.'_email_sent'] = commerce_data('registration_'.$action.'_email_sent'); } }
$client['registration_without_form'] = 'yes';
add_client($client); } }

if ((function_exists('add_member')) && ($prospect['prospect_subscribed_to_members_areas'] == 'yes')) {
if ($_GET['member_id'] > 0) {
update_member_members_areas($_GET['member_id'], $prospect['prospect_members_areas'], 'add');
if ($prospect['prospect_member_category_id'] > 0) {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_members SET category_id = ".$prospect['prospect_member_category_id']." WHERE id = ".$_GET['member_id']); } }
else {
if (isset($affiliate)) { $member = $affiliate; }
elseif (isset($client)) { $member = $client; }
else { $member = $prospect; }
$member['members_areas'] = $prospect['prospect_members_areas'];
$members_areas = array_unique(preg_split('#[^0-9]#', $member['members_areas'], 0, PREG_SPLIT_NO_EMPTY));
if (count($members_areas) == 1) { $_GET['member_area_id'] = (int) $members_areas[0]; } else { unset($_GET['member_area_id']); }
if (!isset($member['login'])) { $member['login'] = $member['email_address']; }
$login = $member['login']; $result = true; $i = 1; while ($result) {
$result = $wpdb->get_results("SELECT login FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$member['login']."'", OBJECT);
if ($result) { $member['login'] = $login.$i; $i = $i + 1; } }
if (!isset($member['password'])) { $member['password'] = substr(md5(mt_rand()), 0, 8); }
foreach (array('category_id', 'status') as $field) {
$member[$field] = $prospect['prospect_member_'.$field];
if ($member[$field] == '') { $member[$field] = member_area_data('members_initial_'.$field); } }
foreach (array('confirmation', 'notification') as $action) {
$member['registration_'.$action.'_email_sent'] = $prospect['membership_registration_'.$action.'_email_sent'];
if ((!is_admin()) && ($member['registration_'.$action.'_email_sent'] == '')) {
$member['registration_'.$action.'_email_sent'] = member_area_data('registration_'.$action.'_email_sent'); } }
$member['registration_without_form'] = 'yes';
add_member($member); } }

if ((!defined('OPTIN_MANAGER_DEMO')) || (OPTIN_MANAGER_DEMO == false)) {
if (($_GET['user_id'] == 0) && ($prospect['prospect_subscribed_as_a_user'] == 'yes')) {
if (isset($affiliate)) { $user = $affiliate; }
elseif (isset($client)) { $user = $client; }
elseif (isset($member)) { $user = $member; }
else { $user = $prospect; }
$user['role'] = $prospect['prospect_user_role'];
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

foreach ($add_prospect_fields as $field) {
if (is_admin()) { $prospect[$field] = stripslashes(do_shortcode($original_prospect[$field])); }
else { $prospect[$field] = optin_form_data($field); } }

foreach (array('confirmation', 'notification') as $action) {
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = str_replace(array("\\t", '\\', '&#91;', '&#93;'), array('	', '', '[', ']'), str_replace(array("\\r\\n", "\\n", "\\r"), '
', $prospect['registration_'.$action.'_email_'.$field])); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); } }

if ((function_exists('referrer_data')) && ($prospect['referrer'] != '') && (!strstr($prospect['referrer'], '@'))) {
if (affiliation_data('prospect_notification_email_deactivated') != 'yes') {
$_GET['referrer'] = $prospect['referrer'];
if (referrer_data('status') == 'active') {
$sent = referrer_data('prospect_notification_email_sent');
if (($sent == 'yes') || (($sent == 'if commission') && ($prospect['commission_amount'] > 0))) {
foreach (array('sender', 'receiver', 'subject', 'body') as $field) {
$$field = str_replace(array('&#91;', '&#93;'), array('[', ']'), affiliation_data('prospect_notification_email_'.$field)); }
wp_mail($receiver, $subject, $body, 'From: '.$sender); } } } }

if (!function_exists('subscribe_to_autoresponder')) { include_once dirname(__FILE__).'/libraries/autoresponders-functions.php'; }
subscribe_to_autoresponder($prospect['autoresponder'], $prospect['autoresponder_list'], $prospect);

if ($prospect['registration_custom_instructions_executed'] == 'yes') {
eval(format_instructions($prospect['registration_custom_instructions'])); } } }