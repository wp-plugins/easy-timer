<?php if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

global $wpdb;
$affiliates_table_name = $wpdb->prefix.'affiliation_manager_affiliates';
$orders_table_name = $wpdb->prefix.'commerce_manager_orders';
$products_table_name = $wpdb->prefix.'commerce_manager_products';
add_action('admin_footer', 'commerce_statistics_form_js');

if ((isset($_GET['id'])) && ($_GET['action'] == 'delete')) {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
$order_data = $wpdb->get_row("SELECT * FROM $orders_table_name WHERE id = '".$_GET['id']."'", OBJECT);
$product_data = $wpdb->get_row("SELECT * FROM $products_table_name WHERE id = '".$order_data->product_id."'", OBJECT);
if (is_numeric($product_data->available_quantity)) { $available_quantity = $product_data->available_quantity + $order_data->quantity; }
else { $available_quantity = 'unlimited'; }
$sales_count = $product_data->sales_count - $order_data->quantity;
if ($order_data->status == 'refunded') { $refunds_count = $product_data->refunds_count - $order_data->quantity; }
else { $refunds_count = $product_data->refunds_count; }
$results = $wpdb->query("UPDATE $products_table_name SET
	available_quantity = '".$available_quantity."',
	sales_count = '".$sales_count."',
	refunds_count = '".$refunds_count."' WHERE id = '".$order_data->product_id."'");
$results = $wpdb->query("DELETE FROM $orders_table_name WHERE id = '".$_GET['id']."'"); } ?>
<div class="wrap">
<div id="poststuff">
<?php commerce_manager_pages_top(); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Order deleted.', 'commerce-manager').'</strong></p></div>'; } ?>
<?php commerce_manager_pages_menu(); ?>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php _e('Do you really want to permanently delete this order?', 'commerce-manager'); ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'commerce-manager'); ?>" />
</div>
</form><?php } ?>
</div>
</div><?php }

