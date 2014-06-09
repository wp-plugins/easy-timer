<?php load_plugin_textdomain('audiobooks-pages', false, AUDIOBOOKS_PAGES_FOLDER.'/languages');
include AUDIOBOOKS_PAGES_PATH.'initial-options.php';
$options = (array) get_option('audiobooks_pages');
$current_options = $options;
if ((isset($options[0])) && ($options[0] === false)) { unset($options[0]); }
foreach ($initial_options as $key => $value) {
if (($key == 'version') || (!isset($options[$key])) || ($options[$key] == '')) { $options[$key] = $value; } }
if ($options != $current_options) { update_option('audiobooks_pages', $options); }