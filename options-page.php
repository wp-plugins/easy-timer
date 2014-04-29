<?php global $wpdb; $error = '';
$back_office_options = (array) get_option('audiobooks_authors_and_narrators_back_office');
$admin_page = 'options';

if ((isset($_GET['action'])) && (($_GET['action'] == 'reset') || ($_GET['action'] == 'uninstall'))) {
$for = (((isset($_GET['for'])) && (is_multisite()) && (current_user_can('manage_network'))) ? $_GET['for'] : 'single');
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!audiobooks_authors_and_narrators_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'audiobooks-authors-and-narrators'); }
else { if ($_GET['action'] == 'reset') { reset_audiobooks_authors_and_narrators(); } else { uninstall_audiobooks_authors_and_narrators($for); } } } ?>
<div class="wrap">
<div id="poststuff" style="padding-top: 0;">
<?php audiobooks_authors_and_narrators_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) {
echo '<div class="updated"><p><strong>'.($_GET['action'] == 'reset' ? __('Options reset.', 'audiobooks-authors-and-narrators') : __('Options and tables deleted.', 'audiobooks-authors-and-narrators')).'</strong></p></div>
<script type="text/javascript">setTimeout(\'window.location = "'.($_GET['action'] == 'reset' ? 'admin.php?page=audiobooks-authors-and-narrators' : ($for == 'network' ? 'network/' : '').'plugins.php').'"\', 2000);</script>'; } ?>
<?php audiobooks_authors_and_narrators_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<p><strong style="color: #c00000;"><?php if ($_GET['action'] == 'reset') { _e('Do you really want to reset the options of Audiobooks - Authors And Narrators?', 'audiobooks-authors-and-narrators'); }
elseif ($for == 'network') { _e('Do you really want to permanently delete the options and tables of Audiobooks - Authors And Narrators for all sites in this network?', 'audiobooks-authors-and-narrators'); }
else { _e('Do you really want to permanently delete the options and tables of Audiobooks - Authors And Narrators?', 'audiobooks-authors-and-narrators'); } ?></strong> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'audiobooks-authors-and-narrators'); ?>" /></p>
</div>
</form><?php } ?>
</div>
</div><?php }

else {
include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'admin-pages.php';
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!audiobooks_authors_and_narrators_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'audiobooks-authors-and-narrators'); }
else {
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = stripslashes(html_entity_decode(str_replace('&nbsp;', ' ', $value))); } }
include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'includes/fill-admin-page-fields.php'; } }
if (!isset($options)) { $options = (array) get_option('audiobooks_authors_and_narrators'); }

foreach ($options as $key => $value) {
if (is_string($value)) { $options[$key] = htmlspecialchars($value); } }
$undisplayed_modules = (array) $back_office_options['options_page_undisplayed_modules']; ?>

<div class="wrap">
<div id="poststuff" style="padding-top: 0;">
<?php audiobooks_authors_and_narrators_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Settings saved.', 'audiobooks-authors-and-narrators').'</strong></p></div>'; } ?>
<form method="post" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php audiobooks_authors_and_narrators_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<p class="description"><?php _e('You can reset an option by leaving the corresponding field blank.', 'audiobooks-authors-and-narrators'); ?></p>
<?php audiobooks_authors_and_narrators_pages_summary($back_office_options); ?>

<div class="postbox" id="descriptions-module"<?php if (in_array('descriptions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 style="font-size: 1.25em;" id="descriptions"><strong><?php echo $modules['options']['descriptions']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="author_description_code"><?php _e('Author', 'audiobooks-authors-and-narrators'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="author_description_code" id="author_description_code" rows="5" cols="75"><?php echo $options['author_description_code']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into this field to display informations about the author.', 'audiobooks-authors-and-narrators'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="narrator_description_code"><?php _e('Narrator', 'audiobooks-authors-and-narrators'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="narrator_description_code" id="narrator_description_code" rows="5" cols="75"><?php echo $options['narrator_description_code']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into this field to display informations about the narrator.', 'audiobooks-authors-and-narrators'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update', 'audiobooks-authors-and-narrators'); ?>" /></td></tr>
</tbody></table>
</div></div>

<p class="submit"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes', 'audiobooks-authors-and-narrators'); ?>" /></p>
</form>
</div>
</div>

<script type="text/javascript">
var anchor = window.location.hash;
<?php foreach ($modules['options'] as $key => $value) {
echo "if (anchor == '#".$key."') { document.getElementById('".$key."-module').style.display = 'block'; }\n";
if (isset($value['modules'])) { foreach ($value['modules'] as $module_key => $module_value) {
echo "if (anchor == '#".$module_key."') {
document.getElementById('".$key."-module').style.display = 'block';
document.getElementById('".$module_key."-module').style.display = 'block'; }\n"; } } } ?>
</script>
<?php }