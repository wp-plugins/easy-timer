<?php $back_office_options = get_option('affiliation_manager_back_office');

if ((strstr($_GET['page'], 'click')) && ($_GET['action'] == 'delete')) {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
global $wpdb;
if (isset($_GET['id'])) { $results = $wpdb->query("DELETE FROM ".$wpdb->prefix."affiliation_manager_clicks WHERE id = '".$_GET['id']."'"); }
elseif (isset($_GET['referrer'])) { $results = $wpdb->query("DELETE FROM ".$wpdb->prefix."affiliation_manager_clicks WHERE referrer = '".$_GET['referrer']."'"); } } ?>
<div class="wrap">
<div id="poststuff">
<?php affiliation_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.(isset($_GET['id']) ? __('Click deleted.', 'affiliation-manager') : __('Clicks deleted.', 'affiliation-manager')).'</strong></p></div>'; } ?>
<?php affiliation_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php if (isset($_GET['id'])) { _e('Do you really want to permanently delete this click?', 'affiliation-manager'); }
elseif (isset($_GET['referrer'])) { _e('Do you really want to permanently delete all the clicks of this referrer?', 'affiliation-manager'); } ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'affiliation-manager'); ?>" />
</div>
<div class="clear"></div>
</form><?php } ?>
</div>
</div><?php }

