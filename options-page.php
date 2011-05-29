<?php if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
$_POST = array_map('html_entity_decode', $_POST);
$_POST = array_map('stripslashes', $_POST);
include_once 'initial-options.php';
if ($_POST['customer_subscribed_to_autoresponder'] != 'yes') { $_POST['customer_subscribed_to_autoresponder'] = 'no'; }
if ($_POST['customer_subscribed_to_autoresponder2'] != 'yes') { $_POST['customer_subscribed_to_autoresponder2'] = 'no'; }
if ($_POST['email_sent_to_customer'] != 'yes') { $_POST['email_sent_to_customer'] = 'no'; }
if ($_POST['email_sent_to_seller'] != 'yes') { $_POST['email_sent_to_seller'] = 'no'; }
if ($_POST['sandbox_enabled'] != 'yes') { $_POST['sandbox_enabled'] = 'no'; }
if ($_POST['shipping_address_required'] != 'yes') { $_POST['shipping_address_required'] = 'no'; }
$_POST['shipping_cost'] = str_replace(array('?', ',', ';'), '.', $_POST['shipping_cost']);
if ($_POST['shipping_cost'] == '') { $_POST['shipping_cost'] = 0; }
if ($_POST['tax_applied'] != 'yes') { $_POST['tax_applied'] = 'no'; }
if ($_POST['tax_included_in_price'] != 'yes') { $_POST['tax_included_in_price'] = 'no'; }
$_POST['tax_percentage'] = str_replace(array('?', ',', ';'), '.', $_POST['tax_percentage']);
if ($_POST['tax_percentage'] == '') { $_POST['tax_percentage'] = 0; }
foreach ($commerce_manager_initial_options as $key => $value) {
if ($_POST[$key] != '') { $commerce_manager_options[$key] = $_POST[$key]; }
else { $commerce_manager_options[$key] = $commerce_manager_initial_options[$key]; } }
update_option('commerce_manager', $commerce_manager_options);
if ($_POST['email_to_customer_body'] == '') { $_POST['email_to_customer_body'] = $commerce_manager_initial_email_to_customer_body; }
update_option('commerce_manager_email_to_customer_body', $_POST['email_to_customer_body']);
if ($_POST['email_to_seller_body'] == '') { $_POST['email_to_seller_body'] = $commerce_manager_initial_email_to_seller_body; }
update_option('commerce_manager_email_to_seller_body', $_POST['email_to_seller_body']); }
else { $commerce_manager_options = (array) get_option('commerce_manager'); }

$commerce_manager_options = array_map('htmlspecialchars', $commerce_manager_options);
$currency_code = commerce_data('currency_code'); ?>

