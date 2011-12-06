<?php global $wpdb;
$back_office_options = get_option('affiliation_manager_back_office');
$is_category = (strstr($_GET['page'], 'category'));
if ($is_category) { $admin_page = 'affiliate_category'; $table_slug = 'affiliates_categories'; $attribute = 'category'; }
else { $admin_page = 'affiliate'; $table_slug = 'affiliates'; $attribute = 'id'; }

if ((isset($_GET['id'])) && ($_GET['action'] == 'delete')) {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if ($is_category) {
$category = $wpdb->get_row("SELECT category_id FROM ".$wpdb->prefix."affiliation_manager_affiliates_categories WHERE id = '".$_GET['id']."'", OBJECT);
foreach (array('affiliates', 'affiliates_categories') as $table) {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_".$table." SET category_id = ".$category->category_id." WHERE category_id = '".$_GET['id']."'"); }
$options = (array) get_option('affiliation_manager');
if ($options['affiliates_initial_category_id'] = $_GET['id']) { $options['affiliates_initial_category_id'] = $category->category_id; }
update_option('affiliation_manager', $options); }
else {
$affiliate = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE id = '".$_GET['id']."'", OBJECT);
$results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_affiliates SET referrer = '' WHERE referrer = '".$affiliate->login."'");
$_POST = array_map('stripslashes', $_POST);
$_POST = array_map('html_entity_decode', $_POST);
if ($_POST['removal_notification_email_sent'] == 'yes') {
$sender = $_POST['removal_notification_email_sender'];
$receiver = $_POST['removal_notification_email_receiver'];
$subject = $_POST['removal_notification_email_subject'];
$body = $_POST['removal_notification_email_body'];
$headers = 'From: '.$sender;
wp_mail($receiver, $subject, $body, $headers); } }
$results = $wpdb->query("DELETE FROM ".$wpdb->prefix."affiliation_manager_".$table_slug." WHERE id = '".$_GET['id']."'"); } ?>
<div class="wrap">
<div id="poststuff">
<?php affiliation_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.($is_category ? __('Category deleted.', 'affiliation-manager') : __('Affiliate deleted.', 'affiliation-manager')).'</strong></p></div>'; } ?>
<?php affiliation_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php echo ($is_category ? __('Do you really want to permanently delete this category?', 'affiliation-manager') : __('Do you really want to permanently delete this affiliate?', 'affiliation-manager')); ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'affiliation-manager'); ?>" />
</div>
<div class="clear"></div>
<?php if (!$is_category) {
$_GET['affiliate_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE id = '".$_GET['id']."'", OBJECT);
foreach (array(
'removal_notification_email_sender',
'removal_notification_email_receiver',
'removal_notification_email_subject',
'removal_notification_email_body') as $field) { $_POST[$field] = htmlspecialchars(affiliation_data($field)); } ?>

<div class="postbox" style="margin-top: 1em;">
<h3 id="removal-notification-email"><strong><?php _e('Removal notification email', 'affiliation-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=affiliation-manager#removal-notification-email"><?php _e('Click here to configure the default options.', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="removal_notification_email_sent" id="removal_notification_email_sent" value="yes" /> <?php _e('Send a removal notification email', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="removal_notification_email_sender" id="removal_notification_email_sender" rows="1" cols="75"><?php echo $_POST['removal_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="removal_notification_email_receiver" id="removal_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['removal_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="removal_notification_email_subject" id="removal_notification_email_subject" rows="1" cols="75"><?php echo $_POST['removal_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="removal_notification_email_body" id="removal_notification_email_body" rows="15" cols="75"><?php echo $_POST['removal_notification_email_body']; ?></textarea></td></tr>
</tbody></table>
</div></div>
<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" value="<?php _e('Delete Affiliate ', 'affiliation-manager'); ?>" /></p>
<?php } ?>
</form><?php } ?>
</div>
</div><?php }

