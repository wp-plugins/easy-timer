<?php if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

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
'commission_amount' => __('the commission amount', 'affiliation-manager'),
'commission_percentage' => __('the commission percentage', 'affiliation-manager'),
'date' => __('the registration date', 'affiliation-manager'),
'date_utc' => __('the registration date (UTC)', 'affiliation-manager'),
'user_agent' => __('the user agent', 'affiliation-manager'),
'ip_address' => __('the IP address', 'affiliation-manager'),
'referring_url' => __('the referring URL', 'affiliation-manager'),
'referrer' => __('the referrer', 'affiliation-manager'));

include_once 'tables/affiliates.php';
include_once 'tables/functions.php';
include 'tables/page.php';