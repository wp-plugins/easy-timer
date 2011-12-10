<?php global $wpdb;
$back_office_options = get_option('optin_manager_back_office');
$is_category = (strstr($_GET['page'], 'category'));
if ($is_category) { $admin_page = 'prospect_category'; $table_slug = 'prospects_categories'; $attribute = 'category'; }
else { $admin_page = 'prospect'; $table_slug = 'prospects'; $attribute = 'id'; }

if ((isset($_GET['id'])) && ($_GET['action'] == 'delete')) {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if ($is_category) {
$category = $wpdb->get_row("SELECT category_id FROM ".$wpdb->prefix."optin_manager_prospects_categories WHERE id = '".$_GET['id']."'", OBJECT);
foreach (array('prospects', 'prospects_categories') as $table) {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."optin_manager_".$table." SET category_id = ".$category->category_id." WHERE category_id = '".$_GET['id']."'"); }
$options = (array) get_option('optin_manager');
if ($options['prospects_initial_category_id'] = $_GET['id']) { $options['prospects_initial_category_id'] = $category->category_id; }
update_option('optin_manager', $options); }
$results = $wpdb->query("DELETE FROM ".$wpdb->prefix."optin_manager_".$table_slug." WHERE id = '".$_GET['id']."'"); } ?>
<div class="wrap">
<div id="poststuff">
<?php optin_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.($is_category ? __('Category deleted.', 'optin-manager') : __('Prospect deleted.', 'optin-manager')).'</strong></p></div>'; } ?>
<?php optin_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php echo ($is_category ? __('Do you really want to permanently delete this category?', 'optin-manager') : __('Do you really want to permanently delete this prospect?', 'optin-manager')); ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'optin-manager'); ?>" />
</div>
<div class="clear"></div>
</form><?php } ?>
</div>
</div><?php }

