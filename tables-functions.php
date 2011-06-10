<?php global $wpdb;
$orders_table_name = $wpdb->prefix.'commerce_manager_orders';
$products_table_name = $wpdb->prefix.'commerce_manager_products';

$fields = array(
'available_quantity',
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
case 'orders': $no_items = __('No orders', 'commerce-manager'); break;
case 'products': $no_items = __('No products', 'commerce-manager'); }
return $no_items; }


function row_actions($table, $item) {
switch ($table) {
case 'orders': $row_actions = '<div class="row-actions" style="margin-top: 2em; position: absolute; width: 1000%;"><span class="edit">
<a href="admin.php?page=commerce-manager-order&amp;id='.$item->id.'">'.__('Edit').'</a></span> | <span class="delete">
<a href="admin.php?page=commerce-manager-order&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span></div>'; break;
case 'products': $row_actions = '<div class="row-actions" style="margin-top: 2em; position: absolute; width: 1000%;"><span class="edit">
<a href="admin.php?page=commerce-manager-product&amp;id='.$item->id.'">'.__('Edit').'</a></span> | <span class="delete">
<a href="admin.php?page=commerce-manager-product&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span> | <span class="view">
<a href="admin.php?page=commerce-manager-statistics&amp;product_id='.$item->id.'">'.__('Statistics', 'commerce-manager').'</a></span></div>'; }
return $row_actions; }


function table_name($table) {
global $wpdb;
switch ($table) {
case 'orders': $table_name = $wpdb->prefix.'commerce_manager_orders'; break;
case 'products': $table_name = $wpdb->prefix.'commerce_manager_products'; }
return $table_name; }


function table_td($table, $column, $item) {
switch ($table) {
case 'orders': $_GET['order_data'] = $item; $data = order_data($column); break;
case 'products': $_GET['product_data'] = $item; $data = product_data($column); break;
default: $data = commerce_format_data($column, $item->$column); }
$data = htmlspecialchars($data);
switch ($column) {
case 'affiliation_enabled': case 'customer_subscribed_to_autoresponder': case 'downloadable': 
case 'email_sent_to_customer': case 'email_sent_to_seller': case 'registration_required': case 'sandbox_enabled': case 'shipping_address_required':
case 'tax_applied': case 'tax_included_in_price':
if ($data == 'yes') { $table_td = '<span style="color: #008000;">'.__('Yes', 'commerce-manager').'</span>'; }
elseif ($data == 'no')  { $table_td = '<span style="color: #c00000;">'.__('No', 'commerce-manager').'</span>'; }
else { $table_td = $data; } break;
case 'available_quantity': if ($data == 'unlimited') { $table_td = '<a href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=unlimited">'.__('Unlimited', 'commerce-manager').'</a>'; } else { $table_td = $data; } break;
case 'commission_payment':
if ($singular == __('order', 'commerce-manager')) {
if ($data == 'deferred') { $table_td = '<a href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=deferred">'.__('Deferred', 'commerce-manager').'</a>'; }
elseif ($data == 'instant') { $table_td = '<a href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=instant">'.__('Instant', 'commerce-manager').'</a>'; }
else { $table_td = $data; } }
else {
if ($data == 'deferred') { $table_td = __('Deferred', 'commerce-manager'); }
elseif ($data == 'instant') { $table_td = __('Instant', 'commerce-manager'); }
else { $table_td = $data; } } break;
case 'commission_status': if ($data == 'paid') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=paid">'.__('Paid', 'commerce-manager').'</a>'; }
elseif ($data == 'unpaid') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=unpaid">'.__('Unpaid', 'commerce-manager').'</a>'; }
else { $table_td = $data; } break;
case 'commission_type': if ($data == 'constant') { $table_td = __('Constant', 'commerce-manager'); }
elseif ($data == 'proportional') { $table_td = __('Proportional', 'commerce-manager'); }
else { $table_td = $data; } break;
case 'description': case 'email_to_customer_body': case 'email_to_seller_body': case 'instructions': case 'shipping_address': if (strlen($data) <= 80) { $table_td = $data; }
else { $table_td = substr($data, 0, 80); if (stristr($table_td, ' ')) { while (substr($table_td, -1) != ' ') { $table_td = substr($table_td, 0, -1); } } $table_td .= '[...]'; } break;
case 'download_url': case 'order_confirmation_url': case 'purchase_button_url': case 'referring_url': case 'thumbnail_url': case 'url': case 'website_url': $table_td = ($data == '' ? '' : '<a href="'.$data.'">'.($data == 'http://'.$_SERVER['SERVER_NAME'] ? '/' : str_replace('http://'.$_SERVER['SERVER_NAME'], '', $data)).'</a>'); break;
case 'email_address': case 'paypal_email_address': $table_td = '<a href="mailto:'.$data.'">'.$data.'</a>'; break;
case 'first_sale_winner': if ($data == 'affiliate') { $table_td = __('Affiliate', 'commerce-manager'); }
elseif ($data == 'affiliator') { $table_td = __('Affiliator', 'commerce-manager'); }
else { $table_td = $data; } break;
case 'ip_address': case 'product_id': case 'referrer': $table_td = ($data == '' ? '' : '<a href="admin.php?page='.$_GET['page'].'&amp;'.$column.'='.$data.'">'.$data.'</a>'); break;
case 'name': $table_td = ($item->url == '' ? $item->name : '<a href="'.$item->url.'">'.($item->name == '' ? str_replace('http://'.$_SERVER['SERVER_NAME'], '', $item->url) : $item->name).'</a>'); break;
case 'orders_initial_status': if ($data == 'processed') { $table_td = '<span style="color: #008000;">'.__('Processed', 'commerce-manager').'</span>'; }
elseif ($data == 'unprocessed') { $table_td = '<span style="color: #e08000;">'.__('Unprocessed', 'commerce-manager').'</span>'; }
elseif ($data == 'refunded') { $table_td = '<span style="color: #c00000;">'.__('Refunded', 'commerce-manager').'</span>'; }
else { $table_td = $data; } break;
case 'refunds_count': $table_td = ($data == 0 ? 0 : '<a href="admin.php?page=commerce-manager-orders&amp;product_id='.$item->id.'&amp;status=refunded">'.$data.'</a>'); break;
case 'sales_count': $table_td = ($data == 0 ? 0 : '<a href="admin.php?page=commerce-manager-orders&amp;product_id='.$item->id.'">'.$data.'</a>'); break;
case 'status': if ($data == 'processed') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=processed">'.__('Processed', 'commerce-manager').'</a>'; }
elseif ($data == 'unprocessed') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=unprocessed">'.__('Unprocessed', 'commerce-manager').'</a>'; }
elseif ($data == 'refunded') { $table_td = '<a style="color: #c00000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=refunded">'.__('Refunded', 'commerce-manager').'</a>'; }
else { $table_td = $data; } break;
case 'website_name': $table_td = ($item->website_url == '' ? $item->website_name : '<a href="'.$item->website_url.'">'.($item->website_name == '' ? str_replace('http://'.$_SERVER['SERVER_NAME'], '', $item->website_url) : $item->website_name).'</a>'); break;
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
case 'orders': $singular = __('order', 'commerce-manager'); $plural = __('orders', 'commerce-manager'); break;
case 'products': $singular = __('product', 'commerce-manager'); $plural = __('products', 'commerce-manager'); break;
default: $singular = __('item', 'commerce-manager'); $plural = __('items', 'commerce-manager'); }
if ($_GET['paged'] == 1) { $prev_paged = 1; } else { $prev_paged = $_GET['paged'] - 1; }
if ($_GET['paged'] == $max_paged) { $next_paged = $max_paged; } else { $next_paged = $_GET['paged'] + 1; }
$url = 'admin.php?page='.$_GET['page'].'&amp;orderby='.$_GET['orderby'].'&amp;order='.$_GET['order'].$_GET['selection_criteria'].($_GET['s'] == '' ? '' : '&amp;s='.$_GET['s']);
echo '<div class="tablenav-pages" style="float: right;"><span class="displaying-num">'.$n.' '.($n <= 1 ? $singular : $plural).'</span>
<a class="first-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the first page').'" href="'.$url.'&amp;paged=1">&laquo;</a>
<a class="prev-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the previous page').'" href="'.$url.'&amp;paged='.$prev_paged.'">&lsaquo;</a>
<span class="paging-input">'.($location == 'top' ? '<input class="current-page" title="'.__('Current page').'" type="text" name="paged" id="paged" value="'.$_GET['paged'].'" size="2" />' : $_GET['paged']).' '.__('of').' <span class="total-pages">'.$max_paged.'</span></span>
<a class="next-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the next page').'" href="'.$url.'&amp;paged='.$next_paged.'">&rsaquo;</a>
<a class="last-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the last page').'" href="'.$url.'&amp;paged='.$max_paged.'">&raquo;</a></div>'; }


remove_shortcode('commerce-manager');
remove_shortcode('customer');
remove_shortcode('order');
remove_shortcode('product');