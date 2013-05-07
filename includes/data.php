<?php global $easy_timer_options;
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; }
else {
$field = (isset($atts[0]) ? $atts[0] : '');
$default = (isset($atts['default']) ? $atts['default'] : '');
$filter = (isset($atts['filter']) ? $atts['filter'] : ''); }
$field = str_replace('-', '_', easy_timer_format_nice_name($field));
if ($field == '') { $field = 'version'; }
$data = (isset($easy_timer_options[$field]) ? $easy_timer_options[$field] : '');
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = easy_timer_filter_data($filter, $data);