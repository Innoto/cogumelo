#!/bin/bash

EU=$USER

# Cogumelo
sudo chown -R $EU:www-data /home/proxectos/cogumelo_0.4/
chmod -R go-rwx,g+rX /home/proxectos/cogumelo_0.4/


sudo chown -R $EU:www-data httpdocs
chmod -R go-rwx,g+rX httpdocs

sudo chown -R $EU:www-data c_app
chmod -R go-rwx,g+rX c_app
chmod -R go-rwx,g+rwX c_app/tmp c_app/log
#chmod -R u-r,go-rwx c_app/logs/*gz
#rm c_app/tmp/templates_c/*
#sudo chown -R $EU:www-data upload
#chmod -R go-rwx,g+rwX upload


echo .
echo 'READY. Enjoy :)'
echo .

