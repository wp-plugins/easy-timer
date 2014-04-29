<?php global $wpdb; $error = '';
$back_office_options = (array) get_option('audiobooks_authors_and_narrators_back_office');
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$current_time = time();
$current_date = date('Y-m-d H:i:s', $current_time + 3600*UTC_OFFSET);
$current_date_utc = date('Y-m-d H:i:s', $current_time);
$admin_page = 'narrator';

if ((isset($_GET['id'])) && (isset($_GET['action'])) && ($_GET['action'] == 'delete')) {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!audiobooks_authors_and_narrators_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'audiobooks-authors-and-narrators'); }
else {
$results = $wpdb->query("DELETE FROM ".$wpdb->prefix."audiobooks_authors_and_narrators_narrators WHERE id = ".$_GET['id']);
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."audiobooks_authors_and_narrators_narrators ORDER BY id DESC LIMIT 1", OBJECT);
if (!$result) { $results = $wpdb->query("ALTER TABLE ".$wpdb->prefix."audiobooks_authors_and_narrators_narrators AUTO_INCREMENT = 1"); }
elseif ($result->id < $_GET['id']) {
$results = $wpdb->query("ALTER TABLE ".$wpdb->prefix."audiobooks_authors_and_narrators_narrators AUTO_INCREMENT = ".($result->id + 1)); } } } ?>
<div class="wrap">
<div id="poststuff" style="padding-top: 0;">
<?php audiobooks_authors_and_narrators_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) {
echo '<div class="updated"><p><strong>'.__('Narrator deleted.', 'audiobooks-authors-and-narrators').'</strong></p></div>
<script type="text/javascript">setTimeout(\'window.location = "admin.php?page=audiobooks-authors-and-narrators-narrators"\', 2000);</script>'; } ?>
<?php audiobooks_authors_and_narrators_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<p><strong style="color: #c00000;"><?php _e('Do you really want to permanently delete this narrator?', 'audiobooks-authors-and-narrators'); ?></strong> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'audiobooks-authors-and-narrators'); ?>" /></p>
</div>
<div class="clear"></div>
</form><?php } ?>
</div>
</div><?php }

else {
include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'libraries/plugins.php';
include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'admin-pages.php'; include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'tables.php';
foreach ($tables['narrators'] as $key => $value) { if (!isset($_POST[$key])) { $_POST[$key] = ''; } }
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!audiobooks_authors_and_narrators_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'audiobooks-authors-and-narrators'); }
else {
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = stripslashes(html_entity_decode(str_replace('&nbsp;', ' ', $value))); } }
include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'includes/fill-admin-page-fields.php'; } }

if (isset($_GET['id'])) {
$narrator_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."audiobooks_authors_and_narrators_narrators WHERE id = ".$_GET['id'], OBJECT);
if ($narrator_data) {
$GLOBALS['narrator_data'] = (array) $narrator_data;
foreach ($narrator_data as $key => $value) { if ((!isset($_POST[$key])) || (!isset($_POST[$key.'_error']))) { $_POST[$key] = $value; } } }
elseif (!headers_sent()) { header('Location: admin.php?page=audiobooks-authors-and-narrators-narrators'); exit(); }
else { echo '<script type="text/javascript">window.location = "admin.php?page=audiobooks-authors-and-narrators-narrators";</script>'; } }

foreach ($_POST as $key => $value) {
if (is_string($value)) {
$_POST[$key] = str_replace(array('&amp;amp;', '&amp;apos;', '&amp;quot;'), array('&amp;', '&apos;', '&quot;'), htmlspecialchars(stripslashes($value)));
if (($value == '0000-00-00 00:00:00') && ((substr($key, -4) == 'date') || (substr($key, -8) == 'date_utc'))) { $_POST[$key] = ''; } } }
$undisplayed_modules = (array) $back_office_options['narrator_page_undisplayed_modules']; ?>

<div class="wrap">
<div id="poststuff" style="padding-top: 0;">
<?php audiobooks_authors_and_narrators_pages_top($back_office_options); ?>
<?php if ((isset($updated)) && ($updated)) {
echo '<div class="updated"><p><strong>'.(isset($_GET['id']) ? __('Narrator updated.', 'audiobooks-authors-and-narrators') : __('Narrator saved.', 'audiobooks-authors-and-narrators')).'</strong></p></div>
'.(isset($_GET['id']) ? '' : '<script type="text/javascript">setTimeout(\'window.location = "admin.php?page=audiobooks-authors-and-narrators-narrators"\', 2000);</script>'); } ?>
<form method="post" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php audiobooks_authors_and_narrators_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<?php audiobooks_authors_and_narrators_pages_summary($back_office_options); ?>

