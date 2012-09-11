<?php
/*
Plugin Name: Membership Manager
Plugin URI: http://www.kleor-editions.com/membership-manager
Description: Allows you to create and manage your members areas.
Version: 4.7
Author: Kleor
Author URI: http://www.kleor-editions.com
Text Domain: membership-manager
*/


if (!defined('HOME_URL')) { define('HOME_URL', get_option('home')); }
if (!defined('UTC_OFFSET')) { define('UTC_OFFSET', get_option('gmt_offset')); }
define('MEMBERSHIP_MANAGER_URL', plugin_dir_url(__FILE__));
$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
define('MEMBERSHIP_MANAGER_VERSION', $plugin_data['Version']);

if (!function_exists('fix_url')) { include_once dirname(__FILE__).'/libraries/formatting-functions.php'; }
if (is_admin()) { include_once dirname(__FILE__).'/admin.php'; }

global $wpdb;
$membership_manager_options = get_option('membership_manager');
if (((is_multisite()) || ($membership_manager_options)) && ($membership_manager_options['version'] != MEMBERSHIP_MANAGER_VERSION)) {
include_once dirname(__FILE__).'/admin.php';
install_membership_manager(); }

fix_url();
membership_session('');
if (!is_admin()) {
add_shortcode('membership-redirection', create_function('$atts', 'include_once dirname(__FILE__)."/shortcodes.php"; return membership_redirection($atts);'));
if (!defined('POST_ID')) {
$root_url = explode('/', str_replace('//', '||', HOME_URL));
$root_url = str_replace('||', '//', $root_url[0]);
$path = explode('?', strtolower($_SERVER['REQUEST_URI']));
$path = explode('#', $path[0]);
$path = str_replace(HOME_URL, '', $root_url.$path[0]);
while (substr($path, 0, 1) == '/') { $path = substr($path, 1); }
while (substr($path, -1) == '/') { $path = substr($path, 0, -1); }
$post = get_page_by_path($path);
$_GET['post_data'] = (array) $post;
$id = (int) $post->ID;
if ($id == 0) { $id = (int) $_GET['page_id']; }
define('POST_ID', $id); }
if (POST_ID > 0) { do_shortcode(get_post_meta(POST_ID, 'membership', true)); }
if (membership_session('')) {
if ($_GET['member_data']['login'] != $_SESSION['membership_login']) {
$_GET['member_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$_SESSION['membership_login']."'", OBJECT); }
if ((function_exists('commerce_session')) && (!commerce_session())) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_clients WHERE email_address = '".$_GET['member_data']['email_address']."' AND status = 'active'", OBJECT);
if ($result) { $_GET['client_data'] = (array) $result; $_SESSION['commerce_login'] = $result->login; } }
if ((function_exists('affiliation_session')) && (!affiliation_session())) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".$_GET['member_data']['email_address']."' AND status = 'active'", OBJECT);
if ($result) { $_GET['affiliate_data'] = (array) $result; $_SESSION['affiliation_login'] = $result->login; } } } }


function add_member($member) { include dirname(__FILE__).'/add-member.php'; }


function members_areas_categories_list($id) {
global $wpdb;
$id = (int) $id;
$list = array($id);
while ($id > 0) {
$category = $wpdb->get_row("SELECT category_id FROM ".$wpdb->prefix."membership_manager_members_areas_categories WHERE id = $id", OBJECT);
if ($category) { $id = $category->category_id; }
if ((!$category) || (in_array($id, $list))) { $id = 0; }
$list[] = $id; }
return $list; }


function members_areas_list($members_areas) {
if (is_int($members_areas)) {
$id = $members_areas;
$list = array($id);
if ($id > 0) {
$new_list = array_unique(array_merge($list, preg_split('#[^0-9]#', member_area_data(array(0 => 'members_areas', 'id' => $id)), 0, PREG_SPLIT_NO_EMPTY)));
while (count($list) < count($new_list)) {
$members_areas = array();
foreach ($new_list as $member_area) { if (!in_array($member_area, $list)) { $members_areas[] = $member_area; } }
$list = $new_list;
$new_list = members_areas_list($members_areas); } } }
else {
if (!is_array($members_areas)) { $members_areas = preg_split('#[^0-9]#', $members_areas, 0, PREG_SPLIT_NO_EMPTY); }
$members_areas = array_unique($members_areas);
$new_list = array();
for ($i = 0; $i < count($members_areas); $i++) {
$members_areas[$i] = (int) $members_areas[$i];
$list[$i] = members_areas_list($members_areas[$i]);
$new_list = array_unique(array_merge($new_list, $list[$i])); }
$list = $new_list; }
sort($list, SORT_NUMERIC);
return $list; }


