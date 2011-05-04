<?php if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

global $wpdb;
$table_name = $wpdb->prefix.'commerce_manager_orders';
if (isset($_GET['product_id'])) { $selection_criteria .= " AND product_id='".$_GET['product_id']."'"; }
if (isset($_GET['status'])) { $selection_criteria .= " AND status='".$_GET['status']."'"; }
if (isset($_GET['commission_payment'])) { $selection_criteria .= " AND commission_payment='".$_GET['commission_payment']."'"; }
if (isset($_GET['commission_status'])) { $selection_criteria .= " AND commission_status='".$_GET['commission_status']."'"; }
if ($selection_criteria != '') { $selection_criteria = 'WHERE'.substr($selection_criteria, 4); }
$no_items = __('No orders', 'commerce-manager');
$_GET['singular'] = __('order', 'commerce-manager');
$_GET['plural'] = __('orders', 'commerce-manager');

$_GET['columns_names'] = array(
'id' => __('ID', 'commerce-manager'),
'first_name' => __('First name', 'commerce-manager'),
'last_name' => __('Last name', 'commerce-manager'),
'email_address' => __('Email address', 'commerce-manager'),
'website_name' => __('Website', 'commerce-manager'),
'website_url' => __('Website URL', 'commerce-manager'),
'address' => __('Address', 'commerce-manager'),
'postcode' => __('Postcode', 'commerce-manager'),
'town' => __('Town', 'commerce-manager'),
'country' => __('Country', 'commerce-manager'),
'phone_number' => __('Phone number', 'commerce-manager'),
'date' => __('Date', 'commerce-manager'),
'date_utc' => __('Date (UTC)', 'commerce-manager'),
'user_agent' => __('User agent', 'commerce-manager'),
'ip_address' => __('IP address', 'commerce-manager'),
'referring_url' => __('Referring URL', 'commerce-manager'),
'product_id' => __('Product ID', 'commerce-manager'),
'quantity' => __('Quantity', 'commerce-manager'),
'price' => __('Price', 'commerce-manager'),
'shipping_cost' => __('Shipping cost', 'commerce-manager'),
'amount' => __('Amount', 'commerce-manager'),
'payment_mode' => __('Payment mode', 'commerce-manager'),
'transaction_number' => __('Transaction number', 'commerce-manager'),
'status' => __('Status', 'commerce-manager'),
'refund_date' => __('Refund date', 'commerce-manager'),
'refund_date_utc' => __('Refund date (UTC)', 'commerce-manager'),
'referrer' => __('Referrer', 'commerce-manager'),
'commission_amount' => __('Commission amount', 'commerce-manager'),
'commission_payment' => __('Commission payment', 'commerce-manager'),
'commission_status' => __('Commission status', 'commerce-manager'),
'commission_payment_date' => __('Commission\'s payment date', 'commerce-manager'),
'commission_payment_date_utc' => __('Commission\'s payment date (UTC)', 'commerce-manager'));

$_GET['columns_widths'] = array(
'id' => 5,
'first_name' => 12,
'last_name' => 12,
'email_address' => 15,
'website_name' => 15,
'website_url' => 18,
'address' => 15,
'postcode' => 9,
'town' => 12,
'country' => 12,
'phone_number' => 12,
'date' => 18,
'date_utc' => 18,
'user_agent' => 24,
'ip_address' => 12,
'referring_url' => 18,
'product_id' => 9,
'quantity' => 9,
'price' => 9,
'shipping_cost' => 9,
'amount' => 12,
'payment_mode' => 12,
'transaction_number' => 15,
'status' => 12,
'refund_date' => 18,
'refund_date_utc' => 18,
'referrer' => 12,
'commission_amount' => 12,
'commission_payment' => 12,
'commission_status' => 12,
'commission_payment_date' => 18,
'commission_payment_date_utc' => 18);

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
'shipping_cost' => __('the shipping cost', 'commerce-manager'),
'amount' => __('the amount', 'commerce-manager'),
'payment_mode' => __('the payment mode', 'commerce-manager'),
'transaction_number' => __('the transaction number', 'commerce-manager'),
'status' => __('the status', 'commerce-manager'),
'refund_date' => __('the refund date', 'commerce-manager'),
'refund_date_utc' => __('the refund date (UTC)', 'commerce-manager'),
'referrer' => __('the referrer', 'commerce-manager'),
'commission_amount' => __('the commission amount', 'commerce-manager'),
'commission_payment' => __('the commission  payment', 'commerce-manager'),
'commission_status' => __('the commission status', 'commerce-manager'),
'commission_payment_date' => __('the commission\'s payment date', 'commerce-manager'),
'commission_payment_date_utc' => __('the commission\'s payment date (UTC)', 'commerce-manager'));

include 'list-pages.php';