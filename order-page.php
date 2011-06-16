<?php global $wpdb;
$affiliates_table_name = $wpdb->prefix.'affiliation_manager_affiliates';
$orders_table_name = $wpdb->prefix.'commerce_manager_orders';
$products_table_name = $wpdb->prefix.'commerce_manager_products';
add_action('admin_footer', 'commerce_statistics_form_js');

if ((isset($_GET['id'])) && ($_GET['action'] == 'delete')) {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
$order_data = $wpdb->get_row("SELECT * FROM $orders_table_name WHERE id = '".$_GET['id']."'", OBJECT);
$results = $wpdb->query("DELETE FROM $orders_table_name WHERE id = '".$_GET['id']."'");
$product_data = $wpdb->get_row("SELECT * FROM $products_table_name WHERE id = '".$order_data->product_id."'", OBJECT);
if (is_numeric($product_data->available_quantity)) { $available_quantity = $product_data->available_quantity + $order_data->quantity; }
else { $available_quantity = 'unlimited'; }
$row = $wpdb->get_row("SELECT SUM(quantity) AS total FROM $orders_table_name WHERE product_id = '".$product_data->id."'", OBJECT);
$sales_count = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(quantity) AS total FROM $orders_table_name WHERE product_id = '".$product_data->id."' AND status = 'refunded'", OBJECT);
$refunds_count = (int) $row->total;
$results = $wpdb->query("UPDATE $products_table_name SET
	available_quantity = '".$available_quantity."',
	sales_count = '".$sales_count."',
	refunds_count = '".$refunds_count."' WHERE id = '".$product_data->id."'"); } ?>
<div class="wrap">
<div id="poststuff">
<?php commerce_manager_pages_top(); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Order deleted.', 'commerce-manager').'</strong></p></div>'; } ?>
<?php commerce_manager_pages_menu(); ?>
<div class="clear"></div>
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
$_POST['product_id'] = (int) $_POST['product_id']; if ($_POST['product_id'] < 1) { $_POST['product_id'] = 1; }
$_GET['product_data'] = $wpdb->get_row("SELECT * FROM $products_table_name WHERE id = '".$_POST['product_id']."'", OBJECT);
if (!$_GET['product_data']) { $_POST['product_id'] = 1; $_GET['product_data'] = $wpdb->get_row("SELECT * FROM $products_table_name WHERE id = 1", OBJECT); }
$_GET['product_id'] = $_POST['product_id'];
$_POST['quantity'] = (int) $_POST['quantity']; if ($_POST['quantity'] < 1) { $_POST['quantity'] = 1; }
$_POST['price'] = str_replace(array('?', ',', ';'), '.', $_POST['price']);
$_POST['price'] = round(100*$_POST['price'])/100; if ($_POST['price'] <= 0) { $_POST['price'] = $_POST['quantity']*product_data('price'); }
if ($_POST['tax_included_in_price'] == '') { $_POST['tax_included_in_price'] = product_data('tax_included_in_price'); }
if ($_POST['tax'] == '') {
if (product_data('tax_applied') == 'no') { $_POST['tax'] = 0; }
else {
if ($_POST['tax_included_in_price'] == 'yes') {
$r = 1 + product_data('tax_percentage')/100;
$_POST['net_price'] = round(100*$_POST['price']/$r)/100;
$_POST['tax'] = $_POST['price'] - $_POST['net_price']; }
else {
$_POST['net_price'] = $_POST['price'];
$_POST['tax'] = round(product_data('tax_percentage')*$_POST['price'])/100; } } }
else { $_POST['tax'] = str_replace(array('?', ',', ';'), '.', $_POST['tax']);
$_POST['tax'] = round(100*$_POST['tax'])/100;
if ($_POST['tax'] < 0) { $_POST['tax'] = 0; } }
if ($_POST['shipping_cost'] == '') { $_POST['shipping_cost'] = product_data('shipping_cost'); }
else { $_POST['shipping_cost'] = str_replace(array('?', ',', ';'), '.', $_POST['shipping_cost']);
$_POST['shipping_cost'] = round(100*$_POST['shipping_cost'])/100;
if ($_POST['shipping_cost'] < 0) { $_POST['shipping_cost'] = 0; } }
$_POST['amount'] = str_replace(array('?', ',', ';'), '.', $_POST['amount']);
$_POST['amount'] = round(100*$_POST['amount'])/100;
if ($_POST['amount'] <= 0) {
$_POST['amount'] = $_POST['price'] + $_POST['shipping_cost'];
if ($_POST['tax_included_in_price'] == 'no') { $_POST['amount'] = $_POST['amount'] + $_POST['tax']; } }
$_POST['transaction_cost'] = str_replace(array('?', ',', ';'), '.', $_POST['transaction_cost']);
$_POST['transaction_cost'] = round(100*$_POST['transaction_cost'])/100;
if ($_POST['transaction_cost'] < 0) { $_POST['transaction_cost'] = 0; }
if ($_POST['date'] == '') {
$_POST['date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['date_utc'] = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['date']);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['date'] = date('Y-m-d H:i:s', $time);
$_POST['date_utc'] = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); }
if ($_POST['status'] == 'refunded') {
if ($_POST['refund_date'] == '') {
$_POST['refund_date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['refund_date_utc'] = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['refund_date']);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['refund_date'] = date('Y-m-d H:i:s', $time);
$_POST['refund_date_utc'] = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); } }
else { $_POST['refund_date'] = ''; }
$_POST['email_address'] = commerce_format_email_address($_POST['email_address']);
if ($_POST['referring_url'] == '') { $_POST['referring_url'] = $_SERVER['HTTP_REFERER']; }
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
$_POST['commission_amount'] = round(100*$_POST['commission_amount'])/100; if ($_POST['commission_amount'] <= 0) { $_POST['commission_amount'] = 0; }
if ($_POST['commission_amount'] == 0) { $_POST['commission_payment'] = ''; }
elseif ($_POST['commission_payment'] == '') { $_POST['commission_payment'] = 'deferred'; }
if ($_POST['commission_payment'] == '') { $_POST['commission_status'] = ''; }
elseif ($_POST['commission_payment'] == 'instant') {
$_POST['commission_status'] = 'paid';
$_POST['commission_payment_date'] = $_POST['date'];
$_POST['commission_payment_date_utc'] = $_POST['date_utc']; }
elseif ($_POST['commission_status'] == '') { $_POST['commission_status'] = 'unpaid'; }
if ($_POST['commission_status'] == 'paid') {
if ($_POST['commission_payment_date'] == '') {
$_POST['commission_payment_date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['commission_payment_date_utc'] = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['commission_payment_date']);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['commission_payment_date'] = date('Y-m-d H:i:s', $time);
$_POST['commission_payment_date_utc'] = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); } }
else { $_POST['commission_payment_date'] = ''; }
if (($_POST['status'] == 'refunded') && ($_POST['commission_status'] != 'paid')) {
$_POST['commission_amount'] = 0;
$_POST['commission_payment'] = '';
$_POST['commission_status'] = '';
$_POST['commission_payment_date'] = ''; }

if (!isset($_GET['id'])) {
if (isset($_POST['update_fields'])) {
foreach ($_POST as $key => $value) { $_GET['order_data']->$key = $value; }
$_GET['order_data']->id = '{order id}';
foreach (add_order_fields() as $field) { $_POST[$field] = str_replace('{order id}', '[order id]', product_data($field)); } }
else {
if (($_POST['email_address'] == '') || ($_POST['first_name'] == '') || ($_POST['last_name'] == '')) {
$error .= ' '.__('Please fill out the required fields.', 'commerce-manager'); }
if ($error == '') {
$result = $wpdb->get_row("SELECT * FROM $orders_table_name WHERE date = '".$_POST['date']."' AND product_id = '".$_POST['product_id']."' AND email_address = '".$_POST['email_address']."'", OBJECT);
if (!$result) { $updated = true; add_order($_POST); } } } }

if (isset($_GET['id'])) {
$updated = true;
$order_data = $wpdb->get_row("SELECT * FROM $orders_table_name WHERE id = '".$_GET['id']."'", OBJECT);
if ($_POST['first_name'] != '') { $results = $wpdb->query("UPDATE $orders_table_name SET first_name = '".$_POST['first_name']."' WHERE id = '".$_GET['id']."'"); }
if ($_POST['last_name'] != '') { $results = $wpdb->query("UPDATE $orders_table_name SET last_name = '".$_POST['last_name']."' WHERE id = '".$_GET['id']."'"); }
if ($_POST['email_address'] != '') { $results = $wpdb->query("UPDATE $orders_table_name SET email_address = '".$_POST['email_address']."' WHERE id = '".$_GET['id']."'"); }
include 'tables.php';
foreach ($tables['orders'] as $key => $value) { switch ($key) {
case 'id': case 'first_name': case 'last_name': case 'email_address': break;
default: $list .= $key." = '".$_POST[$key]."',"; } }
$results = $wpdb->query("UPDATE $orders_table_name SET ".substr($list, 0, -1)." WHERE id = '".$_GET['id']."'");
	
$row = $wpdb->get_row("SELECT SUM(quantity) AS total FROM $orders_table_name WHERE product_id = '".$_POST['product_id']."'", OBJECT);
$sales_count = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(quantity) AS total FROM $orders_table_name WHERE product_id = '".$_POST['product_id']."' AND status = 'refunded'", OBJECT);
$refunds_count = (int) $row->total;
if (is_numeric($_GET['product_data']->available_quantity)) {
if ($_POST['product_id'] == $order_data->product_id) { $available_quantity = $_GET['product_data']->available_quantity - $_POST['quantity'] + $order_data->quantity; }
else { $available_quantity = $_GET['product_data']->available_quantity - $_POST['quantity']; }
if ($available_quantity < 0) { $available_quantity = 0; } }
else { $available_quantity = 'unlimited'; }
$results = $wpdb->query("UPDATE $products_table_name SET
	available_quantity = '".$available_quantity."',
	sales_count = '".$sales_count."',
	refunds_count = '".$refunds_count."' WHERE id = '".$_POST['product_id']."'");
	
if ($_POST['product_id'] != $order_data->product_id) {
$product_data = $wpdb->get_row("SELECT * FROM $products_table_name WHERE id = '".$order_data->product_id."'", OBJECT);
$row = $wpdb->get_row("SELECT SUM(quantity) AS total FROM $orders_table_name WHERE product_id = '".$product_data->id."'", OBJECT);
$sales_count = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(quantity) AS total FROM $orders_table_name WHERE product_id = '".$product_data->id."' AND status = 'refunded'", OBJECT);
$refunds_count = (int) $row->total;
if (is_numeric($product_data->available_quantity)) { $available_quantity = $product_data->available_quantity + $order_data->quantity; }
else { $available_quantity = 'unlimited'; }
$results = $wpdb->query("UPDATE $products_table_name SET
	available_quantity = '".$available_quantity."',
	sales_count = '".$sales_count."',
	refunds_count = '".$refunds_count."' WHERE id = '".$product_data->id."'"); } } }

if (isset($_GET['id'])) {
$order_data = $wpdb->get_row("SELECT * FROM $orders_table_name WHERE id = '".$_GET['id']."'", OBJECT);
if ($order_data) { foreach ($order_data as $key => $value) { $_POST[$key] = $value; } }
elseif (!headers_sent()) { header('Location: admin.php?page=commerce-manager-order'); exit(); } }

$_POST = array_map('stripslashes', $_POST);
$_POST = array_map('htmlspecialchars', $_POST);
foreach ($_POST as $key => $value) {
$_POST[$key] = str_replace('&amp;amp;', '&amp;', $value);
if ($value == '0000-00-00 00:00:00') { $_POST[$key] = ''; } }
$currency_code = commerce_data('currency_code'); ?>

<div class="wrap">
<div id="poststuff">
<?php commerce_manager_pages_top(); ?>
<?php if ($updated) { echo '<div class="updated"><p><strong>'.(isset($_GET['id']) ? __('Order updated.', 'commerce-manager') : __('Order saved.', 'commerce-manager')).'</strong></p></div>'; } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php commerce_manager_pages_menu(); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<p class="description"><?php _e('Fields marked with * are required.', 'commerce-manager'); ?></p>
<ul class="subsubsub" style="float: none; white-space: normal;">
<li><a href="#general-informations"><?php _e('General informations', 'commerce-manager'); ?></a></li>
<li>| <a href="#customer"><?php _e('Customer', 'commerce-manager'); ?></a></li>
<li>| <a href="#affiliation"><?php _e('Affiliation', 'commerce-manager'); ?></a></li><?php if (!isset($_GET['id'])) { ?>
<li>| <a href="#email-sent-to-customer"><?php _e('Email sent to customer', 'commerce-manager'); ?></a></li>
<li>| <a href="#email-sent-to-seller"><?php _e('Email sent to seller', 'commerce-manager'); ?></a></li>
<li>| <a href="#autoresponders"><?php _e('Autoresponders', 'commerce-manager'); ?></a></li><?php } ?>
</ul>
<div class="postbox">
<h3 id="general-informations"><strong><?php _e('General informations', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="id">'.__('ID', 'commerce-manager').'</label></strong></th>
<td><input type="text" name="id" id="id" size="10" value="'.$_GET['id'].'" disabled="disabled" /> <span class="description">'.__('The ID can not be changed.', 'commerce-manager').'</span></td></tr>'; } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="product_id"><?php _e('Product ID', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="product_id" id="product_id" rows="1" cols="25"><?php echo $_POST['product_id']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 1.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="quantity"><?php _e('Quantity', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="quantity" id="quantity" rows="1" cols="25"><?php echo $_POST['quantity']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 1.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="price"><?php _e('Global price', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="price" id="price" rows="1" cols="25"><?php echo $_POST['price']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for the current product price multiplied by the quantity.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="tax"><?php _e('Tax', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="tax" id="tax" rows="1" cols="25"><?php echo $_POST['tax']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank to automatically calculate the tax.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="tax_included_in_price"><?php _e('Tax included in price', 'commerce-manager'); ?></label></strong></th>
<td><select name="tax_included_in_price" id="tax_included_in_price">
<option value=""<?php if ($_POST['tax_included_in_price'] == '') { echo ' selected="selected"'; } ?>><?php _e('Current product option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['tax_included_in_price'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['tax_included_in_price'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="shipping_cost"><?php _e('Shipping cost', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="shipping_cost" id="shipping_cost" rows="1" cols="25"><?php echo $_POST['shipping_cost']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for the current product shipping cost.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="amount"><?php _e('Amount', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="amount" id="amount" rows="1" cols="25"><?php echo $_POST['amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank to automatically calculate the amount.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payment_mode"><?php _e('Payment mode', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="payment_mode" id="payment_mode" rows="1" cols="50"><?php echo $_POST['payment_mode']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="transaction_number"><?php _e('Transaction number', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="transaction_number" id="transaction_number" rows="1" cols="50"><?php echo $_POST['transaction_number']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="transaction_cost"><?php _e('Transaction cost', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="transaction_cost" id="transaction_cost" rows="1" cols="25"><?php echo $_POST['transaction_cost']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 0.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="instructions"><?php _e('Instructions to the seller', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="instructions" id="instructions" rows="5" cols="75"><?php echo $_POST['instructions']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="shipping_address"><?php _e('Shipping address', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="shipping_address" id="shipping_address" rows="5" cols="75"><?php echo $_POST['shipping_address']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="date"><?php _e('Date', 'commerce-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="date" id="date" size="20" value="<?php echo (isset($_POST['date']) ? $_POST['date'] : date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET)); ?>" /></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="status"><?php _e('Status', 'commerce-manager'); ?></label></strong></th>
<td><select name="status" id="status">
<option value="unprocessed"<?php if ($_POST['status'] == 'unprocessed') { echo ' selected="selected"'; } ?>><?php _e('Unprocessed', 'commerce-manager'); ?></option>
<option value="processed"<?php if ($_POST['status'] == 'processed') { echo ' selected="selected"'; } ?>><?php _e('Processed', 'commerce-manager'); ?></option>
<option value="refunded"<?php if ($_POST['status'] == 'refunded') { echo ' selected="selected"'; } ?>><?php _e('Refunded', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="refund_date"><?php _e('Refund date', 'commerce-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="refund_date" id="refund_date" size="20" value="<?php echo $_POST['refund_date']; ?>" /><br />
<span class="description"><?php _e('Leave this field blank if the order is not refunded, or for the current date if the order is refunded.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="customer"><strong><?php _e('Customer', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['first_name'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="first_name"><?php _e('First name', 'commerce-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="first_name" id="first_name" rows="1" cols="50"><?php echo $_POST['first_name']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['last_name'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="last_name"><?php _e('Last name', 'commerce-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="last_name" id="last_name" rows="1" cols="50"><?php echo $_POST['last_name']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['email_address'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="email_address"><?php _e('Email address', 'commerce-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="email_address" id="email_address" rows="1" cols="50"><?php echo $_POST['email_address']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="website_name"><?php _e('Website name', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="website_name" id="website_name" rows="1" cols="50"><?php echo $_POST['website_name']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="website_url"><?php _e('Website URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="website_url" id="website_url" rows="1" cols="75"><?php echo $_POST['website_url']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="address"><?php _e('Address', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="address" id="address" rows="1" cols="50"><?php echo $_POST['address']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="postcode"><?php _e('Postcode', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="postcode" id="postcode" rows="1" cols="50"><?php echo $_POST['postcode']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="town"><?php _e('Town', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="town" id="town" rows="1" cols="50"><?php echo $_POST['town']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="country"><?php _e('Country', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="country" id="country" rows="1" cols="50"><?php echo $_POST['country']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="phone_number"><?php _e('Phone number', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="phone_number" id="phone_number" rows="1" cols="50"><?php echo $_POST['phone_number']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="ip_address"><?php _e('IP address', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="ip_address" id="ip_address" rows="1" cols="50"><?php echo $_POST['ip_address']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="user_agent"><?php _e('User agent', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="user_agent" id="user_agent" rows="1" cols="75"><?php echo $_POST['user_agent']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="referring_url"><?php _e('Referring URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="referring_url" id="referring_url" rows="1" cols="75"><?php echo $_POST['referring_url']; ?></textarea></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="affiliation"><strong><?php _e('Affiliation', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php echo (function_exists('affiliation_manager_admin_menu') ? '<a href="admin.php?page=affiliation-manager">'.__('Click here to configure the default options.', 'commerce-manager').'</a>' : __('To use affiliation, you must have installed and activated <a href="http://www.kleor-editions.com/affiliation-manager">Affiliation Manager</a>.', 'commerce-manager')); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="referrer"><?php _e('Referrer', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="referrer" id="referrer" rows="1" cols="25"><?php echo $_POST['referrer']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Affiliate who referred this order (ID, login name or email address)', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_amount"><?php _e('Commission amount', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_amount" id="commission_amount" rows="1" cols="25"><?php echo $_POST['commission_amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 0.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_payment"><?php _e('Commission payment', 'commerce-manager'); ?></label></strong></th>
<td><select name="commission_payment" id="commission_payment">
<option value=""<?php if ($_POST['commission_payment'] == '') { echo ' selected="selected"'; } ?>><?php _e('None', 'commerce-manager'); ?></option>
<option value="deferred"<?php if ($_POST['commission_payment'] == 'deferred') { echo ' selected="selected"'; } ?>><?php _e('Deferred', 'commerce-manager'); ?></option>
<option value="instant"<?php if ($_POST['commission_payment'] == 'instant') { echo ' selected="selected"'; } ?>><?php _e('Instant', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_status"><?php _e('Commission status', 'commerce-manager'); ?></label></strong></th>
<td><select name="commission_status" id="commission_status">
<option value=""<?php if ($_POST['commission_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('None', 'commerce-manager'); ?></option>
<option value="unpaid"<?php if ($_POST['commission_status'] == 'unpaid') { echo ' selected="selected"'; } ?>><?php _e('Unpaid', 'commerce-manager'); ?></option>
<option value="paid"<?php if ($_POST['commission_status'] == 'paid') { echo ' selected="selected"'; } ?>><?php _e('Paid', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_payment_date"><?php _e('Commission payment date', 'commerce-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="commission_payment_date" id="commission_payment_date" size="20" value="<?php echo $_POST['commission_payment_date']; ?>" /><br />
<span class="description"><?php _e('Leave this field blank if the commission is not paid, or for the current date if the commission is paid.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>
<?php if (!isset($_GET['id'])) {
if (!isset($_POST['submit'])) {
$commerce_manager_options = (array) get_option('commerce_manager');
$commerce_manager_options = array_map('htmlspecialchars', $commerce_manager_options);
foreach (add_order_fields() as $field) { $_POST[$field] = $commerce_manager_options[$field]; }
$_POST['email_to_customer_body'] = htmlspecialchars(get_option('commerce_manager_email_to_customer_body'));
$_POST['email_to_seller_body'] = htmlspecialchars(get_option('commerce_manager_email_to_seller_body')); } ?>
<p class="submit" style="margin: 0 20%;"><input type="hidden" name="submit" value="true" />
<input type="submit" class="button-secondary" name="update_fields" value="<?php _e('Complete the fields below with the informations about the customer, the product and the order', 'commerce-manager'); ?>" /></p>
<div class="postbox">
<h3 id="email-sent-to-customer"><strong><?php _e('Email sent to customer', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager#email-sent-to-customer"><?php _e('Click here to configure the default options.', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="email_sent_to_customer" id="email_sent_to_customer" value="yes"<?php if ($_POST['email_sent_to_customer'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="email_sent_to_customer"><?php _e('Send an order confirmation email to the customer', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="email_to_customer_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_customer_sender" id="email_to_customer_sender" rows="1" cols="75"><?php echo $_POST['email_to_customer_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="email_to_customer_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_customer_receiver" id="email_to_customer_receiver" rows="1" cols="75"><?php echo $_POST['email_to_customer_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="email_to_customer_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_customer_subject" id="email_to_customer_subject" rows="1" cols="75"><?php echo $_POST['email_to_customer_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="email_to_customer_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="email_to_customer_body" id="email_to_customer_body" rows="15" cols="75"><?php echo $_POST['email_to_customer_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="email-sent-to-seller"><strong><?php _e('Email sent to seller', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager#email-sent-to-seller"><?php _e('Click here to configure the default options.', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="email_sent_to_seller" id="email_sent_to_seller" value="yes"<?php if ($_POST['email_sent_to_seller'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="email_sent_to_seller"><?php _e('Send an order notification email to the seller', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="email_to_seller_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_seller_sender" id="email_to_seller_sender" rows="1" cols="75"><?php echo $_POST['email_to_seller_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="email_to_seller_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_seller_receiver" id="email_to_seller_receiver" rows="1" cols="75"><?php echo $_POST['email_to_seller_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="email_to_seller_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_seller_subject" id="email_to_seller_subject" rows="1" cols="75"><?php echo $_POST['email_to_seller_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="email_to_seller_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="email_to_seller_body" id="email_to_seller_body" rows="15" cols="75"><?php echo $_POST['email_to_seller_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="autoresponders"><strong><?php _e('Autoresponders', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager#autoresponders"><?php _e('Click here to configure the default options.', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="customer_subscribed_to_autoresponder" id="customer_subscribed_to_autoresponder" value="yes"<?php if ($_POST['customer_subscribed_to_autoresponder'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="customer_subscribed_to_autoresponder"><?php _e('Subscribe the customer to an autoresponder list', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_autoresponder"><?php _e('Autoresponder', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_autoresponder" id="customer_autoresponder">
<?php include 'autoresponders.php';
$autoresponder = do_shortcode($_POST['customer_autoresponder']);
foreach ($autoresponders as $value) {
echo '<option value="'.$value.'"'.($autoresponder == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_autoresponder_list"><?php _e('List', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="customer_autoresponder_list" id="customer_autoresponder_list" rows="1" cols="50"><?php echo $_POST['customer_autoresponder_list']; ?></textarea></td></tr>
</tbody></table>
</div></div>
<?php if ($_GET['autoresponder_subscription'] != '') { echo '<div><img alt="" src="'.$_GET['autoresponder_subscription'].'" /></div>'; } } ?>
<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php (isset($_GET['id']) ?  _e('Save Changes', 'commerce-manager') : _e('Save Order', 'commerce-manager')); ?>" /></p>
</form>
</div>
</div>
<?php if (isset($_POST['update_fields'])) { ?>
<script type="text/javascript">window.location = '#email-sent-to-customer';</script>
<?php } }