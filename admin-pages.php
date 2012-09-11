<?php $admin_links = array(
'Documentation' => array('name' => __('Documentation', 'optin-manager')),
'Commerce Manager' => array('name' => __('Commerce', 'optin-manager')),
'Affiliation Manager' => array('name' => __('Affiliation', 'optin-manager')),
'Membership Manager' => array('name' => __('Membership', 'optin-manager')),
'Contact Manager' => array('name' => __('Contact', 'optin-manager')));

$admin_pages = array(
'' => array('page_title' => 'Optin Manager ('.__('Options', 'optin-manager').')', 'menu_title' => __('Options', 'optin-manager'), 'file' => 'options-page.php'),
'form' => array('page_title' => 'Optin Manager ('.__('Form', 'optin-manager').')', 'menu_title' => ((($_GET['page'] == 'optin-manager-form') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Form', 'optin-manager') : __('Edit Form', 'optin-manager')) : __('Add Form', 'optin-manager')), 'file' => 'form-page.php'),
'forms' => array('page_title' => 'Optin Manager ('.__('Forms', 'optin-manager').')', 'menu_title' => __('Forms', 'optin-manager'), 'file' => 'table-page.php'),
'form_category' => array('page_title' => 'Optin Manager ('.__('Form Category', 'optin-manager').')', 'menu_title' => ((($_GET['page'] == 'optin-manager-form-category') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Form Category', 'optin-manager') : __('Edit Form Category', 'optin-manager')) : __('Add Form Category', 'optin-manager')), 'file' => 'form-page.php'),
'forms_categories' => array('page_title' => 'Optin Manager ('.__('Forms Categories', 'optin-manager').')', 'menu_title' => __('Forms Categories', 'optin-manager'), 'file' => 'table-page.php'),
'prospect' => array('page_title' => 'Optin Manager ('.__('Prospect', 'optin-manager').')', 'menu_title' => ((($_GET['page'] == 'optin-manager-prospect') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Prospect', 'optin-manager') : __('Edit Prospect', 'optin-manager')) : __('Add Prospect', 'optin-manager')), 'file' => 'prospect-page.php'),
'prospects' => array('page_title' => 'Optin Manager ('.__('Prospects', 'optin-manager').')', 'menu_title' => __('Prospects', 'optin-manager'), 'file' => 'table-page.php'),
'statistics' => array('page_title' => 'Optin Manager ('.__('Statistics', 'optin-manager').')', 'menu_title' => __('Statistics', 'optin-manager'), 'file' => 'statistics-page.php'),
'back_office' => array('page_title' => 'Optin Manager ('.__('Back Office', 'optin-manager').')', 'menu_title' => __('Back Office', 'optin-manager'), 'file' => 'back-office-page.php'));

$modules['back_office'] = array(
'capabilities' => array('name' => __('Capabilities', 'optin-manager')),
'icon' => array('name' => __('Icon', 'optin-manager')),
'top' => array('name' => __('Top', 'optin-manager')),
'menu' => array('name' => __('Menu', 'optin-manager')),
'options-page' => array('name' => __('<em>Options</em> page', 'optin-manager')),
'form-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Form</em> page', 'optin-manager') : __('<em>Add Form</em> page', 'optin-manager'))),
'form-category-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Form Category</em> page', 'optin-manager') : __('<em>Add Form Category</em> page', 'optin-manager'))),
'prospect-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Prospect</em> page', 'optin-manager') : __('<em>Add Prospect</em> page', 'optin-manager'))),
'statistics-page' => array('name' => __('<em>Statistics</em> page', 'optin-manager')),
'back-office-page' => array('name' => __('<em>Back Office</em> page', 'optin-manager'), 'required' => 'yes'));

