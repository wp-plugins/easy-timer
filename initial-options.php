<?php include 'tables.php';
$admin_email = get_option('admin_email');
$blogname = get_option('blogname');
$siteurl = get_option('siteurl');


$initial_options[''] = array(
'activation_confirmation_email_receiver' => '[prospect email-address]',
'activation_confirmation_email_sender' => $blogname.' <'.$admin_email.'>',
'activation_confirmation_email_sent' => 'no',
'activation_confirmation_email_subject' => __('Download Your Gift', 'optin-manager'),
'activation_notification_email_receiver' => $admin_email,
'activation_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'activation_notification_email_sent' => 'no',
'activation_notification_email_subject' => __('Activation Of A Prospect', 'optin-manager'),
'affiliation_enabled' => 'no',
'autoresponder' => '',
'autoresponder_list' => '',
'commission_amount' => 1,
'commission2_amount' => 0.1,
'commission2_enabled' => 'no',
'encrypted_urls_key' => md5(mt_rand()),
'encrypted_urls_validity_duration' => 48,
'getresponse_api_key' => '',
'invalid_email_address_message' => __('This email address appears to be invalid.', 'optin-manager'),
'prospect_members_areas' => '',
'prospect_subscribed_to_members_areas' => 'no',
'prospects_initial_status' => 'inactive',
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
'sg_autorepondeur_account_id' => '',
'sg_autorepondeur_activation_code' => '',
'unfilled_field_message' => __('This field is required.', 'optin-manager'),
'version' => OPTIN_MANAGER_VERSION);


$initial_options['activation_confirmation_email_body'] =
__('Thank you for your registration', 'optin-manager').', [prospect first-name].

[optin-form instructions]

--
'.$blogname.'
'.HOME_URL;


$initial_options['activation_notification_email_body'] =
'[prospect first-name] [prospect last-name]

'.__('Email address:', 'optin-manager').' [prospect email-address]
'.__('Website name:', 'optin-manager').' [prospect website-name]
'.__('Website URL:', 'optin-manager').' [prospect website-url]
'.__('Autoresponder list:', 'optin-manager').' [prospect autoresponder-list]
'.__('Form:', 'optin-manager').' [optin-form name]

'.__('More informations about this prospect:', 'optin-manager').'

'.$siteurl.'/wp-admin/admin.php?page=optin-manager-prospect&id=[prospect id]';


include 'admin-pages.php';
$links = array();
foreach ($admin_links as $key => $value) { $links[] = $key; }
$displayed_links = array();
for ($i = 0; $i < count($links); $i++) { $displayed_links[] = $i; }
$menu_items = array();
foreach ($admin_pages as $key => $value) { $menu_items[] = $key; }
$menu_displayed_items = array();
foreach ($menu_items as $key => $value) {
if (!in_array($value, array(
'form_category',
'forms_categories'))) {
$menu_displayed_items[] = $key; } }

$initial_options['back_office'] = array(
'back_office_page_summary_displayed' => 'yes',
'back_office_page_undisplayed_modules' => array(),
'displayed_links' => $displayed_links,
'links' => $links,
'links_displayed' => 'yes',
'form_category_page_summary_displayed' => 'yes',
'form_category_page_undisplayed_modules' => array(),
'form_page_summary_displayed' => 'yes',
'form_page_undisplayed_modules' => array(),
'menu_displayed' => 'yes',
'menu_displayed_items' => $menu_displayed_items,
'menu_items' => $menu_items,
'options_page_summary_displayed' => 'yes',
'options_page_undisplayed_modules' => array(),
'prospect_page_summary_displayed' => 'yes',
'prospect_page_undisplayed_modules' => array(),
'statistics_page_undisplayed_columns' => array(),
'statistics_page_undisplayed_rows' => array(),
'title' => 'Optin Manager',
'title_displayed' => 'yes');


$initial_options['code'] =
'<p><label><strong>'.__('Your first name:', 'optin-manager').'</strong>[input* first-name size=20]</label></p>
<p><label><strong>'.__('Your email address:', 'optin-manager').'</strong>[input* email-address size=30]</label></p>
<div>[input submit text='.__('Validate', 'optin-manager').']</div>';


$first_columns = array(
'id',
'name',
'description',
'keywords',
'autoresponder',
'autoresponder_list',
'date');
$last_columns = array();
foreach ($tables['forms'] as $key => $value) {
if ((!in_array($key, $first_columns)) && ($value['name'] != '')) { $last_columns[] = $key; } }
$displayed_columns = array();
for ($i = 0; $i < count($first_columns); $i++) { $displayed_columns[] = $i; }

$initial_options['forms'] = array(
'columns' => array_merge($first_columns, $last_columns),
'displayed_columns' => $displayed_columns,
'limit' => 10,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '',
'start_date' => '2011-01-01');


$initial_options['forms_categories'] = $initial_options['forms'];


$first_columns = array(
'id',
'first_name',
'last_name',
'email_address',
'form_id',
'autoresponder_list',
'status',
'date');
$last_columns = array();
foreach ($tables['prospects'] as $key => $value) {
if ((!in_array($key, $first_columns)) && ($value['name'] != '')) { $last_columns[] = $key; } }
$displayed_columns = array();
for ($i = 0; $i < count($first_columns); $i++) { $displayed_columns[] = $i; }

$initial_options['prospects'] = array(
'columns' => array_merge($first_columns, $last_columns),
'displayed_columns' => $displayed_columns,
'limit' => 10,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '',
'start_date' => '2011-01-01');


$initial_options['registration_confirmation_email_body'] = $initial_options['activation_confirmation_email_body'];


$initial_options['registration_custom_instructions'] = '';


$initial_options['registration_notification_email_body'] = $initial_options['activation_notification_email_body'];


$initial_options['statistics'] = array(
'displayed_tables' => array(),
'filterby' => 'autoresponder_list',
'start_date' => '2011-01-01',
'tables' => array('prospects', 'forms', 'forms_categories'));