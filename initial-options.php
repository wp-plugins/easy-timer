<?php include 'tables.php';
$admin_email = get_option('admin_email');
$blogname = get_option('blogname');
$siteurl = get_option('siteurl');


$initial_options[''] = array(
'activation_notification_email_receiver' => '[affiliate email-address]',
'activation_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'activation_notification_email_subject' => __('Activation Of Your Affiliate Account', 'affiliation-manager'),
'affiliate_autoresponder' => '',
'affiliate_autoresponder_list' => '',
'affiliate_members_areas' => '',
'affiliate_subscribed_to_autoresponder' => 'no',
'affiliate_subscribed_to_members_areas' => 'no',
'affiliates_initial_category_id' => 0,
'affiliates_initial_status' => 'active',
'affiliation_enabled' => 'yes',
'bonus_proposal_email_receiver' => $admin_email,
'bonus_proposal_email_sender' => '[affiliate first-name] [affiliate last-name] <[affiliate email-address]>',
'bonus_proposal_email_subject' => __('Bonus Proposal', 'affiliation-manager'),
'clicks_registration_enabled' => 'yes',
'commission_amount' => 10,
'commission_payment' => 'deferred',
'commission_percentage' => 50,
'commission_type' => 'proportional',
'commission2_amount' => 1,
'commission2_enabled' => 'yes',
'commission2_percentage' => 5,
'commission2_type' => 'proportional',
'cookies_lifetime' => 180,
'cookies_name' => 'a',
'deactivation_notification_email_receiver' => '[affiliate email-address]',
'deactivation_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'deactivation_notification_email_subject' => __('Deactivation Of Your Affiliate Account', 'affiliation-manager'),
'first_sale_winner' => 'affiliator',
'maximum_clicks_quantity' => 'unlimited',
'maximum_login_length' => 16,
'maximum_password_length' => 32,
'minimum_login_length' => 1,
'minimum_password_length' => 5,
'password_reset_email_receiver' => '[affiliate email-address]',
'password_reset_email_sender' => $blogname.' <'.$admin_email.'>',
'password_reset_email_subject' => __('Your New Password', 'affiliation-manager'),
'registration_confirmation_email_receiver' => '[affiliate email-address]',
'registration_confirmation_email_sender' => $blogname.' <'.$admin_email.'>',
'registration_confirmation_email_sent' => 'yes',
'registration_confirmation_email_subject' => __('Your Registration To Our Affiliate Program', 'affiliation-manager'),
'registration_confirmation_url' => HOME_URL,
'registration_custom_instructions_executed' => 'no',
'registration_notification_email_receiver' => $admin_email,
'registration_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'registration_notification_email_sent' => 'yes',
'registration_notification_email_subject' => __('Registration Of An Affiliate', 'affiliation-manager').' ([affiliate login])',
'registration_required' => 'yes',
'removal_notification_email_receiver' => '[affiliate email-address]',
'removal_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'removal_notification_email_subject' => __('Removal Of Your Affiliate Account', 'affiliation-manager'),
'url_variable_name' => 'a',
'url_variable_name2' => 'e',
'version' => AFFILIATION_MANAGER_VERSION,
'winner_affiliate' => 'last');


$initial_options['activation_notification_email_body'] =
__('Hi', 'affiliation-manager').', [affiliate first-name].

'.__('Your affiliate account has been activated.', 'affiliation-manager').' '.__('You can login from this page:', 'affiliation-manager').'

'.HOME_URL.'

--
'.$blogname.'
'.HOME_URL;


$first_columns = array(
'id',
'login',
'first_name',
'last_name',
'email_address',
'website_name',
'status',
'date',
'referrer');
$last_columns = array();
foreach ($tables['affiliates'] as $key => $value) {
if ((!in_array($key, $first_columns)) && ($value['name'] != '')) { $last_columns[] = $key; } }
$displayed_columns = array();
for ($i = 0; $i < count($first_columns); $i++) { $displayed_columns[] = $i; }

