<?php global $wpdb;
$back_office_options = get_option('commerce_manager_back_office');
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }

if ((isset($_GET['id'])) && ($_GET['action'] == 'delete')) {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!commerce_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'commerce-manager'); }
else {
$recurring_payment_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE id = ".$_GET['id'], OBJECT);
$results = $wpdb->query("DELETE FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE id = ".$_GET['id']);
$order_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_orders WHERE id = ".$recurring_payment_data->order_id, OBJECT);
$received_payments_number = $order_data->received_payments_number - 1;
if ($received_payments_number < 0) { $received_payments_number = 0; }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_orders SET received_payments_number = ".$received_payments_number." WHERE id = ".$order_data->id);
if ((!defined('COMMERCE_MANAGER_DEMO')) || (COMMERCE_MANAGER_DEMO == false)) {
if (commerce_data('recurring_payment_removal_custom_instructions_executed') == 'yes') {
eval(format_instructions(commerce_data('recurring_payment_removal_custom_instructions'))); } } } } ?>
<div class="wrap">
<div id="poststuff">
<?php commerce_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) {
echo '<div class="updated"><p><strong>'.__('Payment deleted.', 'commerce-manager').'</strong></p></div>
<script type="text/javascript">setTimeout(\'window.location = "admin.php?page=commerce-manager-recurring-payments"\', 2000);</script>'; } ?>
<?php commerce_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php _e('Do you really want to permanently delete this payment?', 'commerce-manager'); ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'commerce-manager'); ?>" />
</div>
<div class="clear"></div>
</form><?php } ?>
</div>
</div><?php }

else {
include 'admin-pages.php';
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!commerce_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'commerce-manager'); }
else {
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = stripslashes(html_entity_decode(str_replace('&nbsp;', ' ', $value))); } }
$back_office_options = update_commerce_manager_back_office($back_office_options, 'recurring_payment');

