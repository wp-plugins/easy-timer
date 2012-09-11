<?php foreach (array('admin_email', 'blogname', 'siteurl') as $key) { $$key = get_option($key); }


$initial_options[''] = array(
'activation_confirmation_email_receiver' => '[affiliate email-address]',
'activation_confirmation_email_sender' => $blogname.' <'.$admin_email.'>',
'activation_confirmation_email_sent' => 'yes',
'activation_confirmation_email_subject' => __('Activation Of Your Affiliate Account', 'affiliation-manager'),
'activation_confirmation_url' => HOME_URL,
'activation_custom_instructions_executed' => 'no',
'activation_notification_email_receiver' => '[affiliate email-address]',
'activation_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'activation_notification_email_subject' => __('Activation Of Your Affiliate Account', 'affiliation-manager'),
'affiliate_autoresponder' => 'AWeber',
'affiliate_autoresponder_list' => '',
'affiliate_client_category_id' => '',
'affiliate_client_status' => '',
'affiliate_member_category_id' => '',
'affiliate_member_status' => '',
'affiliate_members_areas' => '',
'affiliate_subscribed_as_a_client' => 'no',
'affiliate_subscribed_as_a_user' => 'no',
'affiliate_subscribed_to_autoresponder' => 'no',
'affiliate_subscribed_to_members_areas' => 'no',
'affiliate_user_role' => 'subscriber',
'affiliates_initial_category_id' => 0,
'affiliates_initial_status' => 'active',
'affiliation_enabled' => 'yes',
'available_login_indicator_message' => '<span style="color: green;">'.__('Available', 'affiliation-manager').'</span>',
'bonus_proposal_custom_instructions_executed' => 'no',
'bonus_proposal_email_receiver' => $admin_email,
'bonus_proposal_email_sender' => '[affiliate first-name] [affiliate last-name] <[affiliate email-address]>',
'bonus_proposal_email_subject' => __('Bonus Proposal', 'affiliation-manager'),
'click_custom_instructions_executed' => 'no',
'clicks_registration_enabled' => 'yes',
'commerce_registration_confirmation_email_sent' => '',
'commerce_registration_notification_email_sent' => '',
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
'deactivation_custom_instructions_executed' => 'no',
'deactivation_notification_email_receiver' => '[affiliate email-address]',
'deactivation_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'deactivation_notification_email_subject' => __('Deactivation Of Your Affiliate Account', 'affiliation-manager'),
'default_captcha_type' => 'recaptcha',
'default_recaptcha_theme' => 'red',
'encrypted_urls_key' => md5(mt_rand()),
'encrypted_urls_validity_duration' => 48,
'first_sale_winner' => 'affiliator',
'getresponse_api_key' => '',
'inactive_account_message' => __('Your account is inactive.', 'affiliation-manager'),
'inexistent_email_address_message' => __('This email address does not match an affiliate account.', 'affiliation-manager'),
'invalid_captcha_message' => __('The code you entered for the CAPTCHA is incorrect.', 'affiliation-manager'),
'invalid_email_address_message' => __('This email address appears to be invalid.', 'affiliation-manager'),
'invalid_login_or_password_message' => __('Invalid login or password', 'affiliation-manager'),
'login_custom_instructions_executed' => 'no',
'login_notification_email_receiver' => $admin_email,
'login_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'login_notification_email_sent' => 'no',
'login_notification_email_subject' => __('An Affiliate Has Logged In', 'affiliation-manager').' ([affiliate login])',
'logout_custom_instructions_executed' => 'no',
'logout_notification_email_receiver' => $admin_email,
'logout_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'logout_notification_email_sent' => 'no',
'logout_notification_email_subject' => __('An Affiliate Has Logged Out', 'affiliation-manager').' ([affiliate login])',
'mailchimp_api_key' => '',
'maximum_clicks_quantity' => 20000,
'maximum_login_length' => 16,
'maximum_password_length' => 32,
'membership_registration_confirmation_email_sent' => '',
'membership_registration_notification_email_sent' => '',
'minimum_login_length' => 1,
'minimum_password_length' => 5,
'numeric_login_message' => __('Your login name must be a non-numeric string.', 'affiliation-manager'),
'password_reset_custom_instructions_executed' => 'no',
'password_reset_email_receiver' => '[affiliate email-address]',
'password_reset_email_sender' => $blogname.' <'.$admin_email.'>',
'password_reset_email_subject' => __('Your New Password', 'affiliation-manager'),
'password_reset_notification_email_receiver' => $admin_email,
'password_reset_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'password_reset_notification_email_sent' => 'yes',
'password_reset_notification_email_subject' => __('An Affiliate Has Reset His Password', 'affiliation-manager').' ([affiliate login])',
'profile_edit_custom_instructions_executed' => 'no',
'profile_edit_notification_email_receiver' => $admin_email,
'profile_edit_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'profile_edit_notification_email_sent' => 'yes',
'profile_edit_notification_email_subject' => __('An Affiliate Has Edited His Profile', 'affiliation-manager').' ([affiliate login])',
'recaptcha_private_key' => '',
'recaptcha_public_key' => '',
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
'removal_custom_instructions_executed' => 'no',
'removal_notification_email_receiver' => '[affiliate email-address]',
'removal_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'removal_notification_email_subject' => __('Removal Of Your Affiliate Account', 'affiliation-manager'),
'sg_autorepondeur_account_id' => '',
'sg_autorepondeur_activation_code' => '',
'too_long_login_message' => __('Your login name must contain at most [affiliation-manager maximum-login-length] characters.', 'affiliation-manager'),
'too_long_password_message' => __('Your password must contain at most [affiliation-manager maximum-password-length] characters.', 'affiliation-manager'),
'too_short_login_message' => __('Your login name must contain at least [affiliation-manager minimum-login-length] characters.', 'affiliation-manager'),
'too_short_password_message' => __('Your password must contain at least [affiliation-manager minimum-password-length] characters.', 'affiliation-manager'),
'unavailable_email_address_message' => __('This email address is not available.', 'affiliation-manager'),
'unavailable_login_indicator_message' => '<span style="color: red;">'.__('Unavailable', 'affiliation-manager').'</span>',
'unavailable_login_message' => __('This login name is not available.', 'affiliation-manager'),
'unavailable_paypal_email_address_message' => __('This PayPal email address is not available.', 'affiliation-manager'),
'unfilled_field_message' => __('This field is required.', 'affiliation-manager'),
'unfilled_fields_message' => __('Please fill out the required fields.', 'affiliation-manager'),
'url_variable_name' => 'a',
'url_variable_name2' => 'e',
'version' => AFFILIATION_MANAGER_VERSION,
'winner_affiliate' => 'last');


