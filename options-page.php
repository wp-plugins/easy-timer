<?php if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
include 'initial-options.php';
$_POST = array_map('html_entity_decode', $_POST);
$_POST = array_map('stripslashes', $_POST);
if ($_POST['affiliate_subscribed_to_autoresponder'] != 'yes') { $_POST['affiliate_subscribed_to_autoresponder'] = 'no'; }
if ($_POST['affiliation_enabled'] != 'yes') { $_POST['affiliation_enabled'] = 'no'; }
$_POST['commission_amount'] = str_replace(array('?', ',', ';'), '.', $_POST['commission_amount']);
$_POST['commission_percentage'] = str_replace(array('?', ',', ';'), '.', $_POST['commission_percentage']);
$_POST['cookies_lifetime'] = (int) $_POST['cookies_lifetime'];
if ($_POST['cookies_lifetime'] < 1) { $_POST['cookies_lifetime'] = $initial_options['']['cookies_lifetime']; }
$_POST['cookies_name'] = affiliation_format_nice_name($_POST['cookies_name']);
if ($_POST['cookies_name'] == 'a_login') { $_POST['cookies_name'] == 'a-login'; }
if ($_POST['email_sent_to_affiliate'] != 'yes') { $_POST['email_sent_to_affiliate'] = 'no'; }
if ($_POST['email_sent_to_affiliator'] != 'yes') { $_POST['email_sent_to_affiliator'] = 'no'; }
$_POST['maximum_login_length'] = (int) $_POST['maximum_login_length'];
if ($_POST['maximum_login_length'] < 2) { $_POST['maximum_login_length'] = $initial_options['']['maximum_login_length']; }
$_POST['maximum_password_length'] = (int) $_POST['maximum_password_length'];
if ($_POST['maximum_password_length'] < 8) { $_POST['maximum_password_length'] = $initial_options['']['maximum_password_length']; }
$_POST['minimum_login_length'] = (int) $_POST['minimum_login_length'];
if ($_POST['minimum_login_length'] < 1) { $_POST['minimum_login_length'] = $initial_options['']['minimum_login_length']; }
if ($_POST['minimum_login_length'] > $_POST['maximum_login_length']) { $_POST['minimum_login_length'] = $_POST['maximum_login_length']; }
$_POST['minimum_password_length'] = (int) $_POST['minimum_password_length'];
if ($_POST['minimum_password_length'] < 1) { $_POST['minimum_password_length'] = $initial_options['']['minimum_password_length']; }
if ($_POST['minimum_password_length'] > $_POST['maximum_password_length']) { $_POST['minimum_password_length'] = $_POST['maximum_password_length']; }
if ($_POST['registration_required'] != 'yes') { $_POST['registration_required'] = 'no'; }
$url_variable_name = affiliation_format_nice_name($_POST['url_variable_name']);
$url_variable_name2 = affiliation_format_nice_name($_POST['url_variable_name2']);
foreach ($initial_options[''] as $key => $value) {
if ($_POST[$key] != '') { $options[$key] = $_POST[$key]; }
else { $options[$key] = $value; } }
update_option('affiliation_manager', $options);
if ($_POST['email_to_affiliate_body'] == '') { $_POST['email_to_affiliate_body'] = $initial_options['email_to_affiliate_body']; }
update_option('affiliation_manager_email_to_affiliate_body', $_POST['email_to_affiliate_body']);
if ($_POST['email_to_affiliator_body'] == '') { $_POST['email_to_affiliator_body'] = $initial_options['email_to_affiliator_body']; }
update_option('affiliation_manager_email_to_affiliator_body', $_POST['email_to_affiliator_body']);
if ($_POST['password_reset_email_body'] == '') { $_POST['password_reset_email_body'] = $initial_options['password_reset_email_body']; }
update_option('affiliation_manager_password_reset_email_body', $_POST['password_reset_email_body']); }
else { $options = (array) get_option('affiliation_manager'); }

$options = array_map('htmlspecialchars', $options);
if (function_exists('commerce_data')) { $currency_code = commerce_data('currency_code'); }
else { $commerce_manager_options = (array) get_option('commerce_manager');
$currency_code = do_shortcode($commerce_manager_options['currency_code']); } ?>

