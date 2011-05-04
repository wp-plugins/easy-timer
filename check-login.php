<?php include_once('../../../wp-load.php');
global $affiliates_table_name, $wpdb;
$login = affiliation_format_nice_name($_GET['login']);
if (($login == '') || (is_numeric($login))) { echo '<span class="error">'.__('No', 'affiliation-manager').'</span>'; }
else { $result = $wpdb->get_results("SELECT login FROM $affiliates_table_name where login='$login'", OBJECT);
if ($result) { echo '<span class="error">'.__('No', 'affiliation-manager').'</span>'; }
else { echo '<span class="valid">'.__('Yes', 'affiliation-manager').'</span>'; } }