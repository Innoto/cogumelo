#!/bin/bash

CGUSER=$USER
CGSERV=www-data


mkdir -p c_app/tmp
mkdir -p c_app/tmp/templates_c
mkdir -p c_app/log
mkdir -p c_app/backups


sudo chown -R $CGUSER:$CGUSER cogumelo cogumelo.php composer.phar set_permissions.sh
chmod 700 cogumelo cogumelo.php composer.phar set_permissions.sh

sudo chown -R $CGUSER:$CGSERV c_app
#chmod -R ug-x,g-w,o-rwx,u+rwX,g+rX c_app
# TEMPORALMENTE
chmod -R ug-x,g-w,o-rwx,u+rwX,go+rX c_app
chmod -R g+w c_app/tmp c_app/log

sudo chown $CGUSER:$CGUSER c_app/backups
chmod -R go-rwX,o+rw c_app/backups

sudo chown -R $CGUSER:$CGSERV httpdocs
#chmod -R g-wx,o-rwx,u+rwX,g+rX httpdocs
# TEMPORALMENTE
chmod -R g-wx,o-rwx,u+rwX,go+rX httpdocs
chmod g-x,o-wx,ug+rwX,o+rX httpdocs/test_upload


echo -e "\n\n  READY. Enjoy :)  \n\n"

