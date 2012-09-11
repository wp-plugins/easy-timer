<?php $file = 'wp-load.php'; $i = 0;
while ((!file_exists($file)) && ($i < 8)) { $file = '../'.$file; $i = $i + 1; }
include_once $file;
global $wpdb;
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }

function get_paypal_custom_values() {
global $wpdb;
$data = explode('|', $_GET['custom']);
$_GET['code'] = $data[0];
$_GET['referrer'] = $data[1];
$_GET['sale_winner'] = $data[2];
$_GET['form_id'] = (int) $data[3];
$_GET['client_id'] = (int) $data[4];
$_GET['member_id'] = (int) $data[5];
$_GET['affiliate_id'] = (int) $data[6];
$_GET['user_id'] = (int) $data[7];
$_GET['lang'] = $data[8];
$_GET['ip_address'] = $data[9];
$_GET['referring_url'] = $data[10];
$_GET['user_agent'] = $data[11];
$_GET['commerce_form_id'] = $_GET['form_id'];
if (($_GET['referrer'] != '') && (!strstr($_GET['referrer'], '@'))) {
$_GET['affiliate_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_GET['referrer']."'", OBJECT);
$_GET['referrer_data'] = $_GET['affiliate_data']; } }

if ($action == 'order') {
foreach ($_GET as $key => $value) { if (is_string($value)) { $_GET[$key] = str_replace('&amp;amp;', '&amp;', htmlspecialchars($value)); } }
$_GET['referring_url'] = explode('&', $_GET['referring_url']);
$_GET['referring_url'] = $_GET['referring_url'][0];
$fields = array(
'address1' => $_GET['address'],
'business' => ($_GET['sale_winner'] == 'affiliate' ? (strstr($_GET['referrer'], '@') ? $_GET['referrer'] : referrer_data('paypal_email_address')) : product_data('paypal_email_address')),
'buyer_email' => ($_GET['paypal_email_address'] != '' ? $_GET['paypal_email_address'] : $_GET['email_address']),
'cancel_return' => $_GET['url'],
'charset' => get_bloginfo('charset'),
'city' => $_GET['town'],
'country' => $_GET['country'],
'currency_code' => $_GET['currency_code'],
'custom' => $_GET['code'].'|'.$_GET['referrer'].'|'.$_GET['sale_winner'].'|'.$_GET['form_id'].'|'.$_GET['client_id'].'|'.$_GET['member_id'].'|'.$_GET['affiliate_id'].'|'.$_GET['user_id'].'|'.$_GET['lang'].'|'.$_SERVER['REMOTE_ADDR'].'|'.$_GET['referring_url'].'|'.$_SERVER['HTTP_USER_AGENT'],
'email' => $_GET['email_address'],
'first_name' => $_GET['first_name'],
'image_url' => $_GET['thumbnail_url'],
'item_name' => $_GET['name'],
'item_number' => $_GET['id'],
'last_name' => $_GET['last_name'],
'lc' => $_GET['country_code'],
'night_phone_b' => $_GET['phone_number'],
'no_shipping' => ($_GET['shipping_address_required'] == 'yes' ? 0 : 1),
'notify_url' => COMMERCE_MANAGER_URL.'gateways/paypal.php',
'return' => $_GET['order_confirmation_url'],
'zip' => $_GET['postcode']);
if ($_GET['payment_option'] == 0) {
$fields = array_merge($fields, array(
'amount' => $_GET['net_price'],
'cmd' => '_xclick',
'no_note' => 0,
'quantity' => $_GET['quantity']));
if ($_GET['default_shipping_cost_applied'] == 'yes') {
$fields = array_merge($fields, array(
'weight' => $_GET['weight'],
'weight_unit' => ($_GET['weight_unit'] == 'kilogram' ? 'kgs' : 'lbs'))); }
else { $fields = array_merge($fields, array('shipping' => $_GET['shipping_cost'])); } }
else {
$_GET['a1'] = ($_GET['first_payment_amount_used'] == 'yes' ? $_GET['first_payment_amount'] : $_GET['payments_amount']) + $_GET['shipping_cost'];
$_GET['p1'] = ($_GET['first_payment_period_used'] == 'yes' ? $_GET['first_payment_period_quantity'] : $_GET['payments_period_quantity']);
$_GET['p3'] = $_GET['payments_period_quantity'];
$_GET['t1'] = ($_GET['first_payment_period_used'] == 'yes' ? $_GET['first_payment_period_time_unit'] : $_GET['payments_period_time_unit']);
$_GET['t1'] = strtoupper(substr($_GET['t1'], 0, 1));
$_GET['t3'] = strtoupper(substr($_GET['payments_period_time_unit'], 0, 1));
if ($_GET['payments_number'] == 2) { $_GET['payments_amount'] = round(50*($_GET['a1'] + $_GET['payments_amount']))/100; }
else {
$fields = array_merge($fields, array(
'a1' => $_GET['a1'],
'p1' => $_GET['p1'],
't1' => $_GET['t1'])); }
$fields = array_merge($fields, array(
'a3' => $_GET['payments_amount'],
'cmd' => '_xclick-subscriptions',
'no_note' => 1,
'p3' => $_GET['p3'],
'sra' => 1,
'src' => 1,
't3' => $_GET['t3'])); } ?>
<!DOCTYPE html>
<html>
<head>
<title><?php _e('Redirection to', 'commerce-manager'); ?> PayPal</title>
<meta charset="utf-8" />
</head>
<body style="text-align: center;">
<h2><?php _e('You will be redirected to PayPal.', 'commerce-manager'); ?></h2>
<form method="post" name="paypal" id="paypal" action="https://www.paypal.com/cgi-bin/webscr">
<div>
<?php foreach ($fields as $field => $value) { echo '<input type="hidden" name="'.$field.'" value="'.$value.'" />'."\n"; }
if ($_GET['payments_number'] > 1) { echo '<input type="hidden" name="srt" value="'.($_GET['payments_number'] == 2 ? 2 : $_GET['payments_number'] - 1).'" />'."\n"; }
if (($_GET['payment_option'] == 0) && (($_GET['tax_applied'] == 'no') || ($_GET['default_tax_applied'] == 'no'))) { echo '<input type="hidden" name="tax" value="'.$_GET['tax'].'" />'."\n"; } ?>
</div>
<p><?php _e('If you\'re not redirected within', 'commerce-manager'); ?> <span class="scountdown" id="t<?php echo time() + 6; ?>">6 <?php _e('seconds', 'commerce-manager'); ?></span>, <input type="submit" value="<?php _e('click here', 'commerce-manager'); ?>" /></p>
</form>
<script type="text/javascript">
document.forms['paypal'].submit();

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
<?php wp_footer(); ?>
</body>
</html>
<?php }

else {
foreach ($_POST as $key => $value) { if (is_string($value)) { $_POST[$key] = html_entity_decode($value); } }
$_GET = $_POST;
$req = 'cmd=_notify-validate';
foreach ($_POST as $key => $value) { if (is_string($value)) { $value = trim(urlencode(stripslashes($value))); $req .= '&'.$key.'='.$value; } }
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: ".strlen($req)."\r\n\r\n";
if (isset($_GET['item_number'])) { $_GET['product_id'] = $_GET['item_number']; }
else { $_GET['product_id'] = $_GET['item_number1']; }
$fp = fsockopen('www.paypal.com', 80, $errno, $errstr, 30);
if ($fp) {
fputs($fp, $header.$req);
while (!feof($fp)) {
$res = fgets($fp, 1024);
if (strcmp($res, 'VERIFIED') == 0) {

$_GET['action'] = 'commerce_notification';
if ((($_GET['txn_type'] == 'web_accept') || ($_GET['txn_type'] == 'subscr_signup') || ($_GET['txn_type'] == 'subscr_payment') || ($_GET['txn_type'] == 'recurring_payment')) && ($_GET['mc_currency'] == product_data('currency_code'))) {
$_GET['payment_mode'] = 'PayPal';
$_GET['email_address'] = format_email_address($_GET['payer_email']);
$_GET['paypal_email_address'] = $_GET['email_address'];
if ($_GET['business'] != '') { $_GET['receiver_account'] = $_GET['business']; }
else { $_GET['receiver_account'] = $_GET['receiver_email']; }
$_GET['date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_GET['date_utc'] = date('Y-m-d H:i:s');
get_paypal_custom_values();
if ($_GET['client_id'] > 0) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_clients WHERE id = ".$_GET['client_id'], OBJECT);
if ($result) { $_GET['client_data'] = (array) $result; } }
else {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_clients WHERE email_address = '".$_GET['email_address']."'", OBJECT);
if (!$result) { $result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_clients WHERE paypal_email_address = '".$_GET['paypal_email_address']."'", OBJECT); }
if ($result) { $_GET['client_data'] = (array) $result; $_GET['client_id'] = $result->id; } }
if ((function_exists('award_commission')) && (product_data('commission_payment') == 'deferred')) {
if (($_GET['referrer'] == '') && ($_GET['client_id'] > 0)) { $_GET['referrer'] = client_data('referrer'); }
if ($_GET['referrer'] == '') {
$result = $wpdb->get_row("SELECT referrer FROM ".$wpdb->prefix."commerce_manager_orders WHERE email_address = '".$_GET['email_address']."' ORDER BY date DESC LIMIT 1", OBJECT);
if ($result) { $_GET['referrer'] = $result->referrer; } }
if (($_GET['referrer'] != '') && (!strstr($_GET['referrer'], '@'))) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_GET['referrer']."' AND status = 'active'", OBJECT);
if (!$result) { $_GET['referrer'] = ''; }
else {
$_GET['affiliate_data'] = (array) $result;
$_GET['referrer_data'] = $_GET['affiliate_data']; } } }

if (($_GET['txn_type'] == 'web_accept') || ($_GET['txn_type'] == 'subscr_signup')) {
if ($_GET['address_name'] != '') { $line1 = $_GET['address_name']."\n"; }
if ($_GET['address_street'] != '') { $line2 = $_GET['address_street']."\n"; }
if ($_GET['address_zip'] != '') { $line3 = $_GET['address_zip']." "; }
if ($_GET['address_city'] != '') { $line3 .= $_GET['address_city']; }
if ($line3 != '') { $line3 .= "\n"; }
$line4 = $_GET['address_country'];
$_GET['shipping_address'] = $line1.$line2.$line3.$line4;
$_GET['status'] = product_data('orders_initial_status');
$_GET['first_name'] = format_name($_GET['first_name']);
$_GET['last_name'] = format_name($_GET['last_name']);
$_GET['address'] = $_GET['address_street'];
$_GET['postcode'] = $_GET['address_zip'];
$_GET['town'] = $_GET['address_city'];
$_GET['country'] = $_GET['address_country']; }

if ($_GET['txn_type'] == 'web_accept') {
switch ($_GET['payment_status']) {
case 'Completed': case 'Pending': case 'Processed': $valid = true; break;
default: $valid = false; }
if ($valid) {
if ((!isset($_GET['mc_gross'])) || ($_GET['mc_gross'] == 0)) { $_GET['mc_gross'] = $_GET['mc_gross_1']; }
if (!isset($_GET['mc_shipping'])) { $_GET['mc_shipping'] = $_GET['mc_shipping1']; }
if (!isset($_GET['shipping'])) { $_GET['shipping'] = $_GET['mc_shipping']; }
if (!isset($_GET['quantity'])) { $_GET['quantity'] = $_GET['quantity1']; }
if ($_GET['quantity'] < 1) { $_GET['quantity'] = 1; }
$_GET['price'] = $_GET['mc_gross'] - $_GET['shipping'];
if (product_data('default_tax_applied') == 'yes') { $_GET['tax_included_in_price'] = 'no'; }
else { $_GET['tax_included_in_price'] = product_data('tax_included_in_price'); }
if ($_GET['tax_included_in_price'] == 'no') { $_GET['price'] = $_GET['price'] - $_GET['tax']; }
$_GET['shipping_cost'] = $_GET['shipping'];
$_GET['amount'] = $_GET['mc_gross'];
$_GET['transaction_number'] = $_GET['txn_id'];
$_GET['transaction_cost'] = $_GET['mc_fee'];
$_GET['instructions'] = $_GET['memo'];
$_GET['payments_number'] = 0;
if ($_GET['referrer'] == '') { $_GET['commission_amount'] = 0; }
else {
if ($_GET['sale_winner'] == 'affiliate') {
$_GET['commission_amount'] = $_GET['amount'];
$_GET['commission_payment'] = 'instant';
$_GET['commission_status'] = 'paid';
$_GET['commission_payment_date'] = $_GET['date'];
$_GET['commission_payment_date_utc'] = $_GET['date_utc']; }
elseif (product_data('commission_payment') == 'deferred') { if (function_exists('award_commission')) { award_commission(); }
if ($_GET['commission_amount'] > 0) { $_GET['commission_payment'] = 'deferred'; $_GET['commission_status'] = 'unpaid'; } } }
if (($_GET['commission_amount'] == 0) || ($_GET['commission_amount'] == '')) { $_GET['commission_payment'] = ''; }
if (function_exists('award_commission2')) { award_commission2(); }
if ($_GET['price'] >= $_GET['quantity']*product_data('price')) {
$order = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."commerce_manager_orders WHERE payment_mode LIKE '%PayPal%' AND transaction_number = '".$_GET['transaction_number']."'", OBJECT);
if (!$order) { add_order($_GET); } } } }

if ($_GET['txn_type'] == 'subscr_signup') {
$_GET['quantity'] = 1;
$_GET['tax_included_in_price'] = product_data('tax_included_in_price');
$_GET['recurring_payments_profile_number'] = $_GET['subscr_id'];
if ((!isset($_GET['recur_times'])) || (!is_numeric($_GET['recur_times'])) || ($_GET['recur_times'] < 2)) { $_GET['payments_number'] = 'unlimited'; }
elseif ((isset($_GET['mc_amount1'])) && ($_GET['mc_amount1'] > 0)) { $_GET['payments_number'] = $_GET['recur_times'] + 1; }
else { $_GET['payments_number'] = $_GET['recur_times']; }
$_GET['payments_amount'] = $_GET['mc_amount3'];
if (isset($_GET['mc_amount1'])) { $_GET['first_payment_amount'] = $_GET['mc_amount1']; }
else { $_GET['first_payment_amount'] = $_GET['mc_amount3']; }
if ($_GET['first_payment_amount'] > 0) { $_GET['received_payments_number'] = 1; }
else { $_GET['received_payments_number'] = 0; }
$period = explode(' ', $_GET['period3']);
$_GET['payments_period_quantity'] = (int) $period[0];
switch ($period[1]) {
case 'D': $_GET['payments_period_time_unit'] = 'day'; break;
case 'W': $_GET['payments_period_time_unit'] = 'week'; break;
case 'M': $_GET['payments_period_time_unit'] = 'month'; break;
case 'Y': $_GET['payments_period_time_unit'] = 'year'; }
if (!isset($_GET['period1'])) {
$_GET['first_payment_period_quantity'] = $_GET['payments_period_quantity'];
$_GET['first_payment_period_time_unit'] = $_GET['payments_period_time_unit']; }
else {
$period = explode(' ', $_GET['period1']);
$_GET['first_payment_period_quantity'] = (int) $period[0];
switch ($period[1]) {
case 'D': $_GET['first_payment_period_time_unit'] = 'day'; break;
case 'W': $_GET['first_payment_period_time_unit'] = 'week'; break;
case 'M': $_GET['first_payment_period_time_unit'] = 'month'; break;
case 'Y': $_GET['first_payment_period_time_unit'] = 'year'; } }
$_GET['recurring_payments_profile_status'] = 'active';
if (($_GET['referrer'] != '') && (!strstr($_GET['referrer'], '@'))) {
$result = $wpdb->get_row("SELECT referrer FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_GET['referrer']."' AND status = 'active'", OBJECT);
if ($result) { $_GET['referrer2'] = $result->referrer; } }
if (($_GET['referrer'] != '') && (function_exists('award_commission'))) { award_commission(); }
$_GET['commission_amount'] = 0;
$_GET['commission_payment'] = '';
$_GET['commission2_amount'] = 0;
$order = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."commerce_manager_orders WHERE payment_mode LIKE '%PayPal%' AND recurring_payments_profile_number = '".$_GET['recurring_payments_profile_number']."'", OBJECT);
if (!$order) {
add_order($_GET);
$order = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."commerce_manager_orders WHERE payment_mode LIKE '%PayPal%' AND recurring_payments_profile_number = '".$_GET['recurring_payments_profile_number']."'", OBJECT);
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_recurring_payments SET order_id = ".$order->id." WHERE recurring_payments_profile_number = '".$_GET['recurring_payments_profile_number']."'");
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE order_id = ".$order->id, OBJECT);
$received_payments_number = (int) $row->total;
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_orders SET received_payments_number = ".$received_payments_number." WHERE id = ".$order->id); } }

if (($_GET['txn_type'] == 'subscr_payment') || ($_GET['txn_type'] == 'recurring_payment')) {
$_GET['recurring_payments_profile_number'] = $_GET['subscr_id'];
$order = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_orders WHERE payment_mode LIKE '%PayPal%' AND recurring_payments_profile_number = '".$_GET['recurring_payments_profile_number']."'", OBJECT);
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE payment_mode LIKE '%PayPal%' AND recurring_payments_profile_number = '".$_GET['recurring_payments_profile_number']."'", OBJECT);
$recurring_payments_number = (int) $row->total;
$_GET['payment-number'] = $recurring_payments_number + 1;
if ($_GET['payment-number'] > 1) { $_GET['shipping_cost'] = 0; }
else { $_GET['shipping_cost'] = product_data('shipping_cost'); }
$_GET['amount'] = $_GET['mc_gross'];
$_GET['price'] = $_GET['amount'] - $_GET['shipping_cost'];
$_GET['tax_applied'] = product_data('tax_applied');
$_GET['tax_included_in_price'] = product_data('tax_included_in_price');
if ($_GET['tax_applied'] == 'no') { $_GET['tax'] = 0; }
else {
$_GET['tax_percentage'] = product_data('tax_percentage');
$r = 1 + $_GET['tax_percentage']/100;
$_GET['net_price'] = round(100*$_GET['price']/$r)/100;
$_GET['tax'] = $_GET['price'] - $_GET['net_price'];
if ($_GET['tax_included_in_price'] == 'no') { $_GET['price'] = $_GET['net_price']; } }
$_GET['transaction_number'] = $_GET['txn_id'];
$_GET['transaction_cost'] = $_GET['mc_fee'];
$_GET['status'] = 'received';
if ($_GET['referrer'] == '') { $_GET['commission_amount'] = 0; }
else {
if ($recurring_payments_number == 0) {
if ($_GET['sale_winner'] == 'affiliate') { $_GET['commission_payment'] = 'instant'; } }
else {
$first_recurring_payment = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE payment_mode LIKE '%PayPal%' AND recurring_payments_profile_number = '".$_GET['recurring_payments_profile_number']."' ORDER BY date ASC LIMIT 1", OBJECT);
$_GET['commission_payment'] = $first_recurring_payment->commission_payment; }
if ($_GET['commission_payment'] == 'instant') {
$_GET['commission_amount'] = $_GET['amount'];
$_GET['commission_status'] = 'paid';
$_GET['commission_payment_date'] = $_GET['date'];
$_GET['commission_payment_date_utc'] = $_GET['date_utc']; }
elseif (product_data('commission_payment') == 'deferred') { if (function_exists('award_commission')) { award_commission(); }
if ($_GET['commission_amount'] > 0) { $_GET['commission_payment'] = 'deferred'; $_GET['commission_status'] = 'unpaid'; } } }
if (($_GET['commission_amount'] == 0) || ($_GET['commission_amount'] == '')) { $_GET['commission_payment'] = ''; }
if (function_exists('award_commission2')) { award_commission2(); }
$recurring_payment = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE payment_mode LIKE '%PayPal%' AND transaction_number = '".$_GET['transaction_number']."'", OBJECT);
if (!$recurring_payment) {
include '../tables.php';
$sql = commerce_sql_array($tables['recurring_payments'], $_GET);
foreach ($tables['recurring_payments'] as $key => $value) { if ($key != 'id') { $keys_list .= $key.","; $values_list .= $sql[$key].","; } }
$results = $wpdb->query("INSERT INTO ".$wpdb->prefix."commerce_manager_recurring_payments (".substr($keys_list, 0, -1).") VALUES(".substr($values_list, 0, -1).")");
$recurring_payment = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE payment_mode LIKE '%PayPal%' AND transaction_number = '".$_GET['transaction_number']."'", OBJECT);
$_GET['recurring_payment_id'] = $recurring_payment->id;
$_GET['recurring_payment_data'] = (array) $recurring_payment;
if ($order) {
$_GET['order_id'] = $order->id;
$_GET['order_data'] = (array) $order;
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_recurring_payments SET order_id = ".$order->id." WHERE recurring_payments_profile_number = '".$_GET['recurring_payments_profile_number']."'");
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE order_id = ".$order->id, OBJECT);
$received_payments_number = (int) $row->total;
$_GET['order_data']['received_payments_number'] = $received_payments_number;
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_orders SET received_payments_number = ".$received_payments_number." WHERE id = ".$order->id); }
foreach (array('confirmation', 'notification') as $string) {
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = product_data('recurring_payment_'.$string.'_email_'.$field); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); } }
if ((function_exists('referrer_data')) && ($_GET['referrer'] != '') && (!strstr($_GET['referrer'], '@'))) {
if (affiliation_data('recurring_payment_notification_email_deactivated') != 'yes') {
if (referrer_data('status') == 'active') {
$sent = referrer_data('recurring_payment_notification_email_sent');
if (($sent == 'yes') || (($sent == 'if commission') && ($_GET['commission_amount'] > 0))) {
foreach (array('sender', 'receiver', 'subject', 'body') as $field) {
$$field = affiliation_data('recurring_payment_notification_email_'.$field); }
wp_mail($receiver, $subject, $body, 'From: '.$sender); } } } }
if (product_data('recurring_payment_custom_instructions_executed') == 'yes') {
eval(format_instructions(product_data('recurring_payment_custom_instructions'))); } } } }

if (($_GET['payment_status'] == 'Canceled') || ($_GET['payment_status'] == 'Refunded') || ($_GET['payment_status'] == 'Reversed')) {
if (isset($_GET['subscr_id'])) { $type = 'recurring_payment'; $table = 'recurring_payments'; } else { $type = 'order'; $table = 'orders'; }
$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_".$table." WHERE payment_mode LIKE '%PayPal%' AND transaction_number = '".$_GET['parent_txn_id']."' AND status != 'refunded'", OBJECT);
if ($item) {
$_GET['product_id'] = $item->product_id;
$_GET['commerce_form_id'] = $item->form_id;
$_GET['client_id'] = $item->client_id;
$_GET['order_id'] = $item->order_id;
$_GET[$type.'_id'] = $item->id;
$_GET[$type.'_data'] = (array) $item;
if ($_GET['client_id'] > 0) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_clients WHERE id = ".$_GET['client_id'], OBJECT);
if ($result) { $_GET['client_data'] = (array) $result; } }
$refund_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$refund_date_utc = date('Y-m-d H:i:s');
if (($item->commission_payment == 'deferred') && ($item->commission_status == 'paid')) {
$commission_amount = $item->commission_amount;
$commission_payment = 'deferred';
$commission_status = 'paid';
$commission_payment_date = $item->commission_payment_date;
$commission_payment_date_utc = $item->commission_payment_date_utc; }
else {
$commission_amount = 0;
$commission_payment = '';
$commission_status = '';
$commission_payment_date = '0000-00-00 00:00:00';
$commission_payment_date_utc = '0000-00-00 00:00:00'; }
if ($item->commission2_status == 'paid') { $commission2_amount = $item->commission2_amount; $commission2_status = 'paid'; }
else { $commission2_amount = 0; $commission2_status = ''; }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_".$table." SET
	status = 'refunded',
	refund_date = '".$refund_date."',
	refund_date_utc = '".$refund_date_utc."',
	commission_amount = ".$commission_amount.",
	commission_payment = '".$commission_payment."',
	commission_status = '".$commission_status."',
	commission_payment_date = '".$commission_payment_date."',
	commission_payment_date_utc = '".$commission_payment_date_utc."',
	commission2_amount = ".$commission2_amount.",
	commission2_status = '".$commission2_status."' WHERE id = ".$item->id);

if ($type == 'order') {
$product = $wpdb->get_row("SELECT id, refunds_count FROM ".$wpdb->prefix."commerce_manager_products WHERE id = ".$item->product_id, OBJECT);
$refunds_count = $product->refunds_count + $item->quantity;
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_products SET refunds_count = ".$refunds_count." WHERE id = ".$product->id);
if ((function_exists('update_member_members_areas')) && (product_data('customer_unsubscribed_from_members_areas') == 'yes')) {
update_member_members_areas($item->member_id, product_data('customer_members_areas'), 'delete'); } }

foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = product_data($type.'_refund_notification_email_'.$field); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); }
if (product_data($type.'_refund_custom_instructions_executed') == 'yes') {
eval(format_instructions(product_data($type.'_refund_custom_instructions'))); } } }

