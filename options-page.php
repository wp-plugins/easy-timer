<?php global $wpdb;
$back_office_options = get_option('affiliation_manager_back_office');

if ($_GET['action'] == 'uninstall') {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) { uninstall_affiliation_manager(); } ?>
<div class="wrap">
<div id="poststuff">
<?php affiliation_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Options and tables deleted.', 'affiliation-manager').'</strong></p></div>'; } ?>
<?php affiliation_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php _e('Do you really want to permanently delete the options and tables of Affiliation Manager?', 'affiliation-manager'); ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'affiliation-manager'); ?>" />
</div>
</form><?php } ?>
</div>
</div><?php }

else {
include 'admin-pages.php';
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
include 'initial-options.php';
$_POST = array_map('html_entity_decode', $_POST);
$_POST = array_map('stripslashes', $_POST);
if ($_POST['options_page_summary_displayed'] != 'yes') { $_POST['options_page_summary_displayed'] = 'no'; }
$back_office_options['options_page_summary_displayed'] = $_POST['options_page_summary_displayed'];
$back_office_options['options_page_undisplayed_modules'] = array();
foreach ($modules['options'] as $key => $value) {
if (($_POST['options_page_'.str_replace('-', '_', $key).'_module_displayed'] != 'yes') && ($value['required'] != 'yes')) { $back_office_options['options_page_undisplayed_modules'][] = $key; }
if (is_array($value['modules'])) { foreach ($value['modules'] as $module_key => $module_value) {
if (($_POST['options_page_'.str_replace('-', '_', $module_key).'_module_displayed'] != 'yes') && ($module_value['required'] != 'yes')) { $back_office_options['options_page_undisplayed_modules'][] = $module_key; } } } }
update_option('affiliation_manager_back_office', $back_office_options);

foreach (array(
'affiliate_subscribed_to_autoresponder',
'affiliate_subscribed_to_members_areas',
'affiliation_enabled',
'clicks_registration_enabled',
'commission2_enabled',
'registration_confirmation_email_sent',
'registration_custom_instructions_executed',
'registration_notification_email_sent',
'registration_required') as $field) { if ($_POST[$field] != 'yes') { $_POST[$field] = 'no'; } }
foreach (array(
'cookies_lifetime',
'maximum_login_length',
'maximum_password_length',
'minimum_login_length',
'minimum_password_length') as $field) { $_POST[$field] = (int) $_POST[$field]; }
foreach (array(
'commission_amount',
'commission_percentage',
'commission2_amount',
'commission2_percentage') as $field) { $_POST[$field] = str_replace(array('?', ',', ';'), '.', $_POST[$field]); }
foreach (array(
'url_variable_name',
'url_variable_name2') as $field) { $_POST[$field] = affiliation_format_medium_nice_name($_POST[$field]); }
$members_areas = array_unique(preg_split('#[^0-9]#', $_POST['affiliate_members_areas'], 0, PREG_SPLIT_NO_EMPTY));
sort($members_areas, SORT_NUMERIC);
foreach ($members_areas as $member_area) { $members_areas_list .= $member_area.', '; }
$_POST['affiliate_members_areas'] = substr($members_areas_list, 0, -2);
if ($_POST['cookies_lifetime'] < 1) { $_POST['cookies_lifetime'] = $initial_options['']['cookies_lifetime']; }
$_POST['cookies_name'] = affiliation_format_nice_name($_POST['cookies_name']);
if ($_POST['cookies_name'] == 'a_login') { $_POST['cookies_name'] == 'a-login'; }
switch ($_POST['maximum_clicks_quantity']) { case 0 : case '' : case 'i' : case 'infinite' : case 'u' : $_POST['maximum_clicks_quantity'] = 'unlimited'; }
if (is_numeric($_POST['maximum_clicks_quantity'])) {
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."affiliation_manager_clicks", OBJECT);
$clicks_quantity = (int) $row->total;
$n = $clicks_quantity - $_POST['maximum_clicks_quantity'];
if ($n > 0) { $results = $wpdb->query("DELETE FROM ".$wpdb->prefix."affiliation_manager_clicks ORDER BY date ASC LIMIT $n"); } }
if ($_POST['maximum_login_length'] < 2) { $_POST['maximum_login_length'] = $initial_options['']['maximum_login_length']; }
if ($_POST['maximum_password_length'] < 8) { $_POST['maximum_password_length'] = $initial_options['']['maximum_password_length']; }
if ($_POST['minimum_login_length'] < 1) { $_POST['minimum_login_length'] = $initial_options['']['minimum_login_length']; }
if ($_POST['minimum_login_length'] > $_POST['maximum_login_length']) { $_POST['minimum_login_length'] = $_POST['maximum_login_length']; }
if ($_POST['minimum_password_length'] < 1) { $_POST['minimum_password_length'] = $initial_options['']['minimum_password_length']; }
if ($_POST['minimum_password_length'] > $_POST['maximum_password_length']) { $_POST['minimum_password_length'] = $_POST['maximum_password_length']; }
foreach ($initial_options[''] as $key => $value) {
if ($_POST[$key] != '') { $options[$key] = $_POST[$key]; }
else { $options[$key] = $value; } }
update_option('affiliation_manager', $options);
foreach (array(
'activation_notification_email_body',
'bonus_proposal_email_body',
'deactivation_notification_email_body',
'password_reset_email_body',
'removal_notification_email_body',
'registration_confirmation_email_body',
'registration_custom_instructions',
'registration_notification_email_body') as $field) {
if ($_POST[$field] == '') { $_POST[$field] = $initial_options[$field]; }
update_option('affiliation_manager_'.$field, $_POST[$field]); } }
else { $options = (array) get_option('affiliation_manager'); }

