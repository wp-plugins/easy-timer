<?php if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
$_POST = array_map('html_entity_decode', $_POST);
$_POST = array_map('stripslashes', $_POST);
$currency_code = strtoupper($_POST['currency_code']);
$customers_aweber_list = $_POST['customers_aweber_list'];
if ($_POST['customers_subscribed_to_aweber_list'] == 'yes') { $customers_subscribed_to_aweber_list = 'yes'; } else { $customers_subscribed_to_aweber_list = 'no'; }
if ($_POST['email_sent_to_customer'] == 'yes') { $email_sent_to_customer = 'yes'; } else { $email_sent_to_customer = 'no'; }
if ($_POST['email_sent_to_seller'] == 'yes') { $email_sent_to_seller = 'yes'; } else { $email_sent_to_seller = 'no'; }
$email_to_customer_sender = $_POST['email_to_customer_sender'];
$email_to_customer_subject = $_POST['email_to_customer_subject'];
$email_to_seller_receiver = $_POST['email_to_seller_receiver'];
$email_to_seller_subject = $_POST['email_to_seller_subject'];
$order_confirmation_url = $_POST['order_confirmation_url'];
$paypal_email_address = $_POST['paypal_email_address'];
$purchase_button_url = $_POST['purchase_button_url'];

$commerce_manager_options = array(
'currency_code' => $currency_code,
'customers_aweber_list' => $customers_aweber_list,
'customers_subscribed_to_aweber_list' => $customers_subscribed_to_aweber_list,
'email_sent_to_customer' => $email_sent_to_customer,
'email_sent_to_seller' => $email_sent_to_seller,
'email_to_customer_sender' => $email_to_customer_sender,
'email_to_customer_subject' => $email_to_customer_subject,
'email_to_seller_receiver' => $email_to_seller_receiver,
'email_to_seller_subject' => $email_to_seller_subject,
'order_confirmation_url' => $order_confirmation_url,
'paypal_email_address' => $paypal_email_address,
'purchase_button_url' => $purchase_button_url);
update_option('commerce_manager', $commerce_manager_options);

update_option('commerce_manager_email_to_customer_body', $_POST['email_to_customer_body']);
update_option('commerce_manager_email_to_seller_body', $_POST['email_to_seller_body']); }

if (!isset($commerce_manager_options)) { $commerce_manager_options = get_option('commerce_manager'); }
$commerce_manager_options = array_map('htmlspecialchars', $commerce_manager_options); ?>

<div class="wrap">
<div id="poststuff">
<?php commerce_manager_pages_top(); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>'; } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php commerce_manager_pages_menu(); ?>
<div class="postbox">
<h3><?php _e('General options', 'commerce-manager'); ?></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="currency_code"><?php _e('Currency code', 'commerce-manager'); ?></label></strong></th>
<td><select name="currency_code" id="currency_code">
<?php include_once 'currency-codes.php';
foreach ($currency_codes as $key => $currency_code) {
$currency_codes_options .= '<option value="'.$currency_code.'"'.($commerce_manager_options['currency_code'] == $currency_code ? ' selected="selected"' : '').'>'.$currency_code.'</option>'."\n"; }
echo $currency_codes_options; ?>
</select></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="paypal_email_address"><?php _e('PayPal email address', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="paypal_email_address" id="paypal_email_address" rows="1" cols="25"><?php echo $commerce_manager_options['paypal_email_address']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="purchase_button_url"><?php _e('Purchase button URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="purchase_button_url" id="purchase_button_url" rows="1" cols="75"><?php echo $commerce_manager_options['purchase_button_url']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="order_confirmation_url"><?php _e('Order confirmation URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_confirmation_url" id="order_confirmation_url" rows="1" cols="75"><?php echo $commerce_manager_options['order_confirmation_url']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3><?php _e('Email sent to customer', 'commerce-manager'); ?></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="email_sent_to_customer" id="email_sent_to_customer" value="yes"<?php if ($commerce_manager_options['email_sent_to_customer'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="email_sent_to_customer"><?php _e('Send an order confirmation email to the customer', 'commerce-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_customer_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_customer_sender" id="email_to_customer_sender" rows="1" cols="75"><?php echo $commerce_manager_options['email_to_customer_sender']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_customer_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_customer_subject" id="email_to_customer_subject" rows="1" cols="75"><?php echo $commerce_manager_options['email_to_customer_subject']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_customer_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; height: 20%; width: 75%;" name="email_to_customer_body" id="email_to_customer_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('commerce_manager_email_to_customer_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3><?php _e('Email sent to seller', 'commerce-manager'); ?></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="email_sent_to_seller" id="email_sent_to_seller" value="yes"<?php if ($commerce_manager_options['email_sent_to_seller'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="email_sent_to_seller"><?php _e('Send an order notification email to the seller', 'commerce-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_seller_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_seller_receiver" id="email_to_seller_receiver" rows="1" cols="75"><?php echo $commerce_manager_options['email_to_seller_receiver']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_seller_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_seller_subject" id="email_to_seller_subject" rows="1" cols="75"><?php echo $commerce_manager_options['email_to_seller_subject']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_seller_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; height: 20%; width: 75%;" name="email_to_seller_body" id="email_to_seller_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('commerce_manager_email_to_seller_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3><?php _e('Autoresponder', 'commerce-manager'); ?></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="customers_subscribed_to_aweber_list" id="customers_subscribed_to_aweber_list" value="yes"<?php if ($commerce_manager_options['customers_subscribed_to_aweber_list'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="customers_subscribed_to_aweber_list"><?php _e('Subscribe customers to an AWeber list', 'commerce-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="customers_aweber_list"><?php _e('AWeber list', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="customers_aweber_list" id="customers_aweber_list" rows="1" cols="25"><?php echo $commerce_manager_options['customers_aweber_list']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes'); ?>" /></p>
</form>
</div>
</div>