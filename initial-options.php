<?php foreach (array('admin_email', 'blogname', 'siteurl') as $key) { $$key = get_option($key); }


$initial_options[''] = array(
'affiliation_registration_confirmation_email_sent' => '',
'affiliation_registration_notification_email_sent' => '',
'available_login_indicator_message' => '<span style="color: green;">'.__('Available', 'commerce-manager').'</span>',
'commerce_registration_confirmation_email_sent' => '',
'commerce_registration_notification_email_sent' => '',
'currency_code' => (strtolower(substr(WPLANG, 0, 2)) == 'fr' ? 'EUR' : 'USD'),
'customer_affiliate_category_id' => '',
'customer_affiliate_status' => '',
'customer_autoresponder' => 'AWeber',
'customer_autoresponder_list' => '',
'customer_client_category_id' => '',
'customer_client_status' => '',
'customer_member_category_id' => '',
'customer_member_status' => '',
'customer_members_areas' => '',
'customer_subscribed_as_a_client' => 'no',
'customer_subscribed_as_a_user' => 'no',
'customer_subscribed_to_affiliate_program' => 'no',
'customer_subscribed_to_autoresponder' => 'no',
'customer_subscribed_to_members_areas' => 'no',
'customer_unsubscribed_from_members_areas' => 'yes',
'customer_user_role' => 'subscriber',
'default_captcha_type' => 'recaptcha',
'default_payment_mode' => 'PayPal',
'default_payment_option' => 0,
'default_product_id' => 1,
'default_quantity' => 1,
'default_recaptcha_theme' => 'red',
'default_shipping_cost_applied' => 'no',
'default_tax_applied' => 'no',
'encrypted_urls_key' => md5(mt_rand()),
'encrypted_urls_validity_duration' => 48,
'first_payment_amount_used' => 'no',
'first_payment_period_quantity' => 1,
'first_payment_period_time_unit' => 'week',
'first_payment_period_used' => 'no',
'getresponse_api_key' => '',
'inactive_account_message' => __('Your account is inactive.', 'commerce-manager'),
'inexistent_email_address_message' => __('This email address does not match a client account.', 'commerce-manager'),
'inexistent_order_message' => __('This product and this email address do not match an unrefunded order.', 'commerce-manager'),
'invalid_captcha_message' => __('The code you entered for the CAPTCHA is incorrect.', 'commerce-manager'),
'invalid_email_address_message' => __('This email address appears to be invalid.', 'commerce-manager'),
'invalid_login_or_password_message' => __('Invalid login or password', 'commerce-manager'),
'mailchimp_api_key' => '',
'membership_registration_confirmation_email_sent' => '',
'membership_registration_notification_email_sent' => '',
'numeric_login_message' => __('Your login name must be a non-numeric string.', 'commerce-manager'),
'order_confirmation_email_receiver' => '[customer email-address]',
'order_confirmation_email_sender' => $blogname.' <'.$admin_email.'>',
'order_confirmation_email_sent' => 'yes',
'order_confirmation_email_subject' => __('Your Order', 'commerce-manager'),
'order_confirmation_url' => HOME_URL,
'order_custom_instructions_executed' => 'no',
'order_notification_email_receiver' => $admin_email,
'order_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'order_notification_email_sent' => 'yes',
'order_notification_email_subject' => __('Order Notification', 'commerce-manager').' ([product name])',
'order_processing_custom_instructions_executed' => 'no',
'order_processing_notification_email_receiver' => '[customer email-address]',
'order_processing_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'order_processing_notification_email_subject' => __('Your Order Has Been Processed', 'commerce-manager'),
'order_refund_custom_instructions_executed' => 'no',
'order_refund_notification_email_receiver' => $admin_email,
'order_refund_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'order_refund_notification_email_sent' => 'yes',
'order_refund_notification_email_subject' => __('Refund Of An Order', 'commerce-manager').' ([product name])',
'order_removal_custom_instructions_executed' => 'no',
'orders_initial_status' => 'unprocessed',
'payments_number' => 3,
'payments_period_quantity' => 1,
'payments_period_time_unit' => 'month',
'payments_profile_deactivation_custom_instructions_executed' => 'no',
'payments_profile_deactivation_notification_email_receiver' => $admin_email,
'payments_profile_deactivation_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'payments_profile_deactivation_notification_email_sent' => 'yes',
'payments_profile_deactivation_notification_email_subject' => __('Deactivation Of A Recurring Payments Profile', 'commerce-manager').' ([product name])',
'paypal_email_address' => $admin_email,
'purchase_button_text' => __('Purchase', 'commerce-manager'),
'purchase_button_text2' => __('Click here to pay in 2 times', 'commerce-manager'),
'purchase_button_text3' => __('Click here to pay in 3 times', 'commerce-manager'),
'purchase_button_text4' => __('Click here to pay in 4 times', 'commerce-manager'),
'purchase_button_url' => COMMERCE_MANAGER_URL.'images/'.__('en', 'commerce-manager').'/purchase-180.png',
'purchase_button_url2' => COMMERCE_MANAGER_URL.'images/'.__('en', 'commerce-manager').'/purchase-180.png',
'purchase_button_url3' => COMMERCE_MANAGER_URL.'images/'.__('en', 'commerce-manager').'/purchase-180.png',
'purchase_button_url4' => COMMERCE_MANAGER_URL.'images/'.__('en', 'commerce-manager').'/purchase-180.png',
'purchase_link_text' => __('Click here to purchase [product name]', 'commerce-manager'),
'purchase_link_text2' => __('Click here to pay in 2 times', 'commerce-manager'),
'purchase_link_text3' => __('Click here to pay in 3 times', 'commerce-manager'),
'purchase_link_text4' => __('Click here to pay in 4 times', 'commerce-manager'),
'recaptcha_private_key' => '',
'recaptcha_public_key' => '',
'recurring_payment_confirmation_email_receiver' => '[customer email-address]',
'recurring_payment_confirmation_email_sender' => $blogname.' <'.$admin_email.'>',
'recurring_payment_confirmation_email_sent' => 'no',
'recurring_payment_confirmation_email_subject' => __('We Have Received Your Payment', 'commerce-manager'),
'recurring_payment_custom_instructions_executed' => 'no',
'recurring_payment_notification_email_receiver' => $admin_email,
'recurring_payment_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'recurring_payment_notification_email_sent' => 'yes',
'recurring_payment_notification_email_subject' => __('Recurring Payment Notification', 'commerce-manager').' ([product name])',
'recurring_payment_refund_custom_instructions_executed' => 'no',
'recurring_payment_refund_notification_email_receiver' => $admin_email,
'recurring_payment_refund_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'recurring_payment_refund_notification_email_sent' => 'yes',
'recurring_payment_refund_notification_email_subject' => __('Refund Of A Recurring Payment', 'commerce-manager').' ([product name])',
'recurring_payment_removal_custom_instructions_executed' => 'no',
'redelivery_email_receiver' => '[customer email-address]',
'redelivery_email_sender' => $blogname.' <'.$admin_email.'>',
'redelivery_email_subject' => __('Redelivery Of [product name]', 'commerce-manager'),
'redelivery_notification_email_receiver' => $admin_email,
'redelivery_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'redelivery_notification_email_sent' => 'yes',
'redelivery_notification_email_subject' => __('Redelivery Notification', 'commerce-manager').' ([product name])',
'sg_autorepondeur_account_id' => '',
'sg_autorepondeur_activation_code' => '',
'shipping_address_required' => 'no',
'shipping_cost' => 0,
'subscription_button_text' => __('Subscribe', 'commerce-manager'),
'subscription_button_url' => COMMERCE_MANAGER_URL.'images/'.__('en', 'commerce-manager').'/purchase-180.png',
'subscription_link_text' => __('Click here to subscribe', 'commerce-manager'),
'tax_applied' => 'no',
'tax_included_in_price' => 'no',
'tax_percentage' => 0,
'terms_and_conditions_url' => HOME_URL,
'too_long_login_message' => __('Your login name must contain at most [commerce-manager maximum-login-length] characters.', 'commerce-manager'),
'too_long_password_message' => __('Your password must contain at most [commerce-manager maximum-password-length] characters.', 'commerce-manager'),
'too_short_login_message' => __('Your login name must contain at least [commerce-manager minimum-login-length] characters.', 'commerce-manager'),
'too_short_password_message' => __('Your password must contain at least [commerce-manager minimum-password-length] characters.', 'commerce-manager'),
'unavailable_email_address_message' => __('This email address is not available.', 'commerce-manager'),
'unavailable_login_indicator_message' => '<span style="color: red;">'.__('Unavailable', 'commerce-manager').'</span>',
'unavailable_login_message' => __('This login name is not available.', 'commerce-manager'),
'unfilled_field_message' => __('This field is required.', 'commerce-manager'),
'unfilled_fields_message' => __('Please fill out the required fields.', 'commerce-manager'),
'version' => COMMERCE_MANAGER_VERSION,
'weight' => 0,
'weight_unit' => (strtolower(substr(WPLANG, 0, 2)) == 'fr' ? 'kilogram' : 'pound'));


