<?php if ($_GET['action'] != 'order') { if (!headers_sent()) { header('Location: ../'); exit(); } }
else {
$file = 'wp-load.php';
while ((!file_exists($file)) && ($i < 8)) { $file = '../'.$file; $i = $i + 1; }
include_once $file;
global $wpdb;
$affiliates_table_name = $wpdb->prefix.'affiliation_manager_affiliates';
$orders_table_name = $wpdb->prefix.'commerce_manager_orders';
$products_table_name = $wpdb->prefix.'commerce_manager_products';
$_GET = $_REQUEST;
$gateway = str_replace('_', '-', commerce_format_nice_name($_GET['gateway']));
if ($gateway == '') { $gateway = 'paypal'; }
$file = 'gateways/'.$gateway.'.php';

if (file_exists($file)) {
$action = 'order';
$_GET['referrer'] = $_COOKIE[AFFILIATION_COOKIES_NAME];
$_GET['available_quantity'] = product_data('available_quantity');
if (($_GET['available_quantity'] == 'unlimited') || ($_GET['available_quantity'] > 0)) { $_GET['available'] = 'yes'; }
else { $_GET['available'] = 'no'; }
if ($_GET['available'] == 'yes') {
if (($_GET['referrer'] == '') || (!function_exists('award_commission'))) { $_GET['sale_winner'] = 'affiliator'; }
else { award_commission(); }
$fields = array(
'currency_code',
'description',
'downloadable',
'id',
'name',
'order_confirmation_url',
'price',
'reference',
'sandbox_enabled',
'shipping_address_required',
'shipping_cost',
'tax_applied',
'tax_included_in_price',
'tax_percentage',
'thumbnail_url',
'url');
foreach ($fields as $field) { $_GET[$field] = product_data($field); }
if ($_GET['tax_applied'] == 'no') { $_GET['tax'] = 0; $_GET['net_price'] = $_GET['price']; }
else {
if ($_GET['tax_included_in_price'] == 'yes') {
$r = 1 + $_GET['tax_percentage']/100;
$_GET['net_price'] = round(100*$_GET['price']/$r)/100;
$_GET['tax'] = $_GET['quantity']*($_GET['price'] - $_GET['net_price']); }
else {
$_GET['net_price'] = $_GET['price'];
$_GET['tax'] = round($_GET['quantity']*$_GET['tax_percentage']*$_GET['price'])/100; } }
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