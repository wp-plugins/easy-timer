<?php global $wpdb;
$affiliates_table_name = $wpdb->prefix.'affiliation_manager_affiliates';
add_action('admin_footer', 'affiliation_statistics_form_js');

if ((isset($_GET['id'])) && ($_GET['action'] == 'delete')) {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
$results = $wpdb->query("DELETE FROM $affiliates_table_name WHERE id = '".$_GET['id']."'"); } ?>
<div class="wrap">
<div id="poststuff">
<?php affiliation_manager_pages_top(); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Affiliate deleted.', 'affiliation-manager').'</strong></p></div>'; } ?>
<?php affiliation_manager_pages_menu(); ?>
<div class="clear"></div>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php _e('Do you really want to permanently delete this affiliate?', 'affiliation-manager'); ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'affiliation-manager'); ?>" />
</div>
</form><?php } ?>
</div>
</div><?php }

else {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
$_POST = array_map('html_entity_decode', $_POST);
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$_POST['login'] = affiliation_format_nice_name($_POST['login']);
$_POST['email_address'] = affiliation_format_email_address($_POST['email_address']);
$_POST['paypal_email_address'] = affiliation_format_email_address($_POST['paypal_email_address']);
if ($_POST['referring_url'] == '') { $_POST['referring_url'] = $_SERVER['HTTP_REFERER']; }
if ($_POST['date'] == '') {
$_POST['date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['date_utc'] = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['date']);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['date'] = date('Y-m-d H:i:s', $time);
$_POST['date_utc'] = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); }
if (isset($_POST['referrer'])) {
if (is_numeric($_POST['referrer'])) {
$_POST['referrer'] = preg_replace('/[^0-9]/', '', $_POST['referrer']);
$result = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE id='".$_POST['referrer']."'", OBJECT);
if ($result) { $_POST['referrer'] = $result->login; } }
elseif (strstr($_POST['referrer'], '@')) {
$_POST['referrer'] = affiliation_format_email_address($_POST['referrer']);
$result = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE paypal_email_address='".$_POST['referrer']."'", OBJECT);
if ($result) { $_POST['referrer'] = $result->login; }
else { $result = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE email_address='".$_POST['referrer']."'", OBJECT);
if ($result) { $_POST['referrer'] = $result->login; } } }
else { $_POST['referrer'] = affiliation_format_nice_name($_POST['referrer']); } }
$_POST['commission_percentage'] = str_replace(array('?', ',', ';'), '.', $_POST['commission_percentage']);
$_POST['commission_amount'] = str_replace(array('?', ',', ';'), '.', $_POST['commission_amount']);

if (!isset($_GET['id'])) {
if ($_POST['password'] == '') { $_POST['password'] = substr(md5(mt_rand()), 0, 8); }
if (isset($_POST['update_fields'])) {
foreach ($_POST as $key => $value) { $_GET['affiliate_data']->$key = $value; }
$_GET['affiliate_data']->id = '{affiliate id}';
foreach (add_affiliate_fields() as $field) { $_POST[$field] = str_replace('{affiliate id}', '[affiliate id]', affiliation_data($field)); } }
else {
if (is_numeric($_POST['login'])) { $error .= __('The login name must be a non-numeric string.', 'affiliation-manager'); }
$result = $wpdb->get_results("SELECT login FROM $affiliates_table_name WHERE login='".$_POST['login']."'", OBJECT);
if ($result) { $error .= ' '.__('This login name is not available.', 'affiliation-manager'); }
$result = $wpdb->get_results("SELECT email_address FROM $affiliates_table_name WHERE email_address='".$_POST['email_address']."'", OBJECT);
if ($result) { $error .= ' '.__('This email address is not available.', 'affiliation-manager'); }
$result = $wpdb->get_results("SELECT paypal_email_address FROM $affiliates_table_name WHERE paypal_email_address='".$_POST['paypal_email_address']."'", OBJECT);
if ($result) { $error .= ' '.__('This PayPal email address is not available.', 'affiliation-manager'); }
if (($_POST['login'] == '') || ($_POST['first_name'] == '') || ($_POST['last_name'] == '') || ($_POST['email_address'] == '') || ($_POST['paypal_email_address'] == '')) {
$error .= ' '.__('Please fill out the required fields.', 'affiliation-manager'); }
if ($error == '') { $updated = true; add_affiliate($_POST); } } }

if (isset($_GET['id'])) {
$updated = true;
if ($_POST['password'] != '') { $results = $wpdb->query("UPDATE $affiliates_table_name SET password = '".hash('sha256', $_POST['password'])."' WHERE id = '".$_GET['id']."'"); }
if ($_POST['first_name'] != '') { $results = $wpdb->query("UPDATE $affiliates_table_name SET first_name = '".$_POST['first_name']."' WHERE id = '".$_GET['id']."'"); }
if ($_POST['last_name'] != '') { $results = $wpdb->query("UPDATE $affiliates_table_name SET last_name = '".$_POST['last_name']."' WHERE id = '".$_GET['id']."'"); }
$result = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE email_address='".$_POST['email_address']."'", OBJECT);
if (($result) && ($result->id != $_GET['id'])) { $error .= ' '.__('This email address is not available.', 'affiliation-manager'); }
elseif ($_POST['email_address'] != '') { $results = $wpdb->query("UPDATE $affiliates_table_name SET email_address = '".$_POST['email_address']."' WHERE id = '".$_GET['id']."'"); }
$result = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE paypal_email_address = '".$_POST['paypal_email_address']."'", OBJECT);
if (($result) && ($result->id != $_GET['id'])) { $error .= ' '.__('This PayPal email address is not available.', 'affiliation-manager'); }
elseif ($_POST['paypal_email_address'] != '') { $results = $wpdb->query("UPDATE $affiliates_table_name SET paypal_email_address = '".$_POST['paypal_email_address']."' WHERE id = '".$_GET['id']."'"); }
include 'tables.php';
foreach ($tables['affiliates'] as $key => $value) { switch ($key) {
case 'id': case 'login': case 'password': case 'first_name': case 'last_name': case 'email_address': case 'paypal_email_address': break;
default: $list .= $key." = '".$_POST[$key]."',"; } }
$results = $wpdb->query("UPDATE $affiliates_table_name SET ".substr($list, 0, -1)." WHERE id = '".$_GET['id']."'"); } }

