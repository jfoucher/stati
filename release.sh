#!/bin/bash

if [ -z $1 ]; then
    echo 'Please specify a tag for this release';
    exit 1;
fi

echo "Creating Stati release $1"

git tag $1;
./build.sh
git tag -d $1;

#git commit -am"Tagging release $1"
#git tag $1;
#git push --tags