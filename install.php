<?php include_once 'initial-options.php';

$affiliation_manager_options = get_option('affiliation_manager');
foreach ($affiliation_manager_initial_options as $key => $value) {
if ($affiliation_manager_options[$key] == '') { $affiliation_manager_options[$key] = $affiliation_manager_initial_options[$key]; } }
update_option('affiliation_manager', $affiliation_manager_options);

add_option('affiliation_manager_email_to_affiliate_body', $affiliation_manager_initial_email_to_affiliate_body);
add_option('affiliation_manager_email_to_affiliator_body', $affiliation_manager_initial_email_to_affiliator_body);
add_option('affiliation_manager_password_reset_email_body', $affiliation_manager_initial_password_reset_email_body);

$affiliation_manager_affiliates_options = get_option('affiliation_manager_affiliates');
foreach ($affiliation_manager_affiliates_initial_options as $key => $value) {
if ($affiliation_manager_affiliates_options[$key] == '') {
$affiliation_manager_affiliates_options[$key] = $affiliation_manager_affiliates_initial_options[$key]; } }
update_option('affiliation_manager_affiliates', $affiliation_manager_affiliates_options);

$affiliation_manager_clicks_options = get_option('affiliation_manager_clicks');
foreach ($affiliation_manager_clicks_initial_options as $key => $value) {
if ($affiliation_manager_clicks_options[$key] == '') {
$affiliation_manager_clicks_options[$key] = $affiliation_manager_clicks_initial_options[$key]; } }
update_option('affiliation_manager_clicks', $affiliation_manager_clicks_options);

$affiliation_manager_commissions_options = get_option('affiliation_manager_commissions');
foreach ($affiliation_manager_commissions_initial_options as $key => $value) {
if ($affiliation_manager_commissions_options[$key] == '') {
$affiliation_manager_commissions_options[$key] = $affiliation_manager_commissions_initial_options[$key]; } }
update_option('affiliation_manager_commissions', $affiliation_manager_commissions_options);

$affiliation_manager_statistics_options = get_option('affiliation_manager_statistics');
foreach ($affiliation_manager_statistics_initial_options as $key => $value) {
if ($affiliation_manager_statistics_options[$key] == '') {
$affiliation_manager_statistics_options[$key] = $affiliation_manager_statistics_initial_options[$key]; } }
update_option('affiliation_manager_statistics', $affiliation_manager_statistics_options);


include_once ABSPATH.'wp-admin/includes/upgrade.php';
global $wpdb;
$affiliates_table_name = $wpdb->prefix.'affiliation_manager_affiliates';
$clicks_table_name = $wpdb->prefix.'affiliation_manager_clicks';
if (!empty($wpdb->charset)) { $charset_collate = 'DEFAULT CHARACTER SET '.$wpdb->charset; }
if (!empty($wpdb->collate)) { $charset_collate .= ' COLLATE '.$wpdb->collate; }

$sql = "CREATE TABLE ".$affiliates_table_name." (
	id int auto_increment,
	login text NOT NULL,
	password text NOT NULL,
	first_name text NOT NULL,
	last_name text NOT NULL,
	email_address text NOT NULL,
	paypal_email_address text NOT NULL,
	website_name text NOT NULL,
	website_url text NOT NULL,
	address text NOT NULL,
	postcode text NOT NULL,
	town text NOT NULL,
	country text NOT NULL,
	phone_number text NOT NULL,
	commission_amount text NOT NULL,
	commission_percentage text NOT NULL,
	date datetime NOT NULL,
	date_utc datetime NOT NULL,
	user_agent text NOT NULL,
	ip_address text NOT NULL,
	referring_url text NOT NULL,
	referrer text NOT NULL,
	PRIMARY KEY  (id)
) $charset_collate;"; dbDelta($sql);

$sql = "CREATE TABLE ".$clicks_table_name." (
	id int auto_increment,
	referrer text NOT NULL,
	date datetime NOT NULL,
	date_utc datetime NOT NULL,
	user_agent text NOT NULL,
	ip_address text NOT NULL,
	url text NOT NULL,
	referring_url text NOT NULL,
	PRIMARY KEY  (id)
) $charset_collate;"; dbDelta($sql);