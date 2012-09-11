<?php global $wpdb;
$back_office_options = get_option('optin_manager_back_office');
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }

if ((isset($_GET['id'])) && ($_GET['action'] == 'delete')) {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!optin_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'optin-manager'); }
else {
$prospect_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_prospects WHERE id = ".$_GET['id'], OBJECT);
$results = $wpdb->query("DELETE FROM ".$wpdb->prefix."optin_manager_prospects WHERE id = ".$_GET['id']);
if ($prospect_data->form_id > 0) {
$optin_form_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_forms WHERE id = ".$prospect_data->form_id, OBJECT);
$prospects_count = $optin_form_data->prospects_count - 1;
if ($prospects_count < 0) { $prospects_count = 0; }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."optin_manager_forms SET prospects_count = ".$prospects_count." WHERE id = ".$optin_form_data->id); }
if ((!defined('OPTIN_MANAGER_DEMO')) || (OPTIN_MANAGER_DEMO == false)) {
if (optin_data('removal_custom_instructions_executed') == 'yes') {
eval(format_instructions(optin_data('removal_custom_instructions'))); } } } } ?>
<div class="wrap">
<div id="poststuff">
<?php optin_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) {
echo '<div class="updated"><p><strong>'.__('Prospect deleted.', 'optin-manager').'</strong></p></div>
<script type="text/javascript">setTimeout(\'window.location = "admin.php?page=optin-manager-prospects"\', 2000);</script>'; } ?>
<?php optin_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php _e('Do you really want to permanently delete this prospect?', 'optin-manager'); ?> 
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
if (is_string($value)) { $_POST[$key] = stripslashes(html_entity_decode(str_replace('&nbsp;', ' ', $value))); } }
$back_office_options = update_optin_manager_back_office($back_office_options, 'prospect');