$initial_options['activation_confirmation_email_body'] =
__('Hi', 'affiliation-manager').' [affiliate first-name],

'.__('Thanks for activating your affiliate account.', 'affiliation-manager').' '.__('You can login from this page:', 'affiliation-manager').'

'.HOME_URL.'

--
'.$blogname.'
'.HOME_URL;


$initial_options['activation_custom_instructions'] = '';


$initial_options['activation_notification_email_body'] =
__('Hi', 'affiliation-manager').' [affiliate first-name],

'.__('Your affiliate account has been activated.', 'affiliation-manager').' '.__('You can login from this page:', 'affiliation-manager').'

'.HOME_URL.'

--
'.$blogname.'
'.HOME_URL;


$initial_options['affiliate_notification_email_body'] =
__('Hi', 'affiliation-manager').' [referrer first-name],

'.__('You just refer an affiliate.', 'affiliation-manager').'

'.__('Login name:', 'affiliation-manager').' [affiliate login]

--
'.$blogname.'
'.HOME_URL;


$initial_options['affiliates_statistics_code'] =
'<h3 id="affiliates-statistics">'.__('Affiliates Statistics', 'affiliation-manager').'</h3>

[if affiliate]
<table style="width: 100%;">
<tr style="vertical-align: top;">
<th>'.__('Date', 'affiliation-manager').'</th>
<th>'.__('Login name', 'affiliation-manager').'</th>
<th>'.__('First name', 'affiliation-manager').'</th>
<th>'.__('Last name', 'affiliation-manager').'</th>
</tr>
[foreach affiliate]
<tr style="vertical-align: top;">
<td>[affiliate date]</td>
<td>[affiliate login]</td>
<td>[affiliate first-name]</td>
<td>[affiliate last-name]</td>
</tr>[/foreach]
</table>
[else]<p>'.__('No affiliates', 'affiliation-manager').'</p>[/if]';


$initial_options['bonus_proposal_email_body'] =
__('Hi', 'affiliation-manager').',

'.__('I wish to offer this bonus to customers who order through my affiliate link:', 'affiliation-manager').'

[affiliate bonus-download-url]

'.__('Instructions to the customer:', 'affiliation-manager').'

[affiliate bonus-instructions]

'.__('Thank you for add this bonus to my affiliate profile if it suits you:', 'affiliation-manager').'

'.$siteurl.'/wp-admin/admin.php?page=affiliation-manager-affiliate&id=[affiliate id]#bonus-offered-to-customer';


$initial_options['bonus_proposal_form'] = array(
'invalid_captcha_message' => '',
'unfilled_field_message' => '',
'unfilled_fields_message' => '');


$initial_options['bonus_proposal_custom_instructions'] = '';


$initial_options['bonus_proposal_form_code'] =
'[validation-content]<p style="color: green;">'.__('Your bonus proposal has been sent successfully.', 'affiliation-manager').'</p>
[other]<p style="color: red;">[error unfilled-fields] [error invalid-captcha]</p>[/validation-content]

<div style="text-align: center;">
<p><label><strong>'.__('Bonus download URL:', 'affiliation-manager').'</strong><br />
[input bonus-download-url size=60]<br />
[error style="color: red;" bonus-download-url]</label></p>
<p><label><strong>'.__('Instructions to the customer:', 'affiliation-manager').'</strong><br />
[textarea bonus-instructions cols="60" rows="5" required=yes][/textarea]<br />
[error style="color: red;" bonus-instructions]</label></p>
<div>[input submit value="'.__('Send the proposal', 'affiliation-manager').'"]</div>
</div>';


include 'libraries/captchas.php';
$initial_options['captchas_numbers'] = $captchas_numbers;


$initial_options['click_custom_instructions'] = '';


$initial_options['clicks_statistics_code'] =
'<h3 id="clicks-statistics">'.__('Clicks Statistics', 'affiliation-manager').'</h3>

[if click]
<table style="width: 100%;">
<tr style="vertical-align: top;">
<th>'.__('Date', 'affiliation-manager').'</th>
<th>'.__('URL', 'affiliation-manager').'</th>
<th>'.__('Referring URL', 'affiliation-manager').'</th>
</tr>
[foreach click]
<tr style="vertical-align: top;">
<td>[click date]</td>
<td><a href="[click url filter=htmlspecialchars]">[click url filter=htmlspecialchars]</a></td>
<td><a href="[click referring-url filter=htmlspecialchars]">[click referring-url filter=htmlspecialchars]</a></td>
</tr>[/foreach]
</table>
[else]<p>'.__('No clicks', 'affiliation-manager').'</p>[/if]';


$initial_options['client_notification_email_body'] =
__('Hi', 'affiliation-manager').' [referrer first-name],

'.__('You just refer a client.', 'affiliation-manager').'

'.__('Login name:', 'affiliation-manager').' [client login]

--
'.$blogname.'
'.HOME_URL;


$initial_options['clients_statistics_code'] =
'<h3 id="clients-statistics">'.__('Clients Statistics', 'affiliation-manager').'</h3>

[if client]
<table style="width: 100%;">
<tr style="vertical-align: top;">
<th>'.__('Date', 'affiliation-manager').'</th>
<th>'.__('Login name', 'affiliation-manager').'</th>
<th>'.__('First name', 'affiliation-manager').'</th>
<th>'.__('Last name', 'affiliation-manager').'</th>
</tr>
[foreach client]
<tr style="vertical-align: top;">
<td>[client date]</td>
<td>[client login]</td>
<td>[client first-name]</td>
<td>[client last-name]</td>
</tr>[/foreach]
</table>
[else]<p>'.__('No clients', 'affiliation-manager').'</p>[/if]';


$initial_options['commissions1_statistics_code'] =
'<h3 id="commissions1-statistics">'.__('Commissions Statistics', 'affiliation-manager').' '.__('(Level 1)', 'affiliation-manager').'</h3>

[if commission1]
<table style="width: 100%;">
<tr style="vertical-align: top;">
<th>'.__('Date', 'affiliation-manager').'</th>
<th>'.__('Product', 'affiliation-manager').'</th>
<th>'.__('Amount', 'affiliation-manager').'</th>
<th>'.__('Status', 'affiliation-manager').'</th>
</tr>
[foreach commission1]
<tr style="vertical-align: top;">
<td>[order date]</td>
<td><a href="[product url filter=htmlspecialchars]">[product name]</a></td>
<td>[order commission-amount] [commerce-manager currency-code]</td>
<td class="[order commission-status]">[order commission-status filter=ucfirst/i18n]</td>
</tr>[/foreach]
</table>
[else]<p>'.__('No commissions', 'affiliation-manager').'</p>[/if]';


