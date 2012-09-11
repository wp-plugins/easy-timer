<?php global $wpdb;
$back_office_options = get_option('membership_manager_back_office');

if (($_GET['action'] == 'reset') || ($_GET['action'] == 'uninstall')) {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!membership_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'membership-manager'); }
else { if ($_GET['action'] == 'reset') { reset_membership_manager(); } else { uninstall_membership_manager(); } } } ?>
<div class="wrap">
<div id="poststuff">
<?php membership_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) {
echo '<div class="updated"><p><strong>'.($_GET['action'] == 'reset' ? __('Options reset.', 'membership-manager') : __('Options and tables deleted.', 'membership-manager')).'</strong></p></div>
<script type="text/javascript">setTimeout(\'window.location = "'.($_GET['action'] == 'reset' ? 'admin.php?page=membership-manager' : 'plugins.php').'"\', 2000);</script>'; } ?>
<?php membership_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php if ($_GET['action'] == 'reset') { _e('Do you really want to reset the options of Membership Manager?', 'membership-manager'); }
else { _e('Do you really want to permanently delete the options and tables of Membership Manager?', 'membership-manager'); } ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'membership-manager'); ?>" />
</div>
</form><?php } ?>
</div>
</div><?php }

else {
include 'admin-pages.php';
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!membership_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'membership-manager'); }
else {
include 'initial-options.php';
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = stripslashes(html_entity_decode(str_replace('&nbsp;', ' ', $value))); } }
$back_office_options = update_membership_manager_back_office($back_office_options, 'options');

foreach (array(
'activation_confirmation_email_sent',
'activation_custom_instructions_executed',
'deactivation_custom_instructions_executed',
'login_custom_instructions_executed',
'login_notification_email_sent',
'logout_custom_instructions_executed',
'logout_notification_email_sent',
'member_subscribed_as_a_client',
'member_subscribed_as_a_user',
'member_subscribed_to_affiliate_program',
'member_subscribed_to_autoresponder',
'password_reset_custom_instructions_executed',
'password_reset_notification_email_sent',
'profile_edit_custom_instructions_executed',
'profile_edit_notification_email_sent',
'registration_confirmation_email_sent',
'registration_custom_instructions_executed',
'registration_notification_email_sent',
'removal_custom_instructions_executed') as $field) { if ($_POST[$field] != 'yes') { $_POST[$field] = 'no'; } }
foreach (array(
'maximum_login_length',
'maximum_password_length',
'minimum_login_length',
'minimum_password_length') as $field) { $_POST[$field] = (int) $_POST[$field]; }
foreach (array(
'encrypted_urls_validity_duration') as $field) { $_POST[$field] = str_replace(array('?', ',', ';'), '.', $_POST[$field]); }
if ($_POST['maximum_login_length'] < 2) { $_POST['maximum_login_length'] = $initial_options['']['maximum_login_length']; }
if ($_POST['maximum_password_length'] < 8) { $_POST['maximum_password_length'] = $initial_options['']['maximum_password_length']; }
if ($_POST['minimum_login_length'] < 1) { $_POST['minimum_login_length'] = $initial_options['']['minimum_login_length']; }
if ($_POST['minimum_login_length'] > $_POST['maximum_login_length']) { $_POST['minimum_login_length'] = $_POST['maximum_login_length']; }
if ($_POST['minimum_password_length'] < 1) { $_POST['minimum_password_length'] = $initial_options['']['minimum_password_length']; }
if ($_POST['minimum_password_length'] > $_POST['maximum_password_length']) { $_POST['minimum_password_length'] = $_POST['maximum_password_length']; }
foreach ($initial_options[''] as $key => $value) {
if ($_POST[$key] != '') { $options[$key] = $_POST[$key]; }
else { $options[$key] = $value; } }
update_option('membership_manager', $options);
foreach (array(
'activation_confirmation_email_body',
'activation_custom_instructions',
'activation_notification_email_body',
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
update_option('membership_manager_'.$field, $_POST[$field]); } } }
if (!isset($options)) { $options = (array) get_option('membership_manager'); }

foreach ($options as $key => $value) {
if (is_string($value)) { $options[$key] = htmlspecialchars($value); } }
$undisplayed_modules = (array) $back_office_options['options_page_undisplayed_modules']; ?>

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

