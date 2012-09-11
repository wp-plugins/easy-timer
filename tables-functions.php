<?php global $wpdb;

foreach (array(
'amount',
'autoresponder',
'autoresponder_list',
'category_id',
'client_id',
'commission_amount',
'commission_payment',
'commission_status',
'commission2_amount',
'commission2_status',
'first_payment_amount',
'form_id',
'ip_address',
'keywords',
'order_id',
'payment_mode',
'payments_amount',
'payments_number',
'price',
'product_id',
'quantity',
'receiver_account',
'recurring_payments_profile_status',
'referrer',
'referrer2',
'shipping_cost',
'status',
'tax',
'tax_included_in_price',
'transaction_cost') as $field) {
if (isset($_GET[$field])) {
$_GET['selection_criteria'] .= '&amp;'.$field.'='.str_replace(' ', '%20', $_GET[$field]);
$selection_criteria .= ($field == "keywords" ? " AND (".$field." LIKE '%".$_GET[$field]."%')" :
 (is_numeric($_GET[$field]) ? " AND (".$field." = ".$_GET[$field].")" : " AND (".$field." = '".$_GET[$field]."')")); } }
$selection_criteria = str_replace(" AND (status = 'unrefunded')", " AND (status != 'refunded')", $selection_criteria);


function no_items($table) {
switch ($table) {
case 'affiliates': case 'affiliates_performances': $no_items = __('No affiliates', 'affiliation-manager'); break;
case 'affiliates_categories': $no_items = __('No categories', 'affiliation-manager'); break;
case 'clicks': $no_items = __('No clicks', 'affiliation-manager'); break;
case 'commissions': case 'messages_commissions':  case 'prospects_commissions': case 'recurring_commissions': $no_items = __('No commissions', 'affiliation-manager'); }
return $no_items; }


