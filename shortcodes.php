<?php function audiobook_page_content($atts) {
$atts = array_map('audiobooks_page_do_shortcode', (array) $atts);
extract(shortcode_atts(array('filter' => '', 'products' => ''), $atts));
$products = array_unique(array_map('intval', preg_split('#[^0-9]#', $products, 0, PREG_SPLIT_NO_EMPTY)));
if ($products != array()) { $GLOBALS['audiobook_product_ids'] = $products; $GLOBALS['product_id'] = $GLOBALS['audiobook_product_ids'][0]; }
$content = audiobooks_pages_data('page_content');
return commerce_filter_data($filter, $content); }


function audiobook_product_selector($atts) {
if ((isset($GLOBALS['commerce_form_id'])) && (isset($GLOBALS['audiobook_product_ids']))) {
$products = (array) $GLOBALS['audiobook_product_ids'];
if ($products != array()) {
$form_id = $GLOBALS['commerce_form_id'];
$prefix = $GLOBALS['commerce_form_prefix'];
$atts = commerce_shortcode_atts(array(), $atts);
$markup = '';
$name = 'product_id';
$GLOBALS[$prefix.'fields'][] = $name;
foreach (array($name, str_replace('_', '-', $name)) as $key) {
if (((!isset($_POST[$prefix.$name])) || ($_POST[$prefix.$name] == '')) && (isset($_GET[$key]))) { $_POST[$prefix.$name] = htmlspecialchars($_GET[$key]); } }
$products_list = '';
foreach ($products as $product) { $products_list .= '<option value="'.$product.'"'.(((isset($_POST[$prefix.$name])) && ($_POST[$prefix.$name] == $product)) ? ' selected="selected"' : '').'>'.do_shortcode('[product name id='.$product.']').'</option>'."\n"; }
foreach ($atts as $key => $value) { if ((!in_array($key, array('id', 'name'))) && (is_string($key)) && ($value != '')) { $c = (strstr($value, '"') ? "'" : '"'); $markup .= ' '.$key.'='.$c.$value.$c; } }
$content = '<select name="'.$prefix.$name.'" id="'.$prefix.$name.'"'.$markup.'>'.$products_list.'</select>';
return $content; } } }


function audiobook_store($atts) {
$atts = array_map('audiobooks_page_do_shortcode', (array) $atts);
extract(shortcode_atts(array('filter' => '', 'category' => ''), $atts));
$category = (int) preg_replace('/[^0-9]/', '', $category);
$products = array_unique(array_map('intval', preg_split('#[^0-9]#', audiobook_category_data(array(0 => 'audiobooks', 'id' => $category)), 0, PREG_SPLIT_NO_EMPTY)));
$content = ''; foreach ($products as $product) { $GLOBALS['product_id'] = $product; $content .= audiobooks_pages_data('category_page_content'); }
return commerce_filter_data($filter, $content); }