<?php if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

global $wpdb;
$table_name = $wpdb->prefix.'affiliation_manager_affiliates';
$no_items = __('No affiliates', 'affiliation-manager');
$_GET['singular'] = __('affiliate', 'affiliation-manager');
$_GET['plural'] = __('affiliates', 'affiliation-manager');

$_GET['columns_names'] = array(
'id' => __('ID', 'affiliation-manager'),
'login' => __('Login name', 'affiliation-manager'),
'first_name' => __('First name', 'affiliation-manager'),
'last_name' => __('Last name', 'affiliation-manager'),
'email_address' => __('Email address', 'affiliation-manager'),
'paypal_email_address' => __('PayPal email address', 'affiliation-manager'),
'website_name' => __('Website', 'affiliation-manager'),
'website_url' => __('Website URL', 'affiliation-manager'),
'address' => __('Address', 'affiliation-manager'),
'postcode' => __('Postcode', 'affiliation-manager'),
'town' => __('Town', 'affiliation-manager'),
'country' => __('Country', 'affiliation-manager'),
'phone_number' => __('Phone number', 'affiliation-manager'),
'commission_percentage' => __('Commission percentage', 'affiliation-manager'),
'commission_amount' => __('Commission amount', 'affiliation-manager'),
'date' => __('Registration date', 'affiliation-manager'),
'date_utc' => __('Registration date (UTC)', 'affiliation-manager'),
'user_agent' => __('User agent', 'affiliation-manager'),
'ip_address' => __('IP address', 'affiliation-manager'),
'referring_url' => __('Referring URL', 'affiliation-manager'),
'referrer' => __('Referrer', 'affiliation-manager'));

$_GET['columns_widths'] = array(
'id' => 5,
'login' => 12,
'first_name' => 12,
'last_name' => 12,
'email_address' => 15,
'paypal_email_address' => 15,
'website_name' => 15,
'website_url' => 18,
'address' => 15,
'postcode' => 9,
'town' => 12,
'country' => 12,
'phone_number' => 12,
'commission_percentage' => 12,
'commission_amount' => 12,
'date' => 18,
'date_utc' => 18,
'user_agent' => 24,
'ip_address' => 12,
'referring_url' => 18,
'referrer' => 12);

$searchby_options = array(
'id' => __('the ID', 'affiliation-manager'),
'login' => __('the login name', 'affiliation-manager'),
'first_name' => __('the first name', 'affiliation-manager'),
'last_name' => __('the last name', 'affiliation-manager'),
'email_address' => __('the email address', 'affiliation-manager'),
'paypal_email_address' => __('the PayPal email address', 'affiliation-manager'),
'website_name' => __('the website', 'affiliation-manager'),
'website_url' => __('the website URL', 'affiliation-manager'),
'address' => __('the address', 'affiliation-manager'),
'postcode' => __('the postcode', 'affiliation-manager'),
'town' => __('the town', 'affiliation-manager'),
'country' => __('the country', 'affiliation-manager'),
'phone_number' => __('the phone number', 'affiliation-manager'),
'commission_percentage' => __('the commission percentage', 'affiliation-manager'),
'commission_amount' => __('the commission amount', 'affiliation-manager'),
'date' => __('the registration date', 'affiliation-manager'),
'date_utc' => __('the registration date (UTC)', 'affiliation-manager'),
'user_agent' => __('the user agent', 'affiliation-manager'),
'ip_address' => __('the IP address', 'affiliation-manager'),
'referring_url' => __('the referring URL', 'affiliation-manager'),
'referrer' => __('the referrer', 'affiliation-manager'));

include 'list-pages.php';