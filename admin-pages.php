<?php $admin_links = array(
'Documentation' => array('name' => __('Documentation', 'commerce-manager')),
'Affiliation Manager' => array('name' => __('Affiliation', 'commerce-manager')),
'Membership Manager' => array('name' => __('Membership', 'commerce-manager')),
'Optin Manager' => array('name' => __('Optin', 'commerce-manager')),
'Contact Manager' => array('name' => __('Contact', 'commerce-manager')));

$admin_pages = array(
'' => array('page_title' => 'Commerce Manager ('.__('Options', 'commerce-manager').')', 'menu_title' => __('Options', 'commerce-manager'), 'file' => 'options-page.php'),
'clients_accounts' => array('page_title' => 'Commerce Manager ('.__('Clients Accounts', 'commerce-manager').')', 'menu_title' => __('Clients Accounts', 'commerce-manager'), 'file' => 'clients-accounts-page.php'),
'product' => array('page_title' => 'Commerce Manager ('.__('Product', 'commerce-manager').')', 'menu_title' => ((($_GET['page'] == 'commerce-manager-product') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Product', 'commerce-manager') : __('Edit Product', 'commerce-manager')) : __('Add Product', 'commerce-manager')), 'file' => 'product-page.php'),
'products' => array('page_title' => 'Commerce Manager ('.__('Products', 'commerce-manager').')', 'menu_title' => __('Products', 'commerce-manager'), 'file' => 'table-page.php'),
'products_performances' => array('page_title' => 'Commerce Manager ('.__('Products Performances', 'commerce-manager').')', 'menu_title' => __('Products Performances', 'commerce-manager'), 'file' => 'table-page.php'),
'product_category' => array('page_title' => 'Commerce Manager ('.__('Product Category', 'commerce-manager').')', 'menu_title' => ((($_GET['page'] == 'commerce-manager-product-category') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Product Category', 'commerce-manager') : __('Edit Product Category', 'commerce-manager')) : __('Add Product Category', 'commerce-manager')), 'file' => 'product-page.php'),
'products_categories' => array('page_title' => 'Commerce Manager ('.__('Products Categories', 'commerce-manager').')', 'menu_title' => __('Products Categories', 'commerce-manager'), 'file' => 'table-page.php'),
'form' => array('page_title' => 'Commerce Manager ('.__('Form', 'commerce-manager').')', 'menu_title' => ((($_GET['page'] == 'commerce-manager-form') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Form', 'commerce-manager') : __('Edit Form', 'commerce-manager')) : __('Add Form', 'commerce-manager')), 'file' => 'form-page.php'),
'forms' => array('page_title' => 'Commerce Manager ('.__('Forms', 'commerce-manager').')', 'menu_title' => __('Forms', 'commerce-manager'), 'file' => 'table-page.php'),
'forms_performances' => array('page_title' => 'Commerce Manager ('.__('Forms Performances', 'commerce-manager').')', 'menu_title' => __('Forms Performances', 'commerce-manager'), 'file' => 'table-page.php'),
'form_category' => array('page_title' => 'Commerce Manager ('.__('Form Category', 'commerce-manager').')', 'menu_title' => ((($_GET['page'] == 'commerce-manager-form-category') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Form Category', 'commerce-manager') : __('Edit Form Category', 'commerce-manager')) : __('Add Form Category', 'commerce-manager')), 'file' => 'form-page.php'),
'forms_categories' => array('page_title' => 'Commerce Manager ('.__('Forms Categories', 'commerce-manager').')', 'menu_title' => __('Forms Categories', 'commerce-manager'), 'file' => 'table-page.php'),
'client' => array('page_title' => 'Commerce Manager ('.__('Client', 'commerce-manager').')', 'menu_title' => ((($_GET['page'] == 'commerce-manager-client') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Client', 'commerce-manager') : __('Edit Client', 'commerce-manager')) : __('Add Client', 'commerce-manager')), 'file' => 'client-page.php'),
'clients' => array('page_title' => 'Commerce Manager ('.__('Clients', 'commerce-manager').')', 'menu_title' => __('Clients', 'commerce-manager'), 'file' => 'table-page.php'),
'clients_performances' => array('page_title' => 'Commerce Manager ('.__('Clients Performances', 'commerce-manager').')', 'menu_title' => __('Clients Performances', 'commerce-manager'), 'file' => 'table-page.php'),
'client_category' => array('page_title' => 'Commerce Manager ('.__('Client Category', 'commerce-manager').')', 'menu_title' => ((($_GET['page'] == 'commerce-manager-client-category') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Client Category', 'commerce-manager') : __('Edit Client Category', 'commerce-manager')) : __('Add Client Category', 'commerce-manager')), 'file' => 'client-page.php'),
'clients_categories' => array('page_title' => 'Commerce Manager ('.__('Clients Categories', 'commerce-manager').')', 'menu_title' => __('Clients Categories', 'commerce-manager'), 'file' => 'table-page.php'),
'order' => array('page_title' => 'Commerce Manager ('.__('Order', 'commerce-manager').')', 'menu_title' => ((($_GET['page'] == 'commerce-manager-order') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Order', 'commerce-manager') : __('Edit Order', 'commerce-manager')) : __('Add Order', 'commerce-manager')), 'file' => 'order-page.php'),
'orders' => array('page_title' => 'Commerce Manager ('.__('Orders', 'commerce-manager').')', 'menu_title' => __('Orders', 'commerce-manager'), 'file' => 'table-page.php'),
'recurring_payment' => array('page_title' => 'Commerce Manager ('.__('Recurring Payment', 'commerce-manager').')', 'menu_title' => ((($_GET['page'] == 'commerce-manager-recurring-payment') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Recurring Payment', 'commerce-manager') : __('Edit Recurring Payment', 'commerce-manager')) : __('Add Recurring Payment', 'commerce-manager')), 'file' => 'recurring-payment-page.php'),
'recurring_payments' => array('page_title' => 'Commerce Manager ('.__('Recurring Payments', 'commerce-manager').')', 'menu_title' => __('Recurring Payments', 'commerce-manager'), 'file' => 'table-page.php'),
'statistics' => array('page_title' => 'Commerce Manager ('.__('Statistics', 'commerce-manager').')', 'menu_title' => __('Statistics', 'commerce-manager'), 'file' => 'statistics-page.php'),
'front_office' => array('page_title' => 'Commerce Manager ('.__('Front Office', 'commerce-manager').')', 'menu_title' => __('Front Office', 'commerce-manager'), 'file' => 'front-office-page.php'),
'back_office' => array('page_title' => 'Commerce Manager ('.__('Back Office', 'commerce-manager').')', 'menu_title' => __('Back Office', 'commerce-manager'), 'file' => 'back-office-page.php'));

$modules['back_office'] = array(
'capabilities' => array('name' => __('Capabilities', 'commerce-manager')),
'icon' => array('name' => __('Icon', 'commerce-manager')),
'top' => array('name' => __('Top', 'commerce-manager')),
'menu' => array('name' => __('Menu', 'commerce-manager')),
'options-page' => array('name' => __('<em>Options</em> page', 'commerce-manager')),
'clients-accounts-page' => array('name' => __('<em>Clients Accounts</em> page', 'commerce-manager')),
'product-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Product</em> page', 'commerce-manager') : __('<em>Add Product</em> page', 'commerce-manager'))),
'product-category-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Product Category</em> page', 'commerce-manager') : __('<em>Add Product Category</em> page', 'commerce-manager'))),
'form-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Form</em> page', 'commerce-manager') : __('<em>Add Form</em> page', 'commerce-manager'))),
'form-category-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Form Category</em> page', 'commerce-manager') : __('<em>Add Form Category</em> page', 'commerce-manager'))),
'client-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Client</em> page', 'commerce-manager') : __('<em>Add Client</em> page', 'commerce-manager'))),
'order-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Order</em> page', 'commerce-manager') : __('<em>Add Order</em> page', 'commerce-manager'))),
'recurring-payment-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Recurring Payment</em> page', 'commerce-manager') : __('<em>Add Recurring Payment</em> page', 'commerce-manager'))),
'statistics-page' => array('name' => __('<em>Statistics</em> page', 'commerce-manager')),
'front-office-page' => array('name' => __('<em>Front Office</em> page', 'commerce-manager')),
'back-office-page' => array('name' => __('<em>Back Office</em> page', 'commerce-manager'), 'required' => 'yes'));

$modules['client'] = array(
'personal-informations' => array('name' => __('Personal informations', 'commerce-manager'), 'required' => 'yes'),
'registration-confirmation-email' => array('name' => __('Registration confirmation email', 'commerce-manager')),
'registration-notification-email' => array('name' => __('Registration notification email', 'commerce-manager')),
'autoresponders' => array('name' => __('Autoresponders', 'commerce-manager')),
'registration-to-affiliate-program' => array('name' => __('Registration to affiliate program', 'commerce-manager')),
'membership' => array('name' => __('Membership', 'commerce-manager')),
'wordpress' => array('name' => __('WordPress', 'commerce-manager')),
'custom-instructions' => array('name' => __('Custom instructions', 'commerce-manager')),
'client-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Client</em> page', 'commerce-manager') : __('<em>Add Client</em> page', 'commerce-manager'))));

