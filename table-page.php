<?php $table_slug = str_replace('-', '-', str_replace('commerce-manager-', '', $_GET['page']));
include 'tables.php';
include_once 'tables-functions.php';
$options = get_option(str_replace('-', '_', $_GET['page']));
$table_name = table_name($table_slug);
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
$columns = $initial_options[$table_slug]['columns']; }
else { for ($i = 0; $i < $max_columns; $i++) { $columns[$i] = $_POST['column'.$i]; } }
$columns_number = (int) $_POST['columns_number'];
if ($columns_number > $max_columns) { $columns_number = $max_columns; }
elseif ($columns_number < 1) { $columns_number = $options['columns_number']; }
$limit = (int) $_POST['limit'];
if ($limit > 1000) { $limit = 1000; }
elseif ($limit < 1) { $limit = $options['limit']; }
$searchby = $_POST['searchby'];
$start_column = (int) $_POST['start_column'] - 1;
$start_column = $start_column % $max_columns;
if ($start_column < 0) { $start_column = $start_column + $max_columns; } }
else {
$columns = $options['columns'];
$columns_number = $options['columns_number'];
$limit = $options['limit'];
$searchby = $options['searchby'];
$start_column = $options['start_column']; }

$options = array(
'columns' => $columns,
'columns_number' => $columns_number,
'limit' => $limit,
'order' => $_GET['order'],
'orderby' => $_GET['orderby'],
'searchby' => $searchby,
'start_column' => $start_column);
update_option('commerce_manager_'.$table_slug, $options);

for ($i = 0; $i < $max_columns; $i++) { $columns[$max_columns + $i] = $columns[$i]; }

if ($_GET['s'] != '') {
if ($searchby == '') {
foreach ($searchby_options as $key => $value) { $search_criteria .= " OR ".$key." LIKE '%".$_GET['s']."%'"; }
$search_criteria = substr($search_criteria, 4); }
else {
$search_column = true; for ($i = 0; $i < $columns_number; $i++) { if ($searchby == $columns[$start_column + $i]) { $search_column = false; } }
$search_criteria = $searchby." LIKE '%".$_GET['s']."%'"; }
$search_criteria = 'AND ('.$search_criteria.')'; }

$query = $wpdb->get_row("SELECT count(*) as total FROM $table_name WHERE id > 0 $selection_criteria $search_criteria", OBJECT);
$n = (int) $query->total;
$_GET['paged'] = (int) $_REQUEST['paged'];
if ($_GET['paged'] < 1) { $_GET['paged'] = 1; }
$max_paged = ceil($n/$limit); if ($max_paged < 1) { $max_paged = 1; }
if ($_GET['paged'] > $max_paged) { $_GET['paged'] = $max_paged; }
$start = ($_GET['paged'] - 1)*$limit;
$items = $wpdb->get_results("SELECT * FROM $table_name WHERE id > 0 $selection_criteria $search_criteria ORDER BY ".$_GET['orderby']." ".strtoupper($_GET['order'])." LIMIT $start, $limit", OBJECT); ?>

<div class="wrap">
<div id="poststuff">
<?php commerce_manager_pages_top(); ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php commerce_manager_pages_menu(); ?>
<p class="search-box" style="float: right;"><?php _e('Search by', 'commerce-manager'); ?> <select name="searchby" id="searchby">
<?php echo '<option value=""'.($searchby == '' ? ' selected="selected"' : '').'>'.__('all fields', 'commerce-manager').'</option>'; ?>
<?php foreach ($searchby_options as $key => $value) {
echo '<option value="'.$key.'"'.($searchby == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select><br />
<label class="screen-reader-text" for="s"><?php _e('Search', 'commerce-manager'); ?></label>
<input type="text" name="s" id="s" value="<?php echo $_GET['s']; ?>" />
<input type="submit" class="button" name="submit" id="search-submit" value="<?php _e('Search', 'commerce-manager'); ?>" /></p>
<div class="clear"></div>
<div class="tablenav top">
<div class="alignleft actions">
<?php _e('Display', 'commerce-manager'); ?> <input style="text-align: center;" type="text" name="limit" id="limit" size="2" value="<?php echo $limit; ?>" /> 
<?php _e('results per page', 'commerce-manager'); ?> <input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" />
</div><?php tablenav_pages($table_slug, $n, $max_paged, 'top'); ?></div>
<table class="wp-list-table widefat fixed">
<?php if ($search_column) { $search_table_th = table_th($table_slug, $searchby); }
for ($i = 0; $i < $columns_number; $i++) { $table_ths .= table_th($table_slug, $columns[$start_column + $i]); } ?>
<thead><tr><?php echo $search_table_th.$table_ths; ?></tr></thead>
<tfoot><tr><?php echo $search_table_th.$table_ths; ?></tr></tfoot>
<tbody id="the-list">
<?php if ($items) { foreach ($items as $item) {
if ($search_column) { $search_table_td = '<td>'.table_td($table_slug, $searchby, $item).'</td>'; }
for ($i = 1; $i < $columns_number; $i++) { $table_tds .= '<td>'.table_td($table_slug, $columns[$start_column + $i], $item).'</td>'; }
echo '<tr'.($boolean ? '' : ' class="alternate"').'>'.$search_table_td.'<td style="height: 6em;">'.table_td($table_slug, $columns[$start_column], $item).row_actions($table_slug, $item).'</td>'.$table_tds.'</tr>';
$table_tds = ''; $boolean = !$boolean; } }
else { echo '<tr class="no-items"><td class="colspanchange" colspan="'.$columns_number.'">'.no_items($table_slug).'</td></tr>'; } ?>
</tbody>
</table>
<div class="tablenav bottom">
<?php tablenav_pages($table_slug, $n, $max_paged, 'bottom'); ?>
<div class="alignleft actions">
<?php _e('Display', 'commerce-manager'); ?> <input style="text-align: center;" type="text" name="columns_number" id="columns_number" size="2" value="<?php echo $columns_number; ?>" /> 
<?php _e('columns starting from column', 'commerce-manager'); ?> <input style="text-align: center;" type="text" name="start_column" id="start_column" size="2" value="<?php echo $start_column + 1; ?>" /> 
<input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /><input type="hidden" name="submit" value="true" />
<input type="submit" class="button-secondary" name="reset_columns" value="<?php _e('Reset the columns order', 'commerce-manager'); ?>" /><br />
<?php for ($i = 0; $i < $max_columns; $i++) {
echo '<label for="column'.$i.'">'.__('Column', 'commerce-manager').' '.($i + 1).'</label> <select'.($i < 9 ? ' style="margin-left: 0.75em;"': '').' name="column'.$i.'" id="column'.$i.'">';
foreach ($tables[$table_slug] as $key => $value) { echo '<option value="'.$key.'"'.($columns[$i] == $key ? ' selected="selected"' : '').'>'.$value['name'].'</option>'."\n"; }
echo '</select><br />'; } ?> 
</div></div>
</form>
</div>
</div>