<div class="wrap">
<div id="poststuff">
<?php affiliation_manager_pages_top(); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>'; } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php affiliation_manager_pages_menu(); ?>
<div class="clear"></div>
<p class="description"><?php _e('You can reset an option by leaving the corresponding field blank.', 'affiliation-manager'); ?></p>
<ul class="subsubsub" style="float: none; white-space: normal;">
<li><a href="#general-options"><?php _e('General options', 'affiliation-manager'); ?></a></li>
<li>| <a href="#registration"><?php _e('Registration', 'affiliation-manager'); ?></a></li>
<li>| <a href="#email-sent-to-affiliate"><?php _e('Email sent to affiliate', 'affiliation-manager'); ?></a></li>
<li>| <a href="#email-sent-to-affiliator"><?php _e('Email sent to affiliator', 'affiliation-manager'); ?></a></li>
<li>| <a href="#password-reset-email"><?php _e('Password reset email', 'affiliation-manager'); ?></a></li>
<li>| <a href="#autoresponders"><?php _e('Autoresponders', 'affiliation-manager'); ?></a></li>
</ul>
<div class="postbox">
<h3 id="general-options"><strong><?php _e('General options', 'affiliation-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="affiliation_enabled" id="affiliation_enabled" value="yes"<?php if ($options['affiliation_enabled'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="affiliation_enabled"><?php _e('Use affiliation', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="url_variable_name"><?php _e('Name of the variable used in affiliate links', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="url_variable_name" id="url_variable_name" rows="1" cols="25"><?php echo $options['url_variable_name']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('You can customize the affiliate link structure.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#affiliate-links"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="url_variable_name2"><?php _e('Name of the alternative variable used in affiliate links', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="url_variable_name2" id="url_variable_name2" rows="1" cols="25"><?php echo $options['url_variable_name2']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Your affiliate program can be compatible with another affiliate link structure.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#alternative-affiliate-links"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="cookies_name"><?php _e('Cookies name', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="cookies_name" id="cookies_name" rows="1" cols="25"><?php echo $options['cookies_name']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#cookies"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="cookies_lifetime"><?php _e('Cookies lifetime', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="cookies_lifetime" id="cookies_lifetime" rows="1" cols="25"><?php echo $options['cookies_lifetime']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('days', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_type"><?php _e('Commission type', 'affiliation-manager'); ?></label></strong></th>
<td><select name="commission_type" id="commission_type">
<option value="constant"<?php if ($options['commission_type'] == 'constant') { echo ' selected="selected"'; } ?>><?php _e('Constant', 'affiliation-manager'); ?></option>
<option value="proportional"<?php if ($options['commission_type'] == 'proportional') { echo ' selected="selected"'; } ?>><?php _e('Proportional', 'affiliation-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_amount"><?php _e('Commission amount', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_amount" id="commission_amount" rows="1" cols="25"><?php echo $options['commission_amount']; ?></textarea>  <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you select constant commissions', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_percentage"><?php _e('Commission percentage', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_percentage" id="commission_percentage" rows="1" cols="25"><?php echo $options['commission_percentage']; ?></textarea> <span style="vertical-align: 25%;">% <?php _e('of the price', 'affiliation-manager'); ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you select proportional commissions', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="winner_affiliate"><?php _e('Commission award', 'affiliation-manager'); ?></label></strong></th>
<td><?php _e('The commission is awarded to the', 'affiliation-manager'); ?> <select name="winner_affiliate" id="winner_affiliate">
<option value="first"<?php if ($options['winner_affiliate'] == 'first') { echo ' selected="selected"'; } ?>><?php _e('first', 'affiliation-manager'); ?></option>
<option value="last"<?php if ($options['winner_affiliate'] == 'last') { echo ' selected="selected"'; } ?>><?php _e('last', 'affiliation-manager'); ?></option>
</select> <?php _e('affiliate who referred the order.', 'affiliation-manager'); ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_payment"><?php _e('Commission payment', 'affiliation-manager'); ?></label></strong></th>
<td><select name="commission_payment" id="commission_payment">
<option value="deferred"<?php if ($options['commission_payment'] == 'deferred') { echo ' selected="selected"'; } ?>><?php _e('Deferred', 'affiliation-manager'); ?></option>
<option value="instant"<?php if ($options['commission_payment'] == 'instant') { echo ' selected="selected"'; } ?>><?php _e('Instant', 'affiliation-manager'); ?></option>
</select> 
<span class="description"><?php _e('You can pay your affiliates instantly.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#commission-payment"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="first_sale_winner"><?php _e('First sale award', 'affiliation-manager'); ?></label></strong></th>
<td><?php _e('The first sale referred by the affiliate is awarded to the', 'affiliation-manager'); ?> <select name="first_sale_winner" id="first_sale_winner">
<option value="affiliate"<?php if ($options['first_sale_winner'] == 'affiliate') { echo ' selected="selected"'; } ?>><?php _e('affiliate', 'affiliation-manager'); ?></option>
<option value="affiliator"<?php if ($options['first_sale_winner'] == 'affiliator') { echo ' selected="selected"'; } ?>><?php _e('affiliator', 'affiliation-manager'); ?></option>
</select>. 
<span class="description"><?php _e('Used for instant payment of commissions', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#first-sale-award"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="registration_required" id="registration_required" value="yes"<?php if ($options['registration_required'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="registration_required"><?php _e('Registration to the affiliate program required', 'affiliation-manager'); ?></label><br />
<span class="description"><?php _e('The registration can be optional, only if you select instant payment of commissions.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#optional-registration"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="registration"><strong><?php _e('Registration', 'affiliation-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="minimum_login_length"><?php _e('Minimum login length', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="minimum_login_length" id="minimum_login_length" rows="1" cols="25"><?php echo $options['minimum_login_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="maximum_login_length"><?php _e('Maximum login length', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="maximum_login_length" id="maximum_login_length" rows="1" cols="25"><?php echo $options['maximum_login_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="minimum_password_length"><?php _e('Minimum password length', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="minimum_password_length" id="minimum_password_length" rows="1" cols="25"><?php echo $options['minimum_password_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="maximum_password_length"><?php _e('Maximum password length', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="maximum_password_length" id="maximum_password_length" rows="1" cols="25"><?php echo $options['maximum_password_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_url"><?php _e('Registration confirmation URL', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_url" id="registration_confirmation_url" rows="1" cols="75"><?php echo $options['registration_confirmation_url']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="email-sent-to-affiliate"><strong><?php _e('Email sent to affiliate', 'affiliation-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="email_sent_to_affiliate" id="email_sent_to_affiliate" value="yes"<?php if ($options['email_sent_to_affiliate'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="email_sent_to_affiliate"><?php _e('Send a registration confirmation email to the affiliate', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliate_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_affiliate_sender" id="email_to_affiliate_sender" rows="1" cols="75"><?php echo $options['email_to_affiliate_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliate_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_affiliate_receiver" id="email_to_affiliate_receiver" rows="1" cols="75"><?php echo $options['email_to_affiliate_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliate_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_affiliate_subject" id="email_to_affiliate_subject" rows="1" cols="75"><?php echo $options['email_to_affiliate_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliate_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="email_to_affiliate_body" id="email_to_affiliate_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_email_to_affiliate_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="email-sent-to-affiliator"><strong><?php _e('Email sent to affiliator', 'affiliation-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="email_sent_to_affiliator" id="email_sent_to_affiliator" value="yes"<?php if ($options['email_sent_to_affiliator'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="email_sent_to_affiliator"><?php _e('Send a registration notification email to the affiliator', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliator_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_affiliator_sender" id="email_to_affiliator_sender" rows="1" cols="75"><?php echo $options['email_to_affiliator_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliator_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_affiliator_receiver" id="email_to_affiliator_receiver" rows="1" cols="75"><?php echo $options['email_to_affiliator_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliator_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="email_to_affiliator_subject" id="email_to_affiliator_subject" rows="1" cols="75"><?php echo $options['email_to_affiliator_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="email_to_affiliator_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="email_to_affiliator_body" id="email_to_affiliator_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_email_to_affiliator_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="password-reset-email"><strong><?php _e('Password reset email', 'affiliation-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_email_sender" id="password_reset_email_sender" rows="1" cols="75"><?php echo $options['password_reset_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_email_receiver" id="password_reset_email_receiver" rows="1" cols="75"><?php echo $options['password_reset_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_email_subject" id="password_reset_email_subject" rows="1" cols="75"><?php echo $options['password_reset_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="password_reset_email_body" id="password_reset_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_password_reset_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<div class="postbox">
<h3 id="autoresponders"><strong><?php _e('Autoresponders', 'affiliation-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('You must make some adjustments so that the subscription works with AWeber.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#aweber"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="checkbox" name="affiliate_subscribed_to_autoresponder" id="affiliate_subscribed_to_autoresponder" value="yes"<?php if ($options['affiliate_subscribed_to_autoresponder'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="affiliate_subscribed_to_autoresponder"><?php _e('Subscribe the affiliate to an autoresponder list', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliate_autoresponder"><?php _e('Autoresponder', 'affiliation-manager'); ?></label></strong></th>
<td><select name="affiliate_autoresponder" id="affiliate_autoresponder">
<?php include 'autoresponders.php';
$autoresponder = do_shortcode($options['affiliate_autoresponder']);
foreach ($autoresponders as $value) {
echo '<option value="'.$value.'"'.($autoresponder == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliate_autoresponder_list"><?php _e('List', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="affiliate_autoresponder_list" id="affiliate_autoresponder_list" rows="1" cols="50"><?php echo $options['affiliate_autoresponder_list']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes'); ?>" /></p>
</form>
</div>
</div>