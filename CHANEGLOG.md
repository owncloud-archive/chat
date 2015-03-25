Changelog
===

##0.5.0


##0.4.0
 - fix several style issues
 - prevent HTML template to be shown when loading, by showing a spinner
 - drop PHP5.3 Support
 - don't show presence of the user for each chat Message
 - make it possible to start an instant conversation for the XMPP backend
 - make it possible to enable and disable Chat backends via the admin settings
 - make it possible to save XMPP roster contacts in the contacts app
 - Lot's of refactors and cleanups
 - small bug fixes

##0.3.0
 - basic XMPP Support
 - new registering system for backends
 - client side rewrite of backend
 - file attachments


##0.2.2

 - solve ugly error: 
```` 
{"reqId":"53e62c87370de","app":"PHP","message":"Undefined offset: 1 at \/var\/www\/owncloud\/apps\/chat\/core\/appapi.php#99","level":3,"time":"2014-08-09T14:13:27+00:00","method":"GET","url":"\/index.php\/apps\/chat\/"}
````
 - show message when there are no users on this ownCloud
 - show both users and conversation in the left bar
 - Remove the giant contacts on startup
 - Add search field to search in contacts and conversations
 - make emojis just 20px and inline
 - after pressing the "send chat msg button", focus chat text field
 - sort conversations by recently
 - instead of the giant icons shown when inviting people, show a popover with filter functionallity
 - make all avatars 40px
 - use green dot for online state for all avatars. Don't show offline state.
 - restyle of the chat message textarea. 
 - auto resize textarea when there are multiple lines of text
 - move the functions in core/appapi.php to app/chat.php
 - remove core/api.php
 - removed a lot of legacy code
 - close the emoji popover when chosing one
 - add Makefile to easy run unit tests and create package
 - add unit tests



##0.2.0
 - fix syntax errors in database.xml
 - fix error with enabling backends 
 - fix upgrade errors

