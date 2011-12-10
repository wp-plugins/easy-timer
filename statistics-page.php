<?php include 'tables.php';
include_once 'tables-functions.php';
add_action('admin_footer', 'optin_date_picker_js');
$back_office_options = get_option('optin_manager_back_office');
$undisplayed_rows = (array) $back_office_options['statistics_page_undisplayed_rows'];
$undisplayed_columns = (array) $back_office_options['statistics_page_undisplayed_columns'];
include 'admin-pages.php';
$options = get_option('optin_manager_statistics');

$tables_names = array(
'forms' => __('Forms', 'optin-manager'),
'forms_categories' => __('Forms categories', 'optin-manager'),
'prospects' => __('Prospects', 'optin-manager'));
$max_tables = count($tables_names);

$filterby_options = array(
'postcode' => __('postcode', 'optin-manager'),
'town' => __('town', 'optin-manager'),
'country' => __('country', 'optin-manager'),
'ip_address' => __('IP address ', 'optin-manager'),
'user_agent' => __('user agent', 'optin-manager'),
'referring_url' => __('referring URL', 'optin-manager'),
'form_id' => __('form ID', 'optin-manager'),
'autoresponder' => __('autoresponder', 'optin-manager'),
'autoresponder_list' => __('autoresponder list', 'optin-manager'),
'referrer' => __('referrer', 'optin-manager'));

if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
$_POST = array_map('stripslashes', $_POST);
$_GET['s'] = $_POST['s'];
$filterby = $_POST['filterby'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$displayed_tables = array();
for ($i = 0; $i < $max_tables; $i++) {
$tables_slugs[$i] = $_POST['table'.$i];
if ($_POST['table'.$i.'_displayed'] == 'yes') { $displayed_tables[] = $i; } } }
else {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$displayed_tables = (array) $options['displayed_tables'];
$end_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$filterby = $options['filterby'];
$start_date = $options['start_date'];
$tables_slugs = $options['tables']; }

$start_date = trim(mysql_real_escape_string(strip_tags($start_date)));
if (strlen($start_date) == 10) { $start_date .= ' 00:00:00'; }
$end_date = trim(mysql_real_escape_string(strip_tags($end_date)));
if (strlen($end_date) == 10) { $end_date .= ' 23:59:59'; }

if ($options) {
$options = array(
'displayed_tables' => $displayed_tables,
'filterby' => $filterby,
'start_date' => $start_date,
'tables' => $tables_slugs);
update_option('optin_manager_statistics', $options); }

if ($_GET['s'] != '') { $filter_criteria = "AND (".$filterby." = '".$_GET['s']."')"; }

