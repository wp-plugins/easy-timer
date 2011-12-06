<?php global $wpdb;

foreach (array(
'autoresponder',
'autoresponder_list',
'category_id',
'commission_payment',
'commission_status',
'commission2_status',
'form_id',
'ip_address',
'order_id',
'payments_number',
'product_id',
'referrer',
'referrer2',
'status') as $field) {
if (isset($_GET[$field])) {
$_GET['selection_criteria'] .= '&amp;'.$field.'='.$_GET[$field];
$selection_criteria .= " AND ".$field." = '".$_GET[$field]."'"; } }


function no_items($table) {
switch ($table) {
case 'affiliates': $no_items = __('No affiliates', 'affiliation-manager'); break;
case 'affiliates_categories': $no_items = __('No categories', 'affiliation-manager'); break;
case 'clicks': $no_items = __('No clicks', 'affiliation-manager'); break;
case 'commissions': case 'prospects_commissions': case 'recurring_commissions': $no_items = __('No commissions', 'affiliation-manager'); }
return $no_items; }


function row_actions($table, $item) {
global $wpdb;
switch ($table) {
case 'affiliates': $row_actions = 
'<span class="edit"><a href="admin.php?page=affiliation-manager-affiliate&amp;id='.$item->id.'">'.__('Edit').'</a></span>
 | <span class="delete"><a href="admin.php?page=affiliation-manager-affiliate&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span>
 | <span class="view"><a href="admin.php?page=affiliation-manager-statistics&amp;referrer='.$item->login.'">'.__('Statistics', 'affiliation-manager').'</a></span>'; break;
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
'<span class="delete"><a href="admin.php?page=affiliation-manager-clicks&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span>
 | <span class="delete"><a href="admin.php?page=affiliation-manager-clicks&amp;referrer='.$item->referrer.'&amp;action=delete">'.__('Delete all the clicks of this referrer', 'affiliation-manager').'</a></span>'; break;
case 'commissions': if (function_exists('commerce_manager_admin_menu')) {
$row_actions = '<span class="edit"><a href="admin.php?page=commerce-manager-order&amp;id='.$item->id.'#affiliation">'.__('Edit').'</a></span> | '; }
$row_actions .= 
'<span class="delete"><a href="admin.php?page=affiliation-manager-commissions&amp;id='.$item->id.'&amp;action=cancel">'.__('Cancel').'</a></span>
 | <span class="delete"><a href="admin.php?page=affiliation-manager-commissions&amp;referrer='.$item->referrer.'&amp;action=cancel">'.__('Cancel all the commissions of this referrer', 'affiliation-manager').'</a></span>'; break;
case 'prospects_commissions': if (function_exists('optin_manager_admin_menu')) {
$row_actions = '<span class="edit"><a href="admin.php?page=optin-manager-prospect&amp;id='.$item->id.'#affiliation">'.__('Edit').'</a></span> | '; }
$row_actions .= 
'<span class="delete"><a href="admin.php?page=affiliation-manager-prospects-commissions&amp;id='.$item->id.'&amp;action=cancel">'.__('Cancel').'</a></span>
 | <span class="delete"><a href="admin.php?page=affiliation-manager-prospects-commissions&amp;referrer='.$item->referrer.'&amp;action=cancel">'.__('Cancel all the commissions of this referrer', 'affiliation-manager').'</a></span>'; break;
case 'recurring_commissions': $row_actions = 
'<span class="delete"><a href="admin.php?page=affiliation-manager-recurring-commissions&amp;id='.$item->id.'&amp;action=cancel">'.__('Cancel').'</a></span>
 | <span class="delete"><a href="admin.php?page=affiliation-manager-recurring-commissions&amp;referrer='.$item->referrer.'&amp;action=cancel">'.__('Cancel all the commissions of this referrer', 'affiliation-manager').'</a></span>';
if (function_exists('commerce_manager_admin_menu')) {
$row_actions .= ' | <span class="edit"><a href="admin.php?page=commerce-manager-order&amp;id='.$item->order_id.'">'.__('Edit the order', 'affiliation-manager').'</a></span>'; } break; }
return '<div class="row-actions" style="margin-top: 2em; position: absolute;">'.$row_actions.'</div>'; }


function table_criteria($table) {
switch ($table) {
case 'commissions': case 'prospects_commissions': case 'recurring_commissions': $table_criteria = ' AND (commission_amount > 0 OR commission2_amount > 0)'; }
return $table_criteria; }


function table_name($table) {
global $wpdb;
switch ($table) {
case 'affiliates': case 'affiliates_categories': case 'clicks': $table_name = $wpdb->prefix.'affiliation_manager_'.$table; break;
case 'commissions': $table_name = $wpdb->prefix.'commerce_manager_orders'; break;
case 'prospects_commissions': $table_name = $wpdb->prefix.'optin_manager_prospects'; break;
case 'recurring_commissions': $table_name = $wpdb->prefix.'commerce_manager_recurring_payments'; }
return $table_name; }


