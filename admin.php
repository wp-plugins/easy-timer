<?php if (strstr($_SERVER['REQUEST_URI'], '/plugins.php')) { load_plugin_textdomain('audiobooks-authors-and-narrators', false, AUDIOBOOKS_AUTHORS_AND_NARRATORS_FOLDER.'/languages'); }
if ((isset($_GET['page'])) && (strstr($_GET['page'], 'audiobooks-authors-and-narrators'))) { include_once AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'admin-pages-functions.php'; }


function audiobooks_authors_and_narrators_admin_menu() {
$lang = strtolower(substr(get_locale(), 0, 2)); if ($lang == '') { $lang = 'en'; }
include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'admin-pages.php';
$options = (array) get_option('audiobooks_authors_and_narrators_back_office');
if ((!isset($options['menu_title_'.$lang])) || ($options['menu_title_'.$lang] == '') || (!isset($options['pages_titles_'.$lang]))
 || ($options['pages_titles_'.$lang] == '')) { install_audiobooks_authors_and_narrators(); $options = (array) get_option('audiobooks_authors_and_narrators_back_office'); }
$menu_title = $options['menu_title_'.$lang]; $pages_titles = (array) $options['pages_titles_'.$lang];
if (((isset($_GET['page'])) && (strstr($_GET['page'], 'audiobooks-authors-and-narrators'))) || ($menu_title == '')) { $menu_title = __('Authors And Narrators', 'audiobooks-authors-and-narrators'); }
if ((defined('AUDIOBOOKS_AUTHORS_AND_NARRATORS_DEMO')) && (AUDIOBOOKS_AUTHORS_AND_NARRATORS_DEMO == true)) { $capability = 'manage_options'; }
else { $role = $options['minimum_roles']['view']; $capability = $roles[$role]['capability']; }
if ($options['custom_icon_used'] == 'yes') { $icon_url = format_url($options['custom_icon_url']); } else { $icon_url = ''; }
add_menu_page('Authors And Narrators', $menu_title, $capability, 'audiobooks-authors-and-narrators', create_function('', 'include_once AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH."options-page.php";'), $icon_url);
$admin_menu_pages = audiobooks_authors_and_narrators_admin_menu_pages();
foreach ($admin_pages as $key => $value) { if (in_array($key, $admin_menu_pages)) {
$slug = 'audiobooks-authors-and-narrators'.($key == '' ? '' : '-'.str_replace('_', '-', $key));
if ((!isset($_GET['page'])) || (!strstr($_GET['page'], 'audiobooks-authors-and-narrators'))) { $value['menu_title'] = $pages_titles[$key]; }
add_submenu_page('audiobooks-authors-and-narrators', $value['page_title'], $value['menu_title'], $capability, $slug, create_function('', 'include_once AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH."'.$value['file'].'";')); } } }

add_action('admin_menu', 'audiobooks_authors_and_narrators_admin_menu');


function audiobooks_authors_and_narrators_admin_menu_pages() {
include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'admin-pages.php';
$options = (array) get_option('audiobooks_authors_and_narrators_back_office');
$menu_items = (array) $options['menu_items'];
$numbers = (array) $options['menu_displayed_items'];
$menu_displayed_items = array();
foreach ($numbers as $i) { $menu_displayed_items[] = $menu_items[$i]; }
$admin_menu_pages = array(); foreach ($admin_pages as $key => $value) {
$slug = 'audiobooks-authors-and-narrators'.($key == '' ? '' : '-'.str_replace('_', '-', $key));
if (($key == '') || ($key == 'back_office') || ((isset($_GET['page'])) && ($_GET['page'] == $slug))
 || (in_array($key, $menu_displayed_items))) { $admin_menu_pages[] = $key; } }
return $admin_menu_pages; }


function audiobooks_authors_and_narrators_user_can($back_office_options, $capability) {
if ((defined('AUDIOBOOKS_AUTHORS_AND_NARRATORS_DEMO')) && (AUDIOBOOKS_AUTHORS_AND_NARRATORS_DEMO == true)) { $capability = 'manage_options'; }
else { include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'admin-pages.php'; $role = $back_office_options['minimum_roles'][$capability]; $capability = $roles[$role]['capability']; }
return current_user_can($capability); }


function audiobooks_authors_and_narrators_action_links($links) {
if (!is_network_admin()) {
$links = array_merge($links, array(
'<span class="delete"><a href="admin.php?page=audiobooks-authors-and-narrators&amp;action=uninstall" title="'.__('Delete the options and tables of Audiobooks - Authors And Narrators', 'audiobooks-authors-and-narrators').'">'.__('Uninstall', 'audiobooks-authors-and-narrators').'</a></span>',
'<span class="delete"><a href="admin.php?page=audiobooks-authors-and-narrators&amp;action=reset" title="'.__('Reset the options of Audiobooks - Authors And Narrators', 'audiobooks-authors-and-narrators').'">'.__('Reset', 'audiobooks-authors-and-narrators').'</a></span>',
'<a href="admin.php?page=audiobooks-authors-and-narrators">'.__('Options', 'audiobooks-authors-and-narrators').'</a>')); }
else {
$links = array_merge($links, array(
'<span class="delete"><a href="../admin.php?page=audiobooks-authors-and-narrators&amp;action=uninstall&amp;for=network" title="'.__('Delete the options and tables of Audiobooks - Authors And Narrators for all sites in this network', 'audiobooks-authors-and-narrators').'">'.__('Uninstall', 'audiobooks-authors-and-narrators').'</a></span>')); }
return $links; }

foreach (array('', 'network_admin_') as $prefix) { add_filter($prefix.'plugin_action_links_'.AUDIOBOOKS_AUTHORS_AND_NARRATORS_FOLDER.'/audiobooks-authors-and-narrators.php', 'audiobooks_authors_and_narrators_action_links', 10, 2); }


function reset_audiobooks_authors_and_narrators() {
load_plugin_textdomain('audiobooks-authors-and-narrators', false, AUDIOBOOKS_AUTHORS_AND_NARRATORS_FOLDER.'/languages');
include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'initial-options.php';
foreach ($initial_options as $key => $value) {
$_key = ($key == '' ? '' : '_'.$key);
update_option(substr('audiobooks_authors_and_narrators'.$_key, 0, 64), $value); } }


function uninstall_audiobooks_authors_and_narrators($for = 'single') { include AUDIOBOOKS_AUTHORS_AND_NARRATORS_PATH.'includes/uninstall.php'; }