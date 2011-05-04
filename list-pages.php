<?php $options = get_option(str_replace('-', '_', $_GET['page'])); $max_columns = count($_GET['columns_names']);

function table_td($column, $item) {
$item->$column = htmlspecialchars(do_shortcode($item->$column));
switch ($column) {
case 'affiliation_enabled': case 'customers_subsribed_to_aweber_list': case 'downloadable': case 'email_sent_to_customer': case 'email_sent_to_seller': case 'registration_required':
if ($item->$column == 'yes') { $table_td = '<span style="color: #008000;">'.__('Yes', 'commerce-manager').'</span>'; }
else { $table_td = '<span style="color: #c00000;">'.__('No', 'commerce-manager').'</span>'; } break;
case 'available_quantity': if ($item->$column == 'infinite') { $table_td = __('Infinite', 'commerce-manager'); } else { $table_td = $item->$column; } break;
case 'commission_payment': if ($item->$column == 'deferred') { $table_td = '<a href="admin.php?page='.$_GET['page'].'&amp;commission_payment=deferred">'.__('Deferred', 'commerce-manager').'</a>'; }
elseif ($item->$column == 'instant') { $table_td = '<a href="admin.php?page='.$_GET['page'].'&amp;commission_payment=instant">'.__('Instant', 'commerce-manager').'</a>'; } break;
case 'commission_status': if ($item->$column == 'paid') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].'&amp;commission_status=paid">'.__('Paid', 'commerce-manager').'</a>'; }
elseif ($item->$column == 'unpaid') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].'&amp;commission_status=unpaid">'.__('Unpaid', 'commerce-manager').'</a>'; } break;
case 'commission_type': if ($item->$column == 'fixed') { $table_td = '<a href="admin.php?page='.$_GET['page'].'&amp;commission_type=fixed">'.__('Fixed', 'commerce-manager').'</a>'; }
elseif ($item->$column == 'proportional') { $table_td = '<a href="admin.php?page='.$_GET['page'].'&amp;commission_type=proportional">'.__('Proportional', 'commerce-manager').'</a>'; } break;
case 'description': case 'email_to_customer_body': case 'email_to_seller_body': case 'instructions': if (strlen($item->$column) <= 80) { $table_td = $item->$column; }
else { $table_td = substr($item->$column, 0, 80); if (stristr($table_td, ' ')) { while (substr($table_td, -1) != ' ') { $table_td = substr($table_td, 0, -1); } } $table_td .= '[...]'; } break;
case 'download_url': case 'order_confirmation_url': case 'purchase_button_url': case 'referring_url': case 'thumbnail_url': case 'url': case 'website_url': $table_td = ($item->$column == '' ? '' : '<a href="'.$item->$column.'">'.$item->$column.'</a>'); break;
case 'email_address': case 'paypal_email_address': $table_td = '<a href="mailto:'.$item->$column.'">'.$item->$column.'</a>'; break;
case 'product_id': $table_td = '<a href="admin.php?page='.$_GET['page'].'&amp;product_id='.$item->$column.'">'.$item->$column.'</a>'; break;
case 'referrer': if (strstr($item->$column, '@')) { $table_td = '<a href="mailto:'.$item->$column.'">'.$item->$column.'</a>'; }
else { $table_td = ($item->$column == '' ? '' : '<a href="admin.php?page=affiliation-manager-statistics&amp;affiliate_login='.$item->$column.'">'.$item->$column.'</a>'); } break;
case 'status': if ($item->$column == 'processed') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].'&amp;status=processed">'.__('Processed', 'commerce-manager').'</a>'; }
elseif ($item->$column == 'unprocessed') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].'&amp;status=unprocessed">'.__('Unprocessed', 'commerce-manager').'</a>'; }
elseif ($item->$column == 'refunded') { $table_td = '<a href="admin.php?page='.$_GET['page'].'&amp;status=refunded">'.__('Refunded', 'commerce-manager').'</a>'; } break;
case 'website_name': $table_td = ($item->website_url == '' ? $item->website_name : '<a href="'.$item->website_url.'">'.($item->website_name == '' ? $item->website_url : $item->website_name).'</a>'); break;
default: $table_td = $item->$column; }
return $table_td; }

