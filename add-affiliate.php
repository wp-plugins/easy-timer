<?php if (isset($affiliate)) {
global $wpdb;
if ((!is_admin()) || ($_GET['page'] != 'affiliation-manager-affiliate')) {
foreach (array(
'bonus_download_url',
'bonus_instructions',
'commission_type',
'commission_amount',
'commission_percentage',
'commission_payment',
'first_sale_winner',
'commission2_enabled',
'commission2_type',
'commission2_amount',
'commission2_percentage') as $field) { $affiliate[$field] = ''; } }
foreach (array('admin-pages.php', 'tables.php') as $file) { include dirname(__FILE__).'/'.$file; }
if ($affiliate['registration_without_form'] != 'yes') {
if (function_exists('add_client')) {
if ((!is_admin()) && (commerce_session())) { $_GET['client_id'] = (int) client_data('id'); }
$_GET['client_id'] = (int) $_GET['client_id'];
if ($_GET['client_id'] == 0) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_clients WHERE email_address = '".$affiliate['email_address']."'", OBJECT);
if (!$result) { $result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_clients WHERE paypal_email_address = '".$affiliate['paypal_email_address']."'", OBJECT); }
if ($result) { $_GET['client_data'] = (array) $result; $_GET['client_id'] = $result->id; } } }
if (function_exists('add_member')) {
if ((!is_admin()) && (membership_session(''))) { $_GET['member_id'] = (int) member_data('id'); }
$_GET['member_id'] = (int) $_GET['member_id'];
if ($_GET['member_id'] == 0) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members WHERE email_address = '".$affiliate['email_address']."'", OBJECT);
if ($result) { $_GET['member_data'] = (array) $result; $_GET['member_id'] = $result->id; } } }
$_GET['user_id'] = (int) $_GET['user_id'];
if ($_GET['user_id'] == 0) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->base_prefix."users WHERE user_email = '".$affiliate['email_address']."'", OBJECT);
if ($result) { $_GET['user_data'] = (array) $result; $_GET['user_id'] = $result->ID; } } }
$sql = affiliation_sql_array($tables['affiliates'], $affiliate);
foreach ($tables['affiliates'] as $key => $value) { if ($key != 'id') { $keys_list .= $key.","; $values_list .= $sql[$key].","; } }
$result = $wpdb->query("INSERT INTO ".$wpdb->prefix."affiliation_manager_affiliates (".substr($keys_list, 0, -1).") VALUES(".substr($values_list, 0, -1).")");
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$affiliate['login']."'", OBJECT);
$affiliate['id'] = $result->id;
$_GET['affiliate_data'] = $affiliate;
$original_affiliate = $affiliate;
foreach ($add_affiliate_fields as $field) {
if ((is_admin()) && ($affiliate['registration_without_form'] != 'yes')) { $affiliate[$field] = stripslashes(do_shortcode($affiliate[$field])); }
else { $affiliate[$field] = affiliation_data($field); } }

if ($affiliate['registration_without_form'] == 'yes') {
$affiliate['affiliate_subscribed_as_a_client'] = 'no';
$affiliate['affiliate_subscribed_to_members_areas'] = 'no';
$affiliate['affiliate_subscribed_as_a_user'] = 'no'; }