$initial_options['affiliates'] = array(
'columns' => array_merge($first_columns, $last_columns),
'displayed_columns' => $displayed_columns,
'limit' => 10,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '',
'start_date' => '2011-01-01');


$first_columns = array(
'id',
'name',
'description',
'commission_amount',
'commission_percentage',
'commission2_amount',
'commission2_percentage');
$last_columns = array();
foreach ($tables['affiliates_categories'] as $key => $value) {
if ((!in_array($key, $first_columns)) && ($value['name'] != '')) { $last_columns[] = $key; } }
$displayed_columns = array();
for ($i = 0; $i < count($first_columns); $i++) { $displayed_columns[] = $i; }

$initial_options['affiliates_categories'] = array(
'columns' => array_merge($first_columns, $last_columns),
'displayed_columns' => $displayed_columns,
'limit' => 10,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '',
'start_date' => '2011-01-01');


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
'affiliate_category',
'affiliates_categories',
'prospects_commissions',
'recurring_commissions'))) {
$menu_displayed_items[] = $key; } }

$initial_options['back_office'] = array(
'affiliate_category_page_summary_displayed' => 'yes',
'affiliate_category_page_undisplayed_modules' => array(),
'affiliate_page_summary_displayed' => 'yes',
'affiliate_page_undisplayed_modules' => array(),
'back_office_page_summary_displayed' => 'yes',
'back_office_page_undisplayed_modules' => array(),
'displayed_links' => $displayed_links,
'links' => $links,
'links_displayed' => 'yes',
'menu_displayed' => 'yes',
'menu_displayed_items' => $menu_displayed_items,
'menu_items' => $menu_items,
'options_page_summary_displayed' => 'yes',
'options_page_undisplayed_modules' => array(),
'statistics_page_undisplayed_columns' => array(),
'statistics_page_undisplayed_rows' => array('prospects_commissions', 'paid_prospects_commissions', 'unpaid_prospects_commissions', 'prospects_commissions2', 'paid_prospects_commissions2', 'unpaid_prospects_commissions2', 'prospects'),
'title' => 'Affiliation Manager',
'title_displayed' => 'yes');


$initial_options['bonus_proposal_email_body'] =
__('Hi', 'affiliation-manager').',

'.__('I wish to offer this bonus to customers who order through my affiliate link:', 'affiliation-manager').'

[affiliate bonus-download-url]

'.__('Instructions to the customer:', 'affiliation-manager').'

[affiliate bonus-instructions]

'.__('Thank you for add this bonus to my affiliate profile if it suits you:', 'affiliation-manager').'

'.$siteurl.'/wp-admin/admin.php?page=affiliation-manager-affiliate&id=[affiliate id]#bonus-offered-to-customer';


$first_columns = array(
'id',
'referrer',
'url',
'ip_address',
'user_agent',
'referring_url',
'date',
'date_utc');
$last_columns = array();
foreach ($tables['clicks'] as $key => $value) {
if ((!in_array($key, $first_columns)) && ($value['name'] != '')) { $last_columns[] = $key; } }
$displayed_columns = array();
for ($i = 0; $i < count($first_columns); $i++) { $displayed_columns[] = $i; }

$initial_options['clicks'] = array(
'columns' => array_merge($first_columns, $last_columns),
'displayed_columns' => $displayed_columns,
'limit' => 10,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '',
'start_date' => '2011-01-01');


$first_columns = array(
'id',
'referrer',
'date',
'product_id',
'commission_amount',
'commission_payment',
'commission_status',
'commission_payment_date');
$last_columns = array();
foreach ($tables['commissions'] as $key => $value) {
if ((!in_array($key, $first_columns)) && ($value['name'] != '')) { $last_columns[] = $key; } }
$displayed_columns = array();
for ($i = 0; $i < count($first_columns); $i++) { $displayed_columns[] = $i; }

$initial_options['commissions'] = array(
'columns' => array_merge($first_columns, $last_columns),
'displayed_columns' => $displayed_columns,
'limit' => 10,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '',
'start_date' => '2011-01-01');


