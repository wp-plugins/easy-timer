<?php if ((isset($_GET['action'])) && (($_GET['action'] == 'reset') || ($_GET['action'] == 'uninstall'))) {
$for = (((isset($_GET['for'])) && (is_multisite()) && (current_user_can('manage_network'))) ? $_GET['for'] : 'single');
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if ($_GET['action'] == 'reset') { reset_files_generator(); } else { uninstall_files_generator($for); } } ?>
<div class="wrap">
<h2><?php _e('Files Generator', 'files-generator'); ?></h2>
<?php if (isset($_POST['submit'])) {
echo '<div class="updated"><p><strong>'.($_GET['action'] == 'reset' ? __('Options reset.', 'files-generator') : __('Options deleted.', 'files-generator')).'</strong></p></div>
<script type="text/javascript">setTimeout(\'window.location = "'.($_GET['action'] == 'reset' ? 'options-general.php?page=files-generator' : ($for == 'network' ? 'network/' : '').'plugins.php').'"\', 2000);</script>'; } ?>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<p><strong style="color: #c00000;"><?php if ($_GET['action'] == 'reset') { _e('Do you really want to reset the options of Files Generator?', 'files-generator'); }
elseif ($for == 'network') { _e('Do you really want to permanently delete the options of Files Generator for all sites in this network?', 'files-generator'); }
else { _e('Do you really want to permanently delete the options of Files Generator?', 'files-generator'); } ?></strong> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'files-generator'); ?>" /></p>
</div>
</form><?php } ?>
</div><?php }

else {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = stripslashes(html_entity_decode(str_replace('&nbsp;', ' ', $value))); } }
$start_date = ($_POST['start_date'] != '' ? $_POST['start_date'] : $_POST['old_start_date']);
$end_date = ($_POST['end_date'] != '' ? $_POST['end_date'] : $_POST['old_end_date']);
if ($start_date == '') { $start_date = $options['start_date']; }
else {
$d = preg_split('#[^0-9]#', $start_date, 0, PREG_SPLIT_NO_EMPTY);
for ($i = 0; $i < 6; $i++) { $d[$i] = (int) (isset($d[$i]) ? $d[$i] : ($i < 3 ? 1 : 0)); }
$start_date = date('Y-m-d H:i:s', mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0])); }
if ($end_date == '') { $end_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET); }
else {
$d = preg_split('#[^0-9]#', $end_date, 0, PREG_SPLIT_NO_EMPTY);
for ($i = 0; $i < 6; $i++) { $d[$i] = (int) (isset($d[$i]) ? $d[$i] : ($i < 3 ? 1 : ($i == 3 ? 23 : 59))); }
$end_date = date('Y-m-d H:i:s', mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0])); }
$_POST['start_date'] = $start_date;
include FILES_GENERATOR_PATH.'initial-options.php';
foreach ($initial_options as $key => $value) { if (!isset($_POST[$key])) { $_POST[$key] = ''; } }
foreach (array('monthly_email_sent') as $field) { if ($_POST[$field] != 'yes') { $_POST[$field] = 'no'; } }
foreach ($initial_options as $key => $value) { if ($_POST[$key] == '') { $_POST[$key] = $value; } $options[$key] = $_POST[$key]; }
update_option('files_generator', $options); }
else {
$options = (array) get_option('files_generator');
date_default_timezone_set('UTC');
if (isset($_GET['start_date'])) { $start_date = $_GET['start_date']; }
else { $start_date = $options['start_date']; }
if (isset($_GET['end_date'])) { $end_date = $_GET['end_date']; }
else { $end_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET); } }

foreach ($options as $key => $value) {
if (is_string($value)) { $options[$key] = htmlspecialchars($value); } } ?>

<div class="wrap">
<h2><?php _e('Files Generator', 'files-generator'); ?></h2>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Settings saved.', 'files-generator').'</strong></p></div>'; } ?>
<h3><?php _e('Options', 'files-generator'); ?></h3>
<form method="post" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>

<?php files_generator_pages_date_picker($start_date, $end_date); ?>

<p><?php _e('Files:', 'files-generator'); ?> <a href="<?php echo FILES_GENERATOR_URL.'index.php?type=orders&amp;start_date='.$start_date.'&amp;end_date='.$end_date; ?>"><?php _e('Orders', 'files-generator'); ?></a>
<a href="<?php echo FILES_GENERATOR_URL.'index.php?type=recurring-payments&amp;start_date='.$start_date.'&amp;end_date='.$end_date; ?>"><?php _e('Recurring payments', 'files-generator'); ?></a><p>

<div class="postbox" id="monthly-email-module">
<h3 style="font-size: 1.25em;" id="monthly-email"><strong><?php _e('Monthly email', 'files-generator'); ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="monthly_email_sent" id="monthly_email_sent" value="yes"<?php if ($options['monthly_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> />
<?php _e('Send a monthly email', 'files-generator'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="monthly_email_sender"><?php _e('Sender', 'files-generator'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="monthly_email_sender" id="monthly_email_sender" rows="1" cols="75"><?php echo $options['monthly_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="monthly_email_receiver"><?php _e('Receiver', 'files-generator'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="monthly_email_receiver" id="monthly_email_receiver" rows="1" cols="75"><?php echo $options['monthly_email_receiver']; ?></textarea><br />
<span class="description"><?php _e('You can enter several email addresses. Separate them with commas.', 'files-generator'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="monthly_email_subject"><?php _e('Subject', 'files-generator'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="monthly_email_subject" id="monthly_email_subject" rows="1" cols="75"><?php echo $options['monthly_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="monthly_email_body"><?php _e('Body', 'files-generator'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="monthly_email_body" id="monthly_email_body" rows="15" cols="75"><?php echo htmlspecialchars($options['monthly_email_body']); ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update', 'files-generator'); ?>" /></td></tr>
</tbody></table>
</div></div>

<p class="submit"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes', 'files-generator'); ?>" /></p>
</form>
</div>
<?php }