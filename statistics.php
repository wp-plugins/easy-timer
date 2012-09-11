<?php function affiliation_commissions_statistics($atts) {
if (affiliation_session()) {
extract(shortcode_atts(array('level' => 0, 'type' => ''), $atts));
switch ($level) { case 1: case 2: break; default: $level = 0; }
$type = str_replace('_', '-', format_nice_name($type));
switch ($type) { case 'recurring': case 'non-recurring': break; default: $type = ''; }
$leveltype = $level.$type;
switch ($leveltype) {
case '0': $content = affiliation_statistics('commissions1').affiliation_statistics('recurring_commissions1').affiliation_statistics('commissions2').affiliation_statistics('recurring_commissions2'); break;
case '0recurring': $content = affiliation_statistics('recurring_commissions1').affiliation_statistics('recurring_commissions2'); break;
case '0non-recurring': $content = affiliation_statistics('commissions1').affiliation_statistics('commissions2'); break;
case '1': $content = affiliation_statistics('commissions1').affiliation_statistics('recurring_commissions1'); break;
case '1recurring': $content = affiliation_statistics('recurring_commissions1'); break;
case '1non-recurring': $content = affiliation_statistics('commissions1'); break;
case '2': $content = affiliation_statistics('commissions2').affiliation_statistics('recurring_commissions2'); break;
case '2recurring': $content = affiliation_statistics('recurring_commissions2'); break;
case '2non-recurring': $content = affiliation_statistics('commissions2'); }
return $content; } }


function affiliation_messages_commissions_statistics($atts) {
if (affiliation_session()) {
extract(shortcode_atts(array('level' => 0), $atts));
switch ($level) { case 1: case 2: break; default: $level = 0; }
switch ($level) {
case '0': $content = affiliation_statistics('messages_commissions1').affiliation_statistics('messages_commissions2'); break;
case '1': $content = affiliation_statistics('messages_commissions1'); break;
case '2': $content = affiliation_statistics('messages_commissions2'); }
return $content; } }


function affiliation_prospects_commissions_statistics($atts) {
if (affiliation_session()) {
extract(shortcode_atts(array('level' => 0), $atts));
switch ($level) { case 1: case 2: break; default: $level = 0; }
switch ($level) {
case '0': $content = affiliation_statistics('prospects_commissions1').affiliation_statistics('prospects_commissions2'); break;
case '1': $content = affiliation_statistics('prospects_commissions1'); break;
case '2': $content = affiliation_statistics('prospects_commissions2'); }
return $content; } }


function affiliation_statistics($atts) {
if (affiliation_session()) {
if (is_string($atts)) { $type = $atts; }
else { $type = str_replace('-', '_', format_nice_name($atts['type'])); }
$tags = array('foreach', 'if');
foreach ($tags as $tag) { add_shortcode($tag, 'affiliation_statistics_'.str_replace('-', '_', $tag)); }
$content = do_shortcode(get_option('affiliation_manager_'.$type.'_statistics_code'));
foreach ($tags as $tag) { remove_shortcode($tag); }
return $content; } }


function affiliation_statistics_foreach($atts, $content) {
global $wpdb;
extract(shortcode_atts(array('order' => '', 'orderby' => ''), $atts));
$order = strtoupper(format_nice_name($order));
if ($order == '') { $order = 'DESC'; }
$orderby = str_replace('-', '_', format_nice_name($orderby));
if ($orderby == '') { $orderby = 'date'; }
$type = str_replace('-', '_', format_nice_name($atts[0]));
$status = str_replace('-', '_', format_nice_name($atts['status']));
$original_content = $content; $content = '';

switch ($type) {
case 'affiliate': $table = 'affiliation_manager_affiliates'; $_GET['affiliation_affiliates_statistics'] = 'yes'; break;
case 'click': $table = 'affiliation_manager_clicks'; break;
case 'client': $table = 'commerce_manager_clients'; $_GET['affiliation_clients_statistics'] = 'yes'; break;
case 'commission1': $type = 'order'; $type2 = 'commission'; $table = 'commerce_manager_orders'; $criteria = "AND (commission_amount > 0)"; break;
case 'commission2': $type = 'order'; $type2 = 'commission'; $table = 'commerce_manager_orders'; $criteria = "AND (commission2_amount > 0)"; break;
case 'message': $type2 = 'message_commission'; $table = 'contact_manager_messages'; break;
case 'message_commission1': $type = 'message'; $type2 = 'message_commission'; $table = 'contact_manager_messages'; $criteria = "AND (commission_amount > 0)"; break;
case 'message_commission2': $type = 'message'; $type2 = 'message_commission'; $table = 'contact_manager_messages'; $criteria = "AND (commission2_amount > 0)"; break;
case 'order': $type2 = 'commission'; $table = 'commerce_manager_orders'; break;
case 'prospect': $type2 = 'prospect_commission'; $table = 'optin_manager_prospects'; break;
case 'prospect_commission1': $type = 'prospect'; $type2 = 'prospect_commission'; $table = 'optin_manager_prospects'; $criteria = "AND (commission_amount > 0)"; break;
case 'prospect_commission2': $type = 'prospect'; $type2 = 'prospect_commission'; $table = 'optin_manager_prospects'; $criteria = "AND (commission2_amount > 0)"; break;
case 'recurring_commission1': $type = 'recurring_payment'; $type2 = 'recurring_commission'; $table = 'commerce_manager_recurring_payments'; $criteria = "AND (commission_amount > 0)"; break;
case 'recurring_commission2': $type = 'recurring_payment'; $type2 = 'recurring_commission'; $table = 'commerce_manager_recurring_payments'; $criteria = "AND (commission2_amount > 0)"; break;
case 'recurring_payment': $type2 = 'recurring_commission'; $table = 'commerce_manager_recurring_payments'; break;
default: $type = 'order'; $type2 = 'commission'; $table = 'commerce_manager_orders'; }
if ($type2 == '') { $type2 = $type; }
if (strstr($criteria, 'commission2')) { $referrer_field = 'referrer2'; } else { $referrer_field = 'referrer'; }
$date_criteria = "AND (date BETWEEN '".$_POST['affiliation_form_statistics_start_date']."' AND '".$_POST['affiliation_form_statistics_end_date']."')";
if (strstr($data, 'commission2')) { $status_field = 'commission2_status'; }
elseif (strstr($data, 'commission')) { $status_field = 'commission_status'; }
else { $status_field = 'status'; }
if ($status != '') { $status_criteria = "AND $status_field = '".$status."'"; }

$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix.$table." WHERE $referrer_field = '".$_SESSION['affiliation_login']."' $criteria $date_criteria $status_criteria ORDER BY $orderby $order", OBJECT);
$keys = array_unique(array($type.'_id', $type.'_data', $type2.'_id', $type2.'_data', 'contact_form_id', 'contact_form_data', 'optin_form_id', 'optin_form_data', 'order_id', 'order_data', 'product_id', 'product_data'));
foreach ($keys as $key) { if (isset($_GET[$key])) { $original[$key] = $_GET[$key]; } }

