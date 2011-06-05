<?php include_once '../../../../wp-load.php';
global $wpdb;
$affiliates_table_name = $wpdb->prefix.'affiliation_manager_affiliates';
$orders_table_name = $wpdb->prefix.'commerce_manager_orders';
$products_table_name = $wpdb->prefix.'commerce_manager_products';
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }

if ($action == 'order') {
if ($_GET['sale_winner'] == 'affiliate') {
if (strstr($_GET['referrer'], '@')) { $_GET['paypal_email_address'] = $_GET['referrer']; }
else { $_GET['paypal_email_address'] = affiliate_data('paypal_email_address'); } }
else { $_GET['paypal_email_address'] = product_data('paypal_email_address'); }
$fields = array(
'amount' => $_GET['net_price'],
'business' => $_GET['paypal_email_address'],
'cancel_return' => $_GET['url'],
'charset' => get_bloginfo('charset'),
'cmd' => '_xclick',
'currency_code' => $_GET['currency_code'],
'custom' => 'code='.$_GET['code'].'|referrer='.$_GET['referrer'].'|ip_address='.$_SERVER['REMOTE_ADDR'].'|referring_url='.$_GET['referring_url'].'|user_agent='.$_SERVER['HTTP_USER_AGENT'],
'image_url' => $_GET['thumbnail_url'],
'item_name' => $_GET['name'],
'item_number' => $_GET['id'],
'lc' => strtoupper(substr(WPLANG, 0, 2)),
'no_note' => 0,
'no_shipping' => ($_GET['shipping_address_required'] == 'yes' ? 0 : 1),
'notify_url' => COMMERCE_MANAGER_URL.'gateways/paypal.php',
'quantity' => $_GET['quantity'],
'return' => $_GET['order_confirmation_url'],
'shipping' => $_GET['shipping_cost'],
'tax' => $_GET['tax']); ?>
<!DOCTYPE html>
<html>
<head>
<title><?php _e('Redirection to', 'commerce-manager'); ?> PayPal</title>
<meta charset="utf-8" />
</head>
<body style="text-align: center;" onload="document.forms['paypal2'].submit();">
<h2><?php _e('You will be redirected to PayPal.', 'commerce-manager'); ?></h2>
<form method="post" id="paypal" action="https://www.<?php echo ($_GET['sandbox_enabled'] == 'yes' ? 'sandbox.' : ''); ?>paypal.com/cgi-bin/webscr">
<div><?php foreach ($fields as $field => $value) { echo '<input type="hidden" name="'.$field.'" value="'.$value.'" />'."\n"; } ?></div>
<p><?php _e('If you\'re not redirected within', 'commerce-manager'); ?> <span class="scountdown" id="t<?php echo time() + 6; ?>">6 <?php _e('seconds', 'commerce-manager'); ?></span>, <input type="submit" value="<?php _e('click here', 'commerce-manager'); ?>" /></p>
</form>
<script type="text/javascript">
function stimer_decrease(el) {
var T = Math.round(((new Date()).getTime())/1000);
var S = scountdown[el].id.substr(1) - T;
if (S < 1) { string = '0 '+'<?php _e('second', 'commerce-manager'); ?>'; }
else if (S == 1) { string = '1 '+'<?php _e('second', 'commerce-manager'); ?>'; }
else if (S > 1) { string = S+' '+'<?php _e('seconds', 'commerce-manager'); ?>'; }
scountdown[el].innerHTML = string; }

function start() {
if (document.getElementsByClassName == undefined) {
document.getElementsByClassName = function(className) {
var hasClassName = new RegExp('(?:^|\\s)' + className + '(?:$|\\s)');
var allElements = document.getElementsByTagName('*');
var results = [];
var element;
for (i = 0; (element = allElements[i]) != null; i++) {
var elementClass = element.className;
if (elementClass && elementClass.indexOf(className) != -1 && hasClassName.test(elementClass)) {
results.push(element); } }
return results; } }
scountdown = document.getElementsByClassName('scountdown');
for (el in scountdown) { setInterval('stimer_decrease('+el+')', 1000); } }

if (typeof(document.addEventListener) == 'function') {
document.addEventListener('DOMContentLoaded', start, false); }
else { window.onload = start; }
</script>
</body>
</html>
<?php }

