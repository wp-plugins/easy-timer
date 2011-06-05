<?php include 'tables.php';
$admin_email = get_option('admin_email');
$blogname = get_option('blogname');
$siteurl = get_option('siteurl');


$initial_options[''] = array(
'currency_code' => __('USD', 'commerce-manager'),
'customer_autoresponder' => '',
'customer_autoresponder2' => '',
'customer_autoresponder_list' => '',
'customer_autoresponder_list2' => '',
'customer_subscribed_to_autoresponder' => 'no',
'customer_subscribed_to_autoresponder2' => 'no',
'email_sent_to_customer' => 'yes',
'email_sent_to_seller' => 'yes',
'email_to_customer_receiver' => '[customer email-address]',
'email_to_customer_sender' => $blogname.' <'.$admin_email.'>',
'email_to_customer_subject' => __('Your Order', 'commerce-manager'),
'email_to_seller_receiver' => $admin_email,
'email_to_seller_sender' => $blogname.' <'.$admin_email.'>',
'email_to_seller_subject' => __('Order Notification', 'commerce-manager').' ([product name])',
'order_confirmation_url' => HOME_URL,
'orders_initial_status' => 'unprocessed',
'paypal_email_address' => $admin_email,
'purchase_button_text' => __('Purchase', 'commerce-manager'),
'purchase_button_url' => COMMERCE_MANAGER_URL.'images/'.__('en', 'commerce-manager').'/purchase-180.png',
'sandbox_enabled' => 'no',
'shipping_address_required' => 'no',
'shipping_cost' => 0,
'tax_applied' => 'no',
'tax_included_in_price' => 'no',
'tax_percentage' => 0);


$initial_options['email_to_customer_body'] =
__('Thank you for your order', 'commerce-manager').', [customer first-name].

[product instructions]

--
'.$blogname.'
'.HOME_URL;


$initial_options['email_to_seller_body'] =
__('Product', 'commerce-manager').': [product name] ([product price] [commerce-manager currency-code])
'.__('Amount', 'commerce-manager').': [order amount] [commerce-manager currency-code]
'.__('Buyer', 'commerce-manager').': [customer first-name] [customer last-name] <[customer email-address]>

'.__('More informations about this order:', 'commerce-manager').'

'.$siteurl.'/wp-admin/admin.php?page=commerce-manager-order&id=[order id]';


$first_columns = array(
'id',
'first_name',
'last_name',
'email_address',
'product_id',
'amount',
'date',
'status');
$last_columns = array();
foreach ($tables['orders'] as $key => $value) {
if ((!in_array($key, $first_columns)) && ($value['name'] != '')) { $last_columns[] = $key; } }

$initial_options['orders'] = array(
'columns' => array_merge($first_columns, $last_columns),
'columns_number' => 8,
'limit' => 10,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '',
'start_column' => 0);


$first_columns = array(
'id',
'name',
'price',
'reference',
'description',
'available_quantity',
'sales_count',
'refunds_count');
$last_columns = array();
foreach ($tables['products'] as $key => $value) {
if ((!in_array($key, $first_columns)) && ($value['name'] != '')) { $last_columns[] = $key; } }

$initial_options['products'] = array(
'columns' => array_merge($first_columns, $last_columns),
'columns_number' => 8,
'limit' => 10,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '',
'start_column' => 0);


$initial_options['statistics'] = array(
'filterby' => 'product_id',
'start_date' => '2011-01-01',
'start_table' => 0,
'tables' => array('orders', 'products'),
'tables_number' => 2);