$add_client_modules = array(
'registration-confirmation-email',
'registration-notification-email',
'autoresponders',
'registration-to-affiliate-program',
'membership',
'wordpress',
'custom-instructions');

$add_client_fields = array(
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
'client_subscribed_to_autoresponder',
'client_autoresponder',
'client_autoresponder_list',
'client_subscribed_to_affiliate_program',
'client_affiliate_category_id',
'client_affiliate_status',
'client_affiliation_registration_confirmation_email_sent',
'client_affiliation_registration_notification_email_sent',
'client_subscribed_to_members_areas',
'client_members_areas',
'client_member_category_id',
'client_member_status',
'client_membership_registration_confirmation_email_sent',
'client_membership_registration_notification_email_sent',
'client_subscribed_as_a_user',
'client_user_role',
'registration_custom_instructions_executed',
'registration_custom_instructions');

$modules['client_category'] = array(
'general-informations' => array('name' => __('General informations', 'commerce-manager'), 'required' => 'yes'),
'client-category-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Client Category</em> page', 'commerce-manager') : __('<em>Add Client Category</em> page', 'commerce-manager'))));

$modules['clients_accounts'] = array(
'registration' => array('name' => __('Registration', 'commerce-manager')),
'registration-confirmation-email' => array('name' => __('Registration confirmation email', 'commerce-manager')),
'registration-notification-email' => array('name' => __('Registration notification email', 'commerce-manager')),
'autoresponders' => array('name' => __('Autoresponders', 'commerce-manager')),
'registration-to-affiliate-program' => array('name' => __('Registration to affiliate program', 'commerce-manager')),
'membership' => array('name' => __('Membership', 'commerce-manager')),
'wordpress' => array('name' => __('WordPress', 'commerce-manager')),
'custom-instructions' => array('name' => __('Custom instructions', 'commerce-manager'), 'modules' => array(
	'registration-custom-instructions' => array('name' => __('Registration', 'commerce-manager')),
	'login-custom-instructions' => array('name' => __('Login ', 'commerce-manager')),
	'logout-custom-instructions' => array('name' => __('Logout ', 'commerce-manager')),
	'password-reset-custom-instructions' => array('name' => __('Password reset', 'commerce-manager')),
	'profile-edit-custom-instructions' => array('name' => __('Profile edit', 'commerce-manager')),
	'activation-custom-instructions' => array('name' => __('Activation', 'commerce-manager')),
	'deactivation-custom-instructions' => array('name' => __('Deactivation', 'commerce-manager')),
	'removal-custom-instructions' => array('name' => __('Removal', 'commerce-manager')))),
'login-notification-email' => array('name' => __('Login notification email', 'commerce-manager')),
'logout-notification-email' => array('name' => __('Logout notification email', 'commerce-manager')),
'password-reset-email' => array('name' => __('Password reset email', 'commerce-manager')),
'password-reset-notification-email' => array('name' => __('Password reset notification email', 'commerce-manager')),
'profile-edit-notification-email' => array('name' => __('Profile edit notification email', 'commerce-manager')),
'activation-confirmation-email' => array('name' => __('Activation confirmation email', 'commerce-manager')),
'activation-notification-email' => array('name' => __('Activation notification email', 'commerce-manager')),
'deactivation-notification-email' => array('name' => __('Deactivation notification email', 'commerce-manager')),
'removal-notification-email' => array('name' => __('Removal notification email', 'commerce-manager')),
'clients-accounts-page' => array('name' => __('<em>Clients Accounts</em> page', 'commerce-manager')));

