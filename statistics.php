<?php function commerce_statistics($atts) {
if (commerce_session()) {
if (is_string($atts)) { $type = $atts; }
else { $type = str_replace('-', '_', format_nice_name($atts['type'])); }
$tags = array('foreach', 'if');
foreach ($tags as $tag) { add_shortcode($tag, 'commerce_statistics_'.str_replace('-', '_', $tag)); }
$content = do_shortcode(get_option('commerce_manager_'.$type.'_statistics_code'));
foreach ($tags as $tag) { remove_shortcode($tag); }
return $content; } }


function commerce_statistics_foreach($atts, $content) {
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
case 'order': $table = 'commerce_manager_orders'; break;
case 'recurring_payment': $table = 'commerce_manager_recurring_payments'; break;
default: $type = 'order'; $table = 'commerce_manager_orders'; }

$date_criteria = "AND (date BETWEEN '".$_POST['commerce_form_statistics_start_date']."' AND '".$_POST['commerce_form_statistics_end_date']."')";
if ($status != '') { $status_criteria = "AND status = '".$status."'"; }

$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix.$table." WHERE client_id = ".client_data('id')." $date_criteria $status_criteria ORDER BY $orderby $order", OBJECT);
$keys = array_unique(array($type.'_id', $type.'_data', 'order_id', 'order_data', 'product_id', 'product_data'));
foreach ($keys as $key) { if (isset($_GET[$key])) { $original[$key] = $_GET[$key]; } }

foreach ($items as $item) {
$_GET[$type.'_id'] = $item->id;
$_GET[$type.'_data'] = (array) $item;
$_GET['product_id'] = $item->product_id;
if ($type == 'recurring_payment') { $_GET['order_id'] = $item->order_id; }
$content .= do_shortcode($original_content); }

foreach ($keys as $key) { if (isset($original[$key])) { $_GET[$key] = $original[$key]; } }
return $content; }


function commerce_statistics_if($atts, $content) {
global $wpdb;
$content = explode('[else]', do_shortcode($content));
$type = str_replace('-', '_', format_nice_name($atts[0]));
$status = str_replace('-', '_', format_nice_name($atts['status']));

switch ($type) {
case 'order': $table = 'commerce_manager_orders'; break;
case 'recurring_payment': $table = 'commerce_manager_recurring_payments'; break;
default: $table = 'commerce_manager_orders'; }

$date_criteria = "AND (date BETWEEN '".$_POST['commerce_form_statistics_start_date']."' AND '".$_POST['commerce_form_statistics_end_date']."')";
if ($status != '') { $status_criteria = "AND status = '".$status."'"; }

$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix.$table." WHERE client_id = ".client_data('id')." $date_criteria $status_criteria", OBJECT);
$number = (int) $row->total;
if ($number > 0) { $n = 0; } else { $n = 1; }
return $content[$n]; }