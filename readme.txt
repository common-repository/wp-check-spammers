=== WP-Check Spammers ===
Contributors: Xavier Media&reg;,csixty4
Tags: Spam filter, comment spam, SpamBot Search Tool, forum spam, fSpamlist, StopForumSpam, Sorbs, Spamhaus, SpamCop, ProjectHoneyPot, Bot Scout, DroneBL, AHBL, Tor Project, abuse.ch, ZeusTracker 
Requires at least: 2.7.0
Tested up to: 3.3.1
Stable tag: 0.4

Check comment against the SpamBot Search Tool using the IP address, the email and the name of the poster as search criteria.  

== Description ==

Check comment against the SpamBot Search Tool using the IP address, the email and the name 
of the poster as search criteria. If anyone has already reported the user a as a known 
spammer the plugin will not let the comment to be posted and an email will be set to the 
blog admin. You can use your own installation of SpamBot Search Tool or you can use the 
Xavier Media server. 

== Installation ==

   1. Download the ZIP file from our site an extract it using for example WinZip 
      or WinRar
   2. Upload the file(s) to the Wordpress plugin directory
   3. Login at the admin area and click on "Plugins"
   4. Find WP Check Spammers in the plugin list and click on "Activate"
   5. Visit the options page fund in the "Settings" menu and do any changes you want. 
      This is optional since the plugin will work using just the default options. 

== Frequently Asked Questions ==

= How do I setup my own version of the Spambot search Tool? =

   1.  Visit Temerc.com and download the latest version of SpamBot Search Tool
   2.  Follow the install instructions and install the script somewhere on your own server
   3.  Visit the admin area of your blog and click on "WP Check Spammers" in the 
       "Settings" menu
   4.  Fill in the location of your own installation of SpamBot Search Tool as "Spam 
       Checking Server". Make sure you have a / at the end of the URL and also include 
       http:// in the beginning. If your site is for example www.sampleaddress.com and 
       you install SpamBot Search Tool in a directory called Check_Spammers then you 
       should fill in http://www.sampleaddress.com/Check_Spammers/ as Spam Checking 
       Server. If you need help, visit our support forum at www.xavierforum.com. 

== Changelog ==

= 0.4 =
* Minor bugfix

= 0.3 =
* New features:
  - Automatically install SpamBot Search Tool in your wp-content directory
  - Use SMTP for email

= 0.2 =
* Bugfix: Saving options

= 0.1 =
* The first version

== Upgrade Notice ==

= 0.2 =
* Bugfix: Saving options

= 0.1 =
* The first version

`<?php code(); // goes in backticks ?>`