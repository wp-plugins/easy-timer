<?php wp_enqueue_script('dashboard');

function affiliation_manager_options_page() { include 'options-page.php'; }
function affiliation_manager_affiliate_page() { include 'affiliate-page.php'; }
function affiliation_manager_affiliates_page() { include 'affiliates-page.php'; }
function affiliation_manager_clicks_page() { include 'clicks-page.php'; }
function affiliation_manager_commissions_page() { include 'commissions-page.php'; }
function affiliation_manager_statistics_page() { include 'statistics-page.php'; }

function affiliation_manager_admin_menu() {
add_menu_page('Affiliation Manager', __('Affiliation', 'affiliation-manager'), 'manage_options', 'affiliation-manager', 'affiliation_manager_options_page', '', 102);
add_submenu_page('affiliation-manager', 'Affiliation Manager ('.__('Options', 'affiliation-manager').')', __('Options', 'affiliation-manager'), 'manage_options', 'affiliation-manager', 'affiliation_manager_options_page');
add_submenu_page('affiliation-manager', 'Affiliation Manager ('.__('Affiliate', 'affiliation-manager').')', __('Add Affiliate', 'affiliation-manager'), 'manage_options', 'affiliation-manager-affiliate', 'affiliation_manager_affiliate_page');
add_submenu_page('affiliation-manager', 'Affiliation Manager ('.__('Affiliates', 'affiliation-manager').')', __('Affiliates', 'affiliation-manager'), 'manage_options', 'affiliation-manager-affiliates', 'affiliation_manager_affiliates_page');
add_submenu_page('affiliation-manager', 'Affiliation Manager ('.__('Clicks', 'affiliation-manager').')', __('Clicks', 'affiliation-manager'), 'manage_options', 'affiliation-manager-clicks', 'affiliation_manager_clicks_page');
add_submenu_page('affiliation-manager', 'Affiliation Manager ('.__('Commissions', 'affiliation-manager').')', __('Commissions', 'affiliation-manager'), 'manage_options', 'affiliation-manager-commissions', 'affiliation_manager_commissions_page');
add_submenu_page('affiliation-manager', 'Affiliation Manager ('.__('Statistics', 'affiliation-manager').')', __('Statistics', 'affiliation-manager'), 'manage_options', 'affiliation-manager-statistics', 'affiliation_manager_statistics_page'); }

add_action('admin_menu', 'affiliation_manager_admin_menu');


function affiliation_manager_pages_menu() { ?>
<ul class="subsubsub" style="margin: 0 0 1em; float: none;">
<li><a href="admin.php?page=affiliation-manager"<?php if ($_GET['page'] == 'affiliation-manager') { echo ' class="current"'; } ?>><?php _e('Options', 'affiliation-manager'); ?></a></li>
<li>| <a href="admin.php?page=affiliation-manager-affiliate"<?php if (($_GET['page'] == 'affiliation-manager-affiliate') && (!isset($_GET['id']))) { echo ' class="current"'; } ?>><?php _e('Add Affiliate', 'affiliation-manager'); ?></a></li>
<li>| <a href="admin.php?page=affiliation-manager-affiliates"<?php if ($_GET['page'] == 'affiliation-manager-affiliates') { echo ' class="current"'; } ?>><?php _e('Affiliates', 'affiliation-manager'); ?></a></li>
<li>| <a href="admin.php?page=affiliation-manager-clicks"<?php if (($_GET['page'] == 'affiliation-manager-clicks') && (!isset($_GET['id']))) { echo ' class="current"'; } ?>><?php _e('Clicks', 'affiliation-manager'); ?></a></li>
<li>| <a href="admin.php?page=affiliation-manager-commissions"<?php if ($_GET['page'] == 'affiliation-manager-commissions') { echo ' class="current"'; } ?>><?php _e('Commissions', 'affiliation-manager'); ?></a></li>
<li>| <a href="admin.php?page=affiliation-manager-statistics"<?php if ($_GET['page'] == 'affiliation-manager-statistics') { echo ' class="current"'; } ?>><?php _e('Statistics', 'affiliation-manager'); ?></a></li>
</ul>
<?php }


function affiliation_manager_pages_top() { ?>
<h2 style="float: left;">Affiliation Manager</h2>
<ul class="subsubsub" style="margin: 2.5em 0 0 6em; float: left;">
<li><a href="http://www.kleor-editions.com/affiliation-manager/documentation"><?php _e('Documentation', 'affiliation-manager'); ?></a></li>
<?php if (function_exists('commerce_manager_admin_menu')) { echo '<li>| <a href="admin.php?page=commerce-manager'.
(strstr($_GET['page'], 'commissions') ? '-orders' : '').(strstr($_GET['page'], 'statistics') ? '-statistics' : '').'">Commerce Manager</a></li>'; } ?>
</ul>
<div class="clear"></div>
<?php }


function affiliation_manager_row_meta($links, $file) {
if ($file == 'affiliation-manager/affiliation-manager.php') {
return array_merge($links, array(
'<a href="admin.php?page=affiliation-manager">'.__('Options', 'affiliation-manager').'</a>',
'<a href="http://www.kleor-editions.com/affiliation-manager/documentation">'.__('Documentation', 'affiliation-manager').'</a>')); }
return $links; }

add_filter('plugin_row_meta', 'affiliation_manager_row_meta', 10, 2);


if (($_GET['page'] == 'affiliation-manager-affiliate') || ($_GET['page'] == 'affiliation-manager-statistics')) {
add_action('admin_head', 'affiliation_date_picker_css'); }