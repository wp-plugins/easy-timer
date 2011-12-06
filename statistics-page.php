<?php include 'tables.php';
include_once 'tables-functions.php';
add_action('admin_footer', 'affiliation_date_picker_js');
$back_office_options = get_option('affiliation_manager_back_office');
$undisplayed_rows = (array) $back_office_options['statistics_page_undisplayed_rows'];
$undisplayed_columns = (array) $back_office_options['statistics_page_undisplayed_columns'];
include 'admin-pages.php';
$options = get_option('affiliation_manager_statistics');
if (function_exists('commerce_data')) { $currency_code = commerce_data('currency_code'); }
else { $commerce_manager_options = (array) get_option('commerce_manager');
$currency_code = do_shortcode($commerce_manager_options['currency_code']); }

$tables_names = array(
'affiliates' => __('Affiliates', 'affiliation-manager'),
'affiliates_categories' => __('Affiliates categories', 'affiliation-manager'),
'clicks' => __('Clicks', 'affiliation-manager'),
'commissions' => __('Commissions', 'affiliation-manager'),
'prospects_commissions' => __('Prospects commissions', 'affiliation-manager')
'recurring_commissions' => __('Recurring commissions', 'affiliation-manager'));
$max_tables = count($tables_names);

$filterby_options = array(
'referrer' => __('referrer', 'affiliation-manager'),
'product_id' => __('product ID', 'affiliation-manager'),
'quantity' => __('quantity', 'affiliation-manager'),
'price' => __('price', 'affiliation-manager'),
'tax' => __('tax', 'affiliation-manager'),
'shipping_cost' => __('shipping cost', 'affiliation-manager'),
'amount' => __('order amount', 'affiliation-manager'),
'payment_mode' => __('order\'s payment mode', 'affiliation-manager'),
'transaction_cost' => __('transaction cost', 'affiliation-manager'),
'postcode' => __('postcode', 'affiliation-manager'),
'town' => __('town', 'affiliation-manager'),
'country' => __('country', 'affiliation-manager'),
'ip_address' => __('IP address ', 'affiliation-manager'),
'user_agent' => __('user agent', 'affiliation-manager'),
'referring_url' => __('referring URL', 'affiliation-manager'));

if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
$_POST = array_map('stripslashes', $_POST);
$_GET['s'] = $_POST['s'];
$filterby = $_POST['filterby'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$displayed_tables = array();
for ($i = 0; $i < $max_tables; $i++) {
$tables_slugs[$i] = $_POST['table'.$i];
if ($_POST['table'.$i.'_displayed'] == 'yes') { $displayed_tables[] = $i; } } }
else {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$displayed_tables = (array) $options['displayed_tables'];
$end_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$filterby = $options['filterby'];
$start_date = $options['start_date'];
$tables_slugs = $options['tables']; }

$start_date = trim(mysql_real_escape_string(strip_tags($start_date)));
if (strlen($start_date) == 10) { $start_date .= ' 00:00:00'; }
$end_date = trim(mysql_real_escape_string(strip_tags($end_date)));
if (strlen($end_date) == 10) { $end_date .= ' 23:59:59'; }

if ($options) {
$options = array(
'displayed_tables' => $displayed_tables,
'filterby' => $filterby,
'start_date' => $start_date,
'tables' => $tables_slugs);
update_option('affiliation_manager_statistics', $options); }

if ($_GET['s'] != '') { $filter_criteria = "AND (".$filterby." = '".$_GET['s']."')"; }

