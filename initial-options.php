<?php $admin_email = get_option('admin_email');
$blogname = get_option('blogname');
$siteurl = get_option('siteurl');


$commerce_manager_initial_options = array(
'currency_code' => __('USD', 'commerce-manager'),
'customer_autoresponder' => '',
'customer_autoresponder2' => '',
'customer_autoresponder_list' => '',
'customer_autoresponder_list2' => '',
'customer_subscribed_to_autoresponder' => 'no',
'customer_subscribed_to_autoresponder2' => 'no',
'email_sent_to_customer' => 'yes',
'email_sent_to_seller' => 'yes',
'email_to_customer_receiver' => '[customer first-name] [customer last-name] <[customer email-address]>',
'email_to_customer_sender' => $blogname.' <'.$admin_email.'>',
'email_to_customer_subject' => __('Your Order', 'commerce-manager'),
'email_to_seller_receiver' => $blogname.' <'.$admin_email.'>',
'email_to_seller_sender' => $blogname.' <'.$admin_email.'>',
'email_to_seller_subject' => __('Order Notification', 'commerce-manager').' ([product name])',
'order_confirmation_url' => HOME_URL,
'orders_initial_status' => 'unprocessed',
'paypal_email_address' => $admin_email,
'purchase_button_text' => __('Purchase', 'commerce-manager'),
'purchase_button_url' => COMMERCE_MANAGER_URL.'images/'.__('en', 'commerce-manager').'/purchase-180.png',
'sandbox_enabled' => 'no',
'shipping_address_required' => 'yes',
'shipping_cost' => 0,
'tax_applied' => 'no',
'tax_included_in_price' => 'yes',
'tax_percentage' => 0);


$commerce_manager_initial_email_to_customer_body =
__('Thank you for your order', 'commerce-manager').', [customer first-name].

[product instructions]

--
'.$blogname.'
'.HOME_URL;


$commerce_manager_initial_email_to_seller_body =
__('Product', 'commerce-manager').': [product name] ([product price] [commerce-manager currency-code])
'.__('Amount', 'commerce-manager').': [order amount] [commerce-manager currency-code]
'.__('Buyer', 'commerce-manager').': [customer first-name] [customer last-name] <[customer email-address]>

'.__('More informations about this order:', 'commerce-manager').'

'.$siteurl.'/wp-admin/admin.php?page=commerce-manager-order&id=[order id]';


$commerce_manager_orders_initial_options = array(
'columns' => array(
'id',
'first_name',
'last_name',
'email_address',
'date',
'product_id',
'amount',
'status',
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
'tax',
'tax_included_in_price',
'shipping_cost',
'payment_mode',
'transaction_number',
'instructions',
'shipping_address',
'refund_date',
'refund_date_utc',
'referrer',
'commission_amount',
'commission_payment',
'commission_status',
'commission_payment_date',
'commission_payment_date_utc'),
'columns_number' => 8,
'limit' => 20,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '');


$commerce_manager_products_initial_options = array(
'columns' => array(
'id',
'name',
'price',
'reference',
'description',
'available_quantity',
'sales_count',
'refunds_count',
'tax_applied',
'tax_included_in_price',
'tax_percentage',
'shipping_address_required',
'shipping_cost',
'thumbnail_url',
'url',
'downloadable',
'download_url',
'instructions',
'sandbox_enabled',
'paypal_email_address',
'purchase_button_url',
'purchase_button_text',
'order_confirmation_url',
'orders_initial_status',
'email_sent_to_customer',
'email_to_customer_sender',
'email_to_customer_receiver',
'email_to_customer_subject',
'email_to_customer_body',
'email_sent_to_seller',
'email_to_seller_sender',
'email_to_seller_receiver',
'email_to_seller_subject',
'email_to_seller_body',
'customer_subscribed_to_autoresponder',
'customer_autoresponder',
'customer_autoresponder_list',
'customer_subscribed_to_autoresponder2',
'customer_autoresponder2',
'customer_autoresponder_list2',
'affiliation_enabled',
'commission_type',
'commission_amount',
'commission_percentage',
'commission_payment',
'first_sale_winner',
'registration_required',
'date',
'date_utc'),
'columns_number' => 8,
'limit' => 20,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '');


$commerce_manager_statistics_initial_options = array(
'filterby' => 'product_id',
'start_date' => '2011-01-01',
'tables' => array('orders', 'products'),
'tables_number' => 2);