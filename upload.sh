cd /var/lib/openshift/54dde415e0b8cd214f0000c2/app-root/repo
rm -rf build.zip
wget https://occjen-ledfan.rhcloud.com/job/ownCloud-Chat/ws/build.zip
wget https://occjen-ledfan.rhcloud.com/job/ownCloud-Chat/ws/$(wget https://occjen-ledfan.rhcloud.com/job/ownCloud-Chat/ws/commit)
