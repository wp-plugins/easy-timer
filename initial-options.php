<?php foreach (array('admin_email', 'blogname', 'siteurl') as $key) { $$key = get_option($key); }


$initial_options[''] = array(
'activation_confirmation_email_receiver' => '[member email-address]',
'activation_confirmation_email_sender' => $blogname.' <'.$admin_email.'>',
'activation_confirmation_email_sent' => 'yes',
'activation_confirmation_email_subject' => __('Activation Of Your Member Account', 'membership-manager'),
'activation_confirmation_url' => HOME_URL,
'activation_custom_instructions_executed' => 'no',
'activation_notification_email_receiver' => '[member email-address]',
'activation_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'activation_notification_email_subject' => __('Activation Of Your Member Account', 'membership-manager'),
'affiliation_registration_confirmation_email_sent' => '',
'affiliation_registration_notification_email_sent' => '',
'available_login_indicator_message' => '<span style="color: green;">'.__('Available', 'membership-manager').'</span>',
'commerce_registration_confirmation_email_sent' => '',
'commerce_registration_notification_email_sent' => '',
'deactivation_custom_instructions_executed' => 'no',
'deactivation_notification_email_receiver' => '[member email-address]',
'deactivation_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'deactivation_notification_email_subject' => __('Deactivation Of Your Member Account', 'membership-manager'),
'default_captcha_type' => 'recaptcha',
'default_recaptcha_theme' => 'red',
'encrypted_urls_key' => md5(mt_rand()),
'encrypted_urls_validity_duration' => 48,
'getresponse_api_key' => '',
'inactive_account_message' => __('Your account is inactive.', 'membership-manager'),
'inexistent_email_address_message' => __('This email address does not match a member account.', 'membership-manager'),
'invalid_captcha_message' => __('The code you entered for the CAPTCHA is incorrect.', 'membership-manager'),
'invalid_email_address_message' => __('This email address appears to be invalid.', 'membership-manager'),
'invalid_login_or_password_message' => __('Invalid login or password', 'membership-manager'),
'login_custom_instructions_executed' => 'no',
'login_notification_email_receiver' => $admin_email,
'login_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'login_notification_email_sent' => 'no',
'login_notification_email_subject' => __('A Member Has Logged In', 'membership-manager').' ([member login])',
'logout_custom_instructions_executed' => 'no',
'logout_notification_email_receiver' => $admin_email,
'logout_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'logout_notification_email_sent' => 'no',
'logout_notification_email_subject' => __('A Member Has Logged Out', 'membership-manager').' ([member login])',
'mailchimp_api_key' => '',
'maximum_login_length' => 32,
'maximum_password_length' => 32,
'member_affiliate_category_id' => '',
'member_affiliate_status' => '',
'member_autoresponder' => 'AWeber',
'member_autoresponder_list' => '',
'member_client_category_id' => '',
'member_client_status' => '',
'member_subscribed_as_a_client' => 'no',
'member_subscribed_as_a_user' => 'no',
'member_subscribed_to_affiliate_program' => 'no',
'member_subscribed_to_autoresponder' => 'no',
'member_user_role' => 'subscriber',
'members_initial_category_id' => 0,
'members_initial_status' => 'active',
'minimum_login_length' => 1,
'minimum_password_length' => 5,
'numeric_login_message' => __('Your login name must be a non-numeric string.', 'membership-manager'),
'password_reset_custom_instructions_executed' => 'no',
'password_reset_email_receiver' => '[member email-address]',
'password_reset_email_sender' => $blogname.' <'.$admin_email.'>',
'password_reset_email_subject' => __('Your New Password', 'membership-manager'),
'password_reset_notification_email_receiver' => $admin_email,
'password_reset_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'password_reset_notification_email_sent' => 'yes',
'password_reset_notification_email_subject' => __('A Member Has Reset His Password', 'membership-manager').' ([member login])',
'profile_edit_custom_instructions_executed' => 'no',
'profile_edit_notification_email_receiver' => $admin_email,
'profile_edit_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'profile_edit_notification_email_sent' => 'yes',
'profile_edit_notification_email_subject' => __('A Member Has Edited His Profile', 'membership-manager').' ([member login])',
'recaptcha_private_key' => '',
'recaptcha_public_key' => '',
'registration_confirmation_email_receiver' => '[member email-address]',
'registration_confirmation_email_sender' => $blogname.' <'.$admin_email.'>',
'registration_confirmation_email_sent' => 'yes',
'registration_confirmation_email_subject' => __('Your Registration To Our Member Area', 'membership-manager'),
'registration_confirmation_url' => HOME_URL,
'registration_custom_instructions_executed' => 'no',
'registration_notification_email_receiver' => $admin_email,
'registration_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'registration_notification_email_sent' => 'yes',
'registration_notification_email_subject' => __('Registration Of A Member', 'membership-manager').' ([member login])',
'removal_custom_instructions_executed' => 'no',
'removal_notification_email_receiver' => '[member email-address]',
'removal_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'removal_notification_email_subject' => __('Removal Of Your Member Account', 'membership-manager'),
'sg_autorepondeur_account_id' => '',
'sg_autorepondeur_activation_code' => '',
'too_long_login_message' => __('Your login name must contain at most [membership-manager maximum-login-length] characters.', 'membership-manager'),
'too_long_password_message' => __('Your password must contain at most [membership-manager maximum-password-length] characters.', 'membership-manager'),
'too_short_login_message' => __('Your login name must contain at least [membership-manager minimum-login-length] characters.', 'membership-manager'),
'too_short_password_message' => __('Your password must contain at least [membership-manager minimum-password-length] characters.', 'membership-manager'),
'unavailable_email_address_message' => __('This email address is not available.', 'membership-manager'),
'unavailable_login_indicator_message' => '<span style="color: red;">'.__('Unavailable', 'membership-manager').'</span>',
'unavailable_login_message' => __('This login name is not available.', 'membership-manager'),
'unfilled_field_message' => __('This field is required.', 'membership-manager'),
'unfilled_fields_message' => __('Please fill out the required fields.', 'membership-manager'),
'version' => MEMBERSHIP_MANAGER_VERSION);


