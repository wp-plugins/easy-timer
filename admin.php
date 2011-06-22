<?php function easy_timer_options_page() { include 'options-page.php'; }

function easy_timer_admin_menu() {
add_options_page('Easy Timer', 'Easy Timer', 'manage_options', 'easy-timer', 'easy_timer_options_page'); }

add_action('admin_menu', 'easy_timer_admin_menu');


function easy_timer_action_links($links, $file) {
if ($file == 'easy-timer/easy-timer.php') {
return array_merge($links, array(
'<a href="options-general.php?page=easy-timer&amp;action=uninstall">'.__('Uninstall', 'easy-timer').'</a>',
'<a href="options-general.php?page=easy-timer">'.__('Options', 'easy-timer').'</a>')); }
return $links; }

add_filter('plugin_action_links', 'easy_timer_action_links', 10, 2);


function easy_timer_row_meta($links, $file) {
if ($file == 'easy-timer/easy-timer.php') {
return array_merge($links, array(
'<a href="http://www.kleor-editions.com/easy-timer">'.__('Documentation', 'easy-timer').'</a>')); }
return $links; }

add_filter('plugin_row_meta', 'easy_timer_row_meta', 10, 2);


function install_easy_timer() {
include 'initial-options.php';
$options = get_option('easy_timer');
foreach ($initial_options as $key => $value) {
if ($options[$key] == '') { $options[$key] = $value; } }
update_option('easy_timer', $options); }

register_activation_hook('easy-timer/easy-timer.php', 'install_easy_timer');