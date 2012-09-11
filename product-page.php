<?php global $wpdb;
$back_office_options = get_option('commerce_manager_back_office');
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$is_category = (strstr($_GET['page'], 'category'));
if ($is_category) { $admin_page = 'product_category'; $table_slug = 'products_categories'; $attribute = 'category'; }
else { $admin_page = 'product'; $table_slug = 'products'; $attribute = 'id'; }

if ((isset($_GET['id'])) && ($_GET['action'] == 'delete')) {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!commerce_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'commerce-manager'); }
else {
if ($is_category) {
$category = $wpdb->get_row("SELECT category_id FROM ".$wpdb->prefix."commerce_manager_products_categories WHERE id = ".$_GET['id'], OBJECT);
foreach (array('products', 'products_categories') as $table) {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_".$table." SET category_id = ".$category->category_id." WHERE category_id = ".$_GET['id']); } }
$results = $wpdb->query("DELETE FROM ".$wpdb->prefix."commerce_manager_".$table_slug." WHERE id = ".$_GET['id']); } } ?>
<div class="wrap">
<div id="poststuff">
<?php commerce_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) {
echo '<div class="updated"><p><strong>'.($is_category ? __('Category deleted.', 'commerce-manager') : __('Product deleted.', 'commerce-manager')).'</strong></p></div>
<script type="text/javascript">setTimeout(\'window.location = "admin.php?page=commerce-manager-products'.($is_category ? '-categories' : '').'"\', 2000);</script>'; } ?>
<?php commerce_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php echo ($is_category ? __('Do you really want to permanently delete this category?', 'commerce-manager') : __('Do you really want to permanently delete this product?', 'commerce-manager')); ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'commerce-manager'); ?>" />
</div>
<div class="clear"></div>
</form><?php } ?>
</div>
</div><?php }

else {
include 'admin-pages.php';
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!commerce_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'commerce-manager'); }
else {
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = html_entity_decode(str_replace('&nbsp;', ' ', $value)); } }
$back_office_options = update_commerce_manager_back_office($back_office_options, $admin_page);

if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
foreach (array(
'commission_amount',
'commission_percentage',
'commission2_amount',
'commission2_percentage',
'normal_price',
'price',
'shipping_cost',
'tax_percentage',
'weight') as $field) { $_POST[$field] = str_replace(array('?', ',', ';'), '.', $_POST[$field]); }
foreach (array(
'refunds_count',
'sales_count') as $field) {
$_POST[$field] = (int) $_POST[$field];
if ($_POST[$field] < 0) { $_POST[$field] = 0; } }
$members_areas = array_unique(preg_split('#[^0-9]#', $_POST['customer_members_areas'], 0, PREG_SPLIT_NO_EMPTY));
sort($members_areas, SORT_NUMERIC);
foreach ($members_areas as $member_area) { $members_areas_list .= $member_area.', '; }
$_POST['customer_members_areas'] = substr($members_areas_list, 0, -2);
$keywords = explode(',', $_POST['keywords']);
for ($i = 0; $i < count($keywords); $i++) { $keywords[$i] = strtolower(trim($keywords[$i])); }
sort($keywords);
foreach ($keywords as $keyword) { $keywords_list .= $keyword.', '; }
$_POST['keywords'] = substr($keywords_list, 0, -2);
if ($_POST['date'] == '') {
$_POST['date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['date_utc'] = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['date'], 0, PREG_SPLIT_NO_EMPTY);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['date'] = date('Y-m-d H:i:s', $time);
$_POST['date_utc'] = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); }
if (!$is_category) {
switch ($_POST['available_quantity']) { case '' : case 'i' : case 'infinite' : case 'u' : $_POST['available_quantity'] = 'unlimited'; }
if ($_POST['refunds_count'] > $_POST['sales_count']) { $_POST['refunds_count'] = $_POST['sales_count']; } }
for ($i = 1; $i <= 3; $i++) {
switch ($_POST['payments_number'.$i]) { case 'i' : case 'infinite' : case 'u' : $_POST['payments_number'.$i] = 'unlimited'; break; }
foreach (array(
'first_payment_amount',
'first_payment_normal_amount',
'payments_amount',
'payments_normal_amount') as $field) { $_POST[$field.$i] = str_replace(array('?', ',', ';'), '.', $_POST[$field.$i]); } }

if (!isset($_GET['id'])) {
if ($_POST['name'] == '') { $error .= ' '.__('Please fill out the required fields.', 'commerce-manager'); }
elseif ($is_category) {
$result = $wpdb->get_results("SELECT name FROM ".$wpdb->prefix."commerce_manager_products_categories WHERE name = '".$_POST['name']."'", OBJECT);
if ($result) { $error .= ' '.__('This name is not available.', 'commerce-manager'); } }
if ($error == '') {
if ($is_category) { $result = false; }
else { $result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."commerce_manager_products WHERE name = '".$_POST['name']."' AND price = '".$_POST['price']."' AND date = '".$_POST['date']."'", OBJECT); }
if (!$result) {
$updated = true;
include 'tables.php';
$sql = commerce_sql_array($tables[$table_slug], $_POST);
foreach ($tables[$table_slug] as $key => $value) { if ($key != 'id') { $keys_list .= $key.","; $values_list .= $sql[$key].","; } }
$results = $wpdb->query("INSERT INTO ".$wpdb->prefix."commerce_manager_".$table_slug." (".substr($keys_list, 0, -1).") VALUES(".substr($values_list, 0, -1).")"); } } }

