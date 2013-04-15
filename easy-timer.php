<?php
/*
Plugin Name: Easy Timer
Plugin URI: http://www.kleor-editions.com/easy-timer
Description: Allows you to easily display a count down/up timer, the time or the current date on your website, and to schedule an automatic content modification.
Version: 3.2.3
Author: Kleor
Author URI: http://www.kleor-editions.com
Text Domain: easy-timer
License: GPL2
*/

/* 
Copyright 2010 Kleor Editions (http://www.kleor-editions.com)

This program is a free software. You can redistribute it and/or 
modify it under the terms of the GNU General Public License as 
published by the Free Software Foundation, either version 2 of 
the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, 
but without any warranty, without even the implied warranty of 
merchantability or fitness for a particular purpose. See the 
GNU General Public License for more details.
*/


define('EASY_TIMER_URL', plugin_dir_url(__FILE__));
$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
define('EASY_TIMER_VERSION', $plugin_data['Version']);

if (is_admin()) { include_once dirname(__FILE__).'/admin.php'; }

function install_easy_timer() { include dirname(__FILE__).'/install.php'; }

register_activation_hook(__FILE__, 'install_easy_timer');

$easy_timer_options = get_option('easy_timer');
if (((is_multisite()) || ($easy_timer_options)) && ((!isset($easy_timer_options['version']))
 || ($easy_timer_options['version'] != EASY_TIMER_VERSION))) { install_easy_timer(); }

$easy_timer_js_attribute = 'id'; $easy_timer_js_extension = '';
if (stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE')) { $easy_timer_js_attribute = 'title'; $easy_timer_js_extension = '-ie'; }
$easy_timer_cookies = array();


function easy_timer_cookies_js() {
global $easy_timer_cookies;
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$T = time();
$U = $T + 86400*easy_timer_data('cookies_lifetime');
$expiration_date = date('D', $U).', '.date('d', $U).' '.date('M', $U).' '.date('Y', $U).' '.date('H:i:s', $U).' UTC';
if (!empty($easy_timer_cookies)) { echo '<script type="text/javascript">'."\n"; }
foreach ($easy_timer_cookies as $id) { echo 'document.cookie="first-visit-'.$id.'='.$T.'; expires='.$expiration_date.'";'."\n"; }
if (!empty($easy_timer_cookies)) { echo '</script>'."\n"; } }

if (easy_timer_data('javascript_enabled') == 'yes') { add_action('wp_footer', 'easy_timer_cookies_js'); }


function easy_timer_data($atts) {
global $easy_timer_options;
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; }
else {
$field = (isset($atts[0]) ? $atts[0] : '');
$default = (isset($atts['default']) ? $atts['default'] : '');
$filter = (isset($atts['filter']) ? $atts['filter'] : ''); }
$field = str_replace('-', '_', easy_timer_format_nice_name($field));
if ($field == '') { $field = 'version'; }
$data = (isset($easy_timer_options[$field]) ? $easy_timer_options[$field] : '');
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = easy_timer_filter_data($filter, $data);
return $data; }


function easy_timer_filter_data($filter, $data) {
if (is_string($filter)) { $filter = preg_split('#[^a-zA-Z0-9_]#', str_replace('-', '_', $filter), 0, PREG_SPLIT_NO_EMPTY); }
if (is_array($filter)) { foreach ($filter as $function) { $data = easy_timer_string_map($function, $data); } }
return $data; }


function easy_timer_format_nice_name($string) {
$string = easy_timer_strip_accents(strtolower(trim(strip_tags($string))));
$string = str_replace(' ', '-', $string);
$string = preg_replace('/[^a-zA-Z0-9_-]/', '', $string);
return $string; }


function easy_timer_i18n($string) {
load_plugin_textdomain('easy-timer', false, 'easy-timer/languages');
return __(__($string), 'easy-timer'); }


function easy_timer_string_map($function, $string) {
if (!function_exists($function)) { $function = 'easy_timer_'.$function; }
if (function_exists($function)) { $array = array_map($function, array($string)); $string = $array[0]; }
return $string; }


function easy_timer_strip_accents($string) {
return str_replace(
explode(' ', 'á à â ä ã å ç é è ê ë í ì î ï ñ ó ò ô ö õ ø ú ù û ü ý ÿ Á À Â Ä Ã Å Ç É È Ê Ë Í Ì Î Ï Ñ Ó Ò Ô Ö Õ Ø Ú Ù Û Ü Ý Ÿ'),
explode(' ', 'a a a a a a c e e e e i i i i n o o o o o o u u u u y y A A A A A A C E E E E I I I I N O O O O O O U U U U Y Y'),
$string); }

for ($i = 0; $i < 4; $i++) {
foreach (array('counter', 'countdown', 'countup') as $tag) {
add_shortcode($tag.($i == 0 ? '' : $i), create_function('$atts, $content', 'include_once dirname(__FILE__)."/shortcodes.php"; return '.$tag.'($atts, $content);')); } }
foreach (array('clock', 'isoyear', 'monthday', 'month', 'timezone', 'weekday', 'yearday', 'yearweek', 'year') as $tag) {
add_shortcode($tag, create_function('$atts', 'include_once dirname(__FILE__)."/shortcodes.php"; return '.$tag.'($atts);')); }
add_shortcode('easy-timer', 'easy_timer_data');


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