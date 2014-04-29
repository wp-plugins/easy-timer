<?php $admin_pages = array(
'' => array('page_title' => __('Authors And Narrators', 'audiobooks-authors-and-narrators').' ('.__('Options', 'audiobooks-authors-and-narrators').')', 'menu_title' => __('Options', 'audiobooks-authors-and-narrators'), 'file' => 'options-page.php'),
'author' => array('page_title' => __('Authors And Narrators', 'audiobooks-authors-and-narrators').' ('.__('Author', 'audiobooks-authors-and-narrators').')', 'menu_title' => (((isset($_GET['page'])) && ($_GET['page'] == 'audiobooks-authors-and-narrators-author') && (isset($_GET['id']))) ? (((isset($_GET['action'])) && ($_GET['action'] == 'delete')) ? __('Delete Author', 'audiobooks-authors-and-narrators') : __('Edit Author', 'audiobooks-authors-and-narrators')) : __('Add Author', 'audiobooks-authors-and-narrators')), 'file' => 'author-page.php'),
'authors' => array('page_title' => __('Authors And Narrators', 'audiobooks-authors-and-narrators').' ('.__('Authors', 'audiobooks-authors-and-narrators').')', 'menu_title' => __('Authors', 'audiobooks-authors-and-narrators'), 'file' => 'table-page.php'),
'narrator' => array('page_title' => __('Authors And Narrators', 'audiobooks-authors-and-narrators').' ('.__('Narrator', 'audiobooks-authors-and-narrators').')', 'menu_title' => (((isset($_GET['page'])) && ($_GET['page'] == 'audiobooks-authors-and-narrators-narrator') && (isset($_GET['id']))) ? (((isset($_GET['action'])) && ($_GET['action'] == 'delete')) ? __('Delete Narrator', 'audiobooks-authors-and-narrators') : __('Edit Narrator', 'audiobooks-authors-and-narrators')) : __('Add Narrator', 'audiobooks-authors-and-narrators')), 'file' => 'narrator-page.php'),
'narrators' => array('page_title' => __('Authors And Narrators', 'audiobooks-authors-and-narrators').' ('.__('Narrators', 'audiobooks-authors-and-narrators').')', 'menu_title' => __('Narrators', 'audiobooks-authors-and-narrators'), 'file' => 'table-page.php'),
'back_office' => array('page_title' => __('Authors And Narrators', 'audiobooks-authors-and-narrators').' ('.__('Back Office', 'audiobooks-authors-and-narrators').')', 'menu_title' => __('Back Office', 'audiobooks-authors-and-narrators'), 'file' => 'back-office-page.php'));

$modules['back_office'] = array(
'capabilities' => array('name' => __('Capabilities', 'audiobooks-authors-and-narrators')),
'icon' => array('name' => __('Icon', 'audiobooks-authors-and-narrators')),
'top' => array('name' => __('Top', 'audiobooks-authors-and-narrators')),
'menu' => array('name' => __('Menu', 'audiobooks-authors-and-narrators')),
'options-page' => array('name' => __('<em>Options</em> page', 'audiobooks-authors-and-narrators')),
'author-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Author</em> page', 'audiobooks-authors-and-narrators') : __('<em>Add Author</em> page', 'audiobooks-authors-and-narrators'))),
'narrator-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Narrator</em> page', 'audiobooks-authors-and-narrators') : __('<em>Add Narrator</em> page', 'audiobooks-authors-and-narrators'))),
'back-office-page' => array('name' => __('<em>Back Office</em> page', 'audiobooks-authors-and-narrators'), 'required' => 'yes'));

$modules['options'] = array(
'descriptions' => array('name' => __('Descriptions', 'audiobooks-authors-and-narrators')));

$modules['author'] = array(
'personal-informations' => array('name' => __('General informations', 'audiobooks-authors-and-narrators'), 'required' => 'yes'));

$modules['narrator'] = $modules['author'];

$roles = array(
'administrator' => array('name' => __('Administrator', 'audiobooks-authors-and-narrators'), 'capability' => 'manage_options'),
'editor' => array('name' => __('Editor', 'audiobooks-authors-and-narrators'), 'capability' => 'edit_pages'),
'author' => array('name' => __('Author', 'audiobooks-authors-and-narrators'), 'capability' => 'publish_posts'),
'contributor' => array('name' => __('Contributor', 'audiobooks-authors-and-narrators'), 'capability' => 'edit_posts'),
'subscriber' => array('name' => __('Subscriber', 'audiobooks-authors-and-narrators'), 'capability' => 'read'));