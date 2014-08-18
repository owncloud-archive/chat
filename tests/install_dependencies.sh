#!/bin/bash

WORKDIR=$PWD
echo "Work directory: $WORKDIR"
cd ..
git clone https://github.com/owncloud/core
cd core
git submodule update --init
cd apps
git clone https://github.com/owncloud/chat
cd $WORKDIR