function row_actions($table, $item) {
global $wpdb;
switch ($table) {
case 'affiliates': case 'affiliates_performances':
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_orders WHERE commission_amount > 0 AND referrer = '".$item->login."'", OBJECT);
$commissions_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE commission_amount > 0 AND referrer = '".$item->login."'", OBJECT);
$recurring_commissions_number = (int) $row->total;
$row_actions = 
'<span class="edit"><a href="admin.php?page=affiliation-manager-affiliate&amp;id='.$item->id.'">'.__('Edit').'</a></span>
 | <span class="delete"><a href="admin.php?page=affiliation-manager-affiliate&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span>
 | <span class="view"><a href="admin.php?page=affiliation-manager-statistics&amp;referrer='.$item->login.'">'.__('Statistics', 'affiliation-manager').'</a></span>'
.($commissions_number == 0 ? '' : ' | <span class="view"><a href="admin.php?page=affiliation-manager-commissions&amp;referrer='.$item->login.'">'.__('Commissions', 'affiliation-manager').'</a></span>')
.($recurring_commissions_number == 0 ? '' : ' | <span class="view"><a href="admin.php?page=affiliation-manager-recurring-commissions&amp;referrer='.$item->login.'">'.__('Recurring commissions', 'affiliation-manager').'</a></span>'); break;
case 'affiliates_categories':
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE category_id = ".$item->id, OBJECT);
$affiliates_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."affiliation_manager_affiliates_categories WHERE category_id = ".$item->id, OBJECT);
$categories_number = (int) $row->total;
$row_actions = 
'<span class="edit"><a href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$item->id.'">'.__('Edit').'</a></span>
 | <span class="delete"><a href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span>'
.($affiliates_number == 0 ? '' : ' | <span class="view"><a href="admin.php?page=affiliation-manager-affiliates&amp;category_id='.$item->id.'">'.__('Affiliates', 'affiliation-manager').'</a></span>')
.($categories_number == 0 ? '' : ' | <span class="view"><a href="admin.php?page=affiliation-manager-affiliates-categories&amp;category_id='.$item->id.'">'.__('Subcategories', 'affiliation-manager').'</a></span>'); break;
case 'clicks': $row_actions = 
'<span class="edit"><a href="admin.php?page=affiliation-manager-click&amp;id='.$item->id.'">'.__('Edit').'</a></span>
 | <span class="delete"><a href="admin.php?page=affiliation-manager-click&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span>
 | <span class="delete"><a href="admin.php?page=affiliation-manager-clicks&amp;referrer='.$item->referrer.'&amp;action=delete">'.__('Delete all the clicks of this referrer', 'affiliation-manager').'</a></span>'; break;
case 'commissions': if (function_exists('commerce_manager_admin_menu')) {
$row_actions = '<span class="edit"><a href="admin.php?page=commerce-manager-order&amp;id='.$item->id.'#affiliation">'.__('Edit').'</a></span> | '; }
$row_actions .= 
'<span class="delete"><a href="admin.php?page=affiliation-manager-commissions&amp;id='.$item->id.'&amp;action=cancel">'.__('Cancel').'</a></span>
 | <span class="delete"><a href="admin.php?page=affiliation-manager-commissions&amp;referrer='.$item->referrer.'&amp;action=cancel">'.__('Cancel all the commissions of this referrer', 'affiliation-manager').'</a></span>'; break;
case 'messages_commissions': if (function_exists('contact_manager_admin_menu')) {
$row_actions = '<span class="edit"><a href="admin.php?page=contact-manager-message&amp;id='.$item->id.'#affiliation">'.__('Edit').'</a></span> | '; }
$row_actions .= 
'<span class="delete"><a href="admin.php?page=affiliation-manager-messages-commissions&amp;id='.$item->id.'&amp;action=cancel">'.__('Cancel').'</a></span>
 | <span class="delete"><a href="admin.php?page=affiliation-manager-messages-commissions&amp;referrer='.$item->referrer.'&amp;action=cancel">'.__('Cancel all the commissions of this referrer', 'affiliation-manager').'</a></span>'; break;
case 'prospects_commissions': if (function_exists('optin_manager_admin_menu')) {
$row_actions = '<span class="edit"><a href="admin.php?page=optin-manager-prospect&amp;id='.$item->id.'#affiliation">'.__('Edit').'</a></span> | '; }
$row_actions .= 
'<span class="delete"><a href="admin.php?page=affiliation-manager-prospects-commissions&amp;id='.$item->id.'&amp;action=cancel">'.__('Cancel').'</a></span>
 | <span class="delete"><a href="admin.php?page=affiliation-manager-prospects-commissions&amp;referrer='.$item->referrer.'&amp;action=cancel">'.__('Cancel all the commissions of this referrer', 'affiliation-manager').'</a></span>'; break;
case 'recurring_commissions': if (function_exists('commerce_manager_admin_menu')) {
$row_actions = '<span class="edit"><a href="admin.php?page=commerce-manager-recurring-payment&amp;id='.$item->id.'#affiliation">'.__('Edit').'</a></span> | '; }
$row_actions .= 
'<span class="delete"><a href="admin.php?page=affiliation-manager-recurring-commissions&amp;id='.$item->id.'&amp;action=cancel">'.__('Cancel').'</a></span>
 | <span class="delete"><a href="admin.php?page=affiliation-manager-recurring-commissions&amp;referrer='.$item->referrer.'&amp;action=cancel">'.__('Cancel all the commissions of this referrer', 'affiliation-manager').'</a></span>';
if (function_exists('commerce_manager_admin_menu')) {
$row_actions .= ' | <span class="edit"><a href="admin.php?page=commerce-manager-order&amp;id='.$item->order_id.'">'.__('Edit the order', 'affiliation-manager').'</a></span>'; } break; }
return '<div class="row-actions" style="margin-top: 2em; position: absolute;">'.$row_actions.'</div>'; }


function table_criteria($table) {
switch ($table) {
case 'commissions': case 'messages_commissions': case 'prospects_commissions': case 'recurring_commissions': $table_criteria = ' AND (commission_amount > 0 OR commission2_amount > 0)'; }
return $table_criteria; }


function table_name($table) {
global $wpdb;
switch ($table) {
case 'affiliates': case 'affiliates_categories': case 'clicks': $table_name = $wpdb->prefix.'affiliation_manager_'.$table; break;
case 'affiliates_performances': $table_name = $wpdb->prefix.'affiliation_manager_affiliates'; break;
case 'commissions': $table_name = $wpdb->prefix.'commerce_manager_orders'; break;
case 'messages_commissions': $table_name = $wpdb->prefix.'contact_manager_messages'; break;
case 'prospects_commissions': $table_name = $wpdb->prefix.'optin_manager_prospects'; break;
case 'recurring_commissions': $table_name = $wpdb->prefix.'commerce_manager_recurring_payments'; }
return $table_name; }


