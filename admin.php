<?php if ((strstr($_SERVER['REQUEST_URI'], '/plugins.php')) || (strstr($_SERVER['REQUEST_URI'], '/widgets.php'))) {
load_plugin_textdomain('affiliation-manager', false, 'affiliation-manager/languages'); }
if (strstr($_GET['page'], 'affiliation-manager')) { include_once 'admin-pages-functions.php'; }


function affiliation_manager_admin_menu() {
include 'admin-pages.php';
$options = get_option('affiliation_manager_back_office');
if ($options['menu_title'] == '') { $options['menu_title'] = __('Affiliation', 'affiliation-manager'); }
$menu_items = (array) $options['menu_items'];
$numbers = (array) $options['menu_displayed_items'];
$menu_displayed_items = array();
foreach ($numbers as $i) { $menu_displayed_items[] = $menu_items[$i]; }
if ((defined('AFFILIATION_MANAGER_DEMO')) && (AFFILIATION_MANAGER_DEMO == true)) { $capability = 'manage_options'; }
else { $role = $options['minimum_roles']['view']; $capability = $roles[$role]['capability']; }
if ($options['custom_icon_used'] == 'yes') { $icon_url = format_url($options['custom_icon_url']); }
add_menu_page('Affiliation Manager', $options['menu_title'], $capability, 'affiliation-manager', create_function('', 'include_once "options-page.php";'), $icon_url);
foreach ($admin_pages as $key => $value) {
$slug = 'affiliation-manager'.($key == '' ? '' : '-'.str_replace('_', '-', $key));
if (!strstr($_GET['page'], 'affiliation-manager')) { $value['menu_title'] = $options['pages_titles'][$key]; }
if (($key == '') || ($key == 'back_office') || ($_GET['page'] == $slug) || (in_array($key, $menu_displayed_items))) {
add_submenu_page('affiliation-manager', $value['page_title'], $value['menu_title'], $capability, $slug, create_function('', 'include_once "'.$value['file'].'";')); } } }

add_action('admin_menu', 'affiliation_manager_admin_menu');


function affiliation_manager_action_links($links, $file) {
if ($file == 'affiliation-manager/affiliation-manager.php') {
if (!is_multisite()) {
$links = array_merge($links, array(
'<a href="admin.php?page=affiliation-manager&amp;action=uninstall">'.__('Uninstall', 'affiliation-manager').'</a>')); }
$links = array_merge($links, array(
'<a href="admin.php?page=affiliation-manager&amp;action=reset">'.__('Reset', 'affiliation-manager').'</a>',
'<a href="admin.php?page=affiliation-manager">'.__('Options', 'affiliation-manager').'</a>')); }
return $links; }

add_filter('plugin_action_links', 'affiliation_manager_action_links', 10, 2);


function affiliation_manager_row_meta($links, $file) {
if ($file == 'affiliation-manager/affiliation-manager.php') {
$links = array_merge($links, array(
'<a href="http://www.kleor-editions.com/affiliation-manager/documentation">'.__('Documentation', 'affiliation-manager').'</a>')); }
return $links; }

add_filter('plugin_row_meta', 'affiliation_manager_row_meta', 10, 2);


function install_affiliation_manager() {
global $wpdb;
$results = $wpdb->query("ALTER TABLE ".$wpdb->prefix."options CHANGE option_name option_name VARCHAR(128) NOT NULL");
load_plugin_textdomain('affiliation-manager', false, 'affiliation-manager/languages');
include 'initial-options.php';
foreach ($initial_options as $key => $value) {
$_key = ($key == '' ? '' : '_'.$key);
if (is_array($value)) {
$options = get_option('affiliation_manager'.$_key);
foreach ($value as $option => $initial_value) {
if (($option == 'menu_title') || ($option == 'pages_titles') || ($option == 'version')
 || ($options[$option] == '')) { $options[$option] = $initial_value; } }
update_option('affiliation_manager'.$_key, $options); }
else { add_option('affiliation_manager'.$_key, $value); } }

include_once ABSPATH.'wp-admin/includes/upgrade.php';
if (!empty($wpdb->charset)) { $charset_collate = 'DEFAULT CHARACTER SET '.$wpdb->charset; }
if (!empty($wpdb->collate)) { $charset_collate .= ' COLLATE '.$wpdb->collate; }
include 'tables.php';
foreach (array(
'affiliates_performances',
'commissions',
'messages_commissions',
'prospects_commissions',
'recurring_commissions') as $table_slug) { unset($tables[$table_slug]); }
foreach ($tables as $table_slug => $table) {
unset($list); foreach ($table as $key => $value) { $list .= "
".$key." ".$value['type']." ".($key == "id" ? "auto_increment" : "NOT NULL").","; }
$sql = "CREATE TABLE ".$wpdb->prefix."affiliation_manager_".$table_slug." (".$list."
PRIMARY KEY  (id)) $charset_collate;"; dbDelta($sql);
foreach ($table as $key => $value) { if ($value['default'] != '') {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_".$table_slug." SET ".$key." = '".$value['default']."' WHERE ".$key." = ''"); } } } }

register_activation_hook('affiliation-manager/affiliation-manager.php', 'install_affiliation_manager');


function reset_affiliation_manager() {
load_plugin_textdomain('affiliation-manager', false, 'affiliation-manager/languages');
include 'initial-options.php';
foreach ($initial_options as $key => $value) {
$_key = ($key == '' ? '' : '_'.$key);
update_option('affiliation_manager'.$_key, $value); }
install_affiliation_manager(); }


function uninstall_affiliation_manager() {
global $wpdb;
include 'initial-options.php';
foreach ($initial_options as $key => $value) {
$_key = ($key == '' ? '' : '_'.$key);
delete_option('affiliation_manager'.$_key); }
include 'tables.php';
foreach ($tables as $table_slug => $table) {
$results = $wpdb->query("DROP TABLE ".$wpdb->prefix.'affiliation_manager_'.$table_slug); } }