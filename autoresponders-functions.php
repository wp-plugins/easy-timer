<?php function subscribe_to_autoresponder($autoresponder, $list, $contact) {
if ($list != '') {
$contact['email_address'] = affiliation_format_email_address($contact['email_address']);
$contact['referrer'] = affiliation_format_nice_name($contact['referrer']);
$contact['website_url'] = affiliation_format_url($contact['website_url']);
switch ($autoresponder) {
case 'AWeber': subscribe_to_aweber($list, $contact); break;
case 'CyberMailing': subscribe_to_cybermailing($list, $contact); break; } } }


function subscribe_to_aweber($list, $contact) {
$list = str_replace('à', '@', $list);
if (!strstr($list, '@')) { $list = $list.'@aweber.com'; }
$subject = 'AWeber Subscription';
$body =
"\nEmail: ".$contact['email_address'].
"\nName: ".affiliation_strip_accents($contact['first_name']).
"\nReferrer: ".$contact['referrer'];
$domain = $_SERVER['SERVER_NAME'];
if (substr($domain, 0 , 4) == 'www.') { $domain = substr($domain, 4); }
$sender = 'wordpress@'.$domain;
$headers = 'From: '.$sender;
wp_mail($list, $subject, $body, $headers); }


function subscribe_to_cybermailing($list, $contact) {
$_GET['autoresponder_subscription'] = 
'http://www.cybermailing.com/mailing/subscribe.php?'.
'Liste='.$list.'&amp;'.
'ListName='.$list.'&amp;'.
'Identifiant='.$contact['login'].'&amp;'.
'Name='.$contact['first_name'].'&amp;'.
'Email='.$contact['email_address'].'&amp;'.
'WebSite='.$contact['website_url']; }