function table_undisplayed_keys($table, $back_office_options) {
global $wpdb;
include 'tables.php';
$undisplayed_modules = array();
switch ($table) {
case 'affiliates': $undisplayed_modules = (array) $back_office_options['affiliate_page_undisplayed_modules']; break;
case 'affiliates_categories': $undisplayed_modules = (array) $back_office_options['affiliate_category_page_undisplayed_modules']; break;
case 'clicks': $undisplayed_modules = (array) $back_office_options['click_page_undisplayed_modules']; break;
case 'commissions': $options = get_option('commerce_manager_back_office'); $undisplayed_modules = (array) $options['order_page_undisplayed_modules']; break;
case 'messages_commissions': $options = get_option('contact_manager_back_office'); $undisplayed_modules = (array) $options['message_page_undisplayed_modules']; break;
case 'prospects_commissions': $options = get_option('optin_manager_back_office'); $undisplayed_modules = (array) $options['prospect_page_undisplayed_modules']; break;
case 'recurring_commissions': $options = get_option('commerce_manager_back_office'); $undisplayed_modules = (array) $options['recurring_payment_page_undisplayed_modules']; break;
case 'affiliates_performances':
if (!function_exists('commerce_manager_admin_menu')) { $undisplayed_modules = array('clients', 'orders', 'recurring-payments'); }
else {
$options = get_option('commerce_manager_back_office');
if (in_array('recurring-payments', (array) $options['product_page_undisplayed_modules'])) { $undisplayed_modules[] = 'recurring-payments'; }
foreach (array('level-1-commission', 'level-2-commission') as $module) {
if ((in_array($module, (array) $back_office_options['options_page_undisplayed_modules']))
 && (in_array($module, (array) $options['product_page_undisplayed_modules']))) { $undisplayed_modules[] = $module; } } }
if (!function_exists('contact_manager_admin_menu')) { $undisplayed_modules[] = 'messages'; }
else {
$options = get_option('contact_manager_back_office');
foreach (array('level-1-commission', 'level-2-commission') as $module) {
if ((in_array($module, (array) $options['options_page_undisplayed_modules']))
 && (in_array($module, (array) $options['form_page_undisplayed_modules']))) { $undisplayed_modules[] = 'messages-'.$module; } } }
if (!function_exists('optin_manager_admin_menu')) { $undisplayed_modules[] = 'prospects'; }
else {
$options = get_option('optin_manager_back_office');
foreach (array('level-1-commission', 'level-2-commission') as $module) {
if ((in_array($module, (array) $options['options_page_undisplayed_modules']))
 && (in_array($module, (array) $options['form_page_undisplayed_modules']))) { $undisplayed_modules[] = 'prospects-'.$module; } } } break; }
$undisplayed_keys = array();
switch ($table) {
case 'affiliates': case 'affiliates_categories':
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."affiliation_manager_affiliates_categories", OBJECT);
$n = (int) $row->total; if ($n == 0) { $undisplayed_keys[] = 'category_id'; } break;
case 'commissions': case 'recurring_commissions':
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_forms", OBJECT);
$n = (int) $row->total; if ($n == 0) { $undisplayed_keys[] = 'form_id'; }
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_clients", OBJECT);
$n = (int) $row->total; if ($n == 0) { $undisplayed_keys[] = 'client_id'; } break; }
foreach ($tables[$table] as $key => $value) {
foreach ((array) $value['modules'] as $module) {
if (in_array($module, $undisplayed_modules)) { $undisplayed_keys[] = $key; } } }
return $undisplayed_keys; }


