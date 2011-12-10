<?php global $wpdb;

foreach (array(
'autoresponder',
'autoresponder_list',
'category_id',
'commission_status',
'commission2_status',
'form_id',
'ip_address',
'referrer',
'referrer2',
'status') as $field) {
if (isset($_GET[$field])) {
$_GET['selection_criteria'] .= '&amp;'.$field.'='.$_GET[$field];
$selection_criteria .= " AND ".$field." = '".$_GET[$field]."'"; } }


function no_items($table) {
switch ($table) {
case 'forms': $no_items = __('No forms', 'optin-manager'); break;
case 'forms_categories': case 'prospects_categories': $no_items = __('No categories', 'optin-manager'); break;
case 'prospects': $no_items = __('No prospects', 'optin-manager'); }
return $no_items; }


function row_actions($table, $item) {
global $wpdb;
switch ($table) {
case 'forms': $row_actions = 
'<span class="edit"><a href="admin.php?page=optin-manager-form&amp;id='.$item->id.'">'.__('Edit').'</a></span>
 | <span class="delete"><a href="admin.php?page=optin-manager-form&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span>'; break;
case 'forms_categories':
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."optin_manager_forms WHERE category_id = ".$item->id, OBJECT);
$forms_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."optin_manager_forms_categories WHERE category_id = ".$item->id, OBJECT);
$categories_number = (int) $row->total;
$row_actions = 
'<span class="edit"><a href="admin.php?page=optin-manager-form-category&amp;id='.$item->id.'">'.__('Edit').'</a></span>
 | <span class="delete"><a href="admin.php?page=optin-manager-form-category&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span>'
.($forms_number == 0 ? '' : ' | <span class="view"><a href="admin.php?page=optin-manager-forms&amp;category_id='.$item->id.'">'.__('Forms', 'optin-manager').'</a></span>')
.($categories_number == 0 ? '' : ' | <span class="view"><a href="admin.php?page=optin-manager-forms-categories&amp;category_id='.$item->id.'">'.__('Subcategories', 'optin-manager').'</a></span>'); break;
case 'prospects': $row_actions = 
'<span class="edit"><a href="admin.php?page=optin-manager-prospect&amp;id='.$item->id.'">'.__('Edit').'</a></span>
 | <span class="delete"><a href="admin.php?page=optin-manager-prospect&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span>'; break; }
return '<div class="row-actions" style="margin-top: 2em; position: absolute;">'.$row_actions.'</div>'; }


function table_name($table) {
global $wpdb;
return $wpdb->prefix.'optin_manager_'.$table; }


