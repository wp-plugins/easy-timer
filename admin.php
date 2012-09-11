<?php if ((strstr($_SERVER['REQUEST_URI'], '/plugins.php')) || (strstr($_SERVER['REQUEST_URI'], '/widgets.php'))) {
load_plugin_textdomain('membership-manager', false, 'membership-manager/languages'); }
if (strstr($_GET['page'], 'membership-manager')) { include_once 'admin-pages-functions.php'; }


function membership_manager_admin_menu() {
include 'admin-pages.php';
$options = get_option('membership_manager_back_office');
if ($options['menu_title'] == '') { $options['menu_title'] = __('Membership', 'membership-manager'); }
$menu_items = (array) $options['menu_items'];
$numbers = (array) $options['menu_displayed_items'];
$menu_displayed_items = array();
foreach ($numbers as $i) { $menu_displayed_items[] = $menu_items[$i]; }
if ((defined('MEMBERSHIP_MANAGER_DEMO')) && (MEMBERSHIP_MANAGER_DEMO == true)) { $capability = 'manage_options'; }
else { $role = $options['minimum_roles']['view']; $capability = $roles[$role]['capability']; }
if ($options['custom_icon_used'] == 'yes') { $icon_url = format_url($options['custom_icon_url']); }
add_menu_page('Membership Manager', $options['menu_title'], $capability, 'membership-manager', create_function('', 'include_once "options-page.php";'), $icon_url);
foreach ($admin_pages as $key => $value) {
$slug = 'membership-manager'.($key == '' ? '' : '-'.str_replace('_', '-', $key));
if (!strstr($_GET['page'], 'membership-manager')) { $value['menu_title'] = $options['pages_titles'][$key]; }
if (($key == '') || ($key == 'back_office') || ($_GET['page'] == $slug) || (in_array($key, $menu_displayed_items))) {
add_submenu_page('membership-manager', $value['page_title'], $value['menu_title'], $capability, $slug, create_function('', 'include_once "'.$value['file'].'";')); } } }

add_action('admin_menu', 'membership_manager_admin_menu');


function membership_manager_action_links($links, $file) {
if ($file == 'membership-manager/membership-manager.php') {
if (!is_multisite()) {
$links = array_merge($links, array(
'<a href="admin.php?page=membership-manager&amp;action=uninstall">'.__('Uninstall', 'membership-manager').'</a>')); }
$links = array_merge($links, array(
'<a href="admin.php?page=membership-manager&amp;action=reset">'.__('Reset', 'membership-manager').'</a>',
'<a href="admin.php?page=membership-manager">'.__('Options', 'membership-manager').'</a>')); }
return $links; }

add_filter('plugin_action_links', 'membership_manager_action_links', 10, 2);


function membership_manager_row_meta($links, $file) {
if ($file == 'membership-manager/membership-manager.php') {
$links = array_merge($links, array(
'<a href="http://www.kleor-editions.com/membership-manager/documentation">'.__('Documentation', 'membership-manager').'</a>')); }
return $links; }

add_filter('plugin_row_meta', 'membership_manager_row_meta', 10, 2);


function install_membership_manager() {
global $wpdb;
$results = $wpdb->query("ALTER TABLE ".$wpdb->prefix."options CHANGE option_name option_name VARCHAR(128) NOT NULL");
load_plugin_textdomain('membership-manager', false, 'membership-manager/languages');
include 'initial-options.php';
foreach ($initial_options as $key => $value) {
$_key = ($key == '' ? '' : '_'.$key);
if (is_array($value)) {
$options = get_option('membership_manager'.$_key);
foreach ($value as $option => $initial_value) {
if (($option == 'menu_title') || ($option == 'pages_titles') || ($option == 'version')
 || ($options[$option] == '')) { $options[$option] = $initial_value; } }
update_option('membership_manager'.$_key, $options); }
else { add_option('membership_manager'.$_key, $value); } }

include_once ABSPATH.'wp-admin/includes/upgrade.php';
if (!empty($wpdb->charset)) { $charset_collate = 'DEFAULT CHARACTER SET '.$wpdb->charset; }
if (!empty($wpdb->collate)) { $charset_collate .= ' COLLATE '.$wpdb->collate; }
include 'tables.php';
foreach ($tables as $table_slug => $table) {
unset($list); foreach ($table as $key => $value) { $list .= "
".$key." ".$value['type']." ".($key == "id" ? "auto_increment" : "NOT NULL").","; }
$sql = "CREATE TABLE ".$wpdb->prefix."membership_manager_".$table_slug." (".$list."
PRIMARY KEY  (id)) $charset_collate;"; dbDelta($sql);
foreach ($table as $key => $value) { if ($value['default'] != '') {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_".$table_slug." SET ".$key." = '".$value['default']."' WHERE ".$key." = ''"); } } } }

register_activation_hook('membership-manager/membership-manager.php', 'install_membership_manager');


function reset_membership_manager() {
load_plugin_textdomain('membership-manager', false, 'membership-manager/languages');
include 'initial-options.php';
foreach ($initial_options as $key => $value) {
$_key = ($key == '' ? '' : '_'.$key);
update_option('membership_manager'.$_key, $value); }
install_membership_manager(); }


function uninstall_membership_manager() {
global $wpdb;
include 'initial-options.php';
foreach ($initial_options as $key => $value) {
$_key = ($key == '' ? '' : '_'.$key);
delete_option('membership_manager'.$_key); }
include 'tables.php';
foreach ($tables as $table_slug => $table) {
$results = $wpdb->query("DROP TABLE ".$wpdb->prefix.'membership_manager_'.$table_slug); } }