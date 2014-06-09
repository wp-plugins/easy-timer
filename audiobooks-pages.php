<?php
/*
Plugin Name: Audiobooks Pages
Plugin URI: http://www.abs-multimedias.com
Description: Allows you to manage your audiobooks pages.
Version: 100
Author: Kleor
Author URI: http://www.kleor.com
Text Domain: audiobooks-pages
*/


define('AUDIOBOOKS_PAGES_PATH', plugin_dir_path(__FILE__));
define('AUDIOBOOKS_PAGES_URL', plugin_dir_url(__FILE__));
define('AUDIOBOOKS_PAGES_FOLDER', str_replace('/audiobooks-pages.php', '', plugin_basename(__FILE__)));
$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
define('AUDIOBOOKS_PAGES_VERSION', $plugin_data['Version']);

if (is_admin()) { include_once AUDIOBOOKS_PAGES_PATH.'admin.php'; }

function install_audiobooks_pages() { include AUDIOBOOKS_PAGES_PATH.'includes/install.php'; }

register_activation_hook(__FILE__, 'install_audiobooks_pages');

$audiobooks_pages_options = (array) get_option('audiobooks_pages');
if ((!isset($audiobooks_pages_options['version'])) || ($audiobooks_pages_options['version'] != AUDIOBOOKS_PAGES_VERSION)) { install_audiobooks_pages(); }


function audiobooks_pages_data($atts) { include AUDIOBOOKS_PAGES_PATH.'includes/data.php'; return $data; }


function audiobooks_pages_do_shortcode($string) { include AUDIOBOOKS_PAGES_PATH.'includes/do-shortcode.php'; return $string; }


function audiobooks_pages_filter_data($filter, $data) { include AUDIOBOOKS_PAGES_PATH.'includes/filter-data.php'; return $data; }


function audiobooks_pages_format_nice_name($string) {
$string = audiobooks_pages_strip_accents(strtolower(trim(strip_tags($string))));
$string = str_replace(' ', '-', $string);
$string = preg_replace('/[^a-z0-9_-]/', '', $string);
return $string; }


function audiobooks_pages_i18n($string) {
load_plugin_textdomain('audiobooks-pages', false, AUDIOBOOKS_PAGES_FOLDER.'/languages');
return __(__($string), 'audiobooks-pages'); }


function audiobooks_pages_strip_accents($string) {
return str_replace(
explode(' ', 'á à â ä ã å ç é è ê ë í ì î ï ñ ó ò ô ö õ ø ú ù û ü ý ÿ Á À Â Ä Ã Å Ç É È Ê Ë Í Ì Î Ï Ñ Ó Ò Ô Ö Õ Ø Ú Ù Û Ü Ý Ÿ'),
explode(' ', 'a a a a a a c e e e e i i i i n o o o o o o u u u u y y A A A A A A C E E E E I I I I N O O O O O O U U U U Y Y'),
$string); }


foreach (array('audiobook-page-content', 'audiobook-product-selector', 'audiobook-store') as $tag) {
add_shortcode($tag, create_function('$atts', 'include_once AUDIOBOOKS_PAGES_PATH."shortcodes.php"; return '.str_replace('-', '_', $tag).'($atts);')); }
add_shortcode('audiobooks-pages', 'audiobooks_pages_data');


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