foreach ($items as $item) {
foreach (array($type, $type2) as $string) {
$_GET[$string.'_id'] = $item->id;
$_GET[$string.'_data'] = (array) $item; }
$_GET['contact_form_id'] = $item->form_id;
$_GET['optin_form_id'] = $item->form_id;
$_GET['product_id'] = $item->product_id;
if ($type == 'recurring_payment') { $_GET['order_id'] = $item->order_id; }
$content .= do_shortcode($original_content); }

foreach ($keys as $key) { if (isset($original[$key])) { $_GET[$key] = $original[$key]; } }
if ($type == 'affiliate') { unset($_GET['affiliation_affiliates_statistics']); }
elseif ($type == 'client') { unset($_GET['affiliation_clients_statistics']); }
return $content; }


function affiliation_statistics_if($atts, $content) {
global $wpdb;
$content = explode('[else]', do_shortcode($content));
$type = str_replace('-', '_', format_nice_name($atts[0]));
$status = str_replace('-', '_', format_nice_name($atts['status']));

switch ($type) {
case 'affiliate': $table = 'affiliation_manager_affiliates'; break;
case 'click': $table = 'affiliation_manager_clicks'; break;
case 'client': $table = 'commerce_manager_clients'; break;
case 'commission1': $table = 'commerce_manager_orders'; $criteria = "AND (commission_amount > 0)"; break;
case 'commission2': $table = 'commerce_manager_orders'; $criteria = "AND (commission2_amount > 0)"; break;
case 'message': $table = 'contact_manager_messages'; break;
case 'message_commission1': $table = 'contact_manager_messages'; $criteria = "AND (commission_amount > 0)"; break;
case 'message_commission2': $table = 'contact_manager_messages'; $criteria = "AND (commission2_amount > 0)"; break;
case 'order': $table = 'commerce_manager_orders'; break;
case 'prospect': $table = 'optin_manager_prospects'; break;
case 'prospect_commission1': $table = 'optin_manager_prospects'; $criteria = "AND (commission_amount > 0)"; break;
case 'prospect_commission2': $table = 'optin_manager_prospects'; $criteria = "AND (commission2_amount > 0)"; break;
case 'recurring_commission1': $table = 'commerce_manager_recurring_payments'; $criteria = "AND (commission_amount > 0)"; break;
case 'recurring_commission2': $table = 'commerce_manager_recurring_payments'; $criteria = "AND (commission2_amount > 0)"; break;
case 'recurring_payment': $table = 'commerce_manager_recurring_payments'; break;
default: $table = 'commerce_manager_orders'; }

if (strstr($criteria, 'commission2')) { $referrer_field = 'referrer2'; } else { $referrer_field = 'referrer'; }
$date_criteria = "AND (date BETWEEN '".$_POST['affiliation_form_statistics_start_date']."' AND '".$_POST['affiliation_form_statistics_end_date']."')";
if (strstr($data, 'commission2')) { $status_field = 'commission2_status'; }
elseif (strstr($data, 'commission')) { $status_field = 'commission_status'; }
else { $status_field = 'status'; }
if ($status != '') { $status_criteria = "AND $status_field = '".$status."'"; }

$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix.$table." WHERE $referrer_field = '".$_SESSION['affiliation_login']."' $criteria $date_criteria $status_criteria", OBJECT);
$number = (int) $row->total;
if ($number > 0) { $n = 0; } else { $n = 1; }
return $content[$n]; }