$initial_options['activation_confirmation_email_body'] =
__('Hi', 'commerce-manager').' [client first-name],

'.__('Thanks for activating your client account.', 'commerce-manager').' '.__('You can login from this page:', 'commerce-manager').'

'.HOME_URL.'

--
'.$blogname.'
'.HOME_URL;


$initial_options['activation_custom_instructions'] = '';


$initial_options['activation_notification_email_body'] =
__('Hi', 'commerce-manager').' [client first-name],

'.__('Your client account has been activated.', 'commerce-manager').' '.__('You can login from this page:', 'commerce-manager').'

'.HOME_URL.'

--
'.$blogname.'
'.HOME_URL;


include 'libraries/captchas.php';
$initial_options['captchas_numbers'] = $captchas_numbers;


$initial_options['clients_accounts'] = array(
'activation_confirmation_email_receiver' => '[client email-address]',
'activation_confirmation_email_sender' => $blogname.' <'.$admin_email.'>',
'activation_confirmation_email_sent' => 'yes',
'activation_confirmation_email_subject' => __('Activation Of Your Client Account', 'commerce-manager'),
'activation_confirmation_url' => HOME_URL,
'activation_custom_instructions_executed' => 'no',
'activation_notification_email_receiver' => '[client email-address]',
'activation_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'activation_notification_email_subject' => __('Activation Of Your Client Account', 'commerce-manager'),
'client_affiliate_category_id' => '',
'client_affiliate_status' => '',
'client_affiliation_registration_confirmation_email_sent' => '',
'client_affiliation_registration_notification_email_sent' => '',
'client_autoresponder' => 'AWeber',
'client_autoresponder_list' => '',
'client_member_category_id' => '',
'client_member_status' => '',
'client_members_areas' => '',
'client_membership_registration_confirmation_email_sent' => '',
'client_membership_registration_notification_email_sent' => '',
'client_subscribed_as_a_user' => 'no',
'client_subscribed_to_affiliate_program' => 'no',
'client_subscribed_to_autoresponder' => 'no',
'client_subscribed_to_members_areas' => 'no',
'client_user_role' => 'subscriber',
'clients_initial_category_id' => 0,
'clients_initial_status' => 'active',
'deactivation_custom_instructions_executed' => 'no',
'deactivation_notification_email_receiver' => '[client email-address]',
'deactivation_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'deactivation_notification_email_subject' => __('Deactivation Of Your Client Account', 'commerce-manager'),
'login_custom_instructions_executed' => 'no',
'login_notification_email_receiver' => $admin_email,
'login_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'login_notification_email_sent' => 'no',
'login_notification_email_subject' => __('A Client Has Logged In', 'commerce-manager').' ([client login])',
'logout_custom_instructions_executed' => 'no',
'logout_notification_email_receiver' => $admin_email,
'logout_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'logout_notification_email_sent' => 'no',
'logout_notification_email_subject' => __('A Client Has Logged Out', 'commerce-manager').' ([client login])',
'maximum_login_length' => 32,
'maximum_password_length' => 32,
'minimum_login_length' => 1,
'minimum_password_length' => 5,
'password_reset_custom_instructions_executed' => 'no',
'password_reset_email_receiver' => '[client email-address]',
'password_reset_email_sender' => $blogname.' <'.$admin_email.'>',
'password_reset_email_subject' => __('Your New Password', 'commerce-manager'),
'password_reset_notification_email_receiver' => $admin_email,
'password_reset_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'password_reset_notification_email_sent' => 'yes',
'password_reset_notification_email_subject' => __('A Client Has Reset His Password', 'commerce-manager').' ([client login])',
'profile_edit_custom_instructions_executed' => 'no',
'profile_edit_notification_email_receiver' => $admin_email,
'profile_edit_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'profile_edit_notification_email_sent' => 'yes',
'profile_edit_notification_email_subject' => __('A Client Has Edited His Profile', 'commerce-manager').' ([client login])',
'registration_confirmation_email_receiver' => '[client email-address]',
'registration_confirmation_email_sender' => $blogname.' <'.$admin_email.'>',
'registration_confirmation_email_sent' => 'yes',
'registration_confirmation_email_subject' => __('Your Registration As A Client', 'commerce-manager'),
'registration_confirmation_url' => HOME_URL,
'registration_custom_instructions_executed' => 'no',
'registration_notification_email_receiver' => $admin_email,
'registration_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'registration_notification_email_sent' => 'yes',
'registration_notification_email_subject' => __('Registration Of A Client', 'commerce-manager').' ([client login])',
'removal_custom_instructions_executed' => 'no',
'removal_notification_email_receiver' => '[client email-address]',
'removal_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'removal_notification_email_subject' => __('Removal Of Your Client Account', 'commerce-manager'));


$initial_options['code'] =
'[validation-content][other]<p style="color: red;">[error unfilled-fields] [error invalid-captcha]</p>[/validation-content]

<table style="width: 100%;">
<tr style="vertical-align: top;"><td><strong>'.__('Product', 'commerce-manager').'</strong></td>
<td>[product name] ([product price] [commerce-manager currency-code])</td></tr>
<tr style="vertical-align: top;"><td><strong>[label quantity]'.__('Quantity', 'commerce-manager').'[/label]</strong></td>
<td>[input quantity size=3 value=1]<br />[error style="color: red;" quantity]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label payment-option]'.__('Payment', 'commerce-manager').'[/label]</strong></td>
<td>[select payment-option]
[option value=0]'.__('at once', 'commerce-manager').'[/option]
[option value=1][product payments-number1] x [product payments-amount1] [commerce-manager currency-code][/option]
[option value=2][product payments-number2] x [product payments-amount2] [commerce-manager currency-code][/option]
[/select]</td></tr>
</table>
<p style="text-align: center;"><label>[input terms-and-conditions type=checkbox required=yes value=yes] 
'.__('I accept the <a href="[commerce-manager terms-and-conditions-url]">terms and conditions</a>.', 'commerce-manager').'</label>
<br />[error style="color: red;" terms-and-conditions]</p>
<div style="text-align: center;">[input submit value="'.__('Purchase', 'commerce-manager').'"]</div>';


