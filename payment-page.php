<?php global $wpdb;
$back_office_options = get_option('affiliation_manager_back_office');
$options = get_option('affiliation_manager_payment');
if (function_exists('commerce_data')) { $currency_code = commerce_data('currency_code'); }
else { $commerce_manager_options = (array) get_option('commerce_manager');
$currency_code = do_shortcode($commerce_manager_options['currency_code']); }
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }

$filterby_options = array(
'referrer' => __('referrer', 'affiliation-manager'),
'product_id' => __('product ID', 'affiliation-manager'));

$tables = array('commerce_manager_orders', 'commerce_manager_recurring_payments', 'optin_manager_prospects', 'contact_manager_messages');

if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = stripslashes($value); } }
$_GET['s'] = $_POST['s'];
$filterby = $_POST['filterby'];
$start_date = trim(mysql_real_escape_string(strip_tags($_POST['start_date'])));
if (strlen($start_date) == 10) { $start_date .= ' 00:00:00'; }
$end_date = trim(mysql_real_escape_string(strip_tags($_POST['end_date'])));
if (strlen($end_date) == 10) { $end_date .= ' 23:59:59'; }
$date_criteria = "(date BETWEEN '$start_date' AND '$end_date')";
if (isset($_POST['mark_as_paid'])) {
if (!affiliation_manager_user_can($back_office_options, 'manage')) { unset($_POST['mark_as_paid']); $error = __('You don\'t have sufficient permissions.', 'affiliation-manager'); }
else {
$commission_payment_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$commission_payment_date_utc = date('Y-m-d H:i:s');
if ($filterby == 'referrer') {
if ($_GET['s'] != '') {
$filter_criteria = "AND (referrer = '".$_GET['s']."')";
$filter_criteria2 = "AND (referrer2 = '".$_GET['s']."')"; }
foreach ($tables as $table) {
$results = $wpdb->query("UPDATE ".$wpdb->prefix.$table." SET
	commission_status = 'paid',
	commission_payment_date = '".$commission_payment_date."',
	commission_payment_date_utc = '".$commission_payment_date_utc."' WHERE commission_status = 'unpaid' AND $date_criteria $filter_criteria");
$results = $wpdb->query("UPDATE ".$wpdb->prefix.$table." SET
	commission2_status = 'paid',
	commission2_payment_date = '".$commission_payment_date."',
	commission2_payment_date_utc = '".$commission_payment_date_utc."' WHERE commission2_status = 'unpaid' AND $date_criteria $filter_criteria2"); } }
else {
if ($_GET['s'] != '') { $filter_criteria = (is_numeric($_GET['s']) ? "AND (".$filterby." = ".$_GET['s'].")" : "AND (".$filterby." = '".$_GET['s']."')"); }
foreach ($tables as $table) {
$results = $wpdb->query("UPDATE ".$wpdb->prefix.$table." SET
	commission_status = 'paid',
	commission_payment_date = '".$commission_payment_date."',
	commission_payment_date_utc = '".$commission_payment_date_utc."' WHERE commission_status = 'unpaid' AND $date_criteria $filter_criteria");
$results = $wpdb->query("UPDATE ".$wpdb->prefix.$table." SET
	commission2_status = 'paid',
	commission2_payment_date = '".$commission_payment_date."',
	commission2_payment_date_utc = '".$commission_payment_date_utc."' WHERE commission2_status = 'unpaid' AND $date_criteria $filter_criteria"); } } } } }
else {
$end_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$filterby = $options['filterby'];
$start_date = $options['start_date']; }

$date_criteria = "(date BETWEEN '$start_date' AND '$end_date')";

if (($options) && (affiliation_manager_user_can($back_office_options, 'manage'))) {
$options = array(
'filterby' => $filterby,
'start_date' => $start_date);
update_option('affiliation_manager_payment', $options); } ?>

<div class="wrap">
<div id="poststuff">
<?php affiliation_manager_pages_top($back_office_options); ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php affiliation_manager_pages_menu($back_office_options); ?>
<?php affiliation_manager_pages_search_field('filter', $filterby, $filterby_options); ?>
<?php affiliation_manager_pages_date_picker($start_date, $end_date); ?>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<?php if (($_GET['s'] != '') && ($filterby == 'referrer')) { $affiliates = $wpdb->get_results("SELECT login, paypal_email_address FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_GET['s']."'", OBJECT); }
else { $affiliates = $wpdb->get_results("SELECT login, paypal_email_address FROM ".$wpdb->prefix."affiliation_manager_affiliates", OBJECT); }
$all_unpaid_commissions_total_amount = 0;
foreach ($affiliates as $affiliate) {
if (($_GET['s'] != '') && ($filterby == 'product_id')) {
$row1 = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE referrer = '".$affiliate->login."' AND commission_status = 'unpaid' AND $date_criteria AND product_id = ".$_GET['s'], OBJECT);
$row2 = $wpdb->get_row("SELECT SUM(commission2_amount) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE referrer2 = '".$affiliate->login."' AND commission2_status = 'unpaid' AND $date_criteria AND product_id = ".$_GET['s'], OBJECT);
$row3 = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE referrer = '".$affiliate->login."' AND commission_status = 'unpaid' AND $date_criteria AND product_id = ".$_GET['s'], OBJECT);
$row4 = $wpdb->get_row("SELECT SUM(commission2_amount) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE referrer2 = '".$affiliate->login."' AND commission2_status = 'unpaid' AND $date_criteria AND product_id = ".$_GET['s'], OBJECT);
$unpaid_commissions_total_amount = round(100*(($row1->total) + ($row2->total) + ($row3->total) + ($row4->total)))/100; }
else {
$row1 = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE referrer = '".$affiliate->login."' AND commission_status = 'unpaid' AND $date_criteria", OBJECT);
$row2 = $wpdb->get_row("SELECT SUM(commission2_amount) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE referrer2 = '".$affiliate->login."' AND commission2_status = 'unpaid' AND $date_criteria", OBJECT);
$row3 = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE referrer = '".$affiliate->login."' AND commission_status = 'unpaid' AND $date_criteria", OBJECT);
$row4 = $wpdb->get_row("SELECT SUM(commission2_amount) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE referrer2 = '".$affiliate->login."' AND commission2_status = 'unpaid' AND $date_criteria", OBJECT);
$row5 = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM ".$wpdb->prefix."optin_manager_prospects WHERE referrer = '".$affiliate->login."' AND commission_status = 'unpaid' AND $date_criteria", OBJECT);
$row6 = $wpdb->get_row("SELECT SUM(commission2_amount) AS total FROM ".$wpdb->prefix."optin_manager_prospects WHERE referrer2 = '".$affiliate->login."' AND commission2_status = 'unpaid' AND $date_criteria", OBJECT);
$row7 = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM ".$wpdb->prefix."contact_manager_messages WHERE referrer = '".$affiliate->login."' AND commission_status = 'unpaid' AND $date_criteria", OBJECT);
$row8 = $wpdb->get_row("SELECT SUM(commission2_amount) AS total FROM ".$wpdb->prefix."contact_manager_messages WHERE referrer2 = '".$affiliate->login."' AND commission2_status = 'unpaid' AND $date_criteria", OBJECT);
$unpaid_commissions_total_amount = round(100*(($row1->total) + ($row2->total) + ($row3->total) + ($row4->total) + ($row5->total) + ($row6->total) + ($row7->total) + ($row8->total)))/100; }
$all_unpaid_commissions_total_amount = $all_unpaid_commissions_total_amount + $unpaid_commissions_total_amount;
if ($unpaid_commissions_total_amount > 0) { $content .= $affiliate->paypal_email_address.'	'.str_replace('.', ',', $unpaid_commissions_total_amount).'	'.$currency_code."\n"; } }
if ($content == '') {
if (isset($_POST['mark_as_paid'])) { echo '<p>'.__('Commissions have been marked as paid successfully.', 'affiliation-manager').'</p>'; }
else { echo '<p>'.__('No commission to pay', 'affiliation-manager').'</p>'; } }
else { ?>
<p><?php _e('Total amount of unpaid commissions:', 'affiliation-manager'); ?> <?php echo $all_unpaid_commissions_total_amount.'	'.$currency_code; ?></p>
<p><?php _e('Copy this code into a text file to create your PayPal MassPay file:', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#paypal-masspay-file"><?php _e('More informations', 'affiliation-manager'); ?></a></p>
<textarea style="background-color: #f0f0f0; color: #808080; width: 100%;" name="content" id="content" rows="8" cols="100" onclick="this.form.content.select();">
<?php echo $content; ?>
</textarea>
<p><?php _e('When you\'ve paid commissions, click the button below to update the status of commissions:', 'affiliation-manager'); ?></p>
<p class="submit" style="margin: 0 20%;"><input type="hidden" name="submit" value="true" />
<input type="submit" class="button-secondary" name="mark_as_paid" value="<?php _e('Mark commissions as paid', 'affiliation-manager'); ?>" /></p>
<?php } ?>
</form>
</div>
</div>