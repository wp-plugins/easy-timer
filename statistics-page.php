<?php include 'tables.php';
include_once 'tables-functions.php';
$back_office_options = get_option('commerce_manager_back_office');
$undisplayed_rows = (array) $back_office_options['statistics_page_undisplayed_rows'];
$undisplayed_columns = (array) $back_office_options['statistics_page_undisplayed_columns'];
include 'admin-pages.php';
$options = get_option('commerce_manager_statistics');
$currency_code = commerce_data('currency_code');

$tables_names = array(
'clients' => __('Clients', 'commerce-manager'),
'clients_categories' => __('Clients categories', 'commerce-manager'),
'forms' => __('Forms', 'commerce-manager'),
'forms_categories' => __('Forms categories', 'commerce-manager'),
'orders' => __('Orders', 'commerce-manager'),
'products' => __('Products', 'commerce-manager'),
'products_categories' => __('Products categories', 'commerce-manager'),
'recurring_payments' => __('Recurring payments', 'commerce-manager'));
$max_tables = count($tables_names);

$filterby_options = array(
'product_id' => __('product ID', 'commerce-manager'),
'quantity' => __('quantity', 'commerce-manager'),
'price' => __('price', 'commerce-manager'),
'tax' => __('tax', 'commerce-manager'),
'shipping_cost' => __('shipping cost', 'commerce-manager'),
'amount' => __('amount', 'commerce-manager'),
'payment_mode' => __('payment mode', 'commerce-manager'),
'transaction_cost' => __('transaction cost', 'commerce-manager'),
'postcode' => __('postcode', 'commerce-manager'),
'town' => __('town', 'commerce-manager'),
'country' => __('country', 'commerce-manager'),
'ip_address' => __('IP address ', 'commerce-manager'),
'user_agent' => __('user agent', 'commerce-manager'),
'receiver_account' => __('receiver account', 'commerce-manager'),
'referring_url' => __('referring URL', 'commerce-manager'),
'referrer' => __('referrer', 'commerce-manager'));

if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = stripslashes($value); } }
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
$_GET['date_criteria'] = str_replace(' ', '%20', '&amp;start_date='.$start_date.'&amp;end_date='.$end_date);
$date_criteria = "(date BETWEEN '$start_date' AND '$end_date')";

if (($options) && (commerce_manager_user_can($back_office_options, 'manage'))) {
$options = array(
'displayed_tables' => $displayed_tables,
'filterby' => $filterby,
'start_date' => $start_date,
'tables' => $tables_slugs);
update_option('commerce_manager_statistics', $options); }

if ($_GET['s'] != '') {
$_GET['filter_criteria'] = str_replace(' ', '%20', '&amp;'.$filterby.'='.$_GET['s']);
$filter_criteria = (is_numeric($_GET['s']) ? "AND (".$filterby." = ".$_GET['s'].")" : "AND (".$filterby." = '".$_GET['s']."')"); }

