<?php global $wpdb;
if ($type == 'inventory') {
$_GET['product_data'] = (array) $_GET['product_data'];
if ((isset($_GET['product_id'])) && ($_GET['product_data']['id'] != $_GET['product_id'])) {
$_GET['product_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_products WHERE id = ".$_GET['product_id'], OBJECT); }
$product_data = $_GET['product_data'];
extract(shortcode_atts(array('data' => '', 'id' => '', 'limit' => ''), $atts));
$field = str_replace('-', '_', format_nice_name($data));
if (($field == '') || ($field == 'sales')) { $field = 'sales_count'; }
elseif ($field == 'refunds') { $field = 'refunds_count'; }
$id = preg_split('#[^0-9]#', $id, 0, PREG_SPLIT_NO_EMPTY);
$m = count($id);

if ($m < 2) {
$id = (int) $id[0];
if (($id == 0) || ($id == $product_data['id'])) { $data = $product_data[$field]; }
else {
foreach (array('product_id', 'product_data') as $key) {
if (isset($_GET[$key])) { $original[$key] = $_GET[$key]; } }
$product_data = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_products WHERE id = $id", OBJECT);
$_GET['product_id'] = $id; $_GET['product_data'] = $product_data;
$data = $product_data[$field]; } }

else {
$data = 0; for ($i = 0; $i < $m; $i++) {
$id[$i] = (int) $id[$i];
$product_data = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_products WHERE id = ".$id[$i], OBJECT);
$data = $data + $product_data[$field]; } } }

else {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
extract(shortcode_atts(array('data' => '', 'id' => '', 'limit' => '', 'range' => '', 'status' => ''), $atts));
switch ($type) {
case 'client': $_GET['client_data'] = (array) $_GET['client_data'];
if (($id == '') && ((commerce_session()) || (is_admin()))) { $id = client_data('id'); } break;
case 'commerce_form': $_GET['commerce_form_data'] = (array) $_GET['commerce_form_data'];
if ($id == '') { $id = $_GET['commerce_form_id']; } break;
case 'product': $_GET['product_data'] = (array) $_GET['product_data'];
if ($id == '') { $id = $_GET['product_id']; } }
$id = (int) $id;

$data = str_replace('_', '-', format_nice_name($data));
switch ($data) {
case 'amount': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments'); $field = 'amount'; break;
case 'clients': $table = $wpdb->prefix.'commerce_manager_clients'; $field = ''; break;
case 'clients-categories': $table = $wpdb->prefix.'commerce_manager_clients_categories'; $field = ''; break;
case 'commission-amount': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments'); $field = 'commission_amount'; break;
case 'commission2-amount': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments'); $field = 'commission2_amount'; break;
case 'forms': $table = $wpdb->prefix.'commerce_manager_forms'; $field = ''; break;
case 'forms-categories': $table = $wpdb->prefix.'commerce_manager_forms_categories'; $field = ''; break;
case 'orders': $table = $wpdb->prefix.'commerce_manager_orders'; $field = ''; break;
case 'orders-amount': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'amount'; break;
case 'orders-commission-amount': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'commission_amount'; break;
case 'orders-commission2-amount': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'commission2_amount'; break;
case 'orders-price': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'price'; break;
case 'orders-quantity': case 'sales': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'quantity'; break;
case 'orders-shipping-cost': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'shipping_cost'; break;
case 'orders-tax': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'tax'; break;
case 'orders-transaction-cost': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'transaction_cost'; break;
case 'price': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments'); $field = 'price'; break;
case 'products': $table = $wpdb->prefix.'commerce_manager_products'; $field = ''; break;
case 'products-categories': $table = $wpdb->prefix.'commerce_manager_products_categories'; $field = ''; break;
case 'recurring-payments': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = ''; break;
case 'recurring-payments-amount': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'amount'; break;
case 'recurring-payments-commission-amount': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'commission_amount'; break;
case 'recurring-payments-commission2-amount': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'commission2_amount'; break;
case 'recurring-payments-price': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'price'; break;
case 'recurring-payments-shipping-cost': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'shipping_cost'; break;
case 'recurring-payments-tax': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'tax'; break;
case 'recurring-payments-transaction-cost': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'transaction_cost'; break;
case 'refunds': $status = 'refunded'; $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'quantity'; break;
case 'shipping-cost': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments'); $field = 'shipping_cost'; break;
case 'tax': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments'); $field = 'tax'; break;
case 'transaction-cost': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments'); $field = 'transaction_cost'; break;
default: $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments'); $field = 'amount'; }

switch ($type) {
case 'client': $criteria = "client_id = $id"; break;
case 'commerce_form': $criteria = "form_id = $id"; break;
case 'product': $criteria = "product_id = $id"; break;
default: $criteria = "id > 0"; }

$range = str_replace('_', '-', format_nice_name($range));
if (is_numeric($range)) {
$end_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$start_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET - 86400*$range);
$date_criteria = "AND (date BETWEEN '".$start_date."' AND '".$end_date."')"; }
else { switch ($range) {
case 'form':
$start_date = $_POST['commerce_form_statistics_start_date'];
$end_date = $_POST['commerce_form_statistics_end_date'];
$date_criteria = "AND (date BETWEEN '".$start_date."' AND '".$end_date."')"; break;
case 'previous-month':
$Y = (int) date('Y', time() + 3600*UTC_OFFSET);
$M = (int) date('n', time() + 3600*UTC_OFFSET);
if ($M == 1) { $m = 12; $y = $Y - 1; }
else { $m = $M - 1; $y = $Y; }
if ($M < 10) { $M = '0'.$M; }
if ($m < 10) { $m = '0'.$m; }
$start_date = $y.'-'.$m.'-01 00:00:00';
$end_date = $Y.'-'.$M.'-01 00:00:00';
$date_criteria = "AND (date BETWEEN '".$start_date."' AND '".$end_date."')"; break;
case 'previous-year':
$Y = (int) date('Y', time() + 3600*UTC_OFFSET);
$y = $Y - 1;
$start_date = $y.'-01-01 00:00:00';
$end_date = $y.'-12-31 23:59:59';
$date_criteria = "AND (date BETWEEN '".$start_date."' AND '".$end_date."')"; break;
default: $date_criteria = ''; } }

$status = str_replace('-', '_', format_nice_name($status));
if ($status != '') { $status_criteria = "AND status = '".$status."'"; }

if (is_string($table)) {
if ($field == '') {
$row = $wpdb->get_row("SELECT count(*) as total FROM $table WHERE $criteria $date_criteria $status_criteria", OBJECT);
$data = (int) $row->total; }
else {
$row = $wpdb->get_row("SELECT SUM($field) AS total FROM $table WHERE $criteria $date_criteria $status_criteria", OBJECT);
$data = round(100*$row->total)/100; } }

else {
$data = 0; foreach ($table as $table_name) {
$row = $wpdb->get_row("SELECT SUM($field) AS total FROM $table_name WHERE $criteria $date_criteria $status_criteria", OBJECT);
$data = $data + round(100*$row->total)/100; } } }

if ($limit == '') { $limit = '0'; }
else { $limit = '0/'.$limit; }
$limit = preg_split('#[^0-9]#', $limit, 0, PREG_SPLIT_NO_EMPTY);
$n = count($limit);

$i = 0; while (($i < $n) && ($limit[$i] <= $data)) { $k = $i; $i = $i + 1; }
if ($i < $n) { $remaining_number = $limit[$i] - $data; $total_remaining_number = $limit[$n - 1] - $data; }
else { $i = $n - 1; $remaining_number = 0; $total_remaining_number = 0; }

$content = explode('[after]', do_shortcode($content));

$tags = array('limit', 'number', 'remaining-number', 'total-limit', 'total-number', 'total-remaining-number');
foreach ($tags as $tag) {
$_tag = str_replace('-', '_', format_nice_name($tag));
if (isset($_GET['commerce_'.$_tag])) { $original['commerce_'.$_tag] = $_GET['commerce_'.$_tag]; }
add_shortcode($tag, create_function('$atts', '$atts["data"] = "'.$tag.'"; return commerce_counter_tag($atts);')); }

$_GET['commerce_limit'] = $limit[$i];
$_GET['commerce_number'] = $data - $limit[$k];
$_GET['commerce_remaining_number'] = $remaining_number;
$_GET['commerce_total_limit'] = $limit[$n - 1];
$_GET['commerce_total_number'] = $data;
$_GET['commerce_total_remaining_number'] = $total_remaining_number;

$content[$k] = do_shortcode($content[$k]);

foreach ($tags as $tag) {
$_tag = str_replace('-', '_', format_nice_name($tag));
if (isset($original['commerce_'.$_tag])) { $_GET['commerce_'.$_tag] = $original['commerce_'.$_tag]; }
remove_shortcode($tag); }

if ($type == 'inventory') {
foreach (array('product_id', 'product_data') as $key) {
if (isset($original[$key])) { $_GET[$key] = $original[$key]; } } }