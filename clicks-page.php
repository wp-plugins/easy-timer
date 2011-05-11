<?php if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

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
include 'tables/page.php';