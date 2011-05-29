<?php if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

$searchby_options = array(
'id' => __('the ID', 'commerce-manager'),
'first_name' => __('the first name', 'commerce-manager'),
'last_name' => __('the last name', 'commerce-manager'),
'email_address' => __('the email address', 'commerce-manager'),
'website_name' => __('the website', 'commerce-manager'),
'website_url' => __('the website URL', 'commerce-manager'),
'address' => __('the address', 'commerce-manager'),
'postcode' => __('the postcode', 'commerce-manager'),
'town' => __('the town', 'commerce-manager'),
'country' => __('the country', 'commerce-manager'),
'phone_number' => __('the phone number', 'commerce-manager'),
'date' => __('the date', 'commerce-manager'),
'date_utc' => __('the date (UTC)', 'commerce-manager'),
'user_agent' => __('the user agent', 'commerce-manager'),
'ip_address' => __('the IP address', 'commerce-manager'),
'referring_url' => __('the referring URL', 'commerce-manager'),
'product_id' => __('the product ID', 'commerce-manager'),
'quantity' => __('the quantity', 'commerce-manager'),
'price' => __('the price', 'commerce-manager'),
'tax' => __('the tax', 'commerce-manager'),
'shipping_cost' => __('the shipping cost', 'commerce-manager'),
'amount' => __('the amount', 'commerce-manager'),
'payment_mode' => __('the payment mode', 'commerce-manager'),
'transaction_number' => __('the transaction number', 'commerce-manager'),
'instructions' => __('the instructions', 'commerce-manager'),
'shipping_address' => __('the shipping address', 'commerce-manager'),
'refund_date' => __('the refund date', 'commerce-manager'),
'refund_date_utc' => __('the refund date (UTC)', 'commerce-manager'),
'referrer' => __('the referrer', 'commerce-manager'),
'commission_amount' => __('the commission amount', 'commerce-manager'),
'commission_payment_date' => __('the commission\'s payment date', 'commerce-manager'),
'commission_payment_date_utc' => __('the commission\'s payment date (UTC)', 'commerce-manager'));

include_once 'tables/orders.php';
include_once 'tables/functions.php';
include 'tables/page.php';