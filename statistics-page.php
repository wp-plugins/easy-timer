<?php if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

global $wpdb;
include_once 'tables/functions.php';
add_action('admin_footer', 'commerce_statistics_form_js');
$orders_table_name = $wpdb->prefix.'commerce_manager_orders';
$products_table_name = $wpdb->prefix.'commerce_manager_products';
$options = get_option('commerce_manager_statistics');
$commerce_manager_options = get_option('commerce_manager');
$currency_code = commerce_data('currency_code');

$tables_names = array(
'orders' => __('Orders', 'commerce-manager'),
'products' => __('Products', 'commerce-manager'));
$max_tables = count($tables_names);

$filterby_options = array(
'referrer' => __('referrer', 'commerce-manager'),
'product_id' => __('product ID', 'commerce-manager'),
'first_name' => __('first name', 'commerce-manager'),
'last_name' => __('last name', 'commerce-manager'),
'email_address' => __('email address', 'commerce-manager'),
'paypal_email_address' => __('email address (PayPal)', 'commerce-manager'),
'website_name' => __('website', 'commerce-manager'),
'website_url' => __('website URL', 'commerce-manager'),
'address' => __('address', 'commerce-manager'),
'postcode' => __('postcode', 'commerce-manager'),
'town' => __('town', 'commerce-manager'),
'country' => __('country', 'commerce-manager'),
'phone_number' => __('phone number', 'commerce-manager'),
'quantity' => __('quantity', 'commerce-manager'),
'price' => __('price', 'commerce-manager'),
'tax' => __('tax', 'commerce-manager'),
'tax_percentage' => __('tax percentage', 'commerce-manager'),
'shipping_cost' => __('shipping cost', 'commerce-manager'),
'amount' => __('order amount', 'commerce-manager'),
'payment_mode' => __('payment mode', 'commerce-manager'),
'commission_type' => __('commission type', 'commerce-manager'),
'commission_amount' => __('commission amount', 'commerce-manager'),
'commission_percentage' => __('commission percentage', 'commerce-manager'),
'user_agent' => __('user agent', 'commerce-manager'),
'ip_address' => __('IP address', 'commerce-manager'),
'referring_url' => __('referring URL', 'commerce-manager'));

if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
$_POST = array_map('stripslashes', $_POST);
$_GET['s'] = $_POST['s'];
$filterby = $_POST['filterby'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
for ($i = 0; $i < $max_tables; $i++) { $tables[$i] = $_POST['table'.$i]; }
$tables_number = (int) $_POST['tables_number'];
if ($tables_number > $max_tables) { $tables_number = $max_tables; }
elseif ($tables_number < 1) { $tables_number = 0; } }
else {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$end_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$filterby = $options['filterby'];
$start_date = $options['start_date'];
$tables = $options['tables'];
$tables_number = $options['tables_number']; }

$start_date = trim(mysql_real_escape_string(strip_tags($start_date)));
if (strlen($start_date) == 10) { $start_date .= ' 00:00:00'; }
$end_date = trim(mysql_real_escape_string(strip_tags($end_date)));
if (strlen($end_date) == 10) { $end_date .= ' 23:59:59'; }

$options = array(
'filterby' => $filterby,
'start_date' => $start_date,
'tables' => $tables,
'tables_number' => $tables_number);
update_option('commerce_manager_statistics', $options);

if ($_GET['s'] != '') { $filter_criteria = "AND (".$filterby."='".$_GET['s']."')"; }

$row = $wpdb->get_row("SELECT count(*) as total FROM $orders_table_name WHERE (date BETWEEN '$start_date' AND '$end_date') ".$_GET['selection_criteria']." $filter_criteria", OBJECT);
$orders_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(amount) AS total FROM $orders_table_name WHERE (date BETWEEN '$start_date' AND '$end_date') ".$_GET['selection_criteria']." $filter_criteria", OBJECT);
$orders_total_amount = (double) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM $orders_table_name WHERE status = 'processed' AND (date BETWEEN '$start_date' AND '$end_date') ".$_GET['selection_criteria']." $filter_criteria", OBJECT);
$processed_orders_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(amount) AS total FROM $orders_table_name WHERE status = 'processed' AND (date BETWEEN '$start_date' AND '$end_date') ".$_GET['selection_criteria']." $filter_criteria", OBJECT);
$processed_orders_total_amount = (double) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM $orders_table_name WHERE status = 'unprocessed' AND (date BETWEEN '$start_date' AND '$end_date') ".$_GET['selection_criteria']." $filter_criteria", OBJECT);
$unprocessed_orders_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(amount) AS total FROM $orders_table_name WHERE status = 'unprocessed' AND (date BETWEEN '$start_date' AND '$end_date') ".$_GET['selection_criteria']." $filter_criteria", OBJECT);
$unprocessed_orders_total_amount = (double) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM $orders_table_name WHERE status = 'refunded' AND (date BETWEEN '$start_date' AND '$end_date') ".$_GET['selection_criteria']." $filter_criteria", OBJECT);
$refunded_orders_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(amount) AS total FROM $orders_table_name WHERE status = 'refunded' AND (date BETWEEN '$start_date' AND '$end_date') ".$_GET['selection_criteria']." $filter_criteria", OBJECT);
$refunded_orders_total_amount = (double) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM $products_table_name WHERE (date BETWEEN '$start_date' AND '$end_date') ".$_GET['selection_criteria']." $filter_criteria", OBJECT);
$products_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(quantity) AS total FROM $orders_table_name WHERE (date BETWEEN '$start_date' AND '$end_date') ".$_GET['selection_criteria']." $filter_criteria", OBJECT);
$sold_items_number = (int) $row->total;

