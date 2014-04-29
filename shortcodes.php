<?php function audiobooks_authors_and_narrators_counter_tag($atts) {
$atts = array_map('audiobooks_authors_and_narrators_do_shortcode', (array) $atts);
extract(shortcode_atts(array('data' => '', 'decimals' => '0/2', 'filter' => ''), $atts));
$string = $GLOBALS['audiobooks_authors_and_narrators_'.str_replace('-', '_', format_nice_name($data))];
$string = audiobooks_authors_and_narrators_filter_data($filter, $string);
$string = audiobooks_authors_and_narrators_decimals_data($decimals, $string);
return $string; }


function audiobooks_authors_and_narrators_counter($atts, $content) {
$type = '';
include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'includes/counter.php';
return $content; }