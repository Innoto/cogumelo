#!/bin/bash

cd /home/proxectos/cogumelo

CGUSER=$USER
CGSERV=www-data


sudo chown -R $CGUSER:$CGSERV *
#chmod -R u-x,g-wx,o-rwx,u+rwX,g+rX *
chmod -R u-x,g-wx,o-wx,u+rwX,go+rX *
chmod 700 set_cogumelo_permissions.sh


chmod 700 c_packages/sampleApp/cogumelo
cd c_packages/sampleApp/
./cogumelo setPermissions


echo -e "\n\n  READY. Enjoy :)  \n\n"

