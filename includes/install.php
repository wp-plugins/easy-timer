<?php load_plugin_textdomain('files-generator', false, FILES_GENERATOR_FOLDER.'/languages');
include FILES_GENERATOR_PATH.'initial-options.php';
$options = (array) get_option('files_generator');
$current_options = $options;
if ((isset($options[0])) && ($options[0] === false)) { unset($options[0]); }
foreach ($initial_options as $key => $value) {
if (($key == 'version') || (!isset($options[$key])) || ($options[$key] == '')) { $options[$key] = $value; } }
if ($options != $current_options) { update_option('files_generator', $options); }