function table_th($column) {
$columns_names = $_GET['columns_names'];
$columns_widths = $_GET['columns_widths'];
return '<th scope="col" class="manage-column '.($_GET['orderby'] == $column ? 'sorted '.$_GET['order'] : 'sortable desc').'" style="width: '.$columns_widths[$column].'%;">
<a href="admin.php?page='.$_GET['page'].'&amp;orderby='.$column.'&amp;order='.(($_GET['orderby'] == $column && $_GET['order'] == 'asc') ? 'desc' : 'asc').($_GET['product_id'] == '' ? '' : '&amp;product_id='.$_GET['product_id']).($_GET['status'] == '' ? '' : '&amp;status='.$_GET['status']).($_GET['commission_payment'] == '' ? '' : '&amp;commission_payment='.$_GET['commission_payment']).($_GET['commission_status'] == '' ? '' : '&amp;commission_status='.$_GET['commission_status']).($_GET['commission_type'] == '' ? '' : '&amp;commission_type='.$_GET['commission_type']).($_GET['s'] == '' ? '' : '&amp;s='.$_GET['s']).'">
<span>'.$columns_names[$column].'</span><span class="sorting-indicator"></span></a></th>'; }

function tablenav_pages($n, $max_paged, $location) {
if ($_GET['paged'] == 1) { $prev_paged = 1; } else { $prev_paged = $_GET['paged'] - 1; }
if ($_GET['paged'] == $max_paged) { $next_paged = $max_paged; } else { $next_paged = $_GET['paged'] + 1; }
$url = 'admin.php?page='.$_GET['page'].'&amp;orderby='.$_GET['orderby'].'&amp;order='.$_GET['order'].($_GET['product_id'] == '' ? '' : '&amp;product_id='.$_GET['product_id']).($_GET['status'] == '' ? '' : '&amp;status='.$_GET['status']).($_GET['commission_payment'] == '' ? '' : '&amp;commission_payment='.$_GET['commission_payment']).($_GET['commission_status'] == '' ? '' : '&amp;commission_status='.$_GET['commission_status']).($_GET['commission_type'] == '' ? '' : '&amp;commission_type='.$_GET['commission_type']).($_GET['s'] == '' ? '' : '&amp;s='.$_GET['s']);
echo '<div class="tablenav-pages"><span class="displaying-num">'.$n.' '.($n <= 1 ? $_GET['singular'] : $_GET['plural']).'</span>
<a class="first-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the first page').'" href="'.$url.'&amp;paged=1">&laquo;</a>
<a class="prev-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the previous page').'" href="'.$url.'&amp;paged='.$prev_paged.'">&lsaquo;</a>
<span class="paging-input">'.($location == 'top' ? '<input class="current-page" title="'.__('Current page').'" type="text" name="paged" id="paged" value="'.$_GET['paged'].'" size="2" />' : $_GET['paged']).' '.__('of').' <span class="total-pages">'.$max_paged.'</span></span>
<a class="next-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the next page').'" href="'.$url.'&amp;paged='.$next_paged.'">&rsaquo;</a>
<a class="last-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the last page').'" href="'.$url.'&amp;paged='.$max_paged.'">&raquo;</a></div>
<div class="clear"></div>'; }


if ($searchby_options[$_GET['orderby']] == '') { $_GET['orderby'] = $options['orderby']; }
switch ($_GET['order']) { case 'asc': case 'desc': break; default: $_GET['order'] = $options['order']; }

if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
$_GET['s'] = $_POST['s'];
for ($i = 0; $i < $max_columns; $i++) { $columns[$i] = $_POST['column'.$i]; }
$columns_number = (int) $_POST['columns_number'];
if ($columns_number > $max_columns) { $columns_number = $max_columns; }
elseif ($columns_number < 1) { $columns_number = $options['columns_number']; }
$limit = (int) $_POST['limit'];
if ($limit > 1000) { $limit = 1000; }
elseif ($limit < 1) { $limit = $options['limit']; }
$searchby = $_POST['searchby']; }
else {
$columns = $options['columns'];
$columns_number = $options['columns_number'];
$limit = $options['limit'];
$searchby = $options['searchby']; }

$options = array(
'columns' => $columns,
'columns_number' => $columns_number,
'limit' => $limit,
'order' => $_GET['order'],
'orderby' => $_GET['orderby'],
'searchby' => $searchby);
update_option(str_replace('-', '_', $_GET['page']), $options);

if ($_GET['s'] != '') {
if ($searchby == '') {
foreach ($searchby_options as $key => $value) { $search_criteria .= " OR ".$key." LIKE '%".$_GET['s']."%'"; }
$search_criteria = substr($search_criteria, 4); }
else {
$search_column = true; for ($i = 0; $i < $columns_number; $i++) { if ($searchby == $columns[$i]) { $search_column = false; } }
$search_criteria = $searchby." LIKE '%".$_GET['s']."%'"; }
if ($selection_criteria == '') { $search_criteria = 'WHERE '.$search_criteria; }
else { $search_criteria = 'AND ('.$search_criteria.')'; } }

$query = $wpdb->get_row("SELECT count(*) as total FROM $table_name $selection_criteria $search_criteria", OBJECT);
$n = (int) $query->total;
$_GET['paged'] = (int) $_REQUEST['paged'];
if ($_GET['paged'] < 1) { $_GET['paged'] = 1; }
$max_paged = ceil($n/$limit); if ($max_paged < 1) { $max_paged = 1; }
if ($_GET['paged'] > $max_paged) { $_GET['paged'] = $max_paged; }
$start = ($_GET['paged'] - 1)*$limit;
$items = $wpdb->get_results("SELECT * FROM $table_name $selection_criteria $search_criteria ORDER BY ".$_GET['orderby']." ".strtoupper($_GET['order'])." LIMIT $start, $limit", OBJECT); ?>

<div class="wrap">
<div id="poststuff">
<?php commerce_manager_pages_top(); ?>
<form method="post" action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<p class="search-box"><?php _e('Search by', 'commerce-manager'); ?> <select name="searchby" id="searchby">
<?php echo '<option value=""'.($searchby == '' ? ' selected="selected"' : '').'>'.__('all fields', 'commerce-manager').'</option>'; ?>
<?php foreach ($searchby_options as $key => $value) {
echo '<option value="'.$key.'"'.($searchby == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select><br />
<label class="screen-reader-text" for="s"><?php _e('Search', 'commerce-manager'); ?></label>
<input type="text" name="s" id="s" value="<?php echo $_GET['s']; ?>" />
<input type="submit" class="button" name="submit" id="search-submit" value="<?php _e('Search', 'commerce-manager'); ?>" /></p>
<?php commerce_manager_pages_menu(); ?>
<div class="tablenav top">
<div class="alignleft actions">
<?php _e('Display', 'commerce-manager'); ?> <input style="text-align: center;" type="text" name="limit" id="limit" size="2" value="<?php echo $limit; ?>" /> 
<?php _e('results per page', 'commerce-manager'); ?> <input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" />
</div><?php tablenav_pages($n, $max_paged, 'top'); ?></div>
<table class="wp-list-table widefat fixed">
<?php if ($search_column) { $search_table_th = table_th($searchby); }
for ($i = 0; $i < $columns_number; $i++) { $table_ths .= table_th($columns[$i]); } ?>
<thead><tr><?php echo $search_table_th.$table_ths; ?></tr></thead>
<tfoot><tr><?php echo $search_table_th.$table_ths; ?></tr></tfoot>
<tbody id="the-list">
<?php if ($items) { foreach ($items as $item) {
if ($search_column) { $search_table_td = '<td>'.table_td($searchby, $item).'</td>'; }
if ($_GET['page'] == 'commerce-manager-orders') {
$row_actions = '<div class="row-actions" style="margin-top: 2em; position: absolute; width: 1000%;"><span class="edit">
<a href="admin.php?page=commerce-manager-order&amp;id='.$item->id.'">'.__('Edit').'</a></span> | <span class="delete">
<a href="admin.php?page=commerce-manager-order&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span></div>'; }
elseif ($_GET['page'] == 'commerce-manager-products') {
$row_actions = '<div class="row-actions" style="margin-top: 2em; position: absolute; width: 1000%;"><span class="edit">
<a href="admin.php?page=commerce-manager-product&amp;id='.$item->id.'">'.__('Edit').'</a></span> | <span class="delete">
<a href="admin.php?page=commerce-manager-product&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span> | <span class="view">
<a href="admin.php?page=commerce-manager-statistics&amp;product_id='.$item->id.'">'.__('Statistics', 'commerce-manager').'</a></span></div>'; }
for ($i = 1; $i < $columns_number; $i++) { $table_tds .= '<td>'.table_td($columns[$i], $item).'</td>'; }
echo '<tr'.($boolean ? '' : ' class="alternate"').'>'.$search_table_td.'<td style="height: 6em;">'.table_td($columns[0], $item).$row_actions.'</td>'.$table_tds.'</tr>';
$table_tds = ''; $boolean = !$boolean; } }
else { echo '<tr class="no-items"><td class="colspanchange" colspan="'.$columns_number.'">'.$no_items.'</td></tr>'; } ?>
</tbody>
</table>
<div class="tablenav bottom">
<div class="alignleft actions">
<?php _e('Display', 'commerce-manager'); ?> <input style="text-align: center;" type="text" name="columns_number" id="columns_number" size="2" value="<?php echo $columns_number; ?>" /> 
<?php _e('columns', 'commerce-manager'); ?> <input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /><br />
<?php for ($i = 0; $i < $max_columns; $i++) { $j = $i + 1;
echo '<label for="column'.$i.'">'.__('Column', 'commerce-manager').' '.$j.'</label> <select'.($j < 10 ? ' style="margin-left: 0.75em;"': '').' name="column'.$i.'" id="column'.$i.'">';
foreach ($_GET['columns_names'] as $key => $value) { echo '<option value="'.$key.'"'.($columns[$i] == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; }
echo '</select><br />'; } ?> 
</div><?php tablenav_pages($n, $max_paged, 'bottom'); ?></div>
</form>
</div>
</div>