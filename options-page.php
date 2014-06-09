<?php if ((isset($_GET['action'])) && (($_GET['action'] == 'reset') || ($_GET['action'] == 'uninstall'))) {
$for = (((isset($_GET['for'])) && (is_multisite()) && (current_user_can('manage_network'))) ? $_GET['for'] : 'single');
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if ($_GET['action'] == 'reset') { reset_audiobooks_pages(); } else { uninstall_audiobooks_pages($for); } } ?>
<div class="wrap">
<h2><?php _e('Audiobooks Pages', 'audiobooks-pages'); ?></h2>
<div class="clear"></div>
<?php if (isset($_POST['submit'])) {
echo '<div class="updated"><p><strong>'.($_GET['action'] == 'reset' ? __('Options reset.', 'audiobooks-pages') : __('Options deleted.', 'audiobooks-pages')).'</strong></p></div>
<script type="text/javascript">setTimeout(\'window.location = "'.($_GET['action'] == 'reset' ? 'options-general.php?page=audiobooks-pages' : ($for == 'network' ? 'network/' : '').'plugins.php').'"\', 2000);</script>'; } ?>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<p><strong style="color: #c00000;"><?php if ($_GET['action'] == 'reset') { _e('Do you really want to reset the options of Audiobooks Pages?', 'audiobooks-pages'); }
elseif ($for == 'network') { _e('Do you really want to permanently delete the options of Audiobooks Pages for all sites in this network?', 'audiobooks-pages'); }
else { _e('Do you really want to permanently delete the options of Audiobooks Pages?', 'audiobooks-pages'); } ?></strong> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'audiobooks-pages'); ?>" /></p>
</div>
</form><?php } ?>
</div><?php }

else {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = stripslashes(html_entity_decode(str_replace('&nbsp;', ' ', $value))); } }
include AUDIOBOOKS_PAGES_PATH.'initial-options.php';
foreach ($initial_options as $key => $value) { if (!isset($_POST[$key])) { $_POST[$key] = ''; } }
foreach ($initial_options as $key => $value) { if ($_POST[$key] == '') { $_POST[$key] = $value; } $options[$key] = $_POST[$key]; }
update_option('audiobooks_pages', $options); }
else { $options = (array) get_option('audiobooks_pages'); }

foreach ($options as $key => $value) {
if (is_string($value)) { $options[$key] = htmlspecialchars($value); } } ?>

<div class="wrap">
<h2><?php _e('Audiobooks Pages', 'audiobooks-pages'); ?></h2>
<div class="clear"></div>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Settings saved.', 'audiobooks-pages').'</strong></p></div>'; } ?>
<h3><?php _e('Options', 'audiobooks-pages'); ?></h3>
<form method="post" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<p><label><?php _e('Content of categories pages:', 'audiobooks-pages'); ?> <textarea name="category_page_content" id="category_page_content" rows="10" cols="50"><?php echo $options['category_page_content']; ?></textarea></label></p>
<p><label><?php _e('Content of audiobooks pages:', 'audiobooks-pages'); ?> <textarea name="page_content" id="page_content" rows="30" cols="50"><?php echo $options['page_content']; ?></textarea></label></p>
<p class="submit"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes', 'audiobooks-pages'); ?>" /></p>
</form>
</div>
<?php }