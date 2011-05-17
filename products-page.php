<?php if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

$searchby_options = array(
'id' => __('the ID', 'commerce-manager'),
'name' => __('the name', 'commerce-manager'),
'price' => __('the price', 'commerce-manager'),
'reference' => __('the reference', 'commerce-manager'),
'description' => __('the description', 'commerce-manager'),
'shipping_cost' => __('the shipping cost', 'commerce-manager'),
'thumbnail_url' => __('the thumbnail URL', 'commerce-manager'),
'url' => __('the URL', 'commerce-manager'),
'download_url' => __('the download URL', 'commerce-manager'),
'instructions' => __('the instructions', 'commerce-manager'),
'paypal_email_address' => __('the PayPal email address', 'commerce-manager'),
'purchase_button_url' => __('the purchase button URL', 'commerce-manager'),
'order_confirmation_url' => __('the order confirmation URL', 'commerce-manager'),
'email_to_customer_sender' => __('the sender of the email sent to customer', 'commerce-manager'),
'email_to_customer_subject' => __('the subject of the email sent to customer', 'commerce-manager'),
'email_to_customer_body' => __('the body of the email sent to customer', 'commerce-manager'),
'email_to_seller_receiver' => __('the receiver of the email sent to seller', 'commerce-manager'),
'email_to_seller_subject' => __('the subject of the email sent to seller', 'commerce-manager'),
'email_to_seller_body' => __('the body of the email sent to seller', 'commerce-manager'),
'customers_aweber_list' => __('the customers AWeber list', 'commerce-manager'),
'commission_amount' => __('the commission amount', 'commerce-manager'),
'commission_percentage' => __('the commission percentage', 'commerce-manager'),
'date' => __('the launch date', 'commerce-manager'),
'date_utc' => __('the launch date (UTC)', 'commerce-manager'));

include_once 'tables/products.php';
include_once 'tables/functions.php';
include 'tables/page.php';