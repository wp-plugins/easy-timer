<?php $admin_links = array(
'Documentation' => array('name' => __('Documentation', 'membership-manager')),
'Commerce Manager' => array('name' => __('Commerce', 'membership-manager')),
'Affiliation Manager' => array('name' => __('Affiliation', 'membership-manager')),
'Optin Manager' => array('name' => __('Optin', 'membership-manager')),
'Contact Manager' => array('name' => __('Contact', 'membership-manager')));

$admin_pages = array(
'' => array('page_title' => 'Membership Manager ('.__('Options', 'membership-manager').')', 'menu_title' => __('Options', 'membership-manager'), 'file' => 'options-page.php'),
'member_area' => array('page_title' => 'Membership Manager ('.__('Member Area', 'membership-manager').')', 'menu_title' => ((($_GET['page'] == 'membership-manager-member-area') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Member Area', 'membership-manager') : __('Edit Member Area', 'membership-manager')) : __('Add Member Area', 'membership-manager')), 'file' => 'member-area-page.php'),
'members_areas' => array('page_title' => 'Membership Manager ('.__('Members Areas', 'membership-manager').')', 'menu_title' => __('Members Areas', 'membership-manager'), 'file' => 'table-page.php'),
'member_area_category' => array('page_title' => 'Membership Manager ('.__('Member Area Category', 'membership-manager').')', 'menu_title' => ((($_GET['page'] == 'membership-manager-member-area-category') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Member Area Category', 'membership-manager') : __('Edit Member Area Category', 'membership-manager')) : __('Add Member Area Category', 'membership-manager')), 'file' => 'member-area-page.php'),
'members_areas_categories' => array('page_title' => 'Membership Manager ('.__('Members Areas Categories', 'membership-manager').')', 'menu_title' => __('Members Areas Categories', 'membership-manager'), 'file' => 'table-page.php'),
'member' => array('page_title' => 'Membership Manager ('.__('Member', 'membership-manager').')', 'menu_title' => ((($_GET['page'] == 'membership-manager-member') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Member', 'membership-manager') : __('Edit Member', 'membership-manager')) : __('Add Member', 'membership-manager')), 'file' => 'member-page.php'),
'members' => array('page_title' => 'Membership Manager ('.__('Members', 'membership-manager').')', 'menu_title' => __('Members', 'membership-manager'), 'file' => 'table-page.php'),
'member_category' => array('page_title' => 'Membership Manager ('.__('Member Category', 'membership-manager').')', 'menu_title' => ((($_GET['page'] == 'membership-manager-member-category') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Member Category', 'membership-manager') : __('Edit Member Category', 'membership-manager')) : __('Add Member Category', 'membership-manager')), 'file' => 'member-page.php'),
'members_categories' => array('page_title' => 'Membership Manager ('.__('Members Categories', 'membership-manager').')', 'menu_title' => __('Members Categories', 'membership-manager'), 'file' => 'table-page.php'),
'statistics' => array('page_title' => 'Membership Manager ('.__('Statistics', 'membership-manager').')', 'menu_title' => __('Statistics', 'membership-manager'), 'file' => 'statistics-page.php'),
'front_office' => array('page_title' => 'Membership Manager ('.__('Front Office', 'membership-manager').')', 'menu_title' => __('Front Office', 'membership-manager'), 'file' => 'front-office-page.php'),
'back_office' => array('page_title' => 'Membership Manager ('.__('Back Office', 'membership-manager').')', 'menu_title' => __('Back Office', 'membership-manager'), 'file' => 'back-office-page.php'));

$modules['back_office'] = array(
'capabilities' => array('name' => __('Capabilities', 'membership-manager')),
'icon' => array('name' => __('Icon', 'membership-manager')),
'top' => array('name' => __('Top', 'membership-manager')),
'menu' => array('name' => __('Menu', 'membership-manager')),
'options-page' => array('name' => __('<em>Options</em> page', 'membership-manager')),
'member-area-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Member Area</em> page', 'membership-manager') : __('<em>Add Member Area</em> page', 'membership-manager'))),
'member-area-category-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Member Area Category</em> page', 'membership-manager') : __('<em>Add Member Area Category</em> page', 'membership-manager'))),
'member-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Member</em> page', 'membership-manager') : __('<em>Add Member</em> page', 'membership-manager'))),
'statistics-page' => array('name' => __('<em>Statistics</em> page', 'membership-manager')),
'front-office-page' => array('name' => __('<em>Front Office</em> page', 'membership-manager')),
'back-office-page' => array('name' => __('<em>Back Office</em> page', 'membership-manager'), 'required' => 'yes'));

$modules['front_office'] = array(
'registration-form' => array('name' => __('Registration form', 'membership-manager'), 'modules' => array(
	'registration-error-messages' => array('name' => __('Error messages', 'membership-manager')),
	'registration-login-availability-indicator' => array('name' => __('Login name\'s availability indicator', 'membership-manager')))),
'registration-compact-form' => array('name' => __('Registration compact form', 'membership-manager'), 'modules' => array(
	'registration-compact-error-messages' => array('name' => __('Error messages', 'membership-manager')),
	'registration-compact-login-availability-indicator' => array('name' => __('Login name\'s availability indicator', 'membership-manager')))),
'login-form' => array('name' => __('Login form', 'membership-manager'), 'modules' => array(
	'login-error-messages' => array('name' => __('Error messages', 'membership-manager')))),
'login-compact-form' => array('name' => __('Login compact form', 'membership-manager'), 'modules' => array(
	'login-compact-error-messages' => array('name' => __('Error messages', 'membership-manager')))),
'password-reset-form' => array('name' => __('Password reset form', 'membership-manager'), 'modules' => array(
	'password-reset-error-messages' => array('name' => __('Error messages', 'membership-manager')))),
'profile-form' => array('name' => __('Profile form', 'membership-manager'), 'modules' => array(
	'profile-error-messages' => array('name' => __('Error messages', 'membership-manager')),
	'profile-login-availability-indicator' => array('name' => __('Login name\'s availability indicator', 'membership-manager')))),
'front-office-page' => array('name' => __('<em>Front Office</em> page', 'membership-manager')));

$modules['member'] = array(
'personal-informations' => array('name' => __('Personal informations', 'membership-manager'), 'required' => 'yes'),
'registration-confirmation-email' => array('name' => __('Registration confirmation email', 'membership-manager')),
'registration-notification-email' => array('name' => __('Registration notification email', 'membership-manager')),
'autoresponders' => array('name' => __('Autoresponders', 'membership-manager')),
'registration-as-a-client' => array('name' => __('Registration as a client', 'membership-manager')),
'registration-to-affiliate-program' => array('name' => __('Registration to affiliate program', 'membership-manager')),
'wordpress' => array('name' => __('WordPress', 'membership-manager')),
'custom-instructions' => array('name' => __('Custom instructions', 'membership-manager')),
'member-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Member</em> page', 'membership-manager') : __('<em>Add Member</em> page', 'membership-manager'))));