$initial_options['commissions2_statistics_code'] =
'<h3 id="commissions2-statistics">'.__('Commissions Statistics', 'affiliation-manager').' '.__('(Level 2)', 'affiliation-manager').'</h3>

[if commission2]
<table style="width: 100%;">
<tr style="vertical-align: top;">
<th>'.__('Date', 'affiliation-manager').'</th>
<th>'.__('Product', 'affiliation-manager').'</th>
<th>'.__('Amount', 'affiliation-manager').'</th>
<th>'.__('Status', 'affiliation-manager').'</th>
</tr>
[foreach commission2]
<tr style="vertical-align: top;">
<td>[order date]</td>
<td><a href="[product url filter=htmlspecialchars]">[product name]</a></td>
<td>[order commission2-amount] [commerce-manager currency-code]</td>
<td class="[order commission2-status]">[order commission2-status filter=ucfirst/i18n]</td>
</tr>[/foreach]
</table>
[else]<p>'.__('No commissions', 'affiliation-manager').'</p>[/if]';


$initial_options['date_picker_css'] = '<link rel="stylesheet" type="text/css" media="screen" href="'.AFFILIATION_MANAGER_URL.'libraries/date-picker.css" />';


$initial_options['date_picker_js'] =
'<script type="text/javascript" src="'.AFFILIATION_MANAGER_URL.'libraries/date-picker.js"></script>
<script type="text/javascript">
Date.dayNames = [\''.__('Sunday', 'affiliation-manager').'\', \''.__('Monday', 'affiliation-manager').'\', \''.__('Tuesday', 'affiliation-manager').'\', \''.__('Wednesday', 'affiliation-manager').'\', \''.__('Thursday', 'affiliation-manager').'\', \''.__('Friday', 'affiliation-manager').'\', \''.__('Saturday', 'affiliation-manager').'\'];
Date.abbrDayNames = [\''.__('Sun', 'affiliation-manager').'\', \''.__('Mon', 'affiliation-manager').'\', \''.__('Tue', 'affiliation-manager').'\', \''.__('Wed', 'affiliation-manager').'\', \''.__('Thu', 'affiliation-manager').'\', \''.__('Fri', 'affiliation-manager').'\', \''.__('Sat', 'affiliation-manager').'\'];
Date.monthNames = [\''.__('January', 'affiliation-manager').'\', \''.__('February', 'affiliation-manager').'\', \''.__('March', 'affiliation-manager').'\', \''.__('April', 'affiliation-manager').'\', \''.__('May', 'affiliation-manager').'\', \''.__('June', 'affiliation-manager').'\', \''.__('July', 'affiliation-manager').'\', \''.__('August', 'affiliation-manager').'\', \''.__('September', 'affiliation-manager').'\', \''.__('October', 'affiliation-manager').'\', \''.__('November', 'affiliation-manager').'\', \''.__('December', 'affiliation-manager').'\'];
Date.abbrMonthNames = [\''.__('Jan', 'affiliation-manager').'\', \''.__('Feb', 'affiliation-manager').'\', \''.__('Mar', 'affiliation-manager').'\', \''.__('Apr', 'affiliation-manager').'\', \''.__('May', 'affiliation-manager').'\', \''.__('Jun', 'affiliation-manager').'\', \''.__('Jul', 'affiliation-manager').'\', \''.__('Aug', 'affiliation-manager').'\', \''.__('Sep', 'affiliation-manager').'\', \''.__('Oct', 'affiliation-manager').'\', \''.__('Nov', 'affiliation-manager').'\', \''.__('Dec', 'affiliation-manager').'\'];
$.dpText = {
TEXT_PREV_YEAR : \''.__('Previous year', 'affiliation-manager').'\',
TEXT_PREV_MONTH : \''.__('Previous month', 'affiliation-manager').'\',
TEXT_NEXT_YEAR : \''.__('Next year', 'affiliation-manager').'\',
TEXT_NEXT_MONTH : \''.__('Next month', 'affiliation-manager').'\',
TEXT_CLOSE : \''.__('Close', 'affiliation-manager').'\',
TEXT_CHOOSE_DATE : \''.__('Choose a date', 'affiliation-manager').'\',
DATE_PICKER_ALT : \''.__('Date', 'affiliation-manager').'\',
DATE_PICKER_URL : \''.AFFILIATION_MANAGER_URL.'images/date-picker.png\',
HEADER_FORMAT : \'mmmm yyyy\'
}; $(function(){ $(\'.date-pick\').datePicker({startDate:\'2011-01-01\'}); });
</script>';


$initial_options['deactivation_custom_instructions'] = '';


$initial_options['deactivation_notification_email_body'] =
__('Hi', 'affiliation-manager').' [affiliate first-name],



--
'.$blogname.'
'.HOME_URL;


$initial_options['global_statistics_code'] =
'<table style="width: 100%;"><tbody>
<tr style="vertical-align: top;"><td><strong>'.__('Number of clicks', 'affiliation-manager').'</strong></td>
<td>[referrer-counter data=clicks range=form][number][/referrer-counter]</td></tr>
<tr style="vertical-align: top;"><td><strong>'.__('Commissions total amount', 'affiliation-manager').' '.__('(level 1)', 'affiliation-manager').'</strong></td>
<td>[referrer-counter data=orders-commission-amount range=form][number][/referrer-counter] [commerce-manager currency-code]</td></tr>
<tr style="vertical-align: top;"><td><strong>'.__('Paid commissions total amount', 'affiliation-manager').' '.__('(level 1)', 'affiliation-manager').'</strong></td>
<td>[referrer-counter data=orders-commission-paid-amount range=form][number][/referrer-counter] [commerce-manager currency-code]</td></tr>
<tr style="vertical-align: top;"><td><strong>'.__('Unpaid commissions total amount', 'affiliation-manager').' '.__('(level 1)', 'affiliation-manager').'</strong></td>
<td>[referrer-counter data=orders-commission-unpaid-amount range=form][number][/referrer-counter] [commerce-manager currency-code]</td></tr>
<tr style="vertical-align: top;"><td><strong>'.__('Commissions total amount', 'affiliation-manager').' '.__('(level 2)', 'affiliation-manager').'</strong></td>
<td>[referrer-counter data=orders-commission2-amount range=form][number][/referrer-counter] [commerce-manager currency-code]</td></tr>
<tr style="vertical-align: top;"><td><strong>'.__('Paid commissions total amount', 'affiliation-manager').' '.__('(level 2)', 'affiliation-manager').'</strong></td>
<td>[referrer-counter data=orders-commission2-paid-amount range=form][number][/referrer-counter] [commerce-manager currency-code]</td></tr>
<tr style="vertical-align: top;"><td><strong>'.__('Unpaid commissions total amount', 'affiliation-manager').' '.__('(level 2)', 'affiliation-manager').'</strong></td>
<td>[referrer-counter data=orders-commission2-unpaid-amount range=form][number][/referrer-counter] [commerce-manager currency-code]</td></tr>
</tbody></table>';


$initial_options['instant_notifications'] = array(
'affiliate_notification_email_deactivated' => 'no',
'affiliate_notification_email_receiver' => '[referrer email-address]',
'affiliate_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'affiliate_notification_email_sent' => 'no',
'affiliate_notification_email_subject' => __('Registration Of An Affiliate', 'affiliation-manager'),
'client_notification_email_deactivated' => 'no',
'client_notification_email_receiver' => '[referrer email-address]',
'client_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'client_notification_email_sent' => 'no',
'client_notification_email_subject' => __('Registration Of A Client', 'affiliation-manager'),
'message_notification_email_deactivated' => 'no',
'message_notification_email_receiver' => '[referrer email-address]',
'message_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'message_notification_email_sent' => 'no',
'message_notification_email_subject' => __('Sending Of A Message', 'affiliation-manager'),
'order_notification_email_deactivated' => 'no',
'order_notification_email_receiver' => '[referrer email-address]',
'order_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'order_notification_email_sent' => 'no',
'order_notification_email_subject' => __('Order Notification', 'affiliation-manager'),
'prospect_notification_email_deactivated' => 'no',
'prospect_notification_email_receiver' => '[referrer email-address]',
'prospect_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'prospect_notification_email_sent' => 'no',
'prospect_notification_email_subject' => __('Registration Of A Prospect', 'affiliation-manager'),
'recurring_payment_notification_email_deactivated' => 'no',
'recurring_payment_notification_email_receiver' => '[referrer email-address]',
'recurring_payment_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'recurring_payment_notification_email_sent' => 'no',
'recurring_payment_notification_email_subject' => __('Recurring Payment Notification', 'affiliation-manager'));


$initial_options['instant_notifications_form'] = array(
'invalid_captcha_message' => '',
'unfilled_field_message' => '',
'unfilled_fields_message' => '');


$initial_options['instant_notifications_form_code'] =
'[validation-content]<p style="color: green;">'.__('Your preferences has been changed successfully.', 'affiliation-manager').'</p>
[other]<p style="color: red;">[error unfilled-fields] [error invalid-captcha]</p>[/validation-content]

<table style="width: 100%;">

<tr style="vertical-align: top;"><td><strong>[label affiliate-notification-email-sent]'.__('Send me an email when I refer an affiliate', 'affiliation-manager').'[/label]</strong></td>
<td>[select affiliate-notification-email-sent]
[option value=yes]'.__('Yes', 'affiliation-manager').'[/option]
[option value=no]'.__('No', 'affiliation-manager').'[/option]
[/select]</td></tr>

<tr style="vertical-align: top;"><td><strong>[label client-notification-email-sent]'.__('Send me an email when I refer a client', 'affiliation-manager').'[/label]</strong></td>
<td>[select client-notification-email-sent]
[option value=yes]'.__('Yes', 'affiliation-manager').'[/option]
[option value=no]'.__('No', 'affiliation-manager').'[/option]
[/select]</td></tr>

<tr style="vertical-align: top;"><td><strong>[label order-notification-email-sent]'.__('Send me an email when I refer an order', 'affiliation-manager').'[/label]</strong></td>
<td>[select order-notification-email-sent]
[option value=yes]'.__('Yes', 'affiliation-manager').'[/option]
[option value=no]'.__('No', 'affiliation-manager').'[/option]
[option value="if commission"]'.__('If the amount of commission is not 0', 'affiliation-manager').'[/option]
[/select]</td></tr>

<tr style="vertical-align: top;"><td><strong>[label recurring-payment-notification-email-sent]'.__('Send me an email when I refer a recurring payment', 'affiliation-manager').'[/label]</strong></td>
<td>[select recurring-payment-notification-email-sent]
[option value=yes]'.__('Yes', 'affiliation-manager').'[/option]
[option value=no]'.__('No', 'affiliation-manager').'[/option]
[option value="if commission"]'.__('If the amount of commission is not 0', 'affiliation-manager').'[/option]
[/select]</td></tr>

<tr style="vertical-align: top;"><td><strong>[label prospect-notification-email-sent]'.__('Send me an email when I refer a prospect', 'affiliation-manager').'[/label]</strong></td>
<td>[select prospect-notification-email-sent]
[option value=yes]'.__('Yes', 'affiliation-manager').'[/option]
[option value=no]'.__('No', 'affiliation-manager').'[/option]
[option value="if commission"]'.__('If the amount of commission is not 0', 'affiliation-manager').'[/option]
[/select]</td></tr>

<tr style="vertical-align: top;"><td><strong>[label message-notification-email-sent]'.__('Send me an email when I refer a message', 'affiliation-manager').'[/label]</strong></td>
<td>[select message-notification-email-sent]
[option value=yes]'.__('Yes', 'affiliation-manager').'[/option]
[option value=no]'.__('No', 'affiliation-manager').'[/option]
[option value="if commission"]'.__('If the amount of commission is not 0', 'affiliation-manager').'[/option]
[/select]</td></tr>

</table>
<div style="text-align: center;">[input submit value="'.__('Submit', 'affiliation-manager').'"]</div>';


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
<tr style="vertical-align: top;"><td style="width: 40%;"><strong>[label login]'.__('Login name', 'affiliation-manager').'[/label]</strong></td>
<td style="width: 60%;">[input login size=20]<br />[error style="color: red;" login]</td></tr>
<tr style="vertical-align: top;"><td style="width: 40%;"><strong>[label password]'.__('Password', 'affiliation-manager').'[/label]</strong></td>
<td style="width: 60%;">[input password size=20]<br />[error style="color: red;" password]</td></tr>
</table>
<p style="margin: 0.75em; text-align: center;"><label>[input remember value=yes] '.__('Remember me', 'affiliation-manager').'</label></p>
<div style="text-align: center;">[input submit value="'.__('Login', 'affiliation-manager').'"]</div>';


$initial_options['login_compact_form_code'] =
'[validation-content][other]<p style="color: red;">[error invalid-login-or-password] [error inactive-account] [error invalid-captcha]</p>[/validation-content]

<p><strong>[label login]'.__('Login name:', 'affiliation-manager').'[/label]</strong><br />
[input login size=20]<br />[error style="color: red;" login]</p>
<p><strong>[label password]'.__('Password:', 'affiliation-manager').'[/label]</strong><br />
[input password size=20]<br />[error style="color: red;" password]</p>
<p><label>[input remember value=yes] '.__('Remember me', 'affiliation-manager').'</label></p>
<div style="text-align: center;">[input submit value="'.__('Login', 'affiliation-manager').'"]</div>';


$initial_options['login_notification_email_body'] =
'[affiliate first-name] [affiliate last-name]

'.__('Login name:', 'affiliation-manager').' [affiliate login]
'.__('Email address:', 'affiliation-manager').' [affiliate email-address]
'.__('PayPal email address:', 'affiliation-manager').' [affiliate paypal-email-address]
'.__('Website name:', 'affiliation-manager').' [affiliate website-name]
'.__('Website URL:', 'affiliation-manager').' [affiliate website-url]

'.__('More informations about this affiliate:', 'affiliation-manager').'

'.$siteurl.'/wp-admin/admin.php?page=affiliation-manager-affiliate&id=[affiliate id]';


$initial_options['logout_custom_instructions'] = '';


$initial_options['logout_notification_email_body'] = $initial_options['login_notification_email_body'];


$initial_options['message_notification_email_body'] =
__('Hi', 'affiliation-manager').' [referrer first-name],

'.__('You just refer a message.', 'affiliation-manager').'

'.__('Form:', 'affiliation-manager').' [contact-form name]
'.__('Commission amount:', 'affiliation-manager').' [message commission-amount] [commerce-manager currency-code]

--
'.$blogname.'
'.HOME_URL;


$initial_options['messages_commissions1_statistics_code'] =
'<h3 id="messages-commissions1-statistics">'.__('Messages Commissions Statistics', 'affiliation-manager').' '.__('(Level 1)', 'affiliation-manager').'</h3>

[if message-commission1]
<table style="width: 100%;">
<tr style="vertical-align: top;">
<th>'.__('Date', 'affiliation-manager').'</th>
<th>'.__('Form', 'affiliation-manager').'</th>
<th>'.__('Amount', 'affiliation-manager').'</th>
<th>'.__('Status', 'affiliation-manager').'</th>
</tr>
[foreach message-commission1]
<tr style="vertical-align: top;">
<td>[message date]</td>
<td>[contact-form name]</td>
<td>[message commission-amount] [commerce-manager currency-code]</td>
<td class="[message commission-status]">[message commission-status filter=ucfirst/i18n]</td>
</tr>[/foreach]
</table>
[else]<p>'.__('No commissions', 'affiliation-manager').'</p>[/if]';


$initial_options['messages_commissions2_statistics_code'] =
'<h3 id="messages-commissions2-statistics">'.__('Messages Commissions Statistics', 'affiliation-manager').' '.__('(Level 2)', 'affiliation-manager').'</h3>

[if message-commission2]
<table style="width: 100%;">
<tr style="vertical-align: top;">
<th>'.__('Date', 'affiliation-manager').'</th>
<th>'.__('Form', 'affiliation-manager').'</th>
<th>'.__('Amount', 'affiliation-manager').'</th>
<th>'.__('Status', 'affiliation-manager').'</th>
</tr>
[foreach message-commission2]
<tr style="vertical-align: top;">
<td>[message date]</td>
<td>[contact-form name]</td>
<td>[message commission2-amount] [commerce-manager currency-code]</td>
<td class="[message commission2-status]">[message commission2-status filter=ucfirst/i18n]</td>
</tr>[/foreach]
</table>
[else]<p>'.__('No commissions', 'affiliation-manager').'</p>[/if]';


$initial_options['messages_statistics_code'] =
'<h3 id="messages-statistics">'.__('Messages Statistics', 'affiliation-manager').'</h3>

[if message]
<table style="width: 100%;">
<tr style="vertical-align: top;">
<th>'.__('Date', 'affiliation-manager').'</th>
<th>'.__('Form', 'affiliation-manager').'</th>
</tr>
[foreach message]
<tr style="vertical-align: top;">
<td>[message date]</td>
<td>[contact-form name]</td>
</tr>[/foreach]
</table>
[else]<p>'.__('No messages', 'affiliation-manager').'</p>[/if]';


$initial_options['meta_widget'] = array(
'title' => __('Affiliation', 'affiliation-manager'),
'content' => '[affiliation-content]
<ul>
<li><a href="'.AFFILIATION_MANAGER_URL.'?action=logout">'.__('Log out').'</a></li>
</ul>
[other][affiliation-login-compact-form]
[/affiliation-content]');


$initial_options['order_notification_email_body'] =
__('Hi', 'affiliation-manager').' [referrer first-name],

'.__('You just refer an order.', 'affiliation-manager').'

'.__('Product:', 'affiliation-manager').' [product name]
'.__('Amount:', 'affiliation-manager').' [order amount] [commerce-manager currency-code]
'.__('Commission amount:', 'affiliation-manager').' [order commission-amount] [commerce-manager currency-code]

--
'.$blogname.'
'.HOME_URL;


$initial_options['orders_statistics_code'] =
'<h3 id="orders-statistics">'.__('Orders Statistics', 'affiliation-manager').'</h3>

[if order]
<table style="width: 100%;">
<tr style="vertical-align: top;">
<th>'.__('Date', 'affiliation-manager').'</th>
<th>'.__('Product', 'affiliation-manager').'</th>
<th>'.__('Amount', 'affiliation-manager').'</th>
</tr>
[foreach order]
<tr style="vertical-align: top;">
<td>[order date]</td>
<td><a href="[product url filter=htmlspecialchars]">[product name]</a></td>
<td>[order amount] [commerce-manager currency-code]</td>
</tr>[/foreach]
</table>
[else]<p>'.__('No orders', 'affiliation-manager').'</p>[/if]';


$initial_options['password_reset_custom_instructions'] = '';


$initial_options['password_reset_email_body'] =
__('Hi', 'affiliation-manager').' [affiliate first-name],

'.__('Here are your new login informations:', 'affiliation-manager').'

'.__('Your login name:', 'affiliation-manager').' [affiliate login]
'.__('Your password:', 'affiliation-manager').' [affiliate password]

'.__('You can login from this page:', 'affiliation-manager').'

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
'[validation-content]<p style="color: green;">'.__('Your password has been reset successfully.', 'affiliation-manager').'</p>
[other]<p style="color: red;">[error inexistent-email-address] [error invalid-captcha]</p>[/validation-content]

<div style="text-align: center;">
<p><label><strong>'.__('Your email address:', 'affiliation-manager').'</strong><br />
[input email-address size=40]<br />
[error style="color: red;" email-address]</label></p>
<div>[input submit value="'.__('Reset', 'affiliation-manager').'"]</div>
</div>';


$initial_options['password_reset_notification_email_body'] = $initial_options['login_notification_email_body'];


$initial_options['payment'] = array(
'filterby' => 'referrer',
'start_date' => '2011-01-01 00:00:00');


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
'unavailable_paypal_email_address_message' => '',
'unfilled_field_message' => '',
'unfilled_fields_message' => '');


$initial_options['profile_form_code'] =
'[validation-content]<p style="color: green;">'.__('Your profile has been changed successfully.', 'affiliation-manager').'</p>
[other]<p style="color: red;">[error unavailable-login] [error numeric-login] [error too-short-login] [error too-long-login] [error too-short-password] [error too-long-password] [error unavailable-email-address] [error unavailable-paypal-email-address] [error unfilled-fields] [error invalid-captcha]</p>[/validation-content]

<table style="width: 100%;">
<tr style="vertical-align: top;"><td><strong>[label login]'.__('Login name', 'affiliation-manager').'[/label]</strong>*</td>
<td>[input login size=30] [indicator login]<br />
<span class="description">'.__('Letters, numbers, hyphens and underscores only', 'affiliation-manager').'<br />
'.__('If you change your login name, your current affiliate links will not work.', 'affiliation-manager').'</span><br />
[error style="color: red;" login]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label password]'.__('Password', 'affiliation-manager').'[/label]</strong></td>
<td>[input password size=30]<br /><span class="description">'.__('(if you want to change it)', 'affiliation-manager').'</span><br />
[error style="color: red;" password]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label first-name]'.__('First name', 'affiliation-manager').'[/label]</strong>*</td>
<td>[input first-name size=30 required=yes]<br />[error style="color: red;" first-name]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label last-name]'.__('Last name', 'affiliation-manager').'[/label]</strong>*</td>
<td>[input last-name size=30 required=yes]<br />[error style="color: red;" last-name]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label email-address]'.__('Email address', 'affiliation-manager').'[/label]</strong>*</td>
<td>[input email-address size=30 required=yes]<br />[error style="color: red;" email-address]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label paypal-email-address]'.__('PayPal email address', 'affiliation-manager').'[/label]</strong>*</td>
<td>[input paypal-email-address size=30]<br />[error style="color: red;" paypal-email-address]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label website-name]'.__('Website name', 'affiliation-manager').'[/label]</strong></td>
<td>[input website-name size=30]<br />[error style="color: red;" website-name]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label website-url]'.__('Website URL', 'affiliation-manager').'[/label]</strong></td>
<td>[input website-url size=30]<br />[error style="color: red;" website-url]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label address]'.__('Address', 'affiliation-manager').'[/label]</strong></td>
<td>[input address size=30]<br />[error style="color: red;" address]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label postcode]'.__('Postcode', 'affiliation-manager').'[/label]</strong></td>
<td>[input postcode size=30]<br />[error style="color: red;" postcode]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label town]'.__('Town', 'affiliation-manager').'[/label]</strong></td>
<td>[input town size=30]<br />[error style="color: red;" town]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label country]'.__('Country', 'affiliation-manager').'[/label]</strong></td>
<td>[input country size=30]<br />[error style="color: red;" country]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label phone-number]'.__('Phone number', 'affiliation-manager').'[/label]</strong></td>
<td>[input phone-number size=30]<br />[error style="color: red;" phone-number]</td></tr>
</table>
<div style="text-align: center;">[input submit value="'.__('Submit', 'affiliation-manager').'"]</div>';