$initial_options['date_picker_css'] = '<link rel="stylesheet" type="text/css" media="screen" href="'.COMMERCE_MANAGER_URL.'libraries/date-picker.css" />';


$initial_options['date_picker_js'] =
'<script type="text/javascript" src="'.COMMERCE_MANAGER_URL.'libraries/date-picker.js"></script>
<script type="text/javascript">
Date.dayNames = [\''.__('Sunday', 'commerce-manager').'\', \''.__('Monday', 'commerce-manager').'\', \''.__('Tuesday', 'commerce-manager').'\', \''.__('Wednesday', 'commerce-manager').'\', \''.__('Thursday', 'commerce-manager').'\', \''.__('Friday', 'commerce-manager').'\', \''.__('Saturday', 'commerce-manager').'\'];
Date.abbrDayNames = [\''.__('Sun', 'commerce-manager').'\', \''.__('Mon', 'commerce-manager').'\', \''.__('Tue', 'commerce-manager').'\', \''.__('Wed', 'commerce-manager').'\', \''.__('Thu', 'commerce-manager').'\', \''.__('Fri', 'commerce-manager').'\', \''.__('Sat', 'commerce-manager').'\'];
Date.monthNames = [\''.__('January', 'commerce-manager').'\', \''.__('February', 'commerce-manager').'\', \''.__('March', 'commerce-manager').'\', \''.__('April', 'commerce-manager').'\', \''.__('May', 'commerce-manager').'\', \''.__('June', 'commerce-manager').'\', \''.__('July', 'commerce-manager').'\', \''.__('August', 'commerce-manager').'\', \''.__('September', 'commerce-manager').'\', \''.__('October', 'commerce-manager').'\', \''.__('November', 'commerce-manager').'\', \''.__('December', 'commerce-manager').'\'];
Date.abbrMonthNames = [\''.__('Jan', 'commerce-manager').'\', \''.__('Feb', 'commerce-manager').'\', \''.__('Mar', 'commerce-manager').'\', \''.__('Apr', 'commerce-manager').'\', \''.__('May', 'commerce-manager').'\', \''.__('Jun', 'commerce-manager').'\', \''.__('Jul', 'commerce-manager').'\', \''.__('Aug', 'commerce-manager').'\', \''.__('Sep', 'commerce-manager').'\', \''.__('Oct', 'commerce-manager').'\', \''.__('Nov', 'commerce-manager').'\', \''.__('Dec', 'commerce-manager').'\'];
$.dpText = {
TEXT_PREV_YEAR : \''.__('Previous year', 'commerce-manager').'\',
TEXT_PREV_MONTH : \''.__('Previous month', 'commerce-manager').'\',
TEXT_NEXT_YEAR : \''.__('Next year', 'commerce-manager').'\',
TEXT_NEXT_MONTH : \''.__('Next month', 'commerce-manager').'\',
TEXT_CLOSE : \''.__('Close', 'commerce-manager').'\',
TEXT_CHOOSE_DATE : \''.__('Choose a date', 'commerce-manager').'\',
DATE_PICKER_ALT : \''.__('Date', 'commerce-manager').'\',
DATE_PICKER_URL : \''.COMMERCE_MANAGER_URL.'images/date-picker.png\',
HEADER_FORMAT : \'mmmm yyyy\'
}; $(function(){ $(\'.date-pick\').datePicker({startDate:\'2011-01-01\'}); });
</script>';


$initial_options['deactivation_custom_instructions'] = '';


$initial_options['deactivation_notification_email_body'] =
__('Hi', 'commerce-manager').' [client first-name],



--
'.$blogname.'
'.HOME_URL;


$initial_options['global_statistics_code'] =
'<table style="width: 100%;"><tbody>
<tr style="vertical-align: top;"><td><strong>'.__('Number of orders', 'commerce-manager').'</strong></td>
<td>[client-counter data=orders range=form][number][/client-counter]</td></tr>
<tr style="vertical-align: top;"><td><strong>'.__('Orders total amount', 'commerce-manager').'</strong></td>
<td>[client-counter data=orders-amount range=form][number][/client-counter] [commerce-manager currency-code]</td></tr>
</tbody></table>';


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
<tr style="vertical-align: top;"><td style="width: 40%;"><strong>[label login]'.__('Login name', 'commerce-manager').'[/label]</strong></td>
<td style="width: 60%;">[input login size=20]<br />[error style="color: red;" login]</td></tr>
<tr style="vertical-align: top;"><td style="width: 40%;"><strong>[label password]'.__('Password', 'commerce-manager').'[/label]</strong></td>
<td style="width: 60%;">[input password size=20]<br />[error style="color: red;" password]</td></tr>
</table>
<p style="margin: 0.75em; text-align: center;"><label>[input remember value=yes] '.__('Remember me', 'commerce-manager').'</label></p>
<div style="text-align: center;">[input submit value="'.__('Login', 'commerce-manager').'"]</div>';


$initial_options['login_compact_form_code'] =
'[validation-content][other]<p style="color: red;">[error invalid-login-or-password] [error inactive-account] [error invalid-captcha]</p>[/validation-content]

<p><strong>[label login]'.__('Login name:', 'commerce-manager').'[/label]</strong><br />
[input login size=20]<br />[error style="color: red;" login]</p>
<p><strong>[label password]'.__('Password:', 'commerce-manager').'[/label]</strong><br />
[input password size=20]<br />[error style="color: red;" password]</p>
<p><label>[input remember value=yes] '.__('Remember me', 'commerce-manager').'</label></p>
<div style="text-align: center;">[input submit value="'.__('Login', 'commerce-manager').'"]</div>';


$initial_options['login_notification_email_body'] =
'[client first-name] [client last-name]

'.__('Login name:', 'commerce-manager').' [client login]
'.__('Email address:', 'commerce-manager').' [client email-address]
'.__('PayPal email address:', 'commerce-manager').' [client paypal-email-address]
'.__('Website name:', 'commerce-manager').' [client website-name]
'.__('Website URL:', 'commerce-manager').' [client website-url]

'.__('More informations about this client:', 'commerce-manager').'

'.$siteurl.'/wp-admin/admin.php?page=commerce-manager-client&id=[client id]';


$initial_options['logout_custom_instructions'] = '';


$initial_options['logout_notification_email_body'] = $initial_options['login_notification_email_body'];


$initial_options['meta_widget'] = array(
'title' => __('Commerce', 'commerce-manager'),
'content' => '[commerce-content]
<ul>
<li><a href="'.COMMERCE_MANAGER_URL.'?action=logout">'.__('Log out').'</a></li>
</ul>
[other][commerce-login-compact-form]
[/commerce-content]');


$initial_options['order_confirmation_email_body'] =
__('Thank you for your order', 'commerce-manager').', [customer first-name].

[product instructions]

--
'.$blogname.'
'.HOME_URL;


$initial_options['order_custom_instructions'] = '';


$initial_options['order_notification_email_body'] =
__('Product:', 'commerce-manager').' [product name]
'.__('Amount:', 'commerce-manager').' [order amount] [commerce-manager currency-code]
'.__('Buyer:', 'commerce-manager').' [customer first-name] [customer last-name] <[customer email-address]>

'.__('More informations about this order:', 'commerce-manager').'

'.$siteurl.'/wp-admin/admin.php?page=commerce-manager-order&id=[order id]';


$initial_options['order_processing_custom_instructions'] = '';


$initial_options['order_processing_notification_email_body'] =
__('Hi', 'commerce-manager').' [customer first-name],

'.__('We have processed your order. You should receive it in 2 or 3 days.', 'commerce-manager').'

'.__('Product:', 'commerce-manager').' [product name]
'.__('Amount:', 'commerce-manager').' [order amount] [commerce-manager currency-code]

--
'.$blogname.'
'.HOME_URL;


$initial_options['order_refund_custom_instructions'] = '';


$initial_options['order_refund_notification_email_body'] = $initial_options['order_notification_email_body'];


$initial_options['order_removal_custom_instructions'] = '';


$initial_options['orders_statistics_code'] =
'<h3 id="orders-statistics">'.__('Orders Statistics', 'commerce-manager').'</h3>

[if order]
<table style="width: 100%;">
<tr style="vertical-align: top;">
<th>'.__('Date', 'commerce-manager').'</th>
<th>'.__('Product', 'commerce-manager').'</th>
<th>'.__('Amount', 'commerce-manager').'</th>
</tr>
[foreach order]
<tr style="vertical-align: top;">
<td>[order date]</td>
<td><a href="[product url filter=htmlspecialchars]">[product name]</a></td>
<td>[order amount] [commerce-manager currency-code]</td>
</tr>[/foreach]
</table>
[else]<p>'.__('No orders', 'commerce-manager').'</p>[/if]';


$initial_options['password_reset_custom_instructions'] = '';


$initial_options['password_reset_email_body'] =
__('Hi', 'commerce-manager').' [client first-name],

'.__('Here are your new login informations:', 'commerce-manager').'

'.__('Your login:', 'commerce-manager').' [client login]
'.__('Your password:', 'commerce-manager').' [client password]

'.__('You can login from this page:', 'commerce-manager').'

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
'[validation-content]<p style="color: green;">'.__('Your password has been reset successfully.', 'commerce-manager').'</p>
[other]<p style="color: red;">[error inexistent-email-address] [error invalid-captcha]</p>[/validation-content]

<div style="text-align: center;">
<p><label><strong>'.__('Your email address:', 'commerce-manager').'</strong><br />
[input email-address size=40]<br />
[error style="color: red;" email-address]</label></p>
<div>[input submit value="'.__('Reset', 'commerce-manager').'"]</div>
</div>';


$initial_options['password_reset_notification_email_body'] = $initial_options['login_notification_email_body'];


$initial_options['payments_profile_deactivation_custom_instructions'] = '';


$initial_options['payments_profile_deactivation_notification_email_body'] =
__('Product:', 'commerce-manager').' [product name]
'.__('Payments number:', 'commerce-manager').' [order payments-number filter=i18n]
'.__('Received payments number:', 'commerce-manager').' [order received-payments-number]
'.__('Payments amount:', 'commerce-manager').' [order payments-amount] [commerce-manager currency-code]
'.__('Buyer:', 'commerce-manager').' [customer first-name] [customer last-name] <[customer email-address]>

'.__('More informations about this order:', 'commerce-manager').'

'.$siteurl.'/wp-admin/admin.php?page=commerce-manager-order&id=[order id]';


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
'[validation-content]<p style="color: green;">'.__('Your profile has been changed successfully.', 'commerce-manager').'</p>
[other]<p style="color: red;">[error unavailable-login] [error numeric-login] [error too-short-login] [error too-long-login] [error too-short-password] [error too-long-password] [error unavailable-email-address] [error unfilled-fields] [error invalid-captcha]</p>[/validation-content]

<table style="width: 100%;">
<tr style="vertical-align: top;"><td><strong>[label login]'.__('Login name', 'commerce-manager').'[/label]</strong>*</td>
<td>[input login size=30] [indicator login]<br />
<span class="description">'.__('Letters, numbers, hyphens, underscores, points and <em>@</em> only', 'commerce-manager').'</span><br />
[error style="color: red;" login]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label password]'.__('Password', 'commerce-manager').'[/label]</strong></td>
<td>[input password size=30]<br /><span class="description">'.__('(if you want to change it)', 'commerce-manager').'</span><br />
[error style="color: red;" password]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label first-name]'.__('First name', 'commerce-manager').'[/label]</strong>*</td>
<td>[input first-name size=30 required=yes]<br />[error style="color: red;" first-name]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label last-name]'.__('Last name', 'commerce-manager').'[/label]</strong>*</td>
<td>[input last-name size=30 required=yes]<br />[error style="color: red;" last-name]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label email-address]'.__('Email address', 'commerce-manager').'[/label]</strong>*</td>
<td>[input email-address size=30 required=yes]<br />[error style="color: red;" email-address]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label paypal-email-address]'.__('PayPal email address', 'commerce-manager').'[/label]</strong></td>
<td>[input paypal-email-address size=30]<br />[error style="color: red;" paypal-email-address]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label website-name]'.__('Website name', 'commerce-manager').'[/label]</strong></td>
<td>[input website-name size=30]<br />[error style="color: red;" website-name]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label website-url]'.__('Website URL', 'commerce-manager').'[/label]</strong></td>
<td>[input website-url size=30]<br />[error style="color: red;" website-url]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label address]'.__('Address', 'commerce-manager').'[/label]</strong></td>
<td>[input address size=30]<br />[error style="color: red;" address]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label postcode]'.__('Postcode', 'commerce-manager').'[/label]</strong></td>
<td>[input postcode size=30]<br />[error style="color: red;" postcode]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label town]'.__('Town', 'commerce-manager').'[/label]</strong></td>
<td>[input town size=30]<br />[error style="color: red;" town]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label country]'.__('Country', 'commerce-manager').'[/label]</strong></td>
<td>[input country size=30]<br />[error style="color: red;" country]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label phone-number]'.__('Phone number', 'commerce-manager').'[/label]</strong></td>
<td>[input phone-number size=30]<br />[error style="color: red;" phone-number]</td></tr>
</table>
<div style="text-align: center;">[input submit value="'.__('Submit', 'commerce-manager').'"]</div>';


