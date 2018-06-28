#!/bin/bash

# Log through syslog using logger and set the "app-name" protocol header with "httpd-(access|error)-http[s]-"

LOGGER=logger

while read line ; do
  [[ $2 == access ]] && [[ $3 == http ]]  && $LOGGER -p daemon.info  -t "httpd-access-http-${1}"
  [[ $2 == access ]] && [[ $3 == https ]] && $LOGGER -p daemon.info  -t "httpd-access-https-${1}"
  [[ $2 == error ]]  && [[ $3 == http ]]  && $LOGGER -p daemon.error -t "httpd-error-http-${1}"
  [[ $2 == error ]]  && [[ $3 == https ]] && $LOGGER -p daemon.error -t "httpd-error-https-${1}"
done


