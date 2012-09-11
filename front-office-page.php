<?php $back_office_options = get_option('membership_manager_back_office');
include 'admin-pages.php';
$groups = array(
'login_compact_form',
'login_form',
'password_reset_form',
'profile_form',
'registration_compact_form',
'registration_form');
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!membership_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'membership-manager'); }
else {
include 'initial-options.php';
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = stripslashes(html_entity_decode(str_replace('&nbsp;', ' ', $value))); } }
$back_office_options = update_membership_manager_back_office($back_office_options, 'front_office');

foreach ($groups as $group) {
foreach ($initial_options[$group] as $key => $value) {
if ($_POST[$group.'_'.$key] != '') { $options[$group][$key] = $_POST[$group.'_'.$key]; }
else { $options[$group][$key] = $value; } }
update_option('membership_manager_'.$group, $options[$group]); }
foreach (array(
'login_compact_form_code',
'login_form_code',
'password_reset_form_code',
'profile_form_code',
'registration_compact_form_code',
'registration_form_code') as $field) {
if ($_POST[$field] == '') { $_POST[$field] = $initial_options[$field]; }
update_option('membership_manager_'.$field, $_POST[$field]); } } }
if (!isset($options)) { foreach ($groups as $group) { $options[$group] = (array) get_option('membership_manager_'.$group); } }

foreach ($groups as $group) {
foreach ($options[$group] as $key => $value) {
if (is_string($value)) { $options[$group][$key] = htmlspecialchars($value); } } }
$undisplayed_modules = (array) $back_office_options['front_office_page_undisplayed_modules']; ?>

<div class="wrap">
<div id="poststuff">
<?php membership_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>'; } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php membership_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<p class="description"><?php _e('You can reset an option by leaving the corresponding field blank.', 'membership-manager'); ?></p>
<?php membership_manager_pages_summary($back_office_options); ?>