if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$_POST['email_address'] = format_email_address($_POST['email_address']);
$_POST['form_id'] = (int) $_POST['form_id'];
$_GET['optin_form_id'] = $_POST['form_id'];
if ($_POST['form_id'] > 0) { $_GET['optin_form_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_forms WHERE id = ".$_POST['form_id'], OBJECT); }
if ($_POST['autoresponder_list'] == '') { $_POST['autoresponder_list'] = optin_form_data('autoresponder_list'); }
if (isset($_POST['referrer'])) {
if (is_numeric($_POST['referrer'])) {
$_POST['referrer'] = preg_replace('/[^0-9]/', '', $_POST['referrer']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE id = ".$_POST['referrer'], OBJECT);
if ($result) { $_POST['referrer'] = $result->login; } }
elseif (strstr($_POST['referrer'], '@')) {
$_POST['referrer'] = format_email_address($_POST['referrer']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE paypal_email_address = '".$_POST['referrer']."'", OBJECT);
if ($result) { $_POST['referrer'] = $result->login; }
else { $result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".$_POST['referrer']."'", OBJECT);
if ($result) { $_POST['referrer'] = $result->login; } } }
else {
$_POST['referrer'] = format_nice_name($_POST['referrer']);
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
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE id = ".$_POST['referrer2'], OBJECT);
if ($result) { $_POST['referrer2'] = $result->login; } }
elseif (strstr($_POST['referrer2'], '@')) {
$_POST['referrer2'] = format_email_address($_POST['referrer2']);
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE paypal_email_address = '".$_POST['referrer2']."'", OBJECT);
if ($result) { $_POST['referrer2'] = $result->login; }
else { $result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".$_POST['referrer2']."'", OBJECT);
if ($result) { $_POST['referrer2'] = $result->login; } } }
else {
$_POST['referrer2'] = format_nice_name($_POST['referrer2']);
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
else { $_POST['commission2_payment_date'] = ''; } }
if ($_POST['date'] == '') {
$_POST['date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['date_utc'] = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['date'], 0, PREG_SPLIT_NO_EMPTY);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['date'] = date('Y-m-d H:i:s', $time);
$_POST['date_utc'] = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); }

if (!isset($_GET['id'])) {
if ($_POST['referring_url'] == '') { $_POST['referring_url'] = $_SERVER['HTTP_REFERER']; }
if (isset($_POST['update_fields'])) {
foreach ($_POST as $key => $value) { $_GET['prospect_data'][$key] = $value; }
$_GET['prospect_data']['id'] = '{prospect id}';
if ($_POST['referrer'] != '') {
$_GET['affiliate_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE login = '".$_POST['referrer']."'", OBJECT);
$_GET['referrer_data'] = $_GET['affiliate_data']; }
foreach ($add_prospect_fields as $field) { $_POST[$field] = str_replace('{prospect id}', '[prospect id]', optin_form_data($field)); } }
else {
$members_areas = array_unique(preg_split('#[^0-9]#', $_POST['prospect_members_areas'], 0, PREG_SPLIT_NO_EMPTY));
sort($members_areas, SORT_NUMERIC);
foreach ($members_areas as $member_area) { $members_areas_list .= $member_area.', '; }
$_POST['prospect_members_areas'] = substr($members_areas_list, 0, -2);
if (($_POST['email_address'] == '') || ($_POST['autoresponder_list'] == '')) { $error .= ' '.__('Please fill out the required fields.', 'optin-manager'); }
if ($error == '') {
$result = $wpdb->get_results("SELECT id FROM ".$wpdb->prefix."optin_manager_prospects WHERE email_address = '".$_POST['email_address']."' AND autoresponder = '".$_POST['autoresponder']."' AND autoresponder_list = '".$_POST['autoresponder_list']."'", OBJECT);
if (!$result) { $updated = true; add_prospect($_POST); } } } }

if (isset($_GET['id'])) {
$updated = true;
include 'tables.php';
$prospect_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_prospects WHERE id = ".$_GET['id'], OBJECT);
if ($_POST['email_address'] != '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."optin_manager_prospects SET email_address = '".$_POST['email_address']."' WHERE id = ".$_GET['id']); }
if ($_POST['autoresponder_list'] != '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."optin_manager_prospects SET autoresponder_list = '".$_POST['autoresponder_list']."' WHERE id = ".$_GET['id']); }
$sql = optin_sql_array($tables['prospects'], $_POST);
foreach ($tables['prospects'] as $key => $value) { switch ($key) {
case 'id': case 'email_address': case 'autoresponder_list': break;
default: $list .= $key." = ".$sql[$key].","; } }
if ((!defined('OPTIN_MANAGER_DEMO')) || (OPTIN_MANAGER_DEMO == false)) {
if ($_POST['status'] == 'active') {
if ($_POST['old_status'] != 'active') {
if (optin_data('activation_custom_instructions_executed') == 'yes') {
eval(format_instructions(optin_data('activation_custom_instructions'))); } } }
elseif ($_POST['old_status'] == 'active') {
if (optin_data('deactivation_custom_instructions_executed') == 'yes') {
eval(format_instructions(optin_data('deactivation_custom_instructions'))); } } }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."optin_manager_prospects SET ".substr($list, 0, -1)." WHERE id = ".$_GET['id']);

if ($_POST['form_id'] != $prospect_data->form_id) {
if ($prospect_data->form_id > 0) {
$optin_form_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_forms WHERE id = ".$prospect_data->form_id, OBJECT);
$prospects_count = $optin_form_data->prospects_count - 1;
if ($prospects_count < 0) { $prospects_count = 0; }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."optin_manager_forms SET prospects_count = ".$prospects_count." WHERE id = ".$prospect_data->form_id); }

if ($_POST['form_id'] > 0) {
$displays_count = $_GET['optin_form_data']['displays_count'];
$prospects_count = $_GET['optin_form_data']['prospects_count'] + 1;
if ($displays_count < $prospects_count) { $displays_count = $prospects_count; }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."optin_manager_forms SET
	displays_count = ".$displays_count.",
	prospects_count = ".$prospects_count." WHERE id = ".$_POST['form_id']); } } } } }

if (isset($_GET['id'])) {
$prospect_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."optin_manager_prospects WHERE id = ".$_GET['id'], OBJECT);
if ($prospect_data) {
$_GET['prospect_data'] = (array) $prospect_data;
foreach ($prospect_data as $key => $value) { $_POST[$key] = $value; } }
elseif (!headers_sent()) { header('Location: admin.php?page=optin-manager-prospects'); exit(); }
else { echo '<script type="text/javascript">window.location = "admin.php?page=optin-manager-prospects";</script>'; } }

foreach ($_POST as $key => $value) {
if (is_string($value)) {
$_POST[$key] = str_replace(array('&amp;amp;', '&amp;apos;', '&amp;quot;'), array('&amp;', '&apos;', '&quot;'), htmlspecialchars(stripslashes($value)));
if ($value == '0000-00-00 00:00:00') { $_POST[$key] = ''; } } }
$undisplayed_modules = (array) $back_office_options['prospect_page_undisplayed_modules'];
if (function_exists('commerce_data')) { $currency_code = commerce_data('currency_code'); }
else { $commerce_manager_options = (array) get_option('commerce_manager');
$currency_code = do_shortcode($commerce_manager_options['currency_code']); } ?>

<div class="wrap">
<div id="poststuff">
<?php optin_manager_pages_top($back_office_options); ?>
<?php if ($updated) {
echo '<div class="updated"><p><strong>'.(isset($_GET['id']) ? __('Prospect updated.', 'optin-manager') : __('Prospect saved.', 'optin-manager')).'</strong></p></div>
'.(isset($_GET['id']) ? '' : '<script type="text/javascript">setTimeout(\'window.location = "admin.php?page=optin-manager-prospects"\', 2000);</script>'); } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php optin_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<p class="description"><?php _e('Fields marked with * are required.', 'optin-manager'); ?></p>
<?php optin_manager_pages_summary($back_office_options); ?>

<div class="postbox" id="personal-informations-module"<?php if (in_array('personal-informations', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="personal-informations"><strong><?php echo $modules['prospect']['personal-informations']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="id">'.__('ID', 'optin-manager').'</label></strong></th>
<td><input type="text" name="id" id="id" size="10" value="'.$_GET['id'].'" disabled="disabled" /> <span class="description">'.__('The ID can not be changed.', 'optin-manager').'</span></td></tr>'; } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="first_name"><?php _e('First name', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="first_name" id="first_name" rows="1" cols="50"><?php echo $_POST['first_name']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="last_name"><?php _e('Last name', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="last_name" id="last_name" rows="1" cols="50"><?php echo $_POST['last_name']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['email_address'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="email_address"><?php _e('Email address', 'optin-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="email_address" id="email_address" rows="1" cols="50"><?php echo $_POST['email_address']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="website_name"><?php _e('Website name', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="website_name" id="website_name" rows="1" cols="50"><?php echo $_POST['website_name']; ?></textarea> 
<?php $url = htmlspecialchars(prospect_data(array(0 => 'website_url', 'part' => 1, 'id' => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'optin-manager'); ?></a><?php } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="website_url"><?php _e('Website URL', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="website_url" id="website_url" rows="1" cols="75"><?php echo $_POST['website_url']; ?></textarea> 
<?php $url = htmlspecialchars(prospect_data(array(0 => 'website_url', 'part' => 1, 'id' => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'optin-manager'); ?></a><?php } ?></td></tr>
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
<?php $url = htmlspecialchars(prospect_data(array(0 => 'referring_url', 'part' => 1, 'id' => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'optin-manager'); ?></a><?php } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="status"><?php _e('Status', 'optin-manager'); ?></label></strong></th>
<td><select name="status" id="status">
<option value="active"<?php if ($_POST['status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'optin-manager'); ?></option>
<option value="inactive"<?php if ($_POST['status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'optin-manager'); ?></option>
<option value="deactivated"<?php if ($_POST['status'] == 'deactivated') { echo ' selected="selected"'; } ?>><?php _e('Deactivated', 'optin-manager'); ?></option>
</select><?php if (isset($_GET['id'])) { echo '<input type="hidden" name="old_status" value="'.$_POST['status'].'" />'; } ?> 
<span class="description"><a href="http://www.kleor-editions.com/optin-manager/documentation/#prospect-status"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="form_id"><?php _e('Form ID', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="form_id" id="form_id" rows="1" cols="25"><?php echo $_POST['form_id']; ?></textarea>
<?php if ($_POST['form_id'] > 0) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=optin-manager-form&amp;id='.$_POST['form_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=optin-manager-form&amp;id='.$_POST['form_id'].'&amp;action=delete">'.__('Delete').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=optin-manager-statistics&amp;form_id='.$_POST['form_id'].'">'.__('Statistics', 'optin-manager').'</a>'; } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="date"><?php _e('Registration date', 'optin-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="date" id="date" size="20" value="<?php echo (isset($_POST['date']) ? $_POST['date'] : date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET)); ?>" /></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="autoresponders-module"<?php if (in_array('autoresponders', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="autoresponders"><strong><?php echo $modules['prospect']['autoresponders']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=optin-manager#autoresponders"><?php _e('Click here to configure the default options.', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="autoresponder"><?php _e('Autoresponder', 'optin-manager'); ?></label></strong></th>
<td><select name="autoresponder" id="autoresponder">
<?php include 'libraries/autoresponders.php';
$autoresponder = do_shortcode($_POST['autoresponder']);
foreach ($autoresponders as $value) {
echo '<option value="'.$value.'"'.($autoresponder == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['autoresponder_list'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="autoresponder_list"><?php _e('List', 'optin-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="autoresponder_list" id="autoresponder_list" rows="1" cols="50"><?php echo $_POST['autoresponder_list']; ?></textarea><br />
<span class="description"><?php _e('For some autoresponders, you must enter the list ID.', 'optin-manager'); ?> <a href="http://www.kleor-editions.com/optin-manager/documentation/#autoresponders"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="affiliation-module"<?php if (in_array('affiliation', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="affiliation"><strong><?php echo $modules['prospect']['affiliation']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php if (function_exists('affiliation_manager_admin_menu')) { ?>
<a href="admin.php?page=optin-manager#affiliation"><?php _e('Click here to configure the default options.', 'optin-manager'); ?></a>
<?php } else { _e('To use affiliation, you must have installed and activated <a href="http://www.kleor-editions.com/affiliation-manager">Affiliation Manager</a>.', 'optin-manager'); } ?></span></td></tr>
</tbody></table>
<div id="level-1-commission-module"<?php if (in_array('level-1-commission', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
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
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_amount"><?php _e('Amount', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_amount" id="commission_amount" rows="1" cols="25"><?php echo $_POST['commission_amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 0.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_status"><?php _e('Status', 'optin-manager'); ?></label></strong></th>
<td><select name="commission_status" id="commission_status" onchange="if (this.value == 'paid') { document.getElementById('commission-payment-date').style.display = ''; } else { document.getElementById('commission-payment-date').style.display = 'none'; }">
<option value=""<?php if ($_POST['commission_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('None', 'optin-manager'); ?></option>
<option value="unpaid"<?php if ($_POST['commission_status'] == 'unpaid') { echo ' selected="selected"'; } ?>><?php _e('Unpaid', 'optin-manager'); ?></option>
<option value="paid"<?php if ($_POST['commission_status'] == 'paid') { echo ' selected="selected"'; } ?>><?php _e('Paid', 'optin-manager'); ?></option>
</select><?php if (isset($_GET['id'])) { echo '<input type="hidden" name="old_commission_status" value="'.$_POST['commission_status'].'" />'; } ?></td></tr>
<tr id="commission-payment-date" style="<?php if ($_POST['commission_status'] != 'paid') { echo 'display: none; '; } ?>vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_payment_date"><?php _e('Payment date', 'optin-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="commission_payment_date" id="commission_payment_date" size="20" value="<?php echo $_POST['commission_payment_date']; ?>" /><br />
<span class="description"><?php _e('Leave this field blank if the commission is not paid, or for the current date if the commission is paid.', 'optin-manager'); ?></span></td></tr>
</tbody></table>
</div>
<div id="level-2-commission-module"<?php if (in_array('level-2-commission', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
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
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_amount"><?php _e('Amount', 'optin-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission2_amount" id="commission2_amount" rows="1" cols="25"><?php echo $_POST['commission2_amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 0.', 'optin-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_status"><?php _e('Status', 'optin-manager'); ?></label></strong></th>
<td><select name="commission2_status" id="commission2_status" onchange="if (this.value == 'paid') { document.getElementById('commission2-payment-date').style.display = ''; } else { document.getElementById('commission2-payment-date').style.display = 'none'; }">
<option value=""<?php if ($_POST['commission2_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('None', 'optin-manager'); ?></option>
<option value="unpaid"<?php if ($_POST['commission2_status'] == 'unpaid') { echo ' selected="selected"'; } ?>><?php _e('Unpaid', 'optin-manager'); ?></option>
<option value="paid"<?php if ($_POST['commission2_status'] == 'paid') { echo ' selected="selected"'; } ?>><?php _e('Paid', 'optin-manager'); ?></option>
</select><?php if (isset($_GET['id'])) { echo '<input type="hidden" name="old_commission2_status" value="'.$_POST['commission2_status'].'" />'; } ?></td></tr>
<tr id="commission2-payment-date" style="<?php if ($_POST['commission2_status'] != 'paid') { echo 'display: none; '; } ?>vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_payment_date"><?php _e('Payment date', 'optin-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="commission2_payment_date" id="commission2_payment_date" size="20" value="<?php echo $_POST['commission2_payment_date']; ?>" /><br />
<span class="description"><?php _e('Leave this field blank if the commission is not paid, or for the current date if the commission is paid.', 'optin-manager'); ?></span></td></tr>
</tbody></table>
</div>
<?php if (isset($_GET['id'])) { echo '<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr></tbody></table>'; } ?>
</div></div>

<?php if (!isset($_GET['id'])) {
if (!isset($_POST['submit'])) {
$optin_manager_options = (array) get_option('optin_manager');
foreach ($optin_manager_options as $key => $value) {
if (is_string($value)) { $optin_manager_options[$key] = htmlspecialchars($value); } }
foreach ($add_prospect_fields as $field) { $_POST[$field] = $optin_manager_options[$field]; } }
$value = false; foreach ($add_prospect_modules as $module) { if (!$value) { $value = (!in_array($module, $undisplayed_modules)); } }
if ($value) { ?><p class="submit" style="margin: 0 20%;"><input type="hidden" name="submit" value="true" />
<input type="submit" class="button-secondary" name="update_fields" value="<?php _e('Complete the fields below with the informations about the prospect and the form', 'optin-manager'); ?>" /></p><?php } ?>

<div id="add-prospect-modules">
<?php if (!in_array('registration-confirmation-email', $undisplayed_modules)) {
if (!isset($_POST['submit'])) { $_POST['registration_confirmation_email_body'] = htmlspecialchars(get_option('optin_manager_registration_confirmation_email_body')); } ?>
<div class="postbox" id="registration-confirmation-email-module">
<h3 id="registration-confirmation-email"><strong><?php echo $modules['prospect']['registration-confirmation-email']['name']; ?></strong></h3>
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
<div class="postbox" id="registration-notification-email-module">
<h3 id="registration-notification-email"><strong><?php echo $modules['prospect']['registration-notification-email']['name']; ?></strong></h3>
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

<?php if (!in_array('registration-as-a-client', $undisplayed_modules)) { ?>
<div class="postbox" id="registration-as-a-client-module">
<h3 id="registration-as-a-client"><strong><?php echo $modules['prospect']['registration-as-a-client']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php if (function_exists('commerce_manager_admin_menu')) { ?>
<a href="admin.php?page=optin-manager#registration-as-a-client"><?php _e('Click here to configure the default options.', 'optin-manager'); ?></a>
<?php } else { _e('To subscribe your prospects as clients, you must have installed and activated <a href="http://www.kleor-editions.com/commerce-manager">Commerce Manager</a>.', 'optin-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="prospect_subscribed_as_a_client" id="prospect_subscribed_as_a_client" value="yes"<?php if ($_POST['prospect_subscribed_as_a_client'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the prospect as a client', 'optin-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/optin-manager/documentation/#registration-as-a-client"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."commerce_manager_clients_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_client_category_id"><?php _e('Category', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_client_category_id" id="prospect_client_category_id">
<option value="0"<?php if ($_POST['prospect_client_category_id'] == 0) { echo ' selected="selected"'; } ?>><?php _e('None ', 'optin-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($_POST['prospect_client_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ((function_exists('commerce_manager_admin_menu')) && ($_POST['prospect_client_category_id'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-client-category&amp;id='.$_POST['prospect_client_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-client-category&amp;id='.$_POST['prospect_client_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_client_status"><?php _e('Status', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_client_status" id="prospect_client_status">
<option value="active"<?php if ($_POST['prospect_client_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'optin-manager'); ?></option>
<option value="inactive"<?php if ($_POST['prospect_client_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'optin-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#client-status"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="commerce_registration_confirmation_email_sent" id="commerce_registration_confirmation_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['commerce_registration_confirmation_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration confirmation email', 'optin-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="commerce_registration_notification_email_sent" id="commerce_registration_notification_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['commerce_registration_notification_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration notification email', 'optin-manager'); ?></label></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('registration-to-affiliate-program', $undisplayed_modules)) { ?>
<div class="postbox" id="registration-to-affiliate-program-module">
<h3 id="registration-to-affiliate-program"><strong><?php echo $modules['prospect']['registration-to-affiliate-program']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php if (function_exists('affiliation_manager_admin_menu')) { ?>
<a href="admin.php?page=optin-manager#registration-to-affiliate-program"><?php _e('Click here to configure the default options.', 'optin-manager'); ?></a>
<?php } else { _e('To use affiliation, you must have installed and activated <a href="http://www.kleor-editions.com/affiliation-manager">Affiliation Manager</a>.', 'optin-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="prospect_subscribed_to_affiliate_program" id="prospect_subscribed_to_affiliate_program" value="yes"<?php if ($_POST['prospect_subscribed_to_affiliate_program'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the prospect to affiliate program', 'optin-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/optin-manager/documentation/#registration-to-affiliate-program"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."affiliation_manager_affiliates_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_affiliate_category_id"><?php _e('Category', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_affiliate_category_id" id="prospect_affiliate_category_id">
<option value="0"<?php if ($_POST['prospect_affiliate_category_id'] == 0) { echo ' selected="selected"'; } ?>><?php _e('None ', 'optin-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($_POST['prospect_affiliate_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ((function_exists('affiliation_manager_admin_menu')) && ($_POST['prospect_affiliate_category_id'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$_POST['prospect_affiliate_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$_POST['prospect_affiliate_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_affiliate_status"><?php _e('Status', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_affiliate_status" id="prospect_affiliate_status">
<option value="active"<?php if ($_POST['prospect_affiliate_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'optin-manager'); ?></option>
<option value="inactive"<?php if ($_POST['prospect_affiliate_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'optin-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#affiliate-status"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="affiliation_registration_confirmation_email_sent" id="affiliation_registration_confirmation_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['affiliation_registration_confirmation_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration confirmation email', 'optin-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="affiliation_registration_notification_email_sent" id="affiliation_registration_notification_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['affiliation_registration_notification_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration notification email', 'optin-manager'); ?></label></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('membership', $undisplayed_modules)) { ?>
<div class="postbox" id="membership-module">
<h3 id="membership"><strong><?php echo $modules['prospect']['membership']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php if (function_exists('membership_manager_admin_menu')) { ?>
<a href="admin.php?page=optin-manager#membership"><?php _e('Click here to configure the default options.', 'optin-manager'); ?></a>
<?php } else { _e('To use membership, you must have installed and activated <a href="http://www.kleor-editions.com/membership-manager">Membership Manager</a>.', 'optin-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="prospect_subscribed_to_members_areas" id="prospect_subscribed_to_members_areas" value="yes"<?php if ($_POST['prospect_subscribed_to_members_areas'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the prospect to a member area', 'optin-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/optin-manager/documentation/#membership"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
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
<option value="0"<?php if ($_POST['prospect_member_category_id'] == 0) { echo ' selected="selected"'; } ?>><?php _e('None ', 'optin-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($_POST['prospect_member_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ((function_exists('membership_manager_admin_menu')) && ($_POST['prospect_member_category_id'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-category&amp;id='.$_POST['prospect_member_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-category&amp;id='.$_POST['prospect_member_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_member_status"><?php _e('Status', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_member_status" id="prospect_member_status">
<option value="active"<?php if ($_POST['prospect_member_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'optin-manager'); ?></option>
<option value="inactive"<?php if ($_POST['prospect_member_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'optin-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#member-status"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="membership_registration_confirmation_email_sent" id="membership_registration_confirmation_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['membership_registration_confirmation_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration confirmation email', 'optin-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="membership_registration_notification_email_sent" id="membership_registration_notification_email_sent" value="yes"<?php if ((isset($_POST['update_fields'])) && ($_POST['membership_registration_notification_email_sent'] == 'yes')) { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration notification email', 'optin-manager'); ?></label></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('wordpress', $undisplayed_modules)) { ?>
<div class="postbox" id="wordpress-module">
<h3 id="wordpress"><strong><?php echo $modules['prospect']['wordpress']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=optin-manager#wordpress"><?php _e('Click here to configure the default options.', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="prospect_subscribed_as_a_user" id="prospect_subscribed_as_a_user" value="yes"<?php if ($_POST['prospect_subscribed_as_a_user'] == 'yes') { echo ' checked="checked"'; } ?> /> 
<?php _e('Subscribe the prospect as a user', 'optin-manager'); ?></label> <span class="description"><a href="http://www.kleor-editions.com/optin-manager/documentation/#wordpress"><?php _e('More informations', 'optin-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="prospect_user_role"><?php _e('Role', 'optin-manager'); ?></label></strong></th>
<td><select name="prospect_user_role" id="prospect_user_role">
<?php foreach (optin_manager_users_roles() as $role => $name) {
echo '<option value="'.$role.'"'.($_POST['prospect_user_role'] == $role ? ' selected="selected"' : '').'>'.$name.'</option>'."\n"; } ?>
</select></td></tr>
</tbody></table>
</div></div>
<?php } ?>

<?php if (!in_array('custom-instructions', $undisplayed_modules)) {
if (!isset($_POST['submit'])) { $_POST['registration_custom_instructions'] = htmlspecialchars(get_option('optin_manager_registration_custom_instructions')); } ?>
<div class="postbox" id="custom-instructions-module">
<h3 id="custom-instructions"><strong><?php echo $modules['prospect']['custom-instructions']['name']; ?></strong></h3>
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

<?php } ?>
<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php (isset($_GET['id']) ? _e('Save Changes', 'optin-manager') : _e('Save Prospect', 'optin-manager')); ?>" /></p>
<?php optin_manager_pages_module($back_office_options, 'prospect-page', $undisplayed_modules); ?>
</form>
</div>
</div>
<?php if (isset($_POST['update_fields'])) { ?>
<script type="text/javascript">window.location = '#add-prospect-modules';</script>
<?php } }