if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
foreach (array('order', 'form', 'client', 'product') as $item) {
if (($item != 'order') && ($_POST[$item.'_id'] == '')) { $_POST[$item.'_id'] = order_data($item.'_id'); }
$_POST[$item.'_id'] = (int) $_POST[$item.'_id'];
if (($item == 'product') && ($_POST[$item.'_id'] < 1)) { $_POST[$item.'_id'] = 1; }
$_GET[$item.'_id'] = $_POST[$item.'_id'];
if ($_GET[$item.'_id'] > 0) { $_GET[$item.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_".$item."s WHERE id = ".$_GET[$item.'_id'], OBJECT); } }
$_GET['commerce_form_id'] = $_GET['form_id'];
$_GET['commerce_form_data'] = $_GET['form_data'];
$_POST['price'] = str_replace(array('?', ',', ';'), '.', $_POST['price']);
$_POST['price'] = round(100*$_POST['price'])/100;
if ($_POST['tax_included_in_price'] == '') { $_POST['tax_included_in_price'] = product_data('tax_included_in_price'); }
if ($_POST['tax'] == '') {
if (product_data('tax_applied') == 'no') { $_POST['tax'] = 0; }
else {
if ($_POST['tax_included_in_price'] == 'yes') {
$r = 1 + product_data('tax_percentage')/100;
$_POST['net_price'] = round(100*$_POST['price']/$r)/100;
$_POST['tax'] = $_POST['price'] - $_POST['net_price']; }
else {
$_POST['net_price'] = $_POST['price'];
$_POST['tax'] = round(product_data('tax_percentage')*$_POST['price'])/100; } } }
else { $_POST['tax'] = str_replace(array('?', ',', ';'), '.', $_POST['tax']);
$_POST['tax'] = round(100*$_POST['tax'])/100;
if ($_POST['tax'] < 0) { $_POST['tax'] = 0; } }
if ($_POST['shipping_cost'] == '') { $_POST['shipping_cost'] = product_data('shipping_cost'); }
else { $_POST['shipping_cost'] = str_replace(array('?', ',', ';'), '.', $_POST['shipping_cost']);
$_POST['shipping_cost'] = round(100*$_POST['shipping_cost'])/100;
if ($_POST['shipping_cost'] < 0) { $_POST['shipping_cost'] = 0; } }
$_POST['amount'] = str_replace(array('?', ',', ';'), '.', $_POST['amount']);
$_POST['amount'] = round(100*$_POST['amount'])/100;
if ($_POST['amount'] <= 0) {
$_POST['amount'] = $_POST['price'] + $_POST['shipping_cost'];
if ($_POST['tax_included_in_price'] == 'no') { $_POST['amount'] = $_POST['amount'] + $_POST['tax']; } }
foreach (array('payment_mode', 'receiver_account') as $field) { if ($_POST[$field] == '') { $_POST[$field] = order_data($field); } }
$_POST['transaction_cost'] = str_replace(array('?', ',', ';'), '.', $_POST['transaction_cost']);
$_POST['transaction_cost'] = round(100*$_POST['transaction_cost'])/100;
if ($_POST['transaction_cost'] < 0) { $_POST['transaction_cost'] = 0; }
if ($_POST['date'] == '') {
$_POST['date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['date_utc'] = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['date'], 0, PREG_SPLIT_NO_EMPTY);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['date'] = date('Y-m-d H:i:s', $time);
$_POST['date_utc'] = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); }
if ($_POST['status'] == 'refunded') {
if ($_POST['refund_date'] == '') {
$_POST['refund_date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['refund_date_utc'] = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['refund_date'], 0, PREG_SPLIT_NO_EMPTY);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['refund_date'] = date('Y-m-d H:i:s', $time);
$_POST['refund_date_utc'] = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); } }
else { $_POST['refund_date'] = ''; }
if ($_POST['recurring_payments_profile_number'] == '') { $_POST['recurring_payments_profile_number'] = order_data('recurring_payments_profile_number'); }
if ($_POST['referrer'] == '') { $_POST['referrer'] = order_data('referrer'); }
else {
if (is_numeric($_POST['referrer'])) {
$_POST['referrer'] = preg_replace('/[^0-9]/', '', $_POST['referrer']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE id = ".$_POST['referrer'], OBJECT);
if ($result) { $_POST['referrer'] = $result->login; } }
elseif (strstr($_POST['referrer'], '@')) {
$_POST['referrer'] = format_email_address($_POST['referrer']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE paypal_email_address = '".$_POST['referrer']."'", OBJECT);
if ($result) { $_POST['referrer'] = $result->login; }
else { $result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".$_POST['referrer']."'", OBJECT);
if ($result) { $_POST['referrer'] = $result->login; } } }
else {
$_POST['referrer'] = format_nice_name($_POST['referrer']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['referrer']."'", OBJECT);
if (!$result) { $_POST['referrer'] = ''; } } }
if (($_POST['referrer'] != '') && (!strstr($_POST['referrer'], '@'))) {
$_GET['affiliate_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['referrer']."'", OBJECT);
$_GET['referrer_data'] = $_GET['affiliate_data']; }
if ($_POST['referrer'] == '') {
$_POST['commission_amount'] = 0;
$_POST['commission_payment'] = '';
$_POST['commission_status'] = '';
$_POST['commission_payment_date'] = ''; }
else {
$_POST['commission_amount'] = str_replace(array('?', ',', ';'), '.', $_POST['commission_amount']);
$_POST['commission_amount'] = round(100*$_POST['commission_amount'])/100; if ($_POST['commission_amount'] <= 0) { $_POST['commission_amount'] = 0; }
if ($_POST['commission_amount'] == 0) { $_POST['commission_payment'] = ''; }
elseif ($_POST['commission_payment'] == '') { $_POST['commission_payment'] = 'deferred'; }
if ($_POST['commission_payment'] == '') { $_POST['commission_status'] = ''; }
elseif ($_POST['commission_payment'] == 'instant') {
$_POST['commission_status'] = 'paid';
$_POST['commission_payment_date'] = $_POST['date'];
$_POST['commission_payment_date_utc'] = $_POST['date_utc']; }
elseif ($_POST['commission_status'] == '') { $_POST['commission_status'] = 'unpaid'; }
if ($_POST['commission_status'] == 'paid') {
if ($_POST['commission_payment_date'] == '') {
$_POST['commission_payment_date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['commission_payment_date_utc'] = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['commission_payment_date'], 0, PREG_SPLIT_NO_EMPTY);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['commission_payment_date'] = date('Y-m-d H:i:s', $time);
$_POST['commission_payment_date_utc'] = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); } }
else { $_POST['commission_payment_date'] = ''; }
if ((($_POST['status'] == 'refunded') && ($_POST['commission_status'] != 'paid')) || ((strstr($_POST['referrer'], '@')) && ($_POST['commission_payment'] == 'deferred'))) {
$_POST['commission_amount'] = 0;
$_POST['commission_payment'] = '';
$_POST['commission_status'] = '';
$_POST['commission_payment_date'] = ''; } }

if ($_POST['referrer2'] == '') { $_POST['referrer2'] = order_data('referrer2'); }
else {
if (is_numeric($_POST['referrer2'])) {
$_POST['referrer2'] = preg_replace('/[^0-9]/', '', $_POST['referrer2']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE id = ".$_POST['referrer2'], OBJECT);
if ($result) { $_POST['referrer2'] = $result->login; } }
elseif (strstr($_POST['referrer2'], '@')) {
$_POST['referrer2'] = format_email_address($_POST['referrer2']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE paypal_email_address = '".$_POST['referrer2']."'", OBJECT);
if ($result) { $_POST['referrer2'] = $result->login; }
else { $result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".$_POST['referrer2']."'", OBJECT);
if ($result) { $_POST['referrer2'] = $result->login; } } }
else {
$_POST['referrer2'] = format_nice_name($_POST['referrer2']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['referrer2']."'", OBJECT);
if (!$result) { $_POST['referrer2'] = ''; } } }
if ($_POST['referrer2'] == '') {
$_POST['commission2_amount'] = 0;
$_POST['commission2_status'] = '';
$_POST['commission2_payment_date'] = ''; }
else {
$_POST['commission2_amount'] = str_replace(array('?', ',', ';'), '.', $_POST['commission2_amount']);
$_POST['commission2_amount'] = round(100*$_POST['commission2_amount'])/100; if ($_POST['commission2_amount'] <= 0) { $_POST['commission2_amount'] = 0; }
if ($_POST['commission2_amount'] == 0) {
$_POST['commission2_status'] = '';
$_POST['commission2_payment_date'] = ''; }
elseif ($_POST['commission2_status'] == '') { $_POST['commission2_status'] = 'unpaid'; }
if ($_POST['commission2_status'] == 'paid') {
if ($_POST['commission2_payment_date'] == '') {
$_POST['commission2_payment_date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['commission2_payment_date_utc'] = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['commission2_payment_date'], 0, PREG_SPLIT_NO_EMPTY);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['commission2_payment_date'] = date('Y-m-d H:i:s', $time);
$_POST['commission2_payment_date_utc'] = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); } }
else { $_POST['commission2_payment_date'] = ''; }
if (($_POST['status'] == 'refunded') && ($_POST['commission2_status'] != 'paid')) {
$_POST['commission2_amount'] = 0;
$_POST['commission2_status'] = '';
$_POST['commission2_payment_date'] = ''; } }

if ($_POST['commission_payment'] == 'instant') {
$_POST['payment_mode'] = 'PayPal';
if (strstr($_POST['referrer'], '@')) { $_POST['receiver_account'] = $_POST['referrer']; } }

if (!isset($_GET['id'])) {
if (isset($_POST['update_fields'])) {
foreach ($_POST as $key => $value) { $_GET['recurring_payment_data'][$key] = $value; }
$_GET['recurring_payment_data']['id'] = '{recurring-payment id}';
foreach ($add_recurring_payment_fields as $field) { $_POST[$field] = str_replace('{recurring-payment id}', '[recurring-payment id]', product_data($field)); } }
else {
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE date = '".$_POST['date']."' AND order_id = ".$_POST['order_id'], OBJECT);
if (!$result) {
$updated = true;
include 'tables.php';
$sql = commerce_sql_array($tables['recurring_payments'], $_POST);
foreach ($tables['recurring_payments'] as $key => $value) { if ($key != 'id') { $keys_list .= $key.","; $values_list .= $sql[$key].","; } }
$results = $wpdb->query("INSERT INTO ".$wpdb->prefix."commerce_manager_recurring_payments (".substr($keys_list, 0, -1).") VALUES(".substr($values_list, 0, -1).")");
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE date = '".$_POST['date']."' AND order_id = ".$_POST['order_id'], OBJECT);
$_POST['id'] = $result->id;
$_GET['recurring_payment_data'] = $_POST;
$received_payments_number = order_data('received_payments_number') + 1;
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_orders SET received_payments_number = ".$received_payments_number." WHERE id = ".$_POST['order_id']);
foreach ($add_recurring_payment_fields as $field) { $_POST[$field] = do_shortcode($_POST[$field]); }
if ((!defined('COMMERCE_MANAGER_DEMO')) || (COMMERCE_MANAGER_DEMO == false)) {
foreach (array('confirmation', 'notification') as $action) {
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = stripslashes($_POST['recurring_payment_'.$action.'_email_'.$field]); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); } }
if ((function_exists('referrer_data')) && ($_POST['referrer'] != '') && (!strstr($_POST['referrer'], '@'))) {
if (affiliation_data('recurring_payment_notification_email_deactivated') != 'yes') {
$_GET['referrer'] = $_POST['referrer'];
if (referrer_data('status') == 'active') {
$sent = referrer_data('recurring_payment_notification_email_sent');
if (($sent == 'yes') || (($sent == 'if commission') && ($_POST['commission_amount'] > 0))) {
foreach (array('sender', 'receiver', 'subject', 'body') as $field) {
$$field = affiliation_data('recurring_payment_notification_email_'.$field); }
wp_mail($receiver, $subject, $body, 'From: '.$sender); } } } }
if ($_POST['recurring_payment_custom_instructions_executed'] == 'yes') {
eval(format_instructions($_POST['recurring_payment_custom_instructions'])); } } } } }

if (isset($_GET['id'])) {
$updated = true;
include 'tables.php';
$recurring_payment_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE id = ".$_GET['id'], OBJECT);
$sql = commerce_sql_array($tables['recurring_payments'], $_POST);
foreach ($tables['recurring_payments'] as $key => $value) { switch ($key) {
case 'id': break;
default: $list .= $key." = ".$sql[$key].","; } }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_recurring_payments SET ".substr($list, 0, -1)." WHERE id = ".$_GET['id']);
if ((!defined('COMMERCE_MANAGER_DEMO')) || (COMMERCE_MANAGER_DEMO == false)) {
if ($_POST['status'] == 'refunded') {
if ($_POST['old_status'] != 'refunded') {
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = stripslashes($_POST['recurring_payment_refund_notification_email_'.$field]); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); }
if (product_data('recurring_payment_refund_custom_instructions_executed') == 'yes') {
eval(format_instructions(product_data('recurring_payment_refund_custom_instructions'))); } } } }

if ($_POST['order_id'] != $recurring_payment_data->order_id) {
$order_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_orders WHERE id = ".$recurring_payment_data->order_id, OBJECT);
$received_payments_number = $order_data->received_payments_number - 1;
if ($received_payments_number < 0) { $received_payments_number = 0; }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_orders SET received_payments_number = ".$received_payments_number." WHERE id = ".$recurring_payment_data->order_id);
$received_payments_number = $_GET['order_data']['received_payments_number'] + 1;
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_orders SET received_payments_number = ".$received_payments_number." WHERE id = ".$_POST['order_id']); } } } }

if (isset($_GET['id'])) {
$recurring_payment_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE id = ".$_GET['id'], OBJECT);
if ($recurring_payment_data) {
$_GET['recurring_payment_data'] = (array) $recurring_payment_data;
$_GET['recurring_payment_id'] = $recurring_payment_data->id;
$_GET['client_id'] = $recurring_payment_data->client_id;
if ($_GET['client_id'] > 0) { $_GET['client_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_clients WHERE id = ".$_GET['client_id'], OBJECT); }
$_GET['order_id'] = $recurring_payment_data->order_id;
$_GET['commerce_form_id'] = $recurring_payment_data->form_id;
$_GET['product_id'] = $recurring_payment_data->product_id;
foreach ($recurring_payment_data as $key => $value) { $_POST[$key] = $value; } }
elseif (!headers_sent()) { header('Location: admin.php?page=commerce-manager-recurring-payments'); exit(); }
else { echo '<script type="text/javascript">window.location = "admin.php?page=commerce-manager-recurring-payments";</script>'; } }

foreach ($_POST as $key => $value) {
if (is_string($value)) {
$_POST[$key] = str_replace(array('&amp;amp;', '&amp;apos;', '&amp;quot;'), array('&amp;', '&apos;', '&quot;'), htmlspecialchars(stripslashes($value)));
if ($value == '0000-00-00 00:00:00') { $_POST[$key] = ''; } } }
$undisplayed_modules = (array) $back_office_options['recurring_payment_page_undisplayed_modules'];
$currency_code = commerce_data('currency_code'); ?>

<div class="wrap">
<div id="poststuff">
<?php commerce_manager_pages_top($back_office_options); ?>
<?php if ($updated) {
echo '<div class="updated"><p><strong>'.(isset($_GET['id']) ? __('Payment updated.', 'commerce-manager') : __('Payment saved.', 'commerce-manager')).'</strong></p></div>
'.(isset($_GET['id']) ? '' : '<script type="text/javascript">setTimeout(\'window.location = "admin.php?page=commerce-manager-recurring-payments"\', 2000);</script>'); } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php commerce_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<?php commerce_manager_pages_summary($back_office_options); ?>

<div class="postbox" id="general-informations-module"<?php if (in_array('general-informations', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="general-informations"><strong><?php echo $modules['recurring_payment']['general-informations']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="id">'.__('ID', 'commerce-manager').'</label></strong></th>
<td><input type="text" name="id" id="id" size="10" value="'.$_GET['id'].'" disabled="disabled" /> <span class="description">'.__('The ID can not be changed.', 'commerce-manager').'</span></td></tr>'; } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_id"><?php _e('Order ID', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="order_id" id="order_id" rows="1" cols="25"><?php echo $_POST['order_id']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 1.', 'commerce-manager'); ?></span>
<?php if ($_POST['order_id'] > 0) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-order&amp;id='.$_POST['order_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-order&amp;id='.$_POST['order_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php $row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_clients", OBJECT);
$clients_number = (int) $row->total; if ($clients_number > 0) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="client_id"><?php _e('Client ID', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="client_id" id="client_id" rows="1" cols="25"><?php echo $_POST['client_id']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for the client ID of the order.', 'commerce-manager'); ?></span>
<?php if ($_POST['client_id'] > 0) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-client&amp;id='.$_POST['client_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-client&amp;id='.$_POST['client_id'].'&amp;action=delete">'.__('Delete').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-statistics&amp;client_id='.$_POST['client_id'].'">'.__('Statistics', 'commerce-manager').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="product_id"><?php _e('Product ID', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="product_id" id="product_id" rows="1" cols="25"><?php echo $_POST['product_id']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for the product ID of the order.', 'commerce-manager'); ?></span>
<?php if ($_POST['product_id'] > 0) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-product&amp;id='.$_POST['product_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-product&amp;id='.$_POST['product_id'].'&amp;action=delete">'.__('Delete').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-statistics&amp;product_id='.$_POST['product_id'].'">'.__('Statistics', 'commerce-manager').'</a>'; } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="price"><?php _e('Global price', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="price" id="price" rows="1" cols="25"><?php echo $_POST['price']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="tax"><?php _e('Tax', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="tax" id="tax" rows="1" cols="25"><?php echo $_POST['tax']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank to automatically calculate the tax.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="tax_included_in_price"><?php _e('Tax included in price', 'commerce-manager'); ?></label></strong></th>
<td><select name="tax_included_in_price" id="tax_included_in_price">
<option value=""<?php if ($_POST['tax_included_in_price'] == '') { echo ' selected="selected"'; } ?>><?php _e('Current product option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['tax_included_in_price'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['tax_included_in_price'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="shipping_cost"><?php _e('Shipping cost', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="shipping_cost" id="shipping_cost" rows="1" cols="25"><?php echo $_POST['shipping_cost']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for the current product shipping cost.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="amount"><?php _e('Amount', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="amount" id="amount" rows="1" cols="25"><?php echo $_POST['amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank to automatically calculate the amount.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payment_mode"><?php _e('Payment mode', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="payment_mode" id="payment_mode" rows="1" cols="50"><?php echo $_POST['payment_mode']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank for the payment mode of the order.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="receiver_account"><?php _e('Receiver account', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="receiver_account" id="receiver_account" rows="1" cols="50"><?php echo $_POST['receiver_account']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank for the receiver account of the order.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="transaction_number"><?php _e('Transaction number', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="transaction_number" id="transaction_number" rows="1" cols="50"><?php echo $_POST['transaction_number']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="transaction_cost"><?php _e('Transaction cost', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="transaction_cost" id="transaction_cost" rows="1" cols="25"><?php echo $_POST['transaction_cost']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 0.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="date"><?php _e('Date', 'commerce-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="date" id="date" size="20" value="<?php echo (isset($_POST['date']) ? $_POST['date'] : date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET)); ?>" /></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="status"><?php _e('Status', 'commerce-manager'); ?></label></strong></th>
<td><select name="status" id="status" onchange="<?php if ((isset($_GET['id'])) && ($_POST['status'] != 'refunded')) { echo 'display_status_notification_email_module(); '; } ?>if (this.value == 'refunded') { document.getElementById('refund-date').style.display = ''; } else { document.getElementById('refund-date').style.display = 'none'; }">
<option value="received"<?php if ($_POST['status'] == 'received') { echo ' selected="selected"'; } ?>><?php _e('Received', 'commerce-manager'); ?></option>
<option value="refunded"<?php if ($_POST['status'] == 'refunded') { echo ' selected="selected"'; } ?>><?php _e('Refunded ', 'commerce-manager'); ?></option>
</select><?php if (isset($_GET['id'])) { echo '<input type="hidden" name="old_status" value="'.$_POST['status'].'" />'; } ?></td></tr>
<tr id="refund-date" style="<?php if ($_POST['status'] != 'refunded') { echo 'display: none; '; } ?>vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="refund_date"><?php _e('Refund date', 'commerce-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="refund_date" id="refund_date" size="20" value="<?php echo $_POST['refund_date']; ?>" /><br />
<span class="description"><?php _e('Leave this field blank if the payment is not refunded, or for the current date if the payment is refunded.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payments_profile_number"><?php _e('Recurring payments profile\'s number', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="recurring_payments_profile_number" id="recurring_payments_profile_number" rows="1" cols="50"><?php echo $_POST['recurring_payments_profile_number']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank for the recurring payments profile\'s number of the order.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<?php if (isset($_GET['id'])) {
if ($_POST['status'] != 'refunded') {
foreach (array(
'recurring_payment_refund_notification_email_sender',
'recurring_payment_refund_notification_email_receiver',
'recurring_payment_refund_notification_email_subject',
'recurring_payment_refund_notification_email_body') as $field) { $_POST[$field] = htmlspecialchars(product_data($field)); } ?>

<div class="postbox" id="recurring-payment-refund-notification-email-module" style="display: none;">
<h3 id="recurring-payment-refund-notification-email"><strong><?php _e('Refund notification email', 'commerce-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager-product&amp;id=<?php echo $_POST['product_id']; ?>#recurring-payment-refund-notification-email"><?php _e('Click here to configure the default options of the product.', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="recurring_payment_refund_notification_email_sent" id="recurring_payment_refund_notification_email_sent" value="yes" /> <?php _e('Send a recurring payment refund\'s notification email', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_refund_notification_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_refund_notification_email_sender" id="recurring_payment_refund_notification_email_sender" rows="1" cols="75"><?php echo $_POST['recurring_payment_refund_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_refund_notification_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_refund_notification_email_receiver" id="recurring_payment_refund_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['recurring_payment_refund_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_refund_notification_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_refund_notification_email_subject" id="recurring_payment_refund_notification_email_subject" rows="1" cols="75"><?php echo $_POST['recurring_payment_refund_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_refund_notification_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="recurring_payment_refund_notification_email_body" id="recurring_payment_refund_notification_email_body" rows="15" cols="75"><?php echo $_POST['recurring_payment_refund_notification_email_body']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<script type="text/javascript">
function display_status_notification_email_module() {
if (document.forms[0].status.value == 'refunded') { document.getElementById('recurring-payment-refund-notification-email-module').style.display = 'block'; }
else { document.getElementById('recurring-payment-refund-notification-email-module').style.display = 'none'; } }
</script>

<?php } } ?>

<div class="postbox" id="affiliation-module"<?php if (in_array('affiliation', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="affiliation"><strong><?php echo $modules['recurring_payment']['affiliation']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php echo (function_exists('affiliation_manager_admin_menu') ? '<a href="admin.php?page=affiliation-manager">'.__('Click here to configure the default options.', 'commerce-manager').'</a>' : __('To use affiliation, you must have installed and activated <a href="http://www.kleor-editions.com/affiliation-manager">Affiliation Manager</a>.', 'commerce-manager')); ?></span></td></tr>
</tbody></table>
<div id="level-1-commission-module"<?php if (in_array('level-1-commission', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="level-1-commission"><strong><?php echo $modules['recurring_payment']['affiliation']['modules']['level-1-commission']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('The level 1 commission is awarded to the affiliate who referred the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#commissions-levels"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="referrer"><?php _e('Referrer', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="referrer" id="referrer" rows="1" cols="25"><?php echo $_POST['referrer']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Affiliate who referred the order (ID, login name or email address)', 'commerce-manager'); ?><br />
<?php _e('Leave this field blank for the referrer of the order.', 'commerce-manager'); ?></span> 
<?php if ((function_exists('affiliation_manager_admin_menu')) && ($_POST['referrer'] != '') && (!strstr($_POST['referrer'], '@'))) {
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['referrer']."'", OBJECT);
if ($result) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate&amp;id='.$result->id.'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate&amp;id='.$result->id.'&amp;action=delete">'.__('Delete').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-statistics&amp;referrer='.$_POST['referrer'].'">'.__('Statistics', 'commerce-manager').'</a>'; } } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_amount"><?php _e('Amount', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_amount" id="commission_amount" rows="1" cols="25"><?php echo $_POST['commission_amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 0.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_payment"><?php _e('Payment', 'commerce-manager'); ?></label></strong></th>
<td><select name="commission_payment" id="commission_payment">
<option value=""<?php if ($_POST['commission_payment'] == '') { echo ' selected="selected"'; } ?>><?php _e('None', 'commerce-manager'); ?></option>
<option value="deferred"<?php if ($_POST['commission_payment'] == 'deferred') { echo ' selected="selected"'; } ?>><?php _e('Deferred', 'commerce-manager'); ?></option>
<option value="instant"<?php if ($_POST['commission_payment'] == 'instant') { echo ' selected="selected"'; } ?>><?php _e('Instant', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_status"><?php _e('Status', 'commerce-manager'); ?></label></strong></th>
<td><select name="commission_status" id="commission_status" onchange="if (this.value == 'paid') { document.getElementById('commission-payment-date').style.display = ''; } else { document.getElementById('commission-payment-date').style.display = 'none'; }">
<option value=""<?php if ($_POST['commission_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('None', 'commerce-manager'); ?></option>
<option value="unpaid"<?php if ($_POST['commission_status'] == 'unpaid') { echo ' selected="selected"'; } ?>><?php _e('Unpaid', 'commerce-manager'); ?></option>
<option value="paid"<?php if ($_POST['commission_status'] == 'paid') { echo ' selected="selected"'; } ?>><?php _e('Paid', 'commerce-manager'); ?></option>
</select><?php if (isset($_GET['id'])) { echo '<input type="hidden" name="old_commission_status" value="'.$_POST['commission_status'].'" />'; } ?></td></tr>
<tr id="commission-payment-date" style="<?php if ($_POST['commission_status'] != 'paid') { echo 'display: none; '; } ?>vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_payment_date"><?php _e('Payment date', 'commerce-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="commission_payment_date" id="commission_payment_date" size="20" value="<?php echo $_POST['commission_payment_date']; ?>" /><br />
<span class="description"><?php _e('Leave this field blank if the commission is not paid, or for the current date if the commission is paid.', 'commerce-manager'); ?></span></td></tr>
</tbody></table>
</div>
<div id="level-2-commission-module"<?php if (in_array('level-2-commission', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="level-2-commission"><strong><?php echo $modules['recurring_payment']['affiliation']['modules']['level-2-commission']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('The level 2 commission is awarded to the referrer of the affiliate who referred the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#commissions-levels"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="referrer2"><?php _e('Referrer', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="referrer2" id="referrer2" rows="1" cols="25"><?php echo $_POST['referrer2']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for the level 2 referrer of the order.', 'commerce-manager'); ?></span> 
<?php if ((function_exists('affiliation_manager_admin_menu')) && ($_POST['referrer2'] != '') && (!strstr($_POST['referrer2'], '@'))) {
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['referrer2']."'", OBJECT);
if ($result) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate&amp;id='.$result->id.'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate&amp;id='.$result->id.'&amp;action=delete">'.__('Delete').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-statistics&amp;referrer='.$_POST['referrer2'].'">'.__('Statistics', 'commerce-manager').'</a>'; } } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_amount"><?php _e('Amount', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission2_amount" id="commission2_amount" rows="1" cols="25"><?php echo $_POST['commission2_amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 0.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_status"><?php _e('Status', 'commerce-manager'); ?></label></strong></th>
<td><select name="commission2_status" id="commission2_status" onchange="if (this.value == 'paid') { document.getElementById('commission2-payment-date').style.display = ''; } else { document.getElementById('commission2-payment-date').style.display = 'none'; }">
<option value=""<?php if ($_POST['commission2_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('None', 'commerce-manager'); ?></option>
<option value="unpaid"<?php if ($_POST['commission2_status'] == 'unpaid') { echo ' selected="selected"'; } ?>><?php _e('Unpaid', 'commerce-manager'); ?></option>
<option value="paid"<?php if ($_POST['commission2_status'] == 'paid') { echo ' selected="selected"'; } ?>><?php _e('Paid', 'commerce-manager'); ?></option>
</select><?php if (isset($_GET['id'])) { echo '<input type="hidden" name="old_commission2_status" value="'.$_POST['commission2_status'].'" />'; } ?></td></tr>
<tr id="commission2-payment-date" style="<?php if ($_POST['commission2_status'] != 'paid') { echo 'display: none; '; } ?>vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_payment_date"><?php _e('Payment date', 'commerce-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="commission2_payment_date" id="commission2_payment_date" size="20" value="<?php echo $_POST['commission2_payment_date']; ?>" /><br />
<span class="description"><?php _e('Leave this field blank if the commission is not paid, or for the current date if the commission is paid.', 'commerce-manager'); ?></span></td></tr>
</tbody></table>
</div>
<?php if (isset($_GET['id'])) { echo '<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr></tbody></table>'; } ?>
</div></div>

<?php if (!isset($_GET['id'])) {
if (!isset($_POST['submit'])) {
$commerce_manager_options = (array) get_option('commerce_manager');
foreach ($commerce_manager_options as $key => $value) {
if (is_string($value)) { $commerce_manager_options[$key] = htmlspecialchars($value); } }
foreach ($add_recurring_payment_fields as $field) { $_POST[$field] = $commerce_manager_options[$field]; } }
$value = false; foreach ($add_recurring_payment_modules as $module) { if (!$value) { $value = (!in_array($module, $undisplayed_modules)); } }
if ($value) { ?><p class="submit" style="margin: 0 20%;"><input type="hidden" name="submit" value="true" />
<input type="submit" class="button-secondary" name="update_fields" value="<?php _e('Complete the fields below with the informations about the customer, the product, the order and the payment', 'commerce-manager'); ?>" /></p><?php } ?>

<div id="add-recurring-payment-modules">
<?php if (!in_array('recurring-payment-confirmation-email', $undisplayed_modules)) {
if (!isset($_POST['submit'])) { $_POST['recurring_payment_confirmation_email_body'] = htmlspecialchars(get_option('commerce_manager_recurring_payment_confirmation_email_body')); } ?>
<div class="postbox" id="recurring-payment-confirmation-email-module">
<h3 id="recurring-payment-confirmation-email"><strong><?php echo $modules['recurring_payment']['recurring-payment-confirmation-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager#recurring-payment-confirmation-email"><?php _e('Click here to configure the default options.', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="recurring_payment_confirmation_email_sent" id="recurring_payment_confirmation_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['recurring_payment_confirmation_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a recurring payment confirmation email', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_confirmation_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_confirmation_email_sender" id="recurring_payment_confirmation_email_sender" rows="1" cols="75"><?php echo $_POST['recurring_payment_confirmation_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_confirmation_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_confirmation_email_receiver" id="recurring_payment_confirmation_email_receiver" rows="1" cols="75"><?php echo $_POST['recurring_payment_confirmation_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_confirmation_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_confirmation_email_subject" id="recurring_payment_confirmation_email_subject" rows="1" cols="75"><?php echo $_POST['recurring_payment_confirmation_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_confirmation_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="recurring_payment_confirmation_email_body" id="recurring_payment_confirmation_email_body" rows="15" cols="75"><?php echo $_POST['recurring_payment_confirmation_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product, the order and the payment.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('recurring-payment-notification-email', $undisplayed_modules)) {
if (!isset($_POST['submit'])) { $_POST['recurring_payment_notification_email_body'] = htmlspecialchars(get_option('commerce_manager_recurring_payment_notification_email_body')); } ?>
<div class="postbox" id="recurring-payment-notification-email-module">
<h3 id="recurring-payment-notification-email"><strong><?php echo $modules['recurring_payment']['recurring-payment-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager#recurring-payment-notification-email"><?php _e('Click here to configure the default options.', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="recurring_payment_notification_email_sent" id="recurring_payment_notification_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['recurring_payment_notification_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a recurring payment notification email', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_notification_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_notification_email_sender" id="recurring_payment_notification_email_sender" rows="1" cols="75"><?php echo $_POST['recurring_payment_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_notification_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_notification_email_receiver" id="recurring_payment_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['recurring_payment_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_notification_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_notification_email_subject" id="recurring_payment_notification_email_subject" rows="1" cols="75"><?php echo $_POST['recurring_payment_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_notification_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="recurring_payment_notification_email_body" id="recurring_payment_notification_email_body" rows="15" cols="75"><?php echo $_POST['recurring_payment_notification_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product, the order and the payment.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('custom-instructions', $undisplayed_modules)) {
if (!isset($_POST['submit'])) { $_POST['recurring_payment_custom_instructions'] = htmlspecialchars(get_option('commerce_manager_recurring_payment_custom_instructions')); } ?>
<div class="postbox" id="custom-instructions-module">
<h3 id="custom-instructions"><strong><?php echo $modules['recurring_payment']['custom-instructions']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager#recurring-payment-custom-instructions"><?php _e('Click here to configure the default options.', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="recurring_payment_custom_instructions_executed" id="recurring_payment_custom_instructions_executed" value="yes"<?php if ($_POST['recurring_payment_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_custom_instructions"><?php _e('PHP code', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="recurring_payment_custom_instructions" id="recurring_payment_custom_instructions" rows="10" cols="75"><?php echo $_POST['recurring_payment_custom_instructions']; ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the registration of the recurring payment.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#custom-instructions"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<?php } ?>
</div>

<?php } ?>
<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php (isset($_GET['id']) ?  _e('Save Changes', 'commerce-manager') : _e('Save Payment', 'commerce-manager')); ?>" /></p>
<?php commerce_manager_pages_module($back_office_options, 'recurring-payment-page', $undisplayed_modules); ?>
</form>
</div>
</div>
<?php if (isset($_POST['update_fields'])) { ?>
<script type="text/javascript">window.location = '#add-recurring-payment-modules';</script>
<?php } }