$initial_options['deactivation_notification_email_body'] =
__('Hi', 'affiliation-manager').', [affiliate first-name].



--
'.$blogname.'
'.HOME_URL;


$initial_options['password_reset_email_body'] =
__('Hi', 'affiliation-manager').', [affiliate first-name].

'.__('Here are your new login informations:', 'affiliation-manager').'

'.__('Your login:', 'affiliation-manager').' [affiliate login]
'.__('Your password:', 'affiliation-manager').' [affiliate password]

'.__('You can login from this page:', 'affiliation-manager').'

'.HOME_URL.'

--
'.$blogname.'
'.HOME_URL;


$initial_options['payment'] = array(
'filterby' => 'referrer',
'start_date' => '2011-01-01');


$first_columns = array(
'id',
'referrer',
'date',
'form_id',
'autoresponder_list',
'commission_amount',
'commission_status',
'commission_payment_date');
$last_columns = array();
foreach ($tables['prospects_commissions'] as $key => $value) {
if ((!in_array($key, $first_columns)) && ($value['name'] != '')) { $last_columns[] = $key; } }
$displayed_columns = array();
for ($i = 0; $i < count($first_columns); $i++) { $displayed_columns[] = $i; }

$initial_options['prospects_commissions'] = array(
'columns' => array_merge($first_columns, $last_columns),
'displayed_columns' => $displayed_columns,
'limit' => 10,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '',
'start_date' => '2011-01-01');


$first_columns = array(
'id',
'referrer',
'date',
'product_id',
'commission_amount',
'commission_payment',
'commission_status',
'commission_payment_date');
$last_columns = array();
foreach ($tables['recurring_commissions'] as $key => $value) {
if ((!in_array($key, $first_columns)) && ($value['name'] != '')) { $last_columns[] = $key; } }
$displayed_columns = array();
for ($i = 0; $i < count($first_columns); $i++) { $displayed_columns[] = $i; }

$initial_options['recurring_commissions'] = array(
'columns' => array_merge($first_columns, $last_columns),
'displayed_columns' => $displayed_columns,
'limit' => 10,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '',
'start_date' => '2011-01-01');


$initial_options['registration_confirmation_email_body'] =
__('Thank you for your registration to our affiliate program', 'affiliation-manager').', [affiliate first-name].
'.__('You can login from this page:', 'affiliation-manager').'

'.HOME_URL.'

'.__('Your login name:', 'affiliation-manager').' [affiliate login]
'.__('Your password:', 'affiliation-manager').' [affiliate password]
'.__('Your PayPal email address:', 'affiliation-manager').' [affiliate paypal-email-address]

'.__('To receive your commissions, you need a Premier or Business PayPal account. Upgrade your PayPal account if you have a Personal account.', 'affiliation-manager').'

--
'.$blogname.'
'.HOME_URL;


$initial_options['registration_custom_instructions'] = '';


$initial_options['registration_notification_email_body'] =
'[affiliate first-name] [affiliate last-name]

'.__('Login name:', 'affiliation-manager').' [affiliate login]
'.__('Email address:', 'affiliation-manager').' [affiliate email-address]
'.__('PayPal email address:', 'affiliation-manager').' [affiliate paypal-email-address]
'.__('Website name:', 'affiliation-manager').' [affiliate website-name]
'.__('Website URL:', 'affiliation-manager').' [affiliate website-url]

'.__('More informations about this affiliate:', 'affiliation-manager').'

'.$siteurl.'/wp-admin/admin.php?page=affiliation-manager-affiliate&id=[affiliate id]';


$initial_options['removal_notification_email_body'] = $initial_options['deactivation_notification_email_body'];


$initial_options['statistics'] = array(
'displayed_tables' => array(),
'filterby' => 'referrer',
'start_date' => '2011-01-01',
'tables' => array('commissions', 'recurring_commissions', 'prospects_commissions', 'affiliates', 'affiliates_categories', 'clicks'));