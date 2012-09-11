<?php global $wpdb;
$back_office_options = get_option('affiliation_manager_back_office');
include 'admin-pages.php';
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!affiliation_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'affiliation-manager'); }
else {
include 'initial-options.php';
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = stripslashes(html_entity_decode(str_replace('&nbsp;', ' ', $value))); } }
$back_office_options = update_affiliation_manager_back_office($back_office_options, 'instant_notifications');

foreach (array(
'affiliate_notification_email_deactivated',
'affiliate_notification_email_sent',
'client_notification_email_deactivated',
'client_notification_email_sent',
'message_notification_email_deactivated',
'order_notification_email_deactivated',
'prospect_notification_email_deactivated',
'recurring_payment_notification_email_deactivated') as $field) { if ($_POST[$field] != 'yes') { $_POST[$field] = 'no'; } }
foreach ($initial_options['instant_notifications'] as $key => $value) {
if ($_POST[$key] != '') { $options[$key] = $_POST[$key]; }
else { $options[$key] = $value; } }
update_option('affiliation_manager_instant_notifications', $options);
foreach (array(
'affiliate_notification_email_body',
'client_notification_email_body',
'message_notification_email_body',
'order_notification_email_body',
'prospect_notification_email_body',
'recurring_payment_notification_email_body') as $field) {
if ($_POST[$field] == '') { $_POST[$field] = $initial_options[$field]; }
update_option('affiliation_manager_'.$field, $_POST[$field]); } } }
if (!isset($options)) { $options = (array) get_option('affiliation_manager_instant_notifications'); }

foreach ($options as $key => $value) {
if (is_string($value)) { $options[$key] = htmlspecialchars($value); } }
$undisplayed_modules = (array) $back_office_options['instant_notifications_page_undisplayed_modules']; ?>

<div class="wrap">
<div id="poststuff">
<?php affiliation_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>'; } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php affiliation_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<p class="description"><?php _e('You can reset an option by leaving the corresponding field blank.', 'affiliation-manager'); ?></p>
<?php affiliation_manager_pages_summary($back_office_options); ?>