$orders_a_tag = '<a style="text-decoration: none;" href="admin.php?page=commerce-manager-orders">';
$processed_orders_a_tag = '<a style="color: #008000; text-decoration: none;" href="admin.php?page=commerce-manager-orders&amp;status=processed">';
$unprocessed_orders_a_tag = '<a style="color: #e08000; text-decoration: none;" href="admin.php?page=commerce-manager-orders&amp;status=unprocessed">';
$refunded_orders_a_tag = '<a style="color: #c00000; text-decoration: none;" href="admin.php?page=commerce-manager-orders&amp;status=refunded">';
$products_a_tag = '<a style="text-decoration: none;" href="admin.php?page=commerce-manager-products">'; ?>

<div class="wrap">
<div id="poststuff">
<?php commerce_manager_pages_top(); ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php commerce_manager_pages_menu(); ?>
<p class="search-box" style="float: right;"><?php _e('Filter by', 'commerce-manager'); ?> <select name="filterby" id="filterby">
<?php foreach ($filterby_options as $key => $value) {
echo '<option value="'.$key.'"'.($filterby == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select><br />
<label class="screen-reader-text" for="s"><?php _e('Filter', 'commerce-manager'); ?></label>
<input type="text" name="s" id="s" value="<?php echo $_GET['s']; ?>" />
<input type="submit" class="button" name="submit" id="filter-submit" value="<?php _e('Filter', 'commerce-manager'); ?>" /></p>
<div class="clear"></div>
<p style="margin: 0 0 1em 0;"><label for="start_date"><strong><?php _e('Start', 'commerce-manager'); ?>:</strong></label>
<input class="date-pick" style="margin: 0.5em;" type="text" name="start_date" id="start_date" size="10" value="<?php echo $start_date; ?>" />
<label style="margin-left: 3em;" for="end_date"><strong><?php _e('End', 'commerce-manager'); ?>:</strong></label>
<input class="date-pick" style="margin: 0.5em;" type="text" name="end_date" id="end_date" size="10" value="<?php echo $end_date; ?>" />
<input style="margin-left: 3em;" type="submit" class="button-secondary" name="submit" value="<?php _e('Display', 'commerce-manager'); ?>" /></p>
<?php $global_table_ths = '
<th scope="col" class="manage-column" style="width: 30%;">'.__('Data', 'commerce-manager').'</th>
<th scope="col" class="manage-column" style="width: 20%;">'.__('Quantity', 'commerce-manager').'</th>
<th scope="col" class="manage-column" style="width: 30%;">'.__('Percentage of orders', 'commerce-manager').'</th>
<th scope="col" class="manage-column" style="width: 20%;">'.__('Total amount', 'commerce-manager').'</th>';
echo '
<h3 id="global-statistics"><strong>'.__('Global statistics', 'commerce-manager').'</strong></h3>
<table class="wp-list-table widefat fixed" style="margin: 1em 0 2em 0;">
<thead><tr>'.$global_table_ths.'</tr></thead>
<tfoot><tr>'.$global_table_ths.'</tr></tfoot>
<tbody>
<tr class="alternate">
<td><strong>'.__('Orders', 'commerce-manager').'</strong></td>
<td>'.$orders_a_tag.$orders_number.'</a></td>
<td>'.$orders_a_tag.($orders_number == 0 ? '--' : '100 %').'</a></td>
<td>'.$orders_a_tag.$orders_total_amount.' '.$currency_code.'</a></td>
</tr><tr>
<td><strong>'.__('Processed and unrefunded orders', 'commerce-manager').'</strong></td>
<td>'.$processed_orders_a_tag.$processed_orders_number.'</a></td>
<td>'.($orders_number == 0 ? '--' : $processed_orders_a_tag.((round(10000*$processed_orders_number/$orders_number))/100).' %</a>').'</td>
<td>'.$processed_orders_a_tag.$processed_orders_total_amount.' '.$currency_code.'</a></td>
</tr><tr class="alternate">
<td><strong>'.__('Unprocessed orders', 'commerce-manager').'</strong></td>
<td>'.$unprocessed_orders_a_tag.$unprocessed_orders_number.'</a></td>
<td>'.($orders_number == 0 ? '--' : $unprocessed_orders_a_tag.((round(10000*$unprocessed_orders_number/$orders_number))/100).' %</a>').'</td>
<td>'.$unprocessed_orders_a_tag.$unprocessed_orders_total_amount.' '.$currency_code.'</a></td>
</tr><tr>
<td><strong>'.__('Refunded orders', 'commerce-manager').'</strong></td>
<td>'.$refunded_orders_a_tag.$refunded_orders_number.'</a></td>
<td>'.($orders_number == 0 ? '--' : $refunded_orders_a_tag.((round(10000*$refunded_orders_number/$orders_number))/100).' %</a>').'</td>
<td>'.$refunded_orders_a_tag.$refunded_orders_total_amount.' '.$currency_code.'</a></td>
</tr><tr class="alternate">
<td><strong>'.__('Products', 'commerce-manager').'</strong></td>
<td>'.$products_a_tag.$products_number.'</a></td>
<td>--</td>
<td>--</td>
</tr><tr>
<td><strong>'.__('Sold items', 'commerce-manager').'</strong></td>
<td>'.$products_a_tag.$sold_items_number.'</a></td>
<td>--</td>
<td>--</td>
</tr>
</tbody></table>';
if ($tables_number > 1) {
for ($i = 1; $i < $tables_number; $i++) { $summary .= '<li>| <a href="#'.$tables[$i].'">'.$tables_names[$tables[$i]].'</a></li>'; }
$summary = '<ul class="subsubsub" style="float: none; text-align: center;">
<li><a href="#'.$tables[0].'">'.$tables_names[$tables[0]].'</a></li>
'.$summary.'</ul>'; }
for ($i = 0; $i < $tables_number; $i++) {
include 'tables/'.$tables[$i].'.php';
$options = get_option($option_name);
$columns = $options['columns'];
$columns_number = $options['columns_number'];
for ($j = 0; $j < $columns_number; $j++) { $table_ths .= table_th($columns[$j]); }
echo $summary.'
<h3 id="'.$tables[$i].'"><strong>'.$tables_names[$tables[$i]].'</strong></h3>
<table class="wp-list-table widefat fixed" style="margin: 1em 0 2em 0;">
<thead><tr>'.$table_ths.'</tr></thead>
<tfoot><tr>'.$table_ths.'</tr></tfoot>
<tbody>';
$items = $wpdb->get_results("SELECT * FROM $table_name WHERE (date BETWEEN '$start_date' AND '$end_date') $table_criteria ".$_GET['selection_criteria']." $filter_criteria ORDER BY date DESC", OBJECT);
if ($items) { foreach ($items as $item) {
switch ($tables[$i]) {
case 'orders': $row_actions = '<div class="row-actions" style="margin-top: 2em; position: absolute; width: 1000%;"><span class="edit">
<a href="admin.php?page=commerce-manager-order&amp;id='.$item->id.'">'.__('Edit').'</a></span> | <span class="delete">
<a href="admin.php?page=commerce-manager-order&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span></div>'; break;
case 'products': $row_actions = '<div class="row-actions" style="margin-top: 2em; position: absolute; width: 1000%;"><span class="edit">
<a href="admin.php?page=commerce-manager-product&amp;id='.$item->id.'">'.__('Edit').'</a></span> | <span class="delete">
<a href="admin.php?page=commerce-manager-product&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span> | <span class="view">
<a href="admin.php?page=commerce-manager-statistics&amp;product_id='.$item->id.'">'.__('Statistics', 'commerce-manager').'</a></span></div>'; }
for ($j = 1; $j < $columns_number; $j++) { $table_tds .= '<td>'.table_td($columns[$j], $item).'</td>'; }
echo '<tr'.($boolean ? '' : ' class="alternate"').'><td style="height: 6em;">'.table_td($columns[0], $item).$row_actions.'</td>'.$table_tds.'</tr>';
$table_tds = ''; $boolean = !$boolean; } }
else { echo '<tr class="no-items"><td class="colspanchange" colspan="'.$columns_number.'">'.$no_items.'</td></tr>'; }
echo '</tbody></table>';
$table_ths = ''; $table_criteria = ''; } ?>
<div style="text-align: center;">
<?php _e('Display', 'commerce-manager'); ?> <input style="text-align: center;" type="text" name="tables_number" id="tables_number" size="2" value="<?php echo $tables_number; ?>" /> 
<?php _e('tables', 'commerce-manager'); ?> <input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /><br />
<?php for ($i = 0; $i < $max_tables; $i++) { $j = $i + 1;
echo '<label for="table'.$i.'">'.__('Table', 'commerce-manager').' '.$j.'</label> <select'.($j < 10 ? ' style="margin-left: 0.75em;"': '').' name="table'.$i.'" id="table'.$i.'">';
foreach ($tables_names as $key => $value) { echo '<option value="'.$key.'"'.($tables[$i] == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; }
echo '</select><br />'; } ?>
</div>
</form>
</div>
</div>