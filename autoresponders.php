<?php $autoresponders = array(
'AWeber',
'CyberMailing');


function subscribe_to_autoresponder($autoresponder, $list, $user) {
if ($list != '') {
$user['email_address'] = commerce_format_email_address($user['email_address']);
$user['referrer'] = commerce_format_nice_name($user['referrer']);
$user['website_url'] = commerce_format_url($user['website_url']);
switch ($autoresponder) {
case 'AWeber': subscribe_to_aweber($list, $user); break;
case 'CyberMailing': subscribe_to_cybermailing($list, $user); break; } } }


function subscribe_to_aweber($list, $user) {
if (!strstr($list, '@')) { $list = $list.'@aweber.com'; }
$subject = 'AWeber Subscription';
$body =
"\nEmail: ".$user['email_address'].
"\nName: ".commerce_strip_accents($user['first_name']).
"\nReferrer: ".$user['referrer'];
$domain = $_SERVER['SERVER_NAME'];
if (substr($domain, 0 , 4) == 'www.') { $domain = substr($domain, 4); }
$sender = 'wordpress@'.$domain;
$headers = 'From: '.$sender;
wp_mail($list, $subject, $body, $headers); }


function subscribe_to_cybermailing($list, $user) {
}