$modules['form'] = array(
'general-informations' => array('name' => __('General informations', 'commerce-manager'), 'required' => 'yes'),
'counters' => array('name' => __('Counters', 'commerce-manager')),
'form' => array('name' => __('Form', 'commerce-manager'), 'modules' => array(
	'default-values' => array('name' => __('Default values', 'commerce-manager')),
	'error-messages' => array('name' => __('Error messages', 'commerce-manager')))),
'form-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Form</em> page', 'commerce-manager') : __('<em>Add Form</em> page', 'commerce-manager'))));

$modules['form_category'] = $modules['form'];
foreach (array('counters', 'form-page') as $field) { unset($modules['form_category'][$field]); }
$modules['form_category'] = array_merge($modules['form_category'], array(
'form-category-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Form Category</em> page', 'commerce-manager') : __('<em>Add Form Category</em> page', 'commerce-manager')))));

$modules['front_office'] = array(
'registration-form' => array('name' => __('Registration form', 'commerce-manager'), 'modules' => array(
	'registration-error-messages' => array('name' => __('Error messages', 'commerce-manager')),
	'registration-login-availability-indicator' => array('name' => __('Login name\'s availability indicator', 'commerce-manager')))),
'registration-compact-form' => array('name' => __('Registration compact form', 'commerce-manager'), 'modules' => array(
	'registration-compact-error-messages' => array('name' => __('Error messages', 'commerce-manager')),
	'registration-compact-login-availability-indicator' => array('name' => __('Login name\'s availability indicator', 'commerce-manager')))),
'login-form' => array('name' => __('Login form', 'commerce-manager'), 'modules' => array(
	'login-error-messages' => array('name' => __('Error messages', 'commerce-manager')))),
'login-compact-form' => array('name' => __('Login compact form', 'commerce-manager'), 'modules' => array(
	'login-compact-error-messages' => array('name' => __('Error messages', 'commerce-manager')))),
'password-reset-form' => array('name' => __('Password reset form', 'commerce-manager'), 'modules' => array(
	'password-reset-error-messages' => array('name' => __('Error messages', 'commerce-manager')))),
'redelivery-form' => array('name' => __('Redelivery form', 'commerce-manager'), 'modules' => array(
	'redelivery-error-messages' => array('name' => __('Error messages', 'commerce-manager')))),
'profile-form' => array('name' => __('Profile form', 'commerce-manager'), 'modules' => array(
	'profile-error-messages' => array('name' => __('Error messages', 'commerce-manager')),
	'profile-login-availability-indicator' => array('name' => __('Login name\'s availability indicator', 'commerce-manager')))),
'statistics-form' => array('name' => __('Statistics display form', 'commerce-manager'), 'modules' => array(
	'date-picker' => array('name' => __('Date picker', 'commerce-manager')))),
'statistics' => array('name' => __('Statistics', 'commerce-manager'), 'modules' => array(
	'global-statistics' => array('name' => __('Global statistics', 'commerce-manager')),
	'orders-statistics' => array('name' => __('Orders statistics', 'commerce-manager')),
	'recurring-payments-statistics' => array('name' => __('Recurring payments statistics', 'commerce-manager')))),
'front-office-page' => array('name' => __('<em>Front Office</em> page', 'commerce-manager')));

