=== MangoFp ===
Contributors: andresjarviste
Tags: crm, leads, messages, contact form, contact form 7
Requires at least: 5.2
Tested up to: 5.7
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Manage Contact Form 7 messages directly in WordPress like leads in CRM system.

== Description ==
MangoFp allows to manage Contact Form 7 form submissions directly in WordPress. They will be handled like messages or leads in CRM system. Information and service requests, various registration forms - all can be managed according to the tailor-made process that is best for you.

All incoming form submissions (messages) are gathered in one place. Each lead is always assigned to a process step. Initially its "New". Then you decide what action is needed with the lead and it can move to the step **In Progress** or **Accepted** or something else specific to your process.

Reply via email to form submissions directly from MangoFp. Your replies can be based on pre-defined email templates.


== Installation ==
1. Check that you have installed plugin **Contact From 7**.
2. You should also have at least one  Contact From 7 form in use on your WordPress website. If you want to reply to form submissions from MangoFP,  your form(s) should contain email field. MangoFP recongnises email field automaticly when its name is *your-name* (default for Contact Form 7)
3. Install and activate **MangoFP**
4. In Admin Area open MangoFP->Settings. On tab **Parameters** you can specify **Email field** if that is something else than *your-email*. On tab "Define process steps" you can modify process for management of the messages.  See detailed instructions on plugin [webpage](https://mangofp.net)
5. Open your page with a form on your webpage and submit some data.  
6. In Admin Area open MangoFP->Contacts. You should see your form submission as new message.



== Screenshots ==
1. New form submissions as messages
2. Configure which next steps are allowed for message in any step

== Changelog ==

= 1.0.0 =
Initial version. 

== Plugin source code ==

Plugin source code is located in 3 github repositories:

1. Main php code: [https://github.com/wpkonsult/mangofp.git](https://github.com/wpkonsult/mangofp.git)
2. Front end for messages management: [https://github.com/wpkonsult/mangofp.git](https://github.com/wpkonsult/mangofp.git)
3. Front end for settings and process configruation:  [https://github.com/wpkonsult/mangofp-settings-ts.git](https://github.com/wpkonsult/mangofp-settings-ts.git)