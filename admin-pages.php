<?php $admin_links = array(
'Documentation' => array('name' => __('Documentation', 'affiliation-manager')),
'Commerce Manager' => array('name' => __('Commerce', 'affiliation-manager')),
'Membership Manager' => array('name' => __('Membership', 'affiliation-manager')),
'Optin Manager' => array('name' => __('Optin', 'affiliation-manager')),
'Contact Manager' => array('name' => __('Contact', 'affiliation-manager')));

$admin_pages = array(
'' => array('page_title' => 'Affiliation Manager ('.__('Options', 'affiliation-manager').')', 'menu_title' => __('Options', 'affiliation-manager'), 'file' => 'options-page.php'),
'instant_notifications' => array('page_title' => 'Affiliation Manager ('.__('Instant Notifications', 'affiliation-manager').')', 'menu_title' => __('Instant Notifications', 'affiliation-manager'), 'file' => 'instant-notifications-page.php'),
'affiliate' => array('page_title' => 'Affiliation Manager ('.__('Affiliate', 'affiliation-manager').')', 'menu_title' => ((($_GET['page'] == 'affiliation-manager-affiliate') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Affiliate', 'affiliation-manager') : __('Edit Affiliate', 'affiliation-manager')) : __('Add Affiliate', 'affiliation-manager')), 'file' => 'affiliate-page.php'),
'affiliates' => array('page_title' => 'Affiliation Manager ('.__('Affiliates', 'affiliation-manager').')', 'menu_title' => __('Affiliates', 'affiliation-manager'), 'file' => 'table-page.php'),
'affiliates_performances' => array('page_title' => 'Affiliation Manager ('.__('Affiliates Performances', 'affiliation-manager').')', 'menu_title' => __('Affiliates Performances', 'affiliation-manager'), 'file' => 'table-page.php'),
'affiliate_category' => array('page_title' => 'Affiliation Manager ('.__('Affiliate Category', 'affiliation-manager').')', 'menu_title' => ((($_GET['page'] == 'affiliation-manager-affiliate-category') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Affiliate Category', 'affiliation-manager') : __('Edit Affiliate Category', 'affiliation-manager')) : __('Add Affiliate Category', 'affiliation-manager')), 'file' => 'affiliate-page.php'),
'affiliates_categories' => array('page_title' => 'Affiliation Manager ('.__('Affiliates Categories', 'affiliation-manager').')', 'menu_title' => __('Affiliates Categories', 'affiliation-manager'), 'file' => 'table-page.php'),
'click' => array('page_title' => 'Affiliation Manager ('.__('Click', 'affiliation-manager').')', 'menu_title' => ((($_GET['page'] == 'affiliation-manager-click') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Click', 'affiliation-manager') : __('Edit Click', 'affiliation-manager')) : __('Add Click', 'affiliation-manager')), 'file' => 'click-page.php'),
'clicks' => array('page_title' => 'Affiliation Manager ('.__('Clicks', 'affiliation-manager').')', 'menu_title' => __('Clicks', 'affiliation-manager'), 'file' => 'table-page.php'),
'commissions' => array('page_title' => 'Affiliation Manager ('.__('Commissions', 'affiliation-manager').')', 'menu_title' => __('Commissions', 'affiliation-manager'), 'file' => 'table-page.php'),
'recurring_commissions' => array('page_title' => 'Affiliation Manager ('.__('Recurring Commissions', 'affiliation-manager').')', 'menu_title' => __('Recurring Commissions', 'affiliation-manager'), 'file' => 'table-page.php'),
'prospects_commissions' => array('page_title' => 'Affiliation Manager ('.__('Prospects Commissions', 'affiliation-manager').')', 'menu_title' => __('Prospects Commissions', 'affiliation-manager'), 'file' => 'table-page.php'),
'messages_commissions' => array('page_title' => 'Affiliation Manager ('.__('Messages Commissions', 'affiliation-manager').')', 'menu_title' => __('Messages Commissions', 'affiliation-manager'), 'file' => 'table-page.php'),
'payment' => array('page_title' => 'Affiliation Manager ('.__('Payment', 'affiliation-manager').')', 'menu_title' => __('Payment', 'affiliation-manager'), 'file' => 'payment-page.php'),
'statistics' => array('page_title' => 'Affiliation Manager ('.__('Statistics', 'affiliation-manager').')', 'menu_title' => __('Statistics', 'affiliation-manager'), 'file' => 'statistics-page.php'),
'front_office' => array('page_title' => 'Affiliation Manager ('.__('Front Office', 'affiliation-manager').')', 'menu_title' => __('Front Office', 'affiliation-manager'), 'file' => 'front-office-page.php'),
'back_office' => array('page_title' => 'Affiliation Manager ('.__('Back Office', 'affiliation-manager').')', 'menu_title' => __('Back Office', 'affiliation-manager'), 'file' => 'back-office-page.php'));

$modules['affiliate'] = array(
'personal-informations' => array('name' => __('Personal informations', 'affiliation-manager'), 'required' => 'yes'),
'bonus-offered-to-customer' => array('name' => __('Bonus offered to customer', 'affiliation-manager')),
'affiliation' => array('name' => __('Affiliation', 'affiliation-manager'), 'modules' => array(
	'level-1-commission' => array('name' => __('Level 1 commission', 'affiliation-manager')),
	'level-2-commission' => array('name' => __('Level 2 commission', 'affiliation-manager')))),
'instant-notifications' => array('name' => __('Instant notifications', 'affiliation-manager')),
'registration-confirmation-email' => array('name' => __('Registration confirmation email', 'affiliation-manager')),
'registration-notification-email' => array('name' => __('Registration notification email', 'affiliation-manager')),
'autoresponders' => array('name' => __('Autoresponders', 'affiliation-manager')),
'registration-as-a-client' => array('name' => __('Registration as a client', 'affiliation-manager')),
'membership' => array('name' => __('Membership', 'affiliation-manager')),
'wordpress' => array('name' => __('WordPress', 'affiliation-manager')),
'custom-instructions' => array('name' => __('Custom instructions', 'affiliation-manager')),
'affiliate-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Affiliate</em> page', 'affiliation-manager') : __('<em>Add Affiliate</em> page', 'affiliation-manager'))));

$add_affiliate_modules = array(
'registration-confirmation-email',
'registration-notification-email',
'autoresponders',
'registration-as-a-client',
'membership',
'wordpress',
'custom-instructions');

$add_affiliate_fields = array(
'registration_confirmation_email_sent',
'registration_confirmation_email_sender',
'registration_confirmation_email_receiver',
'registration_confirmation_email_subject',
'registration_confirmation_email_body',
'registration_notification_email_sent',
'registration_notification_email_sender',
'registration_notification_email_receiver',
'registration_notification_email_subject',
'registration_notification_email_body',
'affiliate_subscribed_to_autoresponder',
'affiliate_autoresponder',
'affiliate_autoresponder_list',
'affiliate_subscribed_as_a_client',
'affiliate_client_category_id',
'affiliate_client_status',
'commerce_registration_confirmation_email_sent',
'commerce_registration_notification_email_sent',
'affiliate_subscribed_to_members_areas',
'affiliate_members_areas',
'affiliate_member_category_id',
'affiliate_member_status',
'membership_registration_confirmation_email_sent',
'membership_registration_notification_email_sent',
'affiliate_subscribed_as_a_user',
'affiliate_user_role',
'registration_custom_instructions_executed',
'registration_custom_instructions');

$modules['affiliate_category'] = array(
'general-informations' => array('name' => __('General informations', 'affiliation-manager'), 'required' => 'yes'),
'bonus-offered-to-customer' => array('name' => __('Bonus offered to customer', 'affiliation-manager')),
'affiliation' => array('name' => __('Affiliation', 'affiliation-manager'), 'modules' => array(
	'level-1-commission' => array('name' => __('Level 1 commission', 'affiliation-manager')),
	'level-2-commission' => array('name' => __('Level 2 commission', 'affiliation-manager')))),
'instant-notifications' => array('name' => __('Instant notifications', 'affiliation-manager')),
'affiliate-category-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Affiliate Category</em> page', 'affiliation-manager') : __('<em>Add Affiliate Category</em> page', 'affiliation-manager'))));

$modules['back_office'] = array(
'capabilities' => array('name' => __('Capabilities', 'affiliation-manager')),
'icon' => array('name' => __('Icon', 'affiliation-manager')),
'top' => array('name' => __('Top', 'affiliation-manager')),
'menu' => array('name' => __('Menu', 'affiliation-manager')),
'options-page' => array('name' => __('<em>Options</em> page', 'affiliation-manager')),
'instant-notifications-page' => array('name' => __('<em>Instant Notifications</em> page', 'affiliation-manager')),
'affiliate-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Affiliate</em> page', 'affiliation-manager') : __('<em>Add Affiliate</em> page', 'affiliation-manager'))),
'affiliate-category-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Affiliate Category</em> page', 'affiliation-manager') : __('<em>Add Affiliate Category</em> page', 'affiliation-manager'))),
'statistics-page' => array('name' => __('<em>Statistics</em> page', 'affiliation-manager')),
'front-office-page' => array('name' => __('<em>Front Office</em> page', 'affiliation-manager')),
'back-office-page' => array('name' => __('<em>Back Office</em> page', 'affiliation-manager'), 'required' => 'yes'));