$modules['options'] = array(
'general-options' => array('name' => __('General options', 'commerce-manager'), 'required' => 'yes'),
'tax' => array('name' => __('Tax', 'commerce-manager')),
'shipping' => array('name' => __('Shipping', 'commerce-manager')),
'recurring-payments' => array('name' => __('Recurring payments', 'commerce-manager'), 'modules' => array(
	'payment-in-2-times' => array('name' => __('Payment in 2 times', 'commerce-manager')),
	'payment-in-3-times' => array('name' => __('Payment in 3 times', 'commerce-manager')),
	'payment-in-4-times' => array('name' => __('Payment in 4 times', 'commerce-manager')),
	'subscription' => array('name' => __('Subscription', 'commerce-manager')))),
'payment-modes' => array('name' => __('Payment modes', 'commerce-manager')),
'urls-encryption' => array('name' => __('URLs encryption', 'commerce-manager')),
'order-confirmation-email' => array('name' => __('Order confirmation email', 'commerce-manager')),
'order-notification-email' => array('name' => __('Order notification email', 'commerce-manager')),
'autoresponders' => array('name' => __('Autoresponders', 'commerce-manager')),
'autoresponders-integration' => array('name' => __('Autoresponders integration', 'commerce-manager'), 'modules' => array(
	'aweber' => array('name' => 'AWeber'),
	'cybermailing' => array('name' => 'CyberMailing'),
	'getresponse' => array('name' => 'GetResponse'),
	'mailchimp' => array('name' => 'MailChimp'),
	'sg-autorepondeur' => array('name' => 'SG Autorépondeur'))),
'registration-as-a-client' => array('name' => __('Registration as a client', 'commerce-manager')),
'registration-to-affiliate-program' => array('name' => __('Registration to affiliate program', 'commerce-manager')),
'membership' => array('name' => __('Membership', 'commerce-manager')),
'wordpress' => array('name' => __('WordPress', 'commerce-manager')),
'custom-instructions' => array('name' => __('Custom instructions', 'commerce-manager'), 'modules' => array(
	'order-custom-instructions' => array('name' => __('New order', 'commerce-manager')),
	'order-processing-custom-instructions' => array('name' => __('Order processing', 'commerce-manager')),
	'order-refund-custom-instructions' => array('name' => __('Order refund', 'commerce-manager')),
	'order-removal-custom-instructions' => array('name' => __('Order removal', 'commerce-manager')),
	'recurring-payment-custom-instructions' => array('name' => __('New recurring payment', 'commerce-manager')),
	'recurring-payment-refund-custom-instructions' => array('name' => __('Recurring payment refund', 'commerce-manager')),
	'recurring-payment-removal-custom-instructions' => array('name' => __('Recurring payment removal', 'commerce-manager')),
	'recurring-payments-profile-deactivation-custom-instructions' => array('name' => __('Recurring payments profile deactivation', 'commerce-manager')))),
'redelivery-email' => array('name' => __('Redelivery email', 'commerce-manager')),
'redelivery-notification-email' => array('name' => __('Redelivery notification email', 'commerce-manager')),
'order-processing-notification-email' => array('name' => __('Order processing\'s notification email', 'commerce-manager')),
'order-refund-notification-email' => array('name' => __('Order refund\'s notification email', 'commerce-manager')),
'recurring-payment-confirmation-email' => array('name' => __('Recurring payment confirmation email', 'commerce-manager')),
'recurring-payment-notification-email' => array('name' => __('Recurring payment notification email', 'commerce-manager')),
'recurring-payment-refund-notification-email' => array('name' => __('Recurring payment refund\'s notification email', 'commerce-manager')),
'recurring-payments-profile-deactivation-notification-email' => array('name' => __('Recurring payments profile deactivation\'s notification email', 'commerce-manager')),
'forms' => array('name' => __('Forms', 'commerce-manager'), 'modules' => array(
	'default-values' => array('name' => __('Default values', 'commerce-manager')),
	'captcha' => array('name' => __('CAPTCHA', 'commerce-manager')),
	'error-messages' => array('name' => __('Error messages', 'commerce-manager')),
	'login-availability-indicator' => array('name' => __('Login name\'s availability indicator', 'commerce-manager')))),
'options-page' => array('name' => __('<em>Options</em> page', 'commerce-manager')));

