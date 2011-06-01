<?php include_once 'initial-options.php';
$options = get_option('easy_timer');
foreach ($initial_options as $key => $value) {
if ($options[$key] == '') { $options[$key] = $initial_options[$key]; } }
update_option('easy_timer', $options);