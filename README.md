Chat
====

Have chats in your ownCloud!

## Installation
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


## Built-in API
*Note: the API is just a set of commands used by the server and the client*

 Action  | JSON Request Data   | JSON Possible Response Data  
 --- | --- | ---
greet | `{status: “command”, data: {type: “greet”, param: {user: “foo”}}}` | `{status:  “success”}` `{status: “error”, data: {msg: “NO-OC-USER” }}`
join | `{status: “command”, data: {type: “join”, param: {user: “foo”, conversationID: “bar”, timestamp: “”}}}` | `{status: “success”}` `{status: “error”, data: {msg: “NO-OC-USER” }}`
invite | `{status: “command”, data: {type: “invite”, param: { user: “foo”, conversationID: “bar”, timestamp: “” usertoinvite: “”}}}` | `{status: “success”} {status: “error”, data: {msg: “USER-TO-INVITE-NOT-ONLINE”}}` `{status: “error”, data: {msg: “USER-EQAUL-TO-USER-TO-INVITE”}}` `{status: “error”, data: {msg: “USER-TO-INVITE-NOT-OC-USER”}}`
leave | `{status: “command”, data: {type: “leave”, param: {user: “foo”, conversationID: “bar”}}}` | `{status: “success”}` `{status: “error”, data: {msg: “conversationIDdontexists”}}`
getUsers | `{status: “command”, data: {type: “getusers”, param: {conversationID: “bar”}}}` | `{status: “success”, data: {param: {users: [foo, bar, foobar]}}}`
send | `{status: “command”, data: {type: “send”, param: {conversationID: “bar”, msg: “foobar”}}}` | `{status: “success”}`

## Possible Error Messages

Error Message  | Meaning 
  --- | ---
`USER-EQAUL-TO-USER-TO-INVITE` |  
`USER-TO-INVITE-NOT-OC-USER` |
`USER-TO-INVITE-NOT-ONLINE` |
`NO-OC-USER` |
`USER-TO-INVITE-EMPTY` |

