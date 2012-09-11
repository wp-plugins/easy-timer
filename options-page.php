<?php global $wpdb;
$back_office_options = get_option('optin_manager_back_office');

if (($_GET['action'] == 'reset') || ($_GET['action'] == 'uninstall')) {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!optin_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'optin-manager'); }
else { if ($_GET['action'] == 'reset') { reset_optin_manager(); } else { uninstall_optin_manager(); } } } ?>
<div class="wrap">
<div id="poststuff">
<?php optin_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) {
echo '<div class="updated"><p><strong>'.($_GET['action'] == 'reset' ? __('Options reset.', 'optin-manager') : __('Options and tables deleted.', 'optin-manager')).'</strong></p></div>
<script type="text/javascript">setTimeout(\'window.location = "'.($_GET['action'] == 'reset' ? 'admin.php?page=optin-manager' : 'plugins.php').'"\', 2000);</script>'; } ?>
<?php optin_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php if ($_GET['action'] == 'reset') { _e('Do you really want to reset the options of Optin Manager?', 'optin-manager'); }
else { _e('Do you really want to permanently delete the options and tables of Optin Manager?', 'optin-manager'); } ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'optin-manager'); ?>" />
</div>
</form><?php } ?>
</div>
</div><?php }

else {
include 'admin-pages.php';
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!optin_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'optin-manager'); }
else {
include 'initial-options.php';
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = stripslashes(html_entity_decode(str_replace('&nbsp;', ' ', $value))); } }
$back_office_options = update_optin_manager_back_office($back_office_options, 'options');

foreach (array(
'activation_confirmation_email_sent',
'activation_custom_instructions_executed',
'activation_notification_email_sent',
'affiliation_enabled',
'automatic_display_enabled',
'deactivation_custom_instructions_executed',
'commission2_enabled',
'prospect_subscribed_as_a_client',
'prospect_subscribed_as_a_user',
'prospect_subscribed_to_affiliate_program',
'prospect_subscribed_to_members_areas',
'prospects_registration_enabled',
'registration_confirmation_email_sent',
'registration_custom_instructions_executed',
'registration_notification_email_sent',
'removal_custom_instructions_executed') as $field) { if ($_POST[$field] != 'yes') { $_POST[$field] = 'no'; } }
foreach (array(
'automatic_display_form_id') as $field) { $_POST[$field] = (int) $_POST[$field]; if ($_POST[$field] < 1) { $_POST[$field] = $initial_options[''][$field]; } }
foreach (array(
'commission_amount',
'commission2_amount',
'encrypted_urls_validity_duration') as $field) { $_POST[$field] = str_replace(array('?', ',', ';'), '.', $_POST[$field]); }
switch ($_POST['maximum_prospects_quantity']) { case 0: case '': case 'i': case 'infinite': case 'u': $_POST['maximum_prospects_quantity'] = 'unlimited'; }
if (is_numeric($_POST['maximum_prospects_quantity'])) {
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."optin_manager_prospects", OBJECT);
$prospects_quantity = (int) $row->total;
$n = $prospects_quantity - $_POST['maximum_prospects_quantity'];
if ($n > 0) { $results = $wpdb->query("DELETE FROM ".$wpdb->prefix."optin_manager_prospects ORDER BY date ASC LIMIT $n"); } }
$members_areas = array_unique(preg_split('#[^0-9]#', $_POST['prospect_members_areas'], 0, PREG_SPLIT_NO_EMPTY));
sort($members_areas, SORT_NUMERIC);
foreach ($members_areas as $member_area) { $members_areas_list .= $member_area.', '; }
$_POST['prospect_members_areas'] = substr($members_areas_list, 0, -2);
foreach ($initial_options[''] as $key => $value) {
if ($_POST[$key] != '') { $options[$key] = $_POST[$key]; }
else { $options[$key] = $value; } }
update_option('optin_manager', $options);
foreach (array(
'activation_confirmation_email_body',
'activation_custom_instructions',
'activation_notification_email_body',
'code',
'deactivation_custom_instructions',
'registration_confirmation_email_body',
'registration_custom_instructions',
'registration_notification_email_body',
'removal_custom_instructions') as $field) {
if ($_POST[$field] == '') { $_POST[$field] = $initial_options[$field]; }
update_option('optin_manager_'.$field, $_POST[$field]); } } }
if (!isset($options)) { $options = (array) get_option('optin_manager'); }

