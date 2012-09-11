<?php function membership_form($atts) {
extract(shortcode_atts(array('focus' => '', 'id' => '', 'redirection' => '', 'size' => '', 'type' => ''), $atts));
$type = str_replace('-', '_', format_nice_name($type));
switch ($type) {
case 'login': case 'password_reset': case 'registration': $condition = !membership_session(''); break;
case 'profile': $condition = membership_session(''); break;
default: $condition = true; }
if ($condition) {
global $post, $wpdb;
$members_areas = $id;
$focus = format_nice_name($focus);
$size = str_replace('-', '_', format_nice_name($size));
$id = '_'.$type.($size == '' ? '' : '_'.$size);
$prefix = 'membership_form'.$id.'_';
$_GET['membership_form_id'] = $id;
$_GET['membership_form_type'] = $type;
if ($redirection == '#') { $redirection .= 'membership-form'.str_replace('_', '-', $id); }
foreach (array(
'strip_accents_js',
'format_email_address_js') as $function) { add_action('wp_footer', $function); }
$code = get_option('membership_manager'.$id.'_form_code');
$tags = array('indicator');
add_shortcode('indicator', 'membership_form_indicator');
$code = do_shortcode($code);
$tags = array_merge($tags, array('captcha', 'input', 'label', 'option', 'select', 'textarea'));
foreach ($tags as $tag) { add_shortcode($tag, 'membership_form_'.str_replace('-', '_', $tag)); }
if (!isset($_POST['referring_url'])) { $_POST['referring_url'] = htmlspecialchars($_SERVER['HTTP_REFERER']); }
if (isset($_POST[$prefix.'submit'])) {
foreach ($_POST as $key => $value) {
if (is_string($value)) {
$value = str_replace(array('[', ']'), array('&#91;', '&#93;'), quotes_entities($value));
$_POST[$key] = str_replace('\\&', '&', trim(mysql_real_escape_string($value))); } }
$_POST[$prefix.'email_address'] = format_email_address($_POST[$prefix.'email_address']);
$_POST[$prefix.'first_name'] = format_name($_POST[$prefix.'first_name']);
$_POST[$prefix.'last_name'] = format_name($_POST[$prefix.'last_name']);
$_POST[$prefix.'login'] = format_email_address($_POST[$prefix.'login']);
$_POST[$prefix.'website_url'] = format_url($_POST[$prefix.'website_url']);
$_POST['referring_url'] = html_entity_decode($_POST['referring_url']); }
elseif (membership_session('')) {
$_GET['member_data'] = (array) $_GET['member_data'];
if ($_GET['member_data']['login'] != $_SESSION['membership_login']) {
$_GET['member_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$_SESSION['membership_login']."'", OBJECT); }
foreach ($_GET['member_data'] as $key => $value) { if ($key != 'password') { $_POST[$prefix.$key] = member_data($key); } } }
switch ($type) {
case 'login': $_GET[$prefix.'required_fields'] = array('login', 'password'); break;
case 'password_reset': $_GET[$prefix.'required_fields'] = array('email_address'); break;
case 'registration': $_GET[$prefix.'required_fields'] = array('login', 'password', 'email_address'); break;
default: $_GET[$prefix.'required_fields'] = array(); }
$_GET[$prefix.'fields'] = $_GET[$prefix.'required_fields'];
$_GET[$prefix.'checkbox_fields'] = array();
$options = (array) get_option('membership_manager'.$id.'_form');
foreach ($options as $key => $value) {
if ($value == '') { $value = membership_data($key); }
else { $value = quotes_entities_decode(do_shortcode($value)); }
$options[$key] = $value; }
foreach (array('invalid_email_address_message', 'unfilled_field_message') as $key) {
if ($options[$key] != '') { $_GET[$prefix.$key] = $options[$key]; }
else { $_GET[$prefix.$key] = membership_data($key); } }
$code = do_shortcode($code);
foreach (array('fields', 'required_fields') as $array) { $_GET[$prefix.$array] = array_unique($_GET[$prefix.$array]); }

if (isset($_POST[$prefix.'submit'])) {
$_GET['form_error'] = '';
foreach ($_GET[$prefix.'required_fields'] as $field) {
if ($_POST[$prefix.$field] == '') {
$_GET[$prefix.'unfilled_fields_error'] = ($options['unfilled_fields_message'] != '' ? $options['unfilled_fields_message'] : membership_data('unfilled_fields_message'));
$_GET['form_error'] = 'yes'; } }
if (isset($_GET[$prefix.'recaptcha_js'])) {
$resp = recaptcha_check_answer(RECAPTCHA_PRIVATE_KEY, $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);
if (!$resp->is_valid) { $invalid_captcha = 'yes'; } }
elseif (in_array('captcha', $_GET[$prefix.'fields'])) {
if (hash('sha256', $_POST[$prefix.'captcha']) != $_POST[$prefix.'valid_captcha']) { $invalid_captcha = 'yes'; } }
if ($invalid_captcha == 'yes') {
$_GET[$prefix.'invalid_captcha_error'] = ($options['invalid_captcha_message'] != '' ? $options['invalid_captcha_message'] : membership_data('invalid_captcha_message'));
$_GET['form_error'] = 'yes'; }
if ($_GET['form_error'] == '') {
foreach ($_POST as $key => $value) { $_POST[str_replace($prefix, '', $key)] = $value; }

switch ($type) {
case 'login':
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$_POST['login']."' AND password = '".hash('sha256', $_POST['password'])."'", OBJECT);
if (!$result) { $_GET[$prefix.'invalid_login_or_password_error'] = $options['invalid_login_or_password_message']; $_GET['form_error'] = 'yes'; }
elseif ($result->status != 'active') { $_GET[$prefix.'inactive_account_error'] = $options['inactive_account_message']; $_GET['form_error'] = 'yes'; }
if ($_GET['form_error'] == '') {
$plugins = array('membership');
$_GET['member_data'] = (array) $result;
if (!headers_sent()) { session_start(); }
$_SESSION['membership_login'] = $_POST['login'];
if ((function_exists('commerce_session')) && (!commerce_session())) {
$plugins[1] = 'commerce';
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_clients WHERE email_address = '".member_data('email_address')."' AND status = 'active'", OBJECT);
if ($result) { $_GET['client_data'] = (array) $result; $_SESSION['commerce_login'] = $result->login; } else { unset($plugins[1]); } }
if ((function_exists('affiliation_session')) && (!affiliation_session())) {
$plugins[2] = 'affiliation';
$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".member_data('email_address')."' AND status = 'active'", OBJECT);
if ($result) { $_GET['affiliate_data'] = (array) $result; $_SESSION['affiliation_login'] = $result->login; } else { unset($plugins[2]); } }
if (!is_user_logged_in()) { wp_signon(array('user_login' => $_POST['login'], 'user_password' => $_POST['password'], 'remember' => (isset($_POST['remember'])))); }
if (isset($_POST['remember'])) {
$T = time() + 90*86400;
if (!headers_sent()) { foreach ($plugins as $plugin) { setcookie($plugin.'_login', $_SESSION[$plugin.'_login'].hash('sha256', $_SESSION[$plugin.'_login'].AUTH_KEY), $T, '/'); } }
else {
$expiration_date = date('D', $T).', '.date('d', $T).' '.date('M', $T).' '.date('Y', $T).' '.date('H:i:s', $T).' UTC';
foreach ($plugins as $plugin) { add_action('wp_footer', create_function('', 'echo "<script type=\"text/javascript\">document.cookie=\"'.$plugin.'_login='.$_SESSION[$plugin.'_login'].hash('sha256', $_SESSION[$plugin.'_login'].AUTH_KEY).'; expires='.$expiration_date.'; path=/\";</script>";')); } } }
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = str_replace(array('&#91;', '&#93;'), array('[', ']'), membership_data('login_notification_email_'.$field)); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); }
if (membership_data('login_custom_instructions_executed') == 'yes') {
eval(format_instructions(membership_data('login_custom_instructions'))); }
if ($redirection == '') { $redirection = $_SERVER['REQUEST_URI']; } } break;

case 'password_reset':
$result = $wpdb->get_row("SELECT email_address FROM ".$wpdb->prefix."membership_manager_members WHERE email_address = '".$_POST['email_address']."'", OBJECT);
if (!$result) { $_GET[$prefix.'inexistent_email_address_error'] = $options['inexistent_email_address_message']; $_GET['form_error'] = 'yes'; }
else {
$_POST['password'] = substr(md5(mt_rand()), 0, 8);
$results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_members SET password = '".hash('sha256', $_POST['password'])."' WHERE email_address = '".$_POST['email_address']."'");
$_GET['member_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members WHERE email_address = '".$_POST['email_address']."'", OBJECT);
$original['member_data'] = $_GET['member_data'];
$_GET['member_data']['password'] = $_POST['password'];
foreach (array('sender', 'receiver', 'subject', 'body') as $field) {
$$field = str_replace(array('&#91;', '&#93;'), array('[', ']'), membership_data('password_reset_email_'.$field)); }
wp_mail($receiver, $subject, $body, 'From: '.$sender);
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = str_replace(array('&#91;', '&#93;'), array('[', ']'), membership_data('password_reset_notification_email_'.$field)); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); }
if (membership_data('password_reset_custom_instructions_executed') == 'yes') {
eval(format_instructions(membership_data('password_reset_custom_instructions'))); }
$_GET['member_data'] = $original['member_data']; } break;

case 'profile':
if (($_POST['login'] != '') && ($_POST['login'] != $_SESSION['membership_login'])) {
if (is_numeric($_POST['login'])) { $_GET[$prefix.'numeric_login_error'] = $options['numeric_login_message']; $_GET['form_error'] = 'yes'; $login_error = 'yes'; }
if (strlen($_POST['login']) < membership_data('minimum_login_length')) { $_GET[$prefix.'too_short_login_error'] = $options['too_short_login_message']; $_GET['form_error'] = 'yes'; $login_error = 'yes'; }
elseif (strlen($_POST['login']) > membership_data('maximum_login_length')) { $_GET[$prefix.'too_long_login_error'] = $options['too_long_login_message']; $_GET['form_error'] = 'yes'; $login_error = 'yes'; }
$result = $wpdb->get_results("SELECT login FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$_POST['login']."'", OBJECT);
if ($result) { $_GET[$prefix.'unavailable_login_error'] = $options['unavailable_login_message']; $_GET['form_error'] = 'yes'; $login_error = 'yes'; }
if ($login_error == '') {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_members SET login = '".$_POST['login']."' WHERE login = '".$_SESSION['membership_login']."'");
if (!headers_sent()) { session_start(); }
$_SESSION['membership_login'] = $_POST['login'];
if (isset($_COOKIE['membership_login'])) {
$T = time() + 90*86400;
if (!headers_sent()) { setcookie('membership_login', $_POST['login'].hash('sha256', $_POST['login'].AUTH_KEY), $T, '/'); }
else {
$expiration_date = date('D', $T).', '.date('d', $T).' '.date('M', $T).' '.date('Y', $T).' '.date('H:i:s', $T).' UTC';
$content .= '<script type="text/javascript">document.cookie="membership_login='.$_POST['login'].hash('sha256', $_POST['login'].AUTH_KEY).'; expires='.$expiration_date.'";</script>'; } } } }
if ($_POST['password'] != '') {
if (strlen($_POST['password']) < membership_data('minimum_password_length')) { $_GET[$prefix.'too_short_password_error'] = $options['too_short_password_message']; $_GET['form_error'] = 'yes'; }
elseif (strlen($_POST['password']) > membership_data('maximum_password_length')) { $_GET[$prefix.'too_long_password_error'] = $options['too_long_password_message']; $_GET['form_error'] = 'yes'; }
else { $results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_members SET password = '".hash('sha256', $_POST['password'])."' WHERE login = '".$_SESSION['membership_login']."'"); } }
if ($_POST['email_address'] != '') {
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."membership_manager_members WHERE email_address = '".$_POST['email_address']."'", OBJECT);
if (($result) && ($result->login != $_SESSION['membership_login'])) { $_GET[$prefix.'unavailable_email_address_error'] = $options['unavailable_email_address_message']; $_GET['form_error'] = 'yes'; }
elseif (!$result) {
$member = $wpdb->get_row("SELECT email_address FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$_SESSION['membership_login']."'", OBJECT);
$results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_members SET email_address = '".$_POST['email_address']."' WHERE login = '".$_SESSION['membership_login']."'");
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."commerce_manager_clients WHERE email_address = '".$_POST['email_address']."'", OBJECT);
if (!$result) { $results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_clients SET email_address = '".$_POST['email_address']."' WHERE email_address = '".$member->email_address."'"); }
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."affiliation_manager_affiliates WHERE email_address = '".$_POST['email_address']."'", OBJECT);
if (!$result) { $results = $wpdb->query("UPDATE ".$wpdb->prefix."affiliation_manager_affiliates SET email_address = '".$_POST['email_address']."' WHERE email_address = '".$member->email_address."'"); }
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->base_prefix."users WHERE user_email = '".$_POST['email_address']."'", OBJECT);
if (!$result) { $results = $wpdb->query("UPDATE ".$wpdb->base_prefix."users SET user_email = '".$_POST['email_address']."' WHERE user_email = '".$member->email_address."'"); } } }
$list = '';
include dirname(__FILE__).'/tables.php';
$sql = membership_sql_array($tables['members'], $_POST);
include dirname(__FILE__).'/libraries/personal-informations.php';
foreach ($personal_informations as $field) {
if ((in_array($field, $_GET[$prefix.'fields']))
 && (!in_array($field, array('login', 'email_address')))) { $list .= $field." = ".$sql[$field].","; } }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_members SET ".substr($list, 0, -1)." WHERE login = '".$_SESSION['membership_login']."'");
$_GET['member_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$_SESSION['membership_login']."'", OBJECT);
$original['member_data'] = $_GET['member_data'];
$_GET['member_data']['password'] = $_POST['password'];
foreach (array('sent', 'sender', 'receiver', 'subject', 'body') as $field) {
$$field = str_replace(array('&#91;', '&#93;'), array('[', ']'), membership_data('profile_edit_notification_email_'.$field)); }
if ($sent == 'yes') { wp_mail($receiver, $subject, $body, 'From: '.$sender); }
if (membership_data('profile_edit_custom_instructions_executed') == 'yes') {
eval(format_instructions(membership_data('profile_edit_custom_instructions'))); }
$_GET['member_data'] = $original['member_data'];
foreach ($_GET['member_data'] as $key => $value) { if ($key != 'password') { $_POST[$prefix.$key] = member_data($key); } }
$code = do_shortcode(get_option('membership_manager'.$id.'_form_code'));
foreach (array('fields', 'required_fields') as $array) { $_GET[$prefix.$array] = array_unique($_GET[$prefix.$array]); } break;

case 'registration':
if (is_numeric($_POST['login'])) { $_GET[$prefix.'numeric_login_error'] = $options['numeric_login_message']; $_GET['form_error'] = 'yes'; }
if (strlen($_POST['login']) < membership_data('minimum_login_length')) { $_GET[$prefix.'too_short_login_error'] = $options['too_short_login_message']; $_GET['form_error'] = 'yes'; }
elseif (strlen($_POST['login']) > membership_data('maximum_login_length')) { $_GET[$prefix.'too_long_login_error'] = $options['too_long_login_message']; $_GET['form_error'] = 'yes'; }
if (strlen($_POST['password']) < membership_data('minimum_password_length')) { $_GET[$prefix.'too_short_password_error'] = $options['too_short_password_message']; $_GET['form_error'] = 'yes'; }
elseif (strlen($_POST['password']) > membership_data('maximum_password_length')) { $_GET[$prefix.'too_long_password_error'] = $options['too_long_password_message']; $_GET['form_error'] = 'yes'; }
$result = $wpdb->get_results("SELECT login FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$_POST['login']."'", OBJECT);
if ($result) { $_GET[$prefix.'unavailable_login_error'] = $options['unavailable_login_message']; $_GET['form_error'] = 'yes'; }
$result = $wpdb->get_results("SELECT email_address FROM ".$wpdb->prefix."membership_manager_members WHERE email_address = '".$_POST['email_address']."'", OBJECT);
if ($result) { $_GET[$prefix.'unavailable_email_address_error'] = $options['unavailable_email_address_message']; $_GET['form_error'] = 'yes'; }
if ($_GET['form_error'] == '') {
$members_areas = array_unique(preg_split('#[^0-9]#', $members_areas, 0, PREG_SPLIT_NO_EMPTY));
sort($members_areas, SORT_NUMERIC);
foreach ($members_areas as $member_area) { $members_areas_list .= $member_area.', '; }
$_POST['members_areas'] = substr($members_areas_list, 0, -2);
if (count($members_areas) == 1) { $_GET['member_area_id'] = (int) $members_areas[0]; } else { unset($_GET['member_area_id']); }
$_POST['category_id'] = member_area_data('members_initial_category_id');
$_POST['status'] = member_area_data('members_initial_status');
$_POST['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
$_POST['ip_address'] = $_SERVER['REMOTE_ADDR'];
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$_POST['date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['date_utc'] = date('Y-m-d H:i:s');
$_GET['user_id'] = get_current_user_id();
add_member($_POST);
if (substr($redirection, 0, 1) != '#') {
if ($redirection != '') { $redirection = format_url($redirection); }
else { $redirection = membership_data('registration_confirmation_url'); }
if (!headers_sent()) { header('Location: '.$redirection); exit; }
else { $content .= '<script type="text/javascript">window.location = \''.htmlspecialchars($redirection).'\';</script>'; } } } break; }

if (($type != 'registration') && ($_GET['form_error'] == '')) {
if (($redirection != '') && (substr($redirection, 0, 1) != '#')) {
$redirection = format_url($redirection);
if (!headers_sent()) { header('Location: '.$redirection); exit; }
else { add_action('wp_footer', create_function('', 'echo "<script type=\"text/javascript\">window.location = \"'.htmlspecialchars($redirection).'\";</script>";')); } } } } }

foreach ($_GET[$prefix.'required_fields'] as $field) {
$required_fields_js .= '
if '.(in_array($field, $_GET[$prefix.'checkbox_fields']) ? '(form.'.$prefix.$field.'.checked == false)' : '(form.'.$prefix.$field.'.value == "")').' {
if (document.getElementById("'.$prefix.$field.'_error")) { document.getElementById("'.$prefix.$field.'_error").innerHTML = "'.$_GET[$prefix.'unfilled_field_message'].'"; }
if (!error) { form.'.$prefix.$field.'.focus(); } error = true; }
else if (document.getElementById("'.$prefix.$field.'_error")) { document.getElementById("'.$prefix.$field.'_error").innerHTML = ""; }'; }
$form_js = '
<script type="text/javascript">
'.($focus == 'yes' ? $_GET['form_focus'] : '').'
function validate_membership_form'.$id.'(form) {
var error = false;
'.(in_array('email_address', $_GET[$prefix.'fields']) ? 'form.'.$prefix.'email_address.value = format_email_address(form.'.$prefix.'email_address.value);' : '').'
'.(in_array('login', $_GET[$prefix.'fields']) ? 'form.'.$prefix.'login.value = format_email_address(form.'.$prefix.'login.value);' : '').'
'.$required_fields_js.'
'.((($type == 'profile') || ($type == 'registration')) ? '
if ((form.'.$prefix.'login.value != "") && (form.'.$prefix.'login.value != "'.$_SESSION['membership_login'].'")) {
if ('.membership_data('minimum_login_length').' > form.'.$prefix.'login.value.length) {
if (document.getElementById("'.$prefix.'login_error")) { document.getElementById("'.$prefix.'login_error").innerHTML = "'.$options['too_short_login_message'].'"; }
if (!error) { form.'.$prefix.'login.focus(); } error = true; }
else {
if (document.getElementById("'.$prefix.'login_error")) { document.getElementById("'.$prefix.'login_error").innerHTML = ""; }
if (form.'.$prefix.'login.value.length > '.membership_data('maximum_login_length').') {
if (document.getElementById("'.$prefix.'login_error")) { document.getElementById("'.$prefix.'login_error").innerHTML = "'.$options['too_long_login_message'].'"; }
if (!error) { form.'.$prefix.'login.focus(); } error = true; }
else if (document.getElementById("'.$prefix.'login_error")) { document.getElementById("'.$prefix.'login_error").innerHTML = ""; } } }
if (form.'.$prefix.'password.value != "") {
if ('.membership_data('minimum_password_length').' > form.'.$prefix.'password.value.length) {
if (document.getElementById("'.$prefix.'password_error")) { document.getElementById("'.$prefix.'password_error").innerHTML = "'.$options['too_short_password_message'].'"; }
if (!error) { form.'.$prefix.'password.focus(); } error = true; }
else {
if (document.getElementById("'.$prefix.'password_error")) { document.getElementById("'.$prefix.'password_error").innerHTML = ""; }
if (form.'.$prefix.'password.value.length > '.membership_data('maximum_password_length').') {
if (document.getElementById("'.$prefix.'password_error")) { document.getElementById("'.$prefix.'password_error").innerHTML = "'.$options['too_long_password_message'].'"; }
if (!error) { form.'.$prefix.'password.focus(); } error = true; }
else if (document.getElementById("'.$prefix.'password_error")) { document.getElementById("'.$prefix.'password_error").innerHTML = ""; } } }' : '').'
'.(in_array('email_address', $_GET[$prefix.'fields']) ? '
if (form.'.$prefix.'email_address.value != "") {
if ((form.'.$prefix.'email_address.value.indexOf("@") == -1) || (form.'.$prefix.'email_address.value.indexOf(".") == -1)) {
if (document.getElementById("'.$prefix.'email_address_error")) { document.getElementById("'.$prefix.'email_address_error").innerHTML = "'.$_GET[$prefix.'invalid_email_address_message'].'"; }
if (!error) { form.'.$prefix.'email_address.focus(); } error = true; }
else if (document.getElementById("'.$prefix.'email_address_error")) { document.getElementById("'.$prefix.'email_address_error").innerHTML = ""; } }' : '').'
return !error; }
</script>';

$tags = array_merge($tags, array('error', 'validation-content'));
foreach ($tags as $tag) { add_shortcode($tag, 'membership_form_'.str_replace('-', '_', $tag)); }
if (!stristr($code, '<form')) { $code = '<form id="membership-form'.str_replace('_', '-', $id).'" method="post" action="'.htmlspecialchars($_SERVER['REQUEST_URI']).(substr($redirection, 0, 1) == '#' ? $redirection : '').'" onsubmit="return validate_membership_form'.$id.'(this);">'.$code; }
if (!stristr($code, '</form>')) { $code .= '<div style="display: none;"><input type="hidden" name="referring_url" value="'.$_POST['referring_url'].'" /><input type="hidden" name="'.$prefix.'submit" value="yes" /></div></form>'; }
$code = str_replace(array("\\t", '\\'), array('	', ''), str_replace(array("\\r\\n", "\\n", "\\r"), '
', do_shortcode($code)));
$content .= $_GET[$prefix.'recaptcha_js'].$code.$form_js;

foreach ($tags as $tag) { remove_shortcode($tag); }
return $content; } }


function membership_form_captcha($atts) {
$form_id = $_GET['membership_form_id'];
$prefix = 'membership_form'.$form_id.'_';
$attributes = array(
'class' => 'captcha',
'dir' => '',
'onclick' => '',
'ondblclick' => '',
'onkeydown' => '',
'onkeypress' => '',
'onkeyup' => '',
'onmousedown' => '',
'onmousemove' => '',
'onmouseout' => '',
'onmouseover' => '',
'onmouseup' => '',
'style' => '',
'theme' => membership_data('default_recaptcha_theme'),
'title' => '',
'type' => membership_data('default_captcha_type'),
'xmlns' => '');
foreach ($attributes as $key => $value) {
if ($atts[$key] == '') { $atts[$key] = $attributes[$key]; }
if ((is_string($key)) && ($key != 'theme') && ($key != 'type') && ($atts[$key] != '')) { $markup .= ' '.$key.'="'.$atts[$key].'"'; } }
if ($atts['type'] == 'recaptcha') {
$_GET[$prefix.'recaptcha_js'] = '<script type="text/javascript">var RecaptchaOptions = { lang: \''.strtolower(substr(WPLANG, 0, 2)).'\', theme: \''.$atts['theme'].'\' };</script>'."\n";
if (!function_exists('_recaptcha_qsencode')) { include_once dirname(__FILE__).'/libraries/recaptchalib.php'; }
foreach (array('public', 'private') as $string) {
if (!defined('RECAPTCHA_'.strtoupper($string).'_KEY')) {
$key = membership_data('recaptcha_'.$string.'_key');
if (($key == '') && (function_exists('commerce_data'))) { $key = commerce_data('recaptcha_'.$string.'_key'); }
define('RECAPTCHA_'.strtoupper($string).'_KEY', $key); } }
$content = str_replace(' frameborder="0"', '', recaptcha_get_html(RECAPTCHA_PUBLIC_KEY)); }
else {
switch ($atts['type']) {
case 'arithmetic':
$captchas_numbers = get_option('membership_manager_captchas_numbers');
$m = mt_rand(0, 15);
$n = mt_rand(0, 15);
$string = $captchas_numbers[$m].' + '.$captchas_numbers[$n];
$valid_captcha = $m + $n; break;
case 'reversed-string':
include dirname(__FILE__).'/libraries/captchas.php';
$n = mt_rand(5, 12);
for ($i = 0; $i < $n; $i++) { $string .= $captchas_letters[mt_rand(0, 25)]; }
$valid_captcha = strrev($string); break; }
$content = '<label for="'.$prefix.'captcha"><span'.$markup.'>'.$string.'</span></label>
<input type="hidden" name="'.$prefix.'valid_captcha" value="'.hash('sha256', $valid_captcha).'" />'; }
return $content; }


function membership_form_error($atts) {
$form_id = $_GET['membership_form_id'];
$prefix = 'membership_form'.$form_id.'_';
$attributes = array(
0 => 'email_address',
'class' => 'error',
'dir' => '',
'onclick' => '',
'ondblclick' => '',
'onkeydown' => '',
'onkeypress' => '',
'onkeyup' => '',
'onmousedown' => '',
'onmousemove' => '',
'onmouseout' => '',
'onmouseover' => '',
'onmouseup' => '',
'style' => '',
'title' => '',
'xmlns' => '');
foreach ($attributes as $key => $value) {
if ($atts[$key] == '') { $atts[$key] = $attributes[$key]; }
if ((is_string($key)) && ($atts[$key] != '')) { $markup .= ' '.$key.'="'.$atts[$key].'"'; } }
$name = str_replace('-', '_', format_nice_name($atts[0]));
return '<span id="'.$prefix.$name.'_error"'.$markup.'>'.$_GET[$prefix.$name.'_error'].'</span>'; }


function membership_form_indicator($atts) {
add_action('wp_footer', 'membership_jquery_js');
$form_id = $_GET['membership_form_id'];
$prefix = 'membership_form'.$form_id.'_';
$attributes = array(
0 => 'login',
'class' => 'indicator',
'dir' => '',
'onclick' => '',
'ondblclick' => '',
'onkeydown' => '',
'onkeypress' => '',
'onkeyup' => '',
'onmousedown' => '',
'onmousemove' => '',
'onmouseout' => '',
'onmouseover' => '',
'onmouseup' => '',
'style' => '',
'title' => '',
'xmlns' => '');
foreach ($attributes as $key => $value) {
if ($atts[$key] == '') { $atts[$key] = $attributes[$key]; }
if ((is_string($key)) && ($atts[$key] != '')) { $markup .= ' '.$key.'="'.$atts[$key].'"'; } }
$name = str_replace('-', '_', format_nice_name($atts[0]));
if ($name == 'login') { $_GET[$prefix.$name.'_onchange'] = "$.get('".MEMBERSHIP_MANAGER_URL."?action=check-login&amp;form_id=".$form_id."', { login: $('#".$prefix.$name."').val() }, function(data) { $('#".$prefix.$name."_indicator').html(data); });"; }
return '<span id="'.$prefix.$name.'_indicator"'.$markup.'>'.$_GET[$prefix.$name.'_indicator'].'</span>'; }


function membership_form_input($atts) {
$form_id = $_GET['membership_form_id'];
$form_type = $_GET['membership_form_type'];
$prefix = 'membership_form'.$form_id.'_';
$attributes = array(
0 => 'submit',
'accept' => '',
'accesskey' => '',
'alt' => '',
'checked' => '',
'class' => '',
'dir' => '',
'disabled' => '',
'maxlength' => '',
'onblur' => '',
'onchange' => '',
'onclick' => '',
'ondblclick' => '',
'onfocus' => '',
'onkeydown' => '',
'onkeypress' => '',
'onkeyup' => '',
'onmousedown' => '',
'onmousemove' => '',
'onmouseout' => '',
'onmouseover' => '',
'onmouseup' => '',
'onselect' => '',
'readonly' => '',
'required' => 'no',
'size' => '',
'src' => '',
'style' => '',
'tabindex' => '',
'title' => '',
'type' => '',
'usemap' => '',
'value' => '',
'xmlns' => '');
foreach ($attributes as $key => $value) { if ($atts[$key] == '') { $atts[$key] = $attributes[$key]; } }

$name = str_replace('-', '_', format_nice_name($atts[0]));
$_GET[$prefix.'fields'][] = $name;
if (in_array($name, $_GET[$prefix.'required_fields'])) { $atts['required'] = 'yes'; }
switch ($name) {
case 'password': if ($atts['type'] == '') { $atts['type'] = 'password'; } break;
case 'remember': if ($atts['type'] == '') { $atts['type'] = 'checkbox'; } break;
case 'submit': if ($atts['type'] == '') { $atts['type'] = 'submit'; } break;
default: if ($atts['type'] == '') { $atts['type'] = 'text'; } }
switch ($atts['type']) {
case 'checkbox': $_GET[$prefix.'checkbox_fields'][] = $name; break;
case 'password': case 'text':
if ($atts['size'] == '') { $atts['size'] = '30'; }
$id_markup = ' id="'.$prefix.$name.'"'; break; }
if ($name == 'email_address') {
if ($atts['onmouseout'] == '') { $atts['onmouseout'] = "this.value = format_email_address(this.value);"; }
if (isset($_POST[$prefix.'submit'])) {
if ((!strstr($_POST[$prefix.$name], '@')) || (!strstr($_POST[$prefix.$name], '.'))) {
$_GET[$prefix.$name.'_error'] = $_GET[$prefix.'invalid_email_address_message']; } } }
if ($name == 'login') {
if ($atts['onchange'] == '') { $atts['onchange'] = $_GET[$prefix.$name.'_onchange']; }
if ($atts['onmouseout'] == '') { $atts['onmouseout'] = "this.value = format_email_address(this.value);"; } }
if ($name != 'submit') {
if ($_POST[$prefix.$name] != '') { $atts['value'] = $_POST[$prefix.$name]; }
elseif ((isset($_POST[$prefix.'submit'])) && ($atts['required'] == 'yes')) { $_GET[$prefix.$name.'_error'] = $_GET[$prefix.'unfilled_field_message']; } }
foreach (array($name, str_replace('_', '-', $name)) as $key) {
if ($atts['value'] == '') { $atts['value'] = utf8_encode(htmlspecialchars($_GET[$key])); } }
if ((!isset($_POST[$prefix.'submit'])) && ($atts['value'] == '')) {
include dirname(__FILE__).'/libraries/personal-informations.php';
if (in_array($name, $personal_informations)) {
if ((function_exists('affiliation_session')) && (affiliation_session())) { $atts['value'] = affiliate_data($name); }
elseif ((function_exists('commerce_session')) && (commerce_session())) { $atts['value'] = client_data($name); }
elseif ((function_exists('is_user_logged_in')) && (is_user_logged_in())) { $atts['value'] = membership_user_data($name); } } }
if ($name == 'password') {
if ((isset($_POST[$prefix.'submit'])) && (($form_type == 'login') || ($form_type == 'profile'))) { $atts['value'] = ''; } }
if (($_GET['form_focus'] == '') && ($atts['value'] == '') && ($id_markup != '')) { $_GET['form_focus'] = 'document.getElementById("'.$prefix.$name.'").focus();'; }
if ((isset($_POST[$prefix.'submit'])) && ($atts['type'] == 'checkbox')) { $atts['checked'] = (isset($_POST[$prefix.$name]) ? 'checked' : ''); }
$atts['value'] = quotes_entities($atts['value']);
foreach ($attributes as $key => $value) {
switch ($key) {
case 'required': if (($name != 'submit') && ($atts['required'] == 'yes')) { $_GET[$prefix.'required_fields'][] = $name; } break;
default: if ((is_string($key)) && ($atts[$key] != '')) { $markup .= ' '.$key.'="'.$atts[$key].'"'; } } }

return '<input name="'.$prefix.$name.'"'.$id_markup.$markup.' />'; }


function membership_form_label($atts, $content) {
$form_id = $_GET['membership_form_id'];
$prefix = 'membership_form'.$form_id.'_';
$attributes = array(
0 => 'email_address',
'accesskey' => '',
'class' => '',
'dir' => '',
'id' => '',
'onblur' => '',
'onclick' => '',
'ondblclick' => '',
'onfocus' => '',
'onkeydown' => '',
'onkeypress' => '',
'onkeyup' => '',
'onmousedown' => '',
'onmousemove' => '',
'onmouseout' => '',
'onmouseover' => '',
'onmouseup' => '',
'style' => '',
'title' => '',
'xmlns' => '');
foreach ($attributes as $key => $value) {
if ($atts[$key] == '') { $atts[$key] = $attributes[$key]; }
if ((is_string($key)) && ($atts[$key] != '')) { $markup .= ' '.$key.'="'.$atts[$key].'"'; } }
$name = str_replace('-', '_', format_nice_name($atts[0]));
return '<label for="'.$prefix.$name.'"'.$markup.'>'.do_shortcode($content).'</label>'; }


function membership_form_option($atts, $content) {
$form_id = $_GET['membership_form_id'];
$prefix = 'membership_form'.$form_id.'_';
$attributes = array(
'class' => '',
'dir' => '',
'disabled' => '',
'id' => '',
'label' => '',
'onclick' => '',
'ondblclick' => '',
'onkeydown' => '',
'onkeypress' => '',
'onkeyup' => '',
'onmousedown' => '',
'onmousemove' => '',
'onmouseout' => '',
'onmouseover' => '',
'onmouseup' => '',
'selected' => '',
'style' => '',
'title' => '',
'value' => '',
'xmlns' => '');
foreach ($attributes as $key => $value) { if ($atts[$key] == '') { $atts[$key] = $attributes[$key]; } }

$content = do_shortcode($content);
$name = $_GET['membership_field_name'];
if ($atts['value'] == '') { $atts['value'] = $content; }
if ((isset($_POST[$prefix.'submit'])) || ($atts['selected'] == '')) { $atts['selected'] = ($_POST[$prefix.$name] == $atts['value'] ? 'selected' : ''); }
$atts['value'] = quotes_entities($atts['value']);
foreach ($attributes as $key => $value) { if ((is_string($key)) && ($atts[$key] != '')) { $markup .= ' '.$key.'="'.$atts[$key].'"'; } }

return '<option'.$markup.'>'.$content.'</option>'; }


function membership_form_select($atts, $content) {
$form_id = $_GET['membership_form_id'];
$prefix = 'membership_form'.$form_id.'_';
$attributes = array(
0 => 'country',
'class' => '',
'dir' => '',
'disabled' => '',
'multiple' => '',
'onblur' => '',
'onchange' => '',
'onclick' => '',
'ondblclick' => '',
'onfocus' => '',
'onkeydown' => '',
'onkeypress' => '',
'onkeyup' => '',
'onmousedown' => '',
'onmousemove' => '',
'onmouseout' => '',
'onmouseover' => '',
'onmouseup' => '',
'required' => 'no',
'size' => '',
'style' => '',
'tabindex' => '',
'title' => '',
'xmlns' => '');
foreach ($attributes as $key => $value) { if ($atts[$key] == '') { $atts[$key] = $attributes[$key]; } }

$name = str_replace('-', '_', format_nice_name($atts[0]));
$_GET['membership_field_name'] = $name;
$_GET[$prefix.'fields'][] = $name;
if (in_array($name, $_GET[$prefix.'required_fields'])) { $atts['required'] = 'yes'; }
foreach (array($name, str_replace('_', '-', $name)) as $key) {
if ($_POST[$prefix.$name] == '') { $_POST[$prefix.$name] = utf8_encode(htmlspecialchars($_GET[$key])); } }
if ((!isset($_POST[$prefix.'submit'])) && ($_POST[$prefix.$name] == '')) {
include dirname(__FILE__).'/libraries/personal-informations.php';
if (in_array($name, $personal_informations)) {
if ((function_exists('affiliation_session')) && (affiliation_session())) { $_POST[$prefix.$name] = affiliate_data($name); }
elseif ((function_exists('commerce_session')) && (commerce_session())) { $_POST[$prefix.$name] = client_data($name); }
elseif ((function_exists('is_user_logged_in')) && (is_user_logged_in())) { $_POST[$prefix.$name] = membership_user_data($name); } } }
if ((isset($_POST[$prefix.'submit'])) && ($atts['required'] == 'yes') && ($_POST[$prefix.$name] == '')) { $_GET[$prefix.$name.'_error'] = $_GET[$prefix.'unfilled_field_message']; }
if (($_GET['form_focus'] == '') && ($_POST[$prefix.$name] == '')) { $_GET['form_focus'] = 'document.getElementById("'.$prefix.$name.'").focus();'; }
foreach ($attributes as $key => $value) {
switch ($key) {
case 'required': if ($atts['required'] == 'yes') { $_GET[$prefix.'required_fields'][] = $name; } break;
default: if ((is_string($key)) && ($atts[$key] != '')) { $markup .= ' '.$key.'="'.$atts[$key].'"'; } } }

return '<select name="'.$prefix.$name.'" id="'.$prefix.$name.'"'.$markup.'>'.do_shortcode($content).'</select>'; }


function membership_form_textarea($atts, $content) {
$form_id = $_GET['membership_form_id'];
$form_type = $_GET['membership_form_type'];
$prefix = 'membership_form'.$form_id.'_';
$attributes = array(
0 => 'email_address',
'accesskey' => '',
'class' => '',
'cols' => '30',
'dir' => '',
'disabled' => '',
'onblur' => '',
'onchange' => '',
'onclick' => '',
'ondblclick' => '',
'onfocus' => '',
'onkeydown' => '',
'onkeypress' => '',
'onkeyup' => '',
'onmousedown' => '',
'onmousemove' => '',
'onmouseout' => '',
'onmouseover' => '',
'onmouseup' => '',
'onselect' => '',
'readonly' => '',
'required' => 'no',
'rows' => '10',
'style' => '',
'tabindex' => '',
'title' => '',
'xmlns' => '');
foreach ($attributes as $key => $value) { if ($atts[$key] == '') { $atts[$key] = $attributes[$key]; } }

$name = str_replace('-', '_', format_nice_name($atts[0]));
$_GET[$prefix.'fields'][] = $name;
if (in_array($name, $_GET[$prefix.'required_fields'])) { $atts['required'] = 'yes'; }
if ($name == 'email_address') {
if ($atts['onmouseout'] == '') { $atts['onmouseout'] = "this.value = format_email_address(this.value);"; }
if (isset($_POST[$prefix.'submit'])) {
if ((!strstr($_POST[$prefix.$name], '@')) || (!strstr($_POST[$prefix.$name], '.'))) {
$_GET[$prefix.$name.'_error'] = $_GET[$prefix.'invalid_email_address_message']; } } }
if ($name == 'login') {
if ($atts['onchange'] == '') { $atts['onchange'] = $_GET[$prefix.$name.'_onchange']; }
if ($atts['onmouseout'] == '') { $atts['onmouseout'] = "this.value = format_email_address(this.value);"; } }
if ((!isset($_POST[$prefix.'submit'])) && ($_POST[$prefix.$name] == '')) { $_POST[$prefix.$name] = do_shortcode($content); }
foreach (array($name, str_replace('_', '-', $name)) as $key) {
if ($_POST[$prefix.$name] == '') { $_POST[$prefix.$name] = utf8_encode(htmlspecialchars($_GET[$key])); } }
if ((!isset($_POST[$prefix.'submit'])) && ($_POST[$prefix.$name] == '')) {
include dirname(__FILE__).'/libraries/personal-informations.php';
if (in_array($name, $personal_informations)) {
if ((function_exists('affiliation_session')) && (affiliation_session())) { $_POST[$prefix.$name] = affiliate_data($name); }
elseif ((function_exists('commerce_session')) && (commerce_session())) { $_POST[$prefix.$name] = client_data($name); }
elseif ((function_exists('is_user_logged_in')) && (is_user_logged_in())) { $_POST[$prefix.$name] = membership_user_data($name); } } }
if ((isset($_POST[$prefix.'submit'])) && ($atts['required'] == 'yes') && ($_POST[$prefix.$name] == '')) { $_GET[$prefix.$name.'_error'] = $_GET[$prefix.'unfilled_field_message']; }
if (($_GET['form_focus'] == '') && ($_POST[$prefix.$name] == '')) { $_GET['form_focus'] = 'document.getElementById("'.$prefix.$name.'").focus();'; }
foreach ($attributes as $key => $value) {
switch ($key) {
case 'required': if ($atts['required'] == 'yes') { $_GET[$prefix.'required_fields'][] = $name; } break;
default: if ((is_string($key)) && ($atts[$key] != '')) { $markup .= ' '.$key.'="'.$atts[$key].'"'; } } }

return '<textarea name="'.$prefix.$name.'" id="'.$prefix.$name.'"'.$markup.'>'.$_POST[$prefix.$name].'</textarea>'; }


function membership_form_validation_content($atts, $content) {
$form_id = $_GET['membership_form_id'];
if (isset($_POST['membership_form'.$form_id.'_submit'])) {
$content = explode('[other]', do_shortcode($content));
if ($_GET['form_error'] == 'yes') { $n = 1; } else { $n = 0; }
return $content[$n]; } }