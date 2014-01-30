<?php global $easy_timer_js_attribute;
$timer = timer_string($S);

$content = str_replace('['.$prefix.'timer]', '['.$prefix.easy_timer_data('default_timer_prefix').'timer]', $content);
$content = str_replace('['.$prefix.'dhmstimer]', '<span class="dhmscount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['Dhms'].'</span>', $content);
$content = str_replace('['.$prefix.'dhmtimer]', '<span class="dhmcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['Dhm'].'</span>', $content);
$content = str_replace('['.$prefix.'dhtimer]', '<span class="dhcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['Dh'].'</span>', $content);
$content = str_replace('['.$prefix.'dtimer]', '<span class="dcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['D'].'</span>', $content);
$content = str_replace('['.$prefix.'hmstimer]', '<span class="hmscount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['Hms'].'</span>', $content);
$content = str_replace('['.$prefix.'hmtimer]', '<span class="hmcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['Hm'].'</span>', $content);
$content = str_replace('['.$prefix.'htimer]', '<span class="hcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['H'].'</span>', $content);
$content = str_replace('['.$prefix.'mstimer]', '<span class="mscount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['Ms'].'</span>', $content);
$content = str_replace('['.$prefix.'mtimer]', '<span class="mcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['M'].'</span>', $content);
$content = str_replace('['.$prefix.'stimer]', '<span class="scount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['S'].'</span>', $content);

$content = str_replace('['.$prefix.'rtimer]', '['.$prefix.easy_timer_data('default_timer_prefix').'rtimer]', $content);
$content = str_replace('['.$prefix.'dhmsrtimer]', '<span class="dhmscount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['Dhms'].'</span>', $content);
$content = str_replace('['.$prefix.'dhmrtimer]', '<span class="dhmcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['Dhm'].'</span>', $content);
$content = str_replace('['.$prefix.'dhrtimer]', '<span class="dhcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['Dh'].'</span>', $content);
$content = str_replace('['.$prefix.'drtimer]', '<span class="dcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['D'].'</span>', $content);
$content = str_replace('['.$prefix.'hmsrtimer]', '<span class="hmsrcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['hms'].'</span>', $content);
$content = str_replace('['.$prefix.'hmrtimer]', '<span class="hmrcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['hm'].'</span>', $content);
$content = str_replace('['.$prefix.'hrtimer]', '<span class="hrcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['h'].'</span>', $content);
$content = str_replace('['.$prefix.'msrtimer]', '<span class="msrcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['ms'].'</span>', $content);
$content = str_replace('['.$prefix.'mrtimer]', '<span class="mrcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['m'].'</span>', $content);
$content = str_replace('['.$prefix.'srtimer]', '<span class="srcount'.$way.'" '.$easy_timer_js_attribute.'="t'.mt_rand(10000000, 99999999).'-'.$T.'">'.$timer['s'].'</span>', $content);