<?php if ((function_exists('current_user_can')) && (!current_user_can('manage_options'))
 && (function_exists('user_can')) && (!user_can($data['post_author'], 'manage_options'))) {
global $easy_timer_shortcodes;
foreach (array('post_content', 'post_content_filtered', 'post_excerpt', 'post_title') as $key) {
foreach ((array) $easy_timer_shortcodes as $tag) {
$data[$key] = str_replace(array('['.$tag, $tag.']'), array('&#91;'.$tag, $tag.'&#93;'), $data[$key]); } } }