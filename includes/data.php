<?php global $audiobooks_pages_options;
if (empty($audiobooks_pages_options)) { $audiobooks_pages_options = (array) get_option('audiobooks_pages'); }
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $formatting = 'yes'; }
else {
$atts = array_map('audiobooks_pages_do_shortcode', (array) $atts);
$field = (isset($atts[0]) ? $atts[0] : '');
foreach (array('default', 'filter') as $key) { $$key = (isset($atts[$key]) ? $atts[$key] : ''); }
$formatting = (((isset($atts['formatting'])) && ($atts['formatting'] == 'no')) ? 'no' : 'yes'); }
$field = str_replace('-', '_', audiobooks_pages_format_nice_name($field));
$data = (isset($audiobooks_pages_options[$field]) ? $audiobooks_pages_options[$field] : '');
$data = (string) ($formatting == 'yes' ? do_shortcode($data) : $data);
if ($data === '') { $data = $default; }
$data = audiobooks_pages_filter_data($filter, $data);