else {
include 'admin-pages.php';
add_action('admin_footer', 'optin_date_picker_js');
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
update_option('optin_manager_back_office', $back_office_options);

if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
if (!$is_category) {
$_POST['email_address'] = optin_format_email_address($_POST['email_address']);
$_POST['form_id'] = (int) $_POST['form_id'];
$_GET['optin_form_id'] = $_POST['form_id'];
if (isset($_POST['referrer'])) {
if (is_numeric($_POST['referrer'])) {
$_POST['referrer'] = preg_replace('/[^0-9]/', '', $_POST['referrer']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE id = '".$_POST['referrer']."'", OBJECT);
if ($result) { $_POST['referrer'] = $result->login; } }
elseif (strstr($_POST['referrer'], '@')) {
$_POST['referrer'] = optin_format_email_address($_POST['referrer']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE paypal_email_address = '".$_POST['referrer']."'", OBJECT);
if ($result) { $_POST['referrer'] = $result->login; }
else { $result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".$_POST['referrer']."'", OBJECT);
if ($result) { $_POST['referrer'] = $result->login; } } }
else {
$_POST['referrer'] = optin_format_nice_name($_POST['referrer']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['referrer']."'", OBJECT);
if (!$result) { $_POST['referrer'] = ''; } } }
if ($_POST['referrer'] == '') {
$_POST['commission_amount'] = 0;
$_POST['commission_status'] = '';
$_POST['commission_payment_date'] = ''; }
else {
$_POST['commission_amount'] = str_replace(array('?', ',', ';'), '.', $_POST['commission_amount']);
$_POST['commission_amount'] = round(100*$_POST['commission_amount'])/100; if ($_POST['commission_amount'] <= 0) { $_POST['commission_amount'] = 0; }
if ($_POST['commission_amount'] == 0) {
$_POST['commission_status'] = '';
$_POST['commission_payment_date'] = ''; }
elseif ($_POST['commission_status'] == '') { $_POST['commission_status'] = 'unpaid'; }
if ($_POST['commission_status'] == 'paid') {
if ($_POST['commission_payment_date'] == '') {
$_POST['commission_payment_date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['commission_payment_date_utc'] = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['commission_payment_date'], 0, PREG_SPLIT_NO_EMPTY);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['commission_payment_date'] = date('Y-m-d H:i:s', $time);
$_POST['commission_payment_date_utc'] = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); } }
else { $_POST['commission_payment_date'] = ''; } }
if (($_POST['referrer2'] == '') && ($_POST['referrer'] != '')) {
$result = $wpdb->get_row("SELECT referrer FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['referrer']."'", OBJECT);
if ($result) { $_POST['referrer2'] = $result->referrer; } }
else {
if (is_numeric($_POST['referrer2'])) {
$_POST['referrer2'] = preg_replace('/[^0-9]/', '', $_POST['referrer2']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE id = '".$_POST['referrer2']."'", OBJECT);
if ($result) { $_POST['referrer2'] = $result->login; } }
elseif (strstr($_POST['referrer2'], '@')) {
$_POST['referrer2'] = optin_format_email_address($_POST['referrer2']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE paypal_email_address = '".$_POST['referrer2']."'", OBJECT);
if ($result) { $_POST['referrer2'] = $result->login; }
else { $result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".$_POST['referrer2']."'", OBJECT);
if ($result) { $_POST['referrer2'] = $result->login; } } }
else {
$_POST['referrer2'] = optin_format_nice_name($_POST['referrer2']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['referrer2']."'", OBJECT);
if (!$result) { $_POST['referrer2'] = ''; } } }
if ($_POST['referrer2'] == '') {
$_POST['commission2_amount'] = 0;
$_POST['commission2_status'] = '';
$_POST['commission2_payment_date'] = ''; }
else {
$_POST['commission2_amount'] = str_replace(array('?', ',', ';'), '.', $_POST['commission2_amount']);
$_POST['commission2_amount'] = round(100*$_POST['commission2_amount'])/100; if ($_POST['commission2_amount'] <= 0) { $_POST['commission2_amount'] = 0; }
if ($_POST['commission2_amount'] == 0) {
$_POST['commission2_status'] = '';
$_POST['commission2_payment_date'] = ''; }
elseif ($_POST['commission2_status'] == '') { $_POST['commission2_status'] = 'unpaid'; }
if ($_POST['commission2_status'] == 'paid') {
if ($_POST['commission2_payment_date'] == '') {
$_POST['commission2_payment_date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['commission2_payment_date_utc'] = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['commission2_payment_date'], 0, PREG_SPLIT_NO_EMPTY);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['commission2_payment_date'] = date('Y-m-d H:i:s', $time);
$_POST['commission2_payment_date_utc'] = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); } }
else { $_POST['commission2_payment_date'] = ''; } } }
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
if ($_POST['referring_url'] == '') { $_POST['referring_url'] = $_SERVER['HTTP_REFERER']; }
if (isset($_POST['update_fields'])) {
foreach ($_POST as $key => $value) { $_GET['prospect_data'][$key] = $value; }
$_GET['prospect_data']['id'] = '{prospect id}';
if ($_POST['referrer'] != '') { $_GET['affiliate_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['referrer']."'", OBJECT); }
foreach (add_prospect_fields() as $field) { $_POST[$field] = str_replace('{prospect id}', '[prospect id]', optin_form_data($field)); } }
else {
$members_areas = array_unique(preg_split('#[^0-9]#', $_POST['prospect_members_areas'], 0, PREG_SPLIT_NO_EMPTY));
sort($members_areas, SORT_NUMERIC);
foreach ($members_areas as $member_area) { $members_areas_list .= $member_area.', '; }
$_POST['prospect_members_areas'] = substr($members_areas_list, 0, -2);
if (($_POST['email_address'] == '') || ($_POST['autoresponder_list'] == '')) { $error .= ' '.__('Please fill out the required fields.', 'optin-manager'); }
if ($error == '') {
$result = $wpdb->get_results("SELECT id FROM ".$wpdb->prefix."optin_manager_prospects WHERE email_address = '".$_POST['email_address']."' AND autoresponder = '".$_POST['autoresponder']."' AND autoresponder_list = '".$_POST['autoresponder_list']."'", OBJECT);
if (!$result) { $updated = true; add_prospect($_POST); } } } }
else {
if ($_POST['name'] == '') { $error .= ' '.__('Please fill out the required fields.', 'optin-manager'); }
else {
$result = $wpdb->get_results("SELECT name FROM ".$wpdb->prefix."optin_manager_prospects_categories WHERE name = '".$_POST['name']."'", OBJECT);
if ($result) { $error .= ' '.__('This name is not available.', 'optin-manager'); } }
if ($error == '') {
$updated = true;
include 'tables.php';
foreach ($tables['prospects_categories'] as $key => $value) { $keys_list .= $key.","; $values_list .= "'".$_POST[$key]."',"; }
$results = $wpdb->query("INSERT INTO ".$wpdb->prefix."optin_manager_prospects_categories (".substr($keys_list, 0, -1).") VALUES(".substr($values_list, 0, -1).")"); } } }

if (isset($_GET['id'])) {
$updated = true;
include 'tables.php';
if (!$is_category) {
if ($_POST['email_address'] != '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."optin_manager_prospects SET email_address = '".$_POST['email_address']."' WHERE id = '".$_GET['id']."'"); }
if ($_POST['autoresponder_list'] != '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."optin_manager_prospects SET autoresponder_list = '".$_POST['autoresponder_list']."' WHERE id = '".$_GET['id']."'"); }
foreach ($tables['prospects'] as $key => $value) { switch ($key) {
case 'id': case 'email_address': case 'autoresponder_list': break;
default: $list .= $key." = '".$_POST[$key]."',"; } } }
else {
if ($_POST['name'] != '') {
$result = $wpdb->get_results("SELECT name FROM ".$wpdb->prefix."optin_manager_prospects_categories WHERE name = '".$_POST['name']."' AND id != '".$_GET['id']."'", OBJECT);
if ($result) { $error .= ' '.__('This name is not available.', 'optin-manager'); }
else { $results = $wpdb->query("UPDATE ".$wpdb->prefix."optin_manager_prospects_categories SET name = '".$_POST['name']."' WHERE id = '".$_GET['id']."'"); } }
foreach ($tables['prospects_categories'] as $key => $value) { switch ($key) {
case 'id': case 'name': break;
default: $list .= $key." = '".$_POST[$key]."',"; } } }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."optin_manager_".$table_slug." SET ".substr($list, 0, -1)." WHERE id = '".$_GET['id']."'"); } }

if (isset($_GET['id'])) {
$item_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_".$table_slug." WHERE id = '".$_GET['id']."'", OBJECT);
if ($item_data) {
if (!$is_category) { $_GET['prospect_data'] = (array) $item_data; }
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
<?php optin_manager_pages_top($back_office_options); ?>
<?php if ($updated) { echo '<div class="updated"><p><strong>'.(isset($_GET['id']) ? ($is_category ? __('Category updated.', 'optin-manager') : __('Prospect updated.', 'optin-manager')) : ($is_category ? __('Category saved.', 'optin-manager') : __('Prospect saved.', 'optin-manager'))).'</strong></p></div>'; } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php optin_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<p class="description"><?php _e('Fields marked with * are required.', 'optin-manager'); ?>
<?php optin_manager_pages_summary($back_office_options); ?>
<?php if ($is_category) { $module = 'general-informations'; } else { $module = 'personal-informations'; } ?>

<div class="postbox"<?php if (in_array($module, $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="<?php echo $module; ?>"><strong><?php echo $modules[$admin_page][$module]['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="id">'.__('ID', 'optin-manager').'</label></strong></th>
<td><input type="text" name="id" id="id" size="10" value="'.$_GET['id'].'" disabled="disabled" /> <span class="description">'.__('The ID can not be changed.', 'optin-manager').'</span></td></tr>'; } ?>
<?php if ($is_category) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['name'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="name"><?php _e('Name', 'optin-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="name" id="name" rows="1" cols="50"><?php echo $_POST['name']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="description"><?php _e('Description', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="description" id="description" rows="1" cols="75"><?php echo $_POST['description']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="keywords"><?php _e('Keywords', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="keywords" id="keywords" rows="1" cols="75"><?php echo $_POST['keywords']; ?></textarea><br />
<span class="description"><?php _e('Separate the keywords with commas.', 'optin-manager'); ?></span></td></tr>
<?php } else { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="first_name"><?php _e('First name', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="first_name" id="first_name" rows="1" cols="50"><?php echo $_POST['first_name']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="last_name"><?php _e('Last name', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="last_name" id="last_name" rows="1" cols="50"><?php echo $_POST['last_name']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['email_address'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="email_address"><?php _e('Email address', 'optin-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="email_address" id="email_address" rows="1" cols="50"><?php echo $_POST['email_address']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="website_name"><?php _e('Website name', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="website_name" id="website_name" rows="1" cols="50"><?php echo $_POST['website_name']; ?></textarea> 
<?php $url = htmlspecialchars(prospect_data(array(0 => 'website_url', 'part' => 1, $attribute => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'optin-manager'); ?></a><?php } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="website_url"><?php _e('Website URL', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="website_url" id="website_url" rows="1" cols="75"><?php echo $_POST['website_url']; ?></textarea> 
<?php $url = htmlspecialchars(prospect_data(array(0 => 'website_url', 'part' => 1, $attribute => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'optin-manager'); ?></a><?php } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="address"><?php _e('Address', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="address" id="address" rows="1" cols="50"><?php echo $_POST['address']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="postcode"><?php _e('Postcode', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="postcode" id="postcode" rows="1" cols="50"><?php echo $_POST['postcode']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="town"><?php _e('Town', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="town" id="town" rows="1" cols="50"><?php echo $_POST['town']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="country"><?php _e('Country', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="country" id="country" rows="1" cols="50"><?php echo $_POST['country']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="phone_number"><?php _e('Phone number', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="phone_number" id="phone_number" rows="1" cols="50"><?php echo $_POST['phone_number']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="ip_address"><?php _e('IP address', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="ip_address" id="ip_address" rows="1" cols="50"><?php echo $_POST['ip_address']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="user_agent"><?php _e('User agent', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="user_agent" id="user_agent" rows="1" cols="75"><?php echo $_POST['user_agent']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="referring_url"><?php _e('Referring URL', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="referring_url" id="referring_url" rows="1" cols="75"><?php echo $_POST['referring_url']; ?></textarea> 
<?php $url = htmlspecialchars(prospect_data(array(0 => 'referring_url', 'part' => 1, $attribute => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'optin-manager'); ?></a><?php } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="status"><?php _e('Status', 'optin-manager'); ?></label></strong></th>
<td><select name="status" id="status">
<option value="active"<?php if ($_POST['status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'optin-manager'); ?></option>
<option value="inactive"<?php if ($_POST['status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'optin-manager'); ?></option>
<option value="active"<?php if ($_POST['status'] == 'unsubscribed') { echo ' selected="selected"'; } ?>><?php _e('Unsubscribed', 'optin-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/optin-manager/documentation/#prospect-status"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="form_id"><?php _e('Form ID', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="form_id" id="form_id" rows="1" cols="25"><?php echo $_POST['form_id']; ?></textarea>
<?php if ($_POST['form_id'] > 0) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=optin-manager-form&amp;id='.$_POST['form_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=optin-manager-form&amp;id='.$_POST['form_id'].'&amp;action=delete">'.__('Delete').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=optin-manager-statistics&amp;form_id='.$_POST['form_id'].'">'.__('Statistics', 'optin-manager').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="date"><?php ($is_category ? _e('Creation date', 'optin-manager') : _e('Registration date', 'optin-manager')); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="date" id="date" size="20" value="<?php echo (isset($_POST['date']) ? $_POST['date'] : date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET)); ?>" /></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('autoresponders', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="autoresponders"><strong><?php echo $modules[$admin_page]['autoresponders']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=optin-manager#autoresponders"><?php _e('Click here to configure the default options.', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="autoresponder"><?php _e('Autoresponder', 'optin-manager'); ?></label></strong></th>
<td><select name="autoresponder" id="autoresponder">
<?php include 'autoresponders.php';
$autoresponder = do_shortcode($_POST['autoresponder']);
foreach ($autoresponders as $value) {
echo '<option value="'.$value.'"'.($autoresponder == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="autoresponder_list"><?php _e('List', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="autoresponder_list" id="autoresponder_list" rows="1" cols="50"><?php echo $_POST['autoresponder_list']; ?></textarea><br />
<span class="description"><?php _e('For some autoresponders, you must enter the list ID.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#autoresponders"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('affiliation', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="affiliation"><strong><?php echo $modules['prospect']['affiliation']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php if (function_exists('affiliation_manager_admin_menu')) { ?>
<a href="admin.php?page=optin-manager#affiliation"><?php _e('Click here to configure the default options.', 'optin-manager'); ?></a>
<?php } else { _e('To use affiliation, you must have installed and activated <a href="http://www.kleor-editions.com/affiliation-manager">Affiliation Manager</a>.', 'optin-manager'); } ?></span></td></tr>
</tbody></table>
<div<?php if (in_array('level-1-commission', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="level-1-commission"><strong><?php echo $modules['prospect']['affiliation']['modules']['level-1-commission']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('The level 1 commission is awarded to the affiliate who referred the prospect.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="referrer"><?php _e('Referrer', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="referrer" id="referrer" rows="1" cols="25"><?php echo $_POST['referrer']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Affiliate who referred this prospect (ID, login name or email address)', 'optin-manager'); ?></span> 
<?php if ((function_exists('affiliation_manager_admin_menu')) && ($_POST['referrer'] != '')) {
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['referrer']."'", OBJECT);
if ($result) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate&amp;id='.$result->id.'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate&amp;id='.$result->id.'&amp;action=delete">'.__('Delete').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-statistics&amp;referrer='.$_POST['referrer'].'">'.__('Statistics', 'optin-manager').'</a>'; } } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_amount"><?php _e('Commission amount', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_amount" id="commission_amount" rows="1" cols="25"><?php echo $_POST['commission_amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 0.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_status"><?php _e('Commission status', 'optin-manager'); ?></label></strong></th>
<td><select name="commission_status" id="commission_status">
<option value=""<?php if ($_POST['commission_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('None', 'optin-manager'); ?></option>
<option value="unpaid"<?php if ($_POST['commission_status'] == 'unpaid') { echo ' selected="selected"'; } ?>><?php _e('Unpaid', 'optin-manager'); ?></option>
<option value="paid"<?php if ($_POST['commission_status'] == 'paid') { echo ' selected="selected"'; } ?>><?php _e('Paid', 'optin-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_payment_date"><?php _e('Commission payment date', 'optin-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="commission_payment_date" id="commission_payment_date" size="20" value="<?php echo $_POST['commission_payment_date']; ?>" /><br />
<span class="description"><?php _e('Leave this field blank if the commission is not paid, or for the current date if the commission is paid.', 'optin-manager'); ?></span></td></tr>
</tbody></table>
</div>
<div<?php if (in_array('level-2-commission', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="level-2-commission"><strong><?php echo $modules['prospect']['affiliation']['modules']['level-2-commission']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('The level 2 commission is awarded to the referrer of the affiliate who referred the prospect.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="referrer2"><?php _e('Referrer', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="referrer2" id="referrer2" rows="1" cols="25"><?php echo $_POST['referrer2']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for the referrer of the affiliate who referred this prospect.', 'optin-manager'); ?></span> 
<?php if ((function_exists('affiliation_manager_admin_menu')) && ($_POST['referrer2'] != '')) {
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['referrer2']."'", OBJECT);
if ($result) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate&amp;id='.$result->id.'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate&amp;id='.$result->id.'&amp;action=delete">'.__('Delete').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-statistics&amp;referrer='.$_POST['referrer2'].'">'.__('Statistics', 'optin-manager').'</a>'; } } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_amount"><?php _e('Commission amount', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission2_amount" id="commission2_amount" rows="1" cols="25"><?php echo $_POST['commission2_amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 0.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_status"><?php _e('Commission status', 'optin-manager'); ?></label></strong></th>
<td><select name="commission2_status" id="commission2_status">
<option value=""<?php if ($_POST['commission2_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('None', 'optin-manager'); ?></option>
<option value="unpaid"<?php if ($_POST['commission2_status'] == 'unpaid') { echo ' selected="selected"'; } ?>><?php _e('Unpaid', 'optin-manager'); ?></option>
<option value="paid"<?php if ($_POST['commission2_status'] == 'paid') { echo ' selected="selected"'; } ?>><?php _e('Paid', 'optin-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_payment_date"><?php _e('Commission payment date', 'optin-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="commission2_payment_date" id="commission2_payment_date" size="20" value="<?php echo $_POST['commission2_payment_date']; ?>" /><br />
<span class="description"><?php _e('Leave this field blank if the commission is not paid, or for the current date if the commission is paid.', 'optin-manager'); ?></span></td></tr>
</tbody></table>
</div>
<?php if (isset($_GET['id'])) { echo '<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr></tbody></table>'; } ?>
</div></div>

<?php if ((!$is_category) && (!isset($_GET['id']))) {
if (!isset($_POST['submit'])) {
$optin_manager_options = (array) get_option('optin_manager');
$optin_manager_options = array_map('htmlspecialchars', $optin_manager_options);
foreach (add_prospect_fields() as $field) { $_POST[$field] = $optin_manager_options[$field]; } }
if ((!in_array('registration-confirmation-email', $undisplayed_modules)) || (!in_array('registration-notification-email', $undisplayed_modules)) || (!in_array('membership', $undisplayed_modules)) || (!in_array('custom-instructions', $undisplayed_modules))) { ?>
<p class="submit" style="margin: 0 20%;"><input type="hidden" name="submit" value="true" />
<input type="submit" class="button-secondary" name="update_fields" value="<?php _e('Complete the fields below with the informations about the prospect and the form', 'optin-manager'); ?>" /></p><?php } ?>

<div id="add-prospect-modules">
<?php if (!in_array('registration-confirmation-email', $undisplayed_modules)) {
if (!isset($_POST['submit'])) { $_POST['registration_confirmation_email_body'] = htmlspecialchars(get_option('optin_manager_registration_confirmation_email_body')); } ?>
<div class="postbox">
<h3 id="registration-confirmation-email"><strong><?php echo $modules[$admin_page]['registration-confirmation-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=optin-manager#registration-confirmation-email"><?php _e('Click here to configure the default options.', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="registration_confirmation_email_sent" id="registration_confirmation_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['registration_confirmation_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration confirmation email', 'optin-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_sender"><?php _e('Sender', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_sender" id="registration_confirmation_email_sender" rows="1" cols="75"><?php echo $_POST['registration_confirmation_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_receiver"><?php _e('Receiver', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_receiver" id="registration_confirmation_email_receiver" rows="1" cols="75"><?php echo $_POST['registration_confirmation_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_subject"><?php _e('Subject', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_subject" id="registration_confirmation_email_subject" rows="1" cols="75"><?php echo $_POST['registration_confirmation_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_body"><?php _e('Body', 'optin-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_confirmation_email_body" id="registration_confirmation_email_body" rows="15" cols="75"><?php echo $_POST['registration_confirmation_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the prospect and the form.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#email-shortcodes"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('registration-notification-email', $undisplayed_modules)) {
if (!isset($_POST['submit'])) { $_POST['registration_notification_email_body'] = htmlspecialchars(get_option('optin_manager_registration_notification_email_body')); } ?>
<div class="postbox">
<h3 id="registration-notification-email"><strong><?php echo $modules[$admin_page]['registration-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=optin-manager#registration-notification-email"><?php _e('Click here to configure the default options.', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="registration_notification_email_sent" id="registration_notification_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['registration_notification_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration notification email', 'optin-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_sender"><?php _e('Sender', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_sender" id="registration_notification_email_sender" rows="1" cols="75"><?php echo $_POST['registration_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_receiver"><?php _e('Receiver', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_receiver" id="registration_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['registration_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_subject"><?php _e('Subject', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_subject" id="registration_notification_email_subject" rows="1" cols="75"><?php echo $_POST['registration_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_body"><?php _e('Body', 'optin-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_notification_email_body" id="registration_notification_email_body" rows="15" cols="75"><?php echo $_POST['registration_notification_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the prospect and the form.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#email-shortcodes"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
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
<a href="admin.php?page=optin-manager#membership"><?php _e('Click here to configure the default options.', 'optin-manager'); ?></a>
<?php } else { _e('To use membership, you must have installed and activated <a href="http://www.kleor-editions.com/membership-manager">Membership Manager</a>.', 'optin-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="prospect_subscribed_to_members_areas" id="prospect_subscribed_to_members_areas" value="yes"<?php if ($_POST['prospect_subscribed_to_members_areas'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Subscribe the prospect to a member area', 'optin-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_members_areas"><?php _e('Members areas', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="prospect_members_areas" id="prospect_members_areas" rows="1" cols="50"><?php echo $_POST['prospect_members_areas']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/optin-manager/documentation/#membership"><?php _e('More informations', 'optin-manager'); ?></a></span>
<?php if ((function_exists('membership_manager_admin_menu')) && (is_numeric($_POST['prospect_members_areas'])) && ($_POST['prospect_members_areas'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-area&amp;id='.$_POST['prospect_members_areas'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-area&amp;id='.$_POST['prospect_members_areas'].'&amp;action=delete">'.__('Delete').'</a>'; } ?><br />
<span class="description"><?php _e('Separate the IDs of the members areas with commas.', 'optin-manager'); ?></span></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('custom-instructions', $undisplayed_modules)) {
if (!isset($_POST['submit'])) { $_POST['registration_custom_instructions'] = htmlspecialchars(get_option('optin_manager_registration_custom_instructions')); } ?>
<div class="postbox">
<h3 id="custom-instructions"><strong><?php echo $modules[$admin_page]['custom-instructions']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=optin-manager#custom-instructions"><?php _e('Click here to configure the default options.', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="registration_custom_instructions_executed" id="registration_custom_instructions_executed" value="yes"<?php if ($_POST['registration_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'optin-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_custom_instructions"><?php _e('PHP code', 'optin-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_custom_instructions" id="registration_custom_instructions" rows="10" cols="75"><?php echo $_POST['registration_custom_instructions']; ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the registration of the prospect.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#custom-instructions"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
</tbody></table>
</div></div>
<?php } ?>
</div>

<?php if (($updated) && ($_GET['autoresponder_subscription'] != '')) { echo '<div><img alt="" src="'.$_GET['autoresponder_subscription'].'" /></div>'; } } ?>
<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php (isset($_GET['id']) ? _e('Save Changes', 'optin-manager') : ($is_category ? _e('Save Category', 'optin-manager') : _e('Save Prospect', 'optin-manager'))); ?>" /></p>
<?php if ($is_category) { $module = 'prospect-category-page'; } else { $module = 'prospect-page'; }
optin_manager_pages_module($back_office_options, $module, $undisplayed_modules); ?>
</form>
</div>
</div>
<?php if (isset($_POST['update_fields'])) { ?>
<script type="text/javascript">window.location = '#add-prospect-modules';</script>
<?php } }