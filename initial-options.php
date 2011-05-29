<?php $admin_email = get_option('admin_email');
$blogname = get_option('blogname');
$siteurl = get_option('siteurl');


$affiliation_manager_initial_options = array(
'affiliate_autoresponder' => '',
'affiliate_autoresponder2' => '',
'affiliate_autoresponder_list' => '',
'affiliate_autoresponder_list2' => '',
'affiliate_subscribed_to_autoresponder' => 'no',
'affiliate_subscribed_to_autoresponder2' => 'no',
'affiliation_enabled' => 'yes',
'commission_amount' => 10,
'commission_payment' => 'deferred',
'commission_percentage' => 50,
'commission_type' => 'proportional',
'cookies_lifetime' => 180,
'cookies_name' => 'a',
'email_sent_to_affiliate' => 'yes',
'email_sent_to_affiliator' => 'yes',
'email_to_affiliate_receiver' => '[affiliate first-name] [affiliate last-name] <[affiliate email-address]>',
'email_to_affiliate_sender' => $blogname.' <'.$admin_email.'>',
'email_to_affiliate_subject' => __('Your Registration To Our Affiliate Program', 'affiliation-manager'),
'email_to_affiliator_receiver' => $blogname.' <'.$admin_email.'>',
'email_to_affiliator_sender' => $blogname.' <'.$admin_email.'>',
'email_to_affiliator_subject' => __('Registration Of An Affiliate', 'affiliation-manager').' ([affiliate login])',
'first_sale_winner' => 'affiliator',
'maximum_login_length' => 16,
'maximum_password_length' => 32,
'minimum_login_length' => 1,
'minimum_password_length' => 5,
'minimum_payout_amount' => 0,
'password_reset_email_receiver' => '[affiliate first-name] [affiliate last-name] <[affiliate email-address]>',
'password_reset_email_sender' => $blogname.' <'.$admin_email.'>',
'password_reset_email_subject' => __('Your New Password', 'affiliation-manager'),
'registration_confirmation_url' => HOME_URL,
'registration_required' => 'no',
'url_variable_name' => 'a',
'url_variable_name2' => 'e',
'winner_affiliate' => 'last');


$affiliation_manager_initial_email_to_affiliate_body =
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


$affiliation_manager_initial_email_to_affiliator_body =
'[affiliate first-name] [affiliate last-name]

'.__('Login name:', 'affiliation-manager').' [affiliate login]
'.__('Email address:', 'affiliation-manager').' [affiliate email-address]
'.__('PayPal email address:', 'affiliation-manager').' [affiliate paypal-email-address]
'.__('Website name:', 'affiliation-manager').' [affiliate website-name]
'.__('Website URL:', 'affiliation-manager').' [affiliate website-url]

'.__('More informations about this affiliate', 'affiliation-manager').':

'.$siteurl.'/wp-admin/admin.php?page=affiliation-manager-affiliate&id=[affiliate id]';


$affiliation_manager_initial_password_reset_email_body =
__('Hi', 'affiliation-manager').', [affiliate first-name].

'.__('Here are your new login informations:', 'affiliation-manager').'

'.__('Your login:', 'affiliation-manager').' [affiliate login]
'.__('Your password:', 'affiliation-manager').' [affiliate password]

'.__('You can login from this page:', 'affiliation-manager').'

'.HOME_URL.'

--
'.$blogname.'
'.HOME_URL;


$affiliation_manager_affiliates_initial_options = array(
'columns' => array(
'id',
'login',
'first_name',
'last_name',
'email_address',
'website_name',
'date',
'referrer',
'paypal_email_address',
'website_url',
'address',
'postcode',
'town',
'country',
'phone_number',
'commission_amount',
'commission_percentage',
'date_utc',
'user_agent',
'ip_address',
'referring_url'),
'columns_number' => 8,
'limit' => 20,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '');


$affiliation_manager_clicks_initial_options = array(
'columns' => array(
'id',
'referrer',
'date',
'date_utc',
'user_agent',
'ip_address',
'url',
'referring_url'),
'columns_number' => 8,
'limit' => 20,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '');


$affiliation_manager_commissions_initial_options = array(
'columns' => array(
'id',
'referrer',
'date',
'product_id',
'commission_amount',
'commission_payment',
'commission_status',
'commission_payment_date',
'first_name',
'last_name',
'email_address',
'website_name',
'website_url',
'address',
'postcode',
'town',
'country',
'phone_number',
'date_utc',
'user_agent',
'ip_address',
'referring_url',
'quantity',
'price',
'tax',
'tax_included_in_price',
'shipping_cost',
'amount',
'payment_mode',
'transaction_number',
'instructions',
'shipping_address',
'status',
'refund_date',
'refund_date_utc',
'commission_payment_date_utc'),
'columns_number' => 8,
'limit' => 20,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '');


$affiliation_manager_statistics_initial_options = array(
'filterby' => 'referrer',
'start_date' => '2011-01-01',
'tables' => array('commissions', 'affiliates', 'clicks'),
'tables_number' => 3);