<?php session_start();
unset($_SESSION['a_login']);
unset($_SESSION['affiliate_data']);
setcookie('a_login', '', time() - 86400, '/');
if (!headers_sent()) { header('Location: /'); exit; }