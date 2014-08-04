<?php if ((isset($_GET['start_date'])) && (isset($_GET['end_date']))) {
$file = 'wp-load.php'; $i = 0;
while ((!file_exists($file)) && ($i < 8)) { $file = '../'.$file; $i = $i + 1; }
include_once $file;
global $wpdb;
$start_date = trim(mysql_real_escape_string(strip_tags(str_replace('_', ' ', $_GET['start_date']))));
if (strlen($start_date) == 10) { $start_date .= ' 00:00:00'; }
$end_date = trim(mysql_real_escape_string(strip_tags(str_replace('_', ' ', $_GET['end_date']))));
if (strlen($end_date) == 10) { $end_date .= ' 23:59:59'; }
$date_criteria = "AND (date BETWEEN '$start_date' AND '$end_date')";
$type = (((isset($_GET['type'])) && ($_GET['type'] == 'recurring-payments')) ? $_GET['type'] : 'orders');
$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."commerce_manager_".str_replace('-', '_', $type)." WHERE $date_criteria ORDER BY date ASC", OBJECT);

if ($type == 'orders') {
$file = "		Commandes du ".substr($start_date, 8, 2)."/".substr($start_date, 5, 2)."/".substr($start_date, 0, 4)." au ".substr($end_date, 8, 2)."/".substr($end_date, 5, 2)."/".substr($end_date, 0, 4)."		\n\n";
$file .= "		Date	Prenom	Nom	Numero de facture	Numero de transaction	Montant HT	Montant HT port	TVA	Montant TTC\n";
foreach ($items as $item) {
$file .= "		".$item->date." 	".$item->first_name."	".$item->last_name."	A".$item->id." 	".$item->transaction_number." 	".$item->net_amount." EUR 	".$item->shipping_cost." EUR 	".$item->tax." EUR 	".$item->amount." EUR\n"; } }

else {
$file = "		Paiements recurrents du ".substr($start_date, 8, 2)."/".substr($start_date, 5, 2)."/".substr($start_date, 0, 4)." au ".substr($end_date, 8, 2)."/".substr($end_date, 5, 2)."/".substr($end_date, 0, 4)."		\n\n";
$file .= "		Date	Prenom	Nom	Numero de facture	Numero de transaction	Montant HT	Montant HT port	TVA	Montant TTC\n";
foreach ($items as $item) {
$order = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_orders WHERE id = ".$item->order_id, OBJECT);
$file .= "		".$item->date." 	".$order->first_name."	".$order->last_name."	A".$order->id."-".$item->id." 	".$item->transaction_number." 	".$item->net_amount." EUR 	".$item->shipping_cost." EUR 	".$item->tax." EUR 	".$item->amount." EUR\n"; } }

header("Content-type: application/vnd.ms-excel");
header("Content-disposition: attachment; filename=".$type.".csv");//Remplacer .csv par .xls pour exporter en .XLS
print $file;
exit; }
elseif (!headers_sent()) { header('Location: /'); exit(); }