$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_orders WHERE $date_criteria $selection_criteria $filter_criteria", OBJECT);
$orders_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(price) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE $date_criteria $selection_criteria $filter_criteria", OBJECT);
$orders_total_price = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(tax) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE tax_included_in_price = 'yes' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$orders_total_tax = round(100*$row->total)/100;
$orders_total_net_price = round(100*($orders_total_price - $orders_total_tax))/100;
$row = $wpdb->get_row("SELECT SUM(tax) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE $date_criteria $selection_criteria $filter_criteria", OBJECT);
$orders_total_tax = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(shipping_cost) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE $date_criteria $selection_criteria $filter_criteria", OBJECT);
$orders_total_shipping_cost = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(transaction_cost) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE $date_criteria $selection_criteria $filter_criteria", OBJECT);
$orders_total_transaction_cost = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(amount) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE $date_criteria $selection_criteria $filter_criteria", OBJECT);
$orders_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_orders WHERE status = 'processed' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$processed_orders_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(price) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE status = 'processed' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$processed_orders_total_price = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(tax) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE status = 'processed' AND tax_included_in_price = 'yes' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$processed_orders_total_tax = round(100*$row->total)/100;
$processed_orders_total_net_price = round(100*($processed_orders_total_price - $processed_orders_total_tax))/100;
$row = $wpdb->get_row("SELECT SUM(tax) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE status = 'processed' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$processed_orders_total_tax = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(shipping_cost) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE status = 'processed' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$processed_orders_total_shipping_cost = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(transaction_cost) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE status = 'processed' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$processed_orders_total_transaction_cost = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(amount) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE status = 'processed' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$processed_orders_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_orders WHERE status = 'unprocessed' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$unprocessed_orders_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(price) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE status = 'unprocessed' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$unprocessed_orders_total_price = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(tax) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE status = 'unprocessed' AND tax_included_in_price = 'yes' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$unprocessed_orders_total_tax = round(100*$row->total)/100;
$unprocessed_orders_total_net_price = round(100*($unprocessed_orders_total_price - $unprocessed_orders_total_tax))/100;
$row = $wpdb->get_row("SELECT SUM(tax) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE status = 'unprocessed' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$unprocessed_orders_total_tax = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(shipping_cost) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE status = 'unprocessed' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$unprocessed_orders_total_shipping_cost = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(transaction_cost) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE status = 'unprocessed' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$unprocessed_orders_total_transaction_cost = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(amount) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE status = 'unprocessed' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$unprocessed_orders_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_orders WHERE status = 'refunded' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$refunded_orders_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(price) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE status = 'refunded' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$refunded_orders_total_price = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(tax) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE status = 'refunded' AND tax_included_in_price = 'yes' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$refunded_orders_total_tax = round(100*$row->total)/100;
$refunded_orders_total_net_price = round(100*($refunded_orders_total_price - $refunded_orders_total_tax))/100;
$row = $wpdb->get_row("SELECT SUM(tax) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE status = 'refunded' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$refunded_orders_total_tax = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(shipping_cost) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE status = 'refunded' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$refunded_orders_total_shipping_cost = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(transaction_cost) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE status = 'refunded' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$refunded_orders_total_transaction_cost = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(amount) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE status = 'refunded' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$refunded_orders_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE $date_criteria $selection_criteria $filter_criteria", OBJECT);
$recurring_payments_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(price) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE $date_criteria $selection_criteria $filter_criteria", OBJECT);
$recurring_payments_total_price = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(tax) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE tax_included_in_price = 'yes' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$recurring_payments_total_tax = round(100*$row->total)/100;
$recurring_payments_total_net_price = round(100*($recurring_payments_total_price - $recurring_payments_total_tax))/100;
$row = $wpdb->get_row("SELECT SUM(tax) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE $date_criteria $selection_criteria $filter_criteria", OBJECT);
$recurring_payments_total_tax = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(shipping_cost) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE $date_criteria $selection_criteria $filter_criteria", OBJECT);
$recurring_payments_total_shipping_cost = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(transaction_cost) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE $date_criteria $selection_criteria $filter_criteria", OBJECT);
$recurring_payments_total_transaction_cost = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(amount) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE $date_criteria $selection_criteria $filter_criteria", OBJECT);
$recurring_payments_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE status = 'received' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$received_recurring_payments_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(price) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE status = 'received' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$received_recurring_payments_total_price = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(tax) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE status = 'received' AND tax_included_in_price = 'yes' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$received_recurring_payments_total_tax = round(100*$row->total)/100;
$received_recurring_payments_total_net_price = round(100*($received_recurring_payments_total_price - $received_recurring_payments_total_tax))/100;
$row = $wpdb->get_row("SELECT SUM(tax) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE status = 'received' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$received_recurring_payments_total_tax = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(shipping_cost) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE status = 'received' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$received_recurring_payments_total_shipping_cost = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(transaction_cost) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE status = 'received' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$received_recurring_payments_total_transaction_cost = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(amount) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE status = 'received' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$received_recurring_payments_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE status = 'refunded' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$refunded_recurring_payments_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(price) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE status = 'refunded' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$refunded_recurring_payments_total_price = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(tax) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE status = 'refunded' AND tax_included_in_price = 'yes' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$refunded_recurring_payments_total_tax = round(100*$row->total)/100;
$refunded_recurring_payments_total_net_price = round(100*($refunded_recurring_payments_total_price - $refunded_recurring_payments_total_tax))/100;
$row = $wpdb->get_row("SELECT SUM(tax) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE status = 'refunded' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$refunded_recurring_payments_total_tax = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(shipping_cost) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE status = 'refunded' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$refunded_recurring_payments_total_shipping_cost = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(transaction_cost) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE status = 'refunded' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$refunded_recurring_payments_total_transaction_cost = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT SUM(amount) AS total FROM ".$wpdb->prefix."commerce_manager_recurring_payments WHERE status = 'refunded' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$refunded_recurring_payments_total_amount = round(100*$row->total)/100;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_products WHERE $date_criteria $selection_criteria $filter_criteria", OBJECT);
$products_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_products_categories WHERE $date_criteria $selection_criteria $filter_criteria", OBJECT);
$products_categories_number = (int) $row->total;
$row = $wpdb->get_row("SELECT SUM(quantity) AS total FROM ".$wpdb->prefix."commerce_manager_orders WHERE $date_criteria $selection_criteria $filter_criteria", OBJECT);
$sold_items_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_clients WHERE $date_criteria $selection_criteria $filter_criteria", OBJECT);
$clients_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_clients WHERE status = 'active' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$active_clients_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_clients WHERE status = 'inactive' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$inactive_clients_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_clients WHERE status = 'deactivated' AND $date_criteria $selection_criteria $filter_criteria", OBJECT);
$deactivated_clients_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_clients_categories WHERE $date_criteria $selection_criteria $filter_criteria", OBJECT);
$clients_categories_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_forms WHERE $date_criteria $selection_criteria $filter_criteria", OBJECT);
$forms_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."commerce_manager_forms_categories WHERE $date_criteria $selection_criteria $filter_criteria", OBJECT);
$forms_categories_number = (int) $row->total;

$_GET['criteria'] = $_GET['date_criteria'].$_GET['selection_criteria'].$_GET['filter_criteria'];

$orders_a_tag = '<a style="text-decoration: none;" href="admin.php?page=commerce-manager-orders'.$_GET['criteria'].'">';
$processed_orders_a_tag = '<a style="color: #008000; text-decoration: none;" href="admin.php?page=commerce-manager-orders&amp;status=processed'.$_GET['criteria'].'">';
$unprocessed_orders_a_tag = '<a style="color: #e08000; text-decoration: none;" href="admin.php?page=commerce-manager-orders&amp;status=unprocessed'.$_GET['criteria'].'">';
$refunded_orders_a_tag = '<a style="color: #c00000; text-decoration: none;" href="admin.php?page=commerce-manager-orders&amp;status=refunded'.$_GET['criteria'].'">';
$recurring_payments_a_tag = '<a style="text-decoration: none;" href="admin.php?page=commerce-manager-recurring-payments'.$_GET['criteria'].'">';
$received_recurring_payments_a_tag = '<a style="color: #008000; text-decoration: none;" href="admin.php?page=commerce-manager-recurring-payments&amp;status=received'.$_GET['criteria'].'">';
$refunded_recurring_payments_a_tag = '<a style="color: #c00000; text-decoration: none;" href="admin.php?page=commerce-manager-recurring-payments&amp;status=refunded'.$_GET['criteria'].'">';
$products_a_tag = '<a style="text-decoration: none;" href="admin.php?page=commerce-manager-products'.$_GET['criteria'].'">';
$products_categories_a_tag = '<a style="text-decoration: none;" href="admin.php?page=commerce-manager-products-categories'.$_GET['criteria'].'">';
$clients_a_tag = '<a style="text-decoration: none;" href="admin.php?page=commerce-manager-clients'.$_GET['criteria'].'">';
$active_clients_a_tag = '<a style="color: #008000; text-decoration: none;" href="admin.php?page=commerce-manager-clients&amp;status=active'.$_GET['criteria'].'">';
$inactive_clients_a_tag = '<a style="color: #e08000; text-decoration: none;" href="admin.php?page=commerce-manager-clients&amp;status=inactive'.$_GET['criteria'].'">';
$deactivated_clients_a_tag = '<a style="color: #c00000; text-decoration: none;" href="admin.php?page=commerce-manager-clients&amp;status=deactivated'.$_GET['criteria'].'">';
$clients_categories_a_tag = '<a style="text-decoration: none;" href="admin.php?page=commerce-manager-clients-categories'.$_GET['criteria'].'">';
$forms_a_tag = '<a style="text-decoration: none;" href="admin.php?page=commerce-manager-forms'.$_GET['criteria'].'">';
$forms_categories_a_tag = '<a style="text-decoration: none;" href="admin.php?page=commerce-manager-forms-categories'.$_GET['criteria'].'">'; ?>

<div class="wrap">
<div id="poststuff">
<?php commerce_manager_pages_top($back_office_options); ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php commerce_manager_pages_menu($back_office_options); ?>
<?php commerce_manager_pages_search_field('filter', $filterby, $filterby_options); ?>
<?php commerce_manager_pages_date_picker($start_date, $end_date); ?>
<?php if (count($undisplayed_rows) < count($statistics_rows)) {
foreach ($statistics_columns as $key => $value) {
if (!in_array($key, $undisplayed_columns)) { $global_table_ths .= '<th scope="col" class="manage-column" style="width: '.$value['width'].'%;">'.$value['name'].'</th>'; } }
echo '
<h3 id="global-statistics"><strong>'.__('Global statistics', 'commerce-manager').'</strong></h3>
<table class="wp-list-table widefat fixed" style="margin: 1em 0 2em 0;">
<thead><tr>'.$global_table_ths.'</tr></thead>
<tfoot><tr>'.$global_table_ths.'</tr></tfoot>
<tbody>';
if (!in_array('orders', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['orders']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$orders_a_tag.$orders_number.'</a></td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>'.$orders_a_tag.($orders_number == 0 ? '--' : '100 %').'</a></td>').'
'.(in_array('net_prices', $undisplayed_columns) ? '' : '<td>'.$orders_a_tag.$orders_total_net_price.' '.$currency_code.'</a></td>').'
'.(in_array('taxes', $undisplayed_columns) ? '' : '<td>'.$orders_a_tag.$orders_total_tax.' '.$currency_code.'</a></td>').'
'.(in_array('shipping_costs', $undisplayed_columns) ? '' : '<td>'.$orders_a_tag.$orders_total_shipping_cost.' '.$currency_code.'</a></td>').'
'.(in_array('transaction_costs', $undisplayed_columns) ? '' : '<td>'.$orders_a_tag.$orders_total_transaction_cost.' '.$currency_code.'</a></td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$orders_a_tag.$orders_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('processed_orders', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['processed_orders']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$processed_orders_a_tag.$processed_orders_number.'</a></td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>'.($orders_number == 0 ? '--' : $processed_orders_a_tag.((round(10000*$processed_orders_number/$orders_number))/100).' %</a>').'</td>').'
'.(in_array('net_prices', $undisplayed_columns) ? '' : '<td>'.$processed_orders_a_tag.$processed_orders_total_net_price.' '.$currency_code.'</a></td>').'
'.(in_array('taxes', $undisplayed_columns) ? '' : '<td>'.$processed_orders_a_tag.$processed_orders_total_tax.' '.$currency_code.'</a></td>').'
'.(in_array('shipping_costs', $undisplayed_columns) ? '' : '<td>'.$processed_orders_a_tag.$processed_orders_total_shipping_cost.' '.$currency_code.'</a></td>').'
'.(in_array('transaction_costs', $undisplayed_columns) ? '' : '<td>'.$processed_orders_a_tag.$processed_orders_total_transaction_cost.' '.$currency_code.'</a></td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$processed_orders_a_tag.$processed_orders_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('unprocessed_orders', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['unprocessed_orders']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$unprocessed_orders_a_tag.$unprocessed_orders_number.'</a></td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>'.($orders_number == 0 ? '--' : $unprocessed_orders_a_tag.((round(10000*$unprocessed_orders_number/$orders_number))/100).' %</a>').'</td>').'
'.(in_array('net_prices', $undisplayed_columns) ? '' : '<td>'.$unprocessed_orders_a_tag.$unprocessed_orders_total_net_price.' '.$currency_code.'</a></td>').'
'.(in_array('taxes', $undisplayed_columns) ? '' : '<td>'.$unprocessed_orders_a_tag.$unprocessed_orders_total_tax.' '.$currency_code.'</a></td>').'
'.(in_array('shipping_costs', $undisplayed_columns) ? '' : '<td>'.$unprocessed_orders_a_tag.$unprocessed_orders_total_shipping_cost.' '.$currency_code.'</a></td>').'
'.(in_array('transaction_costs', $undisplayed_columns) ? '' : '<td>'.$unprocessed_orders_a_tag.$unprocessed_orders_total_transaction_cost.' '.$currency_code.'</a></td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$unprocessed_orders_a_tag.$unprocessed_orders_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('refunded_orders', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['refunded_orders']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$refunded_orders_a_tag.$refunded_orders_number.'</a></td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>'.($orders_number == 0 ? '--' : $refunded_orders_a_tag.((round(10000*$refunded_orders_number/$orders_number))/100).' %</a>').'</td>').'
'.(in_array('net_prices', $undisplayed_columns) ? '' : '<td>'.$refunded_orders_a_tag.$refunded_orders_total_net_price.' '.$currency_code.'</a></td>').'
'.(in_array('taxes', $undisplayed_columns) ? '' : '<td>'.$refunded_orders_a_tag.$refunded_orders_total_tax.' '.$currency_code.'</a></td>').'
'.(in_array('shipping_costs', $undisplayed_columns) ? '' : '<td>'.$refunded_orders_a_tag.$refunded_orders_total_shipping_cost.' '.$currency_code.'</a></td>').'
'.(in_array('transaction_costs', $undisplayed_columns) ? '' : '<td>'.$refunded_orders_a_tag.$refunded_orders_total_transaction_cost.' '.$currency_code.'</a></td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$refunded_orders_a_tag.$refunded_orders_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('recurring_payments', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['recurring_payments']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$recurring_payments_a_tag.$recurring_payments_number.'</a></td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('net_prices', $undisplayed_columns) ? '' : '<td>'.$recurring_payments_a_tag.$recurring_payments_total_net_price.' '.$currency_code.'</a></td>').'
'.(in_array('taxes', $undisplayed_columns) ? '' : '<td>'.$recurring_payments_a_tag.$recurring_payments_total_tax.' '.$currency_code.'</a></td>').'
'.(in_array('shipping_costs', $undisplayed_columns) ? '' : '<td>'.$recurring_payments_a_tag.$recurring_payments_total_shipping_cost.' '.$currency_code.'</a></td>').'
'.(in_array('transaction_costs', $undisplayed_columns) ? '' : '<td>'.$recurring_payments_a_tag.$recurring_payments_total_transaction_cost.' '.$currency_code.'</a></td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$recurring_payments_a_tag.$recurring_payments_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('received_recurring_payments', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['received_recurring_payments']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$received_recurring_payments_a_tag.$received_recurring_payments_number.'</a></td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('net_prices', $undisplayed_columns) ? '' : '<td>'.$received_recurring_payments_a_tag.$received_recurring_payments_total_net_price.' '.$currency_code.'</a></td>').'
'.(in_array('taxes', $undisplayed_columns) ? '' : '<td>'.$received_recurring_payments_a_tag.$received_recurring_payments_total_tax.' '.$currency_code.'</a></td>').'
'.(in_array('shipping_costs', $undisplayed_columns) ? '' : '<td>'.$received_recurring_payments_a_tag.$received_recurring_payments_total_shipping_cost.' '.$currency_code.'</a></td>').'
'.(in_array('transaction_costs', $undisplayed_columns) ? '' : '<td>'.$received_recurring_payments_a_tag.$received_recurring_payments_total_transaction_cost.' '.$currency_code.'</a></td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$received_recurring_payments_a_tag.$received_recurring_payments_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('refunded_recurring_payments', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['refunded_recurring_payments']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$refunded_recurring_payments_a_tag.$refunded_recurring_payments_number.'</a></td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('net_prices', $undisplayed_columns) ? '' : '<td>'.$refunded_recurring_payments_a_tag.$refunded_recurring_payments_total_net_price.' '.$currency_code.'</a></td>').'
'.(in_array('taxes', $undisplayed_columns) ? '' : '<td>'.$refunded_recurring_payments_a_tag.$refunded_recurring_payments_total_tax.' '.$currency_code.'</a></td>').'
'.(in_array('shipping_costs', $undisplayed_columns) ? '' : '<td>'.$refunded_recurring_payments_a_tag.$refunded_recurring_payments_total_shipping_cost.' '.$currency_code.'</a></td>').'
'.(in_array('transaction_costs', $undisplayed_columns) ? '' : '<td>'.$refunded_recurring_payments_a_tag.$refunded_recurring_payments_total_transaction_cost.' '.$currency_code.'</a></td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>'.$refunded_recurring_payments_a_tag.$refunded_recurring_payments_total_amount.' '.$currency_code.'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('products', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['products']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$products_a_tag.$products_number.'</a></td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('net_prices', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('taxes', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('shipping_costs', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('transaction_costs', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>--</td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('products_categories', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['products_categories']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$products_categories_a_tag.$products_categories_number.'</a></td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('net_prices', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('taxes', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('shipping_costs', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('transaction_costs', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>--</td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('sold_items', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['sold_items']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$products_a_tag.$sold_items_number.'</a></td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('net_prices', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('taxes', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('shipping_costs', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('transaction_costs', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>--</td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('clients', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['clients']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$clients_a_tag.$clients_number.'</a></td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('net_prices', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('taxes', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('shipping_costs', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('transaction_costs', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>--</td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('active_clients', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['active_clients']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$active_clients_a_tag.$active_clients_number.'</a></td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('net_prices', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('taxes', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('shipping_costs', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('transaction_costs', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>--</td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('inactive_clients', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['inactive_clients']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$inactive_clients_a_tag.$inactive_clients_number.'</a></td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('net_prices', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('taxes', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('shipping_costs', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('transaction_costs', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>--</td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('deactivated_clients', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['deactivated_clients']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$deactivated_clients_a_tag.$deactivated_clients_number.'</a></td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('net_prices', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('taxes', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('shipping_costs', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('transaction_costs', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>--</td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('clients_categories', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['clients_categories']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$clients_categories_a_tag.$clients_categories_number.'</a></td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('net_prices', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('taxes', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('shipping_costs', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('transaction_costs', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>--</td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('forms', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['forms']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$forms_a_tag.$forms_number.'</a></td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('net_prices', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('taxes', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('shipping_costs', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('transaction_costs', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>--</td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('forms_categories', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['forms_categories']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$forms_categories_a_tag.$forms_categories_number.'</a></td>').'
'.(in_array('orders_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('net_prices', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('taxes', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('shipping_costs', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('transaction_costs', $undisplayed_columns) ? '' : '<td>--</td>').'
'.(in_array('total_amount', $undisplayed_columns) ? '' : '<td>--</td>').'
</tr>'; $boolean = !$boolean; }
echo '</tbody></table>'; } ?>
<div style="text-align: center;">
<?php for ($i = 0; $i < $max_tables; $i++) {
echo '<label>'.__('Table', 'commerce-manager').' '.($i + 1).' <select name="table'.$i.'" id="table'.$i.'">';
foreach ($tables_names as $key => $value) { echo '<option value="'.$key.'"'.($tables_slugs[$i] == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; }
echo '</select></label>
<label><input type="checkbox" name="table'.$i.'_displayed" id="table'.$i.'_displayed" value="yes"'.(!in_array($i, $displayed_tables) ? '' : ' checked="checked"').' /> '.__('Display', 'commerce-manager').'</label><br />'; } ?><br />
<input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" />
</div>
<?php $tables_displayed = array();
foreach ($displayed_tables as $key => $value) {
if (in_array($tables_slugs[$value], $tables_displayed)) { unset($displayed_tables[$key]); }
$tables_displayed[] = $tables_slugs[$value]; }
if (count($displayed_tables) > 1) {
for ($i = 0; $i < $max_tables; $i++) {
if (in_array($i, $displayed_tables)) { $summary .= '<li> | <a href="#'.str_replace('_', '-', $tables_slugs[$i]).'">'.$tables_names[$tables_slugs[$i]].'</a></li>'; } }
$summary = '<ul class="subsubsub" style="float: none; text-align: center;">
<li>'.substr($summary, 7).'</ul>'; }
for ($i = 0; $i < $max_tables; $i++) {
if (in_array($i, $displayed_tables)) {
$table_slug = $tables_slugs[$i];
$table_name = table_name($table_slug);
$options = get_option('commerce_manager_'.$table_slug);
$columns = (array) $options['columns'];
$max_columns = count($columns);
$displayed_columns = (array) $options['displayed_columns'];
for ($j = 0; $j < $max_columns; $j++) { if (in_array($j, $displayed_columns)) { $table_ths .= table_th($table_slug, $columns[$j]); } }
echo $summary.'
<h3 id="'.str_replace('_', '-', $tables_slugs[$i]).'"><strong>'.$tables_names[$tables_slugs[$i]].'</strong></h3>
<div style="overflow: auto;">
<table class="wp-list-table widefat" style="margin: 1em 0 2em 0;">
<thead><tr>'.$table_ths.'</tr></thead>
<tfoot><tr>'.$table_ths.'</tr></tfoot>
<tbody>';
$items = $wpdb->get_results("SELECT * FROM $table_name WHERE $date_criteria $selection_criteria $filter_criteria ORDER BY date DESC", OBJECT);
if ($items) { foreach ($items as $item) {
$first = true; for ($j = 0; $j < $max_columns; $j++) {
if (in_array($j, $displayed_columns)) {
$table_tds .= '<td'.($first ? ' style="height: 6em;"' : '').'>'.table_td($table_slug, $columns[$j], $item).($first ? row_actions($table_slug, $item) : '').'</td>';
$first = false; } }
echo '<tr'.($boolean ? '' : ' class="alternate"').'>'.$table_tds.'</tr>';
$table_tds = ''; $boolean = !$boolean; } }
else { echo '<tr class="no-items"><td class="colspanchange" colspan="'.count($displayed_columns).'">'.no_items($table_slug).'</td></tr>'; }
echo '</tbody></table></div>';
$table_ths = ''; } } ?>
</form>
</div>
</div>