$initial_options['activation_confirmation_email_body'] =
__('Hi', 'membership-manager').' [member first-name],

'.__('Thanks for activating your member account.', 'membership-manager').' '.__('You can login from this page:', 'membership-manager').'

'.HOME_URL.'

--
'.$blogname.'
'.HOME_URL;


$initial_options['activation_custom_instructions'] = '';


$initial_options['activation_notification_email_body'] =
__('Hi', 'membership-manager').' [member first-name],

'.__('Your member account has been activated.', 'membership-manager').' '.__('You can login from this page:', 'membership-manager').'

'.HOME_URL.'

--
'.$blogname.'
'.HOME_URL;


include 'libraries/captchas.php';
$initial_options['captchas_numbers'] = $captchas_numbers;


$initial_options['deactivation_custom_instructions'] = '';


$initial_options['deactivation_notification_email_body'] =
__('Hi', 'membership-manager').' [member first-name],



--
'.$blogname.'
'.HOME_URL;


$initial_options['login_custom_instructions'] = '';


$initial_options['login_form'] = array(
'inactive_account_message' => '',
'invalid_captcha_message' => '',
'invalid_login_or_password_message' => '',
'unfilled_field_message' => '');


$initial_options['login_compact_form'] = $initial_options['login_form'];


$initial_options['login_form_code'] =
'[validation-content][other]<p style="color: red;">[error invalid-login-or-password] [error inactive-account] [error invalid-captcha]</p>[/validation-content]

<table style="width: 100%;">
<tr style="vertical-align: top;"><td style="width: 40%;"><strong>[label login]'.__('Login name', 'membership-manager').'[/label]</strong></td>
<td style="width: 60%;">[input login size=20]<br />[error style="color: red;" login]</td></tr>
<tr style="vertical-align: top;"><td style="width: 40%;"><strong>[label password]'.__('Password', 'membership-manager').'[/label]</strong></td>
<td style="width: 60%;">[input password size=20]<br />[error style="color: red;" password]</td></tr>
</table>
<p style="margin: 0.75em; text-align: center;"><label>[input remember value=yes] '.__('Remember me', 'membership-manager').'</label></p>
<div style="text-align: center;">[input submit value="'.__('Login', 'membership-manager').'"]</div>';