else {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
$_POST = array_map('html_entity_decode', $_POST);
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
if ($_POST['date'] == '') {
$_POST['date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$date_utc = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['date']);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['date'] = date('Y-m-d H:i:s', $time);
$date_utc = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); }
$_POST['product_id'] = (int) $_POST['product_id']; if ($_POST['product_id'] < 1) { $_POST['product_id'] = 1; }
$_GET['product_data'] = $wpdb->get_row("SELECT * FROM $products_table_name WHERE id = '".$_POST['product_id']."'", OBJECT);
if (!$_GET['product_data']) { $_POST['product_id'] = 1; $_GET['product_data'] = $wpdb->get_row("SELECT * FROM $products_table_name WHERE id = 1", OBJECT); }
$_POST['quantity'] = (int) $_POST['quantity']; if ($_POST['quantity'] < 1) { $_POST['quantity'] = 1; }
$_POST['price'] = str_replace(array('?', ',', ';'), '.', $_POST['price']);
$_POST['price'] = (double) $_POST['price']; if ($_POST['price'] <= 0) { $_POST['price'] = (double) product_data('price'); }
$_POST['shipping_cost'] = str_replace(array('?', ',', ';'), '.', $_POST['shipping_cost']);
$_POST['shipping_cost'] = (double) $_POST['shipping_cost']; if ($_POST['shipping_cost'] <= 0) { $_POST['shipping_cost'] = (double) product_data('shipping_cost'); }
$_POST['amount'] = str_replace(array('?', ',', ';'), '.', $_POST['amount']);
$_POST['amount'] = (double) $_POST['amount']; if ($_POST['amount'] <= 0) { $_POST['amount'] = $_POST['quantity']*$_POST['price'] + $_POST['shipping_cost']; }
$_POST['payment_mode'] = 'PayPal';
if ($_POST['status'] == 'refunded') {
if ($_POST['refund_date'] == '') {
$_POST['refund_date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$refund_date_utc = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['refund_date']);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['refund_date'] = date('Y-m-d H:i:s', $time);
$refund_date_utc = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); } }
else { $_POST['refund_date'] = ''; }
$_POST['email_address'] = commerce_format_email_address($_POST['email_address']);
if ($_POST['referrer'] == '') {
$_POST['commission_amount'] = 0;
$_POST['commission_payment'] = '';
$_POST['commission_status'] = '';
$_POST['commission_payment_date'] = ''; }
else {
if (is_numeric($_POST['referrer'])) {
$_POST['referrer'] = preg_replace('/[^0-9]/', '', $_POST['referrer']);
$result = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE id='".$_POST['referrer']."'", OBJECT);
if ($result) { $_POST['referrer'] = $result->login; } }
elseif (strstr($_POST['referrer'], '@')) {
$_POST['referrer'] = commerce_format_email_address($_POST['referrer']);
$result = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE paypal_email_address='".$_POST['referrer']."'", OBJECT);
if ($result) { $_POST['referrer'] = $result->login; }
else { $result = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE email_address='".$_POST['referrer']."'", OBJECT);
if ($result) { $_POST['referrer'] = $result->login; } } }
else { $_POST['referrer'] = commerce_format_nice_name($_POST['referrer']); } }
$_POST['commission_amount'] = str_replace(array('?', ',', ';'), '.', $_POST['commission_amount']);
$_POST['commission_amount'] = (double) $_POST['commission_amount']; if ($_POST['commission_amount'] <= 0) { $_POST['commission_amount'] = 0; }
if ($_POST['commission_amount'] == 0) { $_POST['commission_payment'] = ''; }
elseif ($_POST['commission_payment'] == '') { $_POST['commission_payment'] = 'deferred'; }
if ($_POST['commission_payment'] == '') { $_POST['commission_status'] = ''; }
elseif ($_POST['commission_status'] == '') { $_POST['commission_status'] = 'unpaid'; }
if ($_POST['commission_status'] == 'paid') {
if ($_POST['commission_payment_date'] == '') {
$_POST['commission_payment_date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$commission_payment_date_utc = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['commission_payment_date']);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['commission_payment_date'] = date('Y-m-d H:i:s', $time);
$commission_payment_date_utc = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); } }
else { $_POST['commission_payment_date'] = ''; }

if (!isset($_GET['id'])) {
if (($_POST['email_address'] == '') || ($_POST['first_name'] == '') || ($_POST['last_name'] == '')) {
$error .= ' '.__('Please fill out the required fields.', 'commerce-manager'); }
if ($error == '') {
$result = $wpdb->get_row("SELECT * FROM $orders_table_name WHERE date = '".$_POST['date']."' AND product_id = '".$_POST['product_id']."' AND email_address = '".$_POST['email_address']."'", OBJECT);
if (!$result) {
$results = $wpdb->query("INSERT INTO $orders_table_name (id, first_name, last_name, email_address, website_name, website_url, address, postcode, town, country, phone_number, date, date_utc, user_agent, ip_address, referring_url, product_id, quantity, price, shipping_cost, amount, payment_mode, transaction_number, status, refund_date, refund_date_utc, referrer, commission_amount, commission_payment, commission_status, commission_payment_date, commission_payment_date_utc) VALUES(
	'',
	'".$_POST['first_name']."',
	'".$_POST['last_name']."',
	'".$_POST['email_address']."',
	'".$_POST['website_name']."',
	'".$_POST['website_url']."',
	'".$_POST['address']."',
	'".$_POST['postcode']."',
	'".$_POST['town']."',
	'".$_POST['country']."',
	'".$_POST['phone_number']."',
	'".$_POST['date']."',
	'".$date_utc."',
	'',
	'',
	'".$_SERVER['HTTP_REFERER']."',
	'".$_POST['product_id']."',
	'".$_POST['quantity']."',
	'".$_POST['price']."',
	'".$_POST['shipping_cost']."',
	'".$_POST['amount']."',
	'".$_POST['payment_mode']."',
	'".$_POST['transaction_number']."',
	'".$_POST['status']."',
	'".$_POST['refund_date']."',
	'".$refund_date_utc."',
	'".$_POST['referrer']."',
	'".$_POST['commission_amount']."',
	'".$_POST['commission_payment']."',
	'".$_POST['commission_status']."',
	'".$_POST['commission_payment_date']."',
	'".$commission_payment_date_utc."')");
	
if (is_numeric($_GET['product_data']->available_quantity)) { $available_quantity = $_GET['product_data']->available_quantity - $_POST['quantity']; }
else { $available_quantity = 'unlimited'; }
$sales_count = $_GET['product_data']->sales_count + $_POST['quantity'];
if ($_POST['status'] == 'refunded') { $refunds_count = $_GET['product_data']->refunds_count + $_POST['quantity']; }
else { $refunds_count = $_GET['product_data']->refunds_count; }
$results = $wpdb->query("UPDATE $products_table_name SET
	available_quantity = '".$available_quantity."',
	sales_count = '".$sales_count."',
	refunds_count = '".$refunds_count."' WHERE id = '".$_POST['product_id']."'");

if (($_POST['email_sent_to_customer'] == 'yes') || ($_POST['email_sent_to_seller'] == 'yes')) {
$_GET['order_data'] = $wpdb->get_row("SELECT * FROM $orders_table_name WHERE date = '".$_POST['date']."' AND product_id = '".$_POST['product_id']."' AND email_address = '".$_POST['email_address']."'", OBJECT);
$_POST = array_map('stripslashes', $_POST);
if ($_POST['email_sent_to_customer'] == 'yes') {
$receiver = $_POST['email_address'];
$subject = do_shortcode($_POST['email_to_customer_subject']);
$body = do_shortcode($_POST['email_to_customer_body']);
$sender = do_shortcode($_POST['email_to_customer_sender']);
$headers = 'From: '.$sender;
wp_mail($receiver, $subject, $body, $headers); }
if ($_POST['email_sent_to_seller'] == 'yes') {
$receiver = do_shortcode($_POST['email_to_seller_receiver']);
$subject = do_shortcode($_POST['email_to_seller_subject']);
$body = do_shortcode($_POST['email_to_seller_body']);
$sender = do_shortcode($_POST['email_to_customer_sender']);
$headers = 'From: '.$sender;
wp_mail($receiver, $subject, $body, $headers); } } } } }

if (isset($_GET['id'])) {
$order_data = $wpdb->get_row("SELECT * FROM $orders_table_name WHERE id = '".$_GET['id']."'", OBJECT);
if ($_POST['first_name'] != '') { $results = $wpdb->query("UPDATE $orders_table_name SET first_name = '".$_POST['first_name']."' WHERE id = '".$_GET['id']."'"); }
if ($_POST['last_name'] != '') { $results = $wpdb->query("UPDATE $orders_table_name SET last_name = '".$_POST['last_name']."' WHERE id = '".$_GET['id']."'"); }
if ($_POST['email_address'] != '') { $results = $wpdb->query("UPDATE $orders_table_name SET email_address = '".$_POST['email_address']."' WHERE id = '".$_GET['id']."'"); }

$results = $wpdb->query("UPDATE $orders_table_name SET
	first_name = '".$_POST['first_name']."',
	last_name = '".$_POST['last_name']."',
	email_address = '".$_POST['email_address']."',
	website_name = '".$_POST['website_name']."',
	website_url = '".$_POST['website_url']."',
	address = '".$_POST['address']."',
	postcode = '".$_POST['postcode']."',
	town = '".$_POST['town']."',
	country = '".$_POST['country']."',
	phone_number = '".$_POST['phone_number']."',
	date = '".$_POST['date']."',
	date_utc = '".$date_utc."',
	product_id = '".$_POST['product_id']."',
	quantity = '".$_POST['quantity']."',
	price = '".$_POST['price']."',
	shipping_cost = '".$_POST['shipping_cost']."',
	amount = '".$_POST['amount']."',
	transaction_number = '".$_POST['transaction_number']."',
	status = '".$_POST['status']."',
	refund_date = '".$_POST['refund_date']."',
	refund_date_utc = '".$refund_date_utc."',
	referrer = '".$_POST['referrer']."',
	commission_amount = '".$_POST['commission_amount']."',
	commission_payment = '".$_POST['commission_payment']."',
	commission_status = '".$_POST['commission_status']."',
	commission_payment_date = '".$_POST['commission_payment_date']."',
	commission_payment_date_utc = '".$commission_payment_date_utc."' WHERE id = '".$_GET['id']."'");
	
$row = $wpdb->get_row("SELECT SUM(quantity) AS total FROM $orders_table_name WHERE product_id = '".$_POST['product_id']."'", OBJECT);
$sales_count = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(quantity) AS total FROM $orders_table_name WHERE product_id = '".$_POST['product_id']."' AND status = 'refunded'", OBJECT);
$refunds_count = (int) $row->total;
if (is_numeric($_GET['product_data']->available_quantity)) {
if ($_POST['product_id'] == $order_data->product_id) { $available_quantity = $_GET['product_data']->available_quantity - $_POST['quantity'] + $order_data->quantity; }
else { $available_quantity = $_GET['product_data']->available_quantity - $_POST['quantity']; } }
else { $available_quantity = 'unlimited'; }
$results = $wpdb->query("UPDATE $products_table_name SET
	available_quantity = '".$available_quantity."',
	sales_count = '".$sales_count."',
	refunds_count = '".$refunds_count."' WHERE id = '".$_POST['product_id']."'");
	
if ($_POST['product_id'] != $order_data->product_id) {
$product_data = $wpdb->get_row("SELECT * FROM $products_table_name WHERE id = '".$order_data->product_id."'", OBJECT);
$row = $wpdb->get_row("SELECT SUM(quantity) AS total FROM $orders_table_name WHERE product_id = '".$order_data->product_id."'", OBJECT);
$sales_count = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(quantity) AS total FROM $orders_table_name WHERE product_id = '".$order_data->product_id."' AND status = 'refunded'", OBJECT);
$refunds_count = (int) $row->total;
if (is_numeric($product_data->available_quantity)) { $available_quantity = $product_data->available_quantity + $order_data->quantity; }
else { $available_quantity = 'unlimited'; }
$results = $wpdb->query("UPDATE $products_table_name SET
	available_quantity = '".$available_quantity."',
	sales_count = '".$sales_count."',
	refunds_count = '".$refunds_count."' WHERE id = '".$order_data->product_id."'"); } } }

if (isset($_GET['id'])) {
$order_data = $wpdb->get_row("SELECT * FROM $orders_table_name WHERE id = '".$_GET['id']."'", OBJECT);
if ($order_data) { foreach ($order_data as $key => $value) { $_POST[$key] = $order_data->$key; } }
elseif (!headers_sent()) { header('Location: admin.php?page=commerce-manager-order'); exit(); } }

$_POST = array_map('stripslashes', $_POST);
$_POST = array_map('htmlspecialchars', $_POST);
foreach ($_POST as $key => $value) {
$_POST[$key] = str_replace('&amp;amp;', '&amp;', $_POST[$key]);
if ($_POST[$key] == '0000-00-00 00:00:00') { $_POST[$key] = ''; } }
$commerce_manager_options = array_map('htmlspecialchars', get_option('commerce_manager'));
$currency_code = do_shortcode($commerce_manager_options['currency_code']); ?>

<div class="wrap">
<div id="poststuff">
<?php commerce_manager_pages_top(); ?>
<?php if ((isset($_POST['submit'])) && ($error == '')) { echo '<div class="updated"><p><strong>'.(isset($_GET['id']) ? __('Order updated.', 'commerce-manager') : __('Order saved.', 'commerce-manager')).'</strong></p></div>'; } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php commerce_manager_pages_menu(); ?>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<p class="description"><?php _e('Fields marked with * are required.', 'commerce-manager'); ?></p>
<div class="postbox">
<h3><?php _e('General informations', 'commerce-manager'); ?></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="date"><?php _e('Date', 'commerce-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="date" id="date" size="20" value="<?php echo (isset($_POST['date']) ? $_POST['date'] : date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET)); ?>" /></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="product_id"><?php _e('Product ID', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="product_id" id="product_id" rows="1" cols="25"><?php echo $_POST['product_id']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 1.', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="quantity"><?php _e('Quantity', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="quantity" id="quantity" rows="1" cols="25"><?php echo $_POST['quantity']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 1.', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="price"><?php _e('Price', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="price" id="price" rows="1" cols="25"><?php echo $_POST['price']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for the current product price.', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="shipping_cost"><?php _e('Shipping cost', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="shipping_cost" id="shipping_cost" rows="1" cols="25"><?php echo $_POST['shipping_cost']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for the current product shipping cost.', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="amount"><?php _e('Amount', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="amount" id="amount" rows="1" cols="25"><?php echo $_POST['amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank to automatically calculate the amount.', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="transaction_number"><?php _e('Transaction number', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="transaction_number" id="transaction_number" rows="1" cols="25"><?php echo $_POST['transaction_number']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="status"><?php _e('Status', 'commerce-manager'); ?></label></strong></th>
<td><select name="status" id="status">
<option value="unprocessed"<?php if ($_POST['status'] == 'unprocessed') { echo ' selected="selected"'; } ?>><?php _e('Unprocessed', 'commerce-manager'); ?></option>
<option value="processed"<?php if ($_POST['status'] == 'processed') { echo ' selected="selected"'; } ?>><?php _e('Processed', 'commerce-manager'); ?></option>
<option value="refunded"<?php if ($_POST['status'] == 'refunded') { echo ' selected="selected"'; } ?>><?php _e('Refunded', 'commerce-manager'); ?></option>
</select></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="refund_date"><?php _e('Refund date', 'commerce-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="refund_date" id="refund_date" size="20" value="<?php echo $_POST['refund_date']; ?>" />
<span class="description" style="vertical-align: 0%;"><?php _e('Leave this field blank if the order is not refunded, or for the current date if the order is refunded.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>
<div class="postbox">
<h3><?php _e('Customer', 'commerce-manager'); ?></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['first_name'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="first_name"><?php _e('First name', 'commerce-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="first_name" id="first_name" rows="1" cols="25"><?php echo $_POST['first_name']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['last_name'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="last_name"><?php _e('Last name', 'commerce-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="last_name" id="last_name" rows="1" cols="25"><?php echo $_POST['last_name']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['email_address'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="email_address"><?php _e('Email address', 'commerce-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="email_address" id="email_address" rows="1" cols="25"><?php echo $_POST['email_address']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="website_name"><?php _e('Website name', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="website_name" id="website_name" rows="1" cols="25"><?php echo $_POST['website_name']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="website_url"><?php _e('Website URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="website_url" id="website_url" rows="1" cols="50"><?php echo $_POST['website_url']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="address"><?php _e('Address', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="address" id="address" rows="1" cols="25"><?php echo $_POST['address']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="postcode"><?php _e('Postcode', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="postcode" id="postcode" rows="1" cols="25"><?php echo $_POST['postcode']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="town"><?php _e('Town', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="town" id="town" rows="1" cols="25"><?php echo $_POST['town']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="country"><?php _e('Country', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="country" id="country" rows="1" cols="25"><?php echo $_POST['country']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="phone_number"><?php _e('Phone number', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="phone_number" id="phone_number" rows="1" cols="25"><?php echo $_POST['phone_number']; ?></textarea></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>
<div class="postbox">
<h3><?php _e('Affiliation', 'commerce-manager'); ?></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php echo (function_exists('affiliation_manager_admin_menu') ? '<a href="admin.php?page=affiliation-manager">'.__('Click here to configure the default options.', 'commerce-manager').'</a>' : __('To use affiliation, you must have installed and activated <a href="http://www.kleor-editions.com/affiliation-manager">Affiliation Manager</a>.', 'commerce-manager')); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="referrer"><?php _e('Referrer', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="referrer" id="referrer" rows="1" cols="25"><?php echo $_POST['referrer']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Affiliate who referred this order (ID, login name or email address)', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="commission_amount"><?php _e('Commission amount', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_amount" id="commission_amount" rows="1" cols="25"><?php echo $_POST['commission_amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 0.', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="commission_payment"><?php _e('Commission payment', 'commerce-manager'); ?></label></strong></th>
<td><select name="commission_payment" id="commission_payment">
<option value=""<?php if ($_POST['commission_payment'] == '') { echo ' selected="selected"'; } ?>><?php _e('None', 'commerce-manager'); ?></option>
<option value="deferred"<?php if ($_POST['commission_payment'] == 'deferred') { echo ' selected="selected"'; } ?>><?php _e('Deferred', 'commerce-manager'); ?></option>
<option value="instant"<?php if ($_POST['commission_payment'] == 'instant') { echo ' selected="selected"'; } ?>><?php _e('Instant', 'commerce-manager'); ?></option>
</select></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="commission_status"><?php _e('Commission status', 'commerce-manager'); ?></label></strong></th>
<td><select name="commission_status" id="commission_status">
<option value=""<?php if ($_POST['commission_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('None', 'commerce-manager'); ?></option>
<option value="unpaid"<?php if ($_POST['commission_status'] == 'unpaid') { echo ' selected="selected"'; } ?>><?php _e('Unpaid', 'commerce-manager'); ?></option>
<option value="paid"<?php if ($_POST['commission_status'] == 'paid') { echo ' selected="selected"'; } ?>><?php _e('Paid', 'commerce-manager'); ?></option>
</select></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="commission_payment_date"><?php _e('Commission payment date', 'commerce-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="commission_payment_date" id="commission_payment_date" size="20" value="<?php echo $_POST['commission_payment_date']; ?>" />
<span class="description" style="vertical-align: 0%;"><?php _e('Leave this field blank if the commission is not paid, or for the current date if the commission is paid.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>
<?php if (!isset($_GET['id'])) {
if (!isset($_POST['submit'])) { $commerce_manager_options = array_map('htmlspecialchars', get_option('commerce_manager')); } ?>
<div class="postbox">
<h3><?php _e('Email sent to customer', 'commerce-manager'); ?></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="email_sent_to_customer" id="email_sent_to_customer" value="yes"<?php if (isset($_POST['submit'])) { if ($_POST['email_sent_to_customer'] == 'yes') { echo ' checked="checked"'; } } else { if ($commerce_manager_options['email_sent_to_customer'] == 'yes') { echo ' checked="checked"'; } } ?> /> <label for="email_sent_to_customer"><?php _e('Send an order confirmation email to the customer', 'commerce-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_customer_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_customer_sender" id="email_to_customer_sender" rows="1" cols="75"><?php if (isset($_POST['submit'])) { echo $_POST['email_to_customer_sender']; } else { echo $commerce_manager_options['email_to_customer_sender']; } ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_customer_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_customer_subject" id="email_to_customer_subject" rows="1" cols="75"><?php if (isset($_POST['submit'])) { echo $_POST['email_to_customer_subject']; } else { echo $commerce_manager_options['email_to_customer_subject']; } ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_customer_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; height: 20%; width: 75%;" name="email_to_customer_body" id="email_to_customer_body" rows="15" cols="75"><?php if (isset($_POST['submit'])) { echo $_POST['email_to_customer_body']; } else { echo htmlspecialchars(get_option('commerce_manager_email_to_customer_body')); } ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3><?php _e('Email sent to seller', 'commerce-manager'); ?></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="email_sent_to_seller" id="email_sent_to_seller" value="yes"<?php if (isset($_POST['submit'])) { if ($_POST['email_sent_to_seller'] == 'yes') { echo ' checked="checked"'; } } else { if ($commerce_manager_options['email_sent_to_seller'] == 'yes') { echo ' checked="checked"'; } } ?> /> <label for="email_sent_to_seller"><?php _e('Send an order notification email to the seller', 'commerce-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_seller_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_seller_receiver" id="email_to_seller_receiver" rows="1" cols="75"><?php if (isset($_POST['submit'])) { echo $_POST['email_to_seller_receiver']; } else { echo $commerce_manager_options['email_to_seller_receiver']; } ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_seller_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_seller_subject" id="email_to_seller_subject" rows="1" cols="75"><?php if (isset($_POST['submit'])) { echo $_POST['email_to_seller_subject']; } else { echo $commerce_manager_options['email_to_seller_subject']; } ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_seller_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; height: 20%; width: 75%;" name="email_to_seller_body" id="email_to_seller_body" rows="15" cols="75"><?php if (isset($_POST['submit'])) { echo $_POST['email_to_seller_body']; } else { echo htmlspecialchars(get_option('commerce_manager_email_to_seller_body')); } ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3><?php _e('Autoresponder', 'commerce-manager'); ?></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="customer_subscribed_to_aweber_list" id="customer_subscribed_to_aweber_list" value="yes"<?php if (isset($_POST['submit'])) { if ($_POST['customer_subscribed_to_aweber_list'] == 'yes') { echo ' checked="checked"'; } } else { if ($commerce_manager_options['customers_subscribed_to_aweber_list'] == 'yes') { echo ' checked="checked"'; } } ?> /> <label for="customer_subscribed_to_aweber_list"><?php _e('Subscribe customer to an AWeber list', 'commerce-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="customer_aweber_list"><?php _e('AWeber list', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="customer_aweber_list" id="customer_aweber_list" rows="1" cols="25"><?php if (isset($_POST['submit'])) { echo $_POST['customer_aweber_list']; } else { echo $commerce_manager_options['customers_aweber_list']; } ?></textarea></td></tr>
</tbody></table>
</div></div>
<?php } ?>
<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php (isset($_GET['id']) ?  _e('Save Changes', 'commerce-manager') : _e('Save Order', 'commerce-manager')); ?>" /></p>
</form>
</div>
</div>
<?php }