<?php global $wpdb;
$back_office_options = get_option('commerce_manager_back_office');

if (($_GET['action'] == 'reset') || ($_GET['action'] == 'uninstall')) {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!commerce_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'commerce-manager'); }
else { if ($_GET['action'] == 'reset') { reset_commerce_manager(); } else { uninstall_commerce_manager(); } } } ?>
<div class="wrap">
<div id="poststuff">
<?php commerce_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) {
echo '<div class="updated"><p><strong>'.($_GET['action'] == 'reset' ? __('Options reset.', 'commerce-manager') : __('Options and tables deleted.', 'commerce-manager')).'</strong></p></div>
<script type="text/javascript">setTimeout(\'window.location = "'.($_GET['action'] == 'reset' ? 'admin.php?page=commerce-manager' : 'plugins.php').'"\', 2000);</script>'; } ?>
<?php commerce_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php if ($_GET['action'] == 'reset') { _e('Do you really want to reset the options of Commerce Manager?', 'commerce-manager'); }
else { _e('Do you really want to permanently delete the options and tables of Commerce Manager?', 'commerce-manager'); } ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'commerce-manager'); ?>" />
</div>
</form><?php } ?>
</div>
</div><?php }

else {
include 'admin-pages.php';
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!commerce_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'commerce-manager'); }
else {
include 'initial-options.php';
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = stripslashes(html_entity_decode(str_replace('&nbsp;', ' ', $value))); } }
$back_office_options = update_commerce_manager_back_office($back_office_options, 'options');

foreach (array(
'customer_subscribed_as_a_client',
'customer_subscribed_as_a_user',
'customer_subscribed_to_affiliate_program',
'customer_subscribed_to_autoresponder',
'customer_subscribed_to_members_areas',
'customer_unsubscribed_from_members_areas',
'default_shipping_cost_applied',
'default_tax_applied',
'first_payment_amount_used',
'first_payment_period_used',
'order_confirmation_email_sent',
'order_custom_instructions_executed',
'order_notification_email_sent',
'order_processing_custom_instructions_executed',
'order_refund_custom_instructions_executed',
'order_refund_notification_email_sent',
'order_removal_custom_instructions_executed',
'payments_profile_deactivation_custom_instructions_executed',
'payments_profile_deactivation_notification_email_sent',
'recurring_payment_confirmation_email_sent',
'recurring_payment_custom_instructions_executed',
'recurring_payment_notification_email_sent',
'recurring_payment_refund_custom_instructions_executed',
'recurring_payment_refund_notification_email_sent',
'recurring_payment_removal_custom_instructions_executed',
'redelivery_notification_email_sent',
'shipping_address_required',
'tax_applied',
'tax_included_in_price') as $field) { if ($_POST[$field] != 'yes') { $_POST[$field] = 'no'; } }
foreach (array(
'first_product_id',
'default_quantity',
'first_payment_period_quantity',
'payments_period_quantity') as $field) { $_POST[$field] = (int) $_POST[$field]; if ($_POST[$field] < 1) { $_POST[$field] = $initial_options[''][$field]; } }
foreach (array(
'encrypted_urls_validity_duration',
'shipping_cost',
'tax_percentage',
'weight') as $field) { $_POST[$field] = str_replace(array('?', ',', ';'), '.', $_POST[$field]); }
$members_areas = array_unique(preg_split('#[^0-9]#', $_POST['customer_members_areas'], 0, PREG_SPLIT_NO_EMPTY));
sort($members_areas, SORT_NUMERIC);
foreach ($members_areas as $member_area) { $members_areas_list .= $member_area.', '; }
$_POST['customer_members_areas'] = substr($members_areas_list, 0, -2);
switch ($_POST['payments_number']) { case 0: case '': case 'i': case 'infinite': case 'u': $_POST['payments_number'] = 'unlimited'; }
foreach ($initial_options[''] as $key => $value) {
if ($_POST[$key] != '') { $options[$key] = $_POST[$key]; }
else { $options[$key] = $value; } }
update_option('commerce_manager', $options);
foreach (array(
'code',
'order_confirmation_email_body',
'order_custom_instructions',
'order_notification_email_body',
'order_processing_custom_instructions',
'order_processing_notification_email_body',
'order_refund_custom_instructions',
'order_refund_notification_email_body',
'order_removal_custom_instructions',
'payments_profile_deactivation_custom_instructions',
'payments_profile_deactivation_notification_email_body',
'recurring_payment_confirmation_email_body',
'recurring_payment_custom_instructions',
'recurring_payment_notification_email_body',
'recurring_payment_refund_custom_instructions',
'recurring_payment_refund_notification_email_body',
'recurring_payment_removal_custom_instructions',
'redelivery_email_body',
'redelivery_notification_email_body') as $field) {
if ($_POST[$field] == '') { $_POST[$field] = $initial_options[$field]; }
update_option('commerce_manager_'.$field, $_POST[$field]); } } }
if (!isset($options)) { $options = (array) get_option('commerce_manager'); }

foreach ($options as $key => $value) {
if (is_string($value)) { $options[$key] = htmlspecialchars($value); } }
$undisplayed_modules = (array) $back_office_options['options_page_undisplayed_modules'];
$currency_code = do_shortcode($options['currency_code']); ?>

<div class="wrap">
<div id="poststuff">
<?php commerce_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>'; } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php commerce_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<p class="description"><?php _e('You can reset an option by leaving the corresponding field blank.', 'commerce-manager'); ?></p>
<?php commerce_manager_pages_summary($back_office_options); ?>

