<?php global $wpdb;
$back_office_options = get_option('affiliation_manager_back_office');

if (($_GET['action'] == 'reset') || ($_GET['action'] == 'uninstall')) {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!affiliation_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'affiliation-manager'); }
else { if ($_GET['action'] == 'reset') { reset_affiliation_manager(); } else { uninstall_affiliation_manager(); } } } ?>
<div class="wrap">
<div id="poststuff">
<?php affiliation_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) {
echo '<div class="updated"><p><strong>'.($_GET['action'] == 'reset' ? __('Options reset.', 'affiliation-manager') : __('Options and tables deleted.', 'affiliation-manager')).'</strong></p></div>
<script type="text/javascript">setTimeout(\'window.location = "'.($_GET['action'] == 'reset' ? 'admin.php?page=affiliation-manager' : 'plugins.php').'"\', 2000);</script>'; } ?>
<?php affiliation_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php if ($_GET['action'] == 'reset') { _e('Do you really want to reset the options of Affiliation Manager?', 'affiliation-manager'); }
else { _e('Do you really want to permanently delete the options and tables of Affiliation Manager?', 'affiliation-manager'); } ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'affiliation-manager'); ?>" />
</div>
</form><?php } ?>
</div>
</div><?php }

else {
include 'admin-pages.php';
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!affiliation_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'affiliation-manager'); }
else {
include 'initial-options.php';
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = stripslashes(html_entity_decode(str_replace('&nbsp;', ' ', $value))); } }
$back_office_options = update_affiliation_manager_back_office($back_office_options, 'options');

foreach (array(
'activation_confirmation_email_sent',
'activation_custom_instructions_executed',
'affiliate_subscribed_as_a_client',
'affiliate_subscribed_as_a_user',
'affiliate_subscribed_to_autoresponder',
'affiliate_subscribed_to_members_areas',
'affiliation_enabled',
'bonus_proposal_custom_instructions_executed',
'click_custom_instructions_executed',
'clicks_registration_enabled',
'commission2_enabled',
'deactivation_custom_instructions_executed',
'login_custom_instructions_executed',
'login_notification_email_sent',
'logout_custom_instructions_executed',
'logout_notification_email_sent',
'password_reset_custom_instructions_executed',
'password_reset_notification_email_sent',
'profile_edit_custom_instructions_executed',
'profile_edit_notification_email_sent',
'registration_confirmation_email_sent',
'registration_custom_instructions_executed',
'registration_notification_email_sent',
'registration_required',
'removal_custom_instructions_executed') as $field) { if ($_POST[$field] != 'yes') { $_POST[$field] = 'no'; } }
foreach (array(
'cookies_lifetime',
'maximum_login_length',
'maximum_password_length',
'minimum_login_length',
'minimum_password_length') as $field) { $_POST[$field] = (int) $_POST[$field]; }
foreach (array(
'commission_amount',
'commission_percentage',
'commission2_amount',
'commission2_percentage',
'encrypted_urls_validity_duration') as $field) { $_POST[$field] = str_replace(array('?', ',', ';'), '.', $_POST[$field]); }
foreach (array(
'url_variable_name',
'url_variable_name2') as $field) { $_POST[$field] = format_medium_nice_name($_POST[$field]); }
$members_areas = array_unique(preg_split('#[^0-9]#', $_POST['affiliate_members_areas'], 0, PREG_SPLIT_NO_EMPTY));
sort($members_areas, SORT_NUMERIC);
foreach ($members_areas as $member_area) { $members_areas_list .= $member_area.', '; }
$_POST['affiliate_members_areas'] = substr($members_areas_list, 0, -2);
if ($_POST['cookies_lifetime'] < 1) { $_POST['cookies_lifetime'] = $initial_options['']['cookies_lifetime']; }
$_POST['cookies_name'] = format_nice_name($_POST['cookies_name']);
if ($_POST['cookies_name'] == 'affiliation_login') { $_POST['cookies_name'] == 'affiliation-login'; }
switch ($_POST['maximum_clicks_quantity']) { case 0: case '': case 'i': case 'infinite': case 'u': $_POST['maximum_clicks_quantity'] = 'unlimited'; }
if (is_numeric($_POST['maximum_clicks_quantity'])) {
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."affiliation_manager_clicks", OBJECT);
$clicks_quantity = (int) $row->total;
$n = $clicks_quantity - $_POST['maximum_clicks_quantity'];
if ($n > 0) { $results = $wpdb->query("DELETE FROM ".$wpdb->prefix."affiliation_manager_clicks ORDER BY date ASC LIMIT $n"); } }
if ($_POST['maximum_login_length'] < 2) { $_POST['maximum_login_length'] = $initial_options['']['maximum_login_length']; }
if ($_POST['maximum_password_length'] < 8) { $_POST['maximum_password_length'] = $initial_options['']['maximum_password_length']; }
if ($_POST['minimum_login_length'] < 1) { $_POST['minimum_login_length'] = $initial_options['']['minimum_login_length']; }
if ($_POST['minimum_login_length'] > $_POST['maximum_login_length']) { $_POST['minimum_login_length'] = $_POST['maximum_login_length']; }
if ($_POST['minimum_password_length'] < 1) { $_POST['minimum_password_length'] = $initial_options['']['minimum_password_length']; }
if ($_POST['minimum_password_length'] > $_POST['maximum_password_length']) { $_POST['minimum_password_length'] = $_POST['maximum_password_length']; }
foreach ($initial_options[''] as $key => $value) {
if ($_POST[$key] != '') { $options[$key] = $_POST[$key]; }
else { $options[$key] = $value; } }
update_option('affiliation_manager', $options);
foreach (array(
'activation_confirmation_email_body',
'activation_custom_instructions',
'activation_notification_email_body',
'bonus_proposal_custom_instructions',
'bonus_proposal_email_body',
'click_custom_instructions',
'deactivation_custom_instructions',
'deactivation_notification_email_body',
'login_custom_instructions',
'login_notification_email_body',
'logout_custom_instructions',
'logout_notification_email_body',
'password_reset_custom_instructions',
'password_reset_email_body',
'password_reset_notification_email_body',
'profile_edit_custom_instructions',
'profile_edit_notification_email_body',
'removal_notification_email_body',
'registration_confirmation_email_body',
'registration_custom_instructions',
'registration_notification_email_body',
'removal_custom_instructions') as $field) {
if ($_POST[$field] == '') { $_POST[$field] = $initial_options[$field]; }
update_option('affiliation_manager_'.$field, $_POST[$field]); } } }
if (!isset($options)) { $options = (array) get_option('affiliation_manager'); }

