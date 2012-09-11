<?php foreach (array('admin_email', 'blogname', 'siteurl') as $key) { $$key = get_option($key); }


$initial_options[''] = array(
'activation_confirmation_email_receiver' => '[prospect email-address]',
'activation_confirmation_email_sender' => $blogname.' <'.$admin_email.'>',
'activation_confirmation_email_sent' => 'no',
'activation_confirmation_email_subject' => __('Download Your Gift', 'optin-manager'),
'activation_custom_instructions_executed' => 'no',
'activation_notification_email_receiver' => $admin_email,
'activation_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'activation_notification_email_sent' => 'no',
'activation_notification_email_subject' => __('Activation Of A Prospect', 'optin-manager').' ([prospect autoresponder-list])',
'affiliation_enabled' => 'no',
'affiliation_registration_confirmation_email_sent' => '',
'affiliation_registration_notification_email_sent' => '',
'automatic_display_enabled' => 'no',
'automatic_display_form_id' => 1,
'automatic_display_location' => 'top',
'autoresponder' => 'AWeber',
'autoresponder_list' => '',
'commerce_registration_confirmation_email_sent' => '',
'commerce_registration_notification_email_sent' => '',
'commission_amount' => 1,
'commission2_amount' => 0.1,
'commission2_enabled' => 'no',
'deactivation_custom_instructions_executed' => 'no',
'default_captcha_type' => 'recaptcha',
'default_recaptcha_theme' => 'red',
'encrypted_urls_key' => md5(mt_rand()),
'encrypted_urls_validity_duration' => 48,
'getresponse_api_key' => '',
'invalid_captcha_message' => __('The code you entered for the CAPTCHA is incorrect.', 'optin-manager'),
'invalid_email_address_message' => __('This email address appears to be invalid.', 'optin-manager'),
'mailchimp_api_key' => '',
'maximum_prospects_quantity' => 'unlimited',
'membership_registration_confirmation_email_sent' => '',
'membership_registration_notification_email_sent' => '',
'prospect_affiliate_category_id' => '',
'prospect_affiliate_status' => '',
'prospect_client_category_id' => '',
'prospect_client_status' => '',
'prospect_member_category_id' => '',
'prospect_member_status' => '',
'prospect_members_areas' => '',
'prospect_subscribed_as_a_client' => 'no',
'prospect_subscribed_as_a_user' => 'no',
'prospect_subscribed_to_affiliate_program' => 'no',
'prospect_subscribed_to_members_areas' => 'no',
'prospect_user_role' => 'subscriber',
'prospects_initial_status' => 'inactive',
'prospects_registration_enabled' => 'yes',
'recaptcha_private_key' => '',
'recaptcha_public_key' => '',
'registration_confirmation_email_receiver' => '[prospect email-address]',
'registration_confirmation_email_sender' => $blogname.' <'.$admin_email.'>',
'registration_confirmation_email_sent' => 'no',
'registration_confirmation_email_subject' => __('Your Registration', 'optin-manager'),
'registration_confirmation_url' => HOME_URL,
'registration_custom_instructions_executed' => 'no',
'registration_notification_email_receiver' => $admin_email,
'registration_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'registration_notification_email_sent' => 'yes',
'registration_notification_email_subject' => __('Registration Of A Prospect', 'optin-manager').' ([prospect autoresponder-list])',
'removal_custom_instructions_executed' => 'no',
'sg_autorepondeur_account_id' => '',
'sg_autorepondeur_activation_code' => '',
'unfilled_field_message' => __('This field is required.', 'optin-manager'),
'unfilled_fields_message' => __('Please fill out the required fields.', 'optin-manager'),
'version' => OPTIN_MANAGER_VERSION);


$initial_options['activation_confirmation_email_body'] =
__('Thank you for your registration', 'optin-manager').', [prospect first-name].

[optin-form instructions]

--
'.$blogname.'
'.HOME_URL;


$initial_options['activation_custom_instructions'] = '';


$initial_options['activation_notification_email_body'] =
'[prospect first-name] [prospect last-name]

'.__('Email address:', 'optin-manager').' [prospect email-address]
'.__('Website name:', 'optin-manager').' [prospect website-name]
'.__('Website URL:', 'optin-manager').' [prospect website-url]
'.__('Autoresponder list:', 'optin-manager').' [prospect autoresponder-list]
'.__('Form:', 'optin-manager').' [optin-form name]

