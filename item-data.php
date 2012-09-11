<?php global $wpdb;
if (strstr($type, 'category')) { $attribute = 'category'; } else { $attribute = 'id'; }
switch ($type) {
case 'client': $table = 'clients'; $default_field = 'login'; break;
case 'client_category': $table = 'clients_categories'; $default_field = 'name'; break;
case 'commerce_form': $table = 'forms'; $default_field = 'name'; break;
case 'commerce_form_category': $table = 'forms_categories'; $default_field = 'name'; break;
case 'order': $table = 'orders'; $default_field = 'first_name'; break;
case 'product': $table = 'products'; $default_field = 'name'; break;
case 'product_category': $table = 'products_categories'; $default_field = 'name'; break;
case 'recurring_payment': $table = 'recurring_payments'; $default_field = 'amount'; break; }
$_GET[$type.'_data'] = (array) $_GET[$type.'_data'];
switch ($type) {
case 'client': if ((!is_admin()) && ($_GET['affiliation_clients_statistics'] != 'yes') && ($_GET['action'] != 'activate') && ($_GET['action'] != 'order') && ($_GET['action'] != 'commerce_notification') && (commerce_session()) && ($_GET[$type.'_data']['login'] != $_SESSION['commerce_login'])) {
$_GET[$type.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_".$table." WHERE login = '".$_SESSION['commerce_login']."'", OBJECT); } break;
default: if ((isset($_GET[$type.'_id'])) && ($_GET[$type.'_data']['id'] != $_GET[$type.'_id'])) {
$n = $_GET[$type.'_id']; $_GET[$type.$n.'_data'] = (array) $_GET[$type.$n.'_data'];
if ($_GET[$type.$n.'_data']['id'] == $_GET[$type.'_id']) { $_GET[$type.'_data'] = $_GET[$type.$n.'_data']; }
else { $_GET[$type.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_".$table." WHERE id = ".$_GET[$type.'_id'], OBJECT); } } }
if ((!is_admin()) && (($type == 'client') || ($type == 'order')) && (!isset($_GET[$type.'_data']['email_address']))) {
$_GET[$type.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_".$table." WHERE ip_address = '".$_SERVER['REMOTE_ADDR']."' ORDER BY date DESC LIMIT 1", OBJECT);
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
$_GET[$type.$id.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_".$table." WHERE id = $id", OBJECT); }
$item_data = $_GET[$type.$id.'_data'];
if ($attribute == 'id') { $_GET[$type.'_id'] = $id; $_GET[$type.'_data'] = $item_data; }
$data = $item_data[$field]; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
switch ($type) {
case 'client': case 'client_category':
$data = (string) $data;
if ($data != '') { $data = commerce_format_data($field, $data); }
$data = (string) $data;
if (($data == '') && ($item_data['category_id'] > 0)) {
if (is_string($atts)) { $atts = array($field); }
$atts['category'] = $item_data['category_id'];
$data = client_category_data($atts); }
elseif ($data == '') {
if (is_array($atts)) { unset($atts['category']); }
$data = commerce_data($atts); } break;
case 'commerce_form': case 'commerce_form_category':
$data = (string) $data;
if ($data != '') { $data = commerce_format_data($field, $data); }
$data = (string) $data;
if (($data == '') && ($item_data['category_id'] > 0)) {
if (is_string($atts)) { $atts = array($field); }
$atts['category'] = $item_data['category_id'];
$data = commerce_form_category_data($atts); }
elseif ($data == '') {
if (is_array($atts)) { unset($atts['category']); }
$data = commerce_data($atts); } break;
case 'product': case 'product_category':
$data = (string) $data;
if ($data != '') { $data = commerce_format_data($field, $data); }
$data = (string) $data;
if (($data == '') && ($item_data['category_id'] > 0)) {
if (is_string($atts)) { $atts = array($field); }
$atts['category'] = $item_data['category_id'];
$data = product_category_data($atts); }
elseif ($data == '') {
if (is_array($atts)) { unset($atts['category']); }
switch ($field) {
case 'affiliation_enabled': case 'commission_amount': case 'commission_payment':
case 'commission_percentage': case 'commission_type': case 'commission2_amount':
case 'commission2_enabled': case 'commission2_percentage': case 'commission2_type': case 'first_sale_winner':
case 'registration_required': if (function_exists('affiliate_data')) { $data = affiliate_data($atts); } break;
default: $data = commerce_data($atts); } } break; } }

else {
$data = 0; for ($i = 0; $i < $m; $i++) {
$atts[$attribute] = (int) $id[$i];
$data = $data + commerce_item_data($type, $atts); } }

$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = commerce_format_data($field, $data);
$data = commerce_filter_data($filter, $data);
foreach (array($type.'_id', $type.'_data') as $key) {
if (isset($original[$key])) { $_GET[$key] = $original[$key]; } }