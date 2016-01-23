Chat
====
Have chats in your ownCloud!

## Status

| What | Status |
| ---- | --- |
| JavaScript and PHP Unit tests | [![Build Status](https://travis-ci.org/owncloud/chat.svg?branch=master)](https://travis-ci.org/owncloud/chat) |
| PHP Unit test coverage | [![Code Coverage](https://scrutinizer-ci.com/g/owncloud/chat/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/owncloud/chat/?branch=master) |
| Scrutinizer Code Quality | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/owncloud/chat/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/owncloud/chat/?branch=master) |
| Development dependencies | [![devDependency Status](https://david-dm.org/owncloud/chat/dev-status.svg)](https://david-dm.org/owncloud/chat#info=devDependencies) |
| [Chat App Package](#install) | [![Build Status](https://occjen-ledfan.rhcloud.com:443/buildStatus/icon?job=ownCloud-Chat)](https://occjen-ledfan.rhcloud.com:443/job/ownCloud-Chat/) |
| [Chat App Live Demo](#live-demo)  | [![Build Status](https://occjen-ledfan.rhcloud.com:443/job/chat-deploy/badge/icon)](https://occjen-ledfan.rhcloud.com:443/job/chat-deploy/) | 

## Install
The Chat app depends on ownCloud 7.0.3 or newer. This means master (https://github.com/owncloud/core) can be used too.
You can't directly use the Git repo without [building the CSS and JS code] (#important-run-grunt-when-you-change-js-files). You can use the package which is build every time a commit is pushed to the repository. [Download it here](https://occjen-ledfan.rhcloud.com/job/ownCloud-Chat/ws/build.zip)

## Live Demo
There is a live demo hosted on [OpenShift](https://oc-ledfan.rhcloud.com/). You can use to test the Chat app. The demo is automatically updated every time a commit is pushed to this repository.
**WARNING** Do not store any private information (passowrds!) in the App! 

### Credentials
There are enough users on the ownCloud instance to give it a good test:

| Username | Password |
| --- | --- |
| derp | derp |
| herp | herp |
| foo | foo |
| bar | bar | 
| foobar | foobar |
| barfoo | barfoo |

## Screenshots
**Initial screen**
![screen shot 2014-11-30 at 09 44 28](https://cloud.githubusercontent.com/assets/2996275/5237202/7efd7bfc-7875-11e4-929a-77349b4a4f45.png)

**XMPP Conversation**
![screen shot 2014-11-30 at 09 45 23](https://cloud.githubusercontent.com/assets/2996275/5237207/a1021578-7875-11e4-8f3b-83b305f12a18.png)

**ownCloud conversation**
![screen shot 2014-11-30 at 09 45 55](https://cloud.githubusercontent.com/assets/2996275/5237209/b5441432-7875-11e4-90b5-dd05f8706628.png)

**Bold when new messages**
![screen shot 2014-11-30 at 09 46 20](https://cloud.githubusercontent.com/assets/2996275/5237210/c1877c2a-7875-11e4-90eb-8cd5747bbc18.png)

**Contact/Conversation filtering**
![screen shot 2014-11-30 at 09 46 45](https://cloud.githubusercontent.com/assets/2996275/5237211/d1b36bfe-7875-11e4-81d7-d3a473baeb34.png)

**Inviting users for group conversations**
![screen shot 2014-11-30 at 09 47 05](https://cloud.githubusercontent.com/assets/2996275/5237212/e13b3192-7875-11e4-98b4-95f19254c0dd.png)

**Group conversations**
![screen shot 2014-11-30 at 09 47 32](https://cloud.githubusercontent.com/assets/2996275/5237213/ed27a27e-7875-11e4-8823-f7e9b5ffe522.png)

**Picking a file to attach**
![screen shot 2014-11-30 at 09 47 53](https://cloud.githubusercontent.com/assets/2996275/5237215/f7041aca-7875-11e4-88c4-924b8d96f370.png)

**File attached**
![screen shot 2014-11-30 at 09 48 06](https://cloud.githubusercontent.com/assets/2996275/5237216/07b28fe6-7876-11e4-8a03-34ee551a74c1.png)


## Features
 - Conversations between 2 users
 - Group conversations
 - The faces and hands of the emoji set can be used
 - Online and offline state of contacts are updated every 10 seconds
 - Inline video and image support [Angular-enhance-text](https://github.com/Raydiation/angular-enhance-text)
 - Notification in tab
 - Notification in left-bar when another conversation is active

## Contribute
Contriubtions are very welcome! You can contribute on many ways:
 - Test the app (see [Installation](https://github.com/owncloud/chat#installation)) and report any issue and problem you encore in the [Issue tracker](https://github.com/owncloud/chat/issues)
 - Improve code or add new features
 - Help with design by commenting on issues
 - Solve bugs

### Important: run grunt when you change JS files
Because both the JavaScript and CSS source files are minified to one single file, grunt must be run after every change. The minified files are ignored by git. To simply test this repo see [Install](#install)
To run grunt you'll need `NodeJS`. On Arch Linux this can be installed via:
````
sudo pacman -S nodejs
````
On Ubuntu this can be installed via:
````
sudo apt-get update
sudo apt-get install nodejs npm
````
On OpenSUSE this can be installed via:
````
sudo zypper install nodejs
````

Install the dependencies (run from inside the Chat app folder)
````
npm install
````

Now you can run `grunt` (also inside the Chat app folder)
````
grunt
````

