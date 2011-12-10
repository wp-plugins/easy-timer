<?php $options = get_option('optin_manager_back_office');
include 'admin-pages.php';
$max_links = count($admin_links);
$max_menu_items = count($admin_pages);

if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
include 'initial-options.php';
$_POST = array_map('html_entity_decode', $_POST);
$_POST = array_map('stripslashes', $_POST);
foreach (array(
'links_displayed',
'menu_displayed',
'title_displayed') as $field) { if ($_POST[$field] != 'yes') { $_POST[$field] = 'no'; } }

foreach (array(
'back_office',
'form',
'form_category',
'options',
'prospect') as $page) {
if ($_POST[$page.'_page_summary_displayed'] != 'yes') { $_POST[$page.'_page_summary_displayed'] = 'no'; }
$_POST[$page.'_page_undisplayed_modules'] = array();
foreach ($modules[$page] as $key => $value) {
if (($_POST[$page.'_page_'.str_replace('-', '_', $key).'_module_displayed'] != 'yes') && ($value['required'] != 'yes')) { $_POST[$page.'_page_undisplayed_modules'][] = $key; }
if (is_array($value['modules'])) { foreach ($value['modules'] as $module_key => $module_value) {
if (($_POST[$page.'_page_'.str_replace('-', '_', $module_key).'_module_displayed'] != 'yes') && ($module_value['required'] != 'yes')) { $_POST[$page.'_page_undisplayed_modules'][] = $module_key; } } } } }
if (isset($_POST['reset_links'])) {
foreach (array('links', 'displayed_links') as $field) { $_POST[$field] = $initial_options['back_office'][$field]; } }
else {
$_POST['displayed_links'] = array();
for ($i = 0; $i < $max_links; $i++) {
$_POST['links'][$i] = $_POST['link'.$i];
if ($_POST['link'.$i.'_displayed'] == 'yes') { $_POST['displayed_links'][] = $i; } } }
if (isset($_POST['reset_menu_items'])) {
foreach (array('menu_items', 'menu_displayed_items') as $field) { $_POST[$field] = $initial_options['back_office'][$field]; } }
else {
$_POST['menu_displayed_items'] = array();
for ($i = 0; $i < $max_menu_items; $i++) {
$_POST['menu_items'][$i] = $_POST['menu_item'.$i];
if ($_POST['menu_item'.$i.'_displayed'] == 'yes') { $_POST['menu_displayed_items'][] = $i; } } }
foreach ($statistics_columns as $key => $value) {
if (($_POST['statistics_page_'.$key.'_column_displayed'] != 'yes') && ($value['required'] != 'yes')) { $_POST['statistics_page_undisplayed_columns'][] = $key; } }
foreach ($statistics_rows as $key => $value) {
if (($_POST['statistics_page_'.$key.'_row_displayed'] != 'yes') && ($value['required'] != 'yes')) { $_POST['statistics_page_undisplayed_rows'][] = $key; } }
foreach ($initial_options['back_office'] as $key => $value) {
if ($_POST[$key] != '') { $options[$key] = $_POST[$key]; }
else { $options[$key] = $value; } }
update_option('optin_manager_back_office', $options); }

$undisplayed_modules = (array) $options['back_office_page_undisplayed_modules']; ?>

<div class="wrap">
<div id="poststuff">
<?php optin_manager_pages_top($options); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>'; } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php optin_manager_pages_menu($options); ?>
<div class="clear"></div>
<?php optin_manager_pages_summary($options); ?>

