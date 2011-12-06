<?php switch ($_GET['action']) {
case 'check-login':
$file = 'wp-load.php'; $i = 0;
while ((!file_exists($file)) && ($i < 8)) { $file = '../'.$file; $i = $i + 1; }
include_once $file;
global $wpdb;
$login = affiliation_format_nice_name($_GET['login']);
if (($login == '') || (is_numeric($login))) { echo '<span class="error">'.__('Unavailable', 'affiliation-manager').'</span>'; }
else { $result = $wpdb->get_results("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '$login'", OBJECT);
if ($result) { echo '<span class="error">'.__('Unavailable', 'affiliation-manager').'</span>'; }
else { echo '<span class="valid">'.__('Available', 'affiliation-manager').'</span>'; } } break;
case 'logout':
session_start();
unset($_SESSION['a_login']);
setcookie('a_login', '', time() - 86400, '/');
if (!headers_sent()) { header('Location: /'); exit; } break;
default: if (!headers_sent()) { header('Location: ../'); exit(); } }