function table_td($table, $column, $item) {
switch ($table) {
case 'forms': $_GET['optin_form_id'] = $item->id; $_GET['optin_form_data'] = $item; $data = optin_form_data($column); break;
case 'forms_categories': $_GET['optin_form_category_id'] = $item->id; $_GET['optin_form_category_data'] = $item; $data = optin_form_category_data($column); break;
case 'prospects': $_GET['prospect_data'] = $item; $data = prospect_data($column); break;
default: $data = optin_format_data($column, $item->$column); }
$data = htmlspecialchars($data);
switch ($column) {
case 'activation_confirmation_email_body': case 'activation_notification_email_body': case 'code': case 'description': case 'gift_instructions':
case 'activation_confirmation_email_sent': case 'activation_notification_email_sent': case 'affiliation_enabled': case 'commission2_enabled': 
case 'prospect_subscribed_to_members_areas': case 'registration_confirmation_email_sent': case 'registration_custom_instructions_executed':
case 'registration_notification_email_sent':
if ($data == 'yes') { $table_td = '<span style="color: #008000;">'.__('Yes', 'optin-manager').'</span>'; }
elseif ($data == 'no')  { $table_td = '<span style="color: #c00000;">'.__('No', 'optin-manager').'</span>'; }
else { $table_td = $data; } break;
case 'registration_confirmation_email_body': case 'registration_custom_instructions': case 'registration_notification_email_body': if (strlen($data) <= 80) { $table_td = $data; }
else { $table_td = substr($data, 0, 80); if (stristr($table_td, ' ')) { while (substr($table_td, -1) != ' ') { $table_td = substr($table_td, 0, -1); } } $table_td .= '[...]'; } break;
case 'autoresponder': case 'autoresponder_list': case 'category_id': case 'form_id': case 'ip_address': case 'referrer': case 'referrer2': $table_td = ($data == '' ? '' : '<a href="admin.php?page='.$_GET['page'].'&amp;'.$column.'='.$data.'">'.$data.'</a>'); break;
case 'commission_status': case 'commission2_status': if ($data == 'paid') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=paid">'.__('Paid', 'optin-manager').'</a>'; }
elseif ($data == 'unpaid') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=unpaid">'.__('Unpaid', 'optin-manager').'</a>'; }
else { $table_td = $data; } break;
case 'email_address': $table_td = '<a href="mailto:'.$data.'">'.$data.'</a>'; break;
case 'gift_download_url': case 'referring_url': case 'registration_confirmation_url': case 'url': case 'website_url': $table_td = ($data == '' ? '' : '<a href="'.$data.'">'.($data == 'http://'.$_SERVER['SERVER_NAME'] ? '/' : str_replace('http://'.$_SERVER['SERVER_NAME'], '', $data)).'</a>'); break;
case 'prospects_initial_status': if ($data == 'active') { $table_td = '<span style="color: #008000;">'.__('Active', 'optin-manager').'</span>'; }
elseif ($data == 'inactive') { $table_td = '<span style="color: #e08000;">'.__('Inactive', 'optin-manager').'</span>'; }
elseif ($data == 'unsubscribed') { $table_td = '<span style="color: #c00000;">'.__('Unsubscribed', 'optin-manager').'</span>'; }
else { $table_td = $data; } break;
case 'prospect_members_areas':
$members_areas = array_unique(preg_split('#[^0-9]#', $data, 0, PREG_SPLIT_NO_EMPTY));
foreach ($members_areas as $member_area) {
if ((function_exists('membership_manager_admin_menu')) && ($member_area > 0)) { $member_area = '<a href="admin.php?page=membership-manager-member-area&amp;id='.$member_area.'">'.$member_area.'</a>'; }
$members_areas_list .= $member_area.', '; }
$table_td = substr($members_areas_list, 0, -2); break;
case 'status': if ($data == 'active') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=active">'.__('Active', 'optin-manager').'</a>'; }
elseif ($data == 'inactive') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=inactive">'.__('Inactive', 'optin-manager').'</a>'; }
elseif ($data == 'unsubscribed') { $table_td = '<a style="color: #c00000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=unsubscribed">'.__('Unsubscribed', 'optin-manager').'</a>'; }
else { $table_td = $data; } break;
case 'website_name': $website_url = htmlspecialchars(optin_format_data($column, $item->website_url)); $table_td = ($website_url == '' ? $data : '<a href="'.$website_url.'">'.($data == '' ? str_replace('http://'.$_SERVER['SERVER_NAME'], '', $website_url) : $data).'</a>'); break;
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
case 'forms': $singular = __('form', 'optin-manager'); $plural = __('forms', 'optin-manager'); break;
case 'forms_categories': $singular = __('category', 'optin-manager'); $plural = __('categories', 'optin-manager'); break;
case 'prospects': $singular = __('prospect', 'optin-manager'); $plural = __('prospects', 'optin-manager'); break;
default: $singular = __('item', 'optin-manager'); $plural = __('items', 'optin-manager'); }
if ($_GET['paged'] == 1) { $prev_paged = 1; } else { $prev_paged = $_GET['paged'] - 1; }
if ($_GET['paged'] == $max_paged) { $next_paged = $max_paged; } else { $next_paged = $_GET['paged'] + 1; }
$url = 'admin.php?page='.$_GET['page'].'&amp;end_date='.$end_date.'&amp;orderby='.$_GET['orderby'].'&amp;order='.$_GET['order'].$_GET['selection_criteria'].($_GET['s'] == '' ? '' : '&amp;s='.$_GET['s']);
echo '<div class="tablenav-pages" style="float: right;"><span class="displaying-num">'.$n.' '.($n <= 1 ? $singular : $plural).'</span>
<a class="first-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the first page').'" href="'.$url.'&amp;paged=1">&laquo;</a>
<a class="prev-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the previous page').'" href="'.$url.'&amp;paged='.$prev_paged.'">&lsaquo;</a>
<span class="paging-input">'.($location == 'top' ? '<input class="current-page" title="'.__('Current page').'" type="text" name="paged" id="paged" value="'.$_GET['paged'].'" size="2" />' : $_GET['paged']).' '.__('of').' <span class="total-pages">'.$max_paged.'</span></span>
<a class="next-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the next page').'" href="'.$url.'&amp;paged='.$next_paged.'">&rsaquo;</a>
<a class="last-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the last page').'" href="'.$url.'&amp;paged='.$max_paged.'">&raquo;</a></div>'; }


remove_shortcode('optin-form');
remove_shortcode('prospect');