$initial_options['prospect_notification_email_body'] =
__('Hi', 'affiliation-manager').' [referrer first-name],

'.__('You just refer a prospect.', 'affiliation-manager').'

'.__('Autoresponder list:', 'affiliation-manager').' [prospect autoresponder-list]
'.__('Form:', 'affiliation-manager').' [optin-form name]
'.__('Commission amount:', 'affiliation-manager').' [prospect commission-amount] [commerce-manager currency-code]

--
'.$blogname.'
'.HOME_URL;


$initial_options['prospects_commissions1_statistics_code'] =
'<h3 id="prospects-commissions1-statistics">'.__('Prospects Commissions Statistics', 'affiliation-manager').' '.__('(Level 1)', 'affiliation-manager').'</h3>

[if prospect-commission1]
<table style="width: 100%;">
<tr style="vertical-align: top;">
<th>'.__('Date', 'affiliation-manager').'</th>
<th>'.__('Form', 'affiliation-manager').'</th>
<th>'.__('Amount', 'affiliation-manager').'</th>
<th>'.__('Status', 'affiliation-manager').'</th>
</tr>
[foreach prospect-commission1]
<tr style="vertical-align: top;">
<td>[prospect date]</td>
<td>[optin-form name]</td>
<td>[prospect commission-amount] [commerce-manager currency-code]</td>
<td class="[prospect commission-status]">[prospect commission-status filter=ucfirst/i18n]</td>
</tr>[/foreach]
</table>
[else]<p>'.__('No commissions', 'affiliation-manager').'</p>[/if]';


