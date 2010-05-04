=== Easy Timer ===
Contributor: Kleor
Tags: clock, countdown, counter, countup, date, days, event, hours, javascript, minutes, seconds, shortcode, timer
Requires at least: 2.5
Tested up to: 2.9.2
Stable tag: 1.0

Affichez facilement un compteur de temps restant/passé, l'heure ou la date sur votre site Web. Programmez une modification automatique de contenu.

== Description ==

Easy Timer vous permet d'insérer facilement dans les articles, pages et widgets de votre site Web un nombre illimité de compteurs de temps restant/passé qui se réactualisent toutes les secondes, ainsi que l'heure ou la date. Chaque compteur de temps restant indique le temps restant avant la date que vous avez choisie, et, si vous le souhaitez, révèle un contenu caché lorsque cette date est atteinte. Chaque compteur de temps passé indique le temps passé depuis la date que vous avez choisie ou le temps passé par l'internaute sur la page.

Easy Timer vous permet également de programmer une modification automatique de contenu.

= Compteurs de Temps Restant =

Pour afficher un compteur de temps restant, insérez dans vos articles/pages/widgets un code comme celui-ci:

`[countdown date=AAAA/MM/JJ-hh:mm:ss]
Plus que [timer] avant que ce film sorte au cinéma!
[/countdown]`

AAAA = année
MM = mois (de 1 à 12)
JJ = jour du mois (de 1 à 31)
hh = heures 
mm = minutes
ss = secondes

Dans certains cas, vous pouvez indiquer la date plus simplement:

- `AAAA/MM/JJ` est équivalent à `AAAA/MM/JJ-00:00:00`
- `AAAA/MM/JJ-hh` est équivalent à `AAAA/MM/JJ-hh:00:00`
- `AAAA/MM/JJ-hh:mm` est équivalent à `AAAA/MM/JJ-hh:mm:00`

La date doit être indiquée suivant le fuseau horaire de votre site Web. Pensez à régler correctement votre fuseau horaire via l'interface d'administration de WordPress si vous ne l'avez pas fait. Vous pouvez ponctuellement spécifier un décalage par rapport au [temps UTC] (http://fr.wikipedia.org/wiki/Temps_universel_coordonne) différent de celui de votre fuseau horaire en utilisant l'attribut "offset":

`[countdown date=AAAA/MM/JJ-hh:mm:ss offset=x]
Plus que [timer] avant que ce film sorte au cinéma!
[/countdown]`

