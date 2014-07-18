Chat
====

[![Build Status](https://travis-ci.org/owncloud/chat.svg?branch=master)](https://travis-ci.org/owncloud/chat)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/owncloud/chat/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/owncloud/chat/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/owncloud/chat/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/owncloud/chat/?branch=master)

Have chats in your ownCloud!

## Screenshots
**Choosing a contact to start a conversation with**
[![Choosing a contact to start a conversation with](https://gist.github.com/LEDfan/346d401f75fee2f02dff/raw/0bc794c964049b0dfc72b768426255755a6393f8/startup.png)](https://gist.github.com/LEDfan/346d401f75fee2f02dff/raw/0bc794c964049b0dfc72b768426255755a6393f8/startup.png)
**Conversation with between two users**
[![Conversation with 2 users](https://gist.github.com/LEDfan/346d401f75fee2f02dff/raw/30f409a62c68ff0a9fc3a885b174ada651ae8a6b/1-1-conv.png)](https://gist.githubusercontent.com/LEDfan/346d401f75fee2f02dff/raw/30f409a62c68ff0a9fc3a885b174ada651ae8a6b/1-1-conv.png)
**Group Conversation**
[![Group Conversation](https://gist.github.com/LEDfan/346d401f75fee2f02dff/raw/76a2c5b26a22293abccf1eb3e038110eaf4ddc19/group-conv.png)](https://gist.github.com/LEDfan/346d401f75fee2f02dff/raw/76a2c5b26a22293abccf1eb3e038110eaf4ddc19/group-conv.png)
**Notication of a new message in another conversation**
[![Notication for a new message in another conversation](https://gist.github.com/LEDfan/346d401f75fee2f02dff/raw/3aa7943b766f6649b7364e37db6908eab5ba21ed/notification-bold.png)](https://gist.github.com/LEDfan/346d401f75fee2f02dff/raw/3aa7943b766f6649b7364e37db6908eab5ba21ed/notification-bold.png)
**Online and offline state of contacts are updated every 10 seconds**
[![Offline and Online state of contacts are updated every 10 seconds](https://gist.github.com/LEDfan/346d401f75fee2f02dff/raw/65ad49ac360606ab3abc5136a3122d7f4fef13ad/ofline-online.png)](https://gist.github.com/LEDfan/346d401f75fee2f02dff/raw/65ad49ac360606ab3abc5136a3122d7f4fef13ad/ofline-online.png)
**Inline video and image support**
[![Inline video and image support]https://gist.github.com/LEDfan/346d401f75fee2f02dff/raw/0ae5488e28d4eed29190eab16cb8dade50870226/video-image.png](https://gist.github.com/LEDfan/346d401f75fee2f02dff/raw/0ae5488e28d4eed29190eab16cb8dade50870226/video-image.png)


## Features
 - Conversations between 2 users
 - Group conversations
 - The faces and hands of the emoji set can be used
 - Online and offline state of contacts are updated every 10 seconds
 - Inline video and image support [Angular-enhance-text](https://github.com/Raydiation/angular-enhance-text)
 - Notification in tab
 - Notification in left-bar when another conversation is active

## Installation
The Chat app depends on ownCloud 7.0 which needs to be downloaded via git. Temporary it's needed to checkout the `localuser-addressbook` branch in the core repo.
After enabling the Chat app you can immediately start with chatting!

 - `git clone https://github.com/owncloud/core`
 - `git checkout localuser-addressbook`
 - `cd apps`
 - `git clone https://github.com/owncloud/chat`
 - Follow the installation wizard of ownCloud
 - Enable the Chat app in the apps settings

## Contribute
Contriubtions are very welcome! You can contribute on many ways:
 - Test the app (see #Installation) and report any issue and problem you encore in the [Issue tracker](https://github.com/owncloud/chat/issues)
 - Improve code or add new features
 - Help with design by commenting on issues
 - Solve bugs
