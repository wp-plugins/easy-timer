<?php if (((isset($_GET['page'])) && (strstr($_GET['page'], 'easy-timer'))) || (strstr($_SERVER['REQUEST_URI'], '/plugins.php'))) {
load_plugin_textdomain('easy-timer', false, 'easy-timer/languages'); }


function easy_timer_options_page() {
add_options_page('Easy Timer', 'Easy Timer', 'manage_options', 'easy-timer', create_function('', 'include "options-page.php";')); }

add_action('admin_menu', 'easy_timer_options_page');


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
'<a href="http://www.kleor-editions.com/easy-timer">'.__('Documentation', 'easy-timer').'</a>')); }
return $links; }

add_filter('plugin_row_meta', 'easy_timer_row_meta', 10, 2);


function reset_easy_timer() {
load_plugin_textdomain('easy-timer', false, 'easy-timer/languages');
include EASY_TIMER_PATH.'/initial-options.php';
update_option('easy_timer', $initial_options); }