$initial_options['login_compact_form_code'] =
'[validation-content][other]<p style="color: red;">[error invalid-login-or-password] [error inactive-account] [error invalid-captcha]</p>[/validation-content]

<p><strong>[label login]'.__('Login name:', 'membership-manager').'[/label]</strong><br />
[input login size=20]<br />[error style="color: red;" login]</p>
<p><strong>[label password]'.__('Password:', 'membership-manager').'[/label]</strong><br />
[input password size=20]<br />[error style="color: red;" password]</p>
<p><label>[input remember value=yes] '.__('Remember me', 'membership-manager').'</label></p>
<div style="text-align: center;">[input submit value="'.__('Login', 'membership-manager').'"]</div>';


$initial_options['login_notification_email_body'] =
'[member first-name] [member last-name]

'.__('Login name:', 'membership-manager').' [member login]
'.__('Email address:', 'membership-manager').' [member email-address]
'.__('Website name:', 'membership-manager').' [member website-name]
'.__('Website URL:', 'membership-manager').' [member website-url]
'.__('Member area:', 'membership-manager').' [member-area name]

'.__('More informations about this member:', 'membership-manager').'

'.$siteurl.'/wp-admin/admin.php?page=membership-manager-member&id=[member id]';


$initial_options['logout_custom_instructions'] = '';


$initial_options['logout_notification_email_body'] = $initial_options['login_notification_email_body'];


