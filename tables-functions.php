<?php global $wpdb;

foreach (array(
'amount',
'available_quantity',
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
'transaction_cost',
'weight') as $field) {
if (isset($_GET[$field])) {
$_GET['selection_criteria'] .= '&amp;'.$field.'='.str_replace(' ', '%20', $_GET[$field]);
$selection_criteria .= ($field == "keywords" ? " AND (".$field." LIKE '%".$_GET[$field]."%')" :
 (is_numeric($_GET[$field]) ? " AND (".$field." = ".$_GET[$field].")" : " AND (".$field." = '".$_GET[$field]."')")); } }
$selection_criteria = str_replace(" AND (status = 'unrefunded')", " AND (status != 'refunded')", $selection_criteria);


function no_items($table) {
switch ($table) {
case 'clients': case 'clients_performances': $no_items = __('No clients', 'commerce-manager'); break;
case 'clients_categories': case 'forms_categories': case 'products_categories': $no_items = __('No categories', 'commerce-manager'); break;
case 'forms': case 'forms_performances': $no_items = __('No forms', 'commerce-manager'); break;
case 'orders': $no_items = __('No orders', 'commerce-manager'); break;
case 'products': case 'products_performances': $no_items = __('No products', 'commerce-manager'); break;
case 'recurring_payments': $no_items = __('No payments', 'commerce-manager'); }
return $no_items; }


