<?php include 'tables.php';
include_once 'tables-functions.php';
add_action('admin_footer', 'affiliation_statistics_form_js');
$options = get_option('affiliation_manager_statistics');
if (function_exists('commerce_data')) { $currency_code = commerce_data('currency_code'); }
else { $commerce_manager_options = (array) get_option('commerce_manager');
$currency_code = do_shortcode($commerce_manager_options['currency_code']); }

$tables_names = array(
'affiliates' => __('Affiliates', 'affiliation-manager'),
'clicks' => __('Clicks', 'affiliation-manager'),
'commissions' => __('Commissions', 'affiliation-manager'));
$max_tables = count($tables_names);

$filterby_options = array(
'referrer' => __('referrer', 'affiliation-manager'),
'commission_amount' => __('commission amount', 'affiliation-manager'),
'product_id' => __('product ID', 'affiliation-manager'),
'quantity' => __('quantity', 'affiliation-manager'),
'price' => __('price', 'affiliation-manager'),
'tax' => __('tax', 'affiliation-manager'),
'shipping_cost' => __('shipping cost', 'affiliation-manager'),
'amount' => __('order amount', 'affiliation-manager'),
'payment_mode' => __('order\'s payment mode', 'affiliation-manager'),
'transaction_cost' => __('transaction cost', 'affiliation-manager'),
'postcode' => __('postcode', 'affiliation-manager'),
'town' => __('town', 'affiliation-manager'),
'country' => __('country', 'affiliation-manager'),
'ip_address' => __('IP address ', 'affiliation-manager'),
'user_agent' => __('user agent', 'affiliation-manager'),
'referring_url' => __('referring URL', 'affiliation-manager'));

if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
$_POST = array_map('stripslashes', $_POST);
$_GET['s'] = $_POST['s'];
$filterby = $_POST['filterby'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$start_table = (int) $_POST['start_table'] - 1;
$start_table = $start_table % $max_tables;
if ($start_table < 0) { $start_table = $start_table + $max_tables; }
for ($i = 0; $i < $max_tables; $i++) { $tables_slugs[$i] = $_POST['table'.$i]; }
$tables_number = (int) $_POST['tables_number'];
if ($tables_number > $max_tables) { $tables_number = $max_tables; }
elseif ($tables_number < 1) { $tables_number = 0; } }
else {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$end_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$filterby = $options['filterby'];
$start_date = $options['start_date'];
$start_table = $options['start_table'];
$tables_slugs = $options['tables'];
$tables_number = $options['tables_number']; }

$start_date = trim(mysql_real_escape_string(strip_tags($start_date)));
if (strlen($start_date) == 10) { $start_date .= ' 00:00:00'; }
$end_date = trim(mysql_real_escape_string(strip_tags($end_date)));
if (strlen($end_date) == 10) { $end_date .= ' 23:59:59'; }

$options = array(
'filterby' => $filterby,
'start_date' => $start_date,
'start_table' => $start_table,
'tables' => $tables_slugs,
'tables_number' => $tables_number);
update_option('affiliation_manager_statistics', $options);

for ($i = 0; $i < $max_tables; $i++) { $tables_slugs[$max_tables + $i] = $tables_slugs[$i]; }

if ($_GET['s'] != '') { $filter_criteria = "AND (".$filterby." = '".$_GET['s']."')"; }