foreach ($options as $key => $value) {
if (is_string($value)) { $options[$key] = htmlspecialchars($value); } }
$undisplayed_modules = (array) $back_office_options['options_page_undisplayed_modules'];
if (function_exists('commerce_data')) { $currency_code = commerce_data('currency_code'); }
else { $commerce_manager_options = (array) get_option('commerce_manager');
$currency_code = do_shortcode($commerce_manager_options['currency_code']); } ?>

<div class="wrap">
<div id="poststuff">
<?php optin_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>'; } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php optin_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<p class="description"><?php _e('You can reset an option by leaving the corresponding field blank.', 'optin-manager'); ?></p>
<?php optin_manager_pages_summary($back_office_options); ?>

<div class="postbox" id="autoresponders-module"<?php if (in_array('autoresponders', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="autoresponders"><strong><?php echo $modules['options']['autoresponders']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('You must make some adjustments so that the subscription works with some autoresponders.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#autoresponders"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="autoresponder"><?php _e('Autoresponder', 'optin-manager'); ?></label></strong></th>
<td><select name="autoresponder" id="autoresponder">
<?php include 'libraries/autoresponders.php';
$autoresponder = do_shortcode($options['autoresponder']);
foreach ($autoresponders as $value) {
echo '<option value="'.$value.'"'.($autoresponder == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="autoresponder_list"><?php _e('List', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="autoresponder_list" id="autoresponder_list" rows="1" cols="50"><?php echo $options['autoresponder_list']; ?></textarea><br />
<span class="description"><?php _e('For some autoresponders, you must enter the list ID.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#autoresponders"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
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
<td><span class="description"><a href="admin.php?page=commerce-manager#autoresponders-integration"><?php _e('Click here to configure the options of Commerce Manager.', 'optin-manager'); ?></a></span></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('You must make some adjustments so that the subscription works with some autoresponders.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#autoresponders"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
</tbody></table>
<div id="aweber-module"<?php if (in_array('aweber', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="aweber"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['aweber']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="http://www.kleor-editions.com/optin-manager/documentation/#aweber"><?php _e('Click here to read the instructions for integration.', 'optin-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="cybermailing-module"<?php if (in_array('cybermailing', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="cybermailing"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['cybermailing']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('You have no adjustment to make so that the subscription works with CyberMailing.', 'optin-manager'); ?></span></td></tr>
</tbody></table>
</div>
<div id="getresponse-module"<?php if (in_array('getresponse', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="getresponse"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['getresponse']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="getresponse_api_key"><?php _e('API key', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="getresponse_api_key" id="getresponse_api_key" rows="1" cols="50"><?php echo $options['getresponse_api_key']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/optin-manager/documentation/#getresponse"><?php _e('More informations', 'optin-manager'); ?></a>
<?php if (function_exists('commerce_manager_admin_menu')) { echo '<br />'.__('Leave this field blank to apply the Commerce Manager\'s option.', 'optin-manager'); } ?></span></td></tr>
</tbody></table>
</div>
<div id="mailchimp-module"<?php if (in_array('mailchimp', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="mailchimp"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['mailchimp']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="mailchimp_api_key"><?php _e('API key', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="mailchimp_api_key" id="mailchimp_api_key" rows="1" cols="50"><?php echo $options['mailchimp_api_key']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/optin-manager/documentation/#mailchimp"><?php _e('More informations', 'optin-manager'); ?></a>
<?php if (function_exists('commerce_manager_admin_menu')) { echo '<br />'.__('Leave this field blank to apply the Commerce Manager\'s option.', 'optin-manager'); } ?></span></td></tr>
</tbody></table>
</div>
<div id="sg-autorepondeur-module"<?php if (in_array('sg-autorepondeur', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="sg-autorepondeur"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['sg-autorepondeur']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="sg_autorepondeur_account_id"><?php _e('Account ID', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="sg_autorepondeur_account_id" id="sg_autorepondeur_account_id" rows="1" cols="25"><?php echo $options['sg_autorepondeur_account_id']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/optin-manager/documentation/#sg-autorepondeur"><?php _e('More informations', 'optin-manager'); ?></a>
<?php if (function_exists('commerce_manager_admin_menu')) { echo '<br />'.__('Leave this field blank to apply the Commerce Manager\'s option.', 'optin-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="sg_autorepondeur_activation_code"><?php _e('Activation code', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="sg_autorepondeur_activation_code" id="sg_autorepondeur_activation_code" rows="1" cols="50"><?php echo $options['sg_autorepondeur_activation_code']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/optin-manager/documentation/#sg-autorepondeur"><?php _e('More informations', 'optin-manager'); ?></a>
<?php if (function_exists('commerce_manager_admin_menu')) { echo '<br />'.__('Leave this field blank to apply the Commerce Manager\'s option.', 'optin-manager'); } ?></span></td></tr>
</tbody></table>
</div>
<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="automatic-display-module"<?php if (in_array('automatic-display', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="automatic-display"><strong><?php echo $modules['options']['automatic-display']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="automatic_display_enabled" id="automatic_display_enabled" value="yes"<?php if ($options['automatic_display_enabled'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Enable automatic display', 'optin-manager'); ?></label> 
<span class="description"><a href="http://www.kleor-editions.com/optin-manager/documentation/#automatic-display"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="automatic_display_location"><?php _e('Location', 'optin-manager'); ?></label></strong></th>
<td><select name="automatic_display_location" id="automatic_display_location">
<option value="top"<?php if ($options['automatic_display_location'] == 'top') { echo ' selected="selected"'; } ?>><?php _e('On the top of posts', 'optin-manager'); ?></option>
<option value="bottom"<?php if ($options['automatic_display_location'] == 'bottom') { echo ' selected="selected"'; } ?>><?php _e('On the bottom of posts', 'optin-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="automatic_display_form_id"><?php _e('Form ID', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="automatic_display_form_id" id="automatic_display_form_id" rows="1" cols="25"><?php echo $options['automatic_display_form_id']; ?></textarea><br />
<a style="text-decoration: none;" href="admin.php?page=optin-manager-form&amp;id=<?php echo $options['automatic_display_form_id']; ?>"><?php _e('Edit'); ?></a> | 
<a style="text-decoration: none;" href="admin.php?page=optin-manager-form&amp;id=<?php echo $options['automatic_display_form_id']; ?>&amp;action=delete"><?php _e('Delete'); ?></a></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="form-module"<?php if (in_array('form', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="form"><strong><?php echo $modules['options']['form']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="code"><?php _e('Code', 'optin-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="code" id="code" rows="15" cols="75"><?php echo htmlspecialchars(get_option('optin_manager_code')); ?></textarea>
<span class="description"><a href="http://www.kleor-editions.com/optin-manager/documentation/#forms"><?php _e('How to display a form?', 'optin-manager'); ?></a><br />
<a href="http://www.kleor-editions.com/optin-manager/documentation/#forms-creation"><?php _e('How to create a form?', 'optin-manager'); ?></a></span></td></tr>
</tbody></table>
<div id="captcha-module"<?php if (in_array('captcha', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="captcha"><strong><?php echo $modules['options']['form']['modules']['captcha']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="default_captcha_type"><?php _e('Default type', 'optin-manager'); ?></label></strong></th>
<td><select name="default_captcha_type" id="default_captcha_type">
<?php include 'libraries/captchas.php';
$captcha_type = do_shortcode($options['default_captcha_type']);
asort($captchas_types);
foreach ($captchas_types as $key => $value) {
echo '<option value="'.$key.'"'.($captcha_type == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select>
<span class="description"><a href="http://www.kleor-editions.com/optin-manager/documentation/#captcha"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="default_recaptcha_theme"><?php _e('Default reCAPTCHA theme', 'optin-manager'); ?></label></strong></th>
<td><select name="default_recaptcha_theme" id="default_recaptcha_theme">
<?php include 'libraries/captchas.php';
$recaptcha_theme = do_shortcode($options['default_recaptcha_theme']);
asort($recaptcha_themes);
foreach ($recaptcha_themes as $key => $value) {
echo '<option value="'.$key.'"'.($recaptcha_theme == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select>
<span class="description"><a href="http://www.kleor-editions.com/optin-manager/documentation/#recaptcha"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recaptcha_public_key"><?php _e('reCAPTCHA public key', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="recaptcha_public_key" id="recaptcha_public_key" rows="1" cols="50"><?php echo $options['recaptcha_public_key']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/optin-manager/documentation/#recaptcha"><?php _e('More informations', 'optin-manager'); ?></a>
<?php if (function_exists('commerce_manager_admin_menu')) { echo '<br />'.__('Leave this field blank to apply the Commerce Manager\'s option.', 'optin-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recaptcha_private_key"><?php _e('reCAPTCHA private key', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="recaptcha_private_key" id="recaptcha_private_key" rows="1" cols="50"><?php echo $options['recaptcha_private_key']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/optin-manager/documentation/#recaptcha"><?php _e('More informations', 'optin-manager'); ?></a>
<?php if (function_exists('commerce_manager_admin_menu')) { echo '<br />'.__('Leave this field blank to apply the Commerce Manager\'s option.', 'optin-manager'); } ?></span></td></tr>
</tbody></table>
</div>
<div id="error-messages-module"<?php if (in_array('error-messages', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="error-messages"><strong><?php echo $modules['options']['form']['modules']['error-messages']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="unfilled_fields_message"><?php _e('Unfilled required fields', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="unfilled_fields_message" id="unfilled_fields_message" rows="1" cols="75"><?php echo $options['unfilled_fields_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="unfilled_field_message"><?php _e('Unfilled required field', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="unfilled_field_message" id="unfilled_field_message" rows="1" cols="75"><?php echo $options['unfilled_field_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="invalid_email_address_message"><?php _e('Invalid email address', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="invalid_email_address_message" id="invalid_email_address_message" rows="1" cols="75"><?php echo $options['invalid_email_address_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="invalid_captcha_message"><?php _e('Invalid CAPTCHA', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="invalid_captcha_message" id="invalid_captcha_message" rows="1" cols="75"><?php echo $options['invalid_captcha_message']; ?></textarea></td></tr>
</tbody></table>
</div>
<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="registration-module"<?php if (in_array('registration', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration"><strong><?php echo $modules['options']['registration']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="prospects_registration_enabled" id="prospects_registration_enabled" value="yes"<?php if ($options['prospects_registration_enabled'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Save prospects in the database', 'optin-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="maximum_prospects_quantity"><?php _e('Maximum prospects quantity', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="maximum_prospects_quantity" id="maximum_prospects_quantity" rows="1" cols="25"><?php echo ($options['maximum_prospects_quantity'] == 'unlimited' ? '' : $options['maximum_prospects_quantity']); ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('You can save only the latest prospects to ease your database.', 'optin-manager'); ?><br />
<?php _e('Leave this field blank for an unlimited quantity.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_url"><?php _e('Registration confirmation URL', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_url" id="registration_confirmation_url" rows="1" cols="75"><?php echo $options['registration_confirmation_url']; ?></textarea> <a style="vertical-align: 25%;" href="<?php echo htmlspecialchars(format_url(do_shortcode($options['registration_confirmation_url']))); ?>"><?php _e('Link', 'optin-manager'); ?></a></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospects_initial_status"><?php _e('Prospects initial status', 'optin-manager'); ?></label></strong></th>
<td><select name="prospects_initial_status" id="prospects_initial_status">
<option value="active"<?php if ($options['prospects_initial_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'optin-manager'); ?></option>
<option value="inactive"<?php if ($options['prospects_initial_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'optin-manager'); ?></option>
</select>
<span class="description"><?php _e('Status assigned to prospects upon their registration', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#prospect-status"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="urls-encryption-module"<?php if (in_array('urls-encryption', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="urls-encryption"><strong><?php echo $modules['options']['urls-encryption']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('You can encrypt the download URLs.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#urls-encryption"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="encrypted_urls_validity_duration"><?php _e('Validity duration', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="encrypted_urls_validity_duration" id="encrypted_urls_validity_duration" rows="1" cols="25"><?php echo $options['encrypted_urls_validity_duration']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('hours', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="encrypted_urls_key"><?php _e('Encryption key', 'optin-manager'); ?></label></strong></th>
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
<td><label><input type="checkbox" name="registration_confirmation_email_sent" id="registration_confirmation_email_sent" value="yes"<?php if ($options['registration_confirmation_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration confirmation email', 'optin-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_sender"><?php _e('Sender', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_sender" id="registration_confirmation_email_sender" rows="1" cols="75"><?php echo $options['registration_confirmation_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_receiver"><?php _e('Receiver', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_receiver" id="registration_confirmation_email_receiver" rows="1" cols="75"><?php echo $options['registration_confirmation_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_subject"><?php _e('Subject', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_subject" id="registration_confirmation_email_subject" rows="1" cols="75"><?php echo $options['registration_confirmation_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_body"><?php _e('Body', 'optin-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_confirmation_email_body" id="registration_confirmation_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('optin_manager_registration_confirmation_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the prospect and the form.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#email-shortcodes"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="registration-notification-email-module"<?php if (in_array('registration-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration-notification-email"><strong><?php echo $modules['options']['registration-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="registration_notification_email_sent" id="registration_notification_email_sent" value="yes"<?php if ($options['registration_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration notification email', 'optin-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_sender"><?php _e('Sender', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_sender" id="registration_notification_email_sender" rows="1" cols="75"><?php echo $options['registration_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_receiver"><?php _e('Receiver', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_receiver" id="registration_notification_email_receiver" rows="1" cols="75"><?php echo $options['registration_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_subject"><?php _e('Subject', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_subject" id="registration_notification_email_subject" rows="1" cols="75"><?php echo $options['registration_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_body"><?php _e('Body', 'optin-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_notification_email_body" id="registration_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('optin_manager_registration_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the prospect and the form.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#email-shortcodes"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="registration-as-a-client-module"<?php if (in_array('registration-as-a-client', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration-as-a-client"><strong><?php echo $modules['options']['registration-as-a-client']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php echo (function_exists('commerce_manager_admin_menu') ? '<a href="admin.php?page=commerce-manager-clients-accounts">'.__('Click here to configure the options of Commerce Manager.', 'optin-manager').'</a>' : __('To subscribe your prospects as clients, you must have installed and activated <a href="http://www.kleor-editions.com/commerce-manager">Commerce Manager</a>.', 'optin-manager')); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="prospect_subscribed_as_a_client" id="prospect_subscribed_as_a_client" value="yes"<?php if ($options['prospect_subscribed_as_a_client'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the prospect as a client', 'optin-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/optin-manager/documentation/#registration-as-a-client"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."commerce_manager_clients_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_client_category_id"><?php _e('Category', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_client_category_id" id="prospect_client_category_id">
<option value=""<?php if ($options['prospect_client_category_id'] == '') { echo ' selected="selected"'; } ?>><?php _e('Commerce Manager\'s option', 'optin-manager'); ?></option>
<option value="0"<?php if ($options['prospect_client_category_id'] == '0') { echo ' selected="selected"'; } ?>><?php _e('None ', 'optin-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($options['prospect_client_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ((function_exists('commerce_manager_admin_menu')) && ($options['prospect_client_category_id'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-client-category&amp;id='.$options['prospect_client_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-client-category&amp;id='.$options['prospect_client_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_client_status"><?php _e('Status', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_client_status" id="prospect_client_status">
<option value=""<?php if ($options['prospect_client_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('Commerce Manager\'s option', 'optin-manager'); ?></option>
<option value="active"<?php if ($options['prospect_client_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'optin-manager'); ?></option>
<option value="inactive"<?php if ($options['prospect_client_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'optin-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#client-status"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commerce_registration_confirmation_email_sent"><?php _e('Send a registration confirmation email', 'optin-manager'); ?></label></strong></th>
<td><select name="commerce_registration_confirmation_email_sent" id="commerce_registration_confirmation_email_sent">
<option value=""<?php if ($options['commerce_registration_confirmation_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Commerce Manager\'s option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($options['commerce_registration_confirmation_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($options['commerce_registration_confirmation_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commerce_registration_notification_email_sent"><?php _e('Send a registration notification email', 'optin-manager'); ?></label></strong></th>
<td><select name="commerce_registration_notification_email_sent" id="commerce_registration_notification_email_sent">
<option value=""<?php if ($options['commerce_registration_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Commerce Manager\'s option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($options['commerce_registration_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($options['commerce_registration_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
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
<td><span class="description"><?php echo (function_exists('affiliation_manager_admin_menu') ? '<a href="admin.php?page=affiliation-manager">'.__('Click here to configure the options of Affiliation Manager.', 'optin-manager').'</a>' : __('To use affiliation, you must have installed and activated <a href="http://www.kleor-editions.com/affiliation-manager">Affiliation Manager</a>.', 'optin-manager')); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="prospect_subscribed_to_affiliate_program" id="prospect_subscribed_to_affiliate_program" value="yes"<?php if ($options['prospect_subscribed_to_affiliate_program'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the prospect to affiliate program', 'optin-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/optin-manager/documentation/#registration-to-affiliate-program"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."affiliation_manager_affiliates_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_affiliate_category_id"><?php _e('Category', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_affiliate_category_id" id="prospect_affiliate_category_id">
<option value=""<?php if ($options['prospect_affiliate_category_id'] == '') { echo ' selected="selected"'; } ?>><?php _e('Affiliation Manager\'s option', 'optin-manager'); ?></option>
<option value="0"<?php if ($options['prospect_affiliate_category_id'] == '0') { echo ' selected="selected"'; } ?>><?php _e('None ', 'optin-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($options['prospect_affiliate_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ((function_exists('affiliation_manager_admin_menu')) && ($options['prospect_affiliate_category_id'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$options['prospect_affiliate_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$options['prospect_affiliate_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_affiliate_status"><?php _e('Status', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_affiliate_status" id="prospect_affiliate_status">
<option value=""<?php if ($options['prospect_affiliate_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('Affiliation Manager\'s option', 'optin-manager'); ?></option>
<option value="active"<?php if ($options['prospect_affiliate_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'optin-manager'); ?></option>
<option value="inactive"<?php if ($options['prospect_affiliate_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'optin-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#affiliate-status"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliation_registration_confirmation_email_sent"><?php _e('Send a registration confirmation email', 'optin-manager'); ?></label></strong></th>
<td><select name="affiliation_registration_confirmation_email_sent" id="affiliation_registration_confirmation_email_sent">
<option value=""<?php if ($options['affiliation_registration_confirmation_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Affiliation Manager\'s option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($options['affiliation_registration_confirmation_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($options['affiliation_registration_confirmation_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliation_registration_notification_email_sent"><?php _e('Send a registration notification email', 'optin-manager'); ?></label></strong></th>
<td><select name="affiliation_registration_notification_email_sent" id="affiliation_registration_notification_email_sent">
<option value=""<?php if ($options['affiliation_registration_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Affiliation Manager\'s option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($options['affiliation_registration_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($options['affiliation_registration_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
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
<td><span class="description"><?php echo (function_exists('membership_manager_admin_menu') ? '<a href="admin.php?page=membership-manager">'.__('Click here to configure the options of Membership Manager.', 'optin-manager').'</a>' : __('To use membership, you must have installed and activated <a href="http://www.kleor-editions.com/membership-manager">Membership Manager</a>.', 'optin-manager')); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="prospect_subscribed_to_members_areas" id="prospect_subscribed_to_members_areas" value="yes"<?php if ($options['prospect_subscribed_to_members_areas'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the prospect to a member area', 'optin-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/optin-manager/documentation/#membership"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_members_areas"><?php _e('Members areas', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="prospect_members_areas" id="prospect_members_areas" rows="1" cols="50"><?php echo $options['prospect_members_areas']; ?></textarea>
<?php if ((function_exists('membership_manager_admin_menu')) && (is_numeric($options['prospect_members_areas'])) && ($options['prospect_members_areas'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-area&amp;id='.$options['prospect_members_areas'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-area&amp;id='.$options['prospect_members_areas'].'&amp;action=delete">'.__('Delete').'</a>'; } ?><br />
<span class="description"><?php _e('Separate the IDs of the members areas with commas.', 'optin-manager'); ?></span></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."membership_manager_members_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_member_category_id"><?php _e('Category', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_member_category_id" id="prospect_member_category_id">
<option value=""<?php if ($options['prospect_member_category_id'] == '') { echo ' selected="selected"'; } ?>><?php _e('Member area\'s option', 'optin-manager'); ?></option>
<option value="0"<?php if ($options['prospect_member_category_id'] == '0') { echo ' selected="selected"'; } ?>><?php _e('None ', 'optin-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($options['prospect_member_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ((function_exists('membership_manager_admin_menu')) && ($options['prospect_member_category_id'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-category&amp;id='.$options['prospect_member_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-category&amp;id='.$options['prospect_member_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_member_status"><?php _e('Status', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_member_status" id="prospect_member_status">
<option value=""<?php if ($options['prospect_member_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('Member area\'s option', 'optin-manager'); ?></option>
<option value="active"<?php if ($options['prospect_member_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'optin-manager'); ?></option>
<option value="inactive"<?php if ($options['prospect_member_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'optin-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#member-status"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="membership_registration_confirmation_email_sent"><?php _e('Send a registration confirmation email', 'optin-manager'); ?></label></strong></th>
<td><select name="membership_registration_confirmation_email_sent" id="membership_registration_confirmation_email_sent">
<option value=""<?php if ($options['membership_registration_confirmation_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Member area\'s option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($options['membership_registration_confirmation_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($options['membership_registration_confirmation_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="membership_registration_notification_email_sent"><?php _e('Send a registration notification email', 'optin-manager'); ?></label></strong></th>
<td><select name="membership_registration_notification_email_sent" id="membership_registration_notification_email_sent">
<option value=""<?php if ($options['membership_registration_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Member area\'s option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($options['membership_registration_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($options['membership_registration_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
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
<td><label><input type="checkbox" name="prospect_subscribed_as_a_user" id="prospect_subscribed_as_a_user" value="yes"<?php if ($options['prospect_subscribed_as_a_user'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the prospect as a user', 'optin-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/optin-manager/documentation/#wordpress"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_user_role"><?php _e('Role', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_user_role" id="prospect_user_role">
<?php foreach (optin_manager_users_roles() as $role => $name) {
echo '<option value="'.$role.'"'.($options['prospect_user_role'] == $role ? ' selected="selected"' : '').'>'.$name.'</option>'."\n"; } ?>
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
<td><label><input type="checkbox" name="registration_custom_instructions_executed" id="registration_custom_instructions_executed" value="yes"<?php if ($options['registration_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'optin-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_custom_instructions"><?php _e('PHP code', 'optin-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_custom_instructions" id="registration_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('optin_manager_registration_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the registration of a prospect.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#custom-instructions"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="activation-custom-instructions-module"<?php if (in_array('activation-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="activation-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['activation-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="activation_custom_instructions_executed" id="activation_custom_instructions_executed" value="yes"<?php if ($options['activation_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'optin-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_custom_instructions"><?php _e('PHP code', 'optin-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="activation_custom_instructions" id="activation_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('optin_manager_activation_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the activation of a prospect.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#custom-instructions"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="deactivation-custom-instructions-module"<?php if (in_array('deactivation-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="deactivation-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['deactivation-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="deactivation_custom_instructions_executed" id="deactivation_custom_instructions_executed" value="yes"<?php if ($options['deactivation_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'optin-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_custom_instructions"><?php _e('PHP code', 'optin-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="deactivation_custom_instructions" id="deactivation_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('optin_manager_deactivation_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the deactivation of a prospect.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#custom-instructions"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="removal-custom-instructions-module"<?php if (in_array('removal-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="removal-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['removal-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="removal_custom_instructions_executed" id="removal_custom_instructions_executed" value="yes"<?php if ($options['removal_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'optin-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_custom_instructions"><?php _e('PHP code', 'optin-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="removal_custom_instructions" id="removal_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('optin_manager_removal_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the removal of a prospect.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#custom-instructions"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="activation-confirmation-email-module"<?php if (in_array('activation-confirmation-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="activation-confirmation-email"><strong><?php echo $modules['options']['activation-confirmation-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="activation_confirmation_email_sent" id="activation_confirmation_email_sent" value="yes"<?php if ($options['activation_confirmation_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send an activation confirmation email', 'optin-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_confirmation_email_sender"><?php _e('Sender', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_confirmation_email_sender" id="activation_confirmation_email_sender" rows="1" cols="75"><?php echo $options['activation_confirmation_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_confirmation_email_receiver"><?php _e('Receiver', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_confirmation_email_receiver" id="activation_confirmation_email_receiver" rows="1" cols="75"><?php echo $options['activation_confirmation_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_confirmation_email_subject"><?php _e('Subject', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_confirmation_email_subject" id="activation_confirmation_email_subject" rows="1" cols="75"><?php echo $options['activation_confirmation_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_confirmation_email_body"><?php _e('Body', 'optin-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="activation_confirmation_email_body" id="activation_confirmation_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('optin_manager_activation_confirmation_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the prospect and the form.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#email-shortcodes"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="activation-notification-email-module"<?php if (in_array('activation-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="activation-notification-email"><strong><?php echo $modules['options']['activation-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="activation_notification_email_sent" id="activation_notification_email_sent" value="yes"<?php if ($options['activation_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send an activation notification email', 'optin-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_sender"><?php _e('Sender', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_sender" id="activation_notification_email_sender" rows="1" cols="75"><?php echo $options['activation_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_receiver"><?php _e('Receiver', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_receiver" id="activation_notification_email_receiver" rows="1" cols="75"><?php echo $options['activation_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_subject"><?php _e('Subject', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_subject" id="activation_notification_email_subject" rows="1" cols="75"><?php echo $options['activation_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_body"><?php _e('Body', 'optin-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="activation_notification_email_body" id="activation_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('optin_manager_activation_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the prospect and the form.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#email-shortcodes"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="affiliation-module"<?php if (in_array('affiliation', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="affiliation"><strong><?php echo $modules['options']['affiliation']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php if (function_exists('affiliation_manager_admin_menu')) { _e('You can award a commission to the affiliate who referred a prospect.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#affiliation"><?php _e('More informations', 'optin-manager'); ?></a><?php }
else { _e('To use affiliation, you must have installed and activated <a href="http://www.kleor-editions.com/affiliation-manager">Affiliation Manager</a>.', 'optin-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="affiliation_enabled" id="affiliation_enabled" value="yes"<?php if ($options['affiliation_enabled'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Use affiliation', 'optin-manager'); ?></label></td></tr>
</tbody></table>
<div id="level-1-commission-module"<?php if (in_array('level-1-commission', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="level-1-commission"><strong><?php echo $modules['options']['affiliation']['modules']['level-1-commission']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('The level 1 commission is awarded to the affiliate who referred the prospect.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_amount"><?php _e('Amount', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_amount" id="commission_amount" rows="1" cols="25"><?php echo $options['commission_amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span></td></tr>
</tbody></table>
</div>
<div id="level-2-commission-module"<?php if (in_array('level-2-commission', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="level-2-commission"><strong><?php echo $modules['options']['affiliation']['modules']['level-2-commission']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('The level 2 commission is awarded to the referrer of the affiliate who referred the prospect.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="commission2_enabled" id="commission2_enabled" value="yes"<?php if ($options['commission2_enabled'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Award a level 2 commission', 'optin-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_amount"><?php _e('Amount', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission2_amount" id="commission2_amount" rows="1" cols="25"><?php echo $options['commission2_amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span></td></tr>
</tbody></table>
</div>
<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes'); ?>" /></p>
<?php optin_manager_pages_module($back_office_options, 'options-page', $undisplayed_modules); ?>
</form>
</div>
</div>
<?php }