$initial_options['recurring_payment_confirmation_email_body'] =
__('Thank you for your payment', 'commerce-manager').', [customer first-name].



--
'.$blogname.'
'.HOME_URL;


$initial_options['recurring_payment_custom_instructions'] = '';


$initial_options['recurring_payment_notification_email_body'] =
__('Product:', 'commerce-manager').' [product name]
'.__('Amount:', 'commerce-manager').' [recurring-payment amount] [commerce-manager currency-code]
'.__('Buyer:', 'commerce-manager').' [customer first-name] [customer last-name] <[customer email-address]>

'.__('More informations about this payment:', 'commerce-manager').'

'.$siteurl.'/wp-admin/admin.php?page=commerce-manager-recurring-payment&id=[recurring-payment id]';


$initial_options['recurring_payment_refund_custom_instructions'] = '';


$initial_options['recurring_payment_refund_notification_email_body'] = $initial_options['recurring_payment_notification_email_body'];


$initial_options['recurring_payment_removal_custom_instructions'] = '';


$initial_options['recurring_payments_statistics_code'] =
'<h3 id="recurring-payments-statistics">'.__('Recurring Payments Statistics', 'commerce-manager').'</h3>

[if recurring-payment]
<table style="width: 100%;">
<tr style="vertical-align: top;">
<th>'.__('Date', 'commerce-manager').'</th>
<th>'.__('Product', 'commerce-manager').'</th>
<th>'.__('Amount', 'commerce-manager').'</th>
</tr>
[foreach recurring-payment]
<tr style="vertical-align: top;">
<td>[recurring-payment date]</td>
<td><a href="[product url filter=htmlspecialchars]">[product name]</a></td>
<td>[recurring-payment amount] [commerce-manager currency-code]</td>
</tr>[/foreach]
</table>
[else]<p>'.__('No payments', 'commerce-manager').'</p>[/if]';