$row = $wpdb->get_row("SELECT count(*) as total FROM $orders_table_name WHERE commission_amount > 0 AND (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$commissions_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM $orders_table_name WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$commissions_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM $orders_table_name WHERE commission_status = 'paid' AND (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$paid_commissions_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM $orders_table_name WHERE commission_status = 'paid' AND (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$paid_commissions_total_amount = round(100*$row->total)/100;
$unpaid_commissions_number = $commissions_number - $paid_commissions_number;
$unpaid_commissions_total_amount = round(100*($commissions_total_amount - $paid_commissions_total_amount))/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM $orders_table_name WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$orders_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(amount) AS total FROM $orders_table_name WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$orders_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM $affiliates_table_name WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$affiliates_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM $clicks_table_name WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$clicks_number = (int) $row->total;

$commissions_a_tag = '<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-commissions">';
$paid_commissions_a_tag = '<a style="color: #008000; text-decoration: none;" href="admin.php?page=affiliation-manager-commissions&amp;commission_status=paid">';
$unpaid_commissions_a_tag = '<a style="color: #e08000; text-decoration: none;" href="admin.php?page=affiliation-manager-commissions&amp;commission_status=unpaid">';
$orders_a_tag = '<a style="text-decoration: none;" href="admin.php?page=commerce-manager-orders">';
$affiliates_a_tag = '<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliates">';
$clicks_a_tag = '<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-clicks">'; ?>

<div class="wrap">
<div id="poststuff">
<?php affiliation_manager_pages_top(); ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php affiliation_manager_pages_menu(); ?>
<p class="search-box" style="float: right;"><?php _e('Filter by', 'affiliation-manager'); ?> <select name="filterby" id="filterby">
<?php foreach ($filterby_options as $key => $value) {
echo '<option value="'.$key.'"'.($filterby == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select><br />
<label class="screen-reader-text" for="s"><?php _e('Filter', 'affiliation-manager'); ?></label>
<input type="text" name="s" id="s" value="<?php echo $_GET['s']; ?>" />
<input type="submit" class="button" name="submit" id="filter-submit" value="<?php _e('Filter', 'affiliation-manager'); ?>" /></p>
<div class="clear"></div>
<p style="margin: 0 0 1em 0;"><label for="start_date"><strong><?php _e('Start', 'affiliation-manager'); ?>:</strong></label>
<input class="date-pick" style="margin: 0.5em;" type="text" name="start_date" id="start_date" size="10" value="<?php echo $start_date; ?>" />
<label style="margin-left: 3em;" for="end_date"><strong><?php _e('End', 'affiliation-manager'); ?>:</strong></label>
<input class="date-pick" style="margin: 0.5em;" type="text" name="end_date" id="end_date" size="10" value="<?php echo $end_date; ?>" />
<input style="margin-left: 3em;" type="submit" class="button-secondary" name="submit" value="<?php _e('Display', 'affiliation-manager'); ?>" /></p>
<?php $global_table_ths = '
<th scope="col" class="manage-column" style="width: 30%;">'.__('Data', 'affiliation-manager').'</th>
<th scope="col" class="manage-column" style="width: 20%;">'.__('Quantity', 'affiliation-manager').'</th>
<th scope="col" class="manage-column" style="width: 30%;">'.__('Percentage of commissions', 'affiliation-manager').'</th>
<th scope="col" class="manage-column" style="width: 30%;">'.__('Percentage of orders', 'affiliation-manager').'</th>
<th scope="col" class="manage-column" style="width: 20%;">'.__('Total amount', 'affiliation-manager').'</th>';
echo '
<h3 id="global-statistics"><strong>'.__('Global statistics', 'affiliation-manager').'</strong></h3>
<table class="wp-list-table widefat fixed" style="margin: 1em 0 2em 0;">
<thead><tr>'.$global_table_ths.'</tr></thead>
<tfoot><tr>'.$global_table_ths.'</tr></tfoot>
<tbody>
<tr class="alternate">
<td><strong>'.__('Commissions', 'affiliation-manager').'</strong></td>
<td>'.$commissions_a_tag.$commissions_number.'</a></td>
<td>'.$commissions_a_tag.($commissions_number == 0 ? '--' : '100 %').'</a></td>
<td>'.($orders_number == 0 ? '--' : $commissions_a_tag.((round(10000*$commissions_number/$orders_number))/100).' %</a>').'</td>
<td>'.$commissions_a_tag.$commissions_total_amount.' '.$currency_code.'</a></td>
</tr><tr>
<td><strong>'.__('Paid commissions', 'affiliation-manager').'</strong></td>
<td>'.$paid_commissions_a_tag.$paid_commissions_number.'</a></td>
<td>'.($commissions_number == 0 ? '--' : $paid_commissions_a_tag.((round(10000*$paid_commissions_number/$commissions_number))/100).' %</a>').'</td>
<td>'.($orders_number == 0 ? '--' : $paid_commissions_a_tag.((round(10000*$paid_commissions_number/$orders_number))/100).' %</a>').'</td>
<td>'.$paid_commissions_a_tag.$paid_commissions_total_amount.' '.$currency_code.'</a></td>
</tr><tr class="alternate">
<td><strong>'.__('Unpaid commissions', 'affiliation-manager').'</strong></td>
<td>'.$unpaid_commissions_a_tag.$unpaid_commissions_number.'</a></td>
<td>'.($commissions_number == 0 ? '--' : $unpaid_commissions_a_tag.((round(10000*$unpaid_commissions_number/$commissions_number))/100).' %</a>').'</td>
<td>'.($orders_number == 0 ? '--' : $unpaid_commissions_a_tag.((round(10000*$unpaid_commissions_number/$orders_number))/100).' %</a>').'</td>
<td>'.$unpaid_commissions_a_tag.$unpaid_commissions_total_amount.' '.$currency_code.'</a></td>
</tr><tr>
<td><strong>'.__('Orders', 'affiliation-manager').'</strong></td>
<td>'.$orders_a_tag.$orders_number.'</a></td>
<td>--</td>
<td>'.$orders_a_tag.($orders_number == 0 ? '--' : '100 %').'</a></td>
<td>'.$orders_a_tag.$orders_total_amount.' '.$currency_code.'</a></td>
</tr><tr class="alternate">
<td><strong>'.__('Affiliates', 'affiliation-manager').'</strong></td>
<td>'.$affiliates_a_tag.$affiliates_number.'</a></td>
<td>--</td>
<td>--</td>
<td>--</td>
</tr><tr>
<td><strong>'.__('Clicks', 'affiliation-manager').'</strong></td>
<td>'.$clicks_a_tag.$clicks_number.'</a></td>
<td>--</td>
<td>--</td>
<td>--</td>
</tr>
</tbody></table>'; ?>
<div style="text-align: center;">
<?php for ($i = 0; $i < $max_tables; $i++) {
echo '<label for="table'.$i.'">'.__('Table', 'affiliation-manager').' '.($i + 1).'</label> <select'.($i < 9 ? ' style="margin-right: 0.75em;"': '').' name="table'.$i.'" id="table'.$i.'">';
foreach ($tables_names as $key => $value) { echo '<option value="'.$key.'"'.($tables_slugs[$i] == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; }
echo '</select>'; } ?><br />
<?php _e('Display', 'affiliation-manager'); ?> <input style="text-align: center;" type="text" name="tables_number" id="tables_number" size="2" value="<?php echo $tables_number; ?>" /> 
<?php _e('tables starting from table', 'affiliation-manager'); ?> <input style="text-align: center;" type="text" name="start_table" id="start_table" size="2" value="<?php echo $start_table + 1; ?>" /> 
<input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" />
</div>
<?php if ($tables_number > 1) {
for ($i = 1; $i < $tables_number; $i++) { $summary .= '<li>| <a href="#'.str_replace('_', '-', $tables_slugs[$start_table + $i]).'">'.$tables_names[$tables_slugs[$start_table + $i]].'</a></li>'; }
$summary = '<ul class="subsubsub" style="float: none; text-align: center;">
<li><a href="#'.str_replace('_', '-', $tables_slugs[$start_table]).'">'.$tables_names[$tables_slugs[$start_table]].'</a></li>
'.$summary.'</ul>'; }
for ($i = 0; $i < $tables_number; $i++) {
$table_slug = $tables_slugs[$start_table + $i];
$table_name = table_name($table_slug);
$table_criteria = table_criteria($table_slug);
$options = get_option('affiliation_manager_'.$table_slug);
$columns = $options['columns'];
$max_columns = count($columns);
$columns_number = $options['columns_number'];
$start_column = $options['start_column'];
for ($j = 0; $j < $max_columns; $j++) { $columns[$max_columns + $j] = $columns[$j]; }
for ($j = 0; $j < $columns_number; $j++) { $table_ths .= table_th($table_slug, $columns[$start_column + $j]); }
echo $summary.'
<h3 id="'.str_replace('_', '-', $tables_slugs[$start_table + $i]).'"><strong>'.$tables_names[$tables_slugs[$start_table + $i]].'</strong></h3>
<table class="wp-list-table widefat fixed" style="margin: 1em 0 2em 0;">
<thead><tr>'.$table_ths.'</tr></thead>
<tfoot><tr>'.$table_ths.'</tr></tfoot>
<tbody>';
$items = $wpdb->get_results("SELECT * FROM $table_name WHERE (date BETWEEN '$start_date' AND '$end_date') $table_criteria $selection_criteria $filter_criteria ORDER BY date DESC", OBJECT);
if ($items) { foreach ($items as $item) {
for ($j = 1; $j < $columns_number; $j++) { $table_tds .= '<td>'.table_td($table_slug, $columns[$start_column + $j], $item).'</td>'; }
echo '<tr'.($boolean ? '' : ' class="alternate"').'><td style="height: 6em;">'.table_td($table_slug, $columns[$start_column], $item).row_actions($table_slug, $item).'</td>'.$table_tds.'</tr>';
$table_tds = ''; $boolean = !$boolean; } }
else { echo '<tr class="no-items"><td class="colspanchange" colspan="'.$columns_number.'">'.no_items($table_slug).'</td></tr>'; }
echo '</tbody></table>';
$table_ths = ''; $table_criteria = ''; } ?>
</form>
</div>
</div>