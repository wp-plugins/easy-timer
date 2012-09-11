<?php global $wpdb;
$back_office_options = get_option('membership_manager_back_office');
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$is_category = (strstr($_GET['page'], 'category'));
if ($is_category) { $admin_page = 'member_category'; $table_slug = 'members_categories'; $attribute = 'category'; }
else { $admin_page = 'member'; $table_slug = 'members'; $attribute = 'id'; }

if ((isset($_GET['id'])) && ($_GET['action'] == 'delete')) {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!membership_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'membership-manager'); }
else {
if ($is_category) {
$category = $wpdb->get_row("SELECT category_id FROM ".$wpdb->prefix."membership_manager_members_categories WHERE id = ".$_GET['id'], OBJECT);
foreach (array('members', 'members_categories') as $table) {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_".$table." SET category_id = ".$category->category_id." WHERE category_id = ".$_GET['id']); }
$options = (array) get_option('membership_manager');
if ($options['members_initial_category_id'] = $_GET['id']) { $options['members_initial_category_id'] = $category->category_id; }
update_option('membership_manager', $options); }
else {
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = stripslashes(html_entity_decode(str_replace('&nbsp;', ' ', $value))); } }
if ((!defined('MEMBERSHIP_MANAGER_DEMO')) || (MEMBERSHIP_MANAGER_DEMO == false)) {
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = $_POST['removal_notification_email_'.$field]; }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); }
if (membership_data('removal_custom_instructions_executed') == 'yes') {
eval(format_instructions(membership_data('removal_custom_instructions'))); } } }
$results = $wpdb->query("DELETE FROM ".$wpdb->prefix."membership_manager_".$table_slug." WHERE id = ".$_GET['id']); } } ?>
<div class="wrap">
<div id="poststuff">
<?php membership_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) {
echo '<div class="updated"><p><strong>'.($is_category ? __('Category deleted.', 'membership-manager') : __('Member deleted.', 'membership-manager')).'</strong></p></div>
<script type="text/javascript">setTimeout(\'window.location = "admin.php?page=membership-manager-members'.($is_category ? '-categories' : '').'"\', 2000);</script>'; } ?>
<?php membership_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php echo ($is_category ? __('Do you really want to permanently delete this category?', 'membership-manager') : __('Do you really want to permanently delete this member?', 'membership-manager')); ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'membership-manager'); ?>" />
</div>
<div class="clear"></div>
<?php if (!$is_category) {
$_GET['member_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members WHERE id = ".$_GET['id'], OBJECT);
foreach (array(
'removal_notification_email_sender',
'removal_notification_email_receiver',
'removal_notification_email_subject',
'removal_notification_email_body') as $field) { $_POST[$field] = htmlspecialchars(membership_data($field)); } ?>
<div class="postbox" id="removal-notification-email-module" style="margin-top: 1em;">
<h3 id="removal-notification-email"><strong><?php _e('Removal notification email', 'membership-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=membership-manager#removal-notification-email"><?php _e('Click here to configure the default options.', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="removal_notification_email_sent" id="removal_notification_email_sent" value="yes" /> <?php _e('Send a removal notification email', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="removal_notification_email_sender" id="removal_notification_email_sender" rows="1" cols="75"><?php echo $_POST['removal_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="removal_notification_email_receiver" id="removal_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['removal_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="removal_notification_email_subject" id="removal_notification_email_subject" rows="1" cols="75"><?php echo $_POST['removal_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="removal_notification_email_body" id="removal_notification_email_body" rows="15" cols="75"><?php echo $_POST['removal_notification_email_body']; ?></textarea></td></tr>
</tbody></table>
</div></div>
<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" value="<?php _e('Delete Member ', 'membership-manager'); ?>" /></p>
<?php } ?>
</form><?php } ?>
</div>
</div><?php }

else {
include 'admin-pages.php';
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!membership_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'membership-manager'); }
else {
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = stripslashes(html_entity_decode(str_replace('&nbsp;', ' ', $value))); } }
if ((!$is_category) && (!isset($_GET['id']))) { $back_office_options = update_membership_manager_back_office($back_office_options, $admin_page); }

