<?php $commerce_manager_default_options = array(
'currency_code' => __('USD', 'commerce-manager'),
'customers_aweber_list' => '',
'customers_subscribed_to_aweber_list' => 'no',
'email_sent_to_customer' => 'yes',
'email_sent_to_seller' => 'yes',
'email_to_customer_sender' => get_option('blogname').' <'.get_option('admin_email').'>',
'email_to_customer_subject' => __('Your Order', 'commerce-manager'),
'email_to_seller_receiver' => get_option('blogname').' <'.get_option('admin_email').'>',
'email_to_seller_subject' => __('Order Notification', 'commerce-manager').' ([product name])',
'order_confirmation_url' => get_option('home'),
'paypal_email_address' => get_option('admin_email'),
'purchase_button_url' => COMMERCE_MANAGER_URL.'images/'.__('en', 'commerce-manager').'/purchase-180.png');

$commerce_manager_options = get_option('commerce_manager');
foreach ($commerce_manager_default_options as $key => $value) {
if ($commerce_manager_options[$key] == '') { $commerce_manager_options[$key] = $commerce_manager_default_options[$key]; } }
update_option('commerce_manager', $commerce_manager_options);


add_option('commerce_manager_email_to_customer_body', __('Thank you for your order', 'commerce-manager').', [customer first-name].

[product instructions]

--
'.get_option('blogname').'
'.get_option('home'));


add_option('commerce_manager_email_to_seller_body', __('Product', 'commerce-manager').': [product name] ([product price] [product currency-code])
'.__('Buyer', 'commerce-manager').': [customer first-name] [customer last-name] <[customer email-address]>

'.__('More informations about this order', 'commerce-manager').':

'.get_option('siteurl').'/wp-admin/admin.php?page=commerce-manager-order&id=[order id]');


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
	company text NOT NULL,
	website text NOT NULL,
	street text NOT NULL,
	postcode text NOT NULL,
	town text NOT NULL,
	country text NOT NULL,
	phone_number text NOT NULL,
	date datetime NOT NULL,
	date_utc datetime NOT NULL,
	user_agent text NOT NULL,
	ip_address text NOT NULL,
	referring_url text NOT NULL,
	product_id int NOT NULL,
	quantity int NOT NULL default 1,
	price double NOT NULL,
	shipping_cost double NOT NULL,
	amount double NOT NULL,
	payment_mode text NOT NULL,
	transaction_number text NOT NULL,
	processed text NOT NULL,
	refunded text NOT NULL,
	refund_date datetime NOT NULL,
	refund_date_utc datetime NOT NULL,
	referrer text NOT NULL,
	commission_amount double NOT NULL,
	commission_payment text NOT NULL,
	commission_paid text NOT NULL,
	commission_payment_date datetime NOT NULL,
	commission_payment_date_utc datetime NOT NULL,
	PRIMARY KEY  (id)
) $charset_collate;"; dbDelta($sql);

$sql = "CREATE TABLE ".$products_table_name." (
	id int auto_increment,
	name text NOT NULL,
	price text NOT NULL,
	shipping_cost text NOT NULL,
	currency_code text NOT NULL,
	reference text NOT NULL,
	thumbnail_url text NOT NULL,
	description text NOT NULL,
	url text NOT NULL,
	downloadable text NOT NULL,
	download_url text NOT NULL,
	instructions text NOT NULL,
	available_quantity int NOT NULL,
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
	commission_percentage text NOT NULL,
	commission_payment text NOT NULL,
	commission_type text NOT NULL,
	commission_amount text NOT NULL,
	registration_required text NOT NULL,
	first_sale_winner text NOT NULL,
	PRIMARY KEY  (id)
) $charset_collate;"; dbDelta($sql);