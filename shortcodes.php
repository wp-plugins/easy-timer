<?php function affiliation_activation_url($atts) {
extract(shortcode_atts(array('filter' => ''), $atts));
$id = affiliate_data('id');
$key = hash('sha256', $id.affiliation_data('encrypted_urls_key'));
$url = AFFILIATION_MANAGER_URL.'?action=activate&id='.$id.'&key='.$key;
$url = affiliation_filter_data($filter, $url);
return $url; }


function affiliation_comments($atts) {
if (!is_admin()) {
extract(shortcode_atts(array('condition' => 'session'), $atts));
$condition = strtolower($condition);
switch ($condition) {
case 'session': if (!affiliation_session()) {
foreach (array('comments_array', 'comments_open') as $function) { add_filter($function, 'affiliation_'.$function, 1, 0); } } break;
case '!session': if (affiliation_session()) {
foreach (array('comments_array', 'comments_open') as $function) { add_filter($function, 'affiliation_'.$function, 1, 0); } } } } }


function affiliation_content($atts, $content) {
$content = explode('[other]', do_shortcode($content));
if (affiliation_session()) { $n = 0; } else { $n = 1; }
return $content[$n]; }


function affiliation_counter_tag($atts) {
extract(shortcode_atts(array('data' => '', 'filter' => ''), $atts));
$string = $_GET['affiliation_'.str_replace('-', '_', format_nice_name($data))];
$string = affiliation_filter_data($filter, $string);
return $string; }


function affiliation_counter($atts, $content) {
include dirname(__FILE__).'/counter.php';
return $content[$k]; }


function referrer_counter($atts, $content) {
$type = 'referrer';
include dirname(__FILE__).'/counter.php';
return $content[$k]; }


function affiliation_redirection($atts) {
if (!is_admin()) {
extract(shortcode_atts(array('action' => '', 'condition' => '', 'url' => ''), $atts));
$action = strtolower($action);
$condition = strtolower($condition);
if ($url == '') { $url = '../'; }
switch ($condition) {
case 'session': if (affiliation_session()) {
if ($action == 'logout') { affiliation_logout(); }
if (!headers_sent()) { header('Location: '.$url); exit; } } else { $url = ''; } break;
case '!session': if (!affiliation_session()) {
if (!headers_sent()) { header('Location: '.$url); exit; } } else { $url = ''; } break;
default: if (($action == 'logout') && (affiliation_session())) { affiliation_logout(); }
if (!headers_sent()) { header('Location: '.$url); exit; } }
return $url; } }