function table_td($table, $column, $item) {
switch ($table) {
case 'affiliates': $_GET['affiliate_data'] = $item; $data = affiliate_data($column); break;
case 'affiliates_categories': $_GET['affiliate_category_id'] = $item->id; $_GET['affiliate_category_data'] = $item; $data = affiliate_category_data($column); break;
case 'clicks': $_GET['click_id'] = $item->id; $_GET['click_data'] = $item; $data = click_data($column); break;
case 'commissions': $_GET['commission_id'] = $item->id; $_GET['commission_data'] = $item; $data = commission_data($column); break;
case 'prospects_commissions': $_GET['prospect_commission_id'] = $item->id; $_GET['prospect_commission_data'] = $item; $data = prospect_commission_data($column); break;
case 'recurring_commissions': $_GET['recurring_commission_id'] = $item->id; $_GET['recurring_commission_data'] = $item; $data = recurring_commission_data($column); break;
default: $data = affiliation_format_data($column, $item->$column); }
$data = htmlspecialchars($data);
switch ($column) {
case 'bonus_download_url': case 'referring_url': case 'url': case 'website_url': $table_td = ($data == '' ? '' : '<a href="'.$data.'">'.($data == 'http://'.$_SERVER['SERVER_NAME'] ? '/' : str_replace('http://'.$_SERVER['SERVER_NAME'], '', $data)).'</a>'); break;
case 'bonus_instructions': case 'description': case 'instructions': case 'shipping_address': if (strlen($data) <= 80) { $table_td = $data; }
else { $table_td = substr($data, 0, 80); if (stristr($table_td, ' ')) { while (substr($table_td, -1) != ' ') { $table_td = substr($table_td, 0, -1); } } $table_td .= '[...]'; } break;
case 'autoresponder': case 'autoresponder_list': case 'category_id': case 'form_id': case 'ip_address': case 'order_id': case 'product_id': case 'referrer': case 'referrer2': $table_td = ($data == '' ? '' : '<a href="admin.php?page='.$_GET['page'].'&amp;'.$column.'='.$data.'">'.$data.'</a>'); break;
case 'commission2_enabled': 
if ($data == 'yes') { $table_td = '<span style="color: #008000;">'.__('Yes', 'affiliation-manager').'</span>'; }
elseif ($data == 'no')  { $table_td = '<span style="color: #c00000;">'.__('No', 'affiliation-manager').'</span>'; }
else { $table_td = $data; } break;
case 'commission_payment':
if ($data == 'deferred') { $table_td = '<a href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=deferred">'.__('Deferred', 'affiliation-manager').'</a>'; }
elseif ($data == 'instant') { $table_td = '<a href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=instant">'.__('Instant', 'affiliation-manager').'</a>'; }
else { $table_td = $data; } break;
case 'commission_status': case 'commission2_status': if ($data == 'paid') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=paid">'.__('Paid', 'affiliation-manager').'</a>'; }
elseif ($data == 'unpaid') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=unpaid">'.__('Unpaid', 'affiliation-manager').'</a>'; }
else { $table_td = $data; } break;
case 'commission_type': case 'commission2_type': if ($data == 'constant') { $table_td = __('Constant', 'affiliation-manager'); }
elseif ($data == 'proportional') { $table_td = __('Proportional', 'affiliation-manager'); }
else { $table_td = $data; } break;
case 'email_address': case 'paypal_email_address': $table_td = '<a href="mailto:'.$data.'">'.$data.'</a>'; break;
case 'first_sale_winner': if ($data == 'affiliate') { $table_td = __('Affiliate', 'affiliation-manager'); }
elseif ($data == 'affiliator') { $table_td = __('Affiliator', 'affiliation-manager'); }
else { $table_td = $data; } break;
case 'login': $table_td = '<a href="admin.php?page=affiliation-manager-affiliate&amp;id='.$item->id.'">'.$data.'</a>'; break;
case 'payments_number': if ($data == 'unlimited') { $table_td = '<a href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=unlimited">'.__('Unlimited', 'affiliation-manager').'</a>'; } else { $table_td = $data; } break;
case 'payments_period_time_unit': $table_td = __(ucfirst($data), 'affiliation-manager'); break;
case 'received_payments_number': $table_td = ((($data == 0) || (!function_exists('commerce_manager_admin_menu'))) ? $data : '<a href="admin.php?page=commerce-manager-recurring-payments&amp;order_id='.$item->id.'">'.$data.'</a>'); break;
case 'status':
if ($table == 'affiliates') {
if ($data == 'active') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=active">'.__('Active', 'affiliation-manager').'</a>'; }
elseif ($data == 'inactive') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=inactive">'.__('Inactive', 'affiliation-manager').'</a>'; }
else { $table_td = $data; } }
elseif ($table == 'prospects_commissions') {
if ($data == 'active') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=active">'.__('Active', 'affiliation-manager').'</a>'; }
elseif ($data == 'inactive') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=inactive">'.__('Inactive', 'affiliation-manager').'</a>'; }
elseif ($data == 'unsubscribed') { $table_td = '<a style="color: #c00000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=unsubscribed">'.__('Unsubscribed', 'affiliation-manager').'</a>'; }
else { $table_td = $data; } }
else {
if ($data == 'processed') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=processed">'.__('Processed', 'affiliation-manager').'</a>'; }
elseif ($data == 'received') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=received">'.__('Received', 'affiliation-manager').'</a>'; }
elseif ($data == 'unprocessed') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=unprocessed">'.__('Unprocessed', 'affiliation-manager').'</a>'; }
elseif ($data == 'refunded') { $table_td = '<a style="color: #c00000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=refunded">'.__('Refunded', 'affiliation-manager').'</a>'; }
else { $table_td = $data; } } break;
case 'tax_included_in_price': if ($data == 'yes') { $table_td = '<span style="color: #008000;">'.__('Yes', 'affiliation-manager').'</span>'; }
elseif ($data == 'no')  { $table_td = '<span style="color: #c00000;">'.__('No', 'affiliation-manager').'</span>'; }
else { $table_td = $data; } break;
case 'website_name': $website_url = htmlspecialchars(affiliation_format_data($column, $item->website_url)); $table_td = ($website_url == '' ? $data : '<a href="'.$website_url.'">'.($data == '' ? str_replace('http://'.$_SERVER['SERVER_NAME'], '', $website_url) : $data).'</a>'); break;
default: $table_td = $data; }
return $table_td; }


