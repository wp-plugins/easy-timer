<?php if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

global $wpdb;
$table_name = $wpdb->prefix.'commerce_manager_orders';
$selection_criteria = "WHERE commission_amount > 0";
if (isset($_GET['product_id'])) { $selection_criteria .= " AND product_id='".$_GET['product_id']."'"; }
if (isset($_GET['status'])) { $selection_criteria .= " AND status='".$_GET['status']."'"; }
if (isset($_GET['commission_payment'])) { $selection_criteria .= " AND commission_payment='".$_GET['commission_payment']."'"; }
if (isset($_GET['commission_status'])) { $selection_criteria .= " AND commission_status='".$_GET['commission_status']."'"; }
$no_items = __('No commissions', 'affiliation-manager');
$_GET['singular'] = __('commission', 'affiliation-manager');
$_GET['plural'] = __('commissions', 'affiliation-manager');

$_GET['columns_names'] = array(
'id' => __('ID', 'affiliation-manager'),
'first_name' => __('Customer\'s first name', 'affiliation-manager'),
'last_name' => __('Customer\'s last name', 'affiliation-manager'),
'email_address' => __('Customer\'s email address', 'affiliation-manager'),
'website_name' => __('Customer\'s website', 'affiliation-manager'),
'website_url' => __('Customer\'s website URL', 'affiliation-manager'),
'address' => __('Customer\'s address', 'affiliation-manager'),
'postcode' => __('Customer\'s postcode', 'affiliation-manager'),
'town' => __('Customer\'s town', 'affiliation-manager'),
'country' => __('Customer\'s country', 'affiliation-manager'),
'phone_number' => __('Customer\'s phone number', 'affiliation-manager'),
'date' => __('Date', 'affiliation-manager'),
'date_utc' => __('Date (UTC)', 'affiliation-manager'),
'user_agent' => __('Customer\'s user agent', 'affiliation-manager'),
'ip_address' => __('Customer\'s IP address', 'affiliation-manager'),
'referring_url' => __('Customer\'s referring URL', 'affiliation-manager'),
'product_id' => __('Product ID', 'affiliation-manager'),
'quantity' => __('Quantity', 'affiliation-manager'),
'price' => __('Price', 'affiliation-manager'),
'shipping_cost' => __('Shipping cost', 'affiliation-manager'),
'amount' => __('Order amount', 'affiliation-manager'),
'payment_mode' => __('Order\'s payment mode', 'affiliation-manager'),
'transaction_number' => __('Transaction number', 'affiliation-manager'),
'status' => __('Order status', 'affiliation-manager'),
'refund_date' => __('Order\'s refund date', 'affiliation-manager'),
'refund_date_utc' => __('Order\'s refund date (UTC)', 'affiliation-manager'),
'referrer' => __('Referrer', 'affiliation-manager'),
'commission_amount' => __('Amount', 'affiliation-manager'),
'commission_payment' => __('Payment', 'affiliation-manager'),
'commission_status' => __('Status', 'affiliation-manager'),
'commission_payment_date' => __('Payment date', 'affiliation-manager'),
'commission_payment_date_utc' => __('Payment date (UTC)', 'affiliation-manager'));

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
'status' => __('the order status', 'affiliation-manager'),
'refund_date' => __('the order\'s refund date', 'affiliation-manager'),
'refund_date_utc' => __('the order\'s refund date (UTC)', 'affiliation-manager'),
'referrer' => __('the referrer', 'affiliation-manager'),
'commission_amount' => __('the amount', 'affiliation-manager'),
'commission_payment' => __('the payment', 'affiliation-manager'),
'commission_status' => __('the status', 'affiliation-manager'),
'commission_payment_date' => __('the payment date', 'affiliation-manager'),
'commission_payment_date_utc' => __('the payment date (UTC)', 'affiliation-manager'));

include 'list-pages.php';