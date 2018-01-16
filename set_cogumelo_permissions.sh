#!/bin/bash

if [ -z "$1" ]
then
  cogumeloPath='/home/proxectos/cogumelo'
else
  cogumeloPath=$1
fi


if [ -e ${cogumeloPath}/set_cogumelo_permissions.sh ]
then
  CGUSER=$USER
  CGSERV=www-data

  sudo chown -R $CGUSER:$CGSERV $cogumeloPath/*
  chmod -R u-x,g-wx,o-wx,u+rwX,go+rX $cogumeloPath/*
  chmod 700 $cogumeloPath/set_cogumelo_permissions.sh

  echo -e "\nSet Cogumelo permissions READY. Enjoy :)\n\n"
else
  echo -e "\n\nERROR - Set Cogumelo permissions\n\nERROR - NOT valid path: ${cogumeloPath}/\n\n"
fi