function table_th($table, $column) {
include 'tables.php';
if (strstr($_GET['page'], 'statistics')) { $table_th = '<th scope="col" class="manage-column" style="width: '.$tables[$table][$column]['width'].'%;">'.$tables[$table][$column]['name'].'</th>'; }
else {
$table_th = '<th scope="col" class="manage-column '.($_GET['orderby'] == $column ? 'sorted '.$_GET['order'] : 'sortable desc').'" style="width: '.$tables[$table][$column]['width'].'%;">
<a href="admin.php?page='.$_GET['page'].'&amp;orderby='.$column.'&amp;order='.(($_GET['orderby'] == $column && $_GET['order'] == 'asc') ? 'desc' : 'asc').
$_GET['selection_criteria'].($_GET['s'] == '' ? '' : '&amp;s='.$_GET['s']).'">
<span>'.$tables[$table][$column]['name'].'</span><span class="sorting-indicator"></span></a></th>'; }
return $table_th; }


function tablenav_pages($table, $n, $max_paged, $end_date, $location) {
switch ($table) {
case 'affiliates': $singular = __('affiliate', 'affiliation-manager'); $plural = __('affiliates', 'affiliation-manager'); break;
case 'affiliates_categories': $singular = __('category', 'affiliation-manager'); $plural = __('categories', 'affiliation-manager'); break;
case 'clicks': $singular = __('click', 'affiliation-manager'); $plural = __('clicks', 'affiliation-manager'); break;
case 'commissions': case 'prospects_commissions': case 'recurring_commissions': $singular = __('commission', 'affiliation-manager'); $plural = __('commissions', 'affiliation-manager'); break; 
default: $singular = __('item', 'affiliation-manager'); $plural = __('items', 'affiliation-manager'); }
if ($_GET['paged'] == 1) { $prev_paged = 1; } else { $prev_paged = $_GET['paged'] - 1; }
if ($_GET['paged'] == $max_paged) { $next_paged = $max_paged; } else { $next_paged = $_GET['paged'] + 1; }
$url = 'admin.php?page='.$_GET['page'].'&amp;end_date='.$end_date.'&amp;orderby='.$_GET['orderby'].'&amp;order='.$_GET['order'].$_GET['selection_criteria'].($_GET['s'] == '' ? '' : '&amp;s='.$_GET['s']);
echo '<div class="tablenav-pages" style="float: right;"><span class="displaying-num">'.$n.' '.($n <= 1 ? $singular : $plural).'</span>
<a class="first-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the first page').'" href="'.$url.'&amp;paged=1">&laquo;</a>
<a class="prev-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the previous page').'" href="'.$url.'&amp;paged='.$prev_paged.'">&lsaquo;</a>
<span class="paging-input">'.($location == 'top' ? '<input class="current-page" title="'.__('Current page').'" type="text" name="paged" id="paged" value="'.$_GET['paged'].'" size="2" />' : $_GET['paged']).' '.__('of').' <span class="total-pages">'.$max_paged.'</span></span>
<a class="next-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the next page').'" href="'.$url.'&amp;paged='.$next_paged.'">&rsaquo;</a>
<a class="last-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the last page').'" href="'.$url.'&amp;paged='.$max_paged.'">&raquo;</a></div>'; }


remove_shortcode('affiliate');
remove_shortcode('commission');
remove_shortcode('recurring-commission');