<?php
/*
Plugin Name: Audiobooks - Authors And Narrators
Plugin URI: http://www.abs-multimedias.com
Description: Allows you to create and manage your authors and narrators.
Version: 100
Author: Kleor
Author URI: http://www.kleor.com
Text Domain: audiobooks-authors-and-narrators
*/


if (!defined('HOME_URL')) { define('HOME_URL', get_option('home')); }
if (!defined('ROOT_URL')) { $url = explode('/', str_replace('//', '||', HOME_URL)); define('ROOT_URL', str_replace('||', '//', $url[0])); }
if (!defined('HOME_PATH')) { $path = str_replace(ROOT_URL, '', HOME_URL); define('HOME_PATH', ($path == '' ? '/' : $path)); }
if (!defined('UTC_OFFSET')) { define('UTC_OFFSET', get_option('gmt_offset')); }
define('AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH', plugin_dir_path(__FILE__));
define('AUDIOBOOKS_AUTHORS_AND_NARRATORS_URL', plugin_dir_url(__FILE__));
define('AUDIOBOOKS_AUTHORS_AND_NARRATORS_FOLDER', str_replace('/audiobooks-authors-and-narrators.php', '', plugin_basename(__FILE__)));
$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
define('AUDIOBOOKS_AUTHORS_AND_NARRATORS_VERSION', $plugin_data['Version']);

if (!function_exists('fix_url')) { include_once AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'libraries/formatting-functions.php'; }
if (is_admin()) { include_once AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'admin.php'; }

function install_audiobooks_authors_and_narrators() { include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'includes/install.php'; }

register_activation_hook(__FILE__, 'install_audiobooks_authors_and_narrators');

global $wpdb;
$audiobooks_authors_and_narrators_options = (array) get_option('audiobooks_authors_and_narrators');
if ((!isset($audiobooks_authors_and_narrators_options['version'])) || ($audiobooks_authors_and_narrators_options['version'] != AUDIOBOOKS_AUTHORS_AND_NARRATORS_VERSION)) { install_audiobooks_authors_and_narrators(); }

fix_url();


function audiobooks_authors_and_narrators_cron() { include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'includes/cron.php'; }

if ((!defined('AUDIOBOOKS_AUTHORS_AND_NARRATORS_DEMO')) || (AUDIOBOOKS_AUTHORS_AND_NARRATORS_DEMO == false)) {
foreach (array('admin_footer', 'login_footer', 'wp_footer') as $hook) { add_action($hook, 'audiobooks_authors_and_narrators_cron'); } }


function audiobooks_authors_and_narrators_data($atts) { include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'includes/data.php'; return $data; }


function audiobooks_authors_and_narrators_decimals_data($decimals, $data) { include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'includes/decimals-data.php'; return $data; }


function audiobooks_authors_and_narrators_do_shortcode($string) { include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'includes/do-shortcode.php'; return $string; }


function audiobooks_authors_and_narrators_excerpt($data, $length = 80) {
$data = (string) $data;
if (strlen($data) > $length) { $data = substr($data, 0, ($length - 4)).' […]'; }
return $data; }


function audiobooks_authors_and_narrators_filter_data($filter, $data) { include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'includes/filter-data.php'; return $data; }


function audiobooks_authors_and_narrators_format_data($field, $data) { include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'includes/format-data.php'; return $data; }


function audiobooks_authors_and_narrators_i18n($string) {
load_plugin_textdomain('audiobooks-authors-and-narrators', false, AUDIOBOOKS_AUTHORS_AND_NARRATORS_FOLDER.'/languages');
return __(__($string), 'audiobooks-authors-and-narrators'); }


function audiobooks_authors_and_narrators_item_data($type, $atts) { include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'includes/item-data.php'; return $data; }


function author_data($atts) {
return audiobooks_authors_and_narrators_item_data('author', $atts); }


function narrator_data($atts) {
return audiobooks_authors_and_narrators_item_data('narrator', $atts); }


function audiobooks_authors_and_narrators_shortcode_atts($default_values, $atts) { include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'includes/shortcode-atts.php'; return $atts; }


function audiobooks_authors_and_narrators_sql_array($table, $array) { include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'includes/sql-array.php'; return $sql; }


for ($i = 0; $i < 4; $i++) {
add_shortcode('audiobooks-authors-and-narrators-counter'.($i == 0 ? '' : $i), create_function('$atts, $content', 'include_once AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH."shortcodes.php"; return audiobooks_authors_and_narrators_counter($atts, $content);')); }

add_shortcode('audiobooks-authors-and-narrators', 'audiobooks_authors_and_narrators_data');
add_shortcode('author', 'author_data');
add_shortcode('narrator', 'narrator_data');


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