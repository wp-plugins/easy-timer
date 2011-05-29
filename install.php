<?php include_once 'initial-options.php';

$commerce_manager_options = get_option('commerce_manager');
foreach ($commerce_manager_initial_options as $key => $value) {
if ($commerce_manager_options[$key] == '') { $commerce_manager_options[$key] = $commerce_manager_initial_options[$key]; } }
update_option('commerce_manager', $commerce_manager_options);

add_option('commerce_manager_email_to_customer_body', $commerce_manager_initial_email_to_customer_body);
add_option('commerce_manager_email_to_seller_body', $commerce_manager_initial_email_to_seller_body);

$commerce_manager_orders_options = get_option('commerce_manager_orders');
foreach ($commerce_manager_orders_initial_options as $key => $value) {
if ($commerce_manager_orders_options[$key] == '') {
$commerce_manager_orders_options[$key] = $commerce_manager_orders_initial_options[$key]; } }
update_option('commerce_manager_orders', $commerce_manager_orders_options);

$commerce_manager_products_options = get_option('commerce_manager_products');
foreach ($commerce_manager_products_initial_options as $key => $value) {
if ($commerce_manager_products_options[$key] == '') {
$commerce_manager_products_options[$key] = $commerce_manager_products_initial_options[$key]; } }
update_option('commerce_manager_products', $commerce_manager_products_options);

$commerce_manager_statistics_options = get_option('commerce_manager_statistics');
foreach ($commerce_manager_statistics_initial_options as $key => $value) {
if ($commerce_manager_statistics_options[$key] == '') {
$commerce_manager_statistics_options[$key] = $commerce_manager_statistics_initial_options[$key]; } }
update_option('commerce_manager_statistics', $commerce_manager_statistics_options);


include_once ABSPATH.'wp-admin/includes/upgrade.php';
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
	tax double NOT NULL,
	tax_included_in_price text NOT NULL,
	shipping_cost double NOT NULL,
	amount double NOT NULL,
	payment_mode text NOT NULL,
	transaction_number text NOT NULL,
	instructions text NOT NULL,
	shipping_address text NOT NULL,
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
	tax_applied text NOT NULL,
	tax_included_in_price text NOT NULL,
	tax_percentage text NOT NULL,
	shipping_address_required text NOT NULL,
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
	sandbox_enabled text NOT NULL,
	paypal_email_address text NOT NULL,
	purchase_button_url text NOT NULL,
	purchase_button_text text NOT NULL,
	order_confirmation_url text NOT NULL,
	orders_initial_status text NOT NULL,
	email_sent_to_customer text NOT NULL,
	email_to_customer_sender text NOT NULL,
	email_to_customer_receiver text NOT NULL,
	email_to_customer_subject text NOT NULL,
	email_to_customer_body text NOT NULL,
	email_sent_to_seller text NOT NULL,
	email_to_seller_sender text NOT NULL,
	email_to_seller_receiver text NOT NULL,
	email_to_seller_subject text NOT NULL,
	email_to_seller_body text NOT NULL,
	customer_subscribed_to_autoresponder text NOT NULL,
	customer_autoresponder text NOT NULL,
	customer_autoresponder_list text NOT NULL,
	customer_subscribed_to_autoresponder2 text NOT NULL,
	customer_autoresponder2 text NOT NULL,
	customer_autoresponder_list2 text NOT NULL,
	affiliation_enabled text NOT NULL,
	commission_type text NOT NULL,
	commission_amount text NOT NULL,
	commission_percentage text NOT NULL,
	commission_payment text NOT NULL,
	first_sale_winner text NOT NULL,
	registration_required text NOT NULL,
	date datetime NOT NULL,
	date_utc datetime NOT NULL,
	PRIMARY KEY  (id)
) $charset_collate;"; dbDelta($sql);

// Instructions provisoires
$results = $wpdb->query("ALTER TABLE $products_table_name DROP customers_subscribed_to_aweber_list");
$results = $wpdb->query("ALTER TABLE $products_table_name DROP customers_aweber_list");