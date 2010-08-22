<?php
/*
Plugin Name: Easy Timer
Plugin URI: http://www.kleor-editions.com/easy-timer
Description: Easily display a count down/up timer, the time or the current date on your website. Schedule an automatic content modification.
Version: 1.8
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


load_plugin_textdomain('easy-timer', 'wp-content/plugins/easy-timer/languages', 'easy-timer/languages');

$wpurl = get_bloginfo('wpurl');
if ((substr($wpurl, -1) == '/')) { $wpurl = substr($wpurl, 0, -1); }
define('EASY_TIMER_URL', $wpurl.'/wp-content/plugins/easy-timer/');

$lang = substr(WPLANG, 0, 2); switch ($lang) {
case 'de': case 'en': case 'es': case 'fr': case 'it': case 'pl': case 'pt': break;
default: $lang = 'en'; }
define('EASY_TIMER_LANG', $lang);

$easy_timer_options = get_option('easy_timer');
define('EASY_TIMER_OPTIONS_DEFAULT_TIMER_PREFIX', $easy_timer_options['default_timer_prefix']);
define('EASY_TIMER_OPTIONS_JAVASCRIPT_ENABLED', $easy_timer_options['javascript_enabled']);


function timer($S) {
if ($S < 0) { $S = 0; }
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

$timer = array(
'Dhms' => $stringDhms,
'Dhm' => $stringDhm,
'Dh' => $stringDh,
'D' => $stringD,
'Hms' => $stringHms,
'Hm' => $stringHm,
'H' => $stringH,
'Ms' => $stringMs,
'M' => $stringM,
'S' => $stringS);

return $timer; }


function timer_replace($S, $T, $way, $content) {
$timer = timer($S);

$content = str_ireplace('[timer]', '['.EASY_TIMER_OPTIONS_DEFAULT_TIMER_PREFIX.'timer]', $content);
$content = str_ireplace('[dhmstimer]', '<span class="dhmscount'.$way.'" title="'.$T.'">'.$timer['Dhms'].'</span>', $content);
$content = str_ireplace('[dhmtimer]', '<span class="dhmcount'.$way.'" title="'.$T.'">'.$timer['Dhm'].'</span>', $content);
$content = str_ireplace('[dhtimer]', '<span class="dhcount'.$way.'" title="'.$T.'">'.$timer['Dh'].'</span>', $content);
$content = str_ireplace('[dtimer]', '<span class="dcount'.$way.'" title="'.$T.'">'.$timer['D'].'</span>', $content);
$content = str_ireplace('[hmstimer]', '<span class="hmscount'.$way.'" title="'.$T.'">'.$timer['Hms'].'</span>', $content);
$content = str_ireplace('[hmtimer]', '<span class="hmcount'.$way.'" title="'.$T.'">'.$timer['Hm'].'</span>', $content);
$content = str_ireplace('[htimer]', '<span class="hcount'.$way.'" title="'.$T.'">'.$timer['H'].'</span>', $content);
$content = str_ireplace('[mstimer]', '<span class="mscount'.$way.'" title="'.$T.'">'.$timer['Ms'].'</span>', $content);
$content = str_ireplace('[mtimer]', '<span class="mcount'.$way.'" title="'.$T.'">'.$timer['M'].'</span>', $content);
$content = str_ireplace('[stimer]', '<span class="scount'.$way.'" title="'.$T.'">'.$timer['S'].'</span>', $content);

return $content; }


function total_timer_replace($S, $T, $way, $content) {
$timer = timer($S);

$content = str_ireplace('[total-timer]', '[total-'.EASY_TIMER_OPTIONS_DEFAULT_TIMER_PREFIX.'timer]', $content);
$content = str_ireplace('[total-dhmstimer]', '<span class="dhmscount'.$way.'" title="'.$T.'">'.$timer['Dhms'].'</span>', $content);
$content = str_ireplace('[total-dhmtimer]', '<span class="dhmcount'.$way.'" title="'.$T.'">'.$timer['Dhm'].'</span>', $content);
$content = str_ireplace('[total-dhtimer]', '<span class="dhcount'.$way.'" title="'.$T.'">'.$timer['Dh'].'</span>', $content);
$content = str_ireplace('[total-dtimer]', '<span class="dcount'.$way.'" title="'.$T.'">'.$timer['D'].'</span>', $content);
$content = str_ireplace('[total-hmstimer]', '<span class="hmscount'.$way.'" title="'.$T.'">'.$timer['Hms'].'</span>', $content);
$content = str_ireplace('[total-hmtimer]', '<span class="hmcount'.$way.'" title="'.$T.'">'.$timer['Hm'].'</span>', $content);
$content = str_ireplace('[total-htimer]', '<span class="hcount'.$way.'" title="'.$T.'">'.$timer['H'].'</span>', $content);
$content = str_ireplace('[total-mstimer]', '<span class="mscount'.$way.'" title="'.$T.'">'.$timer['Ms'].'</span>', $content);
$content = str_ireplace('[total-mtimer]', '<span class="mcount'.$way.'" title="'.$T.'">'.$timer['M'].'</span>', $content);
$content = str_ireplace('[total-stimer]', '<span class="scount'.$way.'" title="'.$T.'">'.$timer['S'].'</span>', $content);

return $content; }


function elapsed_timer_replace($S, $T, $content) {
$timer = timer($S);

$content = str_ireplace('[elapsed-timer]', '[elapsed-'.EASY_TIMER_OPTIONS_DEFAULT_TIMER_PREFIX.'timer]', $content);
$content = str_ireplace('[elapsed-dhmstimer]', '<span class="dhmscountup" title="'.$T.'">'.$timer['Dhms'].'</span>', $content);
$content = str_ireplace('[elapsed-dhmtimer]', '<span class="dhmcountup" title="'.$T.'">'.$timer['Dhm'].'</span>', $content);
$content = str_ireplace('[elapsed-dhtimer]', '<span class="dhcountup" title="'.$T.'">'.$timer['Dh'].'</span>', $content);
$content = str_ireplace('[elapsed-dtimer]', '<span class="dcountup" title="'.$T.'">'.$timer['D'].'</span>', $content);
$content = str_ireplace('[elapsed-hmstimer]', '<span class="hmscountup" title="'.$T.'">'.$timer['Hms'].'</span>', $content);
$content = str_ireplace('[elapsed-hmtimer]', '<span class="hmcountup" title="'.$T.'">'.$timer['Hm'].'</span>', $content);
$content = str_ireplace('[elapsed-htimer]', '<span class="hcountup" title="'.$T.'">'.$timer['H'].'</span>', $content);
$content = str_ireplace('[elapsed-mstimer]', '<span class="mscountup" title="'.$T.'">'.$timer['Ms'].'</span>', $content);
$content = str_ireplace('[elapsed-mtimer]', '<span class="mcountup" title="'.$T.'">'.$timer['M'].'</span>', $content);
$content = str_ireplace('[elapsed-stimer]', '<span class="scountup" title="'.$T.'">'.$timer['S'].'</span>', $content);

return $content; }


function total_elapsed_timer_replace($S, $T, $content) {
$timer = timer($S);

$content = str_ireplace('[total-elapsed-timer]', '[total-elapsed-'.EASY_TIMER_OPTIONS_DEFAULT_TIMER_PREFIX.'timer]', $content);
$content = str_ireplace('[total-elapsed-dhmstimer]', '<span class="dhmscountup" title="'.$T.'">'.$timer['Dhms'].'</span>', $content);
$content = str_ireplace('[total-elapsed-dhmtimer]', '<span class="dhmcountup" title="'.$T.'">'.$timer['Dhm'].'</span>', $content);
$content = str_ireplace('[total-elapsed-dhtimer]', '<span class="dhcountup" title="'.$T.'">'.$timer['Dh'].'</span>', $content);
$content = str_ireplace('[total-elapsed-dtimer]', '<span class="dcountup" title="'.$T.'">'.$timer['D'].'</span>', $content);
$content = str_ireplace('[total-elapsed-hmstimer]', '<span class="hmscountup" title="'.$T.'">'.$timer['Hms'].'</span>', $content);
$content = str_ireplace('[total-elapsed-hmtimer]', '<span class="hmcountup" title="'.$T.'">'.$timer['Hm'].'</span>', $content);
$content = str_ireplace('[total-elapsed-htimer]', '<span class="hcountup" title="'.$T.'">'.$timer['H'].'</span>', $content);
$content = str_ireplace('[total-elapsed-mstimer]', '<span class="mscountup" title="'.$T.'">'.$timer['Ms'].'</span>', $content);
$content = str_ireplace('[total-elapsed-mtimer]', '<span class="mcountup" title="'.$T.'">'.$timer['M'].'</span>', $content);
$content = str_ireplace('[total-elapsed-stimer]', '<span class="scountup" title="'.$T.'">'.$timer['S'].'</span>', $content);

return $content; }


function remaining_timer_replace($S, $T, $content) {
$timer = timer($S);

$content = str_ireplace('[remaining-timer]', '[remaining-'.EASY_TIMER_OPTIONS_DEFAULT_TIMER_PREFIX.'timer]', $content);
$content = str_ireplace('[remaining-dhmstimer]', '<span class="dhmscountdown" title="'.$T.'">'.$timer['Dhms'].'</span>', $content);
$content = str_ireplace('[remaining-dhmtimer]', '<span class="dhmcountdown" title="'.$T.'">'.$timer['Dhm'].'</span>', $content);
$content = str_ireplace('[remaining-dhtimer]', '<span class="dhcountdown" title="'.$T.'">'.$timer['Dh'].'</span>', $content);
$content = str_ireplace('[remaining-dtimer]', '<span class="dcountdown" title="'.$T.'">'.$timer['D'].'</span>', $content);
$content = str_ireplace('[remaining-hmstimer]', '<span class="hmscountdown" title="'.$T.'">'.$timer['Hms'].'</span>', $content);
$content = str_ireplace('[remaining-hmtimer]', '<span class="hmcountdown" title="'.$T.'">'.$timer['Hm'].'</span>', $content);
$content = str_ireplace('[remaining-htimer]', '<span class="hcountdown" title="'.$T.'">'.$timer['H'].'</span>', $content);
$content = str_ireplace('[remaining-mstimer]', '<span class="mscountdown" title="'.$T.'">'.$timer['Ms'].'</span>', $content);
$content = str_ireplace('[remaining-mtimer]', '<span class="mcountdown" title="'.$T.'">'.$timer['M'].'</span>', $content);
$content = str_ireplace('[remaining-stimer]', '<span class="scountdown" title="'.$T.'">'.$timer['S'].'</span>', $content);

return $content; }


function total_remaining_timer_replace($S, $T, $content) {
$timer = timer($S);

$content = str_ireplace('[total-remaining-timer]', '[total-remaining-'.EASY_TIMER_OPTIONS_DEFAULT_TIMER_PREFIX.'timer]', $content);
$content = str_ireplace('[total-remaining-dhmstimer]', '<span class="dhmscountdown" title="'.$T.'">'.$timer['Dhms'].'</span>', $content);
$content = str_ireplace('[total-remaining-dhmtimer]', '<span class="dhmcountdown" title="'.$T.'">'.$timer['Dhm'].'</span>', $content);
$content = str_ireplace('[total-remaining-dhtimer]', '<span class="dhcountdown" title="'.$T.'">'.$timer['Dh'].'</span>', $content);
$content = str_ireplace('[total-remaining-dtimer]', '<span class="dcountdown" title="'.$T.'">'.$timer['D'].'</span>', $content);
$content = str_ireplace('[total-remaining-hmstimer]', '<span class="hmscountdown" title="'.$T.'">'.$timer['Hms'].'</span>', $content);
$content = str_ireplace('[total-remaining-hmtimer]', '<span class="hmcountdown" title="'.$T.'">'.$timer['Hm'].'</span>', $content);
$content = str_ireplace('[total-remaining-htimer]', '<span class="hcountdown" title="'.$T.'">'.$timer['H'].'</span>', $content);
$content = str_ireplace('[total-remaining-mstimer]', '<span class="mscountdown" title="'.$T.'">'.$timer['Ms'].'</span>', $content);
$content = str_ireplace('[total-remaining-mtimer]', '<span class="mcountdown" title="'.$T.'">'.$timer['M'].'</span>', $content);
$content = str_ireplace('[total-remaining-stimer]', '<span class="scountdown" title="'.$T.'">'.$timer['S'].'</span>', $content);

return $content; }


function counter($atts, $content) {
date_default_timezone_set('UTC');
extract(shortcode_atts(array('date' => '', 'offset' => '', 'way' => '', 'delimiter' => ''), $atts));
if ($atts['way'] == 'down') { $way = 'down'; } else { $way = 'up'; }
if ($atts['delimiter'] == 'before') { $delimiter = '[before]'; } else { $delimiter = '[after]'; }

$offset = strtolower($atts['offset']); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); $Soffset = 3600*$offset; break;
case 'local': $Soffset = 3600*get_option('gmt_offset'); break;
default: $offset = (round(100*str_replace(',', '.', $offset)))/100; $Soffset = 3600*$offset; }

$date = $atts['date'];
if ($delimiter == '[after]') { $date = '0//'.$date; }
if ($delimiter == '[before]') { $date = $date.'//0'; }
$date = explode('//', $date);
if ($delimiter == '[before]') { $date = array_reverse($date); }
$n = count($date);

$time = time();
$S = array(0); $T = array($time);
for ($i = 1; $i < $n; $i++) {
$date[$i] = preg_split('#[^0-9]#', $date[$i]);
$m = count($date[$i]); 
for ($j = 0; $j < $m; $j++) { $date[$i][$j] = (int) $date[$i][$j]; }
switch ($m) {
case 0: case 1: $S[$i] = $date[$i][0]; $T[$i] = $time - $S[$i]; break;
case 2: $S[$i] = 60*$date[$i][0] + $date[$i][1]; $T[$i] = $time - $S[$i]; break;
default:
$T[$i] = mktime($date[$i][3], $date[$i][4], $date[$i][5], $date[$i][1], $date[$i][2], $date[$i][0]) - $Soffset;
$S[$i] = $time - $T[$i]; } }

$i = 0; while (($i < $n) && ($S[$i] >= 0)) { $k = $i; $i = $i + 1; }
if ($i == $n) { $i = $n - 1; }

$content = do_shortcode($content);
if (strstr($content, $delimiter) == false) { $content = $content.$delimiter; }
$content = explode($delimiter, $content);
if ($delimiter == '[before]') { $content = array_reverse($content); }

if ((EASY_TIMER_OPTIONS_JAVASCRIPT_ENABLED == 'yes') && (stristr($content[$k], 'timer]') == true)) {
add_action('wp_footer', 'easy_timer_lang_js');
add_action('wp_footer', 'easy_timer_js'); }

if ($way == 'up') {
$content[$k] = timer_replace($S[$k], $T[$k], 'up', $content[$k]);
$content[$k] = total_timer_replace($S[1], $T[1], 'up', $content[$k]); }
if ($way == 'down') {
$content[$k] = timer_replace(-$S[$i], $T[$i], 'down', $content[$k]);
$content[$k] = total_timer_replace(-$S[$n - 1], $T[$n - 1], 'down', $content[$k]); }

$content[$k] = elapsed_timer_replace($S[$k], $T[$k], $content[$k]);
$content[$k] = total_elapsed_timer_replace($S[1], $T[1], $content[$k]);
$content[$k] = remaining_timer_replace(-$S[$i], $T[$i], $content[$k]);
$content[$k] = total_remaining_timer_replace(-$S[$n - 1], $T[$n - 1], $content[$k]);

return $content[$k]; }


function countdown($atts, $content) {
extract(shortcode_atts(array('date' => '', 'offset' => '', 'way' => '', 'delimiter' => ''), $atts));
if ($atts['way'] != 'up') { $atts['way'] = 'down'; }
if ($atts['delimiter'] != 'before') { $atts['delimiter'] = 'after'; }
return counter($atts, $content); }


function countup($atts, $content) {
extract(shortcode_atts(array('date' => '', 'offset' => '', 'way' => '', 'delimiter' => ''), $atts));
if ($atts['way'] != 'down') { $atts['way'] = 'up'; }
if ($atts['delimiter'] != 'after') { $atts['delimiter'] = 'before'; }
return counter($atts, $content); }


function clock($atts) {
date_default_timezone_set('UTC');
if (EASY_TIMER_OPTIONS_JAVASCRIPT_ENABLED == 'yes') { add_action('wp_footer', 'easy_timer_js'); }
extract(shortcode_atts(array('form' => '', 'offset' => ''), $atts));
$offset = strtolower($atts['offset']); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); $Soffset = 3600*$offset; break;
case 'local': $Soffset = 3600*get_option('gmt_offset'); break;
default: $offset = (round(100*str_replace(',', '.', $offset)))/100; $Soffset = 3600*$offset; }
$T = time() + $Soffset;

$form = strtolower($atts['form']); switch ($form) {
case 'hm': $clock = date('H:i', $T); break;
case 'hms': $clock = date('H:i:s', $T); break;
default: $form = 'hm'; $clock = date('H:i', $T); }

if (is_numeric($offset)) { return '<span class="'.$form.'clock" title="'.$offset.'">'.$clock.'</span>'; }
else { return '<span class="local'.$form.'clock">'.$clock.'</span>'; } }


function year($atts) {
date_default_timezone_set('UTC');
extract(shortcode_atts(array('form' => '', 'offset' => ''), $atts));
$offset = strtolower($atts['offset']); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); $Soffset = 3600*$offset; break;
case 'local': $Soffset = 3600*get_option('gmt_offset'); break;
default: $offset = (round(100*str_replace(',', '.', $offset)))/100; $Soffset = 3600*$offset; }
$T = time() + $Soffset;

$form = $atts['form']; switch ($form) {
case '2': $year = date('y', $T); break;
case '4': $year = date('Y', $T); break;
default: $form = '4'; $year = date('Y', $T); }

if (is_numeric($offset)) { return $year; }

else {
if (EASY_TIMER_OPTIONS_JAVASCRIPT_ENABLED == 'yes') { add_action('wp_footer', 'easy_timer_js'); }
return '<span class="local'.$form.'year">'.$year.'</span>'; } }


function isoyear($atts) {
date_default_timezone_set('UTC');
extract(shortcode_atts(array('offset' => ''), $atts));
$offset = strtolower($atts['offset']); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); $Soffset = 3600*$offset; break;
case 'local': $Soffset = 3600*get_option('gmt_offset'); break;
default: $offset = (round(100*str_replace(',', '.', $offset)))/100; $Soffset = 3600*$offset; }
$T = time() + $Soffset;
$isoyear = date('o', $T);

if (is_numeric($offset)) { return $isoyear; }

else {
if (EASY_TIMER_OPTIONS_JAVASCRIPT_ENABLED == 'yes') { add_action('wp_footer', 'easy_timer_js'); }
return '<span class="localisoyear">'.$isoyear.'</span>'; } }


function yearweek($atts) {
date_default_timezone_set('UTC');
extract(shortcode_atts(array('offset' => ''), $atts));
$offset = strtolower($atts['offset']); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); $Soffset = 3600*$offset; break;
case 'local': $Soffset = 3600*get_option('gmt_offset'); break;
default: $offset = (round(100*str_replace(',', '.', $offset)))/100; $Soffset = 3600*$offset; }
$T = time() + $Soffset;
$yearweek = date('W', $T);

if (is_numeric($offset)) { return $yearweek; }

else {
if (EASY_TIMER_OPTIONS_JAVASCRIPT_ENABLED == 'yes') { add_action('wp_footer', 'easy_timer_js'); }
return '<span class="localyearweek">'.$yearweek.'</span>'; } }


function yearday($atts) {
date_default_timezone_set('UTC');
extract(shortcode_atts(array('offset' => ''), $atts));
$offset = strtolower($atts['offset']); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); $Soffset = 3600*$offset; break;
case 'local': $Soffset = 3600*get_option('gmt_offset'); break;
default: $offset = (round(100*str_replace(',', '.', $offset)))/100; $Soffset = 3600*$offset; }
$T = time() + $Soffset;
$yearday = date('z', $T) + 1;

if (is_numeric($offset)) { return $yearday; }

else {
if (EASY_TIMER_OPTIONS_JAVASCRIPT_ENABLED == 'yes') { add_action('wp_footer', 'easy_timer_js'); }
return '<span class="localyearday">'.$yearday.'</span>'; } }


function month($atts) {
date_default_timezone_set('UTC');
extract(shortcode_atts(array('form' => '', 'offset' => ''), $atts));
$offset = strtolower($atts['offset']); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); $Soffset = 3600*$offset; break;
case 'local': $Soffset = 3600*get_option('gmt_offset'); break;
default: $offset = (round(100*str_replace(',', '.', $offset)))/100; $Soffset = 3600*$offset; }
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

else {
if (EASY_TIMER_OPTIONS_JAVASCRIPT_ENABLED == 'yes') {
if (($form == '') || ($form == 'lower') || ($form == 'upper')) { add_action('wp_footer', 'easy_timer_lang_js'); }
add_action('wp_footer', 'easy_timer_js'); }
return '<span class="local'.$form.'month">'.$month.'</span>'; } }


function monthday($atts) {
date_default_timezone_set('UTC');
extract(shortcode_atts(array('form' => '', 'offset' => ''), $atts));
$offset = strtolower($atts['offset']); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); $Soffset = 3600*$offset; break;
case 'local': $Soffset = 3600*get_option('gmt_offset'); break;
default: $offset = (round(100*str_replace(',', '.', $offset)))/100; $Soffset = 3600*$offset; }
$T = time() + $Soffset;

$form = $atts['form']; switch ($form) {
case '1': $monthday = date('j', $T); break;
case '2': $monthday = date('d', $T); break;
default: $form = '1'; $monthday = date('j', $T); }

if (is_numeric($offset)) { return $monthday; }

else {
if (EASY_TIMER_OPTIONS_JAVASCRIPT_ENABLED == 'yes') { add_action('wp_footer', 'easy_timer_js'); }
return '<span class="local'.$form.'monthday">'.$monthday.'</span>'; } }


function weekday($atts) {
date_default_timezone_set('UTC');
extract(shortcode_atts(array('form' => '', 'offset' => ''), $atts));
$offset = strtolower($atts['offset']); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); $Soffset = 3600*$offset; break;
case 'local': $Soffset = 3600*get_option('gmt_offset'); break;
default: $offset = (round(100*str_replace(',', '.', $offset)))/100; $Soffset = 3600*$offset; }
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

else {
if (EASY_TIMER_OPTIONS_JAVASCRIPT_ENABLED == 'yes') {
add_action('wp_footer', 'easy_timer_lang_js');
add_action('wp_footer', 'easy_timer_js'); }
return '<span class="local'.$form.'weekday">'.$weekday.'</span>'; } }


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

else {
if (EASY_TIMER_OPTIONS_JAVASCRIPT_ENABLED == 'yes') { add_action('wp_footer', 'easy_timer_js'); }
return '<span class="localtimezone">UTC</span>'; } }


function easy_timer_js() { ?>
<script type="text/javascript" src="<?php echo EASY_TIMER_URL; ?>easy-timer.js"></script>
<?php }


function easy_timer_lang_js() { ?>
<script type="text/javascript" src="<?php echo EASY_TIMER_URL; ?>languages/<?php echo EASY_TIMER_LANG; ?>.js"></script>
<?php }


function easy_timer_options_page() {
if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

if ($_REQUEST['submit'] ==  __('Save Changes')) {
$default_timer_prefix = $_REQUEST['default-timer-prefix'];
if ($_REQUEST['javascript-enabled'] == 'yes') { $javascript_enabled = 'yes'; } else { $javascript_enabled = 'no'; }
$easy_timer_options = array('default_timer_prefix' => $default_timer_prefix, 'javascript_enabled' => $javascript_enabled);
update_option('easy_timer', $easy_timer_options); }

$easy_timer_options = get_option('easy_timer');
if (isset($_POST['updated']) && $_POST['updated'] == 'true') {
$updated_message = '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>'; }

$content = '
<div class="wrap">
<h2>Easy Timer</h2>'
.$updated_message.'
<p>'.__('Complete Documentation', 'easy-timer').':</p>
<ul style="margin: 1.5em">
<li><a href="http://www.kleor-editions.com/easy-timer/en">'.__('in English', 'easy-timer').'</a></li>
<li><a href="http://www.kleor-editions.com/easy-timer">'.__('in French', 'easy-timer').'</a></li>
</ul>
<h3>'.__('Options', 'easy-timer').'</h3>
<form method="post" action="">
<p><input type="hidden" name="updated" value="true" />
<label for="default-timer-prefix">'.__('The <code>[timer]</code> shortcode is equivalent to', 'easy-timer').':</label> 
<select name="default-timer-prefix" id="default-timer-prefix">
<option value="'.$easy_timer_options['default_timer_prefix'].'">['.$easy_timer_options['default_timer_prefix'].'timer]</option>
<option value="dhms">[dhmstimer]</option>
<option value="dhm">[dhmtimer]</option>
<option value="dh">[dhtimer]</option>
<option value="d">[dtimer]</option>
<option value="hms">[hmstimer]</option>
<option value="hm">[hmtimer]</option>
<option value="h">[htimer]</option>
<option value="ms">[mstimer]</option>
<option value="m">[mtimer]</option>
<option value="s">[stimer]</option>
</select> '.__('<a href="http://www.kleor-editions.com/easy-timer/en/#part4.2">More informations</a>', 'easy-timer').'<br />
'.__('The <code>[total-timer]</code> shortcode is equivalent to', 'easy-timer').' <code>[total-'.$easy_timer_options['default_timer_prefix'].'timer]</code>.<br />
'.__('The <code>[elapsed-timer]</code> shortcode is equivalent to', 'easy-timer').' <code>[elapsed-'.$easy_timer_options['default_timer_prefix'].'timer]</code>.<br />
'.__('The <code>[total-elapsed-timer]</code> shortcode is equivalent to', 'easy-timer').' <code>[total-elapsed-'.$easy_timer_options['default_timer_prefix'].'timer]</code>.<br />
'.__('The <code>[remaining-timer]</code> shortcode is equivalent to', 'easy-timer').' <code>[remaining-'.$easy_timer_options['default_timer_prefix'].'timer]</code>.<br />
'.__('The <code>[total-remaining-timer]</code> shortcode is equivalent to', 'easy-timer').' <code>[total-remaining-'.$easy_timer_options['default_timer_prefix'].'timer]</code>.</p>
<p><input type="checkbox" name="javascript-enabled" id="javascript-enabled" value="yes" '.($easy_timer_options['javascript_enabled'] == 'yes' ? 'checked="checked"' : '' ).' /> <label for="javascript-enabled">'.__('Add JavaScript code', 'easy-timer').'</label><br />
'.__('If you uncheck this box, Easy Timer will never add any JavaScript code to the pages of your website, but your count up/down timers will not refresh.', 'easy-timer').' '.__('<a href="http://www.kleor-editions.com/easy-timer/en/#part7.2">More informations</a>', 'easy-timer').'</p>
<p><input class="button" name="submit" value="'.__('Save Changes').'" type="submit" /></p>
</form>
</div>';
	
echo $content; }


function easy_timer_admin_menu() {
add_options_page('Easy Timer', 'Easy Timer', 'manage_options', 'easy-timer', 'easy_timer_options_page'); }


add_filter('widget_text', 'do_shortcode');
add_shortcode('counter', 'counter');
add_shortcode('counter0', 'counter');
add_shortcode('counter1', 'counter');
add_shortcode('counter2', 'counter');
add_shortcode('counter3', 'counter');
add_shortcode('counter4', 'counter');
add_shortcode('counter5', 'counter');
add_shortcode('counter6', 'counter');
add_shortcode('counter7', 'counter');
add_shortcode('counter8', 'counter');
add_shortcode('counter9', 'counter');
add_shortcode('counter10', 'counter');
add_shortcode('countdown', 'countdown');
add_shortcode('countdown0', 'countdown');
add_shortcode('countdown1', 'countdown');
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
add_shortcode('countup0', 'countup');
add_shortcode('countup1', 'countup');
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
add_action('admin_menu', 'easy_timer_admin_menu');

add_option('easy_timer', array(
'default_timer_prefix' => 'dhms',
'javascript_enabled' => 'yes'));

$easy_timer_options = get_option('easy_timer');
if ($easy_timer_options['default_shortcode'] != '') {
$default_timer_prefix = substr($easy_timer_options['default_shortcode'], 1);
$easy_timer_options['default_timer_prefix'] = substr($default_timer_prefix, 0, -6);
unset($easy_timer_options['default_shortcode']);
update_option('easy_timer', $easy_timer_options); }