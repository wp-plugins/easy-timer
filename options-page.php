<?php if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
$_POST = array_map('html_entity_decode', $_POST);
$_POST = array_map('stripslashes', $_POST);
$affiliates_aweber_list = $_POST['affiliates_aweber_list'];
if ($_POST['affiliates_subscribed_to_aweber_list'] == 'yes') { $affiliates_subscribed_to_aweber_list = 'yes'; } else { $affiliates_subscribed_to_aweber_list = 'no'; }
if ($_POST['affiliation_enabled'] == 'yes') { $affiliation_enabled = 'yes'; } else { $affiliation_enabled = 'no'; }
$commission_amount = str_replace(array('?', ',', ';'), '.', $_POST['commission_amount']);
$commission_payment = $_POST['commission_payment'];
$commission_percentage = str_replace(array('?', ',', ';'), '.', $_POST['commission_percentage']);
$commission_type = $_POST['commission_type'];
$cookies_lifetime = (int) $_POST['cookies_lifetime']; if ($cookies_lifetime < 1) { $cookies_lifetime = 180; }
$cookies_name = affiliation_format_nice_name($_POST['cookies_name']); if ($cookies_name == 'a_login') { $cookies_name == 'a-login'; }
if ($_POST['email_sent_to_affiliate'] == 'yes') { $email_sent_to_affiliate = 'yes'; } else { $email_sent_to_affiliate = 'no'; }
if ($_POST['email_sent_to_affiliator'] == 'yes') { $email_sent_to_affiliator = 'yes'; } else { $email_sent_to_affiliator = 'no'; }
$email_to_affiliate_sender = $_POST['email_to_affiliate_sender'];
$email_to_affiliate_subject = $_POST['email_to_affiliate_subject'];
$email_to_affiliator_receiver = $_POST['email_to_affiliator_receiver'];
$email_to_affiliator_subject = $_POST['email_to_affiliator_subject'];
$first_sale_winner = $_POST['first_sale_winner'];
$maximum_login_length = (int) $_POST['maximum_login_length']; if ($maximum_login_length < 2) { $maximum_login_length = 2; }
$maximum_password_length = (int) $_POST['maximum_password_length']; if ($maximum_password_length < 8) { $maximum_password_length = 8; }
$minimum_login_length = (int) $_POST['minimum_login_length']; if ($minimum_login_length < 1) { $minimum_login_length = 1; }
if ($minimum_login_length > $maximum_login_length) { $minimum_login_length = $maximum_login_length; }
$minimum_password_length = (int) $_POST['minimum_password_length']; if ($minimum_password_length < 1) { $minimum_password_length = 1; }
if ($minimum_password_length > $maximum_password_length) { $minimum_password_length = $maximum_password_length; }
$minimum_payout_amount = str_replace(array('?', ',', ';'), '.', $_POST['minimum_payout_amount']);
$password_reset_email_sender = $_POST['password_reset_email_sender'];
$password_reset_email_subject = $_POST['password_reset_email_subject'];
$registration_confirmation_url = $_POST['registration_confirmation_url'];
if ($_POST['registration_required'] == 'yes') { $registration_required = 'yes'; } else { $registration_required = 'no'; }
$url_variable_name = affiliation_format_nice_name($_POST['url_variable_name']);
$url_variable_name2 = affiliation_format_nice_name($_POST['url_variable_name2']);
$winner_affiliate = $_POST['winner_affiliate'];

