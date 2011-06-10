<?php global $wpdb;
$products_table_name = $wpdb->prefix.'commerce_manager_products';
add_action('admin_footer', 'commerce_statistics_form_js');

if ((isset($_GET['id'])) && ($_GET['action'] == 'delete')) {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
$results = $wpdb->query("DELETE FROM $products_table_name WHERE id = '".$_GET['id']."'"); } ?>
<div class="wrap">
<div id="poststuff">
<?php commerce_manager_pages_top(); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Product deleted.', 'commerce-manager').'</strong></p></div>'; } ?>
<?php commerce_manager_pages_menu(); ?>
<div class="clear"></div>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php _e('Do you really want to permanently delete this product?', 'commerce-manager'); ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'commerce-manager'); ?>" />
</div>
</form><?php } ?>
</div>
</div><?php }

else {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
$_POST = array_map('html_entity_decode', $_POST);
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$_POST['price'] = str_replace(array('?', ',', ';'), '.', $_POST['price']);
if ($_POST['date'] == '') {
$_POST['date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['date_utc'] = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['date']);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['date'] = date('Y-m-d H:i:s', $time);
$_POST['date_utc'] = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); }
if ($_POST['available_quantity'] == '') { $_POST['available_quantity'] = 'unlimited'; }
$_POST['sales_count'] = (int) $_POST['sales_count']; if ($_POST['sales_count'] < 0) { $_POST['sales_count'] = 0; }
$_POST['refunds_count'] = (int) $_POST['refunds_count']; if ($_POST['refunds_count'] < 0) { $_POST['refunds_count'] = 0; }
if ($_POST['refunds_count'] > $_POST['sales_count']) { $_POST['refunds_count'] = $_POST['sales_count']; }
$_POST['tax_percentage'] = str_replace(array('?', ',', ';'), '.', $_POST['tax_percentage']);
$_POST['shipping_cost'] = str_replace(array('?', ',', ';'), '.', $_POST['shipping_cost']);
$_POST['commission_amount'] = str_replace(array('?', ',', ';'), '.', $_POST['commission_amount']);
$_POST['commission_percentage'] = str_replace(array('?', ',', ';'), '.', $_POST['commission_percentage']);

if (!isset($_GET['id'])) {
if (($_POST['name'] == '') || ($_POST['price'] == '')) { $error .= ' '.__('Please fill out the required fields.', 'commerce-manager'); }
if ($error == '') {
$result = $wpdb->get_row("SELECT * FROM $products_table_name WHERE name = '".$_POST['name']."' AND price = '".$_POST['price']."' AND date = '".$_POST['date']."'", OBJECT);
if (!$result) {
$updated = true;
include 'tables.php';
foreach ($tables['products'] as $key => $value) { $keys_list .= $key.","; $values_list .= "'".$_POST[$key]."',"; }
$results = $wpdb->query("INSERT INTO $products_table_name (".substr($keys_list, 0, -1).") VALUES(".substr($values_list, 0, -1).")"); } } }

if (isset($_GET['id'])) {
$updated = true;
if ($_POST['name'] != '') { $results = $wpdb->query("UPDATE $products_table_name SET name = '".$_POST['name']."' WHERE id = '".$_GET['id']."'"); }
if ($_POST['price'] != '') { $results = $wpdb->query("UPDATE $products_table_name SET price = '".$_POST['price']."' WHERE id = '".$_GET['id']."'"); }
include 'tables.php';
foreach ($tables['products'] as $key => $value) { switch ($key) {
case 'id': case 'name': case 'price': break;
default: $list .= $key." = '".$_POST[$key]."',"; } }
$results = $wpdb->query("UPDATE $products_table_name SET ".substr($list, 0, -1)." WHERE id = '".$_GET['id']."'"); } }