$modules['order'] = array(
'general-informations' => array('name' => __('General informations', 'commerce-manager'), 'required' => 'yes'),
'recurring-payments' => array('name' => __('Recurring payments', 'commerce-manager')),
'customer' => array('name' => __('Customer', 'commerce-manager'), 'required' => 'yes'),
'affiliation' => array('name' => __('Affiliation', 'commerce-manager'), 'modules' => array(
	'level-1-commission' => array('name' => __('Level 1 commission', 'commerce-manager')),
	'level-2-commission' => array('name' => __('Level 2 commission', 'commerce-manager')))),
'order-confirmation-email' => array('name' => __('Order confirmation email', 'commerce-manager')),
'order-notification-email' => array('name' => __('Order notification email', 'commerce-manager')),
'autoresponders' => array('name' => __('Autoresponders', 'commerce-manager')),
'registration-as-a-client' => array('name' => __('Registration as a client', 'commerce-manager')),
'registration-to-affiliate-program' => array('name' => __('Registration to affiliate program', 'commerce-manager')),
'membership' => array('name' => __('Membership', 'commerce-manager')),
'wordpress' => array('name' => __('WordPress', 'commerce-manager')),
'custom-instructions' => array('name' => __('Custom instructions', 'commerce-manager')),
'order-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Order</em> page', 'commerce-manager') : __('<em>Add Order</em> page', 'commerce-manager'))));