$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_orders WHERE commission_amount > 0 AND (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$commissions_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$commissions_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_orders WHERE commission_status = 'paid' AND (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$paid_commissions_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE commission_status = 'paid' AND (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$paid_commissions_total_amount = round(100*$row->total)/100;
$unpaid_commissions_number = $commissions_number - $paid_commissions_number;
$unpaid_commissions_total_amount = round(100*($commissions_total_amount - $paid_commissions_total_amount))/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_orders WHERE commission2_amount > 0 AND (date BETWEEN '$start_date' AND '$end_date') ".str_replace("referrer =", "referrer2 =", $selection_criteria." ".$filter_criteria), OBJECT);
$commissions2_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(commission2_amount) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE (date BETWEEN '$start_date' AND '$end_date') ".str_replace("referrer =", "referrer2 =", $selection_criteria." ".$filter_criteria), OBJECT);
$commissions2_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_orders WHERE commission2_status = 'paid' AND (date BETWEEN '$start_date' AND '$end_date') ".str_replace("referrer =", "referrer2 =", $selection_criteria." ".$filter_criteria), OBJECT);
$paid_commissions2_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(commission2_amount) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE commission2_status = 'paid' AND (date BETWEEN '$start_date' AND '$end_date') ".str_replace("referrer =", "referrer2 =", $selection_criteria." ".$filter_criteria), OBJECT);
$paid_commissions2_total_amount = round(100*$row->total)/100;
$unpaid_commissions2_number = $commissions2_number - $paid_commissions2_number;
$unpaid_commissions2_total_amount = round(100*($commissions2_total_amount - $paid_commissions2_total_amount))/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE commission_amount > 0 AND (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$recurring_commissions_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$recurring_commissions_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE commission_status = 'paid' AND (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$paid_recurring_commissions_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE commission_status = 'paid' AND (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$paid_recurring_commissions_total_amount = round(100*$row->total)/100;
$unpaid_recurring_commissions_number = $recurring_commissions_number - $paid_recurring_commissions_number;
$unpaid_recurring_commissions_total_amount = round(100*($recurring_commissions_total_amount - $paid_recurring_commissions_total_amount))/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE commission2_amount > 0 AND (date BETWEEN '$start_date' AND '$end_date') ".str_replace("referrer =", "referrer2 =", $selection_criteria." ".$filter_criteria), OBJECT);
$recurring_commissions2_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(commission2_amount) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE (date BETWEEN '$start_date' AND '$end_date') ".str_replace("referrer =", "referrer2 =", $selection_criteria." ".$filter_criteria), OBJECT);
$recurring_commissions2_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE commission2_status = 'paid' AND (date BETWEEN '$start_date' AND '$end_date') ".str_replace("referrer =", "referrer2 =", $selection_criteria." ".$filter_criteria), OBJECT);
$paid_recurring_commissions2_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(commission2_amount) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE commission2_status = 'paid' AND (date BETWEEN '$start_date' AND '$end_date') ".str_replace("referrer =", "referrer2 =", $selection_criteria." ".$filter_criteria), OBJECT);
$paid_recurring_commissions2_total_amount = round(100*$row->total)/100;
$unpaid_recurring_commissions2_number = $recurring_commissions2_number - $paid_recurring_commissions2_number;
$unpaid_recurring_commissions2_total_amount = round(100*($recurring_commissions2_total_amount - $paid_recurring_commissions2_total_amount))/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."optin_manager_prospects WHERE commission_amount > 0 AND (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$prospects_commissions_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM ".$wpdb->prefix."optin_manager_prospects WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$prospects_commissions_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."optin_manager_prospects WHERE commission_status = 'paid' AND (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$paid_prospects_commissions_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(commission_amount) AS total FROM ".$wpdb->prefix."optin_manager_prospects WHERE commission_status = 'paid' AND (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$paid_prospects_commissions_total_amount = round(100*$row->total)/100;
$unpaid_prospects_commissions_number = $prospects_commissions_number - $paid_prospects_commissions_number;
$unpaid_prospects_commissions_total_amount = round(100*($prospects_commissions_total_amount - $paid_prospects_commissions_total_amount))/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."optin_manager_prospects WHERE commission2_amount > 0 AND (date BETWEEN '$start_date' AND '$end_date') ".str_replace("referrer =", "referrer2 =", $selection_criteria." ".$filter_criteria), OBJECT);
$prospects_commissions2_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(commission2_amount) AS total FROM ".$wpdb->prefix."optin_manager_prospects WHERE (date BETWEEN '$start_date' AND '$end_date') ".str_replace("referrer =", "referrer2 =", $selection_criteria." ".$filter_criteria), OBJECT);
$prospects_commissions2_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."optin_manager_prospects WHERE commission2_status = 'paid' AND (date BETWEEN '$start_date' AND '$end_date') ".str_replace("referrer =", "referrer2 =", $selection_criteria." ".$filter_criteria), OBJECT);
$paid_prospects_commissions2_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(commission2_amount) AS total FROM ".$wpdb->prefix."optin_manager_prospects WHERE commission2_status = 'paid' AND (date BETWEEN '$start_date' AND '$end_date') ".str_replace("referrer =", "referrer2 =", $selection_criteria." ".$filter_criteria), OBJECT);
$paid_prospects_commissions2_total_amount = round(100*$row->total)/100;
$unpaid_prospects_commissions2_number = $prospects_commissions2_number - $paid_prospects_commissions2_number;
$unpaid_prospects_commissions2_total_amount = round(100*($prospects_commissions2_total_amount - $paid_prospects_commissions2_total_amount))/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_orders WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$orders_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(amount) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$orders_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$recurring_payments_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(amount) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$recurring_payments_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."optin_manager_prospects WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$prospects_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$affiliates_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE status = 'active' AND (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$active_affiliates_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE status = 'inactive' AND (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$inactive_affiliates_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."affiliation_manager_affiliates_categories WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$affiliates_categories_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."affiliation_manager_clicks WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$clicks_number = (int) $row->total;

$commissions_a_tag = '<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-commissions">';
$paid_commissions_a_tag = '<a style="color: #008000; text-decoration: none;" href="admin.php?page=affiliation-manager-commissions&amp;commission_status=paid">';
$unpaid_commissions_a_tag = '<a style="color: #e08000; text-decoration: none;" href="admin.php?page=affiliation-manager-commissions&amp;commission_status=unpaid">';
$commissions2_a_tag = '<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-commissions">';
$paid_commissions2_a_tag = '<a style="color: #008000; text-decoration: none;" href="admin.php?page=affiliation-manager-commissions&amp;commission2_status=paid">';
$unpaid_commissions2_a_tag = '<a style="color: #e08000; text-decoration: none;" href="admin.php?page=affiliation-manager-commissions&amp;commission2_status=unpaid">';
$recurring_commissions_a_tag = '<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-recurring-commissions">';
$paid_recurring_commissions_a_tag = '<a style="color: #008000; text-decoration: none;" href="admin.php?page=affiliation-manager-recurring-commissions&amp;commission_status=paid">';
$unpaid_recurring_commissions_a_tag = '<a style="color: #e08000; text-decoration: none;" href="admin.php?page=affiliation-manager-recurring-commissions&amp;commission_status=unpaid">';
$recurring_commissions2_a_tag = '<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-recurring-commissions">';
$paid_recurring_commissions2_a_tag = '<a style="color: #008000; text-decoration: none;" href="admin.php?page=affiliation-manager-recurring-commissions&amp;commission2_status=paid">';
$unpaid_recurring_commissions2_a_tag = '<a style="color: #e08000; text-decoration: none;" href="admin.php?page=affiliation-manager-recurring-commissions&amp;commission2_status=unpaid">';
$prospects_commissions_a_tag = '<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-prospects-commissions">';
$paid_prospects_commissions_a_tag = '<a style="color: #008000; text-decoration: none;" href="admin.php?page=affiliation-manager-prospects-commissions&amp;commission_status=paid">';
$unpaid_prospects_commissions_a_tag = '<a style="color: #e08000; text-decoration: none;" href="admin.php?page=affiliation-manager-prospects-commissions&amp;commission_status=unpaid">';
$prospects_commissions2_a_tag = '<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-prospects-commissions">';
$paid_prospects_commissions2_a_tag = '<a style="color: #008000; text-decoration: none;" href="admin.php?page=affiliation-manager-prospects-commissions&amp;commission2_status=paid">';
$unpaid_prospects_commissions2_a_tag = '<a style="color: #e08000; text-decoration: none;" href="admin.php?page=affiliation-manager-prospects-commissions&amp;commission2_status=unpaid">';
$orders_a_tag = '<a style="text-decoration: none;" href="admin.php?page=commerce-manager-orders">';
$recurring_payments_a_tag = '<a style="text-decoration: none;" href="admin.php?page=commerce-manager-recurring-payments">';
$prospects_a_tag = '<a style="text-decoration: none;" href="admin.php?page=optin-manager-prospects">';
$affiliates_a_tag = '<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliates">';
$active_affiliates_a_tag = '<a style="color: #008000; text-decoration: none;" href="admin.php?page=affiliation-manager-affiliates&amp;status=active">';
$inactive_affiliates_a_tag = '<a style="color: #e08000; text-decoration: none;" href="admin.php?page=affiliation-manager-affiliates&amp;status=inactive">';
$affiliates_categories_a_tag = '<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliates-categories">';
$clicks_a_tag = '<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-clicks">'; ?>

<div class="wrap">
<div id="poststuff">
<?php affiliation_manager_pages_top($back_office_options); ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php affiliation_manager_pages_menu($back_office_options); ?>
<?php affiliation_manager_pages_search_field('filter', $filterby, $filterby_options); ?>
<?php affiliation_manager_pages_date_picker($start_date, $end_date); ?>
<?php $global_table_ths = '
<th scope="col" class="manage-column" style="width: 30%;">'.$statistics_columns['data']['name'].'</th>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<th scope="col" class="manage-column" style="width: 20%;">'.$statistics_columns['quantity']['name'].'</th>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<th scope="col" class="manage-column" style="width: 30%;">'.$statistics_columns['commissions_percentage']['name'].'</th>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<th scope="col" class="manage-column" style="width: 30%;">'.$statistics_columns['orders_percentage']['name'].'</th>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<th scope="col" class="manage-column" style="width: 20%;">'.$statistics_columns['total_amount']['name'].'</th>');
echo '
<h3 id="global-statistics"><strong>'.__('Global statistics', 'affiliation-manager').'</strong></h3>
<table class="wp-list-table widefat fixed" style="margin: 1em 0 2em 0;">
<thead><tr>'.$global_table_ths.'</tr></thead>
<tfoot><tr>'.$global_table_ths.'</tr></tfoot>
<tbody>';
if (!in_array('commissions', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['commissions']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$commissions_a_tag.$commissions_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>'.$commissions_a_tag.($commissions_number == 0 ? '--' : '100 %').'</a></td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>'.($orders_number == 0 ? '--' : $commissions_a_tag.((round(10000*$commissions_number/$orders_number))/100).' %</a>').'</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$commissions_a_tag.$commissions_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('paid_commissions', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['paid_commissions']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$paid_commissions_a_tag.$paid_commissions_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>'.($commissions_number == 0 ? '--' : $paid_commissions_a_tag.((round(10000*$paid_commissions_number/$commissions_number))/100).' %</a>').'</td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>'.($orders_number == 0 ? '--' : $paid_commissions_a_tag.((round(10000*$paid_commissions_number/$orders_number))/100).' %</a>').'</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$paid_commissions_a_tag.$paid_commissions_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('unpaid_commissions', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['unpaid_commissions']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$unpaid_commissions_a_tag.$unpaid_commissions_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>'.($commissions_number == 0 ? '--' : $unpaid_commissions_a_tag.((round(10000*$unpaid_commissions_number/$commissions_number))/100).' %</a>').'</td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>'.($orders_number == 0 ? '--' : $unpaid_commissions_a_tag.((round(10000*$unpaid_commissions_number/$orders_number))/100).' %</a>').'</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$unpaid_commissions_a_tag.$unpaid_commissions_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('commissions2', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['commissions2']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$commissions2_a_tag.$commissions2_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>'.$commissions2_a_tag.($commissions2_number == 0 ? '--' : '100 %').'</a></td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>'.($orders_number == 0 ? '--' : $commissions2_a_tag.((round(10000*$commissions2_number/$orders_number))/100).' %</a>').'</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$commissions2_a_tag.$commissions2_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('paid_commissions2', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['paid_commissions2']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$paid_commissions2_a_tag.$paid_commissions2_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>'.($commissions2_number == 0 ? '--' : $paid_commissions2_a_tag.((round(10000*$paid_commissions2_number/$commissions2_number))/100).' %</a>').'</td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>'.($orders_number == 0 ? '--' : $paid_commissions2_a_tag.((round(10000*$paid_commissions2_number/$orders_number))/100).' %</a>').'</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$paid_commissions2_a_tag.$paid_commissions2_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('unpaid_commissions2', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['unpaid_commissions2']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$unpaid_commissions2_a_tag.$unpaid_commissions2_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>'.($commissions2_number == 0 ? '--' : $unpaid_commissions2_a_tag.((round(10000*$unpaid_commissions2_number/$commissions2_number))/100).' %</a>').'</td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>'.($orders_number == 0 ? '--' : $unpaid_commissions2_a_tag.((round(10000*$unpaid_commissions2_number/$orders_number))/100).' %</a>').'</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$unpaid_commissions2_a_tag.$unpaid_commissions2_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('recurring_commissions', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['recurring_commissions']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$recurring_commissions_a_tag.$recurring_commissions_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>'.$recurring_commissions_a_tag.($recurring_commissions_number == 0 ? '--' : '100 %').'</a></td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$recurring_commissions_a_tag.$recurring_commissions_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('paid_recurring_commissions', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['paid_recurring_commissions']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$paid_recurring_commissions_a_tag.$paid_recurring_commissions_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>'.($recurring_commissions_number == 0 ? '--' : $paid_recurring_commissions_a_tag.((round(10000*$paid_recurring_commissions_number/$recurring_commissions_number))/100).' %</a>').'</td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$paid_recurring_commissions_a_tag.$paid_recurring_commissions_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('unpaid_recurring_commissions', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['unpaid_recurring_commissions']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$unpaid_recurring_commissions_a_tag.$unpaid_recurring_commissions_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>'.($recurring_commissions_number == 0 ? '--' : $unpaid_recurring_commissions_a_tag.((round(10000*$unpaid_recurring_commissions_number/$recurring_commissions_number))/100).' %</a>').'</td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$unpaid_recurring_commissions_a_tag.$unpaid_recurring_commissions_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('recurring_commissions2', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['recurring_commissions2']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$recurring_commissions2_a_tag.$recurring_commissions2_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>'.$recurring_commissions2_a_tag.($recurring_commissions2_number == 0 ? '--' : '100 %').'</a></td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$recurring_commissions2_a_tag.$recurring_commissions2_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('paid_recurring_commissions2', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['paid_recurring_commissions2']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$paid_recurring_commissions2_a_tag.$paid_recurring_commissions2_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>'.($recurring_commissions2_number == 0 ? '--' : $paid_recurring_commissions2_a_tag.((round(10000*$paid_recurring_commissions2_number/$recurring_commissions2_number))/100).' %</a>').'</td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$paid_recurring_commissions2_a_tag.$paid_recurring_commissions2_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('unpaid_recurring_commissions2', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['unpaid_recurring_commissions2']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$unpaid_recurring_commissions2_a_tag.$unpaid_recurring_commissions2_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>'.($recurring_commissions2_number == 0 ? '--' : $unpaid_recurring_commissions2_a_tag.((round(10000*$unpaid_recurring_commissions2_number/$recurring_commissions2_number))/100).' %</a>').'</td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$unpaid_recurring_commissions2_a_tag.$unpaid_recurring_commissions2_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('prospects_commissions', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['prospects_commissions']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$prospects_commissions_a_tag.$prospects_commissions_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>'.$prospects_commissions_a_tag.($prospects_commissions_number == 0 ? '--' : '100 %').'</a></td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$prospects_commissions_a_tag.$prospects_commissions_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('paid_prospects_commissions', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['paid_prospects_commissions']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$paid_prospects_commissions_a_tag.$paid_prospects_commissions_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>'.($prospects_commissions_number == 0 ? '--' : $paid_prospects_commissions_a_tag.((round(10000*$paid_prospects_commissions_number/$prospects_commissions_number))/100).' %</a>').'</td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$paid_prospects_commissions_a_tag.$paid_prospects_commissions_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('unpaid_prospects_commissions', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['unpaid_prospects_commissions']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$unpaid_prospects_commissions_a_tag.$unpaid_prospects_commissions_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>'.($prospects_commissions_number == 0 ? '--' : $unpaid_prospects_commissions_a_tag.((round(10000*$unpaid_prospects_commissions_number/$prospects_commissions_number))/100).' %</a>').'</td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$unpaid_prospects_commissions_a_tag.$unpaid_prospects_commissions_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('prospects_commissions2', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['prospects_commissions2']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$prospects_commissions2_a_tag.$prospects_commissions2_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>'.$prospects_commissions2_a_tag.($prospects_commissions2_number == 0 ? '--' : '100 %').'</a></td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$prospects_commissions2_a_tag.$prospects_commissions2_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('paid_prospects_commissions2', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['paid_prospects_commissions2']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$paid_prospects_commissions2_a_tag.$paid_prospects_commissions2_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>'.($prospects_commissions2_number == 0 ? '--' : $paid_prospects_commissions2_a_tag.((round(10000*$paid_prospects_commissions2_number/$prospects_commissions2_number))/100).' %</a>').'</td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$paid_prospects_commissions2_a_tag.$paid_prospects_commissions2_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('unpaid_prospects_commissions2', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['unpaid_prospects_commissions2']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$unpaid_prospects_commissions2_a_tag.$unpaid_prospects_commissions2_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>'.($prospects_commissions2_number == 0 ? '--' : $unpaid_prospects_commissions2_a_tag.((round(10000*$unpaid_prospects_commissions2_number/$prospects_commissions2_number))/100).' %</a>').'</td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$unpaid_prospects_commissions2_a_tag.$unpaid_prospects_commissions2_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('orders', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['orders']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$orders_a_tag.$orders_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>'.$orders_a_tag.($orders_number == 0 ? '--' : '100 %').'</a></td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$orders_a_tag.$orders_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('recurring_payments', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['recurring_payments']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$recurring_payments_a_tag.$recurring_payments_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$recurring_payments_a_tag.$recurring_payments_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('prospects_payments', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['prospects_payments']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$prospects_payments_a_tag.$prospects_payments_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>--</td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('affiliates', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['affiliates']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$affiliates_a_tag.$affiliates_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>--</td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('active_affiliates', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['active_affiliates']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$active_affiliates_a_tag.$active_affiliates_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>--</td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('inactive_affiliates', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['inactive_affiliates']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$inactive_affiliates_a_tag.$inactive_affiliates_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>--</td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('affiliates_categories', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['affiliates_categories']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$affiliates_categories_a_tag.$affiliates_categories_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>--</td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('clicks', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['clicks']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$clicks_a_tag.$clicks_number.'</a></td>').'
'.(in_array('commissions_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>--</td>').'
</tr>'; $boolean = !$boolean; }
echo '</tbody></table>'; ?>
<div style="text-align: center;">
<?php for ($i = 0; $i < $max_tables; $i++) {
echo '<label>'.__('Table', 'affiliation-manager').' '.($i + 1).' <select name="table'.$i.'" id="table'.$i.'">';
foreach ($tables_names as $key => $value) { echo '<option value="'.$key.'"'.($tables_slugs[$i] == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; }
echo '</select></label>
<label><input type="checkbox" name="table'.$i.'_displayed" id="table'.$i.'_displayed" value="yes"'.(!in_array($i, $displayed_tables) ? '' : ' checked="checked"').' /> '.__('Display', 'affiliation-manager').'<br /></label>'; } ?><br />
<input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" />
</div>
<?php if (count($displayed_tables) > 1) {
for ($i = 0; $i < $max_tables; $i++) {
if (in_array($i, $displayed_tables)) { $summary .= '<li> | <a href="#'.str_replace('_', '-', $tables_slugs[$i]).'">'.$tables_names[$tables_slugs[$i]].'</a></li>'; } }
$summary = '<ul class="subsubsub" style="float: none; text-align: center;">
<li>'.substr($summary, 7).'</ul>'; }
for ($i = 0; $i < $max_tables; $i++) {
if (in_array($i, $displayed_tables)) {
$table_slug = $tables_slugs[$i];
$table_name = table_name($table_slug);
$table_criteria = table_criteria($table_slug);
$options = get_option('affiliation_manager_'.$table_slug);
$columns = (array) $options['columns'];
$max_columns = count($columns);
$displayed_columns = (array) $options['displayed_columns'];
for ($j = 0; $j < $max_columns; $j++) { if (in_array($j, $displayed_columns)) { $table_ths .= table_th($table_slug, $columns[$j]); } }
echo $summary.'
<h3 id="'.str_replace('_', '-', $tables_slugs[$i]).'"><strong>'.$tables_names[$tables_slugs[$i]].'</strong></h3>
<table class="wp-list-table widefat fixed" style="margin: 1em 0 2em 0;">
<thead><tr>'.$table_ths.'</tr></thead>
<tfoot><tr>'.$table_ths.'</tr></tfoot>
<tbody>';
$items = $wpdb->get_results("SELECT * FROM $table_name WHERE (date BETWEEN '$start_date' AND '$end_date') $table_criteria $selection_criteria $filter_criteria ORDER BY date DESC", OBJECT);
if ($items) { foreach ($items as $item) {
$first = true; for ($j = 0; $j < $max_columns; $j++) {
if (in_array($j, $displayed_columns)) {
$table_tds .= '<td'.($first ? ' style="height: 6em;"' : '').'>'.table_td($table_slug, $columns[$j], $item).($first ? row_actions($table_slug, $item) : '').'</td>';
$first = false; } }
echo '<tr'.($boolean ? '' : ' class="alternate"').'>'.$table_tds.'</tr>';
$table_tds = ''; $boolean = !$boolean; } }
else { echo '<tr class="no-items"><td class="colspanchange" colspan="'.count($displayed_columns).'">'.no_items($table_slug).'</td></tr>'; }
echo '</tbody></table>';
$table_ths = ''; $table_criteria = ''; } } ?>
</form>
</div>
</div>