else {
$_GET = $_POST;
$req = 'cmd=_notify-validate';
foreach ($_POST as $key => $value) { $value = trim(urlencode(stripslashes($value))); $req .= '&'.$key.'='.$value; }
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: ".strlen($req)."\r\n\r\n";
if (isset($_GET['item_number'])) { $_GET['product_id'] = $_GET['item_number']; }
else { $_GET['product_id'] = $_GET['item_number1']; }
$sandbox_enabled = product_data('sandbox_enabled');
$fp = fsockopen('www.'.($sandbox_enabled == 'yes' ? 'sandbox.' : '').'paypal.com', 80, $errno, $errstr, 30);

if ($fp) {
fputs($fp, $header.$req);
while (!feof($fp)) {
$res = fgets($fp, 1024);
if (strcmp($res, 'VERIFIED') == 0) {
if (($_GET['payment_status'] == 'Completed') && ($_GET['mc_currency'] == product_data('currency_code'))) {
if (!isset($_GET['mc_gross'])) { $_GET['mc_gross'] = $_GET['mc_gross_1']; }
if (!isset($_GET['mc_shipping'])) { $_GET['mc_shipping'] = $_GET['mc_shipping1']; }
$data = explode('|', $_GET['custom']);
if (!isset($_GET['quantity'])) { $_GET['quantity'] = $_GET['quantity1']; }
if ($_GET['quantity'] < 1) { $_GET['quantity'] = 1; }
$_GET['price'] = $_GET['mc_gross'] - $_GET['mc_shipping'];
$_GET['tax_included_in_price'] = product_data('tax_included_in_price');
if ($_GET['tax_included_in_price'] == 'no') { $_GET['price'] = $_GET['price'] - $_GET['tax']; }
$_GET['shipping_cost'] = $_GET['mc_shipping'];
$_GET['amount'] = $_GET['mc_gross'];
$_GET['payment_mode'] = 'PayPal';
$_GET['transaction_number'] = $_GET['txn_id'];
$_GET['transaction_cost'] = $_GET['mc_fee'];
$_GET['instructions'] = $_GET['memo'];
$_GET['shipping_address'] = $_GET['address_name']."\n".$_GET['address_street']."\n".$_GET['address_zip']." ".$_GET['address_city']."\n".$_GET['address_country'];
$_GET['date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_GET['date_utc'] = date('Y-m-d H:i:s');
$_GET['status'] = product_data('orders_initial_status');
$_GET['first_name'] = commerce_format_name($_GET['first_name']);
$_GET['last_name'] = commerce_format_name($_GET['last_name']);
$_GET['email_address'] = commerce_format_email_address($_GET['payer_email']);
$_GET['address'] = $_GET['address_street'];
$_GET['postcode'] = $_GET['address_zip'];
$_GET['town'] = $_GET['address_city'];
$_GET['country'] = $_GET['address_country'];
$_GET['ip_address'] = str_replace('ip_address=', '', $data[2]);
$_GET['user_agent'] = str_replace('user_agent=', '', $data[4]);
$_GET['referring_url'] = str_replace('referring_url=', '', $data[3]);
$_GET['referrer'] = str_replace('referrer=', '', $data[1]);
if (($_GET['referrer'] == '') || (!function_exists('award_commission'))) { $_GET['sale_winner'] = 'affiliator'; $_GET['commission_amount'] = 0; }
else { award_commission($_GET['price']); }
if ($_GET['commission_amount'] == 0) { $_GET['commission_payment'] = ''; }
elseif ($_GET['commission_payment'] == 'deferred') { $_GET['commission_status'] = 'unpaid'; }
elseif ($_GET['commission_payment'] == 'instant') {
$_GET['commission_status'] = 'paid';
$_GET['commission_payment_date'] = $_GET['date'];
$_GET['commission_payment_date_utc'] = $_GET['date_utc']; }
if ($_GET['sale_winner'] == 'affiliate') {
if (strstr($_GET['referrer'], '@')) { $_GET['paypal_email_address'] = $_GET['referrer']; }
else { $_GET['paypal_email_address'] = affiliate_data('paypal_email_address'); } }
else { $_GET['paypal_email_address'] = product_data('paypal_email_address'); }
if (!isset($_GET['business'])) { $_GET['business'] = $_GET['receiver_email']; }
if ((($_GET['business'] == $_GET['paypal_email_address']) || ($_GET['business'] == product_data('paypal_email_address'))) && ($_GET['price'] >= $_GET['quantity']*product_data('price'))) {
$order = $wpdb->get_row("SELECT * FROM $orders_table_name WHERE payment_mode LIKE '%PayPal%' AND transaction_number = '".$_GET['transaction_number']."'", OBJECT);
if (!$order) { add_order($_GET); } } }

elseif ($_GET['payment_status'] == 'Refunded') {
$order = $wpdb->get_row("SELECT * FROM $orders_table_name WHERE payment_mode LIKE '%PayPal%' AND transaction_number = '".$_GET['parent_txn_id']."'", OBJECT);
if ($order) {
$refund_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$refund_date_utc = date('Y-m-d H:i:s');
if ($order->commission_status == 'paid') {
$commission_amount = $order->commission_amount;
$commission_payment = $order->commission_payment;
$commission_status = $order->commission_status; }
else { $commission_amount = 0; $commission_payment = ''; $commission_status = ''; }
$results = $wpdb->query("UPDATE $orders_table_name SET
	status = 'refunded',
	refund_date = '".$refund_date."',
	refund_date_utc = '".$refund_date_utc."',
	commission_amount = '".$commission_amount."',
	commission_payment = '".$commission_payment."',
	commission_status = '".$commission_status."' WHERE id = '".$order->id."'");

$product = $wpdb->get_row("SELECT * FROM $products_table_name WHERE id = '".$order->product_id."'", OBJECT);
$row = $wpdb->get_row("SELECT SUM(quantity) AS total FROM $orders_table_name WHERE product_id = '".$product->id."'", OBJECT);
$sales_count = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(quantity) AS total FROM $orders_table_name WHERE product_id = '".$product->id."' AND status = 'refunded'", OBJECT);
$refunds_count = (int) $row->total;
$results = $wpdb->query("UPDATE $products_table_name SET
	sales_count = '".$sales_count."',
	refunds_count = '".$refunds_count."' WHERE id = '".$product->id."'"); } } }

} fclose($fp); } }