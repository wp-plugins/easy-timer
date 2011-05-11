<?php function table_td($column, $item) {
$item->$column = str_replace(array("&apos;", '&quot;'), array("'", '"'), htmlspecialchars(do_shortcode($item->$column)));
switch ($column) {
case 'affiliation_enabled': case 'customers_subsribed_to_aweber_list': case 'downloadable': case 'email_sent_to_customer': case 'email_sent_to_seller': case 'registration_required':
if ($item->$column == 'yes') { $table_td = '<span style="color: #008000;">'.__('Yes', 'commerce-manager').'</span>'; }
else { $table_td = '<span style="color: #c00000;">'.__('No', 'commerce-manager').'</span>'; } break;
case 'available_quantity': if ($item->$column == 'unlimited') { $table_td = __('Unlimited', 'commerce-manager'); } else { $table_td = $item->$column; } break;
case 'commission_payment': if ($item->$column == 'deferred') { $table_td = '<a href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=deferred">'.__('Deferred', 'commerce-manager').'</a>'; }
elseif ($item->$column == 'instant') { $table_td = '<a href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=instant">'.__('Instant', 'commerce-manager').'</a>'; } break;
case 'commission_status': if ($item->$column == 'paid') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=paid">'.__('Paid', 'commerce-manager').'</a>'; }
elseif ($item->$column == 'unpaid') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=unpaid">'.__('Unpaid', 'commerce-manager').'</a>'; } break;
case 'commission_type': if ($item->$column == 'constant') { $table_td = '<a href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=constant">'.__('Constant', 'commerce-manager').'</a>'; }
elseif ($item->$column == 'proportional') { $table_td = '<a href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=proportional">'.__('Proportional', 'commerce-manager').'</a>'; } break;
case 'description': case 'email_to_customer_body': case 'email_to_seller_body': case 'instructions': if (strlen($item->$column) <= 80) { $table_td = $item->$column; }
else { $table_td = substr($item->$column, 0, 80); if (stristr($table_td, ' ')) { while (substr($table_td, -1) != ' ') { $table_td = substr($table_td, 0, -1); } } $table_td .= '[...]'; } break;
case 'download_url': case 'order_confirmation_url': case 'purchase_button_url': case 'referring_url': case 'thumbnail_url': case 'url': case 'website_url': $table_td = ($item->$column == '' ? '' : '<a href="'.$item->$column.'">'.$item->$column.'</a>'); break;
case 'email_address': case 'paypal_email_address': $table_td = '<a href="mailto:'.$item->$column.'">'.$item->$column.'</a>'; break;
case 'first_sale_winner': if ($item->$column == 'affiliate') { $table_td = __('Affiliate', 'commerce-manager'); }
elseif ($item->$column == 'affiliator') { $table_td = __('Affiliator', 'commerce-manager'); } break;
case 'ip_address': case 'product_id': case 'referrer': $table_td = ($item->$column == '' ? '' : '<a href="admin.php?page='.$_GET['page'].'&amp;'.$column.'='.$item->$column.'">'.$item->$column.'</a>'); break;
case 'name': $table_td = ($item->url == '' ? $item->name : '<a href="'.$item->url.'">'.($item->name == '' ? $item->url : $item->name).'</a>'); break;
case 'refunds_count': $table_td = ($item->$column == 0 ? 0 : '<a href="admin.php?page=commerce-manager-orders&amp;product_id='.$item->id.'&amp;status=refunded">'.$item->$column.'</a>'); break;
case 'sales_count': $table_td = ($item->$column == 0 ? 0 : '<a href="admin.php?page=commerce-manager-orders&amp;product_id='.$item->id.'">'.$item->$column.'</a>'); break;
case 'status': if ($item->$column == 'processed') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=processed">'.__('Processed', 'commerce-manager').'</a>'; }
elseif ($item->$column == 'unprocessed') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=unprocessed">'.__('Unprocessed', 'commerce-manager').'</a>'; }
elseif ($item->$column == 'refunded') { $table_td = '<a style="color: #c00000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=refunded">'.__('Refunded', 'commerce-manager').'</a>'; } break;
case 'website_name': $table_td = ($item->website_url == '' ? $item->website_name : '<a href="'.$item->website_url.'">'.($item->website_name == '' ? $item->website_url : $item->website_name).'</a>'); break;
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
($_GET['ip_address'] == '' ? '' : '&amp;ip_address='.$_GET['ip_address']).
($_GET['product_id'] == '' ? '' : '&amp;product_id='.$_GET['product_id']).
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


remove_shortcode('commerce-manager');
remove_shortcode('customer');
remove_shortcode('order');
remove_shortcode('product');