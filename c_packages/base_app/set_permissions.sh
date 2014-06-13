#!/bin/bash

EU=$USER

mkdir c_app/tmp
mkdir c_app/tmp/templates_c
mkdir c_app/log
mkdir c_app/backups

sudo chown -R $EU:www-data httpdocs
chmod -R go-rwx,g+rX httpdocs

sudo chown -R $EU:www-data c_app
chmod -R g-x,o-wx,g+rwX,o+rX c_app
chmod -R gu+rwX,o+rX c_app/tmp c_app/log
sudo chown $EU:$EU c_app/backups

echo .
echo 'READY. Enjoy :)'
echo .