$initial_options['redelivery_email_body'] =
__('Hi', 'commerce-manager').' [customer first-name],

[product instructions]

--
'.$blogname.'
'.HOME_URL;


$initial_options['redelivery_form'] = array(
'inexistent_order_message' => '',
'invalid_captcha_message' => '',
'invalid_email_address_message' => '',
'unfilled_field_message' => '');


$initial_options['redelivery_form_code'] =
'[validation-content]<p style="color: green;">'.__('Your product has been redelivered successfully.', 'commerce-manager').'</p>
[other]<p style="color: red;">[error inexistent-order] [error invalid-captcha]</p>[/validation-content]

<div style="text-align: center;">
<p><label><strong>'.__('Product:', 'commerce-manager').'</strong> [commerce-product-selector]</label></p>
<p><label><strong>'.__('Your email address:', 'commerce-manager').'</strong><br />
[input email-address size=40]<br />[error style="color: red;" email-address]</label></p>
<div>[input submit value="'.__('Submit', 'commerce-manager').'"]</div>
</div>';


$initial_options['redelivery_notification_email_body'] = $initial_options['order_notification_email_body'];


$initial_options['registration_confirmation_email_body'] =
__('Thank you for your registration as a client', 'commerce-manager').', [client first-name].
'.__('You can login from this page:', 'commerce-manager').'