$affiliation_manager_options = array(
'affiliates_aweber_list' => $affiliates_aweber_list,
'affiliates_subscribed_to_aweber_list' => $affiliates_subscribed_to_aweber_list,
'affiliation_enabled' => $affiliation_enabled,
'commission_amount' => $commission_amount,
'commission_payment' => $commission_payment,
'commission_percentage' => $commission_percentage,
'commission_type' => $commission_type,
'cookies_lifetime' => $cookies_lifetime,
'cookies_name' => $cookies_name,
'email_sent_to_affiliate' => $email_sent_to_affiliate,
'email_sent_to_affiliator' => $email_sent_to_affiliator,
'email_to_affiliate_sender' => $email_to_affiliate_sender,
'email_to_affiliate_subject' => $email_to_affiliate_subject,
'email_to_affiliator_receiver' => $email_to_affiliator_receiver,
'email_to_affiliator_subject' => $email_to_affiliator_subject,
'first_sale_winner' => $first_sale_winner,
'maximum_login_length' => $maximum_login_length,
'maximum_password_length' => $maximum_password_length,
'minimum_login_length' => $minimum_login_length,
'minimum_password_length' => $minimum_password_length,
'minimum_payout_amount' => $minimum_payout_amount,
'password_reset_email_sender' => $password_reset_email_sender,
'password_reset_email_subject' => $password_reset_email_subject,
'registration_confirmation_url' => $registration_confirmation_url,
'registration_required' => $registration_required,
'url_variable_name' => $url_variable_name,
'url_variable_name2' => $url_variable_name2,
'winner_affiliate' => $winner_affiliate);
update_option('affiliation_manager', $affiliation_manager_options);

update_option('affiliation_manager_email_to_affiliate_body', $_POST['email_to_affiliate_body']);
update_option('affiliation_manager_email_to_affiliator_body', $_POST['email_to_affiliator_body']);
update_option('affiliation_manager_password_reset_email_body', $_POST['password_reset_email_body']); }

if (!isset($affiliation_manager_options)) { $affiliation_manager_options = get_option('affiliation_manager'); }
$affiliation_manager_options = array_map('htmlspecialchars', $affiliation_manager_options);
$commerce_manager_options = array_map('htmlspecialchars', get_option('commerce_manager')); ?>