<div class="postbox" id="general-options-module"<?php if (in_array('general-options', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="general-options"><strong><?php echo $modules['options']['general-options']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="currency_code"><?php _e('Currency code', 'commerce-manager'); ?></label></strong></th>
<td><select name="currency_code" id="currency_code">
<?php include 'libraries/currency-codes.php';
foreach ($currency_codes as $value) {
echo '<option value="'.$value.'"'.($currency_code == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="terms_and_conditions_url"><?php _e('Terms and conditions URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="terms_and_conditions_url" id="terms_and_conditions_url" rows="1" cols="75"><?php echo $options['terms_and_conditions_url']; ?></textarea> 
<a style="vertical-align: 25%;" href="<?php echo htmlspecialchars(format_url(do_shortcode($options['terms_and_conditions_url']))); ?>"><?php _e('Link', 'commerce-manager'); ?></a></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="purchase_button_url"><?php _e('Purchase button URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="purchase_button_url" id="purchase_button_url" rows="1" cols="75"><?php echo $options['purchase_button_url']; ?></textarea> 
<a style="vertical-align: 25%;" href="<?php echo htmlspecialchars(format_url(do_shortcode($options['purchase_button_url']))); ?>"><?php _e('Link', 'commerce-manager'); ?></a><br />
<span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#purchase-buttons"><?php _e('How to display a purchase button?', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="purchase_button_text"><?php _e('Purchase button text', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="purchase_button_text" id="purchase_button_text" rows="1" cols="25"><?php echo $options['purchase_button_text']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Text displayed when the purchase button can not be displayed', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="purchase_link_text"><?php _e('Purchase link text', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="purchase_link_text" id="purchase_link_text" rows="1" cols="75"><?php echo $options['purchase_link_text']; ?></textarea><br />
<span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#purchase-links"><?php _e('How to display a purchase link?', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_confirmation_url"><?php _e('Order confirmation URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_confirmation_url" id="order_confirmation_url" rows="1" cols="75"><?php echo $options['order_confirmation_url']; ?></textarea> 
<a style="vertical-align: 25%;" href="<?php echo htmlspecialchars(format_url(do_shortcode($options['order_confirmation_url']))); ?>"><?php _e('Link', 'commerce-manager'); ?></a></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="orders_initial_status"><?php _e('Orders initial status', 'commerce-manager'); ?></label></strong></th>
<td><select name="orders_initial_status" id="orders_initial_status">
<option value="unprocessed"<?php if ($options['orders_initial_status'] == 'unprocessed') { echo ' selected="selected"'; } ?>><?php _e('Unprocessed', 'commerce-manager'); ?></option>
<option value="processed"<?php if ($options['orders_initial_status'] == 'processed') { echo ' selected="selected"'; } ?>><?php _e('Processed', 'commerce-manager'); ?></option>
</select>
<span class="description"><?php _e('Status assigned to orders upon their registration', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="tax-module"<?php if (in_array('tax', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="tax"><strong><?php echo $modules['options']['tax']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="tax_applied" id="tax_applied" value="yes"<?php if ($options['tax_applied'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Apply a tax', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="default_tax_applied" id="default_tax_applied" value="yes"<?php if ($options['default_tax_applied'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Apply the tax of the merchant account that receives payments', 'commerce-manager'); ?></label> 
<span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#tax"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="tax_included_in_price" id="tax_included_in_price" value="yes"<?php if ($options['tax_included_in_price'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Include the tax in the price', 'commerce-manager'); ?></label> 
<span class="description"><?php _e('Used if you don\'t apply the tax of the merchant account that receives payments', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="tax_percentage"><?php _e('Tax percentage', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="tax_percentage" id="tax_percentage" rows="1" cols="25"><?php echo $options['tax_percentage']; ?></textarea> <span style="vertical-align: 25%;">%</span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you don\'t apply the tax of the merchant account that receives payments', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="shipping-module"<?php if (in_array('shipping', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="shipping"><strong><?php echo $modules['options']['shipping']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="shipping_address_required" id="shipping_address_required" value="yes"<?php if ($options['shipping_address_required'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Shipping address required', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="default_shipping_cost_applied" id="default_shipping_cost_applied" value="yes"<?php if ($options['default_shipping_cost_applied'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Apply the shipping cost of the merchant account that receives payments', 'commerce-manager'); ?></label> 
<span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#shipping"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="weight"><?php _e('Weight', 'commerce-manager'); ?></label></strong></th>
<td><input type="text" name="weight" id="weight" size="5" value="<?php echo $options['weight']; ?>" /> 
<select name="weight_unit" id="weight_unit">
<option value="kilogram"<?php if ($options['weight_unit'] == 'kilogram') { echo ' selected="selected"'; } ?>><?php _e('kilogram(s)', 'commerce-manager'); ?></option>
<option value="pound"<?php if ($options['weight_unit'] == 'pound') { echo ' selected="selected"'; } ?>><?php _e('pound(s)', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="shipping_cost"><?php _e('Shipping cost', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="shipping_cost" id="shipping_cost" rows="1" cols="25"><?php echo $options['shipping_cost']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you don\'t apply the shipping cost of the merchant account that receives payments', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="recurring-payments-module"<?php if (in_array('recurring-payments', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="recurring-payments"><strong><?php echo $modules['options']['recurring-payments']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#recurring-payments"><?php _e('How to display a purchase button for recurring payments?', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_number"><?php _e('Payments number', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="payments_number" id="payments_number" rows="1" cols="25"><?php echo ($options['payments_number'] == 'unlimited' ? 'i' : $options['payments_number']); ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Enter <em><strong>i</strong></em> for an unlimited quantity.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_period_quantity"><?php _e('Payments period', 'commerce-manager'); ?></label></strong></th>
<td><input type="text" name="payments_period_quantity" id="payments_period_quantity" size="2" value="<?php echo $options['payments_period_quantity']; ?>" /> 
<select name="payments_period_time_unit" id="payments_period_time_unit">
<option value="day"<?php if ($options['payments_period_time_unit'] == 'day') { echo ' selected="selected"'; } ?>><?php _e('day(s)', 'commerce-manager'); ?></option>
<option value="week"<?php if ($options['payments_period_time_unit'] == 'week') { echo ' selected="selected"'; } ?>><?php _e('week(s)', 'commerce-manager'); ?></option>
<option value="month"<?php if ($options['payments_period_time_unit'] == 'month') { echo ' selected="selected"'; } ?>><?php _e('month(s)', 'commerce-manager'); ?></option>
<option value="year"<?php if ($options['payments_period_time_unit'] == 'year') { echo ' selected="selected"'; } ?>><?php _e('year(s)', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="first_payment_amount_used" id="first_payment_amount_used" value="yes"<?php if ($options['first_payment_amount_used'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Use an other amount for the first payment', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="first_payment_period_used" id="first_payment_period_used" value="yes"<?php if ($options['first_payment_period_used'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Use an other period for the first payment', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="first_payment_period_quantity"><?php _e('First payment\'s period', 'commerce-manager'); ?></label></strong></th>
<td><input type="text" name="first_payment_period_quantity" id="first_payment_period_quantity" size="2" value="<?php echo $options['first_payment_period_quantity']; ?>" /> 
<select name="first_payment_period_time_unit" id="first_payment_period_time_unit">
<option value="day"<?php if ($options['first_payment_period_time_unit'] == 'day') { echo ' selected="selected"'; } ?>><?php _e('day(s)', 'commerce-manager'); ?></option>
<option value="week"<?php if ($options['first_payment_period_time_unit'] == 'week') { echo ' selected="selected"'; } ?>><?php _e('week(s)', 'commerce-manager'); ?></option>
<option value="month"<?php if ($options['first_payment_period_time_unit'] == 'month') { echo ' selected="selected"'; } ?>><?php _e('month(s)', 'commerce-manager'); ?></option>
<option value="year"<?php if ($options['first_payment_period_time_unit'] == 'year') { echo ' selected="selected"'; } ?>><?php _e('year(s)', 'commerce-manager'); ?></option>
</select></td></tr>
</tbody></table>
<?php for ($i = 2; $i <= 4; $i++) { ?>
<div id="<?php echo 'payment-in-'.$i.'-times-module'; ?>"<?php if (in_array('payment-in-'.$i.'-times', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="payment-in-<?php echo $i; ?>-times"><strong><?php echo $modules['options']['recurring-payments']['modules']['payment-in-'.$i.'-times']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="purchase_button_url<?php echo $i; ?>"><?php _e('Purchase button URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="purchase_button_url<?php echo $i; ?>" id="purchase_button_url<?php echo $i; ?>" rows="1" cols="75"><?php echo $options['purchase_button_url'.$i]; ?></textarea> 
<a style="vertical-align: 25%;" href="<?php echo htmlspecialchars(format_url(do_shortcode($options['purchase_button_url'.$i]))); ?>"><?php _e('Link', 'commerce-manager'); ?></a></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="purchase_button_text<?php echo $i; ?>"><?php _e('Purchase button text', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="purchase_button_text<?php echo $i; ?>" id="purchase_button_text<?php echo $i; ?>" rows="1" cols="25"><?php echo $options['purchase_button_text'.$i]; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Text displayed when the purchase button can not be displayed', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="purchase_link_text<?php echo $i; ?>"><?php _e('Purchase link text', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="purchase_link_text<?php echo $i; ?>" id="purchase_link_text<?php echo $i; ?>" rows="1" cols="75"><?php echo $options['purchase_link_text'.$i]; ?></textarea></td></tr>
</tbody></table>
</div>
<?php } ?>
<div id="subscription-module"<?php if (in_array('subscription', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="subscription"><strong><?php echo $modules['options']['recurring-payments']['modules']['subscription']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="subscription_button_url"><?php _e('Subscription button URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="subscription_button_url" id="subscription_button_url" rows="1" cols="75"><?php echo $options['subscription_button_url']; ?></textarea> 
<a style="vertical-align: 25%;" href="<?php echo htmlspecialchars(format_url(do_shortcode($options['subscription_button_url']))); ?>"><?php _e('Link', 'commerce-manager'); ?></a></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="subscription_button_text"><?php _e('Subscription button text', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="subscription_button_text" id="subscription_button_text" rows="1" cols="25"><?php echo $options['subscription_button_text']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Text displayed when the subscription button can not be displayed', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="subscription_link_text"><?php _e('Subscription link text', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="subscription_link_text" id="subscription_link_text" rows="1" cols="75"><?php echo $options['subscription_link_text']; ?></textarea></td></tr>
</tbody></table>
</div>
<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="payment-modes-module"<?php if (in_array('payment-modes', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="payment-modes"><strong><?php echo $modules['options']['payment-modes']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="paypal_email_address"><?php _e('PayPal email address', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="paypal_email_address" id="paypal_email_address" rows="1" cols="50"><?php echo $options['paypal_email_address']; ?></textarea><br />
<span class="description" style="vertical-align: 25%;"><?php _e('Email address of the PayPal account that receives payments', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="urls-encryption-module"<?php if (in_array('urls-encryption', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="urls-encryption"><strong><?php echo $modules['options']['urls-encryption']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('You can encrypt the download URLs.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#urls-encryption"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="encrypted_urls_validity_duration"><?php _e('Validity duration', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="encrypted_urls_validity_duration" id="encrypted_urls_validity_duration" rows="1" cols="25"><?php echo $options['encrypted_urls_validity_duration']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('hours', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="encrypted_urls_key"><?php _e('Encryption key', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="encrypted_urls_key" id="encrypted_urls_key" rows="1" cols="50"><?php echo $options['encrypted_urls_key']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="order-confirmation-email-module"<?php if (in_array('order-confirmation-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="order-confirmation-email"><strong><?php echo $modules['options']['order-confirmation-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="order_confirmation_email_sent" id="order_confirmation_email_sent" value="yes"<?php if ($options['order_confirmation_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send an order confirmation email', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_confirmation_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_confirmation_email_sender" id="order_confirmation_email_sender" rows="1" cols="75"><?php echo $options['order_confirmation_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_confirmation_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_confirmation_email_receiver" id="order_confirmation_email_receiver" rows="1" cols="75"><?php echo $options['order_confirmation_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_confirmation_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_confirmation_email_subject" id="order_confirmation_email_subject" rows="1" cols="75"><?php echo $options['order_confirmation_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_confirmation_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="order_confirmation_email_body" id="order_confirmation_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('commerce_manager_order_confirmation_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="order-notification-email-module"<?php if (in_array('order-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="order-notification-email"><strong><?php echo $modules['options']['order-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="order_notification_email_sent" id="order_notification_email_sent" value="yes"<?php if ($options['order_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send an order notification email', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_notification_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_notification_email_sender" id="order_notification_email_sender" rows="1" cols="75"><?php echo $options['order_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_notification_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_notification_email_receiver" id="order_notification_email_receiver" rows="1" cols="75"><?php echo $options['order_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_notification_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_notification_email_subject" id="order_notification_email_subject" rows="1" cols="75"><?php echo $options['order_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_notification_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="order_notification_email_body" id="order_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('commerce_manager_order_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="autoresponders-module"<?php if (in_array('autoresponders', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="autoresponders"><strong><?php echo $modules['options']['autoresponders']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('You must make some adjustments so that the subscription works with some autoresponders.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#autoresponders"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="customer_subscribed_to_autoresponder" id="customer_subscribed_to_autoresponder" value="yes"<?php if ($options['customer_subscribed_to_autoresponder'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Subscribe the customer to an autoresponder list', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_autoresponder"><?php _e('Autoresponder', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_autoresponder" id="customer_autoresponder">
<?php include 'libraries/autoresponders.php';
$autoresponder = do_shortcode($options['customer_autoresponder']);
foreach ($autoresponders as $value) {
echo '<option value="'.$value.'"'.($autoresponder == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_autoresponder_list"><?php _e('List', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="customer_autoresponder_list" id="customer_autoresponder_list" rows="1" cols="50"><?php echo $options['customer_autoresponder_list']; ?></textarea><br />
<span class="description"><?php _e('For some autoresponders, you must enter the list ID.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#autoresponders"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="autoresponders-integration-module"<?php if (in_array('autoresponders-integration', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="autoresponders-integration"><strong><?php echo $modules['options']['autoresponders-integration']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('You must make some adjustments so that the subscription works with some autoresponders.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#autoresponders"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
<div id="aweber-module"<?php if (in_array('aweber', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="aweber"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['aweber']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#aweber"><?php _e('Click here to read the instructions for integration.', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="cybermailing-module"<?php if (in_array('cybermailing', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="cybermailing"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['cybermailing']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('You have no adjustment to make so that the subscription works with CyberMailing.', 'commerce-manager'); ?></span></td></tr>
</tbody></table>
</div>
<div id="getresponse-module"<?php if (in_array('getresponse', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="getresponse"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['getresponse']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="getresponse_api_key"><?php _e('API key', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="getresponse_api_key" id="getresponse_api_key" rows="1" cols="50"><?php echo $options['getresponse_api_key']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#getresponse"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="mailchimp-module"<?php if (in_array('mailchimp', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="mailchimp"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['mailchimp']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="mailchimp_api_key"><?php _e('API key', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="mailchimp_api_key" id="mailchimp_api_key" rows="1" cols="50"><?php echo $options['mailchimp_api_key']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#mailchimp"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="sg-autorepondeur-module"<?php if (in_array('sg-autorepondeur', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="sg-autorepondeur"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['sg-autorepondeur']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="sg_autorepondeur_account_id"><?php _e('Account ID', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="sg_autorepondeur_account_id" id="sg_autorepondeur_account_id" rows="1" cols="25"><?php echo $options['sg_autorepondeur_account_id']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#sg-autorepondeur"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="sg_autorepondeur_activation_code"><?php _e('Activation code', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="sg_autorepondeur_activation_code" id="sg_autorepondeur_activation_code" rows="1" cols="50"><?php echo $options['sg_autorepondeur_activation_code']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#sg-autorepondeur"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="registration-as-a-client-module"<?php if (in_array('registration-as-a-client', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration-as-a-client"><strong><?php echo $modules['options']['registration-as-a-client']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager-clients-accounts"><?php _e('Click here to configure the options of the <em>Clients Accounts</em> page.', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="customer_subscribed_as_a_client" id="customer_subscribed_as_a_client" value="yes"<?php if ($options['customer_subscribed_as_a_client'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the customer as a client', 'commerce-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#registration-as-a-client"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."commerce_manager_clients_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_client_category_id"><?php _e('Category', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_client_category_id" id="customer_client_category_id">
<option value=""<?php if ($options['customer_client_category_id'] == '') { echo ' selected="selected"'; } ?>><?php _e('Option of the Clients Accounts page', 'commerce-manager'); ?></option>
<option value="0"<?php if ($options['customer_client_category_id'] == '0') { echo ' selected="selected"'; } ?>><?php _e('None ', 'commerce-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($options['customer_client_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ($options['customer_client_category_id'] > 0) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-client-category&amp;id='.$options['customer_client_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-client-category&amp;id='.$options['customer_client_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_client_status"><?php _e('Status', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_client_status" id="customer_client_status">
<option value=""<?php if ($options['customer_client_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('Option of the Clients Accounts page', 'commerce-manager'); ?></option>
<option value="active"<?php if ($options['customer_client_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'commerce-manager'); ?></option>
<option value="inactive"<?php if ($options['customer_client_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'commerce-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#client-status"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commerce_registration_confirmation_email_sent"><?php _e('Send a registration confirmation email', 'commerce-manager'); ?></label></strong></th>
<td><select name="commerce_registration_confirmation_email_sent" id="commerce_registration_confirmation_email_sent">
<option value=""<?php if ($options['commerce_registration_confirmation_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Option of the Clients Accounts page', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($options['commerce_registration_confirmation_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($options['commerce_registration_confirmation_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commerce_registration_notification_email_sent"><?php _e('Send a registration notification email', 'commerce-manager'); ?></label></strong></th>
<td><select name="commerce_registration_notification_email_sent" id="commerce_registration_notification_email_sent">
<option value=""<?php if ($options['commerce_registration_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Option of the Clients Accounts page', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($options['commerce_registration_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($options['commerce_registration_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="registration-to-affiliate-program-module"<?php if (in_array('registration-to-affiliate-program', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration-to-affiliate-program"><strong><?php echo $modules['options']['registration-to-affiliate-program']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php echo (function_exists('affiliation_manager_admin_menu') ? '<a href="admin.php?page=affiliation-manager">'.__('Click here to configure the options of Affiliation Manager.', 'commerce-manager').'</a>' : __('To use affiliation, you must have installed and activated <a href="http://www.kleor-editions.com/affiliation-manager">Affiliation Manager</a>.', 'commerce-manager')); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="customer_subscribed_to_affiliate_program" id="customer_subscribed_to_affiliate_program" value="yes"<?php if ($options['customer_subscribed_to_affiliate_program'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the customer to affiliate program', 'commerce-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#registration-to-affiliate-program"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."affiliation_manager_affiliates_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_affiliate_category_id"><?php _e('Category', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_affiliate_category_id" id="customer_affiliate_category_id">
<option value=""<?php if ($options['customer_affiliate_category_id'] == '') { echo ' selected="selected"'; } ?>><?php _e('Affiliation Manager\'s option', 'commerce-manager'); ?></option>
<option value="0"<?php if ($options['customer_affiliate_category_id'] == '0') { echo ' selected="selected"'; } ?>><?php _e('None ', 'commerce-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($options['customer_affiliate_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ((function_exists('affiliation_manager_admin_menu')) && ($options['customer_affiliate_category_id'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$options['customer_affiliate_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$options['customer_affiliate_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_affiliate_status"><?php _e('Status', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_affiliate_status" id="customer_affiliate_status">
<option value=""<?php if ($options['customer_affiliate_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('Affiliation Manager\'s option', 'commerce-manager'); ?></option>
<option value="active"<?php if ($options['customer_affiliate_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'commerce-manager'); ?></option>
<option value="inactive"<?php if ($options['customer_affiliate_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'commerce-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#affiliate-status"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliation_registration_confirmation_email_sent"><?php _e('Send a registration confirmation email', 'commerce-manager'); ?></label></strong></th>
<td><select name="affiliation_registration_confirmation_email_sent" id="affiliation_registration_confirmation_email_sent">
<option value=""<?php if ($options['affiliation_registration_confirmation_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Affiliation Manager\'s option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($options['affiliation_registration_confirmation_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($options['affiliation_registration_confirmation_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliation_registration_notification_email_sent"><?php _e('Send a registration notification email', 'commerce-manager'); ?></label></strong></th>
<td><select name="affiliation_registration_notification_email_sent" id="affiliation_registration_notification_email_sent">
<option value=""<?php if ($options['affiliation_registration_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Affiliation Manager\'s option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($options['affiliation_registration_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($options['affiliation_registration_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="membership-module"<?php if (in_array('membership', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="membership"><strong><?php echo $modules['options']['membership']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php echo (function_exists('membership_manager_admin_menu') ? '<a href="admin.php?page=membership-manager">'.__('Click here to configure the options of Membership Manager.', 'commerce-manager').'</a>' : __('To use membership, you must have installed and activated <a href="http://www.kleor-editions.com/membership-manager">Membership Manager</a>.', 'commerce-manager')); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="customer_subscribed_to_members_areas" id="customer_subscribed_to_members_areas" value="yes"<?php if ($options['customer_subscribed_to_members_areas'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the customer to a member area', 'commerce-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#membership"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_members_areas"><?php _e('Members areas', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="customer_members_areas" id="customer_members_areas" rows="1" cols="50"><?php echo $options['customer_members_areas']; ?></textarea>
<?php if ((function_exists('membership_manager_admin_menu')) && (is_numeric($options['customer_members_areas'])) && ($options['customer_members_areas'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-area&amp;id='.$options['customer_members_areas'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-area&amp;id='.$options['customer_members_areas'].'&amp;action=delete">'.__('Delete').'</a>'; } ?><br />
<span class="description"><?php _e('Separate the IDs of the members areas with commas.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="customer_unsubscribed_from_members_areas" id="customer_unsubscribed_from_members_areas" value="yes"<?php if ($options['customer_unsubscribed_from_members_areas'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Unsubscribe the customer from this member area when his order is refunded or when his recurring payments profile is deactivated', 'commerce-manager'); ?></label></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."membership_manager_members_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_member_category_id"><?php _e('Category', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_member_category_id" id="customer_member_category_id">
<option value=""<?php if ($options['customer_member_category_id'] == '') { echo ' selected="selected"'; } ?>><?php _e('Member area\'s option', 'commerce-manager'); ?></option>
<option value="0"<?php if ($options['customer_member_category_id'] == '0') { echo ' selected="selected"'; } ?>><?php _e('None ', 'commerce-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($options['customer_member_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ((function_exists('membership_manager_admin_menu')) && ($options['customer_member_category_id'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-category&amp;id='.$options['customer_member_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-category&amp;id='.$options['customer_member_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_member_status"><?php _e('Status', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_member_status" id="customer_member_status">
<option value=""<?php if ($options['customer_member_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('Member area\'s option', 'commerce-manager'); ?></option>
<option value="active"<?php if ($options['customer_member_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'commerce-manager'); ?></option>
<option value="inactive"<?php if ($options['customer_member_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'commerce-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#member-status"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="membership_registration_confirmation_email_sent"><?php _e('Send a registration confirmation email', 'commerce-manager'); ?></label></strong></th>
<td><select name="membership_registration_confirmation_email_sent" id="membership_registration_confirmation_email_sent">
<option value=""<?php if ($options['membership_registration_confirmation_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Member area\'s option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($options['membership_registration_confirmation_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($options['membership_registration_confirmation_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="membership_registration_notification_email_sent"><?php _e('Send a registration notification email', 'commerce-manager'); ?></label></strong></th>
<td><select name="membership_registration_notification_email_sent" id="membership_registration_notification_email_sent">
<option value=""<?php if ($options['membership_registration_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Member area\'s option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($options['membership_registration_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($options['membership_registration_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="wordpress-module"<?php if (in_array('wordpress', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="wordpress"><strong><?php echo $modules['options']['wordpress']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="customer_subscribed_as_a_user" id="customer_subscribed_as_a_user" value="yes"<?php if ($options['customer_subscribed_as_a_user'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the customer as a user', 'commerce-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#wordpress"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_user_role"><?php _e('Role', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_user_role" id="customer_user_role">
<?php foreach (commerce_manager_users_roles() as $role => $name) {
echo '<option value="'.$role.'"'.($options['customer_user_role'] == $role ? ' selected="selected"' : '').'>'.$name.'</option>'."\n"; } ?>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="custom-instructions-module"<?php if (in_array('custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['name']; ?></strong></h3>
<div class="inside">
<div id="order-custom-instructions-module"<?php if (in_array('order-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="order-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['order-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="order_custom_instructions_executed" id="order_custom_instructions_executed" value="yes"<?php if ($options['order_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_custom_instructions"><?php _e('PHP code', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="order_custom_instructions" id="order_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('commerce_manager_order_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the registration of an order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#custom-instructions"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="order-processing-custom-instructions-module"<?php if (in_array('order-processing-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="order-processing-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['order-processing-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="order_processing_custom_instructions_executed" id="order_processing_custom_instructions_executed" value="yes"<?php if ($options['order_processing_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_processing_custom_instructions"><?php _e('PHP code', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="order_processing_custom_instructions" id="order_processing_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('commerce_manager_order_processing_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the processing of an order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#custom-instructions"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="order-refund-custom-instructions-module"<?php if (in_array('order-refund-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="order-refund-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['order-refund-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="order_refund_custom_instructions_executed" id="order_refund_custom_instructions_executed" value="yes"<?php if ($options['order_refund_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_refund_custom_instructions"><?php _e('PHP code', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="order_refund_custom_instructions" id="order_refund_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('commerce_manager_order_refund_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the refund of an order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#custom-instructions"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="order-removal-custom-instructions-module"<?php if (in_array('order-removal-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="order-removal-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['order-removal-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="order_removal_custom_instructions_executed" id="order_removal_custom_instructions_executed" value="yes"<?php if ($options['order_removal_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_removal_custom_instructions"><?php _e('PHP code', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="order_removal_custom_instructions" id="order_removal_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('commerce_manager_order_removal_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the removal of an order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#custom-instructions"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="recurring-payment-custom-instructions-module"<?php if (in_array('recurring-payment-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="recurring-payment-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['recurring-payment-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="recurring_payment_custom_instructions_executed" id="recurring_payment_custom_instructions_executed" value="yes"<?php if ($options['recurring_payment_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_custom_instructions"><?php _e('PHP code', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="recurring_payment_custom_instructions" id="recurring_payment_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('commerce_manager_recurring_payment_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the registration of a recurring payment.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#custom-instructions"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="recurring-payment-refund-custom-instructions-module"<?php if (in_array('recurring-payment-refund-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="recurring-payment-refund-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['recurring-payment-refund-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="recurring_payment_refund_custom_instructions_executed" id="recurring_payment_refund_custom_instructions_executed" value="yes"<?php if ($options['recurring_payment_refund_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_refund_custom_instructions"><?php _e('PHP code', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="recurring_payment_refund_custom_instructions" id="recurring_payment_refund_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('commerce_manager_recurring_payment_refund_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the refund of a recurring payment.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#custom-instructions"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="recurring-payment-removal-custom-instructions-module"<?php if (in_array('recurring-payment-removal-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="recurring-payment-removal-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['recurring-payment-removal-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="recurring_payment_removal_custom_instructions_executed" id="recurring_payment_removal_custom_instructions_executed" value="yes"<?php if ($options['recurring_payment_removal_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_removal_custom_instructions"><?php _e('PHP code', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="recurring_payment_removal_custom_instructions" id="recurring_payment_removal_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('commerce_manager_recurring_payment_removal_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the removal of a recurring payment.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#custom-instructions"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="recurring-payments-profile-deactivation-custom-instructions-module"<?php if (in_array('recurring-payments-profile-deactivation-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="recurring-payments-profile-deactivation-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['recurring-payments-profile-deactivation-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="payments_profile_deactivation_custom_instructions_executed" id="payments_profile_deactivation_custom_instructions_executed" value="yes"<?php if ($options['payments_profile_deactivation_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_profile_deactivation_custom_instructions"><?php _e('PHP code', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="payments_profile_deactivation_custom_instructions" id="payments_profile_deactivation_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('commerce_manager_payments_profile_deactivation_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the deactivation of a recurring payments profile.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#custom-instructions"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="redelivery-email-module"<?php if (in_array('redelivery-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="redelivery-email"><strong><?php echo $modules['options']['redelivery-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="redelivery_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="redelivery_email_sender" id="redelivery_email_sender" rows="1" cols="75"><?php echo $options['redelivery_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="redelivery_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="redelivery_email_receiver" id="redelivery_email_receiver" rows="1" cols="75"><?php echo $options['redelivery_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="redelivery_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="redelivery_email_subject" id="redelivery_email_subject" rows="1" cols="75"><?php echo $options['redelivery_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="redelivery_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="redelivery_email_body" id="redelivery_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('commerce_manager_redelivery_email_body')); ?></textarea>
<span class="description"><?php _e('You can allow your customers to get an automatic redelivery of a downloadable product they ordered.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#automatic-redelivery"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="redelivery-notification-email-module"<?php if (in_array('redelivery-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="redelivery-notification-email"><strong><?php echo $modules['options']['redelivery-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="redelivery_notification_email_sent" id="redelivery_notification_email_sent" value="yes"<?php if ($options['redelivery_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a redelivery notification email', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="redelivery_notification_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="redelivery_notification_email_sender" id="redelivery_notification_email_sender" rows="1" cols="75"><?php echo $options['redelivery_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="redelivery_notification_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="redelivery_notification_email_receiver" id="redelivery_notification_email_receiver" rows="1" cols="75"><?php echo $options['redelivery_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="redelivery_notification_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="redelivery_notification_email_subject" id="redelivery_notification_email_subject" rows="1" cols="75"><?php echo $options['redelivery_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="redelivery_notification_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="redelivery_notification_email_body" id="redelivery_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('commerce_manager_redelivery_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="order-processing-notification-email-module"<?php if (in_array('order-processing-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="order-processing-notification-email"><strong><?php echo $modules['options']['order-processing-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_processing_notification_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_processing_notification_email_sender" id="order_processing_notification_email_sender" rows="1" cols="75"><?php echo $options['order_processing_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_processing_notification_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_processing_notification_email_receiver" id="order_processing_notification_email_receiver" rows="1" cols="75"><?php echo $options['order_processing_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_processing_notification_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_processing_notification_email_subject" id="order_processing_notification_email_subject" rows="1" cols="75"><?php echo $options['order_processing_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_processing_notification_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="order_processing_notification_email_body" id="order_processing_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('commerce_manager_order_processing_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="order-refund-notification-email-module"<?php if (in_array('order-refund-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="order-refund-notification-email"><strong><?php echo $modules['options']['order-refund-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="order_refund_notification_email_sent" id="order_refund_notification_email_sent" value="yes"<?php if ($options['order_refund_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send an order refund\'s notification email', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_refund_notification_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_refund_notification_email_sender" id="order_refund_notification_email_sender" rows="1" cols="75"><?php echo $options['order_refund_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_refund_notification_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_refund_notification_email_receiver" id="order_refund_notification_email_receiver" rows="1" cols="75"><?php echo $options['order_refund_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_refund_notification_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_refund_notification_email_subject" id="order_refund_notification_email_subject" rows="1" cols="75"><?php echo $options['order_refund_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_refund_notification_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="order_refund_notification_email_body" id="order_refund_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('commerce_manager_order_refund_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="recurring-payment-confirmation-email-module"<?php if (in_array('recurring-payment-confirmation-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="recurring-payment-confirmation-email"><strong><?php echo $modules['options']['recurring-payment-confirmation-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="recurring_payment_confirmation_email_sent" id="recurring_payment_confirmation_email_sent" value="yes"<?php if ($options['recurring_payment_confirmation_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a recurring payment confirmation email', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_confirmation_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_confirmation_email_sender" id="recurring_payment_confirmation_email_sender" rows="1" cols="75"><?php echo $options['recurring_payment_confirmation_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_confirmation_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_confirmation_email_receiver" id="recurring_payment_confirmation_email_receiver" rows="1" cols="75"><?php echo $options['recurring_payment_confirmation_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_confirmation_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_confirmation_email_subject" id="recurring_payment_confirmation_email_subject" rows="1" cols="75"><?php echo $options['recurring_payment_confirmation_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_confirmation_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="recurring_payment_confirmation_email_body" id="recurring_payment_confirmation_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('commerce_manager_recurring_payment_confirmation_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product, the order and the payment.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="recurring-payment-notification-email-module"<?php if (in_array('recurring-payment-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="recurring-payment-notification-email"><strong><?php echo $modules['options']['recurring-payment-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="recurring_payment_notification_email_sent" id="recurring_payment_notification_email_sent" value="yes"<?php if ($options['recurring_payment_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a recurring payment notification email', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_notification_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_notification_email_sender" id="recurring_payment_notification_email_sender" rows="1" cols="75"><?php echo $options['recurring_payment_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_notification_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_notification_email_receiver" id="recurring_payment_notification_email_receiver" rows="1" cols="75"><?php echo $options['recurring_payment_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_notification_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_notification_email_subject" id="recurring_payment_notification_email_subject" rows="1" cols="75"><?php echo $options['recurring_payment_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_notification_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="recurring_payment_notification_email_body" id="recurring_payment_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('commerce_manager_recurring_payment_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product, the order and the payment.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="recurring-payment-refund-notification-email-module"<?php if (in_array('recurring-payment-refund-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="recurring-payment-refund-notification-email"><strong><?php echo $modules['options']['recurring-payment-refund-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="recurring_payment_refund_notification_email_sent" id="recurring_payment_refund_notification_email_sent" value="yes"<?php if ($options['recurring_payment_refund_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a recurring payment refund\'s notification email', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_refund_notification_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_refund_notification_email_sender" id="recurring_payment_refund_notification_email_sender" rows="1" cols="75"><?php echo $options['recurring_payment_refund_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_refund_notification_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_refund_notification_email_receiver" id="recurring_payment_refund_notification_email_receiver" rows="1" cols="75"><?php echo $options['recurring_payment_refund_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_refund_notification_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_refund_notification_email_subject" id="recurring_payment_refund_notification_email_subject" rows="1" cols="75"><?php echo $options['recurring_payment_refund_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_refund_notification_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="recurring_payment_refund_notification_email_body" id="recurring_payment_refund_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('commerce_manager_recurring_payment_refund_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product, the order and the payment.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="recurring-payments-profile-deactivation-notification-email-module"<?php if (in_array('recurring-payments-profile-deactivation-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="recurring-payments-profile-deactivation-notification-email"><strong><?php echo $modules['options']['recurring-payments-profile-deactivation-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="payments_profile_deactivation_notification_email_sent" id="payments_profile_deactivation_notification_email_sent" value="yes"<?php if ($options['payments_profile_deactivation_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a recurring payments profile deactivation\'s notification email', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_profile_deactivation_notification_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="payments_profile_deactivation_notification_email_sender" id="payments_profile_deactivation_notification_email_sender" rows="1" cols="75"><?php echo $options['payments_profile_deactivation_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_profile_deactivation_notification_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="payments_profile_deactivation_notification_email_receiver" id="payments_profile_deactivation_notification_email_receiver" rows="1" cols="75"><?php echo $options['payments_profile_deactivation_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_profile_deactivation_notification_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="payments_profile_deactivation_notification_email_subject" id="payments_profile_deactivation_notification_email_subject" rows="1" cols="75"><?php echo $options['payments_profile_deactivation_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_profile_deactivation_notification_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="payments_profile_deactivation_notification_email_body" id="payments_profile_deactivation_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('commerce_manager_payments_profile_deactivation_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="forms-module"<?php if (in_array('forms', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="forms"><strong><?php echo $modules['options']['forms']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="code"><?php _e('Purchase form code', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="code" id="code" rows="15" cols="75"><?php echo htmlspecialchars(get_option('commerce_manager_code')); ?></textarea>
<span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#purchase-forms"><?php _e('How to display a purchase form?', 'commerce-manager'); ?></a><br />
<a href="http://www.kleor-editions.com/commerce-manager/documentation/#purchase-forms-creation"><?php _e('How to create a purchase form?', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
<div id="default-values-module"<?php if (in_array('default-values', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="default-values"><strong><?php echo $modules['options']['forms']['modules']['default-values']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('These values are used when the corresponding field is missing from the form, or is not filled.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="default_product_id"><?php _e('Product ID', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="default_product_id" id="default_product_id" rows="1" cols="25"><?php echo $options['default_product_id']; ?></textarea><br />
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-product&amp;id=<?php echo $options['default_product_id']; ?>"><?php _e('Edit'); ?></a> | 
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-product&amp;id=<?php echo $options['default_product_id']; ?>&amp;action=delete"><?php _e('Delete'); ?></a> | 
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-statistics&amp;product_id=<?php echo $options['default_product_id']; ?>"><?php _e('Statistics', 'commerce-manager'); ?></a></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="default_quantity"><?php _e('Quantity', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="default_quantity" id="default_quantity" rows="1" cols="25"><?php echo $options['default_quantity']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="default_payment_option"><?php _e('Payment option', 'commerce-manager'); ?></label></strong></th>
<td><select name="default_payment_option" id="default_payment_option">
<?php $payment_option = do_shortcode($options['default_payment_option']);
for ($i = 0; $i < 4; $i++) { echo '<option value="'.$i.'"'.($payment_option == $i ? ' selected="selected"' : '').'>'.$i.'</option>'."\n"; } ?>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="default_payment_mode"><?php _e('Payment mode', 'commerce-manager'); ?></label></strong></th>
<td><select name="default_payment_mode" id="default_payment_mode">
<?php include 'gateways/payment-modes.php';
$payment_mode = do_shortcode($options['default_payment_mode']);
foreach ($payment_modes as $value) {
echo '<option value="'.$value.'"'.($payment_mode == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
</tbody></table>
</div>
<div id="captcha-module"<?php if (in_array('captcha', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="captcha"><strong><?php echo $modules['options']['forms']['modules']['captcha']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="default_captcha_type"><?php _e('Default type', 'commerce-manager'); ?></label></strong></th>
<td><select name="default_captcha_type" id="default_captcha_type">
<?php include 'libraries/captchas.php';
$captcha_type = do_shortcode($options['default_captcha_type']);
asort($captchas_types);
foreach ($captchas_types as $key => $value) {
echo '<option value="'.$key.'"'.($captcha_type == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select>
<span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#captcha"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="default_recaptcha_theme"><?php _e('Default reCAPTCHA theme', 'commerce-manager'); ?></label></strong></th>
<td><select name="default_recaptcha_theme" id="default_recaptcha_theme">
<?php include 'libraries/captchas.php';
$recaptcha_theme = do_shortcode($options['default_recaptcha_theme']);
asort($recaptcha_themes);
foreach ($recaptcha_themes as $key => $value) {
echo '<option value="'.$key.'"'.($recaptcha_theme == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select>
<span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#recaptcha"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recaptcha_public_key"><?php _e('reCAPTCHA public key', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="recaptcha_public_key" id="recaptcha_public_key" rows="1" cols="50"><?php echo $options['recaptcha_public_key']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#recaptcha"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recaptcha_private_key"><?php _e('reCAPTCHA private key', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="recaptcha_private_key" id="recaptcha_private_key" rows="1" cols="50"><?php echo $options['recaptcha_private_key']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#recaptcha"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="error-messages-module"<?php if (in_array('error-messages', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="error-messages"><strong><?php echo $modules['options']['forms']['modules']['error-messages']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="unfilled_fields_message"><?php _e('Unfilled required fields', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="unfilled_fields_message" id="unfilled_fields_message" rows="1" cols="75"><?php echo $options['unfilled_fields_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="unfilled_field_message"><?php _e('Unfilled required field', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="unfilled_field_message" id="unfilled_field_message" rows="1" cols="75"><?php echo $options['unfilled_field_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="invalid_email_address_message"><?php _e('Invalid email address', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="invalid_email_address_message" id="invalid_email_address_message" rows="1" cols="75"><?php echo $options['invalid_email_address_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="numeric_login_message"><?php _e('Numeric login name', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="numeric_login_message" id="numeric_login_message" rows="1" cols="75"><?php echo $options['numeric_login_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="too_short_login_message"><?php _e('Too short login name', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="too_short_login_message" id="too_short_login_message" rows="1" cols="75"><?php echo $options['too_short_login_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="too_long_login_message"><?php _e('Too long login name', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="too_long_login_message" id="too_long_login_message" rows="1" cols="75"><?php echo $options['too_long_login_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="too_short_password_message"><?php _e('Too short password', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="too_short_password_message" id="too_short_password_message" rows="1" cols="75"><?php echo $options['too_short_password_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="too_long_password_message"><?php _e('Too long password', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="too_long_password_message" id="too_long_password_message" rows="1" cols="75"><?php echo $options['too_long_password_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="unavailable_login_message"><?php _e('Unavailable login name', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="unavailable_login_message" id="unavailable_login_message" rows="1" cols="75"><?php echo $options['unavailable_login_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="unavailable_email_address_message"><?php _e('Unavailable email address', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="unavailable_email_address_message" id="unavailable_email_address_message" rows="1" cols="75"><?php echo $options['unavailable_email_address_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="invalid_login_or_password_message"><?php _e('Invalid login or password', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="invalid_login_or_password_message" id="invalid_login_or_password_message" rows="1" cols="75"><?php echo $options['invalid_login_or_password_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="inactive_account_message"><?php _e('Inactive account', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="inactive_account_message" id="inactive_account_message" rows="1" cols="75"><?php echo $options['inactive_account_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="inexistent_email_address_message"><?php _e('Inexistent email address', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="inexistent_email_address_message" id="inexistent_email_address_message" rows="1" cols="75"><?php echo $options['inexistent_email_address_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="inexistent_order_message"><?php _e('Inexistent order', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="inexistent_order_message" id="inexistent_order_message" rows="1" cols="75"><?php echo $options['inexistent_order_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="invalid_captcha_message"><?php _e('Invalid CAPTCHA', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="invalid_captcha_message" id="invalid_captcha_message" rows="1" cols="75"><?php echo $options['invalid_captcha_message']; ?></textarea></td></tr>
</tbody></table>
</div>
<div id="login-availability-indicator-module"<?php if (in_array('login-availability-indicator', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="login-availability-indicator"><strong><?php echo $modules['options']['forms']['modules']['login-availability-indicator']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="available_login_indicator_message"><?php _e('Available', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="available_login_indicator_message" id="available_login_indicator_message" rows="1" cols="75"><?php echo $options['available_login_indicator_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="unavailable_login_indicator_message"><?php _e('Unavailable', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="unavailable_login_indicator_message" id="unavailable_login_indicator_message" rows="1" cols="75"><?php echo $options['unavailable_login_indicator_message']; ?></textarea></td></tr>
</tbody></table>
</div>
<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes'); ?>" /></p>
<?php commerce_manager_pages_module($back_office_options, 'options-page', $undisplayed_modules); ?>
</form>
</div>
</div>
<?php }