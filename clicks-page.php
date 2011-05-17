<?php if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

if ((isset($_GET['id'])) && ($_GET['action'] == 'delete')) {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
global $wpdb;
$clicks_table_name = $wpdb->prefix.'affiliation_manager_clicks';
$results = $wpdb->query("DELETE FROM $clicks_table_name WHERE id = '".$_GET['id']."'"); } ?>
<div class="wrap">
<div id="poststuff">
<?php affiliation_manager_pages_top(); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Click deleted.', 'affiliation-manager').'</strong></p></div>'; } ?>
<?php affiliation_manager_pages_menu(); ?>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php _e('Do you really want to permanently delete this click?', 'affiliation-manager'); ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'affiliation-manager'); ?>" />
</div>
</form><?php } ?>
</div>
</div><?php }

else {
$searchby_options = array(
'id' => __('the ID', 'affiliation-manager'),
'referrer' => __('the referrer', 'affiliation-manager'),
'date' => __('the date', 'affiliation-manager'),
'date_utc' => __('the date (UTC)', 'affiliation-manager'),
'user_agent' => __('the user agent', 'affiliation-manager'),
'ip_address' => __('the IP address', 'affiliation-manager'),
'url' => __('the URL', 'affiliation-manager'),
'referring_url' => __('the referring URL', 'affiliation-manager'));

include_once 'tables/clicks.php';
include_once 'tables/functions.php';
include 'tables/page.php'; }