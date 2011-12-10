<?php $admin_links = array(
'Documentation' => array('name' => __('Documentation', 'optin-manager')),
'Commerce Manager' => array('name' => __('Commerce', 'optin-manager')),
'Affiliation Manager' => array('name' => __('Affiliation', 'optin-manager')),
'Membership Manager' => array('name' => __('Membership', 'optin-manager')));

$admin_pages = array(
'' => array('page_title' => 'Optin Manager ('.__('Options', 'optin-manager').')', 'menu_title' => __('Options', 'optin-manager'), 'function' => 'optin_manager_options_page'),
'form' => array('page_title' => 'Optin Manager ('.__('Form', 'optin-manager').')', 'menu_title' => ((($_GET['page'] == 'optin-manager-form') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Form', 'optin-manager') : __('Edit Form', 'optin-manager')) : __('Add Form', 'optin-manager')), 'function' => 'optin_manager_form_page'),
'forms' => array('page_title' => 'Optin Manager ('.__('Forms', 'optin-manager').')', 'menu_title' => __('Forms', 'optin-manager'), 'function' => 'optin_manager_table_page'),
'form_category' => array('page_title' => 'Optin Manager ('.__('Form Category', 'optin-manager').')', 'menu_title' => ((($_GET['page'] == 'optin-manager-form-category') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Form Category', 'optin-manager') : __('Edit Form Category', 'optin-manager')) : __('Add Form Category', 'optin-manager')), 'function' => 'optin_manager_form_page'),
'forms_categories' => array('page_title' => 'Optin Manager ('.__('Forms Categories', 'optin-manager').')', 'menu_title' => __('Forms Categories', 'optin-manager'), 'function' => 'optin_manager_table_page'),
'prospect' => array('page_title' => 'Optin Manager ('.__('Prospect', 'optin-manager').')', 'menu_title' => ((($_GET['page'] == 'optin-manager-prospect') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Prospect', 'optin-manager') : __('Edit Prospect', 'optin-manager')) : __('Add Prospect', 'optin-manager')), 'function' => 'optin_manager_prospect_page'),
'prospects' => array('page_title' => 'Optin Manager ('.__('Prospects', 'optin-manager').')', 'menu_title' => __('Prospects', 'optin-manager'), 'function' => 'optin_manager_table_page'),
'statistics' => array('page_title' => 'Optin Manager ('.__('Statistics', 'optin-manager').')', 'menu_title' => __('Statistics', 'optin-manager'), 'function' => 'optin_manager_statistics_page'),
'back_office' => array('page_title' => 'Optin Manager ('.__('Back Office', 'optin-manager').')', 'menu_title' => __('Back Office', 'optin-manager'), 'function' => 'optin_manager_back_office_page'));

$modules['back_office'] = array(
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
'autoresponders' => array('name' => __('Autoresponders', 'optin-manager')),
'form' => array('name' => __('Form', 'optin-manager'), 'modules' => array(
	'error-messages' => array('name' => __('Error messages', 'optin-manager')))),
'registration' => array('name' => __('Registration', 'optin-manager')),
'registration-confirmation-email' => array('name' => __('Registration confirmation email', 'optin-manager')),
'registration-notification-email' => array('name' => __('Registration notification email', 'optin-manager')),
'membership' => array('name' => __('Membership', 'optin-manager')),
'custom-instructions' => array('name' => __('Custom instructions', 'optin-manager')),
'activation-confirmation-email' => array('name' => __('Activation confirmation email', 'optin-manager')),
'activation-notification-email' => array('name' => __('Activation notification email', 'optin-manager')),
'affiliation' => array('name' => __('Affiliation', 'optin-manager'), 'modules' => array(
	'level-1-commission' => array('name' => __('Level 1 commission', 'optin-manager')),
	'level-2-commission' => array('name' => __('Level 2 commission', 'optin-manager')))),
'form-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Form</em> page', 'optin-manager') : __('<em>Add Form</em> page', 'optin-manager'))));

$modules['form_category'] = $modules['form'];
unset($modules['form_category']['form-page']);
$modules['form_category'] = array_merge($modules['form_category'], array(
'form-category-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Form Category</em> page', 'optin-manager') : __('<em>Add Form Category</em> page', 'optin-manager')))));

$modules['options'] = array(
'autoresponders' => array('name' => __('Autoresponders', 'optin-manager')),
'autoresponders-integration' => array('name' => __('Autoresponders integration', 'optin-manager'), 'modules' => array(
	'aweber' => array('name' => 'AWeber'),
	'cybermailing' => array('name' => 'CyberMailing'),
	'getresponse' => array('name' => 'GetResponse'),
	'sg-autorepondeur' => array('name' => 'SG Autorépondeur'))),
'form' => array('name' => __('Form', 'optin-manager'), 'modules' => array(
	'error-messages' => array('name' => __('Error messages', 'optin-manager')))),
'registration' => array('name' => __('Registration', 'optin-manager')),
'urls-encryption' => array('name' => __('URLs encryption', 'optin-manager')),
'registration-confirmation-email' => array('name' => __('Registration confirmation email', 'optin-manager')),
'registration-notification-email' => array('name' => __('Registration notification email', 'optin-manager')),
'membership' => array('name' => __('Membership', 'optin-manager')),
'custom-instructions' => array('name' => __('Custom instructions', 'optin-manager')),
'activation-confirmation-email' => array('name' => __('Activation confirmation email', 'optin-manager')),
'activation-notification-email' => array('name' => __('Activation notification email', 'optin-manager')),
'affiliation' => array('name' => __('Affiliation', 'optin-manager'), 'modules' => array(
	'level-1-commission' => array('name' => __('Level 1 commission', 'optin-manager')),
	'level-2-commission' => array('name' => __('Level 2 commission', 'optin-manager')))),
'options-page' => array('name' => __('<em>Options</em> page', 'optin-manager')));

$modules['prospect'] = array(
'personal-informations' => array('name' => __('Personal informations', 'optin-manager'), 'required' => 'yes'),
'autoresponders' => array('name' => __('Autoresponders', 'optin-manager')),
'affiliation' => array('name' => __('Affiliation', 'optin-manager'), 'modules' => array(
	'level-1-commission' => array('name' => __('Level 1 commission', 'optin-manager')),
	'level-2-commission' => array('name' => __('Level 2 commission', 'optin-manager')))),
'registration-confirmation-email' => array('name' => __('Registration confirmation email', 'optin-manager')),
'registration-notification-email' => array('name' => __('Registration notification email', 'optin-manager')),
'membership' => array('name' => __('Membership', 'optin-manager')),
'custom-instructions' => array('name' => __('Custom instructions', 'optin-manager')),
'prospect-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Prospect</em> page', 'optin-manager') : __('<em>Add Prospect</em> page', 'optin-manager'))));

$statistics_columns = array(
'data' => array('name' => __('Data', 'optin-manager'), 'required' => 'yes'),
'quantity' => array('name' => __('Quantity', 'optin-manager')),
'prospects_percentage' => array('name' => __('Percentage of prospects', 'optin-manager')));

$statistics_rows = array(
'prospects' => array('name' => __('Prospects', 'optin-manager')),
'active_prospects' => array('name' => __('Active prospects', 'optin-manager')),
'inactive_prospects' => array('name' => __('Inactive prospects', 'optin-manager')),
'unsubscribed_prospects' => array('name' => __('Unsubscribed prospects', 'optin-manager')),
'forms' => array('name' => __('Forms', 'optin-manager')),
'forms_categories' => array('name' => __('Forms categories', 'optin-manager')));