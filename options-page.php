<?php if ($_GET['action'] == 'uninstall') {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) { delete_option('easy_timer'); } ?>
<div class="wrap">
<h2>Easy Timer</h2>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Options deleted.', 'easy-timer').'</strong></p></div>'; } ?>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php _e('Do you really want to permanently delete the options of Easy Timer?', 'easy-timer'); ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'easy-timer'); ?>" />
</div>
</form><?php } ?>
</div><?php }

else {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
include 'initial-options.php';
$_POST = array_map('html_entity_decode', $_POST);
$_POST = array_map('stripslashes', $_POST);
$_POST['cookies_lifetime'] = (int) $_POST['cookies_lifetime'];
if ($_POST['cookies_lifetime'] < 1) { $_POST['cookies_lifetime'] = $initial_options['cookies_lifetime']; }
if ($_POST['javascript_enabled'] != 'yes') { $_POST['javascript_enabled'] = 'no'; }
foreach ($initial_options as $key => $value) {
if ($_POST[$key] != '') { $options[$key] = $_POST[$key]; }
else { $options[$key] = $value; } }
update_option('easy_timer', $options); }
else { $options = (array) get_option('easy_timer'); }

$options = array_map('htmlspecialchars', $options); ?>

<div class="wrap">
<h2>Easy Timer</h2>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>'; } ?>
<p style="margin: 1.5em"><a href="http://www.kleor-editions.com/easy-timer"><?php _e('Documentation', 'easy-timer'); ?></a></p>
<h3><?php _e('Options', 'easy-timer'); ?></h3>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<p><label for="default_timer_prefix"><?php _e('The', 'easy-timer'); ?> <code>[timer]</code> <?php _e('shortcode is equivalent to', 'easy-timer'); ?>:</label> 
<select name="default_timer_prefix" id="default_timer_prefix">
<?php $prefixes = array('dhms', 'dhm', 'dh', 'd', 'hms', 'hm', 'h', 'ms', 'm', 's');
foreach ($prefixes as $prefix) {
echo '<option value="'.$prefix.'"'.($options['default_timer_prefix'] == $prefix ? ' selected="selected"' : '').'>['.$prefix.'timer]</option>'."\n"; } ?>
</select>. <a href="http://www.kleor-editions.com/easy-timer/#timer-shortcodes"><?php _e('More informations', 'easy-timer'); ?></a><br />
<?php $prefixes = array('total', 'elapsed', 'total-elapsed', 'remaining', 'total-remaining');
foreach ($prefixes as $prefix) {
echo __('The', 'easy-timer').' <code>['.$prefix.'-timer]</code> '.__('shortcode is equivalent to', 'easy-timer').' <code>['.$prefix.'-'.$options['default_timer_prefix'].'timer]</code>.<br />'; } ?></p>
<p><label for="cookies_lifetime"><?php _e('Cookies lifetime (used for relative dates)', 'easy-timer'); ?>:</label> <input type="text" name="cookies_lifetime" id="cookies_lifetime" value="<?php echo $options['cookies_lifetime']; ?>" size="4" /> <?php _e('days', 'easy-timer'); ?> <a href="http://www.kleor-editions.com/easy-timer/#relative-dates"><?php _e('More informations', 'easy-timer'); ?></a></p>
<p><input type="checkbox" name="javascript_enabled" id="javascript_enabled" value="yes"<?php if ($options['javascript_enabled'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="javascript_enabled"><?php _e('Add JavaScript code', 'easy-timer'); ?></label><br />
<span class="description"><?php _e('If you uncheck this box, Easy Timer will never add any JavaScript code to the pages of your website, but your count up/down timers will not refresh.', 'easy-timer'); ?></span></p>
<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes'); ?>" /></p>
</form>
</div>
<?php }