$modules['click'] = array(
'general-informations' => array('name' => __('General informations', 'affiliation-manager'), 'required' => 'yes'),
'click-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Click</em> page', 'affiliation-manager') : __('<em>Add Click</em> page', 'affiliation-manager'))));

$modules['front_office'] = array(
'registration-form' => array('name' => __('Registration form', 'affiliation-manager'), 'modules' => array(
	'registration-error-messages' => array('name' => __('Error messages', 'affiliation-manager')),
	'registration-login-availability-indicator' => array('name' => __('Login name\'s availability indicator', 'affiliation-manager')))),
'registration-compact-form' => array('name' => __('Registration compact form', 'affiliation-manager'), 'modules' => array(
	'registration-compact-error-messages' => array('name' => __('Error messages', 'affiliation-manager')),
	'registration-compact-login-availability-indicator' => array('name' => __('Login name\'s availability indicator', 'affiliation-manager')))),
'login-form' => array('name' => __('Login form', 'affiliation-manager'), 'modules' => array(
	'login-error-messages' => array('name' => __('Error messages', 'affiliation-manager')))),
'login-compact-form' => array('name' => __('Login compact form', 'affiliation-manager'), 'modules' => array(
	'login-compact-error-messages' => array('name' => __('Error messages', 'affiliation-manager')))),
'password-reset-form' => array('name' => __('Password reset form', 'affiliation-manager'), 'modules' => array(
	'password-reset-error-messages' => array('name' => __('Error messages', 'affiliation-manager')))),
'profile-form' => array('name' => __('Profile form', 'affiliation-manager'), 'modules' => array(
	'profile-error-messages' => array('name' => __('Error messages', 'affiliation-manager')),
	'profile-login-availability-indicator' => array('name' => __('Login name\'s availability indicator', 'affiliation-manager')))),
'instant-notifications-form' => array('name' => __('Instant notifications form', 'affiliation-manager'), 'modules' => array(
	'instant-notifications-error-messages' => array('name' => __('Error messages', 'affiliation-manager')))),
'bonus-proposal-form' => array('name' => __('Bonus proposal form', 'affiliation-manager'), 'modules' => array(
	'bonus-proposal-error-messages' => array('name' => __('Error messages', 'affiliation-manager')))),
'statistics-form' => array('name' => __('Statistics display form', 'affiliation-manager'), 'modules' => array(
	'date-picker' => array('name' => __('Date picker', 'affiliation-manager')))),
'statistics' => array('name' => __('Statistics', 'affiliation-manager'), 'modules' => array(
	'global-statistics' => array('name' => __('Global statistics', 'affiliation-manager')),
	'affiliates-statistics' => array('name' => __('Affiliates statistics', 'affiliation-manager')),
	'clicks-statistics' => array('name' => __('Clicks statistics', 'affiliation-manager')),
	'clients-statistics' => array('name' => __('Clients statistics', 'affiliation-manager')),
	'orders-statistics' => array('name' => __('Orders statistics', 'affiliation-manager')),
	'commissions1-statistics' => array('name' => __('Commissions statistics', 'affiliation-manager').' '.__('(level 1)', 'affiliation-manager')),
	'commissions2-statistics' => array('name' => __('Commissions statistics', 'affiliation-manager').' '.__('(level 2)', 'affiliation-manager')),
	'recurring-payments-statistics' => array('name' => __('Recurring payments statistics', 'affiliation-manager')),
	'recurring-commissions1-statistics' => array('name' => __('Recurring commissions statistics', 'affiliation-manager').' '.__('(level 1)', 'affiliation-manager')),
	'recurring-commissions2-statistics' => array('name' => __('Recurring commissions statistics', 'affiliation-manager').' '.__('(level 2)', 'affiliation-manager')),
	'prospects-statistics' => array('name' => __('Prospects statistics', 'affiliation-manager')),
	'prospects-commissions1-statistics' => array('name' => __('Prospects commissions statistics', 'affiliation-manager').' '.__('(level 1)', 'affiliation-manager')),
	'prospects-commissions2-statistics' => array('name' => __('Prospects commissions statistics', 'affiliation-manager').' '.__('(level 2)', 'affiliation-manager')),
	'messages-statistics' => array('name' => __('Messages statistics', 'affiliation-manager')),
	'messages-commissions1-statistics' => array('name' => __('Messages commissions statistics', 'affiliation-manager').' '.__('(level 1)', 'affiliation-manager')),
	'messages-commissions2-statistics' => array('name' => __('Messages commissions statistics', 'affiliation-manager').' '.__('(level 2)', 'affiliation-manager')))),
'front-office-page' => array('name' => __('<em>Front Office</em> page', 'affiliation-manager')));