$modules['form'] = array(
'general-informations' => array('name' => __('General informations', 'optin-manager'), 'required' => 'yes'),
'counters' => array('name' => __('Counters', 'optin-manager')),
'autoresponders' => array('name' => __('Autoresponders', 'optin-manager')),
'form' => array('name' => __('Form', 'optin-manager'), 'modules' => array(
	'error-messages' => array('name' => __('Error messages', 'optin-manager')))),
'registration' => array('name' => __('Registration', 'optin-manager')),
'registration-confirmation-email' => array('name' => __('Registration confirmation email', 'optin-manager')),
'registration-notification-email' => array('name' => __('Registration notification email', 'optin-manager')),
'registration-as-a-client' => array('name' => __('Registration as a client', 'optin-manager')),
'registration-to-affiliate-program' => array('name' => __('Registration to affiliate program', 'optin-manager')),
'membership' => array('name' => __('Membership', 'optin-manager')),
'wordpress' => array('name' => __('WordPress', 'optin-manager')),
'custom-instructions' => array('name' => __('Custom instructions', 'optin-manager')),
'activation-confirmation-email' => array('name' => __('Activation confirmation email', 'optin-manager')),
'activation-notification-email' => array('name' => __('Activation notification email', 'optin-manager')),
'affiliation' => array('name' => __('Affiliation', 'optin-manager'), 'modules' => array(
	'level-1-commission' => array('name' => __('Level 1 commission', 'optin-manager')),
	'level-2-commission' => array('name' => __('Level 2 commission', 'optin-manager')))),
'form-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Form</em> page', 'optin-manager') : __('<em>Add Form</em> page', 'optin-manager'))));

$modules['form_category'] = $modules['form'];
foreach (array('counters', 'form-page') as $field) { unset($modules['form_category'][$field]); }
$modules['form_category'] = array_merge($modules['form_category'], array(
'form-category-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Form Category</em> page', 'optin-manager') : __('<em>Add Form Category</em> page', 'optin-manager')))));

$modules['options'] = array(
'autoresponders' => array('name' => __('Autoresponders', 'optin-manager')),
'autoresponders-integration' => array('name' => __('Autoresponders integration', 'optin-manager'), 'modules' => array(
	'aweber' => array('name' => 'AWeber'),
	'cybermailing' => array('name' => 'CyberMailing'),
	'getresponse' => array('name' => 'GetResponse'),
	'mailchimp' => array('name' => 'MailChimp'),
	'sg-autorepondeur' => array('name' => 'SG Autorépondeur'))),
'automatic-display' => array('name' => __('Automatic display', 'optin-manager')),
'form' => array('name' => __('Form', 'optin-manager'), 'modules' => array(
	'captcha' => array('name' => __('CAPTCHA', 'optin-manager')),
	'error-messages' => array('name' => __('Error messages', 'optin-manager')))),
'registration' => array('name' => __('Registration', 'optin-manager')),
'urls-encryption' => array('name' => __('URLs encryption', 'optin-manager')),
'registration-confirmation-email' => array('name' => __('Registration confirmation email', 'optin-manager')),
'registration-notification-email' => array('name' => __('Registration notification email', 'optin-manager')),
'registration-as-a-client' => array('name' => __('Registration as a client', 'optin-manager')),
'registration-to-affiliate-program' => array('name' => __('Registration to affiliate program', 'optin-manager')),
'membership' => array('name' => __('Membership', 'optin-manager')),
'wordpress' => array('name' => __('WordPress', 'optin-manager')),
'custom-instructions' => array('name' => __('Custom instructions', 'optin-manager'), 'modules' => array(
	'registration-custom-instructions' => array('name' => __('Registration', 'optin-manager')),
	'activation-custom-instructions' => array('name' => __('Activation', 'optin-manager')),
	'deactivation-custom-instructions' => array('name' => __('Deactivation', 'optin-manager')),
	'removal-custom-instructions' => array('name' => __('Removal', 'optin-manager')))),
'activation-confirmation-email' => array('name' => __('Activation confirmation email', 'optin-manager')),
'activation-notification-email' => array('name' => __('Activation notification email', 'optin-manager')),
'affiliation' => array('name' => __('Affiliation', 'optin-manager'), 'modules' => array(
	'level-1-commission' => array('name' => __('Level 1 commission', 'optin-manager')),
	'level-2-commission' => array('name' => __('Level 2 commission', 'optin-manager')))),
'options-page' => array('name' => __('<em>Options</em> page', 'optin-manager')));

