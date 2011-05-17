<?php $admin_email = get_option('admin_email');
$blogname = get_option('blogname');
$siteurl = get_option('siteurl');

$commerce_manager_default_options = array(
'currency_code' => __('USD', 'commerce-manager'),
'customers_aweber_list' => '',
'customers_subscribed_to_aweber_list' => 'no',
'email_sent_to_customer' => 'yes',
'email_sent_to_seller' => 'yes',
'email_to_customer_sender' => $blogname.' <'.$admin_email.'>',
'email_to_customer_subject' => __('Your Order', 'commerce-manager'),
'email_to_seller_receiver' => $blogname.' <'.$admin_email.'>',
'email_to_seller_subject' => __('Order Notification', 'commerce-manager').' ([product name])',
'order_confirmation_url' => HOME_URL,
'paypal_email_address' => $admin_email,
'purchase_button_url' => COMMERCE_MANAGER_URL.'images/'.__('en', 'commerce-manager').'/purchase-180.png');

$commerce_manager_options = get_option('commerce_manager');
foreach ($commerce_manager_default_options as $key => $value) {
if ($commerce_manager_options[$key] == '') { $commerce_manager_options[$key] = $commerce_manager_default_options[$key]; } }
update_option('commerce_manager', $commerce_manager_options);


add_option('commerce_manager_email_to_customer_body',
__('Thank you for your order', 'commerce-manager').', [customer first-name].

[product instructions]

--
'.$blogname.'
'.HOME_URL);


add_option('commerce_manager_email_to_seller_body',
__('Product', 'commerce-manager').': [product name] ([product price] [commerce-manager currency-code])
'.__('Amount', 'commerce-manager').': [order amount] [commerce-manager currency-code]
'.__('Buyer', 'commerce-manager').': [customer first-name] [customer last-name] <[customer email-address]>

'.__('More informations about this order:', 'commerce-manager').'

'.$siteurl.'/wp-admin/admin.php?page=commerce-manager-order&id=[order id]');


$commerce_manager_orders_default_options = array(
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
'shipping_cost',
'payment_mode',
'transaction_number',
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

$commerce_manager_orders_options = get_option('commerce_manager_orders');
foreach ($commerce_manager_orders_default_options as $key => $value) {
if ($commerce_manager_orders_options[$key] == '') {
$commerce_manager_orders_options[$key] = $commerce_manager_orders_default_options[$key]; } }
update_option('commerce_manager_orders', $commerce_manager_orders_options);


$commerce_manager_products_default_options = array(
'columns' => array(
'id',
'name',
'price',
'reference',
'description',
'available_quantity',
'sales_count',
'refunds_count',
'shipping_cost',
'thumbnail_url',
'url',
'downloadable',
'download_url',
'instructions',
'paypal_email_address',
'purchase_button_url',
'order_confirmation_url',
'email_sent_to_customer',
'email_to_customer_sender',
'email_to_customer_subject',
'email_to_customer_body',
'email_sent_to_seller',
'email_to_seller_receiver',
'email_to_seller_subject',
'email_to_seller_body',
'customers_subscribed_to_aweber_list',
'customers_aweber_list',
'affiliation_enabled',
'commission_type',
'commission_amount',
'commission_percentage',
'commission_payment',
'registration_required',
'date',
'date_utc'),
'columns_number' => 8,
'limit' => 20,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '');

$commerce_manager_products_options = get_option('commerce_manager_products');
foreach ($commerce_manager_products_default_options as $key => $value) {
if ($commerce_manager_products_options[$key] == '') {
$commerce_manager_products_options[$key] = $commerce_manager_products_default_options[$key]; } }
update_option('commerce_manager_products', $commerce_manager_products_options);


$commerce_manager_statistics_default_options = array(
'filterby' => 'product_id',
'start_date' => '2011-01-01',
'tables' => array('orders', 'products'),
'tables_number' => 2);

$commerce_manager_statistics_options = get_option('commerce_manager_statistics');
foreach ($commerce_manager_statistics_default_options as $key => $value) {
if ($commerce_manager_statistics_options[$key] == '') {
$commerce_manager_statistics_options[$key] = $commerce_manager_statistics_default_options[$key]; } }
update_option('commerce_manager_statistics', $commerce_manager_statistics_options);


include_once(ABSPATH.'wp-admin/includes/upgrade.php');
global $wpdb;
$orders_table_name = $wpdb->prefix.'commerce_manager_orders';
$products_table_name = $wpdb->prefix.'commerce_manager_products';
if (!empty($wpdb->charset)) { $charset_collate = 'DEFAULT CHARACTER SET '.$wpdb->charset; }
if (!empty($wpdb->collate)) { $charset_collate .= ' COLLATE '.$wpdb->collate; }

$sql = "CREATE TABLE ".$orders_table_name." (
	id int auto_increment,
	first_name text NOT NULL,
	last_name text NOT NULL,
	email_address text NOT NULL,
	website_name text NOT NULL,
	website_url text NOT NULL,
	address text NOT NULL,
	postcode text NOT NULL,
	town text NOT NULL,
	country text NOT NULL,
	phone_number text NOT NULL,
	date datetime NOT NULL,
	date_utc datetime NOT NULL,
	user_agent text NOT NULL,
	ip_address text NOT NULL,
	referring_url text NOT NULL,
	product_id int NOT NULL default 1,
	quantity int NOT NULL default 1,
	price double NOT NULL,
	shipping_cost double NOT NULL,
	amount double NOT NULL,
	payment_mode text NOT NULL,
	transaction_number text NOT NULL,
	status text NOT NULL,
	refund_date datetime NOT NULL,
	refund_date_utc datetime NOT NULL,
	referrer text NOT NULL,
	commission_amount double NOT NULL,
	commission_payment text NOT NULL,
	commission_status text NOT NULL,
	commission_payment_date datetime NOT NULL,
	commission_payment_date_utc datetime NOT NULL,
	PRIMARY KEY  (id)
) $charset_collate;"; dbDelta($sql);

$sql = "CREATE TABLE ".$products_table_name." (
	id int auto_increment,
	name text NOT NULL,
	price text NOT NULL,
	shipping_cost text NOT NULL,
	reference text NOT NULL,
	thumbnail_url text NOT NULL,
	description text NOT NULL,
	url text NOT NULL,
	downloadable text NOT NULL,
	download_url text NOT NULL,
	instructions text NOT NULL,
	available_quantity text NOT NULL,
	sales_count int NOT NULL,
	refunds_count int NOT NULL,
	paypal_email_address text NOT NULL,
	purchase_button_url text NOT NULL,
	order_confirmation_url text NOT NULL,
	email_sent_to_customer text NOT NULL,
	email_to_customer_sender text NOT NULL,
	email_to_customer_subject text NOT NULL,
	email_to_customer_body text NOT NULL,
	email_sent_to_seller text NOT NULL,
	email_to_seller_receiver text NOT NULL,
	email_to_seller_subject text NOT NULL,
	email_to_seller_body text NOT NULL,
	customers_subscribed_to_aweber_list text NOT NULL,
	customers_aweber_list text NOT NULL,
	affiliation_enabled text NOT NULL,
	commission_type text NOT NULL,
	commission_amount text NOT NULL,
	commission_percentage text NOT NULL,
	commission_payment text NOT NULL,
	registration_required text NOT NULL,
	date datetime NOT NULL,
	date_utc datetime NOT NULL,
	PRIMARY KEY  (id)
) $charset_collate;"; dbDelta($sql);