<?php $file = 'wp-load.php'; $i = 0;
while ((!file_exists($file)) && ($i < 8)) { $file = '../'.$file; $i = $i + 1; }
include_once $file;

if ($_GET['action'] == 'order') {
load_plugin_textdomain('commerce-manager', false, 'commerce-manager/languages');
global $wpdb;
$_GET = $_REQUEST;
if ($_GET['referring_url'] == '') { $_GET['referring_url'] = htmlspecialchars($_SERVER['HTTP_REFERER']); }
$gateway = $_GET['gateway'];
if ($gateway == '') { $gateway = commerce_data('default_payment_mode'); }
$gateway = str_replace('_', '-', format_nice_name($gateway));
$file = 'gateways/'.$gateway.'.php';

if (file_exists($file)) {
$action = 'order';
foreach (array(
'payment_option',
'product_id',
'quantity') as $field) {
if ($_GET[$field] == '') { $_GET[$field] = commerce_data('default_'.$field); }
$_GET[$field] = (int) $_GET[$field]; }
$_GET['lang'] = strtolower(substr(WPLANG, 0, 2));
$_GET['country_code'] = strtoupper(substr(WPLANG, -2));
if (function_exists('award_commission')) {
$_GET['referrer'] = $_COOKIE[AFFILIATION_COOKIES_NAME];
if ($_GET['referrer'] == '') {
$result = $wpdb->get_row("SELECT referrer FROM ".$wpdb->prefix."affiliation_manager_clicks WHERE ip_address = '".$_SERVER['REMOTE_ADDR']."' ORDER BY date DESC LIMIT 1", OBJECT);
if ($result) { $_GET['referrer'] = $result->referrer; } }
if (($_GET['referrer'] != '') && (!strstr($_GET['referrer'], '@'))) {
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_GET['referrer']."' AND status = 'active'", OBJECT);
if (!$result) { $_GET['referrer'] = ''; } } }
$_GET['available_quantity'] = product_data('available_quantity');
if ((!is_string($_GET['available_quantity'])) && ($_GET['available_quantity'] < $_GET['quantity'])) { $_GET['available'] = 'no'; }
else { $_GET['available'] = 'yes'; }
if ($_GET['available'] == 'yes') {
if (($_GET['referrer'] == '') || (!function_exists('award_commission'))) { $_GET['sale_winner'] = 'affiliator'; }
else { award_commission(); }
if (commerce_session()) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_clients WHERE login = '".$_SESSION['commerce_login']."'", OBJECT);
$_GET['client_id'] = (int) $result->id;
$_GET['client_data'] = (array) $result;
include dirname(__FILE__).'/libraries/personal-informations.php';
foreach ($personal_informations as $field) {
if ($_GET[$field] == '') { $_GET[$field] = $_GET['client_data'][$field]; } } }
if (function_exists('affiliation_session')) { if (affiliation_session()) {
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_SESSION['affiliation_login']."'", OBJECT);
$_GET['affiliate_id'] = (int) $result->id; } }
if (function_exists('membership_session')) { if (membership_session('')) {
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$_SESSION['membership_login']."'", OBJECT);
$_GET['member_id'] = (int) $result->id; } }
$_GET['user_id'] = (int) get_current_user_id();
foreach (array(
'currency_code',
'default_shipping_cost_applied',
'description',
'downloadable',
'id',
'name',
'order_confirmation_url',
'reference',
'shipping_address_required',
'shipping_cost',
'tax_applied',
'tax_included_in_price',
'tax_percentage',
'thumbnail_url',
'url',
'weight',
'weight_unit') as $field) { $_GET[$field] = product_data($field); }
if ($_GET['payment_option'] == 0) {
foreach (array('default_tax_applied', 'price') as $field) { $_GET[$field] = product_data($field); }
if (($_GET['tax_applied'] == 'no') || ($_GET['default_tax_applied'] == 'yes')) { $_GET['tax'] = 0; $_GET['net_price'] = $_GET['price']; }
else {
if ($_GET['tax_included_in_price'] == 'yes') {
$r = 1 + $_GET['tax_percentage']/100;
$_GET['net_price'] = round(100*$_GET['price']/$r)/100;
$_GET['tax'] = $_GET['quantity']*($_GET['price'] - $_GET['net_price']); }
else {
$_GET['net_price'] = $_GET['price'];
$_GET['tax'] = round($_GET['quantity']*$_GET['tax_percentage']*$_GET['price'])/100; } } }
else {
foreach (array(
'first_payment_amount',
'first_payment_amount_used',
'first_payment_period_quantity',
'first_payment_period_time_unit',
'first_payment_period_used',
'payments_amount',
'payments_number',
'payments_period_quantity',
'payments_period_time_unit') as $field) { $_GET[$field] = product_data($field.$_GET['payment_option']); }
foreach (array('first_payment_amount', 'payments_amount') as $field) { $_GET[$field] = $_GET['quantity']*$_GET[$field]; }
if (($_GET['tax_applied'] == 'yes') && ($_GET['tax_included_in_price'] == 'no')) {
$r = 1 + $_GET['tax_percentage']/100;
foreach (array('first_payment_amount', 'payments_amount') as $field) { $_GET[$field] = round(100*$_GET[$field]*$r)/100; } } }
include_once $file; } }