<div class="wrap">
<div id="poststuff">
<?php affiliation_manager_pages_top(); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>'; } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php affiliation_manager_pages_menu(); ?>
<div class="postbox">
<h3><?php _e('General options', 'affiliation-manager'); ?></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="affiliation_enabled" id="affiliation_enabled" value="yes"<?php if ($affiliation_manager_options['affiliation_enabled'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="affiliation_enabled"><?php _e('Use affiliation', 'affiliation-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="url_variable_name"><?php _e('Name of the variable used in affiliate links', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="url_variable_name" id="url_variable_name" rows="1" cols="25"><?php echo $affiliation_manager_options['url_variable_name']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('You can customize the affiliate link structure.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#affiliate-links"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="url_variable_name2"><?php _e('Name of the alternative variable used in affiliate links', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="url_variable_name2" id="url_variable_name2" rows="1" cols="25"><?php echo $affiliation_manager_options['url_variable_name2']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Your affiliate program can be compatible with another affiliate link structure.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#alternative-affiliate-links"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="cookies_name"><?php _e('Cookies name', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="cookies_name" id="cookies_name" rows="1" cols="25"><?php echo $affiliation_manager_options['cookies_name']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#cookies"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="cookies_lifetime"><?php _e('Cookies lifetime', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="cookies_lifetime" id="cookies_lifetime" rows="1" cols="25"><?php echo $affiliation_manager_options['cookies_lifetime']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('days', 'affiliation-manager'); ?> 
<span class="description"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#cookies-lifetime"><?php _e('More informations', 'affiliation-manager'); ?></a></span></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="commission_percentage"><?php _e('Commission percentage', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_percentage" id="commission_percentage" rows="1" cols="25"><?php echo $affiliation_manager_options['commission_percentage']; ?></textarea> <span style="vertical-align: 25%;">% <?php _e('of the price', 'affiliation-manager'); ?></span><br />
<span class="description"><?php _e('Used for instant payment affiliation and for deferred payment affiliation if you use proportional commissions', 'affiliation-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="winner_affiliate"><?php _e('Commission award', 'affiliation-manager'); ?></label></strong></th>
<td><?php _e('The commission is awarded to the', 'affiliation-manager'); ?> <select name="winner_affiliate" id="winner_affiliate">
<option value="first"<?php if ($affiliation_manager_options['winner_affiliate'] == 'first') { echo ' selected="selected"'; } ?>><?php _e('first', 'affiliation-manager'); ?></option>
<option value="last"<?php if ($affiliation_manager_options['winner_affiliate'] == 'last') { echo ' selected="selected"'; } ?>><?php _e('last', 'affiliation-manager'); ?></option>
</select> <?php _e('affiliate who referred the customer.', 'affiliation-manager'); ?> 
<span class="description"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#commission-award"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="commission_payment"><?php _e('Commission payment', 'affiliation-manager'); ?></label></strong></th>
<td><select name="commission_payment" id="commission_payment">
<option value="deferred"<?php if ($affiliation_manager_options['commission_payment'] == 'deferred') { echo ' selected="selected"'; } ?>><?php _e('Deferred', 'affiliation-manager'); ?></option>
<option value="instant"<?php if ($affiliation_manager_options['commission_payment'] == 'instant') { echo ' selected="selected"'; } ?>><?php _e('Instant', 'affiliation-manager'); ?></option>
</select> 
<span class="description"><?php _e('You can pay your affiliates instantly.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#commission-payment"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3><?php _e('Deferred payment affiliation', 'affiliation-manager'); ?></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="commission_type"><?php _e('Commission type', 'affiliation-manager'); ?></label></strong></th>
<td><select name="commission_type" id="commission_type">
<option value="constant"<?php if ($affiliation_manager_options['commission_type'] == 'constant') { echo ' selected="selected"'; } ?>><?php _e('Constant', 'affiliation-manager'); ?></option>
<option value="proportional"<?php if ($affiliation_manager_options['commission_type'] == 'proportional') { echo ' selected="selected"'; } ?>><?php _e('Proportional', 'affiliation-manager'); ?> (<?php echo do_shortcode($affiliation_manager_options['commission_percentage']); ?>% <?php _e('of the price', 'affiliation-manager'); ?>)</option>
</select></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="commission_amount"><?php _e('Commission amount', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_amount" id="commission_amount" rows="1" cols="25"><?php echo $affiliation_manager_options['commission_amount']; ?></textarea>  <span style="vertical-align: 25%;"><?php echo do_shortcode($commerce_manager_options['currency_code']); ?></span><br />
<span class="description"><?php _e('Used if you select constant commissions', 'affiliation-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="minimum_payout_amount"><?php _e('Minimum payout amount', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="minimum_payout_amount" id="minimum_payout_amount" rows="1" cols="25"><?php echo $affiliation_manager_options['minimum_payout_amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo do_shortcode($commerce_manager_options['currency_code']); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3><?php _e('Instant payment affiliation', 'affiliation-manager'); ?></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="registration_required" id="registration_required" value="yes"<?php if ($affiliation_manager_options['registration_required'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="registration_required"><?php _e('Registration to the affiliate program required', 'affiliation-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="first_sale_winner"><?php _e('First sale award', 'affiliation-manager'); ?></label></strong></th>
<td><?php _e('The first sale referred by the affiliate is awarded to the', 'affiliation-manager'); ?> <select name="first_sale_winner" id="first_sale_winner">
<option value="affiliate"<?php if ($affiliation_manager_options['first_sale_winner'] == 'affiliate') { echo ' selected="selected"'; } ?>><?php _e('affiliate', 'affiliation-manager'); ?></option>
<option value="affiliator"<?php if ($affiliation_manager_options['first_sale_winner'] == 'affiliator') { echo ' selected="selected"'; } ?>><?php _e('affiliator', 'affiliation-manager'); ?></option>
</select>. 
<span class="description"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#first-sale-award"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3><?php _e('Registration', 'affiliation-manager'); ?></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="minimum_login_length"><?php _e('Minimum login length', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="minimum_login_length" id="minimum_login_length" rows="1" cols="25"><?php echo $affiliation_manager_options['minimum_login_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'affiliation-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="maximum_login_length"><?php _e('Maximum login length', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="maximum_login_length" id="maximum_login_length" rows="1" cols="25"><?php echo $affiliation_manager_options['maximum_login_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'affiliation-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="minimum_password_length"><?php _e('Minimum password length', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="minimum_password_length" id="minimum_password_length" rows="1" cols="25"><?php echo $affiliation_manager_options['minimum_password_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'affiliation-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="maximum_password_length"><?php _e('Maximum password length', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="maximum_password_length" id="maximum_password_length" rows="1" cols="25"><?php echo $affiliation_manager_options['maximum_password_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'affiliation-manager'); ?></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_url"><?php _e('Registration confirmation URL', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_url" id="registration_confirmation_url" rows="1" cols="75"><?php echo $affiliation_manager_options['registration_confirmation_url']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3><?php _e('Email sent to affiliate', 'affiliation-manager'); ?></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="email_sent_to_affiliate" id="email_sent_to_affiliate" value="yes"<?php if ($affiliation_manager_options['email_sent_to_affiliate'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="email_sent_to_affiliate"><?php _e('Send a registration confirmation email to the affiliate', 'affiliation-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliate_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_affiliate_sender" id="email_to_affiliate_sender" rows="1" cols="75"><?php echo $affiliation_manager_options['email_to_affiliate_sender']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliate_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_affiliate_subject" id="email_to_affiliate_subject" rows="1" cols="75"><?php echo $affiliation_manager_options['email_to_affiliate_subject']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliate_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; height: 20%; width: 75%;" name="email_to_affiliate_body" id="email_to_affiliate_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_email_to_affiliate_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3><?php _e('Email sent to affiliator', 'affiliation-manager'); ?></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="email_sent_to_affiliator" id="email_sent_to_affiliator" value="yes"<?php if ($affiliation_manager_options['email_sent_to_affiliator'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="email_sent_to_affiliator"><?php _e('Send a registration notification email to the affiliator', 'affiliation-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliator_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_affiliator_receiver" id="email_to_affiliator_receiver" rows="1" cols="75"><?php echo $affiliation_manager_options['email_to_affiliator_receiver']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliator_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_affiliator_subject" id="email_to_affiliator_subject" rows="1" cols="75"><?php echo $affiliation_manager_options['email_to_affiliator_subject']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliator_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; height: 20%; width: 75%;" name="email_to_affiliator_body" id="email_to_affiliator_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_email_to_affiliator_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3><?php _e('Password reset email', 'affiliation-manager'); ?></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="password_reset_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_email_sender" id="password_reset_email_sender" rows="1" cols="75"><?php echo $affiliation_manager_options['password_reset_email_sender']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliator_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_email_subject" id="password_reset_email_subject" rows="1" cols="75"><?php echo $affiliation_manager_options['password_reset_email_subject']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="password_reset_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; height: 20%; width: 75%;" name="password_reset_email_body" id="password_reset_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_password_reset_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3><?php _e('Autoresponder', 'affiliation-manager'); ?></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="affiliates_subscribed_to_aweber_list" id="affiliates_subscribed_to_aweber_list" value="yes"<?php if ($affiliation_manager_options['affiliates_subscribed_to_aweber_list'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="affiliates_subscribed_to_aweber_list"><?php _e('Subscribe affiliates to an AWeber list', 'affiliation-manager'); ?></label></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"><strong><label for="affiliates_aweber_list"><?php _e('AWeber list', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="affiliates_aweber_list" id="affiliates_aweber_list" rows="1" cols="25"><?php echo $affiliation_manager_options['affiliates_aweber_list']; ?></textarea></td></tr>
<tr valign="top"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes'); ?>" /></p>
</form>
</div>
</div>