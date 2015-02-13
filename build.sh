#!/bin/bash
# This file packages the ownCloud Chat app
# This file is meant to run on the Jenkins server used by the Chat app. (https://occjen-ledfan.rhcloud.com)
export HOME=/var/lib/openshift/547b566ee0b8cd470a000128/app-root/data/jobs/ownCloud-Chat/workspace
files_to_delete=("upload.sh" "composer.json" "composer.lock" "vendor/emojione/assets/svg" "vendor/emojione/assets/png" ".npm" ".node-gyp" "node_modules" ".bowerrc" "bower.json" ".git*" "./.scrutinizer.yml" "./.travis.yml" "Gruntfile.js" "karma.conf.js" "Makefile" "package.json" "build.sh" "tests")
rm -rf ../chat
rm -rf node_modules
mkdir ../chat

cp -r ../../../node-v0.10.33-linux-x64/chat/node_modules/ .
../../../node-v0.10.33-linux-x64/bin/node node_modules/grunt-cli/bin/grunt


cp -r . ../chat

for file in ${files_to_delete[@]}
do
    rm -rf ../chat/$file
done

ls -al

cd ../
rm -rf build.zip
zip -r build.zip  chat
mv build.zip workspace/
cd workspace
echo $(git rev-parse HEAD) > commit
cp -r build.zip $(git rev-parse HEAD)