<div class="postbox" id="affiliate-notification-email-module"<?php if (in_array('affiliate-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="affiliate-notification-email"><strong><?php echo $modules['instant_notifications']['affiliate-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="affiliate_notification_email_deactivated" id="affiliate_notification_email_deactivated" value="yes"<?php if ($options['affiliate_notification_email_deactivated'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Deactivate the sending of notifications', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="affiliate_notification_email_sent" id="affiliate_notification_email_sent" value="yes"<?php if ($options['affiliate_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send an email to the affiliate when he refers an affiliate', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliate_notification_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="affiliate_notification_email_sender" id="affiliate_notification_email_sender" rows="1" cols="75"><?php echo $options['affiliate_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliate_notification_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="affiliate_notification_email_receiver" id="affiliate_notification_email_receiver" rows="1" cols="75"><?php echo $options['affiliate_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliate_notification_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="affiliate_notification_email_subject" id="affiliate_notification_email_subject" rows="1" cols="75"><?php echo $options['affiliate_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliate_notification_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="affiliate_notification_email_body" id="affiliate_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_affiliate_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="client-notification-email-module"<?php if (in_array('client-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="client-notification-email"><strong><?php echo $modules['instant_notifications']['client-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="client_notification_email_deactivated" id="client_notification_email_deactivated" value="yes"<?php if ($options['client_notification_email_deactivated'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Deactivate the sending of notifications', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="client_notification_email_sent" id="client_notification_email_sent" value="yes"<?php if ($options['client_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send an email to the affiliate when he refers a client', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="client_notification_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="client_notification_email_sender" id="client_notification_email_sender" rows="1" cols="75"><?php echo $options['client_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="client_notification_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="client_notification_email_receiver" id="client_notification_email_receiver" rows="1" cols="75"><?php echo $options['client_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="client_notification_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="client_notification_email_subject" id="client_notification_email_subject" rows="1" cols="75"><?php echo $options['client_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="client_notification_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="client_notification_email_body" id="client_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_client_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the client.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="order-notification-email-module"<?php if (in_array('order-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="order-notification-email"><strong><?php echo $modules['instant_notifications']['order-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="order_notification_email_deactivated" id="order_notification_email_deactivated" value="yes"<?php if ($options['order_notification_email_deactivated'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Deactivate the sending of notifications', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_notification_email_sent"><?php _e('Send an email to the affiliate when he refers an order', 'affiliation-manager'); ?></label></strong></th>
<td><select name="order_notification_email_sent" id="order_notification_email_sent">
<option value="yes"<?php if ($options['order_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'affiliation-manager'); ?></option>
<option value="no"<?php if ($options['order_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'affiliation-manager'); ?></option>
<option value="if commission"<?php if ($options['order_notification_email_sent'] == 'if commission') { echo ' selected="selected"'; } ?>><?php _e('If the amount of commission is not 0', 'affiliation-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_notification_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_notification_email_sender" id="order_notification_email_sender" rows="1" cols="75"><?php echo $options['order_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_notification_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_notification_email_receiver" id="order_notification_email_receiver" rows="1" cols="75"><?php echo $options['order_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_notification_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_notification_email_subject" id="order_notification_email_subject" rows="1" cols="75"><?php echo $options['order_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_notification_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="order_notification_email_body" id="order_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_order_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="recurring-payment-notification-email-module"<?php if (in_array('recurring-payment-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="recurring-payment-notification-email"><strong><?php echo $modules['instant_notifications']['recurring-payment-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="recurring_payment_notification_email_deactivated" id="recurring_payment_notification_email_deactivated" value="yes"<?php if ($options['recurring_payment_notification_email_deactivated'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Deactivate the sending of notifications', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_notification_email_sent"><?php _e('Send an email to the affiliate when he refers a recurring payment', 'affiliation-manager'); ?></label></strong></th>
<td><select name="recurring_payment_notification_email_sent" id="recurring_payment_notification_email_sent">
<option value="yes"<?php if ($options['recurring_payment_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'affiliation-manager'); ?></option>
<option value="no"<?php if ($options['recurring_payment_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'affiliation-manager'); ?></option>
<option value="if commission"<?php if ($options['recurring_payment_notification_email_sent'] == 'if commission') { echo ' selected="selected"'; } ?>><?php _e('If the amount of commission is not 0', 'affiliation-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_notification_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_notification_email_sender" id="recurring_payment_notification_email_sender" rows="1" cols="75"><?php echo $options['recurring_payment_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_notification_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_notification_email_receiver" id="recurring_payment_notification_email_receiver" rows="1" cols="75"><?php echo $options['recurring_payment_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_notification_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_notification_email_subject" id="recurring_payment_notification_email_subject" rows="1" cols="75"><?php echo $options['recurring_payment_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_notification_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="recurring_payment_notification_email_body" id="recurring_payment_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_recurring_payment_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product, the order and the payment.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="prospect-notification-email-module"<?php if (in_array('prospect-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="prospect-notification-email"><strong><?php echo $modules['instant_notifications']['prospect-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="prospect_notification_email_deactivated" id="prospect_notification_email_deactivated" value="yes"<?php if ($options['prospect_notification_email_deactivated'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Deactivate the sending of notifications', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_notification_email_sent"><?php _e('Send an email to the affiliate when he refers a prospect', 'affiliation-manager'); ?></label></strong></th>
<td><select name="prospect_notification_email_sent" id="prospect_notification_email_sent">
<option value="yes"<?php if ($options['prospect_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'affiliation-manager'); ?></option>
<option value="no"<?php if ($options['prospect_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'affiliation-manager'); ?></option>
<option value="if commission"<?php if ($options['prospect_notification_email_sent'] == 'if commission') { echo ' selected="selected"'; } ?>><?php _e('If the amount of commission is not 0', 'affiliation-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_notification_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="prospect_notification_email_sender" id="prospect_notification_email_sender" rows="1" cols="75"><?php echo $options['prospect_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_notification_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="prospect_notification_email_receiver" id="prospect_notification_email_receiver" rows="1" cols="75"><?php echo $options['prospect_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_notification_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="prospect_notification_email_subject" id="prospect_notification_email_subject" rows="1" cols="75"><?php echo $options['prospect_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_notification_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="prospect_notification_email_body" id="prospect_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_prospect_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the prospect and the form.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="message-notification-email-module"<?php if (in_array('message-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="message-notification-email"><strong><?php echo $modules['instant_notifications']['message-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="message_notification_email_deactivated" id="message_notification_email_deactivated" value="yes"<?php if ($options['message_notification_email_deactivated'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Deactivate the sending of notifications', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="message_notification_email_sent"><?php _e('Send an email to the affiliate when he refers a message', 'affiliation-manager'); ?></label></strong></th>
<td><select name="message_notification_email_sent" id="message_notification_email_sent">
<option value="yes"<?php if ($options['message_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'affiliation-manager'); ?></option>
<option value="no"<?php if ($options['message_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'affiliation-manager'); ?></option>
<option value="if commission"<?php if ($options['message_notification_email_sent'] == 'if commission') { echo ' selected="selected"'; } ?>><?php _e('If the amount of commission is not 0', 'affiliation-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="message_notification_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="message_notification_email_sender" id="message_notification_email_sender" rows="1" cols="75"><?php echo $options['message_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="message_notification_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="message_notification_email_receiver" id="message_notification_email_receiver" rows="1" cols="75"><?php echo $options['message_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="message_notification_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="message_notification_email_subject" id="message_notification_email_subject" rows="1" cols="75"><?php echo $options['message_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="message_notification_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="message_notification_email_body" id="message_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_message_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the sender, the message and the form.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/contact-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes'); ?>" /></p>
<?php affiliation_manager_pages_module($back_office_options, 'instant-notifications-page', $undisplayed_modules); ?>
</form>
</div>
</div>