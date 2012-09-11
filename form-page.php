<?php global $wpdb;
$back_office_options = get_option('optin_manager_back_office');
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$is_category = (strstr($_GET['page'], 'category'));
if ($is_category) { $admin_page = 'form_category'; $table_slug = 'forms_categories'; $attribute = 'category'; }
else { $admin_page = 'form'; $table_slug = 'forms'; $attribute = 'id'; }

if ((isset($_GET['id'])) && ($_GET['action'] == 'delete')) {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!optin_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'optin-manager'); }
else {
if ($is_category) {
$category = $wpdb->get_row("SELECT category_id FROM ".$wpdb->prefix."optin_manager_forms_categories WHERE id = ".$_GET['id'], OBJECT);
foreach (array('forms', 'forms_categories') as $table) {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."optin_manager_".$table." SET category_id = ".$category->category_id." WHERE category_id = ".$_GET['id']); } }
$results = $wpdb->query("DELETE FROM ".$wpdb->prefix."optin_manager_".$table_slug." WHERE id = ".$_GET['id']); } } ?>
<div class="wrap">
<div id="poststuff">
<?php optin_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) {
echo '<div class="updated"><p><strong>'.($is_category ? __('Category deleted.', 'optin-manager') : __('Form deleted.', 'optin-manager')).'</strong></p></div>
<script type="text/javascript">setTimeout(\'window.location = "admin.php?page=optin-manager-forms'.($is_category ? '-categories' : '').'"\', 2000);</script>'; } ?>
<?php optin_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php echo ($is_category ? __('Do you really want to permanently delete this category?', 'optin-manager') : __('Do you really want to permanently delete this form?', 'optin-manager')); ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'optin-manager'); ?>" />
</div>
<div class="clear"></div>
</form><?php } ?>
</div>
</div><?php }

else {
include 'admin-pages.php';
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!optin_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'optin-manager'); }
else {
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = html_entity_decode(str_replace('&nbsp;', ' ', $value)); } }
$back_office_options = update_optin_manager_back_office($back_office_options, $admin_page);

if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
foreach (array(
'commission_amount',
'commission2_amount') as $field) { $_POST[$field] = str_replace(array('?', ',', ';'), '.', $_POST[$field]); }
foreach (array(
'displays_count',
'prospects_count') as $field) {
$_POST[$field] = (int) $_POST[$field];
if ($_POST[$field] < 0) { $_POST[$field] = 0; } }
$keywords = explode(',', $_POST['keywords']);
for ($i = 0; $i < count($keywords); $i++) { $keywords[$i] = strtolower(trim($keywords[$i])); }
sort($keywords);
foreach ($keywords as $keyword) { $keywords_list .= $keyword.', '; }
$_POST['keywords'] = substr($keywords_list, 0, -2);
switch ($_POST['maximum_prospects_quantity']) { case 'i' : case 'infinite' : case 'u' : $_POST['maximum_prospects_quantity'] = 'unlimited'; }
$members_areas = array_unique(preg_split('#[^0-9]#', $_POST['prospect_members_areas'], 0, PREG_SPLIT_NO_EMPTY));
sort($members_areas, SORT_NUMERIC);
foreach ($members_areas as $member_area) { $members_areas_list .= $member_area.', '; }
$_POST['prospect_members_areas'] = substr($members_areas_list, 0, -2);
if ($_POST['date'] == '') {
$_POST['date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['date_utc'] = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['date'], 0, PREG_SPLIT_NO_EMPTY);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['date'] = date('Y-m-d H:i:s', $time);
$_POST['date_utc'] = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); }
if (!$is_category) {
if ($_POST['displays_count'] < $_POST['prospects_count']) { $_POST['displays_count'] = $_POST['prospects_count']; } }

if (!isset($_GET['id'])) {
if ($_POST['name'] == '') { $error .= ' '.__('Please fill out the required fields.', 'optin-manager'); }
elseif ($is_category) {
$result = $wpdb->get_results("SELECT name FROM ".$wpdb->prefix."optin_manager_forms_categories WHERE name = '".$_POST['name']."'", OBJECT);
if ($result) { $error .= ' '.__('This name is not available.', 'optin-manager'); } }
if ($error == '') {
if ($is_category) { $result = false; }
else { $result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."optin_manager_forms WHERE name = '".$_POST['name']."' AND date = '".$_POST['date']."'", OBJECT); }
if (!$result) {
$updated = true;
include 'tables.php';
$sql = optin_sql_array($tables[$table_slug], $_POST);
foreach ($tables[$table_slug] as $key => $value) { if ($key != 'id') { $keys_list .= $key.","; $values_list .= $sql[$key].","; } }
$results = $wpdb->query("INSERT INTO ".$wpdb->prefix."optin_manager_".$table_slug." (".substr($keys_list, 0, -1).") VALUES(".substr($values_list, 0, -1).")"); } } }

