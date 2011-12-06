<?php $admin_links = array(
'Documentation' => array('name' => __('Documentation', 'affiliation-manager')),
'Commerce Manager' => array('name' => __('Commerce', 'affiliation-manager')),
'Membership Manager' => array('name' => __('Membership', 'affiliation-manager')),
'Optin Manager' => array('name' => __('Optin', 'affiliation-manager')));

$admin_pages = array(
'' => array('page_title' => 'Affiliation Manager ('.__('Options', 'affiliation-manager').')', 'menu_title' => __('Options', 'affiliation-manager'), 'function' => 'affiliation_manager_options_page'),
'affiliate' => array('page_title' => 'Affiliation Manager ('.__('Affiliate', 'affiliation-manager').')', 'menu_title' => ((($_GET['page'] == 'affiliation-manager-affiliate') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Affiliate', 'affiliation-manager') : __('Edit Affiliate', 'affiliation-manager')) : __('Add Affiliate', 'affiliation-manager')), 'function' => 'affiliation_manager_affiliate_page'),
'affiliates' => array('page_title' => 'Affiliation Manager ('.__('Affiliates', 'affiliation-manager').')', 'menu_title' => __('Affiliates', 'affiliation-manager'), 'function' => 'affiliation_manager_table_page'),
'affiliate_category' => array('page_title' => 'Affiliation Manager ('.__('Affiliate Category', 'affiliation-manager').')', 'menu_title' => ((($_GET['page'] == 'affiliation-manager-affiliate-category') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Affiliate Category', 'affiliation-manager') : __('Edit Affiliate Category', 'affiliation-manager')) : __('Add Affiliate Category', 'affiliation-manager')), 'function' => 'affiliation_manager_affiliate_page'),
'affiliates_categories' => array('page_title' => 'Affiliation Manager ('.__('Affiliates Categories', 'affiliation-manager').')', 'menu_title' => __('Affiliates Categories', 'affiliation-manager'), 'function' => 'affiliation_manager_table_page'),
'clicks' => array('page_title' => 'Affiliation Manager ('.__('Clicks', 'affiliation-manager').')', 'menu_title' => __('Clicks', 'affiliation-manager'), 'function' => 'affiliation_manager_table_page'),
'commissions' => array('page_title' => 'Affiliation Manager ('.__('Commissions', 'affiliation-manager').')', 'menu_title' => __('Commissions', 'affiliation-manager'), 'function' => 'affiliation_manager_table_page'),
'recurring_commissions' => array('page_title' => 'Affiliation Manager ('.__('Recurring Commissions', 'affiliation-manager').')', 'menu_title' => __('Recurring Commissions', 'affiliation-manager'), 'function' => 'affiliation_manager_table_page'),
'prospects_commissions' => array('page_title' => 'Affiliation Manager ('.__('Prospects Commissions', 'affiliation-manager').')', 'menu_title' => __('Prospects Commissions', 'affiliation-manager'), 'function' => 'affiliation_manager_table_page'),
'payment' => array('page_title' => 'Affiliation Manager ('.__('Payment', 'affiliation-manager').')', 'menu_title' => __('Payment', 'affiliation-manager'), 'function' => 'affiliation_manager_payment_page'),
'statistics' => array('page_title' => 'Affiliation Manager ('.__('Statistics', 'affiliation-manager').')', 'menu_title' => __('Statistics', 'affiliation-manager'), 'function' => 'affiliation_manager_statistics_page'),
'back_office' => array('page_title' => 'Affiliation Manager ('.__('Back Office', 'affiliation-manager').')', 'menu_title' => __('Back Office', 'affiliation-manager'), 'function' => 'affiliation_manager_back_office_page'));

$modules['affiliate'] = array(
'personal-informations' => array('name' => __('Personal informations', 'affiliation-manager'), 'required' => 'yes'),
'bonus-offered-to-customer' => array('name' => __('Bonus offered to customer', 'affiliation-manager')),
'affiliation' => array('name' => __('Affiliation', 'affiliation-manager'), 'modules' => array(
	'level-1-commission' => array('name' => __('Level 1 commission', 'affiliation-manager')),
	'level-2-commission' => array('name' => __('Level 2 commission', 'affiliation-manager')))),
'registration-confirmation-email' => array('name' => __('Registration confirmation email', 'affiliation-manager')),
'registration-notification-email' => array('name' => __('Registration notification email', 'affiliation-manager')),
'autoresponders' => array('name' => __('Autoresponders', 'affiliation-manager')),
'membership' => array('name' => __('Membership', 'affiliation-manager')),
'custom-instructions' => array('name' => __('Custom instructions', 'affiliation-manager')),
'affiliate-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Affiliate</em> page', 'affiliation-manager') : __('<em>Add Affiliate</em> page', 'affiliation-manager'))));

$modules['affiliate_category'] = array(
'general-informations' => array('name' => __('General informations', 'affiliation-manager'), 'required' => 'yes'),
'bonus-offered-to-customer' => array('name' => __('Bonus offered to customer', 'affiliation-manager')),
'affiliation' => array('name' => __('Affiliation', 'affiliation-manager'), 'modules' => array(
	'level-1-commission' => array('name' => __('Level 1 commission', 'affiliation-manager')),
	'level-2-commission' => array('name' => __('Level 2 commission', 'affiliation-manager')))),
'affiliate-category-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Affiliate Category</em> page', 'affiliation-manager') : __('<em>Add Affiliate Category</em> page', 'affiliation-manager'))));