$add_member_modules = array(
'registration-confirmation-email',
'registration-notification-email',
'autoresponders',
'registration-as-a-client',
'registration-to-affiliate-program',
'wordpress',
'custom-instructions');

$add_member_fields = array(
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
'member_subscribed_to_autoresponder',
'member_autoresponder',
'member_autoresponder_list',
'member_subscribed_as_a_client',
'member_client_category_id',
'member_client_status',
'commerce_registration_confirmation_email_sent',
'commerce_registration_notification_email_sent',
'member_subscribed_to_affiliate_program',
'member_affiliate_category_id',
'member_affiliate_status',
'affiliation_registration_confirmation_email_sent',
'affiliation_registration_notification_email_sent',
'member_subscribed_as_a_user',
'member_user_role',
'registration_custom_instructions_executed',
'registration_custom_instructions');

$modules['member_area'] = array(
'general-informations' => array('name' => __('General informations', 'membership-manager'), 'required' => 'yes'),
'registration' => array('name' => __('Registration', 'membership-manager')),
'registration-confirmation-email' => array('name' => __('Registration confirmation email', 'membership-manager')),
'registration-notification-email' => array('name' => __('Registration notification email', 'membership-manager')),
'autoresponders' => array('name' => __('Autoresponders', 'membership-manager')),
'registration-as-a-client' => array('name' => __('Registration as a client', 'membership-manager')),
'registration-to-affiliate-program' => array('name' => __('Registration to affiliate program', 'membership-manager')),
'wordpress' => array('name' => __('WordPress', 'membership-manager')),
'custom-instructions' => array('name' => __('Custom instructions', 'membership-manager')),
'activation-confirmation-email' => array('name' => __('Activation confirmation email', 'membership-manager')),
'member-area-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Member Area</em> page', 'membership-manager') : __('<em>Add Member Area</em> page', 'membership-manager'))));

$modules['member_area_category'] = $modules['member_area'];
unset($modules['member_area_category']['member-area-page']);
$modules['member_area_category'] = array_merge($modules['member_area_category'], array(
'member-area-category-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Member Area Category</em> page', 'membership-manager') : __('<em>Add Member Area Category</em> page', 'membership-manager')))));

$modules['member_category'] = array(
'general-informations' => array('name' => __('General informations', 'membership-manager'), 'required' => 'yes'),
'member-category-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Member Category</em> page', 'membership-manager') : __('<em>Add Member Category</em> page', 'membership-manager'))));

