<?php global $wpdb;

foreach ((array) $_POST as $key => $value) { if (is_string($value)) { $_POST[$key] = quotes_entities($_POST[$key]); } }
$GLOBALS['selection_criteria'] = ''; $selection_criteria = '';
foreach (all_tables_keys($tables) as $field) {
if (isset($_GET[$field])) {
$GLOBALS['selection_criteria'] .= '&amp;'.$field.'='.str_replace('+', '%20', urlencode($_GET[$field]));
$selection_criteria .= (is_numeric($_GET[$field]) ? " AND (".$field." = ".$_GET[$field].")" : " AND (".$field." = '".$_GET[$field]."')"); } }


function all_tables_keys($tables) {
$keys = array();
foreach ($tables as $table_slug => $table) {
foreach ($table as $key => $value) {
if (!in_array($key, $keys)) { $keys[] = $key; } } }
return $keys; }


function no_items($table) {
switch ($table) {
case 'authors': $no_items = __('No authors', 'audiobooks-authors-and-narrators'); break;
case 'narrators': $no_items = __('No narrators', 'audiobooks-authors-and-narrators'); }
return $no_items; }


function row_actions($table, $item) {
$row_actions = 
'<span class="edit"><a href="admin.php?page=audiobooks-authors-and-narrators-'.single_page_slug($table).'&amp;id='.$item->id.'">'.__('Edit', 'audiobooks-authors-and-narrators').'</a></span>
 | <span class="delete"><a href="admin.php?page=audiobooks-authors-and-narrators-'.single_page_slug($table).'&amp;id='.$item->id.'&amp;action=delete">'.__('Delete', 'audiobooks-authors-and-narrators').'</a></span>';
return '<div class="row-actions" style="margin-top: 2em; position: absolute;">'.$row_actions.'</div>'; }


function single_page_slug($table) {
$page = substr($table, 0, -1);
return $page; }


function table_name($table) {
global $wpdb;
return $wpdb->prefix.'audiobooks_authors_and_narrators_'.$table; }


function table_undisplayed_keys($tables, $table, $back_office_options) {
global $wpdb;
$undisplayed_modules = (array) $back_office_options[single_page_slug($table).'_page_undisplayed_modules'];
$undisplayed_keys = array();
foreach ($tables[$table] as $key => $value) {
foreach ((array) $value['modules'] as $module) {
if (in_array($module, $undisplayed_modules)) { $undisplayed_keys[] = $key; } } }
return $undisplayed_keys; }


function table_data($table, $column, $item) {
switch ($table) {
case 'authors': $GLOBALS['author_id'] = $item->id; $GLOBALS['author_data'] = (array) $item; $data = author_data($column); break;
case 'narrators': $GLOBALS['narrator_id'] = $item->id; $GLOBALS['narrator_data'] = (array) $item; $data = narrator_data($column); break;
default: $data = audiobooks_authors_and_narrators_format_data($column, $item->$column); }
return $data; }


function table_td($table, $column, $item) {
$data = htmlspecialchars(table_data($table, $column, $item));
switch ($column) {
case 'audiobooks_url': case 'profile_url': $table_td = ($data == '' ? '' : '<a href="'.$data.'">'.($data == ROOT_URL ? '/' : audiobooks_authors_and_narrators_excerpt(str_replace(ROOT_URL, '', $data), 80)).'</a>'); break;
case 'email_address': $table_td = '<a href="mailto:'.$data.'">'.audiobooks_authors_and_narrators_excerpt($data, 50).'</a>'; break;
default: $table_td = audiobooks_authors_and_narrators_excerpt($data); }
return $table_td; }


function table_th($tables, $table, $column) {
$reverse_order = ($_GET['order'] == 'asc' ? 'desc' : 'asc');
$table_th = '<th scope="col" class="manage-column '.($_GET['orderby'] == $column ? 'sorted '.$_GET['order'] : 'sortable '.$reverse_order).'" style="width: '.$tables[$table][$column]['width'].'%;">
<a href="admin.php?page='.$_GET['page'].$GLOBALS['criteria'].'&amp;orderby='.$column.'&amp;order='.($_GET['orderby'] == $column ? $reverse_order : $_GET['order']).'">
<span>'.$tables[$table][$column]['name'].'</span><span class="sorting-indicator"></span></a></th>';
return $table_th; }


function tablenav_pages($table, $n, $max_paged, $location) {
switch ($table) {
case 'authors': $singular = __('author', 'audiobooks-authors-and-narrators'); $plural = __('authors', 'audiobooks-authors-and-narrators'); break;
case 'narrators': $singular = __('narrator', 'audiobooks-authors-and-narrators'); $plural = __('narrators', 'audiobooks-authors-and-narrators'); break; }
if ($_GET['paged'] == 1) { $prev_paged = 1; } else { $prev_paged = $_GET['paged'] - 1; }
if ($_GET['paged'] == $max_paged) { $next_paged = $max_paged; } else { $next_paged = $_GET['paged'] + 1; }
$url = 'admin.php?page='.$_GET['page'].$GLOBALS['criteria'].'&amp;orderby='.$_GET['orderby'].'&amp;order='.$_GET['order'];
echo '<div class="tablenav-pages" style="float: right;"><span class="displaying-num">'.$n.' '.($n <= 1 ? $singular : $plural).'</span>
<a class="first-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the first page', 'audiobooks-authors-and-narrators').'" href="'.$url.'&amp;paged=1">&laquo;</a>
<a class="prev-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the previous page', 'audiobooks-authors-and-narrators').'" href="'.$url.'&amp;paged='.$prev_paged.'">&lsaquo;</a>
<span class="paging-input">'.($location == 'top' ? '<input type="hidden" name="old_paged" value="'.$_GET['paged'].'" /><input class="current-page" title="'.__('Current page', 'audiobooks-authors-and-narrators').'" type="text" name="paged" id="paged" value="'.$_GET['paged'].'" placeholder="'.$_GET['paged'].'" size="2" onfocus="this.placeholder = \'\';" onchange="this.value = this.value.replace(/[^0-9]/gi, \'\'); if ((this.value == \'\') || (this.value == 0)) { this.value = this.form.old_paged.value; } if (this.value != this.form.old_paged.value) { window.location = \''.$url.'&amp;paged=\'+this.value; }" />' : $_GET['paged']).' '.__('of', 'audiobooks-authors-and-narrators').' <span class="total-pages">'.$max_paged.'</span></span>
<a class="next-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the next page', 'audiobooks-authors-and-narrators').'" href="'.$url.'&amp;paged='.$next_paged.'">&rsaquo;</a>
<a class="last-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the last page', 'audiobooks-authors-and-narrators').'" href="'.$url.'&amp;paged='.$max_paged.'">&raquo;</a></div>'; }