<div class="postbox" id="personal-informations-module"<?php if (in_array('personal-informations', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 style="font-size: 1.25em;" id="personal-informations"><strong><?php echo $modules['narrator']['personal-informations']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="id">'.__('ID', 'audiobooks-authors-and-narrators').'</label></strong></th>
<td><input type="text" name="id" id="id" size="10" value="'.$_GET['id'].'" disabled="disabled" /> <span class="description">'.__('The ID can not be changed.', 'audiobooks-authors-and-narrators').'</span><br />
<a style="text-decoration: none;" href="admin.php?page=audiobooks-authors-and-narrators-narrator&amp;id='.$_GET['id'].'&amp;action=delete" class="delete">'.__('Delete', 'audiobooks-authors-and-narrators').'</a></td></tr>'; } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="first_name"><?php _e('First name', 'audiobooks-authors-and-narrators'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="first_name" id="first_name" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/60)))+'em';" onblur="this.style.height = '1.75em';" cols="50"><?php echo $_POST['first_name']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="last_name"><?php _e('Last name', 'audiobooks-authors-and-narrators'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="last_name" id="last_name" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/60)))+'em';" onblur="this.style.height = '1.75em';" cols="50"><?php echo $_POST['last_name']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="email_address"><?php _e('Email address', 'audiobooks-authors-and-narrators'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="email_address" id="email_address" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/60)))+'em';" onblur="this.style.height = '1.75em';" cols="50" onchange="fill_form(this.form);"><?php echo $_POST['email_address']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="description"><?php _e('Description', 'audiobooks-authors-and-narrators'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; width: 75%;" name="description" id="description" rows="5" cols="75"><?php echo $_POST['description']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="audiobooks_url"><?php _e('Audiobooks URL', 'audiobooks-authors-and-narrators'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="audiobooks_url" id="audiobooks_url" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/90)))+'em';" onblur="this.style.height = '1.75em';" cols="75"><?php echo $_POST['audiobooks_url']; ?></textarea> 
<?php $url = htmlspecialchars(narrator_data(array(0 => 'audiobooks_url', 'part' => 1, 'id' => (isset($_GET['id']) ? $_GET['id'] : 0)))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'audiobooks-authors-and-narrators'); ?></a><?php } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="profile_url"><?php _e('Profile URL', 'audiobooks-authors-and-narrators'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="profile_url" id="profile_url" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/90)))+'em';" onblur="this.style.height = '1.75em';" cols="75"><?php echo $_POST['profile_url']; ?></textarea> 
<?php $url = htmlspecialchars(narrator_data(array(0 => 'profile_url', 'part' => 1, 'id' => (isset($_GET['id']) ? $_GET['id'] : 0)))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'audiobooks-authors-and-narrators'); ?></a><?php } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="date"><?php _e('Registration date', 'audiobooks-authors-and-narrators'); ?></label></strong></th>
<td><input class="date-pick" type="text" name="date" id="date" size="20" value="<?php echo ($_POST['date'] != '' ? $_POST['date'] : $current_date); ?>" onchange="fill_form(this.form);" /></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th><td><input type="submit" class="button-secondary" name="submit" value="<?php echo (isset($_GET['id']) ? __('Update', 'audiobooks-authors-and-narrators') : __('Save', 'audiobooks-authors-and-narrators')); ?>" /></td></tr>
</tbody></table>
</div></div>

<p class="submit"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php (isset($_GET['id']) ? _e('Save Changes', 'audiobooks-authors-and-narrators') : _e('Save Narrator', 'audiobooks-authors-and-narrators')); ?>" /></p>
</form>
</div>
</div>

<script type="text/javascript">
var anchor = window.location.hash;
<?php foreach ($modules['narrator'] as $key => $value) {
echo "if (anchor == '#".$key."') { document.getElementById('".$key."-module').style.display = 'block'; }\n";
if (isset($value['modules'])) { foreach ($value['modules'] as $module_key => $module_value) {
echo "if (anchor == '#".$module_key."') {
document.getElementById('".$key."-module').style.display = 'block';
document.getElementById('".$module_key."-module').style.display = 'block'; }\n"; } } } ?>

<?php $fields = array();
foreach ($tables['narrators'] as $key => $value) { $fields[] = $key; }
$string = ''; foreach ($fields as $field) { $string .= '"'.$field.'", '; }
echo 'function fill_form(form) {
var data = new Object();
var fields = new Array('.substr($string, 0, -2).');
for (i = 0; i < fields.length; i++) {
if (form[fields[i]]) {
if (form[fields[i]].type != "checkbox") { data[fields[i]] = form[fields[i]].value; }
else { if (form[fields[i]].checked == true) { data[fields[i]] = "yes"; } } } }
jQuery.post("'.AUDIOBOOKS_AUTHORS_AND_NARRATORS_URL.'index.php?action=fill-admin-page-fields&page='.$_GET['page'].(isset($_GET['id']) ? '&id='.$_GET['id'] : '').'&time='.$current_time.'&key='.md5(AUTH_KEY).'", data, function(data) {
for (i = 0; i < fields.length; i++) {
if ((form[fields[i]]) && (typeof data[fields[i]] != "undefined")) {
if (form[fields[i]].type != "checkbox") { form[fields[i]].value = data[fields[i]]; }
else { if (data[fields[i]] == "yes") { form[fields[i]].checked = true; } else { form[fields[i]].checked = false; } }
if (document.getElementById(fields[i]+"_error")) {
if (typeof data[fields[i]+"_error"] == "undefined") { document.getElementById(fields[i]+"_error").innerHTML = ""; }
else { document.getElementById(fields[i]+"_error").innerHTML = data[fields[i]+"_error"]; } } } }
jQuery(".noscript").css("display", "none"); }, "json"); }'."\n"; ?>
</script>
<?php }