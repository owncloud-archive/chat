Chat
====

Have chats in your ownCloud!

### Installation
1.	Clone the repo to the apps directory of your owncloud installation
2.	`git submodule init && git submodule update`
3. 	Activate the chat app
4.	Launch server/bin/bootstrap.php via php from the commandline
5.	Do this only in a safe environment, **not** in a productive environment:
	Append `"custom_csp_policy" => "",` before the closing `);` in your config/config.php
6.	You may need to free port 8080 in your firewall
7. 	Go to the chat app in owncloud. You should see 'Connected to the server'
8. 	Fill in the username of an other user
9. 	Click on the left bar on the conversation
10. 	Start chatting!