function table_data($table, $column, $item) {
switch ($table) {
case 'affiliates': case 'affiliates_performances': $_GET['affiliate_data'] = $item; $data = affiliate_data($column); break;
case 'affiliates_categories': $_GET['affiliate_category_id'] = $item->id; $_GET['affiliate_category_data'] = $item; $data = affiliate_category_data($column); break;
case 'clicks': $_GET['click_id'] = $item->id; $_GET['click_data'] = $item; $data = click_data($column); break;
case 'commissions': $_GET['commission_id'] = $item->id; $_GET['commission_data'] = $item; $data = commission_data($column); break;
case 'messages_commissions': $_GET['message_commission_id'] = $item->id; $_GET['message_commission_data'] = $item; $data = message_commission_data($column); break;
case 'prospects_commissions': $_GET['prospect_commission_id'] = $item->id; $_GET['prospect_commission_data'] = $item; $data = prospect_commission_data($column); break;
case 'recurring_commissions': $_GET['recurring_commission_id'] = $item->id; $_GET['recurring_commission_data'] = $item; $data = recurring_commission_data($column); break;
default: $data = affiliation_format_data($column, $item->$column); }
return $data; }


function table_td($table, $column, $item) {
$data = htmlspecialchars(table_data($table, $column, $item));
switch ($column) {
case 'amount': case 'autoresponder': case 'autoresponder_list': case 'category_id': case 'client_id': case 'first_payment_amount': case 'form_id':
case 'ip_address': case 'order_id': case 'payment_mode': case 'payments_amount': case 'product_id': case 'price': case 'quantity':
case 'receiver_account': case 'referrer': case 'referrer2': case 'shipping_cost': case 'tax': case 'transaction_cost':
$table_td = ($data == '' ? '' : '<a href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'='.str_replace(' ', '%20', $data).'">'.$data.'</a>'); break;
case 'commission_amount': case 'commission2_amount':
if (($table == 'affiliates') || ($table == 'affiliates_categories')) { $table_td = $data; }
else { $table_td = ($data == '' ? '' : '<a href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'='.str_replace(' ', '%20', $data).'">'.$data.'</a>'); } break;
case 'bonus_download_url': case 'referring_url': case 'url': case 'website_url': $table_td = ($data == '' ? '' : '<a href="'.$data.'">'.($data == 'http://'.$_SERVER['SERVER_NAME'] ? '/' : str_replace('http://'.$_SERVER['SERVER_NAME'], '', $data)).'</a>'); break;
case 'bonus_instructions': case 'content': case 'description': case 'instructions': case 'shipping_address': if (strlen($data) <= 80) { $table_td = $data; }
else { $table_td = substr($data, 0, 80); if (stristr($table_td, ' ')) { while (substr($table_td, -1) != ' ') { $table_td = substr($table_td, 0, -1); } } $table_td .= '[...]'; } break;
case 'affiliate_notification_email_sent': case 'client_notification_email_sent': case 'commission2_enabled': case 'message_notification_email_sent':
case 'order_notification_email_sent': case 'prospect_notification_email_sent': case 'recurring_payment_notification_email_sent':
if ($data == 'yes') { $table_td = '<span style="color: #008000;">'.__('Yes', 'affiliation-manager').'</span>'; }
elseif ($data == 'no')  { $table_td = '<span style="color: #c00000;">'.__('No', 'affiliation-manager').'</span>'; }
elseif ($data == 'if commission')  { $table_td = '<span style="color: #e08000;">'.__('If the amount of commission is not 0', 'affiliation-manager').'</span>'; }
else { $table_td = $data; } break;
case 'commission_payment':
if ($data == 'deferred') { $table_td = '<a href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=deferred">'.__('Deferred', 'affiliation-manager').'</a>'; }
elseif ($data == 'instant') { $table_td = '<a href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=instant">'.__('Instant', 'affiliation-manager').'</a>'; }
else { $table_td = $data; } break;
case 'commission_status': case 'commission2_status': if ($data == 'paid') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=paid">'.__('Paid', 'affiliation-manager').'</a>'; }
elseif ($data == 'unpaid') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=unpaid">'.__('Unpaid', 'affiliation-manager').'</a>'; }
else { $table_td = $data; } break;
case 'commission_type': case 'commission2_type': if ($data == 'constant') { $table_td = __('Constant', 'affiliation-manager'); }
elseif ($data == 'proportional') { $table_td = __('Proportional', 'affiliation-manager'); }
else { $table_td = $data; } break;
case 'email_address': case 'paypal_email_address': $table_td = '<a href="mailto:'.$data.'">'.$data.'</a>'; break;
case 'first_payment_period_time_unit': case 'payments_period_time_unit': $table_td = __(ucfirst($data), 'affiliation-manager'); break;
case 'first_sale_winner': if ($data == 'affiliate') { $table_td = __('Affiliate', 'affiliation-manager'); }
elseif ($data == 'affiliator') { $table_td = __('Affiliator', 'affiliation-manager'); }
else { $table_td = $data; } break;
case 'keywords':
$keywords = explode(',', $data);
foreach ($keywords as $keyword) {
$keyword = strtolower(trim($keyword));
$keyword = '<a href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;keywords='.$keyword.'">'.$keyword.'</a>';
$keywords_list .= $keyword.', '; }
$table_td = substr($keywords_list, 0, -2); break;
case 'login': $table_td = '<a href="admin.php?page=affiliation-manager-affiliate&amp;id='.$item->id.'">'.$data.'</a>'; break;
case 'payments_number': if ($data == 'unlimited') { $table_td = '<a href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=unlimited">'.__('Unlimited', 'affiliation-manager').'</a>'; } else { $table_td = ($data == '' ? '' : '<a href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'='.$data.'">'.$data.'</a>'); } break;
case 'received_payments_number': $table_td = ((($data == 0) || (!function_exists('commerce_manager_admin_menu'))) ? $data : '<a href="admin.php?page=commerce-manager-recurring-payments&amp;order_id='.$item->id.'">'.$data.'</a>'); break;
case 'recurring_payments_profile_status':
if ($data == 'active') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=active">'.__('Active', 'affiliation-manager').'</a>'; }
elseif ($data == 'inactive') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=inactive">'.__('Inactive', 'affiliation-manager').'</a>'; }
elseif ($data == 'deactivated') { $table_td = '<a style="color: #c00000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=deactivated">'.__('Deactivated', 'affiliation-manager').'</a>'; }
else { $table_td = $data; } break;
case 'status':
if ($table == 'affiliates') {
if ($data == 'active') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=active">'.__('Active', 'affiliation-manager').'</a>'; }
elseif ($data == 'inactive') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=inactive">'.__('Inactive', 'affiliation-manager').'</a>'; }
elseif ($data == 'deactivated') { $table_td = '<a style="color: #c00000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=deactivated">'.__('Deactivated', 'affiliation-manager').'</a>'; }
else { $table_td = $data; } }
else {
if ($data == 'processed') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=processed">'.__('Processed', 'affiliation-manager').'</a>'; }
elseif ($data == 'received') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=received">'.__('Received', 'affiliation-manager').'</a>'; }
elseif ($data == 'unprocessed') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=unprocessed">'.__('Unprocessed', 'affiliation-manager').'</a>'; }
elseif ($data == 'refunded') { $table_td = '<a style="color: #c00000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=refunded">'.__('Refunded', 'affiliation-manager').'</a>'; }
else { $table_td = $data; } } break;
case 'tax_included_in_price':
if (($table == 'commissions') || ($table == 'recurring_commissions')) {
if ($data == 'yes') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=yes">'.__('Yes', 'affiliation-manager').'</a>'; }
elseif ($data == 'no') { $table_td = '<a style="color: #c00000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=no">'.__('No', 'affiliation-manager').'</a>'; }
else { $table_td = $data; } }
else {
if ($data == 'yes') { $table_td = '<span style="color: #008000;">'.__('Yes', 'affiliation-manager').'</span>'; }
elseif ($data == 'no')  { $table_td = '<span style="color: #c00000;">'.__('No', 'affiliation-manager').'</span>'; }
else { $table_td = $data; } } break;
case 'website_name': $website_url = htmlspecialchars(affiliation_format_data($column, $item->website_url)); $table_td = ($website_url == '' ? $data : '<a href="'.$website_url.'">'.($data == '' ? str_replace('http://'.$_SERVER['SERVER_NAME'], '', $website_url) : $data).'</a>'); break;
default: $table_td = $data; }
return $table_td; }


function table_th($table, $column) {
include 'tables.php';
if (strstr($_GET['page'], 'statistics')) { $table_th = '<th scope="col" class="manage-column" style="width: '.$tables[$table][$column]['width'].'%;">'.$tables[$table][$column]['name'].'</th>'; }
else {
$reverse_order = ($_GET['order'] == 'asc' ? 'desc' : 'asc');
$table_th = '<th scope="col" class="manage-column '.($_GET['orderby'] == $column ? 'sorted '.$_GET['order'] : 'sortable '.$reverse_order).'" style="width: '.$tables[$table][$column]['width'].'%;">
<a href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;orderby='.$column.'&amp;order='.($_GET['orderby'] == $column ? $reverse_order : $_GET['order']).'">
<span>'.$tables[$table][$column]['name'].'</span><span class="sorting-indicator"></span></a></th>'; }
return $table_th; }


function tablenav_pages($table, $n, $max_paged, $location) {
switch ($table) {
case 'affiliates': case 'affiliates_performances': $singular = __('affiliate', 'affiliation-manager'); $plural = __('affiliates', 'affiliation-manager'); break;
case 'affiliates_categories': $singular = __('category', 'affiliation-manager'); $plural = __('categories', 'affiliation-manager'); break;
case 'clicks': $singular = __('click', 'affiliation-manager'); $plural = __('clicks', 'affiliation-manager'); break;
case 'commissions': case 'messages_commissions': case 'prospects_commissions': case 'recurring_commissions': $singular = __('commission', 'affiliation-manager'); $plural = __('commissions', 'affiliation-manager'); break; 
default: $singular = __('item', 'affiliation-manager'); $plural = __('items', 'affiliation-manager'); }
if ($_GET['paged'] == 1) { $prev_paged = 1; } else { $prev_paged = $_GET['paged'] - 1; }
if ($_GET['paged'] == $max_paged) { $next_paged = $max_paged; } else { $next_paged = $_GET['paged'] + 1; }
$url = 'admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;orderby='.$_GET['orderby'].'&amp;order='.$_GET['order'];
echo '<div class="tablenav-pages" style="float: right;"><span class="displaying-num">'.$n.' '.($n <= 1 ? $singular : $plural).'</span>
<a class="first-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the first page').'" href="'.$url.'&amp;paged=1">&laquo;</a>
<a class="prev-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the previous page').'" href="'.$url.'&amp;paged='.$prev_paged.'">&lsaquo;</a>
<span class="paging-input">'.($location == 'top' ? '<input class="current-page" title="'.__('Current page').'" type="text" name="paged" id="paged" value="'.$_GET['paged'].'" size="2" />' : $_GET['paged']).' '.__('of').' <span class="total-pages">'.$max_paged.'</span></span>
<a class="next-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the next page').'" href="'.$url.'&amp;paged='.$next_paged.'">&rsaquo;</a>
<a class="last-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the last page').'" href="'.$url.'&amp;paged='.$max_paged.'">&raquo;</a></div>'; }


function affiliate_performance($column, $start_date, $end_date, $filterby, $item) {
global $wpdb;
$_GET['affiliate_data'] = $item;

if (($column == $_GET['orderby']) && (isset($_GET['datas'][$item->id]))) { $data = $_GET['datas'][$item->id]; }
elseif (($column == 'id') || ($column == 'login')) { $data = affiliate_data($column); }
else {
switch ($column) {
case 'affiliates': $table = $wpdb->prefix.'affiliation_manager_affiliates'; $field = ''; break;
case 'amount': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments'); $field = 'amount'; break;
case 'clicks': $table = $wpdb->prefix.'affiliation_manager_clicks'; $field = ''; break;
case 'clients': $table = $wpdb->prefix.'commerce_manager_clients'; $field = ''; break;
case 'commission_amount': case 'commission_paid_amount': case 'commission_unpaid_amount': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments', $wpdb->prefix.'optin_manager_prospects', $wpdb->prefix.'contact_manager_messages'); $field = 'commission_amount'; break;
case 'commission2_amount': case 'commission2_paid_amount': case 'commission2_unpaid_amount': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments', $wpdb->prefix.'optin_manager_prospects', $wpdb->prefix.'contact_manager_messages'); $field = 'commission2_amount'; break;
case 'messages': $table = $wpdb->prefix.'contact_manager_messages'; $field = ''; break;
case 'messages_commission_amount': case 'messages_commission_paid_amount': case 'messages_commission_unpaid_amount': $table = $wpdb->prefix.'contact_manager_messages'; $field = 'commission_amount'; break;
case 'messages_commission2_amount': case 'messages_commission2_paid_amount': case 'messages_commission2_unpaid_amount': $table = $wpdb->prefix.'contact_manager_messages'; $field = 'commission2_amount'; break;
case 'orders': $table = $wpdb->prefix.'commerce_manager_orders'; $field = ''; break;
case 'orders_amount': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'amount'; break;
case 'orders_commission_amount': case 'orders_commission_paid_amount': case 'orders_commission_unpaid_amount': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'commission_amount'; break;
case 'orders_commission2_amount': case 'orders_commission2_paid_amount': case 'orders_commission2_unpaid_amount': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'commission2_amount'; break;
case 'prospects': $table = $wpdb->prefix.'optin_manager_prospects'; $field = ''; break;
case 'prospects_commission_amount': case 'prospects_commission_paid_amount': case 'prospects_commission_unpaid_amount': $table = $wpdb->prefix.'optin_manager_prospects'; $field = 'commission_amount'; break;
case 'prospects_commission2_amount': case 'prospects_commission2_paid_amount': case 'prospects_commission2_unpaid_amount': $table = $wpdb->prefix.'optin_manager_prospects'; $field = 'commission2_amount'; break;
case 'recurring_payments': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = ''; break;
case 'recurring_payments_amount': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'amount'; break;
case 'recurring_payments_commission_amount': case 'recurring_payments_commission_paid_amount': case 'recurring_payments_commission_unpaid_amount': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'commission_amount'; break;
case 'recurring_payments_commission2_amount': case 'recurring_payments_commission2_paid_amount': case 'recurring_payments_commission2_unpaid_amount': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'commission2_amount'; break; }
if (strstr($column, 'commission2')) { $referrer_field = 'referrer2'; } else { $referrer_field = 'referrer'; }

$date_criteria = "AND (date BETWEEN '".$start_date."' AND '".$end_date."')";
if ($_GET['s'] != '') { $filter_criteria = (is_numeric($_GET['s']) ? "AND (".$filterby." = ".$_GET['s'].")" : "AND (".$filterby." = '".$_GET['s']."')"); }
if (strstr($column, 'commission2')) { $status_field = 'commission2_status'; }
elseif (strstr($column, 'commission')) { $status_field = 'commission_status'; }
else { $status_field = 'status'; }
if (strstr($column, 'unpaid')) { $status_criteria = "AND $status_field = 'unpaid'"; }
elseif (strstr($column, 'paid')) { $status_criteria = "AND $status_field = 'paid'"; }

if (is_string($table)) {
if ($field == '') {
$row = $wpdb->get_row("SELECT count(*) as total FROM $table WHERE $referrer_field = '".$item->login."' $date_criteria $filter_criteria $status_criteria", OBJECT);
$data = (int) $row->total; }
else {
$row = $wpdb->get_row("SELECT SUM($field) AS total FROM $table WHERE $referrer_field = '".$item->login."' $date_criteria $filter_criteria $status_criteria", OBJECT);
$data = round(100*$row->total)/100; } }
else {
$data = 0; foreach ($table as $table_name) {
$row = $wpdb->get_row("SELECT SUM($field) AS total FROM $table_name WHERE $referrer_field = '".$item->login."' $date_criteria $filter_criteria $status_criteria", OBJECT);
$data = $data + round(100*$row->total)/100; } } }

return $data; }


function affiliate_performance_td($column, $start_date, $end_date, $filterby, $item, $currency_code) {
if (($column == 'id') || ($column == 'login')) { $performance_td = table_td('affiliates', $column, $item); }
else {
$criteria = str_replace('&amp;s=', '&amp;'.$filterby.'=', $_GET['criteria']);
$performance_td = affiliate_performance($column, $start_date, $end_date, $filterby, $item);
if (strstr($column, 'commission2')) { $referrer_field = 'referrer2'; } else { $referrer_field = 'referrer'; }
if (strstr($column, 'amount')) { $performance_td .= ' '.$currency_code; }
if (strstr($column, 'affiliates')) { $performance_td = '<a href="admin.php?page=affiliation-manager-affiliates'.$criteria.'&amp;'.$referrer_field.'='.$item->login.'">'.$performance_td.'</a>'; }
elseif (strstr($column, 'clicks')) { $performance_td = '<a href="admin.php?page=affiliation-manager-clicks'.$criteria.'&amp;'.$referrer_field.'='.$item->login.'">'.$performance_td.'</a>'; }
elseif ((function_exists('commerce_manager_admin_menu')) && (strstr($column, 'clients'))) { $performance_td = '<a href="admin.php?page=commerce-manager-clients'.$criteria.'&amp;'.$referrer_field.'='.$item->login.'">'.$performance_td.'</a>'; }
elseif (strstr($column, 'orders_commission')) { $performance_td = '<a href="admin.php?page=affiliation-manager-commissions'.$criteria.'&amp;'.$referrer_field.'='.$item->login.'">'.$performance_td.'</a>'; }
elseif ((function_exists('commerce_manager_admin_menu')) && (strstr($column, 'orders'))) { $performance_td = '<a href="admin.php?page=commerce-manager-orders'.$criteria.'&amp;'.$referrer_field.'='.$item->login.'">'.$performance_td.'</a>'; }
elseif (strstr($column, 'recurring_payments_commission')) { $performance_td = '<a href="admin.php?page=affiliation-manager-recurring-commissions'.$criteria.'&amp;'.$referrer_field.'='.$item->login.'">'.$performance_td.'</a>'; }
elseif ((function_exists('commerce_manager_admin_menu')) && (strstr($column, 'recurring_payments'))) { $performance_td = '<a href="admin.php?page=commerce-manager-recurring-payments'.$criteria.'&amp;'.$referrer_field.'='.$item->login.'">'.$performance_td.'</a>'; }
elseif (strstr($column, 'prospects_commission')) { $performance_td = '<a href="admin.php?page=affiliation-manager-prospects-commissions'.$criteria.'&amp;'.$referrer_field.'='.$item->login.'">'.$performance_td.'</a>'; }
elseif ((function_exists('optin_manager_admin_menu')) && (strstr($column, 'prospects'))) { $performance_td = '<a href="admin.php?page=optin-manager-prospects'.$criteria.'&amp;'.$referrer_field.'='.$item->login.'">'.$performance_td.'</a>'; }
elseif (strstr($column, 'messages_commission')) { $performance_td = '<a href="admin.php?page=affiliation-manager-messages-commissions'.$criteria.'&amp;'.$referrer_field.'='.$item->login.'">'.$performance_td.'</a>'; }
elseif ((function_exists('contact_manager_admin_menu')) && (strstr($column, 'messages'))) { $performance_td = '<a href="admin.php?page=contact-manager-messages'.$criteria.'&amp;'.$referrer_field.'='.$item->login.'">'.$performance_td.'</a>'; }

if (strstr($column, 'commission2')) { $status_field = 'commission2_status'; }
elseif (strstr($column, 'commission')) { $status_field = 'commission_status'; }
else { $status_field = 'status'; }
if (strstr($column, 'unpaid')) { $performance_td = str_replace(array('<a', '">'), array('<a style="color: #e08000;"', '&amp;'.$status_field.'=unpaid">'), $performance_td); }
elseif (strstr($column, 'paid')) { $performance_td = str_replace(array('<a', '">'), array('<a style="color: #008000;"', '&amp;'.$status_field.'=paid">'), $performance_td); }
if (($column == 'commission_paid_amount') || ($column == 'commission2_paid_amount')) { $performance_td = '<span style="color: #008000;">'.$performance_td.'</span>'; }
elseif (($column == 'commission_unpaid_amount') || ($column == 'commission2_unpaid_amount')) { $performance_td = '<span style="color: #e08000;">'.$performance_td.'</span>'; } }

return $performance_td; }


foreach (array(
'affiliate',
'affiliation-user',
'commission',
'message-commission',
'prospect-commission',
'recurring-commission',
'referrer',
'referrer-affiliate') as $tag) { remove_shortcode($tag); }