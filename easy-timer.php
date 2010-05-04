<?php
/*
Plugin Name: Easy Timer
Plugin URI: http://www.kleor-editions.com/easy-timer
Description: Easily display a count down/up timer, the time or the current date on your website. Schedule an automatic content modification.
Version: 1.0
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


$wpurl = get_bloginfo('wpurl');
if ((substr($wpurl, -1) == '/')) { $wpurl = substr($wpurl, 0, -1); }
define('EASY_TIMER_URL', $wpurl.'/wp-content/plugins/easy-timer/');

$lang = substr(WPLANG, 0, 2); switch ($lang) {
case 'de': case 'en': case 'es': case 'fr': case 'it': case 'pt': break;
default: $lang = 'en'; }
define('EASY_TIMER_LANG', $lang);

load_plugin_textdomain('easy-timer', 'wp-content/plugins/easy-timer/languages', 'easy-timer/languages');


function timer($S, $T, $way, $content) {
$D = floor($S/86400);
$H = floor($S/3600);
$M = floor($S/60);
$h = $H - 24*$D;
$m = $M - 60*$H;
$s = $S - 60*$M;

$string0second = __('0 second', 'easy-timer');
$string1day = __('1 day', 'easy-timer');
$string1hour = __('1 hour', 'easy-timer');
$string1minute = __('1 minute', 'easy-timer');
$string1second = __('1 second', 'easy-timer');
$stringdays = __('days', 'easy-timer');
$stringhours = __('hours', 'easy-timer');
$stringminutes = __('minutes', 'easy-timer');
$stringseconds = __('seconds', 'easy-timer');

$stringS = $string0second; $stringh = ''; $stringm = ''; $strings = '';
if ($D == 1) { $stringD = $string1day; } elseif ($D > 1) { $stringD = $D.' '.$stringdays; }
if ($H == 1) { $stringH = $string1hour; } elseif ($H > 1) { $stringH = $H.' '.$stringhours; }
if ($M == 1) { $stringM = $string1minute; } elseif ($M > 1) { $stringM = $M.' '.$stringminutes; }
if ($S == 1) { $stringS = $string1second; } elseif ($S > 1) { $stringS = $S.' '.$stringseconds; }
if ($h == 1) { $stringh = ' '.$string1hour; } elseif ($h > 1) { $stringh = ' '.$h.' '.$stringhours; }
if ($m == 1) { $stringm = ' '.$string1minute; } elseif ($m > 1) { $stringm = ' '.$m.' '.$stringminutes; }
if ($s == 1) { $strings = ' '.$string1second; } elseif ($s > 1) { $strings = ' '.$s.' '.$stringseconds; }

if ($S >= 86400) {
$stringDhms = $stringD.$stringh.$stringm.$strings;
$stringDhm = $stringD.$stringh.$stringm;
$stringDh = $stringD.$stringh;
$stringHms = $stringH.$stringm.$strings;
$stringHm = $stringH.$stringm;
$stringMs = $stringM.$strings; }

if (($S >= 3600) && ($S < 86400)) {
$stringDhms = $stringH.$stringm.$strings;
$stringDhm = $stringH.$stringm;
$stringDh = $stringH;
$stringD = $stringH;
$stringHms = $stringH.$stringm.$strings;
$stringHm = $stringH.$stringm;
$stringMs = $stringM.$strings; }

if (($S >= 60) && ($S < 3600)) {
$stringDhms = $stringM.$strings;
$stringDhm = $stringM;
$stringDh = $stringM;
$stringD = $stringM;
$stringHms = $stringM.$strings;
$stringHm = $stringM;
$stringH = $stringM;
$stringMs = $stringM.$strings; }

if ($S < 60) {
$stringDhms = $stringS;
$stringDhm = $stringS;
$stringDh = $stringS;
$stringD = $stringS;
$stringHms = $stringS;
$stringHm = $stringS;
$stringH = $stringS;
$stringMs = $stringS;
$stringM = $stringS; }

$content = str_ireplace('[timer]', '<span class="dhmscount'.$way.'" title="'.$T.'">'.$stringDhms.'</span>', $content);
$content = str_ireplace('[dhmstimer]', '<span class="dhmscount'.$way.'" title="'.$T.'">'.$stringDhms.'</span>', $content);
$content = str_ireplace('[dhmtimer]', '<span class="dhmcount'.$way.'" title="'.$T.'">'.$stringDhm.'</span>', $content);
$content = str_ireplace('[dhtimer]', '<span class="dhcount'.$way.'" title="'.$T.'">'.$stringDh.'</span>', $content);
$content = str_ireplace('[dtimer]', '<span class="dcount'.$way.'" title="'.$T.'">'.$stringD.'</span>', $content);
$content = str_ireplace('[hmstimer]', '<span class="hmscount'.$way.'" title="'.$T.'">'.$stringHms.'</span>', $content);
$content = str_ireplace('[hmtimer]', '<span class="hmcount'.$way.'" title="'.$T.'">'.$stringHm.'</span>', $content);
$content = str_ireplace('[htimer]', '<span class="hcount'.$way.'" title="'.$T.'">'.$stringH.'</span>', $content);
$content = str_ireplace('[mstimer]', '<span class="mscount'.$way.'" title="'.$T.'">'.$stringMs.'</span>', $content);
$content = str_ireplace('[mtimer]', '<span class="mcount'.$way.'" title="'.$T.'">'.$stringM.'</span>', $content);
$content = str_ireplace('[stimer]', '<span class="scount'.$way.'" title="'.$T.'">'.$stringS.'</span>', $content);

return $content; }


function countdown($atts, $content) {
extract(shortcode_atts(array('date' => '', 'offset' => ''), $atts));
$date = preg_split('#[^0-9]#', $atts['date']);
date_default_timezone_set('UTC');

$n = count($date); switch ($n) {
case 0: case 1: $S = $date[0]; $T = time() + $S; break;
case 2: $S = 60*$date[0] + $date[1]; $T = time() + $S; break;
default:
$offset = strtolower($atts['offset']); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); $Soffset = 3600*$offset; break;
case 'local': $Soffset = 3600*get_option('gmt_offset'); break;
default: $offset = (round(100*str_replace(',', '.', $offset)))/100; $Soffset = 3600*$offset; }
$T = mktime($date[3], $date[4], $date[5], $date[1], $date[2], $date[0]) - $Soffset;
$S = $T - time(); }

$content = do_shortcode($content);
$content = explode('[after]', $content);

if ($S > 0) { return timer($S, $T, 'down', $content[0]); } else { return $content[1]; } }


function countup($atts, $content) {
extract(shortcode_atts(array('date' => '', 'offset' => ''), $atts));
$date = preg_split('#[^0-9]#', $atts['date']);
date_default_timezone_set('UTC');

$n = count($date); switch ($n) {
case 0: case 1: $S = $date[0]; $T = time() - $S; break;
case 2: $S = 60*$date[0] + $date[1]; $T = time() - $S; break;
default:
$offset = strtolower($atts['offset']); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); $Soffset = 3600*$offset; break;
case 'local': $Soffset = 3600*get_option('gmt_offset'); break;
default: $offset = (round(100*str_replace(',', '.', $offset)))/100; $Soffset = 3600*$offset; }
$T = mktime($date[3], $date[4], $date[5], $date[1], $date[2], $date[0]) - $Soffset;
$S = time() - $T; }

$content = do_shortcode($content);
$content = explode('[before]', $content);

if ($S >= 0) { return timer($S, $T, 'up', $content[0]); } else { return $content[1]; } }


function clock($atts) {
extract(shortcode_atts(array('form' => '', 'offset' => ''), $atts));
$offset = strtolower($atts['offset']); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); $Soffset = 3600*$offset; break;
case 'local': $Soffset = 3600*get_option('gmt_offset'); break;
default: $offset = (round(100*str_replace(',', '.', $offset)))/100; $Soffset = 3600*$offset; }
date_default_timezone_set('UTC');
$T = time() + $Soffset;

$form = strtolower($atts['form']); switch ($form) {
case 'hm': $clock = date('H:i', $T); break;
case 'hms': $clock = date('H:i:s', $T); break;
default: $form = 'hm'; $clock = date('H:i', $T); }

if (is_numeric($offset)) { return '<span class="'.$form.'clock" title="'.$offset.'">'.$clock.'</span>'; }
else { return '<span class="local'.$form.'clock">'.$clock.'</span>'; } }


function year($atts) {
extract(shortcode_atts(array('form' => '', 'offset' => ''), $atts));
$offset = strtolower($atts['offset']); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); $Soffset = 3600*$offset; break;
case 'local': $Soffset = 3600*get_option('gmt_offset'); break;
default: $offset = (round(100*str_replace(',', '.', $offset)))/100; $Soffset = 3600*$offset; }
date_default_timezone_set('UTC');
$T = time() + $Soffset;

$form = $atts['form']; switch ($form) {
case '2': $year = date('y', $T); break;
case '4': $year = date('Y', $T); break;
default: $form = '4'; $year = date('Y', $T); }

if (is_numeric($offset)) { return $year; }
else { return '<span class="local'.$form.'year">'.$year.'</span>'; } }


function isoyear($atts) {
extract(shortcode_atts(array('offset' => ''), $atts));
$offset = strtolower($atts['offset']); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); $Soffset = 3600*$offset; break;
case 'local': $Soffset = 3600*get_option('gmt_offset'); break;
default: $offset = (round(100*str_replace(',', '.', $offset)))/100; $Soffset = 3600*$offset; }
date_default_timezone_set('UTC');
$T = time() + $Soffset;
$isoyear = date('o', $T);

if (is_numeric($offset)) { return $isoyear; }
else { return '<span class="localisoyear">'.$isoyear.'</span>'; } }


function yearweek($atts) {
extract(shortcode_atts(array('offset' => ''), $atts));
$offset = strtolower($atts['offset']); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); $Soffset = 3600*$offset; break;
case 'local': $Soffset = 3600*get_option('gmt_offset'); break;
default: $offset = (round(100*str_replace(',', '.', $offset)))/100; $Soffset = 3600*$offset; }
date_default_timezone_set('UTC');
$T = time() + $Soffset;
$yearweek = date('W', $T);

if (is_numeric($offset)) { return $yearweek; }
else { return '<span class="localyearweek">'.$yearweek.'</span>'; } }


function yearday($atts) {
extract(shortcode_atts(array('offset' => ''), $atts));
$offset = strtolower($atts['offset']); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); $Soffset = 3600*$offset; break;
case 'local': $Soffset = 3600*get_option('gmt_offset'); break;
default: $offset = (round(100*str_replace(',', '.', $offset)))/100; $Soffset = 3600*$offset; }
date_default_timezone_set('UTC');
$T = time() + $Soffset;
$yearday = date('z', $T) + 1;

if (is_numeric($offset)) { return $yearday; }
else { return '<span class="localyearday">'.$yearday.'</span>'; } }


function month($atts) {
extract(shortcode_atts(array('form' => '', 'offset' => ''), $atts));
$offset = strtolower($atts['offset']); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); $Soffset = 3600*$offset; break;
case 'local': $Soffset = 3600*get_option('gmt_offset'); break;
default: $offset = (round(100*str_replace(',', '.', $offset)))/100; $Soffset = 3600*$offset; }
date_default_timezone_set('UTC');
$T = time() + $Soffset;
$n = date('n', $T);

$form = strtolower($atts['form']); switch ($form) {
case '1': $month = $n; break;
case '2': $month = date('m', $T); break;
case '': case 'lower': case 'upper': break;
default: $form = ''; }

if (($form == '') || ($form == 'lower') || ($form == 'upper')) {
$stringmonth = array(
0 => __('DECEMBER', 'easy-timer'),
1 => __('JANUARY', 'easy-timer'),
2 => __('FEBRUARY', 'easy-timer'),
3 => __('MARCH', 'easy-timer'),
4 => __('APRIL', 'easy-timer'),
5 => __('MAY', 'easy-timer'),
6 => __('JUNE', 'easy-timer'),
7 => __('JULY', 'easy-timer'),
8 => __('AUGUST', 'easy-timer'),
9 => __('SEPTEMBER', 'easy-timer'),
10 => __('OCTOBER', 'easy-timer'),
11 => __('NOVEMBER', 'easy-timer'),
12 => __('DECEMBER', 'easy-timer')); }

if ($form == '') { $month = ucfirst(strtolower($stringmonth[$n])); }
elseif ($form == 'lower') { $month = strtolower($stringmonth[$n]); }
elseif ($form == 'upper') { $month = $stringmonth[$n]; }

if (is_numeric($offset)) { return $month; }
else { return '<span class="local'.$form.'month">'.$month.'</span>'; } }


function monthday($atts) {
extract(shortcode_atts(array('form' => '', 'offset' => ''), $atts));
$offset = strtolower($atts['offset']); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); $Soffset = 3600*$offset; break;
case 'local': $Soffset = 3600*get_option('gmt_offset'); break;
default: $offset = (round(100*str_replace(',', '.', $offset)))/100; $Soffset = 3600*$offset; }
date_default_timezone_set('UTC');
$T = time() + $Soffset;

$form = $atts['form']; switch ($form) {
case '1': $monthday = date('j', $T); break;
case '2': $monthday = date('d', $T); break;
default: $form = '1'; $monthday = date('j', $T); }

if (is_numeric($offset)) { return $monthday; }
else { return '<span class="local'.$form.'monthday">'.$monthday.'</span>'; } }


function weekday($atts) {
extract(shortcode_atts(array('form' => '', 'offset' => ''), $atts));
$offset = strtolower($atts['offset']); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); $Soffset = 3600*$offset; break;
case 'local': $Soffset = 3600*get_option('gmt_offset'); break;
default: $offset = (round(100*str_replace(',', '.', $offset)))/100; $Soffset = 3600*$offset; }
date_default_timezone_set('UTC');
$T = time() + $Soffset;
$w = date('w', $T);

$form = strtolower($atts['form']); switch ($form) {
case '': case 'lower': case 'upper': break;
default: $form = ''; }

$stringweekday = array(
0 => __('SUNDAY', 'easy-timer'),
1 => __('MONDAY', 'easy-timer'),
2 => __('TUESDAY', 'easy-timer'),
3 => __('WEDNESDAY', 'easy-timer'),
4 => __('THURSDAY', 'easy-timer'),
5 => __('FRIDAY', 'easy-timer'),
6 => __('SATURDAY', 'easy-timer'),
7 => __('SUNDAY', 'easy-timer'));

if ($form == '') { $weekday = ucfirst(strtolower($stringweekday[$w])); }
elseif ($form == 'lower') { $weekday = strtolower($stringweekday[$w]); }
elseif ($form == 'upper') { $weekday = $stringweekday[$w]; }

if (is_numeric($offset)) { return $weekday; }
else { return '<span class="local'.$form.'weekday">'.$weekday.'</span>'; } }


function timezone($atts) {
extract(shortcode_atts(array('offset' => ''), $atts));
$offset = strtolower($atts['offset']); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); break;
case 'local': break;
default: $offset = (round(100*str_replace(',', '.', $offset)))/100; }

if (is_numeric($offset)) {
if ($offset == 0) { $timezone = 'UTC'; }
elseif ($offset > 0) { $timezone = 'UTC+'.$offset; }
elseif ($offset < 0) { $timezone = 'UTC'.$offset; }
return $timezone; }

else { return '<span class="localtimezone">UTC</span>'; } }


function easy_timer_js() { ?>
<script type="text/javascript" src="<?php echo EASY_TIMER_URL; ?>languages/<?php echo EASY_TIMER_LANG; ?>.js"></script>
<script type="text/javascript" src="<?php echo EASY_TIMER_URL; ?>easy-timer.js"></script><?php }


add_filter('widget_text', 'do_shortcode');
add_shortcode('countdown', 'countdown');
add_shortcode('countdown2', 'countdown');
add_shortcode('countdown3', 'countdown');
add_shortcode('countdown4', 'countdown');
add_shortcode('countdown5', 'countdown');
add_shortcode('countdown6', 'countdown');
add_shortcode('countdown7', 'countdown');
add_shortcode('countdown8', 'countdown');
add_shortcode('countdown9', 'countdown');
add_shortcode('countdown10', 'countdown');
add_shortcode('countup', 'countup');
add_shortcode('countup2', 'countup');
add_shortcode('countup3', 'countup');
add_shortcode('countup4', 'countup');
add_shortcode('countup5', 'countup');
add_shortcode('countup6', 'countup');
add_shortcode('countup7', 'countup');
add_shortcode('countup8', 'countup');
add_shortcode('countup9', 'countup');
add_shortcode('countup10', 'countup');
add_shortcode('clock', 'clock');
add_shortcode('year', 'year');
add_shortcode('isoyear', 'isoyear');
add_shortcode('yearweek', 'yearweek');
add_shortcode('yearday', 'yearday');
add_shortcode('month', 'month');
add_shortcode('monthday', 'monthday');
add_shortcode('weekday', 'weekday');
add_shortcode('timezone', 'timezone');
add_action('wp_footer', 'easy_timer_js');