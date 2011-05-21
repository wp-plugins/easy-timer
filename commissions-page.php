<?php if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

$searchby_options = array(
'id' => __('the ID', 'affiliation-manager'),
'first_name' => __('the customer\'s first name', 'affiliation-manager'),
'last_name' => __('the customer\'s last name', 'affiliation-manager'),
'email_address' => __('the customer\'s email address', 'affiliation-manager'),
'website_name' => __('the customer\'s website', 'affiliation-manager'),
'website_url' => __('the customer\'s website URL', 'affiliation-manager'),
'address' => __('the customer\'s address', 'affiliation-manager'),
'postcode' => __('the customer\'s postcode', 'affiliation-manager'),
'town' => __('the customer\'s town', 'affiliation-manager'),
'country' => __('the customer\'s country', 'affiliation-manager'),
'phone_number' => __('the customer\'s phone number', 'affiliation-manager'),
'date' => __('the date', 'affiliation-manager'),
'date_utc' => __('the date (UTC)', 'affiliation-manager'),
'user_agent' => __('the customer\'s user agent', 'affiliation-manager'),
'ip_address' => __('the customer\'s IP address', 'affiliation-manager'),
'referring_url' => __('the customer\'s referring URL', 'affiliation-manager'),
'product_id' => __('the product ID', 'affiliation-manager'),
'quantity' => __('the quantity', 'affiliation-manager'),
'price' => __('the price', 'affiliation-manager'),
'shipping_cost' => __('the shipping cost', 'affiliation-manager'),
'amount' => __('the order amount', 'affiliation-manager'),
'payment_mode' => __('the order\'s payment mode', 'affiliation-manager'),
'transaction_number' => __('the transaction number', 'affiliation-manager'),
'refund_date' => __('the order\'s refund date', 'affiliation-manager'),
'refund_date_utc' => __('the order\'s refund date (UTC)', 'affiliation-manager'),
'referrer' => __('the referrer', 'affiliation-manager'),
'commission_amount' => __('the amount', 'affiliation-manager'),
'commission_payment_date' => __('the payment date', 'affiliation-manager'),
'commission_payment_date_utc' => __('the payment date (UTC)', 'affiliation-manager'));

include_once 'tables/commissions.php';
include_once 'tables/functions.php';
include 'tables/page.php';