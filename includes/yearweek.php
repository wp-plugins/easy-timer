<?php date_default_timezone_set('UTC');
$atts = array_map('easy_timer_do_shortcode', (array) $atts);
extract(shortcode_atts(array('filter' => '', 'offset' => ''), $atts));
$T = easy_timer_extract_timestamp($offset);
$yearweek = easy_timer_filter_data($filter, date('W', $T));
if (strtolower($offset) == 'local') {
if (easy_timer_data('javascript_enabled') == 'yes') { wp_enqueue_script('easy-timer'); }
$yearweek = '<span class="localyearweek">'.$yearweek.'</span>'; }