'.HOME_URL.'

'.__('Your login name:', 'commerce-manager').' [client login]
'.__('Your password:', 'commerce-manager').' [client password]

--
'.$blogname.'
'.HOME_URL;


$initial_options['registration_custom_instructions'] = '';


$initial_options['registration_form'] = $initial_options['profile_form'];


$initial_options['registration_compact_form'] = $initial_options['registration_form'];


$initial_options['registration_form_code'] =
'[validation-content][other]<p style="color: red;">[error unavailable-login] [error numeric-login] [error too-short-login] [error too-long-login] [error too-short-password] [error too-long-password] [error unavailable-email-address] [error unfilled-fields] [error invalid-captcha]</p>[/validation-content]

<table style="width: 100%;">
<tr style="vertical-align: top;"><td><strong>[label login]'.__('Login name', 'commerce-manager').'[/label]</strong>*</td>
<td>[input login size=30] [indicator login]<br />
<span class="description">'.__('Letters, numbers, hyphens, underscores, points and <em>@</em> only', 'commerce-manager').'</span><br />
[error style="color: red;" login]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label password]'.__('Password', 'commerce-manager').'[/label]</strong>*</td>
<td>[input password size=30] <span class="description">'.__('at least [commerce-manager minimum-password-length] characters', 'commerce-manager').'</span><br />
[error style="color: red;" password]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label first-name]'.__('First name', 'commerce-manager').'[/label]</strong>*</td>
<td>[input first-name size=30 required=yes]<br />[error style="color: red;" first-name]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label last-name]'.__('Last name', 'commerce-manager').'[/label]</strong>*</td>
<td>[input last-name size=30 required=yes]<br />[error style="color: red;" last-name]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label email-address]'.__('Email address', 'commerce-manager').'[/label]</strong>*</td>
<td>[input email-address size=30]<br />[error style="color: red;" email-address]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label paypal-email-address]'.__('PayPal email address', 'commerce-manager').'[/label]</strong></td>
<td>[input paypal-email-address size=30]<br />[error style="color: red;" paypal-email-address]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label website-name]'.__('Website name', 'commerce-manager').'[/label]</strong></td>
<td>[input website-name size=30]<br />[error style="color: red;" website-name]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label website-url]'.__('Website URL', 'commerce-manager').'[/label]</strong></td>
<td>[input website-url size=30]<br />[error style="color: red;" website-url]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label address]'.__('Address', 'commerce-manager').'[/label]</strong></td>
<td>[input address size=30]<br />[error style="color: red;" address]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label postcode]'.__('Postcode', 'commerce-manager').'[/label]</strong></td>
<td>[input postcode size=30]<br />[error style="color: red;" postcode]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label town]'.__('Town', 'commerce-manager').'[/label]</strong></td>
<td>[input town size=30]<br />[error style="color: red;" town]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label country]'.__('Country', 'commerce-manager').'[/label]</strong></td>
<td>[input country size=30]<br />[error style="color: red;" country]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label phone-number]'.__('Phone number', 'commerce-manager').'[/label]</strong></td>
<td>[input phone-number size=30]<br />[error style="color: red;" phone-number]</td></tr>
</table>
<div style="text-align: center;">[input submit value="'.__('Register', 'commerce-manager').'"]</div>';