$initial_options['prospects_commissions2_statistics_code'] =
'<h3 id="prospects-commissions2-statistics">'.__('Prospects Commissions Statistics', 'affiliation-manager').' '.__('(Level 2)', 'affiliation-manager').'</h3>

[if prospect-commission2]
<table style="width: 100%;">
<tr style="vertical-align: top;">
<th>'.__('Date', 'affiliation-manager').'</th>
<th>'.__('Form', 'affiliation-manager').'</th>
<th>'.__('Amount', 'affiliation-manager').'</th>
<th>'.__('Status', 'affiliation-manager').'</th>
</tr>
[foreach prospect-commission2]
<tr style="vertical-align: top;">
<td>[prospect date]</td>
<td>[optin-form name]</td>
<td>[prospect commission2-amount] [commerce-manager currency-code]</td>
<td class="[prospect commission2-status]">[prospect commission2-status filter=ucfirst/i18n]</td>
</tr>[/foreach]
</table>
[else]<p>'.__('No commissions', 'affiliation-manager').'</p>[/if]';


$initial_options['prospects_statistics_code'] =
'<h3 id="prospects-statistics">'.__('Prospects Statistics', 'affiliation-manager').'</h3>

[if prospect]
<table style="width: 100%;">
<tr style="vertical-align: top;">
<th>'.__('Date', 'affiliation-manager').'</th>
<th>'.__('Form', 'affiliation-manager').'</th>
</tr>
[foreach prospect]
<tr style="vertical-align: top;">
<td>[prospect date]</td>
<td>[optin-form name]</td>
</tr>[/foreach]
</table>
[else]<p>'.__('No prospects', 'affiliation-manager').'</p>[/if]';