<div class="wrap">
<div id="poststuff">
<?php commerce_manager_pages_top(); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>'; } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php commerce_manager_pages_menu(); ?>
<div class="clear"></div>
<p class="description"><?php _e('Leave each field blank to reset the corresponding option.', 'commerce-manager'); ?></p>
<ul class="subsubsub" style="float: none; white-space: normal;">
<li><a href="#general-options"><?php _e('General options', 'commerce-manager'); ?></a></li>
<li>| <a href="#tax"><?php _e('Tax', 'commerce-manager'); ?></a></li>
<li>| <a href="#shipping"><?php _e('Shipping', 'commerce-manager'); ?></a></li>
<li>| <a href="#payment-modes"><?php _e('Payment modes', 'commerce-manager'); ?></a></li>
<li>| <a href="#email-sent-to-customer"><?php _e('Email sent to customer', 'commerce-manager'); ?></a></li>
<li>| <a href="#email-sent-to-seller"><?php _e('Email sent to seller', 'commerce-manager'); ?></a></li>
<li>| <a href="#autoresponders"><?php _e('Autoresponders', 'commerce-manager'); ?></a></li>
</ul>
<div class="postbox">
<h3 id="general-options"><strong><?php _e('General options', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="currency_code"><?php _e('Currency code', 'commerce-manager'); ?></label></strong></th>
<td><select name="currency_code" id="currency_code">
<?php include_once 'currency-codes.php';
foreach ($currency_codes as $key => $value) {
echo '<option value="'.$value.'"'.($currency_code == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="purchase_button_url"><?php _e('Purchase button URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="purchase_button_url" id="purchase_button_url" rows="1" cols="75"><?php echo $commerce_manager_options['purchase_button_url']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="purchase_button_text"><?php _e('Purchase button text', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="purchase_button_text" id="purchase_button_text" rows="1" cols="50"><?php echo $commerce_manager_options['purchase_button_text']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Text displayed when the purchase button can not be displayed', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="order_confirmation_url"><?php _e('Order confirmation URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_confirmation_url" id="order_confirmation_url" rows="1" cols="75"><?php echo $commerce_manager_options['order_confirmation_url']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="orders_initial_status"><?php _e('Orders initial status', 'commerce-manager'); ?></label></strong></th>
<td><select name="orders_initial_status" id="orders_initial_status">
<option value="unprocessed"<?php if ($commerce_manager_options['orders_initial_status'] == 'unprocessed') { echo ' selected="selected"'; } ?>><?php _e('Unprocessed', 'commerce-manager'); ?></option>
<option value="processed"<?php if ($commerce_manager_options['orders_initial_status'] == 'processed') { echo ' selected="selected"'; } ?>><?php _e('Processed', 'commerce-manager'); ?></option>
</select>
<span class="description"><?php _e('Status assigned to orders upon their registration', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="tax"><strong><?php _e('Tax', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="tax_applied" id="tax_applied" value="yes"<?php if ($commerce_manager_options['tax_applied'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="tax_applied"><?php _e('Apply a tax', 'commerce-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="tax_included_in_price" id="tax_included_in_price" value="yes"<?php if ($commerce_manager_options['tax_included_in_price'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="tax_included_in_price"><?php _e('Include the tax in the price', 'commerce-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="tax_percentage"><?php _e('Tax percentage', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="tax_percentage" id="tax_percentage" rows="1" cols="25"><?php echo $commerce_manager_options['tax_percentage']; ?></textarea> <span style="vertical-align: 25%;">%</span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="shipping"><strong><?php _e('Shipping', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="shipping_address_required" id="shipping_address_required" value="yes"<?php if ($commerce_manager_options['shipping_address_required'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="shipping_address_required"><?php _e('Shipping address required', 'commerce-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="shipping_cost"><?php _e('Shipping cost', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="shipping_cost" id="shipping_cost" rows="1" cols="25"><?php echo $commerce_manager_options['shipping_cost']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="payment-modes"><strong><?php _e('Payment modes', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="sandbox_enabled" id="sandbox_enabled" value="yes"<?php if ($commerce_manager_options['sandbox_enabled'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="sandbox_enabled"><?php _e('Enable <em>Sandbox</em> mode', 'commerce-manager'); ?></label><br />
<span class="description"><?php _e('Allows testing without generating real transactions', 'commerce-manager'); ?><br />
<?php _e('Do not enable <em>Sandbox</em> mode if you want to allow Internet users to order your products.', 'commerce-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="paypal_email_address"><?php _e('PayPal email address', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="paypal_email_address" id="paypal_email_address" rows="1" cols="50"><?php echo $commerce_manager_options['paypal_email_address']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="email-sent-to-customer"><strong><?php _e('Email sent to customer', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="email_sent_to_customer" id="email_sent_to_customer" value="yes"<?php if ($commerce_manager_options['email_sent_to_customer'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="email_sent_to_customer"><?php _e('Send an order confirmation email to the customer', 'commerce-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_customer_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_customer_sender" id="email_to_customer_sender" rows="1" cols="75"><?php echo $commerce_manager_options['email_to_customer_sender']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_customer_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_customer_receiver" id="email_to_customer_receiver" rows="1" cols="75"><?php echo $commerce_manager_options['email_to_customer_receiver']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_customer_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_customer_subject" id="email_to_customer_subject" rows="1" cols="75"><?php echo $commerce_manager_options['email_to_customer_subject']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_customer_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="email_to_customer_body" id="email_to_customer_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('commerce_manager_email_to_customer_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="email-sent-to-seller"><strong><?php _e('Email sent to seller', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="email_sent_to_seller" id="email_sent_to_seller" value="yes"<?php if ($commerce_manager_options['email_sent_to_seller'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="email_sent_to_seller"><?php _e('Send an order notification email to the seller', 'commerce-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_seller_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_seller_sender" id="email_to_seller_sender" rows="1" cols="75"><?php echo $commerce_manager_options['email_to_seller_sender']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_seller_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_seller_receiver" id="email_to_seller_receiver" rows="1" cols="75"><?php echo $commerce_manager_options['email_to_seller_receiver']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_seller_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_seller_subject" id="email_to_seller_subject" rows="1" cols="75"><?php echo $commerce_manager_options['email_to_seller_subject']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_seller_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="email_to_seller_body" id="email_to_seller_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('commerce_manager_email_to_seller_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="autoresponders"><strong><?php _e('Autoresponders', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('You must make some adjustments so that the subscription works with AWeber.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#aweber"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="customer_subscribed_to_autoresponder" id="customer_subscribed_to_autoresponder" value="yes"<?php if ($commerce_manager_options['customer_subscribed_to_autoresponder'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="customer_subscribed_to_autoresponder"><?php _e('Subscribe the customer to an autoresponder list', 'commerce-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="customer_autoresponder"><?php _e('Autoresponder', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_autoresponder" id="customer_autoresponder">
<?php include_once 'autoresponders.php';
$autoresponder = commerce_data('customer_autoresponder');
foreach ($autoresponders as $key => $value) {
echo '<option value="'.$value.'"'.($autoresponder == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="customer_autoresponder_list"><?php _e('List', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="customer_autoresponder_list" id="customer_autoresponder_list" rows="1" cols="50"><?php echo $commerce_manager_options['customer_autoresponder_list']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="customer_subscribed_to_autoresponder2" id="customer_subscribed_to_autoresponder2" value="yes"<?php if ($commerce_manager_options['customer_subscribed_to_autoresponder2'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="customer_subscribed_to_autoresponder2"><?php _e('Subscribe the customer to an additional autoresponder list', 'commerce-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="customer_autoresponder2"><?php _e('Additional autoresponder', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_autoresponder2" id="customer_autoresponder2">
<?php include_once 'autoresponders.php';
$autoresponder2 = commerce_data('customer_autoresponder2');
foreach ($autoresponders as $key => $value) {
echo '<option value="'.$value.'"'.($autoresponder2 == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="customer_autoresponder_list2"><?php _e('Additional list', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="customer_autoresponder_list2" id="customer_autoresponder_list2" rows="1" cols="50"><?php echo $commerce_manager_options['customer_autoresponder_list2']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes'); ?>" /></p>
</form>
</div>
</div>