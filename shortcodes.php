<?php function optin_content($atts, $content) {
global $wpdb;
extract(shortcode_atts(array('list' => ''), $atts));
$content = explode('[other]', do_shortcode($content));
$lists = explode('/', $list);
if ((is_admin()) && ($_GET['page'] == 'optin-manager-prospect')) {
if ($list == '') { $n = 0; }
else { if (in_array($_POST['autoresponder_list'], $lists)) { $n = 0; } else { $n = 1; } } }
else {
if ($list == '') { $search_criteria = ''; }
else {
foreach ($lists as $list) { $search_criteria .= " OR autoresponder_list = '".$list."'"; }
$search_criteria = 'AND ('.substr($search_criteria, 4).')'; }
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."optin_manager_prospects WHERE ip_address = '".$_SERVER['REMOTE_ADDR']."' $search_criteria", OBJECT);
if ($result) { $n = 0; } else { $n = 1; } }
return $content[$n]; }


function optin_counter_tag($atts) {
extract(shortcode_atts(array('data' => '', 'filter' => ''), $atts));
$string = $_GET['optin_'.str_replace('-', '_', format_nice_name($data))];
$string = optin_filter_data($filter, $string);
return $string; }


function optin_counter($atts, $content) {
include dirname(__FILE__).'/counter.php';
return $content[$k]; }


function optin_form_counter($atts, $content) {
$type = 'optin_form';
include dirname(__FILE__).'/counter.php';
return $content[$k]; }