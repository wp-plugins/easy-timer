<?php global $wpdb;
$back_office_options = get_option('commerce_manager_back_office');
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }

if ((isset($_GET['id'])) && ($_GET['action'] == 'delete')) {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!commerce_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'commerce-manager'); }
else {
$order_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_orders WHERE id = ".$_GET['id'], OBJECT);
$results = $wpdb->query("DELETE FROM ".$wpdb->prefix."commerce_manager_orders WHERE id = ".$_GET['id']);
$product_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_products WHERE id = ".$order_data->product_id, OBJECT);
if (is_numeric($product_data->available_quantity)) { $available_quantity = $product_data->available_quantity + $order_data->quantity; }
else { $available_quantity = 'unlimited'; }
$sales_count = $product_data->sales_count - $order_data->quantity;
if ($order_data->status == 'refunded') { $refunds_count = $product_data->refunds_count - $order_data->quantity; }
else { $refunds_count = $product_data->refunds_count; }
foreach (array('sales_count', 'refunds_count') as $variable) { if ($$variable < 0) { $$variable = 0; } }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_products SET
	available_quantity = '".$available_quantity."',
	sales_count = ".$sales_count.",
	refunds_count = ".$refunds_count." WHERE id = ".$product_data->id);
if ($order_data->form_id > 0) {
$commerce_form_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_forms WHERE id = ".$order_data->form_id, OBJECT);
$orders_count = $commerce_form_data->orders_count - 1;
if ($orders_count < 0) { $orders_count = 0; }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_forms SET orders_count = ".$orders_count." WHERE id = ".$commerce_form_data->id); }
if ((!defined('COMMERCE_MANAGER_DEMO')) || (COMMERCE_MANAGER_DEMO == false)) {
if (commerce_data('order_removal_custom_instructions_executed') == 'yes') {
eval(format_instructions(commerce_data('order_removal_custom_instructions'))); } } } } ?>
<div class="wrap">
<div id="poststuff">
<?php commerce_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) {
echo '<div class="updated"><p><strong>'.__('Order deleted.', 'commerce-manager').'</strong></p></div>
<script type="text/javascript">setTimeout(\'window.location = "admin.php?page=commerce-manager-orders"\', 2000);</script>'; } ?>
<?php commerce_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php _e('Do you really want to permanently delete this order?', 'commerce-manager'); ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'commerce-manager'); ?>" />
</div>
<div class="clear"></div>
</form><?php } ?>
</div>
</div><?php }

else {
include 'admin-pages.php';
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!commerce_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'commerce-manager'); }
else {
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = stripslashes(html_entity_decode(str_replace('&nbsp;', ' ', $value))); } }
$back_office_options = update_commerce_manager_back_office($back_office_options, 'order');

