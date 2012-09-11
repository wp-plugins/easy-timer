<?php global $wpdb;
if (strstr($type, 'category')) { $attribute = 'category'; } else { $attribute = 'id'; }
switch ($type) {
case 'affiliate': case 'referrer': $table = 'affiliation_manager_affiliates'; $default_field = 'login'; break;
case 'affiliate_category': $table = 'affiliation_manager_affiliates_categories'; $default_field = 'name'; break;
case 'click': $table = 'affiliation_manager_clicks'; $default_field = 'referrer'; break;
case 'commission': $table = 'commerce_manager_orders'; $default_field = 'referrer'; break;
case 'message_commission': $table = 'contact_manager_messages'; $default_field = 'referrer'; break;
case 'prospect_commission': $table = 'optin_manager_prospects'; $default_field = 'referrer'; break;
case 'recurring_commission': $table = 'commerce_manager_recurring_payments'; $default_field = 'referrer'; break; }
$_GET[$type.'_data'] = (array) $_GET[$type.'_data'];
switch ($type) {
case 'affiliate': if ((!is_admin()) && ($_GET['affiliation_affiliates_statistics'] != 'yes') && ($_GET['action'] != 'activate') && ($_GET['action'] != 'order') && ($_GET['action'] != 'commerce_notification') && (affiliation_session()) && ($_GET[$type.'_data']['login'] != $_SESSION['affiliation_login'])) {
$_GET[$type.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.$table." WHERE login = '".$_SESSION['affiliation_login']."'", OBJECT); } break;
case 'referrer': if ((is_admin()) && (isset($_POST['referrer'])) && (!isset($_GET['referrer']))) { $_GET['referrer'] = $_POST['referrer']; }
if ((isset($_GET['referrer'])) && (!strstr($_GET['referrer'], '@')) && ($_GET[$type.'_data']['login'] != $_GET['referrer'])) {
$_GET[$type.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.$table." WHERE login = '".$_GET['referrer']."'", OBJECT); } break;
default: if ((isset($_GET[$type.'_id'])) && ($_GET[$type.'_data']['id'] != $_GET[$type.'_id'])) {
$n = $_GET[$type.'_id']; $_GET[$type.$n.'_data'] = (array) $_GET[$type.$n.'_data'];
if ($_GET[$type.$n.'_data']['id'] == $_GET[$type.'_id']) { $_GET[$type.'_data'] = $_GET[$type.$n.'_data']; }
else { $_GET[$type.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.$table." WHERE id = ".$_GET[$type.'_id'], OBJECT); } } }
if ((!is_admin()) && ($type == 'affiliate') && (!isset($_GET[$type.'_data']['email_address']))) {
$_GET[$type.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.$table." WHERE ip_address = '".$_SERVER['REMOTE_ADDR']."' ORDER BY date DESC LIMIT 1", OBJECT);
$n = $_GET[$type.'_data']['id']; $_GET[$type.$n.'_data'] = $_GET[$type.'_data']; }
$item_data = $_GET[$type.'_data'];
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $id = array(0); $part = 0; }
else {
$field = $atts[0];
$default = $atts['default'];
unset($atts['default']);
$filter = $atts['filter'];
unset($atts['filter']);
$id = preg_split('#[^0-9]#', do_shortcode(str_replace(array('(', ')'), array('[', ']'), $atts[$attribute])), 0, PREG_SPLIT_NO_EMPTY);
$part = (int) $atts['part']; }
$field = str_replace('-', '_', format_nice_name($field));
if ($field == '') { $field = $default_field; }

$m = count($id);
if ($m < 2) {
$id = (int) $id[0];
if (($id == 0) || ($id == $item_data['id'])) { $data = $item_data[$field]; }
elseif ($id > 0) {
foreach (array($type.'_id', $type.'_data') as $key) {
if (isset($_GET[$key])) { $original[$key] = $_GET[$key]; } }
if ($_GET[$type.$id.'_data']['id'] != $id) {
$_GET[$type.$id.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.$table." WHERE id = $id", OBJECT); }
$item_data = $_GET[$type.$id.'_data'];
if ($attribute == 'id') { $_GET[$type.'_id'] = $id; $_GET[$type.'_data'] = $item_data; }
$data = $item_data[$field]; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
switch ($type) {
case 'affiliate': case 'affiliate_category': case 'referrer':
$data = (string) $data;
if ($data != '') { $data = affiliation_format_data($field, $data); }
$data = (string) $data;
if (($data == '') && ($item_data['category_id'] > 0)) {
if (is_string($atts)) { $atts = array($field); }
$atts['category'] = $item_data['category_id'];
$data = affiliate_category_data($atts); }
elseif ($data == '') {
if (is_array($atts)) { unset($atts['category']); }
$data = affiliation_data($atts); } break; } }

else {
$data = 0; for ($i = 0; $i < $m; $i++) {
$atts[$attribute] = (int) $id[$i];
$data = $data + affiliation_item_data($type, $atts); } }

$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = affiliation_format_data($field, $data);
$data = affiliation_filter_data($filter, $data);
foreach (array($type.'_id', $type.'_data') as $key) {
if (isset($original[$key])) { $_GET[$key] = $original[$key]; } }