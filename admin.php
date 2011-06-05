<?php wp_enqueue_script('dashboard');

function commerce_manager_options_page() { include 'options-page.php'; }
function commerce_manager_product_page() { include 'product-page.php'; }
function commerce_manager_order_page() { include 'order-page.php'; }
function commerce_manager_statistics_page() { include 'statistics-page.php'; }
function commerce_manager_table_page() { include 'table-page.php'; }

function commerce_manager_admin_menu() {
add_menu_page('Commerce Manager', __('Commerce', 'commerce-manager'), 'manage_options', 'commerce-manager', 'commerce_manager_options_page', '', 101);
add_submenu_page('commerce-manager', 'Commerce Manager ('.__('Options', 'commerce-manager').')', __('Options', 'commerce-manager'), 'manage_options', 'commerce-manager', 'commerce_manager_options_page');
add_submenu_page('commerce-manager', 'Commerce Manager ('.__('Product', 'commerce-manager').')', __('Add Product', 'commerce-manager'), 'manage_options', 'commerce-manager-product', 'commerce_manager_product_page');
add_submenu_page('commerce-manager', 'Commerce Manager ('.__('Products', 'commerce-manager').')', __('Products', 'commerce-manager'), 'manage_options', 'commerce-manager-products', 'commerce_manager_table_page');
add_submenu_page('commerce-manager', 'Commerce Manager ('.__('Order', 'commerce-manager').')', __('Add Order', 'commerce-manager'), 'manage_options', 'commerce-manager-order', 'commerce_manager_order_page');
add_submenu_page('commerce-manager', 'Commerce Manager ('.__('Orders', 'commerce-manager').')', __('Orders', 'commerce-manager'), 'manage_options', 'commerce-manager-orders', 'commerce_manager_table_page');
add_submenu_page('commerce-manager', 'Commerce Manager ('.__('Statistics', 'commerce-manager').')', __('Statistics', 'commerce-manager'), 'manage_options', 'commerce-manager-statistics', 'commerce_manager_statistics_page'); }

add_action('admin_menu', 'commerce_manager_admin_menu');


function commerce_manager_pages_menu() { ?>
<ul class="subsubsub" style="margin: 0 0 1em; float: left; white-space: normal;">
<li><a href="admin.php?page=commerce-manager"<?php if ($_GET['page'] == 'commerce-manager') { echo ' class="current"'; } ?>><?php _e('Options', 'commerce-manager'); ?></a></li>
<li>| <a href="admin.php?page=commerce-manager-product"<?php if (($_GET['page'] == 'commerce-manager-product') && (!isset($_GET['id']))) { echo ' class="current"'; } ?>><?php _e('Add Product', 'commerce-manager'); ?></a></li>
<li>| <a href="admin.php?page=commerce-manager-products"<?php if ($_GET['page'] == 'commerce-manager-products') { echo ' class="current"'; } ?>><?php _e('Products', 'commerce-manager'); ?></a></li>
<li>| <a href="admin.php?page=commerce-manager-order"<?php if (($_GET['page'] == 'commerce-manager-order') && (!isset($_GET['id']))) { echo ' class="current"'; } ?>><?php _e('Add Order', 'commerce-manager'); ?></a></li>
<li>| <a href="admin.php?page=commerce-manager-orders"<?php if ($_GET['page'] == 'commerce-manager-orders') { echo ' class="current"'; } ?>><?php _e('Orders', 'commerce-manager'); ?></a></li>
<li>| <a href="admin.php?page=commerce-manager-statistics"<?php if ($_GET['page'] == 'commerce-manager-statistics') { echo ' class="current"'; } ?>><?php _e('Statistics', 'commerce-manager'); ?></a></li>
</ul>
<?php }


function commerce_manager_pages_top() { ?>
<h2 style="float: left;">Commerce Manager</h2>
<ul class="subsubsub" style="margin: 2.5em 0 0 6em; float: left; white-space: normal;">
<li><a href="http://www.kleor-editions.com/commerce-manager/documentation"><?php _e('Documentation', 'commerce-manager'); ?></a></li>
<?php if (function_exists('affiliation_manager_admin_menu')) { echo '<li>| <a href="admin.php?page=affiliation-manager'.
(strstr($_GET['page'], 'orders') ? '-commissions' : '').(strstr($_GET['page'], 'statistics') ? '-statistics' : '').'">Affiliation Manager</a></li>'; }
else { echo '<li>| <a href="http://www.kleor-editions.com/affiliation-manager">Affiliation Manager</a></li>'; } ?>
</ul>
<div class="clear"></div>
<?php }


function commerce_manager_action_links($links, $file) {
if ($file == 'commerce-manager/commerce-manager.php') {
return array_merge($links, array(
'<a href="admin.php?page=commerce-manager">'.__('Options', 'commerce-manager').'</a>')); }
return $links; }

add_filter('plugin_action_links', 'commerce_manager_action_links', 10, 2);


function commerce_manager_row_meta($links, $file) {
if ($file == 'commerce-manager/commerce-manager.php') {
return array_merge($links, array(
'<a href="http://www.kleor-editions.com/commerce-manager/documentation">'.__('Documentation', 'commerce-manager').'</a>')); }
return $links; }

add_filter('plugin_row_meta', 'commerce_manager_row_meta', 10, 2);


function install_commerce_manager() {
global $wpdb;
include 'initial-options.php';
foreach ($initial_options as $key => $value) {
$_key = ($key == '' ? '' : '_'.$key);
if (is_array($value)) {
$options = get_option('commerce_manager'.$_key);
foreach ($value as $option => $initial_value) {
if ($options[$option] == '') { $options[$option] = $initial_value; } }
update_option('commerce_manager'.$_key, $options); }
else { add_option('commerce_manager'.$_key, $value); } }

include_once ABSPATH.'wp-admin/includes/upgrade.php';
if (!empty($wpdb->charset)) { $charset_collate = 'DEFAULT CHARACTER SET '.$wpdb->charset; }
if (!empty($wpdb->collate)) { $charset_collate .= ' COLLATE '.$wpdb->collate; }
include 'tables.php';
foreach ($tables as $table_slug => $table) {
unset($list); foreach ($table as $key => $value) { $list .= "
".$key." ".$value['type']." ".($key == 'id' ? 'auto_increment' : 'NOT NULL').","; }
$sql = "CREATE TABLE ".$wpdb->prefix.'commerce_manager_'.$table_slug." (".$list."
PRIMARY KEY  (id)) $charset_collate;"; dbDelta($sql); } }

register_activation_hook('commerce-manager/commerce-manager.php', 'install_commerce_manager');


if (($_GET['page'] == 'commerce-manager-order') || ($_GET['page'] == 'commerce-manager-product') || ($_GET['page'] == 'commerce-manager-statistics')) {
add_action('admin_head', 'commerce_date_picker_css'); }