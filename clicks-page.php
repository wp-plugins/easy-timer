<?php if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

global $wpdb;
$table_name = $wpdb->prefix.'affiliation_manager_clicks';
$no_items = __('No clicks', 'affiliation-manager');
$_GET['singular'] = __('click', 'affiliation-manager');
$_GET['plural'] = __('clicks', 'affiliation-manager');

$_GET['columns_names'] = array(
'id' => __('ID', 'affiliation-manager'),
'referrer' => __('Referrer', 'affiliation-manager'),
'date' => __('Date', 'affiliation-manager'),
'date_utc' => __('Date (UTC)', 'affiliation-manager'),
'user_agent' => __('User agent', 'affiliation-manager'),
'ip_address' => __('IP address', 'affiliation-manager'),
'url' => __('URL', 'affiliation-manager'),
'referring_url' => __('Referring URL', 'affiliation-manager'));

$_GET['columns_widths'] = array(
'id' => 5,
'referrer' => 12,
'date' => 18,
'date_utc' => 18,
'user_agent' => 24,
'ip_address' => 12,
'url' => 18,
'referring_url' => 18);

$searchby_options = array(
'id' => __('the ID', 'affiliation-manager'),
'referrer' => __('the referrer', 'affiliation-manager'),
'date' => __('the date', 'affiliation-manager'),
'date_utc' => __('the date (UTC)', 'affiliation-manager'),
'user_agent' => __('the user agent', 'affiliation-manager'),
'ip_address' => __('the IP address', 'affiliation-manager'),
'url' => __('the URL', 'affiliation-manager'),
'referring_url' => __('the referring URL', 'affiliation-manager'));

include 'list-pages.php';