if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$members_areas = array_unique(preg_split('#[^0-9]#', $_POST['members_areas'], 0, PREG_SPLIT_NO_EMPTY));
sort($members_areas, SORT_NUMERIC);
foreach ($members_areas as $member_area) { $members_areas_list .= $member_area.', '; }
$_POST['members_areas'] = substr($members_areas_list, 0, -2);
if (!$is_category) {
$_POST['login'] = format_email_address($_POST['login']);
$_POST['email_address'] = format_email_address($_POST['email_address']); }
else {
$keywords = explode(',', $_POST['keywords']);
for ($i = 0; $i < count($keywords); $i++) { $keywords[$i] = strtolower(trim($keywords[$i])); }
sort($keywords);
foreach ($keywords as $keyword) { $keywords_list .= $keyword.', '; }
$_POST['keywords'] = substr($keywords_list, 0, -2); }
if ($_POST['date'] == '') {
$_POST['date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['date_utc'] = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['date'], 0, PREG_SPLIT_NO_EMPTY);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['date'] = date('Y-m-d H:i:s', $time);
$_POST['date_utc'] = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); }

if (!isset($_GET['id'])) {
if (!$is_category) {
if ($_POST['password'] == '') { $_POST['password'] = substr(md5(mt_rand()), 0, 8); }
if ($_POST['referring_url'] == '') { $_POST['referring_url'] = $_SERVER['HTTP_REFERER']; }
if (isset($_POST['update_fields'])) {
if (count($members_areas) == 1) { $_GET['member_area_id'] = (int) $members_areas[0]; } else { unset($_GET['member_area_id']); }
foreach ($_POST as $key => $value) { $_GET['member_data'][$key] = $value; }
$_GET['member_data']['id'] = '{member id}';
foreach ($add_member_fields as $field) { $_POST[$field] = str_replace('{member id}', '[member id]', member_area_data($field)); } }
else {
if (is_numeric($_POST['login'])) { $error .= __('The login name must be a non-numeric string.', 'membership-manager'); }
else { $result = $wpdb->get_results("SELECT login FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$_POST['login']."'", OBJECT);
if ($result) { $error .= ' '.__('This login name is not available.', 'membership-manager'); } }
$result = $wpdb->get_results("SELECT email_address FROM ".$wpdb->prefix."membership_manager_members WHERE email_address = '".$_POST['email_address']."'", OBJECT);
if ($result) { $error .= ' '.__('This email address is not available.', 'membership-manager'); }
if (($_POST['login'] == '') || ($_POST['email_address'] == '')) {
$error .= ' '.__('Please fill out the required fields.', 'membership-manager'); }
if ($error == '') { $updated = true; add_member($_POST); } } }
else {
if ($_POST['name'] == '') { $error .= ' '.__('Please fill out the required fields.', 'membership-manager'); }
else {
$result = $wpdb->get_results("SELECT name FROM ".$wpdb->prefix."membership_manager_members_categories WHERE name = '".$_POST['name']."'", OBJECT);
if ($result) { $error .= ' '.__('This name is not available.', 'membership-manager'); } }
if ($error == '') {
$updated = true;
include 'tables.php';
$sql = membership_sql_array($tables['members_categories'], $_POST);
foreach ($tables['members_categories'] as $key => $value) { if ($key != 'id') { $keys_list .= $key.","; $values_list .= $sql[$key].","; } }
$results = $wpdb->query("INSERT INTO ".$wpdb->prefix."membership_manager_members_categories (".substr($keys_list, 0, -1).") VALUES(".substr($values_list, 0, -1).")"); } } }

if (isset($_GET['id'])) {
$updated = true;
include 'tables.php';
if (!$is_category) {
if (is_numeric($_POST['login'])) { $error .= __('The login name must be a non-numeric string.', 'membership-manager'); }
else { $result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$_POST['login']."'", OBJECT);
if (($result) && ($result->id != $_GET['id'])) { $error .= ' '.__('This login name is not available.', 'membership-manager'); }
elseif ($_POST['login'] != '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_members SET login = '".$_POST['login']."' WHERE id = ".$_GET['id']); } }
if ($_POST['password'] != '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_members SET password = '".hash('sha256', $_POST['password'])."' WHERE id = ".$_GET['id']); }
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."membership_manager_members WHERE email_address = '".$_POST['email_address']."'", OBJECT);
if (($result) && ($result->id != $_GET['id'])) { $error .= ' '.__('This email address is not available.', 'membership-manager'); }
elseif ($_POST['email_address'] != '') {
$member = $wpdb->get_row("SELECT email_address FROM ".$wpdb->prefix."membership_manager_members WHERE id = ".$_GET['id'], OBJECT);
$results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_members SET email_address = '".$_POST['email_address']."' WHERE id = ".$_GET['id']);
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."commerce_manager_clients WHERE email_address = '".$_POST['email_address']."'", OBJECT);
if (!$result) { $results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_clients SET email_address = '".$_POST['email_address']."' WHERE email_address = '".$member->email_address."'"); }
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".$_POST['email_address']."'", OBJECT);
if (!$result) { $results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_affiliates SET email_address = '".$_POST['email_address']."' WHERE email_address = '".$member->email_address."'"); }
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->base_prefix."users WHERE user_email = '".$_POST['email_address']."'", OBJECT);
if (!$result) { $results = $wpdb->query("UPDATE ".$wpdb->base_prefix."users SET user_email = '".$_POST['email_address']."' WHERE user_email = '".$member->email_address."'"); } }
$sql = membership_sql_array($tables['members'], $_POST);
foreach ($tables['members'] as $key => $value) { switch ($key) {
case 'id': case 'login': case 'password': case 'email_address': break;
default: $list .= $key." = ".$sql[$key].","; } }
if ((!defined('MEMBERSHIP_MANAGER_DEMO')) || (MEMBERSHIP_MANAGER_DEMO == false)) {
if ($_POST['status'] == 'active') {
if ($_POST['old_status'] != 'active') {
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = stripslashes($_POST['activation_notification_email_'.$field]); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); }
if (membership_data('activation_custom_instructions_executed') == 'yes') {
eval(format_instructions(membership_data('activation_custom_instructions'))); } } }
elseif ($_POST['old_status'] == 'active') {
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = stripslashes($_POST['deactivation_notification_email_'.$field]); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); }
if (membership_data('deactivation_custom_instructions_executed') == 'yes') {
eval(format_instructions(membership_data('deactivation_custom_instructions'))); } }
if (membership_data('profile_edit_custom_instructions_executed') == 'yes') {
eval(format_instructions(membership_data('profile_edit_custom_instructions'))); } } }
else {
if ($_POST['name'] != '') {
$result = $wpdb->get_results("SELECT name FROM ".$wpdb->prefix."membership_manager_members_categories WHERE name = '".$_POST['name']."' AND id != ".$_GET['id'], OBJECT);
if ($result) { $error .= ' '.__('This name is not available.', 'membership-manager'); }
else { $results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_members_categories SET name = '".$_POST['name']."' WHERE id = ".$_GET['id']); } }
$sql = membership_sql_array($tables['members_categories'], $_POST);
foreach ($tables['members_categories'] as $key => $value) { switch ($key) {
case 'id': case 'name': break;
default: $list .= $key." = ".$sql[$key].","; } } }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_".$table_slug." SET ".substr($list, 0, -1)." WHERE id = ".$_GET['id']); } } }

if (isset($_GET['id'])) {
$item_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_".$table_slug." WHERE id = ".$_GET['id'], OBJECT);
if ($item_data) {
if (!$is_category) { $_GET['member_data'] = (array) $item_data; $_GET['member_data']['password'] = ''; }
foreach ($item_data as $key => $value) { $_POST[$key] = $value; } }
elseif (!headers_sent()) { header('Location: admin.php?page=membership-manager-members'.($is_category ? '-categories' : '')); exit(); }
else { echo '<script type="text/javascript">window.location = "admin.php?page=membership-manager-members'.($is_category ? '-categories' : '').'";</script>'; } }

foreach ($_POST as $key => $value) {
if (is_string($value)) {
$_POST[$key] = str_replace(array('&amp;amp;', '&amp;apos;', '&amp;quot;'), array('&amp;', '&apos;', '&quot;'), htmlspecialchars(stripslashes($value)));
if ($value == '0000-00-00 00:00:00') { $_POST[$key] = ''; } } }
$undisplayed_modules = (array) $back_office_options[$admin_page.'_page_undisplayed_modules']; ?>

<div class="wrap">
<div id="poststuff">
<?php membership_manager_pages_top($back_office_options); ?>
<?php if ($updated) {
echo '<div class="updated"><p><strong>'.(isset($_GET['id']) ? ($is_category ? __('Category updated.', 'membership-manager') : __('Member updated.', 'membership-manager')) : ($is_category ? __('Category saved.', 'membership-manager') : __('Member saved.', 'membership-manager'))).'</strong></p></div>
'.(isset($_GET['id']) ? '' : '<script type="text/javascript">setTimeout(\'window.location = "admin.php?page=membership-manager-members'.($is_category ? '-categories' : '').'"\', 2000);</script>'); } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php membership_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<p class="description"><?php _e('Fields marked with * are required.', 'membership-manager'); ?> 
<?php if ($_POST['category_id'] > 0) { _e('You can apply the default option of the category by leaving the corresponding field blank.', 'membership-manager'); } ?></p>
<?php if ((!$is_category) && (!isset($_GET['id']))) { membership_manager_pages_summary($back_office_options); } ?>
<?php if ($is_category) { $module = 'general-informations'; } else { $module = 'personal-informations'; } ?>

<div class="postbox" id="<?php echo $module; ?>-module"<?php if (in_array($module, $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="<?php echo $module; ?>"><strong><?php echo $modules[$admin_page][$module]['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="id">'.__('ID', 'membership-manager').'</label></strong></th>
<td><input type="text" name="id" id="id" size="10" value="'.$_GET['id'].'" disabled="disabled" /> <span class="description">'.__('The ID can not be changed.', 'membership-manager').'</span></td></tr>'; } ?>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."membership_manager_members_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="category_id"><?php echo ($is_category ? __('Parent category', 'membership-manager') : __('Category', 'membership-manager')); ?></label></strong></th>
<td><select name="category_id" id="category_id">
<option value="0"<?php if ($_POST['category_id'] == 0) { echo ' selected="selected"'; } ?>><?php _e('None ', 'membership-manager'); ?></option>
<?php foreach ($categories as $category) {
if ((!$is_category) || (!in_array($_GET['id'], members_categories_list($category->id)))) {
echo '<option value="'.$category->id.'"'.($_POST['category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } } ?>
</select>
<span class="description"><?php ($is_category ? _e('The options of this category will apply by default to the category.', 'membership-manager') : _e('The options of this category will apply by default to the member.', 'membership-manager')); ?></span>
<?php if ($_POST['category_id'] > 0) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-category&amp;id='.$_POST['category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-category&amp;id='.$_POST['category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<?php if ($is_category) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['name'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="name"><?php _e('Name', 'membership-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="name" id="name" rows="1" cols="50"><?php echo $_POST['name']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="description"><?php _e('Description', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="description" id="description" rows="1" cols="75"><?php echo $_POST['description']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="keywords"><?php _e('Keywords', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="keywords" id="keywords" rows="1" cols="75"><?php echo $_POST['keywords']; ?></textarea><br />
<span class="description"><?php _e('Separate the keywords with commas.', 'membership-manager'); ?></span></td></tr>
<?php } else { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['login'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="login"><?php _e('Login name', 'membership-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="login" id="login" rows="1" cols="25"><?php echo $_POST['login']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Letters, numbers, hyphens, underscores, points and <em>@</em> only', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password"><?php _e('Password', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="password" id="password" rows="1" cols="25"><?php echo (isset($_GET['id']) ? '' : $_POST['password']); ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php (isset($_GET['id']) ? _e('(if you want to change it)', 'membership-manager') : _e('Leave this field blank to automatically generate a random password.', 'membership-manager')); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="first_name"><?php _e('First name', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="first_name" id="first_name" rows="1" cols="50"><?php echo $_POST['first_name']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="last_name"><?php _e('Last name', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="last_name" id="last_name" rows="1" cols="50"><?php echo $_POST['last_name']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['email_address'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="email_address"><?php _e('Email address', 'membership-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="email_address" id="email_address" rows="1" cols="50"><?php echo $_POST['email_address']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="website_name"><?php _e('Website name', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="website_name" id="website_name" rows="1" cols="50"><?php echo $_POST['website_name']; ?></textarea> 
<?php $url = htmlspecialchars(member_data(array(0 => 'website_url', 'part' => 1, $attribute => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'membership-manager'); ?></a><?php } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="website_url"><?php _e('Website URL', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="website_url" id="website_url" rows="1" cols="75"><?php echo $_POST['website_url']; ?></textarea> 
<?php $url = htmlspecialchars(member_data(array(0 => 'website_url', 'part' => 1, $attribute => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'membership-manager'); ?></a><?php } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="address"><?php _e('Address', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="address" id="address" rows="1" cols="50"><?php echo $_POST['address']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="postcode"><?php _e('Postcode', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="postcode" id="postcode" rows="1" cols="50"><?php echo $_POST['postcode']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="town"><?php _e('Town', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="town" id="town" rows="1" cols="50"><?php echo $_POST['town']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="country"><?php _e('Country', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="country" id="country" rows="1" cols="50"><?php echo $_POST['country']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="phone_number"><?php _e('Phone number', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="phone_number" id="phone_number" rows="1" cols="50"><?php echo $_POST['phone_number']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="ip_address"><?php _e('IP address', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="ip_address" id="ip_address" rows="1" cols="50"><?php echo $_POST['ip_address']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="user_agent"><?php _e('User agent', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="user_agent" id="user_agent" rows="1" cols="75"><?php echo $_POST['user_agent']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="referring_url"><?php _e('Referring URL', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="referring_url" id="referring_url" rows="1" cols="75"><?php echo $_POST['referring_url']; ?></textarea> 
<?php $url = htmlspecialchars(member_data(array(0 => 'referring_url', 'part' => 1, $attribute => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'membership-manager'); ?></a><?php } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="status"><?php _e('Status', 'membership-manager'); ?></label></strong></th>
<td><select name="status" id="status"<?php if (isset($_GET['id'])) { echo ' onchange="display_status_notification_email_module();"'; } ?>>
<option value="active"<?php if ($_POST['status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'membership-manager'); ?></option>
<option value="inactive"<?php if ($_POST['status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'membership-manager'); ?></option>
<option value="deactivated"<?php if ($_POST['status'] == 'deactivated') { echo ' selected="selected"'; } ?>><?php _e('Deactivated', 'membership-manager'); ?></option>
</select> <?php if (isset($_GET['id'])) { echo '<input type="hidden" name="old_status" value="'.$_POST['status'].'" />'; } ?>
<span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#member-status"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="members_areas"><?php _e('Members areas', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="members_areas" id="members_areas" rows="1" cols="50"><?php echo $_POST['members_areas']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/membership-manager/documentation/#members-areas"><?php _e('More informations', 'membership-manager'); ?></a></span>
<?php if ((is_numeric($_POST['members_areas'])) && ($_POST['members_areas'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-area&amp;id='.$_POST['members_areas'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-area&amp;id='.$_POST['members_areas'].'&amp;action=delete">'.__('Delete').'</a>'; } ?><br />
<span class="description"><?php _e('Separate the IDs of the members areas with commas.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="date"><?php ($is_category ? _e('Creation date', 'membership-manager') : _e('Registration date', 'membership-manager')); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="date" id="date" size="20" value="<?php echo (isset($_POST['date']) ? $_POST['date'] : date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET)); ?>" /></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<?php if ((isset($_GET['id'])) && (!$is_category)) {
if ($_POST['status'] != 'active') {
foreach (array(
'activation_notification_email_sender',
'activation_notification_email_receiver',
'activation_notification_email_subject',
'activation_notification_email_body') as $field) { $_POST[$field] = htmlspecialchars(membership_data($field)); } ?>

<div class="postbox" id="activation-notification-email-module" style="display: none;">
<h3 id="activation-notification-email"><strong><?php _e('Activation notification email', 'membership-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=membership-manager#activation-notification-email"><?php _e('Click here to configure the default options.', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="activation_notification_email_sent" id="activation_notification_email_sent" value="yes" /> <?php _e('Send an activation notification email', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_sender" id="activation_notification_email_sender" rows="1" cols="75"><?php echo $_POST['activation_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_receiver" id="activation_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['activation_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_subject" id="activation_notification_email_subject" rows="1" cols="75"><?php echo $_POST['activation_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="activation_notification_email_body" id="activation_notification_email_body" rows="15" cols="75"><?php echo $_POST['activation_notification_email_body']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<script type="text/javascript">
function display_status_notification_email_module() {
if (document.forms[0].status.value == 'active') { document.getElementById('activation-notification-email-module').style.display = 'block'; }
else { document.getElementById('activation-notification-email-module').style.display = 'none'; } }
</script>

<?php } else {
foreach (array(
'deactivation_notification_email_sender',
'deactivation_notification_email_receiver',
'deactivation_notification_email_subject',
'deactivation_notification_email_body') as $field) { $_POST[$field] = htmlspecialchars(membership_data($field)); } ?>

<div class="postbox" id="deactivation-notification-email-module" style="display: none;">
<h3 id="deactivation-notification-email"><strong><?php _e('Deactivation notification email', 'membership-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=membership-manager#deactivation-notification-email"><?php _e('Click here to configure the default options.', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="deactivation_notification_email_sent" id="deactivation_notification_email_sent" value="yes" /> <?php _e('Send a deactivation notification email', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="deactivation_notification_email_sender" id="deactivation_notification_email_sender" rows="1" cols="75"><?php echo $_POST['deactivation_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="deactivation_notification_email_receiver" id="deactivation_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['deactivation_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="deactivation_notification_email_subject" id="deactivation_notification_email_subject" rows="1" cols="75"><?php echo $_POST['deactivation_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="deactivation_notification_email_body" id="deactivation_notification_email_body" rows="15" cols="75"><?php echo $_POST['deactivation_notification_email_body']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<script type="text/javascript">
function display_status_notification_email_module() {
if (document.forms[0].status.value == 'active') { document.getElementById('deactivation-notification-email-module').style.display = 'none'; }
else { document.getElementById('deactivation-notification-email-module').style.display = 'block'; } }
</script>
<?php } } ?>

<?php if ((!$is_category) && (!isset($_GET['id']))) {
if (!isset($_POST['submit'])) {
$membership_manager_options = (array) get_option('membership_manager');
foreach ($membership_manager_options as $key => $value) {
if (is_string($value)) { $membership_manager_options[$key] = htmlspecialchars($value); } }
foreach ($add_member_fields as $field) { $_POST[$field] = $membership_manager_options[$field]; } }
$value = false; foreach ($add_member_modules as $module) { if (!$value) { $value = (!in_array($module, $undisplayed_modules)); } }
if ($value) { ?><p class="submit" style="margin: 0 20%;"><input type="hidden" name="submit" value="true" />
<input type="submit" class="button-secondary" name="update_fields" value="<?php _e('Complete the fields below with the informations about the member and the member area', 'membership-manager'); ?>" /></p><?php } ?>

<div id="add-member-modules">
<?php if (!in_array('registration-confirmation-email', $undisplayed_modules)) {
if (!isset($_POST['submit'])) { $_POST['registration_confirmation_email_body'] = htmlspecialchars(get_option('membership_manager_registration_confirmation_email_body')); } ?>
<div class="postbox" id="registration-confirmation-email-module">
<h3 id="registration-confirmation-email"><strong><?php echo $modules[$admin_page]['registration-confirmation-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=membership-manager#registration-confirmation-email"><?php _e('Click here to configure the default options.', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="registration_confirmation_email_sent" id="registration_confirmation_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['registration_confirmation_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration confirmation email', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_sender" id="registration_confirmation_email_sender" rows="1" cols="75"><?php echo $_POST['registration_confirmation_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_receiver" id="registration_confirmation_email_receiver" rows="1" cols="75"><?php echo $_POST['registration_confirmation_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_subject" id="registration_confirmation_email_subject" rows="1" cols="75"><?php echo $_POST['registration_confirmation_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_confirmation_email_body" id="registration_confirmation_email_body" rows="15" cols="75"><?php echo $_POST['registration_confirmation_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the member and the member area.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#email-shortcodes"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('registration-notification-email', $undisplayed_modules)) {
if (!isset($_POST['submit'])) { $_POST['registration_notification_email_body'] = htmlspecialchars(get_option('membership_manager_registration_notification_email_body')); } ?>
<div class="postbox" id="registration-notification-email-module">
<h3 id="registration-notification-email"><strong><?php echo $modules[$admin_page]['registration-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=membership-manager#registration-notification-email"><?php _e('Click here to configure the default options.', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="registration_notification_email_sent" id="registration_notification_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['registration_notification_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration notification email', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_sender" id="registration_notification_email_sender" rows="1" cols="75"><?php echo $_POST['registration_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_receiver" id="registration_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['registration_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_subject" id="registration_notification_email_subject" rows="1" cols="75"><?php echo $_POST['registration_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_notification_email_body" id="registration_notification_email_body" rows="15" cols="75"><?php echo $_POST['registration_notification_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the member and the member area.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#email-shortcodes"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('autoresponders', $undisplayed_modules)) { ?>
<div class="postbox" id="autoresponders-module">
<h3 id="autoresponders"><strong><?php echo $modules[$admin_page]['autoresponders']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=membership-manager#autoresponders"><?php _e('Click here to configure the default options.', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="member_subscribed_to_autoresponder" id="member_subscribed_to_autoresponder" value="yes"<?php if ($_POST['member_subscribed_to_autoresponder'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Subscribe the member to an autoresponder list', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="member_autoresponder"><?php _e('Autoresponder', 'membership-manager'); ?></label></strong></th>
<td><select name="member_autoresponder" id="member_autoresponder">
<?php include 'libraries/autoresponders.php';
$autoresponder = do_shortcode($_POST['member_autoresponder']);
foreach ($autoresponders as $value) {
echo '<option value="'.$value.'"'.($autoresponder == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="member_autoresponder_list"><?php _e('List', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="member_autoresponder_list" id="member_autoresponder_list" rows="1" cols="50"><?php echo $_POST['member_autoresponder_list']; ?></textarea><br />
<span class="description"><?php _e('For some autoresponders, you must enter the list ID.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#autoresponders"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('registration-as-a-client', $undisplayed_modules)) { ?>
<div class="postbox" id="registration-as-a-client-module">
<h3 id="registration-as-a-client"><strong><?php echo $modules[$admin_page]['registration-as-a-client']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php if (function_exists('commerce_manager_admin_menu')) { ?>
<a href="admin.php?page=membership-manager#registration-as-a-client"><?php _e('Click here to configure the default options.', 'membership-manager'); ?></a>
<?php } else { _e('To subscribe your members as clients, you must have installed and activated <a href="http://www.kleor-editions.com/commerce-manager">Commerce Manager</a>.', 'membership-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="member_subscribed_as_a_client" id="member_subscribed_as_a_client" value="yes"<?php if ($_POST['member_subscribed_as_a_client'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the member as a client', 'membership-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#registration-as-a-client"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."commerce_manager_clients_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="member_client_category_id"><?php _e('Category', 'membership-manager'); ?></label></strong></th>
<td><select name="member_client_category_id" id="member_client_category_id">
<option value="0"<?php if ($_POST['member_client_category_id'] == 0) { echo ' selected="selected"'; } ?>><?php _e('None ', 'membership-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($_POST['member_client_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ((function_exists('commerce_manager_admin_menu')) && ($_POST['member_client_category_id'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-client-category&amp;id='.$_POST['member_client_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-client-category&amp;id='.$_POST['member_client_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="member_client_status"><?php _e('Status', 'membership-manager'); ?></label></strong></th>
<td><select name="member_client_status" id="member_client_status">
<option value="active"<?php if ($_POST['member_client_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'membership-manager'); ?></option>
<option value="inactive"<?php if ($_POST['member_client_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'membership-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#client-status"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="commerce_registration_confirmation_email_sent" id="commerce_registration_confirmation_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['commerce_registration_confirmation_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration confirmation email', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="commerce_registration_notification_email_sent" id="commerce_registration_notification_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['commerce_registration_notification_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration notification email', 'membership-manager'); ?></label></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('registration-to-affiliate-program', $undisplayed_modules)) { ?>
<div class="postbox" id="registration-to-affiliate-program-module">
<h3 id="registration-to-affiliate-program"><strong><?php echo $modules[$admin_page]['registration-to-affiliate-program']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php if (function_exists('affiliation_manager_admin_menu')) { ?>
<a href="admin.php?page=membership-manager#registration-to-affiliate-program"><?php _e('Click here to configure the default options.', 'membership-manager'); ?></a>
<?php } else { _e('To use affiliation, you must have installed and activated <a href="http://www.kleor-editions.com/affiliation-manager">Affiliation Manager</a>.', 'membership-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="member_subscribed_to_affiliate_program" id="member_subscribed_to_affiliate_program" value="yes"<?php if ($_POST['member_subscribed_to_affiliate_program'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the member to affiliate program', 'membership-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#registration-to-affiliate-program"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."affiliation_manager_affiliates_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="member_affiliate_category_id"><?php _e('Category', 'membership-manager'); ?></label></strong></th>
<td><select name="member_affiliate_category_id" id="member_affiliate_category_id">
<option value="0"<?php if ($_POST['member_affiliate_category_id'] == 0) { echo ' selected="selected"'; } ?>><?php _e('None ', 'membership-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($_POST['member_affiliate_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ((function_exists('affiliation_manager_admin_menu')) && ($_POST['member_affiliate_category_id'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$_POST['member_affiliate_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$_POST['member_affiliate_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="member_affiliate_status"><?php _e('Status', 'membership-manager'); ?></label></strong></th>
<td><select name="member_affiliate_status" id="member_affiliate_status">
<option value="active"<?php if ($_POST['member_affiliate_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'membership-manager'); ?></option>
<option value="inactive"<?php if ($_POST['member_affiliate_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'membership-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#affiliate-status"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="affiliation_registration_confirmation_email_sent" id="affiliation_registration_confirmation_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['affiliation_registration_confirmation_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration confirmation email', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="affiliation_registration_notification_email_sent" id="affiliation_registration_notification_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['affiliation_registration_notification_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration notification email', 'membership-manager'); ?></label></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('wordpress', $undisplayed_modules)) { ?>
<div class="postbox" id="wordpress-module">
<h3 id="wordpress"><strong><?php echo $modules[$admin_page]['wordpress']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=membership-manager#wordpress"><?php _e('Click here to configure the default options.', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="member_subscribed_as_a_user" id="member_subscribed_as_a_user" value="yes"<?php if ($_POST['member_subscribed_as_a_user'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the member as a user', 'membership-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#wordpress"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="member_user_role"><?php _e('Role', 'membership-manager'); ?></label></strong></th>
<td><select name="member_user_role" id="member_user_role">
<?php foreach (membership_manager_users_roles() as $role => $name) {
echo '<option value="'.$role.'"'.($_POST['member_user_role'] == $role ? ' selected="selected"' : '').'>'.$name.'</option>'."\n"; } ?>
</select></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('custom-instructions', $undisplayed_modules)) {
if (!isset($_POST['submit'])) { $_POST['registration_custom_instructions'] = htmlspecialchars(get_option('membership_manager_registration_custom_instructions')); } ?>
<div class="postbox" id="custom-instructions-module">
<h3 id="custom-instructions"><strong><?php echo $modules[$admin_page]['custom-instructions']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=membership-manager#custom-instructions"><?php _e('Click here to configure the default options.', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="registration_custom_instructions_executed" id="registration_custom_instructions_executed" value="yes"<?php if ($_POST['registration_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_custom_instructions"><?php _e('PHP code', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_custom_instructions" id="registration_custom_instructions" rows="10" cols="75"><?php echo $_POST['registration_custom_instructions']; ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the registration of the member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#custom-instructions"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<?php } ?>
</div>

<?php } ?>
<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php (isset($_GET['id']) ? _e('Save Changes', 'membership-manager') : ($is_category ? _e('Save Category', 'membership-manager') : _e('Save Member', 'membership-manager'))); ?>" /></p>
<?php if ((!$is_category) && (!isset($_GET['id']))) { membership_manager_pages_module($back_office_options, 'member-page', $undisplayed_modules); } ?>
</form>
</div>
</div>
<?php if (isset($_POST['update_fields'])) { ?>
<script type="text/javascript">window.location = '#add-member-modules';</script>
<?php } }