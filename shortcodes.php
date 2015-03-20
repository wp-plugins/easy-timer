<?php load_plugin_textdomain('easy-timer', false, EASY_TIMER_FOLDER.'/languages');
wp_register_script('easy-timer', EASY_TIMER_URL.'libraries/easy-timer.js', array(), EASY_TIMER_VERSION, true);


function easy_timer_clock($atts) { include EASY_TIMER_PATH.'includes/clock.php'; return $clock; }


function easy_timer_counter($atts, $content) { include EASY_TIMER_PATH.'includes/counter.php'; return $content[$k]; }


function easy_timer_countdown($atts, $content) {
$atts['way'] = 'down';
$atts['delimiter'] = 'after';
return easy_timer_counter($atts, $content); }


function easy_timer_countup($atts, $content) {
$atts['way'] = 'up';
$atts['delimiter'] = 'before';
return easy_timer_counter($atts, $content); }


function easy_timer_extract_offset($offset) {
$offset = strtolower($offset); switch ($offset) {
case '': case 'local': $offset = 3600*get_option('gmt_offset'); break;
default: $offset = 36*(round(100*str_replace(',', '.', $offset))); }
return $offset; }


function easy_timer_extract_timestamp($offset) {
date_default_timezone_set('UTC');
return time() + easy_timer_extract_offset($offset); }


function easy_timer_isoyear($atts) { include EASY_TIMER_PATH.'includes/isoyear.php'; return $isoyear; }


function easy_timer_lang_js() { include EASY_TIMER_PATH.'includes/lang-js.php'; }


function easy_timer_month($atts) { include EASY_TIMER_PATH.'includes/month.php'; return $month; }


function easy_timer_monthday($atts) { include EASY_TIMER_PATH.'includes/monthday.php'; return $monthday; }


function easy_timer_timer_replace($S, $T, $prefix, $way, $content) { include EASY_TIMER_PATH.'includes/timer-replace.php'; return $content; }


function easy_timer_timer_string($S) { include EASY_TIMER_PATH.'includes/timer-string.php'; return $timer; }


function easy_timer_timezone($atts) { include EASY_TIMER_PATH.'includes/timezone.php'; return $timezone; }


function easy_timer_weekday($atts) { include EASY_TIMER_PATH.'includes/weekday.php'; return $weekday; }


function easy_timer_year($atts) { include EASY_TIMER_PATH.'includes/year.php'; return $year; }


function easy_timer_yearday($atts) { include EASY_TIMER_PATH.'includes/yearday.php'; return $yearday; }


function easy_timer_yearweek($atts) { include EASY_TIMER_PATH.'includes/yearweek.php'; return $yearweek; }