$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."optin_manager_prospects WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$prospects_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."optin_manager_prospects WHERE status = 'active' AND (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$active_prospects_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."optin_manager_prospects WHERE status = 'inactive' AND (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$inactive_prospects_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."optin_manager_prospects WHERE status = 'unsubscribed' AND (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$unsubscribed_prospects_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."optin_manager_forms WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$forms_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."optin_manager_forms_categories WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$forms_categories_number = (int) $row->total;

$prospects_a_tag = '<a style="text-decoration: none;" href="admin.php?page=optin-manager-prospects">';
$active_prospects_a_tag = '<a style="color: #008000; text-decoration: none;" href="admin.php?page=optin-manager-prospects&amp;status=active">';
$inactive_prospects_a_tag = '<a style="color: #e08000; text-decoration: none;" href="admin.php?page=optin-manager-prospects&amp;status=inactive">';
$unsubscribed_prospects_a_tag = '<a style="color: #c00000; text-decoration: none;" href="admin.php?page=optin-manager-prospects&amp;status=unsubscribed">';
$forms_a_tag = '<a style="text-decoration: none;" href="admin.php?page=optin-manager-forms">';
$forms_categories_a_tag = '<a style="text-decoration: none;" href="admin.php?page=optin-manager-forms-categories">'; ?>

<div class="wrap">
<div id="poststuff">
<?php optin_manager_pages_top($back_office_options); ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php optin_manager_pages_menu($back_office_options); ?>
<?php optin_manager_pages_search_field('filter', $filterby, $filterby_options); ?>
<?php optin_manager_pages_date_picker($start_date, $end_date); ?>
<?php $global_table_ths = '
<th scope="col" class="manage-column" style="width: 30%;">'.$statistics_columns['data']['name'].'</th>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<th scope="col" class="manage-column" style="width: 20%;">'.$statistics_columns['quantity']['name'].'</th>').'
'.(in_array('prospects_percentage', $undisplayed_columns) ? '' : '<th scope="col" class="manage-column" style="width: 30%;">'.$statistics_columns['prospects_percentage']['name'].'</th>');
echo '
<h3 id="global-statistics"><strong>'.__('Global statistics', 'optin-manager').'</strong></h3>
<table class="wp-list-table widefat fixed" style="margin: 1em 0 2em 0;">
<thead><tr>'.$global_table_ths.'</tr></thead>
<tfoot><tr>'.$global_table_ths.'</tr></tfoot>
<tbody>';
if (!in_array('prospects', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['prospects']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$prospects_a_tag.$prospects_number.'</a></td>').'
'.(in_array('prospects_percentage', $undisplayed_columns) ? '' : '<td>'.$prospects_a_tag.($prospects_number == 0 ? '--' : '100 %').'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('active_prospects', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['active_prospects']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$active_prospects_a_tag.$active_prospects_number.'</a></td>').'
'.(in_array('prospects_percentage', $undisplayed_columns) ? '' : '<td>'.($prospects_number == 0 ? '--' : $active_prospects_a_tag.((round(10000*$active_prospects_number/$prospects_number))/100).' %</a>').'</td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('inactive_prospects', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['inactive_prospects']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$inactive_prospects_a_tag.$inactive_prospects_number.'</a></td>').'
'.(in_array('prospects_percentage', $undisplayed_columns) ? '' : '<td>'.($prospects_number == 0 ? '--' : $inactive_prospects_a_tag.((round(10000*$inactive_prospects_number/$prospects_number))/100).' %</a>').'</td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('unsubscribed_prospects', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['unsubscribed_prospects']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$unsubscribed_prospects_a_tag.$unsubscribed_prospects_number.'</a></td>').'
'.(in_array('prospects_percentage', $undisplayed_columns) ? '' : '<td>'.($prospects_number == 0 ? '--' : $unsubscribed_prospects_a_tag.((round(10000*$unsubscribed_prospects_number/$prospects_number))/100).' %</a>').'</td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('forms', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['forms']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$forms_a_tag.$forms_number.'</a></td>').'
'.(in_array('prospects_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('forms_categories', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['forms_categories']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$forms_categories_a_tag.$forms_categories_number.'</a></td>').'
'.(in_array('prospects_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
</tr>'; $boolean = !$boolean; }
echo '</tbody></table>'; ?>
<div style="text-align: center;">
<?php for ($i = 0; $i < $max_tables; $i++) {
echo '<label>'.__('Table', 'optin-manager').' '.($i + 1).' <select name="table'.$i.'" id="table'.$i.'">';
foreach ($tables_names as $key => $value) { echo '<option value="'.$key.'"'.($tables_slugs[$i] == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; }
echo '</select></label>
<label><input type="checkbox" name="table'.$i.'_displayed" id="table'.$i.'_displayed" value="yes"'.(!in_array($i, $displayed_tables) ? '' : ' checked="checked"').' /> '.__('Display', 'optin-manager').'<br /></label>'; } ?><br />
<input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" />
</div>
<?php if (count($displayed_tables) > 1) {
for ($i = 0; $i < $max_tables; $i++) {
if (in_array($i, $displayed_tables)) { $summary .= '<li> | <a href="#'.str_replace('_', '-', $tables_slugs[$i]).'">'.$tables_names[$tables_slugs[$i]].'</a></li>'; } }
$summary = '<ul class="subsubsub" style="float: none; text-align: center;">
<li>'.substr($summary, 7).'</ul>'; }
for ($i = 0; $i < $max_tables; $i++) {
if (in_array($i, $displayed_tables)) {
$table_slug = $tables_slugs[$i];
$table_name = table_name($table_slug);
$options = get_option('optin_manager_'.$table_slug);
$columns = (array) $options['columns'];
$max_columns = count($columns);
$displayed_columns = (array) $options['displayed_columns'];
for ($j = 0; $j < $max_columns; $j++) { if (in_array($j, $displayed_columns)) { $table_ths .= table_th($table_slug, $columns[$j]); } }
echo $summary.'
<h3 id="'.str_replace('_', '-', $tables_slugs[$i]).'"><strong>'.$tables_names[$tables_slugs[$i]].'</strong></h3>
<table class="wp-list-table widefat fixed" style="margin: 1em 0 2em 0;">
<thead><tr>'.$table_ths.'</tr></thead>
<tfoot><tr>'.$table_ths.'</tr></tfoot>
<tbody>';
$items = $wpdb->get_results("SELECT * FROM $table_name WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria ORDER BY date DESC", OBJECT);
if ($items) { foreach ($items as $item) {
$first = true; for ($j = 0; $j < $max_columns; $j++) {
if (in_array($j, $displayed_columns)) {
$table_tds .= '<td'.($first ? ' style="height: 6em;"' : '').'>'.table_td($table_slug, $columns[$j], $item).($first ? row_actions($table_slug, $item) : '').'</td>';
$first = false; } }
echo '<tr'.($boolean ? '' : ' class="alternate"').'>'.$table_tds.'</tr>';
$table_tds = ''; $boolean = !$boolean; } }
else { echo '<tr class="no-items"><td class="colspanchange" colspan="'.count($displayed_columns).'">'.no_items($table_slug).'</td></tr>'; }
echo '</tbody></table>';
$table_ths = ''; } } ?>
</form>
</div>
</div>