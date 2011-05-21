<?php if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

global $wpdb;
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
if (!isset($_GET['id'])) {
$add_affiliate_fields = array(
'email_sent_to_affiliate',
'email_to_affiliate_sender',
'email_to_affiliate_subject',
'email_to_affiliate_body',
'email_sent_to_affiliator',
'email_to_affiliator_receiver',
'email_to_affiliator_subject',
'email_to_affiliator_body',
'affiliate_subscribed_to_autoresponder',
'affiliate_autoresponder',
'affiliate_autoresponder_list',
'affiliate_subscribed_to_autoresponder2',
'affiliate_autoresponder2',
'affiliate_autoresponder_list2'); }

if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
$_POST = array_map('html_entity_decode', $_POST);
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$_POST['login'] = affiliation_format_nice_name($_POST['login']);
$_POST['email_address'] = affiliation_format_email_address($_POST['email_address']);
$_POST['paypal_email_address'] = affiliation_format_email_address($_POST['paypal_email_address']);
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
$_POST['referring_url'] = $_SERVER['HTTP_REFERER'];

if (!isset($_GET['id'])) {
if ($_POST['password'] == '') { $_POST['password'] = substr(md5(mt_rand()), 0, 8); }
if (isset($_POST['update_fields'])) {
foreach ($_POST as $key => $value) { $_GET['affiliate_data']->$key = $_POST[$key]; }
$_GET['affiliate_data']->id = '{affiliate id}';
foreach ($add_affiliate_fields as $key => $field) { $_POST[$field] = str_replace('{affiliate id}', '[affiliate id]', affiliation_data($field)); } }

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

if ($error == '') {
$updated = true;
$results = $wpdb->query("INSERT INTO $affiliates_table_name (id, login, password, first_name, last_name, email_address, paypal_email_address, website_name, website_url, address, postcode, town, country, phone_number, commission_percentage, commission_amount, date, date_utc, user_agent, ip_address, referring_url, referrer) VALUES(
	'',
	'".$_POST['login']."',
	'".hash('sha256', $_POST['password'])."',
	'".$_POST['first_name']."',
	'".$_POST['last_name']."',
	'".$_POST['email_address']."',
	'".$_POST['paypal_email_address']."',
	'".$_POST['website_name']."',
	'".$_POST['website_url']."',
	'".$_POST['address']."',
	'".$_POST['postcode']."',
	'".$_POST['town']."',
	'".$_POST['country']."',
	'".$_POST['phone_number']."',
	'".$_POST['commission_percentage']."',
	'".$_POST['commission_amount']."',
	'".$_POST['date']."',
	'".$_POST['date_utc']."',
	'',
	'',
	'".$_POST['referring_url']."',
	'".$_POST['referrer']."')");

if (($_POST['email_sent_to_affiliate'] == 'yes') || ($_POST['email_sent_to_affiliator'] == 'yes')) {
$_GET['affiliate_data'] = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE login='".$_POST['login']."'", OBJECT);
$_GET['affiliate_data']->password = $_POST['password'];
$_POST = array_map('stripslashes', $_POST);
if ($_POST['email_sent_to_affiliate'] == 'yes') {
$receiver = $_POST['email_address'];
$subject = do_shortcode($_POST['email_to_affiliate_subject']);
$body = do_shortcode($_POST['email_to_affiliate_body']);
$sender = do_shortcode($_POST['email_to_affiliate_sender']);
$headers = 'From: '.$sender;
wp_mail($receiver, $subject, $body, $headers); }
if ($_POST['email_sent_to_affiliator'] == 'yes') {
$receiver = do_shortcode($_POST['email_to_affiliator_receiver']);
$subject = do_shortcode($_POST['email_to_affiliator_subject']);
$body = do_shortcode($_POST['email_to_affiliator_body']);
$sender = do_shortcode($_POST['email_to_affiliate_sender']);
$headers = 'From: '.$sender;
wp_mail($receiver, $subject, $body, $headers); } }

include_once 'autoresponders.php';
if ($_POST['affiliate_subscribed_to_autoresponder'] == 'yes') {
subscribe_to_autoresponder($_POST['affiliate_autoresponder'], $_POST['affiliate_autoresponder_list'], $_POST); }
if ($_POST['affiliate_subscribed_to_autoresponder2'] == 'yes') {
subscribe_to_autoresponder($_POST['affiliate_autoresponder2'], $_POST['affiliate_autoresponder_list2'], $_POST); } } } }