$modules['options'] = array(
'registration' => array('name' => __('Registration', 'membership-manager')),
'urls-encryption' => array('name' => __('URLs encryption', 'membership-manager')),
'registration-confirmation-email' => array('name' => __('Registration confirmation email', 'membership-manager')),
'registration-notification-email' => array('name' => __('Registration notification email', 'membership-manager')),
'autoresponders' => array('name' => __('Autoresponders', 'membership-manager')),
'autoresponders-integration' => array('name' => __('Autoresponders integration', 'membership-manager'), 'modules' => array(
	'aweber' => array('name' => 'AWeber'),
	'cybermailing' => array('name' => 'CyberMailing'),
	'getresponse' => array('name' => 'GetResponse'),
	'mailchimp' => array('name' => 'MailChimp'),
	'sg-autorepondeur' => array('name' => 'SG Autorépondeur'))),
'registration-as-a-client' => array('name' => __('Registration as a client', 'membership-manager')),
'registration-to-affiliate-program' => array('name' => __('Registration to affiliate program', 'membership-manager')),
'wordpress' => array('name' => __('WordPress', 'membership-manager')),
'custom-instructions' => array('name' => __('Custom instructions', 'membership-manager'), 'modules' => array(
	'registration-custom-instructions' => array('name' => __('Registration', 'membership-manager')),
	'login-custom-instructions' => array('name' => __('Login ', 'membership-manager')),
	'logout-custom-instructions' => array('name' => __('Logout ', 'membership-manager')),
	'password-reset-custom-instructions' => array('name' => __('Password reset', 'membership-manager')),
	'profile-edit-custom-instructions' => array('name' => __('Profile edit', 'membership-manager')),
	'activation-custom-instructions' => array('name' => __('Activation', 'membership-manager')),
	'deactivation-custom-instructions' => array('name' => __('Deactivation', 'membership-manager')),
	'removal-custom-instructions' => array('name' => __('Removal', 'membership-manager')))),
'login-notification-email' => array('name' => __('Login notification email', 'membership-manager')),
'logout-notification-email' => array('name' => __('Logout notification email', 'membership-manager')),
'password-reset-email' => array('name' => __('Password reset email', 'membership-manager')),
'password-reset-notification-email' => array('name' => __('Password reset notification email', 'membership-manager')),
'profile-edit-notification-email' => array('name' => __('Profile edit notification email', 'membership-manager')),
'activation-confirmation-email' => array('name' => __('Activation confirmation email', 'membership-manager')),
'activation-notification-email' => array('name' => __('Activation notification email', 'membership-manager')),
'deactivation-notification-email' => array('name' => __('Deactivation notification email', 'membership-manager')),
'removal-notification-email' => array('name' => __('Removal notification email', 'membership-manager')),
'forms' => array('name' => __('Forms', 'membership-manager'), 'modules' => array(
	'captcha' => array('name' => __('CAPTCHA', 'membership-manager')),
	'error-messages' => array('name' => __('Error messages', 'membership-manager')),
	'login-availability-indicator' => array('name' => __('Login name\'s availability indicator', 'membership-manager')))),
'options-page' => array('name' => __('<em>Options</em> page', 'membership-manager')));

$statistics_columns = array(
'data' => array('name' => __('Data', 'membership-manager'), 'width' => 30, 'required' => 'yes'),
'quantity' => array('name' => __('Quantity', 'membership-manager'), 'width' => 20),
'members_percentage' => array('name' => __('Percentage of members', 'membership-manager'), 'width' => 30));

$statistics_rows = array(
'members' => array('name' => __('Members', 'membership-manager')),
'active_members' => array('name' => __('Active members', 'membership-manager')),
'inactive_members' => array('name' => __('Inactive members', 'membership-manager')),
'deactivated_members' => array('name' => __('Deactivated members', 'membership-manager')),
'members_categories' => array('name' => __('Members categories', 'membership-manager')),
'members_areas' => array('name' => __('Members areas', 'membership-manager')),
'members_areas_categories' => array('name' => __('Members areas categories', 'membership-manager')));

$roles = array(
'administrator' => array('name' => __('Administrator', 'membership-manager'), 'capability' => 'manage_options'),
'editor' => array('name' => __('Editor', 'membership-manager'), 'capability' => 'moderate_comments'),
'author' => array('name' => __('Author', 'membership-manager'), 'capability' => 'publish_posts'),
'contributor' => array('name' => __('Contributor', 'membership-manager'), 'capability' => 'edit_posts'),
'subscriber' => array('name' => __('Subscriber', 'membership-manager'), 'capability' => 'read'));