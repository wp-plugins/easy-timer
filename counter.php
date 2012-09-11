<?php global $wpdb;
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
extract(shortcode_atts(array('data' => '', 'limit' => '', 'range' => '', 'referrer' => '', 'status' => ''), $atts));
if ($type == 'referrer') {
$_GET['affiliate_data'] = (array) $_GET['affiliate_data'];
if ($referrer == '') {
if ((affiliation_session()) || (is_admin())) {
$referrer = $_GET['affiliate_data']['login'];
if ($referrer == '') { $referrer = $_SESSION['affiliation_login']; } }
else { $referrer = $_GET['referrer']; } }
elseif (strstr($referrer, '@')) { $referrer = format_email_address($referrer); }
else { $referrer = format_nice_name($referrer); } }

if (($type == 'referrer') && ($referrer == '')) { $data = 0; }
else {
$data = str_replace('_', '-', format_nice_name($data));
switch ($data) {
case 'affiliates': $table = $wpdb->prefix.'affiliation_manager_affiliates'; $field = ''; break;
case 'affiliates-categories': $table = $wpdb->prefix.'affiliation_manager_affiliates_categories'; $field = ''; break;
case 'amount': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments'); $field = 'amount'; break;
case 'clicks': $table = $wpdb->prefix.'affiliation_manager_clicks'; $field = ''; break;
case 'clients': $table = $wpdb->prefix.'commerce_manager_clients'; $field = ''; break;
case 'commission-amount': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments', $wpdb->prefix.'optin_manager_prospects', $wpdb->prefix.'contact_manager_messages'); $field = 'commission_amount'; break;
case 'commission-paid-amount': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments', $wpdb->prefix.'optin_manager_prospects', $wpdb->prefix.'contact_manager_messages'); $field = 'commission_amount'; $criteria = "AND (commission_status = 'paid')"; break;
case 'commission-unpaid-amount': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments', $wpdb->prefix.'optin_manager_prospects', $wpdb->prefix.'contact_manager_messages'); $field = 'commission_amount'; $criteria = "AND (commission_status = 'unpaid')"; break;
case 'commission2-amount': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments', $wpdb->prefix.'optin_manager_prospects', $wpdb->prefix.'contact_manager_messages'); $field = 'commission2_amount'; break;
case 'commission2-paid-amount': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments', $wpdb->prefix.'optin_manager_prospects', $wpdb->prefix.'contact_manager_messages'); $field = 'commission2_amount'; $criteria = "AND (commission2_status = 'paid')"; break;
case 'commission2-unpaid-amount': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments', $wpdb->prefix.'optin_manager_prospects', $wpdb->prefix.'contact_manager_messages'); $field = 'commission2_amount'; $criteria = "AND (commission2_status = 'unpaid')"; break;
case 'messages': $table = $wpdb->prefix.'contact_manager_messages'; $field = ''; break;
case 'messages-commission-amount': $table = $wpdb->prefix.'contact_manager_messages'; $field = 'commission_amount'; break;
case 'messages-commission-paid-amount': $table = $wpdb->prefix.'contact_manager_messages'; $field = 'commission_amount'; $criteria = "AND (commission_status = 'paid')"; break;
case 'messages-commission-unpaid-amount': $table = $wpdb->prefix.'contact_manager_messages'; $field = 'commission_amount'; $criteria = "AND (commission_status = 'unpaid')"; break;
case 'messages-commission2-amount': $table = $wpdb->prefix.'contact_manager_messages'; $field = 'commission2_amount'; break;
case 'messages-commission2-paid-amount': $table = $wpdb->prefix.'contact_manager_messages'; $field = 'commission2_amount'; $criteria = "AND (commission2_status = 'paid')"; break;
case 'messages-commission2-unpaid-amount': $table = $wpdb->prefix.'contact_manager_messages'; $field = 'commission2_amount'; $criteria = "AND (commission2_status = 'unpaid')"; break;
case 'orders': $table = $wpdb->prefix.'commerce_manager_orders'; $field = ''; break;
case 'orders-amount': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'amount'; break;
case 'orders-commission-amount': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'commission_amount'; break;
case 'orders-commission-paid-amount': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'commission_amount'; $criteria = "AND (commission_status = 'paid')"; break;
case 'orders-commission-unpaid-amount': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'commission_amount'; $criteria = "AND (commission_status = 'unpaid')"; break;
case 'orders-commission2-amount': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'commission2_amount'; break;
case 'orders-commission2-paid-amount': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'commission2_amount'; $criteria = "AND (commission2_status = 'paid')"; break;
case 'orders-commission2-unpaid-amount': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'commission2_amount'; $criteria = "AND (commission2_status = 'unpaid')"; break;
case 'orders-price': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'price'; break;
case 'orders-quantity': case 'sales': $table = $wpdb->prefix.'commerce_manager_orders'; $field = 'quantity'; break;
case 'price': $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments'); $field = 'price'; break;
case 'prospects': $table = $wpdb->prefix.'optin_manager_prospects'; $field = ''; break;
case 'prospects-commission-amount': $table = $wpdb->prefix.'optin_manager_prospects'; $field = 'commission_amount'; break;
case 'prospects-commission-paid-amount': $table = $wpdb->prefix.'optin_manager_prospects'; $field = 'commission_amount'; $criteria = "AND (commission_status = 'paid')"; break;
case 'prospects-commission-unpaid-amount': $table = $wpdb->prefix.'optin_manager_prospects'; $field = 'commission_amount'; $criteria = "AND (commission_status = 'unpaid')"; break;
case 'prospects-commission2-amount': $table = $wpdb->prefix.'optin_manager_prospects'; $field = 'commission2_amount'; break;
case 'prospects-commission2-paid-amount': $table = $wpdb->prefix.'optin_manager_prospects'; $field = 'commission2_amount'; $criteria = "AND (commission2_status = 'paid')"; break;
case 'prospects-commission2-unpaid-amount': $table = $wpdb->prefix.'optin_manager_prospects'; $field = 'commission2_amount'; $criteria = "AND (commission2_status = 'unpaid')"; break;
case 'recurring-payments': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = ''; break;
case 'recurring-payments-amount': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'amount'; break;
case 'recurring-payments-commission-amount': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'commission_amount'; break;
case 'recurring-payments-commission-paid-amount': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'commission_amount'; $criteria = "AND (commission_status = 'paid')"; break;
case 'recurring-payments-commission-unpaid-amount': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'commission_amount'; $criteria = "AND (commission_status = 'unpaid')"; break;
case 'recurring-payments-commission2-amount': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'commission2_amount'; break;
case 'recurring-payments-commission2-paid-amount': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'commission2_amount'; $criteria = "AND (commission2_status = 'paid')"; break;
case 'recurring-payments-commission2-unpaid-amount': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'commission2_amount'; $criteria = "AND (commission2_status = 'unpaid')"; break;
case 'recurring-payments-price': $table = $wpdb->prefix.'commerce_manager_recurring_payments'; $field = 'price'; break;
default: $table = array($wpdb->prefix.'commerce_manager_orders', $wpdb->prefix.'commerce_manager_recurring_payments', $wpdb->prefix.'optin_manager_prospects', $wpdb->prefix.'contact_manager_messages'); $field = 'commission_amount'; }

if ($type == 'referrer') {
if (strstr($data, 'commission2')) { $referrer_field = 'referrer2'; } else { $referrer_field = 'referrer'; }
$referrer_criteria = "AND $referrer_field = '$referrer'"; }

$range = str_replace('_', '-', format_nice_name($range));
if (is_numeric($range)) {
$end_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$start_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET - 86400*$range);
$date_criteria = "AND (date BETWEEN '".$start_date."' AND '".$end_date."')"; }
else { switch ($range) {
case 'form':
$start_date = $_POST['affiliation_form_statistics_start_date'];
$end_date = $_POST['affiliation_form_statistics_end_date'];
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
if (strstr($data, 'commission2')) { $status_field = 'commission2_status'; }
elseif (strstr($data, 'commission')) { $status_field = 'commission_status'; }
else { $status_field = 'status'; }
if ($status != '') { $status_criteria = "AND $status_field = '".$status."'"; }

if (is_string($table)) {
if ($field == '') {
$row = $wpdb->get_row("SELECT count(*) as total FROM $table WHERE id > 0 $referrer_criteria $criteria $date_criteria $status_criteria", OBJECT);
$data = (int) $row->total; }
else {
$row = $wpdb->get_row("SELECT SUM($field) AS total FROM $table WHERE id > 0 $referrer_criteria $criteria $date_criteria $status_criteria", OBJECT);
$data = round(100*$row->total)/100; } }

else {
$data = 0; foreach ($table as $table_name) {
$row = $wpdb->get_row("SELECT SUM($field) AS total FROM $table_name WHERE id > 0 $referrer_criteria $criteria $date_criteria $status_criteria", OBJECT);
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
if (isset($_GET['affiliation_'.$_tag])) { $original['affiliation_'.$_tag] = $_GET['affiliation_'.$_tag]; }
add_shortcode($tag, create_function('$atts', '$atts["data"] = "'.$tag.'"; return affiliation_counter_tag($atts);')); }

$_GET['affiliation_limit'] = $limit[$i];
$_GET['affiliation_number'] = $data - $limit[$k];
$_GET['affiliation_remaining_number'] = $remaining_number;
$_GET['affiliation_total_limit'] = $limit[$n - 1];
$_GET['affiliation_total_number'] = $data;
$_GET['affiliation_total_remaining_number'] = $total_remaining_number;

$content[$k] = do_shortcode($content[$k]);

foreach ($tags as $tag) {
$_tag = str_replace('-', '_', format_nice_name($tag));
if (isset($original['affiliation_'.$_tag])) { $_GET['affiliation_'.$_tag] = $original['affiliation_'.$_tag]; }
remove_shortcode($tag); }