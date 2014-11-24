#!/bin/bash

cd /home/proxectos/cogumelo

CGUSER=$USER
CGSERV=www-data


sudo chown -R $CGUSER:$CGSERV *
#chmod -R u-x,g-wx,o-rwx,u+rwX,g+rX *
chmod -R u-x,g-wx,o-wx,u+rwX,go+rX *
chmod 700 set_cogumelo_permissions.sh


chmod 700 packages/sampleApp/cogumelo
cd packages/sampleApp/
./cogumelo setPermissionsDevel


echo -e "\n\n  READY. Enjoy :)  \n\n"

