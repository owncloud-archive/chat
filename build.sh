#!/bin/bash

files_to_delete=(".bowerrc" "bower.json" ".git*" "./.scrutinizer.yml" "./.travis.yml" "Gruntfile.js" "karma.conf.js" "Makefile" "package.json" "build.sh" "tests")

mkdir ../build
cp -r . ../build

npm install
grunt

for file in ${files_to_delete[@]}
do
    rm -rf ../build/$file
done
git