if (isset($_GET['id'])) {
$updated = true;
if ((isset($_POST['count_sales'])) || (isset($_POST['count_sales_of_all_products']))) {
$row = $wpdb->get_row("SELECT SUM(quantity) as total FROM ".$wpdb->prefix."commerce_manager_orders WHERE product_id = ".$_GET['id'], OBJECT);
$_POST['sales_count'] = (int) $row->total;
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_products SET sales_count = ".$_POST['sales_count']." WHERE id = ".$_GET['id']); }
if (isset($_POST['count_sales_of_all_products'])) {
$products = $wpdb->get_results("SELECT id FROM ".$wpdb->prefix."commerce_manager_products WHERE id != ".$_GET['id'], OBJECT);
if ($products) { foreach ($products as $product) {
$row = $wpdb->get_row("SELECT SUM(quantity) as total FROM ".$wpdb->prefix."commerce_manager_orders WHERE product_id = ".$product->id, OBJECT);
$sales_count = (int) $row->total;
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_products SET sales_count = ".$sales_count." WHERE id = ".$product->id); } } }
if ((isset($_POST['count_refunds'])) || (isset($_POST['count_refunds_of_all_products']))) {
$row = $wpdb->get_row("SELECT SUM(quantity) as total FROM ".$wpdb->prefix."commerce_manager_orders WHERE product_id = ".$_GET['id']." AND status = 'refunded'", OBJECT);
$_POST['refunds_count'] = (int) $row->total;
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_products SET refunds_count = ".$_POST['refunds_count']." WHERE id = ".$_GET['id']); }
if (isset($_POST['count_refunds_of_all_products'])) {
$products = $wpdb->get_results("SELECT id FROM ".$wpdb->prefix."commerce_manager_products WHERE id != ".$_GET['id'], OBJECT);
if ($products) { foreach ($products as $product) {
$row = $wpdb->get_row("SELECT SUM(quantity) as total FROM ".$wpdb->prefix."commerce_manager_orders WHERE product_id = ".$product->id." AND status = 'refunded'", OBJECT);
$refunds_count = (int) $row->total;
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_products SET refunds_count = ".$refunds_count." WHERE id = ".$product->id); } } }
if ($_POST['name'] != '') {
if (!$is_category) { $results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_".$table_slug." SET name = '".$_POST['name']."' WHERE id = ".$_GET['id']); }
else {
$result = $wpdb->get_results("SELECT name FROM ".$wpdb->prefix."commerce_manager_products_categories WHERE name = '".$_POST['name']."' AND id != ".$_GET['id'], OBJECT);
if ($result) { $error .= ' '.__('This name is not available.', 'commerce-manager'); }
else { $results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_products_categories SET name = '".$_POST['name']."' WHERE id = ".$_GET['id']); } } }
include 'tables.php';
$sql = commerce_sql_array($tables[$table_slug], $_POST);
foreach ($tables[$table_slug] as $key => $value) { switch ($key) {
case 'id': case 'name': break;
default: $list .= $key." = ".$sql[$key].","; } }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."commerce_manager_".$table_slug." SET ".substr($list, 0, -1)." WHERE id = ".$_GET['id']); } } }

if (isset($_GET['id'])) {
$item_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."commerce_manager_".$table_slug." WHERE id = ".$_GET['id'], OBJECT);
if ($item_data) { foreach ($item_data as $key => $value) { $_POST[$key] = $value; } }
elseif (!headers_sent()) { header('Location: admin.php?page=commerce-manager-products'.($is_category ? '-categories' : '')); exit(); }
else { echo '<script type="text/javascript">window.location = "admin.php?page=commerce-manager-products'.($is_category ? '-categories' : '').'";</script>'; } }

foreach ($_POST as $key => $value) {
if (is_string($value)) {
$_POST[$key] = str_replace('&amp;amp;', '&amp;', htmlspecialchars(stripslashes($value)));
if ($value == '0000-00-00 00:00:00') { $_POST[$key] = ''; } } }
$undisplayed_modules = (array) $back_office_options[$admin_page.'_page_undisplayed_modules'];
$currency_code = commerce_data('currency_code'); ?>

<div class="wrap">
<div id="poststuff">
<?php commerce_manager_pages_top($back_office_options); ?>
<?php if ($updated) {
echo '<div class="updated"><p><strong>'.(isset($_GET['id']) ? ($is_category ? __('Category updated.', 'commerce-manager') : __('Product updated.', 'commerce-manager')) : ($is_category ? __('Category saved.', 'commerce-manager') : __('Product saved.', 'commerce-manager'))).'</strong></p></div>
'.(isset($_GET['id']) ? '' : '<script type="text/javascript">setTimeout(\'window.location = "admin.php?page=commerce-manager-products'.($is_category ? '-categories' : '').'"\', 2000);</script>'); } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php commerce_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<p class="description"><?php _e('Fields marked with * are required.', 'commerce-manager'); ?> 
<?php if ($_POST['category_id'] > 0) { _e('You can apply the default option of the category by leaving the corresponding field blank.', 'commerce-manager'); } ?></p>
<?php commerce_manager_pages_summary($back_office_options); ?>

<div class="postbox" id="general-informations-module"<?php if (in_array('general-informations', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="general-informations"><strong><?php echo $modules[$admin_page]['general-informations']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<?php if ($_POST['category_id'] > 0) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager-product-category&amp;id=<?php echo $_POST['category_id']; ?>#general-informations">
<?php ($is_category ? _e('Click here to configure the default options of the parent category.', 'commerce-manager') : _e('Click here to configure the default options of the category.', 'commerce-manager')); ?></a></span></td></tr>
<?php } ?>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="id">'.__('ID', 'commerce-manager').'</label></strong></th>
<td><input type="text" name="id" id="id" size="10" value="'.$_GET['id'].'" disabled="disabled" /> <span class="description">'.__('The ID can not be changed.', 'commerce-manager').'</span></td></tr>'; } ?>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."commerce_manager_products_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="category_id"><?php echo ($is_category ? __('Parent category', 'commerce-manager') : __('Category', 'commerce-manager')); ?></label></strong></th>
<td><select name="category_id" id="category_id">
<option value="0"<?php if ($_POST['category_id'] == '0') { echo ' selected="selected"'; } ?>><?php _e('None ', 'commerce-manager'); ?></option>
<?php foreach ($categories as $category) {
if ((!$is_category) || (!in_array($_GET['id'], products_categories_list($category->id)))) {
echo '<option value="'.$category->id.'"'.($_POST['category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } } ?>
</select>
<span class="description"><?php ($is_category ? _e('The options of this category will apply by default to the category.', 'commerce-manager') : _e('The options of this category will apply by default to the product.', 'commerce-manager')); ?></span>
<?php if ($_POST['category_id'] > 0) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-product-category&amp;id='.$_POST['category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-product-category&amp;id='.$_POST['category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['name'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="name"><?php _e('Name', 'commerce-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="name" id="name" rows="1" cols="50"><?php echo $_POST['name']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="price"><?php _e('Price', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="price" id="price" rows="1" cols="50"><?php echo $_POST['price']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="normal_price"><?php _e('Normal price', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="normal_price" id="normal_price" rows="1" cols="50"><?php echo $_POST['normal_price']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="reference"><?php _e('Reference', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="reference" id="reference" rows="1" cols="50"><?php echo $_POST['reference']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="description"><?php _e('Description', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="description" id="description" rows="1" cols="75"><?php echo $_POST['description']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="keywords"><?php _e('Keywords', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="keywords" id="keywords" rows="1" cols="75"><?php echo $_POST['keywords']; ?></textarea><br />
<span class="description"><?php _e('Separate the keywords with commas.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="url"><?php _e('URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="url" id="url" rows="1" cols="75"><?php echo $_POST['url']; ?></textarea> 
<?php $url = htmlspecialchars(product_data(array(0 => 'url', 'part' => 1, $attribute => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'commerce-manager'); ?></a><?php } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="thumbnail_url"><?php _e('Thumbnail URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="thumbnail_url" id="thumbnail_url" rows="1" cols="75"><?php echo $_POST['thumbnail_url']; ?></textarea> 
<?php $url = htmlspecialchars(product_data(array(0 => 'thumbnail_url', 'part' => 1, $attribute => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'commerce-manager'); ?></a><?php } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="downloadable"><?php _e('Downloadable', 'commerce-manager'); ?></label></strong></th>
<td><select name="downloadable" id="downloadable" onchange="if (this.value != 'no') { document.getElementById('download-url').style.display = ''; } else { document.getElementById('download-url').style.display = 'none'; }">
<?php if ($_POST['category_id'] > 0) { ?><option value=""<?php if ($_POST['downloadable'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option><?php } ?>
<option value="no"<?php if ($_POST['downloadable'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['downloadable'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
</select></td></tr>
<tr id="download-url" style="<?php if ((product_data(array(0 => 'downloadable', $attribute => $_GET['id'])) != 'yes') && (product_data(array(0 => 'download_url', $attribute => $_GET['id'])) == '')) { echo 'display: none; '; } ?>vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="download_url"><?php _e('Download URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="download_url" id="download_url" rows="1" cols="75"><?php echo $_POST['download_url']; ?></textarea> 
<?php $url = htmlspecialchars(product_data(array(0 => 'download_url', 'part' => 1, $attribute => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'commerce-manager'); ?></a><?php } ?><br />
<span class="description"><?php _e('You can specify several URLs.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#multiple-urls"><?php _e('More informations', 'commerce-manager'); ?></a> 
<a href="http://www.kleor-editions.com/commerce-manager/documentation/#urls-encryption"><?php _e('How to encrypt a download URL?', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="instructions"><?php _e('Instructions to the customer', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="instructions" id="instructions" rows="9" cols="75"><?php echo $_POST['instructions']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="date"><?php _e('Launch date', 'commerce-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="date" id="date" size="20" value="<?php echo (isset($_POST['date']) ? $_POST['date'] : date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET)); ?>" /></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<?php if (!$is_category) { ?>
<div class="postbox" id="inventory-module"<?php if (in_array('inventory', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="inventory"><strong><?php echo $modules[$admin_page]['inventory']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="available_quantity"><?php _e('Available quantity', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="available_quantity" id="available_quantity" rows="1" cols="25"><?php echo (!is_numeric($_POST['available_quantity']) ? '' : $_POST['available_quantity']); ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for an unlimited quantity.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="sales_count"><?php _e('Sales count', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="sales_count" id="sales_count" rows="1" cols="25"><?php echo $_POST['sales_count']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 0.', 'commerce-manager'); ?></span><br />
<?php if ($_POST['sales_count'] > 0) { echo '<a style="text-decoration: none;" href="admin.php?page=commerce-manager-orders&amp;product_id='.$_GET['id'].'">'.__('Display the sales', 'commerce-manager').'</a>'; } ?>
<?php if (isset($_GET['id'])) { echo '<input type="hidden" name="submit" value="true" />
<input type="submit" class="button-secondary" name="count_sales" value="'.__('Re-count the sales', 'commerce-manager').'" />
<input type="submit" class="button-secondary" name="count_sales_of_all_products" value="'.__('Re-count the sales of all products', 'commerce-manager').'" />'; } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="refunds_count"><?php _e('Refunds count', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="refunds_count" id="refunds_count" rows="1" cols="25"><?php echo $_POST['refunds_count']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank for 0.', 'commerce-manager'); ?></span><br />
<?php if ($_POST['refunds_count'] > 0) { echo '<a style="text-decoration: none;" href="admin.php?page=commerce-manager-orders&amp;product_id='.$_GET['id'].'&amp;status=refunded">'.__('Display the refunds', 'commerce-manager').'</a>'; } ?>
<?php if (isset($_GET['id'])) { echo '<input type="hidden" name="submit" value="true" />
<input type="submit" class="button-secondary" name="count_refunds" value="'.__('Re-count the refunds', 'commerce-manager').'" />
<input type="submit" class="button-secondary" name="count_refunds_of_all_products" value="'.__('Re-count the refunds of all products', 'commerce-manager').'" />'; } ?></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>
<?php } ?>

<div class="postbox" id="order-module"<?php if (in_array('order', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="order"><strong><?php echo $modules[$admin_page]['order']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager<?php echo ($_POST['category_id'] == 0 ? '' : '-product-category&amp;id='.$_POST['category_id'].'#order'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'commerce-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'commerce-manager') : _e('Click here to configure the default options of the category.', 'commerce-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="purchase_button_url"><?php _e('Purchase button URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="purchase_button_url" id="purchase_button_url" rows="1" cols="75"><?php echo $_POST['purchase_button_url']; ?></textarea> 
<?php $url = htmlspecialchars(product_data(array(0 => 'purchase_button_url', 'part' => 1, $attribute => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'commerce-manager'); ?></a><?php } ?><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?><br />
<a href="http://www.kleor-editions.com/commerce-manager/documentation/#purchase-buttons"><?php _e('How to display a purchase button?', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="purchase_button_text"><?php _e('Purchase button text', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="purchase_button_text" id="purchase_button_text" rows="1" cols="25"><?php echo $_POST['purchase_button_text']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Text displayed when the purchase button can not be displayed', 'commerce-manager'); ?><br />
<?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="purchase_link_text"><?php _e('Purchase link text', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="purchase_link_text" id="purchase_link_text" rows="1" cols="75"><?php echo $_POST['purchase_link_text']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?><br />
<a href="http://www.kleor-editions.com/commerce-manager/documentation/#purchase-links"><?php _e('How to display a purchase link?', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_confirmation_url"><?php _e('Order confirmation URL', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_confirmation_url" id="order_confirmation_url" rows="1" cols="75"><?php echo $_POST['order_confirmation_url']; ?></textarea> 
<?php $url = htmlspecialchars(product_data(array(0 => 'order_confirmation_url', 'part' => 1, $attribute => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'commerce-manager'); ?></a><?php } ?><br />
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="orders_initial_status"><?php _e('Orders initial status', 'commerce-manager'); ?></label></strong></th>
<td><select name="orders_initial_status" id="orders_initial_status">
<option value=""<?php if ($_POST['orders_initial_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="unprocessed"<?php if ($_POST['orders_initial_status'] == 'unprocessed') { echo ' selected="selected"'; } ?>><?php _e('Unprocessed', 'commerce-manager'); ?></option>
<option value="processed"<?php if ($_POST['orders_initial_status'] == 'processed') { echo ' selected="selected"'; } ?>><?php _e('Processed', 'commerce-manager'); ?></option>
</select>
<span class="description"><?php _e('Status assigned to orders upon their registration', 'commerce-manager'); ?><br />
<?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="tax-module"<?php if (in_array('tax', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="tax"><strong><?php echo $modules[$admin_page]['tax']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager<?php echo ($_POST['category_id'] == 0 ? '#tax' : '-product-category&amp;id='.$_POST['category_id'].'#tax'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'commerce-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'commerce-manager') : _e('Click here to configure the default options of the category.', 'commerce-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="tax_applied"><?php _e('Apply a tax', 'commerce-manager'); ?></label></strong></th>
<td><select name="tax_applied" id="tax_applied">
<option value=""<?php if ($_POST['tax_applied'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['tax_applied'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['tax_applied'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="default_tax_applied"><?php _e('Apply the tax of the merchant account that receives payments', 'commerce-manager'); ?></label></strong></th>
<td><select name="default_tax_applied" id="default_tax_applied">
<option value=""<?php if ($_POST['default_tax_applied'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['default_tax_applied'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['default_tax_applied'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#tax"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="tax_included_in_price"><?php _e('Include the tax in the price', 'commerce-manager'); ?></label></strong></th>
<td><select name="tax_included_in_price" id="tax_included_in_price">
<option value=""<?php if ($_POST['tax_included_in_price'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['tax_included_in_price'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['tax_included_in_price'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select>
<span class="description"><?php _e('Used if you don\'t apply the tax of the merchant account that receives payments', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="tax_percentage"><?php _e('Tax percentage', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="tax_percentage" id="tax_percentage" rows="1" cols="25"><?php echo $_POST['tax_percentage']; ?></textarea> <span style="vertical-align: 25%;">%</span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you don\'t apply the tax of the merchant account that receives payments', 'commerce-manager'); ?><br />
<?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="shipping-module"<?php if (in_array('shipping', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="shipping"><strong><?php echo $modules[$admin_page]['shipping']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager<?php echo ($_POST['category_id'] == 0 ? '#shipping' : '-product-category&amp;id='.$_POST['category_id'].'#shipping'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'commerce-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'commerce-manager') : _e('Click here to configure the default options of the category.', 'commerce-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="shipping_address_required"><?php _e('Shipping address required', 'commerce-manager'); ?></label></strong></th>
<td><select name="shipping_address_required" id="shipping_address_required">
<option value=""<?php if ($_POST['shipping_address_required'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['shipping_address_required'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['shipping_address_required'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="default_shipping_cost_applied"><?php _e('Apply the shipping cost of the merchant account that receives payments', 'commerce-manager'); ?></label></strong></th>
<td><select name="default_shipping_cost_applied" id="default_shipping_cost_applied">
<option value=""<?php if ($_POST['default_shipping_cost_applied'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['default_shipping_cost_applied'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['default_shipping_cost_applied'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#shipping"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="weight"><?php _e('Weight', 'commerce-manager'); ?></label></strong></th>
<td><input type="text" name="weight" id="weight" size="5" value="<?php echo $_POST['weight']; ?>" /> 
<select name="weight_unit" id="weight_unit">
<option value=""<?php if ($_POST['weight_unit'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="kilogram"<?php if ($_POST['weight_unit'] == 'kilogram') { echo ' selected="selected"'; } ?>><?php _e('kilogram(s)', 'commerce-manager'); ?></option>
<option value="pound"<?php if ($_POST['weight_unit'] == 'pound') { echo ' selected="selected"'; } ?>><?php _e('pound(s)', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="shipping_cost"><?php _e('Shipping cost', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="shipping_cost" id="shipping_cost" rows="1" cols="25"><?php echo $_POST['shipping_cost']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you don\'t apply the shipping cost of the merchant account that receives payments', 'commerce-manager'); ?><br />
<?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="recurring-payments-module"<?php if (in_array('recurring-payments', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="recurring-payments"><strong><?php echo $modules[$admin_page]['recurring-payments']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager<?php echo ($_POST['category_id'] == 0 ? '#recurring-payments' : '-product-category&amp;id='.$_POST['category_id'].'#recurring-payments'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'commerce-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'commerce-manager') : _e('Click here to configure the default options of the category.', 'commerce-manager'))); ?></a> 
<a style="margin-left: 2em;" href="http://www.kleor-editions.com/commerce-manager/documentation/#recurring-payments"><?php _e('How to display a purchase button for recurring payments?', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
<?php for ($i = 1; $i <= 3; $i++) { ?>
<div id="<?php echo 'payment-option'.$i.'-module'; ?>"<?php if (in_array('payment-option'.$i, $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="payment-option<?php echo $i; ?>"><strong><?php echo $modules[$admin_page]['recurring-payments']['modules']['payment-option'.$i]['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_number<?php echo $i; ?>"><?php _e('Payments number', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="payments_number<?php echo $i; ?>" id="payments_number<?php echo $i; ?>" rows="1" cols="25"><?php echo ($_POST['payments_number'.$i] == 'unlimited' ? 'i' : $_POST['payments_number'.$i]); ?></textarea>
<span class="description" style="vertical-align: 25%;"><?php _e('Enter <em><strong>i</strong></em> for an unlimited quantity.', 'commerce-manager'); ?><br />
<?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_amount<?php echo $i; ?>"><?php _e('Payments amount', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="payments_amount<?php echo $i; ?>" id="payments_amount<?php echo $i; ?>" rows="1" cols="50"><?php echo $_POST['payments_amount'.$i]; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_normal_amount<?php echo $i; ?>"><?php _e('Payments normal amount', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="payments_normal_amount<?php echo $i; ?>" id="payments_normal_amount<?php echo $i; ?>" rows="1" cols="50"><?php echo $_POST['payments_normal_amount'.$i]; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_period_quantity<?php echo $i; ?>"><?php _e('Payments period', 'commerce-manager'); ?></label></strong></th>
<td><input type="text" name="payments_period_quantity<?php echo $i; ?>" id="payments_period_quantity<?php echo $i; ?>" size="2" value="<?php echo $_POST['payments_period_quantity'.$i]; ?>" /> 
<select name="payments_period_time_unit<?php echo $i; ?>" id="payments_period_time_unit<?php echo $i; ?>">
<option value=""<?php if ($_POST['payments_period_time_unit'.$i] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="day"<?php if ($_POST['payments_period_time_unit'.$i] == 'day') { echo ' selected="selected"'; } ?>><?php _e('day(s)', 'commerce-manager'); ?></option>
<option value="week"<?php if ($_POST['payments_period_time_unit'.$i] == 'week') { echo ' selected="selected"'; } ?>><?php _e('week(s)', 'commerce-manager'); ?></option>
<option value="month"<?php if ($_POST['payments_period_time_unit'.$i] == 'month') { echo ' selected="selected"'; } ?>><?php _e('month(s)', 'commerce-manager'); ?></option>
<option value="year"<?php if ($_POST['payments_period_time_unit'.$i] == 'year') { echo ' selected="selected"'; } ?>><?php _e('year(s)', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="first_payment_amount_used<?php echo $i; ?>"><?php _e('Use an other amount for the first payment', 'commerce-manager'); ?></label></strong></th>
<td><select name="first_payment_amount_used<?php echo $i; ?>" id="first_payment_amount_used<?php echo $i; ?>">
<option value=""<?php if ($_POST['first_payment_amount_used'.$i] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['first_payment_amount_used'.$i] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['first_payment_amount_used'.$i] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="first_payment_amount<?php echo $i; ?>"><?php _e('First payment\'s amount', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="first_payment_amount<?php echo $i; ?>" id="first_payment_amount<?php echo $i; ?>" rows="1" cols="50"><?php echo $_POST['first_payment_amount'.$i]; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="first_payment_normal_amount<?php echo $i; ?>"><?php _e('First payment\'s normal amount', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="first_payment_normal_amount<?php echo $i; ?>" id="first_payment_normal_amount<?php echo $i; ?>" rows="1" cols="50"><?php echo $_POST['first_payment_normal_amount'.$i]; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="first_payment_period_used<?php echo $i; ?>"><?php _e('Use an other period for the first payment', 'commerce-manager'); ?></label></strong></th>
<td><select name="first_payment_period_used<?php echo $i; ?>" id="first_payment_period_used<?php echo $i; ?>">
<option value=""<?php if ($_POST['first_payment_period_used'.$i] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['first_payment_period_used'.$i] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['first_payment_period_used'.$i] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="first_payment_period_quantity<?php echo $i; ?>"><?php _e('First payment\'s period', 'commerce-manager'); ?></label></strong></th>
<td><input type="text" name="first_payment_period_quantity<?php echo $i; ?>" id="first_payment_period_quantity<?php echo $i; ?>" size="2" value="<?php echo $_POST['first_payment_period_quantity'.$i]; ?>" /> 
<select name="first_payment_period_time_unit<?php echo $i; ?>" id="first_payment_period_time_unit<?php echo $i; ?>">
<option value=""<?php if ($_POST['first_payment_period_time_unit'.$i] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="day"<?php if ($_POST['first_payment_period_time_unit'.$i] == 'day') { echo ' selected="selected"'; } ?>><?php _e('day(s)', 'commerce-manager'); ?></option>
<option value="week"<?php if ($_POST['first_payment_period_time_unit'.$i] == 'week') { echo ' selected="selected"'; } ?>><?php _e('week(s)', 'commerce-manager'); ?></option>
<option value="month"<?php if ($_POST['first_payment_period_time_unit'.$i] == 'month') { echo ' selected="selected"'; } ?>><?php _e('month(s)', 'commerce-manager'); ?></option>
<option value="year"<?php if ($_POST['first_payment_period_time_unit'.$i] == 'year') { echo ' selected="selected"'; } ?>><?php _e('year(s)', 'commerce-manager'); ?></option>
</select></td></tr>
</tbody></table>
</div>
<?php } ?>
<?php if (isset($_GET['id'])) { echo '<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr></tbody></table>'; } ?>
</div></div>

<div class="postbox" id="payment-modes-module"<?php if (in_array('payment-modes', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="payment-modes"><strong><?php echo $modules[$admin_page]['payment-modes']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager<?php echo ($_POST['category_id'] == 0 ? '#payment-modes' : '-product-category&amp;id='.$_POST['category_id'].'#payment-modes'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'commerce-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'commerce-manager') : _e('Click here to configure the default options of the category.', 'commerce-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="paypal_email_address"><?php _e('PayPal email address', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="paypal_email_address" id="paypal_email_address" rows="1" cols="50"><?php echo $_POST['paypal_email_address']; ?></textarea><br />
<span class="description"><?php _e('Email address of the PayPal account that receives payments', 'commerce-manager'); ?><br />
<?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="order-confirmation-email-module"<?php if (in_array('order-confirmation-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="order-confirmation-email"><strong><?php echo $modules[$admin_page]['order-confirmation-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager<?php echo ($_POST['category_id'] == 0 ? '#order-confirmation-email' : '-product-category&amp;id='.$_POST['category_id'].'#order-confirmation-email'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'commerce-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'commerce-manager') : _e('Click here to configure the default options of the category.', 'commerce-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_confirmation_email_sent"><?php _e('Send an order confirmation email', 'commerce-manager'); ?></label></strong></th>
<td><select name="order_confirmation_email_sent" id="order_confirmation_email_sent">
<option value=""<?php if ($_POST['order_confirmation_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['order_confirmation_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['order_confirmation_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_confirmation_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_confirmation_email_sender" id="order_confirmation_email_sender" rows="1" cols="75"><?php echo $_POST['order_confirmation_email_sender']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_confirmation_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_confirmation_email_receiver" id="order_confirmation_email_receiver" rows="1" cols="75"><?php echo $_POST['order_confirmation_email_receiver']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_confirmation_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_confirmation_email_subject" id="order_confirmation_email_subject" rows="1" cols="75"><?php echo $_POST['order_confirmation_email_subject']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_confirmation_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="order_confirmation_email_body" id="order_confirmation_email_body" rows="15" cols="75"><?php echo $_POST['order_confirmation_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="order-notification-email-module"<?php if (in_array('order-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="order-notification-email"><strong><?php echo $modules[$admin_page]['order-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager<?php echo ($_POST['category_id'] == 0 ? '#order-notification-email' : '-product-category&amp;id='.$_POST['category_id'].'#order-notification-email'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'commerce-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'commerce-manager') : _e('Click here to configure the default options of the category.', 'commerce-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_notification_email_sent"><?php _e('Send an order notification email', 'commerce-manager'); ?></label></strong></th>
<td><select name="order_notification_email_sent" id="order_notification_email_sent">
<option value=""<?php if ($_POST['order_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['order_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['order_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_notification_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_notification_email_sender" id="order_notification_email_sender" rows="1" cols="75"><?php echo $_POST['order_notification_email_sender']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_notification_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_notification_email_receiver" id="order_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['order_notification_email_receiver']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_notification_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_notification_email_subject" id="order_notification_email_subject" rows="1" cols="75"><?php echo $_POST['order_notification_email_subject']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_notification_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="order_notification_email_body" id="order_notification_email_body" rows="15" cols="75"><?php echo $_POST['order_notification_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="autoresponders-module"<?php if (in_array('autoresponders', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="autoresponders"><strong><?php echo $modules[$admin_page]['autoresponders']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager<?php echo ($_POST['category_id'] == 0 ? '#autoresponders' : '-product-category&amp;id='.$_POST['category_id'].'#autoresponders'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'commerce-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'commerce-manager') : _e('Click here to configure the default options of the category.', 'commerce-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_subscribed_to_autoresponder"><?php _e('Subscribe the customer to an autoresponder list', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_subscribed_to_autoresponder" id="customer_subscribed_to_autoresponder">
<option value=""<?php if ($_POST['customer_subscribed_to_autoresponder'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['customer_subscribed_to_autoresponder'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['customer_subscribed_to_autoresponder'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_autoresponder"><?php _e('Autoresponder', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_autoresponder" id="customer_autoresponder">
<?php include 'libraries/autoresponders.php';
$autoresponder = do_shortcode($_POST['customer_autoresponder']);
echo '<option value=""'.($autoresponder == '' ? ' selected="selected"' : '').'>'.__('Default option', 'commerce-manager').'</option>'."\n";
foreach ($autoresponders as $value) {
echo '<option value="'.$value.'"'.($autoresponder == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_autoresponder_list"><?php _e('List', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="customer_autoresponder_list" id="customer_autoresponder_list" rows="1" cols="50"><?php echo $_POST['customer_autoresponder_list']; ?></textarea><br />
<span class="description"><?php _e('For some autoresponders, you must enter the list ID.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#autoresponders"><?php _e('More informations', 'commerce-manager'); ?></a><br />
<?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="registration-as-a-client-module"<?php if (in_array('registration-as-a-client', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration-as-a-client"><strong><?php echo $modules[$admin_page]['registration-as-a-client']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=<?php echo ($_POST['category_id'] == 0 ? 'commerce-manager#registration-as-a-client' : 'commerce-manager-product-category&amp;id='.$_POST['category_id'].'#registration-as-a-client'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'commerce-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'commerce-manager') : _e('Click here to configure the default options of the category.', 'commerce-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_subscribed_as_a_client"><?php _e('Subscribe the customer as a client', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_subscribed_as_a_client" id="customer_subscribed_as_a_client">
<option value=""<?php if ($_POST['customer_subscribed_as_a_client'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['customer_subscribed_as_a_client'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['customer_subscribed_as_a_client'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#registration-as-a-client"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."commerce_manager_clients_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_client_category_id"><?php _e('Category', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_client_category_id" id="customer_client_category_id">
<option value=""<?php if ($_POST['customer_client_category_id'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="0"<?php if ($_POST['customer_client_category_id'] == '0') { echo ' selected="selected"'; } ?>><?php _e('None ', 'commerce-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($_POST['customer_client_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ($_POST['customer_client_category_id'] > 0) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-client-category&amp;id='.$_POST['customer_client_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=commerce-manager-client-category&amp;id='.$_POST['customer_client_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_client_status"><?php _e('Status', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_client_status" id="customer_client_status">
<option value=""<?php if ($_POST['customer_client_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="active"<?php if ($_POST['customer_client_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'commerce-manager'); ?></option>
<option value="inactive"<?php if ($_POST['customer_client_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'commerce-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#client-status"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commerce_registration_confirmation_email_sent"><?php _e('Send a registration confirmation email', 'commerce-manager'); ?></label></strong></th>
<td><select name="commerce_registration_confirmation_email_sent" id="commerce_registration_confirmation_email_sent">
<option value=""<?php if ($_POST['commerce_registration_confirmation_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['commerce_registration_confirmation_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['commerce_registration_confirmation_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commerce_registration_notification_email_sent"><?php _e('Send a registration notification email', 'commerce-manager'); ?></label></strong></th>
<td><select name="commerce_registration_notification_email_sent" id="commerce_registration_notification_email_sent">
<option value=""<?php if ($_POST['commerce_registration_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['commerce_registration_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['commerce_registration_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="registration-to-affiliate-program-module"<?php if (in_array('registration-to-affiliate-program', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration-to-affiliate-program"><strong><?php echo $modules[$admin_page]['registration-to-affiliate-program']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php if (function_exists('affiliation_manager_admin_menu')) { ?>
<a href="admin.php?page=<?php echo ($_POST['category_id'] == 0 ? 'commerce-manager#registration-to-affiliate-program' : 'commerce-manager-product-category&amp;id='.$_POST['category_id'].'#registration-to-affiliate-program'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'commerce-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'commerce-manager') : _e('Click here to configure the default options of the category.', 'commerce-manager'))); ?></a>
<?php } else { _e('To use affiliation, you must have installed and activated <a href="http://www.kleor-editions.com/affiliation-manager">Affiliation Manager</a>.', 'commerce-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_subscribed_to_affiliate_program"><?php _e('Subscribe the customer to affiliate program', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_subscribed_to_affiliate_program" id="customer_subscribed_to_affiliate_program">
<option value=""<?php if ($_POST['customer_subscribed_to_affiliate_program'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['customer_subscribed_to_affiliate_program'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['customer_subscribed_to_affiliate_program'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#registration-to-affiliate-program"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."affiliation_manager_affiliates_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_affiliate_category_id"><?php _e('Category', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_affiliate_category_id" id="customer_affiliate_category_id">
<option value=""<?php if ($_POST['customer_affiliate_category_id'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="0"<?php if ($_POST['customer_affiliate_category_id'] == '0') { echo ' selected="selected"'; } ?>><?php _e('None ', 'commerce-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($_POST['customer_affiliate_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ((function_exists('affiliation_manager_admin_menu')) && ($_POST['customer_affiliate_category_id'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$_POST['customer_affiliate_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=affiliation-manager-affiliate-category&amp;id='.$_POST['customer_affiliate_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_affiliate_status"><?php _e('Status', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_affiliate_status" id="customer_affiliate_status">
<option value=""<?php if ($_POST['customer_affiliate_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="active"<?php if ($_POST['customer_affiliate_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'commerce-manager'); ?></option>
<option value="inactive"<?php if ($_POST['customer_affiliate_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'commerce-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/affiliation-manager/documentation/#affiliate-status"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliation_registration_confirmation_email_sent"><?php _e('Send a registration confirmation email', 'commerce-manager'); ?></label></strong></th>
<td><select name="affiliation_registration_confirmation_email_sent" id="affiliation_registration_confirmation_email_sent">
<option value=""<?php if ($_POST['affiliation_registration_confirmation_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['affiliation_registration_confirmation_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['affiliation_registration_confirmation_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliation_registration_notification_email_sent"><?php _e('Send a registration notification email', 'commerce-manager'); ?></label></strong></th>
<td><select name="affiliation_registration_notification_email_sent" id="affiliation_registration_notification_email_sent">
<option value=""<?php if ($_POST['affiliation_registration_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['affiliation_registration_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['affiliation_registration_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="membership-module"<?php if (in_array('membership', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="membership"><strong><?php echo $modules[$admin_page]['membership']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php if (function_exists('membership_manager_admin_menu')) { ?>
<a href="admin.php?page=<?php echo ($_POST['category_id'] == 0 ? 'commerce-manager#membership' : 'commerce-manager-product-category&amp;id='.$_POST['category_id'].'#membership'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'commerce-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'commerce-manager') : _e('Click here to configure the default options of the category.', 'commerce-manager'))); ?></a>
<?php } else { _e('To use membership, you must have installed and activated <a href="http://www.kleor-editions.com/membership-manager">Membership Manager</a>.', 'commerce-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_subscribed_to_members_areas"><?php _e('Subscribe the customer to a member area', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_subscribed_to_members_areas" id="customer_subscribed_to_members_areas">
<option value=""<?php if ($_POST['customer_subscribed_to_members_areas'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['customer_subscribed_to_members_areas'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['customer_subscribed_to_members_areas'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#membership"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_members_areas"><?php _e('Members areas', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="customer_members_areas" id="customer_members_areas" rows="1" cols="50"><?php echo $_POST['customer_members_areas']; ?></textarea>
<?php if ((function_exists('membership_manager_admin_menu')) && (is_numeric($_POST['customer_members_areas'])) && ($_POST['customer_members_areas'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-area&amp;id='.$_POST['customer_members_areas'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-area&amp;id='.$_POST['customer_members_areas'].'&amp;action=delete">'.__('Delete').'</a>'; } ?><br />
<span class="description"><?php _e('Separate the IDs of the members areas with commas.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_unsubscribed_from_members_areas"><?php _e('Unsubscribe the customer from this member area when his order is refunded or when his recurring payments profile is deactivated', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_unsubscribed_from_members_areas" id="customer_unsubscribed_from_members_areas">
<option value=""<?php if ($_POST['customer_unsubscribed_from_members_areas'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['customer_unsubscribed_from_members_areas'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['customer_unsubscribed_from_members_areas'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."membership_manager_members_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_member_category_id"><?php _e('Category', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_member_category_id" id="customer_member_category_id">
<option value=""<?php if ($_POST['customer_member_category_id'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="0"<?php if ($_POST['customer_member_category_id'] == '0') { echo ' selected="selected"'; } ?>><?php _e('None ', 'commerce-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($_POST['customer_member_category_id'] == $category->id ? ' selected="selected"' : '').'>'.do_shortcode($category->name).'</option>'."\n"; } ?>
</select>
<?php if ((function_exists('membership_manager_admin_menu')) && ($_POST['customer_member_category_id'] > 0)) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-category&amp;id='.$_POST['customer_member_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-category&amp;id='.$_POST['customer_member_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_member_status"><?php _e('Status', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_member_status" id="customer_member_status">
<option value=""<?php if ($_POST['customer_member_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="active"<?php if ($_POST['customer_member_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'commerce-manager'); ?></option>
<option value="inactive"<?php if ($_POST['customer_member_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'commerce-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#member-status"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="membership_registration_confirmation_email_sent"><?php _e('Send a registration confirmation email', 'commerce-manager'); ?></label></strong></th>
<td><select name="membership_registration_confirmation_email_sent" id="membership_registration_confirmation_email_sent">
<option value=""<?php if ($_POST['membership_registration_confirmation_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['membership_registration_confirmation_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['membership_registration_confirmation_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="membership_registration_notification_email_sent"><?php _e('Send a registration notification email', 'commerce-manager'); ?></label></strong></th>
<td><select name="membership_registration_notification_email_sent" id="membership_registration_notification_email_sent">
<option value=""<?php if ($_POST['membership_registration_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['membership_registration_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['membership_registration_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="wordpress-module"<?php if (in_array('wordpress', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="wordpress"><strong><?php echo $modules[$admin_page]['wordpress']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager<?php echo ($_POST['category_id'] == 0 ? '#wordpress' : '-product-category&amp;id='.$_POST['category_id'].'#wordpress'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'commerce-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'commerce-manager') : _e('Click here to configure the default options of the category.', 'commerce-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_subscribed_as_a_user"><?php _e('Subscribe the customer as a user', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_subscribed_as_a_user" id="customer_subscribed_as_a_user">
<option value=""<?php if ($_POST['customer_subscribed_as_a_user'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['customer_subscribed_as_a_user'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['customer_subscribed_as_a_user'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select>
<span class="description"><a href="http://www.kleor-editions.com/commerce-manager/documentation/#wordpress"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="customer_user_role"><?php _e('Role', 'commerce-manager'); ?></label></strong></th>
<td><select name="customer_user_role" id="customer_user_role">
<option value=""<?php if ($_POST['customer_user_role'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<?php foreach (commerce_manager_users_roles() as $role => $name) {
echo '<option value="'.$role.'"'.($_POST['customer_user_role'] == $role ? ' selected="selected"' : '').'>'.$name.'</option>'."\n"; } ?>
</select></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="custom-instructions-module"<?php if (in_array('custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="custom-instructions"><strong><?php echo $modules[$admin_page]['custom-instructions']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager<?php echo ($_POST['category_id'] == 0 ? '#custom-instructions' : '-product-category&amp;id='.$_POST['category_id'].'#custom-instructions'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'commerce-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'commerce-manager') : _e('Click here to configure the default options of the category.', 'commerce-manager'))); ?></a></span></td></tr>
</tbody></table>
<div id="order-custom-instructions-module"<?php if (in_array('order-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="order-custom-instructions"><strong><?php echo $modules[$admin_page]['custom-instructions']['modules']['order-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_custom_instructions_executed"><?php _e('Execute custom instructions', 'commerce-manager'); ?></label></strong></th>
<td><select name="order_custom_instructions_executed" id="order_custom_instructions_executed">
<option value=""<?php if ($_POST['order_custom_instructions_executed'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['order_custom_instructions_executed'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['order_custom_instructions_executed'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_custom_instructions"><?php _e('PHP code', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="order_custom_instructions" id="order_custom_instructions" rows="10" cols="75"><?php echo $_POST['order_custom_instructions']; ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the registration of an order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#custom-instructions"><?php _e('More informations', 'commerce-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
</tbody></table>
</div>
<div id="order-processing-custom-instructions-module"<?php if (in_array('order-processing-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="order-processing-custom-instructions"><strong><?php echo $modules[$admin_page]['custom-instructions']['modules']['order-processing-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_processing_custom_instructions_executed"><?php _e('Execute custom instructions', 'commerce-manager'); ?></label></strong></th>
<td><select name="order_processing_custom_instructions_executed" id="order_processing_custom_instructions_executed">
<option value=""<?php if ($_POST['order_processing_custom_instructions_executed'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['order_processing_custom_instructions_executed'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['order_processing_custom_instructions_executed'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_processing_custom_instructions"><?php _e('PHP code', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="order_processing_custom_instructions" id="order_processing_custom_instructions" rows="10" cols="75"><?php echo $_POST['order_processing_custom_instructions']; ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the processing of an order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#custom-instructions"><?php _e('More informations', 'commerce-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
</tbody></table>
</div>
<div id="order-refund-custom-instructions-module"<?php if (in_array('order-refund-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="order-refund-custom-instructions"><strong><?php echo $modules[$admin_page]['custom-instructions']['modules']['order-refund-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_refund_custom_instructions_executed"><?php _e('Execute custom instructions', 'commerce-manager'); ?></label></strong></th>
<td><select name="order_refund_custom_instructions_executed" id="order_refund_custom_instructions_executed">
<option value=""<?php if ($_POST['order_refund_custom_instructions_executed'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['order_refund_custom_instructions_executed'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['order_refund_custom_instructions_executed'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_refund_custom_instructions"><?php _e('PHP code', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="order_refund_custom_instructions" id="order_refund_custom_instructions" rows="10" cols="75"><?php echo $_POST['order_refund_custom_instructions']; ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the refund of an order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#custom-instructions"><?php _e('More informations', 'commerce-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
</tbody></table>
</div>
<div id="recurring-payment-custom-instructions-module"<?php if (in_array('recurring-payment-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="recurring-payment-custom-instructions"><strong><?php echo $modules[$admin_page]['custom-instructions']['modules']['recurring-payment-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_custom_instructions_executed"><?php _e('Execute custom instructions', 'commerce-manager'); ?></label></strong></th>
<td><select name="recurring_payment_custom_instructions_executed" id="recurring_payment_custom_instructions_executed">
<option value=""<?php if ($_POST['recurring_payment_custom_instructions_executed'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['recurring_payment_custom_instructions_executed'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['recurring_payment_custom_instructions_executed'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_custom_instructions"><?php _e('PHP code', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="recurring_payment_custom_instructions" id="recurring_payment_custom_instructions" rows="10" cols="75"><?php echo $_POST['recurring_payment_custom_instructions']; ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the registration of a recurring payment.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#custom-instructions"><?php _e('More informations', 'commerce-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
</tbody></table>
</div>
<div id="recurring-payment-refund-custom-instructions-module"<?php if (in_array('recurring-payment-refund-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="recurring-payment-refund-custom-instructions"><strong><?php echo $modules[$admin_page]['custom-instructions']['modules']['recurring-payment-refund-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_refund_custom_instructions_executed"><?php _e('Execute custom instructions', 'commerce-manager'); ?></label></strong></th>
<td><select name="recurring_payment_refund_custom_instructions_executed" id="recurring_payment_refund_custom_instructions_executed">
<option value=""<?php if ($_POST['recurring_payment_refund_custom_instructions_executed'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['recurring_payment_refund_custom_instructions_executed'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['recurring_payment_refund_custom_instructions_executed'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_refund_custom_instructions"><?php _e('PHP code', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="recurring_payment_refund_custom_instructions" id="recurring_payment_refund_custom_instructions" rows="10" cols="75"><?php echo $_POST['recurring_payment_refund_custom_instructions']; ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the refund of a recurring payment.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#custom-instructions"><?php _e('More informations', 'commerce-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
</tbody></table>
</div>
<div id="recurring-payments-profile-deactivation-custom-instructions-module"<?php if (in_array('recurring-payments-profile-deactivation-custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="recurring-payments-profile-deactivation-custom-instructions"><strong><?php echo $modules[$admin_page]['custom-instructions']['modules']['recurring-payments-profile-deactivation-custom-instructions']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_profile_deactivation_custom_instructions_executed"><?php _e('Execute custom instructions', 'commerce-manager'); ?></label></strong></th>
<td><select name="payments_profile_deactivation_custom_instructions_executed" id="payments_profile_deactivation_custom_instructions_executed">
<option value=""<?php if ($_POST['payments_profile_deactivation_custom_instructions_executed'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['payments_profile_deactivation_custom_instructions_executed'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['payments_profile_deactivation_custom_instructions_executed'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_profile_deactivation_custom_instructions"><?php _e('PHP code', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="payments_profile_deactivation_custom_instructions" id="payments_profile_deactivation_custom_instructions" rows="10" cols="75"><?php echo $_POST['payments_profile_deactivation_custom_instructions']; ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the deactivation of a recurring payments profile.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#custom-instructions"><?php _e('More informations', 'commerce-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
</tbody></table>
</div>
<?php if (isset($_GET['id'])) { echo '<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr></tbody></table>'; } ?>
</div></div>

<div class="postbox" id="redelivery-email-module"<?php if (in_array('redelivery-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="redelivery-email"><strong><?php echo $modules[$admin_page]['redelivery-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager<?php echo ($_POST['category_id'] == 0 ? '#redelivery-email' : '-product-category&amp;id='.$_POST['category_id'].'#redelivery-email'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'commerce-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'commerce-manager') : _e('Click here to configure the default options of the category.', 'commerce-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="redelivery_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="redelivery_email_sender" id="redelivery_email_sender" rows="1" cols="75"><?php echo $_POST['redelivery_email_sender']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="redelivery_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="redelivery_email_receiver" id="redelivery_email_receiver" rows="1" cols="75"><?php echo $_POST['redelivery_email_receiver']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="redelivery_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="redelivery_email_subject" id="redelivery_email_subject" rows="1" cols="75"><?php echo $_POST['redelivery_email_subject']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="redelivery_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="redelivery_email_body" id="redelivery_email_body" rows="15" cols="75"><?php echo $_POST['redelivery_email_body']; ?></textarea>
<span class="description"><?php _e('You can allow your customers to get an automatic redelivery of a downloadable product they ordered.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#automatic-redelivery"><?php _e('More informations', 'commerce-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="redelivery-notification-email-module"<?php if (in_array('redelivery-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="redelivery-notification-email"><strong><?php echo $modules[$admin_page]['redelivery-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager<?php echo ($_POST['category_id'] == 0 ? '#redelivery-notification-email' : '-product-category&amp;id='.$_POST['category_id'].'#redelivery-notification-email'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'commerce-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'commerce-manager') : _e('Click here to configure the default options of the category.', 'commerce-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="redelivery_notification_email_sent"><?php _e('Send a redelivery notification email', 'commerce-manager'); ?></label></strong></th>
<td><select name="redelivery_notification_email_sent" id="redelivery_notification_email_sent">
<option value=""<?php if ($_POST['redelivery_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['redelivery_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['redelivery_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="redelivery_notification_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="redelivery_notification_email_sender" id="redelivery_notification_email_sender" rows="1" cols="75"><?php echo $_POST['redelivery_notification_email_sender']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="redelivery_notification_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="redelivery_notification_email_receiver" id="redelivery_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['redelivery_notification_email_receiver']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="redelivery_notification_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="redelivery_notification_email_subject" id="redelivery_notification_email_subject" rows="1" cols="75"><?php echo $_POST['redelivery_notification_email_subject']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="redelivery_notification_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="redelivery_notification_email_body" id="redelivery_notification_email_body" rows="15" cols="75"><?php echo $_POST['redelivery_notification_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="order-processing-notification-email-module"<?php if (in_array('order-processing-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="order-processing-notification-email"><strong><?php echo $modules[$admin_page]['order-processing-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager<?php echo ($_POST['category_id'] == 0 ? '#order-processing-notification-email' : '-product-category&amp;id='.$_POST['category_id'].'#order-processing-notification-email'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'commerce-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'commerce-manager') : _e('Click here to configure the default options of the category.', 'commerce-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_processing_notification_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_processing_notification_email_sender" id="order_processing_notification_email_sender" rows="1" cols="75"><?php echo $_POST['order_processing_notification_email_sender']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_processing_notification_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_processing_notification_email_receiver" id="order_processing_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['order_processing_notification_email_receiver']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_processing_notification_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_processing_notification_email_subject" id="order_processing_notification_email_subject" rows="1" cols="75"><?php echo $_POST['order_processing_notification_email_subject']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_processing_notification_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="order_processing_notification_email_body" id="order_processing_notification_email_body" rows="15" cols="75"><?php echo $_POST['order_processing_notification_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="order-refund-notification-email-module"<?php if (in_array('order-refund-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="order-refund-notification-email"><strong><?php echo $modules[$admin_page]['order-refund-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager<?php echo ($_POST['category_id'] == 0 ? '#order-refund-notification-email' : '-product-category&amp;id='.$_POST['category_id'].'#order-refund-notification-email'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'commerce-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'commerce-manager') : _e('Click here to configure the default options of the category.', 'commerce-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_refund_notification_email_sent"><?php _e('Send an order refund\'s notification email', 'commerce-manager'); ?></label></strong></th>
<td><select name="order_refund_notification_email_sent" id="order_refund_notification_email_sent">
<option value=""<?php if ($_POST['order_refund_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['order_refund_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['order_refund_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_refund_notification_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_refund_notification_email_sender" id="order_refund_notification_email_sender" rows="1" cols="75"><?php echo $_POST['order_refund_notification_email_sender']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_refund_notification_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_refund_notification_email_receiver" id="order_refund_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['order_refund_notification_email_receiver']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_refund_notification_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="order_refund_notification_email_subject" id="order_refund_notification_email_subject" rows="1" cols="75"><?php echo $_POST['order_refund_notification_email_subject']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="order_refund_notification_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="order_refund_notification_email_body" id="order_refund_notification_email_body" rows="15" cols="75"><?php echo $_POST['order_refund_notification_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="recurring-payment-confirmation-email-module"<?php if (in_array('recurring-payment-confirmation-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="recurring-payment-confirmation-email"><strong><?php echo $modules[$admin_page]['recurring-payment-confirmation-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager<?php echo ($_POST['category_id'] == 0 ? '#recurring-payment-confirmation-email' : '-product-category&amp;id='.$_POST['category_id'].'#recurring-payment-confirmation-email'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'commerce-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'commerce-manager') : _e('Click here to configure the default options of the category.', 'commerce-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_confirmation_email_sent"><?php _e('Send a recurring payment confirmation email', 'commerce-manager'); ?></label></strong></th>
<td><select name="recurring_payment_confirmation_email_sent" id="recurring_payment_confirmation_email_sent">
<option value=""<?php if ($_POST['recurring_payment_confirmation_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['recurring_payment_confirmation_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['recurring_payment_confirmation_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_confirmation_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_confirmation_email_sender" id="recurring_payment_confirmation_email_sender" rows="1" cols="75"><?php echo $_POST['recurring_payment_confirmation_email_sender']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_confirmation_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_confirmation_email_receiver" id="recurring_payment_confirmation_email_receiver" rows="1" cols="75"><?php echo $_POST['recurring_payment_confirmation_email_receiver']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_confirmation_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_confirmation_email_subject" id="recurring_payment_confirmation_email_subject" rows="1" cols="75"><?php echo $_POST['recurring_payment_confirmation_email_subject']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_confirmation_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="recurring_payment_confirmation_email_body" id="recurring_payment_confirmation_email_body" rows="15" cols="75"><?php echo $_POST['recurring_payment_confirmation_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product, the order and the payment.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="recurring-payment-notification-email-module"<?php if (in_array('recurring-payment-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="recurring-payment-notification-email"><strong><?php echo $modules[$admin_page]['recurring-payment-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager<?php echo ($_POST['category_id'] == 0 ? '#recurring-payment-notification-email' : '-product-category&amp;id='.$_POST['category_id'].'#recurring-payment-notification-email'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'commerce-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'commerce-manager') : _e('Click here to configure the default options of the category.', 'commerce-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_notification_email_sent"><?php _e('Send a recurring payment notification email', 'commerce-manager'); ?></label></strong></th>
<td><select name="recurring_payment_notification_email_sent" id="recurring_payment_notification_email_sent">
<option value=""<?php if ($_POST['recurring_payment_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['recurring_payment_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['recurring_payment_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_notification_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_notification_email_sender" id="recurring_payment_notification_email_sender" rows="1" cols="75"><?php echo $_POST['recurring_payment_notification_email_sender']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_notification_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_notification_email_receiver" id="recurring_payment_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['recurring_payment_notification_email_receiver']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_notification_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_notification_email_subject" id="recurring_payment_notification_email_subject" rows="1" cols="75"><?php echo $_POST['recurring_payment_notification_email_subject']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_notification_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="recurring_payment_notification_email_body" id="recurring_payment_notification_email_body" rows="15" cols="75"><?php echo $_POST['recurring_payment_notification_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product, the order and the payment.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="recurring-payment-refund-notification-email-module"<?php if (in_array('recurring-payment-refund-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="recurring-payment-refund-notification-email"><strong><?php echo $modules[$admin_page]['recurring-payment-refund-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager<?php echo ($_POST['category_id'] == 0 ? '#recurring-payment-refund-notification-email' : '-product-category&amp;id='.$_POST['category_id'].'#recurring-payment-refund-notification-email'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'commerce-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'commerce-manager') : _e('Click here to configure the default options of the category.', 'commerce-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_refund_notification_email_sent"><?php _e('Send a recurring payment refund\'s notification email', 'commerce-manager'); ?></label></strong></th>
<td><select name="recurring_payment_refund_notification_email_sent" id="recurring_payment_refund_notification_email_sent">
<option value=""<?php if ($_POST['recurring_payment_refund_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['recurring_payment_refund_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['recurring_payment_refund_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_refund_notification_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_refund_notification_email_sender" id="recurring_payment_refund_notification_email_sender" rows="1" cols="75"><?php echo $_POST['recurring_payment_refund_notification_email_sender']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_refund_notification_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_refund_notification_email_receiver" id="recurring_payment_refund_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['recurring_payment_refund_notification_email_receiver']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_refund_notification_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="recurring_payment_refund_notification_email_subject" id="recurring_payment_refund_notification_email_subject" rows="1" cols="75"><?php echo $_POST['recurring_payment_refund_notification_email_subject']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recurring_payment_refund_notification_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="recurring_payment_refund_notification_email_body" id="recurring_payment_refund_notification_email_body" rows="15" cols="75"><?php echo $_POST['recurring_payment_refund_notification_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product, the order and the payment.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="recurring-payments-profile-deactivation-notification-email-module"<?php if (in_array('recurring-payments-profile-deactivation-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="recurring-payments-profile-deactivation-notification-email"><strong><?php echo $modules[$admin_page]['recurring-payments-profile-deactivation-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=commerce-manager<?php echo ($_POST['category_id'] == 0 ? '#recurring-payments-profile-deactivation-notification-email' : '-product-category&amp;id='.$_POST['category_id'].'#recurring-payments-profile-deactivation-notification-email'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'commerce-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'commerce-manager') : _e('Click here to configure the default options of the category.', 'commerce-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_profile_deactivation_notification_email_sent"><?php _e('Send a recurring payments profile deactivation\'s notification email', 'commerce-manager'); ?></label></strong></th>
<td><select name="payments_profile_deactivation_notification_email_sent" id="payments_profile_deactivation_notification_email_sent">
<option value=""<?php if ($_POST['payments_profile_deactivation_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['payments_profile_deactivation_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['payments_profile_deactivation_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_profile_deactivation_notification_email_sender"><?php _e('Sender', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="payments_profile_deactivation_notification_email_sender" id="payments_profile_deactivation_notification_email_sender" rows="1" cols="75"><?php echo $_POST['payments_profile_deactivation_notification_email_sender']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_profile_deactivation_notification_email_receiver"><?php _e('Receiver', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="payments_profile_deactivation_notification_email_receiver" id="payments_profile_deactivation_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['payments_profile_deactivation_notification_email_receiver']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_profile_deactivation_notification_email_subject"><?php _e('Subject', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="payments_profile_deactivation_notification_email_subject" id="payments_profile_deactivation_notification_email_subject" rows="1" cols="75"><?php echo $_POST['payments_profile_deactivation_notification_email_subject']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="payments_profile_deactivation_notification_email_body"><?php _e('Body', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="payments_profile_deactivation_notification_email_body" id="payments_profile_deactivation_notification_email_body" rows="15" cols="75"><?php echo $_POST['payments_profile_deactivation_notification_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the customer, the product and the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/commerce-manager/documentation/#email-shortcodes"><?php _e('More informations', 'commerce-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox" id="affiliation-module"<?php if (in_array('affiliation', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="affiliation"><strong><?php echo $modules[$admin_page]['affiliation']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php if (function_exists('affiliation_manager_admin_menu')) { ?>
<a href="admin.php?page=<?php echo ($_POST['category_id'] == 0 ? 'affiliation-manager' : 'commerce-manager-product-category&amp;id='.$_POST['category_id'].'#affiliation'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'commerce-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'commerce-manager') : _e('Click here to configure the default options of the category.', 'commerce-manager'))); ?></a>
<?php } else { _e('To use affiliation, you must have installed and activated <a href="http://www.kleor-editions.com/affiliation-manager">Affiliation Manager</a>.', 'commerce-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="affiliation_enabled"><?php _e('Use affiliation', 'commerce-manager'); ?></label></strong></th>
<td><select name="affiliation_enabled" id="affiliation_enabled">
<option value=""<?php if ($_POST['affiliation_enabled'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['affiliation_enabled'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['affiliation_enabled'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
</tbody></table>
<div id="level-1-commission-module"<?php if (in_array('level-1-commission', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="level-1-commission"><strong><?php echo $modules[$admin_page]['affiliation']['modules']['level-1-commission']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('The level 1 commission is awarded to the affiliate who referred the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#commissions-levels"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_type"><?php _e('Type', 'commerce-manager'); ?></label></strong></th>
<td><select name="commission_type" id="commission_type">
<option value=""<?php if ($_POST['commission_type'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="constant"<?php if ($_POST['commission_type'] == 'constant') { echo ' selected="selected"'; } ?>><?php _e('Constant', 'commerce-manager'); ?></option>
<option value="proportional"<?php if ($_POST['commission_type'] == 'proportional') { echo ' selected="selected"'; } ?>><?php _e('Proportional', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_amount"><?php _e('Amount', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_amount" id="commission_amount" rows="1" cols="25"><?php echo $_POST['commission_amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you select constant commissions', 'commerce-manager'); ?><br />
<?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_percentage"><?php _e('Percentage', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission_percentage" id="commission_percentage" rows="1" cols="25"><?php echo $_POST['commission_percentage']; ?></textarea> <span style="vertical-align: 25%;">% <?php _e('of the price', 'commerce-manager'); ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you select proportional commissions', 'commerce-manager'); ?><br />
<?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission_payment"><?php _e('Payment', 'commerce-manager'); ?></label></strong></th>
<td><select name="commission_payment" id="commission_payment">
<option value=""<?php if ($_POST['commission_payment'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="deferred"<?php if ($_POST['commission_payment'] == 'deferred') { echo ' selected="selected"'; } ?>><?php _e('Deferred', 'commerce-manager'); ?></option>
<option value="instant"<?php if ($_POST['commission_payment'] == 'instant') { echo ' selected="selected"'; } ?>><?php _e('Instant', 'commerce-manager'); ?></option>
</select>
<span class="description"><?php _e('You can pay your affiliates instantly.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#commission-payment"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="first_sale_winner"><?php _e('First sale award', 'commerce-manager'); ?></label></strong></th>
<td><?php _e('The first sale referred by the affiliate is awarded to the', 'commerce-manager'); ?> <select name="first_sale_winner" id="first_sale_winner">
<option value=""<?php if ($_POST['first_sale_winner'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="affiliate"<?php if ($_POST['first_sale_winner'] == 'affiliate') { echo ' selected="selected"'; } ?>><?php _e('affiliate', 'commerce-manager'); ?></option>
<option value="affiliator"<?php if ($_POST['first_sale_winner'] == 'affiliator') { echo ' selected="selected"'; } ?>><?php _e('affiliator', 'commerce-manager'); ?></option>
</select>. 
<span class="description"><?php _e('Used for instant payment of commissions', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#first-sale-award"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_required"><?php _e('Registration to the affiliate program required', 'commerce-manager'); ?></label></strong></th>
<td><select name="registration_required" id="registration_required">
<option value=""<?php if ($_POST['registration_required'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['registration_required'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['registration_required'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select>
<span class="description"><?php _e('The registration can be optional, only if you select instant payment of commissions.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#optional-registration"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div id="level-2-commission-module"<?php if (in_array('level-2-commission', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="level-2-commission"><strong><?php echo $modules[$admin_page]['affiliation']['modules']['level-2-commission']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('The level 2 commission is awarded to the referrer of the affiliate who referred the order.', 'commerce-manager'); ?> <a href="http://www.kleor-editions.com/affiliation-manager/documentation/#commissions-levels"><?php _e('More informations', 'commerce-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_enabled"><?php _e('Award a level 2 commission', 'commerce-manager'); ?></label></strong></th>
<td><select name="commission2_enabled" id="commission2_enabled">
<option value=""<?php if ($_POST['commission2_enabled'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="yes"<?php if ($_POST['commission2_enabled'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'commerce-manager'); ?></option>
<option value="no"<?php if ($_POST['commission2_enabled'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_type"><?php _e('Type', 'commerce-manager'); ?></label></strong></th>
<td><select name="commission2_type" id="commission2_type">
<option value=""<?php if ($_POST['commission2_type'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'commerce-manager'); ?></option>
<option value="constant"<?php if ($_POST['commission2_type'] == 'constant') { echo ' selected="selected"'; } ?>><?php _e('Constant', 'commerce-manager'); ?></option>
<option value="proportional"<?php if ($_POST['commission2_type'] == 'proportional') { echo ' selected="selected"'; } ?>><?php _e('Proportional', 'commerce-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_amount"><?php _e('Amount', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission2_amount" id="commission2_amount" rows="1" cols="25"><?php echo $_POST['commission2_amount']; ?></textarea> <span style="vertical-align: 25%;"><?php echo $currency_code; ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you select constant commissions', 'commerce-manager'); ?><br />
<?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="commission2_percentage"><?php _e('Percentage', 'commerce-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="commission2_percentage" id="commission2_percentage" rows="1" cols="25"><?php echo $_POST['commission2_percentage']; ?></textarea> <span style="vertical-align: 25%;">% <?php _e('of the price', 'commerce-manager'); ?></span> 
<span class="description" style="vertical-align: 25%;"><?php _e('Used if you select proportional commissions', 'commerce-manager'); ?><br />
<?php _e('Leave this field blank to apply the default option.', 'commerce-manager'); ?></span></td></tr>
</tbody></table>
</div>
<?php if (isset($_GET['id'])) { echo '<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr></tbody></table>'; } ?>
</div></div>

<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php (isset($_GET['id']) ?  _e('Save Changes', 'commerce-manager') : ($is_category ? _e('Save Category', 'commerce-manager') : _e('Save Product', 'commerce-manager'))); ?>" /></p>
<?php if ($is_category) { $module = 'product-category-page'; } else { $module = 'product-page'; }
commerce_manager_pages_module($back_office_options, $module, $undisplayed_modules); ?>
</form>
</div>
</div>
<?php }