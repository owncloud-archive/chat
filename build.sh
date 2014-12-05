#!/bin/bash
# This file packages the ownCloud Chat app
# This file is meant to run on the Jenkins server used by the Chat app. (https://occjen-ledfan.rhcloud.com)

files_to_delete=("node_modules" ".bowerrc" "bower.json" ".git*" "./.scrutinizer.yml" "./.travis.yml" "Gruntfile.js" "karma.conf.js" "Makefile" "package.json" "build.sh" "tests")
rm -rf ../chat
mkdir ../chat

#../../../node-v0.10.0-linux-x64/bin/node node_modules/grunt-cli/bin/grunt
../../../node-v0.10.33-linux-x64/bin/node npm install
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
