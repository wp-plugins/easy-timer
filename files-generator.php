<?php
/*
Plugin Name: Files Generator
Plugin URI: http://www.apprendre-memoriser.com
Description: Automatically generates two files for accounting.
Version: 100
Author: Kleor
Author URI: http://www.kleor.com
Text Domain: files-generator
*/


define('FILES_GENERATOR_PATH', plugin_dir_path(__FILE__));
define('FILES_GENERATOR_URL', plugin_dir_url(__FILE__));
define('FILES_GENERATOR_FOLDER', str_replace('/files-generator.php', '', plugin_basename(__FILE__)));
$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
define('FILES_GENERATOR_VERSION', $plugin_data['Version']);

if (is_admin()) { include_once FILES_GENERATOR_PATH.'admin.php'; }

function install_files_generator() { include FILES_GENERATOR_PATH.'includes/install.php'; }

register_activation_hook(__FILE__, 'install_files_generator');

$files_generator_options = (array) get_option('files_generator');
if ((!isset($files_generator_options['version'])) || ($files_generator_options['version'] != FILES_GENERATOR_VERSION)) { install_files_generator(); }


function files_generator_cron() {
date_default_timezone_set('UTC');
$current_timestamp = time();
$files_generator_options = (array) get_option('files_generator');
if (($current_timestamp - $files_generator_options['previous_cron_timestamp']) > 28800) {
$monthday = (int) date('j', $current_timestamp + 3600*UTC_OFFSET);
if (($monthday == 1) && (date('H', $current_timestamp + 3600*UTC_OFFSET) < 8)) {
$files_generator_options['previous_cron_timestamp'] = $current_timestamp;
update_option('files_generator', $files_generator_options);
$yesterday = date('Y-m-d', $current_timestamp + 3600*(UTC_OFFSET - 24));
$first_day = date('Y-m', $current_timestamp + 3600*(UTC_OFFSET - 24)).'-01';
remove_shortcode('start-date'); add_shortcode('start-date', create_function('', 'return "'.$first_day.'_00:00:00";'));
remove_shortcode('end-date'); add_shortcode('end-date', create_function('', 'return "'.$yesterday.'_23:59:59";'));
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) { $$field = files_generator_data('monthly_email_'.$field); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender.(((strstr($body, '</')) || (strstr($body, '/>'))) ? "\r\nContent-type: text/html" : "")); } } } }

foreach (array('admin_footer', 'login_footer', 'wp_footer') as $hook) { add_action($hook, 'files_generator_cron'); }


function files_generator_data($atts) { include FILES_GENERATOR_PATH.'includes/data.php'; return $data; }


function files_generator_do_shortcode($string) { include FILES_GENERATOR_PATH.'includes/do-shortcode.php'; return $string; }


function files_generator_filter_data($filter, $data) { include FILES_GENERATOR_PATH.'includes/filter-data.php'; return $data; }


function files_generator_format_nice_name($string) {
$string = files_generator_strip_accents(strtolower(trim(strip_tags($string))));
$string = str_replace(' ', '-', $string);
$string = preg_replace('/[^a-z0-9_-]/', '', $string);
return $string; }


function files_generator_i18n($string) {
load_plugin_textdomain('files-generator', false, FILES_GENERATOR_FOLDER.'/languages');
return __(__($string), 'files-generator'); }


function files_generator_strip_accents($string) {
return str_replace(
explode(' ', 'á à â ä ã å ç é è ê ë í ì î ï ñ ó ò ô ö õ ø ú ù û ü ý ÿ Á À Â Ä Ã Å Ç É È Ê Ë Í Ì Î Ï Ñ Ó Ò Ô Ö Õ Ø Ú Ù Û Ü Ý Ÿ'),
explode(' ', 'a a a a a a c e e e e i i i i n o o o o o o u u u u y y A A A A A A C E E E E I I I I N O O O O O O U U U U Y Y'),
$string); }


add_shortcode('files-generator', 'files_generator_data');


foreach (array(
'get_the_excerpt',
'get_the_title',
'single_post_title',
'the_excerpt',
'the_excerpt_rss',
'the_title',
'the_title_attribute',
'the_title_rss',
'widget_text',
'widget_title') as $function) { add_filter($function, 'do_shortcode'); }