function members_categories_list($id) {
global $wpdb;
$id = (int) $id;
$list = array($id);
while ($id > 0) {
$category = $wpdb->get_row("SELECT category_id FROM ".$wpdb->prefix."membership_manager_members_categories WHERE id = $id", OBJECT);
if ($category) { $id = $category->category_id; }
if ((!$category) || (in_array($id, $list))) { $id = 0; }
$list[] = $id; }
return $list; }


function membership_comments_array() { return array(); }

function membership_comments_open() { return false; }

function membership_post_comments() {
global $post;
foreach (array('comments_array', 'comments_open') as $function) { remove_filter($function, 'membership_'.$function); }
do_shortcode(get_post_meta($post->ID, 'membership', true)); }

add_filter('the_post', 'membership_post_comments');


function membership_data($atts) {
global $membership_manager_options;
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $part = 0; }
else { $field = $atts[0]; $default = $atts['default']; $filter = $atts['filter']; $part = (int) $atts['part']; }
$field = str_replace('-', '_', format_nice_name($field));
if ($field == '') { $field = 'version'; }
if ((substr($field, -10) == 'email_body') || (substr($field, -19) == 'custom_instructions')) { $data = get_option('membership_manager_'.$field); }
else { $data = $membership_manager_options[$field]; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = membership_format_data($field, $data);
$data = membership_filter_data($filter, $data);
return $data; }


function membership_decrypt_url($url) {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$url = explode('?url=', $url);
$url = $url[1];
$url = base64_decode($url);
$url = trim(mcrypt_decrypt(MCRYPT_BLOWFISH, md5(membership_data('encrypted_urls_key')), $url, MCRYPT_MODE_ECB));
$url = explode('|', $url);
$T = $url[0];
$url = $url[1];
$S = time() - $T;
if ($S > 3600*membership_data('encrypted_urls_validity_duration')) { $url = HOME_URL; }
return $url; }


function membership_encrypt_url($url) {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$url = time().'|'.$url;
$url = mcrypt_encrypt(MCRYPT_BLOWFISH, md5(membership_data('encrypted_urls_key')), $url, MCRYPT_MODE_ECB);
$url = base64_encode($url);
$url = MEMBERSHIP_MANAGER_URL.'?url='.$url;
return $url; }


function membership_filter_data($filter, $data) {
if (is_string($filter)) { $filter = preg_split('#[^a-zA-Z0-9_]#', str_replace('-', '_', $filter), 0, PREG_SPLIT_NO_EMPTY); }
if (is_array($filter)) { foreach ($filter as $function) { $data = membership_string_map($function, $data); } }
return $data; }


function membership_format_data($field, $data) {
$data = quotes_entities_decode(do_shortcode($data));
if ((strstr($field, 'date')) && ($data == '0000-00-00 00:00:00')) { $data = ''; }
elseif (substr($field, -13) == 'email_address') { $data = format_email_address($data); }
elseif (substr($field, -12) == 'instructions') { $data = format_instructions($data); }
elseif ((($field == 'url') || (strstr($field, '_url'))) && (!strstr($field, 'urls'))) { $data = format_url($data); }
switch ($field) { case 'encrypted_urls_validity_duration': $data = round(100*$data)/100; }
return $data; }


function membership_i18n($string) {
load_plugin_textdomain('membership-manager', false, 'membership-manager/languages');
return __(__($string), 'membership-manager'); }


function membership_user_data($atts) { include dirname(__FILE__).'/user-data.php'; return $data; }


function membership_item_data($type, $atts) { include dirname(__FILE__).'/item-data.php'; return $data; }


function member_area_data($atts) {
if ((is_array($atts)) && (!isset($atts['id'])) && (isset($atts['category']))) { return member_area_category_data($atts); }
else { return membership_item_data('member_area', $atts); } }


function member_area_category_data($atts) {
return membership_item_data('member_area_category', $atts); }


function member_data($atts) {
if ((is_array($atts)) && (!isset($atts['id'])) && (isset($atts['category']))) { return member_category_data($atts); }
else { return membership_item_data('member', $atts); } }


function member_category_data($atts) {
return membership_item_data('member_category', $atts); }


