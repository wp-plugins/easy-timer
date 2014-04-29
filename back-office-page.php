<?php global $wpdb; $error = '';
$options = (array) get_option('audiobooks_authors_and_narrators_back_office');
foreach (array('admin-pages.php', 'initial-options.php') as $file) { include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.$file; }
$max_links = count($admin_links);
$max_menu_items = count($admin_pages);

if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!audiobooks_authors_and_narrators_user_can($options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'audiobooks-authors-and-narrators'); }
else {
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = stripslashes(html_entity_decode(str_replace('&nbsp;', ' ', $value))); } }
foreach ($initial_options['back_office'] as $key => $value) { if (!isset($_POST[$key])) { $_POST[$key] = ''; } }
foreach (array(
'custom_icon_used',
'menu_displayed',
'title_displayed') as $field) { if ($_POST[$field] != 'yes') { $_POST[$field] = 'no'; } }
foreach (array('back_office') as $page) { update_audiobooks_authors_and_narrators_back_office($options, $page); }
$_POST['minimum_roles'] = array();
foreach (array('manage', 'view') as $key) { $_POST['minimum_roles'][$key] = $_POST[$key.'_minimum_role']; }
if (isset($_POST['reset_menu_items'])) {
foreach (array('menu_items', 'menu_displayed_items') as $field) { $_POST[$field] = $initial_options['back_office'][$field]; } }
else {
$_POST['menu_displayed_items'] = array();
for ($i = 0; $i < $max_menu_items; $i++) {
$_POST['menu_items'][$i] = $_POST['menu_item'.$i];
if (isset($_POST['menu_item'.$i.'_displayed'])) { $_POST['menu_displayed_items'][] = $i; } } }
foreach ($initial_options['back_office'] as $key => $value) { if ($_POST[$key] == '') { $_POST[$key] = $value; } $options[$key] = $_POST[$key]; }
update_option('audiobooks_authors_and_narrators_back_office', $options); } }

$undisplayed_modules = (array) $options['back_office_page_undisplayed_modules']; ?>

