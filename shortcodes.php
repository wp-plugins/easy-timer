<?php load_plugin_textdomain('easy-timer', false, EASY_TIMER_FOLDER.'/languages');


function clock($atts) {
global $easy_timer_js_attribute;
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
if (easy_timer_data('javascript_enabled') == 'yes') { add_action('wp_footer', 'easy_timer_js'); }
$atts = array_map('easy_timer_do_shortcode', (array) $atts);
extract(shortcode_atts(array('filter' => '', 'format' => '', 'offset' => ''), $atts));
if ($format == '') { $format = (isset($atts['form']) ? $atts['form'] : ''); }
$offset = strtolower($offset); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); break;
case 'local': break;
default: $offset = round(str_replace(',', '.', $offset), 2); }
$T = extract_timestamp($offset);

$format = strtolower($format); switch ($format) {
case 'hms': $clock = date('H:i:s', $T); break;
default: $format = 'hm'; $clock = date('H:i', $T); }
$clock = easy_timer_filter_data($filter, $clock);

if (is_numeric($offset)) { return '<span class="'.$format.'clock" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$offset.'">'.$clock.'</span>'; }
else { return '<span class="local'.$format.'clock">'.$clock.'</span>'; } }


function counter($atts, $content) {
if (!function_exists('adodb_mktime')) { include_once EASY_TIMER_PATH.'libraries/adodb-time.php'; }
global $blog_id, $post;
if (!isset($blog_id)) { $blog_id = 1; }
if ((!isset($post)) || (!is_object($post))) { $post_id = 0; }
else { $post_id = $post->ID; }
$id = $blog_id.'-'.$post_id;
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$atts = array_map('easy_timer_do_shortcode', (array) $atts);
extract(shortcode_atts(array('date' => '', 'delimiter' => '', 'filter' => '', 'offset' => '', 'origin' => '', 'period' => '', 'way' => ''), $atts));
switch ($origin) {
case 'last': case 'last-visit': $origin = 'last-visit'; break;
default: $origin = 'first-visit'; }
if ($way != 'down') { $way = 'up'; }
if ($delimiter == 'before') { $delimiter = '[before]'; } else { $delimiter = '[after]'; }

if ((substr($date, 0, 1) == '-') || (strstr($date, '//-')) || (strstr($date, '+'))) {
if (($origin == 'first-visit') && (!isset($_COOKIE['first-visit-'.$id]))) { global $easy_timer_cookies; $easy_timer_cookies[$id] = $id; } }

if ($delimiter == '[after]') { $date = '0//'.$date; } else { $date = $date.'//0'; }
$date = explode('//', $date);
if ($delimiter == '[before]') { $date = array_reverse($date); }
$n = count($date);

$time = time();
$S = array(0); $T = array($time);

if ($period != '') {
$period = preg_split('#[^0-9]#', $period, 0, PREG_SPLIT_NO_EMPTY);
for ($j = 0; $j < 4; $j++) { $period[$j] = (int) (isset($period[$j]) ? $period[$j] : 0); }
$P = 86400*$period[0] + 3600*$period[1] + 60*$period[2] + $period[3];
if ($P > 0) {
if ($delimiter == '[after]') { $last_date = $date[$n - 1]; } else { $last_date = $date[1]; }
$last_date = preg_split('#[^0-9]#', $last_date, 0, PREG_SPLIT_NO_EMPTY);
for ($j = 0; $j < 6; $j++) { $last_date[$j] = (int) (isset($last_date[$j]) ? $last_date[$j] : ($j < 3 ? 1 : 0)); }
$last_T = adodb_mktime($last_date[3], $last_date[4], $last_date[5], $last_date[1], $last_date[2], $last_date[0]) - extract_offset($offset);
if ($delimiter == '[after]') { $last_S = $time - $last_T; } else { $last_S = $last_T - $time; }
if ($last_S > 0) { $r = ceil($last_S/$P); } } }

$is_positive = array(false);
$is_negative = array(false);
$is_relative = array(false);

for ($i = 1; $i < $n; $i++) {
	if (substr($date[$i], 0, 1) == '+') { $is_positive[$i] = true; } else { $is_positive[$i] = false; }
	if (substr($date[$i], 0, 1) == '-') { $is_negative[$i] = true; } else { $is_negative[$i] = false; }
	$is_relative[$i] = (($is_positive[$i]) || ($is_negative[$i]));
	$date[$i] = preg_split('#[^0-9]#', $date[$i], 0, PREG_SPLIT_NO_EMPTY);
	$original_date[$i] = $date[$i];
	for ($j = 0; $j < 6; $j++) { $date[$i][$j] = (int) (isset($date[$i][$j]) ? $date[$i][$j] : ($j < 3 ? 1 : 0)); }
	
	if ($is_relative[$i]) {
	if (($origin == 'first-visit') && (isset($_COOKIE['first-visit-'.$id]))) { $origin_time = (int) $_COOKIE['first-visit-'.$id]; }
	else { $origin_time = $time; }
	$S[$i] = 86400*$date[$i][0] + 3600*$date[$i][1] + 60*$date[$i][2] + $date[$i][3];
	if ($is_positive[$i]) { $S[$i] = $time - $origin_time - $S[$i]; }
	if ($is_negative[$i]) { $S[$i] = $time - $origin_time + $S[$i]; }
	$T[$i] = $time - $S[$i]; }
	
	else {
	switch (count($original_date[$i])) {
	case 0: case 1: $S[$i] = $date[$i][0]; $T[$i] = $time - $S[$i]; break;
	case 2: $S[$i] = 60*$date[$i][0] + $date[$i][1]; $T[$i] = $time - $S[$i]; break;
	default:
	$T[$i] = adodb_mktime($date[$i][3], $date[$i][4], $date[$i][5], $date[$i][1], $date[$i][2], $date[$i][0]) - extract_offset($offset);
	foreach (array('P', 'r') as $variable) { if (!isset($$variable)) { $$variable = 0; } }
	if ($delimiter == '[after]') { $T[$i] = $T[$i] + $r*$P; } else { $T[$i] = $T[$i] - $r*$P; }
	$S[$i] = $time - $T[$i]; } }
}

$i = 0; while (($i < $n) && ($S[$i] >= 0)) { $k = $i; $i = $i + 1; }
if ($i == $n) { $i = $n - 1; }

$content = do_shortcode($content);
if (!strstr($content, $delimiter)) { $content = $content.$delimiter; }
$content = explode($delimiter, $content);
if ($delimiter == '[before]') { $content = array_reverse($content); }
if (!isset($content[$k])) { $content[$k] = ''; }

if ((easy_timer_data('javascript_enabled') == 'yes') && (strstr($content[$k], 'timer]'))) {
add_action('wp_footer', 'easy_timer_lang_js');
add_action('wp_footer', 'easy_timer_js'); }

if ($way == 'up') {
$content[$k] = timer_replace($S[$k], $T[$k], '', 'up', $content[$k]);
$content[$k] = timer_replace($S[1], $T[1], 'total-', 'up', $content[$k]); }
if ($way == 'down') {
$content[$k] = timer_replace(-$S[$i], $T[$i], '', 'down', $content[$k]);
$content[$k] = timer_replace(-$S[$n - 1], $T[$n - 1], 'total-', 'down', $content[$k]); }

$content[$k] = timer_replace($S[$k], $T[$k], 'elapsed-', 'up', $content[$k]);
$content[$k] = timer_replace($S[1], $T[1], 'total-elapsed-', 'up', $content[$k]);
$content[$k] = timer_replace(-$S[$i], $T[$i], 'remaining-', 'down', $content[$k]);
$content[$k] = timer_replace(-$S[$n - 1], $T[$n - 1], 'total-remaining-', 'down', $content[$k]);

$content[$k] = easy_timer_filter_data($filter, $content[$k]);
return $content[$k]; }


