=== DDAy ===
Tags: comments, widget
Requires at least: 1.5
Tested up to: 2.3
Stable tag: trunk

This plugin allow to associate a countdown to events.

== Description ==

The countdown can works in two ways : for futur and past events : until the events (in 43 days for example) and since the events (for 96 days
So this plugin is very interesting for major (or minor) events of the life, birthdays, etc…

*Feature List*

* Unlimited DDays stocked in the database
* Function of repetition of the Dday. For example : every year for birthday.
* Posibility to give a title, an URL, a description…
* Customizable format of display (ex : The balise %TITLE% will be replaced by the titleo f the DDay). This allows a simply translation in any language.
* Advanced interface of management of the DDays : selection of the order by drag and drope, realtime form validation.
* Posibility to insert the list of the DDays in the sidebar or to insert a specific DDay in a post

== Installation ==

1. Unzip the file
2. Upload it in /wp-content/plugins/dday/ of your Wordpress installation
3. Activate the Plugin
4. Visit the management page in the administration of Wordpress
5. Change the format of display to your taste and to your language for non english-speaking people (See it in “Edit Options” at the bottom )
6. Add DDays and modify them order with the drag and drope function.
7. Insert <?php wp_dday_list(); ?> in your sidebar to display the list of all active DDays or use the widget

*For deactivate Nice tooltip on mouseover a DDay :* In wp-dday.php search $nice_tooltip (l.30) and put him to 0 ( $nice_tooltip = 0;)

== Changelog / Todo ==

0.2
* -1 in display before and after => No display
* Widget
* 1 bug fixed Fonction : drag and drope
* Some improvement in the code 
* Nice tooltip : can be de-activate easilly
  
0.3 :
* Fix a bug with Publish / Unpublish in admin page
   
0.3.1 :
* Fix a select query in the database -> list of dday where not displayed
   
0.3.2 :
* Fix an error to execute the query of the frequence of repetion
* Fix an error that displayed the li tag when the event shouldn't be displayed
   
ToDo :
* Choice of unit of the countdown more flexible
* Correction of eventual bugs
* IE Admin Friendly

Thanks to all who sent bug reports and ideas for improvements.
Please send me a mail if I forgot you to mention here.