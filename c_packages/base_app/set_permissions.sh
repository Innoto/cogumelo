#!/bin/bash

CGUSER=$USER
CGSERV=www-data


mkdir -p c_app/tmp
mkdir -p c_app/tmp/templates_c
mkdir -p c_app/log
mkdir -p c_app/backups


sudo chown -R $CGUSER:$CGUSER cogumelo cogumelo.php set_permissions.sh
chmod 700 cogumelo cogumelo.php set_permissions.sh

sudo chown -R $CGUSER:$CGSERV c_app
#chmod -R ug-x,g-w,o-rwx,u+rwX,g+rX c_app
# TEMPORALMENTE
chmod -R ug-x,g-w,o-rwx,u+rwX,g+rX,o+r c_app
chmod -R g+w c_app/tmp c_app/log

sudo chown $CGUSER:$CGUSER c_app/backups
chmod -R go-rwX,o+rw c_app/backups

sudo chown -R $CGUSER:$CGSERV httpdocs
chmod -R g-wx,o-rwx,u+rwX,g+rX httpdocs


echo -e "\n\n  READY. Enjoy :)  \n\n"

