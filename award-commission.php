<?php global $wpdb;
$leveltype = $level.$type;
switch ($leveltype) {
case '1':
if (($_GET['action'] == 'commerce_notification') && (isset($_GET['recurring_payments_profile_number']))) { $price = $_GET['price']; $_GET['quantity'] = 1; }
else { $price = product_data('price'); }
foreach (array('affiliation_enabled', 'commission_payment') as $field) { $_GET[$field] = product_data($field); }
if (($_GET['affiliation_enabled'] == 'no') || ((strstr($_GET['referrer'], '@')) && ($_GET['commission_payment'] == 'deferred'))) { $_GET['sale_winner'] = 'affiliator'; $_GET['commission_amount'] = 0; }
else {
if (!strstr($_GET['referrer'], '@')) {
$_GET['affiliate_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_GET['referrer']."'", OBJECT);
$_GET['referrer_data'] = $_GET['affiliate_data'];
if ($_GET['referrer_data']['status'] != 'active') { $_GET['sale_winner'] = 'affiliator'; $_GET['commission_amount'] = 0; } }
if ((strstr($_GET['referrer'], '@')) || ($_GET['referrer_data']['status'] == 'active')) {
foreach (array(
'commission_amount',
'commission_percentage',
'commission_type',
'first_sale_winner',
'registration_required') as $field) { $_GET[$field] = product_data($field); }
if ($_GET['commission_payment'] == 'deferred') {
$_GET['sale_winner'] = 'affiliator';
if ($_GET['commission_type'] == 'constant') { $_GET['commission_amount'] = $_GET['quantity']*$_GET['commission_amount']; }
elseif ($_GET['commission_type'] == 'proportional') { $_GET['commission_amount'] = round($_GET['quantity']*$_GET['commission_percentage']*$price)/100; } }
else {
if ((strstr($_GET['referrer'], '@')) && ($_GET['registration_required'] == 'yes')) { $_GET['sale_winner'] = 'affiliator'; }
else {
if ($_GET['commission_type'] == 'constant') { $_GET['commission_percentage'] = 100*($_GET['commission_amount'])/$price; }
if ($_GET['commission_percentage'] == 0) { $_GET['sale_winner'] = 'affiliator'; }
if ($_GET['commission_percentage'] > 0) {
$row = $wpdb->get_row("SELECT SUM(price) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE product_id = ".$_GET['product_id']." AND status != 'refunded' AND referrer = '".$_GET['referrer']."'", OBJECT);
$total_price = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(price) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE product_id = ".$_GET['product_id']." AND status != 'refunded' AND referrer = '".$_GET['referrer']."'", OBJECT);
$total_price = $total_price + round(100*$row->total)/100;
if ($total_price == 0) {
if ($_GET['first_sale_winner'] == 'affiliate') { $_GET['sale_winner'] = 'affiliate'; }
else { $_GET['sale_winner'] = 'affiliator'; } }
if ($total_price > 0) {
$row = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE product_id = ".$_GET['product_id']." AND referrer = '".$_GET['referrer']."'", OBJECT);
$commissions_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE product_id = ".$_GET['product_id']." AND referrer = '".$_GET['referrer']."'", OBJECT);
$commissions_total_amount = $commissions_total_amount + round(100*$row->total)/100;
if ($_GET['first_sale_winner'] == 'affiliate') {
if ($_GET['commission_percentage'] >= 100*$commissions_total_amount/$total_price) { $_GET['sale_winner'] = 'affiliate'; }
else { $_GET['sale_winner'] = 'affiliator'; } }
if ($_GET['first_sale_winner'] == 'affiliator') {
if ($_GET['commission_percentage'] > 100*$commissions_total_amount/$total_price) { $_GET['sale_winner'] = 'affiliate'; }
else { $_GET['sale_winner'] = 'affiliator'; } } } } }
if ($_GET['sale_winner'] == 'affiliator') { $_GET['commission_amount'] = 0; } } } } break;

case '2':
if (($_GET['referrer'] != '') && (!strstr($_GET['referrer'], '@'))) {
if (($_GET['action'] == 'commerce_notification') && (isset($_GET['recurring_payments_profile_number']))) { $price = $_GET['price']; $_GET['quantity'] = 1; }
else { $price = product_data('price'); }
$_GET['affiliation_enabled'] = product_data('affiliation_enabled');
foreach (array('affiliate_data', 'referrer_data') as $key) { $_GET[$key] = (array) $_GET[$key]; }
foreach (array('affiliate_data', 'referrer', 'referrer_data') as $key) { if (isset($_GET[$key])) { $original[$key] = $_GET[$key]; } }
$result = $wpdb->get_row("SELECT referrer FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_GET['referrer']."' AND status = 'active'", OBJECT);
if ($result) { $_GET['referrer'] = $result->referrer; $_GET['referrer2'] = $_GET['referrer']; }
if (($_GET['affiliation_enabled'] == 'yes') && ($_GET['referrer2'] != '')) {
$_GET['affiliate_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_GET['referrer2']."'", OBJECT);
$_GET['referrer_data'] = $_GET['affiliate_data'];
if ($_GET['referrer_data']['status'] != 'active') { $_POST['commission2_amount'] = 0; }
else {
foreach (array(
'commission2_amount',
'commission2_enabled',
'commission2_percentage',
'commission2_type') as $field) { $_GET[$field] = product_data($field); }
if ($_GET['commission2_enabled'] == 'no') { $_GET['commission2_amount'] = 0; }
else {
if ($_GET['commission2_type'] == 'constant') { $_GET['commission2_amount'] = $_GET['quantity']*$_GET['commission2_amount']; }
elseif ($_GET['commission2_type'] == 'proportional') { $_GET['commission2_amount'] = round($_GET['quantity']*$_GET['commission2_percentage']*$price)/100; }
if ($_GET['commission2_amount'] > 0) { $_GET['commission2_status'] = 'unpaid'; } } } }
foreach (array('affiliate_data', 'referrer', 'referrer_data') as $key) { if (isset($original[$key])) { $_GET[$key] = $original[$key]; } } } break;

case '1message': case '1prospect':
$_POST['referrer'] = $_COOKIE[AFFILIATION_COOKIES_NAME];
if ($_POST['referrer'] == '') {
$result = $wpdb->get_row("SELECT referrer FROM ".$wpdb->prefix."affiliation_manager_clicks WHERE ip_address = '".$_SERVER['REMOTE_ADDR']."' ORDER BY date DESC LIMIT 1", OBJECT);
if ($result) { $_POST['referrer'] = $result->referrer; } }
$_GET['referrer'] = $_POST['referrer'];
$_GET['affiliation_enabled'] = ($type == 'message' ? contact_form_data('affiliation_enabled') : optin_form_data('affiliation_enabled'));
if (($_GET['affiliation_enabled'] == 'no') || (strstr($_GET['referrer'], '@'))) { $_POST['commission_amount'] = 0; }
else {
if (!strstr($_GET['referrer'], '@')) {
$_GET['affiliate_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_GET['referrer']."'", OBJECT);
$_GET['referrer_data'] = $_GET['affiliate_data'];
if ($_GET['referrer_data']['status'] != 'active') { $_POST['commission_amount'] = 0; }
else { $_POST['commission_amount'] = ($type == 'message' ? contact_form_data('commission_amount') : optin_form_data('commission_amount')); } } }
if ($_POST['commission_amount'] > 0) { $_POST['commission_status'] = 'unpaid'; } break;

case '2message': case '2prospect':
$_GET['affiliation_enabled'] = ($type == 'message' ? contact_form_data('affiliation_enabled') : optin_form_data('affiliation_enabled'));
if (($_GET['referrer'] != '') && (!strstr($_GET['referrer'], '@'))) {
foreach (array('affiliate_data', 'referrer_data') as $key) { $_GET[$key] = (array) $_GET[$key]; }
foreach (array('affiliate_data', 'referrer', 'referrer_data') as $key) { if (isset($_GET[$key])) { $original[$key] = $_GET[$key]; } }
$result = $wpdb->get_row("SELECT referrer FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_GET['referrer']."' AND status = 'active'", OBJECT);
if ($result) { $_GET['referrer'] = $result->referrer; $_GET['referrer2'] = $_GET['referrer']; } }
if ($_GET['affiliation_enabled'] == 'yes') {
$_GET['affiliate_data'] = array();
if ($_GET['referrer2'] != '') {
$_GET['affiliate_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_GET['referrer2']."'", OBJECT);
$_GET['referrer_data'] = $_GET['affiliate_data'];
if ($_GET['referrer_data']['status'] != 'active') { $_POST['commission2_amount'] = 0; }
else {
foreach (array('commission2_amount', 'commission2_enabled') as $field) { $_POST[$field] = ($type == 'message' ? contact_form_data($field) : optin_form_data($field)); }
if ($_POST['commission2_enabled'] == 'no') { $_POST['commission2_amount'] = 0; } } }
$_POST['referrer2'] = $_GET['referrer2'];
if ($_POST['commission2_amount'] > 0) { $_POST['commission2_status'] = 'unpaid'; }
foreach (array('affiliate_data', 'referrer', 'referrer_data') as $key) { if (isset($original[$key])) { $_GET[$key] = $original[$key]; } } } break; }