if (isset($_GET['id'])) {
$product_data = $wpdb->get_row("SELECT * FROM $products_table_name WHERE id = '".$_GET['id']."'", OBJECT);
if ($product_data) { foreach ($product_data as $key => $value) { $_POST[$key] = $value; } }
elseif (!headers_sent()) { header('Location: admin.php?page=commerce-manager-product'); exit(); } }

$_POST = array_map('stripslashes', $_POST);
$_POST = array_map('htmlspecialchars', $_POST);
foreach ($_POST as $key => $value) {
$_POST[$key] = str_replace('&amp;amp;', '&amp;', $value);
if ($value == '0000-00-00 00:00:00') { $_POST[$key] = ''; } }
$currency_code = commerce_data('currency_code'); ?>

<div class="wrap">
<div id="poststuff">
<?php commerce_manager_pages_top(); ?>
<?php if ($updated) { echo '<div class="updated"><p><strong>'.(isset($_GET['id']) ? __('Product updated.', 'commerce-manager') : __('Product saved.', 'commerce-manager')).'</strong></p></div>'; } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php commerce_manager_pages_menu(); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<p class="description"><?php _e('Fields marked with * are required.', 'commerce-manager'); ?></p>
<ul class="subsubsub" style="float: none; white-space: normal;">
<li><a href="#general-informations"><?php _e('General informations', 'commerce-manager'); ?></a></li>
<li>| <a href="#inventory"><?php _e('Inventory', 'commerce-manager'); ?></a></li>
<li>| <a href="#order"><?php _e('Order', 'commerce-manager'); ?></a></li>
<li>| <a href="#tax"><?php _e('Tax', 'commerce-manager'); ?></a></li>
<li>| <a href="#shipping"><?php _e('Shipping', 'commerce-manager'); ?></a></li>
<li>| <a href="#payment-modes"><?php _e('Payment modes', 'commerce-manager'); ?></a></li>
<li>| <a href="#email-sent-to-customer"><?php _e('Email sent to customer', 'commerce-manager'); ?></a></li>
<li>| <a href="#email-sent-to-seller"><?php _e('Email sent to seller', 'commerce-manager'); ?></a></li>
<li>| <a href="#autoresponders"><?php _e('Autoresponders', 'commerce-manager'); ?></a></li>
<li>| <a href="#affiliation"><?php _e('Affiliation', 'commerce-manager'); ?></a></li>
</ul>
<div class="postbox">
<h3 id="general-informations"><strong><?php _e('General informations', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['name'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="name"><?php _e('Name', 'commerce-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="name" id="name" rows="1" cols="50"><?php echo $_POST['name']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['price'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="price"><?php _e('Price', 'commerce-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="price" id="price" rows="1" cols="50"><?php echo $_POST['price']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="reference"><?php _e('Reference', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="reference" id="reference" rows="1" cols="50"><?php echo $_POST['reference']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="description"><?php _e('Description', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="description" id="description" rows="1" cols="75"><?php echo $_POST['description']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="url"><?php _e('URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="url" id="url" rows="1" cols="75"><?php echo $_POST['url']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="thumbnail_url"><?php _e('Thumbnail URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="thumbnail_url" id="thumbnail_url" rows="1" cols="75"><?php echo $_POST['thumbnail_url']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="downloadable"><?php _e('Downloadable', 'commerce-manager'); ?></label></strong></th>
<td><select name="downloadable" id="downloadable">
<option value="yes"<?php if ($_POST['downloadable'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['downloadable'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="download_url"><?php _e('Download URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="download_url" id="download_url" rows="1" cols="75"><?php echo $_POST['download_url']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="instructions"><?php _e('Instructions to the customer', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="instructions" id="instructions" rows="9" cols="75"><?php echo $_POST['instructions']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="date"><?php _e('Launch date', 'commerce-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="date" id="date" size="20" value="<?php echo (isset($_POST['date']) ? $_POST['date'] : date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET)); ?>" /></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="inventory"><strong><?php _e('Inventory', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="available_quantity"><?php _e('Available quantity', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="available_quantity" id="available_quantity" rows="1" cols="25"><?php echo (!is_numeric($_POST['available_quantity']) ? '' : $_POST['available_quantity']); ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for an unlimited quantity.', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="sales_count"><?php _e('Sales count', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="sales_count" id="sales_count" rows="1" cols="25"><?php echo $_POST['sales_count']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 0.', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="refunds_count"><?php _e('Refunds count', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="refunds_count" id="refunds_count" rows="1" cols="25"><?php echo $_POST['refunds_count']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 0.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="order"><strong><?php _e('Order', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager"><?php _e('Click here to configure the default options.', 'commerce-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="purchase_button_url"><?php _e('Purchase button URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="purchase_button_url" id="purchase_button_url" rows="1" cols="75"><?php echo $_POST['purchase_button_url']; ?></textarea><br />
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="purchase_button_text"><?php _e('Purchase button text', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="purchase_button_text" id="purchase_button_text" rows="1" cols="25"><?php echo $_POST['purchase_button_text']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Text displayed when the purchase button can not be displayed', 'commerce-manager'); ?><br />
<?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="order_confirmation_url"><?php _e('Order confirmation URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_confirmation_url" id="order_confirmation_url" rows="1" cols="75"><?php echo $_POST['order_confirmation_url']; ?></textarea><br />
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="orders_initial_status"><?php _e('Orders initial status', 'commerce-manager'); ?></label></strong></th>
<td><select name="orders_initial_status" id="orders_initial_status">
<option value=""<?php if ($_POST['orders_initial_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="unprocessed"<?php if ($_POST['orders_initial_status'] == 'unprocessed') { echo ' selected="selected"'; } ?>><?php _e('Unprocessed', 'commerce-manager'); ?></option>
<option value="processed"<?php if ($_POST['orders_initial_status'] == 'processed') { echo ' selected="selected"'; } ?>><?php _e('Processed', 'commerce-manager'); ?></option>
</select>
<span class="description"><?php _e('Status assigned to orders upon their registration', 'commerce-manager'); ?><br />
<?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="tax"><strong><?php _e('Tax', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager#tax"><?php _e('Click here to configure the default options.', 'commerce-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="tax_applied"><?php _e('Apply a tax', 'commerce-manager'); ?></label></strong></th>
<td><select name="tax_applied" id="tax_applied">
<option value=""<?php if ($_POST['tax_applied'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['tax_applied'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['tax_applied'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="tax_included_in_price"><?php _e('Include the tax in the price', 'commerce-manager'); ?></label></strong></th>
<td><select name="tax_included_in_price" id="tax_included_in_price">
<option value=""<?php if ($_POST['tax_included_in_price'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['tax_included_in_price'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['tax_included_in_price'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="tax_percentage"><?php _e('Tax percentage', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="tax_percentage" id="tax_percentage" rows="1" cols="25"><?php echo $_POST['tax_percentage']; ?></textarea> <span style="vertical-align: 25%;">%</span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="shipping"><strong><?php _e('Shipping', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager#shipping"><?php _e('Click here to configure the default options.', 'commerce-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="shipping_address_required"><?php _e('Shipping address required', 'commerce-manager'); ?></label></strong></th>
<td><select name="shipping_address_required" id="shipping_address_required">
<option value=""<?php if ($_POST['shipping_address_required'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['shipping_address_required'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['shipping_address_required'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="shipping_cost"><?php _e('Shipping cost', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="shipping_cost" id="shipping_cost" rows="1" cols="25"><?php echo $_POST['shipping_cost']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="payment-modes"><strong><?php _e('Payment modes', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager#payment-modes"><?php _e('Click here to configure the default options.', 'commerce-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="sandbox_enabled"><?php _e('Enable <em>Sandbox</em> mode', 'commerce-manager'); ?></label></strong></th>
<td><select name="sandbox_enabled" id="sandbox_enabled">
<option value=""<?php if ($_POST['sandbox_enabled'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['sandbox_enabled'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['sandbox_enabled'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select>
<span class="description"><?php _e('Allows testing without generating real transactions', 'commerce-manager'); ?><br />
<?php _e('Do not enable <em>Sandbox</em> mode if you want to allow Internet users to order this product.', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="paypal_email_address"><?php _e('PayPal email address', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="paypal_email_address" id="paypal_email_address" rows="1" cols="50"><?php echo $_POST['paypal_email_address']; ?></textarea><br />
<span class="description"><?php _e('Email address of the PayPal account that receives payments', 'commerce-manager'); ?><br />
<?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="email-sent-to-customer"><strong><?php _e('Email sent to customer', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager#email-sent-to-customer"><?php _e('Click here to configure the default options.', 'commerce-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_sent_to_customer"><?php _e('Send an order confirmation email to the customer', 'commerce-manager'); ?></label></strong></th>
<td><select name="email_sent_to_customer" id="email_sent_to_customer">
<option value=""<?php if ($_POST['email_sent_to_customer'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['email_sent_to_customer'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['email_sent_to_customer'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_customer_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_customer_sender" id="email_to_customer_sender" rows="1" cols="75"><?php echo $_POST['email_to_customer_sender']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_customer_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_customer_receiver" id="email_to_customer_receiver" rows="1" cols="75"><?php echo $_POST['email_to_customer_receiver']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_customer_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_customer_subject" id="email_to_customer_subject" rows="1" cols="75"><?php echo $_POST['email_to_customer_subject']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_customer_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="email_to_customer_body" id="email_to_customer_body" rows="15" cols="75"><?php echo $_POST['email_to_customer_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="email-sent-to-seller"><strong><?php _e('Email sent to seller', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager#email-sent-to-seller"><?php _e('Click here to configure the default options.', 'commerce-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_sent_to_seller"><?php _e('Send an order notification email to the seller', 'commerce-manager'); ?></label></strong></th>
<td><select name="email_sent_to_seller" id="email_sent_to_seller">
<option value=""<?php if ($_POST['email_sent_to_seller'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['email_sent_to_seller'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['email_sent_to_seller'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_seller_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_seller_sender" id="email_to_seller_sender" rows="1" cols="75"><?php echo $_POST['email_to_seller_sender']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_seller_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_seller_receiver" id="email_to_seller_receiver" rows="1" cols="75"><?php echo $_POST['email_to_seller_receiver']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_seller_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_seller_subject" id="email_to_seller_subject" rows="1" cols="75"><?php echo $_POST['email_to_seller_subject']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_seller_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="email_to_seller_body" id="email_to_seller_body" rows="15" cols="75"><?php echo $_POST['email_to_seller_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="autoresponders"><strong><?php _e('Autoresponders', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager#autoresponders"><?php _e('Click here to configure the default options.', 'commerce-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="customer_subscribed_to_autoresponder"><?php _e('Subscribe the customer to an autoresponder list', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_subscribed_to_autoresponder" id="customer_subscribed_to_autoresponder">
<option value=""<?php if ($_POST['customer_subscribed_to_autoresponder'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['customer_subscribed_to_autoresponder'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['customer_subscribed_to_autoresponder'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="customer_autoresponder"><?php _e('Autoresponder', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_autoresponder" id="customer_autoresponder">
<?php include 'autoresponders.php';
$autoresponder = do_shortcode($_POST['customer_autoresponder']);
echo '<option value=""'.($autoresponder == '' ? ' selected="selected"' : '').'>'.__('Default option', 'commerce-manager').'</option>'."\n";
foreach ($autoresponders as $value) {
echo '<option value="'.$value.'"'.($autoresponder == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="customer_autoresponder_list"><?php _e('List', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="customer_autoresponder_list" id="customer_autoresponder_list" rows="1" cols="50"><?php echo $_POST['customer_autoresponder_list']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="affiliation"><strong><?php _e('Affiliation', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php echo (function_exists('affiliation_manager_admin_menu') ? '<a href="admin.php?page=affiliation-manager">'.__('Click here to configure the default options.', 'commerce-manager').'</a>' : __('To use affiliation, you must have installed and activated <a href="http://www.kleor-editions.com/affiliation-manager">Affiliation Manager</a>.', 'commerce-manager')); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="affiliation_enabled"><?php _e('Use affiliation', 'commerce-manager'); ?></label></strong></th>
<td><select name="affiliation_enabled" id="affiliation_enabled">
<option value=""<?php if ($_POST['affiliation_enabled'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['affiliation_enabled'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['affiliation_enabled'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="commission_type"><?php _e('Commission type', 'commerce-manager'); ?></label></strong></th>
<td><select name="commission_type" id="commission_type">
<option value=""<?php if ($_POST['commission_type'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="constant"<?php if ($_POST['commission_type'] == 'constant') { echo ' selected="selected"'; } ?>><?php _e('Constant', 'commerce-manager'); ?></option>
<option value="proportional"<?php if ($_POST['commission_type'] == 'proportional') { echo ' selected="selected"'; } ?>><?php _e('Proportional', 'commerce-manager'); ?></option>
</select></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="commission_amount"><?php _e('Commission amount', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_amount" id="commission_amount" rows="1" cols="25"><?php echo $_POST['commission_amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you select constant commissions', 'commerce-manager'); ?><br />
<?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="commission_percentage"><?php _e('Commission percentage', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_percentage" id="commission_percentage" rows="1" cols="25"><?php echo $_POST['commission_percentage']; ?></textarea> <span style="vertical-align: 25%;">% <?php _e('of the price', 'commerce-manager'); ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you select proportional commissions', 'commerce-manager'); ?><br />
<?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="commission_payment"><?php _e('Commission payment', 'commerce-manager'); ?></label></strong></th>
<td><select name="commission_payment" id="commission_payment">
<option value=""<?php if ($_POST['commission_payment'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="deferred"<?php if ($_POST['commission_payment'] == 'deferred') { echo ' selected="selected"'; } ?>><?php _e('Deferred', 'commerce-manager'); ?></option>
<option value="instant"<?php if ($_POST['commission_payment'] == 'instant') { echo ' selected="selected"'; } ?>><?php _e('Instant', 'commerce-manager'); ?></option>
</select>
<span class="description"><?php _e('You can pay your affiliates instantly.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#commission-payment"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="first_sale_winner"><?php _e('First sale award', 'commerce-manager'); ?></label></strong></th>
<td><?php _e('The first sale referred by the affiliate is awarded to the', 'commerce-manager'); ?> <select name="first_sale_winner" id="first_sale_winner">
<option value=""<?php if ($_POST['first_sale_winner'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="affiliate"<?php if ($_POST['first_sale_winner'] == 'affiliate') { echo ' selected="selected"'; } ?>><?php _e('affiliate', 'commerce-manager'); ?></option>
<option value="affiliator"<?php if ($_POST['first_sale_winner'] == 'affiliator') { echo ' selected="selected"'; } ?>><?php _e('affiliator', 'commerce-manager'); ?></option>
</select>. 
<span class="description"><?php _e('Used for instant payment of commissions', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#first-sale-award"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="registration_required"><?php _e('Registration to the affiliate program required', 'commerce-manager'); ?></label></strong></th>
<td><select name="registration_required" id="registration_required">
<option value=""<?php if ($_POST['registration_required'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['registration_required'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['registration_required'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select>
<span class="description"><?php _e('The registration can be optional, only if you select instant payment of commissions.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>
<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php (isset($_GET['id']) ?  _e('Save Changes', 'commerce-manager') : _e('Save Product', 'commerce-manager')); ?>" /></p>
</form>
</div>
</div>
<?php }