if (isset($_GET['id'])) {
$affiliate_data = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE id = '".$_GET['id']."'", OBJECT);
if ($affiliate_data) { foreach ($affiliate_data as $key => $value) { $_POST[$key] = $value; } }
elseif (!headers_sent()) { header('Location: admin.php?page=affiliation-manager-affiliate'); exit(); } }

$_POST = array_map('stripslashes', $_POST);
$_POST = array_map('htmlspecialchars', $_POST);
foreach ($_POST as $key => $value) {
$_POST[$key] = str_replace('&amp;amp;', '&amp;', $value);
if ($value == '0000-00-00 00:00:00') { $_POST[$key] = ''; } }
if (function_exists('commerce_data')) { $currency_code = commerce_data('currency_code'); }
else { $commerce_manager_options = (array) get_option('commerce_manager');
$currency_code = do_shortcode($commerce_manager_options['currency_code']); } ?>

<div class="wrap">
<div id="poststuff">
<?php affiliation_manager_pages_top(); ?>
<?php if ($updated) { echo '<div class="updated"><p><strong>'.(isset($_GET['id']) ? __('Affiliate updated.', 'affiliation-manager') : __('Affiliate saved.', 'affiliation-manager')).'</strong></p></div>'; } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php affiliation_manager_pages_menu(); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<p class="description"><?php _e('Fields marked with * are required.', 'affiliation-manager'); ?></p>
<ul class="subsubsub" style="float: none; white-space: normal;">
<li><a href="#personal-informations"><?php _e('Personal informations', 'affiliation-manager'); ?></a></li>
<li>| <a href="#affiliation"><?php _e('Affiliation', 'affiliation-manager'); ?></a></li><?php if (!isset($_GET['id'])) { ?>
<li>| <a href="#email-sent-to-affiliate"><?php _e('Email sent to affiliate', 'affiliation-manager'); ?></a></li>
<li>| <a href="#email-sent-to-affiliator"><?php _e('Email sent to affiliator', 'affiliation-manager'); ?></a></li>
<li>| <a href="#autoresponders"><?php _e('Autoresponders', 'affiliation-manager'); ?></a></li><?php } ?>
</ul>
<div class="postbox">
<h3 id="personal-informations"><strong><?php _e('Personal informations', 'affiliation-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['login'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="login"><?php _e('Login name', 'affiliation-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="login" id="login" rows="1" cols="25"<?php if (isset($_GET['id'])) { echo ' disabled="disabled"'; } ?>><?php echo $_POST['login']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php echo (isset($_GET['id']) ? __('The login name can not be changed.', 'affiliation-manager') : __('Letters, numbers, hyphens and underscores only', 'affiliation-manager').'<br />'
.__('The login name will be included in affiliate links and can not be changed.', 'affiliation-manager')); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="password"><?php _e('Password', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="password" id="password" rows="1" cols="25"><?php echo (isset($_GET['id']) ? '' : $_POST['password']); ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php (isset($_GET['id']) ? _e('(if you want to change it)', 'affiliation-manager') : _e('Leave this field blank to automatically generate a random password.', 'affiliation-manager')); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['first_name'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="first_name"><?php _e('First name', 'affiliation-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="first_name" id="first_name" rows="1" cols="50"><?php echo $_POST['first_name']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['last_name'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="last_name"><?php _e('Last name', 'affiliation-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="last_name" id="last_name" rows="1" cols="50"><?php echo $_POST['last_name']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['email_address'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="email_address"><?php _e('Email address', 'affiliation-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="email_address" id="email_address" rows="1" cols="50"><?php echo $_POST['email_address']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['paypal_email_address'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="paypal_email_address"><?php _e('PayPal email address', 'affiliation-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="paypal_email_address" id="paypal_email_address" rows="1" cols="50"><?php echo $_POST['paypal_email_address']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="website_name"><?php _e('Website name', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="website_name" id="website_name" rows="1" cols="50"><?php echo $_POST['website_name']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="website_url"><?php _e('Website URL', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="website_url" id="website_url" rows="1" cols="75"><?php echo $_POST['website_url']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="address"><?php _e('Address', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="address" id="address" rows="1" cols="50"><?php echo $_POST['address']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="postcode"><?php _e('Postcode', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="postcode" id="postcode" rows="1" cols="50"><?php echo $_POST['postcode']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="town"><?php _e('Town', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="town" id="town" rows="1" cols="50"><?php echo $_POST['town']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="country"><?php _e('Country', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="country" id="country" rows="1" cols="50"><?php echo $_POST['country']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="phone_number"><?php _e('Phone number', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="phone_number" id="phone_number" rows="1" cols="50"><?php echo $_POST['phone_number']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="ip_address"><?php _e('IP address', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="ip_address" id="ip_address" rows="1" cols="50"><?php echo $_POST['ip_address']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="user_agent"><?php _e('User agent', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="user_agent" id="user_agent" rows="1" cols="75"><?php echo $_POST['user_agent']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="referring_url"><?php _e('Referring URL', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="referring_url" id="referring_url" rows="1" cols="75"><?php echo $_POST['referring_url']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="date"><?php _e('Registration date', 'affiliation-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="date" id="date" size="20" value="<?php echo (isset($_POST['date']) ? $_POST['date'] : date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET)); ?>" /></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="affiliation"><strong><?php _e('Affiliation', 'affiliation-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=affiliation-manager"><?php _e('Click here to configure the default options.', 'affiliation-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="referrer"><?php _e('Referrer', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="referrer" id="referrer" rows="1" cols="25"><?php echo $_POST['referrer']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Affiliate who referred this affiliate (ID, login name or email address)', 'affiliation-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="commission_amount"><?php _e('Commission amount', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_amount" id="commission_amount" rows="1" cols="25"><?php echo $_POST['commission_amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you select constant commissions', 'affiliation-manager'); ?><br />
<?php _e('Leave this field blank to apply the default option.', 'affiliation-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="commission_percentage"><?php _e('Commission percentage', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_percentage" id="commission_percentage" rows="1" cols="25"><?php echo $_POST['commission_percentage']; ?></textarea> <span style="vertical-align: 25%;">% <?php _e('of the price', 'affiliation-manager'); ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you select proportional commissions', 'affiliation-manager'); ?><br />
<?php _e('Leave this field blank to apply the default option.', 'affiliation-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>
<?php if (!isset($_GET['id'])) {
if (!isset($_POST['submit'])) {
$affiliation_manager_options = (array) get_option('affiliation_manager');
$affiliation_manager_options = array_map('htmlspecialchars', $affiliation_manager_options);
foreach (add_affiliate_fields() as $field) { $_POST[$field] = $affiliation_manager_options[$field]; }
$_POST['email_to_affiliate_body'] = htmlspecialchars(get_option('affiliation_manager_email_to_affiliate_body'));
$_POST['email_to_affiliator_body'] = htmlspecialchars(get_option('affiliation_manager_email_to_affiliator_body')); } ?>
<p class="submit" style="margin: 0 20%;"><input type="hidden" name="submit" value="true" />
<input type="submit" class="button-secondary" name="update_fields" value="<?php _e('Complete the fields below with the informations about the affiliate', 'affiliation-manager'); ?>" /></p>
<div class="postbox">
<h3 id="email-sent-to-affiliate"><strong><?php _e('Email sent to affiliate', 'affiliation-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=affiliation-manager#email-sent-to-affiliate"><?php _e('Click here to configure the default options.', 'affiliation-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="email_sent_to_affiliate" id="email_sent_to_affiliate" value="yes"<?php if ($_POST['email_sent_to_affiliate'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="email_sent_to_affiliate"><?php _e('Send a registration confirmation email to the affiliate', 'affiliation-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliate_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_affiliate_sender" id="email_to_affiliate_sender" rows="1" cols="75"><?php echo $_POST['email_to_affiliate_sender']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliate_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_affiliate_receiver" id="email_to_affiliate_receiver" rows="1" cols="75"><?php echo $_POST['email_to_affiliate_receiver']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliate_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_affiliate_subject" id="email_to_affiliate_subject" rows="1" cols="75"><?php echo $_POST['email_to_affiliate_subject']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliate_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="email_to_affiliate_body" id="email_to_affiliate_body" rows="15" cols="75"><?php echo $_POST['email_to_affiliate_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="email-sent-to-affiliator"><strong><?php _e('Email sent to affiliator', 'affiliation-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=affiliation-manager#email-sent-to-affiliator"><?php _e('Click here to configure the default options.', 'affiliation-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="email_sent_to_affiliator" id="email_sent_to_affiliator" value="yes"<?php if ($_POST['email_sent_to_affiliator'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="email_sent_to_affiliator"><?php _e('Send a registration notification email to the affiliator', 'affiliation-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliator_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_affiliator_sender" id="email_to_affiliator_sender" rows="1" cols="75"><?php echo $_POST['email_to_affiliator_sender']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliator_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_affiliator_receiver" id="email_to_affiliator_receiver" rows="1" cols="75"><?php echo $_POST['email_to_affiliator_receiver']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliator_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_affiliator_subject" id="email_to_affiliator_subject" rows="1" cols="75"><?php echo $_POST['email_to_affiliator_subject']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliator_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="email_to_affiliator_body" id="email_to_affiliator_body" rows="15" cols="75"><?php echo $_POST['email_to_affiliator_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="autoresponders"><strong><?php _e('Autoresponders', 'affiliation-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=affiliation-manager#autoresponders"><?php _e('Click here to configure the default options.', 'affiliation-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="affiliate_subscribed_to_autoresponder" id="affiliate_subscribed_to_autoresponder" value="yes"<?php if ($_POST['affiliate_subscribed_to_autoresponder'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="affiliate_subscribed_to_autoresponder"><?php _e('Subscribe the affiliate to an autoresponder list', 'affiliation-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="affiliate_autoresponder"><?php _e('Autoresponder', 'affiliation-manager'); ?></label></strong></th>
<td><select name="affiliate_autoresponder" id="affiliate_autoresponder">
<?php include 'autoresponders.php';
$autoresponder = do_shortcode($_POST['affiliate_autoresponder']);
foreach ($autoresponders as $value) {
echo '<option value="'.$value.'"'.($autoresponder == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="affiliate_autoresponder_list"><?php _e('List', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="affiliate_autoresponder_list" id="affiliate_autoresponder_list" rows="1" cols="50"><?php echo $_POST['affiliate_autoresponder_list']; ?></textarea></td></tr>
</tbody></table>
</div></div>
<?php if (($updated) && ($_GET['autoresponder_subscription'] != '')) { echo '<div><img alt="" src="'.$_GET['autoresponder_subscription'].'" /></div>'; } } ?>
<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php (isset($_GET['id']) ?  _e('Save Changes', 'affiliation-manager') : _e('Save Affiliate', 'affiliation-manager')); ?>" /></p>
</form>
</div>
</div>
<?php if (isset($_POST['update_fields'])) { ?>
<script type="text/javascript">window.location = '#email-sent-to-affiliate';</script>
<?php } }