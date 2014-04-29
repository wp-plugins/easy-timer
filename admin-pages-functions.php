<?php load_plugin_textdomain('audiobooks-authors-and-narrators', false, AUDIOBOOKS_AUTHORS_AND_NARRATORS_FOLDER.'/languages');
if (is_admin()) {
foreach ((array) $_GET as $key => $value) { if (is_string($value)) { $_GET[$key] = quotes_entities($_GET[$key]); } }
if (isset($_GET['id'])) { $_GET['id'] = (int) $_GET['id']; if ($_GET['id'] < 1) { unset($_GET['id']); } }
foreach ($_GET as $key => $value) { if (!isset($GLOBALS[$key])) { $GLOBALS[$key] = $value; } } }


function audiobooks_authors_and_narrators_pages_css() { ?>
<style type="text/css" media="all">
html.wp-toolbar { padding-top: 0; }
#wpadminbar { height: 32px; position: absolute; }
#wpwrap { padding-top: 32px; }
.wrap { margin-top: 0; }
.wrap .delete:hover { color: #ff0000; }
.wrap .dp-choose-date { vertical-align: 6%; }
.wrap .postbox { background-color: #f9f9f9; }
.wrap .description, .wrap input[disabled], .wrap textarea[disabled] { color: #808080; }
.wrap .postbox .description { font-size: 13px; }
.wrap .postbox h3 { background-color: #f1f1f1; color: #000000; }
.wrap .postbox h4 { color: #000000; font-family: Tahoma, Geneva, sans-serif; font-size: 1.125em; }
.wrap .postbox input.button-secondary { background-color: #ffffff; }
.wrap h2 { float: left; }
.wrap input.button-secondary, .wrap select { vertical-align: 0; }
.wrap input.date-pick { margin-right: 0.5em; width: 10.5em; }
.wrap p.submit { margin: 0 20%; }
*:-ms-input-placeholder { color: #a0a0a0; }
</style>
<?php }

add_action('admin_head', 'audiobooks_authors_and_narrators_pages_css');


function audiobooks_authors_and_narrators_pages_menu($back_office_options) {
$menu_items = (array) $back_office_options['menu_items'];
$menu_displayed_items = (array) $back_office_options['menu_displayed_items'];
if (($back_office_options['menu_displayed'] == 'yes') && (count($menu_displayed_items) > 0)) {
include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'admin-pages.php';
echo '<ul class="subsubsub" style="margin: 0 0 1em; float: left; white-space: normal;">';
$first = true; $items_displayed = array();
for ($i = 0; $i < count($admin_pages); $i++) {
$item = (isset($menu_items[$i]) ? $menu_items[$i] : '');
if ((isset($admin_pages[$item])) && (in_array($i, $menu_displayed_items)) && (!in_array($item, $items_displayed))) {
$slug = 'audiobooks-authors-and-narrators'.($item == '' ? '' : '-'.str_replace('_', '-', $item));
echo '<li>'.($first ? '' : '&nbsp;| ').'<a href="admin.php?page='.$slug.'"'.($_GET['page'] == $slug ? ' class="current"' : '').'>'.$admin_pages[$item]['menu_title'].'</a></li>';
$first = false; $items_displayed[] = $item; } }
echo '</ul>'; } }


function audiobooks_authors_and_narrators_pages_module($back_office_options, $module, $undisplayed_modules) {
include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'admin-pages.php';
$page_slug = str_replace('-', '_', str_replace('-page', '', $module));
$page_undisplayed_modules = (array) $back_office_options[$page_slug.'_page_undisplayed_modules']; ?>
<div class="postbox" id="<?php echo $module.'-module'; ?>"<?php if (in_array($module, $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 style="font-size: 1.25em;" id="<?php echo $module; ?>"><strong><?php echo $modules['back_office'][$module]['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<?php if ((strstr($_GET['page'], 'back-office')) && ($page_slug != 'back_office')) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=audiobooks-authors-and-narrators'.($page_slug == 'options' ? '' : '-'.str_replace('_', '-', $page_slug)).'">'.__('Click here to open this page.', 'audiobooks-authors-and-narrators').'</a></span></td></tr>'; } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="<?php echo $page_slug; ?>_page_summary_displayed" id="<?php echo $page_slug; ?>_page_summary_displayed" value="yes"<?php if ($back_office_options[$page_slug.'_page_summary_displayed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Display the summary', 'audiobooks-authors-and-narrators'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><?php _e('Modules displayed', 'audiobooks-authors-and-narrators'); ?></strong></th>
<td><?php foreach ($modules[$page_slug] as $key => $value) {
$name = $page_slug.'_page_'.str_replace('-', '_', $key).'_module_displayed';
if (strstr($_GET['page'], 'back-office')) { $onmouseover = ""; }
else { $onmouseover = " onmouseover=\"document.getElementById('".$key."-submodules').style.display = 'block';\""; }
if ((!isset($value['title'])) || ($value['title'] == '')) {
if ((isset($value['required'])) && ($value['required'] == 'yes')) { $title = ' title="'.__('You can\'t disable the display of this module.', 'audiobooks-authors-and-narrators').'"'; }
else { $title = ''; } }
else { $title = ' title="'.$value['title'].'"'; }
if ((isset($value['required'])) && ($value['required'] == 'yes')) { echo '<label'.$onmouseover.$title.'><input type="checkbox" name="'.$name.'" id="'.$name.'" value="yes" checked="checked" disabled="disabled" /> '.$value['name'].'<br /></label>'; }
else { echo '<label'.$onmouseover.$title.'><input type="checkbox" name="'.$name.'" id="'.$name.'" value="yes"'.(in_array($key, $page_undisplayed_modules) ? '' : ' checked="checked"').' /> '.$value['name'].'<br /></label>'; }
if (!strstr($_GET['page'], 'back-office')) { echo '<div id="'.$key.'-submodules">'; }
if (isset($value['modules'])) { foreach ($value['modules'] as $module_key => $module_value) {
$module_name = $page_slug.'_page_'.str_replace('-', '_', $module_key).'_module_displayed';
if ((!isset($module_value['title'])) || ($module_value['title'] == '')) {
if ((isset($module_value['required'])) && ($module_value['required'] == 'yes')) { $module_title = ' title="'.__('You can\'t disable the display of this module.', 'audiobooks-authors-and-narrators').'"'; }
else { $module_title = ''; } }
else { $module_title = ' title="'.$module_value['title'].'"'; }
if ((isset($module_value['required'])) && ($module_value['required'] == 'yes')) { echo '<label'.$module_title.'><input style="margin-left: 2em;" type="checkbox" name="'.$module_name.'" id="'.$module_name.'" value="yes" checked="checked" disabled="disabled" /> '.$module_value['name'].'<br /></label>'; }
else { echo '<label'.$module_title.'><input style="margin-left: 2em;" type="checkbox" name="'.$module_name.'" id="'.$module_name.'" value="yes"'.(in_array($module_key, $page_undisplayed_modules) ? '' : ' checked="checked"').' /> '.$module_value['name'].'<br /></label>'; } } }
if (!strstr($_GET['page'], 'back-office')) { echo '</div>'; } } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="hidden" name="submit" value="true" />
<input type="submit" class="button-secondary" name="update_back_office_options" value="<?php _e('Update', 'audiobooks-authors-and-narrators'); ?>" onclick="this.title = '<?php _e('Update', 'audiobooks-authors-and-narrators'); ?>';" /></td></tr>
</tbody></table>
</div></div>
<?php if (!strstr($_GET['page'], 'back-office')) { ?>
<script type="text/javascript">
<?php foreach ($modules[$page_slug] as $key => $value) { echo "document.getElementById('".$key."-submodules').style.display = 'none';\n"; } ?>
</script>
<?php } }


function audiobooks_authors_and_narrators_pages_search_field($type, $searchby, $searchby_options) { ?>
<p class="search-box" style="float: right;"><label><?php _e(ucfirst($type).' by', 'audiobooks-authors-and-narrators'); ?> <select name="<?php echo $type; ?>by" id="<?php echo $type; ?>by">
<?php if ($type == 'search') { echo '<option value=""'.($searchby == '' ? ' selected="selected"' : '').'>'.__('all fields', 'audiobooks-authors-and-narrators').'</option>'; } ?>
<?php foreach ($searchby_options as $key => $value) {
echo '<option value="'.$key.'"'.($searchby == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></label><br />
<input type="text" name="s" id="s" size="40" value="<?php if (isset($_GET['s'])) { echo $_GET['s']; } ?>" />
<input type="submit" class="button" name="submit" id="<?php echo $type; ?>-submit" value="<?php _e(ucfirst($type), 'audiobooks-authors-and-narrators'); ?>" /></p>
<?php }


function audiobooks_authors_and_narrators_pages_summary($back_office_options) {
if ($_GET['page'] == 'audiobooks-authors-and-narrators') { $page_slug = 'options'; }
else { $page_slug = str_replace('-', '_', str_replace('audiobooks-authors-and-narrators-', '', $_GET['page'])); }
if ($back_office_options[$page_slug.'_page_summary_displayed'] == 'yes') {
include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'admin-pages.php';
$modules = $modules[$page_slug];
$undisplayed_modules = (array) $back_office_options[$page_slug.'_page_undisplayed_modules'];
$list = ''; foreach ($modules as $key => $value) {
if (!in_array($key, $undisplayed_modules)) { $list .= '<li>&nbsp;| <a href="#'.$key.'">'.$value['name'].'</a></li>'; } }
if ($list != '') { echo '<ul class="subsubsub" style="float: none; margin-bottom: 1em; white-space: normal;"><li>'.substr($list, 12).'</ul>'; } } }


function audiobooks_authors_and_narrators_pages_title($back_office_options) {
if ($back_office_options['title_displayed'] == 'yes') {
echo '<h2 style="font-size: 1.75em;">'.$back_office_options['title'].'</h2>'; } }


function audiobooks_authors_and_narrators_pages_top($back_office_options) {
audiobooks_authors_and_narrators_pages_title($back_office_options);
echo '<div class="clear"></div>'; }


function audiobooks_authors_and_narrators_users_roles() {
$wp_roles = new WP_Roles();
$roles = $wp_roles->get_names();
foreach ($roles as $role => $name) { $roles[$role] = translate_user_role($name); }
return $roles; }


function update_audiobooks_authors_and_narrators_back_office($back_office_options, $page) {
include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'admin-pages.php';
if ((!isset($_POST[$page.'_page_summary_displayed'])) || ($_POST[$page.'_page_summary_displayed'] != 'yes')) { $_POST[$page.'_page_summary_displayed'] = 'no'; }
$_POST[$page.'_page_undisplayed_modules'] = array();
foreach ($modules[$page] as $key => $value) {
if (((!isset($_POST[$page.'_page_'.str_replace('-', '_', $key).'_module_displayed'])) || ($_POST[$page.'_page_'.str_replace('-', '_', $key).'_module_displayed'] != 'yes'))
 && ((!isset($value['required'])) || ($value['required'] != 'yes'))) { $_POST[$page.'_page_undisplayed_modules'][] = $key; }
if (isset($value['modules'])) { foreach ($value['modules'] as $module_key => $module_value) {
if (((!isset($_POST[$page.'_page_'.str_replace('-', '_', $module_key).'_module_displayed'])) || ($_POST[$page.'_page_'.str_replace('-', '_', $module_key).'_module_displayed'] != 'yes'))
 && ((!isset($module_value['required'])) || ($module_value['required'] != 'yes'))) { $_POST[$page.'_page_undisplayed_modules'][] = $module_key; } } } }
if (!strstr($_GET['page'], 'back-office')) {
foreach (array('summary_displayed', 'undisplayed_modules') as $option) {
if (isset($_POST[$page.'_page_'.$option])) { $back_office_options[$page.'_page_'.$option] = $_POST[$page.'_page_'.$option]; } }
update_option('audiobooks_authors_and_narrators_back_office', $back_office_options);
return $back_office_options; } }


function audiobooks_authors_and_narrators_pages_date_picker($start_date, $end_date) {
echo '<p style="margin: 0 0 1em 0; float: left;">
<input type="hidden" name="old_start_date" value="'.$start_date.'" /><label><strong>'.__('Start', 'audiobooks-authors-and-narrators').'</strong>
<input class="date-pick" style="margin: 0.5em;" type="text" name="start_date" id="start_date" size="20" value="'.$start_date.'" placeholder="'.$start_date.'" /></label>
<input type="hidden" name="old_end_date" value="'.$end_date.'" /><label style="margin-left: 3em;"><strong>'.__('End', 'audiobooks-authors-and-narrators').'</strong>
<input class="date-pick" style="margin: 0.5em;" type="text" name="end_date" id="end_date" size="20" value="'.$end_date.'" placeholder="'.$end_date.'" /></label>
<input style="margin-left: 3em; vertical-align: middle;" type="submit" class="button-secondary" name="submit" value="'.__('Display', 'audiobooks-authors-and-narrators').'" /></p>
<div class="clear"></div>'; }


function audiobooks_authors_and_narrators_date_picker_js() { ?>
<script type="text/javascript">
Date.dayNames = ['<?php _e('Sunday', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('Monday', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('Tuesday', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('Wednesday', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('Thursday', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('Friday', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('Saturday', 'audiobooks-authors-and-narrators'); ?>'];
Date.abbrDayNames = ['<?php _e('Sun', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('Mon', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('Tue', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('Wed', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('Thu', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('Fri', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('Sat', 'audiobooks-authors-and-narrators'); ?>'];
Date.monthNames = ['<?php _e('January', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('February', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('March', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('April', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('May', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('June', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('July', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('August', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('September', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('October', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('November', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('December', 'audiobooks-authors-and-narrators'); ?>'];
Date.abbrMonthNames = ['<?php _e('Jan', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('Feb', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('Mar', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('Apr', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('May', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('Jun', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('Jul', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('Aug', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('Sep', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('Oct', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('Nov', 'audiobooks-authors-and-narrators'); ?>', '<?php _e('Dec', 'audiobooks-authors-and-narrators'); ?>'];
jQuery.dpText = {
TEXT_PREV_YEAR : '<?php _e('Previous year', 'audiobooks-authors-and-narrators'); ?>',
TEXT_PREV_MONTH : '<?php _e('Previous month', 'audiobooks-authors-and-narrators'); ?>',
TEXT_NEXT_YEAR : '<?php _e('Next year', 'audiobooks-authors-and-narrators'); ?>',
TEXT_NEXT_MONTH : '<?php _e('Next month', 'audiobooks-authors-and-narrators'); ?>',
TEXT_CLOSE : '<?php _e('Close', 'audiobooks-authors-and-narrators'); ?>',
TEXT_CHOOSE_DATE : '<?php _e('Choose a date', 'audiobooks-authors-and-narrators'); ?>',
DATE_PICKER_ALT : '<?php _e('Date', 'audiobooks-authors-and-narrators'); ?>',
DATE_PICKER_URL : '<?php echo AUDIOBOOKS_AUTHORS_AND_NARRATORS_URL; ?>images/date-picker.png',
HEADER_FORMAT : 'mmmm yyyy'
}; jQuery(function(){ jQuery('.date-pick').datePicker({startDate:'2000-01-01'}); });
</script>
<?php }


function audiobooks_authors_and_narrators_format_placeholder($string) {
return str_replace(array("\\r\\n", "\\n", "\\r", "
"), ' ', $string); }


if ((is_admin()) && ($_GET['page'] != 'audiobooks-authors-and-narrators-back-office')
 && ((!isset($_GET['action'])) || (!in_array($_GET['action'], array('delete', 'uninstall', 'reset'))))) {
wp_enqueue_script('jquery');
if ($_GET['page'] != 'audiobooks-authors-and-narrators') {
wp_enqueue_style('audiobooks-authors-and-narrators-date-picker', AUDIOBOOKS_AUTHORS_AND_NARRATORS_URL.'libraries/date-picker.css', array(), AUDIOBOOKS_AUTHORS_AND_NARRATORS_VERSION, 'all');
wp_enqueue_script('audiobooks-authors-and-narrators-date-picker', AUDIOBOOKS_AUTHORS_AND_NARRATORS_URL.'libraries/date-picker.js', array('jquery'), AUDIOBOOKS_AUTHORS_AND_NARRATORS_VERSION, true);
add_action('admin_footer', 'audiobooks_authors_and_narrators_date_picker_js'); } }