function membership_jquery_js() {
if (!defined('KLEOR_JQUERY_LOADED')) { define('KLEOR_JQUERY_LOADED', true); ?>
<script type="text/javascript" src="<?php echo MEMBERSHIP_MANAGER_URL; ?>libraries/jquery.js"></script>
<?php } }


function membership_login_through_wordpress() {
global $user_email, $wpdb;
if ((!membership_session('')) && ($_SESSION['membership_login'] != 'NONE') && (is_user_logged_in())) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members WHERE email_address = '".$user_email."'", OBJECT);
if ($result) { $_GET['member_data'] = (array) $result; $_SESSION['membership_login'] = $result->login; }
else { $_SESSION['membership_login'] = 'NONE'; } } }

add_action('plugins_loaded', 'membership_login_through_wordpress', 1, 0);


function membership_logout_through_wordpress() {
foreach (array('affiliation', 'commerce', 'membership') as $prefix) {
unset($_SESSION[$prefix.'_login']);
setcookie($prefix.'_login', '', time() - 86400, '/'); } }

add_action('wp_logout', 'membership_logout_through_wordpress', 10, 0);


function membership_logout() {
if (!headers_sent()) { session_start(); }
if (!membership_session('')) {
if ((!defined('MEMBERSHIP_MANAGER_DEMO')) || (MEMBERSHIP_MANAGER_DEMO == false)) {
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = membership_data('logout_notification_email_'.$field); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); }
if (membership_data('logout_custom_instructions_executed') == 'yes') {
eval(format_instructions(membership_data('logout_custom_instructions'))); } } }
include_once ABSPATH.'wp-includes/pluggable.php';
if (function_exists('wp_logout')) { wp_logout(); }
membership_logout_through_wordpress(); }


function membership_session($members_areas) {
global $wpdb;
if (!is_array($members_areas)) { $members_areas = preg_split('#[^0-9]#', $members_areas, 0, PREG_SPLIT_NO_EMPTY); }
$members_areas = array_unique($members_areas);
$n = count($members_areas);
if (!headers_sent()) { session_start(); }
if ((isset($_COOKIE['membership_login'])) && ((!isset($_SESSION['membership_login'])) || ($_SESSION['membership_login'] == 'NONE'))) {
$login = substr($_COOKIE['membership_login'], 0, -64);
if (substr($_COOKIE['membership_login'], -64) == hash('sha256', $login.AUTH_KEY)) {
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$login."' AND status = 'active'", OBJECT);
if ($result) { $_GET['member_data'] = (array) $result; $_SESSION['membership_login'] = $login; }
else { setcookie('membership_login', '', time() - 86400, '/'); } } }	
if ((isset($_SESSION['membership_login'])) && ($_SESSION['membership_login'] != 'NONE')) {
if ($n == 0) { return true; }
else {
$list = members_areas_list(member_data('members_areas'));
if (in_array(0, $list)) { $session = true; }
else {
for ($i = 0; $i < $n; $i++) { $members_areas[$i] = (int) $members_areas[$i]; }
$session = false; $i = 0; while ((!$session) && ($i < $n)) {
if (in_array($members_areas[$i], $list)) { $session = true; }
$i = $i + 1; } }
return $session; } }
else { return false; } }


function membership_sql_array($table, $array) {
foreach ($table as $key => $value) {
$sql[$key] = ($key == 'password' ? hash('sha256', $array[$key]) : $array[$key]);
if ($value['type'] == 'int') { $sql[$key] = (int) $sql[$key]; }
elseif ((strstr($value['type'], 'dec')) && (!is_numeric($sql[$key]))) { $sql[$key] = round(100*$sql[$key])/100; }
elseif (($value['type'] == 'text') || ($value['type'] == 'datetime')) { $sql[$key] = "'".$sql[$key]."'"; } }
return $sql; }


function membership_string_map($function, $string) {
if (!function_exists($function)) { $function = 'membership_'.$function; }
if (function_exists($function)) { $array = array_map($function, array($string)); $string = $array[0]; }
return $string; }


