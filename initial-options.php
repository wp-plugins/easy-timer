<?php date_default_timezone_set('UTC');
foreach (array('admin_email', 'blogname') as $key) { $$key = get_option($key); }
$domain = $_SERVER['SERVER_NAME']; if (substr($domain, 0, 4) == 'www.') { $domain = substr($domain, 4); }
if ($blogname == '') { $blogname = ucfirst($domain); }
$blog_email = $admin_email;
if ((!strstr($blog_email, $domain)) && (isset($_SERVER['SERVER_ADMIN']))) { $blog_email = $_SERVER['SERVER_ADMIN']; }
if (!strstr($blog_email, $domain)) { $blog_email = 'contact@'.$domain; }

$initial_options = array(
'monthly_email_body' => __('Here are the two files concerning the previous month:', 'files-generator').'

'.FILES_GENERATOR_URL.'index.php?type=orders&start_date=[start-date]&end_date=[end-date]
'.FILES_GENERATOR_URL.'index.php?type=recurring-payments&start_date=[start-date]&end_date=[end-date]

--
'.$blogname.'
'.HOME_URL,
'monthly_email_receiver' => $blog_email,
'monthly_email_sender' => $blogname.' <'.$blog_email.'>',
'monthly_email_sent' => 'yes',
'monthly_email_subject' => __('Two files for accounting', 'files-generator'),
'previous_cron_timestamp' => time(),
'start_date' => '2000-01-01 00:00:00',
'version' => FILES_GENERATOR_VERSION);