'.__('More informations about this prospect:', 'optin-manager').'

'.$siteurl.'/wp-admin/admin.php?page=optin-manager-prospect&id=[prospect id]';


include 'libraries/captchas.php';
$initial_options['captchas_numbers'] = $captchas_numbers;


$initial_options['code'] =
'[validation-content]<p style="color: green;">'.__('Thank you for your registration. Check your emails.', 'optin-manager').'</p>
[other]<p style="color: red;">[error unfilled-fields] [error invalid-captcha]</p>[/validation-content]

<p><label><strong>'.__('Your first name:', 'optin-manager').'</strong> [input first-name size=20]<br />
[error style="color: red;" first-name]</label></p>
<p><label><strong>'.__('Your email address:', 'optin-manager').'</strong> [input email-address size=30]<br />
[error style="color: red;" email-address]</label></p>
<div>[input submit value="'.__('Submit', 'optin-manager').'"]</div>';


$initial_options['deactivation_custom_instructions'] = '';


$initial_options['registration_confirmation_email_body'] = $initial_options['activation_confirmation_email_body'];


$initial_options['registration_custom_instructions'] = '';


$initial_options['registration_notification_email_body'] = $initial_options['activation_notification_email_body'];


$initial_options['removal_custom_instructions'] = '';


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
case 'forms': case 'forms_categories': $first_columns = array(
'id',
'name',
'description',
'keywords',
'autoresponder',
'autoresponder_list',
'date'); break;
case 'prospects': $first_columns = array(
'id',
'first_name',
'last_name',
'email_address',
'form_id',
'autoresponder_list',
'status',
'date'); }

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
'filterby' => 'autoresponder_list',
'start_date' => '2011-01-01 00:00:00',
'tables' => array('prospects', 'forms', 'forms_categories'));


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
'form_category',
'forms_categories'))) {
$menu_displayed_items[] = $key; } }

$initial_options['back_office'] = array(
'back_office_page_summary_displayed' => 'yes',
'back_office_page_undisplayed_modules' => array('icon'),
'displayed_links' => $displayed_links,
'custom_icon_url' => OPTIN_MANAGER_URL.'images/icon.png',
'custom_icon_used' => 'no',
'links' => $links,
'links_displayed' => 'yes',
'form_category_page_summary_displayed' => 'yes',
'form_category_page_undisplayed_modules' => array(
	'activation-confirmation-email',
	'activation-notification-email',
	'affiliation',
	'custom-instructions',
	'membership',
	'registration-as-a-client',
	'registration-to-affiliate-program',
	'wordpress'),
'form_page_summary_displayed' => 'yes',
'form_page_undisplayed_modules' => array(
	'activation-confirmation-email',
	'activation-notification-email',
	'affiliation',
	'custom-instructions',
	'membership',
	'registration-as-a-client',
	'registration-to-affiliate-program',
	'wordpress'),
'menu_displayed' => 'yes',
'menu_displayed_items' => $menu_displayed_items,
'menu_items' => $menu_items,
'menu_title' => __('Optin', 'optin-manager'),
'minimum_roles' => array(
	'manage' => 'administrator',
	'view' => 'administrator'),
'options_page_summary_displayed' => 'yes',
'options_page_undisplayed_modules' => array(
	'activation-confirmation-email',
	'activation-notification-email',
	'affiliation',
	'captcha',
	'custom-instructions',
	'membership',
	'registration-as-a-client',
	'registration-to-affiliate-program',
	'wordpress'),
'pages_titles' => $pages_titles,
'prospect_page_summary_displayed' => 'yes',
'prospect_page_undisplayed_modules' => array(
	'affiliation',
	'custom-instructions',
	'membership',
	'registration-as-a-client',
	'registration-to-affiliate-program',
	'wordpress'),
'statistics_page_undisplayed_columns' => array(),
'statistics_page_undisplayed_rows' => array('forms_categories'),
'title' => 'Optin Manager',
'title_displayed' => 'yes');


foreach ($variables as $variable) { if (isset($original[$variable])) { $$variable = $original[$variable]; } }