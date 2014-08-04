<?php global $files_generator_options;
if (empty($files_generator_options)) { $files_generator_options = (array) get_option('files_generator'); }
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $formatting = 'yes'; }
else {
$atts = array_map('files_generator_do_shortcode', (array) $atts);
$field = (isset($atts[0]) ? $atts[0] : '');
foreach (array('default', 'filter') as $key) { $$key = (isset($atts[$key]) ? $atts[$key] : ''); }
$formatting = (((isset($atts['formatting'])) && ($atts['formatting'] == 'no')) ? 'no' : 'yes'); }
$field = str_replace('-', '_', files_generator_format_nice_name($field));
$data = (isset($files_generator_options[$field]) ? $files_generator_options[$field] : '');
$data = (string) ($formatting == 'yes' ? do_shortcode($data) : $data);
if ($data === '') { $data = $default; }
$data = files_generator_filter_data($filter, $data);