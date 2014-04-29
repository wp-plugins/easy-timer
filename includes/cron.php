<?php $cron = get_option('audiobooks_authors_and_narrators_cron');
if ($cron) {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$current_time = time();
$installation = (array) $cron['previous_installation'];
if ($installation['version'] != AUDIOBOOKS_AUTHORS_AND_NARRATORS_VERSION) {
$cron['previous_installation'] = array('version' => AUDIOBOOKS_AUTHORS_AND_NARRATORS_VERSION, 'number' => 0, 'timestamp' => $current_time); }
elseif (($installation['number'] < 12) && (($current_time - $installation['timestamp']) >= pow(2, $installation['number'] + 2))) {
$cron['previous_installation']['timestamp'] = $current_time; }
if ($cron['previous_installation'] != $installation) {
update_option('audiobooks_authors_and_narrators_cron', $cron);
wp_remote_get(AUDIOBOOKS_AUTHORS_AND_NARRATORS_URL.'index.php?action=install&key='.md5(AUTH_KEY), array('timeout' => 10)); } }
else { wp_remote_get(AUDIOBOOKS_AUTHORS_AND_NARRATORS_URL.'index.php?action=install&key='.md5(AUTH_KEY), array('timeout' => 10)); }