function row_actions($table, $item) {
global $wpdb;
switch ($table) {
case 'clients': case 'clients_performances':
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_orders WHERE client_id = ".$item->id, OBJECT);
$orders_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE client_id = ".$item->id, OBJECT);
$recurring_payments_number = (int) $row->total;
$row_actions = 
'<span class="edit"><a href="admin.php?page=commerce-manager-client&amp;id='.$item->id.'">'.__('Edit').'</a></span>
 | <span class="delete"><a href="admin.php?page=commerce-manager-client&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span>
 | <span class="view"><a href="admin.php?page=commerce-manager-statistics&amp;client_id='.$item->id.'">'.__('Statistics', 'commerce-manager').'</a></span>'
.($orders_number == 0 ? '' : ' | <span class="view"><a href="admin.php?page=commerce-manager-orders&amp;client_id='.$item->id.'">'.__('Orders', 'commerce-manager').'</a></span>')
.($recurring_payments_number == 0 ? '' : ' | <span class="view"><a href="admin.php?page=commerce-manager-recurring-payments&amp;client_id='.$item->id.'">'.__('Recurring payments', 'commerce-manager').'</a></span>'); break;
case 'clients_categories':
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_clients WHERE category_id = ".$item->id, OBJECT);
$clients_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_clients_categories WHERE category_id = ".$item->id, OBJECT);
$categories_number = (int) $row->total;
$row_actions = 
'<span class="edit"><a href="admin.php?page=commerce-manager-client-category&amp;id='.$item->id.'">'.__('Edit').'</a></span>
 | <span class="delete"><a href="admin.php?page=commerce-manager-client-category&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span>'
.($clients_number == 0 ? '' : ' | <span class="view"><a href="admin.php?page=commerce-manager-clients&amp;category_id='.$item->id.'">'.__('Clients', 'commerce-manager').'</a></span>')
.($categories_number == 0 ? '' : ' | <span class="view"><a href="admin.php?page=commerce-manager-clients-categories&amp;category_id='.$item->id.'">'.__('Subcategories', 'commerce-manager').'</a></span>'); break;
case 'forms': case 'forms_performances':
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_orders WHERE form_id = ".$item->id, OBJECT);
$orders_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE form_id = ".$item->id, OBJECT);
$recurring_payments_number = (int) $row->total;
$row_actions = 
'<span class="edit"><a href="admin.php?page=commerce-manager-form&amp;id='.$item->id.'">'.__('Edit').'</a></span>
 | <span class="delete"><a href="admin.php?page=commerce-manager-form&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span>
 | <span class="view"><a href="admin.php?page=commerce-manager-statistics&amp;form_id='.$item->id.'">'.__('Statistics', 'commerce-manager').'</a></span>'
.($orders_number == 0 ? '' : ' | <span class="view"><a href="admin.php?page=commerce-manager-orders&amp;form_id='.$item->id.'">'.__('Orders', 'commerce-manager').'</a></span>')
.($recurring_payments_number == 0 ? '' : ' | <span class="view"><a href="admin.php?page=commerce-manager-recurring-payments&amp;form_id='.$item->id.'">'.__('Recurring payments', 'commerce-manager').'</a></span>'); break;
case 'forms_categories':
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_forms WHERE category_id = ".$item->id, OBJECT);
$forms_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_forms_categories WHERE category_id = ".$item->id, OBJECT);
$categories_number = (int) $row->total;
$row_actions = 
'<span class="edit"><a href="admin.php?page=commerce-manager-form-category&amp;id='.$item->id.'">'.__('Edit').'</a></span>
 | <span class="delete"><a href="admin.php?page=commerce-manager-form-category&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span>'
.($forms_number == 0 ? '' : ' | <span class="view"><a href="admin.php?page=commerce-manager-forms&amp;category_id='.$item->id.'">'.__('Forms', 'commerce-manager').'</a></span>')
.($categories_number == 0 ? '' : ' | <span class="view"><a href="admin.php?page=commerce-manager-forms-categories&amp;category_id='.$item->id.'">'.__('Subcategories', 'commerce-manager').'</a></span>'); break;
case 'orders': $row_actions = 
'<span class="edit"><a href="admin.php?page=commerce-manager-order&amp;id='.$item->id.'">'.__('Edit').'</a></span>
 | <span class="delete"><a href="admin.php?page=commerce-manager-order&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span>'
.($item->received_payments_number == 0 ? '' : ' | <span class="view"><a href="admin.php?page=commerce-manager-recurring-payments&amp;order_id='.$item->id.'">'.__('Recurring payments', 'commerce-manager').'</a></span>'); break;
case 'products': case 'products_performances':
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_orders WHERE product_id = ".$item->id, OBJECT);
$orders_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE product_id = ".$item->id, OBJECT);
$recurring_payments_number = (int) $row->total;
$row_actions =
'<span class="edit"><a href="admin.php?page=commerce-manager-product&amp;id='.$item->id.'">'.__('Edit').'</a></span>
 | <span class="delete"><a href="admin.php?page=commerce-manager-product&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span>
 | <span class="view"><a href="admin.php?page=commerce-manager-statistics&amp;product_id='.$item->id.'">'.__('Statistics', 'commerce-manager').'</a></span>' 
.($orders_number == 0 ? '' : ' | <span class="view"><a href="admin.php?page=commerce-manager-orders&amp;product_id='.$item->id.'">'.__('Orders', 'commerce-manager').'</a></span>')
.($recurring_payments_number == 0 ? '' : ' | <span class="view"><a href="admin.php?page=commerce-manager-recurring-payments&amp;product_id='.$item->id.'">'.__('Recurring payments', 'commerce-manager').'</a></span>'); break;
case 'products_categories':
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_products WHERE category_id = ".$item->id, OBJECT);
$products_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_products_categories WHERE category_id = ".$item->id, OBJECT);
$categories_number = (int) $row->total;
$row_actions = 
'<span class="edit"><a href="admin.php?page=commerce-manager-product-category&amp;id='.$item->id.'">'.__('Edit').'</a></span>
 | <span class="delete"><a href="admin.php?page=commerce-manager-product-category&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span>'
.($products_number == 0 ? '' : ' | <span class="view"><a href="admin.php?page=commerce-manager-products&amp;category_id='.$item->id.'">'.__('Products', 'commerce-manager').'</a></span>')
.($categories_number == 0 ? '' : ' | <span class="view"><a href="admin.php?page=commerce-manager-products-categories&amp;category_id='.$item->id.'">'.__('Subcategories', 'commerce-manager').'</a></span>'); break;
case 'recurring_payments': $row_actions = 
'<span class="edit"><a href="admin.php?page=commerce-manager-recurring-payment&amp;id='.$item->id.'">'.__('Edit').'</a></span>
 | <span class="delete"><a href="admin.php?page=commerce-manager-recurring-payment&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span>
 | <span class="delete"><a href="admin.php?page=commerce-manager-recurring-payments&amp;order_id='.$item->order_id.'&amp;action=delete">'.__('Delete all the payments of this order', 'commerce-manager').'</a></span>
 | <span class="edit"><a href="admin.php?page=commerce-manager-order&amp;id='.$item->order_id.'">'.__('Edit the order', 'commerce-manager').'</a></span>'; }
return '<div class="row-actions" style="margin-top: 2em; position: absolute;">'.$row_actions.'</div>'; }


function table_name($table) {
global $wpdb;
switch ($table) {
case 'clients_performances': $table_name = $wpdb->prefix.'commerce_manager_clients'; break;
case 'forms_performances': $table_name = $wpdb->prefix.'commerce_manager_forms'; break;
case 'products_performances': $table_name = $wpdb->prefix.'commerce_manager_products'; break;
default: $table_name = $wpdb->prefix.'commerce_manager_'.$table; }
return $table_name; }


function table_undisplayed_keys($table, $back_office_options) {
global $wpdb;
include 'tables.php';
$undisplayed_modules = array();
switch ($table) {
case 'clients': $undisplayed_modules = (array) $back_office_options['client_page_undisplayed_modules']; break;
case 'clients_categories': $undisplayed_modules = (array) $back_office_options['client_category_page_undisplayed_modules']; break;
case 'forms': $undisplayed_modules = (array) $back_office_options['form_page_undisplayed_modules']; break;
case 'forms_categories': $undisplayed_modules = (array) $back_office_options['form_category_page_undisplayed_modules']; break;
case 'orders': $undisplayed_modules = (array) $back_office_options['order_page_undisplayed_modules']; break;
case 'products': $undisplayed_modules = (array) $back_office_options['product_page_undisplayed_modules']; break;
case 'products_categories': $undisplayed_modules = (array) $back_office_options['product_category_page_undisplayed_modules']; break;
case 'recurring_payments': $undisplayed_modules = (array) $back_office_options['recurring_payment_page_undisplayed_modules']; break;
case 'products_performances': case 'forms_performances': case 'clients_performances':
$undisplayed_modules = (array) $back_office_options['product_page_undisplayed_modules'];
if (!function_exists('affiliation_manager_admin_menu')) { $undisplayed_modules[] = 'affiliation'; }
else {
foreach ($undisplayed_modules as $key => $value) { if ($value == 'affiliation') { unset($undisplayed_modules[$key]); } }
$options = get_option('affiliation_manager_back_office');
foreach (array('level-1-commission', 'level-2-commission') as $module) {
if ((in_array($module, (array) $back_office_options['product_page_undisplayed_modules']))
 && (in_array($module, (array) $options['options_page_undisplayed_modules']))) { $undisplayed_modules[] = $module; } } } break; }
$undisplayed_keys = array();
switch ($table) {
case 'clients': case 'clients_categories':
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_clients_categories", OBJECT);
$n = (int) $row->total; if ($n == 0) { $undisplayed_keys[] = 'category_id'; } break;
case 'forms': case 'forms_categories':
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_forms_categories", OBJECT);
$n = (int) $row->total; if ($n == 0) { $undisplayed_keys[] = 'category_id'; } break;
case 'products': case 'products_categories':
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_products_categories", OBJECT);
$n = (int) $row->total; if ($n == 0) { $undisplayed_keys[] = 'category_id'; } break;
case 'orders': case 'recurring_payments':
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
case 'clients': case 'clients_performances': $_GET['client_id'] = $item->id; $_GET['client_data'] = $item; $data = client_data($column); break;
case 'clients_categories': $_GET['client_category_id'] = $item->id; $_GET['client_category_data'] = $item; $data = client_category_data($column); break;
case 'forms': case 'forms_performances': $_GET['commerce_form_id'] = $item->id; $_GET['commerce_form_data'] = $item; $data = commerce_form_data($column); break;
case 'forms_categories': $_GET['commerce_form_category_id'] = $item->id; $_GET['commerce_form_category_data'] = $item; $data = commerce_form_category_data($column); break;
case 'orders': $_GET['order_id'] = $item->id; $_GET['order_data'] = $item; $data = order_data($column); break;
case 'products': case 'products_performances': $_GET['product_id'] = $item->id; $_GET['product_data'] = $item; $data = product_data($column); break;
case 'products_categories': $_GET['product_category_id'] = $item->id; $_GET['product_category_data'] = $item; $data = product_category_data($column); break;
case 'recurring_payments': $_GET['recurring_payment_id'] = $item->id; $_GET['recurring_payment_data'] = $item; $data = recurring_payment_data($column); break;
default: $data = commerce_format_data($column, $item->$column); }
return $data; }


function table_td($table, $column, $item) {
$data = htmlspecialchars(table_data($table, $column, $item));
switch ($column) {
case 'affiliation_enabled': case 'affiliation_registration_confirmation_email_sent': case 'affiliation_registration_notification_email_sent': case 'commission2_enabled':
case 'commerce_registration_confirmation_email_sent': case 'commerce_registration_notification_email_sent': case 'customer_subscribed_as_a_client':
case 'customer_subscribed_as_a_user': case 'customer_subscribed_to_affiliate_program': case 'customer_subscribed_to_autoresponder': case 'customer_subscribed_to_members_areas':
case 'customer_unsubscribed_from_members_areas': case 'default_shipping_cost_applied': case 'default_tax_applied': case 'downloadable':
case 'first_payment_amount_used1': case 'first_payment_amount_used2': case 'first_payment_amount_used3':
case 'first_payment_period_used1': case 'first_payment_period_used2': case 'first_payment_period_used3':
case 'membership_registration_confirmation_email_sent': case 'membership_registration_notification_email_sent':
case 'order_confirmation_email_sent': case 'order_custom_instructions_executed': case 'order_notification_email_sent':
case 'order_processing_custom_instructions_executed': case 'order_refund_custom_instructions_executed': case 'order_refund_notification_email_sent':
case 'payments_profile_deactivation_custom_instructions_executed': case 'payments_profile_deactivation_notification_email_sent':
case 'recurring_payment_confirmation_email_sent': case 'recurring_payment_custom_instructions_executed': case 'recurring_payment_notification_email_sent':
case 'recurring_payment_refund_custom_instructions_executed': case 'recurring_payment_refund_notification_email_sent':
case 'redelivery_notification_email_sent': case 'registration_required': case 'shipping_address_required': case 'tax_applied':
if ($data == 'yes') { $table_td = '<span style="color: #008000;">'.__('Yes', 'commerce-manager').'</span>'; }
elseif ($data == 'no')  { $table_td = '<span style="color: #c00000;">'.__('No', 'commerce-manager').'</span>'; }
else { $table_td = $data; } break;
case 'amount': case 'category_id': case 'client_id': case 'first_payment_amount': case 'form_id': case 'ip_address': case 'order_id': case 'payment_mode':
case 'payments_amount': case 'product_id': case 'quantity': case 'receiver_account': case 'referrer': case 'referrer2': case 'transaction_cost':
$table_td = ($data == '' ? '' : '<a href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'='.str_replace(' ', '%20', $data).'">'.$data.'</a>'); break;
case 'available_quantity': case 'payments_number': if ($data == 'unlimited') { $table_td = '<a href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=unlimited">'.__('Unlimited', 'commerce-manager').'</a>'; } else { $table_td = ($data == '' ? '' : '<a href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'='.$data.'">'.$data.'</a>'); } break;
case 'commission_amount': case 'commission2_amount': case 'price': case 'shipping_cost': case 'tax': case 'weight':
if (($table == 'orders') || ($table == 'recurring_payments')) { $table_td = ($data == '' ? '' : '<a href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'='.str_replace(' ', '%20', $data).'">'.$data.'</a>'); break; }
else { $table_td = $data; } break;
case 'commission_payment':
if (($table == 'orders') || ($table == 'recurring_payments')) {
if ($data == 'deferred') { $table_td = '<a href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=deferred">'.__('Deferred', 'commerce-manager').'</a>'; }
elseif ($data == 'instant') { $table_td = '<a href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=instant">'.__('Instant', 'commerce-manager').'</a>'; }
else { $table_td = $data; } }
else {
if ($data == 'deferred') { $table_td = __('Deferred', 'commerce-manager'); }
elseif ($data == 'instant') { $table_td = __('Instant', 'commerce-manager'); }
else { $table_td = $data; } } break;
case 'commission_status': case 'commission2_status': if ($data == 'paid') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=paid">'.__('Paid', 'commerce-manager').'</a>'; }
elseif ($data == 'unpaid') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=unpaid">'.__('Unpaid', 'commerce-manager').'</a>'; }
else { $table_td = $data; } break;
case 'commission_type': case 'commission2_type': if ($data == 'constant') { $table_td = __('Constant', 'commerce-manager'); }
elseif ($data == 'proportional') { $table_td = __('Proportional', 'commerce-manager'); }
else { $table_td = $data; } break;
case 'customer_affiliate_status': case 'customer_client_status': case 'customer_member_status':
if ($data == 'active') { $table_td = '<span style="color: #008000;">'.__('Active', 'commerce-manager').'</span>'; }
elseif ($data == 'inactive') { $table_td = '<span style="color: #e08000;">'.__('Inactive', 'commerce-manager').'</span>'; }
else { $table_td = $data; } break;
case 'customer_members_areas':
$members_areas = array_unique(preg_split('#[^0-9]#', $data, 0, PREG_SPLIT_NO_EMPTY));
foreach ($members_areas as $member_area) {
if ((function_exists('membership_manager_admin_menu')) && ($member_area > 0)) { $member_area = '<a href="admin.php?page=membership-manager-member-area&amp;id='.$member_area.'">'.$member_area.'</a>'; }
$members_areas_list .= $member_area.', '; }
$table_td = substr($members_areas_list, 0, -2); break;
case 'customer_user_role': $roles = commerce_manager_users_roles(); $table_td = $roles[$data]; break;
case 'code': case 'description': case 'instructions': case 'order_confirmation_email_body': case 'order_custom_instructions': case 'order_notification_email_body':
case 'order_processing_custom_instructions': case 'order_processing_notification_email_body': case 'order_refund_custom_instructions': case 'order_refund_notification_email_body':
case 'payments_profile_deactivation_custom_instructions': case 'payments_profile_deactivation_notification_email_body': case 'recurring_payment_confirmation_email_body':
case 'recurring_payment_custom_instructions': case 'recurring_payment_notification_email_body': case 'recurring_payment_refund_custom_instructions':
case 'recurring_payment_refund_notification_email_body': case 'redelivery_email_body': case 'redelivery_notification_email_body': case 'shipping_address': if (strlen($data) <= 80) { $table_td = $data; }
else { $table_td = substr($data, 0, 80); if (stristr($table_td, ' ')) { while (substr($table_td, -1) != ' ') { $table_td = substr($table_td, 0, -1); } } $table_td .= '[...]'; } break;
case 'download_url': case 'order_confirmation_url': case 'purchase_button_url': case 'referring_url': case 'thumbnail_url': case 'url': case 'website_url': $table_td = ($data == '' ? '' : '<a href="'.$data.'">'.($data == 'http://'.$_SERVER['SERVER_NAME'] ? '/' : str_replace('http://'.$_SERVER['SERVER_NAME'], '', $data)).'</a>'); break;
case 'email_address': case 'paypal_email_address': $table_td = '<a href="mailto:'.$data.'">'.$data.'</a>'; break;
case 'first_payment_period_time_unit': case 'first_payment_period_time_unit1': case 'first_payment_period_time_unit2': case 'first_payment_period_time_unit3':
case 'payments_period_time_unit': case 'payments_period_time_unit1': case 'payments_period_time_unit2': case 'payments_period_time_unit3':
case 'weight_unit': $table_td = ucfirst(__($data, 'commerce-manager')); break;
case 'first_sale_winner': if ($data == 'affiliate') { $table_td = __('Affiliate', 'commerce-manager'); }
elseif ($data == 'affiliator') { $table_td = __('Affiliator', 'commerce-manager'); }
else { $table_td = $data; } break;
case 'keywords':
$keywords = explode(',', $data);
foreach ($keywords as $keyword) {
$keyword = strtolower(trim($keyword));
$keyword = '<a href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;keywords='.$keyword.'">'.$keyword.'</a>';
$keywords_list .= $keyword.', '; }
$table_td = substr($keywords_list, 0, -2); break;
case 'login': $table_td = '<a href="admin.php?page=commerce-manager-client&amp;id='.$item->id.'">'.$data.'</a>'; break;
case 'name':
if ($table == 'products') { $url = htmlspecialchars(product_data('url')); }
elseif ($table == 'products_categories') { $url = htmlspecialchars(product_category_data('url')); }
$table_td = ($url == '' ? $data : '<a href="'.$url.'">'.($data == '' ? str_replace('http://'.$_SERVER['SERVER_NAME'], '', $url) : $data).'</a>'); break;
case 'orders_count': $table_td = ($data == 0 ? 0 : '<a href="admin.php?page=commerce-manager-orders&amp;form_id='.$item->id.'">'.$data.'</a>'); break;
case 'orders_initial_status': if ($data == 'processed') { $table_td = '<span style="color: #008000;">'.__('Processed', 'commerce-manager').'</span>'; }
elseif ($data == 'unprocessed') { $table_td = '<span style="color: #e08000;">'.__('Unprocessed', 'commerce-manager').'</span>'; }
elseif ($data == 'refunded') { $table_td = '<span style="color: #c00000;">'.__('Refunded', 'commerce-manager').'</span>'; }
else { $table_td = $data; } break;
case 'payments_number1': case 'payments_number2': case 'payments_number3': if ($data == 'unlimited') { $table_td = __('Unlimited', 'commerce-manager'); } else { $table_td = $data; } break;
case 'received_payments_number': $table_td = ($data == 0 ? 0 : '<a href="admin.php?page=commerce-manager-recurring-payments&amp;order_id='.$item->id.'">'.$data.'</a>'); break;
case 'recurring_payments_profile_status':
if ($data == 'active') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=active">'.__('Active', 'commerce-manager').'</a>'; }
elseif ($data == 'inactive') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=inactive">'.__('Inactive', 'commerce-manager').'</a>'; }
elseif ($data == 'deactivated') { $table_td = '<a style="color: #c00000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=deactivated">'.__('Deactivated', 'commerce-manager').'</a>'; }
else { $table_td = $data; } break;
case 'refunds_count': $table_td = ($data == 0 ? 0 : '<a href="admin.php?page=commerce-manager-orders&amp;product_id='.$item->id.'&amp;status=refunded">'.$data.'</a>'); break;
case 'sales_count': $table_td = ($data == 0 ? 0 : '<a href="admin.php?page=commerce-manager-orders&amp;product_id='.$item->id.'">'.$data.'</a>'); break;
case 'status':
if ($table == 'clients') {
if ($data == 'active') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=active">'.__('Active', 'commerce-manager').'</a>'; }
elseif ($data == 'inactive') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=inactive">'.__('Inactive', 'commerce-manager').'</a>'; }
elseif ($data == 'deactivated') { $table_td = '<a style="color: #c00000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=deactivated">'.__('Deactivated', 'commerce-manager').'</a>'; }
else { $table_td = $data; } }
else {
if ($data == 'processed') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=processed">'.__('Processed', 'commerce-manager').'</a>'; }
elseif ($data == 'received') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=received">'.__('Received', 'commerce-manager').'</a>'; }
elseif ($data == 'unprocessed') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=unprocessed">'.__('Unprocessed', 'commerce-manager').'</a>'; }
elseif ($data == 'refunded') { $table_td = '<a style="color: #c00000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=refunded">'.($table == 'orders' ? __('Refunded', 'commerce-manager') : __('Refunded ', 'commerce-manager')).'</a>'; }
else { $table_td = $data; } } break;
case 'tax_included_in_price':
if (($table == 'orders') || ($table == 'recurring_payments')) {
if ($data == 'yes') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=yes">'.__('Yes', 'commerce-manager').'</a>'; }
elseif ($data == 'no') { $table_td = '<a style="color: #c00000;" href="admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;'.$column.'=no">'.__('No', 'commerce-manager').'</a>'; }
else { $table_td = $data; } }
else {
if ($data == 'yes') { $table_td = '<span style="color: #008000;">'.__('Yes', 'commerce-manager').'</span>'; }
elseif ($data == 'no')  { $table_td = '<span style="color: #c00000;">'.__('No', 'commerce-manager').'</span>'; }
else { $table_td = $data; } } break;
case 'website_name': $website_url = htmlspecialchars(order_data('website_url')); $table_td = ($website_url == '' ? $data : '<a href="'.$website_url.'">'.($data == '' ? str_replace('http://'.$_SERVER['SERVER_NAME'], '', $website_url) : $data).'</a>'); break;
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
case 'clients': case 'clients_performances': $singular = __('client', 'commerce-manager'); $plural = __('clients', 'commerce-manager'); break;
case 'clients_categories': case 'forms_categories': case 'products_categories': $singular = __('category', 'commerce-manager'); $plural = __('categories', 'commerce-manager'); break;
case 'forms': case 'forms_performances': $singular = __('form', 'commerce-manager'); $plural = __('forms', 'commerce-manager'); break;
case 'orders': $singular = __('order', 'commerce-manager'); $plural = __('orders', 'commerce-manager'); break;
case 'products': case 'products_performances': $singular = __('product', 'commerce-manager'); $plural = __('products', 'commerce-manager'); break;
case 'recurring_payments': $singular = __('payment', 'commerce-manager'); $plural = __('payments', 'commerce-manager'); break;
default: $singular = __('item', 'commerce-manager'); $plural = __('items', 'commerce-manager'); }
if ($_GET['paged'] == 1) { $prev_paged = 1; } else { $prev_paged = $_GET['paged'] - 1; }
if ($_GET['paged'] == $max_paged) { $next_paged = $max_paged; } else { $next_paged = $_GET['paged'] + 1; }
$url = 'admin.php?page='.$_GET['page'].$_GET['criteria'].'&amp;orderby='.$_GET['orderby'].'&amp;order='.$_GET['order'];
echo '<div class="tablenav-pages" style="float: right;"><span class="displaying-num">'.$n.' '.($n <= 1 ? $singular : $plural).'</span>
<a class="first-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the first page').'" href="'.$url.'&amp;paged=1">&laquo;</a>
<a class="prev-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the previous page').'" href="'.$url.'&amp;paged='.$prev_paged.'">&lsaquo;</a>
<span class="paging-input">'.($location == 'top' ? '<input class="current-page" title="'.__('Current page').'" type="text" name="paged" id="paged" value="'.$_GET['paged'].'" size="2" />' : $_GET['paged']).' '.__('of').' <span class="total-pages">'.$max_paged.'</span></span>
<a class="next-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the next page').'" href="'.$url.'&amp;paged='.$next_paged.'">&rsaquo;</a>
<a class="last-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the last page').'" href="'.$url.'&amp;paged='.$max_paged.'">&raquo;</a></div>'; }


function item_performance($column, $start_date, $end_date, $filterby, $item, $type) {
global $wpdb;
$_GET[$type.'_id'] = $item->id; $_GET[$type.'_data'] = $item;

if (($column == $_GET['orderby']) && (isset($_GET['datas'][$item->id]))) { $data = $_GET['datas'][$item->id]; }
elseif (($column == 'id') || ($column == 'login') || ($column == 'name')) { $data = commerce_item_data($type, $column); }
else {
switch ($column) {
case 'amount': case 'refunded_amount': case 'unrefunded_amount': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments'); $field = 'amount'; break;
case 'commission_amount': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments'); $field = 'commission_amount'; break;
case 'commission2_amount': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments'); $field = 'commission2_amount'; break;
case 'orders': case 'refunded_orders': case 'unrefunded_orders': $table = $wpdb->prefix.'commerce_manager_orders'; $field = ''; break;
case 'orders_amount': case 'refunded_orders_amount': case 'unrefunded_orders_amount': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'amount'; break;
case 'orders_commission_amount': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'commission_amount'; break;
case 'orders_commission2_amount': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'commission2_amount'; break;
case 'sold_items': case 'refunded_items': case 'unrefunded_items': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'quantity'; break;
case 'recurring_payments': case 'refunded_recurring_payments': case 'unrefunded_recurring_payments': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = ''; break;
case 'recurring_payments_amount': case 'refunded_recurring_payments_amount': case 'unrefunded_recurring_payments_amount': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'amount'; break;
case 'recurring_payments_commission_amount': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'commission_amount'; break;
case 'recurring_payments_commission2_amount': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'commission2_amount'; break; }

$date_criteria = "AND (date BETWEEN '".$start_date."' AND '".$end_date."')";
if ($_GET['s'] != '') { $filter_criteria = (is_numeric($_GET['s']) ? "AND (".$filterby." = ".$_GET['s'].")" : "AND (".$filterby." = '".$_GET['s']."')"); }
if (strstr($column, 'unrefunded')) { $status_criteria = "AND status != 'refunded'"; }
elseif (strstr($column, 'refunded')) { $status_criteria = "AND status = 'refunded'"; }

if ($type == 'commerce_form') { $type = 'form'; }
if (is_string($table)) {
if ($field == '') {
$row = $wpdb->get_row("SELECT count(*) as total FROM $table WHERE ".$type."_id = ".$item->id." $date_criteria $filter_criteria $status_criteria", OBJECT);
$data = (int) $row->total; }
else {
$row = $wpdb->get_row("SELECT SUM($field) AS total FROM $table WHERE ".$type."_id = ".$item->id." $date_criteria $filter_criteria $status_criteria", OBJECT);
$data = round(100*$row->total)/100; } }
else {
$data = 0; foreach ($table as $table_name) {
$row = $wpdb->get_row("SELECT SUM($field) AS total FROM $table_name WHERE ".$type."_id = ".$item->id." $date_criteria $filter_criteria $status_criteria", OBJECT);
$data = $data + round(100*$row->total)/100; } } }

return $data; }


function item_performance_td($column, $start_date, $end_date, $filterby, $item, $type, $currency_code) {
if (($column == 'id') || ($column == 'login') || ($column == 'name')) { $performance_td = table_td(str_replace('commerce_', '', $type).'s', $column, $item); }
else {
$criteria = str_replace('&amp;s=', '&amp;'.$filterby.'=', $_GET['criteria']);
$performance_td = item_performance($column, $start_date, $end_date, $filterby, $item, $type);
if (strstr($column, 'amount')) { $performance_td .= ' '.$currency_code; }
if ((strstr($column, 'orders')) || (strstr($column, 'items'))) { $performance_td = '<a href="admin.php?page=commerce-manager-orders'.$criteria.'&amp;'.str_replace('commerce_', '', $type).'_id='.$item->id.'">'.$performance_td.'</a>'; }
elseif (strstr($column, 'recurring_payments')) { $performance_td = '<a href="admin.php?page=commerce-manager-recurring-payments'.$criteria.'&amp;'.str_replace('commerce_', '', $type).'_id='.$item->id.'">'.$performance_td.'</a>'; }
if (strstr($column, 'unrefunded')) { $performance_td = str_replace(array('<a', '">'), array('<a style="color: #008000;"', '&amp;status=unrefunded">'), $performance_td); }
elseif (strstr($column, 'refunded')) { $performance_td = str_replace(array('<a', '">'), array('<a style="color: #c00000;"', '&amp;status=refunded">'), $performance_td); }
if ($column == 'refunded_amount') { $performance_td = '<span style="color: #c00000;">'.$performance_td.'</span>'; }
elseif ($column == 'unrefunded_amount') { $performance_td = '<span style="color: #008000;">'.$performance_td.'</span>'; } }
return $performance_td; }


foreach (array(
'client',
'commerce-user',
'customer',
'order',
'recurring-payment') as $tag) { remove_shortcode($tag); }