$modules['instant_notifications'] = array(
'affiliate-notification-email' => array('name' => __('Affiliate registration\'s notification email', 'affiliation-manager')),
'client-notification-email' => array('name' => __('Client registration\'s notification email', 'affiliation-manager')),
'order-notification-email' => array('name' => __('Order notification email', 'affiliation-manager')),
'recurring-payment-notification-email' => array('name' => __('Recurring payment notification email', 'affiliation-manager')),
'prospect-notification-email' => array('name' => __('Prospect registration\'s notification email', 'affiliation-manager')),
'message-notification-email' => array('name' => __('Message sending\'s notification email', 'affiliation-manager')),
'instant-notifications-page' => array('name' => __('<em>Instant Notifications</em> page', 'affiliation-manager')));

$modules['options'] = array(
'general-options' => array('name' => __('General options', 'affiliation-manager'), 'required' => 'yes', 'modules' => array(
	'level-1-commission' => array('name' => __('Level 1 commission', 'affiliation-manager')),
	'level-2-commission' => array('name' => __('Level 2 commission', 'affiliation-manager')))),
'clicks' => array('name' => __('Clicks', 'affiliation-manager')),
'registration' => array('name' => __('Registration', 'affiliation-manager')),
'urls-encryption' => array('name' => __('URLs encryption', 'affiliation-manager')),
'registration-confirmation-email' => array('name' => __('Registration confirmation email', 'affiliation-manager')),
'registration-notification-email' => array('name' => __('Registration notification email', 'affiliation-manager')),
'autoresponders' => array('name' => __('Autoresponders', 'affiliation-manager')),
'autoresponders-integration' => array('name' => __('Autoresponders integration', 'affiliation-manager'), 'modules' => array(
	'aweber' => array('name' => 'AWeber'),
	'cybermailing' => array('name' => 'CyberMailing'),
	'getresponse' => array('name' => 'GetResponse'),
	'mailchimp' => array('name' => 'MailChimp'),
	'sg-autorepondeur' => array('name' => 'SG Autorépondeur'))),
'registration-as-a-client' => array('name' => __('Registration as a client', 'affiliation-manager')),
'membership' => array('name' => __('Membership', 'affiliation-manager')),
'wordpress' => array('name' => __('WordPress', 'affiliation-manager')),
'custom-instructions' => array('name' => __('Custom instructions', 'affiliation-manager'), 'modules' => array(
	'registration-custom-instructions' => array('name' => __('Registration', 'affiliation-manager')),
	'login-custom-instructions' => array('name' => __('Login ', 'affiliation-manager')),
	'logout-custom-instructions' => array('name' => __('Logout ', 'affiliation-manager')),
	'password-reset-custom-instructions' => array('name' => __('Password reset', 'affiliation-manager')),
	'profile-edit-custom-instructions' => array('name' => __('Profile edit', 'affiliation-manager')),
	'bonus-proposal-custom-instructions' => array('name' => __('Bonus proposal', 'affiliation-manager')),
	'activation-custom-instructions' => array('name' => __('Activation', 'affiliation-manager')),
	'deactivation-custom-instructions' => array('name' => __('Deactivation', 'affiliation-manager')),
	'removal-custom-instructions' => array('name' => __('Removal', 'affiliation-manager')),
	'click-custom-instructions' => array('name' => __('Click', 'affiliation-manager')))),
'login-notification-email' => array('name' => __('Login notification email', 'affiliation-manager')),
'logout-notification-email' => array('name' => __('Logout notification email', 'affiliation-manager')),
'password-reset-email' => array('name' => __('Password reset email', 'affiliation-manager')),
'password-reset-notification-email' => array('name' => __('Password reset notification email', 'affiliation-manager')),
'profile-edit-notification-email' => array('name' => __('Profile edit notification email', 'affiliation-manager')),
'bonus-proposal-email' => array('name' => __('Bonus proposal email', 'affiliation-manager')),
'activation-confirmation-email' => array('name' => __('Activation confirmation email', 'affiliation-manager')),
'activation-notification-email' => array('name' => __('Activation notification email', 'affiliation-manager')),
'deactivation-notification-email' => array('name' => __('Deactivation notification email', 'affiliation-manager')),
'removal-notification-email' => array('name' => __('Removal notification email', 'affiliation-manager')),
'forms' => array('name' => __('Forms', 'affiliation-manager'), 'modules' => array(
	'captcha' => array('name' => __('CAPTCHA', 'affiliation-manager')),
	'error-messages' => array('name' => __('Error messages', 'affiliation-manager')),
	'login-availability-indicator' => array('name' => __('Login name\'s availability indicator', 'affiliation-manager')))),
'options-page' => array('name' => __('<em>Options</em> page', 'affiliation-manager')));

