<?php global $wpdb;
if (strstr($type, 'category')) { $attribute = 'category'; } else { $attribute = 'id'; }
switch ($type) {
case 'member': $table = 'members'; $default_field = 'login'; break;
case 'member_area': $table = 'members_areas'; $default_field = 'name'; break;
case 'member_area_category': $table = 'members_areas_categories'; $default_field = 'name'; break;
case 'member_category': $table = 'members_categories'; $default_field = 'name'; break; }
$_GET[$type.'_data'] = (array) $_GET[$type.'_data'];
switch ($type) {
case 'member': if ((!is_admin()) && ($_GET['action'] != 'activate') && ($_GET['action'] != 'order') && ($_GET['action'] != 'commerce_notification') && (membership_session('')) && ($_GET[$type.'_data']['login'] != $_SESSION['membership_login'])) {
$_GET[$type.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_".$table." WHERE login = '".$_SESSION['membership_login']."'", OBJECT); } break;
default: if ((isset($_GET[$type.'_id'])) && ($_GET[$type.'_data']['id'] != $_GET[$type.'_id'])) {
$n = $_GET[$type.'_id']; $_GET[$type.$n.'_data'] = (array) $_GET[$type.$n.'_data'];
if ($_GET[$type.$n.'_data']['id'] == $_GET[$type.'_id']) { $_GET[$type.'_data'] = $_GET[$type.$n.'_data']; }
else { $_GET[$type.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_".$table." WHERE id = ".$_GET[$type.'_id'], OBJECT); } } }
if ((!is_admin()) && ($type == 'member') && (!isset($_GET[$type.'_data']['email_address']))) {
$_GET[$type.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_".$table." WHERE ip_address = '".$_SERVER['REMOTE_ADDR']."' ORDER BY date DESC LIMIT 1", OBJECT);
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
$_GET[$type.$id.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_".$table." WHERE id = $id", OBJECT); }
$item_data = $_GET[$type.$id.'_data'];
if ($attribute == 'id') { $_GET[$type.'_id'] = $id; $_GET[$type.'_data'] = $item_data; }
$data = $item_data[$field]; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
$data = (string) $data;
if ($data != '') { $data = membership_format_data($field, $data); }
$data = (string) $data;
if (($data == '') && ($item_data['category_id'] > 0)) {
if (is_string($atts)) { $atts = array($field); }
$atts['category'] = $item_data['category_id'];
$data = (strstr($type, 'area') ? member_area_category_data($atts) : member_category_data($atts)); }
elseif ($data == '') {
if (is_array($atts)) { unset($atts['category']); }
$data = membership_data($atts); }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = membership_format_data($field, $data);
$data = membership_filter_data($filter, $data);
foreach (array($type.'_id', $type.'_data') as $key) {
if (isset($original[$key])) { $_GET[$key] = $original[$key]; } }