<?php if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$atts = array_map('easy_timer_do_shortcode', (array) $atts);
extract(shortcode_atts(array('filter' => '', 'offset' => ''), $atts));
$T = extract_timestamp($offset);
$yearday = easy_timer_filter_data($filter, date('z', $T) + 1);
if (strtolower($offset) == 'local') {
if (easy_timer_data('javascript_enabled') == 'yes') { wp_enqueue_script('easy-timer'); }
$yearday = '<span class="localyearday">'.$yearday.'</span>'; }