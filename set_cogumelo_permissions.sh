#!/bin/bash

CGUSER=$USER
CGSERV=www-data


sudo chown -R $CGUSER:$CGSERV *
#chmod -R u-x,g-wx,o-rwx,u+rwX,g+rX *
chmod -R u-x,g-wx,o-rwx,u+rwX,go+rX *
chmod 700 set_cogumelo_permissions.sh


chmod 700 c_packages/base_app/cogumelo
cd c_packages/base_app/
./cogumelo setPermissions


echo -e "\n\n  READY. Enjoy :)  \n\n"