<div class="postbox" id="registration-module"<?php if (in_array('registration', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration"><strong><?php echo $modules['options']['registration']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="minimum_login_length"><?php _e('Minimum login length', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="minimum_login_length" id="minimum_login_length" rows="1" cols="25"><?php echo $options['minimum_login_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="maximum_login_length"><?php _e('Maximum login length', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="maximum_login_length" id="maximum_login_length" rows="1" cols="25"><?php echo $options['maximum_login_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="minimum_password_length"><?php _e('Minimum password length', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="minimum_password_length" id="minimum_password_length" rows="1" cols="25"><?php echo $options['minimum_password_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="maximum_password_length"><?php _e('Maximum password length', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="maximum_password_length" id="maximum_password_length" rows="1" cols="25"><?php echo $options['maximum_password_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_url"><?php _e('Registration confirmation URL', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_url" id="registration_confirmation_url" rows="1" cols="75"><?php echo $options['registration_confirmation_url']; ?></textarea> <a style="vertical-align: 25%;" href="<?php echo htmlspecialchars(format_url(do_shortcode($options['registration_confirmation_url']))); ?>"><?php _e('Link', 'membership-manager'); ?></a></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."membership_manager_members_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="members_initial_category_id"><?php _e('Members initial category', 'membership-manager'); ?></label></strong></th>
<td><select name="members_initial_category_id" id="members_initial_category_id">
<option value="0"<?php if ($options['members_initial_category_id'] == '0') { echo ' selected="selected"'; } ?>><?php _e('None ', 'membership-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($options['members_initial_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<span class="description"><?php _e('Category assigned to members upon their registration', 'membership-manager'); ?></span>
<?php if ($options['members_initial_category_id'] > 0) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-category&amp;id='.$options['members_initial_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-category&amp;id='.$options['members_initial_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="members_initial_status"><?php _e('Members initial status', 'membership-manager'); ?></label></strong></th>
<td><select name="members_initial_status" id="members_initial_status">
<option value="active"<?php if ($options['members_initial_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'membership-manager'); ?></option>
<option value="inactive"<?php if ($options['members_initial_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'membership-manager'); ?></option>
</select>
<span class="description"><?php _e('Status assigned to members upon their registration', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#member-status"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_confirmation_url"><?php _e('Activation confirmation URL', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_confirmation_url" id="activation_confirmation_url" rows="1" cols="75"><?php echo $options['activation_confirmation_url']; ?></textarea> <a style="vertical-align: 25%;" href="<?php echo htmlspecialchars(format_url(do_shortcode($options['activation_confirmation_url']))); ?>"><?php _e('Link', 'membership-manager'); ?></a></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="urls-encryption-module"<?php if (in_array('urls-encryption', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="urls-encryption"><strong><?php echo $modules['options']['urls-encryption']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('You can encrypt the URLs.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#urls-encryption"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="encrypted_urls_validity_duration"><?php _e('Validity duration', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="encrypted_urls_validity_duration" id="encrypted_urls_validity_duration" rows="1" cols="25"><?php echo $options['encrypted_urls_validity_duration']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('hours', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="encrypted_urls_key"><?php _e('Encryption key', 'membership-manager'); ?></label></strong></th>
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
<td><label><input type="checkbox" name="registration_confirmation_email_sent" id="registration_confirmation_email_sent" value="yes"<?php if ($options['registration_confirmation_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration confirmation email', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_sender" id="registration_confirmation_email_sender" rows="1" cols="75"><?php echo $options['registration_confirmation_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_receiver" id="registration_confirmation_email_receiver" rows="1" cols="75"><?php echo $options['registration_confirmation_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_subject" id="registration_confirmation_email_subject" rows="1" cols="75"><?php echo $options['registration_confirmation_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_confirmation_email_body" id="registration_confirmation_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_registration_confirmation_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the member and the member area.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#email-shortcodes"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="registration-notification-email-module"<?php if (in_array('registration-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration-notification-email"><strong><?php echo $modules['options']['registration-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="registration_notification_email_sent" id="registration_notification_email_sent" value="yes"<?php if ($options['registration_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration notification email', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_sender" id="registration_notification_email_sender" rows="1" cols="75"><?php echo $options['registration_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_receiver" id="registration_notification_email_receiver" rows="1" cols="75"><?php echo $options['registration_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_subject" id="registration_notification_email_subject" rows="1" cols="75"><?php echo $options['registration_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_notification_email_body" id="registration_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_registration_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the member and the member area.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#email-shortcodes"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="autoresponders-module"<?php if (in_array('autoresponders', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="autoresponders"><strong><?php echo $modules['options']['autoresponders']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('You must make some adjustments so that the subscription works with some autoresponders.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#autoresponders"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="member_subscribed_to_autoresponder" id="member_subscribed_to_autoresponder" value="yes"<?php if ($options['member_subscribed_to_autoresponder'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Subscribe the member to an autoresponder list', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="member_autoresponder"><?php _e('Autoresponder', 'membership-manager'); ?></label></strong></th>
<td><select name="member_autoresponder" id="member_autoresponder">
<?php include 'libraries/autoresponders.php';
$autoresponder = do_shortcode($options['member_autoresponder']);
foreach ($autoresponders as $value) {
echo '<option value="'.$value.'"'.($autoresponder == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="member_autoresponder_list"><?php _e('List', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="member_autoresponder_list" id="member_autoresponder_list" rows="1" cols="50"><?php echo $options['member_autoresponder_list']; ?></textarea><br />
<span class="description"><?php _e('For some autoresponders, you must enter the list ID.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#autoresponders"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
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
<td><span class="description"><a href="admin.php?page=commerce-manager#autoresponders-integration"><?php _e('Click here to configure the options of Commerce Manager.', 'membership-manager'); ?></a></span></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('You must make some adjustments so that the subscription works with some autoresponders.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#autoresponders"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
</tbody></table>
<div id="aweber-module"<?php if (in_array('aweber', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="aweber"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['aweber']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#aweber"><?php _e('Click here to read the instructions for integration.', 'membership-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="cybermailing-module"<?php if (in_array('cybermailing', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="cybermailing"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['cybermailing']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('You have no adjustment to make so that the subscription works with CyberMailing.', 'membership-manager'); ?></span></td></tr>
</tbody></table>
</div>
<div id="getresponse-module"<?php if (in_array('getresponse', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="getresponse"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['getresponse']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="getresponse_api_key"><?php _e('API key', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="getresponse_api_key" id="getresponse_api_key" rows="1" cols="50"><?php echo $options['getresponse_api_key']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/membership-manager/documentation/#getresponse"><?php _e('More informations', 'membership-manager'); ?></a>
<?php if (function_exists('commerce_manager_admin_menu')) { echo '<br />'.__('Leave this field blank to apply the Commerce Manager\'s option.', 'membership-manager'); } ?></span></td></tr>
</tbody></table>
</div>
<div id="mailchimp-module"<?php if (in_array('mailchimp', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="mailchimp"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['mailchimp']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="mailchimp_api_key"><?php _e('API key', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="mailchimp_api_key" id="mailchimp_api_key" rows="1" cols="50"><?php echo $options['mailchimp_api_key']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/membership-manager/documentation/#mailchimp"><?php _e('More informations', 'membership-manager'); ?></a>
<?php if (function_exists('commerce_manager_admin_menu')) { echo '<br />'.__('Leave this field blank to apply the Commerce Manager\'s option.', 'membership-manager'); } ?></span></td></tr>
</tbody></table>
</div>
<div id="sg-autorepondeur-module"<?php if (in_array('sg-autorepondeur', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="sg-autorepondeur"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['sg-autorepondeur']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="sg_autorepondeur_account_id"><?php _e('Account ID', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="sg_autorepondeur_account_id" id="sg_autorepondeur_account_id" rows="1" cols="25"><?php echo $options['sg_autorepondeur_account_id']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/membership-manager/documentation/#sg-autorepondeur"><?php _e('More informations', 'membership-manager'); ?></a>
<?php if (function_exists('commerce_manager_admin_menu')) { echo '<br />'.__('Leave this field blank to apply the Commerce Manager\'s option.', 'membership-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="sg_autorepondeur_activation_code"><?php _e('Activation code', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="sg_autorepondeur_activation_code" id="sg_autorepondeur_activation_code" rows="1" cols="50"><?php echo $options['sg_autorepondeur_activation_code']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/membership-manager/documentation/#sg-autorepondeur"><?php _e('More informations', 'membership-manager'); ?></a>
<?php if (function_exists('commerce_manager_admin_menu')) { echo '<br />'.__('Leave this field blank to apply the Commerce Manager\'s option.', 'membership-manager'); } ?></span></td></tr>
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
<td><span class="description"><?php echo (function_exists('commerce_manager_admin_menu') ? '<a href="admin.php?page=commerce-manager-clients-accounts">'.__('Click here to configure the options of Commerce Manager.', 'membership-manager').'</a>' : __('To subscribe your members as clients, you must have installed and activated <a href="http://www.kleor-editions.com/commerce-manager">Commerce Manager</a>.', 'membership-manager')); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="member_subscribed_as_a_client" id="member_subscribed_as_a_client" value="yes"<?php if ($options['member_subscribed_as_a_client'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the member as a client', 'membership-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#registration-as-a-client"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."commerce_manager_clients_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="member_client_category_id"><?php _e('Category', 'membership-manager'); ?></label></strong></th>
<td><select name="member_client_category_id" id="member_client_category_id">
<option value=""<?php if ($options['member_client_category_id'] == '') { echo ' selected="selected"'; } ?>><?php _e('Commerce Manager\'s option', 'membership-manager'); ?></option>
<option value="0"<?php if ($options['member_client_category_id'] == '0') { echo ' selected="selected"'; } ?>><?php _e('None ', 'membership-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($options['member_client_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ((function_exists('commerce_manager_admin_menu')) && ($options['member_client_category_id'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-client-category&amp;id='.$options['member_client_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-client-category&amp;id='.$options['member_client_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="member_client_status"><?php _e('Status', 'membership-manager'); ?></label></strong></th>
<td><select name="member_client_status" id="member_client_status">
<option value=""<?php if ($options['member_client_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('Commerce Manager\'s option', 'membership-manager'); ?></option>
<option value="active"<?php if ($options['member_client_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'membership-manager'); ?></option>
<option value="inactive"<?php if ($options['member_client_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'membership-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#client-status"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commerce_registration_confirmation_email_sent"><?php _e('Send a registration confirmation email', 'membership-manager'); ?></label></strong></th>
<td><select name="commerce_registration_confirmation_email_sent" id="commerce_registration_confirmation_email_sent">
<option value=""<?php if ($options['commerce_registration_confirmation_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Commerce Manager\'s option', 'membership-manager'); ?></option>
<option value="yes"<?php if ($options['commerce_registration_confirmation_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'membership-manager'); ?></option>
<option value="no"<?php if ($options['commerce_registration_confirmation_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'membership-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commerce_registration_notification_email_sent"><?php _e('Send a registration notification email', 'membership-manager'); ?></label></strong></th>
<td><select name="commerce_registration_notification_email_sent" id="commerce_registration_notification_email_sent">
<option value=""<?php if ($options['commerce_registration_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Commerce Manager\'s option', 'membership-manager'); ?></option>
<option value="yes"<?php if ($options['commerce_registration_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'membership-manager'); ?></option>
<option value="no"<?php if ($options['commerce_registration_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'membership-manager'); ?></option>
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
<td><span class="description"><?php echo (function_exists('affiliation_manager_admin_menu') ? '<a href="admin.php?page=affiliation-manager">'.__('Click here to configure the options of Affiliation Manager.', 'membership-manager').'</a>' : __('To use affiliation, you must have installed and activated <a href="http://www.kleor-editions.com/affiliation-manager">Affiliation Manager</a>.', 'membership-manager')); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="member_subscribed_to_affiliate_program" id="member_subscribed_to_affiliate_program" value="yes"<?php if ($options['member_subscribed_to_affiliate_program'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the member to affiliate program', 'membership-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#registration-to-affiliate-program"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."affiliation_manager_affiliates_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="member_affiliate_category_id"><?php _e('Category', 'membership-manager'); ?></label></strong></th>
<td><select name="member_affiliate_category_id" id="member_affiliate_category_id">
<option value=""<?php if ($options['member_affiliate_category_id'] == '') { echo ' selected="selected"'; } ?>><?php _e('Affiliation Manager\'s option', 'membership-manager'); ?></option>
<option value="0"<?php if ($options['member_affiliate_category_id'] == '0') { echo ' selected="selected"'; } ?>><?php _e('None ', 'membership-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($options['member_affiliate_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ((function_exists('affiliation_manager_admin_menu')) && ($options['member_affiliate_category_id'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$options['member_affiliate_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$options['member_affiliate_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="member_affiliate_status"><?php _e('Status', 'membership-manager'); ?></label></strong></th>
<td><select name="member_affiliate_status" id="member_affiliate_status">
<option value=""<?php if ($options['member_affiliate_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('Affiliation Manager\'s option', 'membership-manager'); ?></option>
<option value="active"<?php if ($options['member_affiliate_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'membership-manager'); ?></option>
<option value="inactive"<?php if ($options['member_affiliate_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'membership-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#affiliate-status"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliation_registration_confirmation_email_sent"><?php _e('Send a registration confirmation email', 'membership-manager'); ?></label></strong></th>
<td><select name="affiliation_registration_confirmation_email_sent" id="affiliation_registration_confirmation_email_sent">
<option value=""<?php if ($options['affiliation_registration_confirmation_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Affiliation Manager\'s option', 'membership-manager'); ?></option>
<option value="yes"<?php if ($options['affiliation_registration_confirmation_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'membership-manager'); ?></option>
<option value="no"<?php if ($options['affiliation_registration_confirmation_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'membership-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliation_registration_notification_email_sent"><?php _e('Send a registration notification email', 'membership-manager'); ?></label></strong></th>
<td><select name="affiliation_registration_notification_email_sent" id="affiliation_registration_notification_email_sent">
<option value=""<?php if ($options['affiliation_registration_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Affiliation Manager\'s option', 'membership-manager'); ?></option>
<option value="yes"<?php if ($options['affiliation_registration_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'membership-manager'); ?></option>
<option value="no"<?php if ($options['affiliation_registration_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'membership-manager'); ?></option>
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
<td><label><input type="checkbox" name="member_subscribed_as_a_user" id="member_subscribed_as_a_user" value="yes"<?php if ($options['member_subscribed_as_a_user'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the member as a user', 'membership-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#wordpress"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="member_user_role"><?php _e('Role', 'membership-manager'); ?></label></strong></th>
<td><select name="member_user_role" id="member_user_role">
<?php foreach (membership_manager_users_roles() as $role => $name) {
echo '<option value="'.$role.'"'.($options['member_user_role'] == $role ? ' selected="selected"' : '').'>'.$name.'</option>'."\n"; } ?>
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
<td><label><input type="checkbox" name="registration_custom_instructions_executed" id="registration_custom_instructions_executed" value="yes"<?php if ($options['registration_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_custom_instructions"><?php _e('PHP code', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_custom_instructions" id="registration_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_registration_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the registration of a member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#custom-instructions"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="login-custom-instructions-module"<?php if (in_array('login-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="login-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['login-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="login_custom_instructions_executed" id="login_custom_instructions_executed" value="yes"<?php if ($options['login_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="login_custom_instructions"><?php _e('PHP code', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="login_custom_instructions" id="login_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_login_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the login of a member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#custom-instructions"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="logout-custom-instructions-module"<?php if (in_array('logout-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="logout-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['logout-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="logout_custom_instructions_executed" id="logout_custom_instructions_executed" value="yes"<?php if ($options['logout_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="logout_custom_instructions"><?php _e('PHP code', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="logout_custom_instructions" id="logout_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_logout_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the logout of a member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#custom-instructions"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="password-reset-custom-instructions-module"<?php if (in_array('password-reset-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="password-reset-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['password-reset-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="password_reset_custom_instructions_executed" id="password_reset_custom_instructions_executed" value="yes"<?php if ($options['password_reset_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_custom_instructions"><?php _e('PHP code', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="password_reset_custom_instructions" id="password_reset_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_password_reset_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the password reset of a member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#custom-instructions"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="profile-edit-custom-instructions-module"<?php if (in_array('profile-edit-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="profile-edit-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['profile-edit-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="profile_edit_custom_instructions_executed" id="profile_edit_custom_instructions_executed" value="yes"<?php if ($options['profile_edit_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_edit_custom_instructions"><?php _e('PHP code', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="profile_edit_custom_instructions" id="profile_edit_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_profile_edit_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the profile edit of a member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#custom-instructions"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="activation-custom-instructions-module"<?php if (in_array('activation-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="activation-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['activation-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="activation_custom_instructions_executed" id="activation_custom_instructions_executed" value="yes"<?php if ($options['activation_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_custom_instructions"><?php _e('PHP code', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="activation_custom_instructions" id="activation_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_activation_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the activation of a member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#custom-instructions"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="deactivation-custom-instructions-module"<?php if (in_array('deactivation-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="deactivation-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['deactivation-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="deactivation_custom_instructions_executed" id="deactivation_custom_instructions_executed" value="yes"<?php if ($options['deactivation_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_custom_instructions"><?php _e('PHP code', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="deactivation_custom_instructions" id="deactivation_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_deactivation_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the deactivation of a member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#custom-instructions"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="removal-custom-instructions-module"<?php if (in_array('removal-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="removal-custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['modules']['removal-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="removal_custom_instructions_executed" id="removal_custom_instructions_executed" value="yes"<?php if ($options['removal_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_custom_instructions"><?php _e('PHP code', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="removal_custom_instructions" id="removal_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_removal_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the removal of a member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#custom-instructions"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
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
<td><label><input type="checkbox" name="login_notification_email_sent" id="login_notification_email_sent" value="yes"<?php if ($options['login_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a login notification email', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="login_notification_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="login_notification_email_sender" id="login_notification_email_sender" rows="1" cols="75"><?php echo $options['login_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="login_notification_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="login_notification_email_receiver" id="login_notification_email_receiver" rows="1" cols="75"><?php echo $options['login_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="login_notification_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="login_notification_email_subject" id="login_notification_email_subject" rows="1" cols="75"><?php echo $options['login_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="login_notification_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="login_notification_email_body" id="login_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_login_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#email-shortcodes"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="logout-notification-email-module"<?php if (in_array('logout-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="logout-notification-email"><strong><?php echo $modules['options']['logout-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="logout_notification_email_sent" id="logout_notification_email_sent" value="yes"<?php if ($options['logout_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a logout notification email', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="logout_notification_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="logout_notification_email_sender" id="logout_notification_email_sender" rows="1" cols="75"><?php echo $options['logout_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="logout_notification_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="logout_notification_email_receiver" id="logout_notification_email_receiver" rows="1" cols="75"><?php echo $options['logout_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="logout_notification_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="logout_notification_email_subject" id="logout_notification_email_subject" rows="1" cols="75"><?php echo $options['logout_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="logout_notification_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="logout_notification_email_body" id="logout_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_logout_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#email-shortcodes"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="password-reset-email-module"<?php if (in_array('password-reset-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="password-reset-email"><strong><?php echo $modules['options']['password-reset-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_email_sender" id="password_reset_email_sender" rows="1" cols="75"><?php echo $options['password_reset_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_email_receiver" id="password_reset_email_receiver" rows="1" cols="75"><?php echo $options['password_reset_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_email_subject" id="password_reset_email_subject" rows="1" cols="75"><?php echo $options['password_reset_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="password_reset_email_body" id="password_reset_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_password_reset_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#email-shortcodes"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="password-reset-notification-email-module"<?php if (in_array('password-reset-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="password-reset-notification-email"><strong><?php echo $modules['options']['password-reset-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="password_reset_notification_email_sent" id="password_reset_notification_email_sent" value="yes"<?php if ($options['password_reset_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a password reset notification email', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_notification_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_notification_email_sender" id="password_reset_notification_email_sender" rows="1" cols="75"><?php echo $options['password_reset_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_notification_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_notification_email_receiver" id="password_reset_notification_email_receiver" rows="1" cols="75"><?php echo $options['password_reset_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_notification_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_notification_email_subject" id="password_reset_notification_email_subject" rows="1" cols="75"><?php echo $options['password_reset_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_notification_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="password_reset_notification_email_body" id="password_reset_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_password_reset_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#email-shortcodes"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="profile-edit-notification-email-module"<?php if (in_array('profile-edit-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="profile-edit-notification-email"><strong><?php echo $modules['options']['profile-edit-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="profile_edit_notification_email_sent" id="profile_edit_notification_email_sent" value="yes"<?php if ($options['profile_edit_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a profile edit notification email', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_edit_notification_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="profile_edit_notification_email_sender" id="profile_edit_notification_email_sender" rows="1" cols="75"><?php echo $options['profile_edit_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_edit_notification_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="profile_edit_notification_email_receiver" id="profile_edit_notification_email_receiver" rows="1" cols="75"><?php echo $options['profile_edit_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_edit_notification_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="profile_edit_notification_email_subject" id="profile_edit_notification_email_subject" rows="1" cols="75"><?php echo $options['profile_edit_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_edit_notification_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="profile_edit_notification_email_body" id="profile_edit_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_profile_edit_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#email-shortcodes"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="activation-confirmation-email-module"<?php if (in_array('activation-confirmation-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="activation-confirmation-email"><strong><?php echo $modules['options']['activation-confirmation-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="activation_confirmation_email_sent" id="activation_confirmation_email_sent" value="yes"<?php if ($options['activation_confirmation_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send an activation confirmation email', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_confirmation_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_confirmation_email_sender" id="activation_confirmation_email_sender" rows="1" cols="75"><?php echo $options['activation_confirmation_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_confirmation_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_confirmation_email_receiver" id="activation_confirmation_email_receiver" rows="1" cols="75"><?php echo $options['activation_confirmation_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_confirmation_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_confirmation_email_subject" id="activation_confirmation_email_subject" rows="1" cols="75"><?php echo $options['activation_confirmation_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_confirmation_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="activation_confirmation_email_body" id="activation_confirmation_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_activation_confirmation_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the member and the member area.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#email-shortcodes"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="activation-notification-email-module"<?php if (in_array('activation-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="activation-notification-email"><strong><?php echo $modules['options']['activation-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_sender" id="activation_notification_email_sender" rows="1" cols="75"><?php echo $options['activation_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_receiver" id="activation_notification_email_receiver" rows="1" cols="75"><?php echo $options['activation_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_subject" id="activation_notification_email_subject" rows="1" cols="75"><?php echo $options['activation_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="activation_notification_email_body" id="activation_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_activation_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#email-shortcodes"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="deactivation-notification-email-module"<?php if (in_array('deactivation-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="deactivation-notification-email"><strong><?php echo $modules['options']['deactivation-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="deactivation_notification_email_sender" id="deactivation_notification_email_sender" rows="1" cols="75"><?php echo $options['deactivation_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="deactivation_notification_email_receiver" id="deactivation_notification_email_receiver" rows="1" cols="75"><?php echo $options['deactivation_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="deactivation_notification_email_subject" id="deactivation_notification_email_subject" rows="1" cols="75"><?php echo $options['deactivation_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="deactivation_notification_email_body" id="deactivation_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_deactivation_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#email-shortcodes"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="removal-notification-email-module"<?php if (in_array('removal-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="removal-notification-email"><strong><?php echo $modules['options']['removal-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="removal_notification_email_sender" id="removal_notification_email_sender" rows="1" cols="75"><?php echo $options['removal_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="removal_notification_email_receiver" id="removal_notification_email_receiver" rows="1" cols="75"><?php echo $options['removal_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="removal_notification_email_subject" id="removal_notification_email_subject" rows="1" cols="75"><?php echo $options['removal_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="removal_notification_email_body" id="removal_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_removal_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#email-shortcodes"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
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
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="default_captcha_type"><?php _e('Default type', 'membership-manager'); ?></label></strong></th>
<td><select name="default_captcha_type" id="default_captcha_type">
<?php include 'libraries/captchas.php';
$captcha_type = do_shortcode($options['default_captcha_type']);
asort($captchas_types);
foreach ($captchas_types as $key => $value) {
echo '<option value="'.$key.'"'.($captcha_type == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select>
<span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#captcha"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="default_recaptcha_theme"><?php _e('Default reCAPTCHA theme', 'membership-manager'); ?></label></strong></th>
<td><select name="default_recaptcha_theme" id="default_recaptcha_theme">
<?php include 'libraries/captchas.php';
$recaptcha_theme = do_shortcode($options['default_recaptcha_theme']);
asort($recaptcha_themes);
foreach ($recaptcha_themes as $key => $value) {
echo '<option value="'.$key.'"'.($recaptcha_theme == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select>
<span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#recaptcha"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recaptcha_public_key"><?php _e('reCAPTCHA public key', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="recaptcha_public_key" id="recaptcha_public_key" rows="1" cols="50"><?php echo $options['recaptcha_public_key']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/membership-manager/documentation/#recaptcha"><?php _e('More informations', 'membership-manager'); ?></a>
<?php if (function_exists('commerce_manager_admin_menu')) { echo '<br />'.__('Leave this field blank to apply the Commerce Manager\'s option.', 'membership-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recaptcha_private_key"><?php _e('reCAPTCHA private key', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="recaptcha_private_key" id="recaptcha_private_key" rows="1" cols="50"><?php echo $options['recaptcha_private_key']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/membership-manager/documentation/#recaptcha"><?php _e('More informations', 'membership-manager'); ?></a>
<?php if (function_exists('commerce_manager_admin_menu')) { echo '<br />'.__('Leave this field blank to apply the Commerce Manager\'s option.', 'membership-manager'); } ?></span></td></tr>
</tbody></table>
</div>
<div id="error-messages-module"<?php if (in_array('error-messages', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="error-messages"><strong><?php echo $modules['options']['forms']['modules']['error-messages']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="unfilled_fields_message"><?php _e('Unfilled required fields', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="unfilled_fields_message" id="unfilled_fields_message" rows="1" cols="75"><?php echo $options['unfilled_fields_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="unfilled_field_message"><?php _e('Unfilled required field', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="unfilled_field_message" id="unfilled_field_message" rows="1" cols="75"><?php echo $options['unfilled_field_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="invalid_email_address_message"><?php _e('Invalid email address', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="invalid_email_address_message" id="invalid_email_address_message" rows="1" cols="75"><?php echo $options['invalid_email_address_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="numeric_login_message"><?php _e('Numeric login name', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="numeric_login_message" id="numeric_login_message" rows="1" cols="75"><?php echo $options['numeric_login_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="too_short_login_message"><?php _e('Too short login name', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="too_short_login_message" id="too_short_login_message" rows="1" cols="75"><?php echo $options['too_short_login_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="too_long_login_message"><?php _e('Too long login name', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="too_long_login_message" id="too_long_login_message" rows="1" cols="75"><?php echo $options['too_long_login_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="too_short_password_message"><?php _e('Too short password', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="too_short_password_message" id="too_short_password_message" rows="1" cols="75"><?php echo $options['too_short_password_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="too_long_password_message"><?php _e('Too long password', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="too_long_password_message" id="too_long_password_message" rows="1" cols="75"><?php echo $options['too_long_password_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="unavailable_login_message"><?php _e('Unavailable login name', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="unavailable_login_message" id="unavailable_login_message" rows="1" cols="75"><?php echo $options['unavailable_login_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="unavailable_email_address_message"><?php _e('Unavailable email address', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="unavailable_email_address_message" id="unavailable_email_address_message" rows="1" cols="75"><?php echo $options['unavailable_email_address_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="invalid_login_or_password_message"><?php _e('Invalid login or password', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="invalid_login_or_password_message" id="invalid_login_or_password_message" rows="1" cols="75"><?php echo $options['invalid_login_or_password_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="inactive_account_message"><?php _e('Inactive account', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="inactive_account_message" id="inactive_account_message" rows="1" cols="75"><?php echo $options['inactive_account_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="inexistent_email_address_message"><?php _e('Inexistent email address', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="inexistent_email_address_message" id="inexistent_email_address_message" rows="1" cols="75"><?php echo $options['inexistent_email_address_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="invalid_captcha_message"><?php _e('Invalid CAPTCHA', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="invalid_captcha_message" id="invalid_captcha_message" rows="1" cols="75"><?php echo $options['invalid_captcha_message']; ?></textarea></td></tr>
</tbody></table>
</div>
<div id="login-availability-indicator-module"<?php if (in_array('login-availability-indicator', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="login-availability-indicator"><strong><?php echo $modules['options']['forms']['modules']['login-availability-indicator']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="available_login_indicator_message"><?php _e('Available', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="available_login_indicator_message" id="available_login_indicator_message" rows="1" cols="75"><?php echo $options['available_login_indicator_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="unavailable_login_indicator_message"><?php _e('Unavailable', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="unavailable_login_indicator_message" id="unavailable_login_indicator_message" rows="1" cols="75"><?php echo $options['unavailable_login_indicator_message']; ?></textarea></td></tr>
</tbody></table>
</div>
<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes'); ?>" /></p>
<?php membership_manager_pages_module($back_office_options, 'options-page', $undisplayed_modules); ?>
</form>
</div>
</div>
<?php }