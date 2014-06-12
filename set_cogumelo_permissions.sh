#!/bin/bash

EU=$USER


sudo chown -R $EU:www-data *
chmod -R go-rwx,g+rX *

echo .
echo 'READY. Enjoy :)'
echo .