if (isset($_GET['id'])) {
$updated = true;
if (is_numeric($_POST['login'])) { $error .= __('The login name must be a non-numeric string.', 'affiliation-manager'); }
else { $result = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE login='".$_POST['login']."'", OBJECT);
if (($result) && ($result->id != $_GET['id'])) { $error .= ' '.__('This login name is not available.', 'affiliation-manager'); }
elseif ($_POST['login'] != '') { $results = $wpdb->query("UPDATE $affiliates_table_name SET login = '".$_POST['login']."' WHERE id = '".$_GET['id']."'"); } }
if ($_POST['password'] != '') { $results = $wpdb->query("UPDATE $affiliates_table_name SET password = '".hash('sha256', $_POST['password'])."' WHERE id = '".$_GET['id']."'"); }
if ($_POST['first_name'] != '') { $results = $wpdb->query("UPDATE $affiliates_table_name SET first_name = '".$_POST['first_name']."' WHERE id = '".$_GET['id']."'"); }
if ($_POST['last_name'] != '') { $results = $wpdb->query("UPDATE $affiliates_table_name SET last_name = '".$_POST['last_name']."' WHERE id = '".$_GET['id']."'"); }
$result = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE email_address='".$_POST['email_address']."'", OBJECT);
if (($result) && ($result->id != $_GET['id'])) { $error .= ' '.__('This email address is not available.', 'affiliation-manager'); }
elseif ($_POST['email_address'] != '') { $results = $wpdb->query("UPDATE $affiliates_table_name SET email_address = '".$_POST['email_address']."' WHERE id = '".$_GET['id']."'"); }
$result = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE paypal_email_address='".$_POST['paypal_email_address']."'", OBJECT);
if (($result) && ($result->id != $_GET['id'])) { $error .= ' '.__('This PayPal email address is not available.', 'affiliation-manager'); }
elseif ($_POST['paypal_email_address'] != '') { $results = $wpdb->query("UPDATE $affiliates_table_name SET paypal_email_address = '".$_POST['paypal_email_address']."' WHERE id = '".$_GET['id']."'"); }

$results = $wpdb->query("UPDATE $affiliates_table_name SET
	website_name = '".$_POST['website_name']."',
	website_url = '".$_POST['website_url']."',
	address = '".$_POST['address']."',
	postcode = '".$_POST['postcode']."',
	town = '".$_POST['town']."',
	country = '".$_POST['country']."',
	phone_number = '".$_POST['phone_number']."',
	date = '".$_POST['date']."',
	date_utc = '".$_POST['date_utc']."',
	referrer = '".$_POST['referrer']."',
	commission_percentage = '".$_POST['commission_percentage']."',
	commission_amount = '".$_POST['commission_amount']."' WHERE id = '".$_GET['id']."'"); } }

if (isset($_GET['id'])) {
$affiliate_data = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE id = '".$_GET['id']."'", OBJECT);
if ($affiliate_data) { foreach ($affiliate_data as $key => $value) { $_POST[$key] = $affiliate_data->$key; } }
elseif (!headers_sent()) { header('Location: admin.php?page=affiliation-manager-affiliate'); exit(); } }

$_POST = array_map('stripslashes', $_POST);
$_POST = array_map('htmlspecialchars', $_POST);
foreach ($_POST as $key => $value) {
$_POST[$key] = str_replace('&amp;amp;', '&amp;', $_POST[$key]);
if ($_POST[$key] == '0000-00-00 00:00:00') { $_POST[$key] = ''; } }
$affiliation_manager_options = array_map('htmlspecialchars', get_option('affiliation_manager'));
$commerce_manager_options = array_map('htmlspecialchars', get_option('commerce_manager'));
$currency_code = do_shortcode($commerce_manager_options['currency_code']); ?>

<div class="wrap">
<div id="poststuff">
<?php affiliation_manager_pages_top(); ?>
<?php if ($updated) { echo '<div class="updated"><p><strong>'.(isset($_GET['id']) ? __('Affiliate updated.', 'affiliation-manager') : __('Affiliate saved.', 'affiliation-manager')).'</strong></p></div>'; } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php affiliation_manager_pages_menu(); ?>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<p class="description"><?php _e('Fields marked with * are required.', 'affiliation-manager'); ?></p>
<div class="postbox">
<h3 id="personal-informations"><?php _e('Personal informations', 'affiliation-manager'); ?></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['login'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="login"><?php _e('Login name', 'affiliation-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="login" id="login" rows="1" cols="25"><?php echo $_POST['login']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Letters, numbers, hyphens and underscores only', 'affiliation-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="password"><?php _e('Password', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="password" id="password" rows="1" cols="25"><?php echo (isset($_GET['id']) ? '' : $_POST['password']); ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php (isset($_GET['id']) ? _e('(if you want to change it)', 'affiliation-manager') : _e('Leave this field blank to automatically generate a random password.', 'affiliation-manager')); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['first_name'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="first_name"><?php _e('First name', 'affiliation-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="first_name" id="first_name" rows="1" cols="25"><?php echo $_POST['first_name']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['last_name'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="last_name"><?php _e('Last name', 'affiliation-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="last_name" id="last_name" rows="1" cols="25"><?php echo $_POST['last_name']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['email_address'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="email_address"><?php _e('Email address', 'affiliation-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="email_address" id="email_address" rows="1" cols="25"><?php echo $_POST['email_address']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['paypal_email_address'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="paypal_email_address"><?php _e('PayPal email address', 'affiliation-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="paypal_email_address" id="paypal_email_address" rows="1" cols="25"><?php echo $_POST['paypal_email_address']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="website_name"><?php _e('Website name', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="website_name" id="website_name" rows="1" cols="25"><?php echo $_POST['website_name']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="website_url"><?php _e('Website URL', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="website_url" id="website_url" rows="1" cols="50"><?php echo $_POST['website_url']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="address"><?php _e('Address', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="address" id="address" rows="1" cols="25"><?php echo $_POST['address']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="postcode"><?php _e('Postcode', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="postcode" id="postcode" rows="1" cols="25"><?php echo $_POST['postcode']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="town"><?php _e('Town', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="town" id="town" rows="1" cols="25"><?php echo $_POST['town']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="country"><?php _e('Country', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="country" id="country" rows="1" cols="25"><?php echo $_POST['country']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="phone_number"><?php _e('Phone number', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="phone_number" id="phone_number" rows="1" cols="25"><?php echo $_POST['phone_number']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="date"><?php _e('Registration date', 'affiliation-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="date" id="date" size="20" value="<?php echo (isset($_POST['date']) ? $_POST['date'] : date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET)); ?>" /></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="affiliation"><?php _e('Affiliation', 'affiliation-manager'); ?></h3>
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
foreach ($add_affiliate_fields as $key => $field) { $_POST[$field] = $affiliation_manager_options[$field]; }
$_POST['email_to_affiliate_body'] = htmlspecialchars(get_option('affiliation_manager_email_to_affiliate_body'));
$_POST['email_to_affiliator_body'] = htmlspecialchars(get_option('affiliation_manager_email_to_affiliator_body')); } ?>
<p class="submit" style="margin: 0 20%;"><input type="hidden" name="submit" value="true" />
<input type="submit" class="button-secondary" name="update_fields" value="<?php _e('Complete the fields below with the informations about the affiliate', 'affiliation-manager'); ?>" /></p>
<div class="postbox">
<h3 id="email-sent-to-affiliate"><?php _e('Email sent to affiliate', 'affiliation-manager'); ?></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=affiliation-manager#email-sent-to-affiliate"><?php _e('Click here to configure the default options.', 'affiliation-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="email_sent_to_affiliate" id="email_sent_to_affiliate" value="yes"<?php if ($_POST['email_sent_to_affiliate'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="email_sent_to_affiliate"><?php _e('Send a registration confirmation email to the affiliate', 'affiliation-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliate_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_affiliate_sender" id="email_to_affiliate_sender" rows="1" cols="75"><?php echo $_POST['email_to_affiliate_sender']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliate_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_affiliate_subject" id="email_to_affiliate_subject" rows="1" cols="75"><?php echo $_POST['email_to_affiliate_subject']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliate_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; height: 20%; width: 75%;" name="email_to_affiliate_body" id="email_to_affiliate_body" rows="15" cols="75"><?php echo $_POST['email_to_affiliate_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="email-sent-to-affiliator"><?php _e('Email sent to affiliator', 'affiliation-manager'); ?></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=affiliation-manager#email-sent-to-affiliator"><?php _e('Click here to configure the default options.', 'affiliation-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="email_sent_to_affiliator" id="email_sent_to_affiliator" value="yes"<?php if ($_POST['email_sent_to_affiliator'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="email_sent_to_affiliator"><?php _e('Send a registration notification email to the affiliator', 'affiliation-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliator_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_affiliator_receiver" id="email_to_affiliator_receiver" rows="1" cols="75"><?php echo $_POST['email_to_affiliator_receiver']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliator_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_affiliator_subject" id="email_to_affiliator_subject" rows="1" cols="75"><?php echo $_POST['email_to_affiliator_subject']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliator_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; height: 20%; width: 75%;" name="email_to_affiliator_body" id="email_to_affiliator_body" rows="15" cols="75"><?php echo $_POST['email_to_affiliator_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="autoresponders"><?php _e('Autoresponders', 'affiliation-manager'); ?></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=affiliation-manager#autoresponders"><?php _e('Click here to configure the default options.', 'affiliation-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="affiliate_subscribed_to_autoresponder" id="affiliate_subscribed_to_autoresponder" value="yes"<?php if ($_POST['affiliate_subscribed_to_autoresponder'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="affiliate_subscribed_to_autoresponder"><?php _e('Subscribe the affiliate to an autoresponder list', 'affiliation-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="affiliate_autoresponder"><?php _e('Autoresponder', 'affiliation-manager'); ?></label></strong></th>
<td><select name="affiliate_autoresponder" id="affiliate_autoresponder">
<?php include_once 'autoresponders.php';
$autoresponder = do_shortcode($_POST['affiliate_autoresponder']);
foreach ($autoresponders as $key => $value) {
echo '<option value="'.$value.'"'.($autoresponder == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="affiliate_autoresponder_list"><?php _e('List', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="affiliate_autoresponder_list" id="affiliate_autoresponder_list" rows="1" cols="25"><?php echo $_POST['affiliate_autoresponder_list']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="affiliate_subscribed_to_autoresponder2" id="affiliate_subscribed_to_autoresponder2" value="yes"<?php if ($_POST['affiliate_subscribed_to_autoresponder2'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="affiliate_subscribed_to_autoresponder2"><?php _e('Subscribe the affiliate to an additional autoresponder list', 'affiliation-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="affiliate_autoresponder2"><?php _e('Additional autoresponder', 'affiliation-manager'); ?></label></strong></th>
<td><select name="affiliate_autoresponder2" id="affiliate_autoresponder2">
<?php include_once 'autoresponders.php';
$autoresponder2 = do_shortcode($_POST['affiliate_autoresponder2']);
foreach ($autoresponders as $key => $value) {
echo '<option value="'.$value.'"'.($autoresponder2 == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="affiliate_autoresponder_list2"><?php _e('Additional list', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="affiliate_autoresponder_list2" id="affiliate_autoresponder_list2" rows="1" cols="25"><?php echo $_POST['affiliate_autoresponder_list2']; ?></textarea></td></tr>
</tbody></table>
</div></div>
<?php } ?>
<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php (isset($_GET['id']) ?  _e('Save Changes', 'affiliation-manager') : _e('Save Affiliate', 'affiliation-manager')); ?>" /></p>
</form>
</div>
</div>
<?php if (isset($_POST['update_fields'])) { ?>
<script type="text/javascript">window.location = '#email-sent-to-affiliate';</script>
<?php } }