elseif ((strstr($_GET['page'], 'commission')) && ($_GET['action'] == 'cancel')) {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
global $wpdb;
if (isset($_GET['id'])) {
if (strstr($_GET['page'], 'recurring')) { $table = 'commerce_manager_recurring_payments'; }
elseif (strstr($_GET['page'], 'prospects')) { $table = 'optin_manager_prospects'; }
else { $table = 'commerce_manager_orders'; }
$results = $wpdb->query("UPDATE ".$wpdb->prefix.$table." SET
	commission_amount = '0',
	".(strstr($table, "optin") ? "" : "commission_payment = '',")."
	commission_status = '',
	commission_payment_date = '',
	commission_payment_date_utc = '',
	commission2_amount = '0',
	commission2_status = '',
    commission2_payment_date = '',
	commission2_payment_date_utc = '' WHERE id = '".$_GET['id']."'"); }
elseif (isset($_GET['referrer'])) {
foreach (array('commerce_manager_orders', 'commerce_manager_recurring_payments', 'optin_manager_prospects') as $table) {
$results = $wpdb->query("UPDATE ".$wpdb->prefix.$table." SET
	commission_amount = '0',
	".(strstr($table, "optin") ? "" : "commission_payment = '',")."
	commission_status = '',
	commission_payment_date = '',
	commission_payment_date_utc = '' WHERE commission_status = 'unpaid' AND referrer = '".$_GET['referrer']."'");
$results = $wpdb->query("UPDATE ".$wpdb->prefix.$table." SET
	commission2_amount = '0',
	commission2_status = '',
	commission2_payment_date = '',
	commission2_payment_date_utc = '' WHERE commission2_status = 'unpaid' AND referrer2 = '".$_GET['referrer']."'"); } } } ?>
<div class="wrap">
<div id="poststuff">
<?php affiliation_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.(isset($_GET['id']) ? __('Commission canceled.', 'affiliation-manager') : __('Commissions canceled.', 'affiliation-manager')).'</strong></p></div>'; } ?>
<?php affiliation_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php if (isset($_GET['id'])) { _e('Do you really want to cancel this commission?', 'affiliation-manager'); }
elseif (isset($_GET['referrer'])) { _e('Do you really want to cancel all the unpaid commissions of this referrer?', 'affiliation-manager'); } ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'affiliation-manager'); ?>" />
</div>
<div class="clear"></div>
</form><?php } ?>
</div>
</div><?php }

else {
$table_slug = str_replace('-', '_', str_replace('affiliation-manager-', '', $_GET['page']));
include 'tables.php';
include_once 'tables-functions.php';
add_action('admin_footer', 'affiliation_date_picker_js');
$options = get_option(str_replace('-', '_', $_GET['page']));
$table_name = table_name($table_slug);
$table_criteria = table_criteria($table_slug);
foreach ($tables[$table_slug] as $key => $value) {
if ($value['name'] == '') { unset($tables[$table_slug][$key]); }
if ($value['searchby'] != '') { $searchby_options[$key] = $value['searchby']; } }
$max_columns = count($tables[$table_slug]);
if ($tables[$table_slug][$_GET['orderby']] == '') { $_GET['orderby'] = $options['orderby']; }
switch ($_GET['order']) { case 'asc': case 'desc': break; default: $_GET['order'] = $options['order']; }

if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
$_POST = array_map('stripslashes', $_POST);
$_GET['s'] = $_POST['s'];
if (isset($_POST['reset_columns'])) {
include 'initial-options.php';
$columns = $initial_options[$table_slug]['columns'];
$displayed_columns = $initial_options[$table_slug]['displayed_columns']; }
else {
$displayed_columns = array();
for ($i = 0; $i < $max_columns; $i++) {
$columns[$i] = $_POST['column'.$i];
if ($_POST['column'.$i.'_displayed'] == 'yes') { $displayed_columns[] = $i; } } }
$limit = (int) $_POST['limit'];
if ($limit > 1000) { $limit = 1000; }
elseif ($limit < 1) { $limit = $options['limit']; }
$searchby = $_POST['searchby'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date']; }
else {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
if (isset($_GET['end_date'])) { $end_date = $_GET['end_date']; }
else { $end_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET); }
$columns = (array) $options['columns'];
$displayed_columns = (array) $options['displayed_columns'];
$limit = $options['limit'];
$searchby = $options['searchby'];
$start_date = $options['start_date']; }

if ($limit < 1) { $limit = 1; }
$start_date = trim(mysql_real_escape_string(strip_tags($start_date)));
if (strlen($start_date) == 10) { $start_date .= ' 00:00:00'; }
$end_date = trim(mysql_real_escape_string(strip_tags($end_date)));
if (strlen($end_date) == 10) { $end_date .= ' 23:59:59'; }

if ($options) {
$options = array(
'columns' => $columns,
'displayed_columns' => $displayed_columns,
'limit' => $limit,
'order' => $_GET['order'],
'orderby' => $_GET['orderby'],
'searchby' => $searchby,
'start_date' => $start_date);
update_option('affiliation_manager_'.$table_slug, $options); }

if ($_GET['s'] != '') {
if ($searchby == '') {
foreach ($searchby_options as $key => $value) { $search_criteria .= " OR ".$key." LIKE '%".$_GET['s']."%'"; }
$search_criteria = substr($search_criteria, 4); }
else {
$search_column = true; for ($i = 0; $i < $max_columns; $i++) {
if ((in_array($i, $displayed_columns)) && ($searchby == $columns[$i])) { $search_column = false; } }
$search_criteria = $searchby." LIKE '%".$_GET['s']."%'"; }
$search_criteria = 'AND ('.$search_criteria.')'; }

$query = $wpdb->get_row("SELECT count(*) as total FROM $table_name WHERE (date BETWEEN '$start_date' AND '$end_date') $table_criteria $selection_criteria $search_criteria", OBJECT);
$n = (int) $query->total;
$_GET['paged'] = (int) $_REQUEST['paged'];
if ($_GET['paged'] < 1) { $_GET['paged'] = 1; }
$max_paged = ceil($n/$limit);
if ($max_paged < 1) { $max_paged = 1; }
if ($_GET['paged'] > $max_paged) { $_GET['paged'] = $max_paged; }
$start = ($_GET['paged'] - 1)*$limit;
$items = $wpdb->get_results("SELECT * FROM $table_name WHERE (date BETWEEN '$start_date' AND '$end_date') $table_criteria $selection_criteria $search_criteria ORDER BY ".$_GET['orderby']." ".strtoupper($_GET['order'])." LIMIT $start, $limit", OBJECT); ?>

<div class="wrap">
<div id="poststuff">
<?php affiliation_manager_pages_top($back_office_options); ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php affiliation_manager_pages_menu($back_office_options); ?>
<?php affiliation_manager_pages_search_field('search', $searchby, $searchby_options); ?>
<?php affiliation_manager_pages_date_picker($start_date, $end_date); ?>
<div class="tablenav top">
<div class="alignleft actions">
<?php _e('Display', 'affiliation-manager'); ?> <input style="text-align: center;" type="text" name="limit" id="limit" size="2" value="<?php echo $limit; ?>" /> 
<?php _e('results per page', 'affiliation-manager'); ?> <input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" />
</div><?php tablenav_pages($table_slug, $n, $max_paged, $end_date, 'top'); ?></div>
<table class="wp-list-table widefat fixed">
<?php if ($search_column) { $search_table_th = table_th($table_slug, $searchby); }
for ($i = 0; $i < $max_columns; $i++) { if (in_array($i, $displayed_columns)) { $table_ths .= table_th($table_slug, $columns[$i]); } } ?>
<thead><tr><?php echo $search_table_th.$table_ths; ?></tr></thead>
<tfoot><tr><?php echo $search_table_th.$table_ths; ?></tr></tfoot>
<tbody id="the-list">
<?php if ($items) { foreach ($items as $item) {
if ($search_column) { $search_table_td = '<td>'.table_td($table_slug, $searchby, $item).'</td>'; }
$first = true; for ($i = 0; $i < $max_columns; $i++) {
if (in_array($i, $displayed_columns)) {
$table_tds .= '<td'.($first ? ' style="height: 6em;"' : '').'>'.table_td($table_slug, $columns[$i], $item).($first ? row_actions($table_slug, $item) : '').'</td>';
$first = false; } }
echo '<tr'.($boolean ? '' : ' class="alternate"').'>'.$search_table_td.$table_tds.'</tr>';
$table_tds = ''; $boolean = !$boolean; } }
else { echo '<tr class="no-items"><td class="colspanchange" colspan="'.count($displayed_columns).'">'.no_items($table_slug).'</td></tr>'; } ?>
</tbody>
</table>
<div class="tablenav bottom">
<?php tablenav_pages($table_slug, $n, $max_paged, $end_date, 'bottom'); ?>
<div class="alignleft actions">
<input type="hidden" name="submit" value="true" />
<input type="submit" class="button-secondary" name="reset_columns" value="<?php _e('Reset the columns', 'affiliation-manager'); ?>" />
<input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /><br />
<?php for ($i = 0; $i < $max_columns; $i++) {
echo '<label>'.__('Column', 'affiliation-manager').' '.($i + 1).' <select'.($i < 9 ? ' style="margin-left: 0.75em;"': '').' name="column'.$i.'" id="column'.$i.'">';
foreach ($tables[$table_slug] as $key => $value) {
if ($value['name'] != '') { echo '<option value="'.$key.'"'.($columns[$i] == $key ? ' selected="selected"' : '').'>'.$value['name'].'</option>'."\n"; } }
echo '</select></label>
<label><input type="checkbox" name="column'.$i.'_displayed" id="column'.$i.'_displayed" value="yes"'.(!in_array($i, $displayed_columns) ? '' : ' checked="checked"').' /> '.__('Display', 'affiliation-manager').'<br /></label>'; } ?>
<input type="submit" class="button-secondary" name="reset_columns" value="<?php _e('Reset the columns', 'affiliation-manager'); ?>" />
<input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" />
</div></div>
</form>
</div>
</div>
<?php }