$add_order_modules = array(
'order-confirmation-email',
'order-notification-email',
'autoresponders',
'registration-as-a-client',
'registration-to-affiliate-program',
'membership',
'wordpress',
'custom-instructions');

$add_order_fields = array(
'order_confirmation_email_sent',
'order_confirmation_email_sender',
'order_confirmation_email_receiver',
'order_confirmation_email_subject',
'order_confirmation_email_body',
'order_notification_email_sent',
'order_notification_email_sender',
'order_notification_email_receiver',
'order_notification_email_subject',
'order_notification_email_body',
'customer_subscribed_to_autoresponder',
'customer_autoresponder',
'customer_autoresponder_list',
'customer_subscribed_as_a_client',
'customer_client_category_id',
'customer_client_status',
'commerce_registration_confirmation_email_sent',
'commerce_registration_notification_email_sent',
'customer_subscribed_to_affiliate_program',
'customer_affiliate_category_id',
'customer_affiliate_status',
'affiliation_registration_confirmation_email_sent',
'affiliation_registration_notification_email_sent',
'customer_subscribed_to_members_areas',
'customer_members_areas',
'customer_member_category_id',
'customer_member_status',
'membership_registration_confirmation_email_sent',
'membership_registration_notification_email_sent',
'customer_subscribed_as_a_user',
'customer_user_role',
'order_custom_instructions_executed',
'order_custom_instructions');