$initial_options['recurring_commissions1_statistics_code'] =
'<h3 id="recurring-commissions1-statistics">'.__('Recurring Commissions Statistics', 'affiliation-manager').' '.__('(Level 1)', 'affiliation-manager').'</h3>

[if recurring-commission1]
<table style="width: 100%;">
<tr style="vertical-align: top;">
<th>'.__('Date', 'affiliation-manager').'</th>
<th>'.__('Product', 'affiliation-manager').'</th>
<th>'.__('Amount', 'affiliation-manager').'</th>
<th>'.__('Status', 'affiliation-manager').'</th>
</tr>
[foreach recurring-commission1]
<tr style="vertical-align: top;">
<td>[recurring-payment date]</td>
<td><a href="[product url filter=htmlspecialchars]">[product name]</a></td>
<td>[recurring-payment commission-amount] [commerce-manager currency-code]</td>
<td class="[recurring-payment commission-status]">[recurring-payment commission-status filter=ucfirst/i18n]</td>
</tr>[/foreach]
</table>
[else]<p>'.__('No commissions', 'affiliation-manager').'</p>[/if]';


$initial_options['recurring_commissions2_statistics_code'] =
'<h3 id="recurring-commissions2-statistics">'.__('Recurring Commissions Statistics', 'affiliation-manager').' '.__('(Level 2)', 'affiliation-manager').'</h3>

