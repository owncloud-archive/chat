export HOME=/var/lib/openshift/547b566ee0b8cd470a000128/app-root/data/jobs/ownCloud-Chat/workspace
rm -rf ../chat
rm -rf node_modules
mkdir ../chat
cp -r ../../../node-v0.10.33-linux-x64/chat/node_modules/ .
../../../node-v0.10.33-linux-x64/bin/node node_modules/grunt-cli/bin/grunt dist
echo $(git rev-parse HEAD) > commit