function countdown($atts, $content) {
$atts['way'] = 'down';
$atts['delimiter'] = 'after';
return counter($atts, $content); }


function countup($atts, $content) {
$atts['way'] = 'up';
$atts['delimiter'] = 'before';
return counter($atts, $content); }


function easy_timer_js() {
global $easy_timer_js_extension; ?>
<script type="text/javascript" src="<?php echo EASY_TIMER_URL; ?>libraries/easy-timer<?php echo $easy_timer_js_extension; ?>.js?ver=<?php echo EASY_TIMER_VERSION; ?>"></script>
<?php }


function easy_timer_lang_js() { ?>
<script type="text/javascript">
var string0day = '';
var string0hour = '';
var string0minute = '';
var string0second = ' <?php _e('0 second', 'easy-timer'); ?>';
var string1day = ' <?php _e('1 day', 'easy-timer'); ?>';
var string1hour = ' <?php _e('1 hour', 'easy-timer'); ?>';
var string1minute = ' <?php _e('1 minute', 'easy-timer'); ?>';
var string1second = ' <?php _e('1 second', 'easy-timer'); ?>';
var stringNdays = ' [N] <?php _e('days', 'easy-timer'); ?>';
var stringNhours = ' [N] <?php _e('hours', 'easy-timer'); ?>';
var stringNminutes = ' [N] <?php _e('minutes', 'easy-timer'); ?>';
var stringNseconds = ' [N] <?php _e('seconds', 'easy-timer'); ?>';

var stringmonth = new Array(13);
stringmonth[0] = '<?php _e('DECEMBER', 'easy-timer'); ?>';
stringmonth[1] = '<?php _e('JANUARY', 'easy-timer'); ?>';
stringmonth[2] = '<?php _e('FEBRUARY', 'easy-timer'); ?>';
stringmonth[3] = '<?php _e('MARCH', 'easy-timer'); ?>';
stringmonth[4] = '<?php _e('APRIL', 'easy-timer'); ?>';
stringmonth[5] = '<?php _e('MAY', 'easy-timer'); ?>';
stringmonth[6] = '<?php _e('JUNE', 'easy-timer'); ?>';
stringmonth[7] = '<?php _e('JULY', 'easy-timer'); ?>';
stringmonth[8] = '<?php _e('AUGUST', 'easy-timer'); ?>';
stringmonth[9] = '<?php _e('SEPTEMBER', 'easy-timer'); ?>';
stringmonth[10] = '<?php _e('OCTOBER', 'easy-timer'); ?>';
stringmonth[11] = '<?php _e('NOVEMBER', 'easy-timer'); ?>';
stringmonth[12] = '<?php _e('DECEMBER', 'easy-timer'); ?>';

var stringweekday = new Array(8);
stringweekday[0] = '<?php _e('SUNDAY', 'easy-timer'); ?>';
stringweekday[1] = '<?php _e('MONDAY', 'easy-timer'); ?>';
stringweekday[2] = '<?php _e('TUESDAY', 'easy-timer'); ?>';
stringweekday[3] = '<?php _e('WEDNESDAY', 'easy-timer'); ?>';
stringweekday[4] = '<?php _e('THURSDAY', 'easy-timer'); ?>';
stringweekday[5] = '<?php _e('FRIDAY', 'easy-timer'); ?>';
stringweekday[6] = '<?php _e('SATURDAY', 'easy-timer'); ?>';
stringweekday[7] = '<?php _e('SUNDAY', 'easy-timer'); ?>';
</script>
<?php }