$modules['prospect'] = array(
'personal-informations' => array('name' => __('Personal informations', 'optin-manager'), 'required' => 'yes'),
'autoresponders' => array('name' => __('Autoresponders', 'optin-manager'), 'required' => 'yes'),
'affiliation' => array('name' => __('Affiliation', 'optin-manager'), 'modules' => array(
	'level-1-commission' => array('name' => __('Level 1 commission', 'optin-manager')),
	'level-2-commission' => array('name' => __('Level 2 commission', 'optin-manager')))),
'registration-confirmation-email' => array('name' => __('Registration confirmation email', 'optin-manager')),
'registration-notification-email' => array('name' => __('Registration notification email', 'optin-manager')),
'registration-as-a-client' => array('name' => __('Registration as a client', 'optin-manager')),
'registration-to-affiliate-program' => array('name' => __('Registration to affiliate program', 'optin-manager')),
'membership' => array('name' => __('Membership', 'optin-manager')),
'wordpress' => array('name' => __('WordPress', 'optin-manager')),
'custom-instructions' => array('name' => __('Custom instructions', 'optin-manager')),
'prospect-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Prospect</em> page', 'optin-manager') : __('<em>Add Prospect</em> page', 'optin-manager'))));

$add_prospect_modules = array(
'registration-confirmation-email',
'registration-notification-email',
'registration-as-a-client',
'registration-to-affiliate-program',
'membership',
'wordpress',
'custom-instructions');

$add_prospect_fields = array(
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
'prospect_subscribed_as_a_client',
'prospect_client_category_id',
'prospect_client_status',
'commerce_registration_confirmation_email_sent',
'commerce_registration_notification_email_sent',
'prospect_subscribed_to_affiliate_program',
'prospect_affiliate_category_id',
'prospect_affiliate_status',
'affiliation_registration_confirmation_email_sent',
'affiliation_registration_notification_email_sent',
'prospect_subscribed_to_members_areas',
'prospect_members_areas',
'prospect_member_category_id',
'prospect_member_status',
'membership_registration_confirmation_email_sent',
'membership_registration_notification_email_sent',
'prospect_subscribed_as_a_user',
'prospect_user_role',
'registration_custom_instructions_executed',
'registration_custom_instructions');

$statistics_columns = array(
'data' => array('name' => __('Data', 'optin-manager'), 'width' => 30, 'required' => 'yes'),
'quantity' => array('name' => __('Quantity', 'optin-manager'), 'width' => 20),
'prospects_percentage' => array('name' => __('Percentage of prospects', 'optin-manager'), 'width' => 30));

$statistics_rows = array(
'prospects' => array('name' => __('Prospects', 'optin-manager')),
'active_prospects' => array('name' => __('Active prospects', 'optin-manager')),
'inactive_prospects' => array('name' => __('Inactive prospects', 'optin-manager')),
'deactivated_prospects' => array('name' => __('Deactivated prospects', 'optin-manager')),
'forms' => array('name' => __('Forms', 'optin-manager')),
'forms_categories' => array('name' => __('Forms categories', 'optin-manager')));

$roles = array(
'administrator' => array('name' => __('Administrator', 'optin-manager'), 'capability' => 'manage_options'),
'editor' => array('name' => __('Editor', 'optin-manager'), 'capability' => 'moderate_comments'),
'author' => array('name' => __('Author', 'optin-manager'), 'capability' => 'publish_posts'),
'contributor' => array('name' => __('Contributor', 'optin-manager'), 'capability' => 'edit_posts'),
'subscriber' => array('name' => __('Subscriber', 'optin-manager'), 'capability' => 'read'));