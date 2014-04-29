<?php $lang = strtolower(substr(get_locale(), 0, 2)); if ($lang == '') { $lang = 'en'; }


$initial_options[''] = array(
'author_description_code' => '',
'narrator_description_code' => '',
'version' => AUDIOBOOKS_AUTHORS_AND_NARRATORS_VERSION);


$initial_options['cron'] = array(
'previous_cron_timestamp' => 0,
'previous_installation' => array('version' => '', 'number' => 0, 'timestamp' => 0));


if (isset($variables)) { $original['variables'] = $variables; }
$variables = array(
'displayed_columns',
'displayed_links',
'first_columns',
'id',
'last_columns',
'links',
'menu_displayed_items',
'menu_items',
'pages_titles',
'table',
'table_slug',
'tables');
foreach ($variables as $variable) { if (isset($$variable)) { $original[$variable] = $$variable; unset($$variable); } }


include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'tables.php';
foreach ($tables as $table_slug => $table) {
$first_columns = array(
'id',
'first_name',
'last_name',
'description',
'date');

$last_columns = array();
foreach ($table as $key => $value) {
if ((!in_array($key, $first_columns)) && (isset($value['name'])) && ($value['name'] != '')) { $last_columns[] = $key; } }
$displayed_columns = array();
for ($i = 0; $i < count($first_columns); $i++) { $displayed_columns[] = $i; }

$initial_options[$table_slug] = array(
'columns' => array_merge($first_columns, $last_columns),
'columns_list_displayed' => 'yes',
'displayed_columns' => $displayed_columns,
'limit' => 10,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '',
'start_date' => '2000-01-01 00:00:00'); }


include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'admin-pages.php';
$menu_items = array();
$pages_titles = array();
foreach ($admin_pages as $key => $value) {
$menu_items[] = $key;
if (isset($_GET['id'])) { $id = $_GET['id']; unset($_GET['id']); }
$pages_titles[$key] = $value['menu_title'];
if (isset($id)) { $_GET['id'] = $id; unset($id); } }
$menu_displayed_items = array();
foreach ($menu_items as $key => $value) { $menu_displayed_items[] = $key; }

$initial_options['back_office'] = array(
'back_office_page_summary_displayed' => 'yes',
'back_office_page_undisplayed_modules' => array(),
'custom_icon_url' => AUDIOBOOKS_AUTHORS_AND_NARRATORS_URL.'images/icon.png',
'custom_icon_used' => 'no',
'menu_displayed' => 'yes',
'menu_displayed_items' => $menu_displayed_items,
'menu_items' => $menu_items,
'menu_title_'.$lang => __('Authors And Narrators', 'audiobooks-authors-and-narrators'),
'minimum_roles' => array(
	'manage' => 'administrator',
	'view' => 'administrator'),
'options_page_summary_displayed' => 'no',
'options_page_undisplayed_modules' => array(),
'pages_titles_'.$lang => $pages_titles,
'title' => 'Audiobooks - '.__('Authors And Narrators', 'audiobooks-authors-and-narrators'),
'title_displayed' => 'yes',
'author_page_summary_displayed' => 'no',
'author_page_undisplayed_modules' => array(),
'narrator_page_summary_displayed' => 'no',
'narrator_page_undisplayed_modules' => array());


foreach ($variables as $variable) { if (isset($original[$variable])) { $$variable = $original[$variable]; } }
if (isset($original['variables'])) { $variables = $original['variables']; }