$modules['back_office'] = array(
'top' => array('name' => __('Top', 'affiliation-manager')),
'menu' => array('name' => __('Menu', 'affiliation-manager')),
'options-page' => array('name' => __('<em>Options</em> page', 'affiliation-manager')),
'affiliate-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Affiliate</em> page', 'affiliation-manager') : __('<em>Add Affiliate</em> page', 'affiliation-manager'))),
'affiliate-category-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Affiliate Category</em> page', 'affiliation-manager') : __('<em>Add Affiliate Category</em> page', 'affiliation-manager'))),
'statistics-page' => array('name' => __('<em>Statistics</em> page', 'affiliation-manager')),
'back-office-page' => array('name' => __('<em>Back Office</em> page', 'affiliation-manager'), 'required' => 'yes'));

$modules['options'] = array(
'general-options' => array('name' => __('General options', 'affiliation-manager'), 'required' => 'yes', 'modules' => array(
	'level-1-commission' => array('name' => __('Level 1 commission', 'affiliation-manager')),
	'level-2-commission' => array('name' => __('Level 2 commission', 'affiliation-manager')))),
'clicks' => array('name' => __('Clicks', 'affiliation-manager')),
'registration' => array('name' => __('Registration', 'affiliation-manager')),
'registration-confirmation-email' => array('name' => __('Registration confirmation email', 'affiliation-manager')),
'registration-notification-email' => array('name' => __('Registration notification email', 'affiliation-manager')),
'autoresponders' => array('name' => __('Autoresponders', 'affiliation-manager')),
'membership' => array('name' => __('Membership', 'affiliation-manager')),
'custom-instructions' => array('name' => __('Custom instructions', 'affiliation-manager')),
'password-reset-email' => array('name' => __('Password reset email', 'affiliation-manager')),
'bonus-proposal-email' => array('name' => __('Bonus proposal email', 'affiliation-manager')),
'activation-notification-email' => array('name' => __('Activation notification email', 'affiliation-manager')),
'deactivation-notification-email' => array('name' => __('Deactivation notification email', 'affiliation-manager')),
'removal-notification-email' => array('name' => __('Removal notification email', 'affiliation-manager')),
'options-page' => array('name' => __('<em>Options</em> page', 'affiliation-manager')));

$statistics_columns = array(
'data' => array('name' => __('Data', 'affiliation-manager'), 'required' => 'yes'),
'quantity' => array('name' => __('Quantity', 'affiliation-manager')),
'commissions_percentage' => array('name' => __('Percentage of commissions', 'affiliation-manager')),
'orders_percentage' => array('name' => __('Percentage of orders', 'affiliation-manager')),
'total_amount' => array('name' => __('Total amount', 'affiliation-manager')));

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
'orders' => array('name' => __('Orders', 'affiliation-manager')),
'recurring_payments' => array('name' => __('Recurring payments', 'affiliation-manager')),
'prospects' => array('name' => __('Prospects', 'affiliation-manager')),
'affiliates' => array('name' => __('Affiliates', 'affiliation-manager')),
'active_affiliates' => array('name' => __('Active affiliates', 'affiliation-manager')),
'inactive_affiliates' => array('name' => __('Inactive affiliates', 'affiliation-manager')),
'affiliates_categories' => array('name' => __('Affiliates categories', 'affiliation-manager')),
'clicks' => array('name' => __('Clicks', 'affiliation-manager')));