[if recurring-commission2]
<table style="width: 100%;">
<tr style="vertical-align: top;">
<th>'.__('Date', 'affiliation-manager').'</th>
<th>'.__('Product', 'affiliation-manager').'</th>
<th>'.__('Amount', 'affiliation-manager').'</th>
<th>'.__('Status', 'affiliation-manager').'</th>
</tr>
[foreach recurring-commission2]
<tr style="vertical-align: top;">
<td>[recurring-payment date]</td>
<td><a href="[product url filter=htmlspecialchars]">[product name]</a></td>
<td>[recurring-payment commission2-amount] [commerce-manager currency-code]</td>
<td class="[recurring-payment commission2-status]">[recurring-payment commission2-status filter=ucfirst/i18n]</td>
</tr>[/foreach]
</table>
[else]<p>'.__('No commissions', 'affiliation-manager').'</p>[/if]';


$initial_options['recurring_payment_notification_email_body'] =
__('Hi', 'affiliation-manager').' [referrer first-name],

'.__('You just refer a recurring payment.', 'affiliation-manager').'

'.__('Product:', 'affiliation-manager').' [product name]
'.__('Amount:', 'affiliation-manager').' [recurring-payment amount] [commerce-manager currency-code]
'.__('Commission amount:', 'affiliation-manager').' [recurring-payment commission-amount] [commerce-manager currency-code]

--
'.$blogname.'
'.HOME_URL;


$initial_options['recurring_payments_statistics_code'] =
'<h3 id="recurring-payments-statistics">'.__('Recurring Payments Statistics', 'affiliation-manager').'</h3>

[if recurring-payment]
<table style="width: 100%;">
<tr style="vertical-align: top;">
<th>'.__('Date', 'affiliation-manager').'</th>
<th>'.__('Product', 'affiliation-manager').'</th>
<th>'.__('Amount', 'affiliation-manager').'</th>
</tr>
[foreach recurring-payment]
<tr style="vertical-align: top;">
<td>[recurring-payment date]</td>
<td><a href="[product url filter=htmlspecialchars]">[product name]</a></td>
<td>[recurring-payment amount] [commerce-manager currency-code]</td>
</tr>[/foreach]
</table>
[else]<p>'.__('No payments', 'affiliation-manager').'</p>[/if]';


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


$initial_options['registration_form'] = $initial_options['profile_form'];


$initial_options['registration_compact_form'] = $initial_options['registration_form'];


$initial_options['registration_form_code'] =
'[validation-content][other]<p style="color: red;">[error unavailable-login] [error numeric-login] [error too-short-login] [error too-long-login] [error too-short-password] [error too-long-password] [error unavailable-email-address] [error unavailable-paypal-email-address] [error unfilled-fields] [error invalid-captcha]</p>[/validation-content]

<table style="width: 100%;">
<tr style="vertical-align: top;"><td><strong>[label login]'.__('Login name', 'affiliation-manager').'[/label]</strong>*</td>
<td>[input login size=30] [indicator login]<br />
<span class="description">'.__('Letters, numbers, hyphens and underscores only', 'affiliation-manager').'<br />
'.__('Your login name will be included in your affiliate links.', 'affiliation-manager').'</span><br />
[error style="color: red;" login]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label password]'.__('Password', 'affiliation-manager').'[/label]</strong>*</td>
<td>[input password size=30] <span class="description">'.__('at least [affiliation-manager minimum-password-length] characters', 'affiliation-manager').'</span><br />
[error style="color: red;" password]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label first-name]'.__('First name', 'affiliation-manager').'[/label]</strong>*</td>
<td>[input first-name size=30 required=yes]<br />[error style="color: red;" first-name]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label last-name]'.__('Last name', 'affiliation-manager').'[/label]</strong>*</td>
<td>[input last-name size=30 required=yes]<br />[error style="color: red;" last-name]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label email-address]'.__('Email address', 'affiliation-manager').'[/label]</strong>*</td>
<td>[input email-address size=30]<br />[error style="color: red;" email-address]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label paypal-email-address]'.__('PayPal email address', 'affiliation-manager').'[/label]</strong>*</td>
<td>[input paypal-email-address size=30]<br />[error style="color: red;" paypal-email-address]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label website-name]'.__('Website name', 'affiliation-manager').'[/label]</strong></td>
<td>[input website-name size=30]<br />[error style="color: red;" website-name]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label website-url]'.__('Website URL', 'affiliation-manager').'[/label]</strong></td>
<td>[input website-url size=30]<br />[error style="color: red;" website-url]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label address]'.__('Address', 'affiliation-manager').'[/label]</strong></td>
<td>[input address size=30]<br />[error style="color: red;" address]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label postcode]'.__('Postcode', 'affiliation-manager').'[/label]</strong></td>
<td>[input postcode size=30]<br />[error style="color: red;" postcode]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label town]'.__('Town', 'affiliation-manager').'[/label]</strong></td>
<td>[input town size=30]<br />[error style="color: red;" town]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label country]'.__('Country', 'affiliation-manager').'[/label]</strong></td>
<td>[input country size=30]<br />[error style="color: red;" country]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label phone-number]'.__('Phone number', 'affiliation-manager').'[/label]</strong></td>
<td>[input phone-number size=30]<br />[error style="color: red;" phone-number]</td></tr>
</table>
<div style="text-align: center;">[input submit value="'.__('Register', 'affiliation-manager').'"]</div>';


$initial_options['registration_compact_form_code'] =
'[validation-content][other]<p style="color: red;">[error unavailable-login] [error numeric-login] [error too-short-login] [error too-long-login] [error too-short-password] [error too-long-password] [error unavailable-email-address] [error unavailable-paypal-email-address] [error unfilled-fields] [error invalid-captcha]</p>[/validation-content]