function extract_offset($offset) {
$offset = strtolower($offset); switch ($offset) {
case '': case 'local': $offset = 3600*get_option('gmt_offset'); break;
default: $offset = 36*(round(100*str_replace(',', '.', $offset))); }
return $offset; }


function extract_timestamp($offset) {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
return time() + extract_offset($offset); }


function isoyear($atts) {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$atts = array_map('easy_timer_do_shortcode', (array) $atts);
extract(shortcode_atts(array('filter' => '', 'offset' => ''), $atts));
$T = extract_timestamp($offset);
$isoyear = easy_timer_filter_data($filter, date('o', $T));
if (strtolower($offset) != 'local') { return $isoyear; }
else {
if (easy_timer_data('javascript_enabled') == 'yes') { add_action('wp_footer', 'easy_timer_js'); }
return '<span class="localisoyear">'.$isoyear.'</span>'; } }


function month($atts) {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$atts = array_map('easy_timer_do_shortcode', (array) $atts);
extract(shortcode_atts(array('filter' => '', 'format' => '', 'offset' => ''), $atts));
if ($format == '') { $format = (isset($atts['form']) ? $atts['form'] : ''); }
$T = extract_timestamp($offset);
$n = date('n', $T);

$format = strtolower($format); switch ($format) {
case '1': $month = $n; break;
case '2': $month = date('m', $T); break;
case 'lower': case 'upper': break;
default: $format = ''; }

if (($format == '') || ($format == 'lower') || ($format == 'upper')) {
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

if ($format == '') { $month = ucfirst(strtolower($stringmonth[$n])); }
elseif ($format == 'lower') { $month = strtolower($stringmonth[$n]); }
elseif ($format == 'upper') { $month = $stringmonth[$n]; }
$month = easy_timer_filter_data($filter, $month);

if (strtolower($offset) != 'local') { return $month; }
else {
if (easy_timer_data('javascript_enabled') == 'yes') {
if (($format == '') || ($format == 'lower') || ($format == 'upper')) { add_action('wp_footer', 'easy_timer_lang_js'); }
add_action('wp_footer', 'easy_timer_js'); }
return '<span class="local'.$format.'month">'.$month.'</span>'; } }


function monthday($atts) {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$atts = array_map('easy_timer_do_shortcode', (array) $atts);
extract(shortcode_atts(array('filter' => '', 'format' => '', 'offset' => ''), $atts));
if ($format == '') { $format = (isset($atts['form']) ? $atts['form'] : ''); }
$T = extract_timestamp($offset);

switch ($format) {
case '2': $monthday = date('d', $T); break;
default: $format = '1'; $monthday = date('j', $T); }
$monthday = easy_timer_filter_data($filter, $monthday);

if (strtolower($offset) != 'local') { return $monthday; }
else {
if (easy_timer_data('javascript_enabled') == 'yes') { add_action('wp_footer', 'easy_timer_js'); }
return '<span class="local'.$format.'monthday">'.$monthday.'</span>'; } }


function timer_string($S) {
if ($S < 0) { $S = 0; }
$D = floor($S/86400);
$H = floor($S/3600);
$M = floor($S/60);
$h = $H - 24*$D;
$m = $M - 60*$H;
$s = $S - 60*$M;

$string0day = '';
$string0hour = '';
$string0minute = '';
$string0second = ' '.__('0 second', 'easy-timer');
$string1day = ' '.__('1 day', 'easy-timer');
$string1hour = ' '.__('1 hour', 'easy-timer');
$string1minute = ' '.__('1 minute', 'easy-timer');
$string1second = ' '.__('1 second', 'easy-timer');
$stringNdays = ' [N] '.__('days', 'easy-timer');
$stringNhours = ' [N] '.__('hours', 'easy-timer');
$stringNminutes = ' [N] '.__('minutes', 'easy-timer');
$stringNseconds = ' [N] '.__('seconds', 'easy-timer');

$stringD = $string0day;
$stringH = $string0hour;
$stringM = $string0minute;
$stringS = $string0second;
$stringh = $string0hour;
$stringm = $string0minute;
$strings = $string0second;

if ($D == 1) { $stringD = $string1day; } elseif ($D > 1) { $stringD = str_replace('[N]', $D, $stringNdays); }
if ($H == 1) { $stringH = $string1hour; } elseif ($H > 1) { $stringH = str_replace('[N]', $H, $stringNhours); }
if ($M == 1) { $stringM = $string1minute; } elseif ($M > 1) { $stringM = str_replace('[N]', $M, $stringNminutes); }
if ($S == 1) { $stringS = $string1second; } elseif ($S > 1) { $stringS = str_replace('[N]', $S, $stringNseconds); }
if ($h == 1) { $stringh = $string1hour; } elseif ($h > 1) { $stringh = str_replace('[N]', $h, $stringNhours); }
if ($m == 1) { $stringm = $string1minute; } elseif ($m > 1) { $stringm = str_replace('[N]', $m, $stringNminutes); }
if ($s == 1) { $strings = $string1second; } elseif ($s > 1) { $strings = str_replace('[N]', $s, $stringNseconds); }

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

$stringhms = $stringh.$stringm.$strings;
$stringhm = $stringh.$stringm;
$stringms = $stringm.$strings;

$timer = array(
'Dhms' => trim($stringDhms),
'Dhm' => trim($stringDhm),
'Dh' => trim($stringDh),
'D' => trim($stringD),
'Hms' => trim($stringHms),
'Hm' => trim($stringHm),
'H' => trim($stringH),
'Ms' => trim($stringMs),
'M' => trim($stringM),
'S' => trim($stringS),
'hms' => trim($stringhms),
'hm' => trim($stringhm),
'h' => trim($stringh),
'ms' => trim($stringms),
'm' => trim($stringm),
's' => trim($strings));

return $timer; }


function timer_replace($S, $T, $prefix, $way, $content) {
global $easy_timer_js_attribute;
$timer = timer_string($S);

$content = str_replace('['.$prefix.'timer]', '['.$prefix.easy_timer_data('default_timer_prefix').'timer]', $content);
$content = str_replace('['.$prefix.'dhmstimer]', '<span class="dhmscount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['Dhms'].'</span>', $content);
$content = str_replace('['.$prefix.'dhmtimer]', '<span class="dhmcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['Dhm'].'</span>', $content);
$content = str_replace('['.$prefix.'dhtimer]', '<span class="dhcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['Dh'].'</span>', $content);
$content = str_replace('['.$prefix.'dtimer]', '<span class="dcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['D'].'</span>', $content);
$content = str_replace('['.$prefix.'hmstimer]', '<span class="hmscount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['Hms'].'</span>', $content);
$content = str_replace('['.$prefix.'hmtimer]', '<span class="hmcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['Hm'].'</span>', $content);
$content = str_replace('['.$prefix.'htimer]', '<span class="hcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['H'].'</span>', $content);
$content = str_replace('['.$prefix.'mstimer]', '<span class="mscount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['Ms'].'</span>', $content);
$content = str_replace('['.$prefix.'mtimer]', '<span class="mcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['M'].'</span>', $content);
$content = str_replace('['.$prefix.'stimer]', '<span class="scount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['S'].'</span>', $content);

$content = str_replace('['.$prefix.'rtimer]', '['.$prefix.easy_timer_data('default_timer_prefix').'rtimer]', $content);
$content = str_replace('['.$prefix.'dhmsrtimer]', '<span class="dhmscount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['Dhms'].'</span>', $content);
$content = str_replace('['.$prefix.'dhmrtimer]', '<span class="dhmcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['Dhm'].'</span>', $content);
$content = str_replace('['.$prefix.'dhrtimer]', '<span class="dhcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['Dh'].'</span>', $content);
$content = str_replace('['.$prefix.'drtimer]', '<span class="dcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['D'].'</span>', $content);
$content = str_replace('['.$prefix.'hmsrtimer]', '<span class="hmsrcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['hms'].'</span>', $content);
$content = str_replace('['.$prefix.'hmrtimer]', '<span class="hmrcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['hm'].'</span>', $content);
$content = str_replace('['.$prefix.'hrtimer]', '<span class="hrcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['h'].'</span>', $content);
$content = str_replace('['.$prefix.'msrtimer]', '<span class="msrcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['ms'].'</span>', $content);
$content = str_replace('['.$prefix.'mrtimer]', '<span class="mrcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['m'].'</span>', $content);
$content = str_replace('['.$prefix.'srtimer]', '<span class="srcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['s'].'</span>', $content);

return $content; }


function timezone($atts) {
$atts = array_map('easy_timer_do_shortcode', (array) $atts);
extract(shortcode_atts(array('filter' => '', 'offset' => ''), $atts));
$offset = strtolower($offset); switch ($offset) {
case '': $offset = 1*get_option('gmt_offset'); break;
case 'local': break;
default: $offset = round(str_replace(',', '.', $offset), 2); }

if (is_numeric($offset)) {
if ($offset == 0) { $timezone = 'UTC'; }
elseif ($offset > 0) { $timezone = 'UTC+'.$offset; }
elseif ($offset < 0) { $timezone = 'UTC'.$offset; }
return easy_timer_filter_data($filter, $timezone); }

else {
if (easy_timer_data('javascript_enabled') == 'yes') { add_action('wp_footer', 'easy_timer_js'); }
return '<span class="localtimezone">UTC</span>'; } }


function weekday($atts) {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$atts = array_map('easy_timer_do_shortcode', (array) $atts);
extract(shortcode_atts(array('filter' => '', 'format' => '', 'offset' => ''), $atts));
if ($format == '') { $format = (isset($atts['form']) ? $atts['form'] : ''); }
$T = extract_timestamp($offset);
$w = date('w', $T);

$format = strtolower($format); switch ($format) {
case 'lower': case 'upper': break;
default: $format = ''; }

$stringweekday = array(
0 => __('SUNDAY', 'easy-timer'),
1 => __('MONDAY', 'easy-timer'),
2 => __('TUESDAY', 'easy-timer'),
3 => __('WEDNESDAY', 'easy-timer'),
4 => __('THURSDAY', 'easy-timer'),
5 => __('FRIDAY', 'easy-timer'),
6 => __('SATURDAY', 'easy-timer'),
7 => __('SUNDAY', 'easy-timer'));

if ($format == '') { $weekday = ucfirst(strtolower($stringweekday[$w])); }
elseif ($format == 'lower') { $weekday = strtolower($stringweekday[$w]); }
elseif ($format == 'upper') { $weekday = $stringweekday[$w]; }
$weekday = easy_timer_filter_data($filter, $weekday);

if (strtolower($offset) != 'local') { return $weekday; }
else {
if (easy_timer_data('javascript_enabled') == 'yes') {
add_action('wp_footer', 'easy_timer_lang_js');
add_action('wp_footer', 'easy_timer_js'); }
return '<span class="local'.$format.'weekday">'.$weekday.'</span>'; } }


function year($atts) {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$atts = array_map('easy_timer_do_shortcode', (array) $atts);
extract(shortcode_atts(array('filter' => '', 'format' => '', 'offset' => ''), $atts));
if ($format == '') { $format = (isset($atts['form']) ? $atts['form'] : ''); }
$T = extract_timestamp($offset);

switch ($format) {
case '2': $year = date('y', $T); break;
default: $format = '4'; $year = date('Y', $T); }
$year = easy_timer_filter_data($filter, $year);

if (strtolower($offset) != 'local') { return $year; }
else {
if (easy_timer_data('javascript_enabled') == 'yes') { add_action('wp_footer', 'easy_timer_js'); }
return '<span class="local'.$format.'year">'.$year.'</span>'; } }


function yearday($atts) {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$atts = array_map('easy_timer_do_shortcode', (array) $atts);
extract(shortcode_atts(array('filter' => '', 'offset' => ''), $atts));
$T = extract_timestamp($offset);
$yearday = easy_timer_filter_data($filter, date('z', $T) + 1);
if (strtolower($offset) != 'local') { return $yearday; }
else {
if (easy_timer_data('javascript_enabled') == 'yes') { add_action('wp_footer', 'easy_timer_js'); }
return '<span class="localyearday">'.$yearday.'</span>'; } }


function yearweek($atts) {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$atts = array_map('easy_timer_do_shortcode', (array) $atts);
extract(shortcode_atts(array('filter' => '', 'offset' => ''), $atts));
$T = extract_timestamp($offset);
$yearweek = easy_timer_filter_data($filter, date('W', $T));
if (strtolower($offset) != 'local') { return $yearweek; }
else {
if (easy_timer_data('javascript_enabled') == 'yes') { add_action('wp_footer', 'easy_timer_js'); }
return '<span class="localyearweek">'.$yearweek.'</span>'; } }