<div class="postbox" id="registration-form-module"<?php if (in_array('registration-form', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration-form"><strong><?php echo $modules['front_office']['registration-form']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_form_code"><?php _e('Code', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_form_code" id="registration_form_code" rows="15" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_registration_form_code')); ?></textarea>
<span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#forms"><?php _e('How to display this form?', 'membership-manager'); ?></a><br />
<a href="http://www.kleor-editions.com/membership-manager/documentation/#forms-creation"><?php _e('How to personalize this form?', 'membership-manager'); ?></a></span></td></tr>
</tbody></table>
<div id="registration-error-messages-module"<?php if (in_array('registration-error-messages', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="registration-error-messages"><strong><?php echo $modules['front_office']['registration-form']['modules']['registration-error-messages']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=membership-manager#error-messages"><?php _e('Click here to configure the default options.', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_form_unfilled_fields_message"><?php _e('Unfilled required fields', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_form_unfilled_fields_message" id="registration_form_unfilled_fields_message" rows="1" cols="75"><?php echo $options['registration_form']['unfilled_fields_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_form_unfilled_field_message"><?php _e('Unfilled required field', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_form_unfilled_field_message" id="registration_form_unfilled_field_message" rows="1" cols="75"><?php echo $options['registration_form']['unfilled_field_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_form_invalid_email_address_message"><?php _e('Invalid email address', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_form_invalid_email_address_message" id="registration_form_invalid_email_address_message" rows="1" cols="75"><?php echo $options['registration_form']['invalid_email_address_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_form_numeric_login_message"><?php _e('Numeric login name', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_form_numeric_login_message" id="registration_form_numeric_login_message" rows="1" cols="75"><?php echo $options['registration_form']['numeric_login_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_form_too_short_login_message"><?php _e('Too short login name', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_form_too_short_login_message" id="registration_form_too_short_login_message" rows="1" cols="75"><?php echo $options['registration_form']['too_short_login_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_form_too_long_login_message"><?php _e('Too long login name', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_form_too_long_login_message" id="registration_form_too_long_login_message" rows="1" cols="75"><?php echo $options['registration_form']['too_long_login_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_form_too_short_password_message"><?php _e('Too short password', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_form_too_short_password_message" id="registration_form_too_short_password_message" rows="1" cols="75"><?php echo $options['registration_form']['too_short_password_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_form_too_long_password_message"><?php _e('Too long password', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_form_too_long_password_message" id="registration_form_too_long_password_message" rows="1" cols="75"><?php echo $options['registration_form']['too_long_password_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_form_unavailable_login_message"><?php _e('Unavailable login name', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_form_unavailable_login_message" id="registration_form_unavailable_login_message" rows="1" cols="75"><?php echo $options['registration_form']['unavailable_login_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_form_unavailable_email_address_message"><?php _e('Unavailable email address', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_form_unavailable_email_address_message" id="registration_form_unavailable_email_address_message" rows="1" cols="75"><?php echo $options['registration_form']['unavailable_email_address_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_form_invalid_captcha_message"><?php _e('Invalid CAPTCHA', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_form_invalid_captcha_message" id="registration_form_invalid_captcha_message" rows="1" cols="75"><?php echo $options['registration_form']['invalid_captcha_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
</tbody></table>
</div>
<div id="registration-login-availability-indicator-module"<?php if (in_array('registration-login-availability-indicator', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="registration-login-availability-indicator"><strong><?php echo $modules['front_office']['registration-form']['modules']['registration-login-availability-indicator']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=membership-manager#login-availability-indicator"><?php _e('Click here to configure the default options.', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_form_available_login_indicator_message"><?php _e('Available', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_form_available_login_indicator_message" id="registration_form_available_login_indicator_message" rows="1" cols="75"><?php echo $options['registration_form']['available_login_indicator_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_form_unavailable_login_indicator_message"><?php _e('Unavailable', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_form_unavailable_login_indicator_message" id="registration_form_unavailable_login_indicator_message" rows="1" cols="75"><?php echo $options['registration_form']['unavailable_login_indicator_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
</tbody></table>
</div>
<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="registration-compact-form-module"<?php if (in_array('registration-compact-form', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration-compact-form"><strong><?php echo $modules['front_office']['registration-compact-form']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_compact_form_code"><?php _e('Code', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_compact_form_code" id="registration_compact_form_code" rows="15" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_registration_compact_form_code')); ?></textarea>
<span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#forms"><?php _e('How to display this form?', 'membership-manager'); ?></a><br />
<a href="http://www.kleor-editions.com/membership-manager/documentation/#forms-creation"><?php _e('How to personalize this form?', 'membership-manager'); ?></a></span></td></tr>
</tbody></table>
<div id="registration-compact-error-messages-module"<?php if (in_array('registration-compact-error-messages', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="registration-compact-error-messages"><strong><?php echo $modules['front_office']['registration-compact-form']['modules']['registration-compact-error-messages']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=membership-manager#error-messages"><?php _e('Click here to configure the default options.', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_compact_form_unfilled_fields_message"><?php _e('Unfilled required fields', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_compact_form_unfilled_fields_message" id="registration_compact_form_unfilled_fields_message" rows="1" cols="75"><?php echo $options['registration_compact_form']['unfilled_fields_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_compact_form_unfilled_field_message"><?php _e('Unfilled required field', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_compact_form_unfilled_field_message" id="registration_compact_form_unfilled_field_message" rows="1" cols="75"><?php echo $options['registration_compact_form']['unfilled_field_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_compact_form_invalid_email_address_message"><?php _e('Invalid email address', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_compact_form_invalid_email_address_message" id="registration_compact_form_invalid_email_address_message" rows="1" cols="75"><?php echo $options['registration_compact_form']['invalid_email_address_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_compact_form_numeric_login_message"><?php _e('Numeric login name', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_compact_form_numeric_login_message" id="registration_compact_form_numeric_login_message" rows="1" cols="75"><?php echo $options['registration_compact_form']['numeric_login_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_compact_form_too_short_login_message"><?php _e('Too short login name', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_compact_form_too_short_login_message" id="registration_compact_form_too_short_login_message" rows="1" cols="75"><?php echo $options['registration_compact_form']['too_short_login_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_compact_form_too_long_login_message"><?php _e('Too long login name', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_compact_form_too_long_login_message" id="registration_compact_form_too_long_login_message" rows="1" cols="75"><?php echo $options['registration_compact_form']['too_long_login_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_compact_form_too_short_password_message"><?php _e('Too short password', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_compact_form_too_short_password_message" id="registration_compact_form_too_short_password_message" rows="1" cols="75"><?php echo $options['registration_compact_form']['too_short_password_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_compact_form_too_long_password_message"><?php _e('Too long password', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_compact_form_too_long_password_message" id="registration_compact_form_too_long_password_message" rows="1" cols="75"><?php echo $options['registration_compact_form']['too_long_password_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_compact_form_unavailable_login_message"><?php _e('Unavailable login name', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_compact_form_unavailable_login_message" id="registration_compact_form_unavailable_login_message" rows="1" cols="75"><?php echo $options['registration_compact_form']['unavailable_login_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_compact_form_unavailable_email_address_message"><?php _e('Unavailable email address', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_compact_form_unavailable_email_address_message" id="registration_compact_form_unavailable_email_address_message" rows="1" cols="75"><?php echo $options['registration_compact_form']['unavailable_email_address_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_compact_form_invalid_captcha_message"><?php _e('Invalid CAPTCHA', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_compact_form_invalid_captcha_message" id="registration_compact_form_invalid_captcha_message" rows="1" cols="75"><?php echo $options['registration_compact_form']['invalid_captcha_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
</tbody></table>
</div>
<div id="registration-compact-login-availability-indicator-module"<?php if (in_array('registration-compact-login-availability-indicator', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="registration-compact-login-availability-indicator"><strong><?php echo $modules['front_office']['registration-compact-form']['modules']['registration-compact-login-availability-indicator']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=membership-manager#login-availability-indicator"><?php _e('Click here to configure the default options.', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_compact_form_available_login_indicator_message"><?php _e('Available', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_compact_form_available_login_indicator_message" id="registration_compact_form_available_login_indicator_message" rows="1" cols="75"><?php echo $options['registration_compact_form']['available_login_indicator_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_compact_form_unavailable_login_indicator_message"><?php _e('Unavailable', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_compact_form_unavailable_login_indicator_message" id="registration_compact_form_unavailable_login_indicator_message" rows="1" cols="75"><?php echo $options['registration_compact_form']['unavailable_login_indicator_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
</tbody></table>
</div>
<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="login-form-module"<?php if (in_array('login-form', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="login-form"><strong><?php echo $modules['front_office']['login-form']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="login_form_code"><?php _e('Code', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="login_form_code" id="login_form_code" rows="15" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_login_form_code')); ?></textarea>
<span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#forms"><?php _e('How to display this form?', 'membership-manager'); ?></a><br />
<a href="http://www.kleor-editions.com/membership-manager/documentation/#forms-creation"><?php _e('How to personalize this form?', 'membership-manager'); ?></a></span></td></tr>
</tbody></table>
<div id="login-error-messages-module"<?php if (in_array('login-error-messages', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="login-error-messages"><strong><?php echo $modules['front_office']['login-form']['modules']['login-error-messages']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=membership-manager#error-messages"><?php _e('Click here to configure the default options.', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="login_form_unfilled_field_message"><?php _e('Unfilled required field', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="login_form_unfilled_field_message" id="login_form_unfilled_field_message" rows="1" cols="75"><?php echo $options['login_form']['unfilled_field_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="login_form_invalid_login_or_password_message"><?php _e('Invalid login or password', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="login_form_invalid_login_or_password_message" id="login_form_invalid_login_or_password_message" rows="1" cols="75"><?php echo $options['login_form']['invalid_login_or_password_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="login_form_inactive_account_message"><?php _e('Inactive account', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="login_form_inactive_account_message" id="login_form_inactive_account_message" rows="1" cols="75"><?php echo $options['login_form']['inactive_account_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="login_form_invalid_captcha_message"><?php _e('Invalid CAPTCHA', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="login_form_invalid_captcha_message" id="login_form_invalid_captcha_message" rows="1" cols="75"><?php echo $options['login_form']['invalid_captcha_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
</tbody></table>
</div>
<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="login-compact-form-module"<?php if (in_array('login-compact-form', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="login-compact-form"><strong><?php echo $modules['front_office']['login-compact-form']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="login_compact_form_code"><?php _e('Code', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="login_compact_form_code" id="login_compact_form_code" rows="15" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_login_compact_form_code')); ?></textarea>
<span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#forms"><?php _e('How to display this form?', 'membership-manager'); ?></a><br />
<a href="http://www.kleor-editions.com/membership-manager/documentation/#forms-creation"><?php _e('How to personalize this form?', 'membership-manager'); ?></a></span></td></tr>
</tbody></table>
<div id="login-compact-error-messages-module"<?php if (in_array('login-compact-error-messages', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="login-compact-error-messages"><strong><?php echo $modules['front_office']['login-compact-form']['modules']['login-compact-error-messages']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=membership-manager#error-messages"><?php _e('Click here to configure the default options.', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="login_compact_form_unfilled_field_message"><?php _e('Unfilled required field', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="login_compact_form_unfilled_field_message" id="login_compact_form_unfilled_field_message" rows="1" cols="75"><?php echo $options['login_compact_form']['unfilled_field_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="login_compact_form_invalid_login_or_password_message"><?php _e('Invalid login or password', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="login_compact_form_invalid_login_or_password_message" id="login_compact_form_invalid_login_or_password_message" rows="1" cols="75"><?php echo $options['login_compact_form']['invalid_login_or_password_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="login_compact_form_inactive_account_message"><?php _e('Inactive account', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="login_compact_form_inactive_account_message" id="login_compact_form_inactive_account_message" rows="1" cols="75"><?php echo $options['login_compact_form']['inactive_account_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="login_compact_form_invalid_captcha_message"><?php _e('Invalid CAPTCHA', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="login_compact_form_invalid_captcha_message" id="login_compact_form_invalid_captcha_message" rows="1" cols="75"><?php echo $options['login_compact_form']['invalid_captcha_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
</tbody></table>
</div>
<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="password-reset-form-module"<?php if (in_array('password-reset-form', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="password-reset-form"><strong><?php echo $modules['front_office']['password-reset-form']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_form_code"><?php _e('Code', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="password_reset_form_code" id="password_reset_form_code" rows="15" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_password_reset_form_code')); ?></textarea>
<span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#forms"><?php _e('How to display this form?', 'membership-manager'); ?></a><br />
<a href="http://www.kleor-editions.com/membership-manager/documentation/#forms-creation"><?php _e('How to personalize this form?', 'membership-manager'); ?></a></span></td></tr>
</tbody></table>
<div id="password-reset-error-messages-module"<?php if (in_array('password-reset-error-messages', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="password-reset-error-messages"><strong><?php echo $modules['front_office']['password-reset-form']['modules']['password-reset-error-messages']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=membership-manager#error-messages"><?php _e('Click here to configure the default options.', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_form_unfilled_field_message"><?php _e('Unfilled required field', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_form_unfilled_field_message" id="password_reset_form_unfilled_field_message" rows="1" cols="75"><?php echo $options['password_reset_form']['unfilled_field_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_form_invalid_email_address_message"><?php _e('Invalid email address', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_form_invalid_email_address_message" id="password_reset_form_invalid_email_address_message" rows="1" cols="75"><?php echo $options['password_reset_form']['invalid_email_address_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_form_inexistent_email_address_message"><?php _e('Inexistent email address', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_form_inexistent_email_address_message" id="password_reset_form_inexistent_email_address_message" rows="1" cols="75"><?php echo $options['password_reset_form']['inexistent_email_address_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_form_invalid_captcha_message"><?php _e('Invalid CAPTCHA', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_form_invalid_captcha_message" id="password_reset_form_invalid_captcha_message" rows="1" cols="75"><?php echo $options['password_reset_form']['invalid_captcha_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
</tbody></table>
</div>
<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="profile-form-module"<?php if (in_array('profile-form', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="profile-form"><strong><?php echo $modules['front_office']['profile-form']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_form_code"><?php _e('Code', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="profile_form_code" id="profile_form_code" rows="15" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_profile_form_code')); ?></textarea>
<span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#forms"><?php _e('How to display this form?', 'membership-manager'); ?></a><br />
<a href="http://www.kleor-editions.com/membership-manager/documentation/#forms-creation"><?php _e('How to personalize this form?', 'membership-manager'); ?></a></span></td></tr>
</tbody></table>
<div id="profile-error-messages-module"<?php if (in_array('profile-error-messages', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="profile-error-messages"><strong><?php echo $modules['front_office']['profile-form']['modules']['profile-error-messages']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=membership-manager#error-messages"><?php _e('Click here to configure the default options.', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_form_unfilled_fields_message"><?php _e('Unfilled required fields', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="profile_form_unfilled_fields_message" id="profile_form_unfilled_fields_message" rows="1" cols="75"><?php echo $options['profile_form']['unfilled_fields_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_form_unfilled_field_message"><?php _e('Unfilled required field', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="profile_form_unfilled_field_message" id="profile_form_unfilled_field_message" rows="1" cols="75"><?php echo $options['profile_form']['unfilled_field_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_form_invalid_email_address_message"><?php _e('Invalid email address', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="profile_form_invalid_email_address_message" id="profile_form_invalid_email_address_message" rows="1" cols="75"><?php echo $options['profile_form']['invalid_email_address_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_form_numeric_login_message"><?php _e('Numeric login name', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="profile_form_numeric_login_message" id="profile_form_numeric_login_message" rows="1" cols="75"><?php echo $options['profile_form']['numeric_login_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_form_too_short_login_message"><?php _e('Too short login name', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="profile_form_too_short_login_message" id="profile_form_too_short_login_message" rows="1" cols="75"><?php echo $options['profile_form']['too_short_login_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_form_too_long_login_message"><?php _e('Too long login name', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="profile_form_too_long_login_message" id="profile_form_too_long_login_message" rows="1" cols="75"><?php echo $options['profile_form']['too_long_login_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_form_too_short_password_message"><?php _e('Too short password', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="profile_form_too_short_password_message" id="profile_form_too_short_password_message" rows="1" cols="75"><?php echo $options['profile_form']['too_short_password_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_form_too_long_password_message"><?php _e('Too long password', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="profile_form_too_long_password_message" id="profile_form_too_long_password_message" rows="1" cols="75"><?php echo $options['profile_form']['too_long_password_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_form_unavailable_login_message"><?php _e('Unavailable login name', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="profile_form_unavailable_login_message" id="profile_form_unavailable_login_message" rows="1" cols="75"><?php echo $options['profile_form']['unavailable_login_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_form_unavailable_email_address_message"><?php _e('Unavailable email address', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="profile_form_unavailable_email_address_message" id="profile_form_unavailable_email_address_message" rows="1" cols="75"><?php echo $options['profile_form']['unavailable_email_address_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_form_invalid_captcha_message"><?php _e('Invalid CAPTCHA', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="profile_form_invalid_captcha_message" id="profile_form_invalid_captcha_message" rows="1" cols="75"><?php echo $options['profile_form']['invalid_captcha_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
</tbody></table>
</div>
<div id="profile-login-availability-indicator-module"<?php if (in_array('profile-login-availability-indicator', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="profile-login-availability-indicator"><strong><?php echo $modules['front_office']['profile-form']['modules']['profile-login-availability-indicator']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=membership-manager#login-availability-indicator"><?php _e('Click here to configure the default options.', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_form_available_login_indicator_message"><?php _e('Available', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="profile_form_available_login_indicator_message" id="profile_form_available_login_indicator_message" rows="1" cols="75"><?php echo $options['profile_form']['available_login_indicator_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_form_unavailable_login_indicator_message"><?php _e('Unavailable', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="profile_form_unavailable_login_indicator_message" id="profile_form_unavailable_login_indicator_message" rows="1" cols="75"><?php echo $options['profile_form']['unavailable_login_indicator_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
</tbody></table>
</div>
<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes'); ?>" /></p>
<?php membership_manager_pages_module($back_office_options, 'front-office-page', $undisplayed_modules); ?>
</form>
</div>
</div>