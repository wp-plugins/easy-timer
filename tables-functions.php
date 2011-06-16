<?php global $wpdb;
$affiliates_table_name = $wpdb->prefix.'affiliation_manager_affiliates';
$clicks_table_name = $wpdb->prefix.'affiliation_manager_clicks';
$orders_table_name = $wpdb->prefix.'commerce_manager_orders';

$fields = array(
'commission_payment',
'commission_status',
'ip_address',
'product_id',
'referrer',
'status');

foreach ($fields as $field) {
if (isset($_GET[$field])) {
$_GET['selection_criteria'] .= '&amp;'.$field.'='.$_GET[$field];
$selection_criteria .= " AND ".$field." = '".$_GET[$field]."'"; } }


function no_items($table) {
switch ($table) {
case 'affiliates': $no_items = __('No affiliates', 'affiliation-manager'); break;
case 'clicks': $no_items = __('No clicks', 'affiliation-manager'); break;
case 'commissions': $no_items = __('No commissions', 'affiliation-manager'); }
return $no_items; }


function row_actions($table, $item) {
switch ($table) {
case 'affiliates': $row_actions = '<div class="row-actions" style="margin-top: 2em; position: absolute; width: 1000%;"><span class="edit">
<a href="admin.php?page=affiliation-manager-affiliate&amp;id='.$item->id.'">'.__('Edit').'</a></span> | <span class="delete">
<a href="admin.php?page=affiliation-manager-affiliate&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span> | <span class="view">
<a href="admin.php?page=affiliation-manager-statistics&amp;referrer='.$item->login.'">'.__('Statistics', 'affiliation-manager').'</a></span></div>'; break;
case 'clicks': $row_actions = '<div class="row-actions" style="margin-top: 2em; position: absolute; width: 1000%;"><span class="delete">
<a href="admin.php?page=affiliation-manager-clicks&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span></div>'; break;
case 'commissions': if (function_exists('commerce_manager_admin_menu')) {
$row_actions = '<div class="row-actions" style="margin-top: 2em; position: absolute; width: 1000%;"><span class="edit">
<a href="admin.php?page=commerce-manager-order&amp;id='.$item->id.'#affiliation">'.__('Edit').'</a></span> | <span class="delete">
<a href="admin.php?page=commerce-manager-order&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span></div>'; } }
return $row_actions; }


function table_criteria($table) {
switch ($table) {
case 'commissions': $table_criteria = ' AND commission_amount > 0'; }
return $table_criteria; }


function table_name($table) {
global $wpdb;
switch ($table) {
case 'affiliates': $table_name = $wpdb->prefix.'affiliation_manager_affiliates'; break;
case 'clicks': $table_name = $wpdb->prefix.'affiliation_manager_clicks'; break;
case 'commissions': $table_name = $wpdb->prefix.'commerce_manager_orders'; }
return $table_name; }


function table_td($table, $column, $item) {
switch ($table) {
case 'affiliates': $_GET['affiliate_data'] = $item; $data = affiliate_data($column); break;
case 'clicks': $_GET['click_id'] = $item->id; $_GET['click_data'] = $item; $data = click_data($column); break;
case 'commissions': $_GET['commission_id'] = $item->id; $_GET['commission_data'] = $item; $data = commission_data($column); break;
default: $data = affiliation_format_data($column, $item->$column); }
$data = htmlspecialchars($data);
switch ($column) {
case 'commission_payment':
if ($singular == __('commission', 'commerce-manager')) {
if ($data == 'deferred') { $table_td = '<a href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=deferred">'.__('Deferred', 'affiliation-manager').'</a>'; }
elseif ($data == 'instant') { $table_td = '<a href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=instant">'.__('Instant', 'affiliation-manager').'</a>'; }
else { $table_td = $data; } }
else {
if ($data == 'deferred') { $table_td = __('Deferred', 'affiliation-manager'); }
elseif ($data == 'instant') { $table_td = __('Instant', 'affiliation-manager'); }
else { $table_td = $data; } } break;
case 'commission_status': if ($data == 'paid') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=paid">'.__('Paid', 'affiliation-manager').'</a>'; }
elseif ($data == 'unpaid') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=unpaid">'.__('Unpaid', 'affiliation-manager').'</a>'; }
else { $table_td = $data; } break;
case 'commission_type': if ($data == 'constant') { $table_td = __('Constant', 'affiliation-manager'); }
elseif ($data == 'proportional') { $table_td = __('Proportional', 'affiliation-manager'); }
else { $table_td = $data; } break;
case 'email_address': case 'paypal_email_address': $table_td = '<a href="mailto:'.$data.'">'.$data.'</a>'; break;
case 'first_sale_winner': if ($data == 'affiliate') { $table_td = __('Affiliate', 'affiliation-manager'); }
elseif ($data == 'affiliator') { $table_td = __('Affiliator', 'affiliation-manager'); }
else { $table_td = $data; } break;
case 'instructions': case 'shipping_address': if (strlen($data) <= 80) { $table_td = $data; }
else { $table_td = substr($data, 0, 80); if (stristr($table_td, ' ')) { while (substr($table_td, -1) != ' ') { $table_td = substr($table_td, 0, -1); } } $table_td .= '[...]'; } break;
case 'ip_address': case 'product_id': case 'referrer': $table_td = ($data == '' ? '' : '<a href="admin.php?page='.$_GET['page'].'&amp;'.$column.'='.$data.'">'.$data.'</a>'); break;
case 'login': $table_td = '<a href="admin.php?page=affiliation-manager-affiliate&amp;id='.$item->id.'">'.$data.'</a>'; break;
case 'referring_url': case 'url': case 'website_url': $table_td = ($data == '' ? '' : '<a href="'.$data.'">'.($data == 'http://'.$_SERVER['SERVER_NAME'] ? '/' : str_replace('http://'.$_SERVER['SERVER_NAME'], '', $data)).'</a>'); break;
case 'status': if ($data == 'processed') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=processed">'.__('Processed', 'affiliation-manager').'</a>'; }
elseif ($data == 'unprocessed') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=unprocessed">'.__('Unprocessed', 'affiliation-manager').'</a>'; }
elseif ($data == 'refunded') { $table_td = '<a style="color: #c00000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=refunded">'.__('Refunded', 'affiliation-manager').'</a>'; }
else { $table_td = $data; } break;
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


function tablenav_pages($table, $n, $max_paged, $location) {
switch ($table) {
case 'affiliates': $singular = __('affiliate', 'affiliation-manager'); $plural = __('affiliates', 'affiliation-manager'); break;
case 'clicks': $singular = __('click', 'affiliation-manager'); $plural = __('clicks', 'affiliation-manager'); break;
case 'commissions': $singular = __('commission', 'affiliation-manager'); $plural = __('commissions', 'affiliation-manager'); break; 
default: $singular = __('item', 'affiliation-manager'); $plural = __('items', 'affiliation-manager'); }
if ($_GET['paged'] == 1) { $prev_paged = 1; } else { $prev_paged = $_GET['paged'] - 1; }
if ($_GET['paged'] == $max_paged) { $next_paged = $max_paged; } else { $next_paged = $_GET['paged'] + 1; }
$url = 'admin.php?page='.$_GET['page'].'&amp;orderby='.$_GET['orderby'].'&amp;order='.$_GET['order'].$_GET['selection_criteria'].($_GET['s'] == '' ? '' : '&amp;s='.$_GET['s']);
echo '<div class="tablenav-pages" style="float: right;"><span class="displaying-num">'.$n.' '.($n <= 1 ? $singular : $plural).'</span>
<a class="first-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the first page').'" href="'.$url.'&amp;paged=1">&laquo;</a>
<a class="prev-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the previous page').'" href="'.$url.'&amp;paged='.$prev_paged.'">&lsaquo;</a>
<span class="paging-input">'.($location == 'top' ? '<input class="current-page" title="'.__('Current page').'" type="text" name="paged" id="paged" value="'.$_GET['paged'].'" size="2" />' : $_GET['paged']).' '.__('of').' <span class="total-pages">'.$max_paged.'</span></span>
<a class="next-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the next page').'" href="'.$url.'&amp;paged='.$next_paged.'">&rsaquo;</a>
<a class="last-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the last page').'" href="'.$url.'&amp;paged='.$max_paged.'">&raquo;</a></div>'; }


remove_shortcode('affiliate');
remove_shortcode('commission');