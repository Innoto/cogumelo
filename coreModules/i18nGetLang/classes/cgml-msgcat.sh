#!/bin/bash

#echo "-------------" >> $0.log
#echo "- EXECUTANDO: $0 $@" >> $0.log
#echo "-------------" >> $0.log

FIN=$1
FROM=$0.tmp.from
MIX=$0.tmp.mix


# rm $MIX
# touch $MIX
truncate --size 0 $MIX


for PO in "${@:2}"
do
#  echo 'hola'
#  echo "Procesando:"$PO  >> $0.log
  grep -v '"POT-Creation-Date:' $PO | grep -v '"PO-Revision-Date:' > $FROM
  msgcat $FROM $MIX -o $MIX
  #msgcat $FROM $MIX -o $MIX 2>> $0.log
done


cp $MIX $FIN

# rm $FROM $MIX


#echo " - FIN" >> $0.log
#echo "" >> $0.log