if (($_GET['txn_type'] == 'subscr_cancel') || ($_GET['txn_type'] == 'subscr_eot')) {
$order = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_orders WHERE payment_mode LIKE '%PayPal%' AND recurring_payments_profile_number = '".$_GET['subscr_id']."' AND recurring_payments_profile_status != 'deactivated'", OBJECT);
if ($order) {
$_GET['product_id'] = $order->product_id;
$_GET['commerce_form_id'] = $item->form_id;
$_GET['client_id'] = $item->client_id;
$_GET['order_id'] = $order->id;
$_GET['order_data'] = (array) $order;
if ($_GET['client_id'] > 0) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_clients WHERE id = ".$_GET['client_id'], OBJECT);
if ($result) { $_GET['client_data'] = (array) $result; } }
$recurring_payments_profile_deactivation_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$recurring_payments_profile_deactivation_date_utc = date('Y-m-d H:i:s');
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_orders SET
	recurring_payments_profile_status = 'deactivated',
	recurring_payments_profile_deactivation_date = '".$recurring_payments_profile_deactivation_date."',
	recurring_payments_profile_deactivation_date_utc = '".$recurring_payments_profile_deactivation_date_utc."' WHERE id = ".$order->id);

if ((function_exists('update_member_members_areas')) && (product_data('customer_unsubscribed_from_members_areas') == 'yes')) {
update_member_members_areas($order->member_id, product_data('customer_members_areas'), 'delete'); }

foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = product_data('payments_profile_deactivation_notification_email_'.$field); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); }
if (product_data('payments_profile_deactivation_custom_instructions_executed') == 'yes') {
eval(format_instructions(product_data('payments_profile_deactivation_custom_instructions'))); } } }

} } fclose($fp); } }