if (isset($_GET['id'])) {
$updated = true;
if ((isset($_POST['count_prospects'])) || (isset($_POST['count_prospects_of_all_forms']))) {
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."optin_manager_prospects WHERE form_id = ".$_GET['id'], OBJECT);
$_POST['prospects_count'] = (int) $row->total;
if ($_POST['displays_count'] < $_POST['prospects_count']) { $_POST['displays_count'] = $_POST['prospects_count']; }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."optin_manager_forms SET
	displays_count = ".$_POST['displays_count'].",
	prospects_count = ".$_POST['prospects_count']." WHERE id = ".$_GET['id']); }
if (isset($_POST['count_prospects_of_all_forms'])) {
$forms = $wpdb->get_results("SELECT id, displays_count FROM ".$wpdb->prefix."optin_manager_forms WHERE id != ".$_GET['id'], OBJECT);
if ($forms) { foreach ($forms as $form) {
$displays_count = $form->displays_count;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."optin_manager_prospects WHERE form_id = ".$form->id, OBJECT);
$prospects_count = (int) $row->total;
if ($displays_count < $prospects_count) { $displays_count = $prospects_count; }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."optin_manager_forms SET
	displays_count = ".$displays_count.",
	prospects_count = ".$prospects_count." WHERE id = ".$form->id); } } }
if ($_POST['name'] != '') {
if (!$is_category) { $results = $wpdb->query("UPDATE ".$wpdb->prefix."optin_manager_".$table_slug." SET name = '".$_POST['name']."' WHERE id = ".$_GET['id']); }
else {
$result = $wpdb->get_results("SELECT name FROM ".$wpdb->prefix."optin_manager_forms_categories WHERE name = '".$_POST['name']."' AND id != ".$_GET['id'], OBJECT);
if ($result) { $error .= ' '.__('This name is not available.', 'optin-manager'); }
else { $results = $wpdb->query("UPDATE ".$wpdb->prefix."optin_manager_forms_categories SET name = '".$_POST['name']."' WHERE id = ".$_GET['id']); } } }
include 'tables.php';
$sql = optin_sql_array($tables[$table_slug], $_POST);
foreach ($tables[$table_slug] as $key => $value) { switch ($key) {
case 'id': case 'name': break;
default: $list .= $key." = ".$sql[$key].","; } }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."optin_manager_".$table_slug." SET ".substr($list, 0, -1)." WHERE id = ".$_GET['id']); } } }

if (isset($_GET['id'])) {
$item_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_".$table_slug." WHERE id = ".$_GET['id'], OBJECT);
if ($item_data) { foreach ($item_data as $key => $value) { $_POST[$key] = $value; } }
elseif (!headers_sent()) { header('Location: admin.php?page=optin-manager-forms'.($is_category ? '-categories' : '')); exit(); }
else { echo '<script type="text/javascript">window.location = "admin.php?page=optin-manager-forms'.($is_category ? '-categories' : '').'";</script>'; } }

foreach ($_POST as $key => $value) {
if (is_string($value)) {
$_POST[$key] = str_replace('&amp;amp;', '&amp;', htmlspecialchars(stripslashes($value)));
if ($value == '0000-00-00 00:00:00') { $_POST[$key] = ''; } } }
$undisplayed_modules = (array) $back_office_options[$admin_page.'_page_undisplayed_modules'];
if (function_exists('commerce_data')) { $currency_code = commerce_data('currency_code'); }
else { $commerce_manager_options = (array) get_option('commerce_manager');
$currency_code = do_shortcode($commerce_manager_options['currency_code']); } ?>

<div class="wrap">
<div id="poststuff">
<?php optin_manager_pages_top($back_office_options); ?>
<?php if ($updated) {
echo '<div class="updated"><p><strong>'.(isset($_GET['id']) ? ($is_category ? __('Category updated.', 'optin-manager') : __('Form updated.', 'optin-manager')) : ($is_category ? __('Category saved.', 'optin-manager') : __('Form saved.', 'optin-manager'))).'</strong></p></div>
'.(isset($_GET['id']) ? '' : '<script type="text/javascript">setTimeout(\'window.location = "admin.php?page=optin-manager-forms'.($is_category ? '-categories' : '').'"\', 2000);</script>'); } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php optin_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<p class="description"><?php _e('Fields marked with * are required.', 'optin-manager'); ?> 
<?php if ($_POST['category_id'] > 0) { _e('You can apply the default option of the category by leaving the corresponding field blank.', 'optin-manager'); } ?></p>
<?php optin_manager_pages_summary($back_office_options); ?>