<table style="width: 100%;">
<tr style="vertical-align: top;"><td><strong>[label login]'.__('Login name', 'affiliation-manager').'[/label]</strong></td>
<td>[input login size=30] [indicator login]<br />
<span class="description">'.__('Letters, numbers, hyphens and underscores only', 'affiliation-manager').'<br />
'.__('Your login name will be included in your affiliate links.', 'affiliation-manager').'</span><br />
[error style="color: red;" login]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label password]'.__('Password', 'affiliation-manager').'[/label]</strong></td>
<td>[input password size=30] <span class="description">'.__('at least [affiliation-manager minimum-password-length] characters', 'affiliation-manager').'</span><br />
[error style="color: red;" password]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label email-address]'.__('Email address', 'affiliation-manager').'[/label]</strong></td>
<td>[input email-address size=30]<br />[error style="color: red;" email-address]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label paypal-email-address]'.__('PayPal email address', 'affiliation-manager').'[/label]</strong></td>
<td>[input paypal-email-address size=30]<br />[error style="color: red;" paypal-email-address]</td></tr>
</table>
<div style="text-align: center;">[input submit value="'.__('Register', 'affiliation-manager').'"]</div>';


$initial_options['registration_notification_email_body'] = $initial_options['login_notification_email_body'];


$initial_options['removal_custom_instructions'] = '';


$initial_options['removal_notification_email_body'] = $initial_options['deactivation_notification_email_body'];


$initial_options['statistics_form_code'] =
'<div style="text-align: center;">
<p><label style="margin-left: 3em;"><strong>'.__('Start', 'affiliation-manager').'</strong>
[input start-date size=10 style="margin: 0.5em;"]</label>
<label style="margin-left: 3em;"><strong>'.__('End', 'affiliation-manager').'</strong>
[input end-date size=10 style="margin: 0.5em;"]</label></p>
<div>[input submit value="'.__('Display', 'affiliation-manager').'"]</div>
</div>';


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
case 'affiliates': $first_columns = array(
'id',
'login',
'first_name',
'last_name',
'email_address',
'website_name',
'status',
'date',
'referrer'); break;
case 'affiliates_categories': $first_columns = array(
'id',
'name',
'description',
'commission_amount',
'commission_percentage',
'commission2_amount',
'commission2_percentage'); break;
case 'clicks': $first_columns = array(
'id',
'referrer',
'url',
'ip_address',
'user_agent',
'referring_url',
'date',
'date_utc'); break;
case 'commissions': case 'recurring_commissions': $first_columns = array(
'id',
'referrer',
'date',
'product_id',
'commission_amount',
'commission_payment',
'commission_status',
'commission_payment_date'); break;
case 'messages_commissions': $first_columns = array(
'id',
'referrer',
'date',
'form_id',
'commission_amount',
'commission_status',
'commission_payment_date'); break;
case 'prospects_commissions': $first_columns = array(
'id',
'referrer',
'date',
'form_id',
'autoresponder_list',
'commission_amount',
'commission_status',
'commission_payment_date'); break;
case 'affiliates_performances': $first_columns = array(
'id',
'login',
'affiliates',
'clicks',
'orders_amount',
'orders_commission_amount'); }

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
'start_date' => '2011-01-01 00:00:00');

if (strstr($table_slug, 'performances')) {
unset($initial_options[$table_slug]['searchby']);
$initial_options[$table_slug]['filterby'] = 'product_id'; } }


$initial_options['statistics'] = array(
'displayed_tables' => array(),
'filterby' => 'referrer',
'start_date' => '2011-01-01 00:00:00',
'tables' => array('commissions', 'recurring_commissions', 'prospects_commissions', 'messages_commissions', 'affiliates', 'affiliates_categories', 'clicks'));


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
'affiliate_category',
'affiliates_categories',
'click',
'front_office',
'instant_notifications',
'messages_commissions',
'prospects_commissions',
'recurring_commissions'))) {
$menu_displayed_items[] = $key; } }

$initial_options['back_office'] = array(
'affiliate_category_page_summary_displayed' => 'yes',
'affiliate_category_page_undisplayed_modules' => array('instant-notifications'),
'affiliate_page_summary_displayed' => 'yes',
'affiliate_page_undisplayed_modules' => array(
	'custom-instructions',
	'instant-notifications',
	'membership',
	'registration-as-a-client',
	'wordpress'),
'back_office_page_summary_displayed' => 'yes',
'back_office_page_undisplayed_modules' => array('icon'),
'click_page_summary_displayed' => 'no',
'click_page_undisplayed_modules' => array(),
'custom_icon_url' => AFFILIATION_MANAGER_URL.'images/icon.png',
'custom_icon_used' => 'no',
'displayed_links' => $displayed_links,
'front_office_page_summary_displayed' => 'yes',
'front_office_page_undisplayed_modules' => array(
	'clients-statistics',
	'messages-statistics',
	'messages-commissions1-statistics',
	'messages-commissions2-statistics',
	'prospects-statistics',
	'prospects-commissions1-statistics',
	'prospects-commissions2-statistics'),
'instant_notifications_page_summary_displayed' => 'yes',
'instant_notifications_page_undisplayed_modules' => array(),
'links' => $links,
'links_displayed' => 'yes',
'menu_displayed' => 'yes',
'menu_displayed_items' => $menu_displayed_items,
'menu_items' => $menu_items,
'menu_title' => __('Affiliation', 'affiliation-manager'),
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
	'membership',
	'password-reset-notification-email',
	'profile-edit-notification-email',
	'registration-as-a-client',
	'removal-notification-email',
	'urls-encryption',
	'wordpress'),
'pages_titles' => $pages_titles,
'statistics_page_undisplayed_columns' => array(),
'statistics_page_undisplayed_rows' => array(
	'affiliates_categories',
	'clients',
	'commissions2',
	'messages',
	'messages_commissions',
	'messages_commissions2',
	'paid_commissions2',
	'paid_messages_commissions',
	'paid_messages_commissions2',
	'paid_prospects_commissions',
	'paid_prospects_commissions2',
	'paid_recurring_commissions',
	'paid_recurring_commissions2',
	'prospects',
	'prospects_commissions',
	'prospects_commissions2',
	'recurring_commissions',
	'recurring_commissions2',
	'recurring_payments',
	'unpaid_commissions2',
	'unpaid_messages_commissions',
	'unpaid_messages_commissions2',
	'unpaid_prospects_commissions',
	'unpaid_prospects_commissions2',
	'unpaid_recurring_commissions',
	'unpaid_recurring_commissions2'),
'title' => 'Affiliation Manager',
'title_displayed' => 'yes');


foreach ($variables as $variable) { if (isset($original[$variable])) { $$variable = $original[$variable]; } }