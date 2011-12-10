<?php if (strstr($_GET['page'], 'optin-manager')) { wp_enqueue_script('dashboard'); }

function optin_manager_back_office_page() { include 'back-office-page.php'; }
function optin_manager_form_page() { include 'form-page.php'; }
function optin_manager_options_page() { include 'options-page.php'; }
function optin_manager_prospect_page() { include 'prospect-page.php'; }
function optin_manager_statistics_page() { include 'statistics-page.php'; }
function optin_manager_table_page() { include 'table-page.php'; }

function optin_manager_admin_menu() {
include 'admin-pages.php';
add_menu_page('Optin Manager', __('Optin', 'optin-manager'), 'manage_options', 'optin-manager', 'optin_manager_options_page', '');
foreach ($admin_pages as $key => $value) {
add_submenu_page('optin-manager', $value['page_title'], $value['menu_title'], 'manage_options', 'optin-manager'.($key == '' ? '' : '-'.str_replace('_', '-', $key)), $value['function']); } }

add_action('admin_menu', 'optin_manager_admin_menu');


function optin_manager_pages_date_picker($start_date, $end_date) {
echo '<p style="margin: 0 0 1em 0;"><label><strong>'.__('Start', 'optin-manager').'</strong>
<input class="date-pick" style="margin: 0.5em;" type="text" name="start_date" id="start_date" size="20" value="'.$start_date.'" /></label>
<label style="margin-left: 3em;"><strong>'.__('End', 'optin-manager').'</strong>
<input class="date-pick" style="margin: 0.5em;" type="text" name="end_date" id="end_date" size="20" value="'.$end_date.'" /></label>
<input style="margin-left: 3em;" type="submit" class="button-secondary" name="submit" value="'.__('Display', 'optin-manager').'" /></p>'; }


function optin_manager_pages_links($back_office_options) {
if ($back_office_options['links_displayed'] == 'yes') {
include 'admin-pages.php';
if ($back_office_options['title_displayed'] == 'yes') { $left_margin = '6em'; } else { $left_margin = '0'; }
echo '<ul class="subsubsub" style="margin: 2em 0 1.5em '.$left_margin.'; float: left; white-space: normal;">';
$links = (array) $back_office_options['links'];
$displayed_links = (array) $back_office_options['displayed_links'];
$links_markup = array(
'Documentation' => '<a href="http://www.kleor-editions.com/optin-manager/documentation">'.$admin_links['Documentation']['name'].'</a>',
'Commerce Manager' => (function_exists('commerce_manager_admin_menu') ? '<a href="admin.php?page=commerce-manager'.
(strstr($_GET['page'], 'back-office') ? '-back-office' : '').
(strstr($_GET['page'], 'statistics') ? '-statistics' : '').'">'.$admin_links['Commerce Manager']['name'].'</a>' : '<a href="http://www.kleor-editions.com/commerce-manager">'.$admin_links['Commerce Manager']['name'].'</a>'),
'Affiliation Manager' => (function_exists('affiliation_manager_admin_menu') ? '<a href="admin.php?page=affiliation-manager'.
(strstr($_GET['page'], 'back-office') ? '-back-office' : '').
(strstr($_GET['page'], 'prospects') ? '-prospects-commissions' : '').
(strstr($_GET['page'], 'statistics') ? '-statistics' : '').'">'.$admin_links['Affiliation Manager']['name'].'</a>' : '<a href="http://www.kleor-editions.com/affiliation-manager">'.$admin_links['Affiliation Manager']['name'].'</a>'),
'Membership Manager' => (function_exists('membership_manager_admin_menu') ? '<a href="admin.php?page=membership-manager'.
(strstr($_GET['page'], 'back-office') ? '-back-office' : '').
(strstr($_GET['page'], 'statistics') ? '-statistics' : '').'">'.$admin_links['Membership Manager']['name'].'</a>' : '<a href="http://www.kleor-editions.com/membership-manager">'.$admin_links['Membership Manager']['name'].'</a>'));
$first = true;
for ($i = 0; $i < count($admin_links); $i++) {
$link = $links[$i];
if ((in_array($i, $displayed_links)) && ($links_markup[$link] != '')) { echo '<li>'.($first ? '' : ' | ').$links_markup[$link].'</li>'; $first = false; } }
echo '</ul>'; } }


function optin_manager_pages_menu($back_office_options) {
if ($back_office_options['menu_displayed'] == 'yes') {
include 'admin-pages.php';
echo '<ul class="subsubsub" style="margin: 0 0 1em; float: left; white-space: normal;">';
$menu_items = (array) $back_office_options['menu_items'];
$menu_displayed_items = (array) $back_office_options['menu_displayed_items'];
$first = true;
for ($i = 0; $i < count($admin_pages); $i++) {
$item = $menu_items[$i];
$slug = 'optin-manager'.($item == '' ? '' : '-'.str_replace('_', '-', $item));
if (in_array($i, $menu_displayed_items)) {
echo '<li>'.($first ? '' : ' | ').'<a href="admin.php?page='.$slug.'"'.($_GET['page'] == $slug ? ' class="current"' : '').'>'.$admin_pages[$item]['menu_title'].'</a></li>';
$first = false; } }
echo '</ul>'; } }


function optin_manager_pages_module($back_office_options, $module, $undisplayed_modules) {
include 'admin-pages.php';
$page_slug = str_replace('-', '_', str_replace('-page', '', $module));
$page_undisplayed_modules = (array) $back_office_options[$page_slug.'_page_undisplayed_modules']; ?>
<div class="postbox"<?php if (in_array($module, $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="<?php echo $module; ?>"><strong><?php echo $modules['back_office'][$module]['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="<?php echo $page_slug; ?>_page_summary_displayed" id="<?php echo $page_slug; ?>_page_summary_displayed" value="yes"<?php if ($back_office_options[$page_slug.'_page_summary_displayed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Display the summary', 'optin-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><?php _e('Modules displayed', 'optin-manager'); ?></strong></th>
<td><?php foreach ($modules[$page_slug] as $key => $value) {
$name = $page_slug.'_page_'.str_replace('-', '_', $key).'_module_displayed';
if ($value['required'] == 'yes') { echo '<input type="checkbox" name="'.$name.'" id="'.$name.'" value="yes" checked="checked" disabled="disabled" /> '.$value['name'].'<br />'; }
else { echo '<label'.((($page_slug != 'prospect') || (!isset($_GET['id'])) || (!in_array($key, array('registration-confirmation-email', 'registration-notification-email', 'membership', 'custom-instructions')))) ? '' : ' style="display: none;"').'><input type="checkbox" name="'.$name.'" id="'.$name.'" value="yes"'.(in_array($key, $page_undisplayed_modules) ? '' : ' checked="checked"').' /> '.$value['name'].'<br /></label>'; }
if (is_array($value['modules'])) { foreach ($value['modules'] as $module_key => $module_value) {
$module_name = $page_slug.'_page_'.str_replace('-', '_', $module_key).'_module_displayed';
if ($module_value['required'] == 'yes') { echo '<input style="margin-left: 2em;" type="checkbox" name="'.$module_name.'" id="'.$module_name.'" value="yes" checked="checked" disabled="disabled" /> '.$module_value['name'].'<br />'; }
else { echo '<label><input style="margin-left: 2em;" type="checkbox" name="'.$module_name.'" id="'.$module_name.'" value="yes"'.(in_array($module_key, $page_undisplayed_modules) ? '' : ' checked="checked"').' /> '.$module_value['name'].'<br /></label>'; } } } } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<?php }


function optin_manager_pages_search_field($type, $searchby, $searchby_options) { ?>
<p class="search-box" style="float: right;"><label><?php _e(ucfirst($type).' by', 'optin-manager'); ?> <select name="<?php echo $type; ?>by" id="<?php echo $type; ?>by">
<?php if ($type == 'search') { echo '<option value=""'.($searchby == '' ? ' selected="selected"' : '').'>'.__('all fields', 'optin-manager').'</option>'; } ?>
<?php foreach ($searchby_options as $key => $value) {
echo '<option value="'.$key.'"'.($searchby == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></label><br />
<input type="text" name="s" id="s" value="<?php echo $_GET['s']; ?>" />
<input type="submit" class="button" name="submit" id="<?php echo $type; ?>-submit" value="<?php _e(ucfirst($type), 'optin-manager'); ?>" /></p>
<div class="clear"></div>
<?php }


function optin_manager_pages_summary($back_office_options) {
if ($_GET['page'] == 'optin-manager') { $page_slug = 'options'; }
else { $page_slug = str_replace('-', '_', str_replace('optin-manager-', '', $_GET['page'])); }
if ($back_office_options[$page_slug.'_page_summary_displayed'] == 'yes') {
include 'admin-pages.php';
$modules = $modules[$page_slug];
$undisplayed_modules = (array) $back_office_options[$page_slug.'_page_undisplayed_modules'];
foreach ($modules as $key => $value) {
if (($page_slug != 'prospect') || (!isset($_GET['id'])) || (!in_array($key, array('registration-confirmation-email', 'registration-notification-email', 'membership', 'custom-instructions')))) {
if (!in_array($key, $undisplayed_modules)) { $list .= '<li> | <a href="#'.$key.'">'.$value['name'].'</a></li>'; } } }
echo '<ul class="subsubsub" style="float: none; white-space: normal;">
<li>'.substr($list, 7).'
</ul>'; } }


function optin_manager_pages_title($back_office_options) {
if ($back_office_options['title_displayed'] == 'yes') {
echo '<h2 style="float: left;">'.$back_office_options['title'].'</h2>'; } }


function optin_manager_pages_top($back_office_options) {
optin_manager_pages_title($back_office_options);
optin_manager_pages_links($back_office_options);
echo '<div class="clear"></div>'; }


function optin_manager_action_links($links, $file) {
if ($file == 'optin-manager/optin-manager.php') {
return array_merge($links, array(
'<a href="admin.php?page=optin-manager&amp;action=uninstall">'.__('Uninstall', 'optin-manager').'</a>',
'<a href="admin.php?page=optin-manager">'.__('Options', 'optin-manager').'</a>')); }
return $links; }

add_filter('plugin_action_links', 'optin_manager_action_links', 10, 2);


function optin_manager_row_meta($links, $file) {
if ($file == 'optin-manager/optin-manager.php') {
return array_merge($links, array(
'<a href="http://www.kleor-editions.com/optin-manager/documentation">'.__('Documentation', 'optin-manager').'</a>')); }
return $links; }

add_filter('plugin_row_meta', 'optin_manager_row_meta', 10, 2);


function install_optin_manager() {
global $wpdb;
include 'initial-options.php';
foreach ($initial_options as $key => $value) {
$_key = ($key == '' ? '' : '_'.$key);
if (is_array($value)) {
$options = get_option('optin_manager'.$_key);
foreach ($value as $option => $initial_value) {
if (($option == 'version') || ($options[$option] == '')) { $options[$option] = $initial_value; } }
update_option('optin_manager'.$_key, $options); }
else { add_option('optin_manager'.$_key, $value); } }

include_once ABSPATH.'wp-admin/includes/upgrade.php';
if (!empty($wpdb->charset)) { $charset_collate = 'DEFAULT CHARACTER SET '.$wpdb->charset; }
if (!empty($wpdb->collate)) { $charset_collate .= ' COLLATE '.$wpdb->collate; }
include 'tables.php';
foreach ($tables as $table_slug => $table) {
unset($list); foreach ($table as $key => $value) { $list .= "
".$key." ".$value['type']." ".($key == "id" ? "auto_increment" : "NOT NULL").","; }
$sql = "CREATE TABLE ".$wpdb->prefix."optin_manager_".$table_slug." (".$list."
PRIMARY KEY  (id)) $charset_collate;"; dbDelta($sql);
foreach ($table as $key => $value) { if ($value['default'] != '') {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."optin_manager_".$table_slug." SET ".$key." = '".$value['default']."' WHERE ".$key." = ''"); } } } }

register_activation_hook('optin-manager/optin-manager.php', 'install_optin_manager');


function uninstall_optin_manager() {
global $wpdb;
include 'initial-options.php';
foreach ($initial_options as $key => $value) {
$_key = ($key == '' ? '' : '_'.$key);
delete_option('optin_manager'.$_key); }
include 'tables.php';
foreach ($tables as $table_slug => $table) {
$results = $wpdb->query("DROP TABLE ".$wpdb->prefix.'optin_manager_'.$table_slug); } }


if (($_GET['action'] != 'delete') && (
($_GET['page'] == 'optin-manager-form')
 || ($_GET['page'] == 'optin-manager-form-category')
 || ($_GET['page'] == 'optin-manager-forms')
 || ($_GET['page'] == 'optin-manager-forms-categories')
 || ($_GET['page'] == 'optin-manager-prospect')
 || ($_GET['page'] == 'optin-manager-prospects')
 || ($_GET['page'] == 'optin-manager-statistics'))) {
add_action('admin_head', 'optin_date_picker_css'); }