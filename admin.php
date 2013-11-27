<?php if (((isset($_GET['page'])) && (strstr($_GET['page'], 'easy-timer'))) || (strstr($_SERVER['REQUEST_URI'], '/plugins.php'))) {
load_plugin_textdomain('easy-timer', false, 'easy-timer/languages'); }


function easy_timer_options_page() {
add_options_page('Easy Timer', 'Easy Timer', 'manage_options', 'easy-timer', create_function('', 'include_once EASY_TIMER_PATH."/options-page.php";')); }

add_action('admin_menu', 'easy_timer_options_page');


function easy_timer_meta_box($post) {
load_plugin_textdomain('easy-timer', false, 'easy-timer/languages');
$links = array(
'' => __('Documentation', 'easy-timer'),
'#countdown-timers' => __('Display a countdown timer', 'easy-timer'),
'#date' => __('Display the time or the date', 'easy-timer'),
'#screen-options-wrap' => __('Hide this box', 'easy-timer')); ?>
<p><a target="_blank" href="http://www.kleor.com/easy-timer/"><?php echo $links['']; ?></a>
 | <a style="color: #808080;" href="#screen-options-wrap" onclick="document.getElementById('show-settings-link').click(); document.getElementById('easy-timer-hide').click();"><?php echo $links['#screen-options-wrap']; ?></a></p>
<ul>
<?php foreach (array('', '#screen-options-wrap') as $url) { unset($links[$url]); }
foreach ($links as $url => $text) {
echo '<li><a target="_blank" href="http://www.kleor.com/easy-timer/'.$url.'">'.$text.'</a></li>'; } ?>
</ul>
<?php }

add_action('add_meta_boxes', create_function('', 'foreach (array("page", "post") as $type) {
add_meta_box("easy-timer", "Easy Timer", "easy_timer_meta_box", $type, "side"); }'));


function easy_timer_action_links($links, $file) {
if ($file == 'easy-timer/easy-timer.php') {
if (!is_multisite()) {
$links = array_merge($links, array(
'<a href="options-general.php?page=easy-timer&amp;action=uninstall">'.__('Uninstall', 'easy-timer').'</a>')); }
$links = array_merge($links, array(
'<a href="options-general.php?page=easy-timer&amp;action=reset">'.__('Reset', 'easy-timer').'</a>',
'<a href="options-general.php?page=easy-timer">'.__('Options', 'easy-timer').'</a>')); }
return $links; }

add_filter('plugin_action_links', 'easy_timer_action_links', 10, 2);


function easy_timer_row_meta($links, $file) {
if ($file == 'easy-timer/easy-timer.php') {
$links = array_merge($links, array(
'<a href="http://www.kleor.com/easy-timer">'.__('Documentation', 'easy-timer').'</a>')); }
return $links; }

add_filter('plugin_row_meta', 'easy_timer_row_meta', 10, 2);


function reset_easy_timer() {
load_plugin_textdomain('easy-timer', false, 'easy-timer/languages');
include EASY_TIMER_PATH.'/initial-options.php';
update_option('easy_timer', $initial_options); }