<div class="postbox" id="general-informations-module"<?php if (in_array('general-informations', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="general-informations"><strong><?php echo $modules[$admin_page]['general-informations']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<?php if ($_POST['category_id'] > 0) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=optin-manager-form-category&amp;id=<?php echo $_POST['category_id']; ?>#general-informations">
<?php ($is_category ? _e('Click here to configure the default options of the parent category.', 'optin-manager') : _e('Click here to configure the default options of the category.', 'optin-manager')); ?></a></span></td></tr>
<?php } ?>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="id">'.__('ID', 'optin-manager').'</label></strong></th>
<td><input type="text" name="id" id="id" size="10" value="'.$_GET['id'].'" disabled="disabled" /> <span class="description">'.__('The ID can not be changed.', 'optin-manager').'</span></td></tr>'; } ?>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."optin_manager_forms_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="category_id"><?php echo ($is_category ? __('Parent category', 'optin-manager') : __('Category', 'optin-manager')); ?></label></strong></th>
<td><select name="category_id" id="category_id">
<option value="0"<?php if ($_POST['category_id'] == 0) { echo ' selected="selected"'; } ?>><?php _e('None ', 'optin-manager'); ?></option>
<?php foreach ($categories as $category) {
if ((!$is_category) || (!in_array($_GET['id'], optin_forms_categories_list($category->id)))) {
echo '<option value="'.$category->id.'"'.($_POST['category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } } ?>
</select>
<span class="description"><?php ($is_category ? _e('The options of this category will apply by default to the category.', 'optin-manager') : _e('The options of this category will apply by default to the form.', 'optin-manager')); ?></span>
<?php if ($_POST['category_id'] > 0) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=optin-manager-form-category&amp;id='.$_POST['category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=optin-manager-form-category&amp;id='.$_POST['category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['name'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="name"><?php _e('Name', 'optin-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="name" id="name" rows="1" cols="50"><?php echo $_POST['name']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="description"><?php _e('Description', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="description" id="description" rows="1" cols="75"><?php echo $_POST['description']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="keywords"><?php _e('Keywords', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="keywords" id="keywords" rows="1" cols="75"><?php echo $_POST['keywords']; ?></textarea><br />
<span class="description"><?php _e('Separate the keywords with commas.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="gift_download_url"><?php _e('Gift download URL', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="gift_download_url" id="gift_download_url" rows="1" cols="75"><?php echo $_POST['gift_download_url']; ?></textarea> 
<?php $url = htmlspecialchars(optin_form_data(array(0 => 'gift_download_url', 'part' => 1, $attribute => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'optin-manager'); ?></a><?php } ?><br />
<span class="description"><?php _e('(if you want to offer a gift to the prospect)', 'optin-manager'); ?><br />
<?php _e('You can specify several URLs.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#multiple-urls"><?php _e('More informations', 'optin-manager'); ?></a> 
<a href="http://www.kleor-editions.com/optin-manager/documentation/#urls-encryption"><?php _e('How to encrypt a download URL?', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="instructions"><?php _e('Instructions to the prospect', 'optin-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="instructions" id="instructions" rows="9" cols="75"><?php echo $_POST['instructions']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes to display informations about the prospect and the form.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#email-shortcodes"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="date"><?php _e('Creation date', 'optin-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="date" id="date" size="20" value="<?php echo (isset($_POST['date']) ? $_POST['date'] : date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET)); ?>" /></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<?php if (!$is_category) { ?>
<div class="postbox" id="counters-module"<?php if (in_array('counters', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="counters"><strong><?php echo $modules[$admin_page]['counters']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="displays_count"><?php _e('Displays count', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="displays_count" id="displays_count" rows="1" cols="25"><?php echo $_POST['displays_count']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 0.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospects_count"><?php _e('Prospects count', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="prospects_count" id="prospects_count" rows="1" cols="25"><?php echo $_POST['prospects_count']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 0.', 'optin-manager'); ?></span><br />
<?php if ($_POST['prospects_count'] > 0) { echo '<a style="text-decoration: none;" href="admin.php?page=optin-manager-prospects&amp;form_id='.$_GET['id'].'">'.__('Display the prospects', 'optin-manager').'</a>'; } ?>
<?php if (isset($_GET['id'])) { echo '<input type="hidden" name="submit" value="true" />
<input type="submit" class="button-secondary" name="count_prospects" value="'.__('Re-count the prospects', 'optin-manager').'" />
<input type="submit" class="button-secondary" name="count_prospects_of_all_forms" value="'.__('Re-count the prospects of all forms', 'optin-manager').'" />'; } ?></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>
<?php } ?>

<div class="postbox" id="autoresponders-module"<?php if (in_array('autoresponders', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="autoresponders"><strong><?php echo $modules[$admin_page]['autoresponders']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=optin-manager<?php echo ($_POST['category_id'] == 0 ? '#autoresponders' : '-form-category&amp;id='.$_POST['category_id'].'#autoresponders'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'optin-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'optin-manager') : _e('Click here to configure the default options of the category.', 'optin-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="autoresponder"><?php _e('Autoresponder', 'optin-manager'); ?></label></strong></th>
<td><select name="autoresponder" id="autoresponder">
<?php include 'libraries/autoresponders.php';
$autoresponder = do_shortcode($_POST['autoresponder']);
echo '<option value=""'.($autoresponder == '' ? ' selected="selected"' : '').'>'.__('Default option', 'optin-manager').'</option>'."\n";
foreach ($autoresponders as $value) {
echo '<option value="'.$value.'"'.($autoresponder == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="autoresponder_list"><?php _e('List', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="autoresponder_list" id="autoresponder_list" rows="1" cols="50"><?php echo $_POST['autoresponder_list']; ?></textarea><br />
<span class="description"><?php _e('For some autoresponders, you must enter the list ID.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#autoresponders"><?php _e('More informations', 'optin-manager'); ?></a><br />
<?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="form-module"<?php if (in_array('form', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="form"><strong><?php echo $modules[$admin_page]['form']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=optin-manager<?php echo ($_POST['category_id'] == 0 ? '#form' : '-form-category&amp;id='.$_POST['category_id'].'#form'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'optin-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'optin-manager') : _e('Click here to configure the default options of the category.', 'optin-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="code"><?php _e('Code', 'optin-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="code" id="code" rows="15" cols="75"><?php echo $_POST['code']; ?></textarea>
<span class="description"><a href="http://www.kleor-editions.com/optin-manager/documentation/#forms"><?php _e('How to display a form?', 'optin-manager'); ?></a><br />
<a href="http://www.kleor-editions.com/optin-manager/documentation/#forms-creation"><?php _e('How to create a form?', 'optin-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
</tbody></table>
<div id="error-messages-module"<?php if (in_array('error-messages', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="error-messages"><strong><?php echo $modules[$admin_page]['form']['modules']['error-messages']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="unfilled_fields_message"><?php _e('Unfilled required fields', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="unfilled_fields_message" id="unfilled_fields_message" rows="1" cols="75"><?php echo $_POST['unfilled_fields_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="unfilled_field_message"><?php _e('Unfilled required field', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="unfilled_field_message" id="unfilled_field_message" rows="1" cols="75"><?php echo $_POST['unfilled_field_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="invalid_email_address_message"><?php _e('Invalid email address', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="invalid_email_address_message" id="invalid_email_address_message" rows="1" cols="75"><?php echo $_POST['invalid_email_address_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="invalid_captcha_message"><?php _e('Invalid CAPTCHA', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="invalid_captcha_message" id="invalid_captcha_message" rows="1" cols="75"><?php echo $_POST['invalid_captcha_message']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
</tbody></table>
</div>
<?php if (isset($_GET['id'])) { echo '<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr></tbody></table>'; } ?>
</div></div>

<div class="postbox" id="registration-module"<?php if (in_array('registration', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration"><strong><?php echo $modules[$admin_page]['registration']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=optin-manager<?php echo ($_POST['category_id'] == 0 ? '#registration' : '-form-category&amp;id='.$_POST['category_id'].'#registration'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'optin-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'optin-manager') : _e('Click here to configure the default options of the category.', 'optin-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospects_registration_enabled"><?php _e('Save prospects in the database', 'optin-manager'); ?></label></strong></th>
<td><select name="prospects_registration_enabled" id="prospects_registration_enabled">
<option value=""<?php if ($_POST['prospects_registration_enabled'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($_POST['prospects_registration_enabled'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($_POST['prospects_registration_enabled'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
</select>
<span class="description"><?php _e('You can save only the latest prospects to ease your database.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="maximum_prospects_quantity"><?php _e('Maximum prospects quantity', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="maximum_prospects_quantity" id="maximum_prospects_quantity" rows="1" cols="25"><?php echo ($_POST['maximum_prospects_quantity'] == 'unlimited' ? 'i' : $_POST['maximum_prospects_quantity']); ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Enter <em><strong>i</strong></em> for an unlimited quantity.', 'optin-manager'); ?><br />
<?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_url"><?php _e('Registration confirmation URL', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_url" id="registration_confirmation_url" rows="1" cols="75"><?php echo $_POST['registration_confirmation_url']; ?></textarea> 
<?php $url = htmlspecialchars(optin_form_data(array(0 => 'registration_confirmation_url', 'part' => 1, $attribute => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'optin-manager'); ?></a><?php } ?><br />
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospects_initial_status"><?php _e('Prospects initial status', 'optin-manager'); ?></label></strong></th>
<td><select name="prospects_initial_status" id="prospects_initial_status">
<option value=""<?php if ($_POST['prospects_initial_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="active"<?php if ($_POST['prospects_initial_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'optin-manager'); ?></option>
<option value="inactive"<?php if ($_POST['prospects_initial_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'optin-manager'); ?></option>
</select>
<span class="description"><?php _e('Status assigned to prospects upon their registration', 'optin-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="registration-confirmation-email-module"<?php if (in_array('registration-confirmation-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration-confirmation-email"><strong><?php echo $modules[$admin_page]['registration-confirmation-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=optin-manager<?php echo ($_POST['category_id'] == 0 ? '#registration-confirmation-email' : '-form-category&amp;id='.$_POST['category_id'].'#registration-confirmation-email'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'optin-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'optin-manager') : _e('Click here to configure the default options of the category.', 'optin-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_sent"><?php _e('Send a registration confirmation email', 'optin-manager'); ?></label></strong></th>
<td><select name="registration_confirmation_email_sent" id="registration_confirmation_email_sent">
<option value=""<?php if ($_POST['registration_confirmation_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($_POST['registration_confirmation_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($_POST['registration_confirmation_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_sender"><?php _e('Sender', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_sender" id="registration_confirmation_email_sender" rows="1" cols="75"><?php echo $_POST['registration_confirmation_email_sender']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_receiver"><?php _e('Receiver', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_receiver" id="registration_confirmation_email_receiver" rows="1" cols="75"><?php echo $_POST['registration_confirmation_email_receiver']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_subject"><?php _e('Subject', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_subject" id="registration_confirmation_email_subject" rows="1" cols="75"><?php echo $_POST['registration_confirmation_email_subject']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_body"><?php _e('Body', 'optin-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_confirmation_email_body" id="registration_confirmation_email_body" rows="15" cols="75"><?php echo $_POST['registration_confirmation_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the prospect and the form.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#email-shortcodes"><?php _e('More informations', 'optin-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="registration-notification-email-module"<?php if (in_array('registration-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration-notification-email"><strong><?php echo $modules[$admin_page]['registration-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=optin-manager<?php echo ($_POST['category_id'] == 0 ? '#registration-notification-email' : '-form-category&amp;id='.$_POST['category_id'].'#registration-notification-email'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'optin-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'optin-manager') : _e('Click here to configure the default options of the category.', 'optin-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_sent"><?php _e('Send a registration notification email', 'optin-manager'); ?></label></strong></th>
<td><select name="registration_notification_email_sent" id="registration_notification_email_sent">
<option value=""<?php if ($_POST['registration_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($_POST['registration_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($_POST['registration_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_sender"><?php _e('Sender', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_sender" id="registration_notification_email_sender" rows="1" cols="75"><?php echo $_POST['registration_notification_email_sender']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_receiver"><?php _e('Receiver', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_receiver" id="registration_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['registration_notification_email_receiver']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_subject"><?php _e('Subject', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_subject" id="registration_notification_email_subject" rows="1" cols="75"><?php echo $_POST['registration_notification_email_subject']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_body"><?php _e('Body', 'optin-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_notification_email_body" id="registration_notification_email_body" rows="15" cols="75"><?php echo $_POST['registration_notification_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the prospect and the form.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#email-shortcodes"><?php _e('More informations', 'optin-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="registration-as-a-client-module"<?php if (in_array('registration-as-a-client', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration-as-a-client"><strong><?php echo $modules[$admin_page]['registration-as-a-client']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php if (function_exists('commerce_manager_admin_menu')) { ?>
<a href="admin.php?page=<?php echo ($_POST['category_id'] == 0 ? 'optin-manager#registration-as-a-client' : 'optin-manager-form-category&amp;id='.$_POST['category_id'].'#registration-as-a-client'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'optin-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'optin-manager') : _e('Click here to configure the default options of the category.', 'optin-manager'))); ?></a>
<?php } else { _e('To subscribe your prospects as clients, you must have installed and activated <a href="http://www.kleor-editions.com/commerce-manager">Commerce Manager</a>.', 'optin-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_subscribed_as_a_client"><?php _e('Subscribe the prospect as a client', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_subscribed_as_a_client" id="prospect_subscribed_as_a_client">
<option value=""<?php if ($_POST['prospect_subscribed_as_a_client'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($_POST['prospect_subscribed_as_a_client'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($_POST['prospect_subscribed_as_a_client'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/optin-manager/documentation/#registration-as-a-client"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."commerce_manager_clients_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_client_category_id"><?php _e('Category', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_client_category_id" id="prospect_client_category_id">
<option value=""<?php if ($_POST['prospect_client_category_id'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="0"<?php if ($_POST['prospect_client_category_id'] == '0') { echo ' selected="selected"'; } ?>><?php _e('None ', 'optin-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($_POST['prospect_client_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ((function_exists('commerce_manager_admin_menu')) && ($_POST['prospect_client_category_id'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-client-category&amp;id='.$_POST['prospect_client_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-client-category&amp;id='.$_POST['prospect_client_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_client_status"><?php _e('Status', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_client_status" id="prospect_client_status">
<option value=""<?php if ($_POST['prospect_client_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="active"<?php if ($_POST['prospect_client_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'optin-manager'); ?></option>
<option value="inactive"<?php if ($_POST['prospect_client_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'optin-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#client-status"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commerce_registration_confirmation_email_sent"><?php _e('Send a registration confirmation email', 'optin-manager'); ?></label></strong></th>
<td><select name="commerce_registration_confirmation_email_sent" id="commerce_registration_confirmation_email_sent">
<option value=""<?php if ($_POST['commerce_registration_confirmation_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($_POST['commerce_registration_confirmation_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($_POST['commerce_registration_confirmation_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commerce_registration_notification_email_sent"><?php _e('Send a registration notification email', 'optin-manager'); ?></label></strong></th>
<td><select name="commerce_registration_notification_email_sent" id="commerce_registration_notification_email_sent">
<option value=""<?php if ($_POST['commerce_registration_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($_POST['commerce_registration_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($_POST['commerce_registration_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
</select></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="registration-to-affiliate-program-module"<?php if (in_array('registration-to-affiliate-program', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration-to-affiliate-program"><strong><?php echo $modules[$admin_page]['registration-to-affiliate-program']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php if (function_exists('affiliation_manager_admin_menu')) { ?>
<a href="admin.php?page=<?php echo ($_POST['category_id'] == 0 ? 'optin-manager#registration-to-affiliate-program' : 'optin-manager-form-category&amp;id='.$_POST['category_id'].'#registration-to-affiliate-program'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'optin-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'optin-manager') : _e('Click here to configure the default options of the category.', 'optin-manager'))); ?></a>
<?php } else { _e('To use affiliation, you must have installed and activated <a href="http://www.kleor-editions.com/affiliation-manager">Affiliation Manager</a>.', 'optin-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_subscribed_to_affiliate_program"><?php _e('Subscribe the prospect to affiliate program', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_subscribed_to_affiliate_program" id="prospect_subscribed_to_affiliate_program">
<option value=""<?php if ($_POST['prospect_subscribed_to_affiliate_program'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($_POST['prospect_subscribed_to_affiliate_program'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($_POST['prospect_subscribed_to_affiliate_program'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/optin-manager/documentation/#registration-to-affiliate-program"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."affiliation_manager_affiliates_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_affiliate_category_id"><?php _e('Category', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_affiliate_category_id" id="prospect_affiliate_category_id">
<option value=""<?php if ($_POST['prospect_affiliate_category_id'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="0"<?php if ($_POST['prospect_affiliate_category_id'] == '0') { echo ' selected="selected"'; } ?>><?php _e('None ', 'optin-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($_POST['prospect_affiliate_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ((function_exists('affiliation_manager_admin_menu')) && ($_POST['prospect_affiliate_category_id'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$_POST['prospect_affiliate_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$_POST['prospect_affiliate_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_affiliate_status"><?php _e('Status', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_affiliate_status" id="prospect_affiliate_status">
<option value=""<?php if ($_POST['prospect_affiliate_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="active"<?php if ($_POST['prospect_affiliate_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'optin-manager'); ?></option>
<option value="inactive"<?php if ($_POST['prospect_affiliate_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'optin-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#affiliate-status"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliation_registration_confirmation_email_sent"><?php _e('Send a registration confirmation email', 'optin-manager'); ?></label></strong></th>
<td><select name="affiliation_registration_confirmation_email_sent" id="affiliation_registration_confirmation_email_sent">
<option value=""<?php if ($_POST['affiliation_registration_confirmation_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($_POST['affiliation_registration_confirmation_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($_POST['affiliation_registration_confirmation_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliation_registration_notification_email_sent"><?php _e('Send a registration notification email', 'optin-manager'); ?></label></strong></th>
<td><select name="affiliation_registration_notification_email_sent" id="affiliation_registration_notification_email_sent">
<option value=""<?php if ($_POST['affiliation_registration_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($_POST['affiliation_registration_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($_POST['affiliation_registration_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
</select></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="membership-module"<?php if (in_array('membership', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="membership"><strong><?php echo $modules[$admin_page]['membership']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php if (function_exists('membership_manager_admin_menu')) { ?>
<a href="admin.php?page=<?php echo ($_POST['category_id'] == 0 ? 'optin-manager#membership' : 'optin-manager-form-category&amp;id='.$_POST['category_id'].'#membership'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'optin-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'optin-manager') : _e('Click here to configure the default options of the category.', 'optin-manager'))); ?></a>
<?php } else { _e('To use membership, you must have installed and activated <a href="http://www.kleor-editions.com/membership-manager">Membership Manager</a>.', 'optin-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_subscribed_to_members_areas"><?php _e('Subscribe the prospect to a member area', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_subscribed_to_members_areas" id="prospect_subscribed_to_members_areas">
<option value=""<?php if ($_POST['prospect_subscribed_to_members_areas'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($_POST['prospect_subscribed_to_members_areas'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($_POST['prospect_subscribed_to_members_areas'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/optin-manager/documentation/#membership"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_members_areas"><?php _e('Members areas', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="prospect_members_areas" id="prospect_members_areas" rows="1" cols="50"><?php echo $_POST['prospect_members_areas']; ?></textarea>
<?php if ((function_exists('membership_manager_admin_menu')) && (is_numeric($_POST['prospect_members_areas'])) && ($_POST['prospect_members_areas'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-area&amp;id='.$_POST['prospect_members_areas'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-area&amp;id='.$_POST['prospect_members_areas'].'&amp;action=delete">'.__('Delete').'</a>'; } ?><br />
<span class="description"><?php _e('Separate the IDs of the members areas with commas.', 'optin-manager'); ?></span></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."membership_manager_members_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_member_category_id"><?php _e('Category', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_member_category_id" id="prospect_member_category_id">
<option value=""<?php if ($_POST['prospect_member_category_id'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="0"<?php if ($_POST['prospect_member_category_id'] == '0') { echo ' selected="selected"'; } ?>><?php _e('None ', 'optin-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($_POST['prospect_member_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ((function_exists('membership_manager_admin_menu')) && ($_POST['prospect_member_category_id'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-category&amp;id='.$_POST['prospect_member_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-category&amp;id='.$_POST['prospect_member_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_member_status"><?php _e('Status', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_member_status" id="prospect_member_status">
<option value=""<?php if ($_POST['prospect_member_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="active"<?php if ($_POST['prospect_member_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'optin-manager'); ?></option>
<option value="inactive"<?php if ($_POST['prospect_member_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'optin-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#member-status"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="membership_registration_confirmation_email_sent"><?php _e('Send a registration confirmation email', 'optin-manager'); ?></label></strong></th>
<td><select name="membership_registration_confirmation_email_sent" id="membership_registration_confirmation_email_sent">
<option value=""<?php if ($_POST['membership_registration_confirmation_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($_POST['membership_registration_confirmation_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($_POST['membership_registration_confirmation_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="membership_registration_notification_email_sent"><?php _e('Send a registration notification email', 'optin-manager'); ?></label></strong></th>
<td><select name="membership_registration_notification_email_sent" id="membership_registration_notification_email_sent">
<option value=""<?php if ($_POST['membership_registration_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($_POST['membership_registration_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($_POST['membership_registration_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
</select></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="wordpress-module"<?php if (in_array('wordpress', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="wordpress"><strong><?php echo $modules[$admin_page]['wordpress']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=optin-manager<?php echo ($_POST['category_id'] == 0 ? '#wordpress' : '-form-category&amp;id='.$_POST['category_id'].'#wordpress'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'optin-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'optin-manager') : _e('Click here to configure the default options of the category.', 'optin-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_subscribed_as_a_user"><?php _e('Subscribe the prospect as a user', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_subscribed_as_a_user" id="prospect_subscribed_as_a_user">
<option value=""<?php if ($_POST['prospect_subscribed_as_a_user'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($_POST['prospect_subscribed_as_a_user'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($_POST['prospect_subscribed_as_a_user'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/optin-manager/documentation/#wordpress"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_user_role"><?php _e('Role', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_user_role" id="prospect_user_role">
<option value=""<?php if ($_POST['prospect_user_role'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<?php foreach (optin_manager_users_roles() as $role => $name) {
echo '<option value="'.$role.'"'.($_POST['prospect_user_role'] == $role ? ' selected="selected"' : '').'>'.$name.'</option>'."\n"; } ?>
</select></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="custom-instructions-module"<?php if (in_array('custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="custom-instructions"><strong><?php echo $modules[$admin_page]['custom-instructions']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=optin-manager<?php echo ($_POST['category_id'] == 0 ? '#custom-instructions' : '-form-category&amp;id='.$_POST['category_id'].'#custom-instructions'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'optin-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'optin-manager') : _e('Click here to configure the default options of the category.', 'optin-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_custom_instructions_executed"><?php _e('Execute custom instructions', 'optin-manager'); ?></label></strong></th>
<td><select name="registration_custom_instructions_executed" id="registration_custom_instructions_executed">
<option value=""<?php if ($_POST['registration_custom_instructions_executed'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($_POST['registration_custom_instructions_executed'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($_POST['registration_custom_instructions_executed'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_custom_instructions"><?php _e('PHP code', 'optin-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_custom_instructions" id="registration_custom_instructions" rows="10" cols="75"><?php echo $_POST['registration_custom_instructions']; ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the registration of the prospect.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#custom-instructions"><?php _e('More informations', 'optin-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="activation-confirmation-email-module"<?php if (in_array('activation-confirmation-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="activation-confirmation-email"><strong><?php echo $modules[$admin_page]['activation-confirmation-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=optin-manager<?php echo ($_POST['category_id'] == 0 ? '#activation-confirmation-email' : '-form-category&amp;id='.$_POST['category_id'].'#activation-confirmation-email'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'optin-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'optin-manager') : _e('Click here to configure the default options of the category.', 'optin-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_confirmation_email_sent"><?php _e('Send an activation confirmation email', 'optin-manager'); ?></label></strong></th>
<td><select name="activation_confirmation_email_sent" id="activation_confirmation_email_sent">
<option value=""<?php if ($_POST['activation_confirmation_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($_POST['activation_confirmation_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($_POST['activation_confirmation_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_confirmation_email_sender"><?php _e('Sender', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_confirmation_email_sender" id="activation_confirmation_email_sender" rows="1" cols="75"><?php echo $_POST['activation_confirmation_email_sender']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_confirmation_email_receiver"><?php _e('Receiver', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_confirmation_email_receiver" id="activation_confirmation_email_receiver" rows="1" cols="75"><?php echo $_POST['activation_confirmation_email_receiver']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_confirmation_email_subject"><?php _e('Subject', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_confirmation_email_subject" id="activation_confirmation_email_subject" rows="1" cols="75"><?php echo $_POST['activation_confirmation_email_subject']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_confirmation_email_body"><?php _e('Body', 'optin-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="activation_confirmation_email_body" id="activation_confirmation_email_body" rows="15" cols="75"><?php echo $_POST['activation_confirmation_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the prospect and the form.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#email-shortcodes"><?php _e('More informations', 'optin-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="activation-notification-email-module"<?php if (in_array('activation-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="activation-notification-email"><strong><?php echo $modules[$admin_page]['activation-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=optin-manager<?php echo ($_POST['category_id'] == 0 ? '#activation-notification-email' : '-form-category&amp;id='.$_POST['category_id'].'#activation-notification-email'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'optin-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'optin-manager') : _e('Click here to configure the default options of the category.', 'optin-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_sent"><?php _e('Send an activation notification email', 'optin-manager'); ?></label></strong></th>
<td><select name="activation_notification_email_sent" id="activation_notification_email_sent">
<option value=""<?php if ($_POST['activation_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($_POST['activation_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($_POST['activation_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_sender"><?php _e('Sender', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_sender" id="activation_notification_email_sender" rows="1" cols="75"><?php echo $_POST['activation_notification_email_sender']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_receiver"><?php _e('Receiver', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_receiver" id="activation_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['activation_notification_email_receiver']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_subject"><?php _e('Subject', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_subject" id="activation_notification_email_subject" rows="1" cols="75"><?php echo $_POST['activation_notification_email_subject']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_body"><?php _e('Body', 'optin-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="activation_notification_email_body" id="activation_notification_email_body" rows="15" cols="75"><?php echo $_POST['activation_notification_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the prospect and the form.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#email-shortcodes"><?php _e('More informations', 'optin-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="affiliation-module"<?php if (in_array('affiliation', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="affiliation"><strong><?php echo $modules[$admin_page]['affiliation']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php if (function_exists('affiliation_manager_admin_menu')) { ?>
<a href="admin.php?page=optin-manager<?php echo ($_POST['category_id'] == 0 ? '#affiliation' : '-form-category&amp;id='.$_POST['category_id'].'#affiliation'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'optin-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'optin-manager') : _e('Click here to configure the default options of the category.', 'optin-manager'))); ?></a>
<?php } else { _e('To use affiliation, you must have installed and activated <a href="http://www.kleor-editions.com/affiliation-manager">Affiliation Manager</a>.', 'optin-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliation_enabled"><?php _e('Use affiliation', 'optin-manager'); ?></label></strong></th>
<td><select name="affiliation_enabled" id="affiliation_enabled">
<option value=""<?php if ($_POST['affiliation_enabled'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($_POST['affiliation_enabled'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($_POST['affiliation_enabled'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
</select></td></tr>
</tbody></table>
<div id="level-1-commission-module"<?php if (in_array('level-1-commission', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="level-1-commission"><strong><?php echo $modules[$admin_page]['affiliation']['modules']['level-1-commission']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('The level 1 commission is awarded to the affiliate who referred the prospect.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_amount"><?php _e('Amount', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_amount" id="commission_amount" rows="1" cols="25"><?php echo $_POST['commission_amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
</tbody></table>
</div>
<div id="level-2-commission-module"<?php if (in_array('level-2-commission', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="level-2-commission"><strong><?php echo $modules[$admin_page]['affiliation']['modules']['level-2-commission']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('The level 2 commission is awarded to the referrer of the affiliate who referred the prospect.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_enabled"><?php _e('Award a level 2 commission', 'optin-manager'); ?></label></strong></th>
<td><select name="commission2_enabled" id="commission2_enabled">
<option value=""<?php if ($_POST['commission2_enabled'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'optin-manager'); ?></option>
<option value="yes"<?php if ($_POST['commission2_enabled'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'optin-manager'); ?></option>
<option value="no"<?php if ($_POST['commission2_enabled'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'optin-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_amount"><?php _e('Amount', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission2_amount" id="commission2_amount" rows="1" cols="25"><?php echo $_POST['commission2_amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank to apply the default option.', 'optin-manager'); ?></span></td></tr>
</tbody></table>
</div>
<?php if (isset($_GET['id'])) { echo '<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr></tbody></table>'; } ?>
</div></div>

<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php (isset($_GET['id']) ?  _e('Save Changes', 'optin-manager') : ($is_category ? _e('Save Category', 'optin-manager') : _e('Save Form', 'optin-manager'))); ?>" /></p>
<?php if ($is_category) { $module = 'form-category-page'; } else { $module = 'form-page'; }
optin_manager_pages_module($back_office_options, $module, $undisplayed_modules); ?>
</form>
</div>
</div>
<?php }