JavaScript in the ownCloud Chat app
====

In this directory all JavaScript code is located except third party code which is located in the /vendor file.

## Directory Listing
This is a list with all directories with JS code. There is more information about a directory in the directory's README.md.

| Directory | Which code |
| ---- | --- |
| /admin | All source code for the admin settings of the Chat app. This is only used on the `/index.php/settings/admin` page of ownCloud |
| /src | All source code which is common for both the [native]()  as the [integrated]() ownCloud app.  |
| /app | All source code which is only used for the [native]() ownCloud Chat app. Thus this code is used only on the `/index.php/apps/chat` ownCloud page |
| /integrated | All source code which is only used for the [integrated]() ownCloud Chat app. Thus this code is used ony every ownCloud page except the `/index.php/apps/chat` ownCloud page. |
| /test | All JavaScript Unit tests |

## Builded code

| File | Which code |
| --- | --- |
| admin.min.js | This file only includes code of the `/admin` directory |
| app.min.js | This file includes the `/src` and `/app` directory |
| integrated.min.js | This file includes the `/src` and `/integrated` directory |