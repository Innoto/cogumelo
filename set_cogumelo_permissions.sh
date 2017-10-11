#!/bin/bash

cogumeloPath='/home/proxectos/cogumelo'

CGUSER=$USER
CGSERV=www-data


sudo chown -R $CGUSER:$CGSERV $cogumeloPath/*
#chmod -R u-x,g-wx,o-rwx,u+rwX,g+rX *
chmod -R u-x,g-wx,o-wx,u+rwX,go+rX $cogumeloPath/*
chmod 700 $cogumeloPath/set_cogumelo_permissions.sh


echo -e "\n\n  READY. Enjoy :)  \n\n"
