<?php function commerce_activation_url($atts) {
extract(shortcode_atts(array('filter' => ''), $atts));
$id = client_data('id');
$key = hash('sha256', $id.commerce_data('encrypted_urls_key'));
$url = COMMERCE_MANAGER_URL.'?action=activate&id='.$id.'&key='.$key;
$url = commerce_filter_data($filter, $url);
return $url; }


function commerce_comments($atts) {
if (!is_admin()) {
extract(shortcode_atts(array('condition' => 'session'), $atts));
$condition = strtolower($condition);
switch ($condition) {
case 'session': if (!commerce_session()) {
foreach (array('comments_array', 'comments_open') as $function) { add_filter($function, 'commerce_'.$function, 1, 0); } } break;
case '!session': if (commerce_session()) {
foreach (array('comments_array', 'comments_open') as $function) { add_filter($function, 'commerce_'.$function, 1, 0); } } } } }


function commerce_content($atts, $content) {
$content = explode('[other]', do_shortcode($content));
if (commerce_session()) { $n = 0; } else { $n = 1; }
return $content[$n]; }


function commerce_counter_tag($atts) {
extract(shortcode_atts(array('data' => '', 'filter' => ''), $atts));
$string = $_GET['commerce_'.str_replace('-', '_', format_nice_name($data))];
$string = commerce_filter_data($filter, $string);
return $string; }


function commerce_counter($atts, $content) {
include dirname(__FILE__).'/counter.php';
return $content[$k]; }


function client_counter($atts, $content) {
$type = 'client';
include dirname(__FILE__).'/counter.php';
return $content[$k]; }


function commerce_form_counter($atts, $content) {
$type = 'commerce_form';
include dirname(__FILE__).'/counter.php';
return $content[$k]; }


function inventory_counter($atts, $content) {
$type = 'inventory';
include dirname(__FILE__).'/counter.php';
return $content[$k]; }


function product_counter($atts, $content) {
$type = 'product';
include dirname(__FILE__).'/counter.php';
return $content[$k]; }


function commerce_redirection($atts) {
if (!is_admin()) {
extract(shortcode_atts(array('action' => '', 'condition' => '', 'url' => ''), $atts));
$action = strtolower($action);
$condition = strtolower($condition);
if ($url == '') { $url = '../'; }
switch ($condition) {
case 'session': if (commerce_session()) {
if ($action == 'logout') { commerce_logout(); }
if (!headers_sent()) { header('Location: '.$url); exit; } } else { $url = ''; } break;
case '!session': if (!commerce_session()) {
if (!headers_sent()) { header('Location: '.$url); exit; } } else { $url = ''; } break;
default: if (($action == 'logout') && (commerce_session())) { commerce_logout(); }
if (!headers_sent()) { header('Location: '.$url); exit; } }
return $url; } }


function purchase_button($atts) {
extract(shortcode_atts(array('alt' => '', 'code' => '', 'gateway' => 'paypal', 'id' => 0, 'option' => 0, 'quantity' => 1, 'src' => ''), $atts));
if ($code != '') { $_GET['code'] = $code; }
$quantity = (int) $quantity;
$gateway = str_replace('_', '-', format_nice_name($gateway));
$id = (int) $id;
if ($id == 0) { $id = $_GET['product_id']; }
$payment_option = (int) $option;
if (($payment_option < 0) || ($payment_option > 3)) { $payment_option = 0; }
if ($payment_option == 0) { $payments_number = 1; }
else { $payments_number = product_data(array(0 => 'payments_number'.$payment_option, 'id' => $id)); }
if ($src == '') {
switch ($payments_number) {
case 2: case 3: case 4: $src = product_data(array(0 => 'purchase_button_url'.$payments_number, 'id' => $id)); break;
case 'unlimited': $src = product_data(array(0 => 'subscription_button_url', 'id' => $id)); break;
default: $src = product_data(array(0 => 'purchase_button_url', 'id' => $id)); } }
if ($alt == '') {
switch ($payments_number) {
case 2: case 3: case 4: $alt = product_data(array(0 => 'purchase_button_text'.$payments_number, 'id' => $id)); break;
case 'unlimited': $alt = product_data(array(0 => 'subscription_button_text', 'id' => $id)); break;
default: $alt = product_data(array(0 => 'purchase_button_text', 'id' => $id)); } }
return '<form method="post" action="'.COMMERCE_MANAGER_URL.'?action=order">
<div><input type="hidden" name="code" value="'.$_GET['code'].'" />
<input type="hidden" name="gateway" value="'.$gateway.'" />
<input type="hidden" name="payment_option" value="'.$payment_option.'" />
<input type="hidden" name="product_id" value="'.$id.'" />
<input type="hidden" name="quantity" value="'.$quantity.'" />
<input type="hidden" name="referring_url" value="'.htmlspecialchars($_SERVER['HTTP_REFERER']).'" />
<input type="image" name="purchase" src="'.htmlspecialchars($src).'" alt="'.$alt.'" /></div>
</form>'; }


function purchase_content($atts, $content) {
global $wpdb;
extract(shortcode_atts(array('id' => ''), $atts));
$content = explode('[other]', do_shortcode($content));
$products = array_unique(preg_split('#[^0-9]#', $id, 0, PREG_SPLIT_NO_EMPTY));
if (is_admin()) { if ((count($products) == 0) || (in_array($_GET['product_id'], $products))) { $n = 0; } else { $n = 1; } }
else {
$condition = "ip_address = '".$_SERVER['REMOTE_ADDR']."'";
$ip_address = order_data('ip_address');
if ($ip_address != '') { $ip_address .= " OR ip_address = '".$ip_address."'"; }
if (count($products) > 0) {
foreach ($products as $product) { $search_criteria .= " OR product_id = ".$product; }
$search_criteria = 'AND ('.substr($search_criteria, 4).')'; }
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."commerce_manager_orders WHERE ($condition) $search_criteria", OBJECT);
if ($result) { $n = 0; } else { $n = 1; } }
return $content[$n]; }


function purchase_link($atts) {
extract(shortcode_atts(array('code' => '', 'gateway' => 'paypal', 'id' => 0, 'option' => 0, 'quantity' => 1, 'text' => ''), $atts));
if ($code != '') { $_GET['code'] = $code; }
$quantity = (int) $quantity;
$gateway = str_replace('_', '-', format_nice_name($gateway));
$id = (int) $id;
if ($id == 0) { $id = $_GET['product_id']; }
$payment_option = (int) $option;
if (($payment_option < 0) || ($payment_option > 3)) { $payment_option = 0; }
if ($payment_option == 0) { $payments_number = 1; }
else { $payments_number = product_data(array(0 => 'payments_number'.$payment_option, 'id' => $id)); }
if ($text == '') {
switch ($payments_number) {
case 2: case 3: case 4: $text = product_data(array(0 => 'purchase_link_text'.$payments_number, 'id' => $id)); break;
case 'unlimited': $text = product_data(array(0 => 'subscription_link_text', 'id' => $id)); break;
default: $text = product_data(array(0 => 'purchase_link_text', 'id' => $id)); } }
$variables =
'&amp;code='.$_GET['code'].
'&amp;gateway='.$gateway.
'&amp;payment_option='.$payment_option.
'&amp;product_id='.$id.
'&amp;quantity='.$quantity;
return '<a href="'.COMMERCE_MANAGER_URL.'?action=order'.$variables.'">'.$text.'</a>'; }