$initial_options['registration_compact_form_code'] =
'[validation-content][other]<p style="color: red;">[error unavailable-login] [error numeric-login] [error too-short-login] [error too-long-login] [error too-short-password] [error too-long-password] [error unavailable-email-address] [error unfilled-fields] [error invalid-captcha]</p>[/validation-content]

<table style="width: 100%;">
<tr style="vertical-align: top;"><td><strong>[label login]'.__('Login name', 'commerce-manager').'[/label]</strong></td>
<td>[input login size=30] [indicator login]<br />
<span class="description">'.__('Letters, numbers, hyphens, underscores, points and <em>@</em> only', 'commerce-manager').'</span><br />
[error style="color: red;" login]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label password]'.__('Password', 'commerce-manager').'[/label]</strong></td>
<td>[input password size=30] <span class="description">'.__('at least [commerce-manager minimum-password-length] characters', 'commerce-manager').'</span><br />
[error style="color: red;" password]</td></tr>
<tr style="vertical-align: top;"><td><strong>[label email-address]'.__('Email address', 'commerce-manager').'[/label]</strong></td>
<td>[input email-address size=30]<br />[error style="color: red;" email-address]</td></tr>
</table>
<div style="text-align: center;">[input submit value="'.__('Register', 'commerce-manager').'"]</div>';


$initial_options['registration_notification_email_body'] = $initial_options['login_notification_email_body'];


$initial_options['removal_custom_instructions'] = '';


$initial_options['removal_notification_email_body'] = $initial_options['deactivation_notification_email_body'];


$initial_options['statistics_form_code'] =
'<div style="text-align: center;">
<p><label style="margin-left: 3em;"><strong>'.__('Start', 'commerce-manager').'</strong>
[input start-date size=10 style="margin: 0.5em;"]</label>
<label style="margin-left: 3em;"><strong>'.__('End', 'commerce-manager').'</strong>
[input end-date size=10 style="margin: 0.5em;"]</label></p>
<div>[input submit value="'.__('Display', 'commerce-manager').'"]</div>
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
case 'clients': $first_columns = array(
'id',
'login',
'first_name',
'last_name',
'email_address',
'website_name',
'status',
'date'); break;
case 'clients_categories': $first_columns = array(
'id',
'name',
'description',
'keywords',
'date',
'date_utc'); break;
case 'forms': case 'forms_categories': $first_columns = array(
'id',
'name',
'description',
'keywords',
'default_product_id',
'date'); break;
case 'orders': $first_columns = array(
'id',
'first_name',
'last_name',
'email_address',
'product_id',
'amount',
'date',
'status'); break;
case 'products': $first_columns = array(
'id',
'name',
'price',
'reference',
'description',
'available_quantity',
'sales_count',
'refunds_count'); break;
case 'products_categories': $first_columns = array(
'id',
'name',
'price',
'reference',
'description',
'keywords',
'url',
'date'); break;
case 'recurring_payments': $first_columns = array(
'id',
'order_id',
'product_id',
'amount',
'payment_mode',
'transaction_cost',
'date',
'status'); break;
case 'products_performances': case 'forms_performances': $first_columns = array(
'id',
'name',
'sold_items',
'unrefunded_items',
'orders_amount',
'unrefunded_orders_amount'); break;
case 'clients_performances': $first_columns = array(
'id',
'login',
'orders',
'sold_items',
'unrefunded_items',
'orders_amount',
'unrefunded_orders_amount'); }


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
$initial_options[$table_slug]['filterby'] = (strstr($table_slug, 'products') ? 'payment_mode' : 'product_id'); } }


