<?php global $audiobooks_authors_and_narrators_options;
if (empty($audiobooks_authors_and_narrators_options)) { $audiobooks_authors_and_narrators_options = (array) get_option('audiobooks_authors_and_narrators'); }
if (is_string($atts)) { $field = $atts; $decimals = ''; $default = ''; $filter = ''; $part = 0; }
else {
$atts = array_map('audiobooks_authors_and_narrators_do_shortcode', (array) $atts);
$field = (isset($atts[0]) ? $atts[0] : '');
foreach (array('decimals', 'default', 'filter') as $key) {
$$key = (isset($atts[$key]) ? $atts[$key] : '');
if (isset($atts[$key])) { unset($atts[$key]); } }
$part = (int) (isset($atts['part']) ? preg_replace('/[^0-9]/', '', $atts['part']) : 0); }
$field = str_replace('-', '_', format_nice_name($field));
$data = (isset($audiobooks_authors_and_narrators_options[$field]) ? $data = $audiobooks_authors_and_narrators_options[$field] : '');
if ($part > 0) { $data = explode(',', $data); $data = (isset($data[$part - 1]) ? trim($data[$part - 1]) : ''); }
$data = (string) do_shortcode($data);
if ($data === '') { $data = $default; }
$data = audiobooks_authors_and_narrators_format_data($field, $data);
if ($data === '') { $data = $default; }
$data = audiobooks_authors_and_narrators_filter_data($filter, $data);
$data = audiobooks_authors_and_narrators_decimals_data($decimals, $data);