=== DDay ===
Contributors: Mdkart
Tags: events, countdown, date, widget
Requires at least: 1.5
Tested up to: 2.9.2
Stable tag: trunk

This plugin allow to associate a countdown to events.

== Description ==

The countdown can works in two ways : for futur and past events : until the events (in 43 days for example) and since the events (for 96 days)

So this plugin is very interesting for major (or minor) events of the life, birthdays, etc...

= Feature List =

* Unlimited DDays stocked in the database
* Function of repetition of the Dday. For example : every year for birthday.
* Posibility to give a title, an URL, a description...
* Customizable format of display (ex : The balise %TITLE% will be replaced by the title of the DDay). This allows a simply translation in any language.
* Advanced interface of management of the DDays : selection of the order by drag and drop, realtime form validation.
* Posibility to insert the list of the DDays in the sidebar or to insert a specific DDay in a post

== Installation ==

1. Unzip the file
1. Upload it in /wp-content/plugins/dday/ of your Wordpress installation
1. Activate the Plugin
1. Visit the management page in the administration of Wordpress
1. Change the format of display to your taste and to your language for non english-speaking people (See it in "Edit Options" at the bottom ). ITS VERY IMPORTANT FOR NON ENGLISH USERS!!!
1. Add DDays and modify the order with the drag and drop function.
1. Insert with the Widget or use wp_dday_list() to display the list of all active DDays or use the widget

= For deactivate Nice tooltip on mouseover a DDay : =
In wp-dday.php search $nice_tooltip (l.29) and put him to 0 ( $nice_tooltip = 0;)

== Changelog==

= 0.3.7 =

* Adds translation into Belorussian. Thanks to [Marcis G.](http://pc.de/)

= 0.3.6 =

* Fix of an error with Daylight saing time and offset timezone. The problem came from an inadequation between GMT and the time of the server causing errors when changing DST, timezone or server. Now all the dates are coded in GMT so there should be no problem with it anymore. But the dates saved before this upgrade may lagged by an error of some hours. You will need to fix them manually (just go in the admin of DDay and edit the time of these ddays to what you want it is). I am very sorry for this inconvenience.

= 0.3.5 =

* Fix some things for WP 2.7
* Adds a filter %DATE% and %DATEandHOUR% for display formatting

= 0.3.4 =

* Now can be localized

= 0.3.2 =

* Fix an error to execute the query of the frequence of repetion
* Fix an error that displayed the li tag when the event shouldn't be displayed

= 0.3.1 =

* Fix a select query in the database -> list of dday where not displayed

= 0.3 =

* Fix a bug with Publish / Unpublish in admin page

= 0.2 =

* -1 in display before and after => No display
* Widget
* 1 bug fixed Fonction : drag and drope
* Some improvement in the code 
* Nice tooltip : can be de-activate easilly
   
= ToDo =

* Choice of unit of the countdown more flexible
* Color of the event depending on the greater or lesser period until the event.

Thanks to all who sent bug reports and ideas for improvements.

== Upgrade Notice ==
= Be careful when upgrading to 0.3.6 =
Now all the dates are coded in GMT so there should be no problem with it anymore. But the dates saved before this upgrade may lagged by an error of some hours.

You will need to fix them manually (just go in the admin of DDay and edit the time of these ddays to what you want it is). I am very sorry for this inconvenience.