$initial_options['statistics'] = array(
'displayed_tables' => array(),
'filterby' => 'product_id',
'start_date' => '2011-01-01 00:00:00',
'tables' => array('orders', 'recurring_payments', 'products', 'products_categories', 'forms', 'forms_categories', 'clients', 'clients_categories'));


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
'client',
'client_category',
'clients',
'clients_accounts',
'clients_categories',
'clients_performances',
'form',
'form_category',
'forms',
'forms_categories',
'forms_performances',
'front_office',
'product_category',
'products_categories',
'recurring_payment',
'recurring_payments'))) {
$menu_displayed_items[] = $key; } }

$initial_options['back_office'] = array(
'back_office_page_summary_displayed' => 'yes',
'back_office_page_undisplayed_modules' => array('icon'),
'client_category_page_summary_displayed' => 'no',
'client_category_page_undisplayed_modules' => array(),
'client_page_summary_displayed' => 'yes',
'client_page_undisplayed_modules' => array(
	'custom-instructions',
	'membership',
	'registration-to-affiliate-program',
	'wordpress'),
'clients_accounts_page_summary_displayed' => 'yes',
'clients_accounts_page_undisplayed_modules' => array(
	'activation-confirmation-email',
	'activation-notification-email',
	'custom-instructions',
	'deactivation-notification-email',
	'login-notification-email',
	'logout-notification-email',
	'membership',
	'password-reset-notification-email',
	'profile-edit-notification-email',
	'registration-to-affiliate-program',
	'removal-notification-email',
	'wordpress'),
'custom_icon_url' => COMMERCE_MANAGER_URL.'images/icon.png',
'custom_icon_used' => 'no',
'displayed_links' => $displayed_links,
'form_category_page_summary_displayed' => 'yes',
'form_category_page_undisplayed_modules' => array(),
'form_page_summary_displayed' => 'yes',
'form_page_undisplayed_modules' => array(),
'front_office_page_summary_displayed' => 'yes',
'front_office_page_undisplayed_modules' => array(),
'links' => $links,
'links_displayed' => 'yes',
'menu_displayed' => 'yes',
'menu_displayed_items' => $menu_displayed_items,
'menu_items' => $menu_items,
'menu_title' => __('Commerce', 'commerce-manager'),
'minimum_roles' => array(
	'manage' => 'administrator',
	'view' => 'administrator'),
'options_page_summary_displayed' => 'yes',
'options_page_undisplayed_modules' => array(
	'captcha',
	'custom-instructions',
	'membership',
	'order-processing-notification-email',
	'order-refund-notification-email',
	'recurring-payment-confirmation-email',
	'recurring-payment-notification-email',
	'recurring-payment-refund-notification-email',
	'recurring-payments',
	'recurring-payments-profile-deactivation-notification-email',
	'redelivery-email',
	'redelivery-notification-email',
	'registration-as-a-client',
	'registration-to-affiliate-program',
	'wordpress'),
'order_page_summary_displayed' => 'yes',
'order_page_undisplayed_modules' => array(
	'affiliation',
	'custom-instructions',
	'membership',
	'recurring-payments',
	'registration-as-a-client',
	'registration-to-affiliate-program',
	'wordpress'),
'pages_titles' => $pages_titles,
'product_category_page_summary_displayed' => 'yes',
'product_category_page_undisplayed_modules' => array(
	'affiliation',
	'custom-instructions',
	'membership',
	'order-processing-notification-email',
	'order-refund-notification-email',
	'recurring-payment-confirmation-email',
	'recurring-payment-notification-email',
	'recurring-payment-refund-notification-email',
	'recurring-payments',
	'recurring-payments-profile-deactivation-notification-email',
	'redelivery-email',
	'redelivery-notification-email',
	'registration-as-a-client',
	'registration-to-affiliate-program',
	'wordpress'),
'product_page_summary_displayed' => 'yes',
'product_page_undisplayed_modules' => array(
	'affiliation',
	'custom-instructions',
	'membership',
	'order-processing-notification-email',
	'order-refund-notification-email',
	'recurring-payment-confirmation-email',
	'recurring-payment-notification-email',
	'recurring-payment-refund-notification-email',
	'recurring-payments',
	'recurring-payments-profile-deactivation-notification-email',
	'redelivery-email',
	'redelivery-notification-email',
	'registration-as-a-client',
	'registration-to-affiliate-program',
	'wordpress'),
'recurring_payment_page_summary_displayed' => 'yes',
'recurring_payment_page_undisplayed_modules' => array(
	'affiliation',
	'custom-instructions',
	'recurring-payment-confirmation-email',
	'recurring-payment-notification-email'),
'statistics_page_undisplayed_columns' => array(),
'statistics_page_undisplayed_rows' => array(
	'active_clients',
	'clients',
	'clients_categories',
	'deactivated_clients',
	'forms',
	'forms_categories',
	'inactive_clients',
	'products_categories',
	'received_recurring_payments',
	'recurring_payments',
	'refunded_recurring_payments'),
'title' => 'Commerce Manager',
'title_displayed' => 'yes');


foreach ($variables as $variable) { if (isset($original[$variable])) { $$variable = $original[$variable]; } }