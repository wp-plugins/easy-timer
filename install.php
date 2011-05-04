<?php $affiliation_manager_default_options = array(
'affiliates_aweber_list' => '',
'affiliates_subscribed_to_aweber_list' => 'no',
'affiliation_enabled' => 'yes',
'commission_amount' => 10,
'commission_payment' => 'deferred',
'commission_percentage' => 50,
'commission_type' => 'proportional',
'cookies_lifetime' => 180,
'cookies_name' => 'a',
'email_sent_to_affiliate' => 'yes',
'email_sent_to_affiliator' => 'yes',
'email_to_affiliate_sender' => get_option('blogname').' <'.get_option('admin_email').'>',
'email_to_affiliate_subject' => __('Your Registration To Our Affiliate Program', 'affiliation-manager'),
'email_to_affiliator_receiver' => get_option('blogname').' <'.get_option('admin_email').'>',
'email_to_affiliator_subject' => __('Registration Of An Affiliate', 'affiliation-manager').' ([affiliate login])',
'first_sale_winner' => 'affiliator',
'maximum_login_length' => 16,
'maximum_password_length' => 32,
'minimum_login_length' => 1,
'minimum_password_length' => 5,
'minimum_payout_amount' => 0,
'password_reset_email_sender' => get_option('blogname').' <'.get_option('admin_email').'>',
'password_reset_email_subject' => __('Your New Password', 'affiliation-manager'),
'registration_confirmation_url' => get_option('home'),
'registration_required' => 'no',
'url_variable_name' => 'a',
'url_variable_name2' => 'e',
'winner_affiliate' => 'last');

$affiliation_manager_options = get_option('affiliation_manager');
foreach ($affiliation_manager_default_options as $key => $value) {
if ($affiliation_manager_options[$key] == '') { $affiliation_manager_options[$key] = $affiliation_manager_default_options[$key]; } }
update_option('affiliation_manager', $affiliation_manager_options);


add_option('affiliation_manager_email_to_affiliate_body',
__('Thank you for your registration to our affiliate program', 'affiliation-manager').', [affiliate first-name].
'.__('You can login from this page:', 'affiliation-manager').'

'.get_option('home').'

'.__('Your login', 'affiliation-manager').': [affiliate login]
'.__('Your password', 'affiliation-manager').': [affiliate password]
'.__('Your PayPal email address', 'affiliation-manager').': [affiliate paypal-email-address]

'.__('To receive your commissions, you need a Premier or Business PayPal account. Upgrade your PayPal account if you have a Personal account.', 'affiliation-manager').'

--
'.get_option('blogname').'
'.get_option('home'));


add_option('affiliation_manager_email_to_affiliator_body',
'[affiliate first-name] [affiliate last-name]

'.__('Login', 'affiliation-manager').': [affiliate login]
'.__('Email address', 'affiliation-manager').': [affiliate email-address]
'.__('PayPal email address', 'affiliation-manager').': [affiliate paypal-email-address]
'.__('Website name', 'affiliation-manager').': [affiliate website-name]
'.__('Website URL', 'affiliation-manager').': [affiliate website-url]

'.__('More informations about this affiliate', 'affiliation-manager').':

'.get_option('siteurl').'/wp-admin/admin.php?page=affiliation-manager-affiliate&id=[affiliate id]');


add_option('affiliation_manager_password_reset_email_body',
__('Hi', 'affiliation-manager').', [affiliate first-name].

'.__('Here are your new login informations:', 'affiliation-manager').'

'.__('Your login', 'affiliation-manager').': [affiliate login]
'.__('Your password', 'affiliation-manager').': [affiliate password]

'.__('You can login from this page:', 'affiliation-manager').'

'.get_option('home').'

--
'.get_option('blogname').'
'.get_option('home'));


$affiliation_manager_affiliates_default_options = array(
'columns' => array(
'id',
'login',
'first_name',
'last_name',
'email_address',
'website_name',
'date',
'referrer',
'paypal_email_address',
'website_url',
'address',
'postcode',
'town',
'country',
'phone_number',
'commission_percentage',
'commission_amount',
'date_utc',
'user_agent',
'ip_address',
'referring_url'),
'columns_number' => 8,
'limit' => 20,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '');

$affiliation_manager_affiliates_options = get_option('affiliation_manager_affiliates');
foreach ($affiliation_manager_affiliates_default_options as $key => $value) {
if ($affiliation_manager_affiliates_options[$key] == '') {
$affiliation_manager_affiliates_options[$key] = $affiliation_manager_affiliates_default_options[$key]; } }
update_option('affiliation_manager_affiliates', $affiliation_manager_affiliates_options);


$affiliation_manager_clicks_default_options = array(
'columns' => array(
'id',
'referrer',
'date',
'date_utc',
'user_agent',
'ip_address',
'url',
'referring_url'),
'columns_number' => 8,
'limit' => 20,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '');

$affiliation_manager_clicks_options = get_option('affiliation_manager_clicks');
foreach ($affiliation_manager_clicks_default_options as $key => $value) {
if ($affiliation_manager_clicks_options[$key] == '') {
$affiliation_manager_clicks_options[$key] = $affiliation_manager_clicks_default_options[$key]; } }
update_option('affiliation_manager_clicks', $affiliation_manager_clicks_options);


$affiliation_manager_commissions_default_options = array(
'columns' => array(
'id',
'referrer',
'date',
'product_id',
'commission_amount',
'commission_payment',
'commission_status',
'commission_payment_date',
'first_name',
'last_name',
'email_address',
'website_name',
'website_url',
'address',
'postcode',
'town',
'country',
'phone_number',
'date_utc',
'user_agent',
'ip_address',
'referring_url',
'quantity',
'price',
'shipping_cost',
'amount',
'payment_mode',
'transaction_number',
'status',
'refund_date',
'refund_date_utc',
'commission_payment_date_utc'),
'columns_number' => 8,
'limit' => 20,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '');

$affiliation_manager_commissions_options = get_option('affiliation_manager_commissions');
foreach ($affiliation_manager_commissions_default_options as $key => $value) {
if ($affiliation_manager_commissions_options[$key] == '') {
$affiliation_manager_commissions_options[$key] = $affiliation_manager_commissions_default_options[$key]; } }
update_option('affiliation_manager_commissions', $affiliation_manager_commissions_options);


include_once(ABSPATH.'wp-admin/includes/upgrade.php');
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
	commission_percentage text NOT NULL,
	commission_amount text NOT NULL,
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