foreach ($options as $key => $value) {
if (is_string($value)) { $options[$key] = htmlspecialchars($value); } }
$undisplayed_modules = (array) $back_office_options['options_page_undisplayed_modules'];
if (function_exists('commerce_data')) { $currency_code = commerce_data('currency_code'); }
else { $commerce_manager_options = (array) get_option('commerce_manager');
$currency_code = do_shortcode($commerce_manager_options['currency_code']); } ?>

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

<div class="postbox" id="general-options-module"<?php if (in_array('general-options', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="general-options"><strong><?php echo $modules['options']['general-options']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="affiliation_enabled" id="affiliation_enabled" value="yes"<?php if ($options['affiliation_enabled'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Use affiliation', 'affiliation-manager'); ?></label></td></tr>
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
</tbody></table>
<div id="level-1-commission-module"<?php if (in_array('level-1-commission', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="level-1-commission"><strong><?php echo $modules['options']['general-options']['modules']['level-1-commission']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('The level 1 commission is awarded to the affiliate who referred the order.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#commissions-levels"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_type"><?php _e('Type', 'affiliation-manager'); ?></label></strong></th>
<td><select name="commission_type" id="commission_type">
<option value="constant"<?php if ($options['commission_type'] == 'constant') { echo ' selected="selected"'; } ?>><?php _e('Constant', 'affiliation-manager'); ?></option>
<option value="proportional"<?php if ($options['commission_type'] == 'proportional') { echo ' selected="selected"'; } ?>><?php _e('Proportional', 'affiliation-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_amount"><?php _e('Amount', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_amount" id="commission_amount" rows="1" cols="25"><?php echo $options['commission_amount']; ?></textarea>  <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you select constant commissions', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_percentage"><?php _e('Percentage', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_percentage" id="commission_percentage" rows="1" cols="25"><?php echo $options['commission_percentage']; ?></textarea> <span style="vertical-align: 25%;">% <?php _e('of the price', 'affiliation-manager'); ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you select proportional commissions', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="winner_affiliate"><?php _e('Award', 'affiliation-manager'); ?></label></strong></th>
<td><?php _e('The commission is awarded to the', 'affiliation-manager'); ?> <select name="winner_affiliate" id="winner_affiliate">
<option value="first"<?php if ($options['winner_affiliate'] == 'first') { echo ' selected="selected"'; } ?>><?php _e('first', 'affiliation-manager'); ?></option>
<option value="last"<?php if ($options['winner_affiliate'] == 'last') { echo ' selected="selected"'; } ?>><?php _e('last', 'affiliation-manager'); ?></option>
</select> <?php _e('affiliate who referred the order.', 'affiliation-manager'); ?> 
<span class="description"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#commission-award"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_payment"><?php _e('Payment', 'affiliation-manager'); ?></label></strong></th>
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
</tbody></table>
</div>
<div id="level-2-commission-module"<?php if (in_array('level-2-commission', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="level-2-commission"><strong><?php echo $modules['options']['general-options']['modules']['level-2-commission']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('The level 2 commission is awarded to the referrer of the affiliate who referred the order.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#commissions-levels"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="commission2_enabled" id="commission2_enabled" value="yes"<?php if ($options['commission2_enabled'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Award a level 2 commission', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_type"><?php _e('Type', 'affiliation-manager'); ?></label></strong></th>
<td><select name="commission2_type" id="commission2_type">
<option value="constant"<?php if ($options['commission2_type'] == 'constant') { echo ' selected="selected"'; } ?>><?php _e('Constant', 'affiliation-manager'); ?></option>
<option value="proportional"<?php if ($options['commission2_type'] == 'proportional') { echo ' selected="selected"'; } ?>><?php _e('Proportional', 'affiliation-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_amount"><?php _e('Amount', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission2_amount" id="commission2_amount" rows="1" cols="25"><?php echo $options['commission2_amount']; ?></textarea>  <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you select constant commissions', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_percentage"><?php _e('Percentage', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission2_percentage" id="commission2_percentage" rows="1" cols="25"><?php echo $options['commission2_percentage']; ?></textarea> <span style="vertical-align: 25%;">% <?php _e('of the price', 'affiliation-manager'); ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you select proportional commissions', 'affiliation-manager'); ?></span></td></tr>
</tbody></table>
</div>
<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="clicks-module"<?php if (in_array('clicks', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="clicks"><strong><?php echo $modules['options']['clicks']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="clicks_registration_enabled" id="clicks_registration_enabled" value="yes"<?php if ($options['clicks_registration_enabled'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Save clicks on affiliate links', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="maximum_clicks_quantity"><?php _e('Maximum clicks quantity', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="maximum_clicks_quantity" id="maximum_clicks_quantity" rows="1" cols="25"><?php echo ($options['maximum_clicks_quantity'] == 'unlimited' ? '' : $options['maximum_clicks_quantity']); ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('You can save only the latest clicks to ease your database.', 'affiliation-manager'); ?><br />
<?php _e('Leave this field blank for an unlimited quantity.', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="registration-module"<?php if (in_array('registration', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration"><strong><?php echo $modules['options']['registration']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="registration_required" id="registration_required" value="yes"<?php if ($options['registration_required'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Registration to the affiliate program required', 'affiliation-manager'); ?></label><br />
<span class="description"><?php _e('The registration can be optional, only if you select instant payment of commissions.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#optional-registration"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="minimum_login_length"><?php _e('Minimum login length', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="minimum_login_length" id="minimum_login_length" rows="1" cols="25"><?php echo $options['minimum_login_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="maximum_login_length"><?php _e('Maximum login length', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="maximum_login_length" id="maximum_login_length" rows="1" cols="25"><?php echo $options['maximum_login_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="minimum_password_length"><?php _e('Minimum password length', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="minimum_password_length" id="minimum_password_length" rows="1" cols="25"><?php echo $options['minimum_password_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="maximum_password_length"><?php _e('Maximum password length', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="maximum_password_length" id="maximum_password_length" rows="1" cols="25"><?php echo $options['maximum_password_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_url"><?php _e('Registration confirmation URL', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_url" id="registration_confirmation_url" rows="1" cols="75"><?php echo $options['registration_confirmation_url']; ?></textarea> <a style="vertical-align: 25%;" href="<?php echo htmlspecialchars(format_url(do_shortcode($options['registration_confirmation_url']))); ?>"><?php _e('Link', 'affiliation-manager'); ?></a></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."affiliation_manager_affiliates_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliates_initial_category_id"><?php _e('Affiliates initial category', 'affiliation-manager'); ?></label></strong></th>
<td><select name="affiliates_initial_category_id" id="affiliates_initial_category_id">
<option value="0"<?php if ($options['affiliates_initial_category_id'] == '0') { echo ' selected="selected"'; } ?>><?php _e('None ', 'affiliation-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($options['affiliates_initial_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<span class="description"><?php _e('Category assigned to affiliates upon their registration', 'affiliation-manager'); ?></span>
<?php if ($options['affiliates_initial_category_id'] > 0) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$options['affiliates_initial_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$options['affiliates_initial_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliates_initial_status"><?php _e('Affiliates initial status', 'affiliation-manager'); ?></label></strong></th>
<td><select name="affiliates_initial_status" id="affiliates_initial_status">
<option value="active"<?php if ($options['affiliates_initial_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'affiliation-manager'); ?></option>
<option value="inactive"<?php if ($options['affiliates_initial_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'affiliation-manager'); ?></option>
</select>
<span class="description"><?php _e('Status assigned to affiliates upon their registration', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#affiliate-status"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_confirmation_url"><?php _e('Activation confirmation URL', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_confirmation_url" id="activation_confirmation_url" rows="1" cols="75"><?php echo $options['activation_confirmation_url']; ?></textarea> <a style="vertical-align: 25%;" href="<?php echo htmlspecialchars(format_url(do_shortcode($options['activation_confirmation_url']))); ?>"><?php _e('Link', 'affiliation-manager'); ?></a></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="urls-encryption-module"<?php if (in_array('urls-encryption', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="urls-encryption"><strong><?php echo $modules['options']['urls-encryption']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('You can encrypt the URLs.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#urls-encryption"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="encrypted_urls_validity_duration"><?php _e('Validity duration', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="encrypted_urls_validity_duration" id="encrypted_urls_validity_duration" rows="1" cols="25"><?php echo $options['encrypted_urls_validity_duration']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('hours', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="encrypted_urls_key"><?php _e('Encryption key', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="encrypted_urls_key" id="encrypted_urls_key" rows="1" cols="50"><?php echo $options['encrypted_urls_key']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="registration-confirmation-email-module"<?php if (in_array('registration-confirmation-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration-confirmation-email"><strong><?php echo $modules['options']['registration-confirmation-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="registration_confirmation_email_sent" id="registration_confirmation_email_sent" value="yes"<?php if ($options['registration_confirmation_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration confirmation email', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_sender" id="registration_confirmation_email_sender" rows="1" cols="75"><?php echo $options['registration_confirmation_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_receiver" id="registration_confirmation_email_receiver" rows="1" cols="75"><?php echo $options['registration_confirmation_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_subject" id="registration_confirmation_email_subject" rows="1" cols="75"><?php echo $options['registration_confirmation_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_confirmation_email_body" id="registration_confirmation_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_registration_confirmation_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="registration-notification-email-module"<?php if (in_array('registration-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration-notification-email"><strong><?php echo $modules['options']['registration-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="registration_notification_email_sent" id="registration_notification_email_sent" value="yes"<?php if ($options['registration_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration notification email', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_sender" id="registration_notification_email_sender" rows="1" cols="75"><?php echo $options['registration_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_receiver" id="registration_notification_email_receiver" rows="1" cols="75"><?php echo $options['registration_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_subject" id="registration_notification_email_subject" rows="1" cols="75"><?php echo $options['registration_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_notification_email_body" id="registration_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_registration_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="autoresponders-module"<?php if (in_array('autoresponders', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="autoresponders"><strong><?php echo $modules['options']['autoresponders']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('You must make some adjustments so that the subscription works with some autoresponders.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#autoresponders"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="affiliate_subscribed_to_autoresponder" id="affiliate_subscribed_to_autoresponder" value="yes"<?php if ($options['affiliate_subscribed_to_autoresponder'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Subscribe the affiliate to an autoresponder list', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliate_autoresponder"><?php _e('Autoresponder', 'affiliation-manager'); ?></label></strong></th>
<td><select name="affiliate_autoresponder" id="affiliate_autoresponder">
<?php include 'libraries/autoresponders.php';
$autoresponder = do_shortcode($options['affiliate_autoresponder']);
foreach ($autoresponders as $value) {
echo '<option value="'.$value.'"'.($autoresponder == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliate_autoresponder_list"><?php _e('List', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="affiliate_autoresponder_list" id="affiliate_autoresponder_list" rows="1" cols="50"><?php echo $options['affiliate_autoresponder_list']; ?></textarea><br />
<span class="description"><?php _e('For some autoresponders, you must enter the list ID.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#autoresponders"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="autoresponders-integration-module"<?php if (in_array('autoresponders-integration', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="autoresponders-integration"><strong><?php echo $modules['options']['autoresponders-integration']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<?php if (function_exists('commerce_manager_admin_menu')) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager#autoresponders-integration"><?php _e('Click here to configure the options of Commerce Manager.', 'affiliation-manager'); ?></a></span></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('You must make some adjustments so that the subscription works with some autoresponders.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#autoresponders"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
</tbody></table>
<div id="aweber-module"<?php if (in_array('aweber', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="aweber"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['aweber']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#aweber"><?php _e('Click here to read the instructions for integration.', 'affiliation-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="cybermailing-module"<?php if (in_array('cybermailing', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="cybermailing"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['cybermailing']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('You have no adjustment to make so that the subscription works with CyberMailing.', 'affiliation-manager'); ?></span></td></tr>
</tbody></table>
</div>
<div id="getresponse-module"<?php if (in_array('getresponse', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="getresponse"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['getresponse']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="getresponse_api_key"><?php _e('API key', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="getresponse_api_key" id="getresponse_api_key" rows="1" cols="50"><?php echo $options['getresponse_api_key']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#getresponse"><?php _e('More informations', 'affiliation-manager'); ?></a>
<?php if (function_exists('commerce_manager_admin_menu')) { echo '<br />'.__('Leave this field blank to apply the Commerce Manager\'s option.', 'affiliation-manager'); } ?></span></td></tr>
</tbody></table>
</div>
<div id="mailchimp-module"<?php if (in_array('mailchimp', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="mailchimp"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['mailchimp']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="mailchimp_api_key"><?php _e('API key', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="mailchimp_api_key" id="mailchimp_api_key" rows="1" cols="50"><?php echo $options['mailchimp_api_key']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#mailchimp"><?php _e('More informations', 'affiliation-manager'); ?></a>
<?php if (function_exists('commerce_manager_admin_menu')) { echo '<br />'.__('Leave this field blank to apply the Commerce Manager\'s option.', 'affiliation-manager'); } ?></span></td></tr>
</tbody></table>
</div>
<div id="sg-autorepondeur-module"<?php if (in_array('sg-autorepondeur', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="sg-autorepondeur"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['sg-autorepondeur']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="sg_autorepondeur_account_id"><?php _e('Account ID', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="sg_autorepondeur_account_id" id="sg_autorepondeur_account_id" rows="1" cols="25"><?php echo $options['sg_autorepondeur_account_id']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#sg-autorepondeur"><?php _e('More informations', 'affiliation-manager'); ?></a>
<?php if (function_exists('commerce_manager_admin_menu')) { echo '<br />'.__('Leave this field blank to apply the Commerce Manager\'s option.', 'affiliation-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="sg_autorepondeur_activation_code"><?php _e('Activation code', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="sg_autorepondeur_activation_code" id="sg_autorepondeur_activation_code" rows="1" cols="50"><?php echo $options['sg_autorepondeur_activation_code']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#sg-autorepondeur"><?php _e('More informations', 'affiliation-manager'); ?></a>
<?php if (function_exists('commerce_manager_admin_menu')) { echo '<br />'.__('Leave this field blank to apply the Commerce Manager\'s option.', 'affiliation-manager'); } ?></span></td></tr>
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
<td><span class="description"><?php echo (function_exists('commerce_manager_admin_menu') ? '<a href="admin.php?page=commerce-manager-clients-accounts">'.__('Click here to configure the options of Commerce Manager.', 'affiliation-manager').'</a>' : __('To subscribe your affiliates as clients, you must have installed and activated <a href="http://www.kleor-editions.com/commerce-manager">Commerce Manager</a>.', 'affiliation-manager')); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="affiliate_subscribed_as_a_client" id="affiliate_subscribed_as_a_client" value="yes"<?php if ($options['affiliate_subscribed_as_a_client'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the affiliate as a client', 'affiliation-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#registration-as-a-client"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."commerce_manager_clients_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliate_client_category_id"><?php _e('Category', 'affiliation-manager'); ?></label></strong></th>
<td><select name="affiliate_client_category_id" id="affiliate_client_category_id">
<option value=""<?php if ($options['affiliate_client_category_id'] == '') { echo ' selected="selected"'; } ?>><?php _e('Commerce Manager\'s option', 'affiliation-manager'); ?></option>
<option value="0"<?php if ($options['affiliate_client_category_id'] == '0') { echo ' selected="selected"'; } ?>><?php _e('None ', 'affiliation-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($options['affiliate_client_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ((function_exists('commerce_manager_admin_menu')) && ($options['affiliate_client_category_id'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-client-category&amp;id='.$options['affiliate_client_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-client-category&amp;id='.$options['affiliate_client_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliate_client_status"><?php _e('Status', 'affiliation-manager'); ?></label></strong></th>
<td><select name="affiliate_client_status" id="affiliate_client_status">
<option value=""<?php if ($options['affiliate_client_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('Commerce Manager\'s option', 'affiliation-manager'); ?></option>
<option value="active"<?php if ($options['affiliate_client_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'affiliation-manager'); ?></option>
<option value="inactive"<?php if ($options['affiliate_client_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'affiliation-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#client-status"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commerce_registration_confirmation_email_sent"><?php _e('Send a registration confirmation email', 'affiliation-manager'); ?></label></strong></th>
<td><select name="commerce_registration_confirmation_email_sent" id="commerce_registration_confirmation_email_sent">
<option value=""<?php if ($options['commerce_registration_confirmation_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Commerce Manager\'s option', 'affiliation-manager'); ?></option>
<option value="yes"<?php if ($options['commerce_registration_confirmation_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'affiliation-manager'); ?></option>
<option value="no"<?php if ($options['commerce_registration_confirmation_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'affiliation-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commerce_registration_notification_email_sent"><?php _e('Send a registration notification email', 'affiliation-manager'); ?></label></strong></th>
<td><select name="commerce_registration_notification_email_sent" id="commerce_registration_notification_email_sent">
<option value=""<?php if ($options['commerce_registration_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Commerce Manager\'s option', 'affiliation-manager'); ?></option>
<option value="yes"<?php if ($options['commerce_registration_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'affiliation-manager'); ?></option>
<option value="no"<?php if ($options['commerce_registration_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'affiliation-manager'); ?></option>
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
<td><span class="description"><?php echo (function_exists('membership_manager_admin_menu') ? '<a href="admin.php?page=membership-manager">'.__('Click here to configure the options of Membership Manager.', 'affiliation-manager').'</a>' : __('To use membership, you must have installed and activated <a href="http://www.kleor-editions.com/membership-manager">Membership Manager</a>.', 'affiliation-manager')); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="affiliate_subscribed_to_members_areas" id="affiliate_subscribed_to_members_areas" value="yes"<?php if ($options['affiliate_subscribed_to_members_areas'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the affiliate to a member area', 'affiliation-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#membership"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliate_members_areas"><?php _e('Members areas', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="affiliate_members_areas" id="affiliate_members_areas" rows="1" cols="50"><?php echo $options['affiliate_members_areas']; ?></textarea>
<?php if ((function_exists('membership_manager_admin_menu')) && (is_numeric($options['affiliate_members_areas'])) && ($options['affiliate_members_areas'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-area&amp;id='.$options['affiliate_members_areas'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-area&amp;id='.$options['affiliate_members_areas'].'&amp;action=delete">'.__('Delete').'</a>'; } ?><br />
<span class="description"><?php _e('Separate the IDs of the members areas with commas.', 'affiliation-manager'); ?></span></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."membership_manager_members_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliate_member_category_id"><?php _e('Category', 'affiliation-manager'); ?></label></strong></th>
<td><select name="affiliate_member_category_id" id="affiliate_member_category_id">
<option value=""<?php if ($options['affiliate_member_category_id'] == '') { echo ' selected="selected"'; } ?>><?php _e('Member area\'s option', 'affiliation-manager'); ?></option>
<option value="0"<?php if ($options['affiliate_member_category_id'] == '0') { echo ' selected="selected"'; } ?>><?php _e('None ', 'affiliation-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($options['affiliate_member_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ((function_exists('membership_manager_admin_menu')) && ($options['affiliate_member_category_id'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-category&amp;id='.$options['affiliate_member_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-category&amp;id='.$options['affiliate_member_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliate_member_status"><?php _e('Status', 'affiliation-manager'); ?></label></strong></th>
<td><select name="affiliate_member_status" id="affiliate_member_status">
<option value=""<?php if ($options['affiliate_member_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('Member area\'s option', 'affiliation-manager'); ?></option>
<option value="active"<?php if ($options['affiliate_member_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'affiliation-manager'); ?></option>
<option value="inactive"<?php if ($options['affiliate_member_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'affiliation-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#member-status"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="membership_registration_confirmation_email_sent"><?php _e('Send a registration confirmation email', 'affiliation-manager'); ?></label></strong></th>
<td><select name="membership_registration_confirmation_email_sent" id="membership_registration_confirmation_email_sent">
<option value=""<?php if ($options['membership_registration_confirmation_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Member area\'s option', 'affiliation-manager'); ?></option>
<option value="yes"<?php if ($options['membership_registration_confirmation_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'affiliation-manager'); ?></option>
<option value="no"<?php if ($options['membership_registration_confirmation_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'affiliation-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="membership_registration_notification_email_sent"><?php _e('Send a registration notification email', 'affiliation-manager'); ?></label></strong></th>
<td><select name="membership_registration_notification_email_sent" id="membership_registration_notification_email_sent">
<option value=""<?php if ($options['membership_registration_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Member area\'s option', 'affiliation-manager'); ?></option>
<option value="yes"<?php if ($options['membership_registration_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'affiliation-manager'); ?></option>
<option value="no"<?php if ($options['membership_registration_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'affiliation-manager'); ?></option>
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
<td><label><input type="checkbox" name="affiliate_subscribed_as_a_user" id="affiliate_subscribed_as_a_user" value="yes"<?php if ($options['affiliate_subscribed_as_a_user'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the affiliate as a user', 'affiliation-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#wordpress"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliate_user_role"><?php _e('Role', 'affiliation-manager'); ?></label></strong></th>
<td><select name="affiliate_user_role" id="affiliate_user_role">
<?php foreach (affiliation_manager_users_roles() as $role => $name) {
echo '<option value="'.$role.'"'.($options['affiliate_user_role'] == $role ? ' selected="selected"' : '').'>'.$name.'</option>'."\n"; } ?>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="custom-instructions-module"<?php if (in_array('custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['name']; ?></strong></h3>
<div class="inside">
<div id="registration-custom-instructions-module"<?php if (in_array('registration-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="registration-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['registration-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="registration_custom_instructions_executed" id="registration_custom_instructions_executed" value="yes"<?php if ($options['registration_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_custom_instructions"><?php _e('PHP code', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_custom_instructions" id="registration_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_registration_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the registration of an affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#custom-instructions"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="login-custom-instructions-module"<?php if (in_array('login-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="login-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['login-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="login_custom_instructions_executed" id="login_custom_instructions_executed" value="yes"<?php if ($options['login_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="login_custom_instructions"><?php _e('PHP code', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="login_custom_instructions" id="login_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_login_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the login of an affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#custom-instructions"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="logout-custom-instructions-module"<?php if (in_array('logout-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="logout-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['logout-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="logout_custom_instructions_executed" id="logout_custom_instructions_executed" value="yes"<?php if ($options['logout_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="logout_custom_instructions"><?php _e('PHP code', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="logout_custom_instructions" id="logout_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_logout_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the logout of an affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#custom-instructions"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="password-reset-custom-instructions-module"<?php if (in_array('password-reset-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="password-reset-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['password-reset-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="password_reset_custom_instructions_executed" id="password_reset_custom_instructions_executed" value="yes"<?php if ($options['password_reset_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_custom_instructions"><?php _e('PHP code', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="password_reset_custom_instructions" id="password_reset_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_password_reset_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the password reset of an affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#custom-instructions"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="profile-edit-custom-instructions-module"<?php if (in_array('profile-edit-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="profile-edit-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['profile-edit-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="profile_edit_custom_instructions_executed" id="profile_edit_custom_instructions_executed" value="yes"<?php if ($options['profile_edit_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_edit_custom_instructions"><?php _e('PHP code', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="profile_edit_custom_instructions" id="profile_edit_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_profile_edit_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the profile edit of an affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#custom-instructions"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="bonus-proposal-custom-instructions-module"<?php if (in_array('bonus-proposal-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="bonus-proposal-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['bonus-proposal-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="bonus_proposal_custom_instructions_executed" id="bonus_proposal_custom_instructions_executed" value="yes"<?php if ($options['bonus_proposal_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="bonus_proposal_custom_instructions"><?php _e('PHP code', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="bonus_proposal_custom_instructions" id="bonus_proposal_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_bonus_proposal_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the bonus proposal of an affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#custom-instructions"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="activation-custom-instructions-module"<?php if (in_array('activation-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="activation-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['activation-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="activation_custom_instructions_executed" id="activation_custom_instructions_executed" value="yes"<?php if ($options['activation_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_custom_instructions"><?php _e('PHP code', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="activation_custom_instructions" id="activation_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_activation_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the activation of an affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#custom-instructions"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="deactivation-custom-instructions-module"<?php if (in_array('deactivation-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="deactivation-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['deactivation-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="deactivation_custom_instructions_executed" id="deactivation_custom_instructions_executed" value="yes"<?php if ($options['deactivation_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_custom_instructions"><?php _e('PHP code', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="deactivation_custom_instructions" id="deactivation_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_deactivation_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the deactivation of an affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#custom-instructions"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="removal-custom-instructions-module"<?php if (in_array('removal-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="removal-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['removal-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="removal_custom_instructions_executed" id="removal_custom_instructions_executed" value="yes"<?php if ($options['removal_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_custom_instructions"><?php _e('PHP code', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="removal_custom_instructions" id="removal_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_removal_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the removal of an affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#custom-instructions"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="click-custom-instructions-module"<?php if (in_array('click-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="click-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['click-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="click_custom_instructions_executed" id="click_custom_instructions_executed" value="yes"<?php if ($options['click_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="click_custom_instructions"><?php _e('PHP code', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="click_custom_instructions" id="click_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_click_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after each click on an affiliate link.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#custom-instructions"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="login-notification-email-module"<?php if (in_array('login-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="login-notification-email"><strong><?php echo $modules['options']['login-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="login_notification_email_sent" id="login_notification_email_sent" value="yes"<?php if ($options['login_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a login notification email', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="login_notification_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="login_notification_email_sender" id="login_notification_email_sender" rows="1" cols="75"><?php echo $options['login_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="login_notification_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="login_notification_email_receiver" id="login_notification_email_receiver" rows="1" cols="75"><?php echo $options['login_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="login_notification_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="login_notification_email_subject" id="login_notification_email_subject" rows="1" cols="75"><?php echo $options['login_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="login_notification_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="login_notification_email_body" id="login_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_login_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="logout-notification-email-module"<?php if (in_array('logout-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="logout-notification-email"><strong><?php echo $modules['options']['logout-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="logout_notification_email_sent" id="logout_notification_email_sent" value="yes"<?php if ($options['logout_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a logout notification email', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="logout_notification_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="logout_notification_email_sender" id="logout_notification_email_sender" rows="1" cols="75"><?php echo $options['logout_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="logout_notification_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="logout_notification_email_receiver" id="logout_notification_email_receiver" rows="1" cols="75"><?php echo $options['logout_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="logout_notification_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="logout_notification_email_subject" id="logout_notification_email_subject" rows="1" cols="75"><?php echo $options['logout_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="logout_notification_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="logout_notification_email_body" id="logout_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_logout_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="password-reset-email-module"<?php if (in_array('password-reset-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="password-reset-email"><strong><?php echo $modules['options']['password-reset-email']['name']; ?></strong></h3>
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

<div class="postbox" id="password-reset-notification-email-module"<?php if (in_array('password-reset-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="password-reset-notification-email"><strong><?php echo $modules['options']['password-reset-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="password_reset_notification_email_sent" id="password_reset_notification_email_sent" value="yes"<?php if ($options['password_reset_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a password reset notification email', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_notification_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_notification_email_sender" id="password_reset_notification_email_sender" rows="1" cols="75"><?php echo $options['password_reset_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_notification_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_notification_email_receiver" id="password_reset_notification_email_receiver" rows="1" cols="75"><?php echo $options['password_reset_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_notification_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_notification_email_subject" id="password_reset_notification_email_subject" rows="1" cols="75"><?php echo $options['password_reset_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_notification_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="password_reset_notification_email_body" id="password_reset_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_password_reset_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="profile-edit-notification-email-module"<?php if (in_array('profile-edit-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="profile-edit-notification-email"><strong><?php echo $modules['options']['profile-edit-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="profile_edit_notification_email_sent" id="profile_edit_notification_email_sent" value="yes"<?php if ($options['profile_edit_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a profile edit notification email', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_edit_notification_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="profile_edit_notification_email_sender" id="profile_edit_notification_email_sender" rows="1" cols="75"><?php echo $options['profile_edit_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_edit_notification_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="profile_edit_notification_email_receiver" id="profile_edit_notification_email_receiver" rows="1" cols="75"><?php echo $options['profile_edit_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_edit_notification_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="profile_edit_notification_email_subject" id="profile_edit_notification_email_subject" rows="1" cols="75"><?php echo $options['profile_edit_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_edit_notification_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="profile_edit_notification_email_body" id="profile_edit_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_profile_edit_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="bonus-proposal-email-module"<?php if (in_array('bonus-proposal-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="bonus-proposal-email"><strong><?php echo $modules['options']['bonus-proposal-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="bonus_proposal_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="bonus_proposal_email_sender" id="bonus_proposal_email_sender" rows="1" cols="75"><?php echo $options['bonus_proposal_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="bonus_proposal_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="bonus_proposal_email_receiver" id="bonus_proposal_email_receiver" rows="1" cols="75"><?php echo $options['bonus_proposal_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="bonus_proposal_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="bonus_proposal_email_subject" id="bonus_proposal_email_subject" rows="1" cols="75"><?php echo $options['bonus_proposal_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="bonus_proposal_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="bonus_proposal_email_body" id="bonus_proposal_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_bonus_proposal_email_body')); ?></textarea>
<span class="description"><?php _e('You can allow your affiliates to offer a bonus to customers who order through their affiliate link.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#bonus-offered-by-affiliate"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="activation-confirmation-email-module"<?php if (in_array('activation-confirmation-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="activation-confirmation-email"><strong><?php echo $modules['options']['activation-confirmation-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="activation_confirmation_email_sent" id="activation_confirmation_email_sent" value="yes"<?php if ($options['activation_confirmation_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send an activation confirmation email', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_confirmation_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_confirmation_email_sender" id="activation_confirmation_email_sender" rows="1" cols="75"><?php echo $options['activation_confirmation_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_confirmation_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_confirmation_email_receiver" id="activation_confirmation_email_receiver" rows="1" cols="75"><?php echo $options['activation_confirmation_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_confirmation_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_confirmation_email_subject" id="activation_confirmation_email_subject" rows="1" cols="75"><?php echo $options['activation_confirmation_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_confirmation_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="activation_confirmation_email_body" id="activation_confirmation_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_activation_confirmation_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="activation-notification-email-module"<?php if (in_array('activation-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="activation-notification-email"><strong><?php echo $modules['options']['activation-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_sender" id="activation_notification_email_sender" rows="1" cols="75"><?php echo $options['activation_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_receiver" id="activation_notification_email_receiver" rows="1" cols="75"><?php echo $options['activation_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_subject" id="activation_notification_email_subject" rows="1" cols="75"><?php echo $options['activation_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="activation_notification_email_body" id="activation_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_activation_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="deactivation-notification-email-module"<?php if (in_array('deactivation-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="deactivation-notification-email"><strong><?php echo $modules['options']['deactivation-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="deactivation_notification_email_sender" id="deactivation_notification_email_sender" rows="1" cols="75"><?php echo $options['deactivation_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="deactivation_notification_email_receiver" id="deactivation_notification_email_receiver" rows="1" cols="75"><?php echo $options['deactivation_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="deactivation_notification_email_subject" id="deactivation_notification_email_subject" rows="1" cols="75"><?php echo $options['deactivation_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="deactivation_notification_email_body" id="deactivation_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_deactivation_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="removal-notification-email-module"<?php if (in_array('removal-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="removal-notification-email"><strong><?php echo $modules['options']['removal-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="removal_notification_email_sender" id="removal_notification_email_sender" rows="1" cols="75"><?php echo $options['removal_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="removal_notification_email_receiver" id="removal_notification_email_receiver" rows="1" cols="75"><?php echo $options['removal_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="removal_notification_email_subject" id="removal_notification_email_subject" rows="1" cols="75"><?php echo $options['removal_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="removal_notification_email_body" id="removal_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_removal_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="forms-module"<?php if (in_array('forms', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="forms"><strong><?php echo $modules['options']['forms']['name']; ?></strong></h3>
<div class="inside">
<div id="captcha-module"<?php if (in_array('captcha', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="captcha"><strong><?php echo $modules['options']['forms']['modules']['captcha']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="default_captcha_type"><?php _e('Default type', 'affiliation-manager'); ?></label></strong></th>
<td><select name="default_captcha_type" id="default_captcha_type">
<?php include 'libraries/captchas.php';
$captcha_type = do_shortcode($options['default_captcha_type']);
asort($captchas_types);
foreach ($captchas_types as $key => $value) {
echo '<option value="'.$key.'"'.($captcha_type == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select>
<span class="description"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#captcha"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="default_recaptcha_theme"><?php _e('Default reCAPTCHA theme', 'affiliation-manager'); ?></label></strong></th>
<td><select name="default_recaptcha_theme" id="default_recaptcha_theme">
<?php include 'libraries/captchas.php';
$recaptcha_theme = do_shortcode($options['default_recaptcha_theme']);
asort($recaptcha_themes);
foreach ($recaptcha_themes as $key => $value) {
echo '<option value="'.$key.'"'.($recaptcha_theme == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select>
<span class="description"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#recaptcha"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recaptcha_public_key"><?php _e('reCAPTCHA public key', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="recaptcha_public_key" id="recaptcha_public_key" rows="1" cols="50"><?php echo $options['recaptcha_public_key']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#recaptcha"><?php _e('More informations', 'affiliation-manager'); ?></a>
<?php if (function_exists('commerce_manager_admin_menu')) { echo '<br />'.__('Leave this field blank to apply the Commerce Manager\'s option.', 'affiliation-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recaptcha_private_key"><?php _e('reCAPTCHA private key', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="recaptcha_private_key" id="recaptcha_private_key" rows="1" cols="50"><?php echo $options['recaptcha_private_key']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#recaptcha"><?php _e('More informations', 'affiliation-manager'); ?></a>
<?php if (function_exists('commerce_manager_admin_menu')) { echo '<br />'.__('Leave this field blank to apply the Commerce Manager\'s option.', 'affiliation-manager'); } ?></span></td></tr>
</tbody></table>
</div>
<div id="error-messages-module"<?php if (in_array('error-messages', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="error-messages"><strong><?php echo $modules['options']['forms']['modules']['error-messages']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="unfilled_fields_message"><?php _e('Unfilled required fields', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="unfilled_fields_message" id="unfilled_fields_message" rows="1" cols="75"><?php echo $options['unfilled_fields_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="unfilled_field_message"><?php _e('Unfilled required field', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="unfilled_field_message" id="unfilled_field_message" rows="1" cols="75"><?php echo $options['unfilled_field_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="invalid_email_address_message"><?php _e('Invalid email address', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="invalid_email_address_message" id="invalid_email_address_message" rows="1" cols="75"><?php echo $options['invalid_email_address_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="numeric_login_message"><?php _e('Numeric login name', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="numeric_login_message" id="numeric_login_message" rows="1" cols="75"><?php echo $options['numeric_login_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="too_short_login_message"><?php _e('Too short login name', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="too_short_login_message" id="too_short_login_message" rows="1" cols="75"><?php echo $options['too_short_login_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="too_long_login_message"><?php _e('Too long login name', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="too_long_login_message" id="too_long_login_message" rows="1" cols="75"><?php echo $options['too_long_login_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="too_short_password_message"><?php _e('Too short password', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="too_short_password_message" id="too_short_password_message" rows="1" cols="75"><?php echo $options['too_short_password_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="too_long_password_message"><?php _e('Too long password', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="too_long_password_message" id="too_long_password_message" rows="1" cols="75"><?php echo $options['too_long_password_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="unavailable_login_message"><?php _e('Unavailable login name', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="unavailable_login_message" id="unavailable_login_message" rows="1" cols="75"><?php echo $options['unavailable_login_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="unavailable_email_address_message"><?php _e('Unavailable email address', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="unavailable_email_address_message" id="unavailable_email_address_message" rows="1" cols="75"><?php echo $options['unavailable_email_address_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="unavailable_paypal_email_address_message"><?php _e('Unavailable paypal email address', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="unavailable_paypal_email_address_message" id="unavailable_paypal_email_address_message" rows="1" cols="75"><?php echo $options['unavailable_paypal_email_address_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="invalid_login_or_password_message"><?php _e('Invalid login or password', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="invalid_login_or_password_message" id="invalid_login_or_password_message" rows="1" cols="75"><?php echo $options['invalid_login_or_password_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="inactive_account_message"><?php _e('Inactive account', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="inactive_account_message" id="inactive_account_message" rows="1" cols="75"><?php echo $options['inactive_account_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="inexistent_email_address_message"><?php _e('Inexistent email address', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="inexistent_email_address_message" id="inexistent_email_address_message" rows="1" cols="75"><?php echo $options['inexistent_email_address_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="invalid_captcha_message"><?php _e('Invalid CAPTCHA', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="invalid_captcha_message" id="invalid_captcha_message" rows="1" cols="75"><?php echo $options['invalid_captcha_message']; ?></textarea></td></tr>
</tbody></table>
</div>
<div id="login-availability-indicator-module"<?php if (in_array('login-availability-indicator', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="login-availability-indicator"><strong><?php echo $modules['options']['forms']['modules']['login-availability-indicator']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="available_login_indicator_message"><?php _e('Available', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="available_login_indicator_message" id="available_login_indicator_message" rows="1" cols="75"><?php echo $options['available_login_indicator_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="unavailable_login_indicator_message"><?php _e('Unavailable', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="unavailable_login_indicator_message" id="unavailable_login_indicator_message" rows="1" cols="75"><?php echo $options['unavailable_login_indicator_message']; ?></textarea></td></tr>
</tbody></table>
</div>
<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes'); ?>" /></p>
<?php affiliation_manager_pages_module($back_office_options, 'options-page', $undisplayed_modules); ?>
</form>
</div>
</div>
<?php }