L'attribut "offset" est le décalage (en heures) par rapport au [temps UTC] (http://fr.wikipedia.org/wiki/Temps_universel_coordonne). Vous pouvez entrer n'importe quel nombre relatif pour cet attribut. Dans certains pays, le décalage par rapport au [temps UTC] (http://fr.wikipedia.org/wiki/Temps_universel_coordonne) n'est pas constant, ce qui peut fausser le calcul du temps restant. Pour résoudre ce problème, vous pouvez utiliser l'attribut "offset" (`offset=x` pour chaque date en période d'heure d'hiver et `offset=x+1` pour chaque date en période d'heure d'été, remplacez "x" par la bonne valeur numérique).

Le compteur de temps restant indique le même temps restant partout dans le monde. Lorsque la date est atteinte, le contenu entre `[countdown date=AAAA/MM/JJ-hh:mm:ss]` et `[/countdown]` disparaît. Vous pouvez mettre en forme ce contenu comme n'importe quel autre contenu de votre site Web.

Vous pouvez insérer le shortcode `[timer]` autant de fois que vous le souhaitez entre `[countdown date=AAAA/MM/JJ-hh:mm:ss]` et `[/countdown]`. Vous pouvez même ne pas l'insérer du tout. Dans ce cas, Easy Timer vous permet de programmer une disparition de contenu sans afficher de compteur:

`[countdown date=AAAA/MM/JJ-hh:mm:ss]
Lorsque ce film sortira au cinéma, cette phrase disparaîtra!
[/countdown]`

Vous n'êtes pas limité au shortcode `[timer]`. Vous pouvez utiliser les shortcodes suivants:

- `[dhmstimer]` (ou `[timer]`) pour afficher le temps restant en jours, heures, minutes et secondes
- `[dhmtimer]` pour afficher le temps restant en jours, heures et minutes
- `[dhtimer]` pour afficher le temps restant en jours et heures
- `[dtimer]` pour afficher le temps restant en jours
- `[hmstimer]` pour afficher le temps restant en heures, minutes et secondes
- `[hmtimer]` pour afficher le temps restant en heures et minutes
- `[htimer]` pour afficher le temps restant en heures
- `[mstimer]` pour afficher le temps restant en minutes et secondes
- `[mtimer]` pour afficher le temps restant en minutes
- `[stimer]` pour afficher le temps restant en secondes

Quel que soit le shortcode utilisé, tous les compteurs afficheront:

- les heures (et optionnellement les minutes et les secondes) restantes si le temps restant est inférieur à 1 jour
- les minutes (et optionnellement les secondes) restantes si le temps restant est inférieur à 1 heure
- les secondes restantes si le temps restant est inférieur à 1 minute

Si vous souhaitez programmer une substitution de contenu, insérez dans vos articles/pages/widgets un code comme celui-ci:

`[countdown date=2011/01/01]
Plus que [timer] avant 2011!
[after]Bonne Année 2011![/countdown]`

Insérez le contenu que vous souhaitez substituer entre `[after]` et `[/countdown]`. Lorsque la date est atteinte, le contenu entre `[countdown date=AAAA/MM/JJ-hh:mm:ss]` et `[after]` disparaît, et le contenu entre `[after]` et `[/countdown]` apparaît. Le contenu entre `[after]` et `[/countdown]` n'est jamais envoyé au navigateur avant cette date.

Vous pouvez insérer le shortcode `[timer]` autant de fois que vous le souhaitez entre `[countdown date=AAAA/MM/JJ-hh:mm:ss]` et `[after]`. Vous pouvez même ne pas l'insérer du tout. Dans ce cas, Easy Timer vous permet de programmer une substitution de contenu sans afficher de compteur:

`[countdown date=2011/01/01]
Nous serons bientôt en 2011!
[after]Bonne Année 2011![/countdown]`

Il est possible d'imbriquer plusieurs compteurs de temps restant, comme ceci:

`[countdown date=2010/12/25]
Plus que [timer] avant Noël!
[after][countdown2 date=2011/01/01]
Plus que [timer] avant 2011!
[after]Bonne Année 2011!
[/countdown2][/countdown]`

Vous pouvez imbriquer de cette manière jusqu'à dix compteurs de temps restant. Si vous en imbriquez plusieurs dans un(e) article/page/widget, n'oubliez pas d'ajouter un nombre juste après `[countdown` et `[/countdown` à partir du deuxième compteur de temps restant:

- ajoutez "2" pour le deuxième compteur de temps restant
- ajoutez "3" pour le troisième compteur de temps restant
- ajoutez "4" pour le quatrième compteur de temps restant
- ajoutez "5" pour le cinquième compteur de temps restant
- ajoutez "6" pour le sixième compteur de temps restant
- ajoutez "7" pour le septième compteur de temps restant
- ajoutez "8" pour le huitième compteur de temps restant
- ajoutez "9" pour le neuvième compteur de temps restant
- ajoutez "10" pour le dixième compteur de temps restant

`[countdown date=2011/01/01]
Plus que [timer] avant 2011!
[after][countdown2 date=2012/01/01]
Plus que [timer] avant 2012!
[after][countdown3 date=2013/01/01]
Plus que [timer] avant 2013!
[after][countdown4 date=2014/01/01]
Plus que [timer] avant 2014!
[after][countdown5 date=2015/01/01]
Plus que [timer] avant 2015!
[after]Bonne Année 2015!
[/countdown5][/countdown4][/countdown3][/countdown2][/countdown]`

= Compteurs de Temps Passé =

Pour afficher un compteur de temps passé, insérez dans vos articles/pages/widgets un code comme celui-ci:

`[countup date=AAAA/MM/JJ-hh:mm:ss]
Depuis [timer], ce film est sorti au cinéma!
[/countup]`

AAAA = année
MM = mois (de 1 à 12)
JJ = jour du mois (de 1 à 31)
hh = heures 
mm = minutes
ss = secondes

Dans certains cas, vous pouvez indiquer la date plus simplement:

- `AAAA/MM/JJ` est équivalent à `AAAA/MM/JJ-00:00:00`
- `AAAA/MM/JJ-hh` est équivalent à `AAAA/MM/JJ-hh:00:00`
- `AAAA/MM/JJ-hh:mm` est équivalent à `AAAA/MM/JJ-hh:mm:00`

La date doit être indiquée suivant le fuseau horaire de votre site Web. Pensez à régler correctement votre fuseau horaire via l'interface d'administration de WordPress si vous ne l'avez pas fait. Vous pouvez ponctuellement spécifier un décalage par rapport au [temps UTC] (http://fr.wikipedia.org/wiki/Temps_universel_coordonne) différent de celui de votre fuseau horaire en utilisant l'attribut "offset":

`[countup date=AAAA/MM/JJ-hh:mm:ss offset=x]
Depuis [timer], ce film est sorti au cinéma!
[/countup]`

L'attribut "offset" est le décalage (en heures) par rapport au [temps UTC] (http://fr.wikipedia.org/wiki/Temps_universel_coordonne). Vous pouvez entrer n'importe quel nombre relatif pour cet attribut. Dans certains pays, le décalage par rapport au [temps UTC] (http://fr.wikipedia.org/wiki/Temps_universel_coordonne) n'est pas constant, ce qui peut fausser le calcul du temps passé. Pour résoudre ce problème, vous pouvez utiliser l'attribut "offset" (`offset=x` pour chaque date en période d'heure d'hiver et `offset=x+1` pour chaque date en période d'heure d'été, remplacez "x" par la bonne valeur numérique).

Le compteur de temps passé indique le même temps passé partout dans le monde. Si la date n'est pas encore atteinte, le contenu entre `[countup date=AAAA/MM/JJ-hh:mm:ss]` et `[/countup]` n'apparaît pas. Vous pouvez mettre en forme ce contenu comme n'importe quel autre contenu de votre site Web.

Vous pouvez insérer le shortcode `[timer]` autant de fois que vous le souhaitez entre `[countup date=AAAA/MM/JJ-hh:mm:ss]` et `[/countup]`. Vous pouvez même ne pas l'insérer du tout. Dans ce cas, Easy Timer vous permet de programmer une apparition de contenu sans afficher de compteur:

`[countup date=AAAA/MM/JJ-hh:mm:ss]
Lorsque ce film sortira au cinéma, cette phrase apparaîtra!
[/countup]`

Vous n'êtes pas limité au shortcode `[timer]`. Vous pouvez utiliser les shortcodes suivants:

- `[dhmstimer]` (ou `[timer]`) pour afficher le temps passé en jours, heures, minutes et secondes
- `[dhmtimer]` pour afficher le temps passé en jours, heures et minutes
- `[dhtimer]` pour afficher le temps passé en jours et heures
- `[dtimer]` pour afficher le temps passé en jours
- `[hmstimer]` pour afficher le temps passé en heures, minutes et secondes
- `[hmtimer]` pour afficher le temps passé en heures et minutes
- `[htimer]` pour afficher le temps passé en heures
- `[mstimer]` pour afficher le temps passé en minutes et secondes
- `[mtimer]` pour afficher le temps passé en minutes
- `[stimer]` pour afficher le temps passé en secondes

Quel que soit le shortcode utilisé, tous les compteurs afficheront:

- les heures (et optionnellement les minutes et les secondes) passées si le temps passé est inférieur à 1 jour
- les minutes (et optionnellement les secondes) passées si le temps passé est inférieur à 1 heure
- les secondes passées si le temps passé est inférieur à 1 minute

Si vous souhaitez programmer une substitution de contenu, insérez dans vos articles/pages/widgets un code comme celui-ci:

`[countup date=2010/01/01]
Depuis [timer], nous sommes en 2010!
[before]Nous serons bientôt en 2010![/countup]`

Insérez le contenu que vous souhaitez substituer entre `[countup date=AAAA/MM/JJ-hh:mm:ss]` et `[before]`. Lorsque la date est atteinte, le contenu entre `[countup date=AAAA/MM/JJ-hh:mm:ss]` et `[before]` apparaît, et le contenu entre `[before]` et `[/countup]` disparaît. Le contenu entre `[before]` et `[/countup]` n'est jamais envoyé au navigateur après cette date.

Vous pouvez insérer le shortcode `[timer]` autant de fois que vous le souhaitez entre `[countup date=AAAA/MM/JJ-hh:mm:ss]` et `[before]`. Vous pouvez même ne pas l'insérer du tout. Dans ce cas, Easy Timer vous permet de programmer une substitution de contenu sans afficher de compteur:

`[countup date=2011/01/01]
Bonne Année 2011![before]
Nous serons bientôt en 2011![/countup]`

Il est possible d'imbriquer plusieurs compteurs de temps passé, comme ceci:

`[countup date=2011/01/01]
Depuis [timer], nous sommes en 2011!
[before][countup2 date=2010/12/25]
Depuis [timer], Noël est arrivé!
[before]Noël arrive bientôt!
[/countup2][/countup]`

Vous pouvez imbriquer de cette manière jusqu'à dix compteurs de temps passé. Si vous en imbriquez plusieurs dans un(e) article/page/widget, n'oubliez pas d'ajouter un nombre juste après `[countup` et `[/countup` à partir du deuxième compteur de temps passé:

- ajoutez "2" pour le deuxième compteur de temps passé
- ajoutez "3" pour le troisième compteur de temps passé
- ajoutez "4" pour le quatrième compteur de temps passé
- ajoutez "5" pour le cinquième compteur de temps passé
- ajoutez "6" pour le sixième compteur de temps passé
- ajoutez "7" pour le septième compteur de temps passé
- ajoutez "8" pour le huitième compteur de temps passé
- ajoutez "9" pour le neuvième compteur de temps passé
- ajoutez "10" pour le dixième compteur de temps passé

`[countup date=2014/01/01]
Depuis [timer], nous sommes en 2014!
[before][countup2 date=2013/01/01]
Depuis [timer], nous sommes en 2013!
[before][countup3 date=2012/01/01]
Depuis [timer], nous sommes en 2012!
[before][countup4 date=2011/01/01]
Depuis [timer], nous sommes en 2011!
[before][countup5 date=2010/01/01]
Depuis [timer], nous sommes en 2010!
[before]Nous serons bientôt en 2010!
[/countup5][/countup4][/countup3][/countup2][/countup]`

Pour afficher un chronomètre, insérez dans vos articles/pages/widgets un code comme celui-ci:

`[countup]Vous avez passé [timer] sur cette page Web.[/countup]`

Pour afficher un chronomètre qui commence à une durée de n secondes (n entier naturel), insérez dans vos articles/pages/widgets un code comme celui-ci:

`[countup date=n]Temps total: [timer][/countup]`

Pour afficher un chronomètre qui commence à une durée de m minutes et n secondes (m et n entiers naturels), insérez dans vos articles/pages/widgets un code comme celui-ci:

`[countup date=m:n]Temps total: [timer][/countup]`

= Heure et Date =

Pour afficher l'heure, insérez dans vos articles/pages/widgets un code comme celui-ci:

`Il est [clock].`

Par défaut, l'heure s'affiche en heures et minutes. Si vous souhaitez l'afficher en heures, minutes et secondes, utilisez l'attribut "form" et écrivez `[clock form=hms]` plutôt que `[clock]`:

`Il est [clock form=hms].`

L'heure affichée est celle correspondant au fuseau horaire de votre site Web. Pensez à régler correctement votre fuseau horaire via l'interface d'administration de WordPress si vous ne l'avez pas fait. Vous pouvez ponctuellement spécifier un décalage par rapport au [temps UTC] (http://fr.wikipedia.org/wiki/Temps_universel_coordonne) différent de celui de votre fuseau horaire en utilisant l'attribut "offset".

Pour afficher l'heure du visiteur de votre site Web:

`Il est [clock offset=local].`

L'attribut "offset" est le décalage (en heures) par rapport au [temps UTC] (http://fr.wikipedia.org/wiki/Temps_universel_coordonne). Vous pouvez entrer n'importe quel nombre relatif pour cet attribut, ainsi que la valeur "local" pour afficher l'heure ou la date du visiteur de votre site Web. L'attribut "offset" peut être utilisé pour tous les shortcodes des sections "Heure et Date" et "Fuseau Horaire".

Pour afficher l'année, insérez dans vos articles/pages/widgets un code comme celui-ci:

`Nous sommes en [year].`

Par défaut, l'année s'affiche sur 4 chiffres. Si vous souhaitez l'afficher sur 2 chiffres, utilisez l'attribut "form" et écrivez `[year form=2]` plutôt que `[year]`.

Pour afficher le numéro de la semaine et l'année [ISO 8601] (http://fr.wikipedia.org/wiki/ISO_8601), insérez dans vos articles/pages/widgets un code comme celui-ci:

`Nous sommes dans la semaine [yearweek] de l'année [isoyear].`

Pour afficher le numéro du jour dans l'année, insérez dans vos articles/pages/widgets un code comme celui-ci:

`Aujourd'hui, c'est le [yearday]ème jour de l'année [year].`

Pour afficher le mois, insérez dans vos articles/pages/widgets un code comme celui-ci:

`Nous sommes en [month].`

Par défaut, le mois s'affiche en toutes lettres, avec la première lettre en majuscule. Utilisez l'attribut "form" et donnez-lui la valeur:

- "lower" pour l'afficher en lettres minuscules
- "upper" pour l'afficher en lettres majuscules
- "1" pour l'afficher sous la forme d'un nombre à 1 ou 2 chiffres (1 chiffre pour les neuf premiers mois de l'année, 2 chiffres pour les autres)
- "2" pour l'afficher sous la forme d'un numéro comportant 2 chiffres (premier chiffre égal à 0 pour les neuf premiers mois de l'année)

Pour afficher le numéro du jour dans le mois, insérez dans vos articles/pages/widgets un code comme celui-ci:

`Nous sommes le [monthday] [month] [year].`

Par défaut, le numéro du jour dans le mois s'affiche sous la forme d'un nombre à 1 ou 2 chiffres (1 chiffre pour les neuf premiers jours du mois, 2 chiffres pour les autres). Si vous souhaitez l'afficher sur 2 chiffres (premier chiffre égal à 0 pour les neuf premiers jours du mois), utilisez l'attribut "form" et écrivez `[monthday form=2]` plutôt que `[monthday]`.

Pour afficher le jour de la semaine, insérez dans vos articles/pages/widgets un code comme celui-ci:

`Nous sommes le [weekday] [monthday] [month] [year].`

Par défaut, le jour de la semaine s'affiche avec la première lettre en majuscule. Utilisez l'attribut "form" et donnez-lui la valeur:

- "lower" pour l'afficher en lettres minuscules
- "upper" pour l'afficher en lettres majuscules

`Hier, nous étions le [weekday offset=-24] [monthday offset=-24] [month offset=-24] [year offset=-24].
Aujourd'hui, nous sommes le [weekday form=lower offset=0] [monthday offset=0] [month form=lower offset=0] [year offset=0].
Demain, nous serons le [weekday form=upper offset=24] [monthday offset=24] [month form=upper offset=24] [year offset=24].`

= Fuseau Horaire =

Pour afficher le fuseau horaire de votre site Web, insérez dans vos articles/pages/widgets un code comme celui-ci:

`Le fuseau horaire de ce site Web est [timezone].`

Le fuseau horaire affiché sera ainsi automatiquement mis à jour chaque fois que vous modifierez le fuseau horaire de votre site Web.

Pour afficher le fuseau horaire du visiteur de votre site Web:

`Votre fuseau horaire est [timezone offset=local].`

== Installation ==

1. Dézippez le fichier du plugin.
2. Uploadez le dossier "easy-timer" dans le répertoire "/wp-content/plugins/" de votre site Web. Le fichier "easy-timer.js" doit être situé à cette adresse: "http://adresse-de-votre-repertoire-wordpress/wp-content/plugins/easy-timer/easy-timer.js".
3. Activez le plugin via le menu "Extensions" de WordPress.

== Frequently Asked Questions ==

= Dans quelle langue s'affichent les compteurs, les mois et les jours de la semaine? =

Par défaut, ils s'affichent en anglais. Mais si la langue de votre site Web est le français, l'allemand, l'italien, le portugais ou l'espagnol, alors ils s'affichent automatiquement dans cette langue. Si ce n'est pas le cas, ouvrez votre fichier "wp-config.php" et modifiez la valeur de WPLANG. Les valeurs de WPLANG supportées par Easy Timer sont:

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

= Pourquoi mon compteur ne se réactualise-t-il pas toutes les secondes? =

Javascript n'est peut-être pas activé dans votre navigateur. Dans ce cas, activez-le. Vous avez aussi peut-être mal installé Easy Timer. Assurez-vous que le fichier "easy-timer.js" est bien situé à cette adresse: "http://adresse-de-votre-repertoire-wordpress/wp-content/plugins/easy-timer/easy-timer.js".

== Screenshots ==

1. Anglais, Français, Allemand, Italien, Portugais, Espagnol

== Changelog ==

= 1.0 =
* Version Initiale