<div class="wrap">
<div id="poststuff" style="padding-top: 0;">
<?php audiobooks_authors_and_narrators_pages_top($options); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Settings saved.', 'audiobooks-authors-and-narrators').'</strong></p></div>'; } ?>
<form method="post" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php audiobooks_authors_and_narrators_pages_menu($options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<?php audiobooks_authors_and_narrators_pages_summary($options); ?>

<div class="postbox" id="capabilities-module"<?php if (in_array('capabilities', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 style="font-size: 1.25em;" id="capabilities"><strong><?php echo $modules['back_office']['capabilities']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="view_minimum_role"><?php _e('Access', 'audiobooks-authors-and-narrators'); ?></label></strong></th>
<td><select name="view_minimum_role" id="view_minimum_role">
<?php foreach ($roles as $key => $value) {
echo '<option value="'.$key.'"'.($options['minimum_roles']['view'] == $key ? ' selected="selected"' : '').'>'.$value['name'].'</option>'."\n"; } ?>
</select> <span class="description"><?php _e('Minimum role to access the interface of Audiobooks - Authors And Narrators', 'audiobooks-authors-and-narrators'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="manage_minimum_role"><?php _e('Management', 'audiobooks-authors-and-narrators'); ?></label></strong></th>
<td><select name="manage_minimum_role" id="manage_minimum_role">
<?php foreach ($roles as $key => $value) {
echo '<option value="'.$key.'"'.($options['minimum_roles']['manage'] == $key ? ' selected="selected"' : '').'>'.$value['name'].'</option>'."\n"; } ?>
</select> <span class="description"><?php _e('Minimum role to change options and add, edit or delete items of Audiobooks - Authors And Narrators', 'audiobooks-authors-and-narrators'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update', 'audiobooks-authors-and-narrators'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="icon-module"<?php if (in_array('icon', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 style="font-size: 1.25em;" id="icon"><strong><?php echo $modules['back_office']['icon']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="custom_icon_used" id="custom_icon_used" value="yes"<?php if ($options['custom_icon_used'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Use a custom icon', 'audiobooks-authors-and-narrators'); ?></label>
 <span class="description" style="vertical-align: -5%;"><?php _e('Icon displayed in the admin menu of WordPress', 'audiobooks-authors-and-narrators'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="custom_icon_url"><?php _e('Icon URL', 'audiobooks-authors-and-narrators'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="custom_icon_url" id="custom_icon_url" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/90)))+'em';" onblur="this.style.height = '1.75em';" cols="75"><?php echo $options['custom_icon_url']; ?></textarea> 
<span style="vertical-align: 25%;"><a href="<?php echo htmlspecialchars(format_url(do_shortcode($options['custom_icon_url']))); ?>"><?php _e('Link', 'audiobooks-authors-and-narrators'); ?></a>
<?php if (current_user_can('upload_files')) { echo ' | <a href="media-new.php" title="'.__('After the upload, you will just need to copy and paste the URL of the image in this field.', 'audiobooks-authors-and-narrators').'">'.__('Upload an image', 'audiobooks-authors-and-narrators').'</a>'; } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update', 'audiobooks-authors-and-narrators'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="top-module"<?php if (in_array('top', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 style="font-size: 1.25em;" id="top"><strong><?php echo $modules['back_office']['top']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="title_displayed" id="title_displayed" value="yes"<?php if ($options['title_displayed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Display the title', 'audiobooks-authors-and-narrators'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="title"><?php _e('Title', 'audiobooks-authors-and-narrators'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="title" id="title" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/30)))+'em';" onblur="this.style.height = '1.75em';" cols="25"><?php echo $options['title']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update', 'audiobooks-authors-and-narrators'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="menu-module"<?php if (in_array('menu', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 style="font-size: 1.25em;" id="menu"><strong><?php echo $modules['back_office']['menu']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="menu_displayed" id="menu_displayed" value="yes"<?php if ($options['menu_displayed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Display the menu', 'audiobooks-authors-and-narrators'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><?php _e('Pages', 'audiobooks-authors-and-narrators'); ?></strong></th>
<td><input type="hidden" name="submit" value="true" /><input style="margin-bottom: 0.5em;" type="submit" class="button-secondary" name="reset_menu_items" formaction="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>#menu" value="<?php _e('Reset the pages', 'audiobooks-authors-and-narrators'); ?>" /><br />
<?php $menu_displayed_items = (array) $options['menu_displayed_items'];
for ($i = 0; $i < $max_menu_items; $i++) {
echo '<label>'.__('Page', 'audiobooks-authors-and-narrators').' '.($i + 1).($i < 9 ? '&nbsp;&nbsp;': '').' <select name="menu_item'.$i.'" id="menu_item'.$i.'">';
foreach ($admin_pages as $key => $value) { echo '<option value="'.$key.'"'.($options['menu_items'][$i] == $key ? ' selected="selected"' : '').'>'.$value['menu_title'].'</option>'."\n"; }
echo '</select></label>
<label><input type="checkbox" name="menu_item'.$i.'_displayed" id="menu_item'.$i.'_displayed" value="yes"'.(!in_array($i, $menu_displayed_items) ? '' : ' checked="checked"').' /> '.__('Display', 'audiobooks-authors-and-narrators').'</label><br />'; } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update', 'audiobooks-authors-and-narrators'); ?>" /></td></tr>
</tbody></table>
</div></div>

<?php foreach (array('back-office-page') as $module) { audiobooks_authors_and_narrators_pages_module($options, $module, $undisplayed_modules); } ?>

<p class="submit"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes', 'audiobooks-authors-and-narrators'); ?>" /></p>
</form>
</div>
</div>

<script type="text/javascript">
var anchor = window.location.hash;
<?php foreach ($modules['back_office'] as $key => $value) {
echo "if (anchor == '#".$key."') { document.getElementById('".$key."-module').style.display = 'block'; }\n";
if (isset($value['modules'])) { foreach ($value['modules'] as $module_key => $module_value) {
echo "if (anchor == '#".$module_key."') {
document.getElementById('".$key."-module').style.display = 'block';
document.getElementById('".$module_key."-module').style.display = 'block'; }\n"; } } }
foreach (array('custom_icon_url', 'title') as $field) { echo 'document.getElementById("'.$field.'").placeholder = "'.str_replace('"', '\"', audiobooks_authors_and_narrators_format_placeholder($initial_options['back_office'][$field])).'";'."\n"; }
foreach (array('reset_links' => 'top', 'reset_menu_items' => 'menu') as $field => $location) { if (isset($_POST[$field])) { echo 'window.location = \'#'.$location.'\';'; } } ?>
</script>