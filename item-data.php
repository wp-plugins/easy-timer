<?php global $wpdb;
if (strstr($type, 'category')) { $attribute = 'category'; } else { $attribute = 'id'; }
switch ($type) {
case 'optin_form': $table = 'forms'; $default_field = 'name'; break;
case 'optin_form_category': $table = 'forms_categories'; $default_field = 'name'; break;
case 'prospect': $table = 'prospects'; $default_field = 'first_name'; break; }
$_GET[$type.'_data'] = (array) $_GET[$type.'_data'];
if ((isset($_GET[$type.'_id'])) && ($_GET[$type.'_data']['id'] != $_GET[$type.'_id'])) {
$n = $_GET[$type.'_id']; $_GET[$type.$n.'_data'] = (array) $_GET[$type.$n.'_data'];
if ($_GET[$type.$n.'_data']['id'] == $_GET[$type.'_id']) { $_GET[$type.'_data'] = $_GET[$type.$n.'_data']; }
else { $_GET[$type.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_".$table." WHERE id = ".$_GET[$type.'_id'], OBJECT); } }
if ((!is_admin()) && ($type == 'prospect') && (!isset($_GET[$type.'_data']['email_address']))) {
$_GET[$type.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_".$table." WHERE ip_address = '".$_SERVER['REMOTE_ADDR']."' ORDER BY date DESC LIMIT 1", OBJECT);
$n = $_GET[$type.'_data']['id']; $_GET[$type.$n.'_data'] = $_GET[$type.'_data']; }
$item_data = $_GET[$type.'_data'];
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $id = 0; $part = 0; }
else {
$field = $atts[0];
$default = $atts['default'];
unset($atts['default']);
$filter = $atts['filter'];
unset($atts['filter']);
$id = (int) do_shortcode(str_replace(array('(', ')'), array('[', ']'), $atts[$attribute]));
$part = (int) $atts['part']; }
$field = str_replace('-', '_', format_nice_name($field));
if ($field == '') { $field = $default_field; }
if (($id == 0) || ($id == $item_data['id'])) { $data = $item_data[$field]; }
elseif ($id > 0) {
foreach (array($type.'_id', $type.'_data') as $key) {
if (isset($_GET[$key])) { $original[$key] = $_GET[$key]; } }
if ($_GET[$type.$id.'_data']['id'] != $id) {
$_GET[$type.$id.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_".$table." WHERE id = $id", OBJECT); }
$item_data = $_GET[$type.$id.'_data'];
if ($attribute == 'id') { $_GET[$type.'_id'] = $id; $_GET[$type.'_data'] = $item_data; }
$data = $item_data[$field]; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
switch ($type) {
case 'optin_form': case 'optin_form_category':
$data = (string) $data;
if ($data != '') { $data = optin_format_data($field, $data); }
$data = (string) $data;
if (($data == '') && ($item_data['category_id'] > 0)) {
if (is_string($atts)) { $atts = array($field); }
$atts['category'] = $item_data['category_id'];
$data = optin_form_category_data($atts); }
elseif ($data == '') {
if (is_array($atts)) { unset($atts['category']); }
$data = optin_data($atts); } break; }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = optin_format_data($field, $data);
$data = optin_filter_data($filter, $data);
foreach (array($type.'_id', $type.'_data') as $key) {
if (isset($original[$key])) { $_GET[$key] = $original[$key]; } }