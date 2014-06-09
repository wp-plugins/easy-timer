<?php if (((isset($_GET['page'])) && (strstr($_GET['page'], 'audiobooks-pages'))) || (strstr($_SERVER['REQUEST_URI'], '/plugins.php'))) {
load_plugin_textdomain('audiobooks-pages', false, AUDIOBOOKS_PAGES_FOLDER.'/languages'); }


function audiobooks_pages_options_page() {
add_options_page(__('Audiobooks Pages', 'audiobooks-pages'), __('Audiobooks Pages', 'audiobooks-pages'), 'manage_options', 'audiobooks-pages', create_function('', 'include_once AUDIOBOOKS_PAGES_PATH."options-page.php";')); }

add_action('admin_menu', 'audiobooks_pages_options_page');


function audiobooks_pages_options_page_css() { ?>
<style type="text/css" media="all">
.wrap .description { color: #808080; }
.wrap h2 { float: left; }
.wrap input.button-secondary, .wrap select { vertical-align: 0; }
.wrap p.submit { margin: 0 20%; }
.wrap ul.subsubsub { margin: 1em 0 1.5em 6em; float: left; white-space: normal; }
</style>
<?php }

if ((isset($_GET['page'])) && (strstr($_GET['page'], 'audiobooks-pages'))) { add_action('admin_head', 'audiobooks_pages_options_page_css'); }


function audiobooks_pages_action_links($links) {
if (!is_network_admin()) {
$links = array_merge($links, array(
'<span class="delete"><a href="options-general.php?page=audiobooks-pages&amp;action=uninstall" title="'.__('Delete the options of Audiobooks Pages', 'audiobooks-pages').'">'.__('Uninstall', 'audiobooks-pages').'</a></span>',
'<span class="delete"><a href="options-general.php?page=audiobooks-pages&amp;action=reset" title="'.__('Reset the options of Audiobooks Pages', 'audiobooks-pages').'">'.__('Reset', 'audiobooks-pages').'</a></span>',
'<a href="options-general.php?page=audiobooks-pages">'.__('Options', 'audiobooks-pages').'</a>')); }
else {
$links = array_merge($links, array(
'<span class="delete"><a href="../options-general.php?page=audiobooks-pages&amp;action=uninstall&amp;for=network" title="'.__('Delete the options of Audiobooks Pages for all sites in this network', 'audiobooks-pages').'">'.__('Uninstall', 'audiobooks-pages').'</a></span>')); }
return $links; }

foreach (array('', 'network_admin_') as $prefix) { add_filter($prefix.'plugin_action_links_'.AUDIOBOOKS_PAGES_FOLDER.'/audiobooks-pages.php', 'audiobooks_pages_action_links', 10, 2); }


function reset_audiobooks_pages() {
load_plugin_textdomain('audiobooks-pages', false, AUDIOBOOKS_PAGES_FOLDER.'/languages');
include AUDIOBOOKS_PAGES_PATH.'initial-options.php';
update_option('audiobooks_pages', $initial_options); }


function uninstall_audiobooks_pages($for = 'single') { include AUDIOBOOKS_PAGES_PATH.'includes/uninstall.php'; }