<div class="postbox"<?php if (in_array('top', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="top"><strong><?php echo $modules['back_office']['top']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="title_displayed" id="title_displayed" value="yes"<?php if ($options['title_displayed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Display the title', 'optin-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="title"><?php _e('Title', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="title" id="title" rows="1" cols="25"><?php echo $options['title']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="links_displayed" id="links_displayed" value="yes"<?php if ($options['links_displayed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Display the links', 'optin-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><?php _e('Links', 'optin-manager'); ?></strong></th>
<td><input type="hidden" name="submit" value="true" /><input type="submit" class="button-secondary" name="reset_links" value="<?php _e('Reset the links', 'optin-manager'); ?>" /><br />
<?php $displayed_links = (array) $options['displayed_links'];
for ($i = 0; $i < $max_links; $i++) {
echo '<label>'.__('Link', 'optin-manager').' '.($i + 1).' <select'.($i < 9 ? ' style="margin-left: 0.75em;"': '').' name="link'.$i.'" id="link'.$i.'">';
foreach ($admin_links as $key => $value) { echo '<option value="'.$key.'"'.($options['links'][$i] == $key ? ' selected="selected"' : '').'>'.$value['name'].'</option>'."\n"; }
echo '</select></label>
<label><input type="checkbox" name="link'.$i.'_displayed" id="link'.$i.'_displayed" value="yes"'.(!in_array($i, $displayed_links) ? '' : ' checked="checked"').' /> '.__('Display', 'optin-manager').'<br /></label>'; } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('menu', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="menu"><strong><?php echo $modules['back_office']['menu']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="menu_displayed" id="menu_displayed" value="yes"<?php if ($options['menu_displayed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Display the menu', 'optin-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><?php _e('Items', 'optin-manager'); ?></strong></th>
<td><input type="hidden" name="submit" value="true" /><input type="submit" class="button-secondary" name="reset_menu_items" value="<?php _e('Reset the items', 'optin-manager'); ?>" /><br />
<?php $menu_displayed_items = (array) $options['menu_displayed_items'];
for ($i = 0; $i < $max_menu_items; $i++) {
echo '<label>'.__('Item', 'optin-manager').' '.($i + 1).' <select'.($i < 9 ? ' style="margin-left: 0.75em;"': '').' name="menu_item'.$i.'" id="menu_item'.$i.'">';
foreach ($admin_pages as $key => $value) { echo '<option value="'.$key.'"'.($options['menu_items'][$i] == $key ? ' selected="selected"' : '').'>'.$value['menu_title'].'</option>'."\n"; }
echo '</select></label>
<label><input type="checkbox" name="menu_item'.$i.'_displayed" id="menu_item'.$i.'_displayed" value="yes"'.(!in_array($i, $menu_displayed_items) ? '' : ' checked="checked"').' /> '.__('Display', 'optin-manager').'<br /></label>'; } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<?php foreach (array('options-page', 'form-page', 'form-category-page', 'prospect-page') as $module) { optin_manager_pages_module($options, $module, $undisplayed_modules); } ?>

<div class="postbox"<?php if (in_array('statistics-page', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="statistics-page"><strong><?php echo $modules['back_office']['statistics-page']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><?php _e('Columns displayed', 'optin-manager'); ?></strong></th>
<td><?php foreach ($statistics_columns as $key => $value) {
$name = 'statistics_page_'.$key.'_column_displayed';
$undisplayed_columns = (array) $options['statistics_page_undisplayed_columns'];
if ($value['required'] == 'yes') { echo '<input type="checkbox" name="'.$name.'" id="'.$name.'" value="yes" checked="checked" disabled="disabled" /> '.$value['name'].'<br />'; }
else { echo '<label><input type="checkbox" name="'.$name.'" id="'.$name.'" value="yes"'.(in_array($key, $undisplayed_columns) ? '' : ' checked="checked"').' /> '.$value['name'].'<br /></label>'; } } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><?php _e('Rows displayed', 'optin-manager'); ?></strong></th>
<td><?php foreach ($statistics_rows as $key => $value) {
$name = 'statistics_page_'.$key.'_row_displayed';
$undisplayed_rows = (array) $options['statistics_page_undisplayed_rows'];
if ($value['required'] == 'yes') { echo '<input type="checkbox" name="'.$name.'" id="'.$name.'" value="yes" checked="checked" disabled="disabled" /> '.$value['name'].'<br />'; }
else { echo '<label><input type="checkbox" name="'.$name.'" id="'.$name.'" value="yes"'.(in_array($key, $undisplayed_rows) ? '' : ' checked="checked"').' /> '.$value['name'].'<br /></label>'; } } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<?php optin_manager_pages_module($options, 'back-office-page', $undisplayed_modules); ?>
<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes'); ?>" /></p>
</form>
</div>
</div>