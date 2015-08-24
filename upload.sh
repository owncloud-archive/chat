cd /var/lib/openshift/54dde415e0b8cd214f0000c2/app-root/repo
rm -rf build.zip
rm -rf commit
wget https://occjen-ledfan.rhcloud.com/job/ownCloud-Chat/ws/build.zip
wget https://occjen-ledfan.rhcloud.com/job/ownCloud-Chat/ws/commit
mv build.zip $(cat commit).zip
rm -rf commit
