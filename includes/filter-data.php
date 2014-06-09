<?php if (is_string($filter)) { $filter = preg_split('#[^a-zA-Z0-9_]#', str_replace('-', '_', audiobooks_pages_do_shortcode($filter)), 0, PREG_SPLIT_NO_EMPTY); }
if (is_array($filter)) { foreach ($filter as $function) {
if ($function != 'eval') {
if (!function_exists($function)) { $function = 'audiobooks_pages_'.$function; }
if (function_exists($function)) { $data = @$function($data); } } } }