else {
include 'admin-pages.php';
add_action('admin_footer', 'affiliation_date_picker_js');
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
$_POST = array_map('stripslashes', $_POST);
$_POST = array_map('html_entity_decode', $_POST);
if ($_POST[$admin_page.'_page_summary_displayed'] != 'yes') { $_POST[$admin_page.'_page_summary_displayed'] = 'no'; }
$back_office_options[$admin_page.'_page_summary_displayed'] = $_POST[$admin_page.'_page_summary_displayed'];
$back_office_options[$admin_page.'_page_undisplayed_modules'] = array();
foreach ($modules[$admin_page] as $key => $value) {
if (($_POST[$admin_page.'_page_'.str_replace('-', '_', $key).'_module_displayed'] != 'yes') && ($value['required'] != 'yes')) { $back_office_options[$admin_page.'_page_undisplayed_modules'][] = $key; }
if (is_array($value['modules'])) { foreach ($value['modules'] as $module_key => $module_value) {
if (($_POST[$admin_page.'_page_'.str_replace('-', '_', $module_key).'_module_displayed'] != 'yes') && ($module_value['required'] != 'yes')) { $back_office_options[$admin_page.'_page_undisplayed_modules'][] = $module_key; } } } }
update_option('affiliation_manager_back_office', $back_office_options);

if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
foreach (array(
'commission_amount',
'commission_percentage',
'commission2_amount',
'commission2_percentage') as $field) { $_POST[$field] = str_replace(array('?', ',', ';'), '.', $_POST[$field]); }
if (!$is_category) {
$_POST['login'] = affiliation_format_nice_name($_POST['login']);
$_POST['email_address'] = affiliation_format_email_address($_POST['email_address']);
$_POST['paypal_email_address'] = affiliation_format_email_address($_POST['paypal_email_address']); }
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
if (!$is_category) {
if (isset($_POST['referrer'])) {
if (is_numeric($_POST['referrer'])) {
$_POST['referrer'] = preg_replace('/[^0-9]/', '', $_POST['referrer']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE id = '".$_POST['referrer']."'", OBJECT);
if ($result) { $_POST['referrer'] = $result->login; } }
elseif (strstr($_POST['referrer'], '@')) {
$_POST['referrer'] = affiliation_format_email_address($_POST['referrer']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE paypal_email_address = '".$_POST['referrer']."'", OBJECT);
if ($result) { $_POST['referrer'] = $result->login; }
else { $result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".$_POST['referrer']."'", OBJECT);
if ($result) { $_POST['referrer'] = $result->login; } } }
else {
$_POST['referrer'] = affiliation_format_nice_name($_POST['referrer']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['referrer']."'", OBJECT);
if (!$result) { $_POST['referrer'] = ''; } } } }

if (!isset($_GET['id'])) {
if (!$is_category) {
if ($_POST['password'] == '') { $_POST['password'] = substr(md5(mt_rand()), 0, 8); }
if ($_POST['referring_url'] == '') { $_POST['referring_url'] = $_SERVER['HTTP_REFERER']; }
if (isset($_POST['update_fields'])) {
foreach ($_POST as $key => $value) { $_GET['affiliate_data'][$key] = $value; }
$_GET['affiliate_data']['id'] = '{affiliate id}';
foreach (add_affiliate_fields() as $field) { $_POST[$field] = str_replace('{affiliate id}', '[affiliate id]', affiliation_data($field)); } }
else {
$members_areas = array_unique(preg_split('#[^0-9]#', $_POST['affiliate_members_areas'], 0, PREG_SPLIT_NO_EMPTY));
sort($members_areas, SORT_NUMERIC);
foreach ($members_areas as $member_area) { $members_areas_list .= $member_area.', '; }
$_POST['affiliate_members_areas'] = substr($members_areas_list, 0, -2);
if (is_numeric($_POST['login'])) { $error .= __('The login name must be a non-numeric string.', 'affiliation-manager'); }
$result = $wpdb->get_results("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['login']."'", OBJECT);
if ($result) { $error .= ' '.__('This login name is not available.', 'affiliation-manager'); }
$result = $wpdb->get_results("SELECT email_address FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".$_POST['email_address']."'", OBJECT);
if ($result) { $error .= ' '.__('This email address is not available.', 'affiliation-manager'); }
$result = $wpdb->get_results("SELECT paypal_email_address FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE paypal_email_address = '".$_POST['paypal_email_address']."'", OBJECT);
if ($result) { $error .= ' '.__('This PayPal email address is not available.', 'affiliation-manager'); }
if (($_POST['login'] == '') || ($_POST['first_name'] == '') || ($_POST['last_name'] == '') || ($_POST['email_address'] == '') || ($_POST['paypal_email_address'] == '')) {
$error .= ' '.__('Please fill out the required fields.', 'affiliation-manager'); }
if ($error == '') { $updated = true; add_affiliate($_POST); } } }
else {
if ($_POST['name'] == '') { $error .= ' '.__('Please fill out the required fields.', 'affiliation-manager'); }
else {
$result = $wpdb->get_results("SELECT name FROM ".$wpdb->prefix."affiliation_manager_affiliates_categories WHERE name = '".$_POST['name']."'", OBJECT);
if ($result) { $error .= ' '.__('This name is not available.', 'affiliation-manager'); } }
if ($error == '') {
$updated = true;
include 'tables.php';
foreach ($tables['affiliates_categories'] as $key => $value) { $keys_list .= $key.","; $values_list .= "'".$_POST[$key]."',"; }
$results = $wpdb->query("INSERT INTO ".$wpdb->prefix."affiliation_manager_affiliates_categories (".substr($keys_list, 0, -1).") VALUES(".substr($values_list, 0, -1).")"); } } }

if (isset($_GET['id'])) {
$updated = true;
include 'tables.php';
if (!$is_category) {
if ($_POST['password'] != '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_affiliates SET password = '".hash('sha256', $_POST['password'])."' WHERE id = '".$_GET['id']."'"); }
if ($_POST['first_name'] != '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_affiliates SET first_name = '".$_POST['first_name']."' WHERE id = '".$_GET['id']."'"); }
if ($_POST['last_name'] != '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_affiliates SET last_name = '".$_POST['last_name']."' WHERE id = '".$_GET['id']."'"); }
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address='".$_POST['email_address']."'", OBJECT);
if (($result) && ($result->id != $_GET['id'])) { $error .= ' '.__('This email address is not available.', 'affiliation-manager'); }
elseif ($_POST['email_address'] != '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_affiliates SET email_address = '".$_POST['email_address']."' WHERE id = '".$_GET['id']."'"); }
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE paypal_email_address = '".$_POST['paypal_email_address']."'", OBJECT);
if (($result) && ($result->id != $_GET['id'])) { $error .= ' '.__('This PayPal email address is not available.', 'affiliation-manager'); }
elseif ($_POST['paypal_email_address'] != '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_affiliates SET paypal_email_address = '".$_POST['paypal_email_address']."' WHERE id = '".$_GET['id']."'"); }
foreach ($tables['affiliates'] as $key => $value) { switch ($key) {
case 'id': case 'login': case 'password': case 'first_name': case 'last_name': case 'email_address': case 'paypal_email_address': break;
default: $list .= $key." = '".$_POST[$key]."',"; } }
if (($_POST['status'] == 'active') && ($_POST['activation_notification_email_sent'] == 'yes')) {
$sender = $_POST['activation_notification_email_sender'];
$receiver = $_POST['activation_notification_email_receiver'];
$subject = $_POST['activation_notification_email_subject'];
$body = $_POST['activation_notification_email_body'];
$headers = 'From: '.$sender;
wp_mail($receiver, $subject, $body, $headers); }
elseif (($_POST['status'] == 'inactive') && ($_POST['deactivation_notification_email_sent'] == 'yes')) {
$sender = $_POST['deactivation_notification_email_sender'];
$receiver = $_POST['deactivation_notification_email_receiver'];
$subject = $_POST['deactivation_notification_email_subject'];
$body = $_POST['deactivation_notification_email_body'];
$headers = 'From: '.$sender;
wp_mail($receiver, $subject, $body, $headers); } }
else {
if ($_POST['name'] != '') {
$result = $wpdb->get_results("SELECT name FROM ".$wpdb->prefix."affiliation_manager_affiliates_categories WHERE name = '".$_POST['name']."' AND id != '".$_GET['id']."'", OBJECT);
if ($result) { $error .= ' '.__('This name is not available.', 'affiliation-manager'); }
else { $results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_affiliates_categories SET name = '".$_POST['name']."' WHERE id = '".$_GET['id']."'"); } }
foreach ($tables['affiliates_categories'] as $key => $value) { switch ($key) {
case 'id': case 'name': break;
default: $list .= $key." = '".$_POST[$key]."',"; } } }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_".$table_slug." SET ".substr($list, 0, -1)." WHERE id = '".$_GET['id']."'"); } }

if (isset($_GET['id'])) {
$item_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_".$table_slug." WHERE id = '".$_GET['id']."'", OBJECT);
if ($item_data) {
if (!$is_category) { $_GET['affiliate_data'] = (array) $item_data; }
foreach ($item_data as $key => $value) { $_POST[$key] = $value; } }
elseif (!headers_sent()) { header('Location: admin.php?page='.$_GET['page']); exit(); } }

$_POST = array_map('stripslashes', $_POST);
$_POST = array_map('htmlspecialchars', $_POST);
foreach ($_POST as $key => $value) {
$_POST[$key] = str_replace(array('&amp;amp;', '&amp;apos;', '&amp;quot;'), array('&amp;', '&apos;', '&quot;'), $value);
if ($value == '0000-00-00 00:00:00') { $_POST[$key] = ''; } }
$undisplayed_modules = (array) $back_office_options[$admin_page.'_page_undisplayed_modules'];
if (function_exists('commerce_data')) { $currency_code = commerce_data('currency_code'); }
else { $commerce_manager_options = (array) get_option('commerce_manager');
$currency_code = do_shortcode($commerce_manager_options['currency_code']); } ?>

<div class="wrap">
<div id="poststuff">
<?php affiliation_manager_pages_top($back_office_options); ?>
<?php if ($updated) { echo '<div class="updated"><p><strong>'.(isset($_GET['id']) ? ($is_category ? __('Category updated.', 'affiliation-manager') : __('Affiliate updated.', 'affiliation-manager')) : ($is_category ? __('Category saved.', 'affiliation-manager') : __('Affiliate saved.', 'affiliation-manager'))).'</strong></p></div>'; } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php affiliation_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<p class="description"><?php _e('Fields marked with * are required.', 'affiliation-manager'); ?> 
<?php if ($_POST['category_id'] > 0) { _e('You can apply the default option of the category by leaving the corresponding field blank.', 'affiliation-manager'); } ?></p>
<?php affiliation_manager_pages_summary($back_office_options); ?>
<?php if ($is_category) { $module = 'general-informations'; } else { $module = 'personal-informations'; } ?>

<div class="postbox"<?php if (in_array($module, $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="<?php echo $module; ?>"><strong><?php echo $modules[$admin_page][$module]['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="id">'.__('ID', 'affiliation-manager').'</label></strong></th>
<td><input type="text" name="id" id="id" size="10" value="'.$_GET['id'].'" disabled="disabled" /> <span class="description">'.__('The ID can not be changed.', 'affiliation-manager').'</span></td></tr>'; } ?>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."affiliation_manager_affiliates_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="category_id"><?php echo ($is_category ? __('Parent category', 'affiliation-manager') : __('Category', 'affiliation-manager')); ?></label></strong></th>
<td><select name="category_id" id="category_id">
<option value="0"<?php if ($_POST['category_id'] == 0) { echo ' selected="selected"'; } ?>><?php _e('None ', 'affiliation-manager'); ?></option>
<?php foreach ($categories as $category) {
if ((!$is_category) || (!in_array($_GET['id'], affiliates_categories_list($category->id)))) {
echo '<option value="'.$category->id.'"'.($_POST['category_id'] == $category->id ? ' selected="selected"' : '').'>'.$category->name.'</option>'."\n"; } } ?>
</select>
<span class="description"><?php ($is_category ? _e('The options of this category will apply by default to the category.', 'affiliation-manager') : _e('The options of this category will apply by default to the affiliate.', 'affiliation-manager')); ?></span>
<?php if ($_POST['category_id'] > 0) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$_POST['category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$_POST['category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<?php if ($is_category) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['name'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="name"><?php _e('Name', 'affiliation-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="name" id="name" rows="1" cols="50"><?php echo $_POST['name']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="description"><?php _e('Description', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="description" id="description" rows="1" cols="75"><?php echo $_POST['description']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="keywords"><?php _e('Keywords', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="keywords" id="keywords" rows="1" cols="75"><?php echo $_POST['keywords']; ?></textarea><br />
<span class="description"><?php _e('Separate the keywords with commas.', 'affiliation-manager'); ?></span></td></tr>
<?php } else { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['login'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="login"><?php _e('Login name', 'affiliation-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="login" id="login" rows="1" cols="25"<?php if (isset($_GET['id'])) { echo ' disabled="disabled"'; } ?>><?php echo $_POST['login']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php echo (isset($_GET['id']) ? __('The login name can not be changed.', 'affiliation-manager') : __('Letters, numbers, hyphens and underscores only', 'affiliation-manager').'<br />'
.__('The login name will be included in affiliate links and can not be changed.', 'affiliation-manager')); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password"><?php _e('Password', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="password" id="password" rows="1" cols="25"><?php echo (isset($_GET['id']) ? '' : $_POST['password']); ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php (isset($_GET['id']) ? _e('(if you want to change it)', 'affiliation-manager') : _e('Leave this field blank to automatically generate a random password.', 'affiliation-manager')); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['first_name'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="first_name"><?php _e('First name', 'affiliation-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="first_name" id="first_name" rows="1" cols="50"><?php echo $_POST['first_name']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['last_name'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="last_name"><?php _e('Last name', 'affiliation-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="last_name" id="last_name" rows="1" cols="50"><?php echo $_POST['last_name']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['email_address'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="email_address"><?php _e('Email address', 'affiliation-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="email_address" id="email_address" rows="1" cols="50"><?php echo $_POST['email_address']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['paypal_email_address'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="paypal_email_address"><?php _e('PayPal email address', 'affiliation-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="paypal_email_address" id="paypal_email_address" rows="1" cols="50"><?php echo $_POST['paypal_email_address']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="website_name"><?php _e('Website name', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="website_name" id="website_name" rows="1" cols="50"><?php echo $_POST['website_name']; ?></textarea> 
<?php $url = htmlspecialchars(affiliate_data(array(0 => 'website_url', 'part' => 1, $attribute => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'affiliation-manager'); ?></a><?php } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="website_url"><?php _e('Website URL', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="website_url" id="website_url" rows="1" cols="75"><?php echo $_POST['website_url']; ?></textarea> 
<?php $url = htmlspecialchars(affiliate_data(array(0 => 'website_url', 'part' => 1, $attribute => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'affiliation-manager'); ?></a><?php } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="address"><?php _e('Address', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="address" id="address" rows="1" cols="50"><?php echo $_POST['address']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="postcode"><?php _e('Postcode', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="postcode" id="postcode" rows="1" cols="50"><?php echo $_POST['postcode']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="town"><?php _e('Town', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="town" id="town" rows="1" cols="50"><?php echo $_POST['town']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="country"><?php _e('Country', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="country" id="country" rows="1" cols="50"><?php echo $_POST['country']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="phone_number"><?php _e('Phone number', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="phone_number" id="phone_number" rows="1" cols="50"><?php echo $_POST['phone_number']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="ip_address"><?php _e('IP address', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="ip_address" id="ip_address" rows="1" cols="50"><?php echo $_POST['ip_address']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="user_agent"><?php _e('User agent', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="user_agent" id="user_agent" rows="1" cols="75"><?php echo $_POST['user_agent']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="referring_url"><?php _e('Referring URL', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="referring_url" id="referring_url" rows="1" cols="75"><?php echo $_POST['referring_url']; ?></textarea> 
<?php $url = htmlspecialchars(affiliate_data(array(0 => 'referring_url', 'part' => 1, $attribute => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'affiliation-manager'); ?></a><?php } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="status"><?php _e('Status', 'affiliation-manager'); ?></label></strong></th>
<td><select name="status" id="status"<?php if (isset($_GET['id'])) { echo 'onchange="display_notification_email_module();"'; } ?>>
<option value="active"<?php if ($_POST['status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'affiliation-manager'); ?></option>
<option value="inactive"<?php if ($_POST['status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'affiliation-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#affiliate-status"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="date"><?php ($is_category ? _e('Creation date', 'affiliation-manager') : _e('Registration date', 'affiliation-manager')); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="date" id="date" size="20" value="<?php echo (isset($_POST['date']) ? $_POST['date'] : date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET)); ?>" /></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<?php if ((isset($_GET['id'])) && (!$is_category)) { ?>
<script type="text/javascript">
function display_notification_email_module() {
if (document.forms[0].status.value == '<?php echo $_POST['status']; ?>') {
document.getElementById('notification_email').style.display = 'none'; }
else { document.getElementById('notification_email').style.display = 'block'; } }
</script>

<?php if ($_POST['status'] == 'inactive') {
foreach (array(
'activation_notification_email_sender',
'activation_notification_email_receiver',
'activation_notification_email_subject',
'activation_notification_email_body') as $field) { $_POST[$field] = htmlspecialchars(affiliation_data($field)); } ?>

<div class="postbox" id="notification_email" style="display: none;">
<h3 id="activation-notification-email"><strong><?php _e('Activation notification email', 'affiliation-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=affiliation-manager#activation-notification-email"><?php _e('Click here to configure the default options.', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="activation_notification_email_sent" id="activation_notification_email_sent" value="yes" /> <?php _e('Send an activation notification email', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_sender" id="activation_notification_email_sender" rows="1" cols="75"><?php echo $_POST['activation_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_receiver" id="activation_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['activation_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_subject" id="activation_notification_email_subject" rows="1" cols="75"><?php echo $_POST['activation_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="activation_notification_email_body" id="activation_notification_email_body" rows="15" cols="75"><?php echo $_POST['activation_notification_email_body']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<?php } else {
foreach (array(
'deactivation_notification_email_sender',
'deactivation_notification_email_receiver',
'deactivation_notification_email_subject',
'deactivation_notification_email_body') as $field) { $_POST[$field] = htmlspecialchars(affiliation_data($field)); } ?>

<div class="postbox" id="notification_email" style="display: none;">
<h3 id="deactivation-notification-email"><strong><?php _e('Deactivation notification email', 'affiliation-manager'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=affiliation-manager#deactivation-notification-email"><?php _e('Click here to configure the default options.', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="deactivation_notification_email_sent" id="deactivation_notification_email_sent" value="yes" /> <?php _e('Send a deactivation notification email', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="deactivation_notification_email_sender" id="deactivation_notification_email_sender" rows="1" cols="75"><?php echo $_POST['deactivation_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="deactivation_notification_email_receiver" id="deactivation_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['deactivation_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="deactivation_notification_email_subject" id="deactivation_notification_email_subject" rows="1" cols="75"><?php echo $_POST['deactivation_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="deactivation_notification_email_body" id="deactivation_notification_email_body" rows="15" cols="75"><?php echo $_POST['deactivation_notification_email_body']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<?php } } ?>

<div class="postbox"<?php if (in_array('bonus-offered-to-customer', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="bonus-offered-to-customer"><strong><?php echo $modules[$admin_page]['bonus-offered-to-customer']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<?php if ($_POST['category_id'] > 0) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=affiliation-manager-affiliate-category&amp;id=<?php echo $_POST['category_id']; ?>#bonus-offered-to-customer">
<?php ($is_category ? _e('Click here to configure the default options of the parent category.', 'affiliation-manager') : _e('Click here to configure the default options of the category.', 'affiliation-manager')); ?></a></span></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="bonus_download_url"><?php _e('Download URL', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="bonus_download_url" id="bonus_download_url" rows="1" cols="75"><?php echo $_POST['bonus_download_url']; ?></textarea> 
<?php $url = htmlspecialchars(affiliate_data(array(0 => 'bonus_download_url', 'part' => 1, $attribute => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'affiliation-manager'); ?></a><?php } ?><br />
<span class="description"><?php _e('You can specify several URLs.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#multiple-urls"><?php _e('More informations', 'affiliation-manager'); ?></a> 
<a href="http://www.kleor-editions.com/affiliation-manager/documentation/#urls-encryption"><?php _e('How to encrypt a download URL?', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="bonus_instructions"><?php _e('Instructions to the customer', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="bonus_instructions" id="bonus_instructions" rows="5" cols="75"><?php echo $_POST['bonus_instructions']; ?></textarea>
<span class="description"><?php _e('You can allow your affiliates to offer a bonus to customers who order through their affiliate link.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#bonus-offered-by-affiliate"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('affiliation', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="affiliation"><strong><?php echo $modules[$admin_page]['affiliation']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=affiliation-manager<?php echo ($_POST['category_id'] == 0 ? '' : '-affiliate-category&amp;id='.$_POST['category_id'].'#affiliation'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'affiliation-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'affiliation-manager') : _e('Click here to configure the default options of the category.', 'affiliation-manager'))); ?></a></span></td></tr>
<?php if (!$is_category) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="referrer"><?php _e('Referrer', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="referrer" id="referrer" rows="1" cols="25"><?php echo $_POST['referrer']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Affiliate who referred this affiliate (ID, login name or email address)', 'affiliation-manager'); ?></span> 
<?php if (($_POST['referrer'] != '') && (!strstr($_POST['referrer'], '@'))) {
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['referrer']."'", OBJECT);
if ($result) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate&amp;id='.$result->id.'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate&amp;id='.$result->id.'&amp;action=delete">'.__('Delete').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-statistics&amp;referrer='.$_POST['referrer'].'">'.__('Statistics', 'commerce-manager').'</a>'; } } ?></td></tr>
<?php } ?>
</tbody></table>
<div<?php if (in_array('level-1-commission', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="level-1-commission"><strong><?php echo $modules[$admin_page]['affiliation']['modules']['level-1-commission']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('The level 1 commission is awarded to the affiliate when he refers himself an order.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#commissions-levels"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_type"><?php _e('Commission type', 'affiliation-manager'); ?></label></strong></th>
<td><select name="commission_type" id="commission_type">
<option value=""<?php if ($_POST['commission_type'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'affiliation-manager'); ?></option>
<option value="constant"<?php if ($_POST['commission_type'] == 'constant') { echo ' selected="selected"'; } ?>><?php _e('Constant', 'affiliation-manager'); ?></option>
<option value="proportional"<?php if ($_POST['commission_type'] == 'proportional') { echo ' selected="selected"'; } ?>><?php _e('Proportional', 'affiliation-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_amount"><?php _e('Commission amount', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_amount" id="commission_amount" rows="1" cols="25"><?php echo $_POST['commission_amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you select constant commissions', 'affiliation-manager'); ?><br />
<?php _e('Leave this field blank to apply the default option.', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_percentage"><?php _e('Commission percentage', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_percentage" id="commission_percentage" rows="1" cols="25"><?php echo $_POST['commission_percentage']; ?></textarea> <span style="vertical-align: 25%;">% <?php _e('of the price', 'affiliation-manager'); ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you select proportional commissions', 'affiliation-manager'); ?><br />
<?php _e('Leave this field blank to apply the default option.', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_payment"><?php _e('Commission payment', 'affiliation-manager'); ?></label></strong></th>
<td><select name="commission_payment" id="commission_payment">
<option value=""<?php if ($_POST['commission_payment'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'affiliation-manager'); ?></option>
<option value="deferred"<?php if ($_POST['commission_payment'] == 'deferred') { echo ' selected="selected"'; } ?>><?php _e('Deferred', 'affiliation-manager'); ?></option>
<option value="instant"<?php if ($_POST['commission_payment'] == 'instant') { echo ' selected="selected"'; } ?>><?php _e('Instant', 'affiliation-manager'); ?></option>
</select>
<span class="description"><?php _e('You can pay your affiliates instantly.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#commission-payment"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="first_sale_winner"><?php _e('First sale award', 'affiliation-manager'); ?></label></strong></th>
<td><?php _e('The first sale referred by the affiliate is awarded to the', 'affiliation-manager'); ?> <select name="first_sale_winner" id="first_sale_winner">
<option value=""<?php if ($_POST['first_sale_winner'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'affiliation-manager'); ?></option>
<option value="affiliate"<?php if ($_POST['first_sale_winner'] == 'affiliate') { echo ' selected="selected"'; } ?>><?php _e('affiliate', 'affiliation-manager'); ?></option>
<option value="affiliator"<?php if ($_POST['first_sale_winner'] == 'affiliator') { echo ' selected="selected"'; } ?>><?php _e('affiliator', 'affiliation-manager'); ?></option>
</select>. 
<span class="description"><?php _e('Used for instant payment of commissions', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#first-sale-award"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div<?php if (in_array('level-2-commission', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="level-2-commission"><strong><?php echo $modules[$admin_page]['affiliation']['modules']['level-2-commission']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('The level 2 commission is awarded to the affiliate when an affiliate referred by him refers an order.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#commissions-levels"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_enabled"><?php _e('Award a level 2 commission', 'affiliation-manager'); ?></label></strong></th>
<td><select name="commission2_enabled" id="commission2_enabled">
<option value=""<?php if ($_POST['commission2_enabled'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'affiliation-manager'); ?></option>
<option value="yes"<?php if ($_POST['commission2_enabled'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'affiliation-manager'); ?></option>
<option value="no"<?php if ($_POST['commission2_enabled'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'affiliation-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_type"><?php _e('Commission type', 'affiliation-manager'); ?></label></strong></th>
<td><select name="commission2_type" id="commission2_type">
<option value=""<?php if ($_POST['commission2_type'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'affiliation-manager'); ?></option>
<option value="constant"<?php if ($_POST['commission2_type'] == 'constant') { echo ' selected="selected"'; } ?>><?php _e('Constant', 'affiliation-manager'); ?></option>
<option value="proportional"<?php if ($_POST['commission2_type'] == 'proportional') { echo ' selected="selected"'; } ?>><?php _e('Proportional', 'affiliation-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_amount"><?php _e('Commission amount', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission2_amount" id="commission2_amount" rows="1" cols="25"><?php echo $_POST['commission2_amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you select constant commissions', 'affiliation-manager'); ?><br />
<?php _e('Leave this field blank to apply the default option.', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_percentage"><?php _e('Commission percentage', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission2_percentage" id="commission2_percentage" rows="1" cols="25"><?php echo $_POST['commission2_percentage']; ?></textarea> <span style="vertical-align: 25%;">% <?php _e('of the price', 'affiliation-manager'); ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you select proportional commissions', 'affiliation-manager'); ?><br />
<?php _e('Leave this field blank to apply the default option.', 'affiliation-manager'); ?></span></td></tr>
</tbody></table>
</div>
<?php if (isset($_GET['id'])) { echo '<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr></tbody></table>'; } ?>
</div></div>

<?php if ((!$is_category) && (!isset($_GET['id']))) {
if (!isset($_POST['submit'])) {
$affiliation_manager_options = (array) get_option('affiliation_manager');
$affiliation_manager_options = array_map('htmlspecialchars', $affiliation_manager_options);
foreach (add_affiliate_fields() as $field) { $_POST[$field] = $affiliation_manager_options[$field]; } }
if ((!in_array('registration-confirmation-email', $undisplayed_modules)) || (!in_array('registration-notification-email', $undisplayed_modules)) || (!in_array('autoresponders', $undisplayed_modules)) || (!in_array('membership', $undisplayed_modules)) || (!in_array('custom-instructions', $undisplayed_modules))) { ?>
<p class="submit" style="margin: 0 20%;"><input type="hidden" name="submit" value="true" />
<input type="submit" class="button-secondary" name="update_fields" value="<?php _e('Complete the fields below with the informations about the affiliate', 'affiliation-manager'); ?>" /></p><?php } ?>

<div id="add-affiliate-modules">
<?php if (!in_array('registration-confirmation-email', $undisplayed_modules)) {
if (!isset($_POST['submit'])) { $_POST['registration_confirmation_email_body'] = htmlspecialchars(get_option('affiliation_manager_registration_confirmation_email_body')); } ?>
<div class="postbox">
<h3 id="registration-confirmation-email"><strong><?php echo $modules[$admin_page]['registration-confirmation-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=affiliation-manager#registration-confirmation-email"><?php _e('Click here to configure the default options.', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="registration_confirmation_email_sent" id="registration_confirmation_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['registration_confirmation_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration confirmation email', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_sender" id="registration_confirmation_email_sender" rows="1" cols="75"><?php echo $_POST['registration_confirmation_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_receiver" id="registration_confirmation_email_receiver" rows="1" cols="75"><?php echo $_POST['registration_confirmation_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_subject" id="registration_confirmation_email_subject" rows="1" cols="75"><?php echo $_POST['registration_confirmation_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_confirmation_email_body" id="registration_confirmation_email_body" rows="15" cols="75"><?php echo $_POST['registration_confirmation_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('registration-notification-email', $undisplayed_modules)) {
if (!isset($_POST['submit'])) { $_POST['registration_notification_email_body'] = htmlspecialchars(get_option('affiliation_manager_registration_notification_email_body')); } ?>
<div class="postbox">
<h3 id="registration-notification-email"><strong><?php echo $modules[$admin_page]['registration-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=affiliation-manager#registration-notification-email"><?php _e('Click here to configure the default options.', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="registration_notification_email_sent" id="registration_notification_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['registration_notification_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration notification email', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_sender" id="registration_notification_email_sender" rows="1" cols="75"><?php echo $_POST['registration_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_receiver" id="registration_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['registration_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_subject" id="registration_notification_email_subject" rows="1" cols="75"><?php echo $_POST['registration_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_notification_email_body" id="registration_notification_email_body" rows="15" cols="75"><?php echo $_POST['registration_notification_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('autoresponders', $undisplayed_modules)) { ?>
<div class="postbox">
<h3 id="autoresponders"><strong><?php echo $modules[$admin_page]['autoresponders']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=affiliation-manager#autoresponders"><?php _e('Click here to configure the default options.', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="affiliate_subscribed_to_autoresponder" id="affiliate_subscribed_to_autoresponder" value="yes"<?php if ($_POST['affiliate_subscribed_to_autoresponder'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Subscribe the affiliate to an autoresponder list', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliate_autoresponder"><?php _e('Autoresponder', 'affiliation-manager'); ?></label></strong></th>
<td><select name="affiliate_autoresponder" id="affiliate_autoresponder">
<?php include 'autoresponders.php';
$autoresponder = do_shortcode($_POST['affiliate_autoresponder']);
foreach ($autoresponders as $value) {
echo '<option value="'.$value.'"'.($autoresponder == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliate_autoresponder_list"><?php _e('List', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="affiliate_autoresponder_list" id="affiliate_autoresponder_list" rows="1" cols="50"><?php echo $_POST['affiliate_autoresponder_list']; ?></textarea><br />
<span class="description"><?php _e('For some autoresponders, you must enter the list ID.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#autoresponders"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('membership', $undisplayed_modules)) { ?>
<div class="postbox">
<h3 id="membership"><strong><?php echo $modules[$admin_page]['membership']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php if (function_exists('membership_manager_admin_menu')) { ?>
<a href="admin.php?page=affiliation-manager#membership"><?php _e('Click here to configure the default options.', 'affiliation-manager'); ?></a>
<?php } else { _e('To use membership, you must have installed and activated <a href="http://www.kleor-editions.com/membership-manager">Membership Manager</a>.', 'affiliation-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="affiliate_subscribed_to_members_areas" id="affiliate_subscribed_to_members_areas" value="yes"<?php if ($_POST['affiliate_subscribed_to_members_areas'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Subscribe the affiliate to a member area', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliate_members_areas"><?php _e('Members areas', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="affiliate_members_areas" id="affiliate_members_areas" rows="1" cols="50"><?php echo $_POST['affiliate_members_areas']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#membership"><?php _e('More informations', 'affiliation-manager'); ?></a></span>
<?php if ((function_exists('membership_manager_admin_menu')) && (is_numeric($_POST['affiliate_members_areas'])) && ($_POST['affiliate_members_areas'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-area&amp;id='.$_POST['affiliate_members_areas'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-area&amp;id='.$_POST['affiliate_members_areas'].'&amp;action=delete">'.__('Delete').'</a>'; } ?><br />
<span class="description"><?php _e('Separate the IDs of the members areas with commas.', 'affiliation-manager'); ?></span></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('custom-instructions', $undisplayed_modules)) {
if (!isset($_POST['submit'])) { $_POST['registration_custom_instructions'] = htmlspecialchars(get_option('affiliation_manager_registration_custom_instructions')); } ?>
<div class="postbox">
<h3 id="custom-instructions"><strong><?php echo $modules[$admin_page]['custom-instructions']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=affiliation-manager#custom-instructions"><?php _e('Click here to configure the default options.', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="registration_custom_instructions_executed" id="registration_custom_instructions_executed" value="yes"<?php if ($_POST['registration_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_custom_instructions"><?php _e('PHP code', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_custom_instructions" id="registration_custom_instructions" rows="10" cols="75"><?php echo $_POST['registration_custom_instructions']; ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the registration of the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#custom-instructions"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<?php } ?>
</div>

<?php if (($updated) && ($_GET['autoresponder_subscription'] != '')) { echo '<div><img alt="" src="'.$_GET['autoresponder_subscription'].'" /></div>'; } } ?>
<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php (isset($_GET['id']) ? _e('Save Changes', 'affiliation-manager') : ($is_category ? _e('Save Category', 'affiliation-manager') : _e('Save Affiliate', 'affiliation-manager'))); ?>" /></p>
<?php if ($is_category) { $module = 'affiliate-category-page'; } else { $module = 'affiliate-page'; }
affiliation_manager_pages_module($back_office_options, $module, $undisplayed_modules); ?>
</form>
</div>
</div>
<?php if (isset($_POST['update_fields'])) { ?>
<script type="text/javascript">window.location = '#add-affiliate-modules';</script>
<?php } }