if ((function_exists('add_client')) && ($affiliate['affiliate_subscribed_as_a_client'] == 'yes')) {
if ($_GET['client_id'] > 0) {
if ($affiliate['affiliate_client_category_id'] > 0) {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_clients SET category_id = ".$affiliate['affiliate_client_category_id']." WHERE id = ".$_GET['client_id']); } }
else {
$client = $affiliate;
$login = $client['login']; $result = true; $i = 1; while ($result) {
$result = $wpdb->get_results("SELECT login FROM ".$wpdb->prefix."commerce_manager_clients WHERE login = '".$client['login']."'", OBJECT);
if ($result) { $client['login'] = $login.$i; $i = $i + 1; } }
foreach (array('category_id', 'status') as $field) {
$client[$field] = $affiliate['affiliate_client_'.$field];
if ($client[$field] == '') { $client[$field] = commerce_data('clients_initial_'.$field); } }
foreach (array('confirmation', 'notification') as $action) {
$client['registration_'.$action.'_email_sent'] = $affiliate['commerce_registration_'.$action.'_email_sent'];
if ((!is_admin()) && ($client['registration_'.$action.'_email_sent'] == '')) {
$client['registration_'.$action.'_email_sent'] = commerce_data('registration_'.$action.'_email_sent'); } }
$client['registration_without_form'] = 'yes';
add_client($client); } }

if ((function_exists('add_member')) && ($affiliate['affiliate_subscribed_to_members_areas'] == 'yes')) {
if ($_GET['member_id'] > 0) {
update_member_members_areas($_GET['member_id'], $affiliate['affiliate_members_areas'], 'add');
if ($affiliate['affiliate_member_category_id'] > 0) {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_members SET category_id = ".$affiliate['affiliate_member_category_id']." WHERE id = ".$_GET['member_id']); } }
else {
$member = $affiliate;
$member['members_areas'] = $affiliate['affiliate_members_areas'];
$members_areas = array_unique(preg_split('#[^0-9]#', $member['members_areas'], 0, PREG_SPLIT_NO_EMPTY));
if (count($members_areas) == 1) { $_GET['member_area_id'] = (int) $members_areas[0]; } else { unset($_GET['member_area_id']); }
$login = $member['login']; $result = true; $i = 1; while ($result) {
$result = $wpdb->get_results("SELECT login FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$member['login']."'", OBJECT);
if ($result) { $member['login'] = $login.$i; $i = $i + 1; } }
foreach (array('category_id', 'status') as $field) {
$member[$field] = $affiliate['affiliate_member_'.$field];
if ($member[$field] == '') { $member[$field] = member_area_data('members_initial_'.$field); } }
foreach (array('confirmation', 'notification') as $action) {
$member['registration_'.$action.'_email_sent'] = $affiliate['membership_registration_'.$action.'_email_sent'];
if ((!is_admin()) && ($member['registration_'.$action.'_email_sent'] == '')) {
$member['registration_'.$action.'_email_sent'] = member_area_data('registration_'.$action.'_email_sent'); } }
$member['registration_without_form'] = 'yes';
add_member($member); } }

if ((!defined('AFFILIATION_MANAGER_DEMO')) || (AFFILIATION_MANAGER_DEMO == false)) {
if (($_GET['user_id'] == 0) && ($affiliate['affiliate_subscribed_as_a_user'] == 'yes')) {
$user = $affiliate;
$user['role'] = $affiliate['affiliate_user_role'];
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

foreach ($add_affiliate_fields as $field) {
if ((is_admin()) && ($affiliate['registration_without_form'] != 'yes')) {
$affiliate[$field] = stripslashes(do_shortcode($original_affiliate[$field])); }
else { $affiliate[$field] = affiliation_data($field); } }

if ($affiliate['registration_without_form'] == 'yes') {
foreach (array('confirmation', 'notification') as $action) {
$affiliate['registration_'.$action.'_email_sent'] = $original_affiliate['registration_'.$action.'_email_sent']; }
$affiliate['affiliate_subscribed_to_autoresponder'] = 'no';
$affiliate['registration_custom_instructions_executed'] = 'no'; }

foreach (array('confirmation', 'notification') as $action) {
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = str_replace(array("\\t", '\\', '&#91;', '&#93;'), array('	', '', '[', ']'), str_replace(array("\\r\\n", "\\n", "\\r"), '
', $affiliate['registration_'.$action.'_email_'.$field])); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); } }

if (($affiliate['referrer'] != '') && (!strstr($affiliate['referrer'], '@'))) {
if (affiliation_data('affiliate_notification_email_deactivated') != 'yes') {
$_GET['referrer'] = $affiliate['referrer'];
if (referrer_data('status') == 'active') {
$sent = referrer_data('affiliate_notification_email_sent');
if ($sent == 'yes') {
foreach (array('sender', 'receiver', 'subject', 'body') as $field) {
$$field = str_replace(array('&#91;', '&#93;'), array('[', ']'), affiliation_data('affiliate_notification_email_'.$field)); }
wp_mail($receiver, $subject, $body, 'From: '.$sender); } } } }

if ($affiliate['affiliate_subscribed_to_autoresponder'] == 'yes') {
if (!function_exists('subscribe_to_autoresponder')) { include_once dirname(__FILE__).'/libraries/autoresponders-functions.php'; }
subscribe_to_autoresponder($affiliate['affiliate_autoresponder'], $affiliate['affiliate_autoresponder_list'], $affiliate); }

if ($affiliate['registration_custom_instructions_executed'] == 'yes') {
eval(format_instructions($affiliate['registration_custom_instructions'])); } } }