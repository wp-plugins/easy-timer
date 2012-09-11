<?php $options = get_option('commerce_manager_back_office');
include 'admin-pages.php';
$max_links = count($admin_links);
$max_menu_items = count($admin_pages);

if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!commerce_manager_user_can($options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'commerce-manager'); }
else {
include 'initial-options.php';
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = stripslashes(html_entity_decode(str_replace('&nbsp;', ' ', $value))); } }
foreach (array(
'custom_icon_used',
'links_displayed',
'menu_displayed',
'title_displayed') as $field) { if ($_POST[$field] != 'yes') { $_POST[$field] = 'no'; } }
foreach (array(
'back_office',
'client',
'clients_accounts',
'form',
'form_category',
'front_office',
'options',
'order',
'product',
'product_category',
'recurring_payment') as $page) { update_commerce_manager_back_office($options, $page); }
$_POST['minimum_roles'] = array();
foreach (array('manage', 'view') as $key) { $_POST['minimum_roles'][$key] = $_POST[$key.'_minimum_role']; }
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
$_POST['statistics_page_undisplayed_columns'] = array();
foreach ($statistics_columns as $key => $value) {
if (($_POST['statistics_page_'.$key.'_column_displayed'] != 'yes') && ($value['required'] != 'yes')) { $_POST['statistics_page_undisplayed_columns'][] = $key; } }
$_POST['statistics_page_undisplayed_rows'] = array();
foreach ($statistics_rows as $key => $value) {
if (($_POST['statistics_page_'.$key.'_row_displayed'] != 'yes') && ($value['required'] != 'yes')) { $_POST['statistics_page_undisplayed_rows'][] = $key; } }
foreach ($initial_options['back_office'] as $key => $value) {
if ($_POST[$key] != '') { $options[$key] = $_POST[$key]; }
else { $options[$key] = $value; } }
update_option('commerce_manager_back_office', $options); } }

$undisplayed_modules = (array) $options['back_office_page_undisplayed_modules']; ?>

<div class="wrap">
<div id="poststuff">
<?php commerce_manager_pages_top($options); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>'; } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php commerce_manager_pages_menu($options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<?php commerce_manager_pages_summary($options); ?>

