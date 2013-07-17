#!/bin/bash 
## atención este script non funciona apra esta versión de cogumelo. 

if [ -z "$1" ]; then
	OPCION="help"
else
	OPCION=$1
fi


if [ $OPCION == "create" ]; then


	echo "========================================="
	echo "Creación de proxecto dende cogumelo Trunk"
	echo "========================================="

	read -p "Nome do proxecto:" nome

	#if exist rename drupal index.php
	mv  $nome/httpdocs/index.php $nome/httpdocs/index_drupal.php
	mv  $nome/httpdocs/.htaccess $nome/httpdocs/.htaccess_drupal

	svn export --force https://cogumelo.googlecode.com/svn/trunk/Packages/NewProject/ $nome

	while true; do
	    read -p "¿Instalar aplicación base con gestión administrador? (s/n)" yn
	    case $yn in
	        [Ss]* ) svn export --force https://cogumelo.googlecode.com/svn/trunk/Packages/AdminModule/ $nome; break;;
	        [Nn]* ) echo "Omitindo...";break;;
	        * ) echo "Debes responder S o N";;
	    esac
	done
	
	while true; do
	    read -p "¿Instalar Filelift? (s/n)" yn
	    case $yn in
	        [Ss]* ) svn export --force https://cogumelo.googlecode.com/svn/trunk/Packages/FileliftModule/ $nome; break;;
	        [Nn]* ) echo "Omitindo...";break;;
	        * ) echo "Debes responder S o N";;
	    esac
	done

	while true; do
	    read -p "¿Instalar módulo drupal? (s/n)" yn
	    case $yn in
	        [Ss]* ) svn export --force https://cogumelo.googlecode.com/svn/trunk/Packages/DrupalModule/ $nome; mv  $nome/httpdocs/.htaccess_drupal $nome/httpdocs/.htaccess; break;;
	        [Nn]* ) echo "Omitindo...";break;;
	        * ) echo "Debes responder S o N";;
	    esac
	done

	# gettext support
	while true; do
	    read -p "¿Soportar internacionalización (i18n) de aplicación y módulos instalados? (s/n)" yn
	    case $yn in
	        [Ss]* ) 
	                find cogumelo_0.4 $nome/c_app/. -iname "*.inc" -o -iname "*.php" | xargs xgettext -kT_gettext -kT_ --from-code utf-8 -d c_project -o $nome/c_app/i18n/c_project.pot -L PHP;
        	        msginit -l en -o $nome/c_app/i18n/c_project_en.po -i $nome/c_app/i18n/c_project.pot;
                	msginit -l en -o $nome/c_app/i18n/c_project_es.po -i $nome/c_app/i18n/c_project.pot;
	                msginit -l en -o $nome/c_app/i18n/c_project_gl.po -i $nome/c_app/i18n/c_project.pot;
	                msgfmt -c -v -o $nome/c_app/i18n//en/LC_MESSAGES/c_project.mo $nome/c_app/i18n/c_project_en.po;
	                msgfmt -c -v -o $nome/c_app/i18n//es/LC_MESSAGES/c_project.mo $nome/c_app/i18n/c_project_es.po;
	                msgfmt -c -v -o $nome/c_app/i18n//gl/LC_MESSAGES/c_project.mo $nome/c_app/i18n/c_project_gl.po;
	
			echo "Los archivos de traducciones se almacenan en c_app/i18n/";
			break;;
	        [Nn]* ) echo "Omitindo...";break;;
	        * ) echo "Debes responder S o N";;
	    esac
	done


	#permisos
	chmod -R 777 $nome/c_app/tmp
	chmod -R 777 $nome/c_app/i18n
	chmod -R 777 $nome/c_app/log

elif [ $OPCION == "updatei18n" ]; then

        echo "========================================="
        echo " Update i18n"
        echo "========================================="

        read -p "Nome do proxecto:" nome


	find cogumelo_0.4 $nome/c_app/. -iname "*.inc" -o -iname "*.php" | xargs xgettext -kT_gettext -kT_ --from-code utf-8 -d c_project -o $nome/c_app/i18n/c_project.pot -L PHP;

	msgmerge -U $nome/c_app/i18n/c_project_gl.po fokerproject/c_app/i18n/c_project.pot
	msgfmt -c -v -o $nome/c_app/i18n/gl/LC_MESSAGES/c_project.mo $nome/c_app/i18n/c_project_gl.po

	msgmerge -U $nome/c_app/i18n/c_project_es.po $nome/c_app/i18n/c_project.pot
	msgfmt -c -v -o $nome/c_app/i18n/es/LC_MESSAGES/c_project.mo $nome/c_app/i18n/c_project_es.po

	msgmerge -U $nome/c_app/i18n/c_project_en.po $nome/c_app/i18n/c_project.pot
	msgfmt -c -v -o $nome/c_app/i18n/en/LC_MESSAGES/c_project.mo $nome/c_app/i18n/c_project_en.po

	echo "Ficheros i18n actualizados con éxito"

else
    echo " "
	echo "====================================================="
    echo " 		Cogumelo util's Script"
    echo "====================================================="
    echo " "
    echo "Usage:"
    echo " ./c_utils_trunk.sh updatei18n :  Actualización archivos multilang i18n"
    echo " ./c_utils_trunk.sh create :  Crear nuevo proyecto (siga instrucciones)"
    echo " "
fi

