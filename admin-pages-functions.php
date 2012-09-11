<?php load_plugin_textdomain('membership-manager', false, 'membership-manager/languages'); wp_enqueue_script('dashboard');


function membership_manager_pages_links($back_office_options) {
$links = (array) $back_office_options['links'];
$displayed_links = (array) $back_office_options['displayed_links'];
if (($back_office_options['links_displayed'] == 'yes') && (count($displayed_links) > 0)) {
include 'admin-pages.php';
if ($back_office_options['title_displayed'] == 'yes') { $left_margin = '6em'; } else { $left_margin = '0'; }
echo '<ul class="subsubsub" style="margin: 2em 0 1.5em '.$left_margin.'; float: left; white-space: normal;">';
$links_markup = array(
'Documentation' => '<a href="http://www.kleor-editions.com/membership-manager/documentation">'.$admin_links['Documentation']['name'].'</a>',
'Commerce Manager' => (function_exists('commerce_manager_admin_menu') ? '<a href="admin.php?page=commerce-manager'.
((($_GET['page'] == 'membership-manager-member') || ($_GET['page'] == 'membership-manager-member-area')) ? '-product' : '').
((($_GET['page'] == 'membership-manager-member-category') || ($_GET['page'] == 'membership-manager-member-area-category')) ? '-product-category' : '').
((($_GET['page'] == 'membership-manager-members') || ($_GET['page'] == 'membership-manager-members-areas')) ? '-products' : '').
((($_GET['page'] == 'membership-manager-members-categories') || ($_GET['page'] == 'membership-manager-members-areas-categories')) ? '-products-categories' : '').
(strstr($_GET['page'], 'back-office') ? '-back-office' : '').
(strstr($_GET['page'], 'front-office') ? '-front-office' : '').
(strstr($_GET['page'], 'statistics') ? '-statistics' : '').'">'.$admin_links['Commerce Manager']['name'].'</a>' : '<a href="http://www.kleor-editions.com/commerce-manager">'.$admin_links['Commerce Manager']['name'].'</a>'),
'Affiliation Manager' => (function_exists('affiliation_manager_admin_menu') ? '<a href="admin.php?page=affiliation-manager'.
((($_GET['page'] == 'membership-manager-member') || ($_GET['page'] == 'membership-manager-member-area')) ? '-affiliate' : '').
((($_GET['page'] == 'membership-manager-member-category') || ($_GET['page'] == 'membership-manager-member-area-category')) ? '-affiliate-category' : '').
((($_GET['page'] == 'membership-manager-members') || ($_GET['page'] == 'membership-manager-members-areas')) ? '-affiliates' : '').
((($_GET['page'] == 'membership-manager-members-categories') || ($_GET['page'] == 'membership-manager-members-areas-categories')) ? '-affiliates-categories' : '').
(strstr($_GET['page'], 'back-office') ? '-back-office' : '').
(strstr($_GET['page'], 'front-office') ? '-front-office' : '').
(strstr($_GET['page'], 'statistics') ? '-statistics' : '').'">'.$admin_links['Affiliation Manager']['name'].'</a>' : '<a href="http://www.kleor-editions.com/affiliation-manager">'.$admin_links['Affiliation Manager']['name'].'</a>'),
'Optin Manager' => (function_exists('optin_manager_admin_menu') ? '<a href="admin.php?page=optin-manager'.
((($_GET['page'] == 'membership-manager-member') || ($_GET['page'] == 'membership-manager-member-area')) ? '-form' : '').
((($_GET['page'] == 'membership-manager-member-category') || ($_GET['page'] == 'membership-manager-member-area-category')) ? '-form-category' : '').
((($_GET['page'] == 'membership-manager-members') || ($_GET['page'] == 'membership-manager-members-areas')) ? '-forms' : '').
((($_GET['page'] == 'membership-manager-members-categories') || ($_GET['page'] == 'membership-manager-members-areas-categories')) ? '-forms-categories' : '').
(strstr($_GET['page'], 'back-office') ? '-back-office' : '').
(strstr($_GET['page'], 'statistics') ? '-statistics' : '').'">'.$admin_links['Optin Manager']['name'].'</a>' : '<a href="http://www.kleor-editions.com/optin-manager">'.$admin_links['Optin Manager']['name'].'</a>'),
'Contact Manager' => (function_exists('contact_manager_admin_menu') ? '<a href="admin.php?page=contact-manager'.
((($_GET['page'] == 'membership-manager-member') || ($_GET['page'] == 'membership-manager-member-area')) ? '-form' : '').
((($_GET['page'] == 'membership-manager-member-category') || ($_GET['page'] == 'membership-manager-member-area-category')) ? '-form-category' : '').
((($_GET['page'] == 'membership-manager-members') || ($_GET['page'] == 'membership-manager-members-areas')) ? '-forms' : '').
((($_GET['page'] == 'membership-manager-members-categories') || ($_GET['page'] == 'membership-manager-members-areas-categories')) ? '-forms-categories' : '').
(strstr($_GET['page'], 'back-office') ? '-back-office' : '').
(strstr($_GET['page'], 'statistics') ? '-statistics' : '').'">'.$admin_links['Contact Manager']['name'].'</a>' : '<a href="http://www.kleor-editions.com/contact-manager">'.$admin_links['Contact Manager']['name'].'</a>'));
$first = true; $links_displayed = array();
for ($i = 0; $i < count($admin_links); $i++) {
$link = $links[$i];
if ((in_array($i, $displayed_links)) && ($links_markup[$link] != '') && (!in_array($link, $links_displayed))) {
echo '<li>'.($first ? '' : ' | ').$links_markup[$link].'</li>'; $first = false; $links_displayed[] = $link; } }
echo '</ul>'; } }


