<?php global $wpdb;
if ($type == 'optin_form') {
$_GET['optin_form_data'] = (array) $_GET['optin_form_data'];
if ((isset($_GET['optin_form_id'])) && ($_GET['optin_form_data']['id'] != $_GET['optin_form_id'])) {
$_GET['optin_form_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_forms WHERE id = ".$_GET['optin_form_id'], OBJECT); }
$optin_form_data = $_GET['optin_form_data'];
extract(shortcode_atts(array('data' => '', 'id' => '', 'limit' => ''), $atts));
$field = str_replace('-', '_', format_nice_name($data));
if (($field == '') || ($field == 'prospects')) { $field = 'prospects_count'; }
elseif ($field == 'displays') { $field = 'displays_count'; }
$id = preg_split('#[^0-9]#', $id, 0, PREG_SPLIT_NO_EMPTY);
$m = count($id);

if ($m < 2) {
$id = (int) $id[0];
if (($id == 0) || ($id == $optin_form_data['id'])) { $data = $optin_form_data[$field]; }
else {
foreach (array('optin_form_id', 'optin_form_data') as $key) {
if (isset($_GET[$key])) { $original[$key] = $_GET[$key]; } }
$optin_form_data = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_forms WHERE id = $id", OBJECT);
$_GET['optin_form_id'] = $id; $_GET['optin_form_data'] = $optin_form_data;
$data = $optin_form_data[$field]; } }

else {
$data = 0; for ($i = 0; $i < $m; $i++) {
$id[$i] = (int) $id[$i];
$optin_form_data = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_forms WHERE id = ".$id[$i], OBJECT);
$data = $data + $optin_form_data[$field]; } } }

else {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
extract(shortcode_atts(array('data' => '', 'limit' => '', 'range' => '', 'status' => ''), $atts));

$data = str_replace('_', '-', format_nice_name($data));
switch ($data) {
case 'forms': $table = $wpdb->prefix.'optin_manager_forms'; $field = ''; break;
case 'forms-categories': $table = $wpdb->prefix.'optin_manager_forms_categories'; $field = ''; break;
case 'prospects': $table = $wpdb->prefix.'optin_manager_prospects'; $field = ''; break;
default: $table = $wpdb->prefix.'optin_manager_prospects'; $field = ''; }

$range = str_replace('_', '-', format_nice_name($range));
if (is_numeric($range)) {
$end_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$start_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET - 86400*$range);
$date_criteria = "AND (date BETWEEN '".$start_date."' AND '".$end_date."')"; }
else { switch ($range) {
case 'previous-month':
$Y = (int) date('Y', time() + 3600*UTC_OFFSET);
$M = (int) date('n', time() + 3600*UTC_OFFSET);
if ($M == 1) { $m = 12; $y = $Y - 1; }
else { $m = $M - 1; $y = $Y; }
if ($M < 10) { $M = '0'.$M; }
if ($m < 10) { $m = '0'.$m; }
$start_date = $y.'-'.$m.'-01 00:00:00';
$end_date = $Y.'-'.$M.'-01 00:00:00';
$date_criteria = "AND (date BETWEEN '".$start_date."' AND '".$end_date."')"; break;
case 'previous-year':
$Y = (int) date('Y', time() + 3600*UTC_OFFSET);
$y = $Y - 1;
$start_date = $y.'-01-01 00:00:00';
$end_date = $y.'-12-31 23:59:59';
$date_criteria = "AND (date BETWEEN '".$start_date."' AND '".$end_date."')"; break;
default: $date_criteria = ''; } }

$status = str_replace('-', '_', format_nice_name($status));
if ($status != '') { $status_criteria = "AND status = '".$status."'"; }

if (is_string($table)) {
if ($field == '') {
$row = $wpdb->get_row("SELECT count(*) as total FROM $table WHERE id > 0 $date_criteria $status_criteria", OBJECT);
$data = (int) $row->total; }
else {
$row = $wpdb->get_row("SELECT SUM($field) AS total FROM $table WHERE id > 0 $date_criteria $status_criteria", OBJECT);
$data = round(100*$row->total)/100; } }

else {
$data = 0; foreach ($table as $table_name) {
$row = $wpdb->get_row("SELECT SUM($field) AS total FROM $table_name WHERE id > 0 $date_criteria $status_criteria", OBJECT);
$data = $data + round(100*$row->total)/100; } } }

if ($limit == '') { $limit = '0'; }
else { $limit = '0/'.$limit; }
$limit = preg_split('#[^0-9]#', $limit, 0, PREG_SPLIT_NO_EMPTY);
$n = count($limit);

$i = 0; while (($i < $n) && ($limit[$i] <= $data)) { $k = $i; $i = $i + 1; }
if ($i < $n) { $remaining_number = $limit[$i] - $data; $total_remaining_number = $limit[$n - 1] - $data; }
else { $i = $n - 1; $remaining_number = 0; $total_remaining_number = 0; }

$content = explode('[after]', do_shortcode($content));

$tags = array('limit', 'number', 'remaining-number', 'total-limit', 'total-number', 'total-remaining-number');
foreach ($tags as $tag) {
$_tag = str_replace('-', '_', format_nice_name($tag));
if (isset($_GET['optin_'.$_tag])) { $original['optin_'.$_tag] = $_GET['optin_'.$_tag]; }
add_shortcode($tag, create_function('$atts', '$atts["data"] = "'.$tag.'"; return optin_counter_tag($atts);')); }

$_GET['optin_limit'] = $limit[$i];
$_GET['optin_number'] = $data - $limit[$k];
$_GET['optin_remaining_number'] = $remaining_number;
$_GET['optin_total_limit'] = $limit[$n - 1];
$_GET['optin_total_number'] = $data;
$_GET['optin_total_remaining_number'] = $total_remaining_number;

$content[$k] = do_shortcode($content[$k]);

foreach ($tags as $tag) {
$_tag = str_replace('-', '_', format_nice_name($tag));
if (isset($original['optin_'.$_tag])) { $_GET['optin_'.$_tag] = $original['optin_'.$_tag]; }
remove_shortcode($tag); }

if ($type == 'optin_form') {
foreach (array('optin_form_id', 'optin_form_data') as $key) {
if (isset($original[$key])) { $_GET[$key] = $original[$key]; } } }