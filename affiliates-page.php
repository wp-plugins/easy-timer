<?php if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
$limit = (int) $_POST['limit'];
if ($limit > 1000) { $limit = 1000; }
if ($limit < 1) { unset($limit); } else { update_option('affiliation_manager_affiliates_page_display_limit', $limit); } }

if (!isset($limit)) { $limit = get_option('affiliation_manager_affiliates_page_display_limit'); }

global $wpdb;
$affiliates_table_name = $wpdb->prefix.'affiliation_manager_affiliates';
$query = $wpdb->get_row("SELECT count(*) as total FROM $affiliates_table_name", OBJECT);
$n = (int) $query->total;
if (!isset($_GET['orderby'])) { $_GET['orderby'] = 'id'; }
if (!isset($_GET['order'])) { $_GET['order'] = 'desc'; }
$_GET['paged'] = (int) $_REQUEST['paged'];
if ($_GET['paged'] < 1) { $_GET['paged'] = 1; }
$max_paged = ceil($n/$limit); if ($max_paged < 1) { $max_paged = 1; }
if ($_GET['paged'] > $max_paged) { $_GET['paged'] = $max_paged; }
$start = ($_GET['paged'] - 1)*$limit;
$affiliates = $wpdb->get_results("SELECT * FROM $affiliates_table_name ORDER BY ".$_GET['orderby']." ".strtoupper($_GET['order'])." LIMIT $start, $limit", OBJECT);