$modules['product'] = array(
'general-informations' => array('name' => __('General informations', 'commerce-manager'), 'required' => 'yes'),
'inventory' => array('name' => __('Inventory', 'commerce-manager')),
'order' => array('name' => __('Order', 'commerce-manager')),
'tax' => array('name' => __('Tax', 'commerce-manager')),
'shipping' => array('name' => __('Shipping', 'commerce-manager')),
'recurring-payments' => array('name' => __('Recurring payments', 'commerce-manager'), 'modules' => array(
	'payment-option1' => array('name' => __('Option 1', 'commerce-manager')),
	'payment-option2' => array('name' => __('Option 2', 'commerce-manager')),
	'payment-option3' => array('name' => __('Option 3', 'commerce-manager')))),
'payment-modes' => array('name' => __('Payment modes', 'commerce-manager')),
'order-confirmation-email' => array('name' => __('Order confirmation email', 'commerce-manager')),
'order-notification-email' => array('name' => __('Order notification email', 'commerce-manager')),
'autoresponders' => array('name' => __('Autoresponders', 'commerce-manager')),
'registration-as-a-client' => array('name' => __('Registration as a client', 'commerce-manager')),
'registration-to-affiliate-program' => array('name' => __('Registration to affiliate program', 'commerce-manager')),
'membership' => array('name' => __('Membership', 'commerce-manager')),
'wordpress' => array('name' => __('WordPress', 'commerce-manager')),
'custom-instructions' => array('name' => __('Custom instructions', 'commerce-manager'), 'modules' => array(
	'order-custom-instructions' => array('name' => __('New order', 'commerce-manager')),
	'order-processing-custom-instructions' => array('name' => __('Order processing', 'commerce-manager')),
	'order-refund-custom-instructions' => array('name' => __('Order refund', 'commerce-manager')),
	'recurring-payment-custom-instructions' => array('name' => __('New recurring payment', 'commerce-manager')),
	'recurring-payment-refund-custom-instructions' => array('name' => __('Recurring payment refund', 'commerce-manager')),
	'recurring-payments-profile-deactivation-custom-instructions' => array('name' => __('Recurring payments profile deactivation', 'commerce-manager')))),
'redelivery-email' => array('name' => __('Redelivery email', 'commerce-manager')),
'redelivery-notification-email' => array('name' => __('Redelivery notification email', 'commerce-manager')),
'order-processing-notification-email' => array('name' => __('Order processing\'s notification email', 'commerce-manager')),
'order-refund-notification-email' => array('name' => __('Order refund\'s notification email', 'commerce-manager')),
'recurring-payment-confirmation-email' => array('name' => __('Recurring payment confirmation email', 'commerce-manager')),
'recurring-payment-notification-email' => array('name' => __('Recurring payment notification email', 'commerce-manager')),
'recurring-payment-refund-notification-email' => array('name' => __('Recurring payment refund\'s notification email', 'commerce-manager')),
'recurring-payments-profile-deactivation-notification-email' => array('name' => __('Recurring payments profile deactivation\'s notification email', 'commerce-manager')),
'affiliation' => array('name' => __('Affiliation', 'commerce-manager'), 'modules' => array(
	'level-1-commission' => array('name' => __('Level 1 commission', 'commerce-manager')),
	'level-2-commission' => array('name' => __('Level 2 commission', 'commerce-manager')))),
'product-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Product</em> page', 'commerce-manager') : __('<em>Add Product</em> page', 'commerce-manager'))));

$modules['product_category'] = $modules['product'];
foreach (array('inventory', 'product-page') as $field) { unset($modules['product_category'][$field]); }
$modules['product_category'] = array_merge($modules['product_category'], array(
'product-category-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Product Category</em> page', 'commerce-manager') : __('<em>Add Product Category</em> page', 'commerce-manager')))));

$modules['recurring_payment'] = array(
'general-informations' => array('name' => __('General informations', 'commerce-manager'), 'required' => 'yes'),
'affiliation' => array('name' => __('Affiliation', 'commerce-manager'), 'modules' => array(
	'level-1-commission' => array('name' => __('Level 1 commission', 'commerce-manager')),
	'level-2-commission' => array('name' => __('Level 2 commission', 'commerce-manager')))),
'recurring-payment-confirmation-email' => array('name' => __('Recurring payment confirmation email', 'commerce-manager')),
'recurring-payment-notification-email' => array('name' => __('Recurring payment notification email', 'commerce-manager')),
'custom-instructions' => array('name' => __('Custom instructions', 'commerce-manager')),
'recurring-payment-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Recurring Payment</em> page', 'commerce-manager') : __('<em>Add Recurring Payment</em> page', 'commerce-manager'))));