$options = array_map('htmlspecialchars', $options);
$undisplayed_modules = (array) $back_office_options['options_page_undisplayed_modules'];
if (function_exists('commerce_data')) { $currency_code = commerce_data('currency_code'); }
else { $commerce_manager_options = (array) get_option('commerce_manager');
$currency_code = do_shortcode($commerce_manager_options['currency_code']); } ?>

<div class="wrap">
<div id="poststuff">
<?php affiliation_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>'; } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php affiliation_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<p class="description"><?php _e('You can reset an option by leaving the corresponding field blank.', 'affiliation-manager'); ?></p>
<?php affiliation_manager_pages_summary($back_office_options); ?>

<div class="postbox"<?php if (in_array('general-options', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="general-options"><strong><?php echo $modules['options']['general-options']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="affiliation_enabled" id="affiliation_enabled" value="yes"<?php if ($options['affiliation_enabled'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Use affiliation', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="url_variable_name"><?php _e('Name of the variable used in affiliate links', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="url_variable_name" id="url_variable_name" rows="1" cols="25"><?php echo $options['url_variable_name']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('You can customize the affiliate link structure.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#affiliate-links"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="url_variable_name2"><?php _e('Name of the alternative variable used in affiliate links', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="url_variable_name2" id="url_variable_name2" rows="1" cols="25"><?php echo $options['url_variable_name2']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Your affiliate program can be compatible with another affiliate link structure.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#alternative-affiliate-links"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="cookies_name"><?php _e('Cookies name', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="cookies_name" id="cookies_name" rows="1" cols="25"><?php echo $options['cookies_name']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#cookies"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="cookies_lifetime"><?php _e('Cookies lifetime', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="cookies_lifetime" id="cookies_lifetime" rows="1" cols="25"><?php echo $options['cookies_lifetime']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('days', 'affiliation-manager'); ?></span></td></tr>
</tbody></table>
<div<?php if (in_array('level-1-commission', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="level-1-commission"><strong><?php echo $modules['options']['general-options']['modules']['level-1-commission']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('The level 1 commission is awarded to the affiliate who referred the order.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#commissions-levels"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_type"><?php _e('Commission type', 'affiliation-manager'); ?></label></strong></th>
<td><select name="commission_type" id="commission_type">
<option value="constant"<?php if ($options['commission_type'] == 'constant') { echo ' selected="selected"'; } ?>><?php _e('Constant', 'affiliation-manager'); ?></option>
<option value="proportional"<?php if ($options['commission_type'] == 'proportional') { echo ' selected="selected"'; } ?>><?php _e('Proportional', 'affiliation-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_amount"><?php _e('Commission amount', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_amount" id="commission_amount" rows="1" cols="25"><?php echo $options['commission_amount']; ?></textarea>  <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you select constant commissions', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_percentage"><?php _e('Commission percentage', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_percentage" id="commission_percentage" rows="1" cols="25"><?php echo $options['commission_percentage']; ?></textarea> <span style="vertical-align: 25%;">% <?php _e('of the price', 'affiliation-manager'); ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you select proportional commissions', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="winner_affiliate"><?php _e('Commission award', 'affiliation-manager'); ?></label></strong></th>
<td><?php _e('The commission is awarded to the', 'affiliation-manager'); ?> <select name="winner_affiliate" id="winner_affiliate">
<option value="first"<?php if ($options['winner_affiliate'] == 'first') { echo ' selected="selected"'; } ?>><?php _e('first', 'affiliation-manager'); ?></option>
<option value="last"<?php if ($options['winner_affiliate'] == 'last') { echo ' selected="selected"'; } ?>><?php _e('last', 'affiliation-manager'); ?></option>
</select> <?php _e('affiliate who referred the order.', 'affiliation-manager'); ?> 
<span class="description"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#commission-award"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_payment"><?php _e('Commission payment', 'affiliation-manager'); ?></label></strong></th>
<td><select name="commission_payment" id="commission_payment">
<option value="deferred"<?php if ($options['commission_payment'] == 'deferred') { echo ' selected="selected"'; } ?>><?php _e('Deferred', 'affiliation-manager'); ?></option>
<option value="instant"<?php if ($options['commission_payment'] == 'instant') { echo ' selected="selected"'; } ?>><?php _e('Instant', 'affiliation-manager'); ?></option>
</select> 
<span class="description"><?php _e('You can pay your affiliates instantly.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#commission-payment"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="first_sale_winner"><?php _e('First sale award', 'affiliation-manager'); ?></label></strong></th>
<td><?php _e('The first sale referred by the affiliate is awarded to the', 'affiliation-manager'); ?> <select name="first_sale_winner" id="first_sale_winner">
<option value="affiliate"<?php if ($options['first_sale_winner'] == 'affiliate') { echo ' selected="selected"'; } ?>><?php _e('affiliate', 'affiliation-manager'); ?></option>
<option value="affiliator"<?php if ($options['first_sale_winner'] == 'affiliator') { echo ' selected="selected"'; } ?>><?php _e('affiliator', 'affiliation-manager'); ?></option>
</select>. 
<span class="description"><?php _e('Used for instant payment of commissions', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#first-sale-award"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div<?php if (in_array('level-2-commission', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="level-2-commission"><strong><?php echo $modules['options']['general-options']['modules']['level-2-commission']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('The level 2 commission is awarded to the referrer of the affiliate who referred the order.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#commissions-levels"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="commission2_enabled" id="commission2_enabled" value="yes"<?php if ($options['commission2_enabled'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Award a level 2 commission', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_type"><?php _e('Commission type', 'affiliation-manager'); ?></label></strong></th>
<td><select name="commission2_type" id="commission2_type">
<option value="constant"<?php if ($options['commission2_type'] == 'constant') { echo ' selected="selected"'; } ?>><?php _e('Constant', 'affiliation-manager'); ?></option>
<option value="proportional"<?php if ($options['commission2_type'] == 'proportional') { echo ' selected="selected"'; } ?>><?php _e('Proportional', 'affiliation-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_amount"><?php _e('Commission amount', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission2_amount" id="commission2_amount" rows="1" cols="25"><?php echo $options['commission2_amount']; ?></textarea>  <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you select constant commissions', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_percentage"><?php _e('Commission percentage', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission2_percentage" id="commission2_percentage" rows="1" cols="25"><?php echo $options['commission2_percentage']; ?></textarea> <span style="vertical-align: 25%;">% <?php _e('of the price', 'affiliation-manager'); ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you select proportional commissions', 'affiliation-manager'); ?></span></td></tr>
</tbody></table>
</div>
<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('clicks', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="clicks"><strong><?php echo $modules['options']['clicks']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="clicks_registration_enabled" id="clicks_registration_enabled" value="yes"<?php if ($options['clicks_registration_enabled'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Save clicks on affiliate links', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="maximum_clicks_quantity"><?php _e('Maximum clicks quantity', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="maximum_clicks_quantity" id="maximum_clicks_quantity" rows="1" cols="25"><?php echo ($options['maximum_clicks_quantity'] == 'unlimited' ? '' : $options['maximum_clicks_quantity']); ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('You can save only the latest clicks to ease your database.', 'affiliation-manager'); ?><br />
<?php _e('Leave this field blank for an unlimited quantity.', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('registration', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration"><strong><?php echo $modules['options']['registration']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="registration_required" id="registration_required" value="yes"<?php if ($options['registration_required'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Registration to the affiliate program required', 'affiliation-manager'); ?></label><br />
<span class="description"><?php _e('The registration can be optional, only if you select instant payment of commissions.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#optional-registration"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="minimum_login_length"><?php _e('Minimum login length', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="minimum_login_length" id="minimum_login_length" rows="1" cols="25"><?php echo $options['minimum_login_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="maximum_login_length"><?php _e('Maximum login length', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="maximum_login_length" id="maximum_login_length" rows="1" cols="25"><?php echo $options['maximum_login_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="minimum_password_length"><?php _e('Minimum password length', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="minimum_password_length" id="minimum_password_length" rows="1" cols="25"><?php echo $options['minimum_password_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="maximum_password_length"><?php _e('Maximum password length', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="maximum_password_length" id="maximum_password_length" rows="1" cols="25"><?php echo $options['maximum_password_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_url"><?php _e('Registration confirmation URL', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_url" id="registration_confirmation_url" rows="1" cols="75"><?php echo $options['registration_confirmation_url']; ?></textarea> <a style="vertical-align: 25%;" href="<?php echo htmlspecialchars(affiliation_format_url($options['registration_confirmation_url'])); ?>"><?php _e('Link', 'affiliation-manager'); ?></a></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."affiliation_manager_affiliates_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliates_initial_category_id"><?php _e('Affiliates initial category', 'affiliation-manager'); ?></label></strong></th>
<td><select name="affiliates_initial_category_id" id="affiliates_initial_category_id">
<option value="0"<?php if ($options['affiliates_initial_category_id'] == 0) { echo ' selected="selected"'; } ?>><?php _e('None ', 'affiliation-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($options['affiliates_initial_category_id'] == $category->id ? ' selected="selected"' : '').'>'.$category->name.'</option>'."\n"; } ?>
</select>
<span class="description"><?php _e('Category assigned to affiliates upon their registration', 'affiliation-manager'); ?></span>
<?php if ($options['affiliates_initial_category_id'] > 0) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$options['affiliates_initial_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$options['affiliates_initial_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliates_initial_status"><?php _e('Affiliates initial status', 'affiliation-manager'); ?></label></strong></th>
<td><select name="affiliates_initial_status" id="affiliates_initial_status">
<option value="active"<?php if ($options['affiliates_initial_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'affiliation-manager'); ?></option>
<option value="inactive"<?php if ($options['affiliates_initial_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'affiliation-manager'); ?></option>
</select>
<span class="description"><?php _e('Status assigned to affiliates upon their registration', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#affiliate-status"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('registration-confirmation-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration-confirmation-email"><strong><?php echo $modules['options']['registration-confirmation-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="registration_confirmation_email_sent" id="registration_confirmation_email_sent" value="yes"<?php if ($options['registration_confirmation_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration confirmation email', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_sender" id="registration_confirmation_email_sender" rows="1" cols="75"><?php echo $options['registration_confirmation_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_receiver" id="registration_confirmation_email_receiver" rows="1" cols="75"><?php echo $options['registration_confirmation_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_subject" id="registration_confirmation_email_subject" rows="1" cols="75"><?php echo $options['registration_confirmation_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_confirmation_email_body" id="registration_confirmation_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_registration_confirmation_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('registration-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration-notification-email"><strong><?php echo $modules['options']['registration-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="registration_notification_email_sent" id="registration_notification_email_sent" value="yes"<?php if ($options['registration_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration notification email', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_sender" id="registration_notification_email_sender" rows="1" cols="75"><?php echo $options['registration_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_receiver" id="registration_notification_email_receiver" rows="1" cols="75"><?php echo $options['registration_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_subject" id="registration_notification_email_subject" rows="1" cols="75"><?php echo $options['registration_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_notification_email_body" id="registration_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_registration_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('autoresponders', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="autoresponders"><strong><?php echo $modules['options']['autoresponders']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('You must make some adjustments so that the subscription works with some autoresponders.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#autoresponders"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="affiliate_subscribed_to_autoresponder" id="affiliate_subscribed_to_autoresponder" value="yes"<?php if ($options['affiliate_subscribed_to_autoresponder'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Subscribe the affiliate to an autoresponder list', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliate_autoresponder"><?php _e('Autoresponder', 'affiliation-manager'); ?></label></strong></th>
<td><select name="affiliate_autoresponder" id="affiliate_autoresponder">
<?php include 'autoresponders.php';
$autoresponder = do_shortcode($options['affiliate_autoresponder']);
foreach ($autoresponders as $value) {
echo '<option value="'.$value.'"'.($autoresponder == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliate_autoresponder_list"><?php _e('List', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="affiliate_autoresponder_list" id="affiliate_autoresponder_list" rows="1" cols="50"><?php echo $options['affiliate_autoresponder_list']; ?></textarea><br />
<span class="description"><?php _e('For some autoresponders, you must enter the list ID.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#autoresponders"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('membership', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="membership"><strong><?php echo $modules['options']['membership']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php echo (function_exists('membership_manager_admin_menu') ? '<a href="admin.php?page=membership-manager">'.__('Click here to configure the options of Membership Manager.', 'affiliation-manager').'</a>' : __('To use membership, you must have installed and activated <a href="http://www.kleor-editions.com/membership-manager">Membership Manager</a>.', 'affiliation-manager')); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="affiliate_subscribed_to_members_areas" id="affiliate_subscribed_to_members_areas" value="yes"<?php if ($options['affiliate_subscribed_to_members_areas'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Subscribe the affiliate to a member area', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliate_members_areas"><?php _e('Members areas', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="affiliate_members_areas" id="affiliate_members_areas" rows="1" cols="50"><?php echo $options['affiliate_members_areas']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#membership"><?php _e('More informations', 'affiliation-manager'); ?></a></span>
<?php if ((function_exists('membership_manager_admin_menu')) && (is_numeric($options['affiliate_members_areas'])) && ($options['affiliate_members_areas'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-area&amp;id='.$options['affiliate_members_areas'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-area&amp;id='.$options['affiliate_members_areas'].'&amp;action=delete">'.__('Delete').'</a>'; } ?><br />
<span class="description"><?php _e('Separate the IDs of the members areas with commas.', 'affiliation-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="registration_custom_instructions_executed" id="registration_custom_instructions_executed" value="yes"<?php if ($options['registration_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'affiliation-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_custom_instructions"><?php _e('PHP code', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_custom_instructions" id="registration_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_registration_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the registration of the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#custom-instructions"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('password-reset-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="password-reset-email"><strong><?php echo $modules['options']['password-reset-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_email_sender" id="password_reset_email_sender" rows="1" cols="75"><?php echo $options['password_reset_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_email_receiver" id="password_reset_email_receiver" rows="1" cols="75"><?php echo $options['password_reset_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_email_subject" id="password_reset_email_subject" rows="1" cols="75"><?php echo $options['password_reset_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="password_reset_email_body" id="password_reset_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_password_reset_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('bonus-proposal-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="bonus-proposal-email"><strong><?php echo $modules['options']['bonus-proposal-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="bonus_proposal_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="bonus_proposal_email_sender" id="bonus_proposal_email_sender" rows="1" cols="75"><?php echo $options['bonus_proposal_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="bonus_proposal_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="bonus_proposal_email_receiver" id="bonus_proposal_email_receiver" rows="1" cols="75"><?php echo $options['bonus_proposal_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="bonus_proposal_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="bonus_proposal_email_subject" id="bonus_proposal_email_subject" rows="1" cols="75"><?php echo $options['bonus_proposal_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="bonus_proposal_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="bonus_proposal_email_body" id="bonus_proposal_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_bonus_proposal_email_body')); ?></textarea>
<span class="description"><?php _e('You can allow your affiliates to offer a bonus to customers who order through their affiliate link.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#bonus-offered-by-affiliate"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('activation-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="activation-notification-email"><strong><?php echo $modules['options']['activation-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_sender" id="activation_notification_email_sender" rows="1" cols="75"><?php echo $options['activation_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_receiver" id="activation_notification_email_receiver" rows="1" cols="75"><?php echo $options['activation_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_subject" id="activation_notification_email_subject" rows="1" cols="75"><?php echo $options['activation_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="activation_notification_email_body" id="activation_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_activation_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('deactivation-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="deactivation-notification-email"><strong><?php echo $modules['options']['deactivation-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="deactivation_notification_email_sender" id="deactivation_notification_email_sender" rows="1" cols="75"><?php echo $options['deactivation_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="deactivation_notification_email_receiver" id="deactivation_notification_email_receiver" rows="1" cols="75"><?php echo $options['deactivation_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="deactivation_notification_email_subject" id="deactivation_notification_email_subject" rows="1" cols="75"><?php echo $options['deactivation_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="deactivation_notification_email_body" id="deactivation_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_deactivation_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('removal-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="removal-notification-email"><strong><?php echo $modules['options']['removal-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_sender"><?php _e('Sender', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="removal_notification_email_sender" id="removal_notification_email_sender" rows="1" cols="75"><?php echo $options['removal_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_receiver"><?php _e('Receiver', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="removal_notification_email_receiver" id="removal_notification_email_receiver" rows="1" cols="75"><?php echo $options['removal_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_subject"><?php _e('Subject', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="removal_notification_email_subject" id="removal_notification_email_subject" rows="1" cols="75"><?php echo $options['removal_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_body"><?php _e('Body', 'affiliation-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="removal_notification_email_body" id="removal_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('affiliation_manager_removal_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the affiliate.', 'affiliation-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#email-shortcodes"><?php _e('More informations', 'affiliation-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes'); ?>" /></p>
<?php affiliation_manager_pages_module($back_office_options, 'options-page', $undisplayed_modules); ?>
</form>
</div>
</div>
<?php }