function membership_manager_pages_menu($back_office_options) {
$menu_items = (array) $back_office_options['menu_items'];
$menu_displayed_items = (array) $back_office_options['menu_displayed_items'];
if (($back_office_options['menu_displayed'] == 'yes') && (count($menu_displayed_items) > 0)) {
include 'admin-pages.php';
echo '<ul class="subsubsub" style="margin: 0 0 1em; float: left; white-space: normal;">';
$first = true; $items_displayed = array();
for ($i = 0; $i < count($admin_pages); $i++) {
$item = $menu_items[$i];
if ((in_array($i, $menu_displayed_items)) && (!in_array($item, $items_displayed))) {
$slug = 'membership-manager'.($item == '' ? '' : '-'.str_replace('_', '-', $item));
echo '<li>'.($first ? '' : ' | ').'<a href="admin.php?page='.$slug.'"'.($_GET['page'] == $slug ? ' class="current"' : '').'>'.$admin_pages[$item]['menu_title'].'</a></li>';
$first = false; $items_displayed[] = $item; } }
echo '</ul>'; } }


function membership_manager_pages_module($back_office_options, $module, $undisplayed_modules) {
include 'admin-pages.php';
$page_slug = str_replace('-', '_', str_replace('-page', '', $module));
$page_undisplayed_modules = (array) $back_office_options[$page_slug.'_page_undisplayed_modules']; ?>
<div class="postbox" id="<?php echo $module.'-module'; ?>"<?php if (in_array($module, $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="<?php echo $module; ?>"><strong><?php echo $modules['back_office'][$module]['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="<?php echo $page_slug; ?>_page_summary_displayed" id="<?php echo $page_slug; ?>_page_summary_displayed" value="yes"<?php if ($back_office_options[$page_slug.'_page_summary_displayed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Display the summary', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><?php _e('Modules displayed', 'membership-manager'); ?></strong></th>
<td><?php foreach ($modules[$page_slug] as $key => $value) {
$name = $page_slug.'_page_'.str_replace('-', '_', $key).'_module_displayed';
if (!strstr($_GET['page'], 'back-office')) { $onmouseover = " onmouseover=\"document.getElementById('".$key."-submodules').style.display = 'block';\""; }
if ($value['required'] == 'yes') { echo '<label'.$onmouseover.'><input type="checkbox" name="'.$name.'" id="'.$name.'" value="yes" checked="checked" disabled="disabled" /> '.$value['name'].'<br /></label>'; }
else {
if ((($page_slug == 'back_office') || (!strstr($_GET['page'], 'back-office'))) && ($key != $module)) {
$onclick = " onclick=\"if (this.checked == true) { document.getElementById('".$key."-module').style.display = 'block'; } else { document.getElementById('".$key."-module').style.display = 'none'; } window.location = '#".$module."-module';\""; }
else { $onclick = ""; }
echo '<label'.$onmouseover.(((!isset($_GET['id'])) || ($page_slug != 'member') || (!in_array($key, $add_member_modules))) ? '' : ' style="display: none;"').'><input type="checkbox" name="'.$name.'" id="'.$name.'" value="yes"'.(in_array($key, $page_undisplayed_modules) ? '' : ' checked="checked"').$onclick.' /> '.$value['name'].'<br /></label>'; }
if (!strstr($_GET['page'], 'back-office')) { echo '<div style="display: none;" id="'.$key.'-submodules">'; }
if (is_array($value['modules'])) { foreach ($value['modules'] as $module_key => $module_value) {
$module_name = $page_slug.'_page_'.str_replace('-', '_', $module_key).'_module_displayed';
if ($module_value['required'] == 'yes') { echo '<label><input style="margin-left: 2em;" type="checkbox" name="'.$module_name.'" id="'.$module_name.'" value="yes" checked="checked" disabled="disabled" /> '.$module_value['name'].'<br /></label>'; }
else {
if (($page_slug == 'back_office') || (!strstr($_GET['page'], 'back-office'))) {
$module_onclick = " onclick=\"if (this.checked == true) { document.getElementById('".$module_key."-module').style.display = 'block'; } else { document.getElementById('".$module_key."-module').style.display = 'none'; } window.location = '#".$module."-module';\""; }
else { $module_onclick = ""; }
echo '<label><input style="margin-left: 2em;" type="checkbox" name="'.$module_name.'" id="'.$module_name.'" value="yes"'.(in_array($module_key, $page_undisplayed_modules) ? '' : ' checked="checked"').$module_onclick.' /> '.$module_value['name'].'<br /></label>'; } } }
if (!strstr($_GET['page'], 'back-office')) { echo '</div>'; } } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<?php }


function membership_manager_pages_search_field($type, $searchby, $searchby_options) { ?>
<p class="search-box" style="float: right;"><label><?php _e(ucfirst($type).' by', 'membership-manager'); ?> <select name="<?php echo $type; ?>by" id="<?php echo $type; ?>by">
<?php if ($type == 'search') { echo '<option value=""'.($searchby == '' ? ' selected="selected"' : '').'>'.__('all fields', 'membership-manager').'</option>'; } ?>
<?php foreach ($searchby_options as $key => $value) {
echo '<option value="'.$key.'"'.($searchby == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></label><br />
<input type="text" name="s" id="s" size="40" value="<?php echo $_GET['s']; ?>" />
<input type="submit" class="button" name="submit" id="<?php echo $type; ?>-submit" value="<?php _e(ucfirst($type), 'membership-manager'); ?>" /></p>
<?php }


function membership_manager_pages_summary($back_office_options) {
if ($_GET['page'] == 'membership-manager') { $page_slug = 'options'; }
else { $page_slug = str_replace('-', '_', str_replace('membership-manager-', '', $_GET['page'])); }
if ($back_office_options[$page_slug.'_page_summary_displayed'] == 'yes') {
include 'admin-pages.php';
$modules = $modules[$page_slug];
$undisplayed_modules = (array) $back_office_options[$page_slug.'_page_undisplayed_modules'];
foreach ($modules as $key => $value) {
if ((!isset($_GET['id'])) || ($page_slug != 'member') || (!in_array($key, $add_member_modules))) {
if (!in_array($key, $undisplayed_modules)) { $list .= '<li> | <a href="#'.$key.'">'.$value['name'].'</a></li>'; } } }
if (strlen($list) > 7) { echo '<ul class="subsubsub" style="float: none; white-space: normal;"><li>'.substr($list, 7).'</ul>'; } } }


function membership_manager_pages_title($back_office_options) {
if ($back_office_options['title_displayed'] == 'yes') {
echo '<h2 style="float: left;">'.$back_office_options['title'].'</h2>'; } }


function membership_manager_pages_top($back_office_options) {
membership_manager_pages_title($back_office_options);
membership_manager_pages_links($back_office_options);
echo '<div class="clear"></div>'; }


function membership_manager_user_can($back_office_options, $capability) {
if ((defined('MEMBERSHIP_MANAGER_DEMO')) && (MEMBERSHIP_MANAGER_DEMO == true)) { $capability = 'manage_options'; }
else { include 'admin-pages.php'; $role = $back_office_options['minimum_roles'][$capability]; $capability = $roles[$role]['capability']; }
return current_user_can($capability); }


function membership_manager_users_roles() {
$wp_roles = new WP_Roles();
$roles = $wp_roles->get_names();
foreach ($roles as $role => $name) { $roles[$role] = translate_user_role($name); }
return $roles; }


function update_membership_manager_back_office($back_office_options, $page) {
include 'admin-pages.php';
if ($_POST[$page.'_page_summary_displayed'] != 'yes') { $_POST[$page.'_page_summary_displayed'] = 'no'; }
$_POST[$page.'_page_undisplayed_modules'] = array();
foreach ($modules[$page] as $key => $value) {
if (($_POST[$page.'_page_'.str_replace('-', '_', $key).'_module_displayed'] != 'yes') && ($value['required'] != 'yes')) { $_POST[$page.'_page_undisplayed_modules'][] = $key; }
if (is_array($value['modules'])) { foreach ($value['modules'] as $module_key => $module_value) {
if (($_POST[$page.'_page_'.str_replace('-', '_', $module_key).'_module_displayed'] != 'yes') && ($module_value['required'] != 'yes')) { $_POST[$page.'_page_undisplayed_modules'][] = $module_key; } } } }
if (!strstr($_GET['page'], 'back-office')) {
foreach (array('summary_displayed', 'undisplayed_modules') as $option) {
$back_office_options[$page.'_page_'.$option] = $_POST[$page.'_page_'.$option]; }
update_option('membership_manager_back_office', $back_office_options);
return $back_office_options; } }


function membership_manager_pages_date_picker($start_date, $end_date) {
echo '<p style="margin: 0 0 1em 0; float: left;"><label><strong>'.__('Start', 'membership-manager').'</strong>
<input class="date-pick" style="margin: 0.5em;" type="text" name="start_date" id="start_date" size="20" value="'.$start_date.'" /></label>
<label style="margin-left: 3em;"><strong>'.__('End', 'membership-manager').'</strong>
<input class="date-pick" style="margin: 0.5em;" type="text" name="end_date" id="end_date" size="20" value="'.$end_date.'" /></label>
<input style="margin-left: 3em;" type="submit" class="button-secondary" name="submit" value="'.__('Display', 'membership-manager').'" /></p>
<div class="clear"></div>'; }


function membership_manager_date_picker_css() { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo MEMBERSHIP_MANAGER_URL; ?>libraries/date-picker.css" />
<?php }


function membership_manager_date_picker_js() { ?>
<script type="text/javascript" src="<?php echo MEMBERSHIP_MANAGER_URL; ?>libraries/date-picker.js"></script>
<script type="text/javascript">
Date.dayNames = ['<?php _e('Sunday', 'membership-manager'); ?>', '<?php _e('Monday', 'membership-manager'); ?>', '<?php _e('Tuesday', 'membership-manager'); ?>', '<?php _e('Wednesday', 'membership-manager'); ?>', '<?php _e('Thursday', 'membership-manager'); ?>', '<?php _e('Friday', 'membership-manager'); ?>', '<?php _e('Saturday', 'membership-manager'); ?>'];
Date.abbrDayNames = ['<?php _e('Sun', 'membership-manager'); ?>', '<?php _e('Mon', 'membership-manager'); ?>', '<?php _e('Tue', 'membership-manager'); ?>', '<?php _e('Wed', 'membership-manager'); ?>', '<?php _e('Thu', 'membership-manager'); ?>', '<?php _e('Fri', 'membership-manager'); ?>', '<?php _e('Sat', 'membership-manager'); ?>'];
Date.monthNames = ['<?php _e('January', 'membership-manager'); ?>', '<?php _e('February', 'membership-manager'); ?>', '<?php _e('March', 'membership-manager'); ?>', '<?php _e('April', 'membership-manager'); ?>', '<?php _e('May', 'membership-manager'); ?>', '<?php _e('June', 'membership-manager'); ?>', '<?php _e('July', 'membership-manager'); ?>', '<?php _e('August', 'membership-manager'); ?>', '<?php _e('September', 'membership-manager'); ?>', '<?php _e('October', 'membership-manager'); ?>', '<?php _e('November', 'membership-manager'); ?>', '<?php _e('December', 'membership-manager'); ?>'];
Date.abbrMonthNames = ['<?php _e('Jan', 'membership-manager'); ?>', '<?php _e('Feb', 'membership-manager'); ?>', '<?php _e('Mar', 'membership-manager'); ?>', '<?php _e('Apr', 'membership-manager'); ?>', '<?php _e('May', 'membership-manager'); ?>', '<?php _e('Jun', 'membership-manager'); ?>', '<?php _e('Jul', 'membership-manager'); ?>', '<?php _e('Aug', 'membership-manager'); ?>', '<?php _e('Sep', 'membership-manager'); ?>', '<?php _e('Oct', 'membership-manager'); ?>', '<?php _e('Nov', 'membership-manager'); ?>', '<?php _e('Dec', 'membership-manager'); ?>'];
$.dpText = {
TEXT_PREV_YEAR : '<?php _e('Previous year', 'membership-manager'); ?>',
TEXT_PREV_MONTH : '<?php _e('Previous month', 'membership-manager'); ?>',
TEXT_NEXT_YEAR : '<?php _e('Next year', 'membership-manager'); ?>',
TEXT_NEXT_MONTH : '<?php _e('Next month', 'membership-manager'); ?>',
TEXT_CLOSE : '<?php _e('Close', 'membership-manager'); ?>',
TEXT_CHOOSE_DATE : '<?php _e('Choose a date', 'membership-manager'); ?>',
DATE_PICKER_ALT : '<?php _e('Date', 'membership-manager'); ?>',
DATE_PICKER_URL : '<?php echo MEMBERSHIP_MANAGER_URL; ?>images/date-picker.png',
HEADER_FORMAT : 'mmmm yyyy'
}; $(function(){ $('.date-pick').datePicker({startDate:'2011-01-01'}); });
</script>
<?php }

if (($_GET['action'] != 'delete')
 && ($_GET['page'] != 'membership-manager')
 && ($_GET['page'] != 'membership-manager-front-office')
 && ($_GET['page'] != 'membership-manager-back-office')) {
add_action('admin_head', 'membership_manager_date_picker_css');
add_action('admin_footer', 'membership_jquery_js');
add_action('admin_footer', 'membership_manager_date_picker_js'); }