$statistics_columns = array(
'data' => array('name' => __('Data', 'affiliation-manager'), 'width' => 30, 'required' => 'yes'),
'quantity' => array('name' => __('Quantity', 'affiliation-manager'), 'width' => 20),
'commissions_percentage' => array('name' => __('Percentage of commissions', 'affiliation-manager'), 'width' => 30),
'orders_percentage' => array('name' => __('Percentage of orders', 'affiliation-manager'), 'width' => 30),
'total_amount' => array('name' => __('Total amount', 'affiliation-manager'), 'width' => 20));

$statistics_rows = array(
'commissions' => array('name' => __('Commissions', 'affiliation-manager').' '.__('(level 1)', 'affiliation-manager')),
'paid_commissions' => array('name' => __('Paid commissions', 'affiliation-manager').' '.__('(level 1)', 'affiliation-manager')),
'unpaid_commissions' => array('name' => __('Unpaid commissions', 'affiliation-manager').' '.__('(level 1)', 'affiliation-manager')),
'commissions2' => array('name' => __('Commissions', 'affiliation-manager').' '.__('(level 2)', 'affiliation-manager')),
'paid_commissions2' => array('name' => __('Paid commissions', 'affiliation-manager').' '.__('(level 2)', 'affiliation-manager')),
'unpaid_commissions2' => array('name' => __('Unpaid commissions', 'affiliation-manager').' '.__('(level 2)', 'affiliation-manager')),
'recurring_commissions' => array('name' => __('Recurring commissions', 'affiliation-manager').' '.__('(level 1)', 'affiliation-manager')),
'paid_recurring_commissions' => array('name' => __('Paid recurring commissions', 'affiliation-manager').' '.__('(level 1)', 'affiliation-manager')),
'unpaid_recurring_commissions' => array('name' => __('Unpaid recurring commissions', 'affiliation-manager').' '.__('(level 1)', 'affiliation-manager')),
'recurring_commissions2' => array('name' => __('Recurring commissions', 'affiliation-manager').' '.__('(level 2)', 'affiliation-manager')),
'paid_recurring_commissions2' => array('name' => __('Paid recurring commissions', 'affiliation-manager').' '.__('(level 2)', 'affiliation-manager')),
'unpaid_recurring_commissions2' => array('name' => __('Unpaid recurring commissions', 'affiliation-manager').' '.__('(level 2)', 'affiliation-manager')),
'prospects_commissions' => array('name' => __('Prospects commissions', 'affiliation-manager').' '.__('(level 1)', 'affiliation-manager')),
'paid_prospects_commissions' => array('name' => __('Paid prospects commissions', 'affiliation-manager').' '.__('(level 1)', 'affiliation-manager')),
'unpaid_prospects_commissions' => array('name' => __('Unpaid prospects commissions', 'affiliation-manager').' '.__('(level 1)', 'affiliation-manager')),
'prospects_commissions2' => array('name' => __('Prospects commissions', 'affiliation-manager').' '.__('(level 2)', 'affiliation-manager')),
'paid_prospects_commissions2' => array('name' => __('Paid prospects commissions', 'affiliation-manager').' '.__('(level 2)', 'affiliation-manager')),
'unpaid_prospects_commissions2' => array('name' => __('Unpaid prospects commissions', 'affiliation-manager').' '.__('(level 2)', 'affiliation-manager')),
'messages_commissions' => array('name' => __('Messages commissions', 'affiliation-manager').' '.__('(level 1)', 'affiliation-manager')),
'paid_messages_commissions' => array('name' => __('Paid messages commissions', 'affiliation-manager').' '.__('(level 1)', 'affiliation-manager')),
'unpaid_messages_commissions' => array('name' => __('Unpaid messages commissions', 'affiliation-manager').' '.__('(level 1)', 'affiliation-manager')),
'messages_commissions2' => array('name' => __('Messages commissions', 'affiliation-manager').' '.__('(level 2)', 'affiliation-manager')),
'paid_messages_commissions2' => array('name' => __('Paid messages commissions', 'affiliation-manager').' '.__('(level 2)', 'affiliation-manager')),
'unpaid_messages_commissions2' => array('name' => __('Unpaid messages commissions', 'affiliation-manager').' '.__('(level 2)', 'affiliation-manager')),
'orders' => array('name' => __('Orders', 'affiliation-manager')),
'recurring_payments' => array('name' => __('Recurring payments', 'affiliation-manager')),
'clients' => array('name' => __('Clients', 'affiliation-manager')),
'prospects' => array('name' => __('Prospects', 'affiliation-manager')),
'messages' => array('name' => __('Messages', 'affiliation-manager')),
'affiliates' => array('name' => __('Affiliates', 'affiliation-manager')),
'active_affiliates' => array('name' => __('Active affiliates', 'affiliation-manager')),
'inactive_affiliates' => array('name' => __('Inactive affiliates', 'affiliation-manager')),
'deactivated_affiliates' => array('name' => __('Deactivated affiliates', 'affiliation-manager')),
'affiliates_categories' => array('name' => __('Affiliates categories', 'affiliation-manager')),
'clicks' => array('name' => __('Clicks', 'affiliation-manager')));

$roles = array(
'administrator' => array('name' => __('Administrator', 'affiliation-manager'), 'capability' => 'manage_options'),
'editor' => array('name' => __('Editor', 'affiliation-manager'), 'capability' => 'moderate_comments'),
'author' => array('name' => __('Author', 'affiliation-manager'), 'capability' => 'publish_posts'),
'contributor' => array('name' => __('Contributor', 'affiliation-manager'), 'capability' => 'edit_posts'),
'subscriber' => array('name' => __('Subscriber', 'affiliation-manager'), 'capability' => 'read'));