<?php switch ($_GET['action']) {
case 'check-login':
include_once '../../../wp-load.php';
global $wpdb;
$affiliates_table_name = $wpdb->prefix.'affiliation_manager_affiliates';
$login = affiliation_format_nice_name($_GET['login']);
if (($login == '') || (is_numeric($login))) { echo '<span class="error">'.__('No', 'affiliation-manager').'</span>'; }
else { $result = $wpdb->get_results("SELECT login FROM $affiliates_table_name WHERE login = '$login'", OBJECT);
if ($result) { echo '<span class="error">'.__('No', 'affiliation-manager').'</span>'; }
else { echo '<span class="valid">'.__('Yes', 'affiliation-manager').'</span>'; } } break;
case 'logout':
session_start();
unset($_SESSION['a_login']);
setcookie('a_login', '', time() - 86400, '/');
if (!headers_sent()) { header('Location: /'); exit; } break;
default: if (!headers_sent()) { header('Location: ../'); exit(); } }