if ($_GET['available'] == 'no') { ?>
<!DOCTYPE html>
<html>
<head>
<title><?php _e('Unavailable Product', 'commerce-manager'); ?></title>
<meta charset="utf-8" />
<meta http-equiv="Refresh" content="2; url=<?php echo $_SERVER['HTTP_REFERER']; ?>" />
</head>
<body style="text-align: center;">
<h2><?php _e('This product is no longer available.', 'commerce-manager'); ?></h2>
</body>
</html>
<?php } }

else {
switch ($_GET['action']) {
case 'activate':
global $wpdb;
if ($_GET['key'] == hash('sha256', $_GET['id'].commerce_data('encrypted_urls_key'))) {
$_GET['client_id'] = $_GET['id'];
$_GET['client_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_clients WHERE id = ".$_GET['id'], OBJECT);
if ($_GET['client_data']['status'] != 'active') {
$_GET['client_data']['status'] = 'active';
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_clients SET status = 'active' WHERE id = ".$_GET['id']);
if ((!defined('COMMERCE_MANAGER_DEMO')) || (COMMERCE_MANAGER_DEMO == false)) {
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = commerce_data('activation_confirmation_email_'.$field); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); }
if (commerce_data('activation_custom_instructions_executed') == 'yes') {
eval(format_instructions(commerce_data('activation_custom_instructions'))); } }
if (!headers_sent()) { header('Location: '.commerce_data('activation_confirmation_url')); exit(); } }
elseif (!headers_sent()) { header('Location: '.HOME_URL); exit; } }
elseif (!headers_sent()) { header('Location: '.HOME_URL); exit; } break;
case 'check-login':
global $wpdb;
$login = format_email_address($_GET['login']);
if (($login == '') || (is_numeric($login))) { $key = 'unavailable'; }
elseif ($login == $_SESSION['commerce_login']) { $key = 'available'; }
else {
$result = $wpdb->get_results("SELECT login FROM ".$wpdb->prefix."commerce_manager_clients WHERE login = '$login'", OBJECT);
if ($result) { $key = 'unavailable'; } else { $key = 'available'; } }
$options = (array) get_option('commerce_manager'.$_GET['form_id'].'_form');
$key .= '_login_indicator_message';
if ($options[$key] == '') { $message = commerce_data($key); }
else { $message = quotes_entities_decode(do_shortcode($options[$key])); }
echo $message; break;
case 'logout': commerce_logout(); if (!headers_sent()) { header('Location: '.HOME_URL); exit; } break;
default: if (isset($_GET['url'])) {
$url = commerce_decrypt_url($_SERVER['REQUEST_URI']);
if (!headers_sent()) { header('Location: '.$url); exit(); } }
else { if (!headers_sent()) { header('Location: '.HOME_URL); exit(); } } } }