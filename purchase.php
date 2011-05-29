<?php include_once '../../../wp-load.php';
global $wpdb;
$affiliates_table_name = $wpdb->prefix.'affiliation_manager_affiliates';
$orders_table_name = $wpdb->prefix.'commerce_manager_orders';
$products_table_name = $wpdb->prefix.'commerce_manager_products';
$_GET = $_REQUEST;
$gateway = str_replace('_', '-', commerce_format_nice_name($_GET['gateway']));
$filename = 'gateways/'.$gateway.'.php';

if (file_exists($filename)) {
$_GET['action'] = 'purchase';
$_GET['referrer'] = $_COOKIE[AFFILIATION_COOKIES_NAME];
$_GET['available_quantity'] = product_data('available_quantity');
if (($_GET['available_quantity'] == 'unlimited') || ($_GET['available_quantity'] > 0)) { $_GET['available'] = 'yes'; }
else { $_GET['available'] = 'no'; }
if ($_GET['available'] == 'yes') {
if (!function_exists('affiliate_data')) { $_GET['affiliation_enabled'] = 'no'; }
else { $_GET['affiliation_enabled'] = product_data('affiliation_enabled'); }
$_GET['commission_payment'] = product_data('commission_payment');
if (($_GET['affiliation_enabled'] == 'no') || ($_GET['commission_payment'] == 'deferred')) { $_GET['sale_winner'] = 'affiliator'; }
else {
if (!strstr($_GET['referrer'], '@')) { $_GET['affiliate_data'] = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE login = '".$_GET['referrer']."'", OBJECT); }
$fields = array(
'commission_amount',
'commission_percentage',
'commission_type',
'first_sale_winner',
'registration_required');
foreach ($fields as $key => $field) { $_GET[$field] = product_data($field); }
if ((strstr($_GET['referrer'], '@')) && ($_GET['registration_required'] == 'yes')) { $_GET['sale_winner'] = 'affiliator'; }
else {
if ($_GET['commission_type'] == 'constant') { $_GET['commission_percentage'] = 100*($_GET['commission_amount'])/($_GET['price']); }
if ($_GET['commission_percentage'] == 0) { $_GET['sale_winner'] = 'affiliator'; }
if ($_GET['commission_percentage'] > 0) {
$row = $wpdb->get_row("SELECT SUM(price) AS total FROM $orders_table_name WHERE product_id = '".$_GET['product_id']."' AND referrer = '".$_GET['referrer']."'", OBJECT);
$total_price = (double) $row->total;
if ($total_price == 0) {
if ($_GET['first_sale_winner'] == 'affiliate') { $_GET['sale_winner'] = 'affiliate'; }
else { $_GET['sale_winner'] = 'affiliator'; } }
if ($total_price > 0) {
$row = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM $orders_table_name WHERE product_id = '".$_GET['product_id']."' AND referrer = '".$_GET['referrer']."'", OBJECT);
$commissions_total_amount = (double) $row->total;
if ($_GET['first_sale_winner'] == 'affiliate') {
if ($_GET['commission_percentage'] >= 100*$commissions_total_amount/$total_price) { $_GET['sale_winner'] = 'affiliate'; }
else { $_GET['sale_winner'] = 'affiliator'; } }
if ($_GET['first_sale_winner'] == 'affiliator') {
if ($_GET['commission_percentage'] > 100*$commissions_total_amount/$total_price) { $_GET['sale_winner'] = 'affiliate'; }
else { $_GET['sale_winner'] = 'affiliator'; } } } } } }
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
foreach ($fields as $key => $field) { $_GET[$field] = product_data($field); }
if ($_GET['tax_applied'] == 'no') { $_GET['tax'] = 0; $_GET['net_price'] = $_GET['price']; }
else {
if ($_GET['tax_included_in_price'] == 'yes') {
$r = 1 + $_GET['tax_percentage']/100;
$_GET['net_price'] = round(100*$_GET['price']/$r)/100;
$_GET['tax'] = $_GET['quantity']*($_GET['price'] - $_GET['net_price']); }
else {
$_GET['net_price'] = $_GET['price'];
$_GET['tax'] = round($_GET['quantity']*$_GET['tax_percentage']*$_GET['price'])/100; } }
include_once $filename; } }

if ($_GET['available'] == 'no') { ?>
<!DOCTYPE html>
<html>
<head>
<title><?php _e('Unavailable Product', 'commerce-manager'); ?></title>
<meta charset="utf-8" />
</head>
<body style="text-align: center;">
<h2><?php _e('This product is no longer available.', 'commerce-manager'); ?></h2>
</body>
</html>
<?php }