<?php switch ($admin_page) {
case 'options':
include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'initial-options.php';
foreach ($initial_options[''] as $key => $value) { if (!isset($_POST[$key])) { $_POST[$key] = ''; } }
foreach ($initial_options[''] as $key => $value) { if ($_POST[$key] == '') { $_POST[$key] = $value; } $options[$key] = $_POST[$key]; }
if (isset($_POST['submit'])) { update_option('audiobooks_authors_and_narrators', $options); }
break;


case 'author': case 'narrator':
$table_slug = $admin_page.'s';
foreach ($tables[$table_slug] as $key => $value) { if (!isset($_POST[$key])) { $_POST[$key] = ''; } }
$_POST['email_address'] = format_email_address($_POST['email_address']);
if ($_POST['date'] == '') {
$_POST['date'] = $current_date;
$_POST['date_utc'] = $current_date_utc; }
else {
$d = preg_split('#[^0-9]#', $_POST['date'], 0, PREG_SPLIT_NO_EMPTY);
for ($i = 0; $i < 6; $i++) { $d[$i] = (int) (isset($d[$i]) ? $d[$i] : ($i < 3 ? 1 : 0)); }
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['date'] = date('Y-m-d H:i:s', $time);
$_POST['date_utc'] = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); }

if (!isset($_GET['id'])) {
if (($error == '') && (isset($_POST['submit']))) {
$result = $wpdb->get_results("SELECT id FROM ".$wpdb->prefix."audiobooks_authors_and_narrators_".$table_slug." WHERE first_name = '".str_replace("'", "''", $_POST['first_name'])."' AND last_name = '".str_replace("'", "''", $_POST['last_name'])."'", OBJECT);
if (!$result) {
$updated = true;
$sql = audiobooks_authors_and_narrators_sql_array($tables[$table_slug], $_POST);
$keys_list = ''; $values_list = '';
foreach ($tables[$table_slug] as $key => $value) { if ($key != 'id') { $keys_list .= $key.","; $values_list .= $sql[$key].","; } }
$results = $wpdb->query("INSERT INTO ".$wpdb->prefix."audiobooks_authors_and_narrators_".$table_slug." (".substr($keys_list, 0, -1).") VALUES(".substr($values_list, 0, -1).")"); } } }

if (isset($_GET['id'])) {
$updated = true;
if (isset($_POST['submit'])) {
$sql = audiobooks_authors_and_narrators_sql_array($tables[$table_slug], $_POST);
$list = '';
foreach ($tables[$table_slug] as $key => $value) { switch ($key) {
case 'id': break;
default: $list .= $key." = ".$sql[$key].","; } }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."audiobooks_authors_and_narrators_".$table_slug." SET ".substr($list, 0, -1)." WHERE id = ".$_GET['id']); } }
break; }