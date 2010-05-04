=== Easy Timer ===
Contributor: Kleor
Tags: clock, countdown, counter, countup, date, days, event, hours, javascript, minutes, seconds, shortcode, timer
Requires at least: 2.5
Tested up to: 2.9.2
Stable tag: 1.0
Easily display a count down/up timer, the time or the current date on your website. Schedule an automatic content modification.
== Description ==
Easy Timer allows you to easily insert into posts, pages and widgets of your website an unlimited number of count down/up timers which refresh every second, and the time or date. Each countdown timer shows the time remaining until the date you choose, and, if you want, reveals a hidden content when this date is reached. Each countup timer shows the time elapsed since the date you choose or the time spent by the Internet user on the webpage.
Easy Timer also allows you to schedule an automatic content modification.
= Countdown Timers =
To display a countdown timer, insert into your posts/pages/widgets a code like this:
`[countdown date=YYYY/MM/DD-hh:mm:ss]
Just [timer] until this film comes out in cinemas!
[/countdown]`
YYYY = year
MM = month (from 1 to 12)
DD = day number of the month (from 1 to 31)
hh = hours
mm = minutes
ss = seconds
In some cases, you can specify the date differently:
- `YYYY/MM/DD` is equivalent to `YYYY/MM/DD-00:00:00`
- `YYYY/MM/DD-hh` is equivalent to `YYYY/MM/DD-hh:00:00`
- `YYYY/MM/DD-hh:mm` is equivalent to `YYYY/MM/DD-hh:mm:00`
The date shall be indicated according to the time zone of your website. Set correctly your time zone via the WordPress Administration Panel if you have not done. You may occasionally specify an offset to [UTC time](http://en.wikipedia.org/wiki/Coordinated_Universal_Time) different from your time zone using the "offset" attribute:
`[countdown date=YYYY/MM/DD-hh:mm:ss offset=x]
Just [timer] until this film comes out in cinemas!
[/countdown]`
The "offset" attribute is the offset (in hours) to [UTC time](http://en.wikipedia.org/wiki/Coordinated_Universal_Time). You can specify any relative number for this attribute. In some countries, the offset to [UTC time](http://en.wikipedia.org/wiki/Coordinated_Universal_Time) is not constant. It may distort the calculation of the remaining time. To solve this problem, you can use the "offset" attribute (`offset=x` for each date during winter time and `offset=x+1` for each date during summer time, replace "x" with the right numeric value).
The countdown timer shows the same remaining time everywhere in the world. When the date is reached, the content between `[countdown date=YYYY/MM/DD-hh:mm:ss]` and `[/countdown]` disappears. You can format this content as any content of your website.
You can insert the `[timer]` shortcode as many times as you want between `[countdown date=YYYY/MM/DD-hh:mm:ss]` and `[/countdown]`. If you don't insert it, Easy Timer allows you to schedule a content disappearance without displaying a countdown timer:
`[countdown date=YYYY/MM/DD-hh:mm:ss]
When this film will come out in cinemas, this sentence will disappear!
[/countdown]`
You are not limited to the `[timer]` shortcode. You can use the following shortcodes:
- `[dhmstimer]` (or `[timer]`) to display the remaining time in days, hours, minutes and seconds
- `[dhmtimer]` to display the remaining time in days, hours and minutes
- `[dhtimer]` to display the remaining time in days and hours
- `[dtimer]` to display the remaining time in days
- `[hmstimer]` to display the remaining time in hours, minutes and seconds
- `[hmtimer]` to display the remaining time in hours and minutes
- `[htimer]` to display the remaining time in hours
- `[mstimer]` to display the remaining time in minutes and seconds
- `[mtimer]` to display the remaining time in minutes
- `[stimer]` to display the remaining time in seconds
All countdown timers will display:
- the remaining hours (and optionally minutes and seconds) if the remaining time is less than 1 day
- the remaining minutes (and optionally seconds) if the remaining time is less than 1 hour
- the remaining seconds if the remaining time is less than 1 minute
If you want to schedule a content substitution, insert into your posts/pages/widgets a code like this:
`[countdown date=2011/01/01]
Just [timer] until 2011!
[after]Happy New Year 2011![/countdown]`
Insert the content you want to substitute between `[after]` and `[/countdown]`. When the date is reached, the content between `[countdown date=YYYY/MM/DD-hh:mm:ss]` and `[after]` disappears, and the content between `[after]` and `[/countdown]` appears. The content between `[after]` and `[/countdown]` is never sent to the browser before this date.
You can insert the `[timer]` shortcode as many times as you want between `[countdown date=YYYY/MM/DD-hh:mm:ss]` and `[after]`. If you don't insert it, Easy Timer allows you to schedule a content substitution without displaying a countdown timer:
`[countdown date=2011/01/01]
We will be soon in 2011!
[after]Happy New Year 2011![/countdown]`
It is possible to imbricate several countdown timers, like this:
`[countdown date=2010/12/25]
Just [timer] until Christmas!
[after][countdown2 date=2011/01/01]
Just [timer] until 2011!
[after]Happy New Year 2011!
[/countdown2][/countdown]`
You can imbricate up to ten countdown timers. If you imbricate several countdown timers on a post/page/widget, remember to add a number right after `[countdown` and `[/countdown`:
- add "2" for the second countdown timer
- add "3" for the third countdown timer
- add "4" for the fourth countdown timer
- add "5" for the fifth countdown timer
- add "6" for the sixth countdown timer
- add "7" for the seventh countdown timer
- add "8" for the eighth countdown timer
- add "9" for the ninth countdown timer
- add "10" for the tenth countdown timer
`[countdown date=2011/01/01]
Just [timer] until 2011!
[after][countdown2 date=2012/01/01]
Just [timer] until 2012!
[after][countdown3 date=2013/01/01]
Just [timer] until 2013!
[after][countdown4 date=2014/01/01]
Just [timer] until 2014!
[after][countdown5 date=2015/01/01]
Just [timer] until 2015!
[after]Happy New Year 2015!
[/countdown5][/countdown4][/countdown3][/countdown2][/countdown]`
= Countup Timers =
To display a countup timer, insert into your posts/pages/widgets a code like this:
`[countup date=YYYY/MM/DD-hh:mm:ss]
For [timer], this film is released in cinemas!
[/countup]`
YYYY = year
MM = month (from 1 to 12)
DD = day number of the month (from 1 to 31)
hh = hours 
mm = minutes
ss = seconds
In some cases, you can specify the date differently:
- `YYYY/MM/DD` is equivalent to `YYYY/MM/DD-00:00:00`
- `YYYY/MM/DD-hh` is equivalent to `YYYY/MM/DD-hh:00:00`
- `YYYY/MM/DD-hh:mm` is equivalent to `YYYY/MM/DD-hh:mm:00`
The date shall be indicated according to the time zone of your website. Set correctly your time zone via the WordPress Administration Panel if you have not done. You may occasionally specify an offset to [UTC time](http://en.wikipedia.org/wiki/Coordinated_Universal_Time) different from your time zone using the "offset" attribute:
`[countup date=YYYY/MM/DD-hh:mm:ss offset=x]
For [timer], this film is released in cinemas!
[/countup]`
The "offset" attribute is the offset (in hours) to [UTC time](http://en.wikipedia.org/wiki/Coordinated_Universal_Time). You can specify any relative number for this attribute. In some countries, the offset to [UTC time](http://en.wikipedia.org/wiki/Coordinated_Universal_Time) is not constant. It may distort the calculation of the elapsed time. To solve this problem, you can use the "offset" attribute (`offset=x` for each date during winter time and `offset=x+1` for each date during summer time, replace "x" with the right numeric value).
The countup timer shows the same elapsed time everywhere in the world. If the date is not yet reached, the content between `[countup date=YYYY/MM/DD-hh:mm:ss]` and `[/countup]` doesn't appear. You can format this content as any content of your website.
You can insert the `[timer]` shortcode as many times as you want between `[countup date=YYYY/MM/DD-hh:mm:ss]` and `[/countup]`. If you don't insert it, Easy Timer allows you to schedule a content appearance without displaying a countup timer:
`[countup date=YYYY/MM/DD-hh:mm:ss]
When this film will come out in cinemas, this sentence will appear!
[/countup]`
You are not limited to the `[timer]` shortcode. You can use the following shortcodes:
- `[dhmstimer]` (or `[timer]`) to display the elapsed time in days, hours, minutes and seconds
- `[dhmtimer]` to display the elapsed time in days, hours and minutes
- `[dhtimer]` to display the elapsed time in days and hours
- `[dtimer]` to display the elapsed time in days
- `[hmstimer]` to display the elapsed time in hours, minutes and seconds
- `[hmtimer]` to display the elapsed time in hours and minutes
- `[htimer]` to display the elapsed time in hours
- `[mstimer]` to display the elapsed time in minutes and seconds
- `[mtimer]` to display the elapsed time in minutes
- `[stimer]` to display the elapsed time in seconds
All countup timers will display:
- the elapsed hours (and optionally minutes and seconds) if the elapsed time is less than 1 day
- the elapsed minutes (and optionally seconds) if the elapsed time is less than 1 hour
- the elapsed seconds if the elapsed time is less than 1 minute
If you want to schedule a content substitution, insert into your posts/pages/widgets a code like this:
`[countup date=2010/01/01]
For [timer], we are in 2010!
[before]We will be soon in 2010![/countup]`
Insert the content you want to substitute between `[countup date=YYYY/MM/DD-hh:mm:ss]` and `[before]`. When the date is reached, the content between `[countdown date=YYYY/MM/DD-hh:mm:ss]` and `[before]` appears, and the content between `[before]` and `[/countup]` disappears. The content between `[before]` and `[/countup]` is never sent to the browser after this date.
You can insert the `[timer]` shortcode as many times as you want between `[countup date=YYYY/MM/DD-hh:mm:ss]` and `[before]`. If you don't insert it, Easy Timer allows you to schedule a content substitution without displaying a countup timer:
`[countup date=2011/01/01]
Happy New Year 2011![before]
We will be soon in 2011![/countup]`
It is possible to imbricate several countup timers, like this:
`[countup date=2011/01/01]
For [timer], we are in 2011!
[before][countup2 date=2010/12/25]
For [timer], Christmas has arrived!
[before]Christmas will come soon!
[/countup2][/countup]`
You can imbricate up to ten countup timers. If you imbricate several countup timers on a post/page/widget, remember to add a number right after `[countup` and `[/countup`:
- add "2" for the second countup timer
- add "3" for the third countup timer
- add "4" for the fourth countup timer
- add "5" for the fifth countup timer
- add "6" for the sixth countup timer
- add "7" for the seventh countup timer
- add "8" for the eighth countup timer
- add "9" for the ninth countup timer
- add "10" for the tenth countup timer
`[countup date=2014/01/01]
For [timer], we are in 2014!
[before][countup2 date=2013/01/01]
For [timer], we are in 2013!
[before][countup3 date=2012/01/01]
For [timer], we are in 2012!
[before][countup4 date=2011/01/01]
For [timer], we are in 2011!
[before][countup5 date=2010/01/01]
For [timer], we are in 2010!
[before]We will be soon in 2010!
[/countup5][/countup4][/countup3][/countup2][/countup]`
To display a chronometer, insert into your posts/pages/widgets a code like this:
`[countup]You have spent [timer] on this webpage.[/countup]`
To display a chronometer which begins from n seconds (n must be a positive integer), insert into your posts/pages/widgets a code like this:
`[countup date=n]Total time: [timer][/countup]`
To display a chronometer which begins from m minutes and n seconds (m and n must be positive integers), insert into your posts/pages/widgets a code like this:
`[countup date=m:n]Total time: [timer][/countup]`
= Time And Date =
To display the time, insert into your posts/pages/widgets a code like this:
`It's [clock].`
By default, the time is displayed in hours and minutes. If you want to display it in hours, minutes and seconds, use the "form" attribute and write `[clock form=hms]` instead of `[clock]`:
`It's [clock form=hms].`
The time is displayed according to the time zone of your website. Set correctly your time zone via the WordPress Administration Panel if you have not done. You may occasionally specify an offset to [UTC time](http://en.wikipedia.org/wiki/Coordinated_Universal_Time) different from your time zone using the "offset" attribute.
To display the time of the Internet user:
`It's [clock offset=local].`
The "offset" attribute is the offset (in hours) to [UTC time](http://en.wikipedia.org/wiki/Coordinated_Universal_Time). You can specify any relative number for this attribute. You also can specify the "local" value to display the time of the Internet user. The "offset" attribute can be used for all shortcodes in the "Time and Date" and "Time Zone" sections.
To display the year, insert into your posts/pages/widgets a code like this:
`We are in [year].`
By default, the year is displayed in 4 digits. If you want to display it in 2 digits, use the "form" attribute and write `[year form=2]` instead of `[year]`.
To display the [ISO 8601](http://en.wikipedia.org/wiki/ISO_8601) week number and year, insert into your posts/pages/widgets a code like this:
`We are in the week [yearweek] of [isoyear].`
To display the day number of the year, insert into your posts/pages/widgets a code like this:
`Today, it's the day [yearday] of [year].`
To display the month, insert into your posts/pages/widgets a code like this:
`We are in [month].`
By default, the month is displayed in letters, with the first letter capitalized. Use the "form" attribute and give the value:
- "lower" to display it in lowercase letters
- "upper" to display it in uppercase letters
- "1" to display it as a number with 1 or 2 digits (1 digit for the first nine months of the year, 2 digits for the others)
- "2" to display it as a number with 2 digits (first digit equal to 0 for the first nine months of the year)
To display the day number of the month, insert into your posts/pages/widgets a code like this:
`Today is [month] [monthday], [year].`
By default, the day number of the month is displayed as a number with 1 or 2 digits (1 digit for the first nine days of the month, 2 digits for the others). If you want to display it as a number with 2 digits (first digit equal to 0 for the first nine days of the month), use the "form" attribute and write `[monthday form=2]` instead of `[monthday]`.
To display the weekday, insert into your posts/pages/widgets a code like this:
`Today is [weekday], [month] [monthday], [year].`
By default, the weekday is displayed with the first letter capitalized. Use the "form" attribute and give the value:
- "lower" to display it in lowercase letters
- "upper" to display it in uppercase letters
`Yesterday was [weekday offset=-24], [month offset=-24] [monthday offset=-24], [year offset=-24].
Today is [weekday form=lower offset=0], [month form=lower offset=0] [monthday offset=0], [year offset=0].
Tomorrow will be [weekday form=upper offset=24], [month form=upper offset=24] [monthday offset=24], [year offset=24].`
= Time Zone =
To display the time zone of your website, insert into your posts/pages/widgets a code like this:
`The time zone of this website is [timezone].`
The displayed time zone will be automatically updated each time you will change the time zone of your website.
To display the time zone of the Internet user:
`Your time zone is [timezone offset=local].`
== Installation ==
1. Unzip the plugin file.
2. Upload the "easy-timer" folder to the "/wp-content/plugins/" directory. The "easy-timer.js" file must be located at this address: "http://your-wordpress-directory-address/wp-content/plugins/easy-timer/easy-timer.js".
3. Activate the plugin through the "Plugins" menu in WordPress.
== Frequently Asked Questions ==
= In which language are displayed the count down/up timers, months and weekdays? =
By default, they are displayed in English. But if your website language is French, German, Italian, Portuguese or Spanish, then they will automatically be displayed in this language. If this is not the case, open your "wp-config.php" file and change the WPLANG value. WPLANG values supported by Easy Timer are:
- de_BE
- de_CH
- de_DE
- de_LU
- es_AR
- es_ES
- es_MX
- fr_BE
- fr_CA
- fr_CH
- fr_FR
- fr_LU
- fr_MC
- it_CH
- it_IT
- pt_BR
- pt_PT
= Why does my count down/up timer not refresh every second? =
Javascript may be deactivated in your browser. In this case, activate it. Check your Easy Timer installation. Make sure that the "easy-timer.js" file is located at this address: "http://your-wordpress-directory-address/wp-content/plugins/easy-timer/easy-timer.js".
== Screenshots ==
1. English, French, German, Italian, Portuguese, Spanish
== Changelog ==
= 1.0 =
* Initial version