<div class="postbox" id="capabilities-module"<?php if (in_array('capabilities', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="capabilities"><strong><?php echo $modules['back_office']['capabilities']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="view_minimum_role"><?php _e('Access', 'commerce-manager'); ?></label></strong></th>
<td><select name="view_minimum_role" id="view_minimum_role">
<?php foreach ($roles as $key => $value) {
echo '<option value="'.$key.'"'.($options['minimum_roles']['view'] == $key ? ' selected="selected"' : '').'>'.$value['name'].'</option>'."\n"; } ?>
</select> <span class="description"><?php _e('Minimum role to access the back office', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="manage_minimum_role"><?php _e('Management', 'commerce-manager'); ?></label></strong></th>
<td><select name="manage_minimum_role" id="manage_minimum_role">
<?php foreach ($roles as $key => $value) {
echo '<option value="'.$key.'"'.($options['minimum_roles']['manage'] == $key ? ' selected="selected"' : '').'>'.$value['name'].'</option>'."\n"; } ?>
</select> <span class="description"><?php _e('Minimum role to change options and add, edit or delete items', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="icon-module"<?php if (in_array('icon', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="icon"><strong><?php echo $modules['back_office']['icon']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="custom_icon_used" id="custom_icon_used" value="yes"<?php if ($options['custom_icon_used'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Use a custom icon', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="custom_icon_url"><?php _e('Icon URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="custom_icon_url" id="custom_icon_url" rows="1" cols="75"><?php echo $options['custom_icon_url']; ?></textarea> 
<a style="vertical-align: 25%;" href="<?php echo htmlspecialchars(format_url(do_shortcode($options['custom_icon_url']))); ?>"><?php _e('Link', 'commerce-manager'); ?></a></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="top-module"<?php if (in_array('top', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="top"><strong><?php echo $modules['back_office']['top']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="title_displayed" id="title_displayed" value="yes"<?php if ($options['title_displayed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Display the title', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="title"><?php _e('Title', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="title" id="title" rows="1" cols="25"><?php echo $options['title']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="links_displayed" id="links_displayed" value="yes"<?php if ($options['links_displayed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Display the links', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><?php _e('Links', 'commerce-manager'); ?></strong></th>
<td><input type="hidden" name="submit" value="true" /><input type="submit" class="button-secondary" name="reset_links" value="<?php _e('Reset the links', 'commerce-manager'); ?>" /><br />
<?php $displayed_links = (array) $options['displayed_links'];
for ($i = 0; $i < $max_links; $i++) {
echo '<label>'.__('Link', 'commerce-manager').' '.($i + 1).($i < 9 ? '&nbsp;&nbsp;': '').' <select name="link'.$i.'" id="link'.$i.'">';
foreach ($admin_links as $key => $value) { echo '<option value="'.$key.'"'.($options['links'][$i] == $key ? ' selected="selected"' : '').'>'.$value['name'].'</option>'."\n"; }
echo '</select></label>
<label><input type="checkbox" name="link'.$i.'_displayed" id="link'.$i.'_displayed" value="yes"'.(!in_array($i, $displayed_links) ? '' : ' checked="checked"').' /> '.__('Display', 'commerce-manager').'</label><br />'; } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="menu-module"<?php if (in_array('menu', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="menu"><strong><?php echo $modules['back_office']['menu']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="menu_displayed" id="menu_displayed" value="yes"<?php if ($options['menu_displayed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Display the menu', 'commerce-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><?php _e('Pages', 'commerce-manager'); ?></strong></th>
<td><input type="hidden" name="submit" value="true" /><input type="submit" class="button-secondary" name="reset_menu_items" value="<?php _e('Reset the pages', 'commerce-manager'); ?>" /><br />
<?php $menu_displayed_items = (array) $options['menu_displayed_items'];
for ($i = 0; $i < $max_menu_items; $i++) {
echo '<label>'.__('Page', 'commerce-manager').' '.($i + 1).($i < 9 ? '&nbsp;&nbsp;': '').' <select name="menu_item'.$i.'" id="menu_item'.$i.'">';
foreach ($admin_pages as $key => $value) { echo '<option value="'.$key.'"'.($options['menu_items'][$i] == $key ? ' selected="selected"' : '').'>'.$value['menu_title'].'</option>'."\n"; }
echo '</select></label>
<label><input type="checkbox" name="menu_item'.$i.'_displayed" id="menu_item'.$i.'_displayed" value="yes"'.(!in_array($i, $menu_displayed_items) ? '' : ' checked="checked"').' /> '.__('Display', 'commerce-manager').'</label><br />'; } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<?php foreach (array(
'options-page',
'clients-accounts-page',
'product-page',
'product-category-page',
'form-page',
'form-category-page',
'client-page',
'order-page',
'recurring-payment-page') as $module) { commerce_manager_pages_module($options, $module, $undisplayed_modules); } ?>

<div class="postbox" id="statistics-page-module"<?php if (in_array('statistics-page', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="statistics-page"><strong><?php echo $modules['back_office']['statistics-page']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><?php _e('Columns displayed', 'commerce-manager'); ?></strong></th>
<td><?php foreach ($statistics_columns as $key => $value) {
$name = 'statistics_page_'.$key.'_column_displayed';
$undisplayed_columns = (array) $options['statistics_page_undisplayed_columns'];
if ($value['required'] == 'yes') { echo '<input type="checkbox" name="'.$name.'" id="'.$name.'" value="yes" checked="checked" disabled="disabled" /> '.$value['name'].'<br />'; }
else { echo '<label><input type="checkbox" name="'.$name.'" id="'.$name.'" value="yes"'.(in_array($key, $undisplayed_columns) ? '' : ' checked="checked"').' /> '.$value['name'].'</label><br />'; } } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><?php _e('Rows displayed', 'commerce-manager'); ?></strong></th>
<td><?php foreach ($statistics_rows as $key => $value) {
$name = 'statistics_page_'.$key.'_row_displayed';
$undisplayed_rows = (array) $options['statistics_page_undisplayed_rows'];
if ($value['required'] == 'yes') { echo '<input type="checkbox" name="'.$name.'" id="'.$name.'" value="yes" checked="checked" disabled="disabled" /> '.$value['name'].'<br />'; }
else { echo '<label><input type="checkbox" name="'.$name.'" id="'.$name.'" value="yes"'.(in_array($key, $undisplayed_rows) ? '' : ' checked="checked"').' /> '.$value['name'].'</label><br />'; } } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<?php foreach (array('front-office-page', 'back-office-page') as $module) { commerce_manager_pages_module($options, $module, $undisplayed_modules); } ?>
<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes'); ?>" /></p>
</form>
</div>
</div>