function tablenav_pages($n, $max_paged, $location) {
if ($_GET['paged'] == 1) { $prev_paged = 1; } else { $prev_paged = $_GET['paged'] - 1; }
if ($_GET['paged'] == $max_paged) { $next_paged = $max_paged; } else { $next_paged = $_GET['paged'] + 1; }
echo '<div class="tablenav-pages"><span class="displaying-num">'.$n.' '.($n <= 1 ? __('affiliate', 'affiliation-manager') : __('affiliates', 'affiliation-manager')).'</span>
<a class="first-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the first page').'" href="admin.php?page='.$_GET['page'].'&amp;orderby='.$_GET['orderby'].'&amp;order='.$_GET['order'].'&amp;paged=1">&laquo;</a>
<a class="prev-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the previous page').'" href="admin.php?page='.$_GET['page'].'&amp;orderby='.$_GET['orderby'].'&amp;order='.$_GET['order'].'&amp;paged='.$prev_paged.'">&lsaquo;</a>
<span class="paging-input">'.($location == 'top' ? '<input class="current-page" title="'.__('Current page').'" type="text" name="paged" value="'.$_GET['paged'].'" size="2" />' : $_GET['paged']).' '.__('of').' <span class="total-pages">'.$max_paged.'</span></span>
<a class="next-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the next page').'" href="admin.php?page='.$_GET['page'].'&amp;orderby='.$_GET['orderby'].'&amp;order='.$_GET['order'].'&amp;paged='.$next_paged.'">&rsaquo;</a>
<a class="last-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the last page').'" href="admin.php?page='.$_GET['page'].'&amp;orderby='.$_GET['orderby'].'&amp;order='.$_GET['order'].'&amp;paged='.$max_paged.'">&raquo;</a></div>
<div class="clear"></div>'; }

$table_columns = '<tr>
<th scope="col" class="manage-column '.($_GET['orderby'] == 'id' ? 'sorted '.$_GET['order'] : 'sortable desc').'" style="width: 5%;">
<a href="admin.php?page='.$_GET['page'].'&amp;orderby=id&amp;order='.(($_GET['orderby'] == 'id' && $_GET['order'] == 'asc') ? 'desc' : 'asc').'">
<span>'.__('ID', 'affiliation-manager').'</span><span class="sorting-indicator"></span></a></th>
<th scope="col" class="manage-column '.($_GET['orderby'] == 'login' ? 'sorted '.$_GET['order'] : 'sortable desc').'" style="width: 12%;">
<a href="admin.php?page='.$_GET['page'].'&amp;orderby=login&amp;order='.(($_GET['orderby'] == 'login' && $_GET['order'] == 'asc') ? 'desc' : 'asc').'">
<span>'.__('Login name', 'affiliation-manager').'</span><span class="sorting-indicator"></span></a></th>
<th scope="col" class="manage-column '.($_GET['orderby'] == 'first_name' ? 'sorted '.$_GET['order'] : 'sortable desc').'" style="width: 12%;">
<a href="admin.php?page='.$_GET['page'].'&amp;orderby=first_name&amp;order='.(($_GET['orderby'] == 'first_name' && $_GET['order'] == 'asc') ? 'desc' : 'asc').'">
<span>'.__('First name', 'affiliation-manager').'</span><span class="sorting-indicator"></span></a></th>
<th scope="col" class="manage-column '.($_GET['orderby'] == 'last_name' ? 'sorted '.$_GET['order'] : 'sortable desc').'" style="width: 12%;">
<a href="admin.php?page='.$_GET['page'].'&amp;orderby=last_name&amp;order='.(($_GET['orderby'] == 'last_name' && $_GET['order'] == 'asc') ? 'desc' : 'asc').'">
<span>'.__('Last name', 'affiliation-manager').'</span><span class="sorting-indicator"></span></a></th>
<th scope="col" class="manage-column '.($_GET['orderby'] == 'email_address' ? 'sorted '.$_GET['order'] : 'sortable desc').'" style="width: 15%;">
<a href="admin.php?page='.$_GET['page'].'&amp;orderby=email_address&amp;order='.(($_GET['orderby'] == 'email_address' && $_GET['order'] == 'asc') ? 'desc' : 'asc').'">
<span>'.__('Email address', 'affiliation-manager').'</span><span class="sorting-indicator"></span></a></th>
<th scope="col" class="manage-column '.($_GET['orderby'] == 'website_name' ? 'sorted '.$_GET['order'] : 'sortable desc').'" style="width: 15%;">
<a href="admin.php?page='.$_GET['page'].'&amp;orderby=website_name&amp;order='.(($_GET['orderby'] == 'website_name' && $_GET['order'] == 'asc') ? 'desc' : 'asc').'">
<span>'.__('Website', 'affiliation-manager').'</span><span class="sorting-indicator"></span></a></th>
<th scope="col" class="manage-column '.($_GET['orderby'] == 'date' ? 'sorted '.$_GET['order'] : 'sortable desc').'" style="width: 17%;">
<a href="admin.php?page='.$_GET['page'].'&amp;orderby=date&amp;order='.(($_GET['orderby'] == 'date' && $_GET['order'] == 'asc') ? 'desc' : 'asc').'">
<span>'.__('Registration date', 'affiliation-manager').'</span><span class="sorting-indicator"></span></a></th>
<th scope="col" class="manage-column '.($_GET['orderby'] == 'referrer' ? 'sorted '.$_GET['order'] : 'sortable desc').'" style="width: 12%;">
<a href="admin.php?page='.$_GET['page'].'&amp;orderby=referrer&amp;order='.(($_GET['orderby'] == 'referrer' && $_GET['order'] == 'asc') ? 'desc' : 'asc').'">
<span>'.__('Referrer', 'affiliation-manager').'</span><span class="sorting-indicator"></span></a></th>
</tr>'; ?>

<div class="wrap">
<div id="poststuff">
<?php affiliation_manager_pages_top(); ?>
<?php affiliation_manager_pages_menu(); ?>
<form method="post" action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="tablenav top">
<div class="alignleft actions">
<?php _e('Display', 'affiliation-manager'); ?> <input style="text-align: center;" type="text" name="limit" size="2" value="<?php echo $limit; ?>" /> 
<?php _e('results per page', 'affiliation-manager'); ?> 
<input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" />
</div><?php tablenav_pages($n, $max_paged, 'top'); ?></div>
<table class="wp-list-table widefat fixed" cellspacing="0">
<thead><?php echo $table_columns; ?></thead>
<tfoot><?php echo $table_columns; ?></tfoot>
<tbody id="the-list">
<?php if ($affiliates) { foreach ($affiliates as $affiliate) {
echo '<tr'.($i ? '' : ' class="alternate"').'>
<td>'.$affiliate->id.'</td>
<td style="height: 4em;">'.$affiliate->login.'<br />
<div class="row-actions" style="position: absolute; width: 800%;"><span class="edit">
<a href="admin.php?page=affiliation-manager-affiliate&amp;id='.$affiliate->id.'">'.__('Edit').'</a></span> | <span class="delete">
<a href="admin.php?page=affiliation-manager-affiliate&amp;id='.$affiliate->id.'&amp;action=delete">'.__('Delete').'</a></span> | <span class="view">
<a href="admin.php?page=affiliation-manager-statistics&amp;id='.$affiliate->id.'">'.__('Statistics', 'affiliation-manager').'</a></span></div></td>
<td>'.$affiliate->first_name.'</td>
<td>'.$affiliate->last_name.'</td>
<td><a href="mailto:'.$affiliate->email_address.'">'.$affiliate->email_address.'</a></td>
<td>'.($affiliate->website_url != '' ? '<a href="'.$affiliate->website_url.'">'.($affiliate->website_name != '' ? $affiliate->website_name : $affiliate->website_url).'</a>' : '').'</td>
<td>'.$affiliate->date.'</td>
<td>'.($affiliate->referrer != '' ? '<a href="admin.php?page=affiliation-manager-affiliate&amp;login='.$affiliate->referrer.'">'.$affiliate->referrer.'</a><br />
<div class="row-actions" style="position: absolute;"><span class="view"><a href="admin.php?page=affiliation-manager-statistics&amp;login='.$affiliate->referrer.'">'.__('Statistics', 'affiliation-manager').'</a></span></div>' : '').'</td>
</tr>'; $i = !$i; } }
else { echo '<tr class="no-items"><td class="colspanchange" colspan="8">'.__('No affiliates', 'affiliation-manager').'</td></tr>'; } ?>
</tbody>
</table>
<div class="tablenav bottom"><?php tablenav_pages($n, $max_paged, 'bottom'); ?></div>
</form>
</div>
</div>