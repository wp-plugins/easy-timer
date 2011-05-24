<?php include_once 'initial-options.php';

$easy_timer_options = get_option('easy_timer');
foreach ($easy_timer_initial_options as $key => $value) {
if ($easy_timer_options[$key] == '') { $easy_timer_options[$key] = $easy_timer_initial_options[$key]; } }
update_option('easy_timer', $easy_timer_options);