$initial_options['meta_widget'] = array(
'title' => __('Membership', 'membership-manager'),
'content' => '[membership-content]
<ul>
<li><a href="'.MEMBERSHIP_MANAGER_URL.'?action=logout">'.__('Log out').'</a></li>
</ul>
[other][membership-login-compact-form]
[/membership-content]');


$initial_options['password_reset_custom_instructions'] = '';


$initial_options['password_reset_email_body'] =
__('Hi', 'membership-manager').' [member first-name],

'.__('Here are your new login informations:', 'membership-manager').'

'.__('Your login name:', 'membership-manager').' [member login]
'.__('Your password:', 'membership-manager').' [member password]

'.__('You can login from this page:', 'membership-manager').'

'.HOME_URL.'

--
'.$blogname.'
'.HOME_URL;


$initial_options['password_reset_form'] = array(
'inexistent_email_address_message' => '',
'invalid_captcha_message' => '',
'invalid_email_address_message' => '',
'unfilled_field_message' => '');


$initial_options['password_reset_form_code'] =
'[validation-content]<p style="color: green;">'.__('Your password has been reset successfully.', 'membership-manager').'</p>
[other]<p style="color: red;">[error inexistent-email-address] [error invalid-captcha]</p>[/validation-content]

<div style="text-align: center;">
<p><label><strong>'.__('Your email address:', 'membership-manager').'</strong><br />
[input email-address size=40]<br />
[error style="color: red;" email-address]</label></p>
<div>[input submit value="'.__('Reset', 'membership-manager').'"]</div>
</div>';


$initial_options['password_reset_notification_email_body'] = $initial_options['login_notification_email_body'];


$initial_options['profile_edit_custom_instructions'] = '';


$initial_options['profile_edit_notification_email_body'] = $initial_options['login_notification_email_body'];


$initial_options['profile_form'] = array(
'available_login_indicator_message' => '',
'invalid_captcha_message' => '',
'invalid_email_address_message' => '',
'numeric_login_message' => '',
'too_long_login_message' => '',
'too_long_password_message' => '',
'too_short_login_message' => '',
'too_short_password_message' => '',
'unavailable_email_address_message' => '',
'unavailable_login_indicator_message' => '',
'unavailable_login_message' => '',
'unfilled_field_message' => '',
'unfilled_fields_message' => '');


$initial_options['profile_form_code'] =
'[validation-content]<p style="color: green;">'.__('Your profile has been changed successfully.', 'membership-manager').'</p>
[other]<p style="color: red;">[error unavailable-login] [error numeric-login] [error too-short-login] [error too-long-login] [error too-short-password] [error too-long-password] [error unavailable-email-address] [error unfilled-fields] [error invalid-captcha]</p>[/validation-content]

<table style="width: 100%;">
<tr style="vertical-align: top;"><td><strong>[label login]'.__('Login name', 'membership-manager').'[/label]</strong>*</td>
<td>[input login size=30] [indicator login]<br />
<span class="description">'.__('Letters, numbers, hyphens, underscores, points and <em>@</em> only', 'membership-manager').'</span><br />
[error style="color: red;" login]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label password]'.__('Password', 'membership-manager').'[/label]</strong></td>
<td>[input password size=30]<br /><span class="description">'.__('(if you want to change it)', 'membership-manager').'</span><br />
[error style="color: red;" password]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label first-name]'.__('First name', 'membership-manager').'[/label]</strong>*</td>
<td>[input first-name size=30 required=yes]<br />[error style="color: red;" first-name]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label last-name]'.__('Last name', 'membership-manager').'[/label]</strong>*</td>
<td>[input last-name size=30 required=yes]<br />[error style="color: red;" last-name]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label email-address]'.__('Email address', 'membership-manager').'[/label]</strong>*</td>
<td>[input email-address size=30 required=yes]<br />[error style="color: red;" email-address]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label website-name]'.__('Website name', 'membership-manager').'[/label]</strong></td>
<td>[input website-name size=30]<br />[error style="color: red;" website-name]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label website-url]'.__('Website URL', 'membership-manager').'[/label]</strong></td>
<td>[input website-url size=30]<br />[error style="color: red;" website-url]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label address]'.__('Address', 'membership-manager').'[/label]</strong></td>
<td>[input address size=30]<br />[error style="color: red;" address]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label postcode]'.__('Postcode', 'membership-manager').'[/label]</strong></td>
<td>[input postcode size=30]<br />[error style="color: red;" postcode]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label town]'.__('Town', 'membership-manager').'[/label]</strong></td>
<td>[input town size=30]<br />[error style="color: red;" town]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label country]'.__('Country', 'membership-manager').'[/label]</strong></td>
<td>[input country size=30]<br />[error style="color: red;" country]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label phone-number]'.__('Phone number', 'membership-manager').'[/label]</strong></td>
<td>[input phone-number size=30]<br />[error style="color: red;" phone-number]</td></tr>
</table>
<div style="text-align: center;">[input submit value="'.__('Modify', 'membership-manager').'"]</div>';


$initial_options['registration_confirmation_email_body'] =
__('Thank you for your registration to our member area', 'membership-manager').', [member first-name].
'.__('You can login from this page:', 'membership-manager').'

'.HOME_URL.'

'.__('Your login name:', 'membership-manager').' [member login]
'.__('Your password:', 'membership-manager').' [member password]

--
'.$blogname.'
'.HOME_URL;


$initial_options['registration_custom_instructions'] = '';


$initial_options['registration_form'] = $initial_options['profile_form'];


$initial_options['registration_compact_form'] = $initial_options['registration_form'];


$initial_options['registration_form_code'] =
'[validation-content][other]<p style="color: red;">[error unavailable-login] [error numeric-login] [error too-short-login] [error too-long-login] [error too-short-password] [error too-long-password] [error unavailable-email-address] [error unfilled-fields] [error invalid-captcha]</p>[/validation-content]

