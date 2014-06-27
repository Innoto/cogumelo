#!/bin/bash

CGUSER=$USER
CGSERV=www-data


sudo chown -R $CGUSER:$CGSERV *
chmod -R u-x,g-wx,o-rwx,u+rwX,g+rX *
chmod 700 set_cogumelo_permissions.sh


#
# TEMPORALMENTE
#
chmod 700 c_packages/base_app/set_permissions.sh
cd c_packages/base_app/
./set_permissions.sh


echo -e "\n\n  READY. Enjoy :)  \n\n"