function update_member_members_areas($member, $members_areas, $action) {
global $wpdb;
if (is_numeric($member)) { $field = 'id'; } else { $field = 'login'; $member = "'".$member."'"; }
$result = $wpdb->get_row("SELECT members_areas FROM ".$wpdb->prefix."membership_manager_members WHERE ".$field." = ".$member, OBJECT);
$current_members_areas = $result->members_areas;
if ($action == 'add') { $new_members_areas = array_unique(preg_split('#[^0-9]#', $current_members_areas.', '.$members_areas, 0, PREG_SPLIT_NO_EMPTY)); }
else {
$members_areas = array_unique(preg_split('#[^0-9]#', $members_areas, 0, PREG_SPLIT_NO_EMPTY));
$current_members_areas = array_unique(preg_split('#[^0-9]#', $current_members_areas, 0, PREG_SPLIT_NO_EMPTY));
$new_members_areas = array();
foreach ($current_members_areas as $member_area) {
if (!in_array($member_area, $members_areas)) { $new_members_areas[] = $member_area; } } }
sort($new_members_areas, SORT_NUMERIC);
foreach ($new_members_areas as $member_area) { $members_areas_list .= $member_area.', '; }
$new_members_areas = substr($members_areas_list, 0, -2);
$results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_members SET members_areas = '".$new_members_areas."' WHERE ".$field." = ".$member); }


class Membership_Meta_Widget extends WP_Widget {
function __construct() {
parent::WP_Widget('membership-meta-widget', __('Meta (Membership)', 'membership-manager'), array('description' => __('Log in/out', 'membership-manager'))); }

function form($instance) {
include 'initial-options.php';
$instance = wp_parse_args($instance, $initial_options['meta_widget']);
$title = esc_attr($instance['title']);
$content = esc_attr($instance['content']); ?>
<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'membership-manager'); ?>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</label></p>
<p><label for="<?php echo $this->get_field_id('content'); ?>"><?php _e('Content:', 'membership-manager'); ?>
<textarea class="widefat" id="<?php echo $this->get_field_id('content'); ?>" name="<?php echo $this->get_field_name('content'); ?>" cols="20" rows="10"><?php echo $content; ?></textarea>
</label></p><?php }

function update($instance, $old_instance) {
include 'initial-options.php';
foreach ($instance as $key => $value) { if ($value == '') { $instance[$key] = $initial_options['meta_widget'][$key]; } }
return $instance; }

function widget($args, $instance) {
extract($args);
include 'initial-options.php';
foreach ($instance as $key => $value) { if ($value == '') { $instance[$key] = $initial_options['meta_widget'][$key]; } }
$title = apply_filters('widget_title', $instance['title']);
$content = apply_filters('widget_text', $instance['content']);
echo $before_widget.$before_title.$title.$after_title.$content.$after_widget; }
}

add_action('widgets_init', create_function('', 'return register_widget("Membership_Meta_Widget");'));


for ($i = 0; $i < 4; $i++) {
foreach (array('membership-content', 'membership-counter') as $tag) {
add_shortcode($tag.($i == 0 ? '' : $i), create_function('$atts, $content', 'include_once dirname(__FILE__)."/shortcodes.php"; return '.str_replace('-', '_', $tag).'($atts, $content);')); } }

foreach (array(
'' => '',
'login-' => '$atts["type"] = "login"; ',
'login-compact-' => '$atts["size"] = "compact"; $atts["type"] = "login"; ',
'login-widget-' => '$atts["size"] = "compact"; $atts["type"] = "login"; ',
'password-reset-' => '$atts["type"] = "password_reset"; ',
'profile-' => '$atts["type"] = "profile"; ',
'profile-edit-' => '$atts["type"] = "profile"; ',
'registration-' => '$atts["type"] = "registration"; ',
'registration-compact-' => '$atts["size"] = "compact"; $atts["type"] = "registration"; ') as $key => $value) {
add_shortcode('membership-'.$key.'form', create_function('$atts', $value.'include_once dirname(__FILE__)."/forms.php"; return membership_form($atts);')); }

foreach (array('membership-activation-url', 'membership-comments') as $tag) {
add_shortcode($tag, create_function('$atts', 'include_once dirname(__FILE__)."/shortcodes.php"; return '.str_replace('-', '_', $tag).'($atts);')); }

add_shortcode('user', 'membership_user_data');
add_shortcode('membership-manager', 'membership_data');
add_shortcode('member-area', 'member_area_data');
add_shortcode('membership-user', 'member_data');
add_shortcode('member', 'member_data');


foreach (array(
'get_the_excerpt',
'get_the_title',
'single_post_title',
'the_excerpt',
'the_excerpt_rss',
'the_title',
'the_title_attribute',
'the_title_rss',
'widget_text',
'widget_title') as $function) { add_filter($function, 'do_shortcode'); }