<table style="width: 100%;">
<tr style="vertical-align: top;"><td><strong>[label login]'.__('Login name', 'membership-manager').'[/label]</strong>*</td>
<td>[input login size=30] [indicator login]<br />
<span class="description">'.__('Letters, numbers, hyphens, underscores, points and <em>@</em> only', 'membership-manager').'</span><br />
[error style="color: red;" login]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label password]'.__('Password', 'membership-manager').'[/label]</strong>*</td>
<td>[input password size=30] <span class="description">'.__('at least [membership-manager minimum-password-length] characters', 'membership-manager').'</span><br />
[error style="color: red;" password]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label first-name]'.__('First name', 'membership-manager').'[/label]</strong>*</td>
<td>[input first-name size=30 required=yes]<br />[error style="color: red;" first-name]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label last-name]'.__('Last name', 'membership-manager').'[/label]</strong>*</td>
<td>[input last-name size=30 required=yes]<br />[error style="color: red;" last-name]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label email-address]'.__('Email address', 'membership-manager').'[/label]</strong>*</td>
<td>[input email-address size=30]<br />[error style="color: red;" email-address]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label website-name]'.__('Website name', 'membership-manager').'[/label]</strong></td>
<td>[input website-name size=30]<br />[error style="color: red;" website-name]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label website-url]'.__('Website URL', 'membership-manager').'[/label]</strong></td>
<td>[input website-url size=30]<br />[error style="color: red;" website-url]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label address]'.__('Address', 'membership-manager').'[/label]</strong></td>
<td>[input address size=30]<br />[error style="color: red;" address]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label postcode]'.__('Postcode', 'membership-manager').'[/label]</strong></td>
<td>[input postcode size=30]<br />[error style="color: red;" postcode]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label town]'.__('Town', 'membership-manager').'[/label]</strong></td>
<td>[input town size=30]<br />[error style="color: red;" town]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label country]'.__('Country', 'membership-manager').'[/label]</strong></td>
<td>[input country size=30]<br />[error style="color: red;" country]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label phone-number]'.__('Phone number', 'membership-manager').'[/label]</strong></td>
<td>[input phone-number size=30]<br />[error style="color: red;" phone-number]</td></tr>
</table>
<div style="text-align: center;">[input submit value="'.__('Register', 'membership-manager').'"]</div>';


$initial_options['registration_compact_form_code'] =
'[validation-content][other]<p style="color: red;">[error unavailable-login] [error numeric-login] [error too-short-login] [error too-long-login] [error too-short-password] [error too-long-password] [error unavailable-email-address] [error unfilled-fields] [error invalid-captcha]</p>[/validation-content]

<table style="width: 100%;">
<tr style="vertical-align: top;"><td><strong>[label login]'.__('Login name', 'membership-manager').'[/label]</strong></td>
<td>[input login size=30] [indicator login]<br />
<span class="description">'.__('Letters, numbers, hyphens, underscores, points and <em>@</em> only', 'membership-manager').'</span><br />
[error style="color: red;" login]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label password]'.__('Password', 'membership-manager').'[/label]</strong></td>
<td>[input password size=30] <span class="description">'.__('at least [membership-manager minimum-password-length] characters', 'membership-manager').'</span><br />
[error style="color: red;" password]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label email-address]'.__('Email address', 'membership-manager').'[/label]</strong></td>
<td>[input email-address size=30]<br />[error style="color: red;" email-address]</td></tr>
</table>
<div style="text-align: center;">[input submit value="'.__('Register', 'membership-manager').'"]</div>';


$initial_options['registration_notification_email_body'] = $initial_options['login_notification_email_body'];


$initial_options['removal_custom_instructions'] = '';


$initial_options['removal_notification_email_body'] = $initial_options['deactivation_notification_email_body'];


$variables = array(
'displayed_columns',
'displayed_links',
'first_columns',
'last_columns',
'links',
'menu_displayed_items',
'menu_items',
'pages_titles',
'table',
'table_slug');
foreach ($variables as $variable) { if (isset($$variable)) { $original[$variable] = $$variable; unset($$variable); } }


