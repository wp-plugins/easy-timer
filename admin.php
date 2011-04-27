<?php wp_enqueue_script('dashboard');

function commerce_manager_options_page() { include 'options-page.php'; }
function commerce_manager_product_page() { include 'product-page.php'; }
function commerce_manager_products_page() { include 'products-page.php'; }
function commerce_manager_order_page() { include 'order-page.php'; }
function commerce_manager_orders_page() { include 'orders-page.php'; }
function commerce_manager_statistics_page() { include 'statistics-page.php'; }

function commerce_manager_admin_menu() {
add_menu_page('Commerce Manager', __('Commerce', 'commerce-manager'), 'manage_options', 'commerce-manager', 'commerce_manager_options_page', '', 101);
add_submenu_page('commerce-manager', 'Commerce Manager ('.__('Options', 'commerce-manager').')', __('Options', 'commerce-manager'), 'manage_options', 'commerce-manager', 'commerce_manager_options_page');
add_submenu_page('commerce-manager', 'Commerce Manager ('.__('Add Product', 'commerce-manager').')', __('Add Product', 'commerce-manager'), 'manage_options', 'commerce-manager-product', 'commerce_manager_product_page');
add_submenu_page('commerce-manager', 'Commerce Manager ('.__('Products', 'commerce-manager').')', __('Products', 'commerce-manager'), 'manage_options', 'commerce-manager-products', 'commerce_manager_products_page');
add_submenu_page('commerce-manager', 'Commerce Manager ('.__('Add Order', 'commerce-manager').')', __('Add Order', 'commerce-manager'), 'manage_options', 'commerce-manager-order', 'commerce_manager_order_page');
add_submenu_page('commerce-manager', 'Commerce Manager ('.__('Orders', 'commerce-manager').')', __('Orders', 'commerce-manager'), 'manage_options', 'commerce-manager-orders', 'commerce_manager_orders_page');
add_submenu_page('commerce-manager', 'Commerce Manager ('.__('Statistics', 'commerce-manager').')', __('Statistics', 'commerce-manager'), 'manage_options', 'commerce-manager-statistics', 'commerce_manager_statistics_page'); }

add_action('admin_menu', 'commerce_manager_admin_menu');


function commerce_manager_pages_menu() { ?>
<ul class="subsubsub" style="margin: 0 0 1em; float: none;">
<li><a href="admin.php?page=commerce-manager"<?php if ($_GET['page'] == 'commerce-manager') { echo ' class="current"'; } ?>><?php _e('Options', 'commerce-manager'); ?></a></li>
<li>| <a href="admin.php?page=commerce-manager-product"<?php if ($_GET['page'] == 'commerce-manager-product') { echo ' class="current"'; } ?>><?php _e('Add Product', 'commerce-manager'); ?></a></li>
<li>| <a href="admin.php?page=commerce-manager-products"<?php if ($_GET['page'] == 'commerce-manager-products') { echo ' class="current"'; } ?>><?php _e('Products', 'commerce-manager'); ?></a></li>
<li>| <a href="admin.php?page=commerce-manager-order"<?php if ($_GET['page'] == 'commerce-manager-order') { echo ' class="current"'; } ?>><?php _e('Add Order', 'commerce-manager'); ?></a></li>
<li>| <a href="admin.php?page=commerce-manager-orders"<?php if ($_GET['page'] == 'commerce-manager-orders') { echo ' class="current"'; } ?>><?php _e('Orders', 'commerce-manager'); ?></a></li>
<li>| <a href="admin.php?page=commerce-manager-statistics"<?php if ($_GET['page'] == 'commerce-manager-statistics') { echo ' class="current"'; } ?>><?php _e('Statistics', 'commerce-manager'); ?></a></li>
</ul>
<?php }


function commerce_manager_pages_top() { ?>
<h2 style="float:left;">Commerce Manager</h2>
<ul class="subsubsub" style="margin: 2.5em 0 0 6em; float: left;">
<li><a href="http://www.kleor-editions.com/commerce-manager/documentation"><?php _e('Documentation', 'commerce-manager'); ?></a></li>
<?php if (function_exists('affiliation_manager_admin_menu')) { ?>
<li>| <a href="admin.php?page=affiliation-manager">Affiliation Manager</a></li><?php } ?>
</ul>
<div class="clear"></div>
<?php }


function commerce_manager_row_meta($links, $file) {
if ($file == 'commerce-manager/commerce-manager.php') {
return array_merge($links, array(
'<a href="admin.php?page=commerce-manager">'.__('Options', 'commerce-manager').'</a>',
'<a href="http://www.kleor-editions.com/commerce-manager/documentation">'.__('Documentation', 'commerce-manager').'</a>')); }
return $links; }

add_filter('plugin_row_meta', 'commerce_manager_row_meta', 10, 2);