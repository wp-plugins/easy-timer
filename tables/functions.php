<?php function table_td($column, $item) {
$item->$column = affiliation_format_data($column, $item->$column);
$item->$column = htmlspecialchars($item->$column);
switch ($column) {
case 'commission_payment': if ($item->$column == 'deferred') { $table_td = '<a href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=deferred">'.__('Deferred', 'affiliation-manager').'</a>'; }
elseif ($item->$column == 'instant') { $table_td = '<a href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=instant">'.__('Instant', 'affiliation-manager').'</a>'; } break;
case 'commission_payment_date': case 'commission_payment_date_utc': case 'date': case 'date_utc': case 'refund_date': case 'refund_date_utc': $table_td = ($item->$column == '0000-00-00 00:00:00' ? '' : $item->$column); break;
case 'commission_status': if ($item->$column == 'paid') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=paid">'.__('Paid', 'affiliation-manager').'</a>'; }
elseif ($item->$column == 'unpaid') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=unpaid">'.__('Unpaid', 'affiliation-manager').'</a>'; } break;
case 'email_address': case 'paypal_email_address': $table_td = '<a href="mailto:'.$item->$column.'">'.$item->$column.'</a>'; break;
case 'ip_address': case 'product_id': case 'referrer': $table_td = ($item->$column == '' ? '' : '<a href="admin.php?page='.$_GET['page'].'&amp;'.$column.'='.$item->$column.'">'.$item->$column.'</a>'); break;
case 'login': $table_td = '<a href="admin.php?page=affiliation-manager-affiliate&amp;id='.$item->id.'">'.$item->$column.'</a>'; break;
case 'referring_url': case 'url': case 'website_url': $table_td = ($item->$column == '' ? '' : '<a href="'.$item->$column.'">'.($item->$column == HOME_URL ? '/' : str_replace(HOME_URL, '', $item->$column)).'</a>'); break;
case 'status': if ($item->$column == 'processed') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=processed">'.__('Processed', 'affiliation-manager').'</a>'; }
elseif ($item->$column == 'unprocessed') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=unprocessed">'.__('Unprocessed', 'affiliation-manager').'</a>'; }
elseif ($item->$column == 'refunded') { $table_td = '<a style="color: #c00000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=refunded">'.__('Refunded', 'affiliation-manager').'</a>'; } break;
case 'website_name': $table_td = ($item->website_url == '' ? $item->website_name : '<a href="'.$item->website_url.'">'.($item->website_name == '' ? str_replace(HOME_URL, '', $item->website_url) : $item->website_name).'</a>'); break;
default: $table_td = $item->$column; }
return $table_td; }

function table_th($column) {
$columns_names = $_GET['columns_names'];
$columns_widths = $_GET['columns_widths'];
if (strstr($_GET['page'], 'statistics')) { $table_th = '<th scope="col" class="manage-column" style="width: '.$columns_widths[$column].'%;">'.$columns_names[$column].'</th>'; }
else {
$table_th = '<th scope="col" class="manage-column '.($_GET['orderby'] == $column ? 'sorted '.$_GET['order'] : 'sortable desc').'" style="width: '.$columns_widths[$column].'%;">
<a href="admin.php?page='.$_GET['page'].'&amp;orderby='.$column.'&amp;order='.(($_GET['orderby'] == $column && $_GET['order'] == 'asc') ? 'desc' : 'asc').
($_GET['commission_payment'] == '' ? '' : '&amp;commission_payment='.$_GET['commission_payment']).
($_GET['commission_status'] == '' ? '' : '&amp;commission_status='.$_GET['commission_status']).
($_GET['commission_type'] == '' ? '' : '&amp;commission_type='.$_GET['commission_type']).
($_GET['product_id'] == '' ? '' : '&amp;product_id='.$_GET['product_id']).
($_GET['ip_address'] == '' ? '' : '&amp;ip_address='.$_GET['ip_address']).
($_GET['referrer'] == '' ? '' : '&amp;referrer='.$_GET['referrer']).
($_GET['status'] == '' ? '' : '&amp;status='.$_GET['status']).
($_GET['s'] == '' ? '' : '&amp;s='.$_GET['s']).'">
<span>'.$columns_names[$column].'</span><span class="sorting-indicator"></span></a></th>'; }
return $table_th; }

function tablenav_pages($n, $max_paged, $location) {
if ($_GET['paged'] == 1) { $prev_paged = 1; } else { $prev_paged = $_GET['paged'] - 1; }
if ($_GET['paged'] == $max_paged) { $next_paged = $max_paged; } else { $next_paged = $_GET['paged'] + 1; }
$url = 'admin.php?page='.$_GET['page'].'&amp;orderby='.$_GET['orderby'].'&amp;order='.$_GET['order'].
($_GET['commission_payment'] == '' ? '' : '&amp;commission_payment='.$_GET['commission_payment']).
($_GET['commission_status'] == '' ? '' : '&amp;commission_status='.$_GET['commission_status']).
($_GET['commission_type'] == '' ? '' : '&amp;commission_type='.$_GET['commission_type']).
($_GET['ip_address'] == '' ? '' : '&amp;ip_address='.$_GET['ip_address']).
($_GET['product_id'] == '' ? '' : '&amp;product_id='.$_GET['product_id']).
($_GET['referrer'] == '' ? '' : '&amp;referrer='.$_GET['referrer']).
($_GET['status'] == '' ? '' : '&amp;status='.$_GET['status']).
($_GET['s'] == '' ? '' : '&amp;s='.$_GET['s']);
echo '<div class="tablenav-pages"><span class="displaying-num">'.$n.' '.($n <= 1 ? $_GET['singular'] : $_GET['plural']).'</span>
<a class="first-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the first page').'" href="'.$url.'&amp;paged=1">&laquo;</a>
<a class="prev-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the previous page').'" href="'.$url.'&amp;paged='.$prev_paged.'">&lsaquo;</a>
<span class="paging-input">'.($location == 'top' ? '<input class="current-page" title="'.__('Current page').'" type="text" name="paged" id="paged" value="'.$_GET['paged'].'" size="2" />' : $_GET['paged']).' '.__('of').' <span class="total-pages">'.$max_paged.'</span></span>
<a class="next-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the next page').'" href="'.$url.'&amp;paged='.$next_paged.'">&rsaquo;</a>
<a class="last-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the last page').'" href="'.$url.'&amp;paged='.$max_paged.'">&raquo;</a></div>
<div class="clear"></div>'; }


remove_shortcode('affiliation-manager');
remove_shortcode('commerce-manager');
remove_shortcode('affiliate');
remove_shortcode('click');