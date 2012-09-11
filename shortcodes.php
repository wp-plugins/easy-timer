<?php function membership_activation_url($atts) {
extract(shortcode_atts(array('filter' => ''), $atts));
$id = member_data('id');
$key = hash('sha256', $id.membership_data('encrypted_urls_key'));
$url = MEMBERSHIP_MANAGER_URL.'?action=activate&id='.$id.'&key='.$key;
$url = membership_filter_data($filter, $url);
return $url; }


function membership_comments($atts) {
if (!is_admin()) {
global $post;
extract(shortcode_atts(array('condition' => 'session', 'date' => '', 'delay' => '', 'id' => ''), $atts));
$condition = strtolower($condition);

if ((($date == '') && ($delay == '')) || (!membership_session(''))) { $delay_reached = true; }
else {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$date = strtolower($date);
if ($date == '') { $time = time(); }
else {
if ($date == 'post') { $date = $post->post_date; }
$d = preg_split('#[^0-9]#', $date, 0, PREG_SPLIT_NO_EMPTY);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]) - 3600*UTC_OFFSET; }
$delay = preg_split('#[^0-9]#', $delay, 0, PREG_SPLIT_NO_EMPTY);
for ($i = 0; $i < 4; $i++) { $delay[$i] = (int) $delay[$i]; }
$delay = 86400*$delay[0] + 3600*$delay[1] + 60*$delay[2] + $delay[3];
$d = preg_split('#[^0-9]#', member_data('date_utc'), 0, PREG_SPLIT_NO_EMPTY);
$registration_time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
if (($time - $delay - $registration_time) < 0) { $delay_reached = false; } else { $delay_reached = true; } }

switch ($condition) {
case 'session': if ((!membership_session($id)) || (!$delay_reached)) {
foreach (array('comments_array', 'comments_open') as $function) { add_filter($function, 'membership_'.$function, 1, 0); } } break;
case '!session': if ((membership_session($id)) && ($delay_reached)) {
foreach (array('comments_array', 'comments_open') as $function) { add_filter($function, 'membership_'.$function, 1, 0); } } } } }


function membership_content($atts, $content) {
global $post;
extract(shortcode_atts(array('date' => '', 'delay' => '', 'id' => ''), $atts));
$content = explode('[other]', do_shortcode($content));
if (($date == '') && ($delay == '')) { $delay_reached = true; }
else {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$date = strtolower($date);
if ($date == '') { $time = time(); }
else {
if ($date == 'post') { $date = $post->post_date; }
$d = preg_split('#[^0-9]#', $date, 0, PREG_SPLIT_NO_EMPTY);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]) - 3600*UTC_OFFSET; }
$delay = preg_split('#[^0-9]#', $delay, 0, PREG_SPLIT_NO_EMPTY);
for ($i = 0; $i < 4; $i++) { $delay[$i] = (int) $delay[$i]; }
$delay = 86400*$delay[0] + 3600*$delay[1] + 60*$delay[2] + $delay[3];
$d = preg_split('#[^0-9]#', member_data('date_utc'), 0, PREG_SPLIT_NO_EMPTY);
$registration_time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
if (($time - $delay - $registration_time) < 0) { $delay_reached = false; } else { $delay_reached = true; } }
if ((membership_session($id)) && ($delay_reached)) { $n = 0; } else { $n = 1; }
return $content[$n]; }


function membership_counter_tag($atts) {
extract(shortcode_atts(array('data' => '', 'filter' => ''), $atts));
$string = $_GET['membership_'.str_replace('-', '_', format_nice_name($data))];
$string = membership_filter_data($filter, $string);
return $string; }


function membership_counter($atts, $content) {
include dirname(__FILE__).'/counter.php';
return $content[$k]; }


function membership_redirection($atts) {
if (!is_admin()) {
$post = (object) $_GET['post_data'];
extract(shortcode_atts(array('action' => '', 'condition' => '', 'date' => '', 'delay' => '', 'id' => '', 'url' => ''), $atts));
$action = strtolower($action);
$condition = strtolower($condition);
if ($url == '') { $url = '../'; }

if ((($date == '') && ($delay == '')) || (!membership_session(''))) { $delay_reached = true; }
else {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$date = strtolower($date);
if ($date == '') { $time = time(); }
else {
if ($date == 'post') { $date = $post->post_date; }
$d = preg_split('#[^0-9]#', $date, 0, PREG_SPLIT_NO_EMPTY);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]) - 3600*UTC_OFFSET; }
$delay = preg_split('#[^0-9]#', $delay, 0, PREG_SPLIT_NO_EMPTY);
for ($i = 0; $i < 4; $i++) { $delay[$i] = (int) $delay[$i]; }
$delay = 86400*$delay[0] + 3600*$delay[1] + 60*$delay[2] + $delay[3];
$d = preg_split('#[^0-9]#', member_data('date_utc'), 0, PREG_SPLIT_NO_EMPTY);
$registration_time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
if (($time - $delay - $registration_time) < 0) { $delay_reached = false; } else { $delay_reached = true; } }

switch ($condition) {
case 'session': if ((membership_session($id)) && ($delay_reached)) {
if ($action == 'logout') { membership_logout(); }
if (!headers_sent()) { header('Location: '.$url); exit; } } else { $url = ''; } break;
case '!session': if ((!membership_session($id)) || (!$delay_reached)) {
if (!headers_sent()) { header('Location: '.$url); exit; } } else { $url = ''; } break;
default: if (($action == 'logout') && (membership_session('')) && ($delay_reached)) { membership_logout(); }
if (!headers_sent()) { header('Location: '.$url); exit; } }

return $url; } }