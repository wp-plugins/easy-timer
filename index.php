<?php if ((isset($_GET['action'])) || (isset($_GET['url']))) {
$file = 'wp-load.php'; $i = 0;
while ((!file_exists($file)) && ($i < 8)) { $file = '../'.$file; $i = $i + 1; }
include_once $file;
if (isset($_GET['action'])) {
switch ($_GET['action']) {
case 'fill-admin-page-fields':
if (!headers_sent()) { header('Content-type: text/plain'); }
if ((isset($_GET['page'])) && (isset($_GET['key'])) && ($_GET['key'] == md5(AUTH_KEY))) {
foreach (array('admin.php', 'admin-pages-functions.php') as $file) { include_once AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.$file; }
if (audiobooks_authors_and_narrators_user_can((array) get_option('audiobooks_authors_and_narrators_back_office'), 'view')) {
$GLOBALS['action'] = 'fill_admin_page_fields';
function audiobooks_authors_and_narrators_fill_admin_page_fields() {
global $wpdb; $error = '';
$back_office_options = (array) get_option('audiobooks_authors_and_narrators_back_office');
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$current_time = (isset($_GET['time']) ? $_GET['time'] : time());
$current_date = date('Y-m-d H:i:s', $current_time + 3600*UTC_OFFSET);
$current_date_utc = date('Y-m-d H:i:s', $current_time);
$admin_page = ($_GET['page'] == 'audiobooks-authors-and-narrators' ? 'options' : str_replace('-', '_', str_replace('audiobooks-authors-and-narrators-', '', $_GET['page'])));
foreach ($_POST as $key => $value) { if (is_string($value)) { $_POST[$key] = stripslashes($value); } }
$_POST['update_fields'] = 'yes'; if (isset($_POST['submit'])) { unset($_POST['submit']); }
foreach (array('admin-pages.php', 'tables.php') as $file) { include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.$file; }
include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'includes/fill-admin-page-fields.php';
echo json_encode(array_map('strval', $_POST)); }
audiobooks_authors_and_narrators_fill_admin_page_fields(); } } break;
case 'install': if ((isset($_GET['key'])) && ($_GET['key'] == md5(AUTH_KEY))) { install_audiobooks_authors_and_narrators(); } break;
case 'update-admin-page-options':
if ((isset($_GET['page'])) && (isset($_GET['key'])) && ($_GET['key'] == md5(AUTH_KEY))) {
foreach (array('admin.php', 'admin-pages-functions.php') as $file) { include_once AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.$file; }
if (audiobooks_authors_and_narrators_user_can((array) get_option('audiobooks_authors_and_narrators_back_office'), 'manage')) {
$options = get_option(str_replace('-', '_', $_GET['page']));
if ($options) { $options = (array) $options;
foreach ($options as $key => $value) { if (isset($_GET[$key])) { $options[$key] = stripslashes($_GET[$key]); } }
update_option(str_replace('-', '_', $_GET['page']), $options); } } } break;
default: if (!headers_sent()) { header('Location: '.HOME_URL); exit(); } } } }
elseif (!headers_sent()) { header('Location: /'); exit(); }