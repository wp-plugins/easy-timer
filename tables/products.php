<?php global $wpdb;
$option_name = 'commerce_manager_products';
$table_name = $wpdb->prefix.'commerce_manager_products';
$no_items = __('No products', 'commerce-manager');
$_GET['singular'] = __('product', 'commerce-manager');
$_GET['plural'] = __('products', 'commerce-manager');

$_GET['columns_names'] = array(
'id' => __('ID', 'commerce-manager'),
'name' => __('Name', 'commerce-manager'),
'price' => __('Price', 'commerce-manager'),
'reference' => __('Reference', 'commerce-manager'),
'description' => __('Description', 'commerce-manager'),
'available_quantity' => __('Available quantity', 'commerce-manager'),
'sales_count' => __('Sales count', 'commerce-manager'),
'refunds_count' => __('Refunds count', 'commerce-manager'),
'shipping_cost' => __('Shipping cost', 'commerce-manager'),
'thumbnail_url' => __('Thumbnail URL', 'commerce-manager'),
'url' => __('URL', 'commerce-manager'),
'downloadable' => __('Downloadable', 'commerce-manager'),
'download_url' => __('Download URL', 'commerce-manager'),
'instructions' => __('Instructions', 'commerce-manager'),
'paypal_email_address' => __('PayPal email address', 'commerce-manager'),
'purchase_button_url' => __('Purchase button URL', 'commerce-manager'),
'order_confirmation_url' => __('Order confirmation URL', 'commerce-manager'),
'email_sent_to_customer' => __('Email sent to customer', 'commerce-manager'),
'email_to_customer_sender' => __('Sender of the email sent to customer', 'commerce-manager'),
'email_to_customer_subject' => __('Subject of the email sent to customer', 'commerce-manager'),
'email_to_customer_body' => __('Body of the email sent to customer', 'commerce-manager'),
'email_sent_to_seller' => __('Email sent to seller', 'commerce-manager'),
'email_to_seller_receiver' => __('Receiver of the email sent to seller', 'commerce-manager'),
'email_to_seller_subject' => __('Subject of the email sent to seller', 'commerce-manager'),
'email_to_seller_body' => __('Body of the email sent to seller', 'commerce-manager'),
'customers_subscribed_to_aweber_list' => __('Customers subscribed to an AWeber list', 'commerce-manager'),
'customers_aweber_list' => __('Customers AWeber list', 'commerce-manager'),
'affiliation_enabled' => __('Affiliation enabled', 'commerce-manager'),
'commission_type' => __('Commission type', 'commerce-manager'),
'commission_amount' => __('Commission amount', 'commerce-manager'),
'commission_percentage' => __('Commission percentage', 'commerce-manager'),
'commission_payment' => __('Commission payment', 'commerce-manager'),
'registration_required' => __('Registration required', 'commerce-manager'),
'date' => __('Launch date', 'commerce-manager'),
'date_utc' => __('Launch date (UTC)', 'commerce-manager'));

$_GET['columns_widths'] = array(
'id' => 5,
'name' => 18,
'price' => 9,
'reference' => 12,
'description' => 18,
'available_quantity' => 12,
'sales_count' => 12,
'refunds_count' => 12,
'shipping_cost' => 12,
'thumbnail_url' => 18,
'url' => 18,
'downloadable' => 9,
'download_url' => 18,
'instructions' => 18,
'paypal_email_address' => 15,
'purchase_button_url' => 18,
'order_confirmation_url' => 18,
'email_sent_to_customer' => 15,
'email_to_customer_sender' => 15,
'email_to_customer_subject' => 15,
'email_to_customer_body' => 18,
'email_sent_to_seller' => 15,
'email_to_seller_receiver' => 15,
'email_to_seller_subject' => 15,
'email_to_seller_body' => 18,
'customers_subscribed_to_aweber_list' => 18,
'customers_aweber_list' => 15,
'affiliation_enabled' => 12,
'commission_type' => 12,
'commission_amount' => 12,
'commission_percentage' => 12,
'commission_payment' => 12,
'registration_required' => 12,
'date' => 18,
'date_utc' => 18);