include 'tables.php';
foreach ($tables as $table_slug => $table) {
switch ($table_slug) {
case 'members': $first_columns = array(
'id',
'login',
'first_name',
'last_name',
'email_address',
'website_name',
'members_areas',
'status',
'date'); break;
case 'members_areas': case 'members_areas_categories': $first_columns = array(
'id',
'name',
'description',
'keywords',
'members_areas',
'url',
'date'); break;
case 'members_categories': $first_columns = array(
'id',
'name',
'description',
'keywords',
'members_areas',
'date',
'date_utc'); }

$last_columns = array();
foreach ($table as $key => $value) {
if ((!in_array($key, $first_columns)) && ($value['name'] != '')) { $last_columns[] = $key; } }
$displayed_columns = array();
for ($i = 0; $i < count($first_columns); $i++) { $displayed_columns[] = $i; }

$initial_options[$table_slug] = array(
'columns' => array_merge($first_columns, $last_columns),
'columns_list_displayed' => 'yes',
'displayed_columns' => $displayed_columns,
'limit' => 10,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '',
'start_date' => '2011-01-01 00:00:00'); }


$initial_options['statistics'] = array(
'displayed_tables' => array(),
'filterby' => 'user_agent',
'start_date' => '2011-01-01 00:00:00',
'tables' => array('members', 'members_categories', 'members_areas', 'members_areas_categories'));


include 'admin-pages.php';
$links = array();
foreach ($admin_links as $key => $value) { $links[] = $key; }
$displayed_links = array();
for ($i = 0; $i < count($links); $i++) { $displayed_links[] = $i; }
$menu_items = array();
$pages_titles = array();
foreach ($admin_pages as $key => $value) {
$menu_items[] = $key;
$id = $_GET['id']; unset($_GET['id']);
$pages_titles[$key] = $value['menu_title'];
$_GET['id'] = $id; }
$menu_displayed_items = array();
foreach ($menu_items as $key => $value) {
if (!in_array($value, array(
'front_office',
'member_area_category',
'members_areas_categories',
'member_category',
'members_categories'))) {
$menu_displayed_items[] = $key; } }

$initial_options['back_office'] = array(
'back_office_page_summary_displayed' => 'yes',
'back_office_page_undisplayed_modules' => array('icon'),
'displayed_links' => $displayed_links,
'custom_icon_url' => MEMBERSHIP_MANAGER_URL.'images/icon.png',
'custom_icon_used' => 'no',
'front_office_page_summary_displayed' => 'yes',
'front_office_page_undisplayed_modules' => array(),
'links' => $links,
'links_displayed' => 'yes',
'member_area_category_page_summary_displayed' => 'yes',
'member_area_category_page_undisplayed_modules' => array(
	'activation-confirmation-email',
	'custom-instructions',
	'registration-as-a-client',
	'registration-to-affiliate-program'),
'member_area_page_summary_displayed' => 'yes',
'member_area_page_undisplayed_modules' => array(
	'activation-confirmation-email',
	'custom-instructions',
	'registration-as-a-client',
	'registration-to-affiliate-program'),
'member_category_page_summary_displayed' => 'no',
'member_category_page_undisplayed_modules' => array(),
'member_page_summary_displayed' => 'yes',
'member_page_undisplayed_modules' => array(
	'custom-instructions',
	'registration-as-a-client',
	'registration-to-affiliate-program'),
'menu_displayed' => 'yes',
'menu_displayed_items' => $menu_displayed_items,
'menu_items' => $menu_items,
'menu_title' => __('Membership', 'membership-manager'),
'minimum_roles' => array(
	'manage' => 'administrator',
	'view' => 'administrator'),
'options_page_summary_displayed' => 'yes',
'options_page_undisplayed_modules' => array(
	'activation-confirmation-email',
	'activation-notification-email',
	'captcha',
	'custom-instructions',
	'deactivation-notification-email',
	'login-notification-email',
	'logout-notification-email',
	'password-reset-notification-email',
	'profile-edit-notification-email',
	'registration-as-a-client',
	'registration-to-affiliate-program',
	'removal-notification-email',
	'urls-encryption'),
'pages_titles' => $pages_titles,
'statistics_page_undisplayed_columns' => array(),
'statistics_page_undisplayed_rows' => array(
	'members_categories',
	'members_areas_categories'),
'title' => 'Membership Manager',
'title_displayed' => 'yes');


foreach ($variables as $variable) { if (isset($original[$variable])) { $$variable = $original[$variable]; } }