if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
foreach (array('client', 'form', 'product') as $item) {
$_POST[$item.'_id'] = (int) $_POST[$item.'_id'];
if (($item == 'product') && ($_POST[$item.'_id'] < 1)) { $_POST[$item.'_id'] = 1; }
$_GET[$item.'_id'] = $_POST[$item.'_id'];
if ($_GET[$item.'_id'] > 0) { $_GET[$item.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_".$item."s WHERE id = ".$_GET[$item.'_id'], OBJECT); } }
$_GET['commerce_form_id'] = $_GET['form_id'];
$_GET['commerce_form_data'] = $_GET['form_data'];
$_POST['quantity'] = (int) $_POST['quantity']; if ($_POST['quantity'] < 1) { $_POST['quantity'] = 1; }
$_POST['price'] = str_replace(array('?', ',', ';'), '.', $_POST['price']);
if ($_POST['price'] == '') { $_POST['price'] = $_POST['quantity']*product_data('price'); }
else { $_POST['price'] = round(100*$_POST['price'])/100; }
if ($_POST['tax_included_in_price'] == '') { $_POST['tax_included_in_price'] = product_data('tax_included_in_price'); }
if ($_POST['tax'] == '') {
if (product_data('tax_applied') == 'no') { $_POST['tax'] = 0; }
else {
if ($_POST['tax_included_in_price'] == 'yes') {
$r = 1 + product_data('tax_percentage')/100;
$_POST['net_price'] = round(100*$_POST['price']/$r)/100;
$_POST['tax'] = $_POST['price'] - $_POST['net_price']; }
else {
$_POST['net_price'] = $_POST['price'];
$_POST['tax'] = round(product_data('tax_percentage')*$_POST['price'])/100; } } }
else { $_POST['tax'] = str_replace(array('?', ',', ';'), '.', $_POST['tax']);
$_POST['tax'] = round(100*$_POST['tax'])/100;
if ($_POST['tax'] < 0) { $_POST['tax'] = 0; } }
if ($_POST['shipping_cost'] == '') { $_POST['shipping_cost'] = product_data('shipping_cost'); }
else { $_POST['shipping_cost'] = str_replace(array('?', ',', ';'), '.', $_POST['shipping_cost']);
$_POST['shipping_cost'] = round(100*$_POST['shipping_cost'])/100;
if ($_POST['shipping_cost'] < 0) { $_POST['shipping_cost'] = 0; } }
$_POST['amount'] = str_replace(array('?', ',', ';'), '.', $_POST['amount']);
$_POST['amount'] = round(100*$_POST['amount'])/100;
if ($_POST['amount'] <= 0) {
$_POST['amount'] = $_POST['price'] + $_POST['shipping_cost'];
if ($_POST['tax_included_in_price'] == 'no') { $_POST['amount'] = $_POST['amount'] + $_POST['tax']; } }
$_POST['transaction_cost'] = str_replace(array('?', ',', ';'), '.', $_POST['transaction_cost']);
$_POST['transaction_cost'] = round(100*$_POST['transaction_cost'])/100;
if ($_POST['transaction_cost'] < 0) { $_POST['transaction_cost'] = 0; }
if ($_POST['date'] == '') {
$_POST['date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['date_utc'] = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['date'], 0, PREG_SPLIT_NO_EMPTY);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['date'] = date('Y-m-d H:i:s', $time);
$_POST['date_utc'] = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); }
if ($_POST['status'] == 'refunded') {
if ($_POST['refund_date'] == '') {
$_POST['refund_date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['refund_date_utc'] = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['refund_date'], 0, PREG_SPLIT_NO_EMPTY);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['refund_date'] = date('Y-m-d H:i:s', $time);
$_POST['refund_date_utc'] = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); } }
else { $_POST['refund_date'] = ''; }
switch ($_POST['payments_number']) {
case '' : case 'i' : case 'infinite' : case 'u' : $_POST['payments_number'] = 'unlimited'; break;
default: $_POST['payments_number'] = (int) $_POST['payments_number']; if ($_POST['payments_number'] < 0) { $_POST['payments_number'] = 0; } }
$_POST['received_payments_number'] = (int) $_POST['received_payments_number']; if ($_POST['received_payments_number'] < 0) { $_POST['received_payments_number'] = 0; }
if ((is_numeric($_POST['payments_number'])) && ($_POST['payments_number'] < $_POST['received_payments_number'])) { $_POST['payments_number'] = $_POST['received_payments_number']; }
foreach (array('first_payment_amount', 'payments_amount') as $field) {
$_POST[$field] = str_replace(array('?', ',', ';'), '.', $_POST[$field]);
$_POST[$field] = round(100*$_POST[$field])/100;
if ($_POST[$field] < 0) { $_POST[$field] = 0; } }
foreach (array('first_payment_period_quantity', 'payments_period_quantity') as $field) { $_POST[$field] = (int) $_POST[$field]; if ($_POST[$field] < 1) { $_POST[$field] = 1; } }
if (((is_numeric($_POST['payments_number'])) && ($_POST['payments_number'] == 0)) || ($_POST['payments_amount'] == 0)) {
$_POST['recurring_payments_profile_status'] = '';
$_POST['recurring_payments_profile_deactivation_date'] = ''; }
elseif ($_POST['recurring_payments_profile_status'] == '') { $_POST['recurring_payments_profile_status'] = 'active'; }
if ($_POST['recurring_payments_profile_status'] == 'deactivated') {
if ($_POST['recurring_payments_profile_deactivation_date'] == '') {
$_POST['recurring_payments_profile_deactivation_date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['recurring_payments_profile_deactivation_date_utc'] = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['recurring_payments_profile_deactivation_date'], 0, PREG_SPLIT_NO_EMPTY);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['recurring_payments_profile_deactivation_date'] = date('Y-m-d H:i:s', $time);
$_POST['recurring_payments_profile_deactivation_date_utc'] = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); } }
else { $_POST['recurring_payments_profile_deactivation_date'] = ''; }
$_POST['email_address'] = format_email_address($_POST['email_address']);
if (is_numeric($_POST['referrer'])) {
$_POST['referrer'] = preg_replace('/[^0-9]/', '', $_POST['referrer']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE id = ".$_POST['referrer'], OBJECT);
if ($result) { $_POST['referrer'] = $result->login; } }
elseif (strstr($_POST['referrer'], '@')) {
$_POST['referrer'] = format_email_address($_POST['referrer']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE paypal_email_address = '".$_POST['referrer']."'", OBJECT);
if ($result) { $_POST['referrer'] = $result->login; }
else { $result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".$_POST['referrer']."'", OBJECT);
if ($result) { $_POST['referrer'] = $result->login; } } }
else {
$_POST['referrer'] = format_nice_name($_POST['referrer']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['referrer']."'", OBJECT);
if (!$result) { $_POST['referrer'] = ''; } }
if (($_POST['referrer'] != '') && (!strstr($_POST['referrer'], '@'))) {
$_GET['affiliate_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['referrer']."'", OBJECT);
$_GET['referrer_data'] = $_GET['affiliate_data']; }
if ($_POST['referrer'] == '') {
$_POST['commission_amount'] = 0;
$_POST['commission_payment'] = '';
$_POST['commission_status'] = '';
$_POST['commission_payment_date'] = ''; }
else {
$_POST['commission_amount'] = str_replace(array('?', ',', ';'), '.', $_POST['commission_amount']);
$_POST['commission_amount'] = round(100*$_POST['commission_amount'])/100; if ($_POST['commission_amount'] <= 0) { $_POST['commission_amount'] = 0; }
if ($_POST['commission_amount'] == 0) { $_POST['commission_payment'] = ''; }
elseif ($_POST['commission_payment'] == '') { $_POST['commission_payment'] = 'deferred'; }
if ($_POST['commission_payment'] == '') { $_POST['commission_status'] = ''; }
elseif ($_POST['commission_payment'] == 'instant') {
$_POST['commission_status'] = 'paid';
$_POST['commission_payment_date'] = $_POST['date'];
$_POST['commission_payment_date_utc'] = $_POST['date_utc']; }
elseif ($_POST['commission_status'] == '') { $_POST['commission_status'] = 'unpaid'; }
if ($_POST['commission_status'] == 'paid') {
if ($_POST['commission_payment_date'] == '') {
$_POST['commission_payment_date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['commission_payment_date_utc'] = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['commission_payment_date'], 0, PREG_SPLIT_NO_EMPTY);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['commission_payment_date'] = date('Y-m-d H:i:s', $time);
$_POST['commission_payment_date_utc'] = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); } }
else { $_POST['commission_payment_date'] = ''; }
if ((($_POST['status'] == 'refunded') && ($_POST['commission_status'] != 'paid')) || ((strstr($_POST['referrer'], '@')) && ($_POST['commission_payment'] == 'deferred'))) {
$_POST['commission_amount'] = 0;
$_POST['commission_payment'] = '';
$_POST['commission_status'] = '';
$_POST['commission_payment_date'] = ''; } }

if (($_POST['referrer2'] == '') && ($_POST['referrer'] != '') && (!strstr($_POST['referrer'], '@'))) {
$result = $wpdb->get_row("SELECT referrer FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['referrer']."'", OBJECT);
if ($result) { $_POST['referrer2'] = $result->referrer; } }
else {
if (is_numeric($_POST['referrer2'])) {
$_POST['referrer2'] = preg_replace('/[^0-9]/', '', $_POST['referrer2']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE id = ".$_POST['referrer2'], OBJECT);
if ($result) { $_POST['referrer2'] = $result->login; } }
elseif (strstr($_POST['referrer2'], '@')) {
$_POST['referrer2'] = format_email_address($_POST['referrer2']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE paypal_email_address = '".$_POST['referrer2']."'", OBJECT);
if ($result) { $_POST['referrer2'] = $result->login; }
else { $result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".$_POST['referrer2']."'", OBJECT);
if ($result) { $_POST['referrer2'] = $result->login; } } }
else {
$_POST['referrer2'] = format_nice_name($_POST['referrer2']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['referrer2']."'", OBJECT);
if (!$result) { $_POST['referrer2'] = ''; } } }
if ($_POST['referrer2'] == '') {
$_POST['commission2_amount'] = 0;
$_POST['commission2_status'] = '';
$_POST['commission2_payment_date'] = ''; }
else {
$_POST['commission2_amount'] = str_replace(array('?', ',', ';'), '.', $_POST['commission2_amount']);
$_POST['commission2_amount'] = round(100*$_POST['commission2_amount'])/100; if ($_POST['commission2_amount'] <= 0) { $_POST['commission2_amount'] = 0; }
if ($_POST['commission2_amount'] == 0) {
$_POST['commission2_status'] = '';
$_POST['commission2_payment_date'] = ''; }
elseif ($_POST['commission2_status'] == '') { $_POST['commission2_status'] = 'unpaid'; }
if ($_POST['commission2_status'] == 'paid') {
if ($_POST['commission2_payment_date'] == '') {
$_POST['commission2_payment_date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['commission2_payment_date_utc'] = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['commission2_payment_date'], 0, PREG_SPLIT_NO_EMPTY);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['commission2_payment_date'] = date('Y-m-d H:i:s', $time);
$_POST['commission2_payment_date_utc'] = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); } }
else { $_POST['commission2_payment_date'] = ''; }
if (($_POST['status'] == 'refunded') && ($_POST['commission2_status'] != 'paid')) {
$_POST['commission2_amount'] = 0;
$_POST['commission2_status'] = '';
$_POST['commission2_payment_date'] = ''; } }

if ($_POST['commission_payment'] == 'instant') {
$_POST['payment_mode'] = 'PayPal';
if (strstr($_POST['referrer'], '@')) { $_POST['receiver_account'] = $_POST['referrer']; }
elseif ($_POST['receiver_account'] == '') { $_POST['receiver_account'] = referrer_data('paypal_email_address'); } }
elseif ($_POST['receiver_account'] == '') {
switch ($_POST['payment_mode']) {
case 'PayPal': $_POST['receiver_account'] = product_data('paypal_email_address'); break; } }

if (!isset($_GET['id'])) {
if ($_POST['referring_url'] == '') { $_POST['referring_url'] = $_SERVER['HTTP_REFERER']; }
if (isset($_POST['update_fields'])) {
foreach ($_POST as $key => $value) { $_GET['order_data'][$key] = $value; }
$_GET['order_data']['id'] = '{order id}';
foreach ($add_order_fields as $field) { $_POST[$field] = str_replace('{order id}', '[order id]', product_data($field)); } }
else {
$members_areas = array_unique(preg_split('#[^0-9]#', $_POST['customer_members_areas'], 0, PREG_SPLIT_NO_EMPTY));
sort($members_areas, SORT_NUMERIC);
foreach ($members_areas as $member_area) { $members_areas_list .= $member_area.', '; }
$_POST['customer_members_areas'] = substr($members_areas_list, 0, -2);
if (($_POST['email_address'] == '') || ($_POST['first_name'] == '') || ($_POST['last_name'] == '')) {
$error .= ' '.__('Please fill out the required fields.', 'commerce-manager'); }
if ($error == '') {
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."commerce_manager_orders WHERE date = '".$_POST['date']."' AND product_id = ".$_POST['product_id']." AND email_address = '".$_POST['email_address']."'", OBJECT);
if (!$result) { $updated = true; add_order($_POST); } } } }

if (isset($_GET['id'])) {
$updated = true;
if ((isset($_POST['count_payments'])) || (isset($_POST['count_payments_of_all_orders']))) {
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE order_id = ".$_GET['id'], OBJECT);
$_POST['received_payments_number'] = (int) $row->total;
if ((is_numeric($_POST['payments_number'])) && ($_POST['payments_number'] < $_POST['received_payments_number'])) { $_POST['payments_number'] = $_POST['received_payments_number']; }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_orders SET
	payments_number = ".$_POST['payments_number'].",
	received_payments_number = ".$_POST['received_payments_number']." WHERE id = ".$_GET['id']); }
if (isset($_POST['count_payments_of_all_orders'])) {
$orders = $wpdb->get_results("SELECT id, payments_number FROM ".$wpdb->prefix."commerce_manager_orders WHERE id != ".$_GET['id'], OBJECT);
if ($orders) { foreach ($orders as $order) {
$payments_number = $order->payments_number;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE order_id = ".$order->id, OBJECT);
$received_payments_number = (int) $row->total;
if ((is_numeric($payments_number)) && ($payments_number < $received_payments_number)) { $payments_number = $received_payments_number; }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_orders SET
	payments_number = ".$payments_number.",
	received_payments_number = ".$received_payments_number." WHERE id = ".$order->id); } } }
$order_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_orders WHERE id = ".$_GET['id'], OBJECT);
if ($_POST['first_name'] != '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_orders SET first_name = '".$_POST['first_name']."' WHERE id = ".$_GET['id']); }
if ($_POST['last_name'] != '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_orders SET last_name = '".$_POST['last_name']."' WHERE id = ".$_GET['id']); }
if ($_POST['email_address'] != '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_orders SET email_address = '".$_POST['email_address']."' WHERE id = ".$_GET['id']); }
include 'tables.php';
$sql = commerce_sql_array($tables['orders'], $_POST);
foreach ($tables['orders'] as $key => $value) { switch ($key) {
case 'id': case 'first_name': case 'last_name': case 'email_address': case 'member_id': break;
default: $list .= $key." = ".$sql[$key].","; } }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_orders SET ".substr($list, 0, -1)." WHERE id = ".$_GET['id']);
if ((!defined('COMMERCE_MANAGER_DEMO')) || (COMMERCE_MANAGER_DEMO == false)) {
if ($_POST['status'] == 'processed') {
if ($_POST['old_status'] != 'processed') {
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = stripslashes($_POST['order_processing_notification_email_'.$field]); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); }
if (product_data('order_processing_custom_instructions_executed') == 'yes') {
eval(format_instructions(product_data('order_processing_custom_instructions'))); } } }
elseif ($_POST['status'] == 'refunded') {
if ($_POST['old_status'] != 'refunded') {
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = stripslashes($_POST['order_refund_notification_email_'.$field]); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); }
if (product_data('order_refund_custom_instructions_executed') == 'yes') {
eval(format_instructions(product_data('order_refund_custom_instructions'))); } } }
if (($_POST['recurring_payments_profile_status'] == 'inactive') || ($_POST['recurring_payments_profile_status'] == 'deactivated')) {
if (($_POST['old_recurring_payments_profile_status'] != 'inactive') && ($_POST['old_recurring_payments_profile_status'] != 'deactivated')) {
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = stripslashes($_POST['payments_profile_deactivation_notification_email_'.$field]); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); }
if (product_data('payments_profile_deactivation_custom_instructions_executed') == 'yes') {
eval(format_instructions(product_data('payments_profile_deactivation_custom_instructions'))); } } } }
if ((($_POST['status'] == 'refunded') && ($_POST['old_status'] != 'refunded')) || (($_POST['recurring_payments_profile_status'] == 'deactivated') && ($_POST['old_recurring_payments_profile_status'] != 'deactivated'))) {
if ((function_exists('update_member_members_areas')) && (product_data('customer_unsubscribed_from_members_areas') == 'yes')) {
update_member_members_areas(order_data(array('member_id', 'id' => $_GET['id'])), product_data('customer_members_areas'), 'delete'); } }

if ($_POST['product_id'] == $order_data->product_id) {
if (is_numeric($_GET['product_data']['available_quantity'])) {
$available_quantity = $_GET['product_data']['available_quantity'] - $_POST['quantity'] + $order_data->quantity;
if ($available_quantity < 0) { $available_quantity = 0; } }
else { $available_quantity = 'unlimited'; }
$sales_count = $_GET['product_data']['sales_count'] + $_POST['quantity'] - $order_data->quantity;
if (($order_data->status != 'refunded') && ($_POST['status'] != 'refunded')) { $refunds_count = $_GET['product_data']['refunds_count']; }
if (($order_data->status != 'refunded') && ($_POST['status'] == 'refunded')) { $refunds_count = $_GET['product_data']['refunds_count'] + $_POST['quantity']; }
if (($order_data->status == 'refunded') && ($_POST['status'] == 'refunded')) { $refunds_count = $_GET['product_data']['refunds_count'] + $_POST['quantity'] - $order_data->quantity; }
if (($order_data->status == 'refunded') && ($_POST['status'] != 'refunded')) { $refunds_count = $_GET['product_data']['refunds_count'] - $order_data->quantity; }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_products SET
	available_quantity = '".$available_quantity."',
	sales_count = ".$sales_count.",
	refunds_count = ".$refunds_count." WHERE id = ".$_POST['product_id']); }
	
if ($_POST['product_id'] != $order_data->product_id) {
$product_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_products WHERE id = ".$order_data->product_id, OBJECT);
if (is_numeric($product_data->available_quantity)) { $available_quantity = $product_data->available_quantity + $order_data->quantity; }
else { $available_quantity = 'unlimited'; }
$sales_count = $product_data->sales_count - $order_data->quantity;
if ($order_data->status == 'refunded') { $refunds_count = $product_data->refunds_count - $order_data->quantity; }
else { $refunds_count = $product_data->refunds_count; }
foreach (array('sales_count', 'refunds_count') as $variable) { if ($$variable < 0) { $$variable = 0; } }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_products SET
	available_quantity = '".$available_quantity."',
	sales_count = ".$sales_count.",
	refunds_count = ".$refunds_count." WHERE id = ".$order_data->product_id);
	
if (is_numeric($_GET['product_data']['available_quantity'])) { $available_quantity = $_GET['product_data']['available_quantity'] - $_POST['quantity'];
if ($available_quantity < 0) { $available_quantity = 0; } }
else { $available_quantity = 'unlimited'; }
$sales_count = $_GET['product_data']['sales_count'] + $_POST['quantity'];
if ($_POST['status'] == 'refunded') { $refunds_count = $_GET['product_data']['refunds_count'] + $_POST['quantity']; }
else { $refunds_count = $_GET['product_data']['refunds_count']; }
foreach (array('sales_count', 'refunds_count') as $variable) { if ($$variable < 0) { $$variable = 0; } }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_products SET
	available_quantity = '".$available_quantity."',
	sales_count = ".$sales_count.",
	refunds_count = ".$refunds_count." WHERE id = ".$_POST['product_id']); }

if ($_POST['form_id'] != $order_data->form_id) {
if ($order_data->form_id > 0) {
$commerce_form_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_forms WHERE id = ".$order_data->form_id, OBJECT);
$orders_count = $commerce_form_data->orders_count - 1;
if ($orders_count < 0) { $orders_count = 0; }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_forms SET orders_count = ".$orders_count." WHERE id = ".$order_data->form_id); }

if ($_POST['form_id'] > 0) {
$displays_count = $_GET['commerce_form_data']['displays_count'];
$orders_count = $_GET['commerce_form_data']['orders_count'] + 1;
if ($displays_count < $orders_count) { $displays_count = $orders_count; }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_forms SET
	displays_count = ".$displays_count.",
	orders_count = ".$orders_count." WHERE id = ".$_POST['form_id']); } } } } }

if (isset($_GET['id'])) {
$order_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_orders WHERE id = ".$_GET['id'], OBJECT);
if ($order_data) {
$_GET['order_data'] = (array) $order_data;
$_GET['order_id'] = $order_data->id;
$_GET['client_id'] = $order_data->client_id;
if ($_GET['client_id'] > 0) { $_GET['client_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_clients WHERE id = ".$_GET['client_id'], OBJECT); }
$_GET['commerce_form_id'] = $order_data->form_id;
$_GET['product_id'] = $order_data->product_id;
foreach ($order_data as $key => $value) { $_POST[$key] = $value; } }
elseif (!headers_sent()) { header('Location: admin.php?page=commerce-manager-orders'); exit(); }
else { echo '<script type="text/javascript">window.location = "admin.php?page=commerce-manager-orders";</script>'; } }

foreach ($_POST as $key => $value) {
if (is_string($value)) {
$_POST[$key] = str_replace(array('&amp;amp;', '&amp;apos;', '&amp;quot;'), array('&amp;', '&apos;', '&quot;'), htmlspecialchars(stripslashes($value)));
if ($value == '0000-00-00 00:00:00') { $_POST[$key] = ''; } } }
$undisplayed_modules = (array) $back_office_options['order_page_undisplayed_modules'];
$currency_code = commerce_data('currency_code'); ?>

<div class="wrap">
<div id="poststuff">
<?php commerce_manager_pages_top($back_office_options); ?>
<?php if ($updated) {
echo '<div class="updated"><p><strong>'.(isset($_GET['id']) ? __('Order updated.', 'commerce-manager') : __('Order saved.', 'commerce-manager')).'</strong></p></div>
'.(isset($_GET['id']) ? '' : '<script type="text/javascript">setTimeout(\'window.location = "admin.php?page=commerce-manager-orders"\', 2000);</script>'); } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php commerce_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<p class="description"><?php _e('Fields marked with * are required.', 'commerce-manager'); ?></p>
<?php commerce_manager_pages_summary($back_office_options); ?>

<div class="postbox" id="general-informations-module"<?php if (in_array('general-informations', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="general-informations"><strong><?php echo $modules['order']['general-informations']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="id">'.__('ID', 'commerce-manager').'</label></strong></th>
<td><input type="text" name="id" id="id" size="10" value="'.$_GET['id'].'" disabled="disabled" /> <span class="description">'.__('The ID can not be changed.', 'commerce-manager').'</span></td></tr>'; } ?>
<?php $row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_forms", OBJECT);
$forms_number = (int) $row->total; if ($forms_number > 0) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="form_id"><?php _e('Form ID', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="form_id" id="form_id" rows="1" cols="25"><?php echo $_POST['form_id']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 0.', 'commerce-manager'); ?></span>
<?php if ($_POST['form_id'] > 0) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-form&amp;id='.$_POST['form_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-form&amp;id='.$_POST['form_id'].'&amp;action=delete">'.__('Delete').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-statistics&amp;form_id='.$_POST['form_id'].'">'.__('Statistics', 'commerce-manager').'</a>'; } ?></td></tr>
<?php } ?>
<?php $row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_clients", OBJECT);
$clients_number = (int) $row->total; if ($clients_number > 0) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="client_id"><?php _e('Client ID', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="client_id" id="client_id" rows="1" cols="25"><?php echo $_POST['client_id']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 0.', 'commerce-manager'); ?></span>
<?php if ($_POST['client_id'] > 0) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-client&amp;id='.$_POST['client_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-client&amp;id='.$_POST['client_id'].'&amp;action=delete">'.__('Delete').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-statistics&amp;client_id='.$_POST['client_id'].'">'.__('Statistics', 'commerce-manager').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="product_id"><?php _e('Product ID', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="product_id" id="product_id" rows="1" cols="25"><?php echo $_POST['product_id']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 1.', 'commerce-manager'); ?></span>
<?php if ($_POST['product_id'] > 0) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-product&amp;id='.$_POST['product_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-product&amp;id='.$_POST['product_id'].'&amp;action=delete">'.__('Delete').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-statistics&amp;product_id='.$_POST['product_id'].'">'.__('Statistics', 'commerce-manager').'</a>'; } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="quantity"><?php _e('Quantity', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="quantity" id="quantity" rows="1" cols="25"><?php echo $_POST['quantity']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 1.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="price"><?php _e('Global price', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="price" id="price" rows="1" cols="25"><?php echo $_POST['price']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for the current product price multiplied by the quantity.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="tax"><?php _e('Tax', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="tax" id="tax" rows="1" cols="25"><?php echo $_POST['tax']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank to automatically calculate the tax.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="tax_included_in_price"><?php _e('Tax included in price', 'commerce-manager'); ?></label></strong></th>
<td><select name="tax_included_in_price" id="tax_included_in_price">
<option value=""<?php if ($_POST['tax_included_in_price'] == '') { echo ' selected="selected"'; } ?>><?php _e('Current product option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['tax_included_in_price'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['tax_included_in_price'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="shipping_cost"><?php _e('Shipping cost', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="shipping_cost" id="shipping_cost" rows="1" cols="25"><?php echo $_POST['shipping_cost']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for the current product shipping cost.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="amount"><?php _e('Amount', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="amount" id="amount" rows="1" cols="25"><?php echo $_POST['amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank to automatically calculate the amount.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payment_mode"><?php _e('Payment mode', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="payment_mode" id="payment_mode" rows="1" cols="50"><?php echo $_POST['payment_mode']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="receiver_account"><?php _e('Receiver account', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="receiver_account" id="receiver_account" rows="1" cols="50"><?php echo $_POST['receiver_account']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="transaction_number"><?php _e('Transaction number', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="transaction_number" id="transaction_number" rows="1" cols="50"><?php echo $_POST['transaction_number']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="transaction_cost"><?php _e('Transaction cost', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="transaction_cost" id="transaction_cost" rows="1" cols="25"><?php echo $_POST['transaction_cost']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 0.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="instructions"><?php _e('Instructions to the seller', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="instructions" id="instructions" rows="5" cols="75"><?php echo $_POST['instructions']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="shipping_address"><?php _e('Shipping address', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="shipping_address" id="shipping_address" rows="5" cols="75"><?php echo $_POST['shipping_address']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="date"><?php _e('Date', 'commerce-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="date" id="date" size="20" value="<?php echo (isset($_POST['date']) ? $_POST['date'] : date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET)); ?>" /></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="status"><?php _e('Status', 'commerce-manager'); ?></label></strong></th>
<td><select name="status" id="status" onchange="<?php if (isset($_GET['id'])) { echo 'display_status_notification_email_module(); '; } ?>if (this.value == 'refunded') { document.getElementById('refund-date').style.display = ''; } else { document.getElementById('refund-date').style.display = 'none'; }">
<option value="unprocessed"<?php if ($_POST['status'] == 'unprocessed') { echo ' selected="selected"'; } ?>><?php _e('Unprocessed', 'commerce-manager'); ?></option>
<option value="processed"<?php if ($_POST['status'] == 'processed') { echo ' selected="selected"'; } ?>><?php _e('Processed', 'commerce-manager'); ?></option>
<option value="refunded"<?php if ($_POST['status'] == 'refunded') { echo ' selected="selected"'; } ?>><?php _e('Refunded', 'commerce-manager'); ?></option>
</select><?php if (isset($_GET['id'])) { echo '<input type="hidden" name="old_status" value="'.$_POST['status'].'" />'; } ?></td></tr>
<tr id="refund-date" style="<?php if ($_POST['status'] != 'refunded') { echo 'display: none; '; } ?>vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="refund_date"><?php _e('Refund date', 'commerce-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="refund_date" id="refund_date" size="20" value="<?php echo $_POST['refund_date']; ?>" /><br />
<span class="description"><?php _e('Leave this field blank if the order is not refunded, or for the current date if the order is refunded.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<?php if (isset($_GET['id'])) {
if ($_POST['status'] != 'processed') {
foreach (array(
'order_processing_notification_email_sender',
'order_processing_notification_email_receiver',
'order_processing_notification_email_subject',
'order_processing_notification_email_body') as $field) { $_POST[$field] = htmlspecialchars(product_data($field)); } ?>

<div class="postbox" id="order-processing-notification-email-module" style="display: none;">
<h3 id="order-processing-notification-email"><strong><?php _e('Processing notification email', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager-product&amp;id=<?php echo $_POST['product_id']; ?>#order-processing-notification-email"><?php _e('Click here to configure the default options of the product.', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="order_processing_notification_email_sent" id="order_processing_notification_email_sent" value="yes" /> <?php _e('Send an order processing\'s notification email', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_processing_notification_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_processing_notification_email_sender" id="order_processing_notification_email_sender" rows="1" cols="75"><?php echo $_POST['order_processing_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_processing_notification_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_processing_notification_email_receiver" id="order_processing_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['order_processing_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_processing_notification_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_processing_notification_email_subject" id="order_processing_notification_email_subject" rows="1" cols="75"><?php echo $_POST['order_processing_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_processing_notification_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="order_processing_notification_email_body" id="order_processing_notification_email_body" rows="15" cols="75"><?php echo $_POST['order_processing_notification_email_body']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<?php } if ($_POST['status'] != 'refunded') {
foreach (array(
'order_refund_notification_email_sender',
'order_refund_notification_email_receiver',
'order_refund_notification_email_subject',
'order_refund_notification_email_body') as $field) { $_POST[$field] = htmlspecialchars(product_data($field)); } ?>

<div class="postbox" id="order-refund-notification-email-module" style="display: none;">
<h3 id="order-refund-notification-email"><strong><?php _e('Refund notification email', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager-product&amp;id=<?php echo $_POST['product_id']; ?>#order-refund-notification-email"><?php _e('Click here to configure the default options of the product.', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="order_refund_notification_email_sent" id="order_refund_notification_email_sent" value="yes" /> <?php _e('Send an order refund\'s notification email', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_refund_notification_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_refund_notification_email_sender" id="order_refund_notification_email_sender" rows="1" cols="75"><?php echo $_POST['order_refund_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_refund_notification_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_refund_notification_email_receiver" id="order_refund_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['order_refund_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_refund_notification_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_refund_notification_email_subject" id="order_refund_notification_email_subject" rows="1" cols="75"><?php echo $_POST['order_refund_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_refund_notification_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="order_refund_notification_email_body" id="order_refund_notification_email_body" rows="15" cols="75"><?php echo $_POST['order_refund_notification_email_body']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<?php } ?>

<script type="text/javascript">
function display_status_notification_email_module() {<?php if ($_POST['status'] != 'processed') { ?>
if (document.forms[0].status.value == 'processed') { document.getElementById('order-processing-notification-email-module').style.display = 'block'; }
else { document.getElementById('order-processing-notification-email-module').style.display = 'none'; }<?php } if ($_POST['status'] != 'refunded') { ?>
if (document.forms[0].status.value == 'refunded') { document.getElementById('order-refund-notification-email-module').style.display = 'block'; }
else { document.getElementById('order-refund-notification-email-module').style.display = 'none'; }<?php } ?>}
</script>

<?php } ?>

<div class="postbox" id="recurring-payments-module"<?php if (in_array('recurring-payments', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="recurring-payments"><strong><?php echo $modules['order']['recurring-payments']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager#recurring-payments"><?php _e('Click here to configure the default options.', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payments_profile_number"><?php _e('Profile number', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="recurring_payments_profile_number" id="recurring_payments_profile_number" rows="1" cols="50"><?php echo $_POST['recurring_payments_profile_number']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_number"><?php _e('Payments number', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="payments_number" id="payments_number" rows="1" cols="25"><?php echo ((($_POST['payments_number'] == 'unlimited') || ((!isset($_GET['id'])) && (!isset($_POST['submit'])))) ? '' : (int) $_POST['payments_number']); ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for an unlimited quantity.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="received_payments_number"><?php _e('Received payments number', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="received_payments_number" id="received_payments_number" rows="1" cols="25"><?php echo $_POST['received_payments_number']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 0.', 'commerce-manager'); ?></span><br />
<?php if ($_POST['received_payments_number'] > 0) { echo '<a style="text-decoration: none;" href="admin.php?page=commerce-manager-recurring-payments&amp;order_id='.$_GET['id'].'">'.__('Display the payments', 'commerce-manager').'</a>'; } ?>
<?php if (isset($_GET['id'])) { echo '<input type="hidden" name="submit" value="true" />
<input type="submit" class="button-secondary" name="count_payments" value="'.__('Re-count the payments', 'commerce-manager').'" />
<input type="submit" class="button-secondary" name="count_payments_of_all_orders" value="'.__('Re-count the payments of all orders', 'commerce-manager').'" />'; } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_amount"><?php _e('Payments amount', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="payments_amount" id="payments_amount" rows="1" cols="25"><?php echo $_POST['payments_amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_period_quantity"><?php _e('Payments period', 'commerce-manager'); ?></label></strong></th>
<td><input type="text" name="payments_period_quantity" id="payments_period_quantity" size="2" value="<?php echo $_POST['payments_period_quantity']; ?>" /> 
<select name="payments_period_time_unit" id="payments_period_time_unit">
<option value="day"<?php if ($_POST['payments_period_time_unit'] == 'day') { echo ' selected="selected"'; } ?>><?php _e('day(s)', 'commerce-manager'); ?></option>
<option value="week"<?php if ($_POST['payments_period_time_unit'] == 'week') { echo ' selected="selected"'; } ?>><?php _e('week(s)', 'commerce-manager'); ?></option>
<option value="month"<?php if (($_POST['payments_period_time_unit'] == 'month') || ($_POST['payments_period_time_unit'] == '')) { echo ' selected="selected"'; } ?>><?php _e('month(s)', 'commerce-manager'); ?></option>
<option value="year"<?php if ($_POST['payments_period_time_unit'] == 'year') { echo ' selected="selected"'; } ?>><?php _e('year(s)', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="first_payment_amount"><?php _e('First payment\'s amount', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="first_payment_amount" id="first_payment_amount" rows="1" cols="25"><?php echo $_POST['first_payment_amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="first_payment_period_quantity"><?php _e('First payment\'s period', 'commerce-manager'); ?></label></strong></th>
<td><input type="text" name="first_payment_period_quantity" id="first_payment_period_quantity" size="2" value="<?php echo $_POST['first_payment_period_quantity']; ?>" /> 
<select name="first_payment_period_time_unit" id="first_payment_period_time_unit">
<option value="day"<?php if ($_POST['first_payment_period_time_unit'] == 'day') { echo ' selected="selected"'; } ?>><?php _e('day(s)', 'commerce-manager'); ?></option>
<option value="week"<?php if ($_POST['first_payment_period_time_unit'] == 'week') { echo ' selected="selected"'; } ?>><?php _e('week(s)', 'commerce-manager'); ?></option>
<option value="month"<?php if (($_POST['first_payment_period_time_unit'] == 'month') || ($_POST['first_payment_period_time_unit'] == '')) { echo ' selected="selected"'; } ?>><?php _e('month(s)', 'commerce-manager'); ?></option>
<option value="year"<?php if ($_POST['first_payment_period_time_unit'] == 'year') { echo ' selected="selected"'; } ?>><?php _e('year(s)', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payments_profile_status"><?php _e('Profile status', 'commerce-manager'); ?></label></strong></th>
<td><select name="recurring_payments_profile_status" id="recurring_payments_profile_status" onchange="
<?php if ((isset($_GET['id'])) && ($_POST['recurring_payments_profile_status'] != 'inactive') && ($_POST['recurring_payments_profile_status'] != 'deactivated')) { echo 'display_recurring_payments_profile_status_notification_email_module(); '; } ?>
 if (this.value == 'deactivated') { document.getElementById('recurring-payments-profile-deactivation-date').style.display = ''; } else { document.getElementById('recurring-payments-profile-deactivation-date').style.display = 'none'; }">
<option value=""<?php if ($_POST['recurring_payments_profile_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('None', 'commerce-manager'); ?></option>
<option value="active"<?php if ($_POST['recurring_payments_profile_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'commerce-manager'); ?></option>
<option value="inactive"<?php if ($_POST['recurring_payments_profile_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'commerce-manager'); ?></option>
<option value="deactivated"<?php if ($_POST['recurring_payments_profile_status'] == 'deactivated') { echo ' selected="selected"'; } ?>><?php _e('Deactivated', 'commerce-manager'); ?></option>
</select><?php if (isset($_GET['id'])) { echo '<input type="hidden" name="old_recurring_payments_profile_status" value="'.$_POST['recurring_payments_profile_status'].'" />'; } ?></td></tr>
<tr id="recurring-payments-profile-deactivation-date" style="<?php if ($_POST['recurring_payments_profile_status'] != 'deactivated') { echo 'display: none; '; } ?>vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payments_profile_deactivation_date"><?php _e('Deactivation date', 'commerce-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="recurring_payments_profile_deactivation_date" id="recurring_payments_profile_deactivation_date" size="20" value="<?php echo $_POST['recurring_payments_profile_deactivation_date']; ?>" /><br />
<span class="description"><?php _e('Leave this field blank if the profile is not deactivated, or for the current date if the profile is deactivated.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<?php if (isset($_GET['id'])) {
if (($_POST['recurring_payments_profile_status'] != 'inactive') && ($_POST['recurring_payments_profile_status'] != 'deactivated')) {
foreach (array(
'payments_profile_deactivation_notification_email_sender',
'payments_profile_deactivation_notification_email_receiver',
'payments_profile_deactivation_notification_email_subject',
'payments_profile_deactivation_notification_email_body') as $field) { $_POST[$field] = htmlspecialchars(product_data($field)); } ?>

<div class="postbox" id="recurring-payments-profile-deactivation-notification-email-module" style="display: none;">
<h3 id="recurring-payments-profile-deactivation-notification-email"><strong><?php _e('Recurring payments profile deactivation\'s notification email', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager-product&amp;id=<?php echo $_POST['product_id']; ?>#recurring-payments-profile-deactivation-notification-email"><?php _e('Click here to configure the default options of the product.', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="payments_profile_deactivation_notification_email_sent" id="payments_profile_deactivation_notification_email_sent" value="yes" /> <?php _e('Send a recurring payments profile deactivation\'s notification email', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_profile_deactivation_notification_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="payments_profile_deactivation_notification_email_sender" id="payments_profile_deactivation_notification_email_sender" rows="1" cols="75"><?php echo $_POST['payments_profile_deactivation_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_profile_deactivation_notification_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="payments_profile_deactivation_notification_email_receiver" id="payments_profile_deactivation_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['payments_profile_deactivation_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_profile_deactivation_notification_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="payments_profile_deactivation_notification_email_subject" id="payments_profile_deactivation_notification_email_subject" rows="1" cols="75"><?php echo $_POST['payments_profile_deactivation_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_profile_deactivation_notification_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="payments_profile_deactivation_notification_email_body" id="payments_profile_deactivation_notification_email_body" rows="15" cols="75"><?php echo $_POST['payments_profile_deactivation_notification_email_body']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<script type="text/javascript">
function display_recurring_payments_profile_status_notification_email_module() {
if ((document.forms[0].recurring_payments_profile_status.value == 'inactive') || (document.forms[0].recurring_payments_profile_status.value == 'deactivated')) {
document.getElementById('recurring-payments-profile-deactivation-notification-email-module').style.display = 'block'; }
else { document.getElementById('recurring-payments-profile-deactivation-notification-email-module').style.display = 'none'; } }
</script>

<?php } } ?>

<div class="postbox" id="customer-module"<?php if (in_array('customer', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="customer"><strong><?php echo $modules['order']['customer']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['first_name'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="first_name"><?php _e('First name', 'commerce-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="first_name" id="first_name" rows="1" cols="50"><?php echo $_POST['first_name']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['last_name'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="last_name"><?php _e('Last name', 'commerce-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="last_name" id="last_name" rows="1" cols="50"><?php echo $_POST['last_name']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['email_address'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="email_address"><?php _e('Email address', 'commerce-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="email_address" id="email_address" rows="1" cols="50"><?php echo $_POST['email_address']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="website_name"><?php _e('Website name', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="website_name" id="website_name" rows="1" cols="50"><?php echo $_POST['website_name']; ?></textarea> 
<?php $url = htmlspecialchars(order_data(array(0 => 'website_url', 'part' => 1, 'id' => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'commerce-manager'); ?></a><?php } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="website_url"><?php _e('Website URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="website_url" id="website_url" rows="1" cols="75"><?php echo $_POST['website_url']; ?></textarea> 
<?php $url = htmlspecialchars(order_data(array(0 => 'website_url', 'part' => 1, 'id' => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'commerce-manager'); ?></a><?php } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="address"><?php _e('Address', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="address" id="address" rows="1" cols="50"><?php echo $_POST['address']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="postcode"><?php _e('Postcode', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="postcode" id="postcode" rows="1" cols="50"><?php echo $_POST['postcode']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="town"><?php _e('Town', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="town" id="town" rows="1" cols="50"><?php echo $_POST['town']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="country"><?php _e('Country', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="country" id="country" rows="1" cols="50"><?php echo $_POST['country']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="phone_number"><?php _e('Phone number', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="phone_number" id="phone_number" rows="1" cols="50"><?php echo $_POST['phone_number']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="ip_address"><?php _e('IP address', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="ip_address" id="ip_address" rows="1" cols="50"><?php echo $_POST['ip_address']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="user_agent"><?php _e('User agent', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="user_agent" id="user_agent" rows="1" cols="75"><?php echo $_POST['user_agent']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="referring_url"><?php _e('Referring URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="referring_url" id="referring_url" rows="1" cols="75"><?php echo $_POST['referring_url']; ?></textarea> 
<?php $url = htmlspecialchars(order_data(array(0 => 'referring_url', 'part' => 1, 'id' => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'commerce-manager'); ?></a><?php } ?></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="affiliation-module"<?php if (in_array('affiliation', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="affiliation"><strong><?php echo $modules['order']['affiliation']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php echo (function_exists('affiliation_manager_admin_menu') ? '<a href="admin.php?page=affiliation-manager">'.__('Click here to configure the default options.', 'commerce-manager').'</a>' : __('To use affiliation, you must have installed and activated <a href="http://www.kleor-editions.com/affiliation-manager">Affiliation Manager</a>.', 'commerce-manager')); ?></span></td></tr>
</tbody></table>
<div id="level-1-commission-module"<?php if (in_array('level-1-commission', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="level-1-commission"><strong><?php echo $modules['order']['affiliation']['modules']['level-1-commission']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('The level 1 commission is awarded to the affiliate who referred the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#commissions-levels"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="referrer"><?php _e('Referrer', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="referrer" id="referrer" rows="1" cols="25"><?php echo $_POST['referrer']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Affiliate who referred this order (ID, login name or email address)', 'commerce-manager'); ?></span> 
<?php if ((function_exists('affiliation_manager_admin_menu')) && ($_POST['referrer'] != '') && (!strstr($_POST['referrer'], '@'))) {
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['referrer']."'", OBJECT);
if ($result) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate&amp;id='.$result->id.'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate&amp;id='.$result->id.'&amp;action=delete">'.__('Delete').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-statistics&amp;referrer='.$_POST['referrer'].'">'.__('Statistics', 'commerce-manager').'</a>'; } } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_amount"><?php _e('Amount', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_amount" id="commission_amount" rows="1" cols="25"><?php echo $_POST['commission_amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 0.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_payment"><?php _e('Payment', 'commerce-manager'); ?></label></strong></th>
<td><select name="commission_payment" id="commission_payment">
<option value=""<?php if ($_POST['commission_payment'] == '') { echo ' selected="selected"'; } ?>><?php _e('None', 'commerce-manager'); ?></option>
<option value="deferred"<?php if ($_POST['commission_payment'] == 'deferred') { echo ' selected="selected"'; } ?>><?php _e('Deferred', 'commerce-manager'); ?></option>
<option value="instant"<?php if ($_POST['commission_payment'] == 'instant') { echo ' selected="selected"'; } ?>><?php _e('Instant', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_status"><?php _e('Status', 'commerce-manager'); ?></label></strong></th>
<td><select name="commission_status" id="commission_status" onchange="if (this.value == 'paid') { document.getElementById('commission-payment-date').style.display = ''; } else { document.getElementById('commission-payment-date').style.display = 'none'; }">
<option value=""<?php if ($_POST['commission_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('None', 'commerce-manager'); ?></option>
<option value="unpaid"<?php if ($_POST['commission_status'] == 'unpaid') { echo ' selected="selected"'; } ?>><?php _e('Unpaid', 'commerce-manager'); ?></option>
<option value="paid"<?php if ($_POST['commission_status'] == 'paid') { echo ' selected="selected"'; } ?>><?php _e('Paid', 'commerce-manager'); ?></option>
</select><?php if (isset($_GET['id'])) { echo '<input type="hidden" name="old_commission_status" value="'.$_POST['commission_status'].'" />'; } ?></td></tr>
<tr id="commission-payment-date" style="<?php if ($_POST['commission_status'] != 'paid') { echo 'display: none; '; } ?>vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_payment_date"><?php _e('Payment date', 'commerce-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="commission_payment_date" id="commission_payment_date" size="20" value="<?php echo $_POST['commission_payment_date']; ?>" /><br />
<span class="description"><?php _e('Leave this field blank if the commission is not paid, or for the current date if the commission is paid.', 'commerce-manager'); ?></span></td></tr>
</tbody></table>
</div>
<div id="level-2-commission-module"<?php if (in_array('level-2-commission', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="level-2-commission"><strong><?php echo $modules['order']['affiliation']['modules']['level-2-commission']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('The level 2 commission is awarded to the referrer of the affiliate who referred the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#commissions-levels"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="referrer2"><?php _e('Referrer', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="referrer2" id="referrer2" rows="1" cols="25"><?php echo $_POST['referrer2']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for the referrer of the affiliate who referred this order.', 'commerce-manager'); ?></span> 
<?php if ((function_exists('affiliation_manager_admin_menu')) && ($_POST['referrer2'] != '') && (!strstr($_POST['referrer2'], '@'))) {
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['referrer2']."'", OBJECT);
if ($result) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate&amp;id='.$result->id.'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate&amp;id='.$result->id.'&amp;action=delete">'.__('Delete').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-statistics&amp;referrer='.$_POST['referrer2'].'">'.__('Statistics', 'commerce-manager').'</a>'; } } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_amount"><?php _e('Amount', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission2_amount" id="commission2_amount" rows="1" cols="25"><?php echo $_POST['commission2_amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 0.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_status"><?php _e('Status', 'commerce-manager'); ?></label></strong></th>
<td><select name="commission2_status" id="commission2_status" onchange="if (this.value == 'paid') { document.getElementById('commission2-payment-date').style.display = ''; } else { document.getElementById('commission2-payment-date').style.display = 'none'; }">
<option value=""<?php if ($_POST['commission2_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('None', 'commerce-manager'); ?></option>
<option value="unpaid"<?php if ($_POST['commission2_status'] == 'unpaid') { echo ' selected="selected"'; } ?>><?php _e('Unpaid', 'commerce-manager'); ?></option>
<option value="paid"<?php if ($_POST['commission2_status'] == 'paid') { echo ' selected="selected"'; } ?>><?php _e('Paid', 'commerce-manager'); ?></option>
</select><?php if (isset($_GET['id'])) { echo '<input type="hidden" name="old_commission2_status" value="'.$_POST['commission2_status'].'" />'; } ?></td></tr>
<tr id="commission2-payment-date" style="<?php if ($_POST['commission2_status'] != 'paid') { echo 'display: none; '; } ?>vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_payment_date"><?php _e('Payment date', 'commerce-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="commission2_payment_date" id="commission2_payment_date" size="20" value="<?php echo $_POST['commission2_payment_date']; ?>" /><br />
<span class="description"><?php _e('Leave this field blank if the commission is not paid, or for the current date if the commission is paid.', 'commerce-manager'); ?></span></td></tr>
</tbody></table>
</div>
<?php if (isset($_GET['id'])) { echo '<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr></tbody></table>'; } ?>
</div></div>

<?php if (!isset($_GET['id'])) {
if (!isset($_POST['submit'])) {
$commerce_manager_options = (array) get_option('commerce_manager');
foreach ($commerce_manager_options as $key => $value) {
if (is_string($value)) { $commerce_manager_options[$key] = htmlspecialchars($value); } }
foreach ($add_order_fields as $field) { $_POST[$field] = $commerce_manager_options[$field]; } }
$value = false; foreach ($add_order_modules as $module) { if (!$value) { $value = (!in_array($module, $undisplayed_modules)); } }
if ($value) { ?><p class="submit" style="margin: 0 20%;"><input type="hidden" name="submit" value="true" />
<input type="submit" class="button-secondary" name="update_fields" value="<?php _e('Complete the fields below with the informations about the customer, the product and the order', 'commerce-manager'); ?>" /></p><?php } ?>

<div id="add-order-modules">
<?php if (!in_array('order-confirmation-email', $undisplayed_modules)) {
if (!isset($_POST['submit'])) { $_POST['order_confirmation_email_body'] = htmlspecialchars(get_option('commerce_manager_order_confirmation_email_body')); } ?>
<div class="postbox" id="order-confirmation-email-module">
<h3 id="order-confirmation-email"><strong><?php echo $modules['order']['order-confirmation-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager#order-confirmation-email"><?php _e('Click here to configure the default options.', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="order_confirmation_email_sent" id="order_confirmation_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['order_confirmation_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send an order confirmation email', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_confirmation_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_confirmation_email_sender" id="order_confirmation_email_sender" rows="1" cols="75"><?php echo $_POST['order_confirmation_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_confirmation_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_confirmation_email_receiver" id="order_confirmation_email_receiver" rows="1" cols="75"><?php echo $_POST['order_confirmation_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_confirmation_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_confirmation_email_subject" id="order_confirmation_email_subject" rows="1" cols="75"><?php echo $_POST['order_confirmation_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_confirmation_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="order_confirmation_email_body" id="order_confirmation_email_body" rows="15" cols="75"><?php echo $_POST['order_confirmation_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('order-notification-email', $undisplayed_modules)) {
if (!isset($_POST['submit'])) { $_POST['order_notification_email_body'] = htmlspecialchars(get_option('commerce_manager_order_notification_email_body')); } ?>
<div class="postbox" id="order-notification-email-module">
<h3 id="order-notification-email"><strong><?php echo $modules['order']['order-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager#order-notification-email"><?php _e('Click here to configure the default options.', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="order_notification_email_sent" id="order_notification_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['order_notification_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send an order notification email', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_notification_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_notification_email_sender" id="order_notification_email_sender" rows="1" cols="75"><?php echo $_POST['order_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_notification_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_notification_email_receiver" id="order_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['order_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_notification_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_notification_email_subject" id="order_notification_email_subject" rows="1" cols="75"><?php echo $_POST['order_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_notification_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="order_notification_email_body" id="order_notification_email_body" rows="15" cols="75"><?php echo $_POST['order_notification_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('autoresponders', $undisplayed_modules)) { ?>
<div class="postbox" id="autoresponders-module">
<h3 id="autoresponders"><strong><?php echo $modules['order']['autoresponders']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager#autoresponders"><?php _e('Click here to configure the default options.', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="customer_subscribed_to_autoresponder" id="customer_subscribed_to_autoresponder" value="yes"<?php if ($_POST['customer_subscribed_to_autoresponder'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Subscribe the customer to an autoresponder list', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_autoresponder"><?php _e('Autoresponder', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_autoresponder" id="customer_autoresponder">
<?php include 'libraries/autoresponders.php';
$autoresponder = do_shortcode($_POST['customer_autoresponder']);
foreach ($autoresponders as $value) {
echo '<option value="'.$value.'"'.($autoresponder == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_autoresponder_list"><?php _e('List', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="customer_autoresponder_list" id="customer_autoresponder_list" rows="1" cols="50"><?php echo $_POST['customer_autoresponder_list']; ?></textarea><br />
<span class="description"><?php _e('For some autoresponders, you must enter the list ID.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#autoresponders"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('registration-as-a-client', $undisplayed_modules)) { ?>
<div class="postbox" id="registration-as-a-client-module">
<h3 id="registration-as-a-client"><strong><?php echo $modules['order']['registration-as-a-client']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager#registration-as-a-client"><?php _e('Click here to configure the default options.', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="customer_subscribed_as_a_client" id="customer_subscribed_as_a_client" value="yes"<?php if ($_POST['customer_subscribed_as_a_client'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the customer as a client', 'commerce-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#registration-as-a-client"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."commerce_manager_clients_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_client_category_id"><?php _e('Category', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_client_category_id" id="customer_client_category_id">
<option value="0"<?php if ($_POST['customer_client_category_id'] == '0') { echo ' selected="selected"'; } ?>><?php _e('None ', 'commerce-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($_POST['customer_client_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ($_POST['customer_client_category_id'] > 0) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-client-category&amp;id='.$_POST['customer_client_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-client-category&amp;id='.$_POST['customer_client_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_client_status"><?php _e('Status', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_client_status" id="customer_client_status">
<option value="active"<?php if ($_POST['customer_client_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'commerce-manager'); ?></option>
<option value="inactive"<?php if ($_POST['customer_client_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'commerce-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#client-status"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="commerce_registration_confirmation_email_sent" id="commerce_registration_confirmation_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['commerce_registration_confirmation_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration confirmation email', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="commerce_registration_notification_email_sent" id="commerce_registration_notification_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['commerce_registration_notification_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration notification email', 'commerce-manager'); ?></label></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('registration-to-affiliate-program', $undisplayed_modules)) { ?>
<div class="postbox" id="registration-to-affiliate-program-module">
<h3 id="registration-to-affiliate-program"><strong><?php echo $modules['order']['registration-to-affiliate-program']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php if (function_exists('affiliation_manager_admin_menu')) { ?>
<a href="admin.php?page=commerce-manager#registration-to-affiliate-program"><?php _e('Click here to configure the default options.', 'commerce-manager'); ?></a>
<?php } else { _e('To use affiliation, you must have installed and activated <a href="http://www.kleor-editions.com/affiliation-manager">Affiliation Manager</a>.', 'commerce-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="customer_subscribed_to_affiliate_program" id="customer_subscribed_to_affiliate_program" value="yes"<?php if ($_POST['customer_subscribed_to_affiliate_program'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the customer to affiliate program', 'commerce-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#registration-to-affiliate-program"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."affiliation_manager_affiliates_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_affiliate_category_id"><?php _e('Category', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_affiliate_category_id" id="customer_affiliate_category_id">
<option value="0"<?php if ($_POST['customer_affiliate_category_id'] == '0') { echo ' selected="selected"'; } ?>><?php _e('None ', 'commerce-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($_POST['customer_affiliate_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ((function_exists('affiliation_manager_admin_menu')) && ($_POST['customer_affiliate_category_id'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$_POST['customer_affiliate_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$_POST['customer_affiliate_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_affiliate_status"><?php _e('Status', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_affiliate_status" id="customer_affiliate_status">
<option value="active"<?php if ($_POST['customer_affiliate_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'commerce-manager'); ?></option>
<option value="inactive"<?php if ($_POST['customer_affiliate_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'commerce-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#affiliate-status"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="affiliation_registration_confirmation_email_sent" id="affiliation_registration_confirmation_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['affiliation_registration_confirmation_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration confirmation email', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="affiliation_registration_notification_email_sent" id="affiliation_registration_notification_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['affiliation_registration_notification_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration notification email', 'commerce-manager'); ?></label></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('membership', $undisplayed_modules)) { ?>
<div class="postbox" id="membership-module">
<h3 id="membership"><strong><?php echo $modules['order']['membership']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php if (function_exists('membership_manager_admin_menu')) { ?>
<a href="admin.php?page=commerce-manager#membership"><?php _e('Click here to configure the default options.', 'commerce-manager'); ?></a>
<?php } else { _e('To use membership, you must have installed and activated <a href="http://www.kleor-editions.com/membership-manager">Membership Manager</a>.', 'commerce-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="customer_subscribed_to_members_areas" id="customer_subscribed_to_members_areas" value="yes"<?php if ($_POST['customer_subscribed_to_members_areas'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the customer to a member area', 'commerce-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#membership"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_members_areas"><?php _e('Members areas', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="customer_members_areas" id="customer_members_areas" rows="1" cols="50"><?php echo $_POST['customer_members_areas']; ?></textarea>
<?php if ((function_exists('membership_manager_admin_menu')) && (is_numeric($_POST['customer_members_areas'])) && ($_POST['customer_members_areas'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-area&amp;id='.$_POST['customer_members_areas'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-area&amp;id='.$_POST['customer_members_areas'].'&amp;action=delete">'.__('Delete').'</a>'; } ?><br />
<span class="description"><?php _e('Separate the IDs of the members areas with commas.', 'commerce-manager'); ?></span></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."membership_manager_members_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_member_category_id"><?php _e('Category', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_member_category_id" id="customer_member_category_id">
<option value="0"<?php if ($_POST['customer_member_category_id'] == '0') { echo ' selected="selected"'; } ?>><?php _e('None ', 'commerce-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($_POST['customer_member_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ((function_exists('membership_manager_admin_menu')) && ($_POST['customer_member_category_id'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-category&amp;id='.$_POST['customer_member_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-category&amp;id='.$_POST['customer_member_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_member_status"><?php _e('Status', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_member_status" id="customer_member_status">
<option value="active"<?php if ($_POST['customer_member_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'commerce-manager'); ?></option>
<option value="inactive"<?php if ($_POST['customer_member_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'commerce-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#member-status"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="membership_registration_confirmation_email_sent" id="membership_registration_confirmation_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['membership_registration_confirmation_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration confirmation email', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="membership_registration_notification_email_sent" id="membership_registration_notification_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['membership_registration_notification_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration notification email', 'commerce-manager'); ?></label></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('wordpress', $undisplayed_modules)) { ?>
<div class="postbox" id="wordpress-module">
<h3 id="wordpress"><strong><?php echo $modules['order']['wordpress']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager#wordpress"><?php _e('Click here to configure the default options.', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="customer_subscribed_as_a_user" id="customer_subscribed_as_a_user" value="yes"<?php if ($_POST['customer_subscribed_as_a_user'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the customer as a user', 'commerce-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#wordpress"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_user_role"><?php _e('Role', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_user_role" id="customer_user_role">
<?php foreach (commerce_manager_users_roles() as $role => $name) {
echo '<option value="'.$role.'"'.($_POST['customer_user_role'] == $role ? ' selected="selected"' : '').'>'.$name.'</option>'."\n"; } ?>
</select></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('custom-instructions', $undisplayed_modules)) {
if (!isset($_POST['submit'])) { $_POST['order_custom_instructions'] = htmlspecialchars(get_option('commerce_manager_order_custom_instructions')); } ?>
<div class="postbox" id="custom-instructions-module">
<h3 id="custom-instructions"><strong><?php echo $modules['order']['custom-instructions']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager#custom-instructions"><?php _e('Click here to configure the default options.', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="order_custom_instructions_executed" id="order_custom_instructions_executed" value="yes"<?php if ($_POST['order_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_custom_instructions"><?php _e('PHP code', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="order_custom_instructions" id="order_custom_instructions" rows="10" cols="75"><?php echo $_POST['order_custom_instructions']; ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the registration of the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#custom-instructions"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<?php } ?>
</div>

<?php } ?>
<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php (isset($_GET['id']) ?  _e('Save Changes', 'commerce-manager') : _e('Save Order', 'commerce-manager')); ?>" /></p>
<?php commerce_manager_pages_module($back_office_options, 'order-page', $undisplayed_modules); ?>
</form>
</div>
</div>
<?php if (isset($_POST['update_fields'])) { ?>
<script type="text/javascript">window.location = '#add-order-modules';</script>
<?php } }