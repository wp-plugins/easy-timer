<?php global $wpdb;
switch ($type) {
case 'author': $table = 'authors'; $default_field = 'first_name'; break;
case 'narrator': $table = 'narrators'; $default_field = 'first_name'; }
$GLOBALS[$type.'_data'] = (array) (isset($GLOBALS[$type.'_data']) ? $GLOBALS[$type.'_data'] : array());
if ((isset($GLOBALS[$type.'_id'])) && ((!isset($GLOBALS[$type.'_data']['id'])) || ($GLOBALS[$type.'_data']['id'] != $GLOBALS[$type.'_id']))) {
$n = $GLOBALS[$type.'_id']; if (isset($GLOBALS[$type.$n.'_data'])) { $GLOBALS[$type.$n.'_data'] = (array) $GLOBALS[$type.$n.'_data']; $GLOBALS[$type.'_data'] = $GLOBALS[$type.$n.'_data']; }
elseif ($GLOBALS[$type.'_id'] > 0) { $GLOBALS[$type.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."audiobooks_authors_and_narrators_".$table." WHERE id = ".$GLOBALS[$type.'_id'], OBJECT); } }
if (isset($GLOBALS[$type.'_data']['id'])) { $n = $GLOBALS[$type.'_data']['id']; $GLOBALS[$type.$n.'_data'] = $GLOBALS[$type.'_data']; }
$item_data = $GLOBALS[$type.'_data'];
if (is_string($atts)) { $is_array = false; $field = $atts; $decimals = ''; $default = ''; $filter = ''; $id = 0; $part = 0; }
else {
$is_array = true;
$atts = array_map('audiobooks_authors_and_narrators_do_shortcode', (array) $atts);
$field = (isset($atts[0]) ? $atts[0] : '');
foreach (array('decimals', 'default', 'filter') as $key) {
$$key = (isset($atts[$key]) ? $atts[$key] : '');
if (isset($atts[$key])) { unset($atts[$key]); } }
if (!isset($atts['id'])) { $id = 0; }
elseif (format_nice_name($atts['id']) == 'get') { $id = (int) (isset($_GET[$type.'_id']) ? $_GET[$type.'_id'] : (isset($_GET['id']) ? $_GET['id'] : 0)); }
else { $id = (int) preg_replace('/[^0-9]/', '', $atts['id']); }
$part = (int) (isset($atts['part']) ? preg_replace('/[^0-9]/', '', $atts['part']) : 0); }
$field = str_replace('-', '_', format_nice_name($field));
if ($field == '') { $field = $default_field; }
if (($id > 0) && ((!isset($item_data['id'])) || ($id != $item_data['id']))) {
foreach (array($type.'_id', $type.'_data') as $key) {
if (isset($GLOBALS[$key])) { $original[$key] = $GLOBALS[$key]; } }
if (!isset($GLOBALS[$type.$id.'_data'])) { $GLOBALS[$type.$id.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."audiobooks_authors_and_narrators_".$table." WHERE id = $id", OBJECT); }
$item_data = (array) $GLOBALS[$type.$id.'_data'];
$GLOBALS[$type.'_id'] = $id; $GLOBALS[$type.'_data'] = $item_data; }
$data = (isset($item_data[$field]) ? $item_data[$field] : '');
if ($part > 0) { $data = explode(',', $data); $data = (isset($data[$part - 1]) ? trim($data[$part - 1]) : ''); }
$data = (string) do_shortcode($data);
if ($data === '') { $data = $default; }
$data = audiobooks_authors_and_narrators_format_data($field, $data);
if ($data === '') { $data = $default; }
$data = audiobooks_authors_and_narrators_filter_data($filter, $data);
$data = audiobooks_authors_and_narrators_decimals_data($decimals, $data);
foreach (array($type.'_id', $type.'_data') as $key) {
if (isset($original[$key])) { $GLOBALS[$key] = $original[$key]; } }