$add_recurring_payment_modules = array(
'recurring-payment-confirmation-email',
'recurring-payment-notification-email',
'custom-instructions');

$add_recurring_payment_fields = array(
'recurring_payment_confirmation_email_sent',
'recurring_payment_confirmation_email_sender',
'recurring_payment_confirmation_email_receiver',
'recurring_payment_confirmation_email_subject',
'recurring_payment_confirmation_email_body',
'recurring_payment_notification_email_sent',
'recurring_payment_notification_email_sender',
'recurring_payment_notification_email_receiver',
'recurring_payment_notification_email_subject',
'recurring_payment_notification_email_body',
'recurring_payment_custom_instructions_executed',
'recurring_payment_custom_instructions');

$statistics_columns = array(
'data' => array('name' => __('Data', 'commerce-manager'), 'width' => 30, 'required' => 'yes'),
'quantity' => array('name' => __('Quantity', 'commerce-manager'), 'width' => 20),
'orders_percentage' => array('name' => __('Percentage of orders', 'commerce-manager'), 'width' => 30),
'net_prices' => array('name' => __('Net prices', 'commerce-manager'), 'width' => 20),
'taxes' => array('name' => __('Taxes', 'commerce-manager'), 'width' => 20),
'shipping_costs' => array('name' => __('Shipping costs', 'commerce-manager'), 'width' => 20),
'transaction_costs' => array('name' => __('Transaction costs', 'commerce-manager'), 'width' => 20),
'total_amount' => array('name' => __('Total amount', 'commerce-manager'), 'width' => 20));

$statistics_rows = array(
'orders' => array('name' => __('Orders', 'commerce-manager')),
'processed_orders' => array('name' => __('Processed and unrefunded orders', 'commerce-manager')),
'unprocessed_orders' => array('name' => __('Unprocessed orders', 'commerce-manager')),
'refunded_orders' => array('name' => __('Refunded orders', 'commerce-manager')),
'recurring_payments' => array('name' => __('Recurring payments', 'commerce-manager')),
'received_recurring_payments' => array('name' => __('Received and unrefunded recurring payments', 'commerce-manager')),
'refunded_recurring_payments' => array('name' => __('Refunded recurring payments', 'commerce-manager')),
'products' => array('name' => __('Products', 'commerce-manager')),
'products_categories' => array('name' => __('Products Categories', 'commerce-manager')),
'sold_items' => array('name' => __('Sold items', 'commerce-manager')),
'clients' => array('name' => __('Clients', 'commerce-manager')),
'active_clients' => array('name' => __('Active clients', 'commerce-manager')),
'inactive_clients' => array('name' => __('Inactive clients', 'commerce-manager')),
'deactivated_clients' => array('name' => __('Deactivated clients', 'commerce-manager')),
'clients_categories' => array('name' => __('Clients categories', 'commerce-manager')),
'forms' => array('name' => __('Forms', 'commerce-manager')),
'forms_categories' => array('name' => __('Forms categories', 'commerce-manager')));

$roles = array(
'administrator' => array('name' => __('Administrator', 'commerce-manager'), 'capability' => 'manage_options'),
'editor' => array('name' => __('Editor', 'commerce-manager'), 'capability' => 'moderate_comments'),
'author' => array('name' => __('Author', 'commerce-manager'), 'capability' => 'publish_posts'),
'contributor' => array('name' => __('Contributor', 'commerce-manager'), 'capability' => 'edit_posts'),
'subscriber' => array('name' => __('Subscriber', 'commerce-manager'), 'capability' => 'read'));