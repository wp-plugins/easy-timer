<?php if (((isset($_GET['page'])) && (strstr($_GET['page'], 'files-generator'))) || (strstr($_SERVER['REQUEST_URI'], '/plugins.php'))) {
load_plugin_textdomain('files-generator', false, FILES_GENERATOR_FOLDER.'/languages'); }


function files_generator_options_page() {
add_options_page(__('Files Generator', 'files-generator'), __('Files Generator', 'files-generator'), 'manage_options', 'files-generator', create_function('', 'include_once FILES_GENERATOR_PATH."options-page.php";')); }

add_action('admin_menu', 'files_generator_options_page');


function files_generator_options_page_css() { ?>
<style type="text/css" media="all">
.wrap .description { color: #808080; }
.wrap h2 { float: left; }
.wrap input.button-secondary, .wrap select { vertical-align: 0; }
.wrap p.submit { margin: 0 20%; }
.wrap ul.subsubsub { margin: 1em 0 1.5em 6em; float: left; white-space: normal; }
</style>
<?php }

if ((isset($_GET['page'])) && (strstr($_GET['page'], 'files-generator'))) { add_action('admin_head', 'files_generator_options_page_css'); }


function files_generator_action_links($links) {
if (!is_network_admin()) {
$links = array_merge($links, array(
'<span class="delete"><a href="options-general.php?page=files-generator&amp;action=uninstall" title="'.__('Delete the options of Files Generator', 'files-generator').'">'.__('Uninstall', 'files-generator').'</a></span>',
'<span class="delete"><a href="options-general.php?page=files-generator&amp;action=reset" title="'.__('Reset the options of Files Generator', 'files-generator').'">'.__('Reset', 'files-generator').'</a></span>',
'<a href="options-general.php?page=files-generator">'.__('Options', 'files-generator').'</a>')); }
else {
$links = array_merge($links, array(
'<span class="delete"><a href="../options-general.php?page=files-generator&amp;action=uninstall&amp;for=network" title="'.__('Delete the options of Files Generator for all sites in this network', 'files-generator').'">'.__('Uninstall', 'files-generator').'</a></span>')); }
return $links; }

foreach (array('', 'network_admin_') as $prefix) { add_filter($prefix.'plugin_action_links_'.FILES_GENERATOR_FOLDER.'/files-generator.php', 'files_generator_action_links', 10, 2); }


function files_generator_pages_date_picker($start_date, $end_date) {
echo '<p style="margin: 0 0 1em 0; float: left;">
<input type="hidden" name="old_start_date" value="'.$start_date.'" /><label><strong>'.__('Start', 'files-generator').'</strong>
<input class="date-pick" style="margin: 0.5em;" type="text" name="start_date" id="start_date" size="20" value="'.$start_date.'" onchange="this.value = format_date(this.value, \'start\');" /></label>
<input type="hidden" name="old_end_date" value="'.$end_date.'" /><label style="margin-left: 3em;"><strong>'.__('End', 'files-generator').'</strong>
<input class="date-pick" style="margin: 0.5em;" type="text" name="end_date" id="end_date" size="20" value="'.$end_date.'" onchange="this.value = format_date(this.value, \'end\');" /></label>
<input style="margin-left: 3em; vertical-align: middle;" type="submit" class="button-secondary" name="submit" value="'.__('Display', 'files-generator').'" /></p>
<div class="clear"></div>

<script type="text/javascript">
function format_date(date, type) {
if (date.replace(/[^0-9]/g, "") == "") { date = ""; }
if (date == "") { if (type == "start") { date = "'.$start_date.'"; } else { date = "'.$end_date.'"; } }
else {
var d = date.split(/[^0-9]/g);
for (i = 0; i < 6; i++) {
if (i >= d.length) {
if (i < 3) { d[i] = 1; }
else {
if (type == "start") { d[i] = 0; }
else { if (i == 3) { d[i] = 23; } else { d[i] = 59; } } } }
d[i] = parseInt(d[i]); }
if (d[0] < 1000) { d[0] = d[0] + 2000; }
var time = new Date(d[0], d[1] - 1, d[2], d[3], d[4], d[5], 0).getTime();
d[0] = new Date(time).getFullYear(time);
d[1] = new Date(time).getMonth(time) + 1;
d[2] = new Date(time).getDate(time);
d[3] = new Date(time).getHours(time);
d[4] = new Date(time).getMinutes(time);
d[5] = new Date(time).getSeconds(time);
for (i = 0; i < 6; i++) { if (d[i] < 10) { d[i] = "0"+d[i]; } }
date = d[0]+"-"+d[1]+"-"+d[2]+" "+d[3]+":"+d[4]+":"+d[5]; }
return date; }
</script>'; }


function files_generator_date_picker_js() { ?>
<script type="text/javascript">
Date.dayNames = ['<?php _e('Sunday', 'files-generator'); ?>', '<?php _e('Monday', 'files-generator'); ?>', '<?php _e('Tuesday', 'files-generator'); ?>', '<?php _e('Wednesday', 'files-generator'); ?>', '<?php _e('Thursday', 'files-generator'); ?>', '<?php _e('Friday', 'files-generator'); ?>', '<?php _e('Saturday', 'files-generator'); ?>'];
Date.abbrDayNames = ['<?php _e('Sun', 'files-generator'); ?>', '<?php _e('Mon', 'files-generator'); ?>', '<?php _e('Tue', 'files-generator'); ?>', '<?php _e('Wed', 'files-generator'); ?>', '<?php _e('Thu', 'files-generator'); ?>', '<?php _e('Fri', 'files-generator'); ?>', '<?php _e('Sat', 'files-generator'); ?>'];
Date.monthNames = ['<?php _e('January', 'files-generator'); ?>', '<?php _e('February', 'files-generator'); ?>', '<?php _e('March', 'files-generator'); ?>', '<?php _e('April', 'files-generator'); ?>', '<?php _e('May', 'files-generator'); ?>', '<?php _e('June', 'files-generator'); ?>', '<?php _e('July', 'files-generator'); ?>', '<?php _e('August', 'files-generator'); ?>', '<?php _e('September', 'files-generator'); ?>', '<?php _e('October', 'files-generator'); ?>', '<?php _e('November', 'files-generator'); ?>', '<?php _e('December', 'files-generator'); ?>'];
Date.abbrMonthNames = ['<?php _e('Jan', 'files-generator'); ?>', '<?php _e('Feb', 'files-generator'); ?>', '<?php _e('Mar', 'files-generator'); ?>', '<?php _e('Apr', 'files-generator'); ?>', '<?php _e('May', 'files-generator'); ?>', '<?php _e('Jun', 'files-generator'); ?>', '<?php _e('Jul', 'files-generator'); ?>', '<?php _e('Aug', 'files-generator'); ?>', '<?php _e('Sep', 'files-generator'); ?>', '<?php _e('Oct', 'files-generator'); ?>', '<?php _e('Nov', 'files-generator'); ?>', '<?php _e('Dec', 'files-generator'); ?>'];
jQuery.dpText = {
TEXT_PREV_YEAR : '<?php _e('Previous year', 'files-generator'); ?>',
TEXT_PREV_MONTH : '<?php _e('Previous month', 'files-generator'); ?>',
TEXT_NEXT_YEAR : '<?php _e('Next year', 'files-generator'); ?>',
TEXT_NEXT_MONTH : '<?php _e('Next month', 'files-generator'); ?>',
TEXT_CLOSE : '<?php _e('Close', 'files-generator'); ?>',
TEXT_CHOOSE_DATE : '<?php _e('Choose a date', 'files-generator'); ?>',
DATE_PICKER_ALT : '<?php _e('Date', 'files-generator'); ?>',
DATE_PICKER_URL : '<?php echo FILES_GENERATOR_URL; ?>images/date-picker.png',
HEADER_FORMAT : 'mmmm yyyy'};
jQuery(function(){ jQuery('.date-pick').datePicker({startDate:'2000-01-01'}); });
</script>
<?php }


function reset_files_generator() {
load_plugin_textdomain('files-generator', false, FILES_GENERATOR_FOLDER.'/languages');
include FILES_GENERATOR_PATH.'initial-options.php';
update_option('files_generator', $initial_options); }


function uninstall_files_generator($for = 'single') { include FILES_GENERATOR_PATH.'includes/uninstall.php'; }


if ((isset($_GET['page'])) && ($_GET['page'] == 'files-generator')
 && ((!isset($_GET['action'])) || (!in_array($_GET['action'], array('delete', 'uninstall', 'reset'))))) {
wp_enqueue_script('jquery');
wp_enqueue_style('files-generator-date-picker', FILES_GENERATOR_URL.'libraries/date-picker.css', array(), FILES_GENERATOR_VERSION, 'all');
wp_enqueue_script('files-generator-date-picker', FILES_GENERATOR_URL.'libraries/date-picker.js', array('jquery'), FILES_GENERATOR_VERSION, true);
add_action('admin_footer', 'files_generator_date_picker_js'); }