<?php if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

if ($_POST['submit'] ==  __('Save Changes')) {
$default_timer_prefix = $_POST['default-timer-prefix'];
$cookies_lifetime = (int) $_POST['cookies-lifetime']; if (empty($cookies_lifetime)) { $cookies_lifetime = 15; }
if ($_POST['javascript-enabled'] == 'yes') { $javascript_enabled = 'yes'; } else { $javascript_enabled = 'no'; }

$easy_timer_options = array(
'default_timer_prefix' => $default_timer_prefix,
'cookies_lifetime' => $cookies_lifetime,
'javascript_enabled' => $javascript_enabled);
update_option('easy_timer', $easy_timer_options); }

$easy_timer_options = get_option('easy_timer'); ?>

<div class="wrap">
<h2>Easy Timer</h2>
<?php if ($_POST['submit'] ==  __('Save Changes')) { echo '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>'; } ?>
<p><?php _e('Complete Documentation', 'easy-timer'); ?>:</p>
<ul style="margin: 1.5em">
<li><a href="http://www.kleor-editions.com/easy-timer/en"><?php _e('in English', 'easy-timer'); ?></a></li>
<li><a href="http://www.kleor-editions.com/easy-timer"><?php _e('in French', 'easy-timer'); ?></a></li>
</ul>
<h3><?php _e('Options', 'easy-timer'); ?></h3>
<form method="post" action="">
<p><label for="default-timer-prefix"><?php _e('The <code>[timer]</code> shortcode is equivalent to', 'easy-timer'); ?>:</label> 
<select name="default-timer-prefix" id="default-timer-prefix">
<option value="dhms"<?php if ($easy_timer_options['default_timer_prefix'] == 'dhms') { echo ' selected="selected"'; } ?>>[dhmstimer]</option>
<option value="dhm"<?php if ($easy_timer_options['default_timer_prefix'] == 'dhm') { echo ' selected="selected"'; } ?>>[dhmtimer]</option>
<option value="dh"<?php if ($easy_timer_options['default_timer_prefix'] == 'dh') { echo ' selected="selected"'; } ?>>[dhtimer]</option>
<option value="d"<?php if ($easy_timer_options['default_timer_prefix'] == 'd') { echo ' selected="selected"'; } ?>>[dtimer]</option>
<option value="hms"<?php if ($easy_timer_options['default_timer_prefix'] == 'hms') { echo ' selected="selected"'; } ?>>[hmstimer]</option>
<option value="hm"<?php if ($easy_timer_options['default_timer_prefix'] == 'hm') { echo ' selected="selected"'; } ?>>[hmtimer]</option>
<option value="h"<?php if ($easy_timer_options['default_timer_prefix'] == 'h') { echo ' selected="selected"'; } ?>>[htimer]</option>
<option value="ms"<?php if ($easy_timer_options['default_timer_prefix'] == 'ms') { echo ' selected="selected"'; } ?>>[mstimer]</option>
<option value="m"<?php if ($easy_timer_options['default_timer_prefix'] == 'm') { echo ' selected="selected"'; } ?>>[mtimer]</option>
<option value="s"<?php if ($easy_timer_options['default_timer_prefix'] == 's') { echo ' selected="selected"'; } ?>>[stimer]</option>
</select> <?php _e('<a href="http://www.kleor-editions.com/easy-timer/en/#part4.2">More informations</a>', 'easy-timer'); ?><br />
<?php _e('The <code>[total-timer]</code> shortcode is equivalent to', 'easy-timer'); ?> <code>[total-<?php echo $easy_timer_options['default_timer_prefix']; ?>timer]</code>.<br />
<?php _e('The <code>[elapsed-timer]</code> shortcode is equivalent to', 'easy-timer'); ?> <code>[elapsed-<?php echo $easy_timer_options['default_timer_prefix']; ?>timer]</code>.<br />
<?php _e('The <code>[total-elapsed-timer]</code> shortcode is equivalent to', 'easy-timer'); ?> <code>[total-elapsed-<?php echo $easy_timer_options['default_timer_prefix']; ?>timer]</code>.<br />
<?php _e('The <code>[remaining-timer]</code> shortcode is equivalent to', 'easy-timer'); ?> <code>[remaining-<?php echo $easy_timer_options['default_timer_prefix']; ?>timer]</code>.<br />
<?php _e('The <code>[total-remaining-timer]</code> shortcode is equivalent to', 'easy-timer'); ?> <code>[total-remaining-<?php echo $easy_timer_options['default_timer_prefix']; ?>timer]</code>.</p>
<p><label for="cookies-lifetime"><?php _e('Cookies lifetime (used for relative dates)', 'easy-timer'); ?>:</label> <input type="text" name="cookies-lifetime" id="cookies-lifetime" value="<?php echo $easy_timer_options['cookies_lifetime']; ?>" size="4" /> <?php _e('days', 'easy-timer'); ?> <?php _e('<a href="http://www.kleor-editions.com/easy-timer/en/#part4.4">More informations</a>', 'easy-timer'); ?></p>
<p><input type="checkbox" name="javascript-enabled" id="javascript-enabled" value="yes"<?php if ($easy_timer_options['javascript_enabled'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="javascript-enabled"><?php _e('Add JavaScript code', 'easy-timer'); ?></label><br />
<em><?php _e('If you uncheck this box, Easy Timer will never add any JavaScript code to the pages of your website, but your count up/down timers will not refresh.', 'easy-timer'); ?> <?php _e('<a href="http://www.kleor-editions.com/easy-timer/en/#